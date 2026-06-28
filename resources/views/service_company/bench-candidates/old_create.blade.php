@extends('layouts.service_company_layout')
@php 
	use App\Helper\GeneralMethod;	
	$customTitleId	=	0;
@endphp
@push('head-script')
<style>
body {overflow-x:hidden;} 
.dropzone { padding: 1.8rem 1.75rem!important; border: 2px dashed #e6e7e8!important; background:transparent!important; text-align: inherit!important; }
.font-size-icon { font-size: 1.3em; }
.dashed-border-top{ border-top:1px dashed gray;}
.form{ clear: both; }
.form--disabled input, .form--disabled textarea, .form--disabled select {padding-left: 0; padding-top: 4px; border: 0 solid #efefef; background: white; height: auto; transition: all 0.2s ease-in-out; font-size: 1.05rem; font-weight: 500; color: #3f4254; text-transform: capitalize; }
.enabled_input { display: block; width: 100%; border-radius: 2px; border: 1px solid #efefef; outline: none; -webkit-appearance: none; background-color: #f5f8fa; border-color: #f5f8fa; color: #5e6278; padding: 0.5em; transition: color 0.2s ease, background-color 0.2s ease;}
#calender::-webkit-inner-spin-button,
#calender::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesRecruiter {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesRecruiter {width: 1200px;}
}

.tagify .tagify__tag div .tagify__tag-text {
    color: #0c0c0c !important;
    font-weight: 500 !important;
    font-size: 14px !important;
}
.tagify{
  height: 37px !important;
}

.disablesectionOn.setOpacity{
    cursor: not-allowed;
    background: #8a878d;
    opacity: 0.5;
}

</style>
@endpush

@section('content')

@php 	
	$country_code =  GeneralMethod::countryCodeFun();
  $currency_code =  GeneralMethod::defaultCurrency();

  $phone_code = $country_code;
  $second_phone_code = $country_code;
  $url = '';

  $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
  $photo  					=	url('public/avatar.png');	
  if(@$page_event == 'ADD'){
   $pagetitle			=	'Add Candidate';
   $resume_url			=	'';	
  }else if(@$page_event == 'EDIT'){ 
    $phone_code = ($application->phone_code) ? $application->phone_code : $phone_code;
    $second_phone_code = ($application->second_phone_code) ? $application->second_phone_code : $second_phone_code;
   $pagetitle	=	'Edit Candidate';      
   if(!empty($application->resume)){
    // $url				=	 url('/public/user-uploads/documents/'.$documents->documentable_id.'/'.$documents->resume);
    $url				=	 $application->resume;
    $info = pathinfo($url);
        $resume_url			=	'<a href="'.route('download.resume', ['id' => $application->id,'path' => urlencode($url)]).'" target="_blank" download><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>';
   }else{
    $resume_url			=	'';	
   }
   if(!empty($application->photo)){
    $photo			=	$application->photo;	
   }   
  }	
      
 

$city			=	$country_id		=	 $country_name	=	$recruiterData = $recruiterDataId	= $state_name		=	$address		=	'';
$address		=	@$jobLocation->address;
if(@$jobLocation->country_id == 101){
   $city			=	@$jobLocation->city_name;
   $state_name		=	@$jobLocation->state_name;	
}else{
   $city		=	@$jobLocation->city;		
}
if(@$jobLocation->country_id > 0){
    $country_name	=	GeneralMethod::getCountryName(@$jobLocation->country_id);	
    foreach($country_name as $cval){
     $country_name	=	ucwords($cval['name']);	
    }				 
}

 $status_class		=	'';
 if(@$jobDetail->status == 'active'){
  $status_class	=	'bg-light-success text-success';
 }else if(@$jobDetail->status == 'hold'){
  $status_class	=	'bg-light-warning text-warning';
 }else if(@$jobDetail->status == 'inactive'){
  $status_class	=	'bg-light-danger text-danger';
 }


$snp_one	=	$snp_two	= $snp_zero	= $snp_text_dummy = $snp_last_working = "";
$snp_text_one	=	$snp_text_two	= $snp_text_zero	=	'display:none;';		
$snp_mode = @$application->serving_notice_period_mode;
if($snp_mode== 1){
  $snp_one		=	'checked="checked"';
  $snp_text_one	=	'display:flex'; 
  $snp_text_dummy = 'Serving Notice Period';   
  $snp_last_working = $application->np_id;                                  	
}else if($snp_mode== 2){
  $snp_two	=	'checked="checked"';
  $snp_text_one	=	'display:flex';
  $snp_text_dummy = 'Immediate';  
  $snp_last_working = $application->last_working_date; 
}else{
  $snp_zero		=	'checked="checked"';	
  $snp_text_zero	=	'display:block';
}  

       $rec_inc		=	0;
			foreach($recruiterlist as $recData){
				if($rec_inc == 0){
					$recruiterData	=	$recData->name;	
          $recruiterDataId	=	$recData->id;	
				}else{
					$recruiterData	=	$recruiterData.' , '.$recData->name;	
          $recruiterDataId	=	$recruiterDataId.' , '.$recData->id;	
				}
				$rec_inc++;
			}

								   				
@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-gray-800 mb-1">Add Inhouse Talent</a> </h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Inhouse Talent details and submit</span> </div>
  </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="card-body pt-3 px-5">
          <h3 class="py-3"></h3>


          <!--begin::Form-->
          <form class="form" action="#" method="post">
            <!--begin::Input group-->
            <div class="form-group row">
                <!--begin::Label-->
                {{-- <label class="col-lg-2 col-form-label text-lg-right">Upload Resume:</label> --}}
                <!--end::Label-->

                <!--begin::Col-->
                <div class="col-lg-12">
                    <!--begin::Dropzone-->
                    <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_3">
                      
                      <meta name="csrf-token" content="{{ csrf_token() }}">
                        <!--begin::Controls-->
                        <div class="dropzone-panel mb-lg-0 mb-2">
                            <a class="dropzone-select btn btn-sm btn-primary me-2"><i class="fa fa-upload" aria-hidden="true" style="color:#fffffff"></i> Resume {{ ($resume_url) ? 'Update' : 'Upload' }} </a>
                            <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a>
                        </div>
                        <!--end::Controls-->

                        <!--begin::Items-->
                        <div class="dropzone-items wm-200px">
                            <div class="dropzone-item" style="display:none">
                                <!--begin::File-->
                                <div class="dropzone-file">
                                    <div class="dropzone-filename" title="some_image_file_name.jpg">
                                        <span data-dz-name>some_image_file_name.jpg</span>
                                        <strong>(<span data-dz-size>340kb</span>)</strong>
                                    </div>

                                    <div class="dropzone-error" data-dz-errormessage></div>
                                </div>
                                <!--end::File-->

                                <!--begin::Progress-->
                                <div class="dropzone-progress">
                                    <div class="progress">
                                        <div
                                            class="progress-bar bg-primary"
                                            role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Progress-->

                                <!--begin::Toolbar-->
                                <div class="dropzone-toolbar">
                                    <span class="dropzone-delete" data-dz-remove><i class="bi bi-x fs-1"></i></span>
                                </div>
                                <!--end::Toolbar-->
                            </div>
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Dropzone-->

                    <!--begin::Hint-->
                    <span class="form-text text-muted"><span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.. &nbsp; {!! @$resume_url !!}</span></span>
                    <!--end::Hint-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
          </form><br>
         <!--end::Form-->




          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="edit_token" value="{{ @$application->id }}"/>
            <div class="mb-5">
              <div class="flex-column">
                <div class="form-group row fv-row mb-10 d-none">
                  <div class="col-lg-4 col-xl-4 text-center">
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                      <div class="image-input-wrapper w-90px h-90px" style="background-image: url({!! @$photo !!})"></div>
                      <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                      <input type="file" name="photo" id="avatar" accept=".png, .jpg, .jpeg" />
                      <input type="hidden" name="avatar_remove" />
                      </label>
                      <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span>
                      <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span>
                    </div>
                    <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                  </div>
                  <div class="col-lg-8 col-xl-8">
                    <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume" id="resume">
                    <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                    <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2 MB)  Allowed file types: Pdf or Word. &nbsp;
                    	@if(@$application->resume)
			         <a href="{{ @$application->resume}}" target="_blank"><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>
			         @endif
			         </span> 
                    </div>
                  </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Enter Full Name:</label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" placeholder="Enter full name" value="{{ @$application->full_name }}">
                    <input type="hidden" name="resumeCheck" value="1" id="resumeCheck" />
                    <span class="form-text text-muted">Please enter candidate full name</span> </div>
                    <div class="col-lg-2 d-none">
                      <label class="form-label">Gender</label>
                      <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="gender" id="gender">
                        <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option> 
                        <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                      </select>
                      <span class="form-text text-muted">Please select gender</span> 
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Enter Current Designation:</label>
                    <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{ @$application->current_designation }}">
                    <span class="form-text text-muted">Please enter candidate designation</span> </div>
                  
                    <div class="col-lg-4">
                      <label class="form-label">Enter Employee ID (If Any)</label>
                      <input type="email" class="form-control form-control-solid" name="employee_number" id="employee_number" placeholder="Enter employee number" value="{{ @$application->employee_number }}">
                      <span class="form-text text-muted">Please enter employee number</span> 
                    </div>


                </div>
                <div class="form-group row fv-row mb-10 fv-plugins-icon-container has-danger">
                  <div class="col-lg-4">
                    <label class="form-label required">Enter Mobile Number(Confidential)</label>
                    <input type="number" class="form-control form-control-solid" name="phone" id="phone" placeholder="Enter mobile number" value="{{ @$application->phone }}">
                    <input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code">
                    <span class="form-text text-muted">*This number will not be shared with Clients</span> </div>
                  <div class="col-lg-4 d-none">
                    <label class="form-label">Secondary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="{{ @$application->secondary_mobile }}">
                    <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
                  <div class="col-lg-4 d-none">
                    <label class="form-label">Date of Birth:</label>
                    <input type="date" class="form-control form-control-solid"  name="dob" id="dob" placeholder="Enter dob" value="{{ @$application->dob }}" onclick="this.showPicker()" >
                    <span class="form-text text-muted">Please enter your dob</span> </div>

                    <div class="col-lg-4">
                      <label class="form-label required">Enter Email ID(Confidential)</label>
                      <input type="email" class="form-control form-control-solid" name="email" id="email" placeholder="Enter email" value="{{ @$application->email }}">
                      <span class="form-text text-muted">*This email id will not be shared with Clients</span> </div>

                      <div class="col-lg-4">
                        <label class="form-label required">Education:</label>
                        <select class="form-select form-select-solid"  name="qualification" id="qualification" aria-label="Select qualification">
                          <option value="" >Select Education</option>
                            @foreach($qualification as $data_value)
                                @php
                                $sel	=	'';
                                if(@$application->qualification == $data_value->id ){
                                  $sel	=	'selected="selected"';
                                }
                              @endphp
                              <option value="{{ @$data_value->id }}" {{ @$sel }}>{{ @$data_value->name }}</option>	
                            @endforeach
                        </select>
                        <span class="form-text text-muted">Please select candidate education</span> </div>

                </div>
                <div class="form-group row fv-row mb-10 d-none">
                
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label"> 
                    <div style="">
                      <div class="d-inline-flex">
                        <label class="radio-inline cursor-pointer">
                        <input class="form-check-input h-15px w-15px fresher mt-0 me-1 cursor-pointer" type="radio" name="pincode" value="0" {{(@$application->pincode==0) ? 'checked' : 'checked' }} onclick="fresherrbutton(this.value);">
                        Experience </label>
                      </div>
                      <div class="d-inline-flex">
                        <label class="radio-inline cursor-pointer">
                        <input  class="form-check-input h-15px w-15px fresher mt-0 me-1 cursor-pointer" type="radio" name="pincode" value="1" {{(@$application->pincode==1) ? 'checked' : '' }} onclick="fresherrbutton(this.value);">
                        Fresher</label>
                      </div>
                    </div>
                    </label>

                     <div class="row disablesectionOnRow">
                      @php

                       $value = @$application->total_exp; // Convert to string to preserve decimal
                       if(!is_null($value)){
                        $total_exp =  GeneralMethod::separateNumberParts($value);

                        //  dd($value);
                      
                        $exp_years = ($total_exp[0]) ;
                        $exp_months = ($total_exp[1]); 
                      
                        $selected_exp_year = ($exp_years) ;
                        $selected_exp_month = ($exp_months); 
                       }else{
                        $selected_exp_year = null ;
                        $selected_exp_month = null; 
                       }

                      @endphp
                      <div class="col-lg-6">
                        <select class="form-select form-select-solid" arial-label="Select total_exp_year" id="total_exp_year" name="total_exp_year">
                          <option value="0">Select Year</option>
                          @php 
                                        for ($i = 1; $i <= 60; $i++) {
                                          $selected = '';
                                              $s = 'Year';
                                              if($i > 1){
                                                 $s = 'Years';
                                              }
                                              if(is_null($selected_exp_year)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_exp_year){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>

                      </div>
                      <div class="col-lg-6">
                        <select class="form-select form-select-solid" arial-label="Select total_exp_month" id="total_exp_month" name="total_exp_month">
                          <option value="">Select Month</option>
                          @php 
                                       for ($i = 0; $i <= 11; $i++) {
                                        $selected = '';
                                              $s = 'Month';
                                              if($i > 1){
                                                 $s = 'Months';
                                              }
                                              if(is_null($selected_exp_month)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_exp_month){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>
                      </div>
                      <div class="disablesection {{(@$application->pincode==1) ? 'disablesectionOn' : '' }} setOpacity"> </div>
                     </div>


                    {{-- <input type="number" class="form-control form-control-solid" name="total_exp" id="total_exp" placeholder="Enter total experience" value="{{ @$application->total_exp }}"> --}}

                    <span class="form-text text-muted">Please enter total experience</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Relevant Experience:</label>

                     <div class="row disablesectionOnRow">
                      @php

                       $valuew = @$application->rel_exp;
                      // dd( $value);
                      if(!is_null($value)){
                        $total_rel_exp =  GeneralMethod::separateNumberParts($valuew);
                        // dd($total_rel_exp,$valuew);
                        $rel_exp_years = ($total_rel_exp[0]);
                        $rel_exp_months = ($total_rel_exp[1]);  

                        $selected_rel_exp_year = ($rel_exp_years);
                        $selected_rel_exp_month= ($rel_exp_months);
                        // dd($selected_rel_exp_year,$selected_rel_exp_month);
                      }else{
                        $selected_rel_exp_year = null;
                        $selected_rel_exp_month= null;
                      }

                     @endphp

                      <div class="col-lg-6">
                        <select class="form-select form-select-solid" arial-label="Select rel_exp_year" id="rel_exp_year" name="rel_exp_year">
                          <option value="0">Select Year</option>
                          @php 
                                        for ($i = 1; $i <= 60; $i++) {
                                          $selected = '';
                                              $s = 'Year';
                                              if($i > 1){
                                                 $s = 'Years';
                                              }
                                              if(is_null($selected_rel_exp_year)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_rel_exp_year){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>

                      </div>
                      <div class="col-lg-6">
                        <select class="form-select form-select-solid" arial-label="Select rel_exp_month" id="rel_exp_month" name="rel_exp_month">
                          <option value="">Select Month</option>
                          @php 
                                       for ($i = 0; $i <= 11; $i++) {
                                        $selected = '';
                                              $s = 'Month';
                                              if($i > 1){
                                                 $s = 'Months';
                                              }
                                              if(is_null($selected_rel_exp_month)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_rel_exp_month){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>
                      </div>
                      <div class="disablesection {{(@$application->pincode==1) ? 'disablesectionOn' : '' }} setOpacity"> </div>
                     </div>

                    {{-- <input type="number" class="form-control form-control-solid" name="rel_exp" id="rel_exp" placeholder="Enter relevant experience" value="{{ @$application->rel_exp }}"> --}}


                    <span class="form-text text-muted">Please enter relevant experience</span> 
                    <div class="fv-plugins-message-container invalid-feedback" id="rel_exp_error"></div></div>
                  <div class="col-lg-4">
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" {{ @$snp_zero }}>
                      Notice period </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="1" onclick="rbutton(this.value);" {{ @$snp_one }}>
                      Available from </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="2" onclick="rbutton(this.value);" {{ @$snp_two }}>
                      Immediate </label>
                    </div>
                    </label>

                    <div style="{{ @$snp_text_one }}" id="snp-immediate" class="" >
                      <div style="position: relative;margin-right: 6px;">
                        <input type="text" id="text_display" value="{{ $snp_text_dummy }}" class="form-control form-control-solid" placeholder="" readonly>
                      </div>
  
                      <div style="position: relative;">
                          <input type="text" id="np_id_display" name="np_id_display" class="form-control form-control-solid" placeholder="Select joining date" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" onclick="document.getElementById('np_id').click()">
                          <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" style="opacity: 0; position: absolute; z-index: -1;top: 0;" onclick="this.showPicker()">
                      </div>
                    </div>

                    {{-- <input type="text" name="np_id" id="np_id" class="form-control form-control-solid np_id" placeholder="" style="{{ @$snp_text_one }}"  value="{{ @$application->np_id }}" onclick="this.showPicker()"> --}}

                    <select class="form-select form-select-solid" arial-label="Select ddl_np_id" id="ddl_np_id" name="ddl_np_id" style="<?php echo @$snp_text_zero; ?>">
                      <option value="">Select Notice Period</option>
                      @php 
                                   for ($i = 1; $i <= 200; $i++) {
                                           $s = 'Day';
                                            if($i > 1){
                                               $s = 'Days';
                                            }
                                          $selected = ($i == @$application->np_id) ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                    }
                      @endphp
                    </select>


                    {{-- <input type="number" name="ddl_np_id" id="ddl_np_id" class="form-control form-control-solid" style="<?php echo @$snp_text_zero; ?>" placeholder="Enter Notice Period" value="{{ @$application->np_id }}" > --}}

                    <input type="text" id="calender" name="calender" style="display:none">
                    <span class="form-text text-muted">Enter the days within which your candidate can join</span> </div>
                </div>
                <div class="form-group row">
                  <div class="col-lg-12">
                    <label class="form-label required">Pay Rate: (Enter the candidate's salary)</label>
                    <div class="row fv-row mb-10">
                      <div class="col-1 pe-0">
                        <label class="form-label">Currency </label>
                        <select class="form-select form-select-solid currency" arial-label="Select Currency" id="pay_rate_currency" name="pay_rate_currency">
                        {{-- @foreach($currency as $key=>$val) --}}

                          @php 
                                          $current_ctc_cur	=	"";
                                          $current_ctc_cur	=	@$application->pay_rate_currency;
                                          
                                          foreach($currency as $key=>$val){
                                              $sel	=	'';
                                              if($current_ctc_cur===null){
                                                if($currency_code==$val){
                                                  $sel	=	'selected="selected"';	
                                                }	
                                              }else{
                                                if($current_ctc_cur == $key){
                                                  $sel	=	'selected="selected"';	
                                              }	
                                              }
                                                                  
                                              echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                          }
                          @endphp

                          {{-- <option value="{{ $key }}" {{ (@$application->pay_rate_currency==$key) ? 'selected' : '' }}>{{ $val }}</option>	 --}}
                        {{-- @endforeach --}}
                        </select>
                      </div>
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">Pay rate annual <span  style="position: absolute; right: 8px; color: #ef560a;"id="pay_rate_annual_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="pay_annual" id="pay_annual" placeholder="Enter pay rate annual" value="{{ @$application->ctc_yearly }}">
                        <span class="form-text text-muted payrateannual"></span>
                      </div>
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">Pay rate monthly <span style="position: absolute; right: 8px; color: #ef560a;" id="pay_rate_monthly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="pay_monthly" id="pay_monthly" placeholder="Enter pay rate monthly" value="{{ @$application->ctc_monthly }}">
                        <span class="form-text text-muted payratemonthly"></span>
                      </div>
                      <div class="col-3 px-2">
                        <div class="form-label" style="position: relative;">Pay rate hourly <span style="position: absolute; right: 8px; color: #ef560a;" id="pay_rate_hourly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="pay_rate_amount" id="pay_rate_amount" placeholder="Enter pay rate hourly" value="{{ @$application->ctc_hourly }}">
                        <span class="form-text text-muted payratehourly">*The salary will not be shared with clients.</span>
                      </div>
                      <div class="col-2 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="pay_rate_mode" id="pay_rate_mode">                          
                        @foreach($salaryMode as $key=>$val)
                          <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>	
                        @endforeach					
                        </select>
                      </div>
                      
                        </div>
                    </div>
                  <div class="col-lg-12">
                    {{-- //Enter the amount you are charging from clients --}}
                    <label class="form-label required ">Bill Rate: (Enter the amount you will charge the client)</label>
                    <div class="row fv-row mb-10">
                      <div class="col-1 pe-0">
                        <label class="form-label">Currency </label>
                        <select class="form-select form-select-solid currency" arial-label="Select Currency" name="bill_rate_currency" id="bill_rate_currency">
                          {{-- @foreach($currency as $key=>$val) --}}
                              @php 
                                          $current_ectc_cur	=	"";
                                          $current_ectc_cur	=	@$application->bill_rate_currency;
                                          foreach($currency as $key=>$val){
                                            $sel	=	'';
                                            if($current_ectc_cur===null){
                                              if($currency_code==$val){
                                                $sel	=	'selected="selected"';	
                                              }
                                            }else{
                                              if($current_ectc_cur == $key){
                                              $sel	=	'selected="selected"';	
                                              }	
                                            }
                                            
                                            echo '<option value="'.$key.'"   '.$sel.' >'.$val.'</option>';	
                                          }
                              @endphp
                            {{-- <option value="{{ $key }}" {{ (@$application->bill_rate_currency==$key) ? 'selected' : '' }}>{{ $val }}</option>	 --}}
                            {{-- @endforeach	 --}}
                        </select>
                      </div>

                      
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">Bill rate annual <span style="position: absolute; right: 8px; color: #ef560a;" id="bill_rate_annual_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="bill_annual" id="bill_annual" placeholder="Enter Bill rate annual" value="{{ @$application->ectc_yearly }}" >
                        <span class="form-text text-muted billrateannual"></span>
                      </div>
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">Bill rate monthly <span style="position: absolute; right: 8px; color: #ef560a;"id="bill_rate_monthly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="bill_monthly" id="bill_monthly" placeholder="Enter Bill rate monthly" value="{{ @$application->ectc_monthly }}" >
                        <span class="form-text text-muted billratemonthly"></span>
                      </div>
                      <div class="col-3 px-2">
                        <div class="form-label" style="position: relative;" >Bill rate hourly <span  style="position: absolute; right: 8px; color: #ef560a;"id="bill_rate_hourly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="bill_rate_amount" id="bill_rate_amount" placeholder="Enter Bill rate hourly" value="{{ @$application->ectc_hourly }}" >
                        <span class="form-text text-muted billratehourly">*This amount will be shared with clients</span>
                      </div>
                      <div class="col-2 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="bill_rate_mode" id="bill_rate_mode">                         
                        @foreach($salaryMode as $key=>$val)							
                          <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>	
                        @endforeach
                        </select>
                      </div>
                       </div>
                  </div>

                  

                  
                   

                </div>
                <div class="form-group row fv-row mb-10">
                  {{-- <div class="col-lg-6">
                    @php

                          $country			=	$display_city		=	'';
                          $country			=	@$application->country;
                          $display_city_id	= 'style="display:none"';		
                          if($country == 101){
                            $display_city_id	=	'';
                            $display_city		=	'style="display:none"';	
                          }
                          else{
                            $display_city_id	= 'style="display:none"';			
                          }
                          
                    @endphp

                    <label class="form-label required">Country</label>
                    <select class="form-select form-select-solid" arial-label="Select Currency" name="country" id="country" >
                      <option value="">Select Country</option>						
                          @foreach($countries as $countryVal)	

                            @php
                          $sel			=	'';
                          if($country != ''){
                            if($countryVal['id'] == $country ){
                              $sel	=	'selected="selected"';	
                            }	
                          }else{
                              if($countryVal['id'] == 101 ){
                                $sel	=	'selected="selected"';	
                              }
                          }
                          @endphp		

              															
                        <option value="{{ @$countryVal['id'] }}" {{ $sel }}>{{ @$countryVal['name'] }}</option>	
                      @endforeach						
                    </select>
                    <span class="form-text text-muted">Please enter your Country</span> 
                  </div> --}}
                  {{-- <div class="col-lg-4">
                    <label class="form-label required">State</label>
                    <select class="form-select form-select-solid change_state" arial-label="Select Currency" name="state" id="state">
                      <option value="">Select State</option>
                      @php 
                            $sel		=	'';
                            $state		=	'';
                            $state		=	@$application->state;
                            if(!empty($statelist)){
                              foreach($statelist as $key=>$sVal){
                                $sel	=	'';
                                if($state == @$sVal['id'] ){
                                  $sel	=	'selected="selected"';	
                                }
                                echo '<option value="'.@$sVal['id'].'"  '.@$sel.' >'.@$sVal['name'].'</option>';	
                              }
                            }
                      @endphp
                    </select>
                    <span class="form-text text-muted">Please enter your State</span> 
                  </div> --}}
                  <div class="col-lg-12">
                    <div class="row">

                      <div class="col-lg-4">
                        <label class="form-label required">Outsource type</label>
                        @php
                        $selected = '';
                          if(@$application->outsourcing_type==''){
                            $selected = 'selected';
                          }
                        @endphp
                        <select class="form-select form-select-solid" arial-label="Select Outsource type" name="outsourcing_type" id="outsourcing_type">
                          <option value="">Select Outsource type</option>
                          <option value="1" {{ (@$application->outsourcing_type==1) ? 'selected' : '' }} >Fulltime </option>
                          <option value="2" {{ (@$application->outsourcing_type==2) ? 'selected' : $selected }}>Contract</option>
                        </select>
                        <span class="form-text text-muted">Please enter your Outsource type</span> </div>

                        <div class="col-lg-4">
                        <label class="form-label required">Commission Type</label>
                        @php
                            $selected = '';
                              if(@$application->comission_type==''){
                                $selected = 'selected';
                              }
                        @endphp
                      <select class="form-select form-select-solid" arial-label="Select Comission type" name="comission_type" id="comission_type">
                        <option value="">Select Commision type</option>
                        <option value="1" {{ (@$application->comission_type==1) ? 'selected' : '' }}>Free (Not charged from Clients)</option>
                        <option value="2" {{ (@$application->comission_type==2) ? 'selected' : $selected }}>Paid Service</option>
                      </select>
                      <span class="form-text text-muted">Please enter candidate comission type</span> </div>
                    
                        {{-- <div class="col-6">
                          <label class="form-label required">Pincode</label>
                          <input type="number" class="form-control form-control-solid" name="pincode" id="pincode" placeholder="Enter pincode" value="{{ @$application->pincode }}">
                          <span class="form-text text-muted">Please enter your pincode</span> 
                        </div> --}}

                        
                      <div class="col-lg-4">
                        <label class="form-label">Profit Percentage</label>
                        <div class="row">
                          <div class="col-12 pe-0" style="position: relative;">
                            <input type="number" class="form-control form-control-solid" name="percentage_hike" id="percentage_hike" placeholder="Enter Profit" value="{{ @$application->percentage_hike }}">
                            <span class="form-text text-muted">Profit in percentage</span>
                            <span style="position: absolute; right: 0; top: 10px;"> 
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                      <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                      <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" id="Stroke-1" fill="#009ef7 " fill-rule="nonzero"></path>
                                      <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" id="Combined-Shape" fill="#009ef7 " opacity="0.3"></path>
                                  </g>
                              </svg>
                              </span>
                          </div>
                        </div>
                      </div>

                      
                      <div class="col-12 pe-0">
                        <label class="form-label">Select Candidate Location</label>
                        <input class="form-control form-control-solid" value="{{ @$default_loc }}" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter your Location..."/>
                        <span class="form-text text-muted">*Location where your candidate is available for an onsite job.</span> 
                      </div>


                      </div>
                      
                  </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-12" style="position: relative;">
                    <label class="form-label required">Enter Skills </label>
                    <span id="addSkillBtn" class="btn btn-icon btn-active-light-primary btn-sm" style="position: absolute; right: 13px; width: 72px; top: -3px;display:none;">Add Skill</span>
                    <input class="form-control form-control-solid" value="{{ @$application->skills }}" placeholder="Enter skills" id="skills" name="skills" />
                    <span class="form-text text-muted">*Enter the relevant skills where customers can search the candidate profile according to their requirements (Max 10 skills allowed).</span>
                   </div>
                </div>

                

                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-12">
                    <div class="dashed-border-top pt-10">
                      <h3 class="font-weight-bold text-dark">Skills Experience</h3>
                      <div class="form-group">
                        <div class="col-sm-12 px-0">
                          <div class="form-group mb-0" id="addmore"> </div>
                          <div class="row custom-detail-container">
                            <div class="form-group mb-0 mt-7 all_custom_details" id="remove">

                                  <div class="row" id="skillExperienceWrapper">
                                        <!-- Dynamic skill experience rows will be added here -->
                                  </div>

                              @php 
                              if(!empty(@$job_additional_detail)){  @endphp
                                 <div class="fv-row row d-none" >
                                  @php
                                     foreach(@$job_additional_detail as $aDetailArr){
                                @endphp
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-1">
                                    <div class="form form--disabled" id="form_lavel_input_{{@$counter}}_1">
                                      <input type="text" name="customDetailsQuestion[]" value="{{ @$aDetailArr->custom_keys }}" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer c-link form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="1" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]" value="{{ @$aDetailArr->custom_keys_value }}" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Detail">
                                </div>
                            @php 
                                } @endphp
                                 </div>
                                 @php }else{ 
                               @endphp
                               <div class="fv-row row d-none">
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-1">
                                    <div class="form form--disabled" id="form_lavel_input_{{@$counter}}_1">
                                      <input type="text" name="customDetailsQuestion[]" value="Enter Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer c-link form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="1" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Detail">
                                </div>
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                                    <div class="form form--disabled" id="form_lavel_input_{{ @$counter }}_2">
                                      <input type="text" name="customDetailsQuestion[]" value="Enter Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="2" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Detail">
                                </div>
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                                    <div class="form form--disabled" id="form_lavel_input_{{ @$counter }}_3">
                                      <input type="text" name="customDetailsQuestion[]" value="Enter Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="3" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Detail">
                                </div>
                              </div>

                               @php 
                            }	
                            @endphp

                            </div>
                          </div>
                          <div id="customDetailLoader"></div>
                          <input type="hidden" name="customTitleId" id="customTitleId" value="{{ @$customTitleId }}"  />
                          <div class="d-flex justify-content-center py-5 my-5 d-none">
                            <button type="button" class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_custom_detail d-flex" id="add_custom_detail">
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                               <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span> <span>Add more </span></button>
                            <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_cperson d-flex" id="remove_custom_detail">
                            <span class="svg-icon svg-icon svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                           <span> Remove</span> </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                
                <div class="dashed-border-top form-group row fv-row mb-10">
                  <h3 class="font-weight-bold text-dark mt-5 mb-5">Relationship Manager</h3>
                   <div class="col-lg-12">
                    {{-- <label class="form-label required">Select the Relationship Manager:</label> --}}
                    <input class="form-control form-control-solid" name="recruiter" id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>" readonly="" />
                    <input type="hidden" class="form-control form-control-solid" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>" readonly="" />
                    <span class="form-text text-muted">*The selected relationship manager's number and email will be shared with clients to coordinate regarding the above candidate.</span> 
                  </div>
                  
                </div>

                <div class="dashed-border-top form-group row fv-row mb-10">
                  <div class="col-lg-12">
                        <h3 class="py-5">Social Links</h3>                     
                        <div class="row my-3 px-3">
                          <div class="col-lg-12">
                              <label class="form-label">Likedin Link</label>
                              <input class="form-control form-control-solid" value="{{ @$application->linkedin }}" placeholder="Enter Likedin Link" id="linkedin" name="linkedin" />
                              <span id="linkedin-msg"></span>
                          </div>
                        </div>
                        <div class="row mt-5 my-3 px-3">
                          <div class="col-lg-12">
                              <label class="form-label">Github Link</label>
                              <input class="form-control form-control-solid" value="{{ @$application->github }}" placeholder="Enter Github Link" id="github" name="github" />
                              <span id="github-msg"></span>
                          </div>
                        </div>
                        <div class="row mt-5 my-3 px-3">
                          <div class="col-lg-12">
                              <label class="form-label">Behance Link</label>
                              <input class="form-control form-control-solid" value="{{ @$application->behance }}" placeholder="Enter Behance Link" id="behance" name="behance" />
                              <span id="behance-msg"></span>
                          </div>
                        </div>
                        <div class="row mt-5 my-3 px-3">
                          <div class="col-lg-12">
                              <label class="form-label">Other Link</label>
                              <input class="form-control form-control-solid" value="{{ @$application->otherLink }}" placeholder="Enter Other Link" id="otherLink" name="otherLink" />
                          </div>
                        </div>
                    </div>
                </div>


                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                      @php 
                              if(@$page_event == 'EDIT'){ 
                                echo '<button type="button" name="edit-form" id="edit-form" value="update" class="btn btn-primary">Update</button>';
                              }else{
                                echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>';	
                              }
                    @endphp
                    {{-- <a href="{{ route('service_company.bench-candidates.index') }}" class="btn btn-secondary">Cancel</a>  --}}
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end::Content-->


<div id="kt_activitiesRecruiter_toggle"></div>
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          @php
          $logo_path = @$companylogo;
          // dd($logo_path );
          @endphp
          @if (!empty(@$logo_path)) 
            <div data-row-id="' . $row->id . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                          <a href="javascript:void(0);">
                                              <div class="symbol-label border-radius-5">
                                                <img src="{{ str_replace('index.php','', @$logo_path)  }}" alt="Company Logo" class="border-radius-5 w-100">
                                              </div>
                                          </a>
                                      </div>
        @else
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l"> 
          <div class="symbol-label border-radius-5" id="light_recruiter_caption">{{ substr(@$company_name,0,1) }} </div>
          </a> 
        </div>
  
      @endif

          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name_n">{{  @$company_name }}</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
            <rect x="0" y="7" width="16" height="2" rx="1" />
            <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
  </div>
</div>


@endsection

@php
    $rates = $currencyConverter['usd_based_rates'] ?? [
        'USD' => 1,
        'INR' => 83,
        'EUR' => 0.87,
        'JPY' => 159.65,
        'GBP' => 0.76,
        'AED' => 3.67
    ];

    $currencyKeys = $currencyConverter['getKey'] ?? [
        'INR',
        'EUR',
        'JPY',
        'GBP',
        'USD',
        'AED'
    ];
@endphp

@push('footer-script')

<script>

  $(document).ready(function () {
    // Initialize intl-tel-input for Primary and Secondary phone fields
    const primaryPhoneInput = window.intlTelInput(document.querySelector("#phone"), {
      separateDialCode: true,
      initialCountry: "{{ $phone_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });

    const secondaryPhoneInput = window.intlTelInput(document.querySelector("#secondary_mobile"), {
      separateDialCode: true,
      initialCountry: "{{ $second_phone_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });

      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);
      
    }
    // Update country code on input or country flag selection
    $("#phone").on("input change", function () {
      updateCountryCode(primaryPhoneInput, "#primary_country_code");
    });

    $("#secondary_mobile").on("input change", function () {
      updateCountryCode(secondaryPhoneInput, "#secondary_country_code");
    });
    // Update country code dynamically when the user selects a different country flag
    $(".iti__flag-container").on("click", function (event) {
      const targetInput = $(event.target).closest(".iti").find("input");

      if (targetInput.is("#phone")) {
        updateCountryCode(primaryPhoneInput, "#primary_country_code");
      } else if (targetInput.is("#secondary_mobile")) {
        updateCountryCode(secondaryPhoneInput, "#secondary_country_code");
      }
    });

    setTimeout(function () {	
      $(".iti__flag-container").click();
    },500);

    const HOURS_PER_MONTH = 160;
    const MONTHS_PER_YEAR = 12;
    const currencyRates = @json($rates);
    const currencyRateschar = @json($currencyKeys);

    let isUpdating = false;

    // Base values (USD)
    let payBase = { hourly: 0 };
    let billBase = { hourly: 0 };

    function format(val) {
        return val ? parseFloat(val).toFixed(2) : '';
    }
    function round2(val) {
        return Math.round((val + Number.EPSILON) * 100) / 100;
    }
    
    function fromUSD(amount, currency) {
        return amount / currencyRates[currency];
    }

   
    // ================= PAY RATE =================
    $('#pay_rate_amount').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let hourly = parseFloat($(this).val()) || 0;

        $('#pay_monthly').val(format(hourly * HOURS_PER_MONTH));
        $('#pay_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
        updatePayUI();
        percentage_hike();

        isUpdating = false;
    });

    $('#pay_monthly').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let monthly = parseFloat($(this).val()) || 0;

        let hourly = monthly / HOURS_PER_MONTH;

        $('#pay_rate_amount').val(format(hourly));
        $('#pay_annual').val(format(monthly * MONTHS_PER_YEAR));
        updatePayUI();
        percentage_hike();

        isUpdating = false;
    });

    $('#pay_annual').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let annual = parseFloat($(this).val()) || 0;

        let monthly = annual / MONTHS_PER_YEAR;
        let hourly = monthly / HOURS_PER_MONTH;

        $('#pay_rate_amount').val(format(hourly));
        $('#pay_monthly').val(format(monthly));
        updatePayUI();
        percentage_hike();

        isUpdating = false;
    });

    // ================= BILL RATE =================
    $('#bill_rate_amount').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let hourly = parseFloat($(this).val()) || 0;

        $('#bill_monthly').val(format(hourly * HOURS_PER_MONTH));
        $('#bill_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
        updateBillUI();
        percentage_hike();
        isUpdating = false;
    });

    $('#bill_monthly').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let monthly = parseFloat($(this).val()) || 0;

        let hourly = monthly / HOURS_PER_MONTH;

        $('#bill_rate_amount').val(format(hourly));
        $('#bill_annual').val(format(monthly * MONTHS_PER_YEAR));
         updateBillUI();
        percentage_hike();

        isUpdating = false;
    });

    $('#bill_annual').on('input', function () {
        if (isUpdating) return;
        isUpdating = true;

        let annual = parseFloat($(this).val()) || 0;

        let monthly = annual / MONTHS_PER_YEAR;
        let hourly = monthly / HOURS_PER_MONTH;

        $('#bill_rate_amount').val(format(hourly));
        $('#bill_monthly').val(format(monthly));
         updateBillUI();
        percentage_hike();

        isUpdating = false;
    });
    function updatePayUI() {
        let currency = $('#pay_rate_currency').val();
        let pay_rate_amount = $('#pay_rate_amount').val();
        let currSet = currencyRateschar[currency];
        if(currency==4){ 
          $('#pay_rate_hourly_usd').text('');
          $('#pay_rate_monthly_usd').text('');
          $('#pay_rate_annual_usd').text('');
          updateCTCFields();
          return false;
        }

        let hourly = fromUSD(pay_rate_amount, currSet);
        let monthly = hourly * HOURS_PER_MONTH;
        let annual = monthly * MONTHS_PER_YEAR;
        
        $('#pay_rate_hourly_usd').text('Approx $ '+ format(hourly));
        $('#pay_rate_monthly_usd').text('Approx $ '+ format(monthly));
        $('#pay_rate_annual_usd').text('Approx $ '+ format(annual));
        updateCTCFields();
    }
    function updateBillUI() {
        let currency = $('#pay_rate_currency').val();
        let bill_rate_amount = $('#bill_rate_amount').val();
        let currSet = currencyRateschar[currency];
        if(currency==4){ 
          $('#bill_rate_hourly_usd').text('');
          $('#bill_rate_monthly_usd').text('');
          $('#bill_rate_annual_usd').text('');
          updateCTCFields();
          return false;
        }

        let hourly = fromUSD(bill_rate_amount, currSet);
        let monthly = hourly * HOURS_PER_MONTH;
        let annual = monthly * MONTHS_PER_YEAR;

        $('#bill_rate_hourly_usd').text('Approx $ '+ format(hourly));
        $('#bill_rate_monthly_usd').text('Approx $ '+ format(monthly));
        $('#bill_rate_annual_usd').text('Approx $ '+ format(annual));
        updateCTCFields();
    }

    $('#pay_rate_amount').on('input', function () {
       
    });


    function payRateUpdate(){

       if (isUpdating) return;
        isUpdating = true;
        // var payamount = $('#pay_rate_amount').val();
        // let hourly = parseFloat(payamount) || 0;
        // $('#pay_monthly').val(format(hourly * HOURS_PER_MONTH));
        // $('#pay_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));

        updatePayUI();

        isUpdating = false;
      
    }

    function billRateUpdate(){

       if (isUpdating) return;
        isUpdating = true;
        // var payamount = $('#bill_rate_amount').val();
        // let hourly = parseFloat(payamount) || 0;

        // $('#bill_monthly').val(format(hourly * HOURS_PER_MONTH));
        // $('#bill_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
        updateBillUI();

        isUpdating = false;
      
    }


    $('#pay_rate_currency').on('change', function () {
        updatePayUI();
        updateBillUI();
    });
    $('#bill_rate_currency').on('change', function () {
        updateBillUI();
        updatePayUI();
    });
    setTimeout(() => {
          billRateUpdate();
          payRateUpdate();
      }, 1000);

    

  });


</script>


<script>

    window.existingSkillExperience = @json($skillExperiences);
		var skills = document.querySelector("#skills");
    var experienceWrapper = document.getElementById("skillExperienceWrapper");

    var tagifyskills =  new Tagify(skills, {
        whitelist: [],
        maxTags: 10,
        transformTag: function(tagData) {
        // Remove special characters, allow only letters, numbers, and spaces
        tagData.value = tagData.value.replace(/[^a-zA-Z0-9 +#\/.]/g, '');
        return tagData;
        },
        validate: function(tagData) {
            // Prevent empty tags after cleaning
            return tagData.value.trim().length > 0 ? true : false;
        },
        dropdown: {
            maxItems: 10,           // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
        }
    });

    /**
 * Create Experience Row
 */

/**
 * Add Experience Block (Edit safe)
 */
function addSkillExperience(skill, data = {}) {

    let skillKey = skill.toLowerCase();

    if (document.getElementById('skill-exp-' + skillKey)) return;

    let html = `
    <div class="col-md-4 mb-3" id="skill-exp-${skillKey}">
        <label class="form-label">${skill} EXPERIENCE</label>
        <div class="row">
            <div class="col-lg-6">
                <select class="form-select form-select-solid"
                        name="experience[${skillKey}][year]">
                    <option value="">Select Year</option>
                    ${generateYears(data.year)}
                </select>
            </div>
            <div class="col-lg-6">
                <select class="form-select form-select-solid"
                        name="experience[${skillKey}][month]">
                    <option value="">Select Month</option>
                    ${generateMonths(data.month)}
                </select>
            </div>
        </div>
    </div>
    `;

    experienceWrapper.insertAdjacentHTML('beforeend', html);
}

/**
 * Helpers (preselect values)
 */
function generateYears(selected = null) {
    let html = '';
    for (let i = 1; i <= 60; i++) {
        html += `<option value="${i}" ${i == selected ? 'selected' : ''}>
                    ${i} Year${i > 1 ? 's' : ''}
                 </option>`;
    }
    return html;
}

function generateMonths(selected = null) {
    let html = '';
    for (let i = 0; i <= 11; i++) {
        html += `<option value="${i}" ${i == selected ? 'selected' : ''}>
                    ${i} Month${i !== 1 ? 's' : ''}
                 </option>`;
    }
    return html;
}

/**
 * Load Existing Skills + Experience
 */
Object.keys(window.existingSkillExperience).forEach(skillKey => {
    let row = window.existingSkillExperience[skillKey];
    addSkillExperience(skillKey.toUpperCase(), {
        year: row.year,
        month: row.month
    });
});

/**
 * Sync add/remove
 */
tagifyskills.on('add', e => {
    addSkillExperience(e.detail.data.value);
});

tagifyskills.on('remove', e => {
    let key = e.detail.data.value.toLowerCase();
    document.getElementById('skill-exp-' + key)?.remove();
});

tagifyskills.value.forEach(tag => {
    addSkillExperience(tag.value);
});





    const fetchSuggestionsSkills = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Skill: ", data);

                if (Array.isArray(data)) {
                    tagifyskills.settings.whitelist = data; // Update whitelist
                    // tagifyskill.dropdown.show(query); // Show suggestions
                    tagifyskills.dropdown.show.call(tagifyskills);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching Skill:", err);
            }
        });
    }, 5); 

    // Listen for user input to trigger API call
    tagifyskills.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 1) return;
        fetchSuggestionsSkills(query);
    });
    
		new Tagify(skills);


    var inputloc = document.querySelector("#kt_tagify_work_location");
    var cities = @json($cities);
                             
    // Initialize Tagify script on the above inputs
    var tagifylocation =  new Tagify(inputloc, {
        whitelist: cities,
        enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true,    // <- do not hide the suggestions dropdown once an item has been selected
            highlightFirst: true // First suggestion active
        }
    });

    // Prevent creating tags on blur or enter key
    tagifylocation.on('input', function(e) {
        tagify.dropdown.show(); // show the dropdown immediately
    });

    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
        tagify.addTags([e.detail.data.value]);
    });
    tagifylocation.on('add', function(e) {
    // Remove the tag if it wasn't selected from the dropdown
      // if (!cities.includes(e.detail.data.value)) {
      //   setTimeout(() => {
      //     tagifylocation.removeTag(e.detail.data.value);
      //   }, 200);
      // }
   });
    
   function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

         // Debounced function to fetch suggestions
    const fetchSuggestions = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Cities: ", data);

                if (Array.isArray(data)) {
                    tagifylocation.settings.whitelist = data; // Update whitelist
                    // tagifylocation.dropdown.show(query); // Show suggestions
                    tagifylocation.dropdown.show.call(tagifylocation);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching cities:", err);
            }
        });
    }, 200); // 300ms delay

    // Listen for user input to trigger API call
    tagifylocation.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions(query);
    });

    setTimeout(() => {
      var city = "{{@$default_loc}}";
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = {!! json_encode(@$default_loc) !!}; // Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
            tagifylocation.dropdown.hide();
        }
    }, 2000); // 3 seconds delay
		
