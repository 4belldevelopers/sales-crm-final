@extends('layouts.staffing_company_layout')
@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!-- Responsive CSS End -->
<style>
</style>
@endpush
@if(in_array("add_jobs", $userPermissions))
@section('create-button')
<div class="row align-items-center">
  <div class="col-md-7 text-left d-flex">
    <!-- invisible -->
    <div class="dropdown dropdown-inline invisible">
      <button class="dropbtn bg-light dropdown-toggle dropbtn-border">10</button>
      <div class="dropdown-content">
        <ul class="navi">
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
        </ul>
      </div>
    </div>
    <div class="all-positions pl-3 padding-left-browser">
      <h3 class="card-label mb-0" style="font-size: 14px;"> <span class="d-block text-dark font-size-sm mt-custom">
        <div class="d-flex align-items-center"> <a href="Javascript:void(0);" class=""></a> <a href="Javascript:void(0);" class="text-secondary mr-1">All Position&nbsp; <span class="badge badge-secondary badge-sm">{{ number_format($totalJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-primary mr-1">&nbsp;Active <span class="badge badge-primary badge-sm">{{ number_format($activeJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-warning mr-1">&nbsp;Hold <span class="badge badge-warning badge-sm">{{ number_format($holdJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-danger mr-1">&nbsp;Closed <span class="badge badge-danger badge-sm">{{ number_format($inactiveJobs) }}</span> </a>
          <!--end::Button-->
        </div>
        </span>
        <!-- All Jobs -->
      </h3>
    </div>
  </div>
  <div class="col-md-5 text-right searchbar-parent">
    <div class="input-group mr-1 searchbar-child" style="">
      <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
    </div>
    <a href="{{ route('staffing_company.jobs.create') }}" class="d-flex align-items-center btn-grad-req btn-sm mr-1 px-2 py-1"> <span><i class="fa fa-plus-square fa-sm"></i>&nbsp;Add Requisition</span> </a>
    <!--a href="{{ route('staffing_company.job-applications.create') }}" class="btn btn-light-danger btn-sm mr-1 px-2 py-1">
         <i class="fa fa-upload"></i>&nbsp;Upload Profile
         </a-->
    <a href="{{ route('staffing_company.job-applications.create') }}" class="d-flex align-items-center btn-grad-app btn-sm mr-1 px-2 py-1"> <span><i class="fa fa-user-plus"></i>&nbsp;Add Applicant</span> </a> <a onclick="exportExcel()">
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
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-primary-800 mb-1">All Requisition</a> </h1>
        <span class="text-gray-600" id="">6,456</span> </div>
      <!--end::All Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Client Req.-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1">Client Req.</a> </h1>
        <span class="text-gray-600">1,222</span> </div>
      <!--end::Client Req.-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Custom Req.-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1">Custom Req.</a> </h1>
        <span class="text-gray-600">2,945</span> </div>
      <!--end::Custom Req.-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Jobs-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1">Jobs</a> </h1>
        <span class="text-gray-600">484</span> </div>
      <!--end::Jobs-->
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
          <!--begin::Add Requisition-->
          <a  href="<?php echo url('staffing_company/jobs/create'); ?>" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24"/>
              <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
            </g>
          </svg>
          </span> Add Requisition </a> </div>
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
    <div id="kt_content_container" class="container-xxl px-2">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Table-->
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
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
              <th class="">Date</th>
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
          <!--begin::HM/CP sidebar-->
          <a id="kt_activitiesHiring_toggle" class="show-detail btn btn-sm btn-primary mb-4 cursor-pointer me-4 d-none" onclick="opensidebar();">HM/CP Sidebar</a>
          <!--end::HM/CP sidebar-->
          <!--begin::Internal Team sidebar-->
          <a id="kt_activitiesITeam_toggle" class="show-detail btn btn-sm btn-primary mb-4 cursor-pointer me-4" onclick="opensidebar();">Internal Team Sidebar</a>
          <!--end::Internal Team sidebar-->
          <!--begin::Client sidebar-->
          <a id="kt_activitiesClient_toggle" class="show-detail btn btn-sm btn-primary mb-4 cursor-pointer me-4" onclick="opensidebar();">Client Sidebar</a>
          <!--end::Client sidebar-->
          <!--begin::Staffing Partners sidebar-->
          <a id="kt_activitiesStaffing_toggle" class="show-detail btn btn-sm btn-primary mb-4 cursor-pointer" onclick="opensidebar();">Staffing Partners Sidebar</a>
          <!--end::Staffing Partners sidebar-->
          <!--begin::HM/CP sidebar-->
          <div id="kt_activitiesHiring" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHiring_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
            <div class="card shadow-none rounded-0 w-100">
              <!--begin::Header-->
              <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                  <div class=" d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
                      <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">4</div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">4Bell Technology</a>
                      <!-- <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7">Veebrij</a> -->
                    </div>
                  </div>
                  </h3>
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
              <!--begin::Body-->
              <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
                <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
                  <!--begin::Tabs-->
                  <div class="card card-custom mx-2">
                    <!--Begin::Tabs Header-->
                    <div class="card-header card-header-tabs-line">
                      <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#team1"> <span class="nav-text font-weight-bold">Team</span> </a> </li>
                          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#assigned1"> <span class="nav-text font-weight-bold">Assigned</span> </a> </li>
                        </ul>
                      </div>
                    </div>
                    <!--end::Tabs Header-->
                    <!--Begin::Tabs Body-->
                    <div class="card-body py-0">
                      <div class="tab-content">
                        <!--begin::Team Content-->
                        <div class="tab-pane active show" id="team1" role="tabpanel">
                          <!--begin::Team header-->
                          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
                            <!--begin::Card title-->
                            <div class="card-title m-0 d-block">
                              <h3 class="fw-bolder m-0">Team</h3>
                            </div>
                            <!--end::Card title-->
                          </div>
                          <!--end::Team header-->
                          <div class="card-body px-0 py-5">
                            <div class="main-section my-0 dashed-border rounded px-3">
                              <form class="" id="team_form" method="post">
                                <!--begin::Header-->
                                <div class="row bg-light rounded-top-left dashed-border-bottom">
                                  <div class="col-xl-3 col-lg-3 py-4 dashed-border-right pl-2">
                                    <h3>Position</h3>
                                  </div>
                                  <div class="col-xl-9 col-lg-9 py-4 pl-2">
                                    <h3 class="">Choose Team Members</h3>
                                  </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Recruiter-->
                                <div class="row dashed-border-bottom" id="recruiter_check">
                                  <div class="col-lg-3 py-7 dashed-border-right">
                                    <label class="form-check form-check-custom form-check-solid me-10 pl-1">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#recruiter_check .form-check-input-rec" value="1">
                                    <span class="form-check-label fw-bold fs-5">Recruiter</span> </label>
                                  </div>
                                  <div class="col-lg-9 py-7">
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-rec me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning">S</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wordpress Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-rec me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Magento Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-rec me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-danger text-danger">A</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Andorid Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <!--end::Recruiter-->
                                <!--begin::Hiring Manager-->
                                <div class="row dashed-border-bottom" id="hiring_manager_check">
                                  <div class="col-lg-3 py-7 dashed-border-right">
                                    <label class="form-check form-check-custom form-check-solid me-10 pl-1">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" checked="checked" data-kt-check-target="#hiring_manager_check .form-check-input-hiri" value="1">
                                    <span class="form-check-label fw-bold fs-5">Hiring Manager</span> </label>
                                  </div>
                                  <div class="col-lg-9 py-7">
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flexmb-3">
                                      <input class="form-check-input form-check-input-hiri me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-1.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Priyanka</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Manager</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-hiri me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning">S</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wordpress Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-hiri me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-20.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Magento Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-hiri me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-danger text-danger">A</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Andorid Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-hiri me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-5.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Soni</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Recruiter</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <!--end::Hiring Manager-->
                                <!--begin::Team Lead-->
                                <div class="row dashed-border-bottom" id="team_lead_check">
                                  <div class="col-lg-3 py-7 dashed-border-right">
                                    <label class="form-check form-check-custom form-check-solid me-10 pl-1">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" checked="checked" data-kt-check-target="#team_lead_check .form-check-input-team" value="1">
                                    <span class="form-check-label fw-bold fs-5">Team Lead</span> </label>
                                  </div>
                                  <div class="col-lg-9 py-7">
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-danger text-danger">A</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Andorid Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-1.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Priyanka</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Manager</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-info text-info">S</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sobha</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Designer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-8.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Anuj Kumar</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HR</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-primary text-primary">D</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Diya Sharma</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">SEO</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Magento Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                    <div class="row">
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-10.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Neeraj Singh</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HR</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="col-lg-4 form-check form-check-custom form-check-solid pl-1 d-inline-flex mb-3">
                                      <input class="form-check-input form-check-input-team me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light text-dark">P</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Prachi Negi</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Andorid Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <!--end::Team Lead-->
                                <!--begin::Administator-->
                                <div class="row dashed-border-bottom" id="administator_check">
                                  <div class="col-lg-3 py-7 dashed-border-right">
                                    <label class="form-check form-check-custom form-check-solid me-10 pl-1">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#administator_check .form-check-input-admin" value="1">
                                    <span class="form-check-label fw-bold fs-5">Administator</span> </label>
                                  </div>
                                  <div class="col-lg-9 py-7">
                                    <div class="row">
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-admin me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-success text-success">M</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Money Tiwari</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HR</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-admin me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-21.jpg" alt="Max Smith" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Himanshu</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">PHP Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <!--end::Administator-->
                                <!--begin::Interviewer-->
                                <div class="row" id="interviewer_check">
                                  <div class="col-lg-3 py-7 dashed-border-right">
                                    <label class="form-check form-check-custom form-check-solid me-10 pl-1">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" checked="checked" data-kt-check-target="#interviewer_check .form-check-input-int" value="1">
                                    <span class="form-check-label fw-bold fs-5">Interviewer</span> </label>
                                  </div>
                                  <div class="col-lg-9 py-7">
                                    <div class="row">
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-int me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-4.jpg" alt="Manpreet Singh" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Manpreet Singh</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-int me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <a href="javascript:void(0);">
                                          <div class="symbol-label fs-3 bg-light-primary text-primary">A</div>
                                          </a> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Anjali Awasti</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Sales</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-int me-2" type="checkbox" value="1">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-12.jpg" alt="Praveen Gupta" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Praveen Gupta</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">React Developer</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                    <div class="row">
                                      <label class="form-check form-check-custom form-check-solid pl-1 d-inline-flex col-lg-4 mb-3">
                                      <input class="form-check-input form-check-input-int me-2" type="checkbox" value="1" checked="checked">
                                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-1.jpg" alt="Archana" class="w-100"> </div>
                                        <!--end::Avatar-->
                                        <!--begin::User details-->
                                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Archana</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Recruiter</a> </div>
                                        <!--begin::User details-->
                                      </td>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <!--end::Interviewer-->
                              </form>
                            </div>
                          </div>
                        </div>
                        <!--end::Team Content-->
                        <!--begin::Assigned Content-->
                        <div class="tab-pane" id="assigned1" role="tabpanel">
                          <!--begin::Working header-->
                          <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                              <h3 class="fw-bolder m-0">Assigned</h3>
                            </div>
                            <!--end::Card title-->
                          </div>
                          <!--end::Working header-->
                          <div class="card-body px-0 py-5">
                            <!--begin:Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                              <!--begin::Table head-->
                              <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                  <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-team1" value="1" />
                                    </div></th>
                                  <th class="">Name</th>
                                  <th class="">Contact Person</th>
                                  <th class="">Upload Profile</th>
                                  <th class="">Interview</th>
                                  <th class="">Joinings</th>
                                  <th class="">Action</th>
                                </tr>
                                <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <!--begin::Table body-->
                              <tbody class="text-gray-600 fw-bold">
                                <!--begin::Table row-1-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-team1" type="checkbox" value="1">
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="logo-first-letter" class="border-radius-5 w-100"> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Abu Baker</span> <span class="fw-bold text-muted d-block fs-7">PHP Developer</span> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Contact Person-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Recruiter</span> </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Upload Profile-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">102</span> </div></td>
                                  <!--end::Upload Profile-->
                                  <!--begin::Interview-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30</span> </div></td>
                                  <!--end::Interview-->
                                  <!--begin::Joinings-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">10</span> </div></td>
                                  <!--end::Joinings-->
                                  <!--begin::Action-->
                                  <td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger">Remove</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-1-->
                                <!--begin::Table row-2-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-team1" type="checkbox" value="1">
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail" id="kt_activities3_toggle">
                                        <div class="symbol-label fs-3 fs-3 bg-light-warning text-warning border-radius-5">S</div>
                                        </a> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Sonika Sharma</span> <span class="fw-bold text-muted d-block fs-7">Frontend Developer</span> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Contact Person-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Hiring Manager</span> </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Upload Profile-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td>
                                  <!--end::Upload Profile-->
                                  <!--begin::Interview-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td>
                                  <!--end::Interview-->
                                  <!--begin::Joinings-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td>
                                  <!--end::Joinings-->
                                  <!--begin::Action-->
                                  <td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger">Remove</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-2-->
                              </tbody>
                              <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                          </div>
                        </div>
                        <!--end::Assigned Content-->
                      </div>
                    </div>
                    <!--end::Tabs Body-->
                  </div>
                  <!--end::Tabs-->
                </div>
              </div>
              <!--end::Body-->
            </div>
          </div>
          <script>
                  function opensidebar()
                  {
                      $('#kt_activitiesHiring_toggle').trigger('click');
                  } 	
               </script>
          <!--end::HM/CP sidebar-->
          <!--begin::Client sidebar-->
          <div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
            <div class="card shadow-none rounded-0 w-100">
              <!--begin::Header-->
              <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                  <div class=" d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
                      <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">H</div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">HCL Technologies</a> </div>
                  </div>
                  </h3>
                  <!--begin::seprator-->
                  <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                  <!--end::seprator-->
                </div>
                <!--end::Header-->
                <div class="card-toolbar">
                  <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesITeam_close">
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
              <!--begin::Body-->
              <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
                <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
                  <!--begin::Client card-->
                  <div class="card card-custom mx-2">
                    <!--begin::Client header-->
                    <div class="card-header cursor-pointer">
                      <!--begin::Card title-->
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Client</h3>
                      </div>
                      <!--end::Card title-->
                      <!--begin::Action-->
                      <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none" data-toggle="modal" data-target="#add_team_member">Add Member</a>
                      <!--end::Action-->
                    </div>
                    <!--end::Client header-->
                    <!--begin::Client Content-->
                    <div class="card-body py-7">
                      <!--begin::Table-->
                      <table class="table align-middle table-row-dashed fs-6 gy-5" id="Client_Table">
                        <!--begin::Table head-->
                        <thead>
                          <!--begin::Table row-->
                          <tr class="fw-bolder text-muted">
                            <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-target="#Client_Table .form-check-input-client" value="1">
                              </div></th>
                            <th class="min-w-150px">Name</th>
                            <th class="min-w-150px">Role</th>
                            <th class="min-w-150px">Contact</th>
                            <th class="min-w-150px">Action</th>
                          </tr>
                          <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">
                          <!--begin::Table row-->
                          <tr class="">
                            <!--begin::Checkbox-->
                            <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input form-check-input-client" type="checkbox" value="1">
                              </div></td>
                            <!--end::Checkbox-->
                            <!--begin::Name-->
                            <td><div class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                  <div class="symbol-label"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-11.jpg" alt="team Img" class="w-100"> </div>
                                  </a> </div>
                                <!--end::Avatar-->
                                <!--begin::Name details-->
                                <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Sobha</span> <span class="text-muted fw-bold d-block fs-7">Recruiter</span> </div>
                                <!--end::Name details-->
                              </div></td>
                            <!--end::Name-->
                            <!--begin::Role-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Team Leader</span> </div></td>
                            <!--begin::Role-->
                            <!--begin::Contact-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">9090909090</span> <span class="text-muted fw-bold d-block fs-7">sobha@hcl.com</span> </div></td>
                            <!--end::Contact-->
                            <!--begin::Action-->
                            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Add</a> </div></td>
                            <!--end::Action-->
                          </tr>
                          <!--end::Table row-->
                          <!--begin::Table row-->
                          <tr class="">
                            <!--begin::Checkbox-->
                            <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input form-check-input-client" type="checkbox" value="1">
                              </div></td>
                            <!--end::Checkbox-->
                            <!--begin::Name-->
                            <td><div class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                  <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">A</div>
                                  </a> </div>
                                <!--end::Avatar-->
                                <!--begin::Name details-->
                                <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Anjali</span> <span class="text-muted fw-bold d-block fs-7">Recruiter</span> </div>
                                <!--end::Name details-->
                              </div></td>
                            <!--end::Name-->
                            <!--begin::Role-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Interviewer</span> </div></td>
                            <!--begin::Role-->
                            <!--begin::Contact-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">8989898989</span> <span class="text-muted d-block fs-7">anjali@hcl.com</span> </div></td>
                            <!--end::Contact-->
                            <!--begin::Action-->
                            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                            <!--end::Action-->
                          </tr>
                          <!--end::Table row-->
                        </tbody>
                        <!--end::Table body-->
                      </table>
                      <!--end::Table-->
                    </div>
                    <!--end::Client Content-->
                  </div>
                  <!--end::Client card-->
                </div>
              </div>
              <!--end::Body-->
            </div>
          </div>
          <script>
                  function opensidebar()
                  {
                      $('#kt_activitiesClient_toggle').trigger('click');
                  }
               </script>
          <!--end::Client sidebar-->
          <!--begin::Internal Team sidebar-->
          <div id="kt_activitiesITeam" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesITeam_toggle" data-kt-drawer-close="#kt_activitiesITeam_close">
            <div class="card shadow-none rounded-0 w-100">
              <!--begin::Header-->
              <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                  <div class=" d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
                      <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">4</div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">4Bell Technology</a>
                      <!-- <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7">Veebrij</a> -->
                    </div>
                  </div>
                  </h3>
                  <!--begin::seprator-->
                  <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                  <!--end::seprator-->
                </div>
                <!--end::Header-->
                <div class="card-toolbar">
                  <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesITeam_close">
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
              <!--begin::Body-->
              <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
                <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
                  <!--begin::Team card-->
                  <div class="card card-custom mx-2">
                    <!--begin::Team header-->
                    <div class="card-header cursor-pointer">
                      <!--begin::Card title-->
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Team</h3>
                      </div>
                      <!--end::Card title-->
                      <!--begin::Action-->
                      <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none" data-toggle="modal" data-target="#add_team_member">Add Member</a>
                      <!--end::Action-->
                    </div>
                    <!--end::Team header-->
                    <!--begin::Team Content-->
                    <div class="card-body py-7">
                      <!--begin::Table-->
                      <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table">
                        <!--begin::Table head-->
                        <thead>
                          <!--begin::Table row-->
                          <tr class="fw-bolder text-muted">
                            <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-target="#Iteam_Table .form-check-input-Item" value="1">
                              </div></th>
                            <th class="min-w-150px">Name</th>
                            <th class="min-w-150px">Role</th>
                            <th class="min-w-150px">Status</th>
                            <th class="min-w-150px">Action</th>
                          </tr>
                          <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">
                          <!--begin::Table row-->
                          <tr>
                            <!--begin::Checkbox-->
                            <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input form-check-input-Item" type="checkbox" value="1">
                              </div></td>
                            <!--end::Checkbox-->
                            <!--begin::Name-->
                            <td><div class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                  <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">P</div>
                                  </a> </div>
                                <!--end::Avatar-->
                                <!--begin::Name details-->
                                <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Priyanka</span> <span class="text-muted fw-bold d-block fs-7">Recruiter</span> </div>
                                <!--end::Name details-->
                              </div></td>
                            <!--end::Name-->
                            <!--begin::Role-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Hiring Manager</span> </div></td>
                            <!--begin::Role-->
                            <!--begin::Status-->
                            <td><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Active</span> </div></td>
                            <!--end::Status-->
                            <!--begin::Action-->
                            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Add</a> </div></td>
                            <!--end::Action-->
                          </tr>
                          <!--end::Table row-->
                          <!--begin::Table row-->
                          <tr>
                            <!--begin::Checkbox-->
                            <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input form-check-input-Item" type="checkbox" value="1">
                              </div></td>
                            <!--end::Checkbox-->
                            <!--begin::Name-->
                            <td><div class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                  <div class="symbol-label"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-1.jpg" alt="team Img" class="w-100"> </div>
                                  </a> </div>
                                <!--end::Avatar-->
                                <!--begin::Name details-->
                                <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Soni</span> <span class="text-muted fw-bold d-block fs-7">Recruiter</span> </div>
                                <!--end::Name details-->
                              </div></td>
                            <!--end::Name-->
                            <!--begin::Role-->
                            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Developer</span> </div></td>
                            <!--begin::Role-->
                            <!--begin::Status-->
                            <td><div class="d-flex flex-column"> <span class="text-danger fw-bolder fs-6">Inactive</span> </div></td>
                            <!--end::Status-->
                            <!--begin::Action-->
                            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                            <!--end::Action-->
                          </tr>
                          <!--end::Table row-->
                        </tbody>
                        <!--end::Table body-->
                      </table>
                      <!--end::Table-->
                    </div>
                    <!--end::Team Content-->
                  </div>
                  <!--end::Team card-->
                </div>
              </div>
              <!--end::Body-->
            </div>
          </div>
          <script>
                  function opensidebar()
                  {
                      $('#kt_activitiesITeam_toggle').trigger('click');
                  }
               </script>
          <!--end::Internal Team sidebar-->
          <!--begin::Staffing Partners sidebar-->
          <div id="kt_activitiesStaffing" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesStaffing_toggle" data-kt-drawer-close="#kt_activitiesStaffing_close">
            <div class="card shadow-none rounded-0 w-100">
              <!--begin::Header-->
              <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                  <h3 class="card-title fw-bolder text-dark">Staffing Partners</h3>
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
              <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
                <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
                  <!--begin::Tabs-->
                  <div class="card card-custom mx-2">
                    <!--Begin::Tabs Header-->
                    <div class="card-header card-header-tabs-line">
                      <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#recommended"> <span class="nav-text font-weight-bold">Recommended</span> </a> </li>
                          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#registered"> <span class="nav-text font-weight-bold">Registered</span> </a> </li>
                          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#assigned"> <span class="nav-text font-weight-bold">Assigned</span> </a> </li>
                        </ul>
                      </div>
                    </div>
                    <!--end::Tabs Header-->
                    <!--Begin::Tabs Body-->
                    <div class="card-body py-0">
                      <div class="tab-content pt-5">
                        <!--begin::Recommended Content-->
                        <div class="tab-pane active show" id="recommended" role="tabpanel">
                          <!--begin::Recommended header-->
                          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
                            <!--begin::Card title-->
                            <div class="card-title m-0 d-block">
                              <h3 class="fw-bolder m-0">Recommended</h3>
                              <!-- <p class="text-muted fs-6 fw-bold pt-2">Top 10</p> -->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Action-->
                            <!-- <a href="{{route('staffing_company.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a> -->
                            <!--end::Action-->
                          </div>
                          <!--end::Recommended header-->
                          <div class="card-body px-0 py-7">
                            <!--begin:Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                              <!--begin::Table head-->
                              <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input-reco" value="1" />
                                    </div></th>
                                  <th class="min-w-150px">Name</th>
                                  <th class="min-w-80px">Match</th>
                                  <th class="min-w-50px">Similar Req.</th>
                                  <th class="min-w-50px">Profiles</th>
                                  <th class="min-w-100px">Hiring Type</th>
                                  <th class="min-w-100px">Status</th>
                                  <th class="min-w-100px">Action</th>
                                </tr>
                                <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <!--begin::Table body-->
                              <tbody class="text-gray-600 fw-bold">
                                <!--begin::Table row-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name=-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol-label border-radius-5"> <img src="https://cpb-us-w2.wpmucdn.com/blog.smu.edu/dist/0/141/files/2017/12/94e9035a11b4869bec9266f730084bac.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Adobe</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Staffing Partners</a> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Progressbar-->
                                  <td class=""><div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">78%</span> </div>
                                      <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 78%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7">Wordpress</span> </div></td>
                                  <!--end::Progressbar-->
                                  <!--begin::Similar Req.-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">567</span> </div></td>
                                  <!--end::Similar Req.-->
                                  <!--begin::Profiles-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">7,651</span> </div></td>
                                  <!--end::Profiles-->
                                  <!--begin::Hiring Type-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Full Time</span> </div></td>
                                  <!--end::Hiring Type-->
                                  <!--begin::Status-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                                  <!--end::Status-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-->
                                <!--begin::Table row-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name=-->
                                  <td class=" d-flex align-items-center"><!--begin::Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                                      <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">H</div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="HCL Technologies">HCL Technologies</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Recruiter</a> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Progressbar-->
                                  <td class=""><div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">20%</span> </div>
                                      <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7">React JS</span> </div></td>
                                  <!--end::Progressbar-->
                                  <!--begin::Similar Req.-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30</span> </div></td>
                                  <!--end::Similar Req.-->
                                  <!--begin::Profiles-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">456</span> </div></td>
                                  <!--end::Profiles-->
                                  <!--begin::Hiring Type-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                                  <!--end::Hiring Type-->
                                  <!--begin::Status-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-muted fw-bolder fs-6">Unregistered</span> </div></td>
                                  <!--end::Status-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Hire</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-->
                                <!--begin::Table row-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name=-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol-label border-radius-5"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Tehcnology</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Hiring Manager</a> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Progressbar-->
                                  <td class=""><div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">100%</span> </div>
                                      <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7">Core Java</span> </div></td>
                                  <!--end::Progressbar-->
                                  <!--begin::Similar Req.-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">57</span> </div></td>
                                  <!--end::Similar Req.-->
                                  <!--begin::Profiles-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">651</span> </div></td>
                                  <!--end::Profiles-->
                                  <!--begin::Hiring Type-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                                  <!--end::Hiring Type-->
                                  <!--begin::Status-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-muted fw-bolder fs-6">Unregistered</span> </div></td>
                                  <!--end::Status-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-->
                                <!--begin::Table row-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name=-->
                                  <td class=" d-flex align-items-center"><!--begin::Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                                      <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">T</div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Ibm-test2">Tech Mahindra</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Team Leader</a> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Progressbar-->
                                  <td class=""><div class="d-flex flex-column w-100 me-2">
                                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">70%</span> </div>
                                      <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7">Node JS</span> </div></td>
                                  <!--end::Progressbar-->
                                  <!--begin::Similar Req.-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">80</span> </div></td>
                                  <!--end::Similar Req.-->
                                  <!--begin::Profiles-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">56</span> </div></td>
                                  <!--end::Profiles-->
                                  <!--begin::Hiring Type-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Full Time</span> </div></td>
                                  <!--end::Hiring Type-->
                                  <!--begin::Status-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                                  <!--end::Status-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Hire</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-->
                              </tbody>
                              <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                          </div>
                        </div>
                        <!--end::Recommended Content-->
                        <!--begin::Registered Content-->
                        <div class="tab-pane" id="registered" role="tabpanel">
                          <!--begin::Registered header-->
                          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
                            <!--begin::Card title-->
                            <div class="card-title m-0 d-block">
                              <h3 class="fw-bolder m-0">Registered</h3>
                              <!-- <p class="text-muted fs-6 fw-bold pt-2">Top 10</p> -->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Action-->
                            <!-- <a href="{{route('staffing_company.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a> -->
                            <!--end::Action-->
                          </div>
                          <!--end::Registered header-->
                          <!--begin::Registered Content-->
                          <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                              <!--begin::Table head-->
                              <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                  <th class="w-10px pe-0 sorting_disabled"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input-staff" value="1">
                                    </div></th>
                                  <th class="">Name</th>
                                  <th class="">Location</th>
                                  <th class="">Joining</th>
                                  <th class="">Requisitions</th>
                                  <th class="">Connected On</th>
                                  <th class="">Contact Person</th>
                                  <th class="">Action</th>
                                </tr>
                                <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <!--begin::Table body-->
                              <tbody class="">
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1">
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style=""> <a href="javascript:void(0);" class="show-detail" id="">
                                      <div class="symbol-label fs-3 bg-light-secondary text-dark border-radius-5">T</div>
                                      </a> </div>
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Tech Mahindra</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">IT Recruitment</a> </div></td>
                                  <!--end::Name-->
                                  <!--begin::Location-->
                                  <td><div class=""> <span class="text-dark fw-bolder fs-6">New Delhi</span>
                                      <div class="d-flex align-items-center text-muted"> <span class="fw-bolder d-block fs-7">New Delhi</span> &nbsp;|&nbsp; <span class="fw-bold d-block fs-7 text-uppercase">india</span> </div>
                                    </div></td>
                                  <!--end::Location-->
                                  <!--begin::Joining-->
                                  <td class=""><div class=""> <span class="text-dark fw-bolder fs-6">435</span> <span class="fw-bold d-block text-muted fs-7">10</span> </div></td>
                                  <!--end::Joining-->
                                  <!--begin::Requisitions-->
                                  <td class=""><div class=""> <span class="fw-bolder text-success fs-6">2343 Active</span>
                                      <div class="d-flex align-items-center"> <span class="fw-bold fs-7 text-warning">10 Hold</span> &nbsp;|&nbsp; <span class="fw-bold fs-7 text-danger">692 Closed</span> </div>
                                    </div></td>
                                  <!--end::Requisitions-->
                                  <!--begin::Connected On-->
                                  <td><div class=""> <span class="fw-bolder text-dark fs-6">22-Sep-2021</span> <span class="fw-bold text-muted fs-7 d-block">1 year ago</span> </div></td>
                                  <!--end::Connected On-->
                                  <!--begin::Contact Person-->
                                  <td><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                                        <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="Amit Kumar"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="More users list.."> <span class="symbol-label font-weight-bold">3+</span> </div>
                                      </div>
                                    </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Action-->
                                  <td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Assign Job</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1">
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style=""> <a href="javascript:void(0);" class="show-detail" id="">
                                      <div class="symbol-label fs-3 bg-light-secondary text-dark border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                                      </a> </div>
                                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">HCL Technologies</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">Design & Development</a> </div></td>
                                  <!--end::Name-->
                                  <!--begin::Location-->
                                  <td><div class=""> <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                                      <div class="d-flex align-items-center text-muted"> <span class="fw-bolder d-block fs-7">Uttar Pradesh</span> &nbsp;|&nbsp; <span class="fw-bold d-block fs-7 text-uppercase">india</span> </div>
                                    </div></td>
                                  <!--end::Location-->
                                  <!--begin::Joining-->
                                  <td class=""><div class=""> <span class="text-dark fw-bolder fs-6">1,321</span> <span class="fw-bold d-block text-muted fs-7">853</span> </div></td>
                                  <!--end::Joining-->
                                  <!--begin::Requisitions-->
                                  <td class=""><div class=""> <span class="fw-bolder text-success fs-6">10 Active</span>
                                      <div class="d-flex align-items-center"> <span class="fw-bold fs-7 text-warning">64 Hold</span> &nbsp;|&nbsp; <span class="fw-bold fs-7 text-danger">02 Closed</span> </div>
                                    </div></td>
                                  <!--end::Requisitions-->
                                  <!--begin::Connected On-->
                                  <td><div class=""> <span class="fw-bolder text-dark fs-6">01-Oct-2021</span> <span class="fw-bold text-muted fs-7 d-block">9 years ago</span> </div></td>
                                  <!--end::Connected On-->
                                  <!--begin::Contact Person-->
                                  <td><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                                        <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="Sobha"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                                      </div>
                                    </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Action-->
                                  <td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Assigned</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                              </tbody>
                              <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                          </div>
                          <!--end::Requisitions Content-->
                        </div>
                        <!--end::Requisitions Content-->
                        <!--begin::Assigned Content-->
                        <div class="tab-pane" id="assigned" role="tabpanel">
                          <!--begin::Working header-->
                          <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                              <h3 class="fw-bolder m-0">Assigned</h3>
                            </div>
                            <!--end::Card title-->
                          </div>
                          <!--end::Working header-->
                          <div class="card-body px-0 py-7">
                            <!--begin:Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                              <!--begin::Table head-->
                              <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                  <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-staff" value="1" />
                                    </div></th>
                                  <th class="">Name</th>
                                  <th class="">Contact Person</th>
                                  <th class="">Upload Profile</th>
                                  <th class="">Interview</th>
                                  <th class="">Joinings</th>
                                  <th class="">Action</th>
                                </tr>
                                <!--end::Table row-->
                              </thead>
                              <!--end::Table head-->
                              <!--begin::Table body-->
                              <tbody class="text-gray-600 fw-bold">
                                <!--begin::Table row-1-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail" id="kt_activities3_toggle">
                                        <div class="symbol-label fs-3 fs-3 bg-light-danger text-danger border-radius-5">T</div>
                                        </a> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">TCS</span> <span class="fw-bold text-muted d-block fs-7">IT Services</span> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Contact Person-->
                                  <td class=""><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="One"> <img alt="One" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="Two"> <img alt="Two" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="Three"> <img alt="Three" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                                      </div>
                                    </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Upload Profile-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td>
                                  <!--end::Upload Profile-->
                                  <!--begin::Interview-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td>
                                  <!--end::Interview-->
                                  <!--begin::Joinings-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td>
                                  <!--end::Joinings-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-1-->
                                <!--begin::Table row-2-->
                                <tr class="">
                                  <!--begin::Checkbox-->
                                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1" />
                                    </div></td>
                                  <!--end::Checkbox-->
                                  <!--begin::Name-->
                                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                                      <div class="symbol-label border-radius-5"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="logo-first-letter" class="border-radius-5 w-100"> </div>
                                      </a> </div>
                                    <!--end::Avatar-->
                                    <!--begin::User details-->
                                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">4Bell Technology</span> <span class="fw-bold text-muted d-block fs-7">IT Consultancy</span> </div>
                                    <!--begin::User details-->
                                  </td>
                                  <!--end::Name-->
                                  <!--begin::Contact Person-->
                                  <td class=""><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="One"> <img alt="One" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="Amita"> <span class="symbol-label bg-warning text-inverse-warning fw-bolder">A</span> </div>
                                        <div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="Priyanka"> <span class="symbol-label bg-primary text-inverse-primary fw-bolder">P</span> </div>
                                      </div>
                                    </div></td>
                                  <!--end::Contact Person-->
                                  <!--begin::Upload Profile-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td>
                                  <!--end::Upload Profile-->
                                  <!--begin::Interview-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">03</span> </div></td>
                                  <!--end::Interview-->
                                  <!--begin::Joinings-->
                                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">6</span> </div></td>
                                  <!--end::Joinings-->
                                  <!--begin::Action-->
                                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                                  <!--end::Action-->
                                </tr>
                                <!--end::Table row-2-->
                              </tbody>
                              <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                          </div>
                        </div>
                        <!--end::Assigned Content-->
                      </div>
                    </div>
                    <!--end::Tabs Body-->
                  </div>
                  <!--end::Tabs-->
                </div>
              </div>
              <!--end::Body-->
            </div>
          </div>
          <script>
                  function opensidebar()
                  {
                      $('#kt_activitiesStaffing_toggle').trigger('click');
                  } 	
               </script>
          <!--begin::Table-->
          <!--table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                  <thead>
                   <tr class="fw-bolder text-muted">
                     <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                         <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                       </div></th>
                     <th class="">Requisition</th>
                     <th class="">Exp.</th>
                     <th class="">Salary</th>
                     <th class="">Location</th>
                     <th class="">Status</th>
                     <th class="">Date</th>
                     <th class="">HM | CP</th>
                     <th class="">S.Partners</th>
                     <th class="">TOT</th>
                     <th class="">SOU</th>
                     <th class="">SCR</th>
                     <th class="">EVA</th>
                     <th class="">NEO</th>
                     <th class="">OFF</th>
                     <th class="">JOI</th>
                     <th class="">REJ</th>
                   </tr>
                  </thead>
                  <tbody class="text-gray-600 fw-bold">
                   <tr class="">
                     <td class="">
                       <div class="form-check form-check-sm form-check-custom form-check-solid">
                         <input class="form-check-input" type="checkbox" value="1" />
                       </div>
                     </td>
                     <td class="d-flex align-items-center border-0">
                       <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                         <a href="javascript:void(0);">
                           <div class="symbol-label border-radius-5">
                             <img src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png" alt="Company Logo" class="border-radius-5 w-100">
                           </div>
                         </a>
                       </div>
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Wordpress Developer</a>
                         <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">14+</span>
                         <span class="text-muted fw-bold text-muted d-block fs-7">8+</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">18 LPA</span>
                         <span class="fw-bold d-block fs-7">12 LPA</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                         <div class="d-flex align-items-center">
                           <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp;
                           <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                         <div>            
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a>
                         <span class="fw-bold text-muted d-block fs-7">R1001</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">05-Jul-2021</span>
                         <span class="text-muted fw-bold d-block fs-7">3,974</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod">
                             <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list..">
                             <span class="symbol-label font-weight-bold">1+</span>
                           </div>
                         </div>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Amit Kumar"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">3+</span> </div>
                         </div>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column"> 
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">3,294</a> 
                         <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">58</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">34</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">29</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">32</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">384</a>
                       </div>
                     </td>
                   </tr>
                   <tr class="">
                     <td class="">
                       <div class="form-check form-check-sm form-check-custom form-check-solid">
                         <input class="form-check-input" type="checkbox" value="1" />
                       </div>
                     </td>
                     <td class="d-flex align-items-center border-0">
                       <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
                         <a href="javascript:void(0);">
                           <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">U</div>
                         </a>
                       </div>
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a>
                         <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">22+</span>
                         <span class="text-muted fw-bold text-muted d-block fs-7">18+</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">50 LPA</span>
                         <span class="fw-bold d-block fs-7">30 LPA</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                       <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                       <div class="d-flex align-items-center">
                         <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                       <div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <a href="javascript:void(0);" class="fs-6 hold-bg text-hover-warning">Hold</a>
                         <span class="fw-bold text-muted d-block fs-7">R1002-Client</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">28-Apr-2021</span>
                         <span class="text-muted fw-bold d-block fs-7">774</span>
                       </div>
                     </td>
                     <td class="">
                       <div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod">
                             <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg">
                           </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Pawan">
                             <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg">
                           </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list..">
                             <span class="symbol-label font-weight-bold">2+</span>
                           </div>
                         </div>
                       </div>
                     </td>
                     <td class=""><div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Abinesh"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                         </div>
                       </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">23</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">24</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">83</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">49</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                   </tr>
                   <tr class="">
                     <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                         <input class="form-check-input" type="checkbox" value="1" />
                       </div></td>
                     <td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                         <div class="symbol-label border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                         </a> </div>
                       <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Frontend Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl Technologies</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30+</span> <span class="text-muted fw-bold text-muted d-block fs-7">27+</span> </div></td>
                     <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70 LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> </div></td>
                     <td class=""><div class="d-flex flex-column">
                       <span class="text-dark fw-bolder fs-6">Noida</span>
                       <div class="d-flex align-items-center">
                       <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                       <div> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R1003-Custom</span> </div></td>
                     <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                     <td class=""><div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Priyanka"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                         </div>
                       </div></td>
                     <td class=""><div class="d-flex flex-column">
                         <div class="symbol-group symbol-hover">
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Three"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                           <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">3+</span> </div>
                         </div>
                       </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">345</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">94</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">03</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">45</a> </div></td>
                     <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">540</a> </div></td>
                   </tr>
                  </tbody>
                </table-->
          <!--end::Table-->
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
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
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
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"> &nbsp; </div>
    <!--begin::Body-->
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
     var url       		= "{{ route('staffing_company.jobs.priority',':id') }}";
     url         		= url.replace(':id', job_id);
     $('#modelHeading_priority').html('Schedule');
     $.ajaxModal('#scheduleDetailModal_priority', url);
     });
   });  
   
   function editJobStatus(job_id)
   {
     var id        = job_id;
     if(typeof scheduleDate === "undefined")
     {
        scheduleDate  = '';
     }
     var url       = "{{ route('staffing_company.jobs.editJobStatus',':id') }}";
     url         = url.replace(':id', id);
     $('#modelHeadingStatus').html('Schedule');
     $.ajaxModal('#scheduleDetailModalStatus', url);
   }
</script>
<script>
   var table = $('#myTable').dataTable({
   
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering
   			
   			"bLengthChange" : false, //thought this line could hide the LengthMenu
   			//"bInfo":false, 
   			sDom			: 'lrtip',
   			//"bFilter" : false,
   			//"bInfo"   : true,
   			// "bLengthChange": false,
   			oLanguage 		: {
   									sLengthMenu: "_MENU_",
   								},
   								
   	 		/*responsive      : true,
           	processing      : true,
           	serverSide      : true,
           	"bFilter"       : false,
           	stateSave       : true,
           	"bInfo"         : true,
           	"bLengthChange" : false,
   			oLanguage:
   			{
   			  sLengthMenu: "_MENU_",
   			},
   
           	//"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
           	"pageLength": 10,
   	
   			colReorder: true,*/
   			/*buttons:
   					[{
   						text      			:"Visibility Control",
   						extend      		: 'colvis',
   								collectionLayout	: 'two-column',
   								colReorder    		: true,
   								columnText    		: function ( dt, idx, title ) {
   							return (idx+1)+': '+title;
   						},
   						dropup      		: true
   				}],*/
   				/*createdRow: function ( row, data, index )
   				{
   					var suffix    = data['status'];
   					var extnumber = parseInt(suffix.replace(/[^0-9]/g,''));
   					if ( data['status'] == '<a href="Javascript:void(0);" onclick="editJobStatus('+extnumber+');">Closed</a>' )
   				  {
   					$('td', row).eq(2).addClass('text-danger','');
   						$('td', row).eq(3).addClass('text-black','font-weight-normal');
   					}
   					else if ( data['status'] == '<a href="Javascript:void(0);" onclick="editJobStatus('+extnumber+');">Hold</a>' )
   				  {
   						$('td', row).eq(2).addClass('text-warning','');
   						$('td', row).eq(3).addClass('text-black','font-weight-normal');
   					} 
   					else
   				  {
   					$('td', row).eq(2).addClass('text-success','');
   						$('td', row).eq(3).addClass('text-black','font-weight-normal');
   					}
   				
   				  $('td', row).eq(11).addClass('custom-bdr-right');
   				},  */     
   			 
   
   
      					ajax: {'url' : '{!! route('staffing_company.jobs.data') !!}',
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
   				
   				/*"fnRowCallback" : function(nRow, aData, iDisplayIndex)
   				{
   				  $("td:first", nRow).html(iDisplayIndex +1);
   				  return nRow;
   				},*/
   
   				"columnDefs":
   						  [ 
   									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "50px", "targets": 3 },		
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
   					        
   				  	   
   				]
   				
   				}); 
   
   				new $.fn.dataTable.FixedHeader( table );
   
   
   
   
   	//table.columns.adjust().draw();   
   	//new $.fn.dataTable( table );
   	//table.columns.adjust().draw();
   
   /*table.on('click', '.show-detail', function ()
   {
   		alert('call me');
       $(".right-sidebar").slideDown(50).addClass("shw-rside");
       var id    = $(this).data('row-id');
       var url   = "{{ route('staffing_company.jobs.viewmore',':id') }}";
       url     = url.replace(':id', id);
       $.easyAjax({
   		type  : 'GET',
   		url   : url,
     	success : function (response) {
   
   	  if (response.status == "success")
         {
           $('#right-sidebar-content').html(response.view);
   		  }
   		}
     });
   }); */
   
   
   table.on('click', '.show-detail', function ()
   	{
   	
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('staffing_company.jobs.viewmore',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		//alert(url); 
   		//$.easyAjax({
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
   	
   
   
   
   
   $('#filter-company, #filter-status').change(function ()
   {
     table._fnDraw();
   })
   
   $('#column_visibility_control_btn').click(function()
   {
     //alert("call");
     var id  = 12;
     if(typeof scheduleDate === "undefined")
     {
       scheduleDate  = '';
     }
     var url       = "{{ route('staffing_company.jobs.load_visibility_control',':id') }}";
     url         = url.replace(':id', id);
     $('#modelHeading_vis').html('Schedule');
     $.ajaxModal('#scheduleDetailModal_vis', url);
   });
   
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
       var url = "{{ route('staffing_company.jobs.destroy',':id') }}";
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
     var url       	= "{{ route('staffing_company.jobs.updatestatus',':id') }}";
     url         	= url.replace(':id', id);
     var token       = "{{ csrf_token() }}";
     $.easyAjax({
   	  type		: 'POST',
   	  url		: url,
   	  data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
   	  success	: function (response)
       {
   		  table._fnDraw();
   		}
     });
   }
   
   function exportExcel()
   {
   	var table 		        = $('#myTable').dataTable();
   	var length 		        = table.fnGetData().length; 	
   	var oSettings         = table.fnSettings();
     var start             = oSettings._iDisplayStart;					
     var url 			        = '{{ route('staffing_company.jobs.export', [':start' , ':length']) }}';
     url 					        = url.replace(':start', start);
   	url 					        = url.replace(':length', length);
   	window.location.href  = url;
   }
</script>
@endpush