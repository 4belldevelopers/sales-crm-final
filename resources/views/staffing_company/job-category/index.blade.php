@extends('layouts.staffing_company_layout')
<!--Kanban Css-->
<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">
<!--Kanban Css End-->
<style>
body
{
   overflow-x:hidden;
}
.table > :not(caption) > * > *
{
   padding: 0.9rem 0.2rem!important;
}
</style>
</head>
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <span class="text-gray-800 mb-1">Company Profile</span> </h1>
      </div>
      <!--end::All Client-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1">
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
    <div id="kt_content_container" class="container-xxl px-2 mb-4">
      <!--begin::Card-->
      <!--beign::Company Details-->
      <div class="card mb-5 mb-xl-5 mx-2">
        <div class="card-body pt-5 pb-0 px-5">
          <!--begin::Details-->
          <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
              <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="image">
                <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
              </div>
            </div>
            <!--end::Pic-->
            <!--begin::Info-->
            <div class="flex-grow-1">
              <!--begin::Row-1-->
              <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                <!--begin::Candidate Details-->
                <div class="d-flex flex-column">
                  <!--begin::Company Name-->
                  <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2">4Bell Technology</span> <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Verified">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                    <span class="svg-icon svg-icon-1 svg-icon-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                      <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                      <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    </a> </div>
                  <!--end::Company Name-->
                  <!--begin::Designaiton-->
                  <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                        <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    IT Consulting | Product Based </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                      <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Noida, INDIA </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Badge </a> </div>
                  <!--end::Designaiton-->
                </div>
                <!--end::Company Details-->
              </div>
              <!--end::Row-1-->
              <!--begin::Row-2-->
              <div class="d-flex flex-wrap flex-stack">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column flex-grow-1 pe-8">
                  <div class="row w-75"> <span class=""> <span class="badge badge-light">IT Consulting</span> <span class="badge badge-light">IT Services</span> <span class="badge badge-light">Consulting</span> <span class="badge badge-light">Testing</span> <span class="badge badge-light">Devlopment</span> <span class="badge badge-light">Designing</span> <span class="badge badge-light">Sales & Marketing</span> </span> </div>
                </div>
                <!--end::Wrapper-->
              </div>
              <!--end::Row-2-->
            </div>
            <!--end::Info-->
          </div>
          <!--begin::separator-->
          <div class="separator separator-solid my-7"></div>
          <!--end::separator-->
          <!--begin::section-->
          <div class="d-flex align-items-center flex-wrap pb-7">
            <!--begin: Item1-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                  <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                  <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Type</span> <span class="fw-bolder fs-3">Private</span> </div>
            </div>
            <!--end: Item1-->
            <!--begin: Item2-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3">2,609</span> </div>
            </div>
            <!--end: Item2-->
            <!--begin: Item3-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Active Req.</span> <span class="fw-bolder fs-3">435</span> </div>
            </div>
            <!--end: Item3-->
            <!--begin: Item4-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M7,3 L17,3 C19.209139,3 21,4.790861 21,7 C21,9.209139 19.209139,11 17,11 L7,11 C4.790861,11 3,9.209139 3,7 C3,4.790861 4.790861,3 7,3 Z M7,9 C8.1045695,9 9,8.1045695 9,7 C9,5.8954305 8.1045695,5 7,5 C5.8954305,5 5,5.8954305 5,7 C5,8.1045695 5.8954305,9 7,9 Z" fill="#000000"></path>
                  <path d="M7,13 L17,13 C19.209139,13 21,14.790861 21,17 C21,19.209139 19.209139,21 17,21 L7,21 C4.790861,21 3,19.209139 3,17 C3,14.790861 4.790861,13 7,13 Z M17,19 C18.1045695,19 19,18.1045695 19,17 C19,15.8954305 18.1045695,15 17,15 C15.8954305,15 15,15.8954305 15,17 C15,18.1045695 15.8954305,19 17,19 Z" fill="#000000" opacity="0.3"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3">10 Years ago</span> </div>
            </div>
            <!--end: Item4-->
            <!--begin: Item5-->
            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z" fill="#000000"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span class="fw-bolder fs-3">3 Years ago</span> </div>
            </div>
            <!--end: Item5-->
            <!--begin: Item-->
            <div class="d-flex align-items-center flex-lg-fill my-1">
              <!--begin::Svg Icon-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                  <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="symbol-group symbol-hover">
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Mark Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Charlie Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Luca Doncic"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Nick Mana"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Teresa Fox"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_18.jpg"> </div>
                <div class="symbol symbol1 symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="List.."> <span class="symbol-label symbol-label1 font-weight-bold">5+</span> </div>
              </div>
            </div>
            <!--end: Item-->
          </div>
          <!--end::sections-->
          <!--end::Details-->
        </div>
      </div>
      <!--end::Company Details-->
      <!--begin::Tabs-->
      <div class="card card-custom mx-2">
        <!--Begin::Tabs Header-->
        <div class="card-header card-header-tabs-line">
          <div class="card-toolbar">
            <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
              <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#team"> <span class="nav-text font-weight-bold">Team</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interview_stage"> <span class="nav-text font-weight-bold">Interview Stage</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>
            </ul>
          </div>
        </div>
        <!--end::Tabs Header-->
        <!--Begin::Body-->
        <div class="card-body py-0">
          <div class="tab-content pt-5">
            <!--begin::Overview Content-->
            <div class="tab-pane active show" id="overview" role="tabpanel">
              <!--begin::Overview header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Overview</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="{{route('staffing_company.profile.edit-profile')}}" class="btn btn-primary align-self-center">Edit Profile</a>
                <!--end::Action-->
              </div>
              <!--end::Overview header-->
              <div class="card-body px-0 py-7">
                <!--begin::overview Content-->
                <div class="row">
                  <!--begin::Col-Desc-->
                  <div class="col-lg-12 mb-7">
                    <!--begin::Col-->
                    <p class="fw-bold text-muted fs-6 text-justify">4Bell Technology is a leading software development company which providing expert IT solutions to its global clients,
                      focusing on enterprise solutions, mobile/web apps development and staffing services. Our goal from the start has been to ideate, design, develop, and Market the
                      solutions that are specific to the needs of our clients. We belief in ourselves and the services we offer have enabled us to continue growing the way we have,
                      over the years.4Bell Technology has built solid experience in multiple domains and established its technical expertise across all technologies. Our rich portfolio of
                      work and client testimonials is based on that fact. Three “D’s” is our work Principle. </p>
                    <p class="fw-bold text-muted fs-6 text-justify">Our mission is to challenge how organizations engage software technology in creating enterprise
                      value. We do so by providing software development, outsourcing and technology consulting services that enable organizations to seize the market
                      opportunity, empower their people and drive operational efficiency. </p>
                    <p class="fw-bold text-muted fs-6 text-justify">4Bell Technology helps Startups set up their businesses with robust, rapid, profitable and scalable
                      IT solutions. 4Bell Technology helps Startups to assess the feasibility in low-income markets.4Bell Technology helps clients design and manage
                      pilots for new products or markets. </p>
                    <p class="fw-bold text-muted fs-6 text-justify">4Bell Technology can help connect your business's people, information and IT management systems to
                      create a smarter, more successful enterprise. We support thousands of small and medium sized businesses every day, with their own unique blends
                      of our products and services. </p>
                    <p class="fw-bold text-muted fs-6 text-justify">Our aim is to improve client profit, proficiency and productivity while providing our employees with
                      unique and rewarding opportunities. 4Bell Technology provides both Management and Technology Consulting Services to organizations seeking to improve
                      their competitive positioning in their respective markets. </p>
                  </div>
                  <div class="col-lg-12">
                    <!--end::Col-Desc-->
                    <!--begin::Website-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <a href="javascript:void(0);" class="fw-bold fs-6 text-primary">www.4bell.com</a> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Website-->
                    <!--begin::Industry-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">Information Technology &amp; IT Consulting</span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Industry-->
                    <!--begin::Company Size-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted"> 10,390+ employees, 4,866 on LinkedIn <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="4Bell is Information Technology Compnay">
                        <!--begin::Svg Icon-->
                        <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"></circle>
                            <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1"></rect>
                            <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1"></rect>
                          </g>
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        </a> </span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Company Size-->
                    <!--begin::Headquarters-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">Gurgaon, Haryana</span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Headquarters-->
                    <!--begin::Type-->
                    <div class="row mb-7">
                      <!--begin::Titile-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Type</label>
                      <!--end::Titile-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">Private Company</span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Type-->
                    <!--begin::Founded-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">2011</span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Founded-->
                    <!--begin::Specialties-->
                    <div class="row mb-7">
                      <!--begin::Title-->
                      <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>
                      <!--end::Title-->
                      <!--begin::Description-->
                      <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">IT Services, Business Solutions, and Consulting</span> </div>
                      <!--end::Description-->
                    </div>
                    <!--end::Specialties-->
                  </div>
                </div>
                <!--end::overview Content-->
              </div>
            </div>
            <!--end::overview Content-->
            <!--begin::Team Content-->
            <div class="tab-pane" id="team" role="tabpanel">
              <!--begin::Contacts header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Team</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="javascript:void(0);" class="btn btn-primary align-self-center" data-toggle="modal" data-target="#add_team_member">Add Member</a>
                <!--end::Action-->
              </div>
              <!--end::Contacts header-->
              <!--begin::Contacts Content-->
              <div class="card-body px-0 py-7">
                <!--being::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1">
                        </div></th>
                      <th class="min-w-150px">Name</th>
                      <th class="min-w-150px">Role</th>
                      <th class="min-w-150px">Status</th>
                      <th class="min-w-15px">Added Date</th>
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
                          <input class="form-check-input form-check-input1" type="checkbox" value="1">
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Name=-->
                      <td id="kt_activitiesClient_toggle" class="d-flex align-items-center cursor-pointer" onclick="opensidebar();"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">S</div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <span class="text-dark fw-bolder text-hover-primary fs-6">Sobha</span> <span class="text-muted fw-bold d-block fs-7 text-hover-primary">Recruiter</span> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Name-->
                      <!--begin::Role-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Hiring Manager</span> </div></td>
                      <!--end::Role-->
                      <!--begin::Status-->
                      <td class=""><div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                          <input class="form-check-input" type="checkbox" value="" name="staus" checked="checked">
                        </div>
                        <!-- <span class="bg-light-danger text-danger fw-bolder fs-6 px-1">Inactive</span> -->
                      </td>
                      <!--end::Status-->
                      <!--begin::Status-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">06-Sep-2021</span> <span class="text-muted fw-bold d-block fs-7">01:05 PM</span> </div></td>
                      <!--end::Status-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr>
                      <!--begin::Checkbox-->
                      <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input1" type="checkbox" value="1">
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Name=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">P</div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <span class="text-dark fw-bolder text-hover-primary fs-6">Priyanka</span> <span class="text-muted fw-bold d-block fs-7 text-hover-primary">Recruiter</span> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Name-->
                      <!--begin::Role-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Interviewer</span> </div></td>
                      <!--end::Role-->
                      <!--begin::Status-->
                      <td class=""><div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                          <input class="form-check-input" type="checkbox" value="" name="staus">
                        </div>
                        <!-- <span class="bg-light-danger text-danger fw-bolder fs-6 px-1">Inactive</span> -->
                      </td>
                      <!--end::Status-->
                      <!--begin::Status-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">10-Oct-2021</span> <span class="text-muted fw-bold d-block fs-7">03:05 PM</span> </div></td>
                      <!--end::Status-->
                    </tr>
                    <!--end::Table row-->
                  </tbody>
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Contacts Content-->
            </div>
            <!--end::Team Content-->
            <!--begin::Interview Stage Content-->
            <div class="tab-pane" id="interview_Stage" role="tabpanel">
              <!--begin::Interview Stage header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Interview Stage</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
                <!--end::Action-->
              </div>
              <!--end::Interview Stage header-->
              <!--begin::Interview stage Content-->
              <div class="card-body px-0 py-7">
                <div id="kt_docs_jkanban_basic">
                  <div class="kanban-container" style="">
                    <div class="row" style="width: 1230px;">
                      <!--begin::Sourcing-->
                      <div data-id="Sourcing" data-order="1" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header dark d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board">Sourcing</div>
                          <div class="plus-button"> <a class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between" data-class="secondary"> <span class="fw-bold">Sourcing - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span> </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between" data-class="secondary"> <span class="fw-bold">Sourcing - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none"  href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Sourcing-->
                      <!--begim::Screening-->
                      <div data-id="Screening" data-order="2" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header screening d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board text-white">Screening</div>
                          <div class="plus-button"> <a class="btn btn-sm bg-light-info" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between screening-light" data-class=""> <span class="fw-bold">Screening - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between screening-light" data-class=""> <span class="fw-bold">Screening - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none"  class="text-primary me-2" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Screening-->
                      <!--begin::Evaluation-->
                      <div data-id="Evaluation" data-order="3" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header primary d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board text-white">Evaluation</div>
                          <div class="plus-button"> <a class="btn btn-sm btn btn-sm bg-light-primary" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between bg-light-primary" data-class=""> <span class="fw-bold">Evaluation - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between bg-light-primary" data-class=""> <span class="fw-bold">Evaluation - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Evaluation-->
                    </div>
                    <div class="row" style="width: 1230px;">
                      <!--begin::Negotation-->
                      <div data-id="Negotation" data-order="4" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header negotation d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board text-white">Negotation</div>
                          <div class="plus-button"> <a class="btn btn-sm btn btn-sm negotation-light" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between negotation-light" data-class=""> <span class="fw-bold">Negotation - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between negotation-light" data-class=""> <span class="fw-bold">Negotation - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Negotation-->
                      <!--begin::Offer-->
                      <div data-id="Offer" data-order="5" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header offer d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board text-white">Offer</div>
                          <div class="plus-button"> <a class="btn btn-sm btn btn-sm offer-light" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between offer-light" data-class=""> <span class="fw-bold">Offer - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between offer-light" data-class=""> <span class="fw-bold">Offer - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Offer-->
                      <!--begin::Joined-->
                      <div data-id="Joined" data-order="5" class="kanban-board px-0" style="width: 390px; margin-left: 0px; margin-right: 0px;">
                        <header class="kanban-board-header success d-flex align-items-center justify-content-between">
                          <div class="kanban-title-board text-white">Joined</div>
                          <div class="plus-button"> <a class="btn btn-sm btn btn-sm joined-light" data-bs-toggle="modal" data-bs-target="#add_interview_sub_stage">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Add </a> </div>
                        </header>
                        <main class="kanban-drag">
                          <div class="kanban-item d-flex align-items-center justify-content-between joined-light" data-class=""> <span class="fw-bold">Joined - 1</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_interview_sub_stage"> <span class="svg-icon svg-icon-primary" >
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                          <div class="kanban-item d-flex align-items-center justify-content-between joined-light" data-class=""> <span class="fw-bold">Joined - 2</span>
                            <div class="edit_delete cursor-pointer">
                              <!--begin::Edit Icon-->
                              <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                </g>
                              </svg>
                              </span>&nbsp;Edit </a>
                              <!--end::Edit Icon-->
                              <!--begin::Delete Icon-->
                              <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                              </svg>
                              </span>
                              <!-- Delete -->
                              </a>
                              <!--end::Delete Icon-->
                            </div>
                          </div>
                        </main>
                        <footer></footer>
                      </div>
                      <!--end::Joined-->
                    </div>
                  </div>
                </div>
              </div>
              <!--end::Interview stage Content-->
            </div>
            <!--end::Interview Stage Content-->
            <!--begin::Settings Content-->
            <div class="tab-pane" id="settings" role="tabpanel">
              <form  id="setting_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
              <!--begin::Main body div-->
              <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
              <!--begin::Main body section-->
              <div class="card-body px-0 pt-0 pb-0">
              <!--begin::Main Settings Content-->
              <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
              <!--begin::Main Settings Header header-->
              <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#setting_one" aria-expanded="true" aria-controls="kt_account_profile_details">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Settings</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit</a> -->
                <!--end::Action-->
              </div>
              <!--begin::Main Settings Header header-->
              <!--begin::Main Settings Content-->
              <div id="setting_one" class="collapse show">
              <!--begin::Form-->
              <form id="setting_one_form" class="form">
                <!--begin::Card body-->
                <div class="card-body border-top py-8 px-2">
                  <!--begin::Language-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label required fw-bold fs-6">Language</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Input-->
                      <select name="language" aria-label="Select a Language" data-placeholder="Select a language..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                        <option value="" >Select a Language...</option>
                        <option data-kt-flag="flags/indonesia.svg" value="id">Bahasa Indonesia - Indonesian</option>
                        <option data-kt-flag="flags/germany.svg" value="de">Deutsch - German</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en">English</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">English UK - British English</option>
                        <option data-kt-flag="flags/spain.svg" value="es">Español - Spanish</option>
                        <option data-kt-flag="flags/hungary.svg" value="hu">Magyar - Hungarian</option>
                        <option data-kt-flag="flags/india.svg" value="hi">हिन्दी - Hindi</option>
                      </select>
                      <!--end::Input-->
                      <!--begin::Hint-->
                      <div class="form-text">Please select a preferred language, including date, time, and number formatting.</div>
                      <!--end::Hint-->
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Language-->
                  <!--begin::Timezone-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label required fw-bold fs-6">Time Zone</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Input-->
                      <select name="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                        <option value="" >Select a timezone...</option>
                        <option data-kt-flag="flags/indonesia.svg" value="id">(GMT-11:00) Midway Island</option>
                        <option data-kt-flag="flags/germany.svg" value="de">(GMT-11:00) Samoa</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en">(GMT-10:00) Hawaii</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">(GMT-08:00) Alaska</option>
                        <option data-kt-flag="flags/spain.svg" value="es">(GMT-07:00) Pacific Time (US & Canada)</option>
                        <option data-kt-flag="flags/hungary.svg" value="hu">(GMT-06:00) Mountain Time (US & Canada)</option>
                      </select>
                      <!--end::Input-->
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Timezone-->
                  <!--begin::Currency-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6">Currency</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Input-->
                      <select name="currency" aria-label="Select a Currency" data-placeholder="Select a currency..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                        <option value="" >Select a currency...</option>
                        <option data-kt-flag="flags/indonesia.svg" value="id">INR - Rupees</option>
                        <option data-kt-flag="flags/germany.svg" value="de">GBP - British pound</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en">JPY - Japanese yen</option>
                        <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">CAD - Canadian dollar</option>
                        <option data-kt-flag="flags/spain.svg" value="es">USD - USA dollar</option>
                      </select>
                      <!--end::Input-->
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Currency-->
                  <!--begin::Time Format-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6">Time Format</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Input-->
                      <select name="time_format" aria-label="Select a time format" data-placeholder="Select a time format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                        <option value="" >Select a time format...</option>
                        <option data-kt-flag="flags/indonesia.svg" value="id">12 hours</option>
                        <option data-kt-flag="flags/germany.svg" value="de">24 hours</option>
                      </select>
                      <!--end::Input-->
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Time Format-->
                  <!--begin::Date Format-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6">Date Format</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Input-->
                      <select name="date_format" aria-label="Select a date format" data-placeholder="Select a date format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                        <option value="" >Select a date format...</option>
                        <option data-kt-flag="flags/indonesia.svg" value="id">07-Sep-2021</option>
                        <option data-kt-flag="flags/germany.svg" value="de">07-09-2021</option>
                        <option data-kt-flag="flags/germany.svg" value="de">2021/09/07</option>
                        <option data-kt-flag="flags/germany.svg" value="de">September 07, 2021</option>
                      </select>
                      <!--end::Input-->
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Date Format-->
                  <!--begin::Communication-->
                  <div class="row mb-6">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label required fw-bold fs-6">Communication</label>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 fv-row">
                      <!--begin::Options-->
                      <div class="d-flex align-items-center mt-3">
                        <!--begin::Option-->
                        <label class="form-check form-check-inline form-check-solid me-5">
                        <input class="form-check-input" name="communication[]" type="checkbox" value="1">
                        <span class="fw-bold ps-2 fs-6">Email</span> </label>
                        <!--end::Option-->
                        <!--begin::Option-->
                        <label class="form-check form-check-inline form-check-solid">
                        <input class="form-check-input" name="communication[]" type="checkbox" value="2">
                        <span class="fw-bold ps-2 fs-6">Phone</span> </label>
                        <!--end::Option-->
                        <!--begin::Option-->
                        <label class="form-check form-check-inline form-check-solid">
                        <input class="form-check-input" name="communication[]" type="checkbox" value="3">
                        <span class="fw-bold ps-2 fs-6">SMS</span> </label>
                        <!--end::Option-->
                      </div>
                      <!--end::Options-->
                    </div>
                    <!--end::Description-->
                  </div>
                  <!--end::Communication-->
                  <!--begin::Allow Marketing-->
                  <div class="row mb-0">
                    <!--begin::Title-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6">Allow Marketing</label>
                    <!--begin::Title-->
                    <!--begin::Description-->
                    <div class="col-lg-9 d-flex align-items-center">
                      <div class="form-check form-check-solid form-switch fv-row">
                        <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" checked="checked" />
                        <label class="form-check-label" for="allowmarketing"></label>
                      </div>
                    </div>
                    <!--begin::Description-->
                  </div>
                  <!--end::Allow Marketing-->
                </div>
                <!--end::Card body-->
              </form>
              <!--end::Form-->
            </div>
            <!--end::Main Settings Content-->
          </div>
          <!--end::Main Settings Content-->
          <!--begin::Sign in method-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Sign-in Method</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="kt_account_signin_method" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <!--begin::Email Address-->
                <div class="d-flex flex-wrap align-items-center">
                  <!--begin::Label-->
                  <div id="kt_signin_email">
                    <div class="fs-6 fw-bolder mb-1">Email Address</div>
                    <div class="fw-bold text-gray-600">client2021@test.com</div>
                  </div>
                  <!--end::Label-->
                  <!--begin::Edit-->
                  <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                    <!--begin::Form-->
                    <form id="kt_signin_change_email" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                      <div class="row mb-6">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                          <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="emailaddress" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                            <input type="email" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="Email Address" name="emailaddress" value="client2021@test.com">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="confirmemailpassword" class="form-label fs-6 fw-bolder mb-3">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="confirmemailpassword" id="confirmemailpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex">
                        <button id="kt_signin_submit" type="button" class="btn btn-primary me-2 px-6">Update Email</button>
                        <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                      </div>
                      <div></div>
                    </form>
                    <!--end::Form-->
                  </div>
                  <!--end::Edit-->
                  <!--begin::Action-->
                  <div id="kt_signin_email_button" class="ms-auto">
                    <button class="btn btn-light btn-active-light-primary">Change Email</button>
                  </div>
                  <!--end::Action-->
                </div>
                <!--end::Email Address-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-6"></div>
                <!--end::Separator-->
                <!--begin::Password-->
                <div class="d-flex flex-wrap align-items-center mb-10">
                  <!--begin::Label-->
                  <div id="kt_signin_password">
                    <div class="fs-6 fw-bolder mb-1">Password</div>
                    <div class="fw-bold text-gray-600">************</div>
                  </div>
                  <!--end::Label-->
                  <!--begin::Edit-->
                  <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                    <!--begin::Form-->
                    <form id="kt_signin_change_password" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                      <div class="row mb-1">
                        <div class="col-lg-4">
                          <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="currentpassword" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="currentpassword" id="currentpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="newpassword" class="form-label fs-6 fw-bolder mb-3">New Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="newpassword" id="newpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="confirmpassword" class="form-label fs-6 fw-bolder mb-3">Confirm New Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="confirmpassword" id="confirmpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                        </div>
                      </div>
                      <div class="form-text mb-5">Password must be at least 8 character and contain symbols</div>
                      <div class="d-flex">
                        <button id="kt_password_submit" type="button" class="btn btn-primary me-2 px-6">Update Password</button>
                        <button id="kt_password_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                      </div>
                      <div></div>
                    </form>
                    <!--end::Form-->
                  </div>
                  <!--end::Edit-->
                  <!--begin::Action-->
                  <div id="kt_signin_password_button" class="ms-auto">
                    <button class="btn btn-light btn-active-light-primary">Reset Password</button>
                  </div>
                  <!--end::Action-->
                </div>
                <!--end::Password-->
                <!--begin::Notice-->
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                  <!--begin::Icon-->
                  <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                  <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
                    <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <!--end::Icon-->
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                    <!--begin::Content-->
                    <div class="mb-3 mb-md-0 fw-bold">
                      <h4 class="text-gray-900 fw-bolder">Secure Your Account</h4>
                      <div class="fs-6 text-gray-700 pe-7">Two-factor authentication adds an extra layer of security to your account. To log in, in addition you'll need to provide a 6 digit code</div>
                    </div>
                    <!--end::Content-->
                    <!--begin::Action-->
                    <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_two_factor_authentication">Enable</a>
                    <!--end::Action-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Notice-->
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
            <!--begin::Modal - Two-factor authentication-->
            <div class="modal fade" id="kt_modal_two_factor_authentication" tabindex="-1" aria-hidden="true">
              <!--begin::Modal header-->
              <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                  <!--begin::Modal header-->
                  <div class="modal-header flex-stack">
                    <!--begin::Title-->
                    <h2>Choose An Authentication Method</h2>
                    <!--end::Title-->
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
                  <!--begin::Modal header-->
                  <!--begin::Modal body-->
                  <div class="modal-body scroll-y pt-10 pb-15 px-lg-17">
                    <!--begin::Options-->
                    <div data-kt-element="options">
                      <!--begin::Notice-->
                      <p class="text-muted fs-5 fw-bold mb-10">In addition to your username and password, you’ll have to enter a code (delivered via app or SMS) to log into your account.</p>
                      <!--end::Notice-->
                      <!--begin::Wrapper-->
                      <div class="pb-10">
                        <!--begin::Option-->
                        <input type="radio" class="btn-check" name="auth_option" value="apps" checked="checked" id="kt_modal_two_factor_authentication_option_1" />
                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center mb-5" for="kt_modal_two_factor_authentication_option_1">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod001.svg-->
                        <span class="svg-icon svg-icon-4x me-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="black" />
                          <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="black" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <span class="d-block fw-bold text-start"> <span class="text-dark fw-bolder d-block fs-3">Authenticator Apps</span> <span class="text-muted fw-bold fs-6">Get codes from an app like Google Authenticator, Microsoft Authenticator, Authy or 1Password.</span> </span> </label>
                        <!--end::Option-->
                        <!--begin::Option-->
                        <input type="radio" class="btn-check" name="auth_option" value="sms" id="kt_modal_two_factor_authentication_option_2" />
                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center" for="kt_modal_two_factor_authentication_option_2">
                        <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                        <span class="svg-icon svg-icon-4x me-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="black" />
                          <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="black" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <span class="d-block fw-bold text-start"> <span class="text-dark fw-bolder d-block fs-3">SMS</span> <span class="text-muted fw-bold fs-6">We will send a code via SMS if you need to use your backup login method.</span> </span> </label>
                        <!--end::Option-->
                      </div>
                      <!--end::Options-->
                      <!--begin::Action-->
                      <button class="btn btn-primary w-100" data-kt-element="options-select">Continue</button>
                      <!--end::Action-->
                    </div>
                    <!--end::Options-->
                    <!--begin::Apps-->
                    <div class="d-none" data-kt-element="apps">
                      <!--begin::Heading-->
                      <h3 class="text-dark fw-bolder mb-7">Authenticator Apps</h3>
                      <!--end::Heading-->
                      <!--begin::Description-->
                      <div class="text-gray-500 fw-bold fs-6 mb-10">Using an authenticator app like <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a>, <a href="https://www.microsoft.com/en-us/account/authenticator" target="_blank">Microsoft Authenticator</a>, <a href="https://authy.com/download/" target="_blank">Authy</a>, or <a href="https://support.1password.com/one-time-passwords/" target="_blank">1Password</a>, scan the QR code. It will generate a 6 digit code for you to enter below.
                        <!--begin::QR code image-->
                        <div class="pt-5 text-center"> <img src="assets/media/misc/qr.png" alt="" class="mw-150px" /> </div>
                        <!--end::QR code image-->
                      </div>
                      <!--end::Description-->
                      <!--begin::Notice-->
                      <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-10 p-6">
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                        <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                          <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                          <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-grow-1">
                          <!--begin::Content-->
                          <div class="fw-bold">
                            <div class="fs-6 text-gray-700">If you having trouble using the QR code, select manual entry on your app, and enter your username and the code:
                              <div class="fw-bolder text-dark pt-2">KBSS3QDAAFUMCBY63YCKI5WSSVACUMPN</div>
                            </div>
                          </div>
                          <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                      </div>
                      <!--end::Notice-->
                      <!--begin::Form-->
                      <form data-kt-element="apps-form" class="form" action="#">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                          <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Enter authentication code" name="code" />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-center">
                          <button type="reset" data-kt-element="apps-cancel" class="btn btn-light me-3">Cancel</button>
                          <button type="submit" data-kt-element="apps-submit" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
                        </div>
                        <!--end::Actions-->
                      </form>
                      <!--end::Form-->
                    </div>
                    <!--end::Options-->
                    <!--begin::SMS-->
                    <div class="d-none" data-kt-element="sms">
                      <!--begin::Heading-->
                      <h3 class="text-dark fw-bolder fs-3 mb-5">SMS: Verify Your Mobile Number</h3>
                      <!--end::Heading-->
                      <!--begin::Notice-->
                      <div class="text-muted fw-bold mb-10">Enter your mobile phone number with country code and we will send you a verification code upon request.</div>
                      <!--end::Notice-->
                      <!--begin::Form-->
                      <form data-kt-element="sms-form" class="form" action="#">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                          <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Mobile number with country code..." name="mobile" />
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-center">
                          <button type="reset" data-kt-element="sms-cancel" class="btn btn-light me-3">Cancel</button>
                          <button type="submit" data-kt-element="sms-submit" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
                        </div>
                        <!--end::Actions-->
                      </form>
                      <!--end::Form-->
                    </div>
                    <!--end::SMS-->
                  </div>
                  <!--begin::Modal body-->
                </div>
                <!--end::Modal content-->
              </div>
              <!--end::Modal header-->
            </div>
            <!--end::Modal - Two-factor authentication-->
          </div>
          <!--end::Sign in method-->
          <!--begin::Connected Accounts-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Connected Accounts</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="kt_account_connected_accounts" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <!--begin::Notice-->
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                  <!--begin::Icon-->
                  <!--begin::Svg Icon | path: icons/duotune/art/art006.svg-->
                  <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M22 19V17C22 16.4 21.6 16 21 16H8V3C8 2.4 7.6 2 7 2H5C4.4 2 4 2.4 4 3V19C4 19.6 4.4 20 5 20H21C21.6 20 22 19.6 22 19Z" fill="black" />
                    <path d="M20 5V21C20 21.6 19.6 22 19 22H17C16.4 22 16 21.6 16 21V8H8V4H19C19.6 4 20 4.4 20 5ZM3 8H4V4H3C2.4 4 2 4.4 2 5V7C2 7.6 2.4 8 3 8Z" fill="black" />
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <!--end::Icon-->
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-bold">
                      <div class="fs-6 text-gray-700">Two-factor authentication adds an extra layer of security to your account. To log in, in you'll need to provide a 4 digit amazing code. <a href="#" class="fw-bolder">Learn More</a></div>
                    </div>
                    <!--end::Content-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Notice-->
                <!--begin::Items-->
                <div class="py-2">
                  <!--begin::Item-->
                  <div class="d-flex flex-stack">
                    <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
                        <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end">
                      <div class="form-check form-check-solid form-switch">
                        <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" checked="checked">
                        <label class="form-check-label" for="googleswitch"></label>
                      </div>
                    </div>
                  </div>
                  <!--end::Item-->
                  <div class="separator separator-dashed my-5"></div>
                  <!--begin::Item-->
                  <div class="d-flex flex-stack">
                    <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Github</a>
                        <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end">
                      <div class="form-check form-check-solid form-switch">
                        <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch">
                        <label class="form-check-label" for="githubswitch"></label>
                      </div>
                    </div>
                  </div>
                  <!--end::Item-->
                  <div class="separator separator-dashed my-5"></div>
                  <!--begin::Item-->
                  <div class="d-flex flex-stack">
                    <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Slack</a>
                        <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end">
                      <div class="form-check form-check-solid form-switch">
                        <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" />
                        <label class="form-check-label" for="slackswitch"></label>
                      </div>
                    </div>
                  </div>
                  <!--end::Item-->
                </div>
                <!--end::Items-->
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Connected Accounts-->
          <!--begin::Email Preferences-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_email_preferences" aria-expanded="true" aria-controls="kt_account_email_preferences">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Email Preferences</h3>
              </div>
            </div>
            <!--begin::Card header-->
            <!--begin::Content-->
            <div id="kt_account_email_preferences" class="collapse show">
              <!--begin::Form-->
              <form class="form">
                <!--begin::Card body-->
                <div class="card-body border-top py-8 px-2">
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="1" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Successful Payments</span> <span class="text-muted fs-6">Receive a notification for every successful payment.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="2" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Payouts</span> <span class="text-muted fs-6">Receive a notification for every initiated payout.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="3" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Fee Collection</span> <span class="text-muted fs-6">Receive a notification each time you collect a fee from sales</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="4" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Customer Payment Dispute</span> <span class="text-muted fs-6">Receive a notification if a payment is disputed by a customer and for dispute purposes.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="5" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Refund Alerts</span> <span class="text-muted fs-6">Receive a notification if a payment is stated as risk by the Finance Department.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="6" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Invoice Payments</span> <span class="text-muted fs-6">Receive a notification if a customer sends an incorrect amount to pay their invoice.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <div class="separator separator-dashed my-6"></div>
                  <!--end::Option-->
                  <!--begin::Option-->
                  <label class="form-check form-check-custom form-check-solid align-items-start">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="7" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Webhook API Endpoints</span> <span class="text-muted fs-6">Receive notifications for consistently failing webhook API endpoints.</span> </span>
                  <!--end::Label-->
                  </label>
                  <!--end::Option-->
                </div>
                <!--end::Card body-->
              </form>
              <!--end::Form-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Email Preferences-->
          <!--begin::Notifications-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_notifications" aria-expanded="true" aria-controls="kt_account_notifications">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Notifications</h3>
              </div>
            </div>
            <!--begin::Card header-->
            <!--begin::Content-->
            <div id="kt_account_notifications" class="collapse show">
              <!--begin::Form-->
              <form class="form">
                <!--begin::Card body-->
                <div class="card-body border-top py-8 px-2">
                  <!--begin::Table-->
                  <div class="table-responsive">
                    <table class="table table-row-dashed border-gray-300 align-middle gy-6">
                      <tbody class="fs-6 fw-bold">
                        <!--begin::Table row-->
                        <tr>
                          <td class="min-w-250px fs-4 fw-bolder">Notifications</td>
                          <td class="w-125px"><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="kt_settings_notification_email" checked="checked" data-kt-check="true" data-kt-check-target="[data-kt-settings-notification=email]" />
                              <label class="form-check-label ps-2" for="kt_settings_notification_email">Email</label>
                            </div></td>
                          <td class="w-125px"><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="kt_settings_notification_phone" data-kt-check="true" data-kt-check-target="[data-kt-settings-notification=phone]" />
                              <label class="form-check-label ps-2" for="kt_settings_notification_phone">Phone</label>
                            </div></td>
                        </tr>
                        <!--begin::Table row-->
                        <!--begin::Table row-->
                        <tr class="">
                          <td>Billing Updates</td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="1" id="billing1" data-kt-settings-notification="email" />
                              <label class="form-check-label ps-2" for="billing1">Yes</label>
                            </div></td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="billing2" checked="checked" data-kt-settings-notification="phone" />
                              <label class="form-check-label ps-2" for="billing2">Yes</label>
                            </div></td>
                        </tr>
                        <!--begin::Table row-->
                        <!--begin::Table row-->
                        <tr class="">
                          <td>New Team Members</td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="team1" data-kt-settings-notification="email" />
                              <label class="form-check-label ps-2" for="team1">Yes</label>
                            </div></td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="team2" checked="checked" data-kt-settings-notification="phone" />
                              <label class="form-check-label ps-2" for="team2">Yes</label>
                            </div></td>
                        </tr>
                        <!--begin::Table row-->
                        <!--begin::Table row-->
                        <tr class="">
                          <td>Completed Projects</td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="project1" checked="checked" data-kt-settings-notification="email" />
                              <label class="form-check-label ps-2" for="project1">Yes</label>
                            </div></td>
                          <td><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="project2" data-kt-settings-notification="phone" />
                              <label class="form-check-label ps-2" for="project2">Yes</label>
                            </div></td>
                        </tr>
                        <!--begin::Table row-->
                        <!--begin::Table row-->
                        <tr class="">
                          <td class="border-bottom-0">Newsletters</td>
                          <td class="border-bottom-0"><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="newsletter1" data-kt-settings-notification="email" />
                              <label class="form-check-label ps-2" for="newsletter1">Yes</label>
                            </div></td>
                          <td class="border-bottom-0"><div class="form-check form-check-solid">
                              <input class="form-check-input" type="checkbox" value="" id="newsletter2" data-kt-settings-notification="phone" />
                              <label class="form-check-label ps-2" for="newsletter2">Yes</label>
                            </div></td>
                        </tr>
                        <!--begin::Table row-->
                      </tbody>
                    </table>
                  </div>
                  <!--end::Table-->
                </div>
                <!--end::Card body-->
              </form>
              <!--end::Form-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Notifications-->
          <!--begin::Application Duplicate Settings-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#application_duplicate_settings" aria-expanded="true" aria-controls="application_duplicate_settings">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Application Duplicate Setting</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="application_duplicate_settings" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <form class="form">
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="application_duplicate_settings" value="1" checked="checked">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Overroll System</span>
                        <input type="number" name="overroll" value="12345" placeholder="Enter..." class="w-25 rounded input_style">
                        </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="application_duplicate_settings" value="2">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Requisition wise</span>
                        <input type="number" name="requisition_wise" value="12345" placeholder="Enter..." class="w-25 rounded input_style">
                        </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="application_duplicate_settings" value="3">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">None</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                </form>
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Application Duplicate Settings-->
          <!--begin::Company Type-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#company_type" aria-expanded="true" aria-controls="company_type">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Company Type</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="company_type" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <form class="form">
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="company_type" value="1" checked="checked">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Staffing Partners</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="company_type" value="2">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Consulting</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="company_type" value="3">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Other Companies</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                </form>
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Company Type-->
          <!--begin::Account Type-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#acount_type" aria-expanded="true" aria-controls="acount_type">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Account Type</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="acount_type" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <form class="form">
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="acount_type" value="1" checked="checked">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Public</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                      <div class="col-lg-4">
                        <label class="option"> <span class="option-control"> <span class="radio radio-outline">
                        <input type="radio" name="acount_type" value="2">
                        <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Private</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                </form>
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Account Type-->
          <!--begin::Department Type-->
          <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#department" aria-expanded="true" aria-controls="department">
              <!--begin::Card title-->
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Department</h3>
              </div>
              <!--end::Card title-->
              <!--begin::Action-->
              <a href="javascript:void(0);" class="btn btn-primary align-self-center" data-bs-toggle="modal" data-bs-target="#add_department">Add Department</a>
              <!--end::Action-->
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="department" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <form class="form">
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Sales/Business Development</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Marketing/Content</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Logistics/Warehouse</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">IT/Software</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Healthcare</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Events/Media</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Engineer</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Design/Creativity</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">BPO/Telecaller</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                  <div class="form-group fv-plugins-icon-container has-success">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                          <div class="fs-4">Accounts & Finance</div>
                          <div class="edit_delete cursor-pointer">
                            <!--begin::Edit Icon-->
                            <a class="text-primary me-2 d-none" href="javascript:void(0);"> <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>Edit </a>
                            <!--end::Edit Icon-->
                            <!--begin::Delete Icon-->
                            <a class="text-danger" href="javascript:void(0);"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                              </g>
                            </svg>
                            </span>
                            <!-- Delete -->
                            </a>
                            <!--end::Delete Icon-->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-check"></div>
                    <div class="form-text text-muted"></div>
                  </div>
                </form>
              </div>
              <!--end::Card body-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Department Type-->
          <!--begin::Deactivate Account-->
          <div class="card mb-0 mb-xl-0 mx-0 shadow-none rounded-0">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#deactivate_account" aria-expanded="true" aria-controls="deactivate_account">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Deactivate Account</h3>
              </div>
            </div>
            <!--end::Card header-->
            <!--begin::Content-->
            <div id="deactivate_account" class="collapse show">
              <!--begin::Card body-->
              <div class="card-body border-top py-8 px-2">
                <!--begin::Notice-->
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                  <!--begin::Icon-->
                  <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                  <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <!--end::Icon-->
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-bold">
                      <h4 class="text-gray-900 fw-bolder">You Are Deactivating Your Account</h4>
                      <div class="fs-6 text-gray-700">For extra security, this requires you to confirm your email or phone number when you reset yousignr password. <br>
                        <a class="fw-bolder" href="#">Learn more</a></div>
                    </div>
                    <!--end::Content-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Notice-->
                <!--begin::Form input row-->
                <div class="form-check form-check-solid fv-row fv-plugins-icon-container">
                  <input name="deactivate" class="form-check-input" type="checkbox" value="" id="deactivate">
                  <label class="form-check-label fw-bold ps-2 fs-6" for="deactivate">I confirm my account deactivation</label>
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Form input row-->
              </div>
              <!--end::Card body-->
              <!--begin::Card footer-->
              <div class="card-footer d-flex justify-content-end py-3 px-9 d-none">
                <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-danger fw-bold">Deactivate Account</button>
              </div>
              <!--end::Card footer-->
              <input type="hidden">
              <div></div>
            </div>
            <!--end::Content-->
          </div>
          <!--end::Deactivate Account-->
        </div>
        <!--end::Main body content-->
        <!--begin::Main footer-->
        <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white">
          <button class="btn btn-secondary me-2" type="button">Cancel</button>
          <button class="btn btn-primary me-2" type="button">Save Changes</button>
          <button id="kt_account_deactivate_account_submit" type="button" class="btn btn-danger fw-bold">Deactivate Account</button>
        </div>
        <!--end::Main footer-->
        <!--end::Main body div-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Settings Content-->
    </div>
  </div>
  <!--end::Body-->