</script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/3.14.3/tagify.css">
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>

$(document).ready(function() {
  $( "#recruiter" ).on( "click", function() {
          
          var client_id 	=	"{{ @$client->id }}"			
          	// alert(client_id);
                  
            if(client_id > 0)
            {
              
                $('#kt_activitiesRecruiter_toggle').trigger('click');   						
                
                var clientName    	=   $("#client_id option:selected"); 
                var text_value 		=	clientName.text();
                // $('#light_recruiter_name_n').html(text_value); 
              var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';						
              str					=	"{{ @$company_name }}";
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
                var recruiter_id	=	$('#recruiter_id').val();
                var token 		=	"{{ csrf_token() }}";
                var id    		=	client_id;
                      var url   		=	"{{ route('service_company.jobs.recruiter',':id') }}"; 
                      url     		=	url.replace(':id', id);  		
                      var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    
                $.ajax({
                      type  		: 'GET',
                      url   		: url,
                      data		: {'_token': token, 'recruiter' : recruiter,'recruiter_id':recruiter_id},
                        beforeSend	: function() {
                              $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                      },
                      success 	: function (response) 
                      {
                            $('#kt_activitiesRecruiter_body').html(response.view);
                      }
                  }); 
                        
            }
            else
            {
              alert("Please select any client after that choose our Recruiters team");
            }	
                                      
  });
});

