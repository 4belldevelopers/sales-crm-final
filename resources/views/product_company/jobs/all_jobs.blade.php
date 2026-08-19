@extends('layouts.product_company_layout')
@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<!-- Responsive CSS End -->
<style>

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

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesJob
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesJob
  {
    width: 1200px;
  }
}
/*end::Media query*/


</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Requisition-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ asset('product_company/jobs') }}" class=" text-gray-800 text-hover-primary">All Requisition</a> </h5>
        <span class="text-gray-600" id="">6,456</span> </div>
      <!--end::All Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Client Req.-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Client requests</a> </h5>
        <span class="text-gray-600">1,222</span></div>
      <!--end::Client Req.-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Custom Req.-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Custom requests</a> </h5>
        <span class="text-gray-600">2,945</span></div>
      <!--end::Custom Req.-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Jobs-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary">Job requests</a> </h5>
        <span class="text-gray-600">484</span></div>
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
          <!--begin::Table-->
          <table id="myTable_New" class="table align-middle table-row-dashed fs-6 gy-5">
            <!--begin::Table head-->
            <thead>
              <tr class="fw-bolder text-muted" role="row">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1">
                  </div></th>
                <th class="d-flex align-items-center">Requisition</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date | OPN</th>
                <th class="">HM | CP</th>
              </tr>
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody>
              <tr role="row" class="odd">
                <td tabindex="0"><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div></td>
                <td class=" d-flex align-items-center"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://rm.vibhanshujaiswal.com/public/user-uploads/client-document/18/5d/ed/spider-1635419547.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="2">2</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Spider</a> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">14+</span> <span class="text-muted fw-bold text-muted d-block fs-7">12+</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    3/A </span> <span class="fw-bold d-block fs-7">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    2/A </span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4    " title="" data-bs-original-title="Spider">Spider</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Enraku</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">GB</span>
                      <div> </div>
                    </div>
                  </div></td>
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R97</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29 Nov, 2021</span> <span class="text-muted fw-bold d-block fs-7">2</span> </div></td>
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="abu-2"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
              </tr>
              <tr role="row" class="odd">
                <td tabindex="0"><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div></td>
                <td class=" d-flex align-items-center"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://rm.vibhanshujaiswal.com/public/user-uploads/client-document/18/6a/e9/heracles-1637824543.jpg" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Heracles">Heracles</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Heracles</a> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">5+</span> <span class="text-muted fw-bold text-muted d-block fs-7">1+</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    5/A </span> <span class="fw-bold d-block fs-7">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    2/A </span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="" data-bs-original-title="Okhla">Okhla</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Delhi</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">IN</span>
                      <div> </div>
                    </div>
                  </div></td>
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R94</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">25 Nov, 2021</span> <span class="text-muted fw-bold d-block fs-7">2</span> </div></td>
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="4Bell Technology"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
              </tr>
              <tr role="row" class="even">
                <td tabindex="0"><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div></td>
                <td class=" d-flex align-items-center"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://rm.vibhanshujaiswal.com/public/user-uploads/client-document/18/6a/e0/camel-1635422025.jpg" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Administator">Administator</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Camel</a> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">11+</span> <span class="text-muted fw-bold text-muted d-block fs-7">14+</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    8/A </span> <span class="fw-bold d-block fs-7">
                    <svg class="svg-inline--fa fa-indian-rupee-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="indian-rupee-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M320 176c0 17.67-14.33 32-32 32h-35.99c-13.32 58.19-61.63 103.2-121.8 110.8l144.3 103.1c14.39 10.28 17.72 30.27 7.453 44.64C275.8 475.3 265.1 480 255.1 480c-6.438 0-12.94-1.938-18.57-5.953l-224-160C2.078 305.1-2.719 291.5 1.531 278.2C5.766 264.1 18.09 256 32 256h80c32.72 0 60.84-19.8 73.22-48H32c-17.67 0-32-14.33-32-32s14.33-32 32-32h153.2C172.8 115.8 144.7 96 112 96H32C14.33 96 0 81.67 0 64s14.33-32 32-32h256c17.67 0 32 14.33 32 32s-14.33 32-32 32h-56.38c9.637 14.36 16.47 30.63 20.44 48H288C305.7 144 320 158.3 320 176z"></path>
                    </svg>
                    879/A </span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="" data-bs-original-title="Camel">Camel</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7"></span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">IN</span>
                      <div> </div>
                    </div>
                  </div></td>
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R93</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">23 Nov, 2021</span> <span class="text-muted fw-bold d-block fs-7">78</span> </div></td>
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="abu-2"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
              </tr>
              <tr role="row" class="odd">
                <td tabindex="0"><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div></td>
                <td class=" d-flex align-items-center"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://rm.vibhanshujaiswal.com/public/user-uploads/client-document/18/16/27/company_logo-1637149830.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="November-09">November-09</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Microsoft</a> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">6+</span> <span class="text-muted fw-bold text-muted d-block fs-7">5+</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
                    <svg class="svg-inline--fa fa-euro-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="euro-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M351.2 436.2c3.984 17.22-6.75 34.41-23.97 38.41c-15.84 3.656-31.81 5.438-47.59 5.438c-56.84 0-111.7-23.22-152.8-66.19C104.9 391 88.89 364.4 78.49 336H48c-17.67 0-32-14.31-32-32S30.33 272 48 272h16.94c-.7285-10.66-.7266-21.34 0-32H48c-17.67 0-32-14.3-32-31.99S30.33 176 48 176h30.49c10.41-28.4 26.4-55.06 48.31-77.91c52.58-54.88 127.5-77.5 200.4-60.72c17.22 4 27.95 21.19 23.97 38.41c-3.969 17.22-21.12 27.97-38.38 23.97C262.1 88.09 209.8 104 173 142.4C163.2 152.6 155.6 164.1 149 176H272c17.67 0 31.1 14.32 31.1 32.01S289.7 240 272 240H129.4c-1.018 10.68-1.02 21.32 0 32H272c17.67 0 31.1 14.31 31.1 31.1S289.7 336 272 336H149c6.588 11.93 14.17 23.41 23.94 33.59c36.83 38.44 89.03 54.34 139.8 42.66C329.1 408.2 347.2 419 351.2 436.2z"></path>
                    </svg>
                    500k/M </span> <span class="fw-bold d-block fs-7">
                    <svg class="svg-inline--fa fa-euro-sign" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="euro-sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg="">
                      <path fill="currentColor" d="M351.2 436.2c3.984 17.22-6.75 34.41-23.97 38.41c-15.84 3.656-31.81 5.438-47.59 5.438c-56.84 0-111.7-23.22-152.8-66.19C104.9 391 88.89 364.4 78.49 336H48c-17.67 0-32-14.31-32-32S30.33 272 48 272h16.94c-.7285-10.66-.7266-21.34 0-32H48c-17.67 0-32-14.3-32-31.99S30.33 176 48 176h30.49c10.41-28.4 26.4-55.06 48.31-77.91c52.58-54.88 127.5-77.5 200.4-60.72c17.22 4 27.95 21.19 23.97 38.41c-3.969 17.22-21.12 27.97-38.38 23.97C262.1 88.09 209.8 104 173 142.4C163.2 152.6 155.6 164.1 149 176H272c17.67 0 31.1 14.32 31.1 32.01S289.7 240 272 240H129.4c-1.018 10.68-1.02 21.32 0 32H272c17.67 0 31.1 14.31 31.1 31.1S289.7 336 272 336H149c6.588 11.93 14.17 23.41 23.94 33.59c36.83 38.44 89.03 54.34 139.8 42.66C329.1 408.2 347.2 419 351.2 436.2z"></path>
                    </svg>
                    50k/M </span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="" data-bs-original-title=""></span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7"></span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7"></span>
                      <div> </div>
                    </div>
                  </div></td>
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R92</span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">09 Nov, 2021</span> <span class="text-muted fw-bold d-block fs-7">20</span> </div></td>
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="" data-bs-original-title="New user"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
              </tr>
            </tbody>
            <!--begin::Table body-->
          </table>
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
<!--begin::ActivitiesJob-->
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
<!--end::ActivitiesJob-->
<!--begin::Staffing Partners sidebar-->
<div id="kt_activitiesStaffing_toggle"></div>
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
    <div class="card-body position-relative px-0 py-0" id="kt_activities_body"> </div>
    <!--end::Body-->
  </div>
</div>
<!--end::Staffing Partners sidebar-->
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
   
   function editJobStatus(job_id)
   {
     var id        = job_id;
     if(typeof scheduleDate === "undefined")
     {
        scheduleDate  = '';
     }
     var url       = "{{ route('product_company.jobs.editJobStatus',':id') }}";
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
				
				timeout: 500000,
           async: false
   				
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
       var url   = "{{ route('product_company.jobs.viewmore',':id') }}";
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
   		var url   		= "{{ route('product_company.jobs.viewmore',':id') }}";
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
     var url       = "{{ route('product_company.jobs.load_visibility_control',':id') }}";
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
     var url 			        = '{{ route('product_company.jobs.export', [':start' , ':length']) }}';
     url 					        = url.replace(':start', start);
   	url 					        = url.replace(':length', length);
   	window.location.href  = url;
   }
</script>
@endpush