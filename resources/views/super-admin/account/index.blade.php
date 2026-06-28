@extends('layouts.superadmin_layout')

<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">
<style>
body {overflow-x:hidden; }
.table > :not(caption) > * > * { padding: 0.9rem 0.2rem!important; }
.kanban-container .kanban-board { float: none; flex-shrink: 0; margin-bottom: 1.25rem; background: #fff; border-radius: 0.475rem; padding-right: 1.5em!important; }
header { position: relative !important; top: 0; left: 0; display: flex; flex-direction: row; flex-wrap: wrap; width: 100%; height: 80px; background-color: #318ebb; box-shadow: 0 0px 8px rgb(212 212 212 / 40%);}
.svg-icon.svg-icon-danger-Sourcing svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #318ebb !important }
.svg-icon.svg-icon-danger-Screening svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease;fill: #c000ff !important }
.svg-icon.svg-icon-danger-Evaluation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #009ef7 !important }
.svg-icon.svg-icon-danger-Negotiation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ff4098 !important}
.svg-icon.svg-icon-danger-Offer svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ffab00 !important}
.svg-icon.svg-icon-danger-Joined svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #50cd89 !important }
</style>

@section('content')
 
@php $logo_path				=	url('img/client-default.png'); @endphp
@if(!empty(@$account->logo) && strlen(@$account->logo)>10)
		@php $logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); @endphp  	
@endif
  
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span class="text-gray-800 mb-1">Company Profile</span> </h5>
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
<div id="kt_content_container" class="container-fluid px-0">
<!--beign::Company Details-->
<div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body pt-5 pb-0 px-5">
    <!--begin::Details-->
    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
      <!--begin: Pic-->
      <div class="me-7 mb-4">
        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="{{ @$logo_path }}" alt="image">
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
           
            <div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$account->company_name) }}</span> 
				<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Verified">
              <span class="svg-icon svg-icon-1 svg-icon-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
              </svg>
              </span>
              </a> 
			  </div>
            
            <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                  <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                </g>
              </svg>
              </span> 
			  	{{ @$industries_name }}
			  </a> 
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
              </svg>
              </span>
			  	{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}
              </a>
			  
			   <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>
              ------- </a> 
			  </div>
            <!--end::Designaiton-->
          </div>
          <!--end::Company Details-->
        </div>
        <!--end::Row-1-->
        <!--begin::Row-2-->
        <div class="d-flex flex-wrap flex-stack">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column flex-grow-1 pe-8">
            <div class="row w-75"> <span class="">
				 @if(!empty($client->tags))
					 @php $tags	=	explode(',', $client->tags); @endphp 
					 @foreach($tags as $key=>$val) 
						{!! '<span class="badge badge-light me-2">'.ucwords(@$val).'</span>' !!}
					 @endforeach
				 @endif		
              </span> </div>
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
        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Type</span> <span class="fw-bolder fs-3">
				@if($account->industries_type >= 0)
					{{ $industries_type_arr[$account->industries_type] }}    
				@endif						
          </span> </div>
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
        <div class="d-flex flex-column text-dark-75"> 
			<span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3">{{ @$account->company_size }}</span> 
		</div>
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
        <div class="d-flex flex-column text-dark-75"> 
			<span class="fw-bolder fs-6">Active Req.</span> <span class="fw-bolder fs-3">{{ @$activeJobs }}</span> 
		</div>
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
        <div class="d-flex flex-column text-dark-75"> 
			<span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3">{{ @$yearAgo }}</span> 
		</div>
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
        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span class="fw-bolder fs-3">{{ @$joined }}</span> </div>
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
					 		
@if(!empty($team))
		@foreach($team as $teamVal)
			@php
				$name				=	'';
				$name				=	ucwords($teamVal->name);
				$logo_path			=	url('public/user-uploads/team_profile_img/'.@$teamVal->image); 	
			@endphp	
			{!! '<div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="'.@$name.'"><img alt="Pic" src="'.@$logo_path.'"></div>' !!}
		@endforeach
@endif                                 
@if($team_count > 5)
			{!! '<div class="symbol symbol1 symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="List.."><span class="symbol-label symbol-label1 font-weight-bold">5+</span></div>' !!}