function rbutton(cvalue)
   {
		$('#np_id').val('');
		$('#ddl_np_id').val('');
		if(cvalue == 0){
			$('#ddl_np_id').show();
			$('#np_id').hide();		
      $('#snp-immediate').hide();
		}else if(cvalue==1){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$('#np_id').attr("disabled", false);	
			$("#np_id").attr('type','date');
      $('#text_display').val('Available from');
			$("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });		
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}else if(cvalue ==2){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$("#np_id").attr('type','date');
			$('#np_id').attr("disabled", false);
			$('#text_display').val('Immediate');
      $("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });	
      $("#snp-immediate").attr('style','display:flex');
      dateManageFunc();
		}	
   }

   $(document).on('change','#np_id',function(){
        var datess = $(this).val();
        if (datess) {
        var dateObj = new Date(datess);
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        var formattedDate = 
            ('0' + dateObj.getDate()).slice(-2) + '/' + 
            months[dateObj.getMonth()] + '/' + 
            dateObj.getFullYear(); // Convert to DD/Mon/YYYY

        document.getElementById('np_id_display').value = formattedDate;
    }
   });
  
         
         

  function dateManageFunc(){
    var radios = document.getElementsByName('serving_notice_period');
         var today = new Date().toISOString().split('T')[0];
          // Loop through each radio button and check which one is selected
          for (var i = 0; i < radios.length; i++) {
              if (radios[i].checked) {
                if (radios[i].value == 1) {
                    // Future dates allowed
                    $('#np_id').removeAttr('max'); // Remove max restriction
                    $('#np_id').attr('min', today); // Set min to today
                    $('#np_id').attr('placeholder', 'Select your last working date (future dates allowed)'); // Set placeholder
                } else {
                    // Past dates allowed
                    $('#np_id').removeAttr('max'); // Remove min restriction
                    $('#np_id').attr('min', today); // Set max to today
                    $('#np_id').attr('placeholder', 'Select your last working date (future  dates allowed)'); // Set placeholder
                }
                  break; // Stop the loop once the selected radio button is found
              }
          }
  }
  dateManageFunc();


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});


     

  
  $(document).on('change', '.change_state', function() {
				var country				=	$('#country').val(); 
   				var state_id			=	this.value;				
				if(country == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').show();	
					$('#city_id').hide();	
				}
				var url 			=	"{{ route('service_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id').html(response.data);
															   }		
				});	
  });  		
  
  $('body').on('change', '#pay_rate_currency', function(){
   			var	pay_rate_currency		=	$(this).val();
			document.getElementById("bill_rate_currency").selectedIndex = pay_rate_currency;
   });
   $('body').on('change', '#bill_rate_currency', function(){
   			var	bill_rate_currency		=	$(this).val();
			document.getElementById("pay_rate_currency").selectedIndex = bill_rate_currency;
   });
   $('body').on('change', '#pay_rate_mode', function(){
   			var	pay_rate_mode	=	$(this).val();
			document.getElementById("bill_rate_mode").selectedIndex = pay_rate_mode;
   });
   $('body').on('change', '#bill_rate_mode', function(){
   			var	bill_rate_mode	=	$(this).val();
			document.getElementById("pay_rate_mode").selectedIndex = bill_rate_mode;
   });
   
  //  $( document ).ready(function() {   
	// 	   $(".serving_notice_period").click(function(){
	// 			   $('#calender').val('');
	// 			   var selected 	=	$(".serving_notice_period:checked");
	// 			   var selVal		=	selected.val();
	// 			   if(selVal == 0){
	// 					$('#np_id').hide();
	// 					$('#ddl_np_id').hide();	
	// 					$('#calender').show();
	// 			   }else if(selVal == 1){
	// 					$('#np_id').hide();
	// 					$('#ddl_np_id').hide();	
	// 					$('#calender').show();
	// 			   }else if(selVal == 2){
	// 					$('#np_id').show();	
	// 					$('#ddl_np_id').hide();
	// 					$('#calender').hide();		
	// 			   } 
	// 	   }); 	
  //   });
   
  	$(document).on('click', '.formEditMode', function(){
	
				var id    			=	$(this).data('row-id');
				var sno    			=	$(this).data('row-sno');
				var foamStatus		=	$(this).hasClass('form--disabled'); 
				if(foamStatus == true){
					$(this).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/><path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/></g></svg>');
					$(this).removeClass('form--disabled').addClass('form--enabled');	
					$('#form_lavel_input_'+id+'_'+sno).removeClass('form--disabled').addClass('form--enabled');
				}else{
					$(this).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path><rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect></g></svg>'); 
					$(this).removeClass('form--enabled').addClass('form--disabled');
					$('#form_lavel_input_'+id+'_'+sno).removeClass('form--enabled').addClass('form--disabled');
				}			
	});
    
