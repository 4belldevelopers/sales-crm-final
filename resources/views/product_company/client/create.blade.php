@extends('layouts.product_company_layout')
@push('head-script')
<!-- <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>"> -->
<!--Add New Css of Metronic-7-->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!--Add New Css of Metronic-7 End-->
<style>
body
{
   overflow-x	:	hidden;
   widht		:	100%;
}
.form-label
{
   margin-bottom	: 0.5rem!important;
   font-size		: 1.05rem!important;
   font-weight		: 500!important;
   color			: #3f4254!important;
}
.last-input-border-radius
{
   border-radius	: 5px 0px 0px 5px!important;
}
</style>
@endpush
@section('content')
<?php 
   use App\Helper\GeneralMethod;
   
     $background_image		=	url('img/client-default.png'); //'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
     $name	=	$website	=	$industry	=	$company_sizes	=	$company_type	=	$founded	= $description = $tags	= $client_location_arr = $client_contact_arr =  '';
     if($page_event == 'ADD'){
     	$pagetitle	=	'Add Client';	
     }else if($page_event == 'EDIT'){ 
     	$pagetitle	=	'Edit Client';
     	
     	$name					= @$client->name; 
     	$website				= @$client->website; 
     	$industry				= @$client->industry; 
     	$company_sizes			= @$client->company_sizes; 
     	$company_type			= @$client->company_type; 
     	$founded				= @$client->founded; 
     	
     	$description			= 	@$client->description;
     	$tags					= 	@$client->tags;
     	$client_location_arr	=	$client_location;
     	$client_contact_arr 	=	$client_contact_info;
     	
     	if(!empty(@$client->logo_logical_path) && strlen(@$client->logo_logical_path)>10){
     			$background_image		=  url('public/'.@$client->logo_logical_path); 	
     	}
     	
     	//echo '<pre>'; print_r($client); exit; 
     }
     
     ?>										
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
   <!--begin::Container-->
   <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
         <!--begin::Add Requisition-->      
         <div class="d-flex flex-column">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1"><?php echo @$pagetitle; ?></a></h1>
         </div>
         <!--end::Add Requisition-->
         <!--begin::Separator-->
         <span class="h-20px border-gray-200 border-start mx-4"></span>
         <!--end::Separator-->
         <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter client details and submit</span>
      </div>
      <!--end::Page title-->
   </div>
   <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-5" id="kt_content">
   <!--begin::Entry-->
   <div class="d-flex flex-column-fluid">
      <!--begin::Container-->
      <div class="container-fluid px-3">
         <!--Card-->
         <div class="card card-custom">
            <div class="card-body p-0">
               <!--begin::Wizard-->
               <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="first" data-wizard-clickable="false">
                  <!--begin::Wizard Nav-->
                  <div class="wizard-nav border-bottom">
                     <div class="wizard-steps p-8 p-lg-10">
                        <!--begin::Client Information-->
                        <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                           <div class="wizard-label">
                              <!-- <i class="wizard-icon flaticon-bus-stop"></i> -->
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-4x wizard-icon">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                       <rect x="0" y="0" width="24" height="24"/>
                                       <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
                                       <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    </g>
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                              <h3 class="wizard-title">1. Client Information</h3>
                           </div>
                           <span class="svg-icon svg-icon-xl wizard-arrow">
                              <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                 </g>
                              </svg>
                              <!--end::Svg Icon-->
                           </span>
                        </div>
                        <!--end::Client Information-->
                        <!--begin::Client Description-->
                        <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                           <div class="wizard-label">
                              <!-- <i class="wizard-icon flaticon-list"></i> -->
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-4x wizard-icon">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                       <rect x="0" y="0" width="24" height="24" />
                                       <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3" />
                                       <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000" />
                                    </g>
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                              <h3 class="wizard-title">2. Client Description</h3>
                           </div>
                           <span class="svg-icon svg-icon-xl wizard-arrow">
                              <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                 </g>
                              </svg>
                              <!--end::Svg Icon-->
                           </span>
                        </div>
                        <!--end::Client Description-->
                        <!--begin::Address Details-->
                        <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                           <div class="wizard-label">
                              <!-- <i class="wizard-icon flaticon-responsive"></i> -->
                              <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Globe.svg-->
                              <span class="svg-icon svg-icon-4x wizard-icon">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                       <rect x="0" y="0" width="24" height="24" />
                                       <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero" />
                                       <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6" />
                                    </g>
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                              <h3 class="wizard-title">3. Address Details</h3>
                           </div>
                           <span class="svg-icon svg-icon-xl wizard-arrow">
                              <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                 </g>
                              </svg>
                              <!--end::Svg Icon-->
                           </span>
                        </div>
                        <!--end::Address Details-->
                        <!--begin::Contact Person-->
                        <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                           <div class="wizard-label">
                              <!-- <i class="wizard-icon flaticon-truck"></i> -->
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-4x wizard-icon">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                       <polygon points="0 0 24 0 24 24 0 24"/>
                                       <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                       <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                    </g>
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                              <h3 class="wizard-title">4. Contact Person</h3>
                           </div>
                           <span class="svg-icon svg-icon-xl wizard-arrow">
                              <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                                    <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                                 </g>
                              </svg>
                              <!--end::Svg Icon-->
                           </span>
                        </div>
                        <!--end::Contact Person-->
                        <!--begin::Review and Submit-->
                        <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending">
                           <div class="wizard-label">
                              <!-- <i class="wizard-icon flaticon-globe"></i> -->
                              <!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
                              <span class="svg-icon svg-icon-4x wizard-icon">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                       <rect x="0" y="0" width="24" height="24" />
                                       <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000" />
                                       <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                                    </g>
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                              <h3 class="wizard-title">5. Review and Submit</h3>
                           </div>
                        </div>
                        <!--end::Review and Submit-->
                     </div>
                  </div>
                  <!--end::Wizard Nav-->
                  <!--begin::Wizard Body-->
                  <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
                     <div class="col-xl-12 col-xxl-12">
                        <!--begin::Wizard Form-->
                        <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_form" name="kt_form">
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
                                          <div class="image-input image-input-outline" id="logo">
                                             <div class="image-input-wrapper w-90px h-90px" style="background-image: url(<?php echo @$background_image; ?>)"></div>
                                             <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="Change logo">
                                                <i class="fa fa-pen icon-sm text-muted"></i>
                                                <input type="file" name="logo" id="logo" accept=".png, .jpg, .jpeg"  onchange="loadFile(event)"/>
                                                <input type="hidden" name="avatar_remove" />
                                             </label>
                                             <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-danger btn-shadow" data-action="cancel" data-toggle="tooltip" title="Remove logo">
                                                <i class="fa fa-times icon-sm text-muted"></i>
                                             </span>
                                          </div>
                                          <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                                       </div>
                                    </div>
                                    <!--end::Image-->						
                                    <!--begin::Client-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Client:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-building"></i>
                                                </span>
                                             </div>
                                             <input type="text" class="form-control form-control-lg form-control-solid" name="name" id="client_id" value="<?php echo @$name; ?>" placeholder="Enter client">
                                          </div>
                                          <span class="form-text text-muted">Please enter your Name</span>
                                       </div>
                                    </div>
                                    <!--end::Client-->
                                    <!--begin::Website-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Website:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-globe"></i>
                                                </span>
                                             </div>
                                             <input type="text" class="form-control form-control-lg form-control-solid" name="website" id="website" value="<?php echo @$website; ?>" placeholder="Enter website">
                                          </div>
                                          <span class="form-text text-muted">Please enter your Website</span>
                                       </div>
                                    </div>
                                    <!--end::Website-->
                                    <!--begin::Industry-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Industry:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-bar-chart"></i>
                                                </span>
                                             </div>
                                             <select class="form-select form-select-lg form-select-solid" name="industry" id="industry">
                                                <option value="" >Select Industry</option>
                                                <?php 
                                                   foreach($department as $key=>$val){
                                                   	$sel	=	'';
                                                   	if($key == $industry){
                                                   		$sel	=	'selected="selected"';	
                                                   	}
                                                   	echo '<option value="'.@$key.'"  '.@$sel.' >'.ucwords(@$val).'</option>';	 
                                                   }
                                                   ?>
                                             </select>
                                          </div>
                                          <span class="form-text text-muted">Please enter your industry</span>
                                       </div>
                                    </div>
                                    <!--end::Industry-->
                                    <!--begin::Company Sizes-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Company Sizes:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-calculator"></i>
                                                </span>
                                             </div>
                                             <input type="number" class="form-control form-control-lg form-control-solid" name="company_sizes" id="company_sizes" value="<?php echo @$company_sizes; ?>" placeholder="Enter company sizes">
                                          </div>
                                          <span class="form-text text-muted">Please enter your company sizes</span>
                                       </div>
                                    </div>
                                    <!--end::Company Sizes-->
                                    <!--begin::Type-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Type:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-codepen"></i>
                                                </span>
                                             </div>
                                             <select class="form-select form-select-lg form-select-solid" name="company_type" id="company_type">
                                                <option value="" >Select Type</option>
                                                <?php 
                                                   foreach($companyType as $key=>$val){
                                                   	$sel	=	'';
                                                   	if($key == $company_type){
                                                   		$sel	=	'selected="selected"';
                                                   	}
                                                   	echo '<option value="'.@$key.'"  '.@$sel.'>'.ucwords(@$val).'</option>';	 
                                                   }
                                                   ?>
                                             </select>
                                          </div>
                                          <span class="form-text text-muted">Please enter your type</span>
                                       </div>
                                    </div>
                                    <!--end::Type-->
                                    <!--begin::Founded-->
                                    <div class="form-group row fv-plugins-icon-container">
                                       <label class="col-xl-3 col-lg-3 col-form-label required fw-bolder">Founded:</label>
                                       <div class="col-lg-9 col-xl-9">
                                          <div class="input-group input-group-lg input-group-solid">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <i class="la la-delicious"></i>
                                                </span>
                                             </div>
                                             <input type="number" class="form-control form-control-lg form-control-solid" name="founded" id="founded" value="<?php echo @$founded; ?>" placeholder="Enter founded year">
                                          </div>
                                          <span class="form-text text-muted">Please enter your founded year</span>
                                       </div>
                                    </div>
                                    <!--end::Founded-->
                                 </div>
                              </div>
                           </div>
                           <!--end::Client Information-->
                           <!--begin::Client Description-->
                           <div class="pb-5" data-wizard-type="step-content">
                              <h3 class="mb-10 font-weight-bold text-dark">Enter the Client Description</h3>
                              <div class="form-group row fv-row mb-10">
                                 <!--begin::Description-->
                                 <div class="col-lg-12">
                                    <label class="form-label required fw-bolder">Description:</label>
                                    <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$description; ?></textarea>
                                 </div>
                                 <!--end::Description-->
                                 <!--begin::Tags-->
                                 <div class="col-lg-12 mt-7">
                                    <label class="form-label required fw-bolder">Tags:</label>
                                    <!-- <input class="form-control" name="tagify_input" value="" id="kt_tagify" /> -->
                                    <input class="form-control form-control-solid" name="tags" id="tags" placeholder="Enter tags" value="<?php echo @$tags; ?>" >
                                    <span class="form-text text-muted">Please select your tags</span>
                                 </div>
                                 <!--end::Tags-->
                              </div>
                           </div>
                           <!--end::Client Description-->
                           <!--begin::Address Details-->
                           <div class="pb-5" data-wizard-type="step-content" >
                              <h3 class="mb-10 font-weight-bold text-dark">Enter the Address Details</h3>
                              <?php 
                                 $countryListing	= $stateListing =	'';
                                 foreach($countryArr as $cont){
                                 $sel	=	'';
                                 //if($cont['id'] == 101){
                                 	//$sel	=	'selected="selected"';
                                 //}
                                 $countryListing	.= '<option value="'.$cont['id'].'"  '.@$sel.' >'.$cont['name'].'</option>';	
                                 }										
                                 
                                 
                                 foreach($statelist as $state){
                                 $sel	=	'';
                                 //if($state['id'] == 101){
                                 //	$sel	=	'selected="selected"';
                                 //}
                                 $stateListing .= '<option value="'.$state['id'].'"  '.@$sel.' >'.$state['name'].'</option>';	
                                 }
                                 
                                
								
								 
                                 		
                                 ?>
                              <?php 
                                 $addressId	=	0;
                                 if($page_event == 'EDIT')
                                 {
									$display_city_id	=	'';
									$stateListing 		=	'';
									$cityListing 		=	'';
																	
                                 	$clhtml			=	'<div class="row client-address-container">';
                                 	foreach($client_location_arr as $clVal) 
                                 	{
                                 		
                                 								$countryListing	= $stateListing =	'';
                                 								foreach($countryArr as $cont){
                                 										$sel	=	'';
                                 										if($cont['id'] == $clVal->country_id){
                                 											$sel	=	'selected="selected"';
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
																
																
																if($clVal->country_id == 101)
																{
																	$display_city_id	=	'';
																	$stateListing 		=	'';
																	$cityListing 		=	'';
																	foreach($indianStatelist as $sRow){
																		$sel	=	'';
                                 										if($sRow['id'] == $clVal->state){
                                 											$sel	=	'selected="selected"';
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
                                 										}
                                 										$cityListing .= '<option value="'.$cRow->id.'"  '.@$sel.' >'.$cRow->name.'</option>';	
																	}
																	
																	
													
																}else{
																	$display_city	= '';
																}
																
                                 									
                                 		$clhtml			.='
                                          <div class="row client-address-container-child" id="clientAddr_'.$addressId.'">                                                
                                             <div class="form-group">
                                                <label class="form-label required fw-bolder">Address:</label>
                                                <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="'.@$clVal->address.'" id="address_'.@$addressId.'">
                                                <span class="form-text text-muted">Please enter your Address</span>
                                             </div>
                                          
                                             <div class="col-xl-3">
                                                <div class="form-group fv-plugins-icon-container">
                                                   <label class="form-label required fw-bolder">Country:</label>
                                                   <input type="hidden" name="hid_country_id[]" id="hid_country_id_'.@$addressId.'" class="gethiddencountry" value="'.$clVal->country_id.'" />
                                                   <select class="form-select form-select form-control form-control-lg form-control-solid country" name="country[]" id="country_'.$addressId.'">
                                                   <option value="">Select</option>
                                                   '.@$countryListing.'									  
                                                   </select>
                                                   <span class="form-text text-muted">Please select your country</span>
                                                   <div class="fv-plugins-message-container"></div>
                                                </div>
                                             </div>
                                          
                                             <div class="col-xl-3">
                                             <div class="form-group fv-plugins-icon-container">
                                                <label class="form-label required fw-bolder">State:</label>
                                                <select class="form-select form-control form-control-lg form-control-solid change_state" name="state[]" id="state_'.$addressId.'">
                                                   <option value="">Select</option>
                                                   '.@$stateListing.'
                                                </select>
                                                <span class="form-text text-muted">Please select your state</span>
                                                <div class="fv-plugins-message-container"></div>
                                             </div>
                                          </div>
                                          <div class="col-xl-3">
                                             <div class="form-group fv-plugins-icon-container">
                                                <label class="form-label required fw-bolder">City</label>
                                                <input type="text" class="form-control form-control-lg form-control-solid" name="city[]" placeholder="Enter city" value="'.@$clVal->city.'" id="city_'.$addressId.'"  '.$display_city.'>
                                                <select class="form-select form-control form-control-lg form-control-solid" name="city_id[]" id="city_id_'.@$addressId.'"   '.$display_city_id.'>
                                                <option value="">Select City</option>
                                                '.$cityListing.'
                                                </select>

                                                <span class="form-text text-muted">Please enter your city:</span>
                                                <div class="fv-plugins-message-container"></div>
                                             </div>
                                          </div>
                                          
                                          <div class="col-xl-3">
                                                <div class="form-group fv-plugins-icon-container">
                                                   <label class="form-label required fw-bolder">Postcode:</label>
                                                   <input type="number" class="form-control form-control-lg form-control-solid" name="postcode[]" placeholder="Enter postcode" value="'.@$clVal->postcode.'" id="postcode_'.$addressId.'">
                                                   <span class="form-text text-muted">Please enter your postcode</span>
                                                   <div class="fv-plugins-message-container"></div>
                                                </div>
                                             </div>                                                                     
                                          </div>';
                                 		
                                 		$addressId++;	
                                 	}
                                 	
                                 	$clhtml			.=	'</div>'; 
                                 	
                                 	echo $clhtml;
                                 	$addressId--;
                                 	
                                 }
                                 else{					 
                                 ?>
								 
                              <div class="row client-address-container">
                                 <div class="row client-address-container-child" id="clientAddr_<?php echo @$addressId; ?>">
                                    <div class="form-group">
                                       <label class="form-label required fw-bolder">Address:</label>
                                       <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="" id="address_<?php echo @$addressId; ?>">
                                       <span class="form-text text-muted">Please enter your Address</span>
                                    </div>
                                    <div class="col-xl-3">
                                       <div class="form-group fv-plugins-icon-container">
                                          <label class="form-label required fw-bolder">Country:</label>
                                          <input type="hidden" name="hid_country_id[]" id="hid_country_id_<?php echo @$addressId; ?>" class="gethiddencountry" value="" />
										               <select class="form-select form-control form-control-lg form-control-solid country" name="country[]" id="country_<?php echo @$addressId; ?>">
                                             <option value="">Select</option>
                                             <?php echo @$countryListing;?>									  
                                          </select>
                                          <span class="form-text text-muted">Please select your country</span>
                                          <div class="fv-plugins-message-container"></div>
                                       </div>
                                    </div>
                                    <div class="col-xl-3">
                                       <div class="form-group fv-plugins-icon-container">
                                          <label class="form-label required fw-bolder">State:</label>
                                          <select class="form-select form-control form-control-lg form-control-solid change_state" name="state[]" id="state_<?php echo @$addressId; ?>">
                                             <option value="">Select</option>
                                            
                                          </select>
                                          <span class="form-text text-muted">Please select your state</span>
                                          <div class="fv-plugins-message-container"></div>
                                       </div>
                                    </div>
                                    <div class="col-xl-3">
                                       <div class="form-group fv-plugins-icon-container">
                                          <label class="form-label required fw-bolder">City</label>
                                          <input type="text" class="form-control form-control-lg form-control-solid" name="city[]" placeholder="Enter city" value="" id="city_<?php echo @$addressId; ?>">
                                          <select class="form-select form-control form-control-lg form-control-solid" name="city_id[]" id="city_id_<?php echo @$addressId; ?>" style="display:none">
                                             <option value="">Select City</option>
                                          </select>
										            <span class="form-text text-muted">Please enter your city:</span>
                                          <div class="fv-plugins-message-container"></div>
                                       </div>
                                    </div>
                                    <div class="col-xl-3">
                                       <div class="form-group fv-plugins-icon-container">
                                          <label class="form-label required fw-bolder">Postcode:</label>
                                          <input type="number" class="form-control form-control-lg form-control-solid" name="postcode[]" placeholder="Enter postcode" value="" id="postcode_<?php echo @$addressId; ?>">
                                          <span class="form-text text-muted">Please enter your postcode</span>
                                          <div class="fv-plugins-message-container"></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php 
                                 }
                                 ?>	
                              <div id="addressLoader"></div>
                              <input type="hidden" name="addressId" id="addressId" value="<?php echo @$addressId; ?>"  /> 
                              <!--begin::Add More Button-->
                              <div class="d-flex justify-content-center py-5 my-5">
                                 <button type="button" class="btn btn-light-dark font-weight-bolder text-uppercase me-5  add_address">
                                    <!--begin::Svg Icon-->
                                    <span class="svg-icon svg-icon svg-icon me-0">
                                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                             <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                             <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                          </g>
                                       </svg>
                                    </span>
                                    Add more
                                 </button>
                                 <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_address">
                                    <!-- <i class="fa fa-plus"></i> -->
                                    <!--begin::Svg Icon-->
                                    <span class="svg-icon svg-icon svg-icon-2x">
                                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                             <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                          </g>
                                       </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    Remove
                                 </button>
                              </div>
                              <!--begin::Add More Button-->
                           </div>
                           <!--end::Address Details-->
                           <!--begin::Contact Person-->                 
                           <div class="pb-5" data-wizard-type="step-content">
                              <h3 class="mb-10 font-weight-bold text-dark">Enter the Contact Person Details</h3>
                              <div class="form-group">
                                 <div class="col-sm-12 px-0">
                                    <div class="form-group mb-0" id="addmore">     
                                       <label class="form-label fw-bolder">Contact Person Info:</label> 													
                                    </div>
                                    <?php 
                                       $contactPersonId = 0;
                                       if($page_event == 'EDIT')
                                       {
                                       	foreach($client_contact_arr as $ccArr)
                                       	{
                                             echo '<div class="row client-cperson-container">	
                                             
                                             <div class="col-xl-3">
                                                <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                                   <input type="text" name="cp_name[]" id="cp_name_'.$contactPersonId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_name.'" placeholder="Enter person name">
                                                   <span class="form-text text-muted">Please enter contact person name</span>
                                                </div>
                                             </div>   

                                             <div class="col-xl-3">
                                                <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                                   <input type="email" name="cp_email[]" id="cp_email_'.$contactPersonId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_email.'" placeholder="Enter email">
                                                   <span class="form-text text-muted">Please enter contact email</span>
                                                </div>
                                             </div> 

                                             <div class="col-xl-3">
                                                <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                                   <input type="number" name="cp_number[]" id="cp_number_'.$contactPersonId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_number.'" placeholder="Enter contact number">
                                                   <span class="form-text text-muted">Please enter contact number</span>
                                                </div>
                                             </div> 

                                             <div class="col-xl-3">
                                                <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                                   <input type="text" name="cp_desigination[]" id="cp_desigination_'.$contactPersonId.'" class="form-control form-control-lg form-control-solid rounded" value="'.$ccArr->contact_person_designation.'" placeholder="Enter desigination">
                                                   <span class="form-text text-muted">Please enter desigination</span>
                                                </div>
                                             </div>
                                          </div>';	
                                       		$contactPersonId++;	
                                       	}
                                       	$contactPersonId--;
                                       }
                                       else 
                                       {
                                       ?>	
                                    <div class="row client-cperson-container">
                                       <div class="col-xl-3">                              
                                          <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                             <input type="text" name="cp_name[]" id="cp_name_<?php echo $contactPersonId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter person name">
                                             <span class="form-text text-muted">Please enter contact person name</span>
                                          </div>
                                       </div>

                                       <div class="col-xl-3">                              
                                          <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                             <input type="email" name="cp_email[]" id="cp_email_<?php echo $contactPersonId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter email">
                                             <span class="form-text text-muted">Please enter contact email</span>
                                          </div>
                                       </div>

                                       <div class="col-xl-3">                              
                                          <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                             <input type="number" name="cp_number[]" id="cp_number_<?php echo $contactPersonId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter contact number">
                                             <span class="form-text text-muted">Please enter contact number</span>
                                          </div>
                                       </div>

                                       <div class="col-xl-3">                              
                                          <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                                              <input type="text" name="cp_desigination[]" id="cp_desigination_<?php echo $contactPersonId;?>" class="form-control form-control-lg form-control-solid rounded" placeholder="Enter desigination">
                                             <span class="form-text text-muted">Please enter desigination</span>
                                          </div>
                                       </div>
                                    </div>
                                    <?php 
                                       }
                                       ?>
                                    <input type="hidden" name="contactPersonId" id="contactPersonId" value="<?php echo @$contactPersonId; ?>"  /> 
                                    <div class="d-flex justify-content-center py-5 my-5">
                                       <button type="button" class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_cperson">
                                          <!--begin::Svg Icon-->
                                          <span class="svg-icon svg-icon svg-icon me-0">
                                             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                   <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                   <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                                                </g>
                                             </svg>
                                          </span>
                                          Add more
                                       </button>
                                       <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_cperson">
                                          <!-- <i class="fa fa-plus"></i> -->
                                          <!--begin::Svg Icon-->
                                          <span class="svg-icon svg-icon svg-icon-2x">
                                             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                   <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                </g>
                                             </svg>
                                          </span>
                                          <!--end::Svg Icon-->
                                          Remove
                                       </button>
                                    </div>
                                    <!--begin::Add & Remove Button-->
                                 </div>
                              </div>
                           </div>
                           <!--end::Contact Person-->
                           <!--begin::Review and Submit-->
                           <div class="pb-5" data-wizard-type="step-content">
                              <h3 class="mb-10 font-weight-bold text-dark">Review your Details and Submit</h3>
                              <h6 class="font-weight-bold mb-3">Client Information:</h6>
                              <!--begin::Client Information content-->
                              <div class="row pt-7">
                                 <!--begin::Left Section-->
                                 <div class="col-lg-6">
                                    <!--begin::Ciient-->
                                    <div class="row mb-7">
                                       <!--begin::Title-->
                                       <label class="col-lg-4 fw-bold text-muted fs-6">Client Name</label>
                                       <!--end::Title-->
                                       <!--begin::Description-->
                                       <div class="col-lg-8">
                                          <span class="fw-bold text-gray-800 fs-6 span_name">&nbsp;</span>
                                       </div>
                                       <!--end::Description-->
                                    </div>
                                    <!--end::Ciient-->                        
                                    <!--begin::Website-->
                                    <div class="row mb-7">
                                       <!--begin::Title-->
                                       <label class="col-lg-4 fw-bold text-muted fs-6">Website</label>
                                       <!--end::Title-->
                                       <!--begin::Description-->
                                       <div class="col-lg-8">
                                          <a href="javascript:void(0);" class="fw-bold text-gray-800 text-hover-primary fs-6 span_website">&nbsp;</a>
                                       </div>
                                       <!--end::Description-->
                                    </div>
                                    <!--end::Website-->                       
                                    <!--begin::Industry-->
                                    <div class="row mb-7">
                                       <!--begin::Title-->
                                       <label class="col-lg-4 fw-bold text-muted fs-6">Industry</label>
                                       <!--end::Title-->
                                       <!--begin::Description-->
                                       <div class="col-lg-8">
                                          <span class="fw-bold text-gray-800 fs-6 span_industry">&nbsp;</span>
                                       </div>
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
                                       <div class="col-lg-8">
                                          <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_sizes">&nbsp;</span>
                                       </div>
                                       <!--end::Description-->
                                    </div>
                                    <!--end::Company Sizes-->                     
                                    <!--begin::Company Type-->
                                    <div class="row mb-7">
                                       <!--begin::Title-->
                                       <label class="col-lg-4 fw-bold text-muted fs-6">Company Type</label>
                                       <!--end::Title-->
                                       <!--begin::Description-->
                                       <div class="col-lg-8">
                                          <span class="fw-bold fs-6 text-gray-800 fs-6 span_company_type">&nbsp;</span>
                                       </div>
                                       <!--end::Description-->
                                    </div>
                                    <!--end::Company Type-->                       
                                    <!--begin::Founded-->
                                    <div class="row mb-7">
                                       <!--begin::Title-->
                                       <label class="col-lg-4 fw-bold text-muted fs-6">Founded</label>
                                       <!--end::Title-->
                                       <!--begin::Description-->
                                       <div class="col-lg-8">
                                          <span class="fw-bold fs-6 text-gray-800 fs-6 span_founded">&nbsp;</span>
                                       </div>
                                       <!--end::Description-->
                                    </div>
                                    <!--end::Founded-->        
                                 </div>
                                 <!--end::Right Section-->
                              </div>
                              <!--end::Client Information content-->
                              <div class="separator separator-dashed mt-5 mb-10"></div>
                              <h6 class="font-weight-bold mb-3">Client Description:</h6>
                              <!--begin::Client Description content-->
                              <div class="row pt-7">
                                 <!--Client Description-->
                                 <p class="text-justify span_kt_docs_tinymce_hidden">&nbsp;
                                 </p>
                              </div>
                              <!--end::Client Description content-->
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
                                       <div class="col-lg-10">
                                          <span class="fw-bold text-gray-800 fs-6 span_tags">&nbsp;</span>
                                       </div>
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
                              <div class="row pt-7 divAddressDetail">&nbsp;</div>
                              <!--end::Address Details content-->
                              <div class="separator separator-dashed mt-5 mb-10"></div>
                              <!--begin::Contact Person content-->	
                              <h6 class="font-weight-bold mb-3">Contact Person:</h6>
                              <div class="row pt-7 div-contact-person">&nbsp;</div>
                           </div>
                           <!--end::Review and Submit-->
                           <!--begin::Wizard Actions-->
                           <div class="d-flex justify-content-center border-top mt-5 pt-10">
                              <div class="mr-2">
                                 <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Back</button>
                              </div>
                              <div>
                                 <?php 
                                    if($page_event == 'EDIT'){ 
                                    	echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 updatefrm" data-wizard-type="action-submit1" style="display:none">Update</button>';
                                    }else{
                                    	echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 submitfrm" data-wizard-type="action-submit1" style="display:none">Submit</button>';	
                                    }
                                    ?>
                                 <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Continue</button>
                              </div>
                           </div>
                           <!--end::Wizard Actions-->               
                        </form>
                        <!--end::Wizard Form-->
                     </div>
                  </div>
                  <!--end::Wizard Body-->
               </div>
               <!--end::Wizard-->
            </div>
            <!--end::Wizard-->
         </div>
         <!--Card-->
      </div>
      <!--end::Container-->
   </div>
   <!--end::Entry-->
</div>
<!--end::Content-->
@endsection
@push('footer-script')
<!-- <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script> -->
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<script>
   // The DOM element you wish to replace with Tagify
   var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input)
   		function isNumberKey(evt)
   		{
   			var charCode = (evt.which) ? evt.which : evt.keyCode
   			if (charCode > 31 && (charCode < 48 || charCode > 57))
   				return false;
   			return true;
   		}
   
           $('#save-form').click(function () 
   		{   		
   				$('.cp_name').each(function() {
   					$(this).rules("add", 
   					{
   							required: true,
   							messages: { required: "Name is required", }
   					});
   				});
   	
   			  if( $('#createForm').valid() == false)
   				{
   					return false;
   				}
   
               $.easyAjax({
               url			: '{{route('product_company.clients.store')}}',
               container	: '#createForm',
               type		   : "POST",
               redirect	   : true,
               file		   : true
               })
           });
</script>
<!--Client Description Script-->
<script>
   tinymce.init({
       selector: '#kt_docs_tinymce_basic'
   });
</script>
<!--Client Description Script End-->

<!--Tinymce Editor-->
<!-- <script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script> -->
<script src="{{asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js')}}"></script>
<!-- <script src="{{asset('demo/dist/assets/js/custom/modals/new-target.js')}}"></script> -->
<!--Tinymce Editor End-->
<!--end::Page Scripts-->
<!--Tooltip Script-->
<script>
   $(function ()
   {
     $('[data-toggle="tooltip"]').tooltip()
   })
   
   $.noConflict();
</script>
<script>
   var loadFile = function(event) {
   	var output = URL.createObjectURL(event.target.files[0]);
   	$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
   };
   
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
   			/*$('.client-address-container').append('<div class="row client-address-container-child" id="'+clientAddr_id+'"><div class="form-group"><label class="form-label required fw-bolder">Address:</label><input type="text" class="form-control form-control-lg form-control-solid" name="address" placeholder="Enter address" value="" id="'+address_id+'"><span class="form-text text-muted">Please enter your Address</span> </div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="form-label required fw-bolder">Country:</label><select name="country[]" id="'+country_id+'" class="form-control form-control-lg form-control-solid"><option value="">Select</option>'+countryListing+'</select><span class="form-text text-muted">Please select your country</span><div class="fv-plugins-message-container"></div></div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="form-label required fw-bolder">State:</label><select name="state[]" id="'+state_id+'" class="form-control form-control-lg form-control-solid"><option value="">Select</option></select><span class="form-text text-muted">Please select your state</span><div class="fv-plugins-message-container"></div></div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="form-label required fw-bolder">City</label><input type="text" id="'+city_id+'" class="form-control form-control-lg form-control-solid" name="city[]" placeholder="Enter city" value="Noida"><span class="form-text text-muted">Please enter your city:</span><div class="fv-plugins-message-container"></div></div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="form-label required fw-bolder">Postcode:</label><input type="text" id="'+postcode_id+'" class="form-control form-control-lg form-control-solid" name="postcode[]" placeholder="Enter postcode" value=""><span class="form-text text-muted">Please enter your postcode</span><div class="fv-plugins-message-container"></div></div></div></div>');	*/
   
   $('#addressLoader').show();
   $('#addressLoader').html('');
   	
   var baseurl    		= "{{ asset('assets/images/Loading2.gif') }}"; 	
   var cid				=	addressId ;
   var url 			=	"{{ route('product_company.clients.addresslist',':cid') }}";
   url 				=	url.replace(':cid', cid);
   $.ajax({
   			type	: 'GET',
   			url		: url,
   			 beforeSend: function() {
   			 							
                                     		$('#addressLoader').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       				},
   			success	: function (response) {																		
   												$('#addressLoader').hide();
   												$('.client-address-container').last().append(response.data);
   										  }		
   });		
   	
   	
   	
   	});
   	
   
   	$('.remove_address').click(function(){
   			$('.client-address-container').children().last().remove();
   	});
   	
   	
   		
   	$('.add_cperson').click(function (){
   			
   			var contactPersonId	=	$('#contactPersonId').val();
   			contactPersonId++;
   			
   			$('#contactPersonId').val(contactPersonId);  			
   			
   			$('.client-cperson-container').last().append('<div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="cp_name[]" id="cp_name_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter person name"><span class="form-text text-muted">Please enter contact person name</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="email" name="cp_email[]" id="cp_email_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter email"><span class="form-text text-muted">Please enter contact email</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="number" name="cp_number[]" id="cp_number_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter contact number"><span class="form-text text-muted">Please enter contact number</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="cp_desigination[]" id="cp_desigination_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter desigination"><span class="form-text text-muted">Please enter contact desigination</span></div></div></div></div>');});
   	
   	$('.remove_cperson').click(function(){
   			$('.client-cperson-container').children().last().remove();
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
   				
   					var url 			=	"{{ route('product_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {		
   																		$('#state_'+id).html(response.data);
   																		
   																   }		
   										   
   					});				
   								
   	});
   	
   	
   	$(document).on('click', '.btn', function() { 
   	
   		var state 	=	$('#kt_wizard').attr("data-wizard-state"); 
   		if(state == 'last')
   		{
   			$('.span_name').html('');
   			$('.span_website').html('');
   			$('.span_industry').html('');	
   			$('.span_company_sizes').html('');	
   			$('.span_company_type').html('');	
   			$('.span_founded').html('');
   			$('.span_kt_docs_tinymce_hidden').html('');
   			$('.span_tags').html('');
   			$('.span_tags').html('');
   			$('.divAddressDetail').html('');
   			$('.div-contact-person').html('');
   			
   				
   			var name			=	$('#client_id').val();
   			$('.span_name').html(name);			
   			var website			=	$('#website').val();
   			$('.span_website').html(website);			
   			var industry		=	$('#industry option:selected').text(); 
   			$('.span_industry').html(industry);			
   			var company_sizes	=	$('#company_sizes').val(); 
   			$('.span_company_sizes').html(company_sizes+' + Employees');			
   			var company_type	=	$('#company_type option:selected').text(); 
   			$('.span_company_type').html(company_type);			
   			var founded			=	$('#founded').val(); 
   			$('.span_founded').html(founded);
   			
   			
   			var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();      //$('#kt_docs_tinymce_hidden').tinyMCE().getContent() ;   //$('#kt_docs_tinymce_hidden').val(); 
   			$('.span_kt_docs_tinymce_hidden').html(kt_docs_tinymce_hidden);
   			
   			
   			
   			var tags					=	$('#tags').val(); 	
   			var json_tags 				= $.parseJSON(tags);
   			for(let len=0; len<json_tags.length; len++){
   					if(len==0){
   						$('.span_tags').append(json_tags[len].value);	
   					}else{
   						$('.span_tags').append('  , '+json_tags[len].value);	
   					}
   					
   			}
   			//$.each(json_tags,function(key,value){
   			//	alert(value);
   			//});
   			//alert(tags);		
   			//alert(json_tags[0].value);  
   			//$('.span_tags').html(tags);
   			
   			
   			
   			//Client Address			
   			var addressId				=	$('#addressId').val();
   			for(let cntAdd=0; cntAdd<=addressId; cntAdd++)
   			{
   					var address			=	$('#address_'+cntAdd).val();
   					var country			=	$('#country_'+cntAdd+' option:selected').text();      //$('#country_'+cntAdd).val();
   					var state			=	$('#state_'+cntAdd+' option:selected').text();      // $('#state_'+cntAdd).val();
   					var city			=	$('#city_'+cntAdd).val();
   					var postcode		=	$('#postcode_'+cntAdd).val();

                  if(country == '101')
                  {	
                     city			=	$('#city_id_'+cntAdd+' option:selected').text();      
                  }
   					
   					$('.divAddressDetail').append('<div class="row"><div class="row mb-4"><label class="col-lg-2 fw-bold text-muted fs-6">Address</label><div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6">'+address+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Country</label><span class="fw-bold text-gray-800 fs-6 w-100">'+country+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">State</label><span class="fw-bold text-gray-800 fs-6 w-100">'+state+'</span></div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">City</label><span class="fw-bold text-gray-800 fs-6 w-100">'+city+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Pincode</label><span class="fw-bold text-gray-800 fs-6 w-100">'+postcode+'</span> </div></div></div>');	
   					
   			}
   			
   			//Contact Person 
   			var contactPersonCounter	=	$('#contactPersonId').val();
   			for(let cnt=0; cnt<=contactPersonCounter; cnt++)
   			{
   				var cp_name			=	$('#cp_name_'+cnt).val();
   				var cp_email		=	$('#cp_email_'+cnt).val();
   				var cp_number		=	$('#cp_number_'+cnt).val();
   				var cp_desigination	=	$('#cp_desigination_'+cnt).val();
   				
if(cp_name != 'undefined')
{
		$('.div-contact-person').append('<div class="row"><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Person Name</label><span class="fw-bold text-gray-800 fs-6 w-100">'+cp_name+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Phone Number</label><span class="fw-bold text-gray-800 fs-6 w-100">'+cp_number+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Email</label><span class="fw-bold text-gray-800 fs-6 w-100">'+cp_email+'</span> </div></div><div class="col-xl-3"><div class="form-group fv-plugins-icon-container"><label class="fw-bold text-muted fs-6 w-100">Designation</label><span class="fw-bold text-gray-800 fs-6 w-100">'+cp_desigination+'</span> </div></div></div>')
}
				//alert(cp_name); 	
   			}   			
   			$('.submitfrm').show();
   			$('.updatefrm').show();
   		}
   		else
   		{
   			$('.submitfrm').hide();
   			$('.updatefrm').hide();
   		}
   		
   	});
   	 
   	
        
   });
