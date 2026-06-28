@extends('layouts.staffing_company_layout')
<style>
#send_email .modal-dialog
{
  margin: .3rem auto!important;
}
#fwd_resume .modal-dialog
{
  margin: .3rem auto!important;
}
/*begin::Media query*/
@media  only screen and (min-width: 900px) and (max-width: 2560px)
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

@media  only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesApplication
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesApplication
  {
    width: 1200px;
  }
}
/*end::Media query*/
</style>
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Candidates-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary-800">All Candidates</a> </h5>
        <span class="text-gray-600" id="total_record">0</span> </div>
      <!--end::All Candidates-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Similar Candidates-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Similar Candidates</a> </h5>
        <span class="text-gray-600">222</span> </div>
      <!--end::Similar Candidates-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="before-event d-flex align-items-center position-relative my-1 me-3">
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
        <div class="before-event d-flex justify-content-end" data-kt-user-table-toolbar="base">
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
          <!--begin::Add Candidate-->
          <a href="{{ route('staffing_company.job-applications.create') }}" type="button" class="d-none btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
          <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24"/>
              <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Add Candidate </a>
          <!--end::Add Candidate-->
        </div>
        <!--end::Toolbar-->
        <!--begin::On seleced events/buttons-->
        <div class="after-event d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="base">
          <!--begin::Total number of selected rows-->
          <div class="fw-bolder text-dark me-5"> <span class="me-2" data-kt-view-roles-table-select="selected_count">220</span>Selected </div>
          <!--end::Total number of selected rows-->
          <!--begin::Forward CV-->
          <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3" data-toggle="modal" data-target="#fwd_resume">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"/>
              <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Fwd Resume </a>
          <!--end::Forward CV-->
          <!--begin::Send Email-->
          <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3" data-toggle="modal" data-target="#send_email">
          <!--begin::Svg Icon -->
          <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
              <path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Send Email </a>
          <!--end::Send Email-->
          <!--begin::Copy-->
          <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3" data-toggle="modal" data-target="#copy">
          <!--begin::Svg icon-->
          <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z" fill="#000000" fill-rule="nonzero"/>
              <path d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z" fill="#000000" opacity="0.3"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Copy </a>
          <!--end::Copy-->
          <!--begin::Move-->
          <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3" data-toggle="modal" data-target="#move">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M8,13.1668961 L20.4470385,11.9999863 L8,10.8330764 L8,5.77181995 C8,5.70108058 8.01501031,5.63114635 8.04403925,5.56663761 C8.15735832,5.31481744 8.45336217,5.20254012 8.70518234,5.31585919 L22.545552,11.5440255 C22.6569791,11.5941677 22.7461882,11.6833768 22.7963304,11.794804 C22.9096495,12.0466241 22.7973722,12.342628 22.545552,12.455947 L8.70518234,18.6841134 C8.64067359,18.7131423 8.57073936,18.7281526 8.5,18.7281526 C8.22385763,18.7281526 8,18.504295 8,18.2281526 L8,13.1668961 Z" fill="#000000"/>
              <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M4,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L4,8 C3.44771525,8 3,7.55228475 3,7 C3,6.44771525 3.44771525,6 4,6 Z" fill="#000000" opacity="0.3"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Move </a>
          <!--end::Move-->
          <!--begin::Export-->
          <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
            <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
            <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
          </svg>
          </span>
          <!--end::Svg Icon-->
          Export </a>
          <!--end::Export-->
        </div>
        <!--end::On selected events/buttons-->
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
        <div class="card-body pt-3 px-5"> @if(in_array("view_job_applications", $userPermissions))
          <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Contacts</th>
                <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                <th class="">Salary</th>
                <th class="" data-toggle="tooltip" title="Notice Period">NP</th>
                <th class="">Qualifaction</th>
                <th class="">Location</th>
                <th class="">Source</th>
                <th class="" data-toggle="tooltip" title="Upload Date">Date</th>
                <th class="">Requisitions</th>
                <th class="">Stage</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-600 fw-bold">
            </tbody>
            <!--end::Table body-->
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
<!--begin::Fwd Resume - Modal-->
<div class="modal fade" id="fwd_resume" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Forward Resume</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form fv-plugins-bootstrap fv-plugins-framework" id="" method="post">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
          <div data-kt-element="options">
            <div class="py-5">
              <!--begin::Template-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                <div class="col-lg-10 col-xl-10">
                  <select name="template" class="form-select form-select-solid" aria-label="Select template">
                    <option value="">Select Email Template...</option>
                    <option value="1">Email Template - 1</option>
                    <option value="2">Email Template - 2</option>
                    <option value="3">Email Template - 3</option>
                    <option value="3">Email Template - 4</option>
                    <option value="3">Email Template - 5</option>
                  </select>
                  <span class="form-text text-muted">Please select email template</span> </div>
              </div>
              <!--end::Template-->
              <!--begin::From email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="from_email" id="" placeholder="Enter email" value="sonika@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::From email-->
              <!--begin::To email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="to_email" id="" placeholder="Enter email" value="amit@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::To email-->
              <!--begin::CC email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="cc_email" id="" placeholder="Enter email" value="abu@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::CC email-->
              <!--begin::Subject-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="subject" id="" placeholder="Enter subject" value="" >
                  <span class="form-text text-muted">Please enter subject</span> </div>
              </div>
              <!--end::Subject-->
              <!--begin::Body-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                <div class="col-lg-10 col-xl-10">
                  <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">
                      <h1>Quick and Simple TinyMCE 5 Integration</h1>
                      <p>Here goes the&nbsp;<a href="#">Minitial content of the editor</a>. Lorem Ipsum is simply dummy text of the&nbsp;<em>printing and typesetting</em>&nbsp;industry.</p>
                      <blockquote>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</blockquote>
                      <h3 style="text-align: right;">Flexible &amp; Powerful</h3>
                      <p style="text-align: right;"><strong>Lorem Ipsum has been the industry's</strong>&nbsp;standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                      <ul>
                          <li>List item 1</li>
                          <li>List item 2</li>
                          <li>List item 3</li>
                          <li>List item 4</li>
                      </ul>
                  </textarea>
                  <textarea class="form-control form-control-solid" row="10" placeholder="TinyMCE editor is not working...!"></textarea>
                  <span class="form-text text-muted">Please enter email content</span> </div>
              </div>
              <!--end::Body-->
              <!--begin::Files-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Files</label>
                <div class="col-xl-10 col-lg-10">
                  <!--begin::Dropzone-->
                  <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                    <!--begin::Controls-->
                    <div class="dropzone-panel mb-lg-0 mb-2"> <a class="dropzone-select btn btn-sm btn-primary me-2">Attach files</a> <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload All</a> <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a> </div>
                    <!--end::Controls-->
                    <!--begin::Items-->
                    <div class="dropzone-items wm-200px">
                      <div class="dropzone-item" style="display:none">
                        <!--begin::File-->
                        <div class="dropzone-file">
                          <div class="dropzone-filename" title="some_image_file_name.jpg"> <span data-dz-name="">some_image_file_name.jpg</span> <strong>( <span data-dz-size="">340kb</span>)</strong> </div>
                          <div class="dropzone-error" data-dz-errormessage=""></div>
                        </div>
                        <!--end::File-->
                        <!--begin::Progress-->
                        <div class="dropzone-progress">
                          <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                          </div>
                        </div>
                        <!--end::Progress-->
                        <!--begin::Toolbar-->
                        <div class="dropzone-toolbar"> <span class="dropzone-start"> <i class="bi bi-play-fill fs-3"></i> </span> <span class="dropzone-cancel" data-dz-remove="" style="display: none;"> <i class="bi bi-x fs-3"></i> </span> <span class="dropzone-delete" data-dz-remove=""> <i class="bi bi-x fs-1"></i> </span> </div>
                        <!--end::Toolbar-->
                      </div>
                    </div>
                    <!--end::Items-->
                  </div>
                  <!--end::Dropzone-->
                  <!--begin::Hint-->
                  <span class="form-text text-muted">Max file size is 1MB and max number of files is 5.</span>
                  <!--end::Hint-->
                </div>
              </div>
              <!--end::Files-->
            </div>
          </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--begin::Send-->
            <button type="button" id="" class="btn btn-light-primary"> <span class="indicator-label">Send</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
            <!--end::Send-->
          </div>
          <!--<span style="position: absolute;right: 2%;"></span>-->
          <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Fwd Resume - Modal-->