@endif                                  
 
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
    @if(in_array("edit_settings", $userPermissions))
    <!--begin::Action-->
    <a href="{{ route('service_company.account.edit', 1) }}" class="btn btn-primary align-self-center">Edit Profile</a>
    <!--ensd::Action-->
    @endif </div>
  <!--end::Overview header-->
  <div class="card-body px-0 py-7"> @if(in_array("view_settings", $userPermissions))
    <!--begin::overview Content-->
    <div class="row">
      <!--begin::Col-Desc-->
      <div class="col-lg-12 mb-7"> {{ @$account->description }} </div>
      <div class="col-lg-12">
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
          <div class="col-lg-9"> <a href="javascript:void(0);" class="fw-bold fs-6 text-primary">{{ @$account->website }}</a> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
            {{ @$industries_name }}
            </span> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted"> {{ @$account->company_size }}+ employees <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="4Bell is Information Technology Compnay">
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
            </a> </span> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
		  	{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}
            </span> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Type</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">                           
					  @if($account->industries_type >= 0)					  	
						 {{ $industries_type_arr[$account->industries_type] }}   
					  @endif				             	
            </span> </div>
        </div>      
        <div class="row mb-7">         
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>          
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">{{ @$account->founded }}</span> </div>         
        </div>        
        <div class="row mb-7">          
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>          
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">{{ @$client->tags}}</span> </div>          
        </div>
       
      </div>
    </div>
    
    @endif </div>