</script>
<script>
   $(document).ready(function () {
   
       $('#kt_form').validate({ 
           						rules: {
               								name: {
                   										required: true,
                   										email: true
               										},
               								website: {
   														required: true,
   														minlength: 5
   													 }
           							}
       					});
   
   });   
   
   
   
   
   $(document).on('click', '.submitfrm', function() { 
   
   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();
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
       			url					: '{{route('product_company.clients.store')}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
       			success				: function (data)
                {
                  //alert(data.status); 
                  if(data.status == 'success')
                  {
                     window.location = '{{ route('product_company.clients.index') }}'
                     //{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/product_company\/clients"}	
                  }   										
       			}
       			//error: function (e) {
           				//error
       			//}
   			});
   
   });
   
       
</script>
<?php 
   if($page_event == 'EDIT')
   {
   ?>
<script>
   $(document).on('click', '.updatefrm', function() {
   	 
   	//$("#kt_form").validate(); 
   	
   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();
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
   				url					: '{{route('product_company.clients.update', @$client->id)}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				   : data,
   				redirect			   : true,
       			success				: function (data)
               {
                  if(data.status == 'success')
                  {
                     window.location = '{{ route('product_company.clients.index') }}'	
                  }						
       			}
   			});
   
   });
</script>
<?php 
   }
   ?>
   
   
<script>

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
				
   					var url 			=	"{{ route('product_company.clients.citylist',':state_id') }}";
   					url 				=	url.replace(':state_id', state_id);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {		
   																		$('#city_id_'+ccountryid).html(response.data);
   																   }		
   										   
   					});				
   								
   	});
	
</script>   

<!--begin::Page Scripts-->
<!-- <script src="{{ asset('demo/v7/js/prismjs.bundle.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<!--begin::Validation-->
<script src="{{ asset('demo/v7/js/add-clients.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::Validation-->

<!--begin::Image input-->
<script src="{{ asset('demo/v7/js/pages/crud/file-upload/image-input.js') }}"></script>
<!--end::Image input-->

@endpush