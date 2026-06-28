@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">

<!--begin::Add New Css of Metronic-7-->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!--end::Add New Css of Metronic-7-->

<style>
    body
    {
        overflow-x: hidden;
    }
</style>

@endpush
@section('content')


<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
            <a href="javascript:void(0);" class="text-gray-800 mb-1">Edit Company</a>
        </h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Update company details and submit</span> </div>
  </div>
</div>
<!--end::Toolbar-->

<!--begin::content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-5" id="kt_content">
  <div class="d-flex flex-column-fluid">
    <div class="container-fluid px-3">
      <div class="card card-custom">
        <div class="card-body p-0">
          <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="first" data-wizard-clickable="false">
            <div class="wizard-nav border-bottom">
              <div class="wizard-steps p-8 p-lg-10">
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "></path>
                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">1. Company Information</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  </span>
                </div>
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3"></path>
                        <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000"></path>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">2. Company Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  </span>
                </div>
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero"></path>
                        <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6"></circle>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">3. Address Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  </span>
                </div>
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">4. Contact Person</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  </span>
                </div>
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000"></path>
                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5"></circle>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">5. Review and Submit</h3>
                  </div>
                </div>
              </div>
            </div>
            <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
                <form class="form mx-auto fv-plugins-bootstrap fv-plugins-framework" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_form" name="kt_form">
                  <meta name="csrf-token" content="SmvTYC0DhzXrSIPZGqQKa0cyQsQOldhR9dcGfRKC">
                  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                    <h3 class="mb-10 font-weight-bold text-dark">Enter the Company Information</h3>
                    <div class="row">
                      <div class="col-xl-12">
                        <div class="form-group row">
                          <label class="col-xl-3 col-lg-3 col-form-label fw-bolder">Change Image:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="image-input image-input-outline" id="logo">
                              <div class="image-input-wrapper w-90px h-90px" style="background-image: url(https://bot.4bell.com/img/client-default.png)"></div>
                              <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-bs-original-title="Change logo"> <i class="fa fa-pen icon-sm text-muted"></i>
                              <input type="file" name="logo" id="logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event)">
                              <input type="hidden" name="avatar_remove">
                              </label>
                              <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-danger btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-bs-original-title="Remove logo"> <i class="fa fa-times icon-sm text-muted"></i> </span> </div>
                            <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span> </div>
                        </div>
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder" for="company_name">Company:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                  <i class="la la-building"></i> </span>
                                </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company_name" id="company_name" value="" placeholder="Enter company">
                            </div>
                            <span class="form-text text-muted">Please enter your company name</span> <div class="fv-plugins-message-container"></div></div>
                        </div>
                        @if(module_enabled('Subdomain'))
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="subdomain" name="sub_domain" id="sub_domain">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">.{{ get_domain() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder" for="company_website">Website:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="la la-globe"></i></span>
                                </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company_website" id="website" value="" placeholder="Enter website">
                            </div>
                            <span class="form-text text-muted">Please enter your Website</span> <div class="fv-plugins-message-container"></div></div>
                        </div>

                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder" for="company_email">Email:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="la la-archive"></i></span>
                                </div>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company_email" id="company_email" value="" placeholder="Enter email">
                            </div>
                            <span class="form-text text-muted">Please enter your Email</span> <div class="fv-plugins-message-container"></div></div>
                        </div>

                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Industry:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-bar-chart"></i> </span> </div>
                              <select class="form-select form-select-lg form-select-solid" name="industry" id="industry">
                                <option value="">Select Industry</option>
                                <option value="0" selected="selected">Accounts &amp; Finance</option>
                                <option value="1">BPO / Telecaller</option>
                                <option value="2">Design / Creativity</option>
                                <option value="3">Engineer</option>
                                <option value="4">Events / Media</option>
                                <option value="5">Healthcare</option>
                                <option value="6">IT / Software</option>
                                <option value="7">Logistics / Warehouse</option>
                                <option value="8">Marketing / Content</option>
                                <option value="9">Sales / Business Development</option>
                            </select>
                            </div>
                            <span class="form-text text-muted">Please enter your industry</span> <div class="fv-plugins-message-container"></div></div>
                        </div>
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Company Sizes:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-calculator"></i> </span> </div>
                              <input type="number" class="form-control form-control-lg form-control-solid" name="company_sizes" id="company_sizes" value="" placeholder="Enter company sizes">
                            </div>
                            <span class="form-text text-muted">Please enter your company sizes</span> <div class="fv-plugins-message-container"></div></div>
                        </div>
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Type:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-codepen"></i> </span> </div>
                              <select class="form-select form-select-lg form-select-solid" name="company_type" id="company_type">
                                <option value="">Select Type</option>
                                <option value="0" selected="selected">Public</option><option value="1">Private</option><option value="2">Joint-Venture</option><option value="3">Partnership Firm</option><option value="4">One Person</option><option value="5">Sole Proprietorship</option><option value="6">Branch Office</option><option value="7">Non-Government Organization</option>                              </select>
                            </div>
                            <span class="form-text text-muted">Please enter your type</span> <div class="fv-plugins-message-container"></div></div>
                        </div>
                        <div class="form-group row fv-plugins-icon-container">
                          <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Founded:</label>
                          <div class="col-lg-9 col-xl-9">
                            <div class="input-group input-group-lg input-group-solid">
                              <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-delicious"></i> </span> </div>
                              <input type="number" class="form-control form-control-lg form-control-solid" name="founded" id="founded" value="" placeholder="Enter founded year">
                            </div>
                            <span class="form-text text-muted">Please enter your founded year</span> <div class="fv-plugins-message-container"></div></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content">
                    <div class="card card-custom shadow-none">                        
                        <label class="form-label fw-bolder">Description:</label>
                        <div class="card-body p-0">
                            <div class="tinymce">
                                <textarea id="kt-tinymce-4" name="kt-tinymce-4" class="tox-target">
                                    
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                      <div class="col-lg-12">
                    </div>
                      <div class="col-lg-12 mt-7">
                        <label class="form-label fw-bolder">Tags:</label>
                        <tags class="tagify form-control form-control-solid tagify--noTags tagify--empty" tabindex="-1">
                                 <span contenteditable="" tabindex="0" data-placeholder="Enter tags" aria-placeholder="Enter tags" class="tagify__input" role="textbox" aria-autocomplete="both" aria-multiline="false"></span>
                                        &ZeroWidthSpace;
                             </tags><input class="form-control form-control-solid" name="" id="tags" placeholder="Enter tags" value="" tabindex="-1">
                        <span class="form-text text-muted">Please select your tags</span> <div class="fv-plugins-message-container"></div></div>
                    </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content">
                    <h3 class="mb-10 font-weight-bold text-dark">Enter the Address Details</h3>
                    <div class="row client-address-container">
                      <div class="row client-address-container-child" id="clientAddr_0">
                        <div class="form-group fv-plugins-icon-container">
                          <label class="form-label required fw-bolder">Address:</label>
                          <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="" id="address_0">
                          <span class="form-text text-muted">Please enter your Address</span> <div class="fv-plugins-message-container"></div></div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">Country:</label>
                            <input type="hidden" name="hid_country_id[]" id="hid_country_id_0" class="gethiddencountry" value="">
                            <select class="form-select form-control form-control-lg form-control-solid country" name="country[]" id="country_0">
                              <option value="">Select</option>
                              <option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua And Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas The</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegovina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="49">Republic Of The Congo</option><option value="50">Democratic Republic Of The Congo</option><option value="51">Cook Islands</option><option value="52">Costa Rica</option><option value="53">Cote D''Ivoire (Ivory Coast)</option><option value="54">Croatia (Hrvatska)</option><option value="55">Cuba</option><option value="56">Cyprus</option><option value="57">Czech Republic</option><option value="58">Denmark</option><option value="59">Djibouti</option><option value="60">Dominica</option><option value="61">Dominican Republic</option><option value="62">East Timor</option><option value="63">Ecuador</option><option value="64">Egypt</option><option value="65">El Salvador</option><option value="66">Equatorial Guinea</option><option value="67">Eritrea</option><option value="68">Estonia</option><option value="69">Ethiopia</option><option value="70">External Territories of Australia</option><option value="71">Falkland Islands</option><option value="72">Faroe Islands</option><option value="73">Fiji Islands</option><option value="74">Finland</option><option value="75">France</option><option value="76">French Guiana</option><option value="77">French Polynesia</option><option value="78">French Southern Territories</option><option value="79">Gabon</option><option value="80">Gambia The</option><option value="81">Georgia</option><option value="82">Germany</option><option value="83">Ghana</option><option value="84">Gibraltar</option><option value="85">Greece</option><option value="86">Greenland</option><option value="87">Grenada</option><option value="88">Guadeloupe</option><option value="89">Guam</option><option value="90">Guatemala</option><option value="91">Guernsey and Alderney</option><option value="92">Guinea</option><option value="93">Guinea-Bissau</option><option value="94">Guyana</option><option value="95">Haiti</option><option value="96">Heard and McDonald Islands</option><option value="97">Honduras</option><option value="98">Hong Kong S.A.R.</option><option value="99">Hungary</option><option value="100">Iceland</option><option value="101">India</option><option value="102">Indonesia</option><option value="103">Iran</option><option value="104">Iraq</option><option value="105">Ireland</option><option value="106">Israel</option><option value="107">Italy</option><option value="108">Jamaica</option><option value="109">Japan</option><option value="110">Jersey</option><option value="111">Jordan</option><option value="112">Kazakhstan</option><option value="113">Kenya</option><option value="114">Kiribati</option><option value="115">Korea North</option><option value="116">Korea South</option><option value="117">Kuwait</option><option value="118">Kyrgyzstan</option><option value="119">Laos</option><option value="120">Latvia</option><option value="121">Lebanon</option><option value="122">Lesotho</option><option value="123">Liberia</option><option value="124">Libya</option><option value="125">Liechtenstein</option><option value="126">Lithuania</option><option value="127">Luxembourg</option><option value="128">Macau S.A.R.</option><option value="129">Macedonia</option><option value="130">Madagascar</option><option value="131">Malawi</option><option value="132">Malaysia</option><option value="133">Maldives</option><option value="134">Mali</option><option value="135">Malta</option><option value="136">Man (Isle of)</option><option value="137">Marshall Islands</option><option value="138">Martinique</option><option value="139">Mauritania</option><option value="140">Mauritius</option><option value="141">Mayotte</option><option value="142">Mexico</option><option value="143">Micronesia</option><option value="144">Moldova</option><option value="145">Monaco</option><option value="146">Mongolia</option><option value="147">Montserrat</option><option value="148">Morocco</option><option value="149">Mozambique</option><option value="150">Myanmar</option><option value="151">Namibia</option><option value="152">Nauru</option><option value="153">Nepal</option><option value="154">Netherlands Antilles</option><option value="155">Netherlands The</option><option value="156">New Caledonia</option><option value="157">New Zealand</option><option value="158">Nicaragua</option><option value="159">Niger</option><option value="160">Nigeria</option><option value="161">Niue</option><option value="162">Norfolk Island</option><option value="163">Northern Mariana Islands</option><option value="164">Norway</option><option value="165">Oman</option><option value="166">Pakistan</option><option value="167">Palau</option><option value="168">Palestinian Territory Occupied</option><option value="169">Panama</option><option value="170">Papua new Guinea</option><option value="171">Paraguay</option><option value="172">Peru</option><option value="173">Philippines</option><option value="174">Pitcairn Island</option><option value="175">Poland</option><option value="176">Portugal</option><option value="177">Puerto Rico</option><option value="178">Qatar</option><option value="179">Reunion</option><option value="180">Romania</option><option value="181">Russia</option><option value="182">Rwanda</option><option value="183">Saint Helena</option><option value="184">Saint Kitts And Nevis</option><option value="185">Saint Lucia</option><option value="186">Saint Pierre and Miquelon</option><option value="187">Saint Vincent And The Grenadines</option><option value="188">Samoa</option><option value="189">San Marino</option><option value="190">Sao Tome and Principe</option><option value="191">Saudi Arabia</option><option value="192">Senegal</option><option value="193">Serbia</option><option value="194">Seychelles</option><option value="195">Sierra Leone</option><option value="196">Singapore</option><option value="197">Slovakia</option><option value="198">Slovenia</option><option value="199">Smaller Territories of the UK</option><option value="200">Solomon Islands</option><option value="201">Somalia</option><option value="202">South Africa</option><option value="203">South Georgia</option><option value="204">South Sudan</option><option value="205">Spain</option><option value="206">Sri Lanka</option><option value="207">Sudan</option><option value="208">Suriname</option><option value="209">Svalbard And Jan Mayen Islands</option><option value="210">Swaziland</option><option value="211">Sweden</option><option value="212">Switzerland</option><option value="213">Syria</option><option value="214">Taiwan</option><option value="215">Tajikistan</option><option value="216">Tanzania</option><option value="217">Thailand</option><option value="218">Togo</option><option value="219">Tokelau</option><option value="220">Tonga</option><option value="221">Trinidad And Tobago</option><option value="222">Tunisia</option><option value="223">Turkey</option><option value="224">Turkmenistan</option><option value="225">Turks And Caicos Islands</option><option value="226">Tuvalu</option><option value="227">Uganda</option><option value="228">Ukraine</option><option value="229">United Arab Emirates</option><option value="230">United Kingdom</option><option value="231">United States</option><option value="232">United States Minor Outlying Islands</option><option value="233">Uruguay</option><option value="234">Uzbekistan</option><option value="235">Vanuatu</option><option value="236">Vatican City State (Holy See)</option><option value="237">Venezuela</option><option value="238">Vietnam</option><option value="239">Virgin Islands (British)</option><option value="240">Virgin Islands (US)</option><option value="241">Wallis And Futuna Islands</option><option value="242">Western Sahara</option><option value="243">Yemen</option><option value="244">Yugoslavia</option><option value="245">Zambia</option><option value="246">Zimbabwe</option>                            </select>
                            <span class="form-text text-muted">Please select your country</span>
                            <div class="fv-plugins-message-container"></div>
                          <div class="fv-plugins-message-container"></div></div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">State:</label>
                            <select class="form-select form-control form-control-lg form-control-solid change_state" name="state[]" id="state_0">
                              <option value="">Select</option>
                              <option value="1">Uttar Pradesh</option>
                            </select>
                            <span class="form-text text-muted">Please select your state</span>
                            <div class="fv-plugins-message-container"></div>
                          <div class="fv-plugins-message-container"></div></div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">City</label>
                            <input type="text" class="form-control form-control-lg form-control-solid" name="city[]" placeholder="Enter city" value="" id="city_0">
                            <select class="form-select form-control form-control-lg form-control-solid" name="city_id[]" id="city_id_0" style="display:none">
                              <option value="">Select City</option>
                            </select>
                            <span class="form-text text-muted">Please enter your city:</span>
                            <div class="fv-plugins-message-container"></div>
                          </div>
                        </div>
                        <div class="col-xl-3">
                          <div class="form-group fv-plugins-icon-container">
                            <label class="form-label required fw-bolder">Postcode:</label>
                            <input type="number" class="form-control form-control-lg form-control-solid" name="postcode[]" placeholder="Enter postcode" value="" id="postcode_0">
                            <span class="form-text text-muted">Please enter your postcode</span>
                            <div class="fv-plugins-message-container"></div>
                          <div class="fv-plugins-message-container"></div></div>
                        </div>
                      </div>
                    </div>
                    <div id="addressLoader"></div>
                    <input type="hidden" name="addressId" id="addressId" value="0">
                    <div class="d-flex justify-content-center py-5 my-5">
                      <button type="button" class="btn btn-light-dark font-weight-bolder text-uppercase me-5  add_address">
                      <span class="svg-icon svg-icon svg-icon me-0">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                          <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                        </g>
                      </svg>
                      </span> Add more </button>
                      <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_address">
                      <span class="svg-icon svg-icon svg-icon-2x">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                        </g>
                      </svg>
                      </span>
                      Remove </button>
                    </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content">
                    <h3 class="mb-10 font-weight-bold text-dark">Enter the Contact Person Details</h3>
                    <div class="form-group">
                      <div class="col-sm-12 px-0">
                        <div class="form-group mb-0" id="addmore">
                          <label class="form-label fw-bolder">Contact Person Info:</label>
                        </div>
                        <div class="row client-cperson-container">
                          <div class="col-xl-3">
                            <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                              <input type="text" name="cp_name[]" id="cp_name_0" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter person name">
                              <span class="form-text text-muted">Please enter contact person name</span> <div class="fv-plugins-message-container"></div></div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                              <input type="email" name="cp_email[]" id="cp_email_0" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter email">
                              <span class="form-text text-muted">Please enter contact email</span> <div class="fv-plugins-message-container"></div></div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                              <input type="number" name="cp_number[]" id="cp_number_0" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter contact number">
                              <span class="form-text text-muted">Please enter contact number</span> <div class="fv-plugins-message-container"></div></div>
                          </div>
                          <div class="col-xl-3">
                            <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                              <input type="text" name="cp_desigination[]" id="cp_desigination_0" class="form-control form-control-lg form-control-solid rounded" placeholder="Enter desigination">
                              <span class="form-text text-muted">Please enter desigination</span> <div class="fv-plugins-message-container"></div></div>
                          </div>
                        </div>
                        <input type="hidden" name="contactPersonId" id="contactPersonId" value="0">
                        <div class="d-flex justify-content-center py-5 my-5">
                          <button type="button" class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_cperson">
                          <span class="svg-icon svg-icon svg-icon me-0">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                              <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                            </g>
                          </svg>
                          </span> Add more </button>
                          <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_cperson">
                          <span class="svg-icon svg-icon svg-icon-2x">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                            </g>
                          </svg>
                          </span>
                          Remove </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content">
                    <h3 class="mb-10 font-weight-bold text-dark">Review your Details and Submit</h3>
                    <h6 class="font-weight-bold mb-3">Company Information:</h6>
                    <div class="row pt-7">
                      <div class="col-lg-6">
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company Name</label>
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_name">&nbsp;</span> </div>
                        </div>
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Website</label>
                          <div class="col-lg-8"> <a href="javascript:void(0);" class="fw-bold text-gray-800 text-hover-primary fs-6 span_website">&nbsp;</a> </div>
                        </div>
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Industry</label>
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_industry">&nbsp;</span> </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company Sizes</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_sizes">&nbsp;</span> </div>
                        </div>
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company Type</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_type">&nbsp;</span> </div>
                        </div>
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Founded</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_founded">&nbsp;</span> </div>
                        </div>
                      </div>
                    </div>
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <h6 class="font-weight-bold mb-3">Company Description:</h6>
                    <div class="row pt-7">
                      <p class="text-justify span_kt_docs_tinymce_hidden">&nbsp; </p>
                    </div>
                    <div class="row pt-7">
                      <div class="col-lg-12">
                        <div class="row mb-7">
                          <label class="col-lg-2 fw-bold text-muted fs-6">Tags</label>
                          <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6 span_tags">&nbsp;</span> </div>
                        </div>
                      </div>
                    </div>
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <h6 class="font-weight-bold mb-3">Address Details:</h6>
                    <div class="row pt-7 divAddressDetail">&nbsp;</div>
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <h6 class="font-weight-bold mb-3">Contact Person:</h6>
                    <div class="row pt-7 div-contact-person">&nbsp;</div>
                  </div>
                  <div class="d-flex justify-content-center border-top mt-5 pt-10">
                    <div class="mr-2">
                      <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Back</button>
                    </div>
                    <div>
                      <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 submitfrm" data-wizard-type="action-submit1" style="display:none">Submit</button>                      <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Continue</button>
                    </div>
                  </div>
                <div></div><div></div><div></div><div></div></form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('app.edit')</h4>

                <form id="editSettings" class="ajax-form">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $company->company_name }}">
                    </div>
                    @if(module_enabled('Subdomain'))
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="subdomain" name="sub_domain" id="sub_domain"
                                       value="{{str_replace('.'.get_domain(),'',$company->sub_domain)}}">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">.{{ get_domain() }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                        <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $company->company_email }}">
                    </div>
                    <div class="form-group">
                        <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                        <input type="tel" class="form-control" id="company_phone" name="company_phone" value="{{ $company->company_phone }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                        <input type="text" class="form-control" id="website" name="website" value="{{ $company->website }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>
                        <div class="card">
                            <div class="card-body">
                                <input type="file" id="input-file-now" name="logo" class="dropify" @if(is_null($company->logo))
                                data-default-file="{{ asset('logo-not-found.png') }}" @else data-default-file="{{ asset_url('company-logo/'.$company->logo)
                                }}" @endif />
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                        <textarea class="form-control" id="address" rows="5" name="address">{{ $company->address }}</textarea>
                    </div>


                    <div class="form-group">
                        <label for="address">@lang('app.status')</label>
                        <select name="status" id="status" class="form-control">
                                <option @if($company->status == 'active') selected @endif>active</option>
                                <option @if($company->status == 'inactive') selected @endif>inactive</option>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="address">@lang('modules.company.showFrontend')</label>
                        <select name="show_in_frontend" id="show_in_frontend" class="form-control">
                                            <option @if($company->show_in_frontend == 'true') selected @endif value="true">@lang('app.yes')</option>
                                            <option @if($company->show_in_frontend == 'false') selected @endif value="false">@lang('app.no')</option>
                                        </select>
                    </div>
                    <div class="form-group">
                        <label class="">
                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" style="position: relative;">
                                <input  type="checkbox" @if($company->featured) checked @endif value="yes" name="featured" id="featured" class="flat-red"  style="position: absolute; opacity: 0;">
                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                            </div>
                            @lang('app.addAs') @lang('app.featured')
                        </label>
                    </div>
                    <div id="featureDateBox" @if(!$company->featured) style="display: none" @endif>

                        <div class="form-group" >
                            <label for="address">@lang('app.featured') @lang('app.startDate')</label>
                            <input type="text" class="form-control datepicker" id="date-start"  autocomplete="off"  value="@if($company->featured_start_date) {{ $company->featured_start_date->format('Y-m-d') }} @endif" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="address">@lang('app.featured') @lang('app.endDate')</label>
                            <input type="text" class="form-control datepicker" id="date-end" autocomplete="off" value="@if($company->featured_end_date) {{ $company->featured_end_date->format('Y-m-d') }} @endif" name="end_date">
                        </div>

                    </div>

                    <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                    <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 @push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script>

    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
    })

    // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    })

    $('#featured').on('ifChecked', function (event){
        $("#featureDateBox").show();
    });
    $('#featured').on('ifUnchecked', function (event) {
        $("#featureDateBox").hide();
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route("superadmin.company.update", $company->id)}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true
        })
    });

</script>




@endpush