<!--begin::Send Email - Modal-->
<div class="modal fade" id="send_email" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Send Email</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form fv-plugins-bootstrap fv-plugins-framework" id="" method="post">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
          <div data-kt-element="options">
            <div class="py-5">
              <!--begin::Template-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                <div class="col-lg-10 col-xl-10">
                  <select name="template" class="form-select form-select-solid" aria-label="Select template">
                    <option value="">Select Email Template...</option>
                    <option value="1">Email Template - 1</option>
                    <option value="2">Email Template - 2</option>
                    <option value="3">Email Template - 3</option>
                    <option value="3">Email Template - 4</option>
                    <option value="3">Email Template - 5</option>
                  </select>
                  <span class="form-text text-muted">Please select email template</span> </div>
              </div>
              <!--end::Template-->
              <!--begin::From email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="from_email" id="" placeholder="Enter email" value="sonika@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::From email-->
              <!--begin::To email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="to_email" id="" placeholder="Enter email" value="amit@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::To email-->
              <!--begin::CC email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="cc_email" id="" placeholder="Enter email" value="abu@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::CC email-->
              <!--begin::Subject-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="subject" id="" placeholder="Enter subject" value="" >
                  <span class="form-text text-muted">Please enter subject</span> </div>
              </div>
              <!--end::Subject-->
              <!--begin::Body-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                <div class="col-lg-10 col-xl-10">
                  <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">
                      <h1>Quick and Simple TinyMCE 5 Integration</h1>
                      <p>Here goes the&nbsp;<a href="#">Minitial content of the editor</a>. Lorem Ipsum is simply dummy text of the&nbsp;<em>printing and typesetting</em>&nbsp;industry.</p>
                      <blockquote>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</blockquote>
                      <h3 style="text-align: right;">Flexible &amp; Powerful</h3>
                      <p style="text-align: right;"><strong>Lorem Ipsum has been the industry's</strong>&nbsp;standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                      <ul>
                          <li>List item 1</li>
                          <li>List item 2</li>
                          <li>List item 3</li>
                          <li>List item 4</li>
                      </ul>
                  </textarea>
                  <textarea class="form-control form-control-solid" row="10" placeholder="TinyMCE editor is not working...!"></textarea>
                  <span class="form-text text-muted">Please enter email content</span> </div>
              </div>
              <!--end::Body-->
              <!--begin::Upload Files-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Upload Files</label>
                <div class="col-xl-10 col-lg-10">
                  <!--begin::Dropzone-->
                  <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                    <!--begin::Controls-->
                    <div class="dropzone-panel mb-lg-0 mb-2"> <a class="dropzone-select btn btn-sm btn-primary me-2">Attach files</a> <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload All</a> <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a> </div>
                    <!--end::Controls-->
                    <!--begin::Items-->
                    <div class="dropzone-items wm-200px">
                      <div class="dropzone-item" style="display:none">
                        <!--begin::File-->
                        <div class="dropzone-file">
                          <div class="dropzone-filename" title="some_image_file_name.jpg"> <span data-dz-name="">some_image_file_name.jpg</span> <strong>( <span data-dz-size="">340kb</span>)</strong> </div>
                          <div class="dropzone-error" data-dz-errormessage=""></div>
                        </div>
                        <!--end::File-->
                        <!--begin::Progress-->
                        <div class="dropzone-progress">
                          <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                          </div>
                        </div>
                        <!--end::Progress-->
                        <!--begin::Toolbar-->
                        <div class="dropzone-toolbar"> <span class="dropzone-start"> <i class="bi bi-play-fill fs-3"></i> </span> <span class="dropzone-cancel" data-dz-remove="" style="display: none;"> <i class="bi bi-x fs-3"></i> </span> <span class="dropzone-delete" data-dz-remove=""> <i class="bi bi-x fs-1"></i> </span> </div>
                        <!--end::Toolbar-->
                      </div>
                    </div>
                    <!--end::Items-->
                  </div>
                  <!--end::Dropzone-->
                  <!--begin::Hint-->
                  <span class="form-text text-muted">Max file size is 1MB and max number of files is 5.</span>
                  <!--end::Hint-->
                </div>
              </div>
              <!--end::Upload Files-->
            </div>
          </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--begin::Send Email-->
            <button type="button" id="" class="btn btn-light-primary"> <span class="indicator-label">Send Email</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
            <!--end::Send Email-->
          </div>
          <!--<span style="position: absolute;right: 2%;"></span>-->
          <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Send Email - Modal-->
