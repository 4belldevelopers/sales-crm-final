@extends('layouts.service_company_layout')
@push('head-script')
<script>
   $( function()
   {
     $( "#calender" ).datepicker
     	({
			showOn				: "button",
			buttonImage			: "images/calendar.gif",
			buttonImageOnly		: true,
			buttonText			: "Select date"
       });
   });	
</script>
<style>
body {overflow-x:hidden; }
.dropzone { padding: 1.8rem 1.75rem!important; border: 2px dashed #e6e7e8!important; background:transparent!important; text-align: inherit!important; }
.border-radius-rb { border-top-right-radius: 0em!important; }
.border-radius-rt { border-bottom-right-radius: 0em!important; }
.border-radius-lt { border-bottom-left-radius: .4em!important; }
.border-radius-lb { border-top-left-radius: .4em!important; }
.font-size-icon{ font-size: 1.3em; }
.dashed-border-top { border-top:1px dashed gray; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#np_id::-webkit-inner-spin-button,
#np_id::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }

.disablesectionOn.setOpacity{
    cursor: not-allowed;
    background: #8a878d;
    opacity: 0.5;
}

</style>
@endpush

@section('content')
@php 
		use App\Helper\GeneralMethod;
    $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
    $country_code =  GeneralMethod::countryCodeFun();
    $currency_code =  GeneralMethod::defaultCurrency();

      $phone_code = $country_code;
      $second_phone_code = $country_code;
      $salary_currency = @$jobDetail->max_salary_currency ?? 0;

@endphp

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">

@php 
      $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
      $url = '';
	  $photo  					=	url('public/avatar.png');
      if($page_event == 'ADD')
      {
         $pagetitle				=	'Add Candidate';
         $resume_url			=	'';	
      }
      else if($page_event == 'EDIT')
      { 
         $pagetitle	=	'Edit Candidate';     
         if(!empty($application->resume)){
            $url				=	 $application->resume;
            $info = pathinfo($url);
            $resume_url			=	'<a href="'.route('download.resume', ['id' => $application->id,'path' => urlencode($url)]).'" target="_blank" download><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>';	
         }else{
            $resume_url			=	'';	
         }
         if(!empty($application->photo)){
            $photo			=	 url('/public/user-uploads/candidate-photos/'.$application->photo);	
         }   
      }	
@endphp

  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
     @include('sections.service_company.toolbar',['id' => $jobDetail->id])
    <!--end::Page title-->
</div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Form-->
          <h3 class="py-5">{{ @$pagetitle }}</h3>

          <!--begin::Form-->
          <form class="form" action="#" method="post">
            <!--begin::Input group-->
            <div class="form-group row">
                <!--begin::Label-->
                {{-- <label class="col-lg-2 col-form-label text-lg-right">Upload Files:</label> --}}
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
            <input type="hidden" name="job_id" value="{{ @$jobId }}" id="job_id" />
            <input type="hidden" name="appId" value="{{ @$appId }}" id="appId" />
            <input type="hidden" name="currentPage" value="{{ @$currentPage }}" id="currentPage" />
            <input type="hidden" name="resumeCheck" value="1" id="resumeCheck" />
            <!--<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">-->
            <div class="mb-5">
              <div class="flex-column">
                <!--begin::Upload Photo & Resume/CV-->
                <div class="form-group row fv-row mb-10 d-none">
                  <div class="col-lg-4 col-xl-4 text-center">
                    <!--begin::Image input-->
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                      <!--begin::Image preview wrapper-->
                      <div class="image-input-wrapper w-90px h-90px" style="background-image: url({{ @$photo }})"></div>
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
                    <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume" id="resume">
                    <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                    <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.. &nbsp;{{ @$resume_url }}</span> </div>
                    <!--end::Input group-->
                  </div>
                </div>
                <!--end::Upload Photo & Resume/CV-->
                <!--begin::Fullname | Designation | Current Company-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Full Name:</label>
<input type="text" class="form-control form-control-solid" name="full_name" id="full_name" placeholder="Enter full name" value="{{ @$application->full_name }}">
                    <span class="form-text text-muted">*Enter the full name of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Current Designation:</label>
<input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{ (@$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}">
                    <span class="form-text text-muted">*Enter the current designation of the candidate</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Current Company Name:</label>
<input type="text" class="form-control form-control-solid" name="current_company" id="current_company" placeholder="Enter current company" value="{{ @$application->current_company }}">
                    <span class="form-text text-muted">*Enter the current company name of the candidate</span> </div>
                </div>
                <!--end::Fullname | Designation | Current Company-->
                <!--begin::Primary Number | Secondary Number | Date of Birth-->
                <div class="form-group row fv-row mb-10 fv-plugins-icon-container has-danger">
                  <div class="col-lg-4">
                    <label class="form-label required">Enter Primary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="phone" id="phone" placeholder="Enter primary number" value="{{ @$application->phone }}">
                    <input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code">
                    <span class="form-text text-muted">*Enter the primary number of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Enter Secondary Number:</label>
<input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="{{ @$application->secondary_mobile }}">
<input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code">
                    <span class="form-text text-muted">*Enter the Secondary number of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Please enter date of birth of the candidate</label>
                    <input type="date" class="form-control form-control-solid"  name="dob" id="dob" placeholder="Enter dob" value="{{ @$application->dob }}" onclick="this.showPicker()" >
                    <span class="form-text text-muted">Please enter your dob</span> </div>
                </div>
                <!--end::Primary Number | Secondary Number | Date of Birth-->
                <!--begin::Primary Email | Secondary Email | Qualifaction-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Enter Primary Email ID:</label>
                    <input type="email" class="form-control form-control-solid" name="email" id="email" placeholder="Enter primary email" value="{{ @$application->email }}">
                    <span class="form-text text-muted">*Enter the primary email id of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Enter Secondary Email ID:</label>
<input type="email" class="form-control form-control-solid" name="secondrary_email" id="secondrary_email" placeholder="Enter secondary email" value="{{ @$application->secondrary_email }}">
                    <span class="form-text text-muted">*Enter the secondary email ID of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Qualification:</label>
                    <select class="form-select form-select-solid"  name="qualification" id="qualification" aria-label="Select qualification">
                      <option value="" >Select Qualification</option>
							@foreach($qualification as $data_value)
								@php 
								   $sel	=	'';
								   if(@$application->qualification == $data_value->id ){
									  $sel	=	'selected="selected"';
								   }
								@endphp    
							   <option value="{{ @$data_value->id }}"  {{ @$sel }}>{{ @$data_value->name }}</option>
							@endforeach	             
                    </select>
                    <span class="form-text text-muted">*Please select the qualification of the candidate.</span> </div>
                  <div class="col-lg-4 d-none">
                    <label class="form-label">Minimum Salary</label>
                    <div class="row">
                      <div class="col-3">
<select class="form-select form-select-solid" arial-label="Select Currency">
@foreach($currency as $key=>$val)
	<option value="{{ $key }}">{{ $val }}</option> 	
@endforeach
</select>
                      </div>
                      <div class="col-5">
                        <input type="number" class="form-control form-control-solid" name="min_salary" placeholder="Enter minimum" value="">
                      </div>
                      <div class="col-4">
<select class="form-select form-select-solid" arial-label="Select Annual Salary"> 
@foreach($salaryMode as $key=>$val)
	<option value="{{ $key }}">{{ $val }}</option>	
@endforeach
</select>
                      </div>
                    </div>
                    <span class="form-text text-muted">Please enter your minimum salary</span> </div>
                </div>
                <!-- end::Primary Email | Secondary Email | Qualifaction -->
                <!--begin::Total Experience | Relevant Experience | Notice Period-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label" style="margin-bottom: 0.0rem;"> 
                      <div style="">
                        <div class="d-inline-flex">
                          <label class="radio-inline cursor-pointer">
                          <input class="form-check-input h-15px w-15px fresher mt-0 me-1 cursor-pointer" type="radio" name="pincode" value="0" {{(@$application->pincode==0) ? 'checked' : 'checked' }} onclick="fresherrbutton(this.value);" >
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

                    <span class="form-text text-muted">Please enter total exp.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Relevant Experience:</label>

                     <div class="row disablesectionOnRow">
                      @php
                       $valuew = @$application->rel_exp;
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


                    <span class="form-text text-muted">Please enter relevant exp.</span> 
                    <div class="fv-plugins-message-container invalid-feedback" id="rel_exp_error"></div>
                  </div>
                  <div class="col-lg-4">
                  
                      @php  
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
                      @endphp
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                       <input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" {{ @$snp_zero }} >
                      Notice </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                       <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="1" onclick="rbutton(this.value);" {{ @$snp_one }} >
                      Serving Notice Period </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                       <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="2" onclick="rbutton(this.value);" {{ @$snp_two }} >
                      Immediate </label>
                    </div>
                    </label>

                    <div style="{{ @$snp_text_one }}" id="snp-immediate" class="" >
                      <div style="position: relative;margin-right: 6px;">
                        <input type="text" id="text_display" value="{{ $snp_text_dummy }}" class="form-control form-control-solid" placeholder="" readonly>
                      </div>
  
                      <div style="position: relative;">
                          <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select last working date" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" onclick="document.getElementById('np_id').click()">
                          <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" style="opacity: 0; position: absolute; z-index: -1;top: 0;" onclick="this.showPicker()">
                      </div>
                    </div>

                      {{-- <input type="text" name="np_id" id="np_id" class="form-control form-control-solid np_id" placeholder="" style="<?php echo @$snp_text_one; ?>"  value="{{ @$application->np_id }}" onclick="this.showPicker()"> --}}

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
                    <span class="form-text text-muted">Please enter noitce period</span> 
                  </div>

                  {{-- <div class="col-lg-4">
                    <label class="form-label">Notice Comment</label>
                    <input type="text" class="form-control form-control-solid" name="notice_comments" id="notice_comments" placeholder="Enter Notice Comment" value="{{ @$application->notice_comments }}">
                    <span class="form-text text-muted">Please enter comment</span> 
                  </div> --}}

                </div>
                <!--end::Total Experience | Relevant Experience | Notice Period-->
               <!--begin::Current CTC | Expected CTC | Percentage-->
                <div class="form-group row fv-row mb-1">
                  <div class="col-lg-12">
                    <h3 class="py-1">Current Salary</h3>
                    <div class="row fv-row mb-10">
                      <div class="col-1 pe-0">
                        {{-- @dd(@$application->current_ctc_cur); --}}
                        <label class="form-label">Currency </label>
                        <select class="form-select form-select-solid currency" arial-label="Select Currency" id="current_ctc_cur" name="current_ctc_cur">
                          @php 
                                        $current_ctc_cur	=	"";
                                        $current_ctc_cur	=	@$application->current_ctc_cur ?? $salary_currency;//@$application->current_ctc_cur;
                                        
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
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">CTC Annual <span  style="position: absolute; right: 8px; color: #ef560a;"id="pay_rate_annual_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="pay_annual" id="pay_annual" placeholder="Enter CTC annual" value="{{ @$application->ctc_yearly }}">
                        <span class="form-text text-muted payrateannual"></span>
                      </div>
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">CTC Monthly <span style="position: absolute; right: 8px; color: #ef560a;" id="pay_rate_monthly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="pay_monthly" id="pay_monthly" placeholder="Enter CTC monthly" value="{{ @$application->ctc_monthly }}">
                        <span class="form-text text-muted payratemonthly"></span>
                      </div>
                      <div class="col-3 px-2">
                        <div class="form-label" style="position: relative;">CTC Hourly <span style="position: absolute; right: 8px; color: #ef560a;" id="pay_rate_hourly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="ctc" id="ctc" placeholder="Enter" value="{{ @$application->ctc_hourly }}">
                        <span class="form-text text-muted CurrentCTC">*Enter the current salary of the candidate</span> 
                      </div>

                      <div class="col-4 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                            @php 
                                        $current_ctc_mode	=	"";
                                        $current_ctc_mode	=	@$application->current_ctc_mode;
                                        foreach($salaryMode as $key=>$val){
                                          $sel	=	'';
                                          if(2==$key){
                                            $sel	=	'selected="selected"';	
                                          }	
                                          echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                        }
                            @endphp
                        </select>
                      </div>
                    </div>
                  </div>
                 <div class="form-group row">
                  <div class="col-lg-12">
                     <h3 class="py-1">Expected Salary</h3>
                   
                    <div class="row fv-row mb-1">
                      <div class="col-1 pe-0">
                         <label class="form-label">Currency </label>
                        <select class="form-select form-select-solid currency" arial-label="Select Currency" name="current_ectc_cur" id="current_ectc_cur">
                              @php 
                                          $current_ectc_cur	=	"";
                                          $current_ectc_cur	=	@$application->current_ectc_cur ?? $salary_currency;//@$application->current_ectc_cur;
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
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">ECTC Annual <span style="position: absolute; right: 8px; color: #ef560a;" id="bill_rate_annual_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="bill_annual" id="bill_annual" placeholder="Enter ECTC annual" value="{{ @$application->ectc_yearly }}" >
                        <span class="form-text text-muted billrateannual"></span>
                      </div>
                      <div class="col-4 px-2">
                        <div class="form-label" style="position: relative;">ECTC Monthly <span style="position: absolute; right: 8px; color: #ef560a;"id="bill_rate_monthly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="bill_monthly" id="bill_monthly" placeholder="Enter ECTC monthly" value="{{ @$application->ectc_monthly }}" >
                        <span class="form-text text-muted billratemonthly"></span>
                      </div>
                      <div class="col-3 px-2">
                         <div class="form-label" style="position: relative;" >ECTC Hourly <span  style="position: absolute; right: 8px; color: #ef560a;"id="bill_rate_hourly_usd"> </span> </div>
                        <input type="number" class="form-control form-control-solid" name="ectc" id="ectc" placeholder="Enter " value="{{ @$application->ectc_hourly }}" >
                         <span class="form-text text-muted expectedctc">*Enter the expected salary of the candidate</span> 
                      </div>
                      <div class="col-4 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
                              @php 
                                          $current_ectc_mode	=	"";
                                          $current_ectc_mode	=	@$application->current_ectc_mode;
                                          foreach($salaryMode as $key=>$val){
                                            $sel	=	'';
                                            if(2==$key){
                                              $sel	=	'selected="selected"';	
                                            }
                                            echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                          }
                              @endphp
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                  
                </div>
                <!--end::Current CTC | Expected CTC | Percentage-->
               <!--begin::Country | State | City | Pincode-->
               <div class="form-group row fv-row mb-10">
                
                <div class="col-lg-12">
                  <div class="row">
                    
                      <div class="col-10">
                        <label class="form-label">Comments</label>
                        <input type="text" class="form-control form-control-solid" name="comment" id="comment" placeholder="Enter comment" value="{{ @$application->comment }}">
                      </div>
                      <div class="col-2 pe-0">
                        <label class="form-label required">Percentage</label>
                        <input type="number" class="form-control form-control-solid" name="percentage_hike" id="percentage_hike" placeholder="Enter hike" value="{{ @$application->percentage_hike }}">
                        <span class="form-text text-muted"></span> 
                      </div>

                    <div class="col-12">
                      <label class="form-label">Enter Current Location of the candidate</label>
                      <input class="form-control form-control-solid" value="{{ @$default_loc }}" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter your Location..."/>
                      
                      <span class="form-text text-muted">*Select the current location of the candidate</span> 
                    </div>
                    {{-- <div class="col-6">
                      <label class="form-label">Pincode</label>
                      <input type="number" class="form-control form-control-solid" name="pincode" id="pincode" placeholder="Enter pincode" value="{{ @$application->pincode }}">
                      <span class="form-text text-muted">Please enter your pincode</span> 
                    </div> --}}
                  </div>
                </div>
              </div>
              <!--end::Country | State | City | Pincode-->
                <!--begin::Skills-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-12">
                    <script>
                                 var input = document.querySelector('input[name=skills]'); 
                                 new Tagify(input)
                                       function isNumberKey(evt)
                                       {
                                          var charCode = (evt.which) ? evt.which : evt.keyCode
                                          if (charCode > 31 && (charCode < 48 || charCode > 57))
                                             return false;
                                          return true;
                                       }
                     </script>
                    <label class="form-label required">*Enter the relevant skills of the candidate (Max 10 skills allowed).</label>
                    <input class="form-control form-control-solid" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
                    <span class="form-text text-muted">Please enter your skills</span> </div>
                </div>
                <!--end::Skills-->

                @if(!empty($jobSkill))
                <div class="dashed-border-top" id="job_skill_detail">
                  <h3 class="py-5">Job Skills</h3>
                  <div class="row">
                    @foreach($jobSkill as $skill_val)
                        @if(!empty($skill_val->skill_name))
                        <div class="col-md-4">
                          <label class="form-label">{{ strtoupper($skill_val->skill_name) }} EXPERIENCE</label>
                          {{-- <input type="text" class="form-control form-control-solid" name="skillId[{{$skill_val->id }}]" placeholder="Enter detail" value="{{ ($skillShow) ? $skill_val->skill_value : '' }}"> --}}
                          

                          <div class="row">
                              @php

                            
                              //  dd($total_skill_exp);
                              if($skill_val->skill_value){
                                    $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                    // dd($skillValue);
                                    if(!is_null($value) && !empty($value)){
                                      $total_skill_exp =  GeneralMethod::separateNumberParts($skillValue);
                                      $skill_exp_years = ($total_skill_exp[0]);
                                      $skill_exp_months = ($total_skill_exp[1]); 

                                      $selected_skill_exp_year = $skill_exp_years;
                                      $selected_skill_exp_month = $skill_exp_months;
                                    }else{
                                      $selected_skill_exp_year = null;
                                      $selected_skill_exp_month =null;
                                    }
                                    //  dd($skillValue,$skill_val->skill_value);
                                  }else{
                                    $selected_skill_exp_year = null;
                                    $selected_skill_exp_month =null;
                                  }
                              
                              // dd($skill_exp_years,$skill_exp_months);
        
                            @endphp
        
                              <div class="col-lg-6">
                                <select class="form-select form-select-solid" arial-label="Select rel_exp_year" id="rel_exp_year" name="YearskillId[{{ $skill_val->id }}]">
                                  <option value="0">Select Year</option>
                                  @php 
                                      for ($i = 1; $i <= 60; $i++) {
                                        $selected = '';
                                            $s = 'Year';
                                            if($i > 1){
                                                $s = 'Years';
                                            }
                                            if(is_null($selected_skill_exp_year)){
                                                $selected = '';
                                                }else{
                                                  if($i==$selected_skill_exp_year){
                                                    $selected =  'selected';
                                                }
                                                }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                      }
                                  @endphp
                                </select>
        
                              </div>
                              <div class="col-lg-6">
                                <select class="form-select form-select-solid" arial-label="Select rel_exp_month" id="rel_exp_month" name="MonthskillId[{{ $skill_val->id }}]">
                                  <option value="">Select Month</option>
                                  @php 
                                      for ($i = 0; $i <= 11; $i++) {
                                        $selected = '';
                                            $s = 'Month';
                                            if($i > 1){
                                                $s = 'Months';
                                            }
                                            if(is_null($selected_skill_exp_month)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_skill_exp_month){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                      }
                                  @endphp
                                </select>
                              </div>
                          </div>




                          {{-- <select name="skillId[{{ $skill_val->id }}]" id="experience" class="form-control form-control-solid">
                            <option value="No Experience" 
                                    {{ ($skillShow && $skill_val->skill_value == 'No Experience') ? 'selected' : '' }}>
                                No Experience
                            </option>
                            <option value="Basic Knowledge" 
                                    {{ ($skillShow && $skill_val->skill_value == 'Basic Knowledge') ? 'selected' : '' }}>
                                Basic Knowledge
                            </option>
                            <option value="6 Months" 
                                    {{ ($skillShow && $skill_val->skill_value == '6 Months') ? 'selected' : '' }}>
                                6 Months
                            </option>
                            @for ($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }} Year{{ $i > 1 ? 's' : '' }}" 
                                        {{ ($skillShow && ($skill_val->skill_value == "$i Year" || $skill_val->skill_value == "$i Years")) ? 'selected' : '' }}>
                                    {{ $i }} Year{{ $i > 1 ? 's' : '' }}
                                </option>
                                <option value="{{ $i }}.5 Years" 
                                        {{ ($skillShow && $skill_val->skill_value == "$i.5 Years") ? 'selected' : '' }}>
                                    {{ $i }}.5 Years
                                </option>
                            @endfor
                            <option value="50+ Years" 
                                    {{ ($skillShow && $skill_val->skill_value == '50+ Years') ? 'selected' : '' }}>
                                50+ Years
                            </option>
                        </select> --}}

                      
                          <span class="text-muted form-text">&nbsp;</span>
                        </div>

                        
                      

                        @endif
                    @endforeach
                  </div>
                </div>
              @endif


                <!--begin::Custom Details-->
                <div class="dashed-border-top" id="job_additional_detail">
                  @php 
                              if(!empty($job_additional_detail)){
                              
                              	$customTitleId	=	1;
                              	$html		=	'';
                                
                                foreach($job_additional_detail as $aDetailArr)
                              	{
                                  if(!empty($aDetailArr->detail_value)){
                              	    $html		=	'<h3 class="py-5">Job Questions</h3>';
                                  }
                                }
                              	$inc_first	=	0;
                              	$inc_last	=	1;
                                	$html	.=	'<div class="form-group row fv-row mb-10">';	
                              	foreach($job_additional_detail as $aDetailArr)
                              	{
                                  if(!empty($aDetailArr->detail_value)){
                              		
                              		$ans_value	=	'';
                              		if(!empty($application_additional_detail)){
                              			foreach($application_additional_detail as $aval){
                              					if($aDetailArr->id == $aval->question_id){
                              						$ans_value	=	@$aval->application_detail_value;	
                              					}
                              			}
                              		}
                              		
                              		
                              		// if($inc_first %3 == 0) 
                              		// {
                              		
                              		// }	
                              		$html		.=	'<div class="col-md-4">
                              						   <label class="form-label">'.strtoupper($aDetailArr->detail_value).':</label>
                              						   <input type="hidden" name="job_question[]" value="'.$aDetailArr->id.'">
                              						   <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
                              						   <span class="text-muted form-text">Please enter your details</span>
                              						</div>';
                              						 
                              		// if(($inc_last %3 == 0)&& $inc_last > 0){
                              		
                              		// }
                                }
                              		$inc_first++;
                              		$inc_last++;
                              		$customTitleId++;				
                              	}
                                $html		.='</div>';
                              	echo $html;	
                              }	
                              
                              //echo '<pre>'; print_r($statelist); exit; 				
                      @endphp 
                </div>
                <!--end::Custom Details-->


                 <div class="dashed-border-top form-group row fv-row mb-10">
                  <div class="col-lg-12">
                    <h3 class="py-5">Interview Availability</h3>                     
                        <div class="row my-3 px-3">
                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-1)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateOne" id="interviewDateOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_one" id="interview_mode_one"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_one == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>

                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-2)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateTwo" id="interviewDateTwo" value="{{ (@$application->interviewDateTwo)  ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y'): '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_two" id="interview_mode_two"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_two == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>


                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-3)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateThree" id="interviewDateThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_three" id="interview_mode_three"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_three == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>

                        </div>

                        <div class="row mt-5 my-3 px-3">
                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-4)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateFour" id="interviewDateFour" value="{{ (@$application->interviewDateFour) ?  \Carbon\Carbon::parse(@$application->interviewDateFour)->format('d-m-Y') :'' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeFour" id="interviewTimeFour" value="{{ (@$application->interviewDateFour) ?  \Carbon\Carbon::parse(@$application->interviewDateFour)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_four" id="interview_mode_four"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_four == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>

                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-5)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateFive" id="interviewDateFive" value="{{ (@$application->interviewDateFive) ?  \Carbon\Carbon::parse(@$application->interviewDateFive)->format('d-m-Y') :'' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeFive" id="interviewTimeFive" value="{{ (@$application->interviewDateFive) ?  \Carbon\Carbon::parse(@$application->interviewDateFive)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_five" id="interview_mode_five"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_five == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>

                          <div class="col-lg-4">
                              <label class="form-label">Date and Time(Slot-6)</label>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateSix" id="interviewDateSix" value="{{ (@$application->interviewDateSix) ? \Carbon\Carbon::parse(@$application->interviewDateSix)->format('d-m-Y') :'' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeSix" id="interviewTimeSix" value="{{ (@$application->interviewDateSix) ?  \Carbon\Carbon::parse(@$application->interviewDateSix)->format('h:i A') :'' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_six" id="interview_mode_six"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_six == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>
                          
                        </div>

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



                <!--begin::Buttons-->
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                    @php 
                                 if($page_event == 'EDIT')
                                 { 
                                 	echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update</button>';
                                 }
                                 else{
                                 	echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>';	
                                 }
                    @endphp

                    {{-- <a href="javascript:void(0);" class="btn btn-secondary">Cancel</a>  --}}
                  
                  </div>
                </div>
                <!--end::Buttons-->
              </div>
            </div>
          </form>
          <!--end::Form-->
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<!--<div class="mb-10">
    <label class="form-label">Default input style</label>
    <input class="form-control" value="tag1, tag2, tag3" id="kt_tagify_1"/>
</div>

<div class="mb-0">
    <label class="form-label">Solid background style</label>
    <input class="form-control form-control-solid" value="tag1, tag2, tag3" id="kt_tagify_2"/>
</div>-->
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>


   

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
        
        function fromUSD(amount, currency) {
            return amount / currencyRates[currency];
        }

   
        // ================= PAY RATE =================
        $('#ctc').on('input', function () {
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

            $('#ctc').val(format(hourly));
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

            $('#ctc').val(format(hourly));
            $('#pay_monthly').val(format(monthly));
            updatePayUI();
            percentage_hike();

            isUpdating = false;
        });

        // ================= BILL RATE =================
        $('#ectc').on('input', function () {
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

            $('#ectc').val(format(hourly));
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

            $('#ectc').val(format(hourly));
            $('#bill_monthly').val(format(monthly));
            updateBillUI();
            percentage_hike();

            isUpdating = false;
        });
        function updatePayUI() {
            let currency = $('#current_ctc_cur').val();
            let pay_rate_amount = $('#ctc').val();
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
            let currency = $('#current_ctc_cur').val();
            let bill_rate_amount = $('#ectc').val();
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



        function payRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;
            updatePayUI();
            isUpdating = false;
          
        }

        function billRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;

            updateBillUI();

            isUpdating = false;
          
        } 
        function percentageBillRate(){
             var currentCTC = parseFloat($('#bill_annual').val());

            let annual = currentCTC;

            let monthly = annual / MONTHS_PER_YEAR;
            let hourly = monthly / HOURS_PER_MONTH;

            $('#ectc').val(format(hourly));
            $('#bill_monthly').val(format(monthly));
            updateBillUI();
        }


        $('#current_ctc_cur').on('change', function () {
            updatePayUI();
            updateBillUI();
        });
        $('#current_ectc_cur').on('change', function () {
            updateBillUI();
            updatePayUI();
        });
        setTimeout(() => {
              billRateUpdate();
              payRateUpdate();
          }, 1000);


 


  setTimeout(() => {
          
          var defaultTimeFormat = '{{$defaultTimeFormat}}';
         

          // For all time pickers
          function initTimePicker(selector, format) {
                return $(selector).flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: format,   // "h:i K" or "H:i"
                    time_24hr: format === "H:i", 
                    minuteIncrement: 1,
                    allowInput: true, // allow user to delete value
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            let d = selectedDates[0];
                            if (d.getHours() === 0 && d.getMinutes() === 0) {
                                instance.clear(); // auto clear if 00:00
                            }
                        }
                    }
                });
            }

            // Example init
          let timeOnePicker   = initTimePicker("#interviewTimeOne",  '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeTwoPicker   = initTimePicker("#interviewTimeTwo",  '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeThreePicker = initTimePicker("#interviewTimeThree",'{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeFourPicker  = initTimePicker("#interviewTimeFour", '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeFivePicker  = initTimePicker("#interviewTimeFive", '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeSixPicker   = initTimePicker("#interviewTimeSix",  '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');



          function parseDMY(dmy) {
              if (!dmy) return null;
              let parts = dmy.split("-");
              return new Date(parts[2], parts[1] - 1, parts[0]); // Y, M-1, D
          }

          function initInterviewDatePicker(selector, offsetDays) {
              let existingDate = $(selector).val();
              let savedDateObj = parseDMY(existingDate);

              $(selector).flatpickr({
                  dateFormat: "d-m-Y",
                  altInput: true,
                  altFormat: "d-M-Y",
                  defaultDate: savedDateObj || getFutureDate(offsetDays),
                  minDate: savedDateObj ? null : "today", // block past unless saved
                  disable: savedDateObj
                      ? [
                          function (date) {
                              let today = new Date();
                              today.setHours(0,0,0,0);
                              let sameAsSaved = date.getTime() === savedDateObj.getTime();
                              return date < today && !sameAsSaved;
                          }
                        ]
                      : [],
                  onReady: function(selectedDates, dateStr, instance) {
                      instance.altInput.style.width = "129px";
                  }
              });
          }

          function getFutureDate(days) {
              const date = new Date();
              date.setDate(date.getDate() + days);
              return date;
          }

          initInterviewDatePicker("#interviewDateOne", 0);
          initInterviewDatePicker("#interviewDateTwo", 1);
          initInterviewDatePicker("#interviewDateThree", 2);
          initInterviewDatePicker("#interviewDateFour", 3);
          initInterviewDatePicker("#interviewDateFive", 4);
          initInterviewDatePicker("#interviewDateSix", 5);



          // Global clear function
        function clearAll() {
            timeOnePicker.clear();
            timeTwoPicker.clear();
            timeThreePicker.clear();
            timeFourPicker.clear();
            timeFivePicker.clear();
            timeSixPicker.clear();

            dateOnePicker.clear();
            dateTwoPicker.clear();
            dateThreePicker.clear();
            dateFourPicker.clear();
            dateFivePicker.clear();
            dateSixPicker.clear();
        }

          
}, 500);


localStorage.setItem('currentPage', "{{ @$currentPage }}");
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

  });


</script>


<script>
	var input1 = document.querySelector("#kt_tagify_1");
	var input2 = document.querySelector("#kt_tagify_2");
	new Tagify(input1);
	var tagifyskill =  new Tagify(input2, {
        whitelist: [''],
        maxTags: 10,
        transformTag: function(tagData) {
        // Remove special characters, allow only letters, numbers, and spaces
        tagData.value = tagData.value.replace(/[^a-zA-Z0-9 +#\/.]/g, '');
        return tagData;
        },
        validate: function(tagData) {
            // Prevent empty tags after cleaning
            return tagData.value.trim().length > 0 ? true : false;
        }
    });

    const fetchSuggestionsSkills = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Skill: ", data);

                if (Array.isArray(data)) {
                    tagifyskill.settings.whitelist = data; // Update whitelist
                    // tagifyskill.dropdown.show(query); // Show suggestions
                    tagifyskill.dropdown.show.call(tagifyskill);
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
    tagifyskill.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 1) return;
        fetchSuggestionsSkills(query);
    });


  var inputloc = document.querySelector("#kt_tagify_work_location");
  var cities = @json($cities);
                             
    // Initialize Tagify script on the above inputs
    var tagifylocation =  new Tagify(inputloc, {
        whitelist: [],
        enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted tagify__inline__suggestions
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
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
      // alert(cities);
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
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/3.14.3/tagify.css">
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>

@if($page_event == 'EDIT')
<script src="{{ asset('demo/v7/js/edit-applicant.js') }}"></script>
@else
{{-- <script src="{{ asset('demo/v7/js/add-applicant.js') }}"></script> --}}
@endif

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>


   new Tagify(document.querySelector('#skills'),{
       tagTextProp	: "name",
       duplicates	: true,
       whitelist	: [{"value":"123","name":"Bahamas"}]
   });
</script>

<script>
   $.noConflict();   
   var input1 = document.querySelector("#kt_tagify_1");
   new Tagify(input2);
</script>

<!--end::Old Tagify-->
<script>
   const fetchCountryState 	=	'';
   const csrfToken 			=	"{{ csrf_token() }}";
   const selectCountry 		=	"@lang('modules.front.selectCountry')";
   const selectState 		=	"@lang('modules.front.selectState')";
   const selectCity 		=	"@lang('modules.front.selectCity')";
   const pleaseWait 		=	"@lang('modules.front.pleaseWait')";   
   let country 				= 	"";
   let state 				= 	"";
</script>

<script>
   function rbutton(cvalue)
   {
		//alert(cvalue); 
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
      $("#snp-immediate").attr('style','display:flex');	
      $("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });	
      $('#text_display').val('Serving Notice Period');
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}else if(cvalue ==2){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$("#np_id").attr('type','date');
			$('#np_id').attr("disabled", false);
      $('#text_display').val('Immediate');
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
                    $('#np_id').removeAttr('min'); // Remove min restriction
                    $('#np_id').attr('max', today); // Set max to today
                    $('#np_id').attr('placeholder', 'Select your last working date (past  dates allowed)'); // Set placeholder
                }
                  break; // Stop the loop once the selected radio button is found
              }
          }
  }
  dateManageFunc();

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
   
   
   $( document ).ready(function() {


    $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });

   	
   $( ".getAdditionalDetail" ).change(function() 
   {	
   
   					var job_id       	=   $(this).val();			
   					var url 			=	"{{ route('service_company.job-applications.getAdditionalDetail',':job_id') }}";
   					url 				=	url.replace(':job_id', job_id);
   		
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {
   																		if (response.status == "success") 
   																		{
   																			$('#job_additional_detail').html(response.view);
   																		} else { 
   																			$('#job_additional_detail').hide();                           
   																		}
   																   }		
   										   
   					});
     
   							
   }).change();
   
   
   $('#country').change(function(){
   		
					var cid				=	$(this).val() ;
					if(cid == 101){
								$('#city_id').show();	
								$('#city').hide();	
					}else{
								$('#city').show();	
								$('#city_id').hide();	
					}
				
   					var url 			=	"{{ route('service_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type		: 'GET',
   									url			: url,
   									success		: function (response) {																		
   																		$('#state').html(response.data);	
   																   }							   
   							});
   		});
    		
   });
   
   var datepicker = $('.dob').datepicker({
         autoclose: true,
         format: 'yyyy-mm-dd',
         endDate: (new Date()).toDateString(),
     });
     
   $('.select2').select2({
         width: '100%'
     });
    
   
   var val = $('#job_id').val(); 	// get Current Selected Job
   if (val != '' && typeof(val) !== 'undefined') {
     getQuestions(val); 			// get Questions by question on page load            
   }
   
   function getQuestions(id) {
         var url = "{{ route('service_company.job-applications.question',':id') }}";
         url = url.replace(':id', id);
   
         $.ajax({
             type: 'GET',
             url: url,
             success: function (response) {
                 if (response.status == "success") {
                     if (response.count > 0) { // Question Found for selected job
                         $('#questionBox').show();
                         $('#questionBoxTitle').show();
                         $('#questionBox').html(response.view);
                     } else { // Question Not Found for selected job
                         $('#questionBox').hide();
                         $('#questionBoxTitle').hide();
                     }
                     $('#show-columns').html(response.requiredColumnsView);
                     $('#show-sections').html(response.requiredSectionsView);
                     if(response.requiredColumnsView !== '') {
                         var datepicker = $('.dob').datepicker({
                             autoclose: true,
                             format: 'yyyy-mm-dd',
                             endDate: (new Date()).toDateString(),
                         });
                         
                         $('.select2').select2({
                             width: '100%'
                         });
   
                         var loc = new locationInfo()
                         loc.getCountries()
                     }
                 }
             }
         });
     }

     function percentage_hike(){
     
     var currentCTC = parseFloat($('#pay_annual').val());
     var expectedCTC = parseFloat($('#bill_annual').val());
     
     var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;
     if (expectedCTC > 0) {
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
   
   function handleFails(response) {
   
         if (typeof response.responseJSON.errors != "undefined") {
             var keys = Object.keys(response.responseJSON.errors);
             console.log(keys);
             $('#createForm').find(".has-error").find(".help-block").remove();
             $('#createForm').find(".has-error").removeClass("has-error");
   
             for (var i = 0; i < keys.length; i++) {
                 // Escape dot that comes with error in array fields
                 var key = keys[i].replace(".", '\\.');
                 var formarray = keys[i];
   
                 // If the response has form array
                 if (formarray.indexOf('.') > 0) {
                     var array = formarray.split('.');
                     response.responseJSON.errors[keys[i]] = response.responseJSON.errors[keys[i]];
                     key = array[0] + '[' + array[1] + ']';
                 }
   
                 var ele = $('#createForm').find("[name='" + key + "']");
   
                 var grp = ele.closest(".form-group");
                 $(grp).find(".help-block").remove();
   
                 //check if wysihtml5 editor exist
                 var wys = $(grp).find(".wysihtml5-toolbar").length;
   
                 if (wys > 0) {
                     var helpBlockContainer = $(grp);
                 } else {
                     var helpBlockContainer = $(grp).find("div:first");
                 }
                 if ($(ele).is(':radio')) {
                     helpBlockContainer = $(grp);
                 }
   
                 if (helpBlockContainer.length == 0) {
                     helpBlockContainer = $(grp);
                 }
   
                 helpBlockContainer.append('<div class="help-block">' + response.responseJSON.errors[keys[i]] + '</div>');
                 $(grp).addClass("has-error");
             }
   
             if (keys.length > 0) {
                 var element = $("[name='" + keys[0] + "']");
                 if (element.length > 0) {
                     $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                 }
             }
         }
     }

   
   $( document ).ready(function() {
      
		   $("#calender").datepicker({   
										onSelect	: function(dateText, inst) {
																					$('#np_id').show(); 
																					$('#np_id').val(dateText);
													   }
		   });
		   
		   $('#datepicker').focus(function(){
				$('#datepicker').datepicker('show');
		   });
   		
   });
   
</script>
<script>
   var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
       url				: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
       paramName		: "file", // The name that will be used to transfer the file
       maxFiles			: 1,
       maxFilesize		: 5, // MB
       addRemoveLinks	: true,
       accept			: function(file, done) {
													   if (file.name == "wow.jpg") {
														   done("Success, you don't.");
													   } else {
														   done();
													   }
       						}
   });
   
</script>
 
@if($page_event == 'EDIT') 
<script>
   $('#update-form').click(function () {				
   		  var formData 					=	new FormData($('#createForm')[0]);					
          $.ajax({
              		  url				:	'{{route('service_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',	
   					  type				:	"POST",
   					  data 				:	formData,
   					  processData		:	false,  	// tell jQuery not to process the data
   					  contentType		:	false,  	// tell jQuery not to set contentType
   					  redirect			:	true,
   					  file				:	true,
   					  success			:	function (response) {
   					  							window.location = response.url;
   					  						},
              			error			:	sfunction (response) {
                  										handleFails(response);
              										}
          		})
      });
</script>
@endif

<script>
   $(document).ready(function(){

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
          maxFilesize: 1, // Max filesize in MB
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
              if (resume_url && resume_url.trim() !== "") {
                  // Decode URI components so stored paths like "07%2C05" display as real filename
                  var rawName = existingFileUrl.split('/').pop();
                  var existingFileName = decodeURIComponent(rawName);
                  if (existingFileName) {
                      existingFileNameFunc(existingFileName);
                      // Populate uploaded_file_path so the server keeps the existing resume
                      // when the form is submitted without uploading a new file
                      document.querySelector('#uploaded_file_path').value = existingFileUrl;
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
              } else {
                  $("#resumeCheck").val('');
                  // Clear path so server does not reference a stale value
                  if (!document.querySelector('#uploaded_file_path').value) {
                      document.querySelector('#uploaded_file_path').value = '';
                  }
              }
          }, 500);


          $(document).on('click','.dropzone-delete',function(){
            $('.dropzone-items').html('');
          })


var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
       
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    
					'resume':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Resume is required"}
                            
                        }
                    },
					
                    'full_name':
                    {
                        validators:
                        {
                            notEmpty:{message: "Full name is required"}
                           
                        }
                    },
					
                    'current_designation':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Current designation is required"}
                           
                        }
                    },
					
                    'current_company':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current company is required"}
                            
                        }
                    },
					
                    'phone':
                    {
                        validators:
                        {
                            notEmpty:{message: "Primary number is required"},
                            digits:{ message: "Primary number must contains only digits"},
                            stringLength:{ min: 10, max: 10, message: "Primary number must contains 10 digits only"}
                            
                        }
                    },
                    
                    'secondary_mobile':
                    {
                        validators:
                        {
                            digits:{ message: "Primary number must contains only digits"},
                            stringLength:{ min: 10, max: 10, message: "Primary number must contains 10 digits only"}
                            
                        }
                    },
					
                    'email':
                    {
                        validators:
                        {
                            notEmpty:{message: "Primary Email is required"},
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
                    
                    'secondrary_email':
                    {
                        validators:
                        {
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            // notEmpty:{message: 'Qualification Name is required'}
                           
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            // notEmpty:{message:"Total Experience is required"},                           
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            // notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current CTC is required"}
                            
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
                    // 'percentage_hike':
                    // {
                    //     validators:
                    //     {
                    //         // notEmpty:{ message: "Hike is required"},                          
                    //         digits:{message: "Hike must contain digits"}
                    //         // stringLength:{ min: 1,	max: 2,	message: "Hike contain 2 digit"}
                          
                    //     }
                    // },
					
                    'country':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            // notEmpty:{message: "State is required"}
                           
                        }
                    },
					
                    'pincode':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Pincode is required"},                           
                            digits:{message: "Pincode must contain digits"},
                            stringLength:{ min: 1,	max: 1,	message: ""}
                           
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            // notEmpty:{ message: 'Skills is required'}
                          
                        }
                    },
                    'specialties':
                    {
                        validators:
                        { notEmpty:{message: "Specialties is required"}
                           
                        }
                    }
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
            // if (submitButton.disabled) {
            //     return; // ⛔ Prevent multiple clicks
            // }
            // submitButton.disabled = true;
            
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
					
												var formData = new FormData($('#createForm')[0]);							
												$.ajax({
														  //  url 				: '{{route('service_company.job-applications.store')}}'+'?_token=' + '{{ csrf_token() }}',			
                              url 				: '{{route('service_company.job-applications.clientappstore')}}'+'?_token=' + '{{ csrf_token() }}',										   
														   type 			: 'POST',
														   data 			: formData,
														   processData		: false,  	// tell jQuery not to process the data
														   contentType		: false,  	// tell jQuery not to set contentType
														   redirect			: true,
														   success			: function (response) {
                                
                                if(response.status == 'coinCheck'){
   																	Swal.fire("Success", response.message , "warning").then(function(){
																		
																	});	
                                   return false;
                                }
                                
                                if(response.status == 'mailcheck'){
                                   $("#save-form").html(`Submit`);
   																	Swal.fire("Success", response.message , "warning").then(function(){
																	});	
                                   return false;
                                }
                                

                                   submitButton.removeAttribute('data-kt-indicator');
                                      submitButton.disabled = true;
                                      Swal.fire({
                                  text				: "You have successfully Submitted",
                                  icon				: "success",
                                  buttonsStyling		: false,
                                  confirmButtonText	: "Ok, got it!",
                                  customClass			: {
                                                confirmButton: "btn btn-primary"
                                              }
                                      }).then(function (result) {
                                          if (result.isConfirmed) {
                                              form.querySelector('[name="email1"]').value 		= "";
                                              form.querySelector('[name="password1"]').value 		= "";
                                          }
                                      });


																				      window.location = response.url;
																			},
															error			: function (response) {
																									//handleFails(response);
																			}
													});
							
                       
                    }, 2000);
                } 
				else 
				{
                   
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

   	// 	$('#save-form').click(function () {
   	// 					var formData = new FormData($('#createForm')[0]);							
   	// 					$.ajax({
   	// 							   url 				: '{{route('service_company.job-applications.clientappstore')}}'+'?_token=' + '{{ csrf_token() }}',								   
   	// 							   type 			: 'POST',
   	// 							   data 			: formData,
   	// 							   processData		: false,  	// tell jQuery not to process the data
   	// 							   contentType		: false,  	// tell jQuery not to set contentType
   	// 							   redirect			: true,
   	// 							    success			: function (response) {
   	// 													window.location = response.url; 
   	// 												},
   	// 							  	error			: function (response) {
   	// 																		//handleFails(response);
   	// 											  	}
   	// 						});
    //  });    
}); 
</script>

