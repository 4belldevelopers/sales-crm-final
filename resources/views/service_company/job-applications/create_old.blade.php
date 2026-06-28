@extends('layouts.service_company_layout')
@push('head-script')
<script>
   $( function(){
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
body{overflow-x:hidden;}
.dropzone{padding: 1.8rem 1.75rem!important; border: 2px dashed #e6e7e8!important; background:transparent!important; text-align: inherit!important;}
.border-radius-rb {border-top-right-radius: 0em!important;}
.border-radius-rt {border-bottom-right-radius: 0em!important;}
.border-radius-lt {border-bottom-left-radius: .4em!important;}
.border-radius-lb {border-top-left-radius: .4em!important;}
.font-size-icon {font-size: 1.3em;}
.dashed-border-top {border-top:1px dashed gray;}
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none;}
#np_id::-webkit-inner-spin-button,
#np_id::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none;}

.tagify .tagify__tag div .tagify__tag-text {
    color: #0c0c0c !important;
    font-weight: 500 !important;
    font-size: 14px !important;
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
    input::placeholder {
      font-size: 11px; /* Placeholder text size */
      color: gray; /* Optional: Change placeholder color */
  }
  input {
    font-size: 11px; /* Input text size */
  }
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

		  use App\Helper\GeneralMethod;
       $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
      $country_code =  GeneralMethod::countryCodeFun();
      $currency_code =  GeneralMethod::defaultCurrency();
      // dd($currency_code);

      $phone_code = $country_code;
      $second_phone_code = $country_code;
      $url= '';
	
		  $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
		  $photo  					=	url('public/avatar.png');	
		  if($page_event == 'ADD'){
			 $pagetitle			=	'Add Candidate';
			 $resume_url			=	'';	
		  }else if($page_event == 'EDIT'){ 

        $phone_code = ($application->phone_code) ? $application->phone_code : $phone_code;
        $second_phone_code = ($application->second_phone_code) ? $application->second_phone_code : $second_phone_code;

			 $pagetitle	=	'Edit Candidate';   
      //  dd($application);   
			 if(!empty($application->resume)){
				$url				=	 $application->resume;
        // $url = @$application->hashname;
        $info = pathinfo($url);
        if($resumeAwsPath){
            $resume_url			=	'<a href="'.route('download.resume', ['id' => $application->id,'path' => urlencode($url)]).'" target="_blank" download><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>';
        }else{
           $resume_url			=	'';	
        }
			 }else{
				$resume_url			=	'';	
			 }
			 if(!empty($application->photo)){
				//$photo			=	 url('/public/user-uploads/candidate-photos/'.$application->photo);	
				$photo			=	 $application->photo;
			 }   
		  }	
					
     

		$bgTextDecore				=	$strVal	=	'';
		$str						=	@$jobDetail->title;
    // dd($str);
		$str	= $strVal			=	strtolower($str[0]);
		$str					    =	ord($str);
		if($str >=97 && $str <=101){
			$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
		}else if($str >=102 && $str <=106){
			$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
		}else if($str >=107 && $str <=111){
			$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
		}else if($str >=112 && $str <=116){
			$bgTextDecore		=	'fs-3 bg-light-success text-success';	
		}else if($str >=117 && $str <=121){
			$bgTextDecore		=	'fs-3 bg-dark text-light';	
		}else{
			$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
		}	

		$city			=	$country_id		=	 $country_name	=	$state_name		=	$address		=	'';
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
		
		$created_at 		=	"";
		$created_at 		=	@$jobDetail->created_at;
		$created_at 		=	date("d M, Y", strtotime($created_at));
		
		$snp_one	=	$snp_two	= $snp_zero	= $snp_text_dummy = $snp_last_working = "";
		$snp_text_one	=	$snp_text_two	= $snp_text_zero	=	'display:none;';		
		$snp_mode = @$application->serving_notice_period_mode;
		if($snp_mode== 1){
			$snp_one		=	'checked="checked"';
			$snp_text_one	=	'display: flex;';
      $snp_text_dummy = 'Serving Notice Period';   
      $snp_last_working = $application->np_id;                                 	
		}else if($snp_mode== 2){
			$snp_two	=	'checked="checked"';
			$snp_text_one	=	'display: flex;';
      $snp_text_dummy = 'Immediate';  
      $snp_last_working = $application->last_working_date;   
		}else{
			$snp_zero		=	'checked="checked"';	
			$snp_text_zero	=	'display:block';
		}  
    $logos = GeneralMethod::companyDetails($jobDetail->id);

    $logo_path = '';
    
    $logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
       if (!empty($logos->logo_logical_path)) {
					$logo_path = url('public/' . @$logos->logo_logical_path);
				}
        
$logo_path = GeneralMethod::companyDetails($jobDetail->id);
$company_or_client = GeneralMethod::companyDetailsByClient($jobDetail->id);	
$Jobcities = GeneralMethod::Jobcities($jobDetail->id);	
$locations  = explode('`,`',$Jobcities);
$salaryModeSelect = GeneralMethod::salaryMode();
$currencySymbole = GeneralMethod::currencySymbole();
        $min_sal_mode = strtoupper(substr($salaryModeSelect[$jobDetail->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryModeSelect[$jobDetail->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$jobDetail->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$jobDetail->max_salary_currency];
        $max_salary = GeneralMethod::thousandsCurrencyFormat($jobDetail->max_salary);
        $salary_currency = @$jobDetail->max_salary_currency ?? 0;

        // dd($min_sal_mode,$max_sal_mode,$min_salary_currency,$max_salary_currency);

       	

@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">


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
            <input type="hidden" name="currentSlug" value="{{ @$currentSlug }}" id="currentSlug" />
            <input type="hidden" name="resumeCheck" value="1" id="resumeCheck" />
            <input type="hidden" name="AddJobWiseCondidateRankRow" id="AddJobWiseCondidateRankRow" value="{{ (@$AddJobWiseCondidateRankRow) ? $AddJobWiseCondidateRankRow : '' }}">
            <div class="mb-5">
              <div class="flex-column">
                <!--begin::Upload Photo & Resume/CV-->
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
                    <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume" id="resume">
                    <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                    <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.. &nbsp; {!! @$resume_url !!}</span> </div>
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
                    <label class="form-label required">Current Designation:</label>
                    <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}">
                    <span class="form-text text-muted">*Enter the current designation of the candidate</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Current Company Name:</label>
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
                    <span class="form-text text-muted">*Enter the primary number of the candidate. </span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Enter Secondary Number:</label>
                  <input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="{{ @$application->secondary_mobile }}">
                  <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code">
                    <span class="form-text text-muted">*Enter the Secondary number of the candidate.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Please enter date of birth of the candidate:</label>
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
                    <label class="form-label">Qualification:</label>
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
                    <span class="form-text text-muted">*Please select the qualification of the candidate</span> </div>
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
						@foreach($salaryMode as $key=>$val){
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
                      
                      //  $total_exp = explode('.',@$application->total_exp);

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

                      //  dd($selected_exp_year,$selected_exp_month);

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
                                              // $selected = ($selected_exp_year != null && $i === $selected_exp_year) ? 'selected' : '';
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>

                        {{-- <input type="number" class="form-control form-control-solid" name="total_exp" id="total_exp" placeholder="Enter total experience" value="{{ @$application->total_exp }}"> --}}

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
                    <span class="form-text text-muted">Please enter total exp.</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Relevant Experience:</label>

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
                      //  dd($total_rel_exp);
                      //  dd($selected_rel_exp_year,$selected_rel_exp_month);

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
                   
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                       <input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" {{ @$snp_zero }} >
                      Notice period</label>
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
                          {{-- Serving Notice Period --}}
                          <div style="position: relative;">
                              <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select last working date" name="np_id_display" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" onclick="document.getElementById('np_id').click()" readonly>
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
                      {{-- <input type="number" name="ddl_np_id" id="ddl_np_id" class="form-control form-control-solid"  placeholder="Enter Notice Period" value="{{ @$application->np_id }}" > --}}

                    <input type="text" id="calender" name="calender" style="display:none">

                    <span class="form-text text-muted">Please Enter the current notice period of the candidate</span> 
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
                  {{-- <div class="col-lg-12"> --}}
                         
                    {{-- <label class="form-label">Country</label>
                    <select class="form-select form-select-solid" arial-label="Select Currency" name="country" id="country" >
                      <option value="">Select Country</option> --}}
 
                          {{-- @foreach($countries as $countryVal)
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
                              <option value="{{ @$countryVal['id'] }}"  {{ @$sel}} >{{ @$countryVal['name'] }}</option>	
                          @endforeach --}}

                    {{-- </select>
                    <span class="form-text text-muted">Please enter your Country</span> 
                  </div> --}}
                  {{-- <div class="col-lg-4">
                    <label class="form-label">State</label>
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
                      <div class="col-10">
                        <label class="form-label">Comments</label>
                        <input type="text" class="form-control form-control-solid" name="comment" id="comment" placeholder="Enter comment" value="{{ @$application->comment }}">
                      </div>
                      
                      <div class="col-2 pe-0">
                        <label class="form-label">Percentage</label>
                        <input type="text" class="form-control form-control-solid" name="percentage_hike" id="percentage_hike" placeholder="Enter hike" value="{{ @$application->percentage_hike }}">
                        <span class="form-text text-muted"></span> 
                      </div>
                      
                      <div class="col-12">
                        <label class="form-label">Enter Current Location of the candidate</label>
                        <input class="form-control form-control-solid" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter your Location..."/>
                        
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
                                 // The DOM element you wish to replace with Tagify
                                 var input = document.querySelector('input[name=skills]'); 
                                 // initialize Tagify on the above input node reference
                                 new Tagify(input)
                                       function isNumberKey(evt)
                                       {
                                          var charCode = (evt.which) ? evt.which : evt.keyCode
                                          if (charCode > 31 && (charCode < 48 || charCode > 57))
                                             return false;
                                          return true;
                                       }
                     </script>
                    <label class="form-label">*Enter the relevant skills of the candidate (Max 10 skills allowed).</label>
                    <!-- <input class="form-control mb-3" value="tag1, tag2, tag3" id="kt_tagify_4"/> -->
                    <!-- <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="<?php echo @$application->
                    skills; ?>" id="kt_tagify_1111" name="skills" /> -->
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

                                
                                  //  dd($skill_val->skill_value);
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
                    $html			=	'';
                    foreach($job_additional_detail as $aDetailArr)
                    {
                      if(!empty($aDetailArr->detail_value)){
                        $html		=	'<h3 class="py-5">Job Questions</h3>';
                      }
                    }
                    $inc_first		=	0;
                    $inc_last		=	1;
                     $html	.=	'<div class="form-group row fv-row mb-10">';	
                    foreach($job_additional_detail as $aDetailArr){
                      if(!empty($aDetailArr->detail_value)){
                      
                      $ans_value	=	'';
                      if(!empty($application_additional_detail)){
                        foreach($application_additional_detail as $aval){
                            if($aDetailArr->id == $aval->question_id){
                              $ans_value	=	@$aval->application_detail_value;	
                            }
                        }
                      }
						
                            // if($inc_first %3 == 0) {
                             
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
					 if($page_event == 'EDIT'){ 
						echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update</button>';
					 }else{
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


    let linkedinRegex = /^(https?:\/\/)?(www\.)?linkedin\.com\/.*$/i;
    let githubRegex   = /^(https?:\/\/)?(www\.)?github\.com\/[A-Za-z0-9_-]+\/?$/i;
    let behanceRegex  = /^(https?:\/\/)?(www\.)?behance\.net\/[A-Za-z0-9_-]+\/?$/i;

  function validateLink(input, regex, messageId, platform) {
          let value = $(input).val().trim();
          if (value === "") {
              $(messageId).html(""); // Clear if empty
              return;
          }
          if (regex.test(value)) {
              $(messageId).html("✅ Valid " + platform).css("color", "green");
          } else {
              $(messageId).html("❌ Invalid " + platform).css("color", "red");
          }
      }


    $("#linkedin").on("change", function () {
        validateLink(this, linkedinRegex, "#linkedin-msg", "LinkedIn");
    });

    $("#github").on("change", function () {
        validateLink(this, githubRegex, "#github-msg", "GitHub");
    });

    $("#behance").on("change", function () {
        validateLink(this, behanceRegex, "#behance-msg", "Behance");
    });


  localStorage.setItem('currentPage', "{{ @$currentPage }}");
  
var inputloc = document.querySelector("#kt_tagify_work_location");
var cities = @json($cities);


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

@php if($page_event == 'EDIT'){ @endphp
<script>


  ///// update  old code  start
		  //  $('#update-form_now').click(function () {  							
			// 	  var formData 					=	new FormData($('#createForm')[0]);			
      //     $("#update-form").prop("disabled", true);
      //             $("#update-form").html(
      //               `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      //             );		
			// 	  $.ajax({
			// 				  url				: '{{route('service_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',	
			// 				  type				: "POST",
			// 				  data 				: formData,
			// 				  processData		: false,  				// tell jQuery not to process the data
			// 				  contentType		: false,  				// tell jQuery not to set contentType
			// 				  //container		: '#createForm',
							 
			// 				  redirect			: true,
			// 				  file				: true,
			// 				  //data			: $('#createForm').serialize(),
			// 				  success			: function (response) {
      //             // alert(response.url);
			// 											window.location = response.url;
			// 										},
			// 					error			: function (response) {
      //             $("#update-form").prop("disabled", false);
      //             $("#update-form").html(`Update`);	
			// 													handleFails(response);
			// 												}
			// 			})
			//   });

///////////////// update end old code 


//// new code for update  start
    var KTUPDATECandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
       
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
					
                  // 'resume':
                  //   {
                  //       validators:
                  //       {
                  //           file: {
                  //               maxSize: 5242880, // 5 MB in bytes
                  //               message: "The file size must not exceed 5 MB",
                  //               type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  //               message: "Only PDF, DOC, or DOCX files are allowed"
                  //           }
                  //       }
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
                            notEmpty:{ message: 'Primary email is required'},
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
                            // notEmpty:{message:"Total Experience is required"}
                           
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
                  $("#update-form").prop("disabled", true);
                  $("#update-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    setTimeout(function () {					
					
												var formData = new FormData($('#createForm')[0]);		
                        // formData.append('_method', 'PUT');					
												$.ajax({
                          url				: '{{route('service_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
														   type 			: 'POST',
														   data 			: formData,
														   processData		: false,  	// tell jQuery not to process the data
														   contentType		: false,  	// tell jQuery not to set contentType
														   redirect			: true,
														   success			: function (response) {

                                 $("#update-form").prop("disabled", false);
                                if(response.status == 'mailcheck'){
                                   $("#update-form").html(`Update`);
   																	Swal.fire("Success", response.message , "warning").then(function(){
																	});	
                                   return false;
                                }


                                submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    Swal.fire({
                                text				: "You have successfully updated",
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
										submitButton 	=	document.querySelector('#update-form');
										handleForm();
									}
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTUPDATECandidate.init();
});


</script>	
@php } @endphp

<script>

	var input2 = document.querySelector("#kt_tagify_2");
	// new Tagify(input2);

  var tagifyskill =  new Tagify(input2, {
        whitelist: [''],
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions 
            classname: "", // <- custom classname for this dropdown, so it could be targeted tagify__inline__suggestions
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
        },
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


 
                             
    // Initialize Tagify script on the above inputs
    var tagifylocation =  new Tagify(inputloc, {
        whitelist: cities,
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
      tagifylocation.dropdown.show(); // show the dropdown immediately
    });

    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
      tagifylocation.addTags([e.detail.data.value]);
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
    }, 300); // 300ms delay

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
      $('#text_display').val('Serving Notice Period');
			$("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });	
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}else if(cvalue ==2){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			// $("#np_id").attr('type','text');
			$('#np_id').attr("disabled", false);
      $("#np_id").attr('type','date');
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
        // document.getElementById('np_id_display').value = datess;
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
  
      setTimeout(() => {

        $('#state option:eq({{ @$state }})').prop('selected', true);
       
      }, 1000);
	
	$(document).on('change', '.change_state', function() { 
   				
				  var country				=	$('#country').val(); 
   				var state_id			=	this.value;				
   								
				if(country == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').hide();	
					$('#city_id').show();	
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

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
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
          previewTemplate: previewTemplate,
          dictMaxFilesExceeded: "You can only upload one file. Please delete the existing file before adding a new one.", // Custom message for max file limit
          previewsContainer: id + " .dropzone-items", // Define the container to display the previews
          clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
          headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Pass CSRF token
          },
          
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
              $('.dropzone-items').append('<div class="dropzone-item dz-processing dz-success dz-complete" style="" id="">                                  <!--begin::File-->                                  <div class="dropzone-file">                                      <div class="dropzone-filename" title="some_image_file_name.jpg">                                          <span data-dz-name="">'+existingFileName+'</span>                                          <strong><span data-dz-size=""><strong></strong> </span></strong>                                      </div>                                      <div class="dropzone-error" data-dz-errormessage=""></div>                                  </div>                                  <!--end::File-->                                  <!--begin::Progress-->                                  <div class="dropzone-progress">                                      <div class="progress" style="opacity: 0;">                                          <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress="" style="opacity: 0; width: 100%;">                                          </div>                                      </div>                                  </div>                                  <!--end::Progress-->                                  <!--begin::Toolbar-->                                  <div class="dropzone-toolbar">                                      <span class="dropzone-delete dfsdfgssdsss dropzone-delete-cus" data-dz-remove=""><i class="bi bi-x fs-1"></i></span>                                  </div>                                  <!--end::Toolbar-->                              </div>')   
          }

         
          setTimeout(() => {
              var existingFileUrl = "{{ $url }}";
              var resume_url = "{{ $resume_url }}";

              if (resume_url && resume_url.trim() !== "") {
                  // var existingFileName = "Jero_UMESH_SADA.docx"; // Name of the file
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

     


@php if($page_event != 'EDIT'){ @endphp
var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
       
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    
				       	  // 'resume':
                  //   {
                  //     validators:
                  //       {
                  //           file: {
                  //               maxSize: 5242880, // 5 MB in bytes
                  //               message: "The file size must not exceed 5 MB",
                  //               type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  //               message: "Only PDF, DOC, or DOCX files are allowed"
                  //           }
                  //       }
                  // },
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
            if (submitButton.disabled) {
                return; // ⛔ Prevent multiple clicks
            }
            submitButton.disabled = true;

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
                submitButton.disabled = false;
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
														   url 				: '{{route('service_company.job-applications.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
														   type 			: 'POST',
														   data 			: formData,
														   processData		: false,  	// tell jQuery not to process the data
														   contentType		: false,  	// tell jQuery not to set contentType
														   redirect			: true,
														   success			: function (response) {
                                $("#save-form").prop("disabled", false);
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
                                $("#save-form").prop("disabled", false);
																									//handleFails(response);
																			}
													});
							
                  
                    }, 2000);
                } 
				else 
				{
              submitButton.disabled = false;     
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
function re_test(text){
  var jobId = "{{ @$jobId }}";
  $('.page-loader').css('display', 'flex');
  var page_event_check = "{{$page_event}}";
  $.ajax({
                    type		: 'POST',
                    url			: "{{ route('service_company.extract.resume-job-applications',':id') }}",
                    data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'resumeText' : text,'jobId':jobId},
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
                      $('#current_company').val(response.currentjob);
                      $('#qualification').val(response.qualification).change();

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

                }

                  

                  // $.each(response.skills, function(index, skill) {
                  //     console.log(skill); // Output each skill
                      
                  //     $('.value_JSON').prepend('<tag title="'+skill+'" contenteditable="false" spellcheck="false" tabindex="-1" class="tagify__tag " value="'+skill+'"><x title="" class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><div><span class="tagify__tag-text">'+skill+'</span></div></tag>');
                  //   });

                      }
               });	

}


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

          
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

    
$(document).ready(function() {
    $.noConflict();  
        var optionFormat = function(item) {
            if ( !item.id ) {
                return item.text;
            }

            var span = document.createElement('span');
            var imgUrl = item.element.getAttribute('data-kt-select2-user');
            var template = '';

            template += '';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }

        $('#city_id').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').change(function(){  
   				var cid				=	$(this).val() ;
          var cityId				=	"{{ (@$application->city_id) ? @$application->city_id : 0 }}"; 
            if(cid == 101){
              $('#city_id').show();	
              $('#city').hide();	
            }else{
              $('#city').hide();	
              $('#city_id').show();	
            }
              //route('service_company.job-applications.statelist',':cid') 
              var url 			=	"{{ route('countrycitylist',':cid') }}";
              url 				=	url.replace(':cid', cid);
              $.ajax({
                      type	: 'GET',
                      url		: url,
                      success	: function (response) {																		
                                        $('#city_id').html(response.data);
                                        setTimeout(() => {
                                        if(cityId	>=1){
                                          $('#city_id').val(cityId).change();
                                        }
                                      
                                      }, 500);
                                      }		
              });
        });
        $('#country').trigger('change');
			
   
   });





       


</script>

@endpush