<!--begin::Copy Data - Modal-->
<div class="modal fade" id="copy" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Copy Data</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form fv-plugins-bootstrap fv-plugins-framework" id="" method="post">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
          <div data-kt-element="options">
            <div class="py-5">
              <!--begin::Client-->
              <div class="form-group row mb-8">
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5">Client</label>
                <div class="col-xl-12 col-lg-12 px-2">
                  <select name="client" class="form-select form-select-solid" aria-label="Select client">
                    <option value="">Select Client</option>
                    <option value="1">HCL Technologies</option>
                    <option value="2">Tech Mahindra</option>
                    <option value="3">4Bell Technology</option>
                    <option value="4">Google</option>
                    <option value="5">Microsoft</option>
                  </select>
                  <span class="form-text text-muted">Please select client</span> </div>
              </div>
              <!--end::Client-->
              <!--begin::Client New-->
              <div class="client-section mb-8" data-select2-id="select2-data-131-fmyd">
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5">Client</label>
                <select class="form-select" data-placeholder="Select an option" id="kt_docs_select2_users">
                  <option></option>
                  <option value="0" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-1.jpg">Emma Smith</option>
                  <option value="2" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg">Max Smith</option>
                  <option value="3" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-4.jpg">Sean Bean</option>
                  <option value="4" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-15.jpg">Brian Cox</option>
                  <option value="6" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-8.jpg">Francis Mitcham</option>
                  <option value="9" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-6.jpg">Dan Wilson</option>
                  <option value="11" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-7.jpg">Ana Crown</option>
                  <option value="13" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-17.jpg">John Miller</option>
                  <option value="15" data-kt-select2-user="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-10.jpg">Ethan Wilder</option>
                </select>
              </div>
              <!--end::Client New-->
              <!--begin::Requisitions-->
              <div class="form-group row">
                <!--begin::Requisiton Title-->
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5 py-0">Requisitions</label>
                <!--end::Requisition Title-->
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 mx-2" id="reqTable">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#reqTable .form-check-input-copy" value="1" />
                        </div></th>
                      <th class="">Requisition</th>
                      <th class="">Exp.</th>
                      <th class="">Salary</th>
                      <th class="">Location</th>
                      <th class="">Status</th>
                      <th class="">Date</th>
                      <th class="">HM | CP</th>
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
                          <input class="form-check-input form-check-input-copy" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label border-radius-5"> <img src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Wordpress Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">14+</span> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">18 LPA</span> <span class="fw-bold d-block fs-7">12 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                            <div> </div>
                          </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R1001</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span class="text-muted fw-bold d-block fs-7">3,974</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-copy" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">U</div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22+</span> <span class="text-muted fw-bold text-muted d-block fs-7">18+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">50 LPA</span> <span class="fw-bold d-block fs-7">30 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                        <div class="d-flex align-items-center">
                        <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                        <div> </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 hold-bg text-hover-warning">Hold</a> <span class="fw-bold text-muted d-block fs-7">R1002-Client</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span class="text-muted fw-bold d-block fs-7">774</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-copy" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Frontend Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl Technologies</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30+</span> <span class="text-muted fw-bold text-muted d-block fs-7">27+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70 LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">Noida</span>
                        <div class="d-flex align-items-center">
                        <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                        <div> </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R1003-Custom</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                  </tbody>
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Requisitions-->
            </div>
          </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--end::Cancel-->
            <!--begin::Copy-->
            <button type="button" id="" class="btn btn-primary">Copy</button>
            <!--end::Copy-->
          </div>
          <!--<span style="position: absolute;right: 2%;"></span>-->
          <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Copy Data - Modal-->