</div>
<!--end::Tabs-->
<!--end::Card-->
</div>
<!--end::Container-->
</div>
<!--end::Post-->
</div>
<!--end::Content-->
<!--begin::User Profile Sidebar-->
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">User Profile</h3>
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
        <!--begin::User Details-->
        <div class="card mb-5 mb-xl-5 mx-2">
          <!--begin::Card body-->
          <div class="card-body py-5 px-5">
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
              <!--begin:Profile Image-->
              <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-11.jpg" alt="image">
                  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                </div>
              </div>
              <!--end::Profile Image-->
              <!--begin::User Information-->
              <div class="flex-grow-1">
                <!--begin::Row-1-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                  <!--begin::User Details-->
                  <div class="d-flex flex-column">
                    <!--begin::User Name & ID-->
                    <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2">Sobha </span> <span class="text-muted fw-bolder fs-7">C-1009</span> </div>
                    <!--end::User Name & ID-->
                    <!--begin::Designation | Location | Bagde-->
                    <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                      <!--begin::Designation-->
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                      <span class="svg-icon svg-icon-4 me-1">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                          <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        </g>
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      Recruiter </a>
                      <!--end::Designation-->
                      <!--begin::Location-->
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"/>
                        </g>
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      Ghaziabad, INDIA </a>
                      <!--end::Location-->
                      <!--begin::Badge-->
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <polygon points="0 0 24 0 24 24 0 24"></polygon>
                          <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                          <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                        </g>
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      Badge </a>
                      <!--end::Badge-->
                    </div>
                    <!--end::Designaiton | Location | Badge-->
                  </div>
                  <!--end::User Details-->
                  <!--begin::Action-->
                  <div class="d-flex mt-4">
                    <!--begin::Whatsapp-->
                    <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light-success fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
                    <!--begin::Svg Icon-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>Active </a>
                    <!--end::Whatsapp-->
                    <!--begin::Email-->
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary fw-bolder me-3 fs-6">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-primary svg-icon-2x">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>Email </a>
                    <!--end::Email-->
                  </div>
                  <!--end::Action-->
                </div>
                <!--end::Row-1-->
                <!--begin::Row-2-->
                <div class="d-flex flex-wrap flex-stack">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column flex-grow-1 pe-8">
                    <div class="row w-75"> <span class=""> <b>Skills/Tags :</b> <span class="badge badge-light mb-2">Communication</span> <span class="badge badge-light mb-2">Computer Basic Knowledge</span> <span class="badge badge-light mb-2">MBA</span> <span class="badge badge-light mb-2">Good English</span> <span class="badge badge-light mb-2">Typing Speed</span> <span class="badge badge-light mb-2">Communication</span> <span class="badge badge-light mb-2">Computer Basic Knowledge</span> <span class="badge badge-light mb-2">MBA</span> <span class="badge badge-light mb-2">Good English</span> <span class="badge badge-light mb-2">Typing Speed</span> </span> </div>
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Row-2-->
              </div>
              <!--end::User Information-->
            </div>
          </div>
          <!--end::Card body-->
        </div>
        <!--end::User Details-->
        <!--begin::Tabs-->
        <div class="card card-custom mx-2">
          <!--Begin::Tabs Header-->
          <div class="card-header card-header-tabs-line">
            <div class="card-toolbar">
              <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#user_overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
                <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_requisitions"> <span class="nav-text font-weight-bold">Requisitions</span> </a> </li>
                <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
                <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
                <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_joining"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>
                <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>
              </ul>
            </div>
          </div>
          <!--end::Tabs Header-->
          <!--Begin::Tabs Body-->
          <div class="card-body py-0">
          <div class="tab-content pt-5">
            <!--begin::Overview Content-->
            <div class="tab-pane active show" id="user_overview" role="tabpanel">
              <!--begin::Overview header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Overview</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="{{route('staffing_company.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a>
                <!--end::Action-->
              </div>
              <!--end::Overview header-->
              <div class="card-body px-0 py-7">
                <!--begin::Full Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Full Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">Sobha</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Full Name-->
                <!--begin::Designation-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Designation</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">Recruiter</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Designation-->
                <!--begin::Email-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Email</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">sobha@test.com</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Email-->
                <!--begin::Phone-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Phone</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">9090909090</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Phone-->
                <!--begin::Company Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">4Bell Technology</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Name-->
                <!--begin::Company Site-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Site</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">www.4bell.com</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Site-->
                <!--begin::Country-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Country</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6 text-uppercase">india</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Country-->
                <!--begin::State-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">State</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">Uttar Pradesh</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::State-->
                <!--begin::City-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">City</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">Noida</span> </div>
                  <!--end::Col-->
                </div>
                <!--end::City-->
              </div>
            </div>
            <!--end::overview Content-->
            <!--begin::Requisitions Content-->
            <div class="tab-pane" id="user_requisitions" role="tabpanel">
              <!--begin::Requisitions header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Requisitions</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Member</a> -->
                <!--end::Action-->
              </div>
              <!--end::Requisitions header-->
              <!--begin::Requisitions Content-->
              <div class="card-body px-0 py-7">
                <!--begin:Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-req" value="1" />
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
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                  <!--begin::Table body-->
                  <tbody class="text-gray-600 fw-bold">
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
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
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R1</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span class="text-muted fw-bold d-block fs-7">3,974</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                      <!--begin::Staffing Partners-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Amit Kumar"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Staffing Partners-->
                      <!--begin::TOT-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">3,294</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">58</a> </div></td>
                      <!--end::TOT-->
                      <!--begin::SOU-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::SOU-->
                      <!--begin::SCR-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">34</a> </div></td>
                      <!--end::SCR-->
                      <!--begin::EVA-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::EVA-->
                      <!--begin::NEO-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">29</a> </div></td>
                      <!--end::NEO-->
                      <!--begin::OFF-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">32</a> </div></td>
                      <!--end::OFF-->
                      <!--begin::JOI-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::JOI-->
                      <!--begin::REJ-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">384</a> </div></td>
                      <!--end::REJ-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
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
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span> </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 hold-bg text-hover-warning">Hold</a> <span class="fw-bold text-muted d-block fs-7">R2-HCL</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span class="text-muted fw-bold d-block fs-7">774</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Pawan"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                      <!--begin::Staffing Partners-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Abinesh"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Staffing Partners-->
                      <!--begin::TOT-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a> </div></td>
                      <!--end::TOT-->
                      <!--begin::SOU-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::SOU-->
                      <!--begin::SCR-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">23</a> </div></td>
                      <!--end::SCR-->
                      <!--begin::EVA-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::EVA-->
                      <!--begin::NEO-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">24</a> </div></td>
                      <!--end::NEO-->
                      <!--begin::OFF-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">83</a> </div></td>
                      <!--end::OFF-->
                      <!--begin::JOI-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">49</a> </div></td>
                      <!--end::JOI-->
                      <!--begin::REJ-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::REJ-->
                    </tr>
                    <!--end::Table row-->
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
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
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Noida</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span> </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Priyanka"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                      <!--begin::Staffing Partners-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Staffing Partners-->
                      <!--begin::TOT-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a> </div></td>
                      <!--end::TOT-->
                      <!--begin::SOU-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">345</a> </div></td>
                      <!--end::SOU-->
                      <!--begin::SCR-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::SCR-->
                      <!--begin::EVA-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">94</a> </div></td>
                      <!--end::EVA-->
                      <!--begin::NEO-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">03</a> </div></td>
                      <!--end::NEO-->
                      <!--begin::OFF-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::OFF-->
                      <!--begin::JOI-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">45</a> </div></td>
                      <!--end::JOI-->
                      <!--begin::REJ-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">540</a> </div></td>
                      <!--end::REJ-->
                    </tr>
                    <!--end::Table row-->
                  </tbody>
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Requisitions Content-->
            </div>
            <!--end::Requisitions Content-->
            <!--begin::Candidate Content-->
            <div class="tab-pane" id="user_candidate" role="tabpanel">
              <!--begin::Candidate header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Candidate</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit Candidate</a> -->
                <!--end::Action-->
              </div>
              <!--end::Candidate header-->
              <!--begin::Candidate Content-->
              <div class="card-body px-0 py-7">
              <!--begin::Table-->
              <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                <!--begin::Table head-->
                <thead>
                  <!--begin::Table row-->
                  <tr class="fw-bolder text-muted">
                    <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-candidate" value="1" />
                      </div></th>
                    <th class="">Name</th>
                    <th class="">Contacts</th>
                    <th class="">Exp.</th>
                    <th class="">Salary</th>
                    <th class="">Notice Period</th>
                    <th class="">Qualifaction</th>
                    <th class="">Location</th>
                    <th class="">Source</th>
                    <th class="">Upload Date</th>
                    <th class="">Requisitions</th>
                    <th class="">Stage</th>
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
                        <input class="form-check-input form-check-input-candidate" type="checkbox" value="1" />
                      </div></td>
                    <!--end::Checkbox-->
                    <!--begin::Name=-->
                    <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                        <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">S</div>
                        </a> </div>
                      <!--end::Avatar-->
                      <!--begin::User details-->
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a> </div>
                      <!--begin::User details-->
                    </td>
                    <!--end::Name-->
                    <!--begin::Contacts-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 8989898998</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">sonika@test.com</a> </div></td>
                    <!--end::Contacts-->
                    <!--begin::Experience-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">8+</a> <span class="text-muted fw-bold text-muted d-block fs-7">5+</span> </div></td>
                    <!--end::Experience-->
                    <!--begin::Salary-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">18 LPA</a> <span class="text-muted fw-bold text-muted d-block fs-7">14 LPA</span> </div></td>
                    <!--end::Salary-->
                    <!--begin::Notice Period-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">15 Days</a> <span class="text-muted fw-bold text-muted d-block fs-7">Microsoft</span> </div></td>
                    <!--end::Notice Period-->
                    <!--begin::Qualifaction-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">M.Sc(IT)</a> <span class="text-muted fw-bold text-muted d-block fs-7">HTML,Css</span> </div></td>
                    <!--end::Qualifaction-->
                    <!--begin::Location-->
                    <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Noida</a> <span class="text-muted fw-bold text-muted d-block fs-7">U.P | INDIA</span> </div></td>
                    <!--end::Location-->
                    <!--begin::Source-->
                    <td><div class="d-flex flex-column">
                        <div class="symbol-group symbol-hover">
                          <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                        </div>
                      </div>
                      <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span>
                </div>
                
                </td>
                
                <!--end::Source-->
                <!--begin::Upload Date-->
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">10-Nov-2021</a> </div></td>
                  <!--end::Upload Date-->
                  <!--begin::Requisitions-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Designer</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
                  <!--end::Requisitions-->
                  <!--begin::Stage-->
                  <td><a href="javascript.void(0);" class="d-flex flex-column" data-bs-toggle="modal" data-bs-target="#stage_sub_stage"> <span class="badge fw-bolder fs-6 screening-bg px-2 py-1">Screening</span> <span class="text-muted fw-bold text-muted d-block fs-7">Screening - 1</span> </a> </td>
                  <!--end::Stage-->
                </tr>
                <!--end::Table row-->
                <!--begin::Table row-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-candidate" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name=-->
                  <td class="d-flex align-items-center border-bottom-0"><!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="amit kumar image" class="w-100"> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <span class="text-muted fw-bold text-muted d-block fs-7">Andorid Developer</span> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name=-->
                  <!--begin::Contacts-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 9090909090</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">amit@test.com</a> </div></td>
                  <!--end::Contacts-->
                  <!--begin::Experience-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">15+</a> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
                  <!--end::Experience-->
                  <!--begin::Salary-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">26 LPA</a> <span class="text-muted fw-bold text-muted d-block fs-7">18 LPA</span> </div></td>
                  <!--end::Salary-->
                  <!--begin::Notice Period-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">1 Month</a> <span class="text-muted fw-bold text-muted d-block fs-7">HCL Technologies</span> </div></td>
                  <!--end::Notice Period-->
                  <!--begin::Qualifaction-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">B.Tech</a> <span class="text-muted fw-bold text-muted d-block fs-7">SQL Server, Java</span> </div></td>
                  <!--end::Qualifaction-->
                  <!--begin::Location-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Okhla</a> <span class="text-muted fw-bold text-muted d-block fs-7">ND | INDIA</span> </div></td>
                  <!--end::Location-->
                  <!--begin::Source-->
                  <td><div class="d-flex flex-column">
                      <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Sonali kaushik"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                      </div>
                      <span class="text-muted fw-bold text-muted d-block fs-7">TCS</span> </div></td>
                  <!--end::Source-->
                  <!--begin::Upload Date-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">12-Oct-2021</a> </div></td>
                  <!--end::Upload Date-->
                  <!--begin::Requisitions-->
                  <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Marketing</a> <span class="text-muted fw-bold text-muted d-block fs-7">Tech Mahindra</span> </div></td>
                  <!--end::Requisitions-->
                  <!--begin::Stage-->
                  <td><a href="javascript:void(0);" class="d-flex flex-column" data-toggle="modal" data-target="#stage_sub_stage1"> <span class="badge fw-bolder fs-6 rejected-bg px-2 py-1">Rejected</span> <span class="text-muted fw-bold text-muted d-block fs-7">Rejected - 1</span> </a> </td>
                  <!--end::Stage-->
                </tr>
                <!--end::Table row-->
                </tbody>
                
                <!--end::Table body-->
              </table>
              <!--end::Table-->
            </div>
            <!--end::Candidate Content-->
          </div>
          <!--end::Candidate Content-->
          <!--begin::Interview Tracker Content-->
          <div class="tab-pane" id="user_interview_tracker" role="tabpanel">
          <!--begin::Interview Tracker header-->
          <div class="card-header cursor-pointer px-0">
            <!--begin::Card title-->
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Interview Tracker</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
            <!--end::Action-->
          </div>
          <!--end::Interview Tracker header-->
          <!--begin::Interview Tracker Content-->
          <div class="card-body px-0 py-7">
          <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-int-tra" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Requisitions</th>
                <th class="">Interview Mode</th>
                <th class="">Interviewers</th>
                <th class="">Source</th>
                <th class="">Interview Date</th>
                <th class="">Feedback</th>
                <th class="">Rating</th>
                <th class="">Stage</th>
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
                    <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Name=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">S</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <span class="text-muted fw-bold text-muted d-block fs-7">Frontend Developer</span> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Name=-->
                <!--begin::Requisitions=-->
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Designer</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
                <!--end::Requisitions=-->
                <!--begin::Interview Mode=-->
                <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Telecom</a> <span class="text-muted fw-bold text-muted d-block fs-7">Skype/Phone</span> </div></td>
                <!--end::Interview Mode=-->
                <!--begin::Interviewers=-->
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Amit"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Anjali"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Rahul"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Soni"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_18.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="More users list.."> <span class="symbol-label font-weight-bold">5+</span> </div>
                    </div>
                  </div></td>
                </script>
                <!--end::Interviewers=-->
                <!--begin::Source=-->
                <td><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                    </div>
                  </div>
                  <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span>
            </div>
            
            </td>
            
            <!--end::Source=-->
            <!--begin::Interview Date=-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">10-Nov-2021</a> <span class="text-muted fw-bold text-muted d-block fs-7">6:43 am</span> </div></td>
              <!--end::Interview Date=-->
              <!--begin::Feedback=-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#interview_feedback">Shortlisted</a> <span class="text-muted fw-bold text-muted d-block fs-7">Available</span> </div></td>
              <!--end::Feedback=-->
              <!--begin::Rating=-->
              <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span> </td>
              <!--end::Rating=-->
              <!--begin::Stage-->
              <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 screening-bg px-2 py-1">Screening</span> <span class="text-muted fw-bold text-muted d-block fs-7">Screening - 1</span> </div></td>
              <!--end::Stage-->
            </tr>
            <!--end::Table row-->
            <!--begin::Table row-->
            <tr class="">
              <!--begin::Checkbox-->
              <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
                </div></td>
              <!--end::Checkbox-->
              <!--begin::Name=-->
              <td class="d-flex align-items-center"><!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                  <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Max Smith" class="w-100"> </div>
                  </a> </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <span class="text-muted fw-bold text-muted d-block fs-7">Andorid Developer</span> </div>
                <!--begin::User details-->
              </td>
              <!--end::Name=-->
              <!--begin::Requisitions=-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sales</a> <span class="text-muted fw-bold text-muted d-block fs-7">Tech Mahindra</span> </div></td>
              <!--end::Requisitions=-->
              <!--begin::Interview Mode=-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Video</span> <span class="text-muted fw-bold d-block fs-7">Zoom/Google Meet</span> </div></td>
              <!--end::Interview Mode=-->
              <!--begin::Interviewers=-->
              <td><div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover">
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Three"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                  </div>
                </div></td>
              <!--end::Interviewers=-->
              <!--begin::Source=-->
              <td><div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover">
                    <div class="symbol symbol-30 symbol-circle"  data-bs-toggle="tooltip" data-bs-placement="top" title="Anjali"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                  </div>
                </div>
                <span class="text-muted fw-bold d-block fs-7">HCl Technologies</span>
            </div>
            
            </td>
            
            <!--end::Source=-->
            <!--begin::Interview Date=-->
            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">2:30 pm</span> </div></td>
              <!--end::Interview Date=-->
              <!--begin::Feedback=-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Pending</span> <span class="text-muted fw-bold d-block fs-7">No Response</span> </div></td>
              <!--end::Feedback=-->
              <!--begin::Rating=-->
              <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold d-block fs-7">Good</span> </td>
              <!--end::Rating=-->
              <!--begin::Stage-->
              <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 offer-bg px-2 py-1">Offer</span> <span class="text-muted fw-bold d-block fs-7">Offer - 1</span> </div></td>
              <!--end::Stage-->
            </tr>
            <!--end::Table row-->
            <!--begin::Table row-->
            <tr>
              <!--begin::Checkbox-->
              <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
                </div></td>
              <!--end::Checkbox-->
              <!--begin::Name=-->
              <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                  <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">A</div>
                  </a> </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <span class="text-muted fw-bold text-muted d-block fs-7">PHP Developer</span> </div>
                <!--begin::User details-->
              </td>
              <!--end::Name=-->
              <!--begin::Requisitions=-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">SEO</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
              <!--end::Requisitions=-->
              <!--begin::Interview Mode=-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Face to Face</span> <span class="text-muted fw-bold d-block fs-7">Address</span> </div></td>
              <!--end::Interview Mode=-->
              <!--begin::Interviewers=-->
              <td><div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover">
                    <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 1"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 2"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 3"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 4"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                  </div>
                </div></td>
              <!--end::Interviewers=-->
              <!--begin::Source=-->
              <td><div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover">
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Charlie Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Charlie Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                  </div>
                </div>
                <span class="text-muted fw-bold d-block fs-7">TCS</span>
            </div>
            
            </td>
            
            <!--end::Source=-->
            <!--begin::Interview Date=-->
            <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">21-Oct-2021</span> <span class="text-muted fw-bold d-block fs-7">11:35 am</span> </div></td>
              <!--end::Interview Date=-->
              <!--begin::Feedback=-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Rejected</span> <span class="text-muted fw-bold d-block fs-7">No Response</span> </div></td>
              <!--end::Feedback=-->
              <!--begin::Rating=-->
              <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold d-block fs-7">Best</span> </td>
              <!--end::Rating=-->
              <!--begin::Stage-->
              <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 joined-bg px-2 py-1">Joined</span> <span class="text-muted fw-bold text-muted d-block fs-7">Joined - 1</span> </div></td>
              <!--end::Stage-->
            </tr>
            <!--end::Table row-->
            </tbody>
            
            <!--end::Table body-->
          </table>
          <!--end::Table-->
        </div>
        <!--end::Interview Tracker Content-->
      </div>
      <!--end::Interview Tracker  Content-->
      <!--begin::Joinings Content-->
      <div class="tab-pane" id="user_joining" role="tabpanel">
        <!--begin::Joinings header-->
        <div class="card-header cursor-pointer px-0">
          <!--begin::Card title-->
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Joinings</h3>
          </div>
          <!--end::Card title-->
          <!--begin::Action-->
          <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
          <!--end::Action-->
        </div>
        <!--end::Joinings header-->
        <!--begin::Joinings Content-->
        <div class="card-body px-0 py-7">
          <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-joining" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Contact</th>
                <th class="">Requisitions</th>
                <th class="">Source</th>
                <th class="">Offer Amount</th>
                <th class="">Joining Date</th>
                <th class="">Stage</th>
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
                    <input class="form-check-input form-check-input-joining" type="checkbox" value="1">
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Name=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">A</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">PHP Developer</a> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Name-->
                <!--begin::Contact-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">9090909090</span> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">abu@test.com</a> </div></td>
                <!--end::Contact-->
                <!--begin::Requisitions-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Wordpress Developer</span> <span class="text-muted fw-bold d-block fs-7">4Bell Technology</span> </div></td>
                <!--end::Requisitions-->
                <!--begin::Source-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Sonali kaushik"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                    </div>
                    <span class="text-muted fw-bold d-block fs-7">Wipro</span> </div></td>
                <!--end::Source-->
                <!--begin::Offer Ammount-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">65 LPA</span> </div></td>
                <!--end::Offer Ammount-->
                <!--begin::Joining Date-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-July-2021</span> <span class="text-muted fw-bold d-block fs-7">2:05 pm</span> </div></td>
                <!--end::Joining Date-->
                <!--begin::Stage-->
                <td class=""><div class="d-flex flex-column"> <span class="fw-bolder joined-bg fs-6 px-2">Joined</span> </div></td>
                <!--end::Stage-->
              </tr>
              <!--end::Table row-->
              <!--begin::Table row-->
              <tr class="">
                <!--begin::Checkbox-->
                <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input form-check-input-joining" type="checkbox" value="1">
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Name=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">S</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Name-->
                <!--begin::Contact-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">8788987890</span> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">sonika@test.com</a> </div></td>
                <!--end::Contact-->
                <!--begin::Requisitions-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">UI/UX Developer</span> <span class="text-muted fw-bold d-block fs-7 text-hover-primary">Adobe</span> </div></td>
                <!--end::Requisitions-->
                <!--begin::Source-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_9.jpg"> </div>
                    </div>
                    <span class="text-muted fw-bold d-block fs-7">Tech Mahindra</span> </div></td>
                <!--end::Source-->
                <!--begin::Offer Ammount-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45 LPA</span> </div></td>
                <!--end::Offer Ammount-->
                <!--begin::Joining Date-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">27-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">5:05 pm</span> </div></td>
                <!--end::Joining Date-->
                <!--begin::Stage-->
                <td class=""><div class="d-flex flex-column"> <span class="fw-bolder joined-bg fs-6 px-2">Joined</span> </div></td>
                <!--end::Stage-->
              </tr>
              <!--end::Table row-->
            </tbody>
            <!--end::Table body-->
          </table>
          <!--end::Table-->
        </div>
        <!--end::Joinings Content-->
      </div>
      <!--end::Joinings Content-->
      <!--begin::Settings Content-->
      <div class="tab-pane" id="user_settings" role="tabpanel">
        <form  id="setting_content1" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
        <!--begin::Main body div-->
        <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
        <!--begin::Main body section-->
        <div class="card-body px-0 pt-0 pb-0">
        <!--begin::Main Settings Content-->
        <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          <!--begin::Main Settings Header header-->
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#setting_one" aria-expanded="true" aria-controls="kt_account_profile_details">
            <!--begin::Card title-->
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Settings</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit</a> -->
            <!--end::Action-->
          </div>
          <!--begin::Main Settings Header header-->
          <!--begin::Main Settings Content-->
          <div id="setting_one" class="collapse show">
            <!--begin::Card body-->
            <div class="card-body border-top py-8 px-2">
              <!--begin::Language-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label required fw-bold fs-6">Language</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Input-->
                  <select name="language" aria-label="Select a Language" data-placeholder="Select a language..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                    <option value="" >Select a Language...</option>
                    <option data-kt-flag="flags/indonesia.svg" value="id">Bahasa Indonesia - Indonesian</option>
                    <option data-kt-flag="flags/germany.svg" value="de">Deutsch - German</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en">English</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">English UK - British English</option>
                    <option data-kt-flag="flags/spain.svg" value="es">Español - Spanish</option>
                    <option data-kt-flag="flags/hungary.svg" value="hu">Magyar - Hungarian</option>
                    <option data-kt-flag="flags/india.svg" value="hi">हिन्दी - Hindi</option>
                  </select>
                  <!--end::Input-->
                  <!--begin::Hint-->
                  <div class="form-text">Please select a preferred language, including date, time, and number formatting.</div>
                  <!--end::Hint-->
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Description-->
              </div>
              <!--end::Language-->
              <!--begin::Timezone-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label required fw-bold fs-6">Time Zone</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Input-->
                  <select name="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                    <option value="" >Select a timezone...</option>
                    <option data-kt-flag="flags/indonesia.svg" value="id">(GMT-11:00) Midway Island</option>
                    <option data-kt-flag="flags/germany.svg" value="de">(GMT-11:00) Samoa</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en">(GMT-10:00) Hawaii</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">(GMT-08:00) Alaska</option>
                    <option data-kt-flag="flags/spain.svg" value="es">(GMT-07:00) Pacific Time (US & Canada)</option>
                    <option data-kt-flag="flags/hungary.svg" value="hu">(GMT-06:00) Mountain Time (US & Canada)</option>
                  </select>
                  <!--end::Input-->
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Description-->
              </div>
              <!--end::Timezone-->
              <!--begin::Currency-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label fw-bold fs-6">Currency</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Input-->
                  <select name="currency" aria-label="Select a Currency" data-placeholder="Select a currency..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                    <option value="" >Select a currency...</option>
                    <option data-kt-flag="flags/indonesia.svg" value="id">INR - Rupees</option>
                    <option data-kt-flag="flags/germany.svg" value="de">GBP - British pound</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en">JPY - Japanese yen</option>
                    <option data-kt-flag="flags/united-kingdom.svg" value="en-gb">CAD - Canadian dollar</option>
                    <option data-kt-flag="flags/spain.svg" value="es">USD - USA dollar</option>
                  </select>
                  <!--end::Input-->
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Description-->
              </div>
              <!--end::Currency-->
              <!--begin::Time Format-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label fw-bold fs-6">Time Format</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Input-->
                  <select name="time_format" aria-label="Select a time format" data-placeholder="Select a time format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                    <option value="" >Select a time format...</option>
                    <option data-kt-flag="flags/indonesia.svg" value="id">12 hours</option>
                    <option data-kt-flag="flags/germany.svg" value="de">24 hours</option>
                  </select>
                  <!--end::Input-->
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Description-->
              </div>
              <!--end::Time Format-->
              <!--begin::Date Format-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label fw-bold fs-6">Date Format</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Input-->
                  <select name="date_format" aria-label="Select a date format" data-placeholder="Select a date format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                    <option value="" >Select a date format...</option>
                    <option data-kt-flag="flags/indonesia.svg" value="id">07-Sep-2021</option>
                    <option data-kt-flag="flags/germany.svg" value="de">07-09-2021</option>
                    <option data-kt-flag="flags/germany.svg" value="de">2021/09/07</option>
                    <option data-kt-flag="flags/germany.svg" value="de">September 07, 2021</option>
                  </select>
                  <!--end::Input-->
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Description-->
              </div>
              <!--end::Date Format-->
              <!--begin::Communication-->
              <div class="row mb-6">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label required fw-bold fs-6">Communication</label>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 fv-row">
                  <!--begin::Options-->
                  <div class="d-flex align-items-center mt-3">
                    <!--begin::Option-->
                    <label class="form-check form-check-inline form-check-solid me-5">
                    <input class="form-check-input" name="communication[]" type="checkbox" value="1">
                    <span class="fw-bold ps-2 fs-6">Email</span> </label>
                    <!--end::Option-->
                    <!--begin::Option-->
                    <label class="form-check form-check-inline form-check-solid">
                    <input class="form-check-input" name="communication[]" type="checkbox" value="2">
                    <span class="fw-bold ps-2 fs-6">Phone</span> </label>
                    <!--end::Option-->
                    <!--begin::Option-->
                    <label class="form-check form-check-inline form-check-solid">
                    <input class="form-check-input" name="communication[]" type="checkbox" value="3">
                    <span class="fw-bold ps-2 fs-6">SMS</span> </label>
                    <!--end::Option-->
                  </div>
                  <!--end::Options-->
                </div>
                <!--end::Description-->
              </div>
              <!--end::Communication-->
              <!--begin::Allow Marketing-->
              <div class="row mb-0">
                <!--begin::Title-->
                <label class="col-lg-3 col-form-label fw-bold fs-6">Allow Marketing</label>
                <!--begin::Title-->
                <!--begin::Description-->
                <div class="col-lg-9 d-flex align-items-center">
                  <div class="form-check form-check-solid form-switch fv-row">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" checked="checked" />
                    <label class="form-check-label" for="allowmarketing"></label>
                  </div>
                </div>
                <!--begin::Description-->
              </div>
              <!--end::Allow Marketing-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Main Settings Content-->
        </div>
        <!--end::Main Settings Content-->
        <!--begin::Sign in method-->
        <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
        <!--begin::Card header-->
        <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Sign-in Method</h3>
          </div>
        </div>
        <!--end::Card header-->
        <!--begin::Content-->
        <div id="kt_account_signin_method" class="collapse show">
          <!--begin::Card body-->
          <div class="card-body border-top py-8 px-2">
            <!--begin::Email Address-->
            <div class="d-flex flex-wrap align-items-center">
              <!--begin::Label-->
              <div id="kt_signin_email">
                <div class="fs-6 fw-bolder mb-1">Email Address</div>
                <div class="fw-bold text-gray-600">client2021@test.com</div>
              </div>
              <!--end::Label-->
              <!--begin::Edit-->
              <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                <div class="row mb-6">
                  <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="fv-row mb-0 fv-plugins-icon-container">
                      <label for="emailaddress" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                      <input type="email" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="Email Address" name="emailaddress" value="client2021@test.com">
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="fv-row mb-0 fv-plugins-icon-container">
                      <label for="confirmemailpassword" class="form-label fs-6 fw-bolder mb-3">Confirm Password</label>
                      <input type="password" class="form-control form-control-lg form-control-solid" name="confirmemailpassword" id="confirmemailpassword">
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex">
                  <button id="kt_signin_submit" type="button" class="btn btn-primary me-2 px-6">Update Email</button>
                  <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                </div>
              </div>
              <!--end::Edit-->
              <!--begin::Action-->
              <div id="kt_signin_email_button" class="ms-auto">
                <button class="btn btn-light btn-active-light-primary">Change Email</button>
              </div>
              <!--end::Action-->
            </div>
            <!--end::Email Address-->
            <!--begin::Separator-->
            <div class="separator separator-dashed my-6"></div>
            <!--end::Separator-->
            <!--begin::Password-->
            <div class="d-flex flex-wrap align-items-center mb-10">
              <!--begin::Label-->
              <div id="kt_signin_password">
                <div class="fs-6 fw-bolder mb-1">Password</div>
                <div class="fw-bold text-gray-600">************</div>
              </div>
              <!--end::Label-->
              <!--begin::Edit-->
              <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                <div class="row mb-1">
                  <div class="col-lg-4">
                    <div class="fv-row mb-0 fv-plugins-icon-container">
                      <label for="currentpassword" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                      <input type="password" class="form-control form-control-lg form-control-solid" name="currentpassword" id="currentpassword">
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="fv-row mb-0 fv-plugins-icon-container">
                      <label for="newpassword" class="form-label fs-6 fw-bolder mb-3">New Password</label>
                      <input type="password" class="form-control form-control-lg form-control-solid" name="newpassword" id="newpassword">
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="fv-row mb-0 fv-plugins-icon-container">
                      <label for="confirmpassword" class="form-label fs-6 fw-bolder mb-3">Confirm New Password</label>
                      <input type="password" class="form-control form-control-lg form-control-solid" name="confirmpassword" id="confirmpassword">
                      <div class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                  </div>
                </div>
                <div class="form-text mb-5">Password must be at least 8 character and contain symbols</div>
                <div class="d-flex">
                  <button id="kt_password_submit" type="button" class="btn btn-primary me-2 px-6">Update Password</button>
                  <button id="kt_password_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                </div>
              </div>
              <!--end::Edit-->
              <!--begin::Action-->
              <div id="kt_signin_password_button" class="ms-auto">
                <button class="btn btn-light btn-active-light-primary">Reset Password</button>
              </div>
              <!--end::Action-->
            </div>
            <!--end::Password-->
            <!--begin::Notice-->
            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
              <!--begin::Icon-->
              <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
              <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="black"></path>
                <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="black"></path>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <!--end::Icon-->
              <!--begin::Wrapper-->
              <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                <!--begin::Content-->
                <div class="mb-3 mb-md-0 fw-bold">
                  <h4 class="text-gray-900 fw-bolder">Secure Your Account</h4>
                  <div class="fs-6 text-gray-700 pe-7">Two-factor authentication adds an extra layer of security to your account. To log in, in addition you'll need to provide a 6 digit code</div>
                </div>
                <!--end::Content-->
                <!--begin::Action-->
                <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_two_factor_authentication">Enable</a>
                <!--end::Action-->
              </div>
              <!--end::Wrapper-->
            </div>
            <!--end::Notice-->
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Content-->
        <!--begin::Modal - Two-factor authentication-->
        <div class="modal fade" id="kt_modal_two_factor_authentication" tabindex="-1" aria-hidden="true">
        <!--begin::Modal header-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
        <!--begin::Modal header-->
        <div class="modal-header flex-stack">
          <!--begin::Title-->
          <h2>Choose An Authentication Method</h2>
          <!--end::Title-->
          <!--begin::Close-->
          <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
        <!--begin::Modal header-->
        <!--begin::Modal body-->
        <div class="modal-body scroll-y pt-10 pb-15 px-lg-17">
        <!--begin::Options-->
        <div data-kt-element="options">
          <!--begin::Notice-->
          <p class="text-muted fs-5 fw-bold mb-10">In addition to your username and password, you’ll have to enter a code (delivered via app or SMS) to log into your account.</p>
          <!--end::Notice-->
          <!--begin::Wrapper-->
          <div class="pb-10">
            <!--begin::Option-->
            <input type="radio" class="btn-check" name="auth_option" value="apps" checked="checked" id="kt_modal_two_factor_authentication_option_1" />
            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center mb-5" for="kt_modal_two_factor_authentication_option_1">
            <!--begin::Svg Icon | path: icons/duotune/coding/cod001.svg-->
            <span class="svg-icon svg-icon-4x me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="black" />
              <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="black" />
            </svg>
            </span>
            <!--end::Svg Icon-->
            <span class="d-block fw-bold text-start"> <span class="text-dark fw-bolder d-block fs-3">Authenticator Apps</span> <span class="text-muted fw-bold fs-6">Get codes from an app like Google Authenticator, Microsoft Authenticator, Authy or 1Password.</span> </span> </label>
            <!--end::Option-->
            <!--begin::Option-->
            <input type="radio" class="btn-check" name="auth_option" value="sms" id="kt_modal_two_factor_authentication_option_2" />
            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center" for="kt_modal_two_factor_authentication_option_2">
            <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
            <span class="svg-icon svg-icon-4x me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="black" />
              <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="black" />
            </svg>
            </span>
            <!--end::Svg Icon-->
            <span class="d-block fw-bold text-start"> <span class="text-dark fw-bolder d-block fs-3">SMS</span> <span class="text-muted fw-bold fs-6">We will send a code via SMS if you need to use your backup login method.</span> </span> </label>
            <!--end::Option-->
          </div>
          <!--end::Options-->
          <!--begin::Action-->
          <button class="btn btn-primary w-100" data-kt-element="options-select">Continue</button>
          <!--end::Action-->
        </div>
        <!--end::Options-->
        <!--begin::Apps-->
        <div class="d-none" data-kt-element="apps">
        <!--begin::Heading-->
        <h3 class="text-dark fw-bolder mb-7">Authenticator Apps</h3>
        <!--end::Heading-->
        <!--begin::Description-->
        <div class="text-gray-500 fw-bold fs-6 mb-10">Using an authenticator app like <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a>, <a href="https://www.microsoft.com/en-us/account/authenticator" target="_blank">Microsoft Authenticator</a>, <a href="https://authy.com/download/" target="_blank">Authy</a>, or <a href="https://support.1password.com/one-time-passwords/" target="_blank">1Password</a>, scan the QR code. It will generate a 6 digit code for you to enter below.
          <!--begin::QR code image-->
          <div class="pt-5 text-center"> <img src="assets/media/misc/qr.png" alt="" class="mw-150px" /> </div>
          <!--end::QR code image-->
        </div>
        <!--end::Description-->
        <!--begin::Notice-->
        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-10 p-6">
          <!--begin::Icon-->
          <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
          <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          <!--end::Icon-->
          <!--begin::Wrapper-->
          <div class="d-flex flex-stack flex-grow-1">
            <!--begin::Content-->
            <div class="fw-bold">
              <div class="fs-6 text-gray-700">If you having trouble using the QR code, select manual entry on your app, and enter your username and the code:
                <div class="fw-bolder text-dark pt-2">KBSS3QDAAFUMCBY63YCKI5WSSVACUMPN</div>
              </div>
            </div>
            <!--end::Content-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Notice-->
        <!--begin::Form-->
        <form data-kt-element="apps-form" class="form" action="#">
          <!--begin::Input group-->
          <div class="mb-10 fv-row">
            <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Enter authentication code" name="code" />
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="d-flex flex-center">
            <button type="reset" data-kt-element="apps-cancel" class="btn btn-light me-3">Cancel</button>
            <button type="submit" data-kt-element="apps-submit" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Options-->
      <!--begin::SMS-->
      <div class="d-none" data-kt-element="sms">
        <!--begin::Heading-->
        <h3 class="text-dark fw-bolder fs-3 mb-5">SMS: Verify Your Mobile Number</h3>
        <!--end::Heading-->
        <!--begin::Notice-->
        <div class="text-muted fw-bold mb-10">Enter your mobile phone number with country code and we will send you a verification code upon request.</div>
        <!--end::Notice-->
        <!--begin::Form-->
        <form data-kt-element="sms-form" class="form" action="#">
          <!--begin::Input group-->
          <div class="mb-10 fv-row">
            <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Mobile number with country code..." name="mobile" />
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="d-flex flex-center">
            <button type="reset" data-kt-element="sms-cancel" class="btn btn-light me-3">Cancel</button>
            <button type="submit" data-kt-element="sms-submit" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::SMS-->
    </div>
    <!--begin::Modal body-->
  </div>
  <!--end::Modal content-->
