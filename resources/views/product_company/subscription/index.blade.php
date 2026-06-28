@extends('layouts.product_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">

<style>
body
{
  overflow-x:hidden;
}
.w-md-130px {
    width: 130px !important;
}
.active_plan
{
  width: fit-content;
}

.btn-group.hg-plan-btn-group
{
    width: 100%;
}
.shared-hosting .container_hosting-package .btn-group button.btn {
    padding: 10px 15px;
}
.shared-hosting .container_hosting-package .btn-group button.btn {
    padding: 14px 15px;
    text-align: left;
    width: 100%;
}
.btn.btn-info.hg-plan-btn-info {
    box-shadow: none;
    border-color: #ddd;
    width: 100%;
    float: none;
}
.btn-group .selected-term {
    display: inline;
    color: #333;
}
.btn-group .selected-term .term-length {
    color: #666;
    font-size: 14px;
    text-transform: capitalize;
}
.btn-group .selected-term .WebRupee.webrupee1, .btn-group .selected-term span.WebRupee {
    color: #0171bc;
}
.hg-plan-btn-group span.WebRupee {
    font-size: 22px;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.hg-plan-btn-group span.term-price {
    font-size: 22px;
    font-weight: 800;
}
.btn-group .selected-term .term-price {
    font-weight: 700;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.btn-group .selected-term .term-price {
    font-weight: 700;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.selected-term .testsdfd {
    margin-left: 3%;
    margin-right: 12%;
    line-height: 17px;
    float: none;
}
.hg-percentage-discount {
      color: #f15922;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}
span.hg-actual-price {
    display: block;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}
.hg-plan-btn-group .hg-actual-price span.WebRupee {
    font-size: 14px;
}
.WebRupee {
    font-family: 'WebRupee' !important;
}
span.hg-actual-price {
    display: block;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}

.package-term
{
  font-size:16px;
}
.dropdown-menu li
{
  padding: .8em 1em;
  cursor: pointer;
  border-bottom: 1px dotted lightgray;
  display: block;
}
.dropdown-menu li:last-child
{
  border-bottom: none;
}
</style>

@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
   <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
        <div class="d-flex flex-column">
            <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
               <span class="text-gray-800 mb-1">Subscription Package</span>
            </h5>
            <span class="text-gray-600" id="">&nbsp;</span>
         </div>
         <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="d-flex align-items-center py-1 d-none ">
          <!--begin::Add to cart-->
          <a href="{{ route('product_company.subscribe.cart') }}" type="button" class="btn btn-sm btn-primary position-relative fs-6 me-3 mt-1">
              <span class="svg-icon svg-icon-primary svg-icon-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                    <path d="M3.28077641,9 L20.7192236,9 C21.2715083,9 21.7192236,9.44771525 21.7192236,10 C21.7192236,10.0817618 21.7091962,10.163215 21.6893661,10.2425356 L19.5680983,18.7276069 C19.234223,20.0631079 18.0342737,21 16.6576708,21 L7.34232922,21 C5.96572629,21 4.76577697,20.0631079 4.43190172,18.7276069 L2.31063391,10.2425356 C2.17668518,9.70674072 2.50244587,9.16380623 3.03824078,9.0298575 C3.11756139,9.01002735 3.1990146,9 3.28077641,9 Z M12,12 C11.4477153,12 11,12.4477153 11,13 L11,17 C11,17.5522847 11.4477153,18 12,18 C12.5522847,18 13,17.5522847 13,17 L13,13 C13,12.4477153 12.5522847,12 12,12 Z M6.96472382,12.1362967 C6.43125772,12.2792385 6.11467523,12.8275755 6.25761704,13.3610416 L7.29289322,17.2247449 C7.43583503,17.758211 7.98417199,18.0747935 8.51763809,17.9318517 C9.05110419,17.7889098 9.36768668,17.2405729 9.22474487,16.7071068 L8.18946869,12.8434035 C8.04652688,12.3099374 7.49818992,11.9933549 6.96472382,12.1362967 Z M17.0352762,12.1362967 C16.5018101,11.9933549 15.9534731,12.3099374 15.8105313,12.8434035 L14.7752551,16.7071068 C14.6323133,17.2405729 14.9488958,17.7889098 15.4823619,17.9318517 C16.015828,18.0747935 16.564165,17.758211 16.7071068,17.2247449 L17.742383,13.3610416 C17.8853248,12.8275755 17.5687423,12.2792385 17.0352762,12.1362967 Z" fill="#000000"/>
                </g>
              </svg>
            </span>Cart
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                20+
                <span class="visually-hidden">Add to cart list</span>
              </span>
            </a>

          <!--begin::Add to cart-->
      </div>
   </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
      <div id="kt_content_container" class="container-fluid px-3">
          <!--begin::card-->
          <div class="card bg-light">
              <!--begin::card body-->
              <div class="card-body pt-3 px-0 pb-4">
			  
			  	<div class="card shadow-none">
                    <!--begin::Header-->
                    <div class="card-header px-7">
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Current Package</h3>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none">
                          <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z" fill="#000000" opacity="0.3"/>
                                  <path d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z" fill="#000000"/>
                              </g>
                            </svg>
                          </span>
                          See Pricing
                        </a>
                    </div>
                    <!--end::Header-->
                    <!--begin::Table-->
                    <div class="card-body">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="logins_list">
                          <thead>
                              <tr class="fw-bolder text-muted">
                                <th class="w-10px pe-2">#</th>
                                <th class="">Product</th>
                                <th class="">Quantity Allocated</th>
                                <th class="">Consumed</th>
                                <th class="">Remaining</th>
                               
							    <th class="">Start Date</th>
                                <th class="">End Date</th>
								<th class="">Status</th>
                              </tr>
                          </thead>
                          <tbody class="text-gray-600 fw-bold">
								
								<tr class="odd">
									<td class="invisible1">
										1.
									</td>
									<td class=" align-items-center">
										<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
										  <a href="javascript:void(0);" class="">
											<div class="symbol-label fs-3 bg-dark-light text-light border-radius-5">
											  <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->
											  <span class="svg-icon svg-icon-danger svg-icon-2x">
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
													  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														  <polygon points="0 0 24 0 24 24 0 24"/>
														  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
														  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
													  </g>
												  </svg>
											  </span>
											</div>
										  </a>
										</div>                  
										<div class="d-flex flex-column">
										  <div class="">
											<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">CV Access / Received</a>
										  </div>
											<span class="badge badge-light-success active_plan d-none">Active Plan</span>
									  </div>                          
									</td>
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
										<?php echo @$package_active->cvaccess_quantity? number_format($package_active->cvaccess_quantity):''; ?>
									</td>
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
										<?php echo @$package_active->cvaccess_used? number_format($package_active->cvaccess_used):'0'; ?>
									</td>
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
										<?php echo @$package_active->cvaccess_balance? number_format($package_active->cvaccess_balance):'0'; ?>
									</td>
									
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
										<?php echo @$package_active->cvaccess_startdate? date('d M, Y', strtotime(date($package_active->cvaccess_startdate))):''; ?>
									</td>
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
										<?php echo @$package_active->cvaccess_enddate? date('d M, Y', strtotime(date($package_active->cvaccess_enddate))):''; ?>
									</td> 
									<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php 
											echo    @$package_active->cvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
									?>
									</td>
								  </tr>
							  
                              	<tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      2.
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                          <span class="svg-icon svg-icon-warning  svg-icon-2x">
                                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <polygon points="0 0 24 0 24 24 0 24"/>
                                                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                  </g>
                                              </svg>
                                          </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Users</a>
                                        </div>
                                          
                                    </div>
                                  </td>
                               
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->users_quantity? number_format($package_active->users_quantity):'0'; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->users_used? number_format($package_active->users_used):'0'; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->users_balance? number_format($package_active->users_balance):'0'; ?>
								</td>
                               
							    <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->users_startdate? date('d M, Y', strtotime(date($package_active->users_startdate))):''; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->users_enddate? date('d M, Y', strtotime(date($package_active->users_enddate))):''; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
								<?php 
										echo    @$package_active->users_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
								?>	
								</td>
                              </tr>                              	
							  
							  	<tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      3.
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                          <span class="svg-icon svg-icon-success  svg-icon-2x">
                                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <polygon points="0 0 24 0 24 24 0 24"/>
                                                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                  </g>
                                              </svg>
                                          </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">New Bench Client Request</a>
                                        </div>
                                          
                                    </div>
                                  </td>
                               
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->newbenchclientrequest_quantity? number_format($package_active->newbenchclientrequest_quantity):'0'; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->newbenchclientrequest_used? number_format($package_active->newbenchclientrequest_used):'0'; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->newbenchclientrequest_balance? number_format($package_active->newbenchclientrequest_balance):'0'; ?>
								</td>
                               
							    <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->newbenchclientrequest_startdate? date('d M, Y', strtotime(date($package_active->newbenchclientrequest_startdate))):''; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->newbenchclientrequest_enddate? date('d M, Y', strtotime(date($package_active->newbenchclientrequest_enddate))):''; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php 
										echo    @$package_active->newbenchclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
									?>
								</td>
                              </tr> 							  							     
							  
							   	<tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      4.
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                          <span class="svg-icon svg-icon-warning  svg-icon-2x">
                                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <polygon points="0 0 24 0 24 24 0 24"/>
                                                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                  </g>
                                              </svg>
                                          </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Emails</a>
                                        </div>
                                          
                                    </div>
                                  </td>
                               
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->emails_quantity? number_format($package_active->emails_quantity):'0'; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->emails_used? number_format($package_active->emails_used):'0'; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->emails_balance? number_format($package_active->emails_balance):'0'; ?>
								</td>
                               
							    <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->emails_startdate? date('d M, Y', strtotime(date($package_active->emails_startdate))):''; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->emails_enddate? date('d M, Y', strtotime(date($package_active->emails_enddate))):''; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php 
										echo    @$package_active->emails_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
									?>
								</td>
                              </tr> 
							  
							  	<tr class="odd">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      5.
                                    </div>
                                </td>                        
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                            <span class="svg-icon svg-icon-danger svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Old CV Access</a>
                                        </div>
                                          
                                    </div>
                                  </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->oldcvaccess_quantity? number_format($package_active->oldcvaccess_quantity):'0'; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->oldcvaccess_used? number_format($package_active->oldcvaccess_used):'0'; ?>	
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->oldcvaccess_balance? number_format($package_active->oldcvaccess_balance):'0'; ?>	
								</td>
                               
							    <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->oldcvaccess_startdate? date('d M, Y', strtotime(date($package_active->oldcvaccess_startdate))):''; ?>
								</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php echo @$package_active->oldcvaccess_enddate? date('d M, Y', strtotime(date($package_active->oldcvaccess_enddate))):''; ?>
								</td>
								<td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">
									<?php 
										echo    @$package_active->oldcvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
									?>
								</td>
                              </tr> 
							  
                          </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                  </div>
				  
				  
				  
                  <!--begin::Logins -->
                  <div class="card shadow-none d-none">
                    <!--begin::Header-->
                    <div class="card-header px-7">
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Logins</h3>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                          <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z" fill="#000000" opacity="0.3"/>
                                  <path d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z" fill="#000000"/>
                              </g>
                            </svg>
                          </span>
                          See Pricing
                        </a>
                    </div>
                    <!--end::Header-->
                    <!--begin::Table-->
                    <div class="card-body">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="logins_list">
                          <thead>
                              <tr class="fw-bolder text-muted">
                                <th class="w-10px pe-2">#</th>
                                <th class="">Package Name</th>
                                <th class="">Number of users</th>
                                <th class="">Price</th>
                                <th class="">Select the duration</th>
                                <th class="">Number of CV limit</th>
                                <th class="">Actions</th>
                              </tr>
                          </thead>
                          <tbody class="text-gray-600 fw-bold">
                              <tr class="odd">
                                <td class="invisible">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input">
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                      <a href="javascript:void(0);" class="">
                                        <div class="symbol-label fs-3 bg-dark-light text-light border-radius-5">
                                          <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->
                                          <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                                    <path d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                          </span>
                                        </div>
                                      </a>
                                    </div>                  
                                    <div class="d-flex flex-column">
                                      <div class="">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                      </div>
                                        <span class="badge badge-light-success active_plan">Active Plan</span>
                                  </div>                          
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                <td class="text-dark fs-6 fw-bolder">Free</td>
                                <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                <td class="text-dark fs-6 fw-bolder"  style="padding-left: 4em!important;">30/ Month</td>
                                <td></td>
                              </tr>
                              <tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="logins_plan">
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                          <span class="svg-icon svg-icon-success svg-icon-2x">
                                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <polygon points="0 0 24 0 24 24 0 24"/>
                                                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                  </g>
                                              </svg>
                                          </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic service</a>
                                    </div>
                                  </td>
                                <td>
                                  <div class="position-relative w-md-130px" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="50000" data-kt-dialer-step="1" data-kt-dialer-prefix="" data-kt-dialer-decimals="">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0" data-kt-dialer-control="decrease">
                                          <span class="svg-icon svg-icon-1">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                  <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                                  <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                              </svg>
                                            </span>
                                    </button>
                                    <input type="number" class="form-control form-control-solid border-0 ps-12" style=" padding: 0.5rem;padding-left: 4em!important;" data-kt-dialer-control="input" placeholder="Enter users" name="users"  value="30">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0" data-kt-dialer-control="increase">
                                        <span class="svg-icon svg-icon-1">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                          </svg>
                                        </span>
                                    </button>
                                  </div>                          
                                </td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                    </div>
                                </td>
                                <td>
                                  <div class="btn-group hg-plan-btn-group">
                                      <button type="button" class="btn btn-secondary  hg-plan-btn-secondary dropdown-toggle block-level shared d-flex align-items-center justify-content-between" data-toggle="dropdown" aria-expanded="false">
                                          <div class="selected-term" data-term-length="3">
                                            <span class="term-length">3 Months</span>
                                          </div>
                                          <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                                      </button>
                                      <ul class="dropdown-menu block-level w-100" role="menu">
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="1">
                                                <span class="term-length">1 Month</span>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="3">
                                                <span class="term-length">3 Months</span>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="6">
                                                <span class="term-length">6 Months</span>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="12">
                                                <span class="term-length">1 Year</span>
                                                <div class="testsdfd" style="display:inline-block;">
                                                  <span class="hg-percentage-discount one-year-percentage">SAVE 20%</span>
                                                </div>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="24">
                                                <span class="term-length">2 Years</span>
                                                <div class="testsdfd" style="display:inline-block;">
                                                  <span class="hg-percentage-discount two-year-percentage">SAVE 30%</span>
                                                </div>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="36">
                                                <span class="term-length">3 Years</span>
                                                <div class="testsdfd" style="display:inline-block;">
                                                  <span class="hg-percentage-discount three-year-percentage">SAVE 30%</span>
                                                </div>
                                            </a>
                                          </li>
                                          <li>
                                            <a class="package-term d-flex align-items-center justify-content-between" data-term-length="60">
                                                <span class="term-length">5 Years</span>
                                                <div class="testsdfd" style="display:inline-block;">
                                                  <span class="hg-percentage-discount five-year-percentage">SAVE 50%</span>
                                                </div>
                                            </a>
                                          </li>
                                      </ul>
                                  </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder"  style="padding-left: 5em!important;">12</td>
                                <td></td>
                              </tr>
                              <tr class="odd">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="logins_plan">
                                    </div>
                                </td>                        
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium service</a>
                                    </div>
                                  </td>
                                <td>
                                  <div class="position-relative w-md-130px" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="50000" data-kt-dialer-step="1" data-kt-dialer-prefix="" data-kt-dialer-decimals="">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0" data-kt-dialer-control="decrease">
                                          <span class="svg-icon svg-icon-1">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                  <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                                  <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                              </svg>
                                            </span>
                                    </button>
                                    <input type="number" class="form-control form-control-solid border-0 ps-12" style=" padding: 0.5rem;padding-left: 4em!important;"" data-kt-dialer-control="input" placeholder="Enter users" name="users"  value="120">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0" data-kt-dialer-control="increase">
                                        <span class="svg-icon svg-icon-1">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                          </svg>
                                        </span>
                                    </button>
                                  </div>                          
                                </td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">6,999 INR</a>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">10 days left</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em!important;">30</td>                                
                                <td>
                                  <span class="text-dark fs-6 fw-bolder">Added</span>
                                </td>
                              </tr>
                              <tr class="even">
                                <td>
                                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="logins_plan">
                                  </div>
                                </td>               
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro service</a>
                                    </div>
                                  </td>
                                <td>
                                  <div class="position-relative w-md-130px" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="50000" data-kt-dialer-step="1" data-kt-dialer-prefix="" data-kt-dialer-decimals="">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0" data-kt-dialer-control="decrease">
                                          <span class="svg-icon svg-icon-1">
                                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                  <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                                  <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                              </svg>
                                            </span>
                                    </button>
                                    <input type="number" class="form-control form-control-solid border-0 ps-12" style=" padding: 0.5rem;padding-left: 4em!important;"" data-kt-dialer-control="input" placeholder="Enter users" name="users"  value="45">
                                    <button type="button" class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0" data-kt-dialer-control="increase">
                                        <span class="svg-icon svg-icon-1">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                          </svg>
                                        </span>
                                    </button>
                                  </div>                          
                                </td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">60 days left</td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em!important;">120</td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to cart</a>
                                </td>
                              </tr>
                          </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                  </div>
                  <!--end::Logins-->

                  <!--begin::Emails-->
                  <div class="card shadow-none mt-2 d-none">
                    <!--begin::Header-->
                    <div class="card-header px-7">
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Emails</h3>
                      </div>
                      <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                        <span class="svg-icon svg-icon-2">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http;//www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z" fill="#000000" opacity="0.3"/>
                                  <path d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z" fill="#000000"/>
                              </g>
                          </svg>
                        </span>
                        See Pricing
                      </a>
                    </div>
                    <!--end::Header-->
                    <!--begin::Table-->
                    <div class="card-body">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="email_list">
                          <thead>
                              <tr class="fw-bolder text-muted">
                                <th class="w-10px pe-2">#</th>
                                <th class="">Package Name</th>
                                <th class="">Number of emails</th>
                                <th class="">Price</th>
                                <th class="">Duration</th>
                                <th class="">Actions</th>
                              </tr>
                          </thead>
                          <tbody class="text-gray-600 fw-bold">
                              <tr class="odd">
                                <td class="invisible">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input">
                                    </div>
                                </td>
                                <td class="align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                      <a href="javascript:void(0);" class="">
                                        <div class="symbol-label fs-3 bg-dark-light text-light border-radius-5">
                                          <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->                                          
                                          <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                                    <path d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                          </span>
                                        </div>
                                      </a>
                                    </div>                  
                                    <div class="d-flex flex-column">
                                      <div class="">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                      </div>
                                        <span class="badge badge-light-success active_plan">Active Plan</span>
                                  </div>                          
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                <td class="text-dark fs-6 fw-bolder">Free</td>
                                <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                <td></td>
                              </tr>
                              <tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="email_plan">
                                    </div>
                                </td>
                                <td class="align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->                                            
                                            <span class="svg-icon svg-icon-success svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic service</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">5</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">
                                  <span style="padding-left:1.6em;">5</span>
                                </td>
                                <td>
                                  <span class="text-dark fs-6 fw-bolder">Added</span>
                                </td>
                              </tr>
                              <tr class="odd">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="email_plan">
                                    </div>
                                </td>                        
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium service</a>
                                    </div>
                                </td>                                  
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">56</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">6,999 INR</span>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">
                                  <span style="padding-left:1.6em;">6</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to cart</a>
                                </td>
                              </tr>
                              <tr class="even">
                                <td>
                                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="email_plan">
                                  </div>
                                </td>               
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro service</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">32</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">
                                  <span style="padding-left:1.6em;">2</span>
                                </td>                                
                                <td></td>
                              </tr>
                          </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                  </div>
                  <!--end::Emails-->

                  <!--begin::Resume access-->
                  <div class="card shadow-none mt-2 d-none">
                    <!--begin::Header-->
                    <div class="card-header px-7">
                      <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Resume access</h3>
                      </div>
                      <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                        <span class="svg-icon svg-icon-2">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http;//www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z" fill="#000000" opacity="0.3"/>
                                  <path d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z" fill="#000000"/>
                              </g>
                          </svg>
                        </span>
                        See Pricing
                      </a>
                    </div>
                    <!--end::Header-->
                    <!--begin::Table-->
                    <div class="card-body">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="resume_list">
                          <thead>
                              <tr class="fw-bolder text-muted">
                                <th class="w-10px pe-2">#</th>
                                <th class="">Package Name</th>
                                <th class="">Number of resume access</th>
                                <th class="">Price</th>
                                <th class="">Duration</th>
                                <th class="">Actions</th>
                              </tr>
                          </thead>
                          <tbody class="text-gray-600 fw-bold">
                              <tr class="odd">
                                <td class="invisible">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input">
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                      <a href="javascript:void(0);" class="">
                                        <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">
                                          <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->                                          
                                          <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                                    <path d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                          </span>
                                        </div>
                                      </a>
                                    </div>                  
                                    <div class="d-flex flex-column">
                                      <div class="">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                      </div>
                                        <span class="badge badge-light-success active_plan">Active Plan</span>
                                  </div>                          
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                <td class="text-dark fs-6 fw-bolder">Free</td>
                                <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                <td></td>
                              </tr>
                              <tr class="even">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="resume_plan">
                                    </div>
                                </td>
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                            <span class="svg-icon svg-icon-success svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic service</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">45</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                    </div>
                                </td>
                                <td>
                                  <span class="text-dark fs-6 fw-bolder" style="padding-left:.5em;">5</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to cart</a>
                                </td>
                              </tr>
                              <tr class="odd">
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="resume_plan">
                                    </div>
                                </td>                        
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium service</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">25</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">6,999 INR</a>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder">
                                  <span style="padding:left:.5em;">3</span>
                                </td>
                                <td></td>
                              </tr>
                              <tr class="even">
                                <td>
                                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                                      <input class="form-check-input" type="radio" value="1" name="resume_plan">
                                  </div>
                                </td>               
                                <td class=" align-items-center">
                                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                        <a href="javascript:void(0);" data-row-id="806" class="show-application-detail">
                                          <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                          </div>
                                        </a>
                                      </div>                  
                                      <div class="d-flex flex-column">
                                        <div class="">
                                          <a href="javascript:void(0);" data-row-id="806" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                        </div>
                                          <a href="javascript:void(0);" data-row-id="806" class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro service</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">20</td>
                                <td class="">
                                  <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                      <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                    </div>
                                </td>
                                <td class="text-dark fs-6 fw-bolder" style="padding-left: .5em!important;">2</td>
                                <td>
                                  <span class="text-dark fs-6 fw-bolder">Added</span>
                                </td>
                              </tr>
                          </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                  </div>
                  <!--end::Resume access-->

                  <!--begin::Bench activation-->
                  <!--end::Bench activation-->
              </div>
              <!--end::card body-->
          </div>
          <!--end::card-->
      </div>
  </div>
</div>
<!--end::Content-->

@endsection

@push('footer-script')
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html) {
    var switchery = new Switchery(html, { size: 'medium' });
    });

    $('#package-switch').change(function () {
        let checked = $(this).is(":checked");
        if(checked) {
            $('.monthly-packages').hide();
            $('.annual-packages').fadeIn('slow');
        }
        else {
            $('.annual-packages').hide();
            $('.monthly-packages').fadeIn('slow');
        }
    })

    $('body').on('click', '.unsubscribe', function(){
        var type = $(this).data('method');
        swal({
					title				: "@lang('errors.areYouSure')",
					type				: "warning",
					showCancelButton	: true,
					confirmButtonColor	: "#DD6B55",
					confirmButtonText	: "@lang('app.yes')",
					cancelButtonText	: "@lang('app.cancel')",
					closeOnConfirm		: true,
					closeOnCancel		: true
        }, function(isConfirm){
            if (isConfirm) {
                var url 	= "{{ route('product_company.subscribe.cancel-subscription',':type') }}";
                url 		= url.replace(':type', type);
                $.easyAjax({
								type		: 'GET',
								url			: url,
								redirect	: true,
								success		: function (response) {
									console.log(response);
									if (response.status == "success") {
										$.unblockUI();
										window.location.reload();
									}
								}
                });
            }
        });
    });

    // Show Create Holiday Modal
    $('body').on('click', '.selectPackage', function(){
        var id 		= $(this).data('row-id');
        var type 	= $(this).data('row-type');
        var url 	= "{{ route('product_company.subscribe.select-package',':id') }}?type="+type;
        url 		= url.replace(':id', id);
        $.ajaxModal('#package-select-form', url);
    });
</script>
@endpush