<!--begin::Move Data - Modal-->
<div class="modal fade" id="move" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Move Data</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form fv-plugins-bootstrap fv-plugins-framework" id="" method="post">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
          <div data-kt-element="options">
            <div class="py-5">
              <!--begin::Client-->
              <div class="form-group row mb-8">
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5">Client</label>
                <div class="col-xl-12 col-lg-12 px-2">
                  <select name="client" class="form-select form-select-solid" aria-label="Select client">
                    <option value="">Select Client</option>
                    <option value="1">HCL Technologies</option>
                    <option value="2">Tech Mahindra</option>
                    <option value="3">4Bell Technology</option>
                    <option value="4">Google</option>
                    <option value="5">Microsoft</option>
                  </select>
                  <span class="form-text text-muted">Please select client</span> </div>
              </div>
              <!--end::Client-->
              <!--begin::Requisitions-->
              <div class="form-group row">
                <!--begin::Requisiton Title-->
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5 py-0">Requisitions</label>
                <!--end::Requisition Title-->
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 mx-2" id="reqTable">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#reqTable .form-check-input-move" value="1" />
                        </div></th>
                      <th class="">Requisition</th>
                      <th class="">Exp.</th>
                      <th class="">Salary</th>
                      <th class="">Location</th>
                      <th class="">Status</th>
                      <th class="">Date</th>
                      <th class="">HM | CP</th>
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
                          <input class="form-check-input form-check-input-move" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Frontend Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl Technologies</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30+</span> <span class="text-muted fw-bold text-muted d-block fs-7">27+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70 LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">Noida</span>
                        <div class="d-flex align-items-center">
                        <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                        <div> </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R1001-Custom</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Sep-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-move" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions-->
                      <td class="d-flex align-items-center border-0"><!--begin::Avatar-->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">U</div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a> </div>
                        <!--end::User details-->
                      </td>
                      <!--end::Requisiitons-->
                      <!--begin::Experience-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22+</span> <span class="text-muted fw-bold text-muted d-block fs-7">18+</span> </div></td>
                      <!--end::Experience-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">50 LPA</span> <span class="fw-bold d-block fs-7">30 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7 text-uppercase">india</span> </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <span class="fs-6 hold-bg">Hold</span> <span class="fw-bold text-muted d-block fs-7">R1002-Client</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span class="text-muted fw-bold d-block fs-7">774</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-toggle="tooltip" data-placement="top" title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-move" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label border-radius-5"> <img src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Wordpress Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">14+</span> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">18 LPA</span> <span class="fw-bold d-block fs-7">12 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                            <div> </div>
                          </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R1003</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span class="text-muted fw-bold d-block fs-7">3,974</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-toggle="tooltip" data-placement="top" title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                    </tr>
                    <!--end::Table row-->
                  </tbody>
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Requisitions-->
            </div>
          </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--end::Cancel-->
            <!--begin::Copy-->
            <button type="button" id="" class="btn btn-primary">Move</button>
            <!--end::Copy-->
          </div>
          <!--<span style="position: absolute;right: 2%;"></span>-->
          <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Move Data - Modal-->
<!--begin::Modal - Stage and sub stage-->
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="stage_sub_stage_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="stage_body_content" id="stage_body_content"></div>
    </div>
  </div>
