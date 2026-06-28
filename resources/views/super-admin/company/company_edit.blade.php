@extends('layouts.superadmin_layout')
@push('head-script')
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<style>
body{overflow-x:hidden;}
</style>
@endpush
 
@section('content')
@php 
	use App\Helper\GeneralMethod;
	$tags		=	'';
	$cnt		=	0;
	$logo_path  			=	url('public/avatar.png');	 
	if(!empty(@$account->logo) && strlen(@$account->logo)>10){
   			$logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); 	
   	}
	$tags					= 	@$client->tags;	
	$client_location_arr	=	$client_location;	

  $countAddrees= (count($client_location) > 0) ? count($client_location) : 1;

// dd($countAddrees);

@endphp 

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
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">1. Company Information</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  </span> </div>
                <!--end::Company Information Nav-->
                
				<!--begin::Comapny Description Nav-->
                	<div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">                  
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3" />
                        <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000" />
                      </g>
                    </svg>                    
                    </span>
                    <h3 class="wizard-title">2. Company Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>                 
                  </span> </div>
                <!--end::Company Description Nav-->
                
				<!--begin::Address Details Nav-->
                	<div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero" />
                        <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6" />
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">3. Address Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  </span> </div>
                <!--end::Address Details Nav-->
                
				<!--begin::Review and Submit Nav-->
               		<div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000" />
                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">4. Review and Submit</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow last">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  </span> </div>
              </div>
            </div>
            <!--end::Wizard Nav-->
			
			
			
			
			
			
            <!--begin::Wizard Body-->
            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
                <!--begin::Form Wizard Form-->
                <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_contact_add_form" name="kt_contact_add_form" enctype="multipart/form-data">
                  <meta name="csrf-token" content="{{ csrf_token() }}" />
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
                              <div class="image-input-wrapper w-90px h-90px" style="background-image: url({{ @$logo_path }})"></div>
                              <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-toggle="tooltip" data-placement="top" data-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                              <input type="file" accept="image/*" onchange="loadFile(event)" name="logo" id="logo">
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
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company_name" id="company_name" value="{{ @$account->company_name}}" placeholder="Enter company">
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
                              <input type="text" class="form-control form-control-lg form-control-solid" name="website" id="website" value="{{ @$account->website }}" placeholder="Enter website">
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
                              <!--<input type="text" class="form-control form-control-lg form-control-solid" name="industry" value="" placeholder="Enter industry">-->
                              <select class="form-select form-select-solid"  name="industries" id="industries">
                                <option value="">Select Industries</option>
 
@foreach($industries as $ind)
		@php	
			$category_id		=	$sel	=	'';
			$category_id		=	@$account->industries;
		@endphp 
		@if($category_id == @$ind->ind_id )
			@php $sel			=	'selected="selected"' ; @endphp 	
		@endif
		{!! '<option value="'.@$ind->ind_id.'"  '.$sel.'>'.ucfirst($ind->ind_name).'</option>' !!} 
@endforeach

                              </select>
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
                              <input type="number" class="form-control form-control-lg form-control-solid" name="company_size" id="company_size" value="{{ @$account->company_size }}" placeholder="Enter company sizes">
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
                              <select class="form-control form-control-lg form-control-solid"  name="industries_type" id="industries_type">
                                <option value="" >Select Type</option>
