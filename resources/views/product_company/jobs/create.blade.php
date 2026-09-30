@extends('layouts.product_company_layout')
@push('head-script')
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/sass/vendors/plugins/_select2.scss') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<script src="{{asset('demo/src/js/vendors/plugins/select2.init.js')}}"></script>
<script>
$(document).ready(function()
{
   $('#client_id').select2();
});

</script>
<style>
.form-label
{
   margin-bottom: 0.5rem!important;
   font-size: 1.05rem!important;
   font-weight: 500!important;
   color: #3f4254!important;
}
body
{
   overflow-x:hidden;
}
/*Css for Job Description*/
.tox .tox-toolbar__group
{
   padding: 0px!important;
}
.tox .tox-toolbar-overlord
{
   display: inline-flex;
}
.tox .tox-tbtn--select
{
   margin: 0px!important;
   padding: 0px!important;
   width: 127px!important;
}
.tox:not(.tox-tinymce-inline) .tox-editor-header
{
   border-bottom: 1px solid lightgray;
}
.tox .tox-dialog--width-lg
{
   height: 650px;
   max-width: 920px!important;
}
/*Css for Job Description End*/

/*Custom Css*/
.last-input-border-radius
{
   border-radius: 5px 0px 0px 5px!important;
}
/*Custom Css End*/

/*Requisition Type CSS*/
.option
{
   cursor: pointer;
}
/*Requisition Type CSS End*/

