@extends('layouts.demo_admin_app')
@push('head-script')

<!--begin::Metronic-7 Library-->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!--end::Metronic-7 Library-->

<!--begin::Date picker-->
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css'>
<!--end::Date picker-->

<style>
body
{
  overflow-x:hidden;
  padding: 0!important;
  margin: 0!important;
}
.border-bottom-dark11
{
  border-bottom: 1px solid #e3e4ea;
}
.table th, .table td
{
  vertical-align: middle;
}
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endpush 
@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
   <!--begin::Container-->
   <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
         <!--begin::Date format-->
         <div class="d-flex flex-column" data-toggle="modal" data-target="#scheduleDetailModalCustomReportFilter">
            <!-- <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
               <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1">Report</a>
            </h1> -->
            <span class="text-gray-600 d-none">920</span>
            <div class="d-inline-flex custom_report_filter1 text-hover-primary">
              <span class="cursor-pointer" style="font-size:20px;" id="lbl_start_date"><?php echo date('d-M, Y', strtotime('-7 days'));  ?></span>
              <span class="cursor-pointer" style="font-size:20px;">&nbsp;-&nbsp;</span>
              <span class="cursor-pointer" style="font-size:20px;" id="lbl_end_date">
                <?php echo date("d-M, Y"); ?>&nbsp;<i class="fa fa-calendar-alt cursor-pointer fa-1x"></i>
              </span>
            </div>
         </div>
         <!--end::Date format-->
         <!--begin::Separator-->
         <!-- <span class="h-20px border-gray-200 border-start mx-4"></span> -->
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
            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
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
                  <!--end::Svg Icon-->Filter
               </button>
               <!--end::Filter-->
            </div>
            <!--end::Toolbar-->
         </div>
      </div>
      <!--end::Actions-->
   </div>
   <!--end::Container-->
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Charts Row-1-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-xxl px-6">
      <div class="row">        
        <!--begin::Card-->
        <div class="card card-custom mb-4 px-0">
          <div class="card-header">
            <div class="card-title">
              <h3 class="card-label">All Requisitions</h3>
            </div>
          </div>
          <!--begin::Card body-->
          <div class="card-body px-4 pt-5 pb-5">
            <div class="row row-eq-height" id="chart_teamwork">
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Client's All Requisitions</h3>
                    </div>
                  </div>
                  <div class="card-body">                    
                    <!--begin::Chart-->
                    <div id="Client_Requisitions" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->                 
                  </div>
                </div>
                <!--end::Card-->
              </div>
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Applicant On Requisitions</h3>
                    </div>
                  </div>
                  <div class="card-body">                    
                    <!--begin::Chart-->
                    <div id="Applicant_Requisitions" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->                 
                  </div>
                </div>
                <!--end::Card-->
              </div>
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Assessment Stage</h3>
                    </div>
                  </div>
                  <div class="card-body">                 
                    <!--begin::Chart-->
                    <div id="Assessment_Stage" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->  
                  </div>
                </div>
                <!--end::Card-->
              </div>
            </div>            
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Card-->
      </div>  
    </div>
    <!--end::Container-->
  </div>
  <!--end::Charts Row-1-->

  <!--begin::Charts Row-2-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-xxl px-6">
      <div class="row">        
        <!--begin::Card-->
        <div class="card card-custom mb-4 px-0">
          <div class="card-header">
            <div class="card-title">
              <h3 class="card-label">Recruiter</h3>
            </div>
          </div>
          <!--begin::Card body-->
          <div class="card-body px-4 py-5">
            <div class="row row-eq-height">
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Bajaj - Contribution</h3>
                    </div>
                  </div>
                  <div class="card-body">                    
                    <!--begin::Chart-->
                    <div id="Contribution" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->                 
                  </div>
                </div>
                <!--end::Card-->
              </div>
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Bajaj - Interview Schedule</h3>
                    </div>
                  </div>
                  <div class="card-body">
                    <!--begin::Chart-->
                    <div id="Interview_Schedule" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->                 
                  </div>
                </div>
                <!--end::Card-->
              </div>
              <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-custom gutter-b bg-light h-100">
                  <div class="card-header border-bottom-dark1">
                    <div class="card-title">
                      <h3 class="card-label">Bajaj - Interview Process</h3>
                    </div>
                  </div>
                  <div class="card-body">                 
                    <!--begin::Chart-->
                    <div id="Interview_Process" class="d-flex justify-content-center"></div>
                    <!--end::Chart-->  
                  </div>
                </div>
                <!--end::Card-->
              </div>
            </div>            
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Card-->
      </div>  
    </div>
    <!--end::Container-->
  </div>
  <!--end::Charts Row-2-->

  <!--begin::Data Table-1 -->  
    <div id="kt_content_container" class="container-xxl px-2">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-3">
                  <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div>
                </th>
                <th class="">Client</th>
                <th class="">Requisition</th>
                <th class="">HM | CP</th>
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
                <td class="me-2">
                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>
                </td>
                <td class="d-flex align-items-center border-0">
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                    <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5">
                        <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="Company Logo" class="border-radius-5 w-100">
                      </div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Technology</a>
                    <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">IT Services</a>
                  </div>
                </td>                
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a>
                    <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
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
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary" data-toggle="modal" data-target="#requisitions_data">294</a> 
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
                      <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">H</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">HCL Technologies</a>
                    <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Consultancy</a>
                  </div>
                </td>              
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">PHP Developer</a>
                    <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a>
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
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">65</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">23</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">24</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">83</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">49</a>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <!--end::Table-->
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
  <!--end::Data Table-1 -->

  <!--begin::Data Table-2 -->  
    <div id="kt_content_container" class="container-xxl px-2 mt-4">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-3">
                  <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users1 .form-check-input1" value="1" />
                  </div>
                </th>
                <th class="">Schedule Date</th>
                <th class="">Applicant</th>
                <th class="">Client</th>
                <th class="">Requisitions</th>
                <th class="">HM | CP</th>
                <th class="">Status</th>
                <th class="">Mode</th>
                <th class="">Stage</th>
                <th class="">Interviewer</th>
                <th class="">Feedback</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
              <tr role="row" class="odd">
                <td tabindex="0">
                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input form-check-input1" type="checkbox" value="1">
                  </div>
                </td>
                <td class=" align-items-center">
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="764" class="show-application-detail">
                      <div data-row-id="764" class="symbol-label fs-3 bg-light-secondary text-dark border-radius-5 show-application-detail">25</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="764" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">25-Apr-2021</a>
                    <span class="fw-bold text-muted d-block fs-7 text-hover-primary">10:30 AM</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a>
                    <span class="fw-bold text-muted d-block fs-7 text-hover-primary">Web Designer</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Technology</a>
                    <span class="fw-bold text-muted d-block fs-7 text-hover-primary">Consultancy</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Andorid Developer</a>
                    <span class="fw-bold text-muted d-block fs-7 text-hover-primary">Microsoft</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One">
                        <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg">
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a>
                    <span class="fw-bold text-muted d-block fs-7">R-83</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Video</a>
                    <span class="fw-bold text-muted d-block fs-7 text-hover-primary">Google Meet</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column"> 
                    <span class="badge fw-bolder fs-6 px-2 py-1 joined-bg">Joined</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">Joined-2</span>
                  </div>
                </td>
                <td class="">
                  <div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Promod">
                        <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_6.jpg">
                      </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pawan">
                        <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_8.jpg">
                      </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list..">
                        <span class="symbol-label font-weight-bold">2+</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="">
                  <img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem">
                  <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span>
                </td>
              </tr>
            </tbody>
          </table>
          <!--end::Table-->
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
  <!--end::Data Table-2 -->