</script>
<script>

	$('#add_custom_detail').click(function(){
						var customTitleId			=	$('#customTitleId').val();
						customTitleId++;
						$('#customTitleId').val(customTitleId);
						$('#customDetailLoader').show();
						$('#customDetailLoader').html('');
						var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 	
						var counter				=	customTitleId ;
						var url 				=	"{{ route('service_company.bench-candidates.add_custom_detail',':counter') }}";
						url 					=	url.replace(':counter', counter);
						$.ajax({
									type		: 'GET',
									url			: url,
									beforeSend	: function() {
																			$('#customDetailLoader').html('<img src="'+baseurl+'" height="35px" width="100%">');
															},
									success		: function (response) {																		
																			$('#customDetailLoader').hide();
																			$('.all_custom_details').last().append(response.data);
																	  }		
						});				
	});
	$('#remove_custom_detail').click(function(){
   			$('.all_custom_details').children().last().remove();
   	});
	  
</script>
<script>   

"use strict";


// set the dropzone container id
const id = "#kt_dropzonejs_example_3";
      const dropzone = document.querySelector(id);

      // set the preview element template
      var previewNode = dropzone.querySelector(".dropzone-item");
      previewNode.id = "";
      var previewTemplate = previewNode.parentNode.innerHTML;
      previewNode.parentNode.removeChild(previewNode);

      var myDropzone = new Dropzone(id, { // Make the whole body a dropzone
          url: "{{ url('service_company/uploadresume') }}", // Set the url for your upload script location
          parallelUploads: 20,
          maxFilesize: 5, // Max filesize in MB
          maxFiles: 1, // Only allow one file
          dictMaxFilesExceeded: "You can only upload one file. Please delete the existing file before adding a new one.", // 
          previewTemplate: previewTemplate,
          previewsContainer: id + " .dropzone-items", // Define the container to display the previews
          clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
          headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Pass CSRF token
          }
      });

      myDropzone.on("addedfile", function (file) {
          // Hookup the start button
          const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
          const existingFile = dropzone.querySelector('.dropzone-item.dz-processing, .dropzone-item.dz-success, .dropzone-item.dz-complete');
          if (existingFile) {
                // Remove the newly added file preview
                myDropzone.removeFile(file);
                Swal.fire("Success","You can only upload one resume. Please delete the existing resume before adding a new one" , "warning");	
                // Show a message to the user
            }else{
              dropzoneItems.forEach(dropzoneItem => {
              dropzoneItem.style.display = '';
              });
            }
      });

      // Update the total progress bar
      myDropzone.on("totaluploadprogress", function (progress) {
          const progressBars = dropzone.querySelectorAll('.progress-bar');
          progressBars.forEach(progressBar => {
              progressBar.style.width = progress + "%";
          });
      });

      myDropzone.on("sending", function (file) {
          // Show the total progress bar when upload starts
          const progressBars = dropzone.querySelectorAll('.progress-bar');
          progressBars.forEach(progressBar => {
              progressBar.style.opacity = "1";
          });
      });

      // Handle successful file upload and update the file input
        myDropzone.on("success", (file, response) => {
            if (response.success) {
                // Update the file input field with the uploaded file path
                // document.querySelector('#resume').value = response.file_path; // Adjust `file_path` based on server response
                console.log('File uploaded successfully:', response.file_path);
                // Create a new File object with the uploaded file details
                document.querySelector('#uploaded_file_path').value = response.file_path;
                const uploadedFile = new File([file], response.original_name, {
                      type: file.type,
                      lastModified: new Date().getTime(),
                  });
                  // Use DataTransfer to simulate a FileList
                  const dataTransfer = new DataTransfer();
                  dataTransfer.items.add(uploadedFile);

                  // Get the file input element and set its files property
                  const inputElement = document.querySelector('#resume');
                  inputElement.files = dataTransfer.files;

                  // Trigger the file input change event manually
                  const inputEvent = new Event('change', { bubbles: true });
                  inputElement.dispatchEvent(inputEvent);
            } else {
                console.error('File upload failed:', response.message);
                alert('File upload failed: ' + response.message);
            }
        });


      // Hide the total progress bar when nothing"s uploading anymore
      myDropzone.on("complete", function (progress) {
          const progressBars = dropzone.querySelectorAll('.dz-complete');

          setTimeout(function () {
              progressBars.forEach(progressBar => {
                  progressBar.querySelector('.progress-bar').style.opacity = "0";
                  progressBar.querySelector('.progress').style.opacity = "0";
              });
          }, 300);
      });

      // Handle upload errors
  myDropzone.on("error", (file, errorMessage) => {
      console.error("Upload error:", errorMessage);
      // alert("An error occurred during file upload. Please try again.");
  });

  function existingFileNameFunc(existingFileName) {
              // Add an existing file to the Dropzone
              $('.dropzone-items').append('<div class="dropzone-item dz-processing dz-success dz-complete" style="" id="">                                  <!--begin::File-->                                  <div class="dropzone-file">                                      <div class="dropzone-filename" title="some_image_file_name.jpg">                                          <span data-dz-name="">'+existingFileName+'</span>                                          <strong><span data-dz-size=""><strong></strong> </span></strong>                                      </div>                                      <div class="dropzone-error" data-dz-errormessage=""></div>                                  </div>                                  <!--end::File-->                                  <!--begin::Progress-->                                  <div class="dropzone-progress">                                      <div class="progress" style="opacity: 0;">                                          <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress="" style="opacity: 0; width: 100%;">                                          </div>                                      </div>                                  </div>                                  <!--end::Progress-->                                  <!--begin::Toolbar-->                                  <div class="dropzone-toolbar">                                      <span class="dropzone-delete dropzone-delete-cus" data-dz-remove=""><i class="bi bi-x fs-1"></i></span>                                  </div>                                  <!--end::Toolbar-->                              </div>')   
          }

         
          setTimeout(() => {
              var existingFileUrl = "{{ $url }}";
              var resume_url = "{{ $resume_url }}";
              // var existingFileName = "Jero_UMESH_SADA.docx"; // Name of the file
              if (resume_url && resume_url.trim() !== "") {
              var existingFileName = existingFileUrl.split('/').pop(); // Extract the last part of the URL
              var existingFileSize = 12345; // Approximate file size in bytes (use real size if known)
              if (existingFileName) {
                    existingFileNameFunc(existingFileName);
                } else {
                    console.error("Filename is empty or not present in the URL.");
                }
              } else {
                    console.log("Resume URL is empty");
            }
           
          }, 500);

           setInterval(() => {
      
              const existingFilewww = dropzone.querySelector('.dropzone-item.dz-processing, .dropzone-item.dz-success, .dropzone-item.dz-complete');

                if (existingFilewww) {
                      $("#resumeCheck").val('1');
                }else{
                  $("#resumeCheck").val('');
                }

          }, 500);


          $(document).on('click','.dropzone-delete',function(){
            $('.dropzone-items').html('');
          })