@foreach($industries_type_arr as $key=>$val)
	@php $sel	=	''; @endphp 
	@if($key == @$account->industries_type)
		@php $sel	=	'selected="selected"'; @endphp 
	@endif
	{!! '<option value="'.@$key.'"  '.@$sel.'>'.ucwords(@$val).'</option>' !!}	 
}
@endforeach
                             </select>
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
                              <input type="number" class="form-control form-control-lg form-control-solid" name="founded" id="founded" value="{{ @$account->founded }}" placeholder="Enter founded year">
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
                            <h3 class="font-weight-bold text-dark">Enter Description</h3>
                          </div>
                        </div>
                        <!--begin::Client Description-->
                        <div class="form-group row fv-row mb-10">
                          <div class="col-lg-12">
                            <label class="form-label required fw-bolder">Description:</label>
                            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">{!!$account->description  !!} </textarea>
                          </div>
                        </div>
                        <!--end::Client Description-->
                        <!--begin::Tags-->
                        <div class="col-lg-12 mt-7">
                          <label class="form-label required fw-bolder">Tags:</label>
                          <input class="form-control form-control-solid" name="tags" placeholder="Enter tags" value="{{ @$tags }}" id="tags"/>
                          <span class="form-text text-muted">Please select your tags</span> </div>
                        <!--end::Tags-->
                      </div>
                    </div>
                  </div>
                  <!--end::Client Description-->
                 <!--begin::Address Details-->
                 <div class="pb-5" data-wizard-type="step-content" >
                  <h3 class="mb-10 font-weight-bold text-dark">Enter the Address Details</h3>
                
                      @php 
                            $countryListing	= $stateListing =	'';
                            foreach($countryArr as $cont){
                              $sel			=	'';
                              $countryListing	.= '<option value="'.$cont['id'].'"  '.@$sel.' >'.$cont['name'].'</option>';	
                            }										
                                                                    
                            foreach($statelist as $state){
                              $sel			=	'';
                              $stateListing .= '<option value="'.$state['id'].'"  '.@$sel.' >'.$state['name'].'</option>';	
                            }
                                                                  
                            $addressId			=	0;

                            $countAdd = 1;
                          
                            $display_city_id	=	$stateListing 		=	$cityListing 		=	'';
                            if(count($client_location_arr) > 0){
                                                      
                            $clhtml			=	'<div class="row client-address-container">';
                            foreach($client_location_arr as $clVal) 
                            {
                                                        
                                $countryListing	= $stateListing =	'';
                                foreach($countryArr as $cont){
                                    $sel	=	'';
                                    if($cont['id'] == $clVal->country_id){
                                      $sel	=	'selected="selected"';
                                      $countryName = $cont['name'];
                                    }
                                    $countryListing	.= '<option value="'.$cont['id'].'"  '.@$sel.' >'.$cont['name'].'</option>';	
                                }										
                                $statelist	=	GeneralMethod::statelist($clVal->country_id);
                                foreach($statelist as $state){
                                    $sel	=	'';
                                    if($state['id'] == $clVal->state){
                                      $sel	=	'selected="selected"';
                                    }
                                    $stateListing .= '<option value="'.$state['id'].'"  '.@$sel.' >'.$state['name'].'</option>';	
                                }
                                                    
                                $display_city	=	$display_city_id = 'style="display:none"';

                                $statelist 					=	DB::table('state_list')
                                            ->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
                                            ->where('state_list.country_id', $clVal->country_id)
                                            ->orderBy('state_list.state')
                                            ->get()->toArray();
                            $indianStatelist       = json_decode(json_encode($statelist), true);


                                // if($clVal->country_id == 101)
                                // {
                                  $display_city_id	=	'';
                                  $stateListing 		=	'';
                                  $cityListing 		=	'';
                                  foreach($indianStatelist as $sRow){
                                    $sel	=	'';
                                  
                                    if($sRow['id'] == $clVal->state){
                                      // dd($sRow);
                                      $sel	=	'selected="selected"';
                                      $stateName = $sRow['name'];
                                    }
                                    $stateListing .= '<option value="'.$sRow['id'].'"  '.@$sel.' >'.$sRow['name'].'</option>';	
                                  }
                                                      
                                $Rawcitylist 				=	DB::table('city_list')
                                ->select('city_list.city_id as id', 'city_list.city as name')
                                ->where('city_list.state_id', $clVal->state)
                                ->orderBy('city_list.city')
                                ->get();
                                
                                foreach($Rawcitylist as $cRow){
                                  $sel	=	'';
                                  if($cRow->id == $clVal->city_id){
                                    $sel	=	'selected="selected"';
                                    $cityName = $cRow->name;
                                  }
                                  $cityListing .= '<option value="'.$cRow->id.'"  '.@$sel.' >'.$cRow->name.'</option>';	
                                }
                                                                                                                  
                              // }else{
                              // 	$display_city	= '';
                              // }
                              $return =  $cityName . ' | ' . $stateName . ' | ' . $countryName;
                                                                                  
                              $clhtml			.='
                                                <div class="row client-address-container-child" id="clientAddr_'.$addressId.'">                                                
                                                <div class="form-group">
                                                  <label class="form-label required fw-bolder">Address- : '.$countAdd.'</label>
                                                  <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="'.@$clVal->address.'" id="address_'.@$addressId.'">
                                                  <span class="form-text text-muted">Please enter your Address</span>
                                                </div>
                                                
                                                <div class="col-xl-12">
                                                  <div class="form-group fv-plugins-icon-container">
                                                    <label class="form-label required fw-bolder">Select City:</label>
                                                    <input type="hidden" name="hid_country_id[]" id="hid_country_id_'.@$addressId.'" class="gethiddencountry" value="'.$clVal->country_id.'" />
                                                    <input class="form-control form-control-solid" value="'.$return.'" id="kt_tagify_work_location_'.@$addressId.'" name="kt_tagify_work_location[]" placeholder="Enter your Location..."/>
                                                    <span class="form-text text-muted">Please select your city</span>
                                                    <div class="fv-plugins-message-container"></div>
                                                  </div>
                                                </div>
                                                                                                                                                        
                                                </div>';
                                                        
                                                        $addressId++;	
                                                        $countAdd++;
                                                      }
                                                      
                                                      $clhtml			.=	'</div>'; 
                                                      
                                                      echo $clhtml;
                                                      $addressId--;

                        }else{
                                                                                      
                    @endphp 

                      <div class="row client-address-container">
                                            <div class="row client-address-container-child" id="clientAddr_<?php echo @$addressId; ?>">
                                              <div class="form-group">
                                                <label class="form-label required fw-bolder">Address: 1</label>
                                                <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="" id="address_<?php echo @$addressId; ?>">
                                                <span class="form-text text-muted">Please enter your Address</span> </div>
                                              <div class="col-xl-12">
                                                <div class="form-group fv-plugins-icon-container">
                                                  <label class="form-label required fw-bolder">Select City:</label>
                                                  <input class="form-control form-control-solid" value="" id="kt_tagify_work_location_<?php echo @$addressId; ?>" name="kt_tagify_work_location[]" placeholder="Enter your Location..."/>
                                                  <span class="form-text text-muted">Please select your city</span>
                                                  <div class="fv-plugins-message-container"></div>
                                                </div>
                                              </div>
                                            </div>
                              </div>
                            <?php }
                          ?>
                              <div id="addressLoader"> </div>
                              <input type="hidden" name="addressId" id="addressId" value="{{ @$addressId }}"  />
                              <div class="d-flex justify-content-center py-5 my-5">
                                <button type="button" class="btn btn-light-dark font-weight-bolder text-uppercase me-5  add_address"> <span class="svg-icon svg-icon svg-icon me-0">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                  </g>
                                </svg>
                                </span> Add more </button>
                                <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_address"> <span class="svg-icon svg-icon svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                  </g>
                                </svg>
                                </span> Remove </button>
                              </div>
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
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company</label>
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_company_name">&nbsp;</span> </div>
                        </div>
                        <!--end::Company-->
                        
						<!--begin::Website-->
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Website</label>
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_website">&nbsp;</span> </div>
                        </div>
                        <!--end::Website-->
						
                        <!--begin::Industry-->
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Industry</label>
                          <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_category_id">&nbsp;</span> </div>
                        </div>
                        <!--end::Industry-->
                      </div>
                      <!--end::Left Section-->
                      
					  <!--begin::Right Section-->
                      <div class="col-lg-6">
                        <!--begin::Company Sizes-->
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Company Sizes</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_size">&nbsp;</span> </div>
                        </div>
                        
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Type</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_type">&nbsp;</span> </div>
                        </div>
                        <div class="row mb-7">
                          <label class="col-lg-4 fw-bold text-muted fs-6">Founded</label>
                          <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_founded">&nbsp;</span> </div>
                        </div>
                      </div>
                    </div>
                    <div class="separator separator-dashed mt-5 mb-10"></div>
                    <!--begin::Company Description content-->
                    <h6 class="font-weight-bold mb-3">Company Description:</h6>
                    <div class="row pt-7 text-justify span_description">&nbsp;</div>
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
                    <div class="row pt-7 divAddressDetail"> </div>
                     <div class="separator separator-dashed mt-5 mb-10"></div>
                    <h6 class="font-weight-bold mb-3">Account Verify:</h6>
                    <div class="row pt-7"> 
                      <div class="col-lg-12">
                        <span class="fw-bold text-gray-800 fs-6 w-100">
                          <div class="form-check d-inline-block me-3">
                            <input class="form-check-input" name="accountVerify" type="checkbox" id="accountVerify" {{ @$account->accountVerify ? 'checked' : '' }}>
                            <label class="form-check-label" for="accountVerify"> This Account Verify By JERO.WORKS</label>
                          </div>
                        </span>
                       </div>
                    </div>

                  </div>
                  <div class="d-flex justify-content-center border-top pt-10">
                    <div class="mr-2">
                      <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Back</button>
                    </div>
                    <div>
                      <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 frmupdate" data-wizard-type="action-submit1" style="display:none;">Save</button>
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
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script>
tinymce.init({
   selector: '#kt_docs_tinymce_basic'
});  
</script>
<script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script>
<script src="{{asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js')}}"></script>
<script>
function opensidebar(){
   		$('#kt_activitiesClient_toggle').trigger('click');
}    	
</script>
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/account.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
var input = document.querySelector('input[name=tags]');
new Tagify(input)
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

 // The DOM element you wish to replace with Tagify
 var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input)
   
   const cities = @json($cities);
   var countAddrees  = "{{ $countAddrees }}";
  //  alert('1');
    for (let i = 0; i < countAddrees; i++) {
        let inputId = `#kt_tagify_work_location_${i}`;
        let inputElement = document.querySelector(inputId);

        if (inputElement) {
            let tagifyInstance = new Tagify(inputElement, {
                whitelist: cities,
                maxTags: 1,
                dropdown: {
                    maxItems: 20,           // maximum allowed rendered suggestions
                    classname: "", // custom classname for this dropdown
                    enabled: 0,             // show suggestions on focus
                    closeOnSelect: true    // do not hide the suggestions dropdown once an item has been selected
                }
            });

            // Prevent creating tags on blur or enter key
            tagifyInstance.on('input', function(e) {
                tagifyInstance.dropdown.show(); // show the dropdown immediately
            });

            // Create tags only on select
            tagifyInstance.on('dropdown:select', function(e) {
                tagifyInstance.addTags([e.detail.data.value]);
            });

            tagifyInstance.on('add', function(e) {
              // Remove the tag if it wasn't selected from the dropdown
              if (!cities.includes(e.detail.data.value)) {
                tagifyInstance.removeTag(e.detail.data.value);
              }
            });

        }
    }