</div>
<!--end::Content-->

<!--begin::Report filter-->
<div class="modal fade show" id="scheduleDetailModalCustomReportFilter" tabindex="-1" aria-modal="true" role="dialog" data-select2-id="select2-data-kt_modal_new_target">
   <!--begin::Modal dialog-->
   <div class="modal-dialog modal-dialog-centered mw-800px">
      <!--begin::Modal content-->
      <div class="modal-content rounded">
         <!--begin::Modal header-->
         <div class="modal-header pb-0 border-0 justify-content-end">
            <!--begin::Close-->
            <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
               <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                     <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                     <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                  </svg>
               </span>
               <!--end::Svg Icon-->
            </div>
            <!--end::Close-->
         </div>
         <!--begin::Modal header-->
         <!--begin::Modal body-->
         <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-10">
            <!--begin:Form-->
            <form id="report_filter_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#" data-select2-id="select2-data-kt_modal_new_target_form">
               <!--begin::Heading-->
               <div class="mb-10 text-center">
                  <!--begin::Title-->
                  <h2 class="mb-3">Proceed to Report</h2>
                  <!--end::Title-->
               </div>
               <!--end::Heading-->
               <!--begin::Row-1-->
               <div class="row g-9 mb-8">
                  <!--begin::>Start Date-->
                  <div class="col-md-4 fv-row">
                     <label class="required fs-6 fw-bold mb-2">Start Date</label>
                     <!--begin::Input-->
                     <div class="position-relative d-flex align-items-center">                       
                        <div class="input-group input-group-solid date mb-2" id="start_date">
                          <!-- <input type='text' class="form-control" placeholder="Select start date" name="startDate" /> -->
                          <input type="text" class="form-control" id="start_date" name="startDate" placeholder="Select start date" value="" />
                          <div class="input-group-append">
                            <span class="input-group-text">
                              <i class="la la-calendar-check-o"></i>
                            </span>
                          </div>
                        </div>
                     </div>
                     <!--end::Input-->
                  </div>
                  <!--end::>Start Date-->
                  <!--begin::End Date-->
                  <div class="col-md-4 fv-row">
                     <label class="required fs-6 fw-bold mb-2">End Date</label>
                     <!--begin::Input-->
                     <!--begin::Input-->
                     <div class="position-relative d-flex align-items-center">                       
                        <div class="input-group input-group-solid date mb-2" id="end_date">
                          <!-- <input type='text' class="form-control datepicker" placeholder="Select start date" name="endDate"> -->
                          <input type="text" class="form-control" id="end_date" name="endDate" placeholder="Select start date"/>
                          <div class="input-group-append">
                            <span class="input-group-text">
                              <i class="la la-calendar-check-o"></i>
                            </span>
                          </div>
                        </div>
                     </div>
                     <!--end::Input-->
                     <!--end::Input-->
                  </div>
                  <!--end::End Date-->
                  <!--begin::Clients-->
                  <div class="col-md-4 fv-row">
                      <label class="required fs-6 fw-bold mb-2">Client</label>
                      <select name="clients" id="clients" class="form-select form-select-solid w-100" onchange="getClientJob(this.value);">
                      <option value="all">All Client</option>   
                        @forelse($clients as $clientsArr)                          
                        <option value="{{$clientsArr->id}}">{{ucfirst($clientsArr->name)}}</option>
                        @empty
                        @endforelse            
                      </select>
                      <span class="clientStatusLoader" style="height:25px; width:25px"></span>
                  </div>
                  <!--end::Clients-->
               </div>
               <!--end::Row-1-->
               <!--begin::Row-2-->
               <div class="row g-9 mb-8">
                  <!--begin::Requisition-->
                  <div class="col-md-4 fv-row">
                      <label class="required fs-6 fw-bold mb-2">Requisitions</label>
                      <select class="form-select form-select-solid" name="jobs" id="jobs">
                        <option value="all">All Jobs</option>            
                        @forelse($jobs as $job)
                        <option title="{{ucfirst($job->title)}}" value="{{$job->id}}">{{$job->id}} - {{ucfirst($job->title)}}</option>
                        @empty
                        @endforelse            
                      </select>
                  </div>
                  <!--end::Requisition-->
                  <!--begin::Interview Stage-->
                  <div class="col-md-4 fv-row">
                      <label class="required fs-6 fw-bold mb-2">Interview Stage</label>
                    <select class="form-select form-select-solid" name="master_status_id" id="master_status_id">
                      <option value="all">All Process</option>
                        @forelse($candidateStatus as $status)              
                      <option value="{{$status->id}}">{{ucfirst($status->name)}}</option>
                      @empty
                      @endforelse           
                    </select>
                  </div>
                  <!--end::Interview Stage-->
                  <!--begin::Team-->
                  <div class="col-md-4 fv-row">
                      <label class="required fs-6 fw-bold mb-2">Team</label>
                    <select class="select22 form-select form-select-solid" name="source" id="source">
                      <option value="all">All Source</option>
                      <?php 
                        $type_zero = ''; 
                        $type_one  = '';
                          foreach($users as $nameArr)
                          {
                            if($nameArr->type == 1)
                            {
                              $type_one   .= '<option value="'.$nameArr->id.'">'.ucfirst($nameArr->name).'</option>';	
                            }
                            else
                            {
                              $type_zero  .= '<option value="'.$nameArr->id.'">'.ucfirst($nameArr->name).'</option>';		
                            }
                          }
                      ?>
                      <optgroup label="Internal Team"><?php echo $type_zero; ?></optgroup>
                      <optgroup label="Consultancy"><?php echo $type_one; ?></optgroup>
                    </select>                     
                  </div>
                  <!--end::Team-->
               </div>
               <!--end::Row-2-->
               <!--begin::Row-3-->
               <div class="fv-row mb-15">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-stack">
                      <!--begin::Label-->
                      <div class="fw-bold me-5">
                        <label class="fs-6">Show Interview Details</label>
                        <div class="fs-7 text-muted">Allow Interview Details</div>
                      </div>
                      <!--end::Label-->
                      <!--begin::Checkboxes-->
                      <div class="d-flex align-items-center me-5">
                        <!--begin::Checkbox-->
                        <label class="form-check form-check-custom form-check-solid me-10">
                        <input class="form-check-input h-20px w-20px" type="radio" name="show_interview_details" id="interview_schedule_yes">
                        <span class="form-check-label fw-bold pl-7">Yes</span>
                        </label>
                        <!--end::Checkbox-->
                        <!--begin::Checkbox-->
                        <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input h-20px w-20px" type="radio" name="show_interview_details" id="interview_schedule_no">
                        <span class="form-check-label fw-bold pl-7">No</span>
                        </label>
                        <!--end::Checkbox-->
                      </div>
                      <!--end::Checkboxes-->
                  </div>
                  <!--end::Wrapper-->
                </div>
               <!--end::Row-3-->
               <!--begin::Actions-->
               <div class="text-center">
                  <button type="button" id="apply-filters" class="btn btn-light-primary me-3">Apply</button>
                  <button type="reset" id="" class="btn btn-light-info me-3">Reset</button>
                  <button type="button" id="" class="btn btn-light-dark" data-dismiss="modal">Cancel</button>
               </div>
               <!--end::Actions-->
               <div></div>
            </form>
            <!--end:Form-->
         </div>
         <!--end::Modal body-->
      </div>
      <!--end::Modal content-->
   </div>
   <!--end::Modal dialog-->