/*Multi dropdown css*/
.select2-container--default .select2-selection--multiple
{
   background-color: #f5f8fa!important;
   border: 1px solid #f5f8fa!important;
   height: 44px;
   line-height: 44px;
   display: flex!important;
   align-items: center;
   justify-content: left;
   border-radius: 5px!important;
   overflow-y: scroll;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove
{
   border-right: 1px solid #e7e8e8!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove
{
   margin-right: 0.2rem!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice
{
   background: #e7e8e8!important;
}
.select2-container--default .select2-selection--multiple
{
   padding: 0px 5px!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:before
{
   content: "\2613"!important;
   font-size: 1rem!important;
}
.select2-container--bootstrap5 .select2-selection--multiple:not(.form-select-sm):not(.form-select-lg)
{
  height: 5.7vh;
}

#end_date::-webkit-inner-spin-button,
#end_date::-webkit-calendar-picker-indicator {
    display: none;
    -webkit-appearance: none;
}

@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesRecruiter {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesRecruiter {width: 1200px;}
}

/*Multi dropdown css*/
.br-currency-select2-dropdown {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden;
    z-index: 1060;
}
.br-currency-select2-dropdown .select2-search--dropdown {
    padding: 8px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13px;
    outline: none;
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff;
    width: 100%;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
.br-currency-select2-dropdown .select2-results__options {
    max-height: 260px;
}
.br-currency-select2-dropdown .select2-results__option {
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    line-height: 1.4;
}
.br-currency-select2-dropdown .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
}
.br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}
</style>
@endpush
@section('content')
<?php 
   $pagetitle		=	$end_date	=	$jobtags	=	$recruiterData	= $cpersonData	= $carriew_website_chk =  '';
   $job_id			=	0;
   if($page_event == 'ADD')
   {
   		$pagetitle	=	'Add Requisition';	
   }
   else if($page_event == 'EDIT')
   { 
   		$pagetitle	=	'Edit Requisition';
   $end_date	=	@$job->end_date;
   
   $date 		=	strtotime($end_date);
    	$new_date   =	date('Y-m-d', $date);
   //$new_date   =	$end_date;
   
   
   $cnt		=	0;
   foreach($job_metronics_skills as $jobSkills)
   {
   if($cnt == 0){
   	$jobtags	=	$jobSkills->skill_name;	
   }else{
   	$jobtags	=	$jobtags.' , '.$jobSkills->skill_name;	
   }
   $cnt++;
   }
   $job_id			=	@$job->id;
   
   $rec_inc		=	0;
   foreach($recruiterlist as $recData)
   {
   if($rec_inc == 0){
   	$recruiterData	=	$recData->name;	
   }else{
   	$recruiterData	=	$recruiterData.' , '.$recData->name;	
   }
   $rec_inc++;
   }
   
   $cp_inc		=	0;
   foreach($contactPersonList as $cpData)
   {
   if($cp_inc == 0){
   	$cpersonData	=	$cpData->contact_person_name;	
   }else{
   	$cpersonData	=	$cpersonData.' , '.$cpData->contact_person_name;	
   }
   $cp_inc++;
   }
   
   
   if(@$job->carrier_website == 1)
   {
   $carriew_website_chk =  'checked="checked"';	
   }
   }	
   
   //echo '<pre>'; print_r($contactPersonList); exit;
   ?>
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1"><?php echo @$pagetitle; ?></a></h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter requisition details and submit</span> </div>
  </div>
</div>
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-0" id="kt_content">
  <div class="d-flex flex-column-fluid">
    <div class="container-fluid px-3">
      <div class="card card-custom gutter-b">
        <div class="card-body p-0">
          <div class="wizard wizard-1" id="kt_contact_add" data-wizard-state="step-first" data-wizard-clickable="true">
            <div class="wizard-nav border-bottom">
              <div class="wizard-steps p-8 p-lg-10">
                <!--begin::basic details-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span>
                    <h3 class="wizard-title">1. Basic Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                      </g>
                    </svg>
                    </span>
                </div>
                <!--end::basic details-->
                <!--begin::job description-->
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
                    <h3 class="wizard-title">2. Job Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  </span>
                </div>
                <!--end::job description-->
                <!--begin::custom details-->
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
                    <h3 class="wizard-title">3. Custom Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24" />
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                    </g>
                  </svg>
                  </span>
                </div>
                <!--end::custom details-->
                <!--begin::review & submit-->
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
                <!--end::review & submit-->
            </div>
            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
              <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_contact_add_form" name="kt_contact_add_form">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <!--begin::Basic Details-->
                <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                  <h3 class="mb-10 font-weight-bold text-dark">Enter Your Basic Details</h3>
                  <div class="flex-column">
                    <!--begin::Client | Job Locations | Position Type-->
                    <div class="form-group row fv-row mb-10">
                      <?php 
                      if($global_company_type  == 3)
                      {
                        //echo '<pre>'; print_r($client[0]); exit;                          
                      ?>
                        <div class="col-lg-4">
                          <label class="form-label">Company</label>
                          <input type="hidden" class="" name="client_id" id="client_id" value="<?php echo @$client[0]->id; ?>" placeholder="Enter product company Name">
                          <input type="text" class="form-control form-control-lg form-control-solid" name="" id="" value="<?php echo @$client[0]->name; ?>" placeholder="Enter product company Name" readonly="">
                          <span class="form-text text-muted">Please enter product company name</span>
                        </div>
                      <?php 
                      }
                      else
                      {
                      ?>
                      <div class="col-lg-4">
                        <label class="form-labe required">Client:</label>
                        <select class="form-select form-select-lg form-select-solid client_id" data-control="select2" data-placeholder="Select an option" data-allow-clear="true"  id="client_id" name="client_id" >
                          <option value="" >Select Client</option>
                          <?php 
                            foreach($client as $clientarr)   
                            {
                              $client_id		=	$sel	=	'';
                              $client_id		=	@$job->client_id;
                              if($client_id == $clientarr->id )
                              {
                                $sel		=	'selected="selected"' ;	
                              }
                              echo '<option value="'.@$clientarr->id.'"   '.$sel.'  >'.ucfirst($clientarr->name).'</option>';
                            }
                            ?>
                        </select>
                        <span class="form-text text-muted">Please select your client</span> </div>
                      <?php 
                            }
                          ?>
						  
						  
                    <div class="col-lg-4">
                      <label for="" class="form-label">Job Address:</label>
                      <select class="form-select form-select-solid select2 select2-hidden-accessible d-block w-100 kt_select2_3" id="kt_select2_3" name="kt_select2_3[]" data-select2-id="kt_select2_3" tabindex="-1" aria-hidden="true" data-control="select2" data-placeholder="Select Job Address" data-allow-clear="true" multiple="multiple">
                        <option value="">Select Job Address</option>
                      </select>
                      <span class="form-text text-muted">Please select your job Address</span>
                    </div>
                    

                    <div class="col-lg-4">
                      <label for="" class="form-label">Work City for candidate:</label>
                      <select class="form-control select2" id="kt_select2_2" name="kt_select2_2[]" multiple="multiple">
                            <optgroup label="Select Work Location for candidate">
                               <?php 
                               
                               foreach($jobCity as $jobCityval){
                                $sel = '';
                                if(in_array($jobCityval->city_id,$jobCitySelected)){
                                  $sel =	"selected";	
                                }
                               echo '<option value="'.$jobCityval->city_id.'" '.$sel.'>'.ucfirst($jobCityval->city).'</option>';
                                }
                             
                              ?>

                            </optgroup>
                          </select>
                      <span class="form-text text-muted">Please select your job Address</span>
                    </div>

                    <div class="col-lg-4">
                          <label class="form-label required">Remote:</label>
                          <select name="work_from_home" id="work_from_home" class="form-select form-select-solid" aria-label="Select work from home">
                            <option value="no" <?php if(@$job->work_type=='no'){ echo 'selected'; } ?> >No Remote</option>
                            <option value="yes" <?php if(@$job->work_type=='yes'){ echo 'selected'; } ?> >Only Remote</option>
                            <option value="any" <?php if(@$job->work_type=='any'){ echo 'selected'; } ?> >Any Remote/Onsite Location</option>
                          </select>
                          <span class="form-text text-muted">Please select work from home</span> 
                        </div>
                        <div class="col-lg-4">
                          <label class="form-label required">Show client name to vendors:</label>
                          <select name="show_client_name" id="show_client_name" class="form-select form-select-solid" aria-label="Select client name to vendors">
                            <option value="">Select Show client name to vendors</option>
                            <option value="no" <?php if(@$job->show_vendor=='no'){ echo 'selected'; } ?> >No</option>
                            <!-- default value -->
                            <option value="yes" <?php if(@$job->show_vendor=='yes'){ echo 'selected'; } ?>>Yes</option>
                          </select>
                          <span class="form-text text-muted">Please select show client name to vendors</span> 
                        </div>


                      <div class="col-lg-4">
                        <label class="form-label">Position Type:</label>
                        <select class="form-select form-select-solid" aria-label="Select position type" name="employment_type" id="employment_type" >
                          <?php 
                              foreach($employmentType as $key => $data_value)
                              {
                              $employment_type		=	$sel	=	'';
                              $employment_type		=	@$job->employment_type;
                              if($employment_type == $key )
                              {
                              $sel		=	'selected="selected"' ;	
                              }
                              
                              echo '<option value="'.$key.'"   '.@$sel.'>'.$data_value.'</option>'; 
                              }
                              ?>
                        </select>
                        <span class="form-text text-muted">Please select your position type</span> </div>
                    </div>
                    <!--end::Client | Job Locations | Position Type-->
                    <!--begin::Job Title | Total Openings | Job Department-->
                    <div class="form-group row fv-row mb-10">
                      <div class="col-lg-4">
                        <label class="form-label required">Job Title:</label>
                        <div class="input-group input-group-lg input-group-solid">
                          <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-user"></i> </span> </div>
                          <input type="text" class="form-control form-control-lg form-control-solid" name="title" id="title" value="<?php echo @$job->title; ?>" placeholder="Enter job title">
                        </div>
                        <span class="form-text text-muted">Please enter your job title</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Total Openings:</label>
                        <div class="input-group input-group-lg input-group-solid">
                          <div class="input-group-prepend"> <span class="input-group-text"> <i class="la la-briefcase"></i> </span> </div>
                          <input type="number" class="form-control form-control-lg form-control-solid" name="total_positions" id="total_positions" value="<?php echo @$job->total_positions; ?>" placeholder="Enter total openings">
                        </div>
                        <span class="form-text text-muted">Please enter your total openings</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Job Department:</label>
                        <select class="form-select form-select-solid"  name="category_id" id="category_id">
                          <option value="">Select Job Department</option>
                          <?php 
                              foreach($categories as $category)
                              {
                              $category_id		=	$sel	=	'';
                              $category_id		=	@$job->category_id;
                              if($category_id == @$category->id )
                              {
                              $sel		=	'selected="selected"' ;	
                              }
                              echo '<option value="'.@$category->id.'"  '.$sel.'>'.ucfirst($category->name).'</option>';
                              }	
                              ?>
                        </select>
                        <span class="form-text text-muted">Please select your job department</span> </div>
                    </div>
                    <!--end::Job Title | Total Openings | Job Department-->
                    <!--begin::Minimum Experience | Maximum Experience | Qualifaction-->
                    <div class="form-group row fv-row mb-10">
                      <div class="col-lg-4">
                        <label class="form-label required">Minimum Experience:</label>
                        <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                          <option value=""  disabled selected >Select Experience</option>
                          <option value="0">Freshers</option>
                          <?php 
                              $yearText = "Year";
                              for($i=1; $i<=30; $i++)
                              {
                              if($i > 9)
                              {
                                    $yearText = "Years";
                              }
                              
                                $min_exp		=	$sel	=	'';
                                $min_exp		=	@$job->min_exp;
                                if($min_exp == @$i )
                                {
                                  $sel		=	'selected="selected"' ;	
                                }
                                
                              echo '<option value="'.$i.'"   '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                              }
                              ?>
                        </select>
                        <span class="form-text text-muted">Please enter minimum experience</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Maximum Experience:</label>
                        <select class="form-select form-select-solid" name="max_exp" id="max_exp" >
                          <option value=""  disabled selected >Select Experience</option>
                          <option value="0">Freshers</option>
                          <?php 
                              $yearText = "Year";
                              for($i=1; $i<=30; $i++)
                              {
                                if($i > 9)
                                {
                                  $yearText = "Years";
                                }			
                                $max_exp		=	$sel	=	'';
                                $max_exp		=	@$job->max_exp;
                                if($max_exp == @$i )
                                {
                                  $sel		=	'selected="selected"' ;	
                                }		
                              echo '<option value="'.$i.'"  '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                              }
                              ?>
                        </select>
                        <span class="form-text text-muted">Please enter maximum experience</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label">Qualifaction:</label>
                        <select class="form-select form-select-lg form-select-solid"  name="qualification" id="qualification">
                          <?php 
                              foreach($qualification as $data_value)
                              {
                              $qualification		=	$sel	=	'';
                              $qualification		=	@$job->qualification;
                              if($qualification == @$data_value->id )
                              {
                              $sel		=	'selected="selected"' ;	
                              }			
                              echo '<option value="'.@$data_value->id.'"  '.$sel.'>'.@$data_value->name.'</option>';
                              } 
                              ?>
                        </select>
                        <span class="form-text text-muted">Please select your qualifaction</span> </div>
                    </div>
                    <!--end::Minimum Experience | Maximum Experience | Qualifaction-->
                    <!--begin::Salary Range | Due Date-->
                    <div class="form-group row fv-row mb-10">
                      <div class="col-lg-8 d-none">
                        <label class="form-label required">Salary Range</label>
                        <div class="row">
                          <div class="">
                            <select class="form-select form-select-solid" arial-label="Select Currency" name="salary_currency" id="salary_currency">
                              <?php 
                                foreach($currency as $key=>$val)
                                {
                                  $min_salary_currency = $sel = "";
                                  $min_salary_currency = @$job->min_salary_currency;
                                  if($min_salary_currency == @$key )
                                  {
                                    $sel = "selected=\"selected\"";	
                                  }
                                  $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                  echo "<option value=\"" . $key . "\" data-code=\"" . e($val) . "\" " . $sel . ">" . e($cMeta["label"]) . "</option>";	
                                }
                                ?>
                            </select>
                          </div>
                          <div class="">
                            <input type="number" class="form-control form-control-solid" name="min_salary" id="min_salary" placeholder="From" value="<?php echo @$job->min_salary; ?>">
                          </div>
                          <div class="">
                            <input type="number" class="form-control form-control-solid" name="exp_ctc" id="exp_ctc" placeholder="To" value="">
                          </div>
                          <div class="">
                            <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="salary_time_mode" id="salary_time_mode">
                              <?php 
                                  foreach($salaryMode as $key=>$val)
                                  {
                                    echo '<option value="'.$key.'">'.$val.'</option>';	
                                  }
                                  ?>
                            </select>
                          </div>
                        </div>
                        <span class="form-text text-muted">Please enter your salary range</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Minimum Salary</label>
                        <div class="row">
                          <div class="col-4 px-1">
                            <select class="form-select form-select-solid select2-job-currency" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency" data-control="select2" data-placeholder="Search currency...">
                              <?php 
                                foreach($currency as $key=>$val)
                                {
                                  $min_salary_currency = $sel = "";
                                  $min_salary_currency = @$job->min_salary_currency;
                                  if($min_salary_currency == @$key )
                                  {
                                    $sel = "selected=\"selected\"";	
                                  }
                                  $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                  $short = !empty($cMeta["symbol"]) ? $cMeta["code"] . " (" . $cMeta["symbol"] . ")" : $cMeta["code"];
                                  echo "<option value=\"" . $key . "\" data-code=\"" . e($val) . "\" data-short=\"" . e($short) . "\" data-search=\"" . e($cMeta["keywords"]) . "\" " . $sel . ">" . e($cMeta["label"]) . "</option>";	
                                }
                                ?>
                            </select>
                          </div>
                          <div class="col-4 px-0">
                            <input type="number" class="form-control form-control-lg form-control-solid" name="minimum_salary" id="minimum_salary" placeholder="Enter min salary" value="<?php echo @$job->min_salary; ?>">
                          </div>
                          <div class="col-4 px-1">
                            <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="min_sal_mode" id="min_sal_mode">
                              <?php 
                                foreach($salaryMode as $key=>$val)
                                {
                                  $min_sal_mode		=	$sel	=	'';
                                  $min_sal_mode		=	@$job->min_sal_mode;
                                  if($min_sal_mode == @$key )
                                  {
                                    $sel		=	'selected="selected"' ;	
                                  }
                                  echo '<option value="'.$key.'"   '.$sel.'>'.$val.'</option>';	
                                }
                                ?>
                            </select>
                          </div>
                        </div>
                        <span class="form-text text-muted">Please enter your minimum salary</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Maximum Salary</label>
                        <div class="row">
                          <div class="col-4 px-1">
                            <select class="form-select form-select-solid select2-job-currency" arial-label="Select Currency" name="max_salary_currency" id="max_salary_currency" data-control="select2" data-placeholder="Search currency...">
                              <?php 
                                foreach($currency as $key=>$val)
                                {
                                  $max_salary_currency = $sel = "";
                                  $max_salary_currency = @$job->max_salary_currency;
                                  if($max_salary_currency == @$key )
                                  {
                                    $sel = "selected=\"selected\"";	
                                  }
                                  $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                  $short = !empty($cMeta["symbol"]) ? $cMeta["code"] . " (" . $cMeta["symbol"] . ")" : $cMeta["code"];
                                  echo "<option value=\"" . $key . "\" data-code=\"" . e($val) . "\" data-short=\"" . e($short) . "\" data-search=\"" . e($cMeta["keywords"]) . "\" " . $sel . ">" . e($cMeta["label"]) . "</option>";	
                                }
                                ?>
                            </select>
                          </div>
                          <div class="col-4 px-0">
                            <input type="number" class="form-control form-control-lg form-control-solid" name="maximum_salary" id="maximum_salary" placeholder="Enter max salary" value="<?php echo @$job->max_salary; ?>">
                          </div>
                          <div class="col-4 px-1">
                            <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="max_sal_mode" id="max_sal_mode">
                              <?php 
                                  foreach($salaryMode as $key=>$val)
                                  {
                                    $max_sal_mode		=	$sel	=	'';
                                    $max_sal_mode		=	@$job->max_sal_mode; 
                                    if($max_sal_mode == @$key )
                                    {
                                      $sel		=	'selected="selected"' ;	
                                    }	
                                    echo '<option value="'.$key.'"   '.$sel.'>'.$val.'</option>';	
                                  }
                                  ?>
                            </select>
                          </div>
                        </div>
                        <span class="form-text text-muted">Please enter your maximum salary</span> </div>
                      <div class="col-lg-4">
                        <label class="form-label required">Due Date:</label>
                        <input type="date" class="form-control form-control-solid"  name="end_date" id="end_date" placeholder="Enter due date" value="<?php echo @$new_date; ?>" onclick="this.showPicker()">
                        <span class="form-text text-muted">Please enter your due date</span> </div>
                      <div class="col-lg-4 d-none">
                        <label class="form-label required">Priority:</label>
                        <select class="form-select form-select-solid" aria-label="Select priority">
                          <option>Select Priority</option>
                          <option value="1">Very Urgent</option>
                          <option value="2">Urgent</option>
                        </select>  
                        <select class="form-control select2 m-b-10" name="priority" id="priority" >
                          <?php 
                              foreach($priority as $key => $data_value)
                              {
                                echo '<option value="'.@$key.'">'.$data_value.'</option>';
                              
                              }
                              ?>
                        </select>
                        <span class="form-text text-muted">Please select your priority</span> </div>
                    </div>
                    <!--end::Salary Range | Due Date-->
                    <!--begin::Start Date | Due Date | Status-->
                    <div class="form-group row fv-row mb-10">
                      <!--<div class="col-lg-4">
                              <label class="form-label required">Start Date:</label>
                              <input type="date" class="form-control form-control-solid" name="date-start" name="date-start" placeholder="Enter start date" value="{{ \Carbon\Carbon::now(company()->
                      timezone)->format('Y-m-d') }}"><span class="form-text text-muted">Please enter your start date</span> </div>
                    -->
                    <div class="col-lg-4 d-none">
                      <label class="form-label required">Status:</label>
                      <select name="status" id="status" class="form-select form-select-solid" aria-label="Select status">
                        <option>Select Status</option>
                        <option value="active">@lang('app.active')</option>
                        <option value="hold">Hold</option>
                        <option value="inactive">@lang('app.inactive')</option>
                      </select>
                      <span class="form-text text-muted">Please select your status</span> </div>
                  </div>
                  <!--end::Start Date | Due Date | Status-->
                  <!--begin::Contacts Person/Hiring Manager | Recruiter-->
                  <div class="form-group row fv-row mb-10">
                    <div class="col-lg-6">
                      <label class="form-label required">Contacts Person/Hiring Manager:</label>
                      <input class="form-control form-control-solid hiring_namager" name="hiring_namager" id="hiring_namager" value="<?php echo @$cpersonData; ?>" readonly=""  placeholder="Enter Contacts person & Hiring manager"/>
                      <span class="form-text text-muted">Please select your contacts person/hiring manager</span> </div>
                    <div class="col-lg-6">
                      <label class="form-label required">Recruiter:</label>
                      <input class="form-control form-control-solid" name="recruiter" id="recruiter" value="<?php echo @$recruiterData; ?>" readonly="" placeholder="Enter Recruiter">
                      <span class="form-text text-muted">Please select your recruiter</span>
                     </div>

                     <div class="col-lg-6">
                          <label class="form-label">Country allow:</label>
                          <select class="form-control select2" id="kt_select2_4" name="kt_select2_4[]" multiple="multiple">
                            <optgroup label="Select Country for Vendor">
                               <?php 
                               
                               foreach($countries as $val){
                                $sel = '';
                                if(in_array($val->id,$valSelected)){
                                  $sel =	"selected";	
                                }
                               echo '<option value="'.$val->id.'" '.$sel.'>'.ucfirst($val->country_name).'</option>';
                                }
                             
                              ?>

                            </optgroup>
                          </select>
                          <span class="form-text text-muted">Please select your country</span> 
                        </div>

                  </div>
                  
                  <!--end::Contacts Person/Hiring Manager | Recruiter-->
                </div>
                </div>
                <!--end::Basic Details-->
                <!--begin::Job Description-->
                <div class="pb-5" data-wizard-type="step-content">
                  <div class="row">
                    <div class="col-xl-12">
                      <div class="form-group row">
                        <div class="col-lg-9 col-xl-6">
                          <h3 class="font-weight-bold text-dark">Enter Job Description</h3>
                        </div>
                      </div>
                      <!--begin::Job Description-->
                      <div class="form-group row fv-row mb-10">
                        <div class="col-lg-12">
                          <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$job->job_description; ?></textarea>
                        </div>
                      </div>
                      <!--end::Job Description-->
                      <!--begin::Skills-->
                      <div class="form-group row fv-row mb-10">
                        <div class="col-lg-12">
                          <label class="form-label">Skills:</label>
                          <input class="form-control form-control-solid" value="<?php echo @$jobtags; ?>" id="tags" name="tags"/>
                          <span class="form-text text-muted">Please select your skills</span> </div>
                      </div>
                      <!--end::Skills-->
                    </div>
                  </div>
                </div>
                <!--end::Job Description-->
                <!--begin::Custom Details-->
                <div class="pb-5" data-wizard-type="step-content">
                  <h3 class="mb-10 font-weight-bold text-dark">Enter Custom Details</h3>
                  <div class="form-group">
                    <div class="col-sm-12 px-0">
                      <div class="form-group mb-0" id="addmore"> </div>
                      <?php 
                        $customTitleId = 0;
                        if($page_event == 'EDIT')
                        {
                          $html		=	'';
                        $inc_first	=	0;
                        $inc_last	=	1;
                        foreach($additionalDetail as $aDetailArr)
                        {
                        if($inc_first %3 == 0)
                        {
                        $html	.=	'<div class="row custom-detail-container">
                        <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                        <div class="input-group">';	
                        }	
                        $html		.=	'<input type="text" name="custom_title[]" id="custom_title_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title" value="'.$aDetailArr->detail_value.'">';
                        
                        if(($inc_last %3 == 0)&& $inc_last > 0)
                        {
                                $html		.='</div>
                            </div>
                        </div>';
                        
                        }
                        
                        $inc_first++;
                        $inc_last++;
                        $customTitleId++;	
                        }
                        echo $html;                                       
                        //echo '<pre>'; print_r($additionalDetail); 
                                    
                            /*foreach($client_contact_arr as $ccArr)
                            {
                            echo '<div class="row custom-detail-container">	
                            <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">     
                              <div class="input-group">
                        
                        <input type="text" name="cp_name[]" id="cp_name_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_name.'" placeholder="Enter person name">
                        <input type="email" name="cp_email[]" id="cp_email_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_email.'" placeholder="Enter email">
                        <input type="number" name="cp_number[]" id="cp_number_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded me-5" value="'.$ccArr->contact_person_number.'" placeholder="Enter contact number">
                        <input type="text" name="cp_desigination[]" id="cp_desigination_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded" value="'.$ccArr->contact_person_designation.'" placeholder="Enter desigination">
                              </div>
                              </div>
                        </div>';	
                            $customTitleId++;	
                          }*/
                            $customTitleId--;
                        }
                        else 
                        {
                        ?>
                      <div class="row custom-detail-container">
                        <div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove">
                          <div class="input-group">
                            <input type="text" name="custom_title[]" id="custom_title_<?php echo $customTitleId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title">
                            <input type="text" name="custom_title[]" id="custom_title_<?php echo $customTitleId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title">
                            <input type="text" name="custom_title[]" id="custom_title_<?php echo $customTitleId;?>" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title">
                          </div>
                        </div>
                      </div>
                      <?php 
                        }
                        ?>
                      <input type="hidden" name="customTitleId" id="customTitleId" value="<?php echo @$customTitleId; ?>"  />
                      <div class="d-flex justify-content-center py-5 my-5">
                        <button type="button" class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_custom_title">
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
                <div class="pb-15" data-wizard-type="step-content">
                  <h4 class="mb-10 font-weight-bold">Review your Details and Submit</h4>
                  <h6 class="font-weight-bold mb-3">Requisition Details:</h6>
                  <div class="row pt-7">
                    <div class="col-lg-6">
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Ciient</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_client_name"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Job Title</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_jobtitle"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Minimum Experience</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_min_exp"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Maximum Experience</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_max_exp"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Minimum Salary</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_min_sal"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Maximum Salary</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_max_sal"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Qualification</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_qualification"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Contact Person</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_contact_person"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Recruiter</label>
                        <div class="col-lg-8"> <span class="fw-bold text-gray-800 fs-6 span_recruiter"></span> </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6 ">Job Locations</label>
                        <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_location"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Position Type</label>
                        <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_job_type"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Total Openings</label>
                        <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_openings"></span> </div>
                      </div>
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Job Department</label>
                        <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6 span_department"></span> </div>
                      </div>
                      <!-- <div class="row mb-7">
                                  <label class="col-lg-4 fw-bold text-muted fs-6">Start Date</label>
                                  <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 ">09-Sep-2021</span> </div>
                              </div>-->
                      <div class="row mb-7">
                        <label class="col-lg-4 fw-bold text-muted fs-6">Due Date</label>
                        <div class="col-lg-8"> <span class="fw-bold fs-6 text-gray-800 fs-6 span_due_date"></span> </div>
                      </div>
                    </div>
                  </div>
                  <div class="separator separator-dashed mt-5 mb-10"></div>
                  <h6 class="font-weight-bold mb-3">Job Description:</h6>
                  <div class="row pt-7 span_description"></div>
                  <div class="row pt-7">
                    <div class="col-lg-12">
                      <div class="row mb-7">
                        <label class="col-lg-2 fw-bold text-muted fs-6">Skills</label>
                        <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6 span_skills"></span> </div>
                      </div>
                    </div>
                  </div>
                  <div class="row pt-7">
                    <div class="col-lg-12">
                      <div class="row mb-7">
                        <label class="col-lg-2 fw-bold text-muted fs-6">Custom Details</label>
                        <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6 span_custom_question_title"></span> </div>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="separator separator-dashed mt-5 mb-10"></div>                              
                              <h6 class="font-weight-bold mb-3">Custom Details:</h6>
                              <div class="row pt-7">                                
                                <div class="col-lg-12">                                  
                                    <div class="row mb-7">                                      
                                      <label class="col-lg-2 fw-bold text-muted fs-6">Custom Title</label>                                     
                                      <div class="col-lg-10"> <span class="fw-bold text-gray-800 fs-6 span_custom_question_title">aaaaaaaaaaaaaaaa</span> </div>                                       
                                    </div>                                    
                                </div>                                
                            </div>-->
                  <div class="separator separator-dashed mt-5 mb-10"></div>
                  <div class="col-lg-9 fv-row">
                    <div class="d-flex align-items-center mt-3">
                      <label class="form-check form-check-inline form-check-solid me-5 cursor-pointer">
                      <input class="form-check-input" name="carrier_website" type="checkbox" <?php echo @$carriew_website_chk; ?>  value="1">
                      <span class="fw-bold ps-2 fs-6">Post this Job on Career Site.</span> </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-center border-top pt-10">
                  <div class="mr-2">
                    <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Back</button>
                  </div>
                  <div>				  
                  <?php 
                      if($page_event == 'EDIT'){ 
                        echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 updatefrm" data-wizard-type="action-submit1" style="display:none;">Update</button>';
                      }else{
                        echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 submitfrm" data-wizard-type="action-submit1" style="display:none;">Submit</button>';	
                      }
                  ?>
                  <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Continue</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
            <div class="symbol-label border-radius-5" id="light_client_caption">&nbsp;</div>
            </a> </div>
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_client_name">&nbsp;</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClient_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>
<div id="kt_activitiesRecruiter_toggle"></div>
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
            <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
            </a> </div>
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">&nbsp;</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
  </div>