</div>
<!--end::Modal header-->
</div>
<!--end::Modal - Two-factor authentication-->
</div>
<!--end::Sign in method-->
<!--begin::Connected Accounts-->
<div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
  <!--begin::Card header-->
  <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Connected Accounts</h3>
    </div>
  </div>
  <!--end::Card header-->
  <!--begin::Content-->
  <div id="kt_account_connected_accounts" class="collapse show">
    <!--begin::Card body-->
    <div class="card-body border-top py-8 px-2">
      <!--begin::Notice-->
      <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
        <!--begin::Icon-->
        <!--begin::Svg Icon | path: icons/duotune/art/art006.svg-->
        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path opacity="0.3" d="M22 19V17C22 16.4 21.6 16 21 16H8V3C8 2.4 7.6 2 7 2H5C4.4 2 4 2.4 4 3V19C4 19.6 4.4 20 5 20H21C21.6 20 22 19.6 22 19Z" fill="black" />
          <path d="M20 5V21C20 21.6 19.6 22 19 22H17C16.4 22 16 21.6 16 21V8H8V4H19C19.6 4 20 4.4 20 5ZM3 8H4V4H3C2.4 4 2 4.4 2 5V7C2 7.6 2.4 8 3 8Z" fill="black" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        <!--end::Icon-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-grow-1">
          <!--begin::Content-->
          <div class="fw-bold">
            <div class="fs-6 text-gray-700">Two-factor authentication adds an extra layer of security to your account. To log in, in you'll need to provide a 4 digit amazing code. <a href="#" class="fw-bolder">Learn More</a></div>
          </div>
          <!--end::Content-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Notice-->
      <!--begin::Items-->
      <div class="py-2">
        <!--begin::Item-->
        <div class="d-flex flex-stack">
          <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
              <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <div class="form-check form-check-solid form-switch">
              <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" checked="checked">
              <label class="form-check-label" for="googleswitch"></label>
            </div>
          </div>
        </div>
        <!--end::Item-->
        <div class="separator separator-dashed my-5"></div>
        <!--begin::Item-->
        <div class="d-flex flex-stack">
          <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Github</a>
              <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <div class="form-check form-check-solid form-switch">
              <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch">
              <label class="form-check-label" for="githubswitch"></label>
            </div>
          </div>
        </div>
        <!--end::Item-->
        <div class="separator separator-dashed my-5"></div>
        <!--begin::Item-->
        <div class="d-flex flex-stack">
          <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Slack</a>
              <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <div class="form-check form-check-solid form-switch">
              <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" />
              <label class="form-check-label" for="slackswitch"></label>
            </div>
          </div>
        </div>
        <!--end::Item-->
      </div>
      <!--end::Items-->
    </div>
    <!--end::Card body-->
  </div>
  <!--end::Content-->