</div>
<!--end::Report filter-->

<!--begin::Requisitions Data - Modal-->
<div class="modal fade" id="requisitions_data" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Requisition - UI/UX Developer</h2>
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
              <!--begin::Requisitions-->
              <div class="form-group row">
                <!--begin::Requisiton Title-->
                <label class="col-xl-12 col-lg-12 col-form-label text-dark fw-bolder fs-5 py-0 d-none">Requisitions</label>
                <!--end::Requisition Title-->
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 mx-2" id="reqTable">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-3">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#reqTable .form-check-input-req" value="1" />
                        </div>
                      </th>
                      <th class="">Client</th>
                      <th class="">Requisitions</th>
                      <th class="">HM | CP</th>
                      <th class="">Applicant</th>
                      <th class="">Contact</th>
                      <th class="">Salary</th>
                      <th class="">Experience</th>
                      <th class="">Interview Stage</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                  <!--begin::Table body-->
                  <tbody class="text-gray-600 fw-bold">
                    <!--begin::Table row-->
                    <tr class="">          
                      <!--begin::Checkbox-->
                      <td class="me-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
                        </div>
                      </td>
                      <!--end::Checkbox-->
                      <!--begin::Client-->
                      <td class="d-flex align-items-center border-0">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                          <a href="javascript:void(0);">
                            <div class="symbol-label border-radius-5">
                              <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="Company Logo" class="border-radius-5 w-100">
                            </div>
                          </a>
                        </div>
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Technology</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">IT Services</a>
                        </div>
                      </td>
                      <!--end::Client-->
                      <!--begin::Requisitons-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
                        </div>
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::HM | CP-->
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
                      <!--end::HM | CP-->
                      <!--begin::Applicant-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Laravel Developer</a>
                        </div>
                      </td>
                      <!--end::Applicant-->
                      <!--begin::Phone & Email-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="tel:9999999999" class="text-dark fw-bolder text-hover-primary fs-6">9999999999</a>
                          <a href="mailto:abu@gmail.com" class="fw-bold text-muted d-block fs-7 text-hover-primary">abu@gmail.com</a>
                        </div>
                      </td>
                      <!--end::Phone & Email-->
                      <!--begin::CTC & E.CTC-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">30 LPA</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">28 LPA</a>
                        </div>
                      </td>
                      <!--end::CTC & E.CTC-->
                      <!--begin::Exp. & Rel. Exp.-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">12+</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">10+</a>
                        </div>
                      </td>                      
                      <!--end::Exp. & Rel. Exp.-->
                      <!--begin::Interview Stage-->
                      <td>
                        <a href="javascript.void(0);" class="d-flex flex-column">
                          <span class="badge fw-bolder fs-6 screening-bg px-2 py-1">Screening</span>
                          <span class="text-muted fw-bold text-muted d-block fs-7">Screening - 1</span>
                        </a>
                      </td>
                      <!--end::Interview Stage-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">          
                      <!--begin::Checkbox-->
                      <td class="me-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
                        </div>
                      </td>
                      <!--end::Checkbox-->
                      <!--begin::Client-->
                      <td class="d-flex align-items-center border-0">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
                          <a href="javascript:void(0);">
                            <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">A</div>
                          </a>
                        </div>
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Adobe</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Product Based</a>
                        </div>
                      </td>
                      <!--end::Client-->
                      <!--begin::Requisitons-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">SEO</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Microsoft</a>
                        </div>
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::HM | CP-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod">
                              <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list..">
                              <span class="symbol-label font-weight-bold">1+</span>
                            </div>
                          </div>
                        </div>
                      </td>
                      <!--end::HM | CP-->
                      <!--begin::Applicant-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a>
                        </div>
                      </td>
                      <!--end::Applicant-->
                      <!--begin::Phone & Email-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="tel:9999999911" class="text-dark fw-bolder text-hover-primary fs-6">9999999911</a>
                          <a href="mailto:abu@gmail.com" class="fw-bold text-muted d-block fs-7 text-hover-primary">sonika@gmail.com</a>
                        </div>
                      </td>
                      <!--end::Phone & Email-->
                      <!--begin::CTC & E.CTC-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">40 LPA</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">38 LPA</a>
                        </div>
                      </td>
                      <!--end::CTC & E.CTC-->
                      <!--begin::Exp. & Rel. Exp.-->
                      <td class="">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">16+</a>
                          <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">15+</a>
                        </div>
                      </td>                      
                      <!--end::Exp. & Rel. Exp.-->
                      <!--begin::Interview Stage-->
                      <td>
                        <a href="javascript.void(0);" class="d-flex flex-column">
                          <span class="badge fw-bolder fs-6 negotation-bg px-2 py-1">Negotation</span>
                          <span class="text-muted fw-bold text-muted d-block fs-7">Negotation - 1</span>
                        </a>
                      </td>
                      <!--end::Interview Stage-->
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
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Requisitions Data - Modal-->