</div>
@endsection
@push('footer-script')
<script>
   tinymce.init({
       selector: '#kt_docs_tinymce_basic'
   });  
</script>
<!--Tinymce Editor-->
<script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script>
<script src="{{asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js')}}"></script>
<script>
   function opensidebar()
   {
   		$('#kt_activitiesClient_toggle').trigger('click');
   }    	
</script>
<!--Tinymce Editor End-->
<!--begin::Page Scripts-->
<!-- <script src="{{ asset('demo/v7/js/prismjs.bundle.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/add-requisition.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<!--<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>-->
<!--<script src="{{ asset('demo/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>-->

<script src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/js/pages/crud/forms/widgets/bootstrap-select.js?v=7.2.9"></script>

<script>
$(document).ready(function() {
	
		
  $(document).on("change","#maximum_salary",function(){
      salary_check()
    });
    $(document).on("change","#minimum_salary",function(){
      salary_check()
    });

    $(document).on("change","#min_exp",function(){
      experience_check()
    });
    $(document).on("change","#max_exp",function(){
      experience_check()
    });

   	 $('#client_id').change(function () {
      
   				var id       	=   $(this).val();
   				var value    	=   $("#client_id option:selected"); 
   				var text_value 	=	value.text();
           var jobLocation = "{{ implode(',',$jobLocation) }}";
   				$('#kt_select2_3').html('');    // Location
   				var url    		= "{{route('product_company.jobs.getContactPerson', ':id')}}?job_id={{$job_id}}&&jobLocation={{ implode(',',$jobLocation) }}";
   				url      		= url.replace(':id', id);
   				$.ajax({
   								url    		: url,
   								success  	: function (response) 
   								{
   									//alert(response.locArr); 
   									//$('#contact_person').html(response.ccArr);
   									$('#kt_select2_3').html(response.locArr);     // Location
   								}
   							})
   		}).change();
   
   
   
});  