</div>
<!--end::Modal - Stage and sub stage-->
<!--begin::Button-->
<td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="modal"
			data-target="#stage_sub_stages">Popup</a> </div></td>
<!--end::Button-->
<!--begin::Modal - Stage & Sub Stage-->
<div class="modal fade" id="stage_sub_stages" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Stage and sub stage</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
								transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
								fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Modal body-->
      <div class="modal-body scroll-y py-0 px-lg-7">
        <div data-kt-element="options">
          <div class="pb-5">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 mt-5" id="">
              <!--begin::Table body-->
              <tbody class="text-gray-600 fw-bold">
                <!--begin::Table row-->
                <tr class="">
                  <!--begin::Name=-->
                  <td class="d-flex align-items-center border-bottom-0"><!--begin:: Avatar -->
                    <div
											class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img
														src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg"
														alt="amit kumar image" class="w-100"> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
												class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <span
												class="text-muted fw-bold d-block fs-7">Andorid Developer</span> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name=-->
                  <!--begin::Contacts-->
                  <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);"
												class="text-dark fw-bolder text-hover-primary fs-6">+91 9090909090</a> <a href="javascript:void(0);"
												class="text-muted fw-bold d-block fs-7 text-hover-primary">amit@test.com</a> </div></td>
                  <!--end::Contacts-->
                  <!--begin::Experience-->
                  <td class=""><div class="d-flex flex-column"> <span
												class="text-dark fw-bolder fs-6">15+</span> <span
												class="text-muted fw-bold d-block fs-7">8+</span> </div></td>
                  <!--end::Experience-->
                  <!--begin::Salary-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">26
                      LPA</span> <span class="text-muted fw-bold d-block fs-7">18 LPA</span> </div></td>
                  <!--end::Salary-->
                  <!--begin::Notice Period-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">1
                      Month</span> <span class="text-muted fw-bold d-block fs-7">HCL
                      Technologies</span> </div></td>
                  <!--end::Notice Period-->
                  <!--begin::Qualifaction-->
                  <td class=""><div class="d-flex flex-column"> <span
												class="text-dark fw-bolder fs-6">B.Tech</span> <span
												class="text-muted fw-bold d-block fs-7">SQL Server, Java</span> </div></td>
                  <!--end::Qualifaction-->
                  <!--begin::Location-->
                  <td class=""><div class="d-flex flex-column"> <span
												class="text-dark fw-bolder fs-6">Okhla</span> <span
												class="text-muted fw-bold d-block fs-7">ND | INDIA</span> </div></td>
                  <!--end::Location-->
                  <!--begin::Source-->
                  <td class=""><div class="d-flex flex-column">
                      <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip"
													data-placement="top" title="Sonali kaushik"> <img alt="Pic"
														src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip"
													data-placement="top" title="Abu Baker"> <img alt="Pic"
														src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">TCS</span> </div></td>
                  <!--end::Source-->
                  <!--begin::Requisitions-->
                  <td class=""><div class="d-flex flex-column"> <span
												class="text-dark fw-bolder fs-6">Marketing</span> <span
												class="text-muted fw-bold d-block fs-7">Tech Mahindra</span> </div></td>
                  <!--end::Requisitions-->
                </tr>
                <!--end::Table row-->
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
            <!--begin::Stages-->
            <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Stages</label>
            <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center">
              <div class="inreview-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="inreview">
                <label
									class="btn btn-inreview btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="inreview"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Inreview</span> </span> </label>
              </div>
              <div class="sourcing-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="sourcing">
                <label
									class="btn btn-sourcing btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="sourcing"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Sourcing</span> </span> </label>
              </div>
              <div class="screening-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" checked="checked"
									id="screening">
                <label
									class="btn btn-screening btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="screening"> <span class="fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Screening</span> </span> </label>
                <!-- <span class="form-text text-muted position-absolute">Current</span>            -->
              </div>
              <div class="evaluation-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="evaluation">
                <label
									class="btn btn-evaluation btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="evaluation"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Evaluation</span> </span> </label>
              </div>
              <div class="negonation-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="negotation">
                <label
									class="btn btn-negotation btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="negotation"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Negotation</span> </span> </label>
              </div>
              <div class="offer-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="offer">
                <label
									class="btn btn-offer btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="offer"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Offer</span> </span> </label>
              </div>
              <div class="joined-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="joined">
                <label
									class="btn btn-joined btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="joined"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Joined</span> </span> </label>
              </div>
              <div class="rejected-div d-inline-block w-12 px-2">
                <input type="radio" class="btn-check" name="stages_option" value="" id="rejected">
                <label
									class="btn btn-rejected btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
									for="rejected"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Rejected</span> </span> </label>
              </div>
            </div>
            <!--end::Stages-->
            <!--begin::Sub Stages-->
            <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Sub Stages</label>
            <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center">
              <div class="div1 d-inline-block w-14 px-2">
                <input type="radio" class="btn-check" name="sub_stages_option" value="" id="screening1">
                <label
									class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
									for="screening1"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Screening-1</span> </span> </label>
              </div>
              <div class="div2 d-inline-block w-14 px-2">
                <input type="radio" class="btn-check" name="sub_stages_option" value="" id="screening2">
                <label
									class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
									for="screening2"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Screening-2</span> </span> </label>
              </div>
              <div class="div3 d-inline-block w-14 px-2">
                <input type="radio" class="btn-check" name="sub_stages_option" value=""
									checked="checked" id="screening3">
                <label
									class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
									for="screening3"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Screening-3</span> </span> </label>
                <!-- <span class="form-text text-muted position-absolute">Current</span>   -->
              </div>
              <div class="div4 d-inline-block w-14 px-2">
                <input type="radio" class="btn-check" name="sub_stages_option" value="" id="screening4">
                <label
									class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
									for="screening4"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">Screening-4</span> </span> </label>
              </div>
            </div>
            <!--end::Sub Stages-->
          </div>
        </div>
      </div>
      <!--begin::Modal body-->
      <!--begin::Modal footer-->
      <div class="modal-footer flex-center">
        <div class="div1">
          <!--begin::Cancel-->
          <button type="close" id="" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
          <!--end::Cancel-->
          <!--begin::Submit-->
          <button type="submit" id="" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span
								class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          <!--end::Submit-->
        </div>
        <!-- style=""position: absolute;right: 2%; -->
        <div class="div2 position-absolute end-0 me-5 d-none">
          <!--begin::Rescheduled-->
          <button type="button" id="" class="btn btn-secondary">Rescheduled</button>
          <!--end::Rescheduled-->
        </div>
      </div>
      <!--end::Modal footer-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Modal - Stage & Sub Stage-->