@section('create-button')
<div class="row ml-0">
  <div class="col-md-3 text-left pl-0 text-primary">
    <!-- Page Title -->
    <span class="card-title pt-2 pb-2 text-dark d-none"><i class="fa fa-pie-chart"></i>&nbsp;Report&nbsp;|&nbsp;</span>
    <!-- Page Title End -->
    <div class="border-bottom-black d-inline-flex custom_report_filter1">
      <span class="cursor-pointer text-dark" style="font-size:20px; font-weight:400" id="lbl_start_date"><?php echo date('d-M, Y', strtotime('-7 days'));  ?></span>
      <span class="cursor-pointer text-dark" style="font-size:20px; font-weight:400">&nbsp;-&nbsp;</span>
      <span class="cursor-pointer text-dark" style="font-size:20px; font-weight:400" id="lbl_end_date">
        <?php echo date("d-M, Y"); ?>&nbsp;<i class="fa fa-calendar cursor-pointer"></i>
      </span>
    </div>
  </div>
  <div class="col-md-9 text-right searchbar-parent">
    <div class="input-group searchbar-child d-none"  style="">
      <input type="text" class="form-control w-100" id="custom_text" name="custom_text" placeholder="Enter Client/Team..." value="" style="padding: 0.3em .5em;">
    </div>
    <a onclick="">
    <button class="btn btn-light-primary  btn-sm mr-1 px-2 py-1" type="button">
      <i class="fa fa-download fa-sm"></i>
    </button>
    </a>
    <a href="javascript:void(0);" class="custom_report_filter btn btn-light-dark btn-sm px-2 custom_report_filter1">
      <i class="fa fa-filter fa-sm"></i>
    </a>
  </div>
</div>
@endsection

<div class="modal fade bs-modal-exl in" id="scheduleDetailModalCustomReportFilter" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingCustomReportFilter"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
<div id="chart_teamwork"></div>

