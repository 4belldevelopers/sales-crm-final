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


<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

@endpush

@section('content')

@php 	
	$country_code =  GeneralMethod::countryCodeFun();
  $currency_code =  GeneralMethod::defaultCurrency();

  $phone_code = $country_code;
  $second_phone_code = $country_code;
  $url = '';

  $resume_url				=	'';	
  $photo  					=	url('public/avatar.png');	
  $resumeCheck = '';
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
        // dd( $url,pathinfo($application->resume, PATHINFO_EXTENSION));
        // $info = pathinfo($url);
        if (!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf', 'doc', 'docx'])) {
          $resume_url			=	'<a href="'.route('download.resume', ['id' => $application->id,'path' => urlencode($url)]).'" target="_blank" download style="color: #fff;"><i class="fa fa-download" aria-hidden="true" style="color: #fff;"></i></a>';
          $resumeCheck = 1;
        }
        
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
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Inhouse Talent details and submit</span> 
    </div>

  
      <div class="d-flex align-items-center py-1">
          <div class="before-event d-flex align-items-center position-relative my-1 me-3">
            <!--begin::Buttons-->
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                  @php 
                      if(@$page_event == 'EDIT'){ 
                        echo '<button type="button" name="edit-form" id="edit-form" value="update" class="btn btn-primary">Update Candidate</button>';
                      }else{
                        echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit Candidate</button>';	
                      }
                  @endphp
                    {{-- <a href="javascript:void(0);" class="btn btn-secondary">Cancel</a>  --}}
                  
                  </div>
                </div>
          </div>
      </div>
    <!--end::Page title-->

  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="app">
       
        <!-- MAIN -->
        <main class="mc">

          
          <!-- Form -->
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="edit_token" value="{{ @$application->id }}"/>
            <input type="hidden" name="avatar_remove" />
            <input type="hidden" name="resumeCheck" value="{{ @$resumeCheck }}" id="resumeCheck" />
          <div class="fc">
            <!-- Upload + Career Summary -->
            <div class="g2" style="margin-bottom:16px">
                <!-- Resume Upload -->
                <div class="fs fv-row" style="margin-bottom:0">
                    <div class="fsh">
                        <h3>Upload Resume <span style="color:red">*</span></h3>
                    </div>

                    <div class="fsc">
                        <div class="ua" id="uploadBox">

                            <div class="ui">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </div>

                            <div class="ut">Drag & Drop Resume Here</div>

                            <div class="us">or</div>

                            <button class="ub" type="button" id="chooseFileBtn">
                                Choose File
                            </button>

                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display:none;">

                            <div class="uf">
                                Supports PDF, DOC, DOCX (Max 10MB)
                            </div>

                            <!-- Selected File -->
                            <div class="file-info" id="fileInfo" style="{{ !empty($resume_url) ? 'display:flex' : 'display:none' }}">
                                <span class="file-name" id="fileName">
                                  {{ !empty($application->resume) ? basename($application->resume) : '' }}
                                </span>
                                @if(!empty($resume_url))
                                 <button type="button"
                                        class="download-file"
                                        id="" style="color: #fff; margin-left: 250px;">
                                    {!! $resume_url !!}
                                </button>
                                @endif

                                <button type="button"
                                        class="remove-file"
                                        id="removeFile">
                                    Remove
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="fs fv-row" style="margin-bottom:0">
                  <div class="fsh" style="justify-content:space-between">
                    <h3>Career
                      Summary <span style="color:var(--r)">*</span></h3>
                  </div>
                  <div class="fsc">
                    <textarea placeholder="Enter career summary..." id="careersummary" name="careersummary" rows="10">{{ @$application->careersummary }}</textarea>
                    <div class="cc">0 / 100</div>
                    <div class="tip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                      </svg><span>Tip: A good summary helps the client
                        understand the candidate quickly.</span></div>
                  </div>
                </div>
            </div>
            
            <!--begin::Upload Photo-->
            <div class="form-group row fv-row mb-10 d-none">
              <div class="col-lg-4 col-xl-4 text-center">
                <!--begin::Image input-->
                <div class="image-input image-input-outline" data-kt-image-input="true">
                  <!--begin::Image preview wrapper-->
                  <div class="image-input-wrapper w-90px h-90px" style="background-image: url({!! @$photo !!})"></div>
                  <!--end::Image preview wrapper-->
                  <!--begin::Edit button-->
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                  <!--begin::Inputs-->
                  <input type="file" name="photo" id="avatar" accept=".png, .jpg, .jpeg" />
                  <input type="hidden" name="avatar_remove" />
                  <!--end::Inputs-->
                  </label>
                  <!--end::Edit button-->
                  <!--begin::Cancel button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Cancel button-->
                  <!--begin::Remove button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Remove button-->
                </div>
                <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                <!--end::Image input-->
              </div>
              <div class="col-lg-8 col-xl-8">
                {{-- <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" > --}}
                <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.. &nbsp; {!! @$resume_url !!}</span> </div>
                <!--end::Input group-->
              </div>
            </div>
            <!--end::Upload Photo & Resume/CV-->

            <!-- Basic Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Basic Details</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Full Name <span class="rq">*</span></label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}"
                      placeholder="Enter full name" />
                  </div>
                  <div class="fg fv-row">
                    <label>Gender</label>
                    <select name="gender" id="gender">
                      <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather
                        Not Say</option>
                      <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Nationality</label>
                    <select name="nationality" id="nationality">
                      <option value="">Select nationality</option>
                      <option {{ (@$application->nationality==='Indian') ? 'selected' : '' }}>Indian</option>
                      <option {{ (@$application->nationality==='American') ? 'selected' : '' }}>American</option>
                      <option {{ (@$application->nationality==='British') ? 'selected' : '' }}>British</option>
                      <option {{ (@$application->nationality==='Other') ? 'selected' : '' }}>Other</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()"
                      placeholder="Select date" />
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Enter Primary Email ID <span class="rq">*</span></label>
                    <input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email" />
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Secondary Email ID</label>
                    <input type="email" name="secondrary_email" id="secondrary_email"
                      value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" />
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Primary Number <span class="rq">*</span></label>
                    <div class="ig">
                      {{-- <span class="ig-p">+91 ▼</span> --}}
                      <input type="number" name="phone" id="phone" value="{{ @$application->phone }}"
                        placeholder="Enter primary number" style="padding-left: 76px !important; width: 307px;" />
                      <input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code">
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Secondary Number</label>
                    <div class="ig">
                      {{-- <span class="ig-p">+91 ▼</span> --}}
                      <input type="number" name="secondary_mobile" id="secondary_mobile"
                        value="{{ @$application->secondary_mobile }}" placeholder="Enter secondary number"
                        style="padding-left: 76px !important; width: 307px;" />
                      <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code"
                        name="secondary_country_code">
                    </div>
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row" style="grid-column: span 2">
                    <label>Current Location <span class="rq">*</span></label>
                        <input type="text" class="" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter current location" />
                  </div>
                  <div class="fg fv-row">
                    <label>Highest Education <span class="rq">*</span></label>
                    <select name="qualification" id="qualification">
                      <option value="">Select Qualification</option>
                      @foreach($qualification as $data_value)
                      @php
                      $sel = '';
                      if(@$application->qualification == $data_value->id ){
                      $sel = 'selected="selected"';
                      }
                      @endphp
                      <option value="{{ @$data_value->id }}" {{ @$sel }}>{{ @$data_value->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Preferred Work Mode <span class="rq">*</span></label>
                    <div class="rg">
                      <div class="ri"><input type="radio" name="wm" id="wr" {{ @$application->wm == 'remote' ? 'checked' : '' }} value="remote" />
                        <label for="wr">Remote</label>
                      </div>
                      <div class="ri">
                        <input type="radio" name="wm" id="wh" {{ (@$application->wm == 'hybrid' || empty(@$application->wm)) ? 'checked' : '' }} value="hybrid" checked />
                        <label for="wh">Hybrid</label>
                      </div>
                      <div class="ri">
                        <input type="radio" name="wm" id="wo" {{ @$application->wm == 'office' ? 'checked' : '' }} value="office" />
                        <label for="wo">Work From Office</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs fv-row d-none">
              <div class="fsh"><h3>Primary Skills *</h3></div>
              <div class="fsc">
              <div id="skillTags" class="skill-tags"></div>
               <input type="text" class="" value="{{ @$application->skills }}" placeholder="Enter skills" id="skills" name="skills" />
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Professional Details</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Current Company <span class="rq">*</span></label>
                    <input type="text" name="current_company" id="current_company" value="{{ @$application->current_company }}" placeholder="Enter current company" />
                  </div>
                  <div class="fg fv-row">
                    <label>Current Designation <span class="rq">*</span></label>
                    <input type="text" name="current_designation" id="current_designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}" placeholder="Enter current designation" />
                  </div>
                  <div class="fg fv-row">
                    <label>Total Experience <span class="rq">*</span></label>
                    <div class="ei">
                      @php
                       $value = @$application->total_exp; // Convert to string to preserve decimal
                       if(!is_null($value)){
                        $total_exp =  GeneralMethod::separateNumberParts($value);
                        $exp_years = ($total_exp[0]) ;
                        $exp_months = ($total_exp[1]); 
                      
                        $selected_exp_year = ($exp_years) ;
                        $selected_exp_month = ($exp_months); 
                       }else{
                        $selected_exp_year = null ;
                        $selected_exp_month = null; 
                       }
                      @endphp

                      <select id="total_exp_year" name="total_exp_year">
                        <option value="">Years</option>
                        <option value="0" {{ $selected_exp_year==0 ? 'selected' : '' }}>Fresher</option> 
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
                     <select class="" arial-label="Select total_exp_month" id="total_exp_month" name="total_exp_month">
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
                  </div>
                  <div class="fg fv-row">
                    <label>Relevant Experience <span class="rq">*</span></label>
                    <div class="ei">
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
                            //  dd($total_rel_exp);
                            //  dd($selected_rel_exp_year,$selected_rel_exp_month);

                        @endphp
                      <select id="rel_exp_year" name="rel_exp_year">
                        <option value="">Years</option>
                        <option value="0" {{ $selected_rel_exp_year==0 ? 'selected' : '' }}>Fresher</option>   
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
                      <select class="" arial-label="Select rel_exp_month" id="rel_exp_month" name="rel_exp_month">
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
                  </div>
                </div>
                <div class="g4">
                  
                  <div class="fg fv-row">
                    <label>Employment Type</label>
                    <select name="employmentType" id="EmploymentType">
                      <option value="">Select employment type</option>
                      <option {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Full Time</option>
                      <option {{ (@$application->employmentType==='Contract') ? 'selected' : '' }}>Contract</option>
                      <option {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part Time</option>
                      <option {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance</option>
                    </select>
                  </div>
                   <div class="fg fv-row">
                        <label>Commission Type <span class="rq">*</span></label>
                        @php
                            $selected = '';
                              if(@$application->comission_type==''){
                                $selected = 'selected';
                              }
                        @endphp
                        <select name="comission_type" id="comission_type">
                          <option value="">Select Commision type</option>
                          <option value="1" {{ (@$application->comission_type==1) ? 'selected' : '' }}>Free (Not charged from Clients)</option>
                          <option value="2" {{ (@$application->comission_type===2) ? 'selected' : '' }}>Paid Service</option>>
                        </select>
                   </div>
                  <div class="fg fv-row">
                    <label>Notice Period <span class="rq">*</span></label>
                    <select name="serving_notice_period" id="serving_notice_period">
                      <option value="0" {{ ($snp_mode==0) ? 'selected' : '' }}>Select notice period</option>
                      <option value="1" {{ ($snp_mode===1) ? 'selected' : '' }}>Serving Notice Period</option>
                      <option value="2" {{ ($snp_mode===2) ? 'selected' : '' }}>Immediate</option>
                    </select>
                  </div>

                    <div class="fg fv-row">
                       <label>&nbsp;</label>
                        <div style="{{ @$snp_text_one }}" id="snp-immediate" class="" >
                            <div style="position: relative;margin-right: 6px;">
                              <input type="text" id="text_display" value="{{ $snp_text_dummy }}" class="form-control form-control-solid" placeholder="" readonly>
                            </div>
                          
                            <div style="position: relative;">
                                <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select last working date" name="np_id_display" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" onclick="document.getElementById('np_id').click()" readonly>
                                <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" style="opacity: 0; position: absolute; z-index: -1;top: 0;" onclick="this.showPicker()">
                            </div>
                        </div>
                    
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
                        
                        <input type="text" id="calender" name="calender" style="display:none">
                    </div>                  
                </div>
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Enter Employee ID (If Any)</label>
                     <input type="email" class="form-control form-control-solid" name="employee_number" id="employee_number" placeholder="Enter employee number" value="{{ @$application->employee_number }}">
                  </div>
                </div>
              </div>
            </div>

            <!-- Salary Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Bill Rates Details</h3>
              </div>
              <div class="fsc">
                <div class="sg">
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Annual Rate</label>
                      <div style="font-size: 1rem; font-weight: 600; color:var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      
                      <div class="col-2 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="pay_rate_mode" id="pay_rate_mode">                          
                        @foreach($salaryMode as $key=>$val)
                          <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>	
                        @endforeach					
                        </select>
                      </div>

                      <div class="col-2 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="bill_rate_mode" id="bill_rate_mode">                         
                        @foreach($salaryMode as $key=>$val)							
                          <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>	
                        @endforeach
                        </select>
                      </div>

                      <select id="pay_rate_currency" name="pay_rate_currency">
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
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Pay Rate (Annual) <span  style="float: right; color: #ef560a;"id="pay_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payrateannual"></span>
                    </div>
                    <div class="fg fv-row"><label>Bill Rate (Annual)  <span style="float: right; color: #ef560a;" id="bill_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billrateannual"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Monthly Rate</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="bill_rate_currency" id="bill_rate_currency">
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
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Pay Rate (Monthly) <span style="float: right; color: #ef560a;" id="pay_rate_monthly_usd"></span></label>
                      <div class="ig">
                        <input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payratemonthly"></span>
                    </div>
                    <div class="fg fv-row"><label>Bill Rate (Monthly) <span style="float: right; color: #ef560a;" id="bill_rate_monthly_usd"> </span></label>
                      <div class="ig"><input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billratemonthly"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row"><label style="color: var(--p)">Hourly Rate</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="current_th_cur" id="current_th_cur">
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
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Pay Rate (Hourly) <span style="float: right; color: #ef560a;" id="pay_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="pay_rate_amount" id="pay_rate_amount" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payratehourly"></span> 
                    </div>
                    <div class="fg fv-row"><label>Bill Rate (Hourly) <span  style="float: right; color: #ef560a;"id="bill_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="bill_rate_amount" id="bill_rate_amount" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billratehourly"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                          <label style="color: var(--p)">Other Bill Details</label>
                        <div style="display: flex;">
                            <div class="">
                              <label>Profit Percentage</label>
                              <div class="ig">
                                <input type="number" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" style="text-align: center" />
                                <span class="ig-a">%</span>
                              </div>
                              <div style="font-size: 0.95rem; color: var(--t-l); margin-top: 3px;">(Calculated from Annual CTC)</div>
                            </div>
                            <div class="d-none" style="margin-left: 10px;">
                              <div style="margin-bottom: 8px">
                                <label style="font-size: 1rem; font-weight: 600">Offer in Hand?</label>
                                <div class="rg">
                                  <div class="ri">
                                    <input type="radio" name="oh" id="ohy" value="yes" {{ @$application->oh == 'yes' ? 'checked' : '' }} />
                                    <label for="ohy">Yes</label>
                                  </div>
                                  <div class="ri">
                                    <input type="radio" name="oh" id="ohn" value="no"  {{ (@$application->wm == 'no' || empty(@$application->oh)) ? 'checked' : '' }}  />
                                    <label for="ohn">No</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="fg fv-row">
                      <label>Comments</label>
                      <textarea name="comment" id="comment" placeholder="Enter comments about salary expectations or any other details..." rows="4">{{ @$application->comment }}</textarea>
                      <div class="cc">0 / 500</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Skills Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Job Skills Details</h3>
              </div>
              <div class="fsc">
                <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Experience (Years) <span class="rq">*</span></th>
                      <th>Experience (Months)</th>
                      <th>Proficiency Rating <span class="rq">*</span></th>
                      <th>Actions <span class="rq">*</span></th>
                    </tr>
                  </thead>
                  <tbody id="skillExperienceWrapper">
                  </tbody>
                </table>
                
                <div style="display:flex;gap:6px">
                <button class="asb" type="button">+ Add Skill</button>
                <button class="bab d-none" type="button"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>Bulk Add Skills</button>
                </div>
              </div>
            </div>

             <!-- Skills Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Relationship Manager</h3>
              </div>
              <div class="fsc fv-row">

                <!--begin::Custom Details-->
                <div class="" id="job_additional_detail">
                  
                  <input class="form-control form-control-solid" name="recruiter" id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>" readonly="" />
                    <input type="hidden" class="form-control form-control-solid" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>" readonly="" />
                    <span class="form-text text-muted">*The selected relationship manager's number and email will be shared with clients to coordinate regarding the above candidate.</span> 

                </div>
                <!--end::Custom Details-->
              </div>
            </div>

            <!-- Additional Information -->
            <div class="fs">
              <div class="fsh">
                <h3>Additional Information</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Likedin Link (Optional)</label>
                    <input type="url" id="linkedin" name="linkedin" value="{{ @$application->linkedin }}" placeholder="Enter Likedin Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>GitHub Link (Optional)</label>
                    <input type="url" value="{{ @$application->github }}" id="github" name="github" placeholder="Enter GitHub Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>Behance Link (Optional)</label>
                    <input type="url" id="behance" name="behance" value="{{ @$application->behance }}" placeholder="Enter Behance Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>Other Link (Optional)</label>
                    <input type="url" value="{{ @$application->otherLink }}" id="otherLink" name="otherLink" placeholder="Enter URL" />
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Current Visa Status (For International)</label>
                    <select name="visastatu" id="visastatu">
                      <option value="">Select visa status</option>
                      <option {{ @$application->visastatu == 'H1B' ? 'selected' : '' }}>H1B</option>
                      <option {{ @$application->visastatu == 'L1' ? 'selected' : '' }}>L1</option>
                      <option {{ @$application->visastatu == 'F1' ? 'selected' : '' }}>F1</option>
                      <option {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Green Card</option>
                      <option {{ @$application->visastatu == 'Citizen' ? 'selected' : '' }}>Citizen</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Reference Name (Optional)</label>
                    <input type="text" name="referencename" value="{{ @$application->referencename}}" id="referencename" placeholder="Enter reference name" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </main>

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

    const uploadBox = document.getElementById('uploadBox');
    const resumeInput = document.getElementById('resume');

    // ==========================
    // Common file handler
    // ==========================
    function processFile(file, event = null) {

        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        if ($.inArray(ext, ['pdf', 'doc', 'docx']) === -1) {
            alert('Only PDF, DOC and DOCX files are allowed.');
            return;
        }

        const sizeMB = file.size / (1024 * 1024);

        if (sizeMB > 10) {
            alert('Maximum file size is 10 MB.');
            return;
        }

        $('#fileName').text(
            file.name + ' (' + sizeMB.toFixed(2) + ' MB)'
        );

        $('#fileInfo').css('display', 'flex');

        $('#resumeCheck').val('1');

        $('.download-file').addClass('d-none');

        console.log('Selected File:', file.name);

        if (typeof handleFileInputChange === 'function' && event) {
            handleFileInputChange(event);
        }
    }

    // ==========================
    // Choose File Button
    // ==========================
    $(document).on('click', '#chooseFileBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resumeInput.click();
    });

    document.addEventListener('paste', function (e) {

        const files = e.clipboardData?.files;

        if (files && files.length) {

            const file = files[0];

            // Your existing validation/display function
            processFile(file);

            // Assign file to input so form submit works
            const dt = new DataTransfer();
            dt.items.add(file);

            resumeInput.files = dt.files;

            // Trigger change event
            resumeInput.dispatchEvent(
                new Event('change', { bubbles: true })
            );
        }
    });

    // ==========================
    // Click Upload Area
    // ==========================
    $('#uploadBox').on('click', function (e) {

        if (
            $(e.target).closest('#chooseFileBtn').length ||
            $(e.target).closest('#removeFile').length ||
            $(e.target).closest('.download-file').length
        ) {
            return;
        }

        resumeInput.click();
    });

    // ==========================
    // File Input Change
    // ==========================
    $('#resume').on('change', function (e) {

        if (this.files.length > 0) {

            processFile(this.files[0], e);

        } else {

            $('#fileInfo').hide();
            $('#resumeCheck').val('');
            $('.download-file').addClass('d-none');
        }
    });

    // ==========================
    // Remove File
    // ==========================
    $(document).on('click', '#removeFile', function (e) {

        e.preventDefault();
        e.stopPropagation();

        $('#resume').val('');
        $('#resumeCheck').val('');

        $('#fileName').text('');
        $('#fileInfo').hide();

        $('.download-file').addClass('d-none');
    });

    // ==========================
    // Drag & Drop
    // ==========================
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {

        uploadBox.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    uploadBox.addEventListener('dragenter', function () {
        this.classList.add('dragging');
    });

    uploadBox.addEventListener('dragover', function () {
        this.classList.add('dragging');
    });

    uploadBox.addEventListener('dragleave', function () {
        this.classList.remove('dragging');
    });

    uploadBox.addEventListener('drop', function (e) {

        this.classList.remove('dragging');

        const files = e.dataTransfer.files;

        console.log('DROP FIRED', files);

        if (files.length > 0) {

            const file = files[0];

            // Show filename immediately
            processFile(file);

            // Assign to file input for form submit
            const dt = new DataTransfer();
            dt.items.add(file);

            resumeInput.files = dt.files;

            // andleFileInputChange(e);
             resumeInput.dispatchEvent(
            new Event('change', { bubbles: true })
        );
            
        }
    });

});
</script>


<script>

        // stepper
        document.querySelectorAll('.stp-s').forEach((s) => {
            s.addEventListener('click', () => {
                document
                    .querySelectorAll('.stp-s')
                    .forEach((x) => x.classList.remove('a'));
                s.classList.add('a');
            });
        });
        // char counters
        document.querySelectorAll('textarea').forEach((t) => {
            const c = t.parentElement.querySelector('.cc');
            if (c) {
                const m = c.textContent.includes('1000') ? 1000 : 500;
                t.addEventListener('input', () => {
                    c.textContent = t.value.length + ' / ' + m;
                });
            }
        });
        // star ratings
        document.querySelectorAll('.sr').forEach((r) => {
            const stars = r.querySelectorAll('.st');
            stars.forEach((s, i) => {
                s.addEventListener('click', () => {
                    stars.forEach((x, j) => {
                        if (j <= i) x.classList.remove('e');
                        else x.classList.add('e');
                    });
                });
            });
        });
        // add skill
        document.querySelector('.asb').addEventListener('click', () => {
            const tb = document.querySelector('.sk tbody');
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td><input type="text" name="skills[]" placeholder="Enter skill"></td><td><select class="form-select form-select-solid" name="years[]"> <option value="">Select Year</option> <option value="1"> 1 Year </option><option value="2"> 2 Years </option><option value="3"> 3 Years </option><option value="4"> 4 Years </option><option value="5"> 5 Years </option><option value="6"> 6 Years </option><option value="7"> 7 Years </option><option value="8"> 8 Years </option><option value="9"> 9 Years </option><option value="10"> 10 Years </option><option value="11"> 11 Years </option><option value="12"> 12 Years </option><option value="13"> 13 Years </option><option value="14"> 14 Years </option><option value="15"> 15 Years </option><option value="16"> 16 Years </option><option value="17"> 17 Years </option><option value="18"> 18 Years </option><option value="19"> 19 Years </option><option value="20"> 20 Years </option><option value="21"> 21 Years </option><option value="22"> 22 Years </option><option value="23"> 23 Years </option><option value="24"> 24 Years </option><option value="25"> 25 Years </option><option value="26"> 26 Years </option><option value="27"> 27 Years </option><option value="28"> 28 Years </option><option value="29"> 29 Years </option><option value="30"> 30 Years </option><option value="31"> 31 Years </option><option value="32"> 32 Years </option><option value="33"> 33 Years </option><option value="34"> 34 Years </option><option value="35"> 35 Years </option><option value="36"> 36 Years </option><option value="37"> 37 Years </option><option value="38"> 38 Years </option><option value="39"> 39 Years </option><option value="40"> 40 Years </option><option value="41"> 41 Years </option><option value="42"> 42 Years </option><option value="43"> 43 Years </option><option value="44"> 44 Years </option><option value="45"> 45 Years </option><option value="46"> 46 Years </option><option value="47"> 47 Years </option><option value="48"> 48 Years </option><option value="49"> 49 Years </option><option value="50"> 50 Years </option><option value="51"> 51 Years </option><option value="52"> 52 Years </option><option value="53"> 53 Years </option><option value="54"> 54 Years </option><option value="55"> 55 Years </option><option value="56"> 56 Years </option><option value="57"> 57 Years </option><option value="58"> 58 Years </option><option value="59"> 59 Years </option><option value="60"> 60 Years </option> </select></td><td><select class="form-select form-select-solid" name="months[]"> <option value="">Select Month</option> <option value="0"> 0 Months </option><option value="1"> 1 Month </option><option value="2"> 2 Months </option><option value="3"> 3 Months </option><option value="4"> 4 Months </option><option value="5"> 5 Months </option><option value="6"> 6 Months </option><option value="7"> 7 Months </option><option value="8"> 8 Months </option><option value="9"> 9 Months </option><option value="10"> 10 Months </option><option value="11"> 11 Months </option> </select></td><td><select class="form-select form-select-solid" name="ratings[]"> <option value="">Select Rating</option> <option value="1"> 1 Rating </option><option value="2"> 2 Rating </option><option value="3"> 3 Rating </option><option value="4"> 4 Rating </option><option value="5"> 5 Rating </option> </select></td><td><button class="ab" type="button">🗑️</button></td>';
            tb.appendChild(tr);
            tr.querySelectorAll('.sr').forEach((r) => {
                const s = r.querySelectorAll('.st');
                s.forEach((x, i) => {
                    x.addEventListener('click', () => {
                        s.forEach((y, j) => {
                            if (j <= i) y.classList.remove('e');
                            else y.classList.add('e');
                        });
                    });
                });
            });
            tr.querySelector('.ab').addEventListener('click', () => tr.remove());
        });
        // delete existing
        $(document).on('click','.ab',function(){
          var ele = $(this);
           ele.closest('tr').remove();
        })
        // document.querySelectorAll('.ab').forEach((b) => {
        //     b.addEventListener('click', () => b.closest('tr').remove());
        // });
</script>


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

    let html = `<tr id="skill-exp-${skillKey}">
      <td><input type="text" value="${skill}" name="skills[]" placeholder="Enter skill" /></td>
      <td>
                <select class="form-select form-select-solid"
                        name="years[]">
                    <option value="">Select Year</option>
                    ${generateYears(data.year)}
                </select>
      </td>
      <td>
                <select class="form-select form-select-solid"
                        name="months[]">
                    <option value="">Select Month</option>
                    ${generateMonths(data.month)}
                </select>
      </td>
      <td>
            <select class="form-select form-select-solid"
                    name="ratings[]">
                <option value="">Select Rating</option>
                ${generateRating(data.rating)}
            </select>

      </td>
      <td><button class="ab" type="button">🗑️</button></td>
    </tr>`;

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
function generateRating(selected = null) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += `<option value="${i}" ${i == selected ? 'selected' : ''}>
                    ${i} Rating${i !== 1 ? 's' : ''}
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
        month: row.month,
        rating: row.rating
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

$(document).on('change','#serving_notice_period',function(){
    var cvalue = $('#serving_notice_period').val();
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
  })

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
    // var radios = document.getElementsByName('serving_notice_period');
    
      var radios = $('#serving_notice_period').val();
         var today = new Date().toISOString().split('T')[0];
          // Loop through each radio button and check which one is selected
          for (var i = 0; i < radios.length; i++) {
              if (radios==1 || radios==2) {
                if (radios == 1) {
                    // Future dates allowed
                    $('#np_id').removeAttr('max'); // Remove max restriction
                    $('#np_id').attr('min', today); // Set min to today
                    $('#np_id').attr('placeholder', 'Select your last working date (future dates allowed)'); // Set placeholder
                } else {
                    // Past dates allowed
                    $('#np_id').removeAttr('max'); // Remove min restriction
                    $('#np_id').attr('max', today); // Set max to today
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
    
</script>

<script>   

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
                        'kt_tagify_work_location'				:	{validators:{notEmpty: {message: "Current Location is required"}} },
                    		'current_designation'	:	{validators:{notEmpty:{message: "Current designation is required"}} },
                    		'employmentType'		:	{validators:{notEmpty:{message: "Employment Type is required"}} },
                    		'phone':{validators:{notEmpty:{message: "Primary number is required"},digits:{message: "Primary number must contains only digits"},stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}}},
							          'secondary_mobile':{validators:{digits:{message: "Primary number must contains only digits"},stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}}},
							          'email':	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    		'qualification':	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    		'total_exp':	{validators:{ notEmpty:{message:"Total Experience is required"},	digits:	{message: "Total Experience must contain only digits"} }},
                        'rel_exp'				:	{validators:{ notEmpty:{message:"Relevant Experience is required"},digits:{message: "Relevant Experience must contain only digits"}}},
                        'pay_rate' 				:	{validators:{notEmpty:{message: "Pay rate is required"},digits:{message: "Pay rate must containts only digits"}}},
                        'bill_rate' 			:	{validators:{notEmpty:{message: "Bill rate is required"},digits:{message: "Bill rate must contains only digits"}}},
                        'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}},	
                        'recruiter'					:	{validators:{ notEmpty: { message: "Assign internal team/recruiter is required" } }  },
                        // 'skills'				:	{ validators: { notEmpty: {  message: 'Skills is required' } }},
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
                        'kt_tagify_work_location'				:	{validators:{notEmpty: {message: "Current Location is required"}} },
                    		'current_designation'	:	{validators:{notEmpty:{message: "Current designation is required"}} },
                    		'employmentType'		:	{validators:{notEmpty:{message: "Employment Type is required"}} },
                    		'phone':{validators:{notEmpty:{message: "Primary number is required"},digits:{message: "Primary number must contains only digits"},stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}}},
							          'secondary_mobile':{validators:{digits:{message: "Primary number must contains only digits"},stringLength:{	min: 10, max: 10,	message: "Primary number must contains 10 digits only"}}},
							          'email':	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    		'qualification':	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    		'total_exp':	{validators:{ notEmpty:{message:"Total Experience is required"},	digits:	{message: "Total Experience must contain only digits"} }},
                        'rel_exp'				:	{validators:{ notEmpty:{message:"Relevant Experience is required"},digits:{message: "Relevant Experience must contain only digits"}}},
                        'pay_rate' 				:	{validators:{notEmpty:{message: "Pay rate is required"},digits:{message: "Pay rate must containts only digits"}}},
                        'bill_rate' 			:	{validators:{notEmpty:{message: "Bill rate is required"},digits:{message: "Bill rate must contains only digits"}}},
                        'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}},	
                        'recruiter'					:	{validators:{ notEmpty: { message: "Assign internal team/recruiter is required" } }  },
                        // 'skills'				:	{ validators: { notEmpty: {  message: 'Skills is required' } }},
                        'specialties'			:	{validators:{ notEmpty:{message: "Specialties is required"}}},
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
                    $('#careersummary').val(response.careerSummary);
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
     
     var currentCTC = parseFloat($('#pay_annual').val());
     var expectedCTC = parseFloat($('#bill_annual').val());
     
     var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;

     if (currentCTC > 0) {
        $('#percentage_hike').val(percentageIncrease.toFixed(0));
        }else{
          $('#percentage_hike').val('');
      }
 }

 function ectc(){
     var currentCTC = parseFloat($('#pay_annual').val());
     var percentage_hike = $('#percentage_hike').val();
     var amount = currentCTC*percentage_hike/100;
     $('#bill_annual').val(amount+currentCTC);
     
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
      if (!file) {
          return;
      }
      let formData = new FormData();
       formData.append('file', file);
       formData.append('_token', '{{ csrf_token() }}');

       $.ajax({
        url: "{{ url('service_company/uploadresume') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {

            $('#fileUploadLoader').show();

        },
        success: function (response) {

            console.log(response);

            if(response.status){

                $('#resume_path').val(response.file_path);

                toastr.success('Resume uploaded successfully');
            }

        },
        error: function (xhr) {

            toastr.error('Upload failed');

        },
        complete: function () {

            $('#fileUploadLoader').hide();

        }
    });


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