</div>
<!--end::Connected Accounts-->
<!--begin::Email Preferences-->
<div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
  <!--begin::Card header-->
  <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_email_preferences" aria-expanded="true" aria-controls="kt_account_email_preferences">
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Email Preferences</h3>
    </div>
  </div>
  <!--begin::Card header-->
  <!--begin::Content-->
  <div id="kt_account_email_preferences" class="collapse show">
    <!--begin::Card body-->
    <div class="card-body border-top py-8 px-2">
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="1" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Successful Payments</span> <span class="text-muted fs-6">Receive a notification for every successful payment.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="2" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Payouts</span> <span class="text-muted fs-6">Receive a notification for every initiated payout.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="3" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Fee Collection</span> <span class="text-muted fs-6">Receive a notification each time you collect a fee from sales</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="4" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Customer Payment Dispute</span> <span class="text-muted fs-6">Receive a notification if a payment is disputed by a customer and for dispute purposes.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="5" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Refund Alerts</span> <span class="text-muted fs-6">Receive a notification if a payment is stated as risk by the Finance Department.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" value="6" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Invoice Payments</span> <span class="text-muted fs-6">Receive a notification if a customer sends an incorrect amount to pay their invoice.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
      <!--begin::Option-->
      <div class="separator separator-dashed my-6"></div>
      <!--end::Option-->
      <!--begin::Option-->
      <label class="form-check form-check-custom form-check-solid align-items-start">
      <!--begin::Input-->
      <input class="form-check-input me-3" type="checkbox" name="email-preferences[]" checked="checked" value="7" />
      <!--end::Input-->
      <!--begin::Label-->
      <span class="form-check-label d-flex flex-column align-items-start"> <span class="fw-bolder fs-5 mb-0">Webhook API Endpoints</span> <span class="text-muted fs-6">Receive notifications for consistently failing webhook API endpoints.</span> </span>
      <!--end::Label-->
      </label>
      <!--end::Option-->
    </div>
    <!--end::Card body-->
  </div>
  <!--end::Content-->