<div class="row">
  <div class="col-12">
    <div class="card" style="padding-bottom: 1em;">
      <div class="card-body bg-white pt-3">
        <div class="row ml-0">
          <div class="col-md-6 text-left pl-0">
            <div class="dropdown dropdown-inline invisible">
              <button class="dropbtn bg-light dropdown-toggle dropbtn-border"> 10 </button>
              <div class="dropdown-content">
                <ul class="navi">
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 text-right searchbar-parent">
            <div class="input-group mr-1 searchbar-child">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            <a onclick="exportExcel_application();">
            <button class="btn btn-light-primary  btn-sm ml-1 mr-2 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
            </a> </div>
        </div>
        <table id="myTable" class="w-100 table table-bordered table-striped table-hover table-checkable">
          <thead>
            <tr class="table-bg-custom">
              <th>SNo</th>
              <th>Client</th>
              <th>Requisitions</th>
              <th>Source</th>
              <th style="">
                <div class="tooltip font-weight-700">TOT
                  <span class="tooltiptext font-weight-500">Total Application</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">SOU
                  <span class="tooltiptext font-weight-500">Sourcing</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">SCR
                  <span class="tooltiptext font-weight-500">Screening</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">INT
                  <span class="tooltiptext font-weight-500">Interview</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">NEO
                  <span class="tooltiptext font-weight-500">Negotiation</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">OFF
                  <span class="tooltiptext font-weight-500">Offer</span>
                </div>
              </th>
              <th>
                <div class="tooltip font-weight-700">JOI
                  <span class="tooltiptext font-weight-500">Joined</span>
                </div>
              </th>
              <th style="width: 24px!important;">
                <div class="tooltip font-weight-700">REJ
                  <span class="tooltiptext font-weight-500">Rejected</span>
                </div>
              </th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card" style="padding-bottom: 1em;margin-bottom: 0em!important;">
      <div class="card-body bg-white pt-3">
        <div class="row ml-0">
          <div class="col-md-6 text-left pl-0">
            <div class="dropdown dropdown-inline">
              <button class="dropbtn bg-light dropdown-toggle dropbtn-border"> 10 </button>
              <div class="dropdown-content">
                <ul class="navi">
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span></a> </li>
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span></a> </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 text-right searchbar-parent">
            <div class="input-group mr-1 searchbar-child">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            <a onclick="exportExcel_application();">
            <button class="btn btn-light-primary  btn-sm ml-1 mr-2 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
            </a> </div>
        </div>
        <table id="myInterviewSchedule" class="w-100 table table-bordered table-striped table-hover table-checkable">
          <thead>
            <tr class="table-bg-custom">
              <th>SNo</th>
              <th>Schedule Date</th>
              <th>Application</th>
              <th>Client</th>
              <th>Requisitions</th>
              <th>Source</th>
              <th>Status</th>
              <th>Mode</th>
              <th>Stage</th>
              <!-- <th>Sub Stage</th> -->
              <th>
                <div class="tooltip font-weight-700">S.Stage
                  <span class="tooltiptext font-weight-500">Sub Stage</span>
                </div>
              </th>
              <th>Interviewer</th>
              <th>Feedback</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-xl in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true">
  <div class="modal-dialog modal-xl" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-md in" id="scheduleDetailModal_joblisting" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_joblisting"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-exl in" id="scheduleDetailModalReportFilter" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingReportFilter"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>

<input type="hidden" name="start_date_hidden" id="start_date_hidden" value="<?php echo date('Y-m-d', strtotime('-7 days'));  ?>" />
<input type="hidden" name="end_date_hidden" id="end_date_hidden" value="<?php echo date('Y-m-d');  ?>" />
@endsection
@push('footer-script')

<script type="text/javascript">
   $().ready(function() { 
   
   
   			// $('#custom_report_filter').click(function () {	
        $('.custom_report_filter1').click(function () {  			
        			url 				=  "{{ route('admin.customreport.custom_report_filter') }}";	  				
        			$('#modelHeadingCustomReportFilter').html('Schedule');
        			$.ajaxModal('#scheduleDetailModalCustomReportFilter', url);		
            }); 



  
   			/*$("#report_frm").validate({
   
   											rules: {
   														start_date: "required",
   														end_date: "required"
   													},
   
   											messages: {
   															start_date: "Please enter Start date",
   															end_date: "Please enter End date"
   													}
   			});   */
   });   
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);
   function drawChart() {
    
     var data = google.visualization.arrayToDataTable([  
       ['Clients', 'Requisitions'],  
       ['TCS',     11],
       ['HCL',      2],
       ['IBM',  2],
       ['Wipro', 2],
       ['ABC',    7]
     ]);		
     var options = {
	 					legend: 'none',
						 pieSliceText: 'label',
       					title: "",
   						pieSliceText: 'value'  
     };
     var chart = new google.visualization.PieChart(document.getElementById('client_piechart'));
     chart.draw(data, options);  
   
   }	    
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);  
   function drawChart() { 
     var data = google.visualization.arrayToDataTable([
       ['Requisitions', 'Application'],
       ['Sr PHP Developer',     35],
       ['Automation Testing',      12],
       ['Java Developer',  5],
       ['SAP Programmer', 51],
       ['ABC',    41]
     ]);		
   
   
   
     var options = {
  	      				title: "Application's on Requisitions",
   						pieSliceText: 'value'  
     				};
   
   
   
     var chart = new google.visualization.PieChart(document.getElementById('application_on_jobs_piechart'));   
     chart.draw(data, options);
   
   
   }	  
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawVisualization);
   function drawVisualization() {
   
   
     var data = google.visualization.arrayToDataTable([
		   ['Month', 'Testing', 'Sr PHP Developer', 'Automation Testing', 'Java Developer', 'SAP Programmer', 'ABC'], 
		   ['2021/01',  165,      938,         522,             998,           450,      614.6],
		   ['2021/02',  135,      120,        599,             268,          288,      682],
		   ['2021/03',  157,      167,        587,             407,           397,      623],
		   ['2021/04',  139,      110,        615,             368,           215,      609.4],
		   ['2021/05',  136,      691,         629,             626,          366,      569.6]
     ]);
   
     var options = {
       					title : "Application's on Requisitions",
       					vAxis: {title: 'Application'},
       					hAxis: {title: 'Date'}, 
       					seriesType: 'bars',
       					series: {5: {type: 'line'}}
     				};
   
     var chart = new google.visualization.ComboChart(document.getElementById('application_on_jobs_barchart'));
     chart.draw(data, options);
   }
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);
   function drawChart() {
    
     var data = google.visualization.arrayToDataTable([
														   ['Date', 'Abu', 'Amit', 'Sonika', 'Vibhanshu'],
														   ['jan-2021',  1000,      400, 150, 340],
														   ['feb-2021',  1170,      460 , 245, 125],
														   ['march-2021',  660,       1120, 850, 374],
														   ['april-2021',  1030,      540, 893, 1170]
													 ]);
    
     var options = { 
					   title: 'Team Work(Application submited by team member)',
					   hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
					   vAxis: {minValue: 0}
					 };  
     var chart = new google.visualization.AreaChart(document.getElementById('team_work'));
     chart.draw(data, options);
   }