</div>
<!--end::overview Content-->
<!--begin::Interview Stage Content-->
<div class="tab-pane" id="interview_Stage" role="tabpanel">
  <!--begin::Interview Stage header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Interview Stage</h3>
    </div>
    
  </div>
  <div class="card-body px-0 py-7">
    <div id="kt_docs_jkanban_basic1">
      <div class="kanban-container" style="">
        <div class="row w-100" style="">
          <div data-id="Sourcing" data-order="1" class="col-md-4 kanban-board px-0" id="base_sourcing" >
            <header class="kanban-board-header dark d-flex align-items-center justify-content-between position-absolute bg-transparent" style="background-color:#318ebb !important" >
              <div class="kanban-title-board">Sourcing</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn-light interview_stage" data-class="bg-secondary" data-title="Sourcing" data-id="1" data-root="root_sourcing" data-toggle="modal" data-target="#add_interview_sub_stage" style="color:#318ebb !important"> <span class="svg-icon svg-icon svg-icon me-0" >
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span> Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_sourcing" style="margin-top:80px;" >
@if(!empty($interviewStage_sourcing))
	  @foreach($interviewStage_sourcing as $val)									  				
	  {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between bg-secondary root_child">
								 <span class="fw-bold" style="color:#318ebb !important" >'.@$val->name.'</span>
								 <div class="edit_delete cursor-pointer">                 
									<a class="stage_block text-danger delete_process" href="javascript:void(0);" rel="'.@$val->id.'" >
									   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Sourcing">
										  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24"/>
												<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
												<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
											 </g>
										  </svg>
									   </span>
									</a>          											 
								 </div>
							  </li>' !!}
	  @endforeach
@endif	

            </ul>
            @endif </div>
          <div data-id="Screening" data-order="2" class="col-md-4 kanban-board px-0" >
            <header class="kanban-board-header screening d-flex align-items-center justify-content-between" style="background-color:#c000ff !important" >
              <div class="kanban-title-board text-white">Screening</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm bg-light-info interview_stage" data-class="screening-light" data-title="Screening" data-id="2" data-root="root_screening" data-toggle="modal" data-target="#add_interview_sub_stage" style="background-color:#fae9ff !important; color:#c000ff !important">
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
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_screening">
@if(!empty($interviewStage_screening))
		@foreach($interviewStage_screening as $val)
			{!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between screening-light" data-class="">
						 <span class="fw-bold">'.@$val->name.'</span>
						 <div class="edit_delete cursor-pointer">
							<a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
							   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Screening">
								  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
										<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
									 </g>
								  </svg>
							   </span>
							</a>              
						 </div>
					  </li>' !!}
		@endforeach
@endif
            </ul>
            @endif </div>
          <div data-id="Evaluation" data-order="3" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header primary d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Evaluation</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm bg-light-primary interview_stage" data-class="bg-light-primary" data-title="Evaluation" data-id="3" data-root="root_evaluation" data-toggle="modal" data-target="#add_interview_sub_stage" style="color: #009ef7 !important">
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
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_evaluation" style="position: relative;" >
@if(!empty($interviewStage_evaluation))
	@foreach($interviewStage_evaluation as $val)
		{!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between bg-light-primary" data-class="">
			 <span class="fw-bold" style="color:#009ef7">'.$val->name.'</span>
			 <div class="edit_delete cursor-pointer">
				<a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'" >
				   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Evaluation">
					  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
						 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<rect x="0" y="0" width="24" height="24"/>
							<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
							<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
						 </g>
					  </svg>
				   </span>				
				</a>          
			 </div>
		  </li>' !!}
	@endforeach
@endif
            </ul>
            @endif </div>
        </div>
        <div class="row w-100" style="">
          <div data-id="Negotation" data-order="4" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header negotation d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Negotiation</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm negotation-light interview_stage" data-class="negotation-light" data-title="Negotation" data-id="4" data-root="root_negotation"  data-toggle="modal" data-target="#add_interview_sub_stage">
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
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_negotation" style="position: relative;">
@if(!empty($interviewStage_negotiation))
	@foreach($interviewStage_negotiation as $val)
		{!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between negotation-light" data-class="">
					 <span class="fw-bold">'.$val->name.'</span>
					 <div class="edit_delete cursor-pointer">						
						<a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
						   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Negotiation">
							  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24"/>
									<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
									<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
								 </g>
							  </svg>
						   </span>
						</a>             						
					 </div>
				  </li>' !!}
	@endforeach
@endif
            </ul>
            @endif </div>
          <div data-id="Offer" data-order="5" class="col-md-4 kanban-board px-0" >
            <header class="kanban-board-header offer d-flex align-items-center justify-content-between" style="background-color:#ffab00!important;">
              <div class="kanban-title-board text-white">Offer</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm offer-light interview_stage" data-class="offer-light" data-title="Offer" data-id="5" data-root="root_offer" data-toggle="modal" data-target="#add_interview_sub_stage">
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
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_offer" style="position: relative;">
@if(!empty($interviewStage_offered))
	@foreach($interviewStage_offered as $val)
		{!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between offer-light" data-class="">
					 <span class="fw-bold">'.$val->name.'</span>
					 <div class="edit_delete cursor-pointer">
						<a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
						   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Offer">
							  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24"/>
									<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
									<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
								 </g>
							  </svg>
						   </span> 
						</a>     
					 </div>
				  </li>' !!}
@endforeach
@endif
            </ul>
            @endif </div>
          <div data-id="Joined" data-order="5" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header success d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Joined</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm joined-light interview_stage" data-class="joined-light" data-title="Joined" data-id="6" data-root="root_joined" data-toggle="modal" data-target="#add_interview_sub_stage">
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
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_joined" style="position: relative;">
@if(!empty($interviewStage_joined))
	@foreach($interviewStage_joined as $val)
		{!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between joined-light" data-class="">
					 <span class="fw-bold">'.$val->name.'</span>
					 <div class="edit_delete cursor-pointer">
						<a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
						   <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Joined">
							  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24"/>
									<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
									<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
								 </g>
							  </svg>
						   </span> 
						</a>     
					 </div>
				  </li>' !!}
	@endforeach
@endif 
            </ul>
            @endif </div>
        </div>
      </div>
    </div>
  </div>
  <!--end::Interview stage Content-->
</div>
<!--end::Interview Stage Content-->
<!--begin::Settings Content-->
<div class="tab-pane" id="settings" role="tabpanel">
<!--begin::Form-->
<form  id="setting_content" name="setting_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="pagename" id="pagename" value="settings" />
  @csrf  
  @method('POST')
  <!--begin::Main body div-->
  <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0"> @if(in_array("view_settings", $userPermissions))
    <div class="card-body px-0 pt-0 pb-0">
      <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
        <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#setting_one" aria-expanded="true" aria-controls="kt_account_profile_details">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Settings</h3>
          </div>
        </div>
        <div id="setting_one" class="collapse show">
          <div class="card-body border-top py-8 px-2">
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label required fw-bold fs-6">Language</label>
              <div class="col-lg-9 fv-row">
                <select name="language" id="language" aria-label="Select a Language" data-placeholder="Select a language..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value=""  >Select a Language...</option>
                    @if(!empty($languagesArr))
                    @foreach($languagesArr as $lval)
                        @php $sel	=	''; 
                        if($account->language == $lval->id ){
                            $sel	=	'selected="selected"'; 
                          }elseif($lval->id == 1){
                             $sel	=	'selected="selected"';
                          }
                        @endphp 
                        
                        {!! '<option  value="'.$lval->id.'" '.@$sel.'>'.$lval->name.'</option>' !!}
                    @endforeach
                    @endif 
                </select>
                <div class="form-text">Please select a preferred language, including date, time, and number formatting.</div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label required fw-bold fs-6">Time Zone</label>
              <div class="col-lg-9 fv-row">
                <select name="timezone" id="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a timezone...</option>
                    @if(!empty($timezone))
                    @foreach($timezone as $tval)
                      @php $sel	=	''; 
                      if($account->timezone == $tval->time_zone ){
                        $sel	=	'selected="selected"';
                      }elseif($tval->time_zone=="Asia/Kolkata"){
                        $sel	=	'selected="selected"';
                      }
                     @endphp

                        {!! '<option value="'.$tval->time_zone.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.'('.$tval->time_zone.')</option>' !!}
                    @endforeach
                    @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Currency</label>
              <div class="col-lg-9 fv-row">
                <select name="currency" id="currency" aria-label="Select a Currency" data-placeholder="Select a currency..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a currency...</option>
                  @if(!empty($currencyArr))
                    @foreach($currencyArr as $cval)
                      @php $sel	=	''; 
                      if($account->currency == $cval->id ){
                          $sel	=	'selected="selected"';
                      }elseif($cval->id==35){
                        $sel	=	'selected="selected"';
                      } 
                     @endphp
                      {!! '<option  value="'.$cval->id.'" '.$sel.'>'.$cval->code.' - '.$cval->country.'</option>' !!}
                    @endforeach
                  @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
           
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Time Format</label>
              <div class="col-lg-9 fv-row">
                <select name="time_format" id="time_format" aria-label="Select a time format" data-placeholder="Select a time format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a time format...</option>
                @if(!empty($time_formatArr))
                  @foreach($time_formatArr as $tval)
                    @php $sel	=	'';
                    if($account->time_format == $tval->id ){
                       $sel	=	'selected="selected"'; 
                      }elseif($tval->id==1){
                       $sel	=	'selected="selected"'; 
                      }
                     @endphp 
                    {!! '<option  value="'.$tval->id.'"  '.$sel.'>'.$tval->name.'</option>' !!}
                  @endforeach
                @endif	
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
           
            <div class="row mb-6">
              <!--begin::Title-->
              <label class="col-lg-3 col-form-label fw-bold fs-6">Date Format</label>
              <!--end::Title-->
              <!--begin::Description-->
              <div class="col-lg-9 fv-row">
                <!--begin::Input-->
                <select name="date_format" id="date_format" aria-label="Select a date format" data-placeholder="Select a date format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a date format...</option>
                    @if(!empty($date_formatArr))
                      @foreach($date_formatArr as $dval)
                        @php $sel	=	''; 
                        if($account->date_format == $dval->id ){
                          $sel	=	'selected="selected"';
                        }elseif($dval->id==1){
                          $sel	=	'selected="selected"';
                        }
                           
                          
                          @endphp 
                          {!! '<option  value="'.$dval->id.'"  '.$sel.'>'.$dval->name.'</option>' !!}
                      }
                      @endforeach
                    @endif
                </select>
                <!--end::Input-->
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
              <!--end::Description-->
            </div>
          </div>
        </div>
      </div>
      <!--end::Main Settings Content-->
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
            <!--begin::Items-->
            <div class="py-2">
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
                    <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end"> <a href="javascript:void(0);" class="google_config" rel="GOOGLE" data-bs-toggle="modal" data-bs-target="#google_config"><i class="fas fa-cogs"></i>&nbsp;Configuration&nbsp;</a>
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="google_status" name="google_status" value="1" @php if($account->google_status == 1){ echo 'checked="checked"'; } @endphp  >
                    <label class="form-check-label" for="googleswitch"></label>
                  </div>
                </div>
              </div>
              <!--end::Item-->
              <div class="separator separator-dashed my-5"></div>
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Zoom</a>
                    <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end"> <a href="javascript:void(0);" class="zoom_config" rel="ZOOM" data-bs-toggle="modal" data-bs-target="#zoom_config"><i class="fas fa-cogs"></i>&nbsp;Configuration&nbsp;</a>
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="zoom_status" name="zoom_status" value="1" @php if($account->zoom_status == 1){ echo 'checked="checked"'; } @endphp >
                    <label class="form-check-label" for="githubswitch"></label>
                  </div>
                </div>
              </div>
              <!--end::Item-->
              <div class="separator separator-dashed my-5"></div>
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Microsoft</a>
                    <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="microsoft_status" name="microsoft_status" />
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
 
          @php $app_duplicate_overroll_val	= $app_duplicate_requisition_wise_val	=	'';  @endphp 
          @if($account->app_duplicate  == 2)
            @php $app_duplicate_overroll_val			=	$account->app_duplicate_days; @endphp 
          @endif
          @if($account->app_duplicate  == 1)
            @php $app_duplicate_requisition_wise_val	=	$account->app_duplicate_days; @endphp 
          @endif


        <div class="form-group fv-plugins-icon-container has-success">
          <div class="row">
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="2" @php if($account->app_duplicate == 2){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Overroll System</span>
              <input type="number" name="app_duplicate_overroll" id="app_duplicate_overroll" value="{{ @$app_duplicate_overroll_val }}" placeholder="Enter..." class="w-25 rounded input_style">
              </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="1" @php if($account->app_duplicate == 1){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Requisition wise</span>
              <input type="number" name="app_duplicate_requisition_wise" value="{{ @$app_duplicate_requisition_wise_val }}" placeholder="Enter..." class="w-25 rounded input_style">
              </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="0" @php if($account->app_duplicate == 0){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">None</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
          </div>
          <div class="form-check"></div>
          <div class="form-text text-muted"></div>
        </div>
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Application Duplicate Settings-->
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
        <div class="form-group fv-plugins-icon-container has-success">
          <div class="row">
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="acount_type" value="1" checked="checked" @php if($account->acount_type == 1){ echo 'checked="checked"'; } @endphp >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Public</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="acount_type" value="2" @php if($account->acount_type == 2){ echo 'checked="checked"'; } @endphp >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Private</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
          </div>
          <div class="form-check"></div>
          <div class="form-text text-muted"></div>
        </div>
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Account Type-->
  <!--begin::Department Type-->
  <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
    <!--begin::Card header-->
    <div class="card-header row">
      <div class="cursor-pointer px-0 card-title m-0 col-lg-9" role="button" data-toggle="collapse" data-target="#department" aria-expanded="true" aria-controls="department">
        <h3 class="fw-bolder m-0">Department</h3>
      </div>
      <div class="card-title m-0 col-lg-2 "> @if(in_array("add_settings", $userPermissions)) <a href="javascript:void(0);" class="btn btn-primary align-self-center position-absolute end-0" data-toggle="modal" data-target="#add_department"> Add Department </a> @endif </div>
    </div>
    <!--end::Card header-->
    <!--begin::Content-->
    <div id="department" class="collapse show">
      <!--begin::Card body-->
      <div class="card-body border-top py-8 px-2 dept_content">
@php 
		  $dept		=	'';
		  $cnt		=	0;
		  if(!empty($departmentArr)){
			 foreach($departmentArr as $dval){
										if($cnt %3==0)
										{
										   $dept	.=	'<div class="form-group fv-plugins-icon-container has-success">
													   <div class="row">';					
										}
										$cnt++;
										$dept	.=	'<div class="col-lg-4">
																		 <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
																		 <div class="fs-4">'.$dval->name.'</div>
																		 <div class="edit_delete cursor-pointer">													  
								  <a class="text-primary me-2 d-none" href="javascript:void(0);">
																			   <span class="svg-icon svg-icon-primary">                           
																				  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																				  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																					 <rect x="0" y="0" width="24" height="24"></rect>
																					 <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
																					 <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
																				  </g>
																				  </svg>
																			   </span>Edit
																			</a>                        
																			
																			
																			<!--begin::Delete Icon-->
																			<a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
																			   <span class="svg-icon svg-icon-danger svg-icon-2x">
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
																	  </div>';
										if($cnt %3==0)
										{										
										   $dept	.=	'	</div>
													   <div class="form-check"></div>
													   <div class="form-text text-muted"></div>
													</div>';
										}			 
			 }
		  }			 
				   
		  echo $dept;									 
@endphp 
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
    
    <div id="deactivate_account" class="collapse show">
      <div class="card-body border-top py-8 px-2">
        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
          <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
          </svg>
          </span>
          <div class="d-flex flex-stack flex-grow-1">
            <div class="fw-bold">
              <h4 class="text-gray-900 fw-bolder">You Are Deactivating Your Account</h4>
              <div class="fs-6 text-gray-700">For extra security, this requires you to confirm your email or phone number when you reset yousignr password. <br>
                <a class="fw-bolder" href="#">Learn more</a> </div>
            </div>
          </div>
        </div>
        
        <div class="form-check form-check-solid fv-row fv-plugins-icon-container">
          <input name="status" class="form-check-input" type="checkbox" value="inactive" id="status" @php if($account->status == 'inactive'){ echo 'checked="checked"'; } @endphp >
          <label class="form-check-label fw-bold ps-2 fs-6" for="deactivate">I confirm my account deactivation</label>
          <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
       
      </div>
     
      <div class="card-footer d-flex justify-content-end py-3 px-9 d-none">
        <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-danger fw-bold">Deactivate Account</button>
      </div>
      
      <input type="hidden">
      <div></div>
    </div>
    
  </div>
  <!--end::Deactivate Account-->
  </div>
  <!--end::Main body content-->
  @endif 
  
  
@if(in_array("edit_settings", $userPermissions))
  <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white">
   <a href="{{ route('service_company.account.index') }}" class="btn btn-secondary me-2" >
    	Cancel
	</a>
    <button class="btn btn-primary me-2 updateCompanyProfile" type="button" id="updateCompanyProfile">Save Changes</button>
  </div>
@endif
  </div>
 
</form>
<!--end::Form-->
</div>
<!--end::Settings Content-->
</div>
<!--end::Body-->
</div>
<!--end::Tabs-->
<!--end::Card-->
</div>
<!--end::Tabs-->
</div>
<!--end::Container-->
</div>


<!--begin::Add Interview Sub-Stage modal-->
<div class="modal fade show" id="add_interview_sub_stage" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span>
        </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="add_interview_sub_stage_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <input type="hidden" name="ltb_stage" id="ltb_stage" value="" />
          <input type="hidden" name="ltb_root" id="ltb_root" value="" />
          <input type="hidden" name="ltb_class" id="ltb_class" value="" />
          <div class="mb-11 text-center">
            <h1 class="h1_title">&nbsp;</h1>
          </div>
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required lbl_title">&nbsp;</span> </label>
            <input type="text" class="form-control form-control-solid" placeholder="Enter Sub Stage" name="ltb_stage_name" id="ltb_stage_name">
            <div class="fv-plugins-message-container invalid-feedback">
            </div>
          </div>
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="ltb_submit">Submit</button>
          </div>
          <div></div>
        </form>
        <div id="add_interview_sub_stage_form_loader">&nbsp;</div>
      </div>
    </div>
  </div>
</div>
<!--end::Add Interview Sub-Stage modal--> 

<!--begin::Google config-->
<div class="modal fade show" id="google_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="google_frm" name="google_frm" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="google_success_alert" style="display:none"> <strong>Success!</strong> Update goole configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="google_warning_alert" style="display:none"> <strong>Warning!</strong> Goole configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Google Configuration Settings</h1>
          </div>
          <div class="google_load" id="google_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Google config-->

<!--begin::Add department modal-->
<div class="modal fade show" id="add_department" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="add_department_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="dept_success_alert" style="display:none"> <strong>Success!</strong> Department Added successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="dept_warning_alert" style="display:none"> <strong>Warning!</strong> Department can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Add Department</h1>
          </div>
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Department Name</span> </label>
            <input type="text" class="form-control form-control-solid" placeholder="Enter Department Name" name="dept_name" id="dept_name">
            <div class="fv-plugins-message-container invalid-feedback"> </div>
          </div>
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary submit_department">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Add department modal-->

<!--begin::Zoom config-->
<div class="modal fade show" id="zoom_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="zoom_configfrm" class="form fv-plugins-bootstrap5 fv-plugins-framework zoom_configfrm" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="zoom_success_alert" style="display:none"> <strong>Success!</strong> Update zoom configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="zoom_warning_alert" style="display:none"> <strong>Warning!</strong> Zoom configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Zoom Configuration Settings</h1>
          </div>
          <div class="zoom_load" id="zoom_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--begin::Zoom config-->

@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">
<script>

		$('body').on('click', '.interview_stage', function(){
   
		   $('#add_interview_sub_stage_form').show();
		   $('#add_interview_sub_stage_form_loader').hide();
		   
		   $('.h1_title').html('');	
		   $('.lbl_title').html('');	
		   $('#ltb_stage_name').val('');	
		   
			var id 				=	$(this).data('id');
			var root			=	$(this).data('root');	
			var ltb_class		=	$(this).data('class');
		   
			if(id == 1){
				$('.h1_title').html('Add Sourcing Sub-Stage');	
			$('.lbl_title').html('Sourcing Sub stage Name')
			}else if(id == 2){
				$('.h1_title').html('Add Screening Sub-Stage');	
			$('.lbl_title').html('Screening Sub stage Name')
			}else if(id == 3){
				$('.h1_title').html('Add Evaluation Sub-Stage');	
			$('.lbl_title').html('Evaluation Sub stage Name')
			}else if(id == 4){
				$('.h1_title').html('Add Negotation Sub-Stage');	
			$('.lbl_title').html('Negotation Sub stage Name')
			}else if(id == 5){
				$('.h1_title').html('Add Offer Sub-Stage');	
			$('.lbl_title').html('Offer Sub stage Name')
			}else if(id == 6){
				$('.h1_title').html('Add Joined Sub-Stage');	
			$('.lbl_title').html('Joined Sub stage Name')
			}
		   $('#ltb_stage').val(id);
		   $('#ltb_root').val(root); 
		   $('#ltb_class').val(ltb_class); 
   });

		$('body').on('click', '#ltb_submit', function(e){
         
				var ltb_stage			=	$('#ltb_stage').val();
				var ltb_root			=	$('#ltb_root').val();
				var ltb_stage_name		=	$('#ltb_stage_name').val();
				var ltb_class			=	$('#ltb_class').val();
				var url 				=	"{{ route('service_company.account.addIntStage') }}";
				var token 				=	"{{ csrf_token() }}";
				var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
										
				$.ajax({
							type		: 'POST',
							url			: url,
							beforeSend	: function() {
														$('#add_interview_sub_stage_form').hide();
														$('#add_interview_sub_stage_form_loader').show();
														$('#add_interview_sub_stage_form_loader').html('<img src="'+baseurl+'" height="60%" width="100%">');
													},
							data		: {'_token': token, 'ltb_stage':ltb_stage, 'ltb_stage_name':ltb_stage_name, 'ltb_class':ltb_class },
							success		: function (response) {
																	if (response.status == "success") {
																											 $("#"+ltb_root).last().append(response.stageHtml);
																											 $('#add_interview_sub_stage_form_loader').show();
																											 $('#add_interview_sub_stage_form').hide();
																											 $('#add_interview_sub_stage_form_loader').html(response.message);
																									  }
															 }
					});
   					
     });	   
	 
		$('body').on('click', '.delete_process', function(){
                 var id = 123;
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
                         var url 	=	"{{ route('service_company.job-categories.destroy',':id') }}";
                         url 		=	url.replace(':id', id);
                         var token 	=	"{{ csrf_token() }}";
                         $.ajax({
										 type	: 'POST',
										 url	: url,
										 data	: {'_token': token, '_method': 'DELETE'},
										 success: function (response) {
											 if (response.status == "success") {
												 $.unblockUI();
												 table._fnDraw();
											 }
										 }
							 });
                     }
                 });
             });
	
		$('#kt_docs_jkanban_basic1').on('click', '.stage_block', function() { 
    	
   		var block 		=	$(this).attr('rel');		
   		if(block > 0){
   							var url 				= "{{ route('service_company.account.delIntStage') }}";
   							var token 				= "{{ csrf_token() }}";							
   							var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
							
							const swalWithBootstrapButtons = Swal.mixin({
															customClass		: {	confirmButton	: 'btn btn-success',	cancelButton	: 'btn btn-danger'},
															buttonsStyling	: false
							})
							swalWithBootstrapButtons.fire({
														  title				: 'Are you sure?',
														  text				: "You won't be able to revert this!",
														  icon				: 'warning',
														  showCancelButton	: true,
														  confirmButtonText	: 'Yes, Delete it!',
														  cancelButtonText	: 'No, cancel!',
														  reverseButtons	: true
										}).then((result) => {					
													  if (result.isConfirmed) {
													  
$.ajax({
			type		: 'POST',
			url			: url,
			beforeSend	: function() {},
			data		: {'_token': token, 'block':block},
			success		: function (response) {
				if (response.status == "success") {
													$('#'+block).remove();	 
													swalWithBootstrapButtons.fire
												('Deleted!',	'Selected request has been Deleted successfully.',	'success')																														
				}
				else
				{
					swalWithBootstrapButtons.fire
					( 'Opps Error, Please try again!', 'Something wrong, please try again.', 'success' )
				}								  
			}
});

														} 
														else if (result.dismiss === Swal.DismissReason.cancel) {
															swalWithBootstrapButtons.fire('Cancelled', 'Selected request is safe :)', 'error' )
														}				
									})} });  
			 
		$('body').on('click', '.google_config', function(){
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.google',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type			:	'GET',
   											url				:	url,
   											beforeSend		:	function() {		
																		 $('#google_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
																},
   											success			: function (response) {
   																					if (response.status == "success") {
   																						$('#google_load').html(response.view);	 	
   																					}	
   																			 }
   								});
   });
   		
		 $('body').on('click', '.zoom_config', function(){   								
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.zoom',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type		:	'GET',
   											url			:	url,
   											beforeSend	: 	function() {		
   														 				$('#zoom_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
   															},
   											success		: 	function (response) {
   																					if (response.status == "success") {
   																						$('#zoom_load').html(response.view);	 	
   																					}	
   																			 }
   								});   								
   		});
   
   
</script>      

<script>

$(document).ready(function(){
   
   			$('.submit_department').click(function(){
   									var dept_name	=	$('#dept_name').val();								
									if(dept_name ==''){
											$('#dept_name').val('');	
											$("#dept_warning_alert").fadeTo(2000, 500).slideUp(500, function() {
												$("#dept_warning_alert").slideUp(500);
											});	
											return false;
									}
									var url 					= "{{ route('service_company.account.storeDept') }}";
									var token 					= "{{ csrf_token() }}";
									var baseurl    				= "{{ asset('assets/images/Loading2.gif') }}"; 		
									$.ajax({
												type		: 'POST',
												url			: url,
												beforeSend	: function() { $('.dept_content').html('<img src="'+baseurl+'" height="60%" width="100%">'); },
												data		: {'_token': token, 'dept_name':dept_name },
												success		: function (response) {
																						if (response.status == "success") {
																																$('#dept_name').val('');
																																$("#dept_success_alert").fadeTo(2000, 500).slideUp(500, function() {
																																	$("#dept_success_alert").slideUp(500);
																																});
																																$('.dept_content').html(response.html);																																	
																														  }
																				 }
									});
   				});
				
			$('body').on('click', '.dept_delete', function(){
   	   			
   				   var id				=	$(this).attr('rel');   			   			
				   Swal.fire({
				   title				: "@lang('errors.areYouSure')",
				   text					: "@lang('errors.deleteWarning')",
				   type					: "warning",
				   showCancelButton		: true,
				   confirmButtonColor	: "#DD6B55",
				   confirmButtonText	: "@lang('app.delete')",
				   cancelButtonText		: "@lang('app.cancel')",
				   closeOnConfirm		: true,
				   closeOnCancel		: true
				   })
						   .then((result) => {
											  if (result.isConfirmed) {
											  
																			var url 		=	"{{ route('service_company.job-categories.destroy',':id') }}";
																			url 			=	url.replace(':id', id);
																			var token 		=	"{{ csrf_token() }}";
																			$.ajax({
																						type	: 'POST',
																						url		: url,
																						data	: {'_token': token, '_method': 'DELETE'},
																						success	: function (response) {
																														  if(response.status == "success") {
																																Swal.fire('Deleted!', 'Your record has been deleted.', 'success')
																														   }
																													  }
																				});											   																			
																	  }
						   })	
   					});				
			
			$('body').on('click', '#updateCompanyProfile', function(){
   								
					var data 				=	new FormData();
					var form_data 			=	$('#setting_content').serializeArray();
					$.each(form_data, function (key, input) {
							data.append(input.name, input.value);
					});				
   			   
						$.ajax({
									url					:	'{{route('service_company.account.update', @$account->id)}}',
									headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
									method				:	"POST",
									processData			:	false,
									contentType			:	false,
									data				:	data,
									redirect			:	true,
									success				:	function (data) {
																				if(data.status == 'success'){
																						 Swal.fire("Success", "Account settings update successfully!", "success");	
																				}
															}
								});				
   				});	
	
});

</script>	
@endpush