function salary_check(){
    var minimum_salary = parseInt($("#minimum_salary").val());
      var maximum_salary = parseInt($("#maximum_salary").val());
      if(minimum_salary != ''){
        if(minimum_salary > maximum_salary){
          Swal.fire({
            title: 'Warning',
            text: 'Enter the maximum salary greater than the minimum salary',
            icon: 'warning',
            allowOutsideClick: false,
          });
          $("#maximum_salary").val('');
        }
      }
   }

   function experience_check(){
    var min_exp = parseInt($("#min_exp").val());
      var max_exp = parseInt($("#max_exp").val());
      if(min_exp != ''){
        if(min_exp > max_exp){
          Swal.fire({
            title: 'Warning',
            text: 'Select the maximum experience greater than the minimum experience',
            icon: 'warning',
            allowOutsideClick: false,
          });
          $("#max_exp").val('');
        }
      }
   }


</script>

<script>

$(document).ready(function(){
	
	

		$( "#hiring_namager" ).on( "click", function() {
   		  
   		  		var client_id 	=	$('#client_id').val();				
   				  				
   	if(client_id > 0)
   	{
   		
   			$('#kt_activitiesClient_toggle').trigger('click');   						
   			
   			var clientName    	=   $("#client_id option:selected"); 
   			var text_value 		=	clientName.text();
   			$('#light_client_name').html(text_value); 
   			
   
   		var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';						
   		str					=	text_value;
   		str					=	str.substring(0, 1);
   		str	= strVal		=	str.toLowerCase();
   		var str_bg			=	str.charCodeAt(0);
   		
   		if(str_bg >=97 && str_bg <=101){
   			bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
   		}else if(str_bg >=102 && str_bg <=106){
   			bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
   		}else if(str_bg >=107 && str_bg <=111){
   			bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
   		}else if(str_bg >=112 && str_bg <=116){
   			bgTextDecore		=	'fs-3 bg-light-success text-success';	
   		}else if(str_bg >=117 && str_bg <=121){
   			bgTextDecore		=	'fs-3 bg-dark text-light';	
   		}else {
   			bgTextDecore		=	'fs-3 bg-light text-dark';	
   		}											
   		$('#light_client_caption').html(strVal.toUpperCase()); 		
   		$('#light_client_caption').addClass(bgTextDecore); 						
   			
   			
   			
   			var hiring_namager	=	$('#hiring_namager').val();
   			var token 			=	"{{ csrf_token() }}";
   			var id    			=	client_id;
   						var url   			=	"{{ route('product_company.jobs.hiring_manager',':id') }}";
   						url     			=	url.replace(':id', id);  		
   						var baseurl    		=	"{{ asset('assets/images/Loading2.gif') }}";    		
   			$.ajax({
   						//type  : 'GET',
   						type  		: 'POST',
   						url   		: url,
   						data		: {'_token': token, 'hiring_namager' : hiring_namager},
   						 beforeSend	: function() {
   									 $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
   						},
   						success 	: function (response) 
   						{
   									$('#kt_activitiesClient_body').html(response.view);
   						}
   			});				
   	}
   	else
   	{
   		alert("Please select any client after that choose our Hiring manager or Contact person");
   	}				   				  				
   		  });
});