</script>
<script src="{{ asset('js/jquery.validate.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
// validate signup form on keyup and submit
$("#report_frm").validate({
			onfocusout: function(e)
      {
        this.element(e);
    	},	
			rules:
      {						
				start_date: "required",
				end_date: "required"										
			},
			messages:
      {
				start_date: "Please select start date",
				end_date: "Please select end date"															
			}		 
});		
</script>
<script>
   loadTable(); 
   
   $('#start_date').datepicker({
   // format: 'yyyy-mm-dd'
   // format: 'd-M-Y: H:I:s'
   format: 'd-m-yyyy'
   })
   
   $('#end_date').datepicker({
   // format: 'yyyy-mm-dd'
   //format: 'd-M-Y: H:I:s'
   format: 'd-m-yyyy'
   })		
    
  
   
 
   
  
    
   
   $('#myTable').on('click', '.joblisting', function() {
   
   var id 				= $(this).data('row-id');
   var row_status 		= $(this).data('row-status');			
   //url 				=  '{!! route('admin.customreport.show') !!}?id='+id+'&row_status='+row_status;					
   url 					= '{!! route('admin.customreport.show') !!}?job_id='+id+'&application_status='+row_status				
   	
          	$('#modelHeading_joblisting').html('Schedule');
          	$.ajaxModal('#scheduleDetailModal_joblisting', url);					
   });
     
  
   $('.report_filter').click(function () {				
			url 				= '{!! route('admin.customreport.report_filter') !!}';				
			$('#modelHeadingReportFilter').html('Schedule');
			$.ajaxModal('#scheduleDetailModalReportFilter', url);		
   });  
   
   var chart = document.getElementById("myChart").getContext('2d');
   var cData = JSON.parse(`<?php echo $chart_data; ?>`);
   var keys = [];
   var value = [];
   $.each(cData, function(k, v) {
   					value.push(v)
   					keys.push(k)
   				});
   
    
   var myChart = new Chart(chart, {
   
   	type: 'pie',
     data: {
   	labels: keys,
   	datasets: [{
   				  backgroundColor: [
   										"#95a5a6",
   										"#9b59b6",
   										"#28a745",
   										"#3D8EE8",
   									  ],
   				  data:value,
   			 }]
   	}
   
   });
</script>
<script>

function exportExcel_application()
{
	
		
				var custom_text 				= '';
				var start_date 					= '';
				var end_date 					= '';
				var clients			 			= '';
				var jobs 						= '';
				var master_status_id 			= '';			
				var source           			= '';
				var interview_process           = '';
				//   var interview_process 	= $('input[name="interview_process"]:checked').val();  // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
				
			 	custom_text				=	$('#custom_text').val();
			 	start_date				=	$('#start_date').val();
			 	end_date				=	$('#end_date').val();
			 	clients					=	$('#clients').val();
			 	jobs					=	$('#jobs').val();  
			 	master_status_id		=	$('#master_status_id').val();
				source					=	$('#source').val();
				interview_process		=	$('input[name="interview_process"]:checked').val();
			
				var table 				= $('#myTable').dataTable();
				var length 				= table.fnGetData().length;
						
				var oSettings 			= table.fnSettings();
				var start     			= oSettings._iDisplayStart;			
						
				start_date 				= start_date.replace('/', '-');
				start_date 				= start_date.replace('/', '-'); 
				
				end_date 				= end_date.replace('/', '-');
				end_date 				= end_date.replace('/', '-'); 
				
				if(custom_text == '')
				{
						custom_text = 'none';
				}
  
			//alert(end_date);  //04/01/2021
			// return false; 
			 
		//var url 				= '{{ route('admin.cvtracker.export', [':clients', ':jobs', ':source', ':search_passport', ':search_np_id', ':status' , ':start' , ':length']) }}';			

var url 		= '{{ route('admin.customreport.export', [':clients', ':jobs', ':master_status_id', ':source' ,  ':custom_text', ':start_date', ':end_date', ':start' , ':length']) }}';
												
			url 					= url.replace(':clients', clients);
			url 					= url.replace(':jobs', jobs);
			url 					= url.replace(':master_status_id', master_status_id);
			url 					= url.replace(':source', source);
			url 					= url.replace(':custom_text', custom_text);
			url 					= url.replace(':start_date', start_date);
			url 					= url.replace(':end_date', end_date);
			
			url 					= url.replace(':start', start);
			url 					= url.replace(':length', length);
		
           window.location.href = url;
						
			
        
}