@php if(@$page_event != 'EDIT'){  @endphp
var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;
    var handleForm = function (e) {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {					
                    		'full_name'				:	{validators:{notEmpty: {message: "Full name is required"}} },
                    		'current_designation'	:	{validators:{notEmpty:{message: "Current designation is required"}} },
                    		'outsourcing_type'		:	{validators:{notEmpty:{message: "Outsource type is required"}} },
                    		'phone'					:
														{
															validators:
															{
																notEmpty:{message: "Primary number is required"},
																digits:{message: "Primary number must contains only digits"},
																stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}
															}
														},
							'secondary_mobile'					:
														{
															validators:
															{
																digits:{message: "Primary number must contains only digits"},
																stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}
															}
														},
							'email'					:	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    		'qualification'			:	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    		'total_exp'				:	{validators:{ notEmpty:{message:"Total Experience is required"},	digits:	{message: "Total Experience must contain only digits"} }},
					
                    		'rel_exp'				:	{validators:{ notEmpty:{message:"Relevant Experience is required"},digits:{message: "Relevant Experience must contain only digits"}}},
                    		'pay_rate' 				:	{validators:{notEmpty:{message: "Pay rate is required"},digits:{message: "Pay rate must containts only digits"}}},
							'bill_rate' 			:	{validators:{notEmpty:{message: "Bill rate is required"},digits:{message: "Bill rate must contains only digits"}}},
                    		'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}},			
							// 'resume':
              //       {
              //         validators:
              //           {
              //               notEmpty: { message: "Resume is required" },
              //               file: {
              //                   maxSize: 5242880, // 5 MB in bytes
              //                   message: "The file size must not exceed 5 MB",
              //                   type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
              //                   message: "Only PDF, DOC, or DOCX files are allowed"
              //               }
              //           }
              //   },
                'photo':
                    {
                      validators:
                        {
                            file: {
                                maxSize: 5242880, // 5 MB in bytes
                                message: "The file size must not exceed 5 MB"
                            }
                        }
                   }, 
              'recruiter'					:	{validators:{ notEmpty: { message: "Assign internal team/recruiter is required" } }  },
				   			'skills'				:	{ validators: { notEmpty: {  message: 'Skills is required' } }},
                    		'specialties'			:	{validators:{ notEmpty:{message: "Specialties is required"}}}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );
        submitButton.addEventListener('click', async function (e) {
            e.preventDefault();
             const connected = await isInternetAvailable();
              if (!connected) {
                  Swal.fire({
                      title: "No Internet",
                      text: "You are currently offline or the server is unreachable.",
                      icon: "warning",
                      confirmButtonText: "Ok, got it!",
                      customClass: {
                          confirmButton: "btn btn-warning"
                      }
                  });
                  return; // ⛔ STOP EXECUTION
              }

            validator.validate().then(function (status) {
                if (status == 'Valid') {
                  $("#save-form").prop("disabled", true);
                  $("#save-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    setTimeout(function () {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = true;


													var formData 	=	new FormData($('#createForm')[0]);							
													$.ajax({
															   url 				: '{{route('service_company.bench-candidates.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
															   type 			: 'POST',
															   data 			: formData,
															   processData		: false,  	
															   contentType		: false,  	
															   redirect			: true,
																success			: function (response) {
                                  $("#save-form").prop("disabled", false);
                                  if(response.status == 'coinCheck'){
   																	Swal.fire("Success",` ${response.message}` , "warning");	
                                    return false;
   																}

                                  
                                if(response.status == 'mailcheck'){
                                   $("#save-form").html(`Submit`);
   																	Swal.fire("Success", response.message , "warning").then(function(){
																	});	
                                   return false;
                                }
                                
                                  
																
																					 Swal.fire({
                            text: "You have successfully Submitted",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.querySelector('[name="email1"]').value 	= "";
                                form.querySelector('[name="password1"]').value 	= "";
                            }
                        });
																					window.location = response.url;
																				},
																error			: function (response) {
																				}
														});
														
                       
                    }, 500);
                } else {
							Swal.fire({
										text				: "Sorry, looks like there are some errors detected, please try again.",
										icon				: "error",
										buttonsStyling		: false,
										confirmButtonText	: "Ok, got it!",
										customClass			: {
																	confirmButton: "btn btn-primary"
															  }
							});
                }
            });
        });
    }
    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#save-form');
										handleForm();
								  }
    	   };
}();

KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});
@php } @endphp
@php if(@$page_event == 'EDIT'){  @endphp
var KTEditCandidate = function () {
    var form;
    var submitButton;
    var validator;
    var handleForm = function (e) {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {					
                    		'full_name'				:	{validators:{notEmpty: {message: "Full name is required"}} },
                    		'current_designation'	:	{validators:{notEmpty:{message: "Current designation is required"}} },
                    		'outsourcing_type'		:	{validators:{notEmpty:{message: "Outsource type is required"}} },
                    		'phone'					:
														{
															validators:
															{
																notEmpty:{message: "Primary number is required"},
																digits:{message: "Primary number must contains only digits"},
																stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}
															}
														},
							'secondary_mobile'					:
														{
															validators:
															{
																digits:{message: "Primary number must contains only digits"},
																stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}
															}
														},
							'email'					:	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    		'qualification'			:	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    		'total_exp'				:	{validators:{ notEmpty:{message:"Total Experience is required"} }},
					
                    		'rel_exp'				:	{validators:{ notEmpty:{message:"Relevant Experience is required"}}},
                    		'pay_rate' 				:	{validators:{notEmpty:{message: "Pay rate is required"},digits:{message: "Pay rate must containts only digits"}}},
							'bill_rate' 			:	{validators:{notEmpty:{message: "Bill rate is required"},digits:{message: "Bill rate must contains only digits"}}},
                    		'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}},		
							// 'resume':
              //       {
              //         validators:
              //           {
              //               file: {
              //                   maxSize: 5242880, // 5 MB in bytes
              //                   message: "The file size must not exceed 5 MB",
              //                   type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
              //                   message: "Only PDF, DOC, or DOCX files are allowed"
              //               }
              //           }
              //   },
                'photo':
                    {
                      validators:
                        {
                            file: {
                                maxSize: 5242880, // 5 MB in bytes
                                message: "The file size must not exceed 5 MB"
                            }
                        }
                   },
              'recruiter'					:	{validators:{ notEmpty: { message: "Assign internal team/recruiter is required" } }  }, 
				   			'skills'				:	{ validators: { notEmpty: {  message: 'Skills is required' } }},
                    		'specialties'			:	{validators:{ notEmpty:{message: "Specialties is required"}}}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                  $("#edit-form").prop("disabled", true);
                  $("#edit-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    setTimeout(function () {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = true;


													var formData 	=	new FormData($('#createForm')[0]);							
													$.ajax({
															   url 				: '{{route('service_company.bench-candidates.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
															   type 			: 'POST',
															   data 			: formData,
															   processData		: false,  	
															   contentType		: false,  	
															   redirect			: true,
																success			: function (response) {
                                  $("#edit-form").prop("disabled", false);
                                  if(response.status == 'mailcheck'){
                                   $("#edit-form").html(`Update`);
   																	Swal.fire("Success", response.message , "warning").then(function(){
																	});	
                                   return false;
                                }
                                
																					 Swal.fire({
                            text: "You have successfully Submitted",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.querySelector('[name="email1"]').value 	= "";
                                form.querySelector('[name="password1"]').value 	= "";
                            }
                        });
																					window.location = response.url;
																				},
																error			: function (response) {
																				}
														});
														
                       
                    }, 500);
                } else {
							Swal.fire({
										text				: "Sorry, looks like there are some errors detected, please try again.",
										icon				: "error",
										buttonsStyling		: false,
										confirmButtonText	: "Ok, got it!",
										customClass			: {
																	confirmButton: "btn btn-primary"
															  }
							});
                }
            });
        });
    }
    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#edit-form');
										handleForm();
								  }
    	   };
}();