</script>

<script>

$(document).ready(function()
{
 $( "#recruiter" ).on( "click", function() {
   		  
   		  		var client_id 	=	$('#client_id').val();				
   				 				
   	if(client_id > 0)
   	{
   		
   			$('#kt_activitiesRecruiter_toggle').trigger('click');   						
   			
   			var clientName    	=   $("#client_id option:selected"); 
   			var text_value 		=	clientName.text();
   			$('#light_recruiter_name').html(text_value); 
   			
   
   		var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';						
   		str					=	text_value;
   		str					=	str.substring(0, 1);
   		str	= strVal		=	str.toLowerCase();
   		var str_bg			=	str.charCodeAt(0);
   		
   		if(str_bg >=97 && str_bg <=101){
   			bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
   		}else if(str_bg >=102 && str_bg <=106){
   			bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
   		}else if(str_bg >=107 && str_bg <=111){
   			bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
   		}else if(str_bg >=112 && str_bg <=116){
   			bgTextDecore		=	'fs-3 bg-light-success text-success';	
   		}else if(str_bg >=117 && str_bg <=121){
   			bgTextDecore		=	'fs-3 bg-dark text-light';	
   		}else {
   			bgTextDecore		=	'fs-3 bg-light text-dark';	
   		}											
   		$('#light_recruiter_caption').html(strVal.toUpperCase()); 		
   		$('#light_recruiter_caption').addClass(bgTextDecore); 						
   			
   			
   			var recruiter	=	$('#recruiter').val();
   			var token 		=	"{{ csrf_token() }}";
   		
   			var id    		=	client_id;
   						var url   		=	"{{ route('product_company.jobs.recruiter',':id') }}"; 
   						url     		=	url.replace(':id', id);  		
   						var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
   			$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						data		: {'_token': token, 'recruiter' : recruiter},
   						 beforeSend	: function() {
   									 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
   						},
   						success 	: function (response) 
   						{
   									$('#kt_activitiesRecruiter_body').html(response.view);
   						}
   				}); 
   				
   				
   				
   		/*var panellist	=	$('#panellist').val();
   		var token 		=	"{{ csrf_token() }}";					
   		var id    		=	1;
   		var url   		=	"{{ route('product_company.job-applications.recruiter',':id') }}"; 
   		url     		=	url.replace(':id', id);  		
   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
   		$.ajax({
   					type  		: 'POST',
   					data		: {'_token': token, 'panellist' : panellist},
   					url   		: url,
   					beforeSend	: function() {
   												$('#kt_activitiesIntTeam_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
   					},
   					success 	: function (response) 
   					{
   						$('#kt_activitiesIntTeam_body').html(response.view);
   					}
   		}); 	*/					
   	}
   	else
   	{
   		alert("Please select any client after that choose our Recruiters team");
   	}	
   				 				   				  				
   		  });
		  
		  
});

 	var input = document.querySelector('input[name=tags]');
    new Tagify(input)
   
   
