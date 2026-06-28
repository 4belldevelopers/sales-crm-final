@extends('layouts.demo_admin_app')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush
<style>
body
{
    overflow-x:hidden;
}
</style>
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Staffing Partners-->
      <div class="d-flex flex-column">    
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="{{route('admin.consultancy.index')}}" class="text-gray-800 text-hover-primary mb-1">Staffing Partners</a>
        </h5>
        <span class="text-gray-600">1,715</span>
      </div>
      <!--end::Staffing Partners-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
       <!--begin::Suggested Vendor-->
       <div class="d-flex flex-column">    
          <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
            <a href="https://rm.vibhanshujaiswal.com/admin/consultancy/suggestedPartners/2" class="text-gray-800 text-hover-primary mb-1">Suggested Partners</a>
          </h5>
        <span class="text-gray-600">430</span>
      </div>
      <!--end::Suggested Vendor-->      
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Request-->
       <div class="d-flex flex-column">    
          <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
            <a href="javascript:void(0);" class="text-primary mb-1">Request</a>
          </h5>
        <span class="text-gray-600">240</span>
      </div>
      <!--end::Request-->
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
              <!--end::Svg Icon-->Filter</button>
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
              <!--begin::Add  Staffing Partners<-->
			      <a href="javascript:void(0);">
              <button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
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
              <!--end::Svg Icon-->Add Staffing Partners</button>
			      </a>
              <!--end::Add  Staffing Partners<-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
              <div class="fw-bolder me-5">
              <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
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
    	  <!--beign::Adobe-->
      	<div class="card mb-5 mb-xl-5 mx-2">
          <!--begin::Card body-->
          <div class="card-body px-5">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
              <!--begin: Pic-->
              <div class="me-7 mb-4">
                <!-- <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                  <img src="{{asset('img/hcl_logo.png')}}" alt="client_logo">
                  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                </div> -->
                <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                  <a href="javascript:void(0);">
                    <div class="symbol-label bg-light-primary text-primary border-radius-5 fs-40">A</div>
                  </a>
                  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                </div>
              </div>
              <!--end::Pic-->
              <!--begin::Info-->
              <div class="flex-grow-1">
                <!--begin::Row-1-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                  <!--begin::Vendor Details-->
                  <div class="d-flex flex-column">
                    <!--begin::Name & Candidate ID-->
                    <div class="d-flex align-items-center mb-2">
                      <span class="text-gray-900 fs-2 fw-bolder me-2">
                        Adobe
                      </span>									
                      <!--begin::Svg Icon-->
                      <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                        <span class="svg-icon svg-icon-1 svg-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                          <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                          <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                        </svg>
                        </span>
                      </a>
                      <!--end::Svg Icon-->
                    </div>
                    <!--end::Name & Candidate ID-->
                    <!--begin::Designaiton-->
                    <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                          <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                          <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Industry
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                          <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Noida, INDIA
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Badge
                      </a>
                    </div>
                    <!--end::Designaiton-->
                  </div>
                  <!--end::Vendor Details-->
                  <!--begin::Actions-->
                  <div class="d-flex mt-4">
                    <!--begin::Ignore-->
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Ignore</a>
                    <!--end::Ignore-->
                    <!--begin::Accept-->
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-primary fw-bolder me-3 fs-6">Accept</a>
                    <!--end::Accept-->
                  </div>
                  <!--end::Actions-->
                </div>
                <!--end::Row-1-->
                <!--begin::Row-2-->
                <div class="d-flex flex-wrap flex-stack">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column flex-grow-1 pe-8">
                    <div class="row w-75">
                    <span class="">
                    <span class="badge badge-light my-1">UI/UX Development</span>
                    <span class="badge badge-light my-1">IT Services</span>
                    <span class="badge badge-light my-1">UI/UX Development</span> 
                    <span class="badge badge-light my-1">IT Services</span>  
                    <span class="badge badge-light my-1">UI/UX Development</span>
                    <span class="badge badge-light my-1">IT Services</span> 
                    <span class="badge badge-light my-1">UI/UX Development</span>
                    </span>                  
                    </div>
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Row-2-->
              </div>
              <!--end::Info-->
            </div>
            <!--end::Details-->
            <!--begin::separator-->
            <div class="separator separator-solid my-7 d-none"></div>
            <!--end::separator-->
          </div>
          <!--end::card body-->
      	</div>
      	<!--end::Adobe-->

    	  <!--beign::Tech Mahindra-->
      	<div class="card mb-5 mb-xl-5 mx-2">
          <!--begin::Card body-->
          <div class="card-body px-5">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
              <!--begin: Pic-->
              <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                  <img src="{{asset('img/Tech_mahindra_logo.jpg')}}" alt="client_logo">
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
                    <!--begin::Name & Candidate ID-->
                    <div class="d-flex align-items-center mb-2">
                      <span class="text-gray-900 fs-2 fw-bolder me-2">
                        Tech Mahindra
                      </span>									
                      <!--begin::Svg Icon-->
                      <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                        <span class="svg-icon svg-icon-1 svg-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                          <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                          <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                        </svg>
                        </span>
                      </a>
                      <!--end::Svg Icon-->
                    </div>
                    <!--end::Name & Candidate ID-->
                    <!--begin::Designaiton-->
                    <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                          <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                          <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Industry
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                          <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Noida, INDIA
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Badge
                      </a>
                    </div>
                    <!--end::Designaiton-->
                  </div>
                  <!--end::Candidate Details-->
                  <!--begin::Actions-->
                  <div class="d-flex mt-4">
                    <!--begin::Ignore-->
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Ignore</a>
                    <!--end::Ignore-->
                    <!--begin::Accept-->
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-primary fw-bolder me-3 fs-6">Accept</a>
                    <!--end::Accept-->
                  </div>
                  <!--end::Actions-->
                </div>
                <!--end::Row-1-->
                <!--begin::Row-2-->
                <div class="d-flex flex-wrap flex-stack">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column flex-grow-1 pe-8">
                    <div class="row w-75">
                    <span class="">
                    <span class="badge badge-light my-1">UI/UX Development</span>
                    <span class="badge badge-light my-1">Deep Learning</span>
                    <span class="badge badge-light my-1">IT Company</span>
                    <span class="badge badge-light my-1">Development</span>
                    <span class="badge badge-light my-1">IT Services</span>
                    </span>                  
                    </div>
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Row-2-->
              </div>
              <!--end::Info-->
            </div>
            <!--end::Details-->
            <!--begin::separator-->
            <div class="separator separator-solid my-7 d-none"></div>
            <!--end::separator-->
          </div>
          <!--end::card body-->
      	</div>
      	<!--end::Tech Mahindra-->   

    	  <!--beign::HCL Technologies-->
      	<div class="card mb-5 mb-xl-5 mx-2">
          <!--begin::Card body-->
          <div class="card-body px-5">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
              <!--begin: Pic-->
              <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                  <img src="{{asset('img/hcl_logo.png')}}" alt="client_logo">
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
                    <!--begin::Name & Candidate ID-->
                    <div class="d-flex align-items-center mb-2">
                      <span class="text-gray-900 fs-2 fw-bolder me-2">
                        HCL Technologies
                      </span>									
                      <!--begin::Svg Icon-->
                      <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                        <span class="svg-icon svg-icon-1 svg-icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                          <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                          <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                        </svg>
                        </span>
                      </a>
                      <!--end::Svg Icon-->
                    </div>
                    <!--end::Name & Candidate ID-->
                    <!--begin::Designaiton-->
                    <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                          <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                          <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Industry
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                          <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Greater Noida, INDIA
                      </a>
                      <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                          </g>
                        </svg>
                      </span>
                      <!--end::Svg Icon-->Badge
                      </a>
                    </div>
                    <!--end::Designaiton-->
                  </div>
                  <!--end::Candidate Details-->
                  <!--begin::Actions-->
                  <div class="d-flex mt-4">
                    <!--begin::Ignore button-->
                    <a href="javascript:void(0);" class="btn btn-sm btn-light fw-bolder fs-6 me-3">Ignore</a>
                    <!--begin::Ignore button-->
                    <!--begin::Hire me button-->
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary fw-bolder fs-6 me-3">Accept</a>
                    <!--begin::Hire me button-->
                  </div>
                  <!--end::Actions-->
                </div>
                <!--end::Row-1-->
                <!--begin::Row-2-->
                <div class="d-flex flex-wrap flex-stack">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-column flex-grow-1 pe-8">
                    <div class="row w-75">
                    <span class="">
                    <span class="badge badge-light my-1">IT Company</span>
                    <span class="badge badge-light my-1">Development</span>
                    <span class="badge badge-light my-1">Sales & Marketing</span>
                    <span class="badge badge-light my-1">UI/UX Development</span>
                    <span class="badge badge-light my-1">IT Services</span>   
                    <span class="badge badge-light my-1">Deep Learning</span>
                    </span>                  
                    </div>
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Row-2-->
              </div>
              <!--end::Info-->
            </div>
            <!--end::Details-->
            <!--begin::separator-->
            <div class="separator separator-solid my-7 d-none"></div>
            <!--end::separator-->
          </div>
          <!--end::card body-->
      	</div>
      	<!--end::HCL Technologies-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