<script>
	
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
								success	: function (response) {	$('#city_id').html(response.data); }		
									   
						});	
   								
   	});	
   $('body').on('change', '#current_ctc_cur', function(){
   			var	current_ctc_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_ctc_cur;
   });
   $('body').on('change', '#current_ectc_cur', function(){
   			var	current_ectc_cur	=	$(this).val();
			document.getElementById("current_ctc_cur").selectedIndex = current_ectc_cur;
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").selectedIndex = current_ctc_mode;
   });
   $('body').on('change', '#current_ectc_mode', function(){
   			var	current_ectc_mode	=	$(this).val();
			document.getElementById("current_ctc_mode").selectedIndex = current_ectc_mode;
   });

   document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    console.log("tesrtkgk"+today);
    document.getElementById('dob').setAttribute('max', today);
});


function re_test(text){
  $('.page-loader').css('display', 'flex');
$.ajax({
                  type		: 'POST',
                  url			: "{{ route('service_company.extract.resume-job-applications',':id') }}",
                  data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'resumeText' : text},
                  beforeSend	: function() {
                  },
                  success 	: function (response) {
                    $('.page-loader').css('display', 'none');
                    $('#full_name').val(response.name);
                    $('#phone').val(response.number);
                    $('#email').val(response.email);
                    $('#dob').val(response.dob);
                    $('#current_company').val(response.currentjob);
                    if(response.linkedin !=''){
                      $('#linkedin').val(response.linkedin);
                    }
                    if(response.github !=''){
                       $('#github').val(response.github);
                    }
                    if(response.behance !=''){
                       $('#behance').val(response.behance);
                    }


                      tagifyskill.removeAllTags(); // Clear existing tags first
                      tagifyskill.addTags(response.skills); // Add new tags
                      tagifylocation.removeAllTags(); // Clear existing tags first
                      tagifylocation.addTags(response.country); // Add new tags



                    var resumeText = $('#uploaded_file_path').val();
                      $.ajax({
                        type		: 'POST',
                        url			: "{{ route('service_company.file.unset') }}",
                        data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'file_path' : resumeText},
                        success 	: function (response) {

                        }

                      });

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
                          statedropdown.selectedIndex = ik;
                          $("#state").change();
                          break; // Exit the loop since we found the state
                      }
                  }   
                }, 1000);

                setTimeout(() => {
                  $('city').val(cityName);
                  var city_iddropdown = document.getElementById("city_id");
                  for (var ik = 0; ik < city_iddropdown.options.length; ik++) {
                      if (city_iddropdown.options[ik].text.trim() === cityName) {
                          //Set the option as selected Uttar Pradesh Uttar Pradesh
                          
                          city_iddropdown.selectedIndex = ik;
                          var event = new Event('change');
                          city_iddropdown.dispatchEvent(event);
                          break; 
                      }
                  }   
                }, 2000);

                $.each(response.skills, function(index, skill) {
                    console.log(skill); // Output each skill
                    
                    $('.value_JSON').prepend('<tag title="'+skill+'" contenteditable="false" spellcheck="false" tabindex="-1" class="tagify__tag " value="'+skill+'"><x title="" class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><div><span class="tagify__tag-text">'+skill+'</span></div></tag>');
                  });

                    }
             });	

}

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

                var ctcVal = parseInt($("#ctc").val(), 10);
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
                    $(".CurrentCTC").text(numberToIndianWords(ctcVal));

                    // $(".CurrentCTC").text(ctcWords.charAt(0).toUpperCase() + ctcWords.slice(1));
                    // $(".payratemonthly").text(payMonthlyWords.charAt(0).toUpperCase() + payMonthlyWords.slice(1));
                    // $(".payrateannual").text(payAnnualWords.charAt(0).toUpperCase() + payAnnualWords.slice(1));
                }else{
                    $(".payrateannual").text(' ');
                    $(".payratemonthly").text(' ');
                    $(".CurrentCTC").text(' ');
                }

                // Expected CTC
                var ectcVal = parseInt($("#ectc").val(), 10);
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
                    $(".expectedctc").text(numberToIndianWords(ectcVal));

                    // $(".expectedctc").text(ectcWords.charAt(0).toUpperCase() + ectcWords.slice(1));
                    // $(".billratemonthly").text(billMonthlyWords.charAt(0).toUpperCase() + billMonthlyWords.slice(1));
                    // $(".billrateannual").text(billAnnualWords.charAt(0).toUpperCase() + billAnnualWords.slice(1));
                }else{
                    $(".billrateannual").text('');
                    $(".billratemonthly").text('');
                    $(".expectedctc").text('');
                }
    }
    setTimeout(() => {
          updateCTCFields();
    }, 500);
    $(document).on("keyup", "#ctc", updateCTCFields);
    $(document).on("keyup", "#ectc", updateCTCFields);
    $(".currency").on("change", updateCTCFields);

    $(document).on('keyup', '#ectc', function() { 
      percentage_hike()
     });
     $(document).on('keyup', '#ctc', function() { 
      percentage_hike()
     });

     $(document).on('keyup', '#percentage_hike', function() { 
      ectc();
      percentageBillRate();

     });


     

    function normalizeResumeText(text) {
        return text
            .replace(/[^\x00-\x7F]/g, " ")   // remove broken unicode
            .replace(/\s+/g, " ")
            .replace(/\|/g, " ")
            .trim();
    }



     function handleFileInputChange(event) {
      const file = event.target.files[0];
      if (file.type === 'application/pdf') {
      if (file) {
        const reader = new FileReader();
        reader.onload = async function(event) {
          const typedArray = new Uint8Array(event.target.result);
          const pdfDocument = await pdfjsLib.getDocument(typedArray).promise;
          const numPages = pdfDocument.numPages;
          let resumeText = '';
          // Loop through each page to extract text
          for (let pageNum = 1; pageNum <= numPages; pageNum++) {
            const page = await pdfDocument.getPage(pageNum);
            const textContent = await page.getTextContent();
            const pageText = textContent.items.map(item => item.str).join('\n');
            resumeText += pageText + '\n';
          }
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