function exportExcel_interview()
{
	
	
		var custom_text 				= '';
		var start_date 					= '';
		var end_date 					= '';
		var clients			 			= '';
		var jobs 						= '';
		var master_status_id 			= '';			
		var source           			= '';
		var interview_process           = '';
				
		custom_text						=	$('#custom_text').val();
		start_date						=	$('#start_date').val();
		end_date						=	$('#end_date').val();
		clients							=	$('#clients').val();
		jobs							=	$('#jobs').val();  
		master_status_id				=	$('#master_status_id').val();
		source							=	$('#source').val();
		interview_process				=	$('input[name="interview_process"]:checked').val();
			
		var table 						= $('#myTable').dataTable();
		var length 						= table.fnGetData().length;
						
		var oSettings 					= table.fnSettings();
		var start     					= oSettings._iDisplayStart;			
						
		start_date 						= start_date.replace('/', '-');
		start_date 						= start_date.replace('/', '-'); 
				
		end_date 						= end_date.replace('/', '-');
		end_date 						= end_date.replace('/', '-'); 
				
		if(custom_text == '')
		{
						custom_text = 'none';
		}
		
		var url 						= '{{ route('admin.customreport.interviewexport', [':clients', ':jobs', ':master_status_id', ':source' ,  ':custom_text', ':start_date', ':end_date', ':start' , ':length']) }}';	
		
			url 					= url.replace(':clients', clients);
			url 					= url.replace(':jobs', jobs);
			url 					= url.replace(':master_status_id', master_status_id);
			url 					= url.replace(':source', source);
			url 					= url.replace(':custom_text', custom_text);
			url 					= url.replace(':start_date', start_date);
			url 					= url.replace(':end_date', end_date);
			
			url 					= url.replace(':start', start);
			url 					= url.replace(':length', length);
		
           window.location.href = url;
	
			
}					
</script>
<script>

   	function teamwork()
			{
					var custom_text 				= '';
					var start_date 					= '';
					var end_date 					= '';
					var clients			 			= '';
					var jobs 						= '';
					var master_status_id 			= '';			
					var source           			= '';
					var interview_process           = '';
				
					custom_text						=	$('#custom_text').val();
					start_date						=	$('#start_date').val();
					end_date						=	$('#end_date').val();
					clients							=	$('#clients').val();
					jobs							=	$('#jobs').val();  
					master_status_id				=	$('#master_status_id').val();
					source							=	$('#source').val();
					interview_process				=	$('input[name="interview_process"]:checked').val();
									
					start_date 						= start_date.replace('/', '-');
					start_date 						= start_date.replace('/', '-'); 
							
					end_date 						= end_date.replace('/', '-');
					end_date 						= end_date.replace('/', '-'); 
				
					if(custom_text == '')
					{
									custom_text = 'none';
					}
		
				
			
					var clientId 		= clients;
					var url 			= "{{ route('admin.customreport.chart_teamwork',':clientId') }}";
                    url 				= url.replace(':clientId', clientId);
					var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 				
                    var token 			= "{{ csrf_token() }}";
					//alert(clientId); 
					
					 $.easyAjax({
                        				type: 'POST',
                        				url: url,
                        				data: {'_token': token, '_method': 'POST', 'custom_text' : custom_text, 'start_date' : start_date,  'end_date' : end_date, 'clients' : clients, 'jobs' : jobs, 'master_status_id' : master_status_id, 'source' : source, 'interview_process' : interview_process},
										beforeSend: function() {
																	$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
										},
                        				success: function (response) {
                         
						 					$('#chart_teamwork').html(response.view);
											//$('.clientStatusLoader').html('');
											//tableLoad('filter');
											
										
											
										},
										complete: function (data) {
												$('.clientStatusLoader').html('');
										 }
                    });       	
		}
</script>

<!--begin::Metronic-7 Library-->
<script src="{{ asset('demo/v7/js/prismjs.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::Metronic-7 Library-->

<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('demo/v7/js/pages/features/charts/apexcharts.js')}}"></script>
<!--end::Page Scripts-->

<!--begin::Date picker-->
<!-- <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/bootstrap-datepicker.js') }}"></script> -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js'></script>
<script>
  //Start date script
  $(document).ready(function()
  {
    $(function ()
    {
      $('#start_date').datepicker({
      format: 'dd-M-yyyy' 
      });
    });
  });
  //Start date script

  //End date script
  $(document).ready(function()
  {
    $(function ()
    {
      $('#end_date').datepicker({
        format: "dd-M-yyyy"
      })
    })
  })
  //End date script 
</script>
<!--end::Date picker-->

<!--begin::Validation-->
<script src="{{ asset('demo/js/custom/modals/report-filter.js') }}"></script>
<!--end::Validation-->