</div>
<!--end::Email Preferences-->
<!--begin::Notifications-->
<div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
  <!--begin::Card header-->
  <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_notifications" aria-expanded="true" aria-controls="kt_account_notifications">
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Notifications</h3>
    </div>
  </div>
  <!--begin::Card header-->
  <!--begin::Content-->
  <div id="kt_account_notifications" class="collapse show">
    <!--begin::Card body-->
    <div class="card-body border-top py-8 px-2">
      <!--begin::Table-->
      <div class="table-responsive">
        <table class="table table-row-dashed border-gray-300 align-middle gy-6">
          <tbody class="fs-6 fw-bold">
            <!--begin::Table row-->
            <tr>
              <td class="min-w-250px fs-4 fw-bolder">Notifications</td>
              <td class="w-125px"><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="kt_settings_notification_email" checked="checked" data-kt-check="true" data-kt-check-target="[data-kt-settings-notification=email]" />
                  <label class="form-check-label ps-2" for="kt_settings_notification_email">Email</label>
                </div></td>
              <td class="w-125px"><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="kt_settings_notification_phone" data-kt-check="true" data-kt-check-target="[data-kt-settings-notification=phone]" />
                  <label class="form-check-label ps-2" for="kt_settings_notification_phone">Phone</label>
                </div></td>
            </tr>
            <!--begin::Table row-->
            <!--begin::Table row-->
            <tr class="">
              <td>Billing Updates</td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="1" id="billing1" data-kt-settings-notification="email" />
                  <label class="form-check-label ps-2" for="billing1">Yes</label>
                </div></td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="billing2" checked="checked" data-kt-settings-notification="phone" />
                  <label class="form-check-label ps-2" for="billing2">Yes</label>
                </div></td>
            </tr>
            <!--begin::Table row-->
            <!--begin::Table row-->
            <tr class="">
              <td>New Team Members</td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="team1" data-kt-settings-notification="email" />
                  <label class="form-check-label ps-2" for="team1">Yes</label>
                </div></td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="team2" checked="checked" data-kt-settings-notification="phone" />
                  <label class="form-check-label ps-2" for="team2">Yes</label>
                </div></td>
            </tr>
            <!--begin::Table row-->
            <!--begin::Table row-->
            <tr class="">
              <td>Completed Projects</td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="project1" checked="checked" data-kt-settings-notification="email" />
                  <label class="form-check-label ps-2" for="project1">Yes</label>
                </div></td>
              <td><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="project2" data-kt-settings-notification="phone" />
                  <label class="form-check-label ps-2" for="project2">Yes</label>
                </div></td>
            </tr>
            <!--begin::Table row-->
            <!--begin::Table row-->
            <tr class="">
              <td class="border-bottom-0">Newsletters</td>
              <td class="border-bottom-0"><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="newsletter1" data-kt-settings-notification="email" />
                  <label class="form-check-label ps-2" for="newsletter1">Yes</label>
                </div></td>
              <td class="border-bottom-0"><div class="form-check form-check-solid">
                  <input class="form-check-input" type="checkbox" value="" id="newsletter2" data-kt-settings-notification="phone" />
                  <label class="form-check-label ps-2" for="newsletter2">Yes</label>
                </div></td>
            </tr>
            <!--begin::Table row-->
          </tbody>
        </table>
      </div>
      <!--end::Table-->
    </div>
    <!--end::Card body-->
  </div>
  <!--end::Content-->
