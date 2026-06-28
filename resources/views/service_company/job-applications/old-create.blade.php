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
</style>
@endpush

@section('content')
@php 

		  use App\Helper\GeneralMethod;
	
		  $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
		  $photo  					=	url('public/avatar.png');	
		  if($page_event == 'ADD'){
			 $pagetitle			=	'Add Candidate';
			 $resume_url			=	'';	
		  }else if($page_event == 'EDIT'){ 
			 $pagetitle	=	'Edit Candidate';      
			 if(!empty($documents->hashname)){
				$url				=	 $documents->hashname;
				$resume_url			=	'<a href="'.$url.'" target="_blank" download><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>';	
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
		
		$snp_one	=	$snp_two	= $snp_zero	= "";
		$snp_text_one	=	$snp_text_two	= $snp_text_zero	=	'display:none;';		
		$snp_mode = @$application->serving_notice_period_mode;
		if($snp_mode== 1){
			$snp_one		=	'checked="checked"';
			$snp_text_one	=	'display:block';                                 	
		}else if($snp_mode== 2){
			$snp_two	=	'checked="checked"';
			$snp_text_one	=	'display:block';
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
@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">


  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Requisitions=-->
      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->

        <?php  if (!empty($logo_path)) { ?>
          <div data-row-id="' . $row->id . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                    <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5">
                        <img src="{{ str_replace('index.php','', @$logo_path)  }}" alt="Company Logo" class="border-radius-5 w-100">
                      </div>
                    </a>
                  </div>
      <?php	}else{ ?>
      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
        <div class="symbol-label {{@$bgTextDecore}} border-radius-5">{{strtoupper(@$strVal)}}</div>
        </a> 
      </div>

    <?php  } ?>


      
        <!--end::Avatar-->
        <!--begin::User details-->
        <div class="d-flex flex-column me-5"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(substr(@$jobDetail->title, 0, 5)) }}</a> 
			<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">{{ ucwords(substr(@$company_or_client, 0, 10)) }}</a> </div>
        <!--begin::User details-->
      </td>

      <!--end::Requisitions-->
      <!--begin::Experience & Salary-->
      <td>
	  	<div class="d-flex flex-column me-5"> 
	  		<span class="text-dark fw-bolder fs-6">{{ @$jobDetail->min_exp }}-{{ @$jobDetail->max_exp }} Yrs</span> 
			<span class="text-muted fw-bold d-block fs-7">{{ @$jobDetail->min_salary }}-{{ @$jobDetail->max_salary }}</span> 
		</div>
	 </td>
      <!--end::Experience & Salary-->
      <!--begin::Location-->
      <td>
        <div class="d-flex flex-column me-5"> 
			<span class="text-dark fw-bolder fs-6">{{ ucwords($address) }}</span> 
			<span class="text-muted fw-bold d-block fs-7">{{ ucwords($city) }} | {{ ucwords(@$country_name) }}</span> 
		</div>
	  </td>
      <!--end::Location-->
      <!--begin::Status & Req. ID-->
      <td class="d-none">

        <div class="d-flex flex-column me-5"> 
			<span class="badge fw-bolder fs-6 {{ @$status_class }}  px-2 py-1">{{ ucwords(@$jobDetail->status) }}</span> 
			<span class="text-muted fw-bold d-block fs-7">R-{{ @$jobDetail->id }}</span> 
		</div>
	 </td>
      <!--end::Status & Req. ID-->
      <!--begin::Interview Date & No. of Openings-->
      <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
			{{ @$created_at }}
          </span> <span class="text-muted fw-bold d-block fs-7">{{ @$logos->total_positions }}</span> </div></td>
      <!--end::Interview Date & No. of Openings-->
    </div>
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
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="{{ @$jobId }}" id="job_id" />
            <input type="hidden" name="appId" value="{{ @$appId }}" id="appId" />
            <!--<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">-->
            <div class="mb-5">
              <div class="flex-column">
                <!--begin::Upload Photo & Resume/CV-->
                <div class="form-group row fv-row mb-10">
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
                    <div class="fv-row"> <span class="form-text text-muted d-block">Allowed file types: Pdf or Word. &nbsp; {!! @$resume_url !!}</span> </div>
                    <!--end::Input group-->
                  </div>
                </div>
                <!--end::Upload Photo & Resume/CV-->
                <!--begin::Fullname | Designation | Current Company-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Full Name:</label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" placeholder="Enter full name" value="{{ @$application->full_name }}">
                    <span class="form-text text-muted">Please enter your full name</span> </div>
                    <div class="col-lg-2">
                      <label class="form-label">Gender</label>
                      <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="gender" id="gender">
                        <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option> 
                        <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                      </select>
                      <span class="form-text text-muted">Please select gender</span> </div>
                  <div class="col-lg-3">
                    <label class="form-label required">Designation:</label>
                    <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="{{ @$application->current_designation }}">
                    <span class="form-text text-muted">Please enter your designation</span> </div>
                  <div class="col-lg-3">
                    <label class="form-label required">Current Company:</label>
                    <input type="text" class="form-control form-control-solid" name="current_company" id="current_company" placeholder="Enter current company" value="{{ @$application->current_company }}">
                    <span class="form-text text-muted">Please enter your current company</span> </div>
                 </div>
                <!--end::Fullname | Designation | Current Company-->
                <!--begin::Primary Number | Secondary Number | Date of Birth-->
                <div class="form-group row fv-row mb-10 fv-plugins-icon-container has-danger">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="phone" id="phone" placeholder="Enter primary number" value="{{ @$application->phone }}">
                    <span class="form-text text-muted">Please enter primary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Secondary Number:</label>
<input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="{{ @$application->secondary_mobile }}">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Date of Birth:</label>
                    <input type="date" class="form-control form-control-solid"  name="dob" id="dob" placeholder="Enter dob" value="{{ @$application->dob }}" onclick="this.showPicker()" >
                    <span class="form-text text-muted">Please enter your dob</span> </div>
                </div>
                <!--end::Primary Number | Secondary Number | Date of Birth-->
                <!--begin::Primary Email | Secondary Email | Qualifaction-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Email:</label>
                    <input type="email" class="form-control form-control-solid" name="email" id="email" placeholder="Enter primary number" value="{{ @$application->email }}">
                    <span class="form-text text-muted">Please enter primary email</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Secondary Email:</label>
<input type="email" class="form-control form-control-solid" name="secondrary_email" id="secondrary_email" placeholder="Enter secondary number" value="{{ @$application->secondrary_email }}">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
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
                    <span class="form-text text-muted">Please select your qualification</span> </div>
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
                    <label class="form-label required">Total Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="total_exp" id="total_exp" placeholder="Enter total experience" value="{{ @$application->total_exp }}">
                    <span class="form-text text-muted">Please enter total experience</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Relevant Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="rel_exp" id="rel_exp" placeholder="Enter relevant experience" value="{{ @$application->rel_exp }}">
                    <span class="form-text text-muted">Please enter relevant experience</span> 
                    <div class="fv-plugins-message-container invalid-feedback" id="rel_exp_error"></div>
                  </div>
                  <div class="col-lg-4">
                   
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
<input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" {{ @$snp_zero }} >
                      Notice period </label>
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

<input type="text" name="np_id" id="np_id" class="form-control form-control-solid np_id" placeholder="" style="{{ @$snp_text_one }}"  value="{{ @$application->np_id }}" onclick="this.showPicker()">
<input type="number" name="ddl_np_id" id="ddl_np_id" class="form-control form-control-solid" style="<?php echo @$snp_text_zero; ?>" placeholder="Enter Notice Period" value="{{ @$application->np_id }}" >
                    <input type="text" id="calender" name="calender" style="display:none">
                    <span class="form-text text-muted">Please enter noitce period</span> </div>
                </div>
                <!--end::Total Experience | Relevant Experience | Notice Period-->
                <!--begin::Current CTC | Expected CTC | Percentage-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Current CTC</label>
                    <div class="row">
                      <div class="col-3 pe-0">
                        <select class="form-select form-select-solid" arial-label="Select Currency" id="current_ctc_cur" name="current_ctc_cur">
@php 
						   $current_ctc_cur	=	"";
						   $current_ctc_cur	=	@$application->current_ctc_cur;
						   foreach($currency as $key=>$val){
								   $sel	=	'';
								   if($current_ctc_cur == $key){
								   		$sel	=	'selected="selected"';	
								   }											
								   echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
						   }
@endphp
                        </select>
                      </div>
                      <div class="col-5 px-2">
                        <input type="number" class="form-control form-control-solid" name="ctc" id="ctc" placeholder="Enter current CTC" value="{{ @$application->ctc }}">
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
@php 
						$current_ctc_mode	=	"";
						$current_ctc_mode	=	@$application->current_ctc_mode;
						foreach($salaryMode as $key=>$val){
							$sel	=	'';
							if($current_ctc_mode == $key){
								$sel	=	'selected="selected"';	
							}	
							echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
						}
@endphp
                        </select>
                      </div>
                    </div>
                    <span class="form-text text-muted CurrentCTC">Please enter your current ctc</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Expected CTC</label>
                   
                    <div class="row">
                      <div class="col-3 pe-0">
                        <select class="form-select form-select-solid" arial-label="Select Currency" name="current_ectc_cur" id="current_ectc_cur">
@php 
						$current_ectc_cur	=	"";
						$current_ectc_cur	=	@$application->current_ectc_cur;
						foreach($currency as $key=>$val){
							$sel	=	'';
							if($current_ectc_cur == $key){
								$sel	=	'selected="selected"';	
							}	
							echo '<option value="'.$key.'"   '.$sel.' >'.$val.'</option>';	
						}
@endphp
                        </select>
                        
                      </div>
                      <div class="col-5 px-2">
                        <input type="number" class="form-control form-control-solid" name="ectc" id="ectc" placeholder="Enter expected CTC" value="{{ @$application->ectc }}" >
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
@php 
						$current_ectc_mode	=	"";
						$current_ectc_mode	=	@$application->current_ectc_mode;
						foreach($salaryMode as $key=>$val){
							$sel	=	'';
							if($current_ectc_mode == $key){
								$sel	=	'selected="selected"';	
							}
							echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
						}
@endphp
                        </select>
                      </div>
                      <span class="form-text text-muted expectedctc">Please enter your expected ctc</span> </div>
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Percentage</label>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <input type="number" class="form-control form-control-solid" name="percentage_hike" id="percentage_hike" placeholder="Enter hike" value="{{ @$application->percentage_hike }}">
                        <span class="form-text text-muted">Hike in percentage</span> </div>
                      <div class="col-8">
                        <input type="text" class="form-control form-control-solid" name="comment" id="comment" placeholder="Enter comment" value="{{ @$application->comment }}">
                      </div>
                    </div>
                  </div>
                </div>
                <!--end::Current CTC | Expected CTC | Percentage-->
                <!--begin::Country | State | City | Pincode-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
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
							<option value="{{ @$countryVal['id'] }}"  {{ @$sel}} >{{ @$countryVal['name'] }}</option>	
					@endforeach

                    </select>
                    <span class="form-text text-muted">Please enter your Country</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">State</label>
                    <!--<select class="select2 states form-select form-select-solid" name="state" id="stateId">
                                 <option value="0">Select State</option>
                                 </select> -->
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
                    <span class="form-text text-muted">Please enter your State</span> </div>
                  <div class="col-lg-4">
                    <div class="row">
                      <div class="col-6 pe-0">
                        <label class="form-label required">City</label>
                        <input type="text" class="form-control form-control-solid" name="city" id="city" placeholder="Enter city" value="{{ @$application->city }}"  {{ @$display_city }} >
                        <select class="form-select form-control form-control-lg form-control-solid" name="city_id" id="city_id" {{ @$display_city_id }} >
                          <option value="">Select City</option>
@php 
					$sel		=	'';
					if(!empty($cityListArr)){
						foreach($cityListArr as $key=>$sVal){
							$sel	=	'';
							if($application->city_id == @$sVal['id'] ){
								$sel	=	'selected="selected"';	
							}
							echo '<option value="'.@$sVal['id'].'"  '.@$sel.' >'.@$sVal['name'].'</option>';	
						}
					}
@endphp
                        </select>
                        <span class="form-text text-muted">Please enter your city</span> </div>
                      <div class="col-6">
                        <label class="form-label required">Pincode</label>
                        <input type="number" class="form-control form-control-solid" name="pincode" id="pincode" placeholder="Enter pincode" value="{{ @$application->pincode }}">
                        <span class="form-text text-muted">Please enter your pincode</span> </div>
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
                    <label class="form-label required">Skills</label>
                    <!-- <input class="form-control mb-3" value="tag1, tag2, tag3" id="kt_tagify_4"/> -->
                    <!-- <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="<?php echo @$application->
                    skills; ?>" id="kt_tagify_1111" name="skills" /> -->
                    <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
                    <span class="form-text text-muted">Please enter your skills</span> </div>
                </div>
                <!--end::Skills-->
                <!--begin::Custom Details-->
                <div class="dashed-border-top" id="job_additional_detail">
        @php 
				  if(!empty($job_additional_detail)){
				  
					$customTitleId	=	1;
					$html			=	'';
					$html			=	'<h3 class="py-5">Custom Details</h3>';
					$inc_first		=	0;
					$inc_last		=	1;
					foreach($job_additional_detail as $aDetailArr){
						
						$ans_value	=	'';
						if(!empty($application_additional_detail)){
							foreach($application_additional_detail as $aval){
									if($aDetailArr->id == $aval->question_id){
										$ans_value	=	@$aval->application_detail_value;	
									}
							}
						}
						
						if($inc_first %3 == 0) {
							$html	.=	'<div class="form-group row fv-row mb-10">';	
						}	
						$html		.=	'<div class="col-md-4">
										   <label class="form-label">'.strtoupper($aDetailArr->detail_value).':</label>
										   <input type="hidden" name="job_question[]" value="'.$aDetailArr->id.'">
										   <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
										   <span class="text-muted form-text">Please enter your details</span>
										</div>';
										 
						if(($inc_last %3 == 0)&& $inc_last > 0){
							$html		.='</div>';
						}
						$inc_first++;
						$inc_last++;
						$customTitleId++;				
					}
					echo $html;	
				  }	
                              			
@endphp
                </div>
                <!--end::Custom Details-->
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
                    <a href="javascript:void(0);" class="btn btn-secondary">Cancel</a> </div>
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


@push('footer-script')

@php if($page_event == 'EDIT'){ @endphp
<script src="{{ asset('demo/v7/js/edit-applicant.js') }}"></script>	
<script>

  ///// update  old code  start
		   $('#update-form_now').click(function () {  							
				  var formData 					=	new FormData($('#createForm')[0]);			
          $("#update-form").prop("disabled", true);
                  $("#update-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );		
				  $.ajax({
							  url				: '{{route('service_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',	
							  type				: "POST",
							  data 				: formData,
							  processData		: false,  				// tell jQuery not to process the data
							  contentType		: false,  				// tell jQuery not to set contentType
							  //container		: '#createForm',
							 
							  redirect			: true,
							  file				: true,
							  //data			: $('#createForm').serialize(),
							  success			: function (response) {
                  // alert(response.url);
														window.location = response.url;
													},
								error			: function (response) {
                  $("#update-form").prop("disabled", false);
                  $("#update-form").html(`Update`);	
																handleFails(response);
															}
						})
			  });

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
                            notEmpty:{ message: "Current company is required"}
                            
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
					
                    'email':
                    {
                        validators:
                        {
                            notEmpty:{ message: 'Primary email is required'},
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            notEmpty:{message: 'Qualification Name is required'}
                           
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            notEmpty:{message:"Total Experience is required"}
                           
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Current CTC is required"}
                            
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
                    'percentage_hike':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Hike is required"},                          
                            digits:{message: "Hike must contain digits"}
                            // stringLength:{ min: 1,	max: 2,	message: "Hike contain 2 digit"}
                          
                        }
                    },
					
                    'country':
                    {
                        validators:
                        {
                            notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            notEmpty:{message: "State is required"}
                           
                        }
                    },
					
                    'pincode':
                    {
                        validators:
                        {
                            notEmpty:{message: "Pincode is required"},                           
                            digits:{message: "Pincode must contain digits"},
                            stringLength:{ min: 6,	max: 6,	message: "Pincode contain 6 digit only"}
                           
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            notEmpty:{ message: 'Skills is required'}
                          
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

       
        submitButton.addEventListener('click', function (e) {
           
            e.preventDefault();
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
												$.ajax({
                          url				: '{{route('service_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
														   type 			: 'POST',
														   data 			: formData,
														   processData		: false,  	// tell jQuery not to process the data
														   contentType		: false,  	// tell jQuery not to set contentType
														   redirect			: true,
														   success			: function (response) {
																				window.location = response.url;
																			},
															error			: function (response) {
																									//handleFails(response);
																			}
													});
							
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = true;
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
	new Tagify(input2);

   function rbutton(cvalue)
   {
		$('#np_id').val('');
		$('#ddl_np_id').val('');
		if(cvalue == 0){
			$('#ddl_np_id').show();
			$('#np_id').hide();		
		}else if(cvalue==1){
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$('#np_id').attr("disabled", false);	
			$("#np_id").attr('type','date');
			$("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });			
		}else if(cvalue ==2){
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$("#np_id").attr('type','text');
			$('#np_id').attr("disabled", true);
			$('#np_id').val('Immediate');
		}	
   }
   var today = new Date().toISOString().split('T')[0];
   $('#np_id').attr('min', today);
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
								type	: 'GET',
								url		: url,
								success	: function (response) {																		
																$('#state').html(response.data);
											}		
					});
          
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

            var ones = ['','one','two','three','four','five','six','seven','eight','nine'];
            var teens = ['ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
            var tens = ['','ten','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];

     $(document).on('keyup', '#ctc', function() { 
            var number = parseInt($('#ctc').val());
            var result = convertToWords(number);
                $('.CurrentCTC').text(capitalizeFirstLetter(result));

     });
     $(document).on('keyup', '#ectc', function() { 
            var number = parseInt($('#ectc').val());
            var result = convertToWords(number);
                $('.expectedctc').text(capitalizeFirstLetter(result));
                percentage_hike();

     });
     $(document).on('keyup', '#percentage_hike', function() { 
      ectc();

     });

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


		
</script>



<script>
   $(document).ready(function(){
   		$('#country').trigger('change');
	});	
</script>	

<script>

"use strict";

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
                            notEmpty:{message: "Resume is required"}
                            
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
                            notEmpty:{ message: "Current company is required"}
                            
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
                            notEmpty:{message: 'Qualification Name is required'}
                           
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            notEmpty:{message:"Total Experience is required"},                           
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Current CTC is required"}
                            
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
                    'percentage_hike':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Hike is required"},                          
                            digits:{message: "Hike must contain digits"}
                            // stringLength:{ min: 1,	max: 2,	message: "Hike contain 2 digit"}
                          
                        }
                    },
					
                    'country':
                    {
                        validators:
                        {
                            notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            notEmpty:{message: "State is required"}
                           
                        }
                    },
					
                    'pincode':
                    {
                        validators:
                        {
                            notEmpty:{message: "Pincode is required"},                           
                            digits:{message: "Pincode must contain digits"},
                            stringLength:{ min: 6,	max: 6,	message: "Pincode contain 6 digit only"}
                           
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            notEmpty:{ message: 'Skills is required'}
                          
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

       
        submitButton.addEventListener('click', function (e) {
           
            e.preventDefault();
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
																				window.location = response.url;
																			},
															error			: function (response) {
																									//handleFails(response);
																			}
													});
							
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

function re_test(text){

  $.ajax({
                    type		: 'POST',
                    url			: "{{ route('service_company.extract.resume-job-applications',':id') }}",
                    data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'resumeText' : text},
                    beforeSend	: function() {
                    },
                    success 	: function (response) {

                      $('#full_name').val(response.name);
                      $('#phone').val(response.number);
                      $('#email').val(response.email);
                      $('#dob').val(response.dob);
                      $('#current_company').val(response.currentjob);

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


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
}
function convertToWords(num) {
                if (num < 10) {
                    return ones[num];
                } else if (num < 20) {
                    return teens[num - 10];
                } else if (num < 100) {
                    return tens[Math.floor(num / 10)] + ' ' + ones[num % 10];
                } else if (num < 1000) {
                    return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100);
                } else if (num < 100000) {
                    return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000);
                } else if (num < 10000000) {
                    return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000);
                } else if (num < 1000000000) {
                    return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000);
                }
                return "Number out of range";
            }



          
    function percentage_hike(){
     
        var currentCTC = parseFloat($('#ctc').val());
        var expectedCTC = parseFloat($('#ectc').val());
        
        var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;
        
        $('#percentage_hike').val(percentageIncrease.toFixed(0));
    }

    function ectc(){
        var currentCTC = parseFloat($('#ctc').val());
        var percentage_hike = $('#percentage_hike').val();
        var amount = currentCTC*percentage_hike/100;
        $('#ectc').val(amount+currentCTC);
        
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


          re_test(resumeText);


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
            re_test(resumeText);

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