@endsection
@push('footer-script')
<!-- <script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<!-- <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script> -->
<!--<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>-->
<!--<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>-->
<!-- <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script> -->
<!--<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>-->
<!-- <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script> -->
<!-- <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<!-- <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script> -->
<!-- <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script> -->
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- <script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- <script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script>
   "use strict";

// Class definition
var KTFormsSelect2Demo = function () {
    const exampleUsers = function () {
        // Format options
        const format = (item) => {
            if (!item.id) {
                return item.text;
            }

            var url = 'assets/media/' + item.element.getAttribute('data-kt-select2-user');
            var img = $("<img>", {
                class: "rounded-circle me-2",
                width: 26,
                src: url
            });
            var span = $("<span>", {
                text: " " + item.text
            });
            span.prepend(img);
            return span;
        }

        // Init Select2 --- more info: https://select2.org/
        $('#kt_docs_select2_users').select2({
            templateResult: function (item) {
                return format(item);
            }
        });
    }

    return {
        // Public Functions
        init: function () {
            exampleCountry();
            exampleUsers();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTFormsSelect2Demo.init();
});

</script>
<script>
   function opensidebar()
   {
   	$('#kt_activitiesClient_toggle').trigger('click');
   } 	
</script>
<script>
   $(document).ready(function() { 
        $('#inputSearch').keyup(function() {
          
            tableLoad('filter');   
            //alert("call");   
        });   
     });
   
   
   	
          $('#start-date').datepicker({
              format: 'yyyy-mm-dd'
          })
          $('#end-date').datepicker({
              format: 'yyyy-mm-dd'
          })
   
          var table;
          tableLoad('load');
          // For select 2
          $(".select2").select2();
          $('#reset-filters').click(function () {
   	
   		//$('#myTable').DataTable().column('').search('').draw();
   		var table = $('#myTable').DataTable();
   		
   		table.search( '' ).columns().search( '' ).draw();
   
   		
              $('#filter-form')[0].reset();
             // $('#filter-form').find('select').val('all').trigger('change');
              tableLoad('load');
          })
          $('#apply-filters').click(function () {
              tableLoad('filter');
          });
   	
   	
   	
   	
    function tableLoad(type) 
   	{
   
			var status 				= 'all';
			var jobs 				= 'all';
			
			var   clients 			= 'all';
			var   source 			= 'all';
			
			var   search_passport 	= 'all';
			var   search_np_id 		= 'all';
			
			var   inputSearch 		= $('#inputSearch').val();
   
              table = $('#myTable').dataTable({
                 			   
   		  
		  		responsive     	: true, 
      			processing      : true,
				ordering		: false, //disable ordering      			
      			destroy       	: true,   
      			stateSave     	: true,      
      			"bLengthChange"   : false,   
				sDom			: 'lrtip',       
				          
          		 ajax			: '{!! route('staffing_company.cvtracker.data') !!}?search='+inputSearch,
                          
                language		: {
                       					"url": "<?php echo __("app.datatable") ?>"
                   				  },           
               "fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
                       														  });
                   					},
									
   		
   		
   			
   			             
   			"columnDefs": [
   								{"targets": 0 },
   								{"targets": 1, "class":"align-items-center" },
   								{"targets": 2},
   								{"targets": 3},
   								{"targets": 4},
   								{"targets": 5},
   								{"targets": 6},
   								{"targets": 7},
   								{"targets": 8},
   								{"targets": 9},
   								{"targets": 10},
   								{"targets": 11}
   						  ],  		  		
   			columns: [
   							{ data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false },  
   							{ data: 'Name', name: 'Name', width: '15%', orderable: true },
   							{ data: 'Contacts', name: 'Contacts', width: '10%', orderable: true },
   							{ data: 'Exp', name: 'Exp', width: '5%', orderable: true },
   							{ data: 'Salary', name: 'Salary', width: '5%', orderable: true },
   							{ data: 'NoticePeriod', name: 'NoticePeriod', width: '10%', orderable: true },
   							{ data: 'Qualifaction', name: 'Qualifaction', width: '2%', orderable: true },
   							{ data: 'Location', name: 'Location', width: '2%', orderable: true },
   							{ data: 'Source', name: 'Source', width: '2%', orderable: true },
   							{ data: 'UploadDate', name: 'UploadDate', width: '10%', orderable: true },
   							{ data: 'Requisitions', name: 'Requisitions', width: '2%', orderable: true },
   							{ data: 'Stage', name: 'Stage', width: '2%', orderable: true }
   							
							
							
              			]
   	      });			     
            		new $.fn.dataTable.FixedHeader(table);
   	}
   	
	function updateApplicantStage(id)
	{
			//var id       	= $(this).data('row-id');			   
			var url    		= "{{ route('staffing_company.job-applications.stage_body_content',':id') }}";
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
	table.on('click', '.stageupdate', function ()   
      {       
			//alert("call"); 			
			var id       	= $(this).data('row-id');			   
			var url    		= "{{ route('staffing_company.job-applications.stage_body_content',':id') }}";
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
        });
			
		 
        $('body').on('click', '.right-side-toggle', function () {
   	    $("body").removeClass("control-sidebar-slide-open");
   	    })
   	
   	 table.on('click', '.show-detail', function () 
   		{
   	
   			//alert("call"); 
   		
              $(".right-sidebar").slideDown(50).addClass("shw-rside");
   
              var id 			= $(this).data('row-id');
              var url 		= "{{ route('staffing_company.job-applications.show',':id') }}";
              url 			= url.replace(':id', id);
   
   		//alert(id);
   		
   		
              $.easyAjax({
                  type: 'GET',
                  url: url,
                  success: function (response) {
                      if (response.status == "success") {
                          $('#right-sidebar-content').html(response.view);
                      }
                  }
              });
          }); 
   
   	$('body').on('click', '.ti-close', function () {
   	$("body").removeClass("control-sidebar-slide-open");
   	})
   	
	
	  
		   
	
	
	
   	$('#column_visibility_control_btn').click(function(){
   	
   			//alert("call");
   			var id 				= 12;
   		
   			if(typeof scheduleDate === "undefined") {
   				scheduleDate 	= '';
   			}
   			var url 			= "{{ route('staffing_company.cvtracker.load_visibility_control',':id') }}";
   			url 				= url.replace(':id', id);
   			$('#modelHeading_vis').html('Schedule');
   			$.ajaxModal('#scheduleDetailModal_vis', url);
   	});
   		
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
   
                      var url = "{{ route('staffing_company.cvtracker.destroy',':id') }}";
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
   
          $('.toggle-filter').click(function () {
              $('#ticket-filters').toggle('slide');
          });      
   
   	function updateCandidateStatus(scheduleDate) {
              if (typeof scheduleDate === "undefined") {
                  scheduleDate = '';
              }
              var url = '{{ route('staffing_company.cvtracker.create_status')}}?date=' + scheduleDate;
              $('#modelHeading').html('Schedule');
              $.ajaxModal('#scheduleDetailModal', url);
          }
   			
   	function getClientJob(clientId)
   		{
   				var clientId 		= clientId;
   				var url 			= "{{ route('staffing_company.cvtracker.get_client_job',':clientId') }}";
                      url 				= url.replace(':clientId', clientId);
   
   				var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
   			
                      var token 			= "{{ csrf_token() }}";
   				//alert(clientId); 
   				
   				 $.easyAjax({
                          				type: 'POST',
                          				url: url,
                          				data: {'_token': token, '_method': 'POST', 'clientId' : clientId},
   									beforeSend: function() {
   																$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   									},
                          				success: function (response) {
                           
   					 					$('#jobs').html(response.data);
   										$('.clientStatusLoader').html('');
   										tableLoad('filter');
   										
   										//alert(response.data );
   									  /*  if (response.status == "success") {
   											$.unblockUI();
   									//      swal("Deleted!", response.message, "success");
   											table._fnDraw();
   										}*/
   										
   									},
   									complete: function (data) {
   											$('.clientStatusLoader').html('');
   									 }
                      });
   				
   				
              	
   	}
   	
   	$( document ).ready(function() {
   	 	var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
   		
   		
   		$('#jobs').on('change', function() {
   			//alert("call"); 
   			$('.jobLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   			tableLoad('filter');
   		});
   		
   		$('#source').on('change', function() {
   			$('.sourceLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   			tableLoad('filter');
   		});
   		
   		
   		
   		
   		
   		$('#search_passport').on('change', function() {
   			$('.search_passportLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   			tableLoad('filter');
   		});
   		
   		$('#search_np_id').on('change', function() {
   		
   			//alert("call");
   			$('.search_np_idLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   			tableLoad('filter');
   		});
   		
   		$('#status').on('change', function() {
   		
   			
   			$('.statusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   			tableLoad('filter');
   		});
   			
   		
   		 table.on('click', '.show-resume-detail', function () {
              $(".right-sidebar").slideDown(50).addClass("shw-rside");
   
              var id = $(this).data('row-id');
              var url = "{{ route('staffing_company.cvtracker.resume',':id') }}";
              url = url.replace(':id', id);
   
   				$.easyAjax({
   								type	: 'GET',
   								url		: url,
   								success	: function (response) {
   																if (response.status == "success") {
   																	$('#right-sidebar-content').html(response.view);
   																}
   															}
   				});
         		 });
   	
   	
   	});
   	
   	function openCanadidateLightbox()
   		{
   			$(".right-sidebar").slideDown(50).addClass("shw-rside");
   
   			//alert("call"); 
   			//return false; 
   		
   			//var id 		= $(this).data('row-id');
   			var id 		= "candidate_id";
   			var url 	= "{{ route('staffing_company.cvtracker.candidateRightSideBar',':id') }}";
   			url 		= url.replace(':id', id);
   
   			$.easyAjax({
   				type: 'GET',
   				url: url,
   				success: function (response) {
   					if (response.status == "success") {
   						
   						$('#right-sidebar-content').html(response.view);
   					}
   				}
   			});
   	}
   	
   	function updateCandidateProcess(processId, applicationId)
   		{
   		var processId 		= processId;
   		var applicationId 	= applicationId;
            			
   
              		var url 	= "{{ route('staffing_company.cvtracker.candidate_process_update') }}";
                     // url 		= url.replace(':processId', processId);
   
   				var baseurl = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
   			
                      var token = "{{ csrf_token() }}";
   				
                      $.easyAjax({
                          type: 'POST',
                          url: url,
                          data: {'_token': token,  'processId' : processId, 'applicationId': applicationId },
   					beforeSend: function() {
   							$('.statusLoader_'+applicationId).html('<img src="'+baseurl+'" height="50px" width="50px">');
   					},
                          success: function (response) {
                           
   					  /*  if (response.status == "success") {
                                  $.unblockUI();
   //                                    swal("Deleted!", response.message, "success");
                                  table._fnDraw();
                              }*/
   						
                          },
   					complete: function (data) {
   					  		$('.statusLoader_'+applicationId).html('');
   					 }
                      });
   			
   	}
   			
   	function getNoticePeriodListing(notice_period)
   	{
   		//alert('call me');
   		tableLoad('filter');	
   	}
   			
   	 function exportExcel()
   		{
   	
   			var clients 		= '';
   			var jobs 			= '';
   			var source 			= '';
   			var search_passport = '';
   			var search_np_id 	= '';
   			var status 			= '';
   		
   			var start           = '';
   			var length          = '';
   			
   		 	clients				=	$('#clients').val();
   		 	jobs				=	$('#jobs').val();
   		 	source				=	$('#source').val();
   		 	search_passport		=	$('#search_passport').val();
   		 	search_np_id		=	$('#search_np_id').val();  
   		 	status				=	$('#status').val();
   		
   		
   
   			var table 			= $('#myTable').dataTable();
   			var length 			= table.fnGetData().length;
   		
   		
   			var oSettings 		= table.fnSettings();
   			var start     		= oSettings._iDisplayStart;
   		
   		//alert(start);
   		//alert(oSettings._iDisplayStart); return false; $('#myTable').dataTable().fnGetData()
   		
   		var url 				= '{{ route('staffing_company.cvtracker.export', [':clients', ':jobs', ':source', ':search_passport', ':search_np_id', ':status' , ':start' , ':length']) }}';
   		url 					= url.replace(':clients', clients);
   		url 					= url.replace(':jobs', jobs);
   		url 					= url.replace(':source', source);
   		url 					= url.replace(':search_passport', search_passport);
   		url 					= url.replace(':search_np_id', search_np_id);
   		url 					= url.replace(':status', status);
   		
   		url 					= url.replace(':start', start);
   		url 					= url.replace(':length', length);
   		//alert(url); 
   		//return false;
              window.location.href = url;
   		
   		
   		
          }
   	
   	$('body').on('click', '#AddCandidate', function(){
   		var url					=	'{{ route('staffing_company.job-applications.create') }}';
   		window.location.href 	=	url;			
   	});
   			
   
      
</script>
@endpush