</div>
<!--end::Notifications-->
<!--begin::Deactivate Account-->
<div class="card mb-0 mb-xl-0 mx-0 shadow-none rounded-0">
  <!--begin::Card header-->
  <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#deactivate_account" aria-expanded="true" aria-controls="deactivate_account">
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Deactivate Account</h3>
    </div>
  </div>
  <!--end::Card header-->
  <!--begin::Content-->
  <div id="deactivate_account" class="collapse show">
    <!--begin::Card body-->
    <div class="card-body border-top py-8 px-2">
      <!--begin::Notice-->
      <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
        <!--begin::Icon-->
        <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
        <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
          <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
          <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
        </svg>
        </span>
        <!--end::Svg Icon-->
        <!--end::Icon-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-grow-1">
          <!--begin::Content-->
          <div class="fw-bold">
            <h4 class="text-gray-900 fw-bolder">You Are Deactivating Your Account</h4>
            <div class="fs-6 text-gray-700">For extra security, this requires you to confirm your email or phone number when you reset yousignr password. <br>
              <a class="fw-bolder" href="#">Learn more</a></div>
          </div>
          <!--end::Content-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Notice-->
      <!--begin::Form input row-->
      <div class="form-check form-check-solid fv-row fv-plugins-icon-container">
        <input name="deactivate" class="form-check-input" type="checkbox" value="" id="deactivate">
        <label class="form-check-label fw-bold ps-2 fs-6" for="deactivate">I confirm my account deactivation</label>
        <div class="fv-plugins-message-container invalid-feedback"></div>
      </div>
      <!--end::Form input row-->
    </div>
    <!--end::Card body-->
    <!--begin::Card footer-->
    <div class="card-footer d-flex justify-content-end py-3 px-9 d-none">
      <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-danger fw-bold">Deactivate Account</button>
    </div>
    <!--end::Card footer-->
    <input type="hidden">
    <div> </div>
  </div>
  <!--end::Content-->