KTUtil.onDOMContentLoaded(function () {
    KTEditCandidate.init();
});
@php } @endphp

function re_test(text){
  $('.page-loader').css('display', 'flex');
   var page_event_check = "{{@$page_event}}";
$.ajax({
                  type		: 'POST',
                  url			: "{{ route('service_company.extract.resume-job-applications',':id') }}",
                  data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'resumeText' : text},
                  beforeSend	: function() {
                  },
                  success 	: function (response) {
                    $('.page-loader').css('display', 'none');
                    if(response.linkedin !=''){
                      $('#linkedin').val(response.linkedin);
                    }
                    if(response.github !=''){
                       $('#github').val(response.github);
                    }
                    if(response.behance !=''){
                       $('#behance').val(response.behance);
                    }
                    if(page_event_check != 'EDIT'){
                    $('#full_name').val(response.name);
                    $('#phone').val(response.number);
                    $('#email').val(response.email);
                    $('#dob').val(response.dob);
                    $('#qualification').val(response.qualification).change();

                    var resumeText = $('#uploaded_file_path').val();
                      $.ajax({
                        type		: 'POST',
                        url			: "{{ route('service_company.file.unset') }}",
                        data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'file_path' : resumeText},
                        success 	: function (response) {

                        }

                      });


                    tagifyskill.removeAllTags(); // Clear existing tags first
                    tagifyskill.addTags(response.skills); // Add new tags

                    tagifylocation.removeAllTags(); // Clear existing tags first
                    tagifylocation.addTags(response.country); // Add new tags

                    
                    

                    var countryName = response.country;
                    var stateName = response.state;
                    var cityName = response.city;
                    
                    var countrydropdown = document.getElementById("country");
                    for (var i = 0; i < countrydropdown.options.length; i++) {
                     
                        if (countrydropdown.options[i].text.trim() === countryName) {
                            // Set the option as selected
                            countrydropdown.selectedIndex = i;
                            var event = new Event('change');
                            countrydropdown.dispatchEvent(event);
                            break; // Exit the loop since we found the country
                        }
                    }
                   
                  setTimeout(() => {
                    var statedropdown = document.getElementById("state");
                    for (var ik = 0; ik < statedropdown.options.length; ik++) {
                        if (statedropdown.options[ik].text.trim() === stateName) {
                            //Set the option as selected Uttar Pradesh Uttar Pradesh
                            statedropdown.selectedIndex = ik;
                            var eventstate = new Event('change');
                            statedropdown.dispatchEvent(eventstate);
                            break; // Exit the loop since we found the state
                        }
                    }   
                  }, 1000);
               }
                   
                    
                    // $('.value_JSON').prepend('<tag title="dd" contenteditable="false" spellcheck="false" tabindex="-1" class="tagify__tag " value="dd"><x title="" class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><div><span class="tagify__tag-text">dd</span></div></tag>');

                    }
             });	

}




    //  $(document).on('keyup', '#pay_rate_amount', function() { 
    //         var number = parseInt($('#pay_rate_amount').val());
    //         var result = convertToWords(number);
    //             $('.payrate').text(capitalizeFirstLetter(result));

    //  });
    //  $(document).on('keyup', '#bill_rate_amount', function() { 
    //         var number = parseInt($('#bill_rate_amount').val());
    //         var result = convertToWords(number);
    //             $('.billrate').text(capitalizeFirstLetter(result));
    //             percentage_hike();

    //  });

    function numberToIndianWords(num) {

    num = Math.floor(Number(num));

    if (!num || isNaN(num)) return '';

    const ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
        'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];

    const tens = [
        '', '', 'Twenty', 'Thirty', 'Forty',
        'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    function convert(n) {

        if (n < 20) {
            return ones[n];
        }

        if (n < 100) {
            return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
        }

        if (n < 1000) {
            return ones[Math.floor(n / 100)] + ' Hundred ' + convert(n % 100);
        }

        if (n < 100000) {
            return convert(Math.floor(n / 1000)) + ' Thousand ' + convert(n % 1000);
        }

        if (n < 10000000) {
            return convert(Math.floor(n / 100000)) + ' Lakh ' + convert(n % 100000);
        }

        if (n < 1000000000) {
            return convert(Math.floor(n / 10000000)) + ' Crore ' + convert(n % 10000000);
        }

        return convert(Math.floor(n / 1000000000)) + ' Arab ' + convert(n % 1000000000);
    }

    return convert(num).replace(/\s+/g, ' ').trim() + ' Rupees';
}


     function updateCTCFields() {
                // Current CTC
                
                let maxLimit = 99999999999;

                var ctcVal = parseInt($("#pay_rate_amount").val(), 10);
                var payMonthly = parseInt($("#pay_monthly").val(), 10);
                var payAnnual = parseInt($("#pay_annual").val(), 10);
                var ctcCurrency = $(".currency").val();

                if (Number(payAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#pay_annual').val(maxLimit);
                    $(".payrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }

                if (!isNaN(payAnnual)) {
                    var ctcWords = window.numberToWords(ctcVal, ctcCurrency);
                    var payMonthlyWords = window.numberToWords(payMonthly, ctcCurrency);
                    var payAnnualWords = window.numberToWords(payAnnual, ctcCurrency);
                    
                    
                    $(".payrateannual").text(numberToIndianWords(payAnnual));
                    $(".payratemonthly").text(numberToIndianWords(payMonthly));
                    $(".payratehourly").text(numberToIndianWords(ctcVal));

                    // $(".payratehourly").text(ctcWords.charAt(0).toUpperCase() + ctcWords.slice(1));
                    // $(".payratemonthly").text(payMonthlyWords.charAt(0).toUpperCase() + payMonthlyWords.slice(1));
                    // $(".payrateannual").text(payAnnualWords.charAt(0).toUpperCase() + payAnnualWords.slice(1));
                }else{
                    $(".payrateannual").text(' ');
                    $(".payratemonthly").text(' ');
                    $(".payratehourly").text(' ');
                }

                // Expected CTC
                var ectcVal = parseInt($("#bill_rate_amount").val(), 10);
                var billMonthly = parseInt($("#bill_monthly").val(), 10);
                var billAnnual = parseInt($("#bill_annual").val(), 10);

                
                if (Number(billAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#bill_annual').val(maxLimit);
                    $(".billrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }

                var ectcCurrency = $(".currency").val();
                if (!isNaN(billAnnual)) {
                    var ectcWords = window.numberToWords(ectcVal, ectcCurrency);
                    var billMonthlyWords = window.numberToWords(billMonthly, ctcCurrency);
                    var billAnnualWords = window.numberToWords(billAnnual, ctcCurrency);
                    
                    $(".billrateannual").text(numberToIndianWords(billAnnual));
                    $(".billratemonthly").text(numberToIndianWords(billMonthly));
                    $(".billratehourly").text(numberToIndianWords(ectcVal));
                    //$(".billratehourly").text(ectcWords.charAt(0).toUpperCase() + ectcWords.slice(1));
                    //$(".billratemonthly").text(billMonthlyWords.charAt(0).toUpperCase() + billMonthlyWords.slice(1));
                    // $(".billrateannual").text(billAnnualWords.charAt(0).toUpperCase() + billAnnualWords.slice(1));
                }else{
                    $(".billrateannual").text(' ');
                    $(".billratemonthly").text(' ');
                    $(".billratehourly").text(' ');
                }
    }
    setTimeout(() => {
          updateCTCFields();
    }, 500);
    $(document).on("keyup", "#pay_rate_amount", updateCTCFields);
    $(document).on("keyup", "#bill_rate_amount", updateCTCFields);
    $(".currency").on("change", updateCTCFields);

    $(document).on('keyup', '#bill_rate_amount', function() { 
      percentage_hike()
     });
     $(document).on('keyup', '#pay_rate_amount', function() { 
      percentage_hike()
     });

     $(document).on('keyup', '#percentage_hike', function() { 
      ectc();

     });


$(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


     function percentage_hike(){
     
     var currentCTC = parseFloat($('#pay_rate_amount').val());
     var expectedCTC = parseFloat($('#bill_rate_amount').val());
     
     var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;

     if (currentCTC > 0) {
        $('#percentage_hike').val(percentageIncrease.toFixed(0));
        }else{
          $('#percentage_hike').val('');
      }
 }

 function ectc(){
     var currentCTC = parseFloat($('#pay_rate_amount').val());
     var percentage_hike = $('#percentage_hike').val();
     var amount = currentCTC*percentage_hike/100;
     $('#bill_rate_amount').val(amount+currentCTC);
     
 }


  


function total_exp_and_rel_exp(){
        var total_exp = parseFloat($('#total_exp').val());
        var rel_exp = parseFloat($('#rel_exp').val());
        if(rel_exp>total_exp){
       $('#rel_exp_error').text('Relevant Experience can not be higher then Total Experience');
       setTimeout(() => {
        $('#rel_exp').val('');
       }, 1000);
         
    }else{
      $('#rel_exp_error').text('');
    }
             
    }

    function normalizeResumeText(text) {
        return text
            .replace(/[^\x00-\x7F]/g, " ")   // remove broken unicode
            .replace(/\s+/g, " ")
            .replace(/\|/g, " ")
            .trim();
    }



    function handleFileInputChange(event) {
      const file = event.target.files[0];
      const fileExtension = file.name.split('.').pop().toLowerCase();

if (file.type === 'application/pdf' || fileExtension === 'pdf') {
      if (file) {
        const reader = new FileReader();
        reader.onload = async function(event) {
          const typedArray = new Uint8Array(event.target.result);
          const pdfDocument = await pdfjsLib.getDocument(typedArray).promise;
          const numPages = pdfDocument.numPages;
          let resumeText = '';
          // Loop through each page to extract text

          for (let pageNum = 1; pageNum <= numPages; pageNum++) {
            try {
              const page = await pdfDocument.getPage(pageNum);
              const textContent = await page.getTextContent();
              const pageText = textContent.items.map(item => item.str).join('\n');
              resumeText += pageText + '\n';
            } catch (error) {
              console.error(`Error extracting text from page ${pageNum}:`, error);
            }
          }

          // for (let pageNum = 1; pageNum <= numPages; pageNum++) {
          //   const page = await pdfDocument.getPage(pageNum);
          //   const textContent = await page.getTextContent();
          //   const pageText = textContent.items.map(item => item.str).join('\n');
          //   resumeText += pageText + '\n';
          // }

          // alert(resumeText);
          // Display extracted text
          // document.getElementById('output').textContent = resumeText;

          const cleanText = normalizeResumeText(resumeText);
          re_test(cleanText);


          // Extract name, email, mobile

            // Regular expressions to extract name, mobile number, email
            const nameRegex = /^[A-Z\s]+/i;
            const mobileRegex = /(?:\+?\d{2}\s*-\s*\d{10})|(?:\(\d{3}\)-\d{3}-\d{3})/;
            const emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/;

            // Extracting information
            const name = resumeText.match(nameRegex)[0].trim();
            const mobile = resumeText.match(mobileRegex)[0].replace(/\D/g, '');
            const email = resumeText.match(emailRegex)[0];

            console.log("Name:", name);
            console.log("Mobile:", mobile);
            console.log("Email:", email);
        };
        reader.readAsArrayBuffer(file);
      }
    }else{
        if (file) {
        mammoth.extractRawText({ arrayBuffer: file })
          .then(function(result) {
            const resumeText = result.value; // Extracted text
            // Display extracted text
            // document.getElementById('output').textContent = resumeText;
            const cleanText = normalizeResumeText(resumeText);
            re_test(cleanText);

            // Extract name, email, mobile, and skills (similar to the PDF example)
            const nameRegex = /([A-Z]+ [A-Z]+) \w+/i;
            const emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/;
            const mobileRegex = /(?:\+?\d{2}\s*-\s*\d{10})|(?:\(\d{3}\)-\d{3}-\d{3})/;
            
            const nameMatch = resumeText.match(nameRegex);
            const name = nameMatch ? nameMatch[1].trim() : '';

            const emailMatch = resumeText.match(emailRegex);
            const email = emailMatch ? emailMatch[0] : '';

            const mobileMatch = resumeText.match(mobileRegex);
            const mobile = mobileMatch ? `${mobileMatch[1]}-${mobileMatch[2]}-${mobileMatch[3]}` : '';

            var numericDigits = mobile;
            if(mobile.length > 10){
              numericDigits = mobile.slice(2);
            }

             

            console.log('Name:', name);
            console.log('Email:', email);
            console.log('Mobile:', mobile);
          })
          .catch(function(err) {
            console.log('Error:', err);
          });
      }
    }
    }

    // Attach event listener to file input
    document.getElementById('resume').addEventListener('change', handleFileInputChange);



   

   
</script>
@endpush