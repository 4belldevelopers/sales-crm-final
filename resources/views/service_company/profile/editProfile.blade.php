@extends('layouts.service_company_layout')
@push('head-script')
<!--Add New Css-->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!--Add New Css End-->
<style>
body
{
    overflow-x:hidden;
}
</style>
@endpush
 
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Add Requisition-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1">Company Profile</a></h1>
      </div>
      <!--end::Add Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Edit company details and save</span> </div>
    <!--end::Page title-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-0" id="kt_content">
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-xxl px-2">
      <!--begin::Card-->
      <div class="card card-custom gutter-b">
        <!--begin::Body-->
        <div class="card-body p-0">
          <!--begin::Wizard-->
          <div class="wizard wizard-1" id="kt_contact_add" data-wizard-state="step-first" data-wizard-clickable="true">
            <!--begin::Wizard Nav-->
            <div class="wizard-nav border-bottom">
              <div class="wizard-steps p-8 p-lg-10">
                <!--begin::Company Information Nav-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <!--begin::Svg Icon-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>
                    <h3 class="wizard-title">1. Company Information</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Company Information Nav-->
                <!--begin::Comapny Description Nav-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <!--begin::Svg Icon | path:assets/media/svg/icons/Devices/Display1.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3" />
                        <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000" />
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>
                    <h3 class="wizard-title">2. Company Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Company Description Nav-->
                <!--begin::Address Details Nav-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Globe.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero" />
                        <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6" />
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>
                    <h3 class="wizard-title">3. Address Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Address Details Nav-->
                <!--begin::Review and Submit Nav-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000" />
                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                      </g>
                    </svg>
                    <!--end::Svg Icon-->
                    </span>
                    <h3 class="wizard-title">4. Review and Submit</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow last">
                  <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Review and Submit Nav-->
              </div>
            </div>
            <!--end::Wizard Nav-->
            <!--begin::Wizard Body-->
            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
                <!--begin::Form Wizard Form-->
                <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_contact_add_form">
                  <!--begin::Client Information-->
                  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                    <h3 class="mb-10 font-weight-bold text-dark">Enter the Client Information</h3>
                    <div class="row">
                      <div class="col-xl-12">
                        <!--begin::Image-->
                        <div class="form-group row">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Change Image:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="imageinput image-input image-input-outline" data-kt-image-input="true">
                              <div class="image-input-wrapper w-90px h-90px" style="background-image: url(https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png)"></div>
                              <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-toggle="tooltip" data-placement="top" data-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                              <input type="file" accept="image/*" onchange="loadFile(event)">
                              <input type="hidden" name="avatar_remove">
                              </label>
                              <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-toggle="tooltip" data-placement="top" data-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span> <span class="removeimg btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-toggle="tooltip" data-placement="top" data-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span> </div>
                            <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span> </div>
                        </div>
                        <!--end::Image-->
                        <!--begin::Company-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Company:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-building"></i> </span> </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company" value="" placeholder="Enter company">
                            </div>
                            <span class="form-text text-muted">Please enter your company</span> </div>
                        </div>
                        <!--end::Company-->
                        <!--begin::Website-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Website:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-globe"></i> </span> </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="website" value="" placeholder="Enter website">
                            </div>
                            <span class="form-text text-muted">Please enter your website</span> </div>
                        </div>
                        <!--end::Website-->
                        <!--begin::Industry-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Industry:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-bar-chart"></i> </span> </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="industry" value="" placeholder="Enter industry">
                            </div>
                            <span class="form-text text-muted">Please enter your industry</span> </div>
                        </div>
                        <!--end::Industry-->
                        <!--begin::Company Sizes-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Company Sizes:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-calculator"></i> </span> </div>
                              <input type="number" class="form-control form-control-lg form-control-solid" name="company_sizes" value="" placeholder="Enter company sizes">
                            </div>
                            <span class="form-text text-muted">Please enter your company sizes</span> </div>
                        </div>
                        <!--end::Company Sizes-->
                        <!--begin::Type-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Type:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-codepen"></i> </span> </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="type" value="" placeholder="Enter type">
                            </div>
                            <span class="form-text text-muted">Please enter your type</span> </div>
                        </div>
                        <!--end::Type-->
                        <!--begin::Founded-->
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Founded:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-delicious"></i> </span> </div>
                              <input type="number" class="form-control form-control-lg form-control-solid" name="founded" value="" placeholder="Enter founded year">
                            </div>
                            <span class="form-text text-muted">Please enter your founded year</span> </div>
                        </div>
                        <!--end::Founded-->
                      </div>
                    </div>
                  </div>
                  <!--end::Client Information-->
                  <!--begin::Client Description-->
                  <div class="pb-5" data-wizard-type="step-content">
                    <div class="row">
                      <div class="col-xl-12">
                        <div class="form-group row">
                          <div class="col-lg-9 col-xl-6">
                            <h3 class="font-weight-bold text-dark">Enter Client Description</h3>
                          </div>
                        </div>
                        <!--begin::Client Description-->
                        <div class="form-group row fv-row mb-10">
                          <div class="col-lg-12">
                            <label class="form-label required fw-bolder">Description:</label>
                            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">
															<h4 class="" style="margin: 10px 0px;">ABOUT 4BELL TECHNOLOGY</h4>
                                                            <p class="text-justify">4Bell Technology is a next-generation global technology company that helps enterprises reimagine their businesses for the digital age.
                                                                Our technology products and services are built on four decades of innovation, with a world-renowned management philosophy, a strong culture of invention
                                                                and risk-taking, and a relentless focus on customer relationships. 4Bell also takes pride in its many diversity, social responsibility, sustainability,
                                                                and education initiatives. Through its worldwide network of R&amp;D facilities and co-innovation labs, global delivery capabilities, and over 176,000+
                                                                &lsquo;Ideapreneurs&rsquo; across 50 countries, 4Bell delivers holistic services across industry verticals to leading enterprises, including 250 of the
                                                                Fortune 500 and 650 of the Global 2000.</p>
                                                            <p class="text-justify">Enterprises across industries stand at an inflection point today. In order to thrive in the digital age, technologies such as analytics,
                                                                cloud, IoT, and automation occupy center stage. In order to offer enterprises the maximum benefit of these technologies to further their business objectives,
                                                                4Bell offers an integrated portfolio of products and services through three business units. These are IT and Business Services (ITBS), Engineering and R&amp;D
                                                                Services (ERS), and Products and Platforms (P&amp;P).</p>
                                                            <h5 class="" style="margin: 10px 0px;">Q1 FY'22 RESULTS</h5>
                                                            <ul>
                                                                <li>US$ 10.54 BN REVENUE</li>
                                                                <li>OVER 157 NATIONALITIES</li>
                                                                <li>OPERATING IN 50 COUNTRIES</li>
                                                                <li>176,000+ IDEAPRENEURS</li>
                                                                <li>Revenue at US $ 2,720 mn, up 0.9% QoQ &amp; up 15.5% YoY</li>
                                                                <li>Revenue in Constant Currency; up 0.7% QoQ &amp; up 11.7% YoY</li>
                                                                <li>EBITDA margin at 24.5%; EBIT margin at 19.6%</li>
                                                                <li>Net Income at US $ 436 mn (Net Income margin at 16.0%) up 6.4% QoQ &amp; up 12.8% YoY</li>
                                                                <li>Revenue ? 20,068 crores, up 2.2% QoQ &amp; up 12.5% YoY</li>
                                                                <li>Net Income at ? 3,214 crores, up 8.5% QoQ &amp; up 9.9% YoY</li>
                                                            </ul>
														</textarea>
                          </div>
                        </div>
                        <!--end::Client Description-->
                        <!--begin::Tags-->
                        <div class="col-lg-12 mt-7">
                          <label class="form-label required fw-bolder">Tags:</label>
                          <input class="form-control form-control-solid" name="tags" placeholder="Enter tags" value="IT Recruitment, Consultancy, IT Services, Product Based" id=""/>
                          <span class="form-text text-muted">Please select your tags</span> </div>
                        <!--end::Tags-->
                      </div>
                    </div>
                  </div>
                  <!--end::Client Description-->
                  <!--begin::Address Details-->
                  <div class="pb-5" data-wizard-type="step-content">
                    <h3 class="mb-10 font-weight-bold text-dark">Enter the Address Details</h3>
                    <div class="address-section">
                      <div class="form-group">
                        <label class="form-label required fw-bolder">Address:</label>
                        <input type="text" class="form-control form-control-lg form-control-solid" name="address" placeholder="Enter address" value="F-314, 1st Floor, Sector-63">
                        <span class="form-text text-muted">Please enter your Address</span> </div>
                      <div class="row">
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">Country:</label>
                            <select name="country" class="form-control form-control-lg form-control-solid">
                              <option value="">Select</option>
                              <option value="AU" selected="selected">India</option>
                            </select>
                            <span class="form-text text-muted">Please select your country</span>
                            <div class="fv-plugins-message-container"></div>
                          </div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">State:</label>
                            <select name="state" class="form-control form-control-lg form-control-solid">
                              <option value="">Select</option>
                              <option value="UP" selected="selected">Uttar Pradesh</option>
                              <option value="PB">Panjab</option>
                              <option value="HY">Haryana</option>
                            </select>
                            <span class="form-text text-muted">Please select your state</span>
                            <div class="fv-plugins-message-container"></div>
                          </div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">City</label>
                            <input type="text" class="form-control form-control-lg form-control-solid" name="city" placeholder="Enter city" value="Noida">
                            <span class="form-text text-muted">Please enter your city:</span>
                            <div class="fv-plugins-message-container"></div>
                          </div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">Postcode:</label>
                            <input type="text" class="form-control form-control-lg form-control-solid" name="postcode" placeholder="Enter postcode" value="201301">
                            <span class="form-text text-muted">Please enter your postcode</span>
                            <div class="fv-plugins-message-container"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--begin::Add More Button-->
                    <div class="d-flex justify-content-center py-5">
                      <div class="">
                        <button type="button" class="btn btn-light-dark font-weight-bolder text-uppercase">
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
                        Add more </button>
                      </div>
                    </div>
                    <!--begin::Add More Button-->
                  </div>
                  <!--end::Address Details-->
                  <!--begin::Review and Submit-->
                  <div class="pb-15" data-wizard-type="step-content">
                    <h4 class="mb-10 font-weight-bold">Review your Details and Submit</h4>
                    <h6 class="font-weight-bold mb-3">Company Information:</h6>
                    <!--begin::Company Information content-->
                    <div class="row pt-7">
                      <!--begin::Left Section-->
                      <div class="col-lg-6">
                        <!--begin::Company-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6">4Bell Technology</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Company-->
                        <!--begin::Website-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Website</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6">www.4bell.com</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Website-->
                        <!--begin::Industry-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Industry</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6">IT Services</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Industry-->
                      </div>
                      <!--end::Left Section-->
                      <!--begin::Right Section-->
                      <div class="col-lg-6">
                        <!--begin::Company Sizes-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company Sizes</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6">240</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Company Sizes-->
                        <!--begin::Type-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Type</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6">Private</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Type-->
                        <!--begin::Founded-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-4 fw-bold text-muted fs-6">Founded</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6">2012</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Founded-->
                      </div>
                      <!--end::Right Section-->
                    </div>
                    <!--end::Company Information content-->
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <!--begin::Company Description content-->
                    <h6 class="font-weight-bold mb-3">Company Description:</h6>
                    <div class="row pt-7 text-justify">
                      <!--Company Description-->
                      <h4 class="fw-bolder my-0 text-dark fs-5" style="">ABOUT 4BELL TECHNOLOGY</h4>
                      <p class="fw-bold my-3 text-dark-800 fs-6">4Bell Technology is a next-generation global technology company that helps 
                        enterprises reimagine their businesses for the digital age. Our technology products and services are built on four decades 
                        of innovation, with a world-renowned management philosophy, a strong culture of invention and risk-taking, and a relentless 
                        focus on customer relationships. 4Bell also takes pride in its many diversity, social responsibility, sustainability, and 
                        education initiatives. Through its worldwide network of R&amp;D facilities and co-innovation labs, global delivery capabilities,
                        and over 176,000+ &lsquo;Ideapreneurs&rsquo; across 50 countries, 4Bell delivers holistic services across industry verticals to
                        leading enterprises, including 250 of the Fortune 500 and 650 of the Global 2000.</p>
                      <p class="fw-bold my-3 text-dark-800 fs-6">Enterprises across industries stand at an inflection point today. In order to thrive in the digital age,
                        technologies such as analytics, cloud, IoT, and automation occupy center stage. In order to offer enterprises the maximum
                        benefit of these technologies to further their business objectives, 4Bell offers an integrated portfolio of products and
                        services through three business units. These are IT and Business Services (ITBS), Engineering and R&amp;D Services (ERS),
                        and Products and Platforms (P&amp;P).</p>
                      <ul class="fw-bold text-dark-800 fs-6 px-10">
                        <li>Identify and analyze user requirements.</li>
                        <li>Integrate user-facing elements into applications.</li>
                        <li>Test and debug programs.</li>
                        <li>Write well-designed, efficient code.</li>
                        <li>Improve functionality of existing systems</li>
                        <li>Plan and execute all deployment of system features and monitor for successful integration, maintaining the system throughout its lifecycle.</li>
                        <li>Coordinate with internal teams to understand user requirements and provide technical solutions.</li>
                        <li>Very strong in delivering high quality code and following continuous delivery.</li>
                        <li>Good delegation and time management skills.</li>
                        <li>Familiarity with front-end technologies (like JavaScript and HTML5).</li>
                        <li>Should have problem-solving abilities.</li>
                        <li>Should have excellent verbal and written communication skills.</li>
                      </ul>
                    </div>
                    <!--end::Company Description content-->
                    <!--begin::Tags content-->
                    <div class="row pt-7">
                      <!--begin::Tags Section-->
                      <div class="col-lg-12">
                        <!--begin::Tags-->
                        <div class="row mb-7">
                          <!--begin::Title-->
                          <label class="col-lg-2 fw-bold text-muted fs-6">Tags</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6">IT Services, Development, Designing, Product Based, Sales & Marketing</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Tags-->
                      </div>
                      <!--end::Tags Section-->
                    </div>
                    <!--end::Tags content-->
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <!--begin::Address Details content-->
                    <h6 class="font-weight-bold mb-3">Address Details:</h6>
                    <div class="row pt-7">
                      <!--begin::Address title Section-->
                      <div class="col-lg-12">
                        <!--begin::Address title-->
                        <div class="row mb-4">
                          <!--begin::Title-->
                          <label class="col-lg-2 fw-bold text-muted fs-6">Address</label>
                          <!--end::Title-->
                          <!--begin::Description-->
                          <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6">F-314, 2nd Floor, Sector-63</span> </div>
                          <!--end::Description-->
                        </div>
                        <!--end::Address title-->
                        <div class="row">
                          <div class="col-xl-3">
                            <div class="form-group fv-plugins-icon-container">
                              <label class="fw-bold text-muted fs-6 w-100">Country</label>
                              <span class="fw-bold text-gray-800 fs-6 w-100">INDIA</span> </div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group fv-plugins-icon-container">
                              <label class="fw-bold text-muted fs-6 w-100">State</label>
                              <span class="fw-bold text-gray-800 fs-6 w-100">Uttar Pradesh</span> </div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group fv-plugins-icon-container">
                              <label class="fw-bold text-muted fs-6 w-100">City</label>
                              <span class="fw-bold text-gray-800 fs-6 w-100">Noida</span> </div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group fv-plugins-icon-container">
                              <label class="fw-bold text-muted fs-6 w-100">Pincode</label>
                              <span class="fw-bold text-gray-800 fs-6 w-100">201301</span> </div>
                          </div>
                        </div>
                      </div>
                      <!--end::Address title Section-->
                    </div>
                    <!--end::Address Details content-->
                  </div>
                  <!--end::Review and Submit-->
                  <!--begin::Back | Continue | Submit Buttons-->
                  <div class="d-flex justify-content-center border-top pt-10">
                    <div class="mr-2">
                      <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Back</button>
                    </div>
                    <div>
                      <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-submit">Save</button>
                      <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Continue</button>
                    </div>
                  </div>
                  <!--end::Back | Continue | Submit Buttons-->
                </form>
                <!--end::Form Wizard Form-->
              </div>
            </div>
            <!--end::Wizard Body-->
          </div>
          <!--end::Wizard-->
        </div>
        <!--end::Body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<!--begin::Tagify script-->
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script>
var input = document.querySelector('input[name=tags]');
new Tagify(input)

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>
<!--end::Tagify script-->
<!--begin::Change image-->
<script>
  var loadFile = function(event)
  {
    var output = URL.createObjectURL(event.target.files[0]);
    $('.image-input-wrapper').css('background-image', 'url(' + output + ')');
  };
$( document ).ready(function()
{
    var room = 1;
    $( ".removeimg" ).click(function()
    {
        var defaultUrl 		=	'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
        $('.image-input-wrapper').css('background-image', 'url(' + defaultUrl + ')');
    });
});
</script>
<!--end::Change image-->
<!--begin::Tinymce Editor-->
<script>
"use strict";
var KTFormsTinyMCEHidden = {
    init: function () {
        tinymce.init({
            selector: "#kt_docs_tinymce_hidden",
            menubar: !1,
            toolbar: [
                "styleselect | fontselect | fontsizeselect  | undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code",
            ],
            plugins: "advlist autolink link image lists charmap print preview code",
        });
    },
};
KTUtil.onDOMContentLoaded(function () {
    KTFormsTinyMCEHidden.init();
});
</script>
<!--end::Tinymce Editor-->
<!--Tinymce Editor-->
<script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script>
<!--Tinymce Editor End-->
<!--begin::Page Scripts-->
<!-- <script src="{{ asset('demo/v7/js/prismjs.bundle.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/add-contact.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::Page Scripts-->
@endpush