</div>
<!--end::Deactivate Account-->
</div>
<!--end::Main body content-->
<!--begin::Main footer-->
<div class="card-footer d-flex justify-content-center py-6 px-9 bg-white">
  <button class="btn btn-secondary me-2" type="button" id="kt_activitiesClient_close">Cancel</button>
  <button class="btn btn-primary me-2" type="button">Save Changes</button>
  <button id="kt_account_deactivate_account_submit" type="button" class="btn btn-danger fw-bold">Deactivate Account</button>
</div>
<!--end::Main footer-->
</div>
<!--end::Main body div-->
</form>
<!--end::Form-->
</div>
<!--end::Settings Content-->
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
<!--end::User Profile Sidebar-->
<!--begin::Script for User Profile Sidebar-->
<script>
   function opensidebar()
   {
      $('#kt_activitiesClient_toggle').trigger('click');
   } 	
</script>
<!--end::Script for User Profile Sidebar-->
<!--begin::Add Team Member Modal-->
<div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header" id="add_team_member_header">
        <!--begin::Modal title-->
        <h2 class="fw-bolder">Add Team Member</h2>
        <!--end::Modal title-->
        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
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
      <!--begin::Modal body-->
      <div class="modal-body scroll-y px-5 px-xl-8 py-5">
        <!--begin::Form-->
        <form id="add_team_member_form" class="form" action="#">
          <!--begin::Scroll-->
          <div class="d-flex flex-column scroll-y mx-n7 px-7" id="add_team_member_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
            <!--begin::Input group-->
            <div class="fv-row mb-7">
              <!--begin::Label-->
              <label class="d-block fw-bold fs-6 mb-5">Upload Image</label>
              <!--end::Label-->
              <!--begin::Image input-->
              <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png')">
                <!--begin::Preview existing avatar-->
                <div class="image-input-wrapper w-125px h-125px" style="background-image: url('https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg');"></div>
                <!--end::Preview existing avatar-->
                <!--begin::Label-->
                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar"> <i class="bi bi-pencil-fill fs-7"></i>
                <!--begin::Inputs-->
                <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                <input type="hidden" name="avatar_remove" />
                <!--end::Inputs-->
                </label>
                <!--end::Label-->
                <!--begin::Cancel-->
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar"> <i class="bi bi-x fs-2"></i> </span>
                <!--end::Cancel-->
                <!--begin::Remove-->
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar"> <i class="bi bi-x fs-2"></i> </span>
                <!--end::Remove-->
              </div>
              <!--end::Image input-->
              <!--begin::Hint-->
              <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
              <!--end::Hint-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="fv-row mb-7">
              <!--begin::Label-->
              <label class="required fw-bold fs-6 mb-2">Full Name</label>
              <!--end::Label-->
              <!--begin::Input-->
              <input type="text" name="user_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="Abu Baker" />
              <!--end::Input-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="fv-row mb-7">
              <!--begin::Label-->
              <label class="required fw-bold fs-6 mb-2">Email</label>
              <!--end::Label-->
              <!--begin::Input-->
              <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="sonika@gmail.com" />
              <!--end::Input-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="mb-7">
              <!--begin::Label-->
              <label class="required fw-bold fs-6 mb-5">Role</label>
              <!--end::Label-->
              <!--begin::Roles-->
              <!--begin::Input row-->
              <div class="d-flex fv-row">
                <!--begin::Radio-->
                <div class="form-check form-check-custom form-check-solid">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" name="user_role" type="radio" value="0" id="kt_modal_update_role_option_0" checked='checked' />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <label class="form-check-label" for="kt_modal_update_role_option_0">
                  <div class="fw-bolder text-gray-800">Administrator</div>
                  <div class="text-gray-600">Best for business owners and company administrators</div>
                  </label>
                  <!--end::Label-->
                </div>
                <!--end::Radio-->
              </div>
              <!--end::Input row-->
              <div class='separator separator-dashed my-5'></div>
              <!--begin::Input row-->
              <div class="d-flex fv-row">
                <!--begin::Radio-->
                <div class="form-check form-check-custom form-check-solid">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" name="user_role" type="radio" value="1" id="kt_modal_update_role_option_1" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <label class="form-check-label" for="kt_modal_update_role_option_1">
                  <div class="fw-bolder text-gray-800">Developer</div>
                  <div class="text-gray-600">Best for developers or people primarily using the API</div>
                  </label>
                  <!--end::Label-->
                </div>
                <!--end::Radio-->
              </div>
              <!--end::Input row-->
              <div class='separator separator-dashed my-5'></div>
              <!--begin::Input row-->
              <div class="d-flex fv-row">
                <!--begin::Radio-->
                <div class="form-check form-check-custom form-check-solid">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" name="user_role" type="radio" value="2" id="kt_modal_update_role_option_2" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <label class="form-check-label" for="kt_modal_update_role_option_2">
                  <div class="fw-bolder text-gray-800">Analyst</div>
                  <div class="text-gray-600">Best for people who need full access to analytics data, but don't need to update business settings</div>
                  </label>
                  <!--end::Label-->
                </div>
                <!--end::Radio-->
              </div>
              <!--end::Input row-->
              <div class='separator separator-dashed my-5'></div>
              <!--begin::Input row-->
              <div class="d-flex fv-row">
                <!--begin::Radio-->
                <div class="form-check form-check-custom form-check-solid">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" name="user_role" type="radio" value="3" id="kt_modal_update_role_option_3" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <label class="form-check-label" for="kt_modal_update_role_option_3">
                  <div class="fw-bolder text-gray-800">Support</div>
                  <div class="text-gray-600">Best for employees who regularly refund payments and respond to disputes</div>
                  </label>
                  <!--end::Label-->
                </div>
                <!--end::Radio-->
              </div>
              <!--end::Input row-->
              <div class='separator separator-dashed my-5'></div>
              <!--begin::Input row-->
              <div class="d-flex fv-row">
                <!--begin::Radio-->
                <div class="form-check form-check-custom form-check-solid">
                  <!--begin::Input-->
                  <input class="form-check-input me-3" name="user_role" type="radio" value="4" id="kt_modal_update_role_option_4" />
                  <!--end::Input-->
                  <!--begin::Label-->
                  <label class="form-check-label" for="kt_modal_update_role_option_4">
                  <div class="fw-bolder text-gray-800">Trial</div>
                  <div class="text-gray-600">Best for people who need to preview content data, but don't need to make any updates</div>
                  </label>
                  <!--end::Label-->
                </div>
                <!--end::Radio-->
              </div>
              <!--end::Input row-->
              <!--end::Roles-->
            </div>
            <!--end::Input group-->
          </div>
          <!--end::Scroll-->
          <!--begin::Actions-->
          <div class="text-center pt-15">
            <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
            <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--end::Add Team Member Modal-->