</script>



<script>

 $(document).on('click', '.submitfrm', function() {
   
    		   
   var data 				=	new FormData();
   var form_data 			=	$('#kt_contact_add_form').serializeArray();
   $.each(form_data, function (key, input) {
   		data.append(input.name, input.value);
   });
     
     var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();    
     data.append('detail_description', kt_docs_tinymce_hidden);
     
     $(this).prop("disabled", true);
      $(this).html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );

   $.ajax({
   			url					: '{{route('product_company.jobs.store')}}',
   			headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
   			method				: "POST",
   			processData			: false,
   			contentType			: false,
   			data				: data,
   			redirect			: true,
   			success				: function (data) {
   								//alert(data.status); 
   									if(data.status == 'success'){
   											 window.location = '{{ route('product_company.jobs.index') }}'
   											//{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/product_company\/clients"}	
   									}
   									
   			}
   			//error: function (e) {
   					//error
   			//}
   		});
        
   });
</script>

<script>
   $(document).ready(function() {
   
   		$.noConflict();
   
   		 
   
   });	
   	
   	
    $('.add_custom_title').click(function (){
      			
      			var customTitleId	=	$('#customTitleId').val();
      			customTitleId++;
      			
      			$('#customTitleId').val(customTitleId);
       			
      			$('.custom-detail-container').last().append('<div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="custom_title[]" id="custom_title_'+customTitleId+'" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title"><input type="text" name="custom_title[]" id="custom_title_'+customTitleId+'" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title"><input type="text" name="custom_title[]" id="custom_title_'+customTitleId+'" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom Title"></div></div>');
      	});
   	  $('.remove_cperson').click(function(){
      			$('.custom-detail-container').children().last().remove();
      	});
   //updatefrm
   
   function initJobCurrencySelect2() {
       if (window.jQuery && $.fn.select2) {
           $('.select2-job-currency').each(function() {
               var $el = $(this);
               if ($el.hasClass('select2-hidden-accessible')) {
                   $el.select2('destroy');
               }
               $el.select2({
                   width: '100%',
                   dropdownParent: $(document.body),
                   dropdownCssClass: 'br-currency-select2-dropdown',
                   placeholder: 'Search currency...',
                   minimumResultsForSearch: 0,
                   templateSelection: function(data) {
                       if (!data.id) return data.text;
                       var $opt = $(data.element);
                       var short = $opt.data('short') || $opt.data('code');
                       return short ? short : data.text;
                   },
                   matcher: function (params, data) {
                       if ($.trim(params.term) === '') {
                           return data;
                       }
                       if (typeof data.text === 'undefined') {
                           return null;
                       }
                       var term = params.term.toLowerCase();
                       var text = data.text.toLowerCase();
                       var $opt = $(data.element);
                       var code = ($opt.data('code') || '').toString().toLowerCase();
                       var searchKw = ($opt.data('search') || '').toString().toLowerCase();

                       if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                           return data;
                       }
                       return null;
                   }
               });
           });
       }
   }

   $(document).ready(function() {
       initJobCurrencySelect2();
   });

   $('body').on('change', '#min_salary_currency', function(){
       var val = $(this).val();
       if ($('#max_salary_currency').val() !== val) {
           $('#max_salary_currency').val(val).trigger('change.select2');
       }
       if ($('#salary_currency').length) {
           $('#salary_currency').val(val);
       }
   });
   $('body').on('change', '#max_salary_currency', function(){
       var val = $(this).val();
       if ($('#min_salary_currency').val() !== val) {
           $('#min_salary_currency').val(val).trigger('change.select2');
       }
       if ($('#salary_currency').length) {
           $('#salary_currency').val(val);
       }
   });
   $('body').on('change', '#min_sal_mode', function(){
   			var	min_sal_mode	=	$(this).val();
   document.getElementById("max_sal_mode").selectedIndex = min_sal_mode;
   });
   $('body').on('change', '#max_sal_mode', function(){
   			var	max_sal_mode	=	$(this).val();
   document.getElementById("min_sal_mode").selectedIndex = max_sal_mode;
   });
   