</script>

<script>
var loadFile = function(event){
    var output = URL.createObjectURL(event.target.files[0]);
    $('.image-input-wrapper').css('background-image', 'url(' + output + ')');
  };
$( document ).ready(function(){
    var room = 1;
    $( ".removeimg" ).click(function()
    {
        var defaultUrl 		=	'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
        $('.image-input-wrapper').css('background-image', 'url(' + defaultUrl + ')');
    });
});
</script>

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

<script>

	$(document).on('click', '.btn', function() { 
   	
     var state 	=	$('#kt_contact_add').attr("data-wizard-state"); 
     if(state == 'last'){
    
    $('.span_company_name').html('');
    $('.span_website').html('');
    $('.span_category_id').html('');
    $('.span_company_size').html('');
    $('.span_company_type').html('');
    $('.span_founded').html('');
    $('.span_success').html('');
    $('.span_description').html('');
    $('.span_tags').html('');
    $('.divAddressDetail').html('');
            
    var company_name		=	$('#company_name').val();
       $('.span_company_name').html(company_name);					
    var website				=	$('#website').val();
       $('.span_website').html(website);			
    var industries			=	$('#industries option:selected').text();   //$('#category_id').val();
       $('.span_category_id').html(industries);	
    var company_size		=	$('#company_size').val();
       $('.span_company_size').html(company_size);
    var company_type		=	$('#industries_type option:selected').text();   //$('#category_id').val();
       $('.span_company_type').html(company_type);	
    var founded				=	$('#founded').val();
       $('.span_founded').html(founded);	
       
       // var success_per				=	$('#success_per').val();
       // $('.span_success').html(success_per);	
    
    var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();      
       $('.span_description').html(kt_docs_tinymce_hidden);
          
    var tags					=	$('#tags').val(); 	
       var json_tags 				= $.parseJSON(tags);
       for(let len=0; len<json_tags.length; len++){
           if(len==0){
             $('.span_tags').append(json_tags[len].value);	
           }else{
             $('.span_tags').append('  , '+json_tags[len].value);	
           }
           
       }
       //Client Address			
       var addressId				=	$('#addressId').val();
       for(let cntAdd=0; cntAdd<=addressId; cntAdd++){
           var address			=	$('#address_'+cntAdd).val();
           var country			=	$('#country_'+cntAdd+' option:selected').text();      //$('#country_'+cntAdd).val();
           var state			=	$('#state_'+cntAdd+' option:selected').text();      // $('#state_'+cntAdd).val();
        var postcode		=	$('#postcode_'+cntAdd).val();
        
        var city			=	$('#city_'+cntAdd).val();
        if(country == 'India'){
           city			=	$('#city_id_'+cntAdd+' option:selected').text();	
        }
        
        if(country == 'Select'){
          country 	=	'-------';	
        }
        if(state == 'Select' || state == 'Select State'){
          state 	=	'-------';	
        }
        if(city == 'Select City'){
          city 	=	'-------';	
        }
        if(postcode == ''){
          postcode 	=	'-------';	
        }

        var cextractedValueity		=	$('#kt_tagify_work_location_'+cntAdd).val();
        
        var city = '';
            if (typeof address === 'undefined') {
              
            }else{
          var city = cextractedValueity
                  .replace('[{"value":"', '')  // Remove the starting part
                  .replace('"}]', '');         // Remove the ending part

           $('.divAddressDetail').append('<div class="row mb-4"><div class="col-lg-2"><label class=" fw-bold text-muted fs-6">Address</label></div><div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6">'+address+'</span> </div></div><div class="row"><div class="col-lg-2"><label class="fw-bold text-muted fs-6 w-100">City</label></div><div class="col-lg-10"><span class="fw-bold text-gray-800 fs-6 w-100">'+city+'</span> </div></div>');	
          }
           
       }
       $('.frmupdate').show();
     }else{
       $('.frmupdate').hide();
     }
   });
   	 
   $(document).on('click', '.frmupdate', function() {
   
   	
   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_contact_add_form').serializeArray();
   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});
   	
   	var file_data 		= $('input[name="logo"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}
   
   	var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();
    	data.append("description", kt_docs_tinymce_hidden);
   	data.append('key', 'value');
   
   	$.ajax({
       			
   				url					: '{{route('superadmin.company.update_company', @$account->id)}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
       			success				: function (data) {
   										
   										if(data.status == 'success'){
   												 	window.location = '{{ route('superadmin.company.index') }}'
   													//{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/service_company\/clients"}	
   										}
   										
       			}
       			
   			});
   
   
   
   });