<!--begin::Add Interview Sub-Stage modal-->
<div class="modal fade show" id="add_interview_sub_stage" tabindex="-1" aria-modal="true" role="dialog">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content rounded">
      <!--begin::Modal header-->
      <div class="modal-header pb-0 border-0 justify-content-end">
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <!--begin:Form-->
        <form id="add_interview_sub_stage_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <!--begin::Heading-->
          <div class="mb-11 text-center">
            <!--begin::Title-->
            <h1 class="">Add Interview Sub-Stage</h1>
            <!--end::Title-->
          </div>
          <!--end::Heading-->
          <!--begin::Input group-->
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <!--begin::Label-->
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Interview Sub stage Name</span> </label>
            <!--end::Label-->
            <input type="text" class="form-control form-control-solid" placeholder="Enter Sub Stage" name="add_sub_stage">
            <div class="fv-plugins-message-container invalid-feedback">
              <!-- <div data-field="department_name" data-validator="notEmpty">Department Name is required</div> -->
            </div>
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Submit</button>
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
<!--end::Add Interview Sub-Stage modal-->
<!--begin::Add Interview Sub-Stage modal-->
<div class="modal fade show" id="edit_interview_sub_stage" tabindex="-1" aria-modal="true" role="dialog">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content rounded">
      <!--begin::Modal header-->
      <div class="modal-header pb-0 border-0 justify-content-end">
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <!--begin:Form-->
        <form id="edit_interview_sub_stage_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <!--begin::Heading-->
          <div class="mb-11 text-center">
            <!--begin::Title-->
            <h1 class="">Edit Interview Sub-Stage</h1>
            <!--end::Title-->
          </div>
          <!--end::Heading-->
          <!--begin::Input group-->
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <!--begin::Label-->
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Interview Sub stage Name</span> </label>
            <!--end::Label-->
            <input type="text" class="form-control form-control-solid" placeholder="Edit Sub Stage" name="edit_sub_stage">
            <div class="fv-plugins-message-container invalid-feedback">
              <!-- <div data-field="department_name" data-validator="notEmpty">Department Name is required</div> -->
            </div>
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Save Changes</button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end:Form-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--end::Edit Interview Sub-Stage modal-->
<!--begin::Add department modal-->
<div class="modal fade show" id="add_department" tabindex="-1" aria-modal="true" role="dialog">
  <!--begin::Modal dialog-->
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content rounded">
      <!--begin::Modal header-->
      <div class="modal-header pb-0 border-0 justify-content-end">
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
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
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <!--begin:Form-->
        <form id="add_department_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <!--begin::Heading-->
          <div class="mb-11 text-center">
            <!--begin::Title-->
            <h1 class="">Add Department</h1>
            <!--end::Title-->
          </div>
          <!--end::Heading-->
          <!--begin::Input group-->
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <!--begin::Label-->
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Department Name</span> </label>
            <!--end::Label-->
            <input type="text" class="form-control form-control-solid" placeholder="Enter Department Name" name="department_name">
            <div class="fv-plugins-message-container invalid-feedback">
              <!-- <div data-field="department_name" data-validator="notEmpty">Department Name is required</div> -->
            </div>
          </div>
          <!--end::Input group-->
          <!--begin::Actions-->
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
          <!--end::Actions-->
        </form>
        <!--end:Form-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--end::Add department modal-->
<!--begin::Preview Table-->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body"> @if(in_array("add_category", $userPermissions))
        @section('create-button')
        <div class="row">
          <div class="col-md-6 text-left">
            <div class="dropdown dropdown-inline invisible">
              <button class="dropbtn bg-light dropdown-toggle dropbtn-border"> 10 </button>
              <div class="dropdown-content">
                <ul class="navi">
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
                </ul>
              </div>
            </div>
            <!-- Page Title -->
            <span class="card-title pt-2 pb-2">&nbsp;Job Category</span>
            <!-- Page Title End -->
          </div>
          <div class="col-md-6 text-right searchbar-parent">
            <div class="input-group mr-2 searchbar-child" style="">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            <a href="{{ route('staffing_company.job-categories.create') }}" class="btn btn-light-warning btn-sm px-2 py-1 mr-2"> <i class="fa fa-plus-square"></i>&nbsp;Add Category </a> <a onclick="exportExcel()">
            <button class="btn btn-light-primary btn-sm px-2 py-1" type="button"><i class="fa fa-download fa-sm"></i></button>
            </a> </div>
        </div>
        @endsection
        @endif
        <div class="table-responsive m-t-40">
          <table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
            <thead>
              <tr class="table-bg-custom">
                <th>ID</th>
                <th>Category @lang('app.name')</th>
                <th>@lang('app.action')</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end::Preview Table-->
@endsection
@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!--<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>-->
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!--<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>-->
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!--<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>-->
<!--<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>-->
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<!--<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>-->
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<!--<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
   <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
   <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>-->
<script>
   //$(document).ready(function() {
   
   	
   		$('#inputSearch').keyup(function() {
   				
   				//alert("call"+$('#inputSearch').val() );
   				//e.preventDefault();     
   				//table.fnDestroy();      
   				table._fnDraw();      
   		});       
   //});
     
   	
           var table = $('#myTable').dataTable({
   		
   				responsive      : true,
   				processing      : true,
   				serverSide      : true,
   				"bFilter"       : false,
   				stateSave       : true,
   				"bInfo"         : true, // Showing table records
   				"bLengthChange" : false,
   				oLanguage:
   				{
   				  sLengthMenu: "_MENU_",
   				},
   		
   				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
   				"pageLength": 10,
   								      
               /*responsive: true,
               // processing: true, 
               serverSide: true,*/  
   			
   			
              // ajax: '{!! route('staffing_company.job-categories.data') !!}',
   			
   			ajax: {'url' : '{!! route('staffing_company.job-categories.data') !!}',
   				"data": function ( d )
   					{
   						d.search = $('#inputSearch').val()
   					}
   				},
   			
   			
   			
   			
   			
   			
   			
   			 language: {
                   "url": "<?php echo __("app.datatable") ?>"
               },
               "fnDrawCallback": function( oSettings ) {
                   $("body").tooltip({
                       selector: '[data-toggle="tooltip"]'
                   });
               },
   			
   			
           "columnDefs": [
   
               { "width": "5%", "targets": 0 },
               { "width": "85%", "targets": 1 },
               { "width": "10%", "targets": 2 }
               ],
   
               columns: [
                   { data: 'DT_Row_Index', name:'id'},  
                   { data: 'name', name: 'name' },
                   { data: 'action', name: 'action', width: '40%' }
               ]
           });
   
           new $.fn.dataTable.FixedHeader( table );
   
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
   
                       var url = "{{ route('staffing_company.job-categories.destroy',':id') }}";
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
   
   
   
   function exportExcel()
   {
   	var table 		        		= $('#myTable').dataTable();
   	var length 		        		= table.fnGetData().length; 	
   	var oSettings         			= table.fnSettings();
   	var start             			= oSettings._iDisplayStart;					
   	var url 			        	= '{{ route('staffing_company.job-categories.export', [':start' , ':length']) }}';
   	url 					        = url.replace(':start', start);
   	url 					        = url.replace(':length', length);
   	window.location.href  			= url;
   }
   
   
       
</script>
<!--Kanban Script-->
<script src="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.js') }}"></script>
<!--Kanban Script End-->
<script>
   var kanban = new jKanban({
      element: '#kt_docs_jkanban_basic',
      gutter: '0',
      widthBoard: '180px',
      boards: [{
               'id': 'Sourcing',
               'title': 'Sourcing',
               'item': [{
                     'title': '<span class="font-weight-bold">Sourcing - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Sourcing -2</span>'
                  }
               ]
         },{
               'id': 'Screening',
               'title': 'Screening',
               'item': [{
                     'title': '<span class="font-weight-bold">Screening - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Screening -2</span>'
                  }
               ]
         },{
               'id': 'Evaluation',
               'title': 'Evaluation',
               'item': [{
                     'title': '<span class="font-weight-bold">Evaluation - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Evaluation -2</span>'
                  }
               ]
         },{
               'id': 'Negotation',
               'title': 'Negotation',
               'item': [{
                     'title': '<span class="font-weight-bold">Negotation - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Negotation -2</span>'
                  }
               ]
         },{
               'id': 'Offer',
               'title': 'Offer',
               'item': [{
                     'title': '<span class="font-weight-bold">Offer - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Offer -2</span>'
                  }
               ]
         },{
               'id': 'Joined',
               'title': 'Joined',
               'item': [{
                     'title': '<span class="font-weight-bold">Joined - 1</span>'
                  },
                  {
                     'title': '<span class="font-weight-bold">Joined -2</span>'
                  }
               ]
         }
      ]
   });
</script>
@endpush