</script>

<script>

 	$(document).on('click', '.btn', function() { 
      	
		
      		var state 	=	$('#kt_contact_add').attr("data-wizard-state"); 
   			if(state == 'last')
      		{
				
   				$('.span_client_name').html('');
   				$('.span_location').html('');
   				$('.span_jobtitle').html('');
   				$('.span_job_type').html('');
   				$('.span_openings').html('');
   				
   				
				var global_company_type				=	{{$global_company_type}};
				if(global_company_type == 3)
				{
					var client_id			=	'{{@$client[0]->name}}'; 
      				$('.span_client_name').html(client_id);
				}
				else
				{
					var client_id			=	$('#client_id option:selected').text(); 
      				$('.span_client_name').html(client_id);	
				}
				
				
				
   				
   				
   				var kt_select2_3		=	$('#kt_select2_3 option:selected').text(); 
      				$('.span_location').html(kt_select2_3);	
   				
				var employment_type		=	$('#employment_type option:selected').text(); 
								$('.span_job_type').html(employment_type);	
				
				var title				=	$('#title').val(); 
								$('.span_jobtitle').html(title);	
				
				var total_positions		=	$('#total_positions').val(); 
								$('.span_openings').html(total_positions);
				
				var category_id		=	$('#category_id option:selected').text(); 
								$('.span_department').html(category_id);	
						
				var min_exp		=	$('#min_exp option:selected').text();   
								$('.span_min_exp').html(min_exp);
			   
				var min_exp		=	$('#max_exp option:selected').text();   
								$('.span_max_exp').html(min_exp);
				
				var qualification		=	$('#qualification option:selected').text();   
								$('.span_qualification').html(qualification);	
   
   	
				var min_salary_currency		=	$('#min_salary_currency option:selected').data('code') || $('#min_salary_currency option:selected').text().split('—')[0].trim(); 
				var minimum_salary			=	$('#minimum_salary').val(); 
				var min_sal_mode			=	$('#min_sal_mode option:selected').text();   
				var minSal					=   min_salary_currency +' '+minimum_salary +' '+ min_sal_mode;
				$('.span_min_sal').html(minSal);
				
				var max_salary_currency		=	$('#max_salary_currency option:selected').text(); 
				var maximum_salary			=	$('#maximum_salary').val(); 
				var max_sal_mode			=	$('#max_sal_mode option:selected').text();   
				var maxSal					=   max_salary_currency +' '+maximum_salary +' '+ max_sal_mode;
				$('.span_max_sal').html(maxSal);
				
				var end_date				=	$('#end_date').val(); 
								$('.span_due_date').html(end_date);
				
				var hiring_namager			=	$('#hiring_namager').val(); 
								$('.span_contact_person').html(hiring_namager);	
				
				var recruiter				=	$('#recruiter').val(); 
								$('.span_recruiter').html(recruiter);	
   		
				var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();      
							$('.span_description').html(kt_docs_tinymce_hidden);		
					
				var tags					=	$('#tags').val();  
				arr 						=	$.parseJSON(tags); 
				var cnt						=	0;
				var tags_html				=	'';				 
				$.each(arr,function(key,value){				
					if(cnt == 0){
						tags_html			=	value.value; 	
					}else{
						tags_html			=	tags_html +' , '+ value.value; 	
					}
					cnt++;
				});
				$('.span_skills').html(tags_html);
   	
   
   	
   	
				var arr = $('input[name="custom_title[]"]').map(function () {
					return this.value; 
				}).get();
				$('.span_custom_question_title').html(arr);
				
   	
   	
   	
				$('.submitfrm').show();	
				$('.updatefrm').show();
   					
   		
			}
	   		else
	   		{
		   		$('.submitfrm').hide();	
		   		$('.updatefrm').hide();	
	   		} 
   		
   	});	
	