<script>
 function getClientJob(clientId)
   {
   	//alert("call"); 
	
   	var clientId 		= clientId;					
   	var url 			= "{{ route('admin.cvtracker.get_client_job',':clientId') }}";					
   	url 				= url.replace(':clientId', clientId);					
   	var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 								
   	var token 			= "{{ csrf_token() }}";
   	$.ajax({
   
                       				type: 'POST',
                       				url: url,
                       				data: {'_token': token, '_method': 'POST', 'clientId' : clientId},
   						beforeSend: function() {
   													$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="200px">');
   						},
                       				success: function (response) {
   
   		 					$('#jobs').html(response.data);
   							$('.clientStatusLoader').html('');
   							//tableLoad('filter');
   						},
   
   						complete: function (data) {
   								$('.clientStatusLoader').html('');
   						 }
   
                   });		
   
   }
 
  function loadTable()
   {   	
	   var custom_text			=	$('#custom_text').val();
	   var start_date			=	$('#start_date').val();
	   var end_date				=	$('#end_date').val();
	   var clients				=	$('#clients').val();             					// Clients Default value = all else other number
	   var jobs					=	$('#jobs').val();									// Jobs Default value = all else other number
	   var source				=	$('#source').val();									// Team Default value = all else other number
	   var master_status_id		=	$('#master_status_id').val();						// Jobs Default value = all else other number
	   var interview_process 	= $('input[name="interview_process"]:checked').val();   // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
     
	 
	 
	 	if((start_date == '') && (end_date == '') )
		{
			var start_date		=	$('#start_date_hidden').val();
			var end_date		=	$('#end_date_hidden').val();
			
			var custom_text			=	$('#custom_text').val();
			
			var clients				=	'all';             					// Clients Default value = all else other number
			var jobs				=	'all';								// Jobs Default value = all else other number
			var source				=	'all';								// Team Default value = all else other number
			var master_status_id	=	'all';								// Jobs Default value = all else other number
			var interview_process 	=	1;   								// 0 For Not intrested interview schedule , 1 For intrested interview schedule 
	   			
		}
		
		//  if($("#report_frm").valid())
		//  { 
   
   						//teamwork();
   
   						//$("body").mLoading();
						var table = $('#myTable').dataTable({
      responsive     : false, 
      processing      : true,
      "scrollY"     : 200,
      //"autoWidth"     : false,
      autoWidth     : false, 
      destroy       : true,   
      stateSave     : true,
      colReorder      : true,     
      scrollCollapse    : true,       
      "bFilter"     : true, 
      "bLengthChange"   : true,          
      "fixedHeader":
       {
        "header": false,
        "footer": false  
       }, 
   
       "bInfo"     : false,
       "bLengthChange" : true,
       oLanguage:
       {
         sLengthMenu: "_MENU_",
       },

            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "pageLength": 25,  
            bFilter: false, bInfo: true,					

			   ajax: '{!! route('admin.customreport.data') !!}?custom_text='+custom_text+'&start_date='+start_date+'&end_date='+end_date+'&clients='+clients+'&jobs='+jobs+'&source='+source+'&master_status_id='+master_status_id+'&interview_process='+interview_process,
				   language: {
					   "url": "<?php echo __("app.datatable") ?>"
				   },
			   
				   "fnDrawCallback": function( oSettings ) {
						$("body").tooltip({  
						   selector: '[data-toggle="tooltip"]' 
					   });  
				   },  
			   
					"initComplete":function( settings, json){
							
							setTimeout(function(){   
													$('#scheduleDetailModalReportFilter').modal('hide');
													$('#scheduleDetailModalReportFilter').modal().hide();
													  
												}, 1000);
							setTimeout(function(){$("body").mLoading('hide');}, 1500);
							
					},
					
			   "columnDefs": [   
								  { "width": "3%", "targets": 0 },
								  { "width": "25%", "targets": 1 },
								  { "width": "45%", "targets": 2 },
								  { "width": "3%", "targets": 3 },
								  { "width": "3%", "targets": 4 },
								  { "width": "3%", "targets": 5 },
								  { "width": "3%", "targets": 6 },
								  { "width": "3%", "targets": 7 },
								  { "width": "3%", "targets": 8 },
								  { "width": "3%", "targets": 9 },
								  { "width": "3%", "targets": 10 },
								  { "width": "3%", "targets": 11 } 
					],
			 columns: [
						{ data: 'id', width: '3%'},
						{ data: 'client_name', name: 'client_name', searchable : false, orderable : false, width: '25%'},
						{ data: 'job_title', name: 'job_title' , searchable : false, orderable : false, width: '45%'},
						{ data: 'source', name: 'source', searchable : false, orderable : false },
						{ data: 'total_application', name: 'total_application', searchable : false, orderable : false },	
						{ data: 'sourcing', name: 'sourcing', searchable : false, orderable : false },
						{ data: 'screening', name: 'screening', searchable : false, orderable : false },
						{ data: 'interview', name: 'interview', searchable : false, orderable : false },
						{ data: 'negotiation', name: 'negotiation', searchable : false, orderable : false },
						{ data: 'offered', name: 'offered', searchable : false, orderable : false },
						{ data: 'joined', name: 'joined', searchable : false, orderable : false },
						{ data: 'rejected', name: 'rejected', searchable : false, orderable : false }
			   
					]
			   
				});
			   
						new $.fn.dataTable.FixedHeader( table );					
			   
					
			   
 
  		//	} 
   
   
   }
   
   
   
$(document).ready(()=>{
  	$('#apply-filters').click(function () {
   		loadTable();
    });
});   
        
</script>
@endpush