<div id="kt_activitiesSP_toggle"></div>
<div id="kt_activitiesSP" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesSP_toggle" data-kt-drawer-close="#kt_activitiesSP_close">
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
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesSP_close">
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
	<div class="card-body position-relative px-0 py-0" id="kt_activitiesSP_body">
		&nbsp;
	</div>
    <!--begin::Body-->
    
</div>
</div>


@endsection

@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

<!--<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>-->

<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script>

$('#column_visibility_control_btn')
.click(function()
{		
    var id 				= 12;
    if(typeof scheduleDate === "undefined") {
        scheduleDate 	= '';
    }
    var url 			= "{{ route('admin.consultancy.load_visibility_control',':id') }}";
    url 				= url.replace(':id', id);
    $('#modelHeading').html('Schedule');
    $.ajaxModal('#scheduleDetailModal', url);
});	
</script>

<script>

$(document).ready(function() {

	
	$('#inputSearch').keyup(function() {
		//alert("call");
		table._fnDraw(); 
	});
});


        var table = $('#myTable').dataTable({
            
		/*	
		responsive      : true,
        processing      : true,
        serverSide      : true,
        "bFilter"       : false,
        stateSave       : true,
        "bInfo"         : true,
        "bLengthChange" : true,
        oLanguage:
        {
          sLengthMenu: "_MENU_",
        },

        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
        "pageLength": 25,

        "columnDefs":
        [ 
            { "width": "3%", "targets": 0 },        
            { "width": "24%", "targets": 1 },        
            { "width": "12%", "targets": 2 },        
            { "width": "12%", "targets": 3 }, 
            { "width": "15%", "targets": 4 },
            { "width": "10%", "targets": 5 },              
            { "width": "10%", "targets": 6 },  
            { "width": "5%", "targets": 7 },        
            { "width": "9%", "targets": 8 }
          ],
		
			
			buttons: [{
						text			:"Visibility Control",
						//collectionTitle	: 'Visibility Control',
						extend			: 'colvis',
						collectionLayout: 'two-column',
						colReorder		: true,
						columnText		: function ( dt, idx, title ) {
											return (idx+1)+': '+title;
										},
						dropup			: true
					}],
					*/
					
        
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
		ajax: {'url' : '{!! route('admin.consultancy.data') !!}',
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
            columns: [
    					{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
						{ data: 'Name', name: 'Name', searchable : false, orderable : false},
						{ data: 'Location', name: 'Location', searchable : false, orderable : false},
						{ data: 'Joining', name: 'Joining', searchable : false, orderable : false},
						{ data: 'Requisitions', name: 'Requisitions', searchable : false, orderable : false},
						{ data: 'ConnectedOn', name: 'ConnectedOn', searchable : false, orderable : false},
						{ data: 'ContactPerson', name: 'ContactPerson', searchable : false, orderable : false}
						
    					
            ]
        });
        new $.fn.dataTable.FixedHeader( table );


		table.on('click', '.show-detail', function ()
		{
	
			$('#kt_activitiesSP_toggle').trigger('click');
			
			var id    		= $(this).data('row-id');
			var url   		= "{{ route('admin.consultancy.show',':id') }}";
			url     		= url.replace(':id', id);
			
			 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
			//alert(url); 
			//$.easyAjax({
			$.ajax({
							type  : 'GET',
							url   : url,
							 beforeSend: function() {
									  $('#kt_activitiesSP_body').html('<img src="'+baseurl+'" height="50px" width="100%">');
							},
							success : function (response) 
							{
									//$('#kt_activitiesSP_body').html(response.view);
									//setTimeout(
									//()=>
									//{
											if (response.status == "success")
											{
												$('#kt_activitiesSP_body').html(response.view);
											}
									//}, 500);*/
								
							}
					});
		}); 
	
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title				: "@lang('errors.areYouSure')",
                text				: "@lang('errors.deleteWarning')",
                type				: "warning",
                showCancelButton	: true,
                confirmButtonColor	: "#DD6B55",
                confirmButtonText	: "@lang('app.delete')",
                cancelButtonText	: "@lang('app.cancel')",
                closeOnConfirm		: true,
                closeOnCancel		: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.consultancy.destroy',':id') }}";
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
	var url 			        	= '{{ route('admin.consultancy.export', [':start' , ':length']) }}';
	url 					        = url.replace(':start', start);
	url 					        = url.replace(':length', length);
	window.location.href  			= url;
}
</script>
@endpush