</script>
<script>
   $( document ).ready(function() { 
   
   	$('.add_address').click(function (){	
   	
   	
   	var addressId			=	$('#addressId').val();
   	addressId++;
   	$('#addressId').val(addressId);
   
   	var clientAddr_id		= "clientAddr_"+addressId;
   	var address_id			= "address_"+addressId;
   	var country_id			= "country_"+addressId;	
   	var state_id			= "state_"+addressId;
   	var city_id				= "city_"+addressId;
   	var postcode_id			= "postcode_"+addressId;
   	var countryListing		=	'';
   
   $('#addressLoader').show();
   $('#addressLoader').html('');
   	
		   var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 	
		   var cid					=	addressId ;
		   var url 					=	"{{ route('superadmin.company.addresslist',':cid') }}";
		   url 						=	url.replace(':cid', cid);
								   $.ajax({
											type		: 'GET',
											url			: url,
											 beforeSend	: function() {
																		
																			$('#addressLoader').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
											success		: function (response) {																		
																				$('#addressLoader').hide();
																				$('.client-address-container').last().append(response.data);
																				$(".country").trigger("change");
																		  }		
								   });		
   	
   	});
   	
   
   	$('.remove_address').click(function(){
   			$('.client-address-container').children().last().remove();
   	});
		
	$(document).on('change', '.country', function() { 
   	
   				cid			=	this.value;				
   				var str 	=	$(this).attr('id');
   				var id		=	str.substr(8);   
   				var id 		=	parseInt(id);
				
				if(cid == 101){
					$('#city_id_'+id).show();	
					$('#city_'+id).hide();	
				}else{
					$('#city_'+id).show();	
					$('#city_id_'+id).hide();	
				}
				
				$('#hid_country_id_'+id).val(cid);
   				
   					var url 			=	"{{ route('superadmin.company.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {		
   																		$('#state_'+id).html(response.data);	
   																   }							   
   						 });				
   								
   	});
	$(document).on('change', '.change_state', function() { 
   	
   				state_id			=	this.value;				
   				var str 			=	$(this).attr('id');
   				var ccountryid		=	str.substr(6);   
   				var ccountryid 		=	parseInt(ccountryid);
   				
				var hid_country_id	=	$('#hid_country_id_'+ccountryid).val(); 
				if(hid_country_id == 101){
						$('#city_id_'+ccountryid).show();	
						$('#city_'+ccountryid).hide();	
				}else{
						$('#city_'+ccountryid).show();	
						$('#city_id_'+ccountryid).hide();	
				}
				
				var url 			=	"{{ route('superadmin.company.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id_'+ccountryid).html(response.data);
															   }		
									   
				});				
   								
   	});
	
});	
</script>
@endpush