</script>

<?php 
   if($page_event == 'EDIT')
   {
?>
<script>
   $(document).on('click', '.updatefrm', function() {
   	 
   			var data 				=	new FormData();
   var form_data 			=	$('#kt_contact_add_form').serializeArray();
   $.each(form_data, function (key, input) {
   		data.append(input.name, input.value);
   });
     
     var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();    
     data.append('detail_description', kt_docs_tinymce_hidden);

     $(this).prop("disabled", true);
      $(this).html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );

   $.ajax({
   			url					: '{{route('product_company.jobs.update', $job->id)}}',
   			headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
   			method				: "POST",
   			processData			: false,
   			contentType			: false,
   			data				: data,
   			redirect			: true,
   			success				: function (data) {
   						//alert(data.status); 
   									if(data.status == 'success'){
   											 window.location = '{{ route('product_company.jobs.index') }}'
   											//{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/product_company\/clients"}	
   									}
   									
   			}
   			//error: function (e) {
   					//error
   			//}
   		});
   
   
   
   });
</script>
<script>				  
   $('body').on('change', '#kt_select2_3', function(){
   
   alert("call");
   
   });				  
   
</script>
<!--begin::TinyMCE Editor--> 
<script>
   // See https://www.tinymce.com/docs/configure/editor-appearance/
   tinymce.init({
      selector: 'textarea',
      branding: false,
      menubar: false,
      setup: function (editor) {
         editor.on('keyup', function () {
               // Revalidate the comment field
               fv.revalidateField('comment');
         });
      },
   });
</script>
<!--end::TinyMCE Editor-->
<?php 
   }
   ?>


<!-- <script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}"></script> -->

<script src="{{ asset('demo/js/custom/documentation/forms/select2.js') }}<?php echo TM_VERSION; ?>"></script>
@endpush