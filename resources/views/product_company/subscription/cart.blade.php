@extends('layouts.product_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('demo/css/font-awesome.css') }}">

<style>
.ml-1
{
    margin-left:1em;
}
.box-shadow
{
    box-shadow: 2px 1px 7px 3px whitesmoke;
}
.basic_left_custom
{
    border-left: 3px solid #519ff7!important;
    padding-left: 7px!important;
}
.premium_left_custom
{
    border-left: 3px solid #ffc700!important;
    padding-left: 7px!important;
}
.pro_left_custom
{
    border-left: 3px solid #7239ea!important;
    padding-left: 7px!important;
}
.table
{
    border-collapse: separate;
    border-spacing:0 20px;
}
.w-60
{
    width: 60%;
}
.w-40
{
    width: 40%;
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
               <span class="text-gray-800 mb-1">Cart</span>
            </h5>
            <!-- <span class="text-gray-600" id="">20</span> -->
         </div>
         <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="d-flex align-items-center py-1">
          <!--begin::Button-->
          <!--begin::Button-->
      </div>
   </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-5" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container px-3">
            <div class="d-flex flex-row">
                <!--begin::Left panel-->
                <div class="flex-row-fluid ml-lg-8">
                    <!--begin::My cart-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder text-dark">My Cart</span>
                                <span class="text-muted mt-2 fs-6" id="">3 items</span>
                            </h3>
                            <!-- <div class="d-flex flex-column">
                                <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                                <span class="text-gray-800 mb-1">Cart</span>
                                </h5>
                                <span class="text-gray-600" id="">20</span>
                            </div> -->
                            <div class="card-toolbar">
                                <div class="dropdown dropdown-inline">
                                    <a href="{{ route('product_company.subscribe.index')  }}" class="btn btn-primary btn-sm fw-bolder">
                                        <i class="fa fa-chevron-left"></i>&nbsp;Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle fs-6 gy-5" id="cart_data">
                                    <!--begin::Cart Header-->
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="">Package Name</th>
                                            <th class="">Quantity</th>
                                            <th class="">Price | Duration</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <!--end::Cart Header-->
                                    <tbody class="">
                                        <!--begin::Logins-->
                                        <tr class="even box-shadow">
                                            <td class="align-items-center basic_left_custom">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                    <a href="javascript:void(0);" class="">
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
                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Logins</a>
                                                    </div>
                                                    <span class="badge badge-light-primary px-2" style="width:fit-content;">Basic Plan</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">10</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Users</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">For 3 months</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="fs-6 fw-bolder">
                                                    <i class="fa fa-trash-alt text-danger fa-1x"></i>
                                                </a>
                                            </td>
                                        </tr>                                        
                                        <!--end::Logins-->

                                        <!--begin::Emails-->
                                        <tr class="even box-shadow">
                                            <td class="align-items-center premium_left_custom">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                    <a href="javascript:void(0);" class="">
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
                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Emails</a>
                                                    </div>
                                                    <span class="badge badge-light-info px-2" style="width:fit-content;">Premium Plan</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">2000</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Emails</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">6,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">For 6 months</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="fs-6 fw-bolder">
                                                    <i class="fa fa-trash-alt text-danger fa-1x"></i>
                                                </a>
                                            </td>
                                        </tr>                                        
                                        <!--end::Emails-->

                                        <!--begin::Resume access-->
                                        <tr class="even box-shadow">
                                            <td class="align-items-center pro_left_custom">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                    <a href="javascript:void(0);" class="">
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
                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Resume access</a>
                                                    </div>
                                                    <span class="badge badge-light-warning px-2" style="width:fit-content;">Pro Plan</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">2000</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Resume access</span>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">7,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">For 9 months</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="fs-6 fw-bolder">
                                                    <i class="fa fa-trash-alt text-danger fa-1x"></i>
                                                </a>
                                            </td>
                                        </tr>                                        
                                        <!--end::Resume access-->

                                        <!--begin::Apply for voucher-->
                                        <tr class="">
                                            <td colspan="2" class="border-0 pt-10">
                                                <form>
                                                    <div class="form-group row position-absolute end-0">
                                                        <div class="col-md-4 d-flex align-items-center">
                                                            <label class="text-dark fw-600">Apply Voucher</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="input-group w-100">
                                                                <input type="text" class="form-control" placeholder="Voucher Code" />
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-secondary" type="button">Apply</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                        <!--end::Apply for voucher-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::My cart-->
                    <!--begin::Billing information-->
                    <div class="card card-custom mt-4">
                        <div class="card-header align-items-center">                            
                            <h3 class="card-title">
                                <span class="card-label fw-bolder text-dark">Billing Information</span>
                            </h3>
                            <a href="javascript:void(0);">
                                <i class="fa fa-check-circle fs-3 text-success"></i>
                            </a>
                        </div>
                        <div class="card-body">                            
                            <div class="d-flex justify-content-between align-items-start mb-7" style="padding-right:0em;">
                                <p class="text-dark-800 fs-5">4Bell Technology F-314, 1st floor, sector-63<br>
                                    Noida, Uttar Pradesh 201301, <span class="text-uppercase">india</span><br>
                                    22AABCU9603R1ZX
                                </p>
                                <a href="javascript:void(0);" class="btn btn-light-primary btn-sm font-weight-bolder">
                                    <i class="fa fa-pencil-alt"></i>&nbsp;Edit
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Billing information-->
                </div>
                <!--end::Left panel-->
                <!--begin::Right panel-->
                <div class="flex-column offcanvas-mobile w-300px w-xl-325px ml-1" id="kt_profile_aside">
                    <div class="card card-custom gutter-b">
                        <div class="card-header align-items-center px-4">
                            <h3 class="card-title">
                                <span class="card-label fw-bolder text-dark">Order Summary</span>
                            </h3>
                            <!-- <p class="text-dark-800 fw-bold fs-6 mb-0">3 items</p> -->
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="">
                                <ol class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-start border-bottom-dashed border-top-0 border-left-0 border-right-0 border-1 py-5">
                                        <div class="me-auto w-60">
                                            <p class="fs-5">Subtotal :</p>
                                            <p class="fs-5 text-danger mb-0">Coupon Discount :</p>
                                        </div>
                                        <div class="me-auto w-40">
                                            <p class="fs-5 fw-bolder">
                                                <i class="fa fa-inr text-dark"></i> 12,320
                                            </p>
                                            <p class="fs-5 fw-bolder text-danger mb-0">
                                                <i class="fa fa-inr text-danger"></i> -5,837.00
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start border-bottom-dashed border-top-0 border-left-0 border-right-0 border-1 py-5">
                                        <div class="me-auto w-60">
                                            <p class="fs-5">Net Amount :</p>
                                            <p class="fs-5 mb-0">Tax 
                                                <i class="fa fa-info-circle text-dark" data-toggle="tooltip" title="Tax description"></i> :</p>
                                        </div>
                                        <div class="me-auto w-40">
                                            <p class="fs-5 fw-bolder">
                                                <i class="fa fa-inr text-dark"></i> 7,483
                                            </p>
                                            <p class="fs-5 fw-bolder mb-0">
                                                <i class="fa fa-inr text-dark"></i> 5,343.22
                                            </p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 py-5">
                                        <div class="me-auto w-60">
                                            <p class="fs-5 mb-0">Subtotal :</p>
                                        </div>
                                        <div class="me-auto w-40">
                                            <p class="fs-5 fw-bolder text-success mb-0">
                                                <i class="fa fa-inr text-success"></i> -5,837.00
                                            </p>
                                        </div>
                                    </li>      
                                    <li class="list-group-item border-0">
                                        <div class="me-auto w-100 alert alert-warning d-flex align-items-center justify-content-around mb-0">
                                            <i class="fa fa-warning text-warning"></i>
                                            <p class="fs-5 fw-bold text-dark mb-0">
                                                No Cost EMI Options Available<br>
                                                <a href="javascript:void(0);" class="text-dark text-decoration-underline text-decoration-hover-underline">Learn more</a>
                                            </p>
                                        </div>
                                    </li>           
                                    <li class="list-group-item border-0">
                                        <div class="">
                                            <a href="javascript:void(0);" type="button" class="btn btn-success d-block fs-3">Continue</a>
                                        </div>
                                    </li>                       
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->

<!--Billing Information - Popup-->
<div class="modal fade show" id="add_billing" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="add_billing_header">
                <h2 class="fw-bolder">Add User</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="add_billing_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="add_billing_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#add_billing_header" data-kt-scroll-wrappers="#add_billing_scroll" data-kt-scroll-offset="300px" style="max-height: 400px;">
                       <div class="fv-row mb-7">
                            <label class="d-block fw-bold fs-6 mb-5">Avatar</label>
                            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('/metronic8/demo1/assets/media/svg/avatars/blank.svg')">
                                <div class="image-input-wrapper w-125px h-125px" style="background-image: url(/metronic8/demo1/assets/media/avatars/300-6.jpg);"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Change avatar">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="avatar_remove">
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="" data-bs-original-title="Cancel avatar">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                        </div>
                        <div class="fv-row mb-7 fv-plugins-icon-container">
                            <label class="required fw-bold fs-6 mb-2">Full Name</label>
                            <input type="text" name="user_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="Emma Smith">
                        <div class="fv-plugins-message-container invalid-feedback"></div></div>
                        <div class="fv-row mb-7 fv-plugins-icon-container">
                            <label class="required fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="e.smith@kpmg.com.au">
                        <div class="fv-plugins-message-container invalid-feedback"></div></div>
                        <div class="mb-7">
                            <label class="required fw-bold fs-6 mb-5">Role</label>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="user_role" type="radio" value="0" id="kt_modal_update_role_option_0" checked="checked">
                                    <label class="form-check-label" for="kt_modal_update_role_option_0">
                                        <div class="fw-bolder text-gray-800">Administrator</div>
                                        <div class="text-gray-600">Best for business owners and company administrators</div>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="user_role" type="radio" value="1" id="kt_modal_update_role_option_1">
                                    <label class="form-check-label" for="kt_modal_update_role_option_1">
                                        <div class="fw-bolder text-gray-800">Developer</div>
                                        <div class="text-gray-600">Best for developers or people primarily using the API</div>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="user_role" type="radio" value="2" id="kt_modal_update_role_option_2">
                                    <label class="form-check-label" for="kt_modal_update_role_option_2">
                                        <div class="fw-bolder text-gray-800">Analyst</div>
                                        <div class="text-gray-600">Best for people who need full access to analytics data, but don't need to update business settings</div>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="user_role" type="radio" value="3" id="kt_modal_update_role_option_3">
                                    <label class="form-check-label" for="kt_modal_update_role_option_3">
                                        <div class="fw-bolder text-gray-800">Support</div>
                                        <div class="text-gray-600">Best for employees who regularly refund payments and respond to disputes</div>
                                    </label>
                                </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="user_role" type="radio" value="4" id="kt_modal_update_role_option_4">
                                    <label class="form-check-label" for="kt_modal_update_role_option_4">
                                        <div class="fw-bolder text-gray-800">Trial</div>
                                        <div class="text-gray-600">Best for people who need to preview content data, but don't need to make any updates</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <div></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Billing Information - Popup-->

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

    // redirect on paypal payment page
    $('body').on('click', '.paypalPayment', function(){
        var url = "{{ route('product_company.paypal',[':id',':type']) }}";
        var id = $(this).data('row-id');
        var type = $(this).data('row-type');
        url = url.replace(':id', id);
        url = url.replace(':type', type);
        $.easyBlockUI('#package-select-form', 'Redirecting Please Wait...');
        window.location.href = url;
    });

    $('body').on('click', '.unsubscribe', function(){
        var type = $(this).data('method');
        swal({
            title: "@lang('errors.areYouSure')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('app.yes')",
            cancelButtonText: "@lang('app.cancel')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                var url = "{{ route('product_company.subscribe.cancel-subscription',':type') }}";
                url = url.replace(':type', type);
                $.easyAjax({
                    type: 'GET',
                    url: url,
                    redirect: true,
                    success: function (response) {
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
        var id = $(this).data('row-id');
        var type = $(this).data('row-type');
        var url = "{{ route('product_company.subscribe.select-package',':id') }}?type="+type;
        url = url.replace(':id', id);
        $.ajaxModal('#package-select-form', url);
    });
</script>
@endpush