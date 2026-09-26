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

<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/job-application-create.css') }}{{ TM_VERSION }}&remaining_candidate_cards_yellow=20260925&candidate_header_compact_icons=20260925&candidate_design_repair=20260926&candidate_header_harmonize=20260926&candidate_rating_view=20260926&resume_card_design=20260926&resume_card_clean=20260926" rel="stylesheet" type="text/css" />

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
      $resumeCheck = '';
		  $resume_url				=	'';
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
           if (!empty($application->resume)) {
            $resume_url			=	route('download.resume', ['id' => $application->id, 'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]);
            $resumeCheck = 1;
           }
        }else{
           $resume_url			=	'';
        }
			 }else{
				$resume_url			=	'';	
			 }
			 if(!empty($application->photo)){	
				$photo			=	 $application->photo;
			 }   
		  }	
					
      $bgTextDecore				=	$strVal	=	'';
      $str						=	@$jobDetail->title;
      // dd($str);
      $firstChar = (!empty($str) && is_string($str)) ? strtolower(substr($str, 0, 1)) : '';
      $strVal = $firstChar;
      $str = !empty($firstChar) ? ord($firstChar) : 0;
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
		
      $snp_mode = null;
      if (isset($application) && !empty($application->id)) {
        $stored_snp_mode = (int) ($application->serving_notice_period_mode ?? 0);
        if (in_array($stored_snp_mode, [1, 2], true) || ($stored_snp_mode === 0 && !empty($application->np_id))) {
          $snp_mode = $stored_snp_mode;
        }
      }
      $snp_last_working = '';
      if ($snp_mode === 1) {
        $snp_last_working = @$application->np_id;
      } elseif ($snp_mode === 2) {
        $snp_last_working = @$application->last_working_date;
      }      $jobDetailId = @$jobDetail->id ?? 0;
      $logos = $jobDetailId ? GeneralMethod::companyDetails($jobDetailId) : null;
      $logo_path = '';
      $logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
      if (!empty($logos->logo_logical_path)) {
        $logo_path = url('public/' . @$logos->logo_logical_path);
      }
        
      $company_or_client = $jobDetailId ? GeneralMethod::companyDetailsByClient($jobDetailId) : null;	
      $Jobcities = $jobDetailId ? GeneralMethod::Jobcities($jobDetailId) : '';	
      $locations  = explode('`,`',$Jobcities);
      $salaryModeSelect = GeneralMethod::salaryMode();
      $currencySymbole = GeneralMethod::currencySymbole();
      $min_sal_mode = (!empty($jobDetail->min_sal_mode) && isset($salaryModeSelect[$jobDetail->min_sal_mode])) ? strtoupper(substr($salaryModeSelect[$jobDetail->min_sal_mode], 0, 1)) : '';
      $max_sal_mode = (!empty($jobDetail->max_sal_mode) && isset($salaryModeSelect[$jobDetail->max_sal_mode])) ? strtoupper(substr($salaryModeSelect[$jobDetail->max_sal_mode], 0, 1)) : '';
      $min_salary_currency = $currencySymbole[@$jobDetail->min_salary_currency] ?? '';
      $max_salary_currency = $currencySymbole[@$jobDetail->max_salary_currency] ?? '';
      $max_salary = !empty($jobDetail->max_salary) ? GeneralMethod::thousandsCurrencyFormat($jobDetail->max_salary) : '0';
      $salary_currency = @$jobDetail->max_salary_currency ?? 0;
@endphp 

<!--begin::Toolbar-->
<div class="toolbar candidate-top-toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack candidate-toolbar-container">
    @include('sections.service_company.toolbar',['id' => @$jobDetail->id ?? 0])
    <div class="d-flex align-items-center py-1 candidate-header-actions">
      @php
          if($page_event == 'EDIT'){
            echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span></button>';
          }else{
            echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span></button>';
          }
      @endphp
    </div>
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 job-candidate-create" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="app">
       
        <!-- MAIN -->
        <main class="mc">
          <!-- Form -->
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="{{ @$jobId }}" id="job_id" />
            <input type="hidden" name="appId" value="{{ @$appId }}" id="appId" />
            <input type="hidden" name="currentPage" value="{{ @$currentPage }}" id="currentPage" />
            <input type="hidden" name="currentSlug" value="{{ @$currentSlug }}" id="currentSlug" />
            <input type="hidden" name="resumeCheck" value="{{ $resumeCheck }}" id="resumeCheck" />
            <input type="hidden" name="AddJobWiseCondidateRankRow" id="AddJobWiseCondidateRankRow" value="{{ (@$AddJobWiseCondidateRankRow) ? $AddJobWiseCondidateRankRow : '' }}">
            {{-- v2 upload session UUID — populated by resume-presign.js when browser_upload flag is ON --}}
            <input type="hidden" name="resume_session_uuid" id="resume_session_uuid" value="">
            {{-- V4 Remediation hidden fields --}}
            <input type="hidden" name="client_submission_uuid" id="client_submission_uuid" value="">
            <input type="hidden" name="client_update_uuid" id="client_update_uuid" value="">
            <input type="hidden" name="resume_version" id="resume_version" value="{{ (int)(@$application->resume_version ?? 1) }}">
            <input type="hidden" name="resume_intent" id="resume_intent" value="{{ (!empty($application->resume) || !empty($application->current_resume_id)) ? 'KEEP' : 'NONE' }}">
          <div class="fc">
            <!-- Upload + Career Summary -->
            <div class="g2 candidate-intro-grid" style="margin-bottom:16px">
                <!-- Resume Upload -->
                <div class="fs fv-row resume-card candidate-card" style="margin-bottom:0">
                    <div class="fsh resume-card-header candidate-card-header">
                        <div class="candidate-card-title">
                          <span class="resume-card-icon candidate-card-icon"><i class="fa-solid fa-file-arrow-up" aria-hidden="true"></i></span>
                          <span><strong>Upload Resume <span class="rq">*</span></strong><small>Upload a PDF or Word resume to parse candidate details.</small></span>
                        </div>
                    </div>

                    <div class="fsc resume-card-body candidate-card-body">
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
                                Supports PDF, DOC, DOCX (Max 6 MB)
                            </div>

                            <!-- Progress bar / pill / success banner -->
                            <div class="up-wrap" id="upWrap" style="display:none">
                                <div class="up-row">
                                    <div class="up-track">
                                        <div class="up-bar" id="upBar" style="width:0%"></div>
                                    </div>
                                    <span class="up-pill" id="upPill" style="display:none">
                                        <svg class="up-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                        Uploaded
                                    </span>
                                </div>
                                <div class="up-stats" id="upStats">0% &middot; 0 KB / 0 KB</div>
                                <div class="up-banner" id="upBanner" style="display:none">
                                    <svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>
                                    <span id="upBannerText">Upload complete! Your resume has been uploaded successfully.</span>
                                </div>
                            </div>

                            <!-- Selected File -->
                            @php
                                // basename() can return just a number when the path uses date-based
                                // directories (e.g. resumes/2024/07/candidatename.pdf → "07" is wrong).
                                // Walk the path segments and pick the first one that has a valid extension.
                                $displayResumeName = '';
                                if (!empty($application->resume)) {
                                    preg_match('/([^\/\\\\]+\.(pdf|doc|docx|htm|html))/i', $application->resume, $_rm);
                                    $displayResumeName = $_rm[1] ?? basename($application->resume);
                                    if (!$displayResumeName || !pathinfo($displayResumeName, PATHINFO_EXTENSION)) {
                                        $displayResumeName = 'Resume (uploaded)';
                                    }
                                }
                            @endphp
                            <div class="file-info" id="fileInfo" style="{{ !empty($resume_url) ? 'display:flex' : 'display:none' }}">
                                <span class="file-name" id="fileName">{{ $displayResumeName }}</span>
                                @if(!empty($resume_url))
                                <a href="{{ $resume_url }}"
                                   target="_blank"
                                   class="download-file"
                                   style="color:#fff;margin-left:250px;display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-download" aria-hidden="true" style="color:#fff"></i>
                                </a>
                                @endif

                                <button type="button"
                                        class="remove-file"
                                        id="removeFile">
                                    Remove
                                </button>
                            </div>

                        </div>

                            <!-- ==========================
                                 After-upload Resume Information card
                                 Outside #uploadBox so hiding the dropzone
                                 does not hide this card.
                                 ========================== -->
                            <div class="up-info" id="upInfo">
                                <div class="up-info-banner">
                                    <div class="up-info-banner-left">
                                        <span class="up-info-banner-check">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        </span>
                                        <div class="up-info-banner-text">
                                            <strong>Resume uploaded successfully!</strong>
                                            <small>Your resume has been uploaded and parsed.</small>
                                        </div>
                                    </div>
                                    <button type="button" class="up-info-banner-close" aria-label="Dismiss banner">
                                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="up-file-card">
                                    <div class="up-file-card-left">
                                        <div class="up-file-badge">
                                            <i class="fa-regular fa-file-pdf" aria-hidden="true"></i>
                                            <span class="up-file-badge-ext" id="upFileBadgeExt">PDF</span>
                                        </div>
                                        <div class="up-file-meta">
                                            <div class="up-file-name" id="upFileName">&nbsp;</div>
                                            <div class="up-file-sub" id="upFileSub">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="up-file-actions">
                                        <button type="button" class="up-btn up-btn-remove" id="upRemoveBtn">
                                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                            <span>Remove</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="up-details-card">
                                    <div class="up-details-grid">
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">Uploaded On</div>
                                                <div class="up-detail-val" id="upDetailUploadedOn">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-square-check" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">Parser Status</div>
                                                <div class="up-detail-val" id="upDetailParserStatus">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-file" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">File Type</div>
                                                <div class="up-detail-val" id="upDetailFileType">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-solid fa-database" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">File Size</div>
                                                <div class="up-detail-val" id="upDetailFileSize">&mdash;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
                <div class="fs fv-row summary-card candidate-card" style="margin-bottom:0">
                  <div class="fsh resume-card-header summary-card-header candidate-card-header">
                    <div class="candidate-card-title summary-title-wrap">
                      <span class="summary-card-icon candidate-card-icon"><i class="fa-solid fa-align-left" aria-hidden="true"></i></span>
                      <span><strong>Career Summary <span class="rq">*</span></strong><small>Highlight the candidate's experience, strengths, and suitability.</small></span>
                    </div>
                  </div>
                  <div class="fsc summary-card-body candidate-card-body">
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
                <input type="hidden" name="resume_original_name" id="resume_original_name" value="">
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.</span> </div>
                <!--end::Input group-->
              </div>
            </div>
            <!--end::Upload Photo & Resume/CV-->

            <!-- Candidate Profile -->
            <div class="fs bd-card candidate-card candidate-profile-card">
              <div class="fsh candidate-card-header bd-hero">
                <div class="candidate-card-title bd-hero-title">
                  <span class="candidate-card-icon bd-hero-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                  <span><strong>Candidate Profile</strong><small>Basic information to identify and contact this candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body bd-body">
                <div class="candidate-profile-grid">
                  <div class="fg fv-row">
                    <label>Full Name <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-user" aria-hidden="true"></i><input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}" placeholder="Enter full name" /></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Primary Email <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email address" /></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Primary Contact Number <span class="rq">*</span></label>
                    <div class="candidate-field-control candidate-phone-control"><i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i><div class="ig"><input type="number" name="phone" id="phone" value="{{ @$application->phone }}" placeholder="Enter primary number" style="padding-left: 76px !important; width: 307px;" /><input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code"></div></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Current Location <span class="rq">*</span></label>
                    <div class="candidate-field-control candidate-field-control--tagify candidate-field-control--location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><input type="text" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Search city, state or country" autocomplete="off" aria-label="Search current location" /><button type="button" class="candidate-location-clear" aria-label="Clear location" tabindex="-1"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button><i class="fa-solid fa-chevron-down candidate-location-chevron" aria-hidden="true"></i></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Highest Qualification <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i><select name="qualification" id="qualification">
                      <option value="">Select qualification</option>
                      @foreach($qualification as $data_value)
                      @php
                      $qualificationNameKey = strtolower(preg_replace('/\s+/', ' ', trim((string) @$data_value->name)));
                      if (in_array($qualificationNameKey, ['any degree', 'any post graduation', 'any graduation'], true)) {
                        continue;
                      }
                      $sel = '';
                      if(@$application->qualification == $data_value->id ){
                      $sel = 'selected="selected"';
                      }
                      @endphp
                      <option value="{{ @$data_value->id }}" {{ @$sel }}>{{ @$data_value->name }}</option>
                      @endforeach
                    </select></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Nationality</label>
                    <div class="candidate-field-control"><i class="fa-solid fa-flag" aria-hidden="true"></i><select name="nationality" id="nationality">
                      <option value="">Select nationality</option>
                      <option {{ (@$application->nationality==='Indian') ? 'selected' : '' }}>Indian</option>
                      <option {{ (@$application->nationality==='American') ? 'selected' : '' }}>American</option>
                      <option {{ (@$application->nationality==='British') ? 'selected' : '' }}>British</option>
                      <option {{ (@$application->nationality==='Other') ? 'selected' : '' }}>Other</option>
                    </select></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Gender</label>
                    <div class="candidate-field-control"><i class="fa-regular fa-user" aria-hidden="true"></i><select name="gender" id="gender">
                      <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option>
                      <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                    </select></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Date of Birth</label>
                    <div class="candidate-field-control"><i class="fa-regular fa-calendar" aria-hidden="true"></i><input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()" placeholder="Select date" /></div>
                  </div>
                </div>

                <details class="candidate-secondary-details" {{ (!empty(@$application->secondrary_email) || !empty(@$application->secondary_mobile)) ? 'open' : '' }}>
                  <summary>
                    <span class="candidate-secondary-summary"><span class="candidate-secondary-icon"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></span><span><strong>Additional Secondary Contact</strong><small>Optional secondary email and contact number.</small></span></span>
                    <span class="candidate-secondary-toggle"><span class="candidate-secondary-show">Show Details</span><span class="candidate-secondary-hide">Hide Details</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
                  </summary>
                  <div class="candidate-secondary-grid">
                    <div class="fg fv-row">
                      <label>Secondary Email</label>
                      <div class="candidate-field-control"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" name="secondrary_email" id="secondrary_email" value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" /></div>
                    </div>
                    <div class="fg fv-row">
                      <label>Secondary Contact Number</label>
                      <div class="candidate-field-control candidate-phone-control"><i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i><div class="ig"><input type="number" name="secondary_mobile" id="secondary_mobile" value="{{ @$application->secondary_mobile }}" placeholder="Enter secondary number" style="padding-left: 76px !important; width: 307px;" /><input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code"></div></div>
                    </div>
                  </div>
                </details>
              </div>
            </div>

            <!-- Primary Skills -->
            @php
              $candidateRequiredSkills = collect($jobSkill ?? [])->filter(function ($skill) {
                  return is_object($skill) && trim((string) ($skill->skill_name ?? '')) !== '';
              })->map(function ($skill) {
                  return [
                      'id' => (string) ($skill->id ?? ''),
                      'name' => trim((string) $skill->skill_name),
                  ];
              })->unique(function ($skill) {
                  return mb_strtolower(preg_replace('/\s+/', ' ', $skill['name']));
              })->values();
            @endphp
            <div class="fs fv-row se-card primary-skills-card candidate-card">
              <div class="fsh candidate-card-header se-hero">
                <div class="candidate-card-title se-title">
                  <span class="candidate-card-icon se-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                  <span><strong>Primary Skills <span class="rq">*</span></strong><small>Add the candidate's strongest skills and specializations.</small></span>
                </div>
                <div class="candidate-skill-match is-empty" id="candidateSkillMatch" aria-live="polite">
                  <span class="candidate-skill-match-ring" id="candidateSkillMatchRing" style="--candidate-skill-match: 0%">
                    <strong id="candidateSkillMatchPercentage">--</strong>
                  </span>
                  <span class="candidate-skill-match-copy">
                    <strong>Job Skills Match</strong>
                    <small id="candidateSkillMatchDetail">No job skills defined</small>
                  </span>
                </div>
              </div>
              <div class="fsc candidate-card-body candidate-primary-skills-body">
                <div class="candidate-primary-skill-combobox">
                  <div class="candidate-primary-skill-entry">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="text" id="candidatePrimarySkillInput" autocomplete="off" placeholder="Type a skill and press Enter..." aria-label="Add a primary skill" role="combobox" aria-autocomplete="list" aria-controls="candidatePrimarySkillSuggestions" aria-expanded="false" />
                    <span class="candidate-primary-skill-divider" aria-hidden="true"></span>
                    <button type="button" id="candidatePrimarySkillAdd"><i class="fa-solid fa-plus" aria-hidden="true"></i><span>Add</span></button>
                  </div>
                  <div id="candidatePrimarySkillSuggestions" class="candidate-primary-skill-suggestions" role="listbox" aria-label="Skill suggestions" hidden></div>
                </div>
                <div id="candidatePrimarySkillChips" class="candidate-primary-skill-chips" aria-live="polite"></div>
                <div id="candidatePrimarySkillEmpty" class="candidate-primary-skill-empty">No primary skills added yet.</div>
                <div class="candidate-suggested-skills" id="candidateSuggestedSkills" hidden>
                  <div class="candidate-suggested-skills-head">
                    <span class="candidate-suggested-skills-title"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i><span><strong>Suggested Skills</strong><small>Based on skills from the job requirement. Click to add the missing skills.</small></span></span>
                    <span class="candidate-suggested-skills-count" id="candidateRemainingSkills"></span>
                  </div>
                  <div class="candidate-suggested-skills-list" id="candidateSuggestedSkillsList"></div>
                </div>
                <div id="skillTags" class="skill-tags" hidden></div>
                <input type="text" class="candidate-primary-skills-source" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
              </div>
            </div>

            <!-- Experience Details -->
            <div class="fs pd-experience-section-card candidate-card experience-details-card">
              <div class="fsh candidate-card-header pd-experience-hero">
                <div class="candidate-card-title pd-experience-hero-title">
                  <span class="candidate-card-icon pd-experience-hero-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                  <span><strong>Experience Details</strong><small>Capture total and relevant experience for this job application.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body pd-experience-body">
                @php
                 $value = @$application->total_exp; // Convert to string to preserve decimal
                 if(!is_null($value)){
                  $total_exp = GeneralMethod::separateNumberParts($value);
                  $exp_years = ($total_exp[0]);
                  $exp_months = ($total_exp[1]);
                  $selected_exp_year = ($exp_years);
                  $selected_exp_month = ($exp_months);
                 }else{
                  $selected_exp_year = null;
                  $selected_exp_month = null;
                 }

                 $valuew = @$application->rel_exp;
                 if(!is_null($value)){
                  $total_rel_exp = GeneralMethod::separateNumberParts($valuew);
                  $rel_exp_years = ($total_rel_exp[0]);
                  $rel_exp_months = ($total_rel_exp[1]);
                  $selected_rel_exp_year = ($rel_exp_years);
                  $selected_rel_exp_month = ($rel_exp_months);
                 }else{
                  $selected_rel_exp_year = null;
                  $selected_rel_exp_month = null;
                 }
                @endphp
                <div class="candidate-experience-grid">
                  <div class="candidate-experience-panel">
                    <div class="candidate-panel-title"><span><i class="fa-regular fa-clock" aria-hidden="true"></i></span><span><strong>Total Experience <em>*</em></strong><small>Your overall professional experience.</small></span></div>
                    <div class="ei">
                      <label class="candidate-experience-field"><span>Years</span><select id="total_exp_year" name="total_exp_year">
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
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                      <label class="candidate-experience-field"><span>Months</span><select class="" aria-label="Select total experience month" id="total_exp_month" name="total_exp_month">
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
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                    </div>
                  </div>
                  <div class="candidate-experience-panel candidate-experience-panel--relevant">
                    <div class="candidate-panel-title"><span><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span><span><strong>Relevant Experience <em>*</em></strong><small>Experience relevant to this job or required skills.</small></span></div>
                    <div class="ei">
                      <label class="candidate-experience-field"><span>Years</span><select id="rel_exp_year" name="rel_exp_year">
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
                                        $selected = 'selected';
                                      }
                                    }
                                  echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                              }
                        @endphp
                      </select></label>
                      <label class="candidate-experience-field"><span>Months</span><select class="" aria-label="Select relevant experience month" id="rel_exp_month" name="rel_exp_month">
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
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                    </div>
                  </div>
                  <div class="candidate-experience-tip">
                    <span><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span>
                    <div class="candidate-experience-tip-copy"><strong>Tip</strong><p>Relevant experience should match the skills required for this job.</p>
                      <div class="candidate-experience-examples"><strong>Examples:</strong><ul><li>If the job requires Laravel, mention your Laravel experience here.</li><li>Exclude unrelated experience.</li><li>Freshers can select 0 years.</li></ul></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Profile -->
            <div class="fs pd-card candidate-card professional-profile-card" id="candidateProfessionalProfile">
              <div class="fsh candidate-card-header pd-hero">
                <div class="candidate-card-title pd-hero-title">
                  <span class="candidate-card-icon pd-hero-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span><strong>Professional Profile</strong><small>Define the candidate's current professional role and employment type.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body pd-body">
                <div class="professional-profile-grid">
                  <div class="fg fv-row">
                    <label>Current Company <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-building" aria-hidden="true"></i><input type="text" name="current_company" id="current_company" value="{{ @$application->current_company }}" placeholder="Enter current company" /></div>
                    <small class="candidate-professional-help">e.g. TCS, Infosys, Amazon</small>
                  </div>
                  <div class="fg fv-row">
                    <label>Current Designation <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><input type="text" name="current_designation" id="current_designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}" placeholder="Enter current designation" /></div>
                    <small class="candidate-professional-help">e.g. Software Engineer, Product Manager</small>
                  </div>
                  <div class="fg fv-row">
                    <label>Employment Type <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-solid fa-rectangle-list" aria-hidden="true"></i><select name="employmentType" id="EmploymentType">
                      <option value="">Select employment type</option>
                      <option {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Full Time</option>
                      <option {{ (@$application->employmentType==='Contract') ? 'selected' : '' }}>Contract</option>
                      <option {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part Time</option>
                      <option {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance</option>
                    </select></div>
                  </div>
                </div>
              </div>
            </div>

            @php
              $candidateSalaryMode = (int) (@$application->current_ctc_mode ?? 0);
              $candidateSalaryMode = in_array($candidateSalaryMode, [0, 1, 2], true) ? $candidateSalaryMode : 0;
              $candidateSalaryPeriod = [0 => 'annual', 1 => 'monthly', 2 => 'hourly'][$candidateSalaryMode];
            @endphp
            <!-- Salary Details -->
            <div class="fs br-card salary-card candidate-card">
              <div class="fsh candidate-card-header br-hero">
                <div class="candidate-card-title br-hero-title">
                  <span class="candidate-card-icon br-hero-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                  <span><strong>Salary Details</strong><small>Capture current and expected salary. Related values continue to calculate automatically.</small></span>
                </div>
                <div class="candidate-salary-header-currency">
                  <label for="current_ctc_cur">Currency</label>
                  <select id="current_ctc_cur" name="current_ctc_cur">
                    @php
                        $current_ctc_cur = "";
                        $current_ctc_cur = @$application->current_ctc_cur ?? $salary_currency;
                        foreach($currency as $key=>$val){
                            $sel = '';
                            if($current_ctc_cur===null){
                              if($currency_code==$val){
                                $sel = 'selected="selected"';
                              }
                            }else{
                              if($current_ctc_cur == $key){
                                $sel = 'selected="selected"';
                              }
                            }
                            echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                        }
                    @endphp
                  </select>
                  <div class="d-none">
                    <select name="current_ectc_cur" id="current_ectc_cur">
                      @php
                          foreach($currency as $key=>$val){
                              $sel = ($current_ctc_cur == $key) ? 'selected="selected"' : '';
                              echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                          }
                      @endphp
                    </select>
                    <select name="current_th_cur" id="current_th_cur">
                      @php
                          foreach($currency as $key=>$val){
                              $sel = ($current_ctc_cur == $key) ? 'selected="selected"' : '';
                              echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                          }
                      @endphp
                    </select>
                  </div>
                </div>
              </div>
              <div class="fsc candidate-card-body br-body">
                <div class="candidate-salary-grid">
                  <section class="candidate-salary-panel candidate-salary-panel--current">
                    <div class="candidate-salary-panel-head">
                      <span><i class="fa-solid fa-coins" aria-hidden="true"></i></span>
                      <div><strong>Current Salary</strong><small>What the candidate currently earns</small></div>

                    </div>
                    <div class="candidate-salary-tabs" role="tablist" aria-label="Salary frequency">
                      <button type="button" class="{{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="annual" data-salary-mode="0">Annual</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="monthly" data-salary-mode="1">Monthly</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="hourly" data-salary-mode="2">Hourly</button>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="annual">
                      <div class="d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
                          @php
                              $current_ectc_mode = "";
                              $current_ectc_mode = @$application->current_ectc_mode;
                              foreach($salaryMode as $key=>$val){
                                $sel = '';
                                if($candidateSalaryMode==$key){
                                  $sel = 'selected="selected"';
                                }
                                echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                              }
                          @endphp
                        </select>
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                          @php
                              $current_ctc_mode = "";
                              $current_ctc_mode = @$application->current_ctc_mode;
                              foreach($salaryMode as $key=>$val){
                                $sel = '';
                                if($candidateSalaryMode==$key){
                                  $sel = 'selected="selected"';
                                }
                                echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                              }
                          @endphp
                        </select>
                      </div>
                      <div class="fg fv-row"><label>Current Salary (Annual) <span id="pay_rate_annual_usd"></span></label><div class="ig"><input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted payrateannual"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="monthly">
                      <div class="fg fv-row"><label>Current Salary (Monthly) <span id="pay_rate_monthly_usd"></span></label><div class="ig"><input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted payratemonthly"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="hourly">
                      <div class="fg fv-row"><label>Current Salary (Hourly) <span id="pay_rate_hourly_usd"></span></label><div class="ig"><input type="number" name="ctc" id="ctc" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted CurrentCTC"></span></div>
                    </div>
                    <div class="candidate-salary-hint"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i><span id="candidateCurrentSalaryHint">Enter your current {{ $candidateSalaryPeriod }} salary.</span></div>
                    <div class="candidate-salary-auto candidate-salary-auto--dynamic">
                      <span>Auto Calculated</span>
                      <div><strong id="candidate-current-auto-label-one">Monthly Salary</strong><b id="candidate-current-monthly-auto">{{ @$application->ctc_monthly ?: 0 }}</b></div>
                      <div><strong id="candidate-current-auto-label-two">Hourly Salary</strong><b id="candidate-current-hourly-auto">{{ @$application->ctc_hourly ?: 0 }}</b></div>
                    </div>
                  </section>

                  <section class="candidate-salary-panel candidate-salary-panel--expected">
                    <div class="candidate-salary-panel-head"><span><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></span><div><strong>Expected Salary</strong><small>What the candidate expects to earn</small></div></div>
                    <div class="candidate-salary-tabs candidate-salary-tabs--synced" role="group" aria-label="Expected salary frequency, synchronized with Current Salary">
                      <button type="button" class="{{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="annual" data-salary-mode="0">Annual</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="monthly" data-salary-mode="1">Monthly</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="hourly" data-salary-mode="2">Hourly</button>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="annual">
                      <div class="fg fv-row"><label>Expected Salary (Annual) <span id="bill_rate_annual_usd"></span></label><div class="ig"><input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted billrateannual"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="monthly">
                      <div class="fg fv-row"><label>Expected Salary (Monthly) <span id="bill_rate_monthly_usd"></span></label><div class="ig"><input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted billratemonthly"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="hourly">
                      <div class="fg fv-row"><label>Expected Salary (Hourly) <span id="bill_rate_hourly_usd"></span></label><div class="ig"><input type="number" name="ectc" id="ectc" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted expectedctc"></span></div>
                    </div>
                    <div class="candidate-salary-hint"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i><span id="candidateExpectedSalaryHint">Enter your expected {{ $candidateSalaryPeriod }} salary.</span></div>
                    <div class="candidate-salary-auto candidate-salary-auto--dynamic">
                      <span>Auto Calculated</span>
                      <div><strong id="candidate-expected-auto-label-one">Monthly Salary</strong><b id="candidate-expected-monthly-auto">{{ @$application->ectc_monthly ?: 0 }}</b></div>
                      <div><strong id="candidate-expected-auto-label-two">Hourly Salary</strong><b id="candidate-expected-hourly-auto">{{ @$application->ectc_hourly ?: 0 }}</b></div>
                    </div>
                  </section>

                  <aside class="candidate-salary-panel candidate-salary-summary">
                    <div class="candidate-salary-panel-head"><span><i class="fa-solid fa-calculator" aria-hidden="true"></i></span><div><strong>Salary Summary</strong><small>Calculated from the entered values</small></div></div>
                    <div class="candidate-salary-summary-list">
                      <div><span id="candidateSalaryCurrentLabel">Current Salary (Annual)</span><strong id="candidateSalaryCurrentSummary">0</strong></div>
                      <div><span id="candidateSalaryExpectedLabel">Expected Salary (Annual)</span><strong id="candidateSalaryExpectedSummary">0</strong></div>
                      <div><span>Difference</span><strong id="candidateSalaryDifferenceSummary">0</strong></div>
                      <div><span>Expected Increase</span><strong id="candidateSalaryHikeSummary">0%</strong></div>
                    </div>
                    <input type="hidden" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" />
                    <div class="candidate-offer-field">
                      <strong>Offer in Hand</strong>
                      <div class="rg">
                        <div class="ri"><input type="radio" name="oh" id="ohy" value="yes" {{ @$application->oh == 'yes' ? 'checked' : '' }} /><label for="ohy">Yes</label></div>
                        <div class="ri"><input type="radio" name="oh" id="ohn" value="no" {{ (@$application->wm == 'no' || empty(@$application->oh)) ? 'checked' : '' }} /><label for="ohn">No</label></div>
                      </div>
                    </div>
                  </aside>
                </div>

                <div class="candidate-salary-comments">
                  <div class="candidate-salary-comments-head"><span><i class="fa-regular fa-comment-dots" aria-hidden="true"></i></span><div><strong>Comments <small>(Optional)</small></strong><small>Add salary expectations or internal compensation notes.</small></div></div>
                  <textarea name="comment" id="comment" placeholder="Enter comments about salary expectations or any other details..." rows="4">{{ @$application->comment }}</textarea>
                  <div class="cc">0 / 500</div>
                </div>
              </div>
            </div>

            <!-- Notice Period and Joining Details -->
            <div class="fs aw-card candidate-card candidate-availability-card notice-joining-card">
              <div class="fsh candidate-card-header aw-hero">
                <div class="candidate-card-title aw-hero-title">
                  <span class="candidate-card-icon aw-hero-icon"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span>
                  <span><strong>Notice Period &amp; Joining Details</strong><small>Capture notice period, joining availability, and reason for job change.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body aw-body">
                <div class="candidate-notice-status-block fv-row">
                  <label>Notice Period <span class="candidate-optional-label">(Optional)</span></label>
                  <div class="candidate-notice-options" role="radiogroup" aria-label="Notice Period Selection">
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 2 ? 'is-selected' : '' }}" data-notice-period-value="2" role="radio" aria-checked="{{ $snp_mode === 2 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--immediate"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Immediate Joiner</strong><small>Can join within 0-15 days</small></span>
                    </button>
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 1 ? 'is-selected' : '' }}" data-notice-period-value="1" role="radio" aria-checked="{{ $snp_mode === 1 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--serving"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Serving Notice Period</strong><small>Currently working, serving notice</small></span>
                    </button>
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 0 ? 'is-selected' : '' }}" data-notice-period-value="0" role="radio" aria-checked="{{ $snp_mode === 0 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--select"><i class="fa-regular fa-clock" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Select Notice Period</strong><small>Choose a specific notice period</small></span>
                    </button>
                  </div>
                  <select class="candidate-notice-native-select" name="serving_notice_period" id="serving_notice_period" aria-hidden="true" tabindex="-1">
                    <option value="" {{ $snp_mode === null ? 'selected' : '' }}>Choose Notice Period</option>
                    <option value="0" {{ $snp_mode === 0 ? 'selected' : '' }}>Select Notice Period</option>
                    <option value="1" {{ $snp_mode === 1 ? 'selected' : '' }}>Serving Notice Period</option>
                    <option value="2" {{ $snp_mode === 2 ? 'selected' : '' }}>Immediate Joiner</option>
                  </select>
                </div>

                <div class="candidate-notice-details-grid">
                  <div class="fg fv-row candidate-notice-detail" id="candidateNoticeDateField" {{ !in_array($snp_mode, [1, 2], true) ? 'hidden' : '' }}>
                    <label id="candidateNoticeDateLabel">{{ $snp_mode === 1 ? 'Expected Last Working Date' : 'Last Working Date' }} <span class="rq">*</span></label>
                    <div class="candidate-notice-input-shell">
                      <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                      <div class="candidate-date-picker">
                        <input type="text" id="np_id_display" class="form-control form-control-solid" name="np_id_display" value="{{ $snp_last_working ? date('d/M/Y', strtotime($snp_last_working)) : '' }}" placeholder="{{ $snp_mode === 1 ? 'Select expected last working date' : 'Select last working date' }}" readonly {{ !in_array($snp_mode, [1, 2], true) ? 'disabled' : '' }}>
                        <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" value="{{ $snp_last_working ? date('Y-m-d', strtotime($snp_last_working)) : '' }}" style="opacity:0;position:absolute;z-index:-1;top:0;" {{ !in_array($snp_mode, [1, 2], true) ? 'disabled' : '' }}>
                      </div>
                    </div>
                  </div>
                  <div class="fg fv-row candidate-notice-detail" id="candidateNoticeDaysField" {{ $snp_mode !== 0 ? 'hidden' : '' }}>
                    <label>Notice Period <span class="rq">*</span></label>
                    <div class="candidate-notice-input-shell">
                      <i class="fa-regular fa-clock" aria-hidden="true"></i>
                      <select class="form-select form-select-solid" aria-label="Select Notice Period" id="ddl_np_id" name="ddl_np_id" {{ $snp_mode !== 0 ? 'disabled' : '' }}>
                        <option value="">Select Notice Period</option>
                        @php
                            for ($i = 1; $i <= 200; $i++) {
                                $suffix = $i === 1 ? 'Day' : 'Days';
                                $selected = ($snp_mode === 0 && $i == @$application->np_id) ? 'selected' : '';
                                echo '<option value="' . $i . '" ' . $selected . '>' . $i . ' ' . $suffix . '</option>';
                            }
                        @endphp
                      </select>
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Reason for Job Change</label>
                    <div class="candidate-field-control candidate-reason-control"><i class="fa-regular fa-comment-dots" aria-hidden="true"></i><input type="text" name="reason_for_job_change" maxlength="55" id="reason_for_job_change" value="{{ @$application->reason_for_job_change }}" placeholder="Enter reason for job change" /></div>
                  </div>
                </div>
                <input type="text" id="calender" name="calender" hidden>              </div>
            </div>

            <!-- Preferred Work Mode -->
            <div class="fs candidate-card preferred-work-mode-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--work-mode"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span><strong>Preferred Work Mode</strong><small>Select the candidate's preferred way of working.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="candidate-work-mode-grid">
                  <label class="candidate-work-mode-option" for="wr"><input type="radio" name="wm" id="wr" {{ @$application->wm == 'remote' ? 'checked' : '' }} value="remote" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--remote"><i class="fa-solid fa-laptop" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Remote</strong><small>Can work from any location.</small></span></label>
                  <label class="candidate-work-mode-option" for="wh"><input type="radio" name="wm" id="wh" {{ @$application->wm == 'hybrid' ? 'checked' : '' }} value="hybrid" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--hybrid"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Hybrid</strong><small>Combination of office and remote.</small></span></label>
                  <label class="candidate-work-mode-option" for="wo"><input type="radio" name="wm" id="wo" {{ (@$application->wm == 'office' || empty(@$application->wm)) ? 'checked' : '' }} value="office" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--office"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Work From Office</strong><small>Office location based work.</small></span></label>
                </div>
              </div>
            </div>

            <!-- Skills Details -->
            <div class="fs se-card job-skills-card candidate-card">
              <div class="fsh candidate-card-header se-hero">
                <div class="candidate-card-title se-title">
                  <span class="candidate-card-icon se-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                  <span><strong>Job Skills Details</strong><small>Record experience and proficiency against the job's required skills.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body candidate-table-wrap">
                <div class="candidate-job-skills-scroll">
                <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Experience (Years) <span class="rq">*</span></th>
                      <th>Experience (Months)</th>
                      <th>Proficiency Rating <span class="rq">*</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($jobSkill as $skill_val)
                        @if(!empty($skill_val->skill_name))

                            @php
                                  $selected_skill_exp_year = null;
                                  $selected_skill_exp_month = null;
                                  $selected_skill_rating = null;

                                  if(!empty($skill_val->skill_value)){
                                    $skillValue = str_replace(['Years','Year',' '],'', (string)$skill_val->skill_value);
                                    if($skillValue !== ''){
                                      $total_skill_exp = GeneralMethod::separateNumberParts($skillValue);
                                      $skill_exp_years = $total_skill_exp[0] ?? null;
                                      $skill_exp_months = $total_skill_exp[1] ?? null; 

                                      $selected_skill_exp_year = $skill_exp_years;
                                      $selected_skill_exp_month = $skill_exp_months;
                                      $selected_skill_rating = $skill_val->rating ?? null;
                                     }
                                  }
                            @endphp

                            <tr>
                              <td><label class="candidate-job-skill-name"><input type="text" value="{{ strtoupper($skill_val->skill_name) }}" placeholder="Enter skill" readonly /></label></td>
                              <td>
                                <select class="form-select form-select-solid candidate-job-skill-select" aria-label="Select skill experience year" id="job_skill_year_{{ $skill_val->id }}" name="YearskillId[{{ $skill_val->id }}]">
                                      <option value="0">Select Year</option>
                                      @php 
                                          for ($i = 1; $i <= 60; $i++) {
                                                $selected = '';
                                                $s = 'Year';
                                                if($i > 1){
                                                    $s = 'Years';
                                                }
                                                if(!is_null($selected_skill_exp_year) && (string)$i === (string)$selected_skill_exp_year){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                              <td>
                                <select class="form-select form-select-solid candidate-job-skill-select" aria-label="Select skill experience month" id="job_skill_month_{{ $skill_val->id }}" name="MonthskillId[{{ $skill_val->id }}]">
                                      <option value="">Select Month</option>
                                      @php 
                                          for ($i = 0; $i <= 11; $i++) {
                                                $selected = '';
                                                $s = 'Month';
                                                if($i > 1){
                                                    $s = 'Months';
                                                }
                                                if(!is_null($selected_skill_exp_month) && (string)$i === (string)$selected_skill_exp_month){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                </select>
                              </td>
                              <td>
                                 <select class="form-select form-select-solid candidate-rating-select" aria-label="Select proficiency rating" id="job_skill_rating_{{ $skill_val->id }}" name="rating[{{ $skill_val->id }}]">
                                      <option value="0">Select rating</option>
                                      @php 
                                          for ($i = 1; $i <= 5; $i++) {
                                                $selected = '';
                                                $s = 'Star';
                                                if($i > 1){
                                                    $s = 'Stars';
                                                }
                                                if(!is_null($selected_skill_rating) && (string)$i === (string)$selected_skill_rating){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                            </tr>
                        @endif
                    @endforeach
                  </tbody>
                </table>
                </div>
              </div>
            </div>

            <!-- Screening Questions -->
            @php
                $screeningQuestions = collect($job_additional_detail ?? [])->filter(function ($question) {
                    return is_object($question) && trim((string) ($question->detail_value ?? '')) !== '';
                });
            @endphp
          @if($screeningQuestions->isNotEmpty())
            <div class="fs candidate-card questions-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--questions"><i class="fa-regular fa-circle-question" aria-hidden="true"></i></span>
                  <span><strong>Screening Questions</strong><small>Complete the job-specific information required for this submission.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">

                <!--begin::Custom Details-->
                <div class="" id="job_additional_detail">
                  @php 
                    if(!empty($job_additional_detail)){
                    
                    $customTitleId	=	1;
                    $html			=	'';
                   
                    $inc_first		=	0;
                    $inc_last		=	1;
                     $html	.=	'<div class="form-group row mb-10">';	
                    foreach($screeningQuestions as $aDetailArr){
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
              </div>
            </div>
            @endif


            <!-- Availability -->
            <div class="fs candidate-card interview-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--interview"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
                  <span><strong>Availability for Next 3 Days <span class="rq">*</span></strong><small>Provide three interview windows and the preferred interview mode.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="ag">
                  <div class="ai candidate-slot candidate-slot--one">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">1</span>
                      <strong class="candidate-slot-title">Slot 1</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateOne" id="interviewDateOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_one" id="interview_mode_one" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_one == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>

                  <div class="ai candidate-slot candidate-slot--two">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">2</span>
                      <strong class="candidate-slot-title">Slot 2</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateTwo" id="interviewDateTwo" value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_two" id="interview_mode_two" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_two == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>

                  <div class="ai candidate-slot candidate-slot--three">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">3</span>
                      <strong class="candidate-slot-title">Slot 3</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateThree" id="interviewDateThree" value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_three" id="interview_mode_three" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_three == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>
                </div>
                </div>
            </div>

            <!-- Professional Links -->
            <div class="fs addl-card candidate-card professional-links-card">
              <div class="fsh candidate-card-header addl-hero">
                <div class="candidate-card-title addl-hero-title">
                  <span class="candidate-card-icon addl-hero-icon"><i class="fa-solid fa-link" aria-hidden="true"></i></span>
                  <span><strong>Professional Links</strong><small>Add online profiles that help clients evaluate this candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body addl-body">
                <div class="candidate-professional-links">
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--linkedin" aria-hidden="true">in</span>
                    <span class="candidate-link-copy"><label for="linkedin">LinkedIn <span>(Optional)</span></label><small>Your professional profile</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" id="linkedin" name="linkedin" value="{{ @$application->linkedin }}" placeholder="Enter LinkedIn profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://www.linkedin.com/in/yourname</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--github" aria-hidden="true">GH</span>
                    <span class="candidate-link-copy"><label for="github">GitHub <span>(Optional)</span></label><small>Your code repositories</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" value="{{ @$application->github }}" id="github" name="github" placeholder="Enter GitHub profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://github.com/yourusername</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--behance" aria-hidden="true">Be</span>
                    <span class="candidate-link-copy"><label for="behance">Behance <span>(Optional)</span></label><small>Your design portfolio</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" id="behance" name="behance" value="{{ @$application->behance }}" placeholder="Enter Behance profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://www.behance.net/yourname</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--website" aria-hidden="true"><i class="fa-solid fa-globe"></i></span>
                    <span class="candidate-link-copy"><label for="otherLink">Portfolio / Website <span>(Optional)</span></label><small>Your personal website</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" value="{{ @$application->otherLink }}" id="otherLink" name="otherLink" placeholder="Enter website or portfolio URL" /></div>
                    <small class="candidate-link-example">e.g. https://yourportfolio.com</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Details -->
            <div class="fs candidate-card additional-details-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--additional"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                  <span><strong>Additional Details</strong><small>Provide any other relevant information about the candidate's professional background.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="candidate-additional-details-grid">
                  <div class="fg fv-row">
                    <label>Current Visa Status (For International)</label>
                    <div class="candidate-detail-control candidate-detail-control--select"><span><i class="fa-solid fa-globe" aria-hidden="true"></i></span><select name="visastatu" id="visastatu"><option value="">Select visa status</option><option {{ @$application->visastatu == 'H1B' ? 'selected' : '' }}>H1B</option><option {{ @$application->visastatu == 'L1' ? 'selected' : '' }}>L1</option><option {{ @$application->visastatu == 'F1' ? 'selected' : '' }}>F1</option><option {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Green Card</option><option {{ @$application->visastatu == 'Citizen' ? 'selected' : '' }}>Citizen</option></select></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Professional Reference <span class="candidate-optional-label">(Optional)</span></label>
                    <div class="candidate-detail-control"><span><i class="fa-solid fa-user" aria-hidden="true"></i></span><input type="text" name="referencename" value="{{ @$application->referencename}}" id="referencename" placeholder="Enter reference name" /></div>
                    <small class="candidate-field-help">Provide a reference name who can vouch for the candidate's work experience.</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recruiter Comments -->
            <div class="fs candidate-card recruiter-note-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--note"><i class="fa-regular fa-note-sticky" aria-hidden="true"></i></span>
                  <span><strong>Recruiter Comments</strong><small>Add internal context for the hiring team; this is not shared with the candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                @if(!empty($recruiterNoteTemplates) && $recruiterNoteTemplates->count())
                <div class="fg fv-row recruiter-template-field">
                  <label>Select Template <span>(Optional - loads content into the note below)</span></label>
                  <div class="recruiter-template-control"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><select id="recruiter_note_template_select"><option value="">Select Note Template...</option>@foreach($recruiterNoteTemplates as $tpl)<option value="{{ $tpl->id }}">{{ $tpl->title }}</option>@endforeach</select></div>
                </div>
                @endif
                <div class="fg fv-row recruiter-editor-field">
                  <label>Internal Note <span>(Optional)</span></label>
                  <textarea id="recruiter_note" name="recruiter_note" rows="6" placeholder="Add internal recruiter notes about this candidate...">{{ @$application->recruiter_note }}</textarea>
                  <div class="recruiter-private-note"><i class="fa-solid fa-lock" aria-hidden="true"></i><span>Internal only - Not visible to candidate</span></div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </main>

      </div>
     
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
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/resume-presign.js') }}?v=20260924-1"></script>
@include('service_company.job-applications.jero-client-parser')

<script>
$(document).ready(function () {

    // Authoritative Edit/Add state directly from Laravel route/view model (Phase 6)
    var IS_EDIT_MODE = {{ $page_event === 'EDIT' ? 'true' : 'false' }};
    console.log('[PARSER] IS_EDIT_MODE authoritatively set to:', IS_EDIT_MODE);

    // ── v2 upload: presign URL injected from PHP; flag computed at runtime ──────
    // resume-presign.js is loaded unconditionally; v2 activates only when the
    // module exists and the server confirms the complete session contract.
    @if(config('resumes.features.browser_upload')
        && config('resumes.features.presign_endpoint')
        && config('resumes.features.finalize_endpoint')
        && config('resumes.features.submit_with_session')
        && \Illuminate\Support\Facades\Schema::hasTable('resume_uploads'))
    window.RESUME_PRESIGN_URL = @json(route('service_company.resume.v2.sessions.store'));
    @endif
    var RESUMES_FEATURE_BROWSER_UPLOAD = !!(window.ResumePresign && window.RESUME_PRESIGN_URL);
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.RESUMES_FEATURE_BROWSER_UPLOAD = true;
        // Phase 6: server-controlled retry config passed to the JS module.
        window.RESUMES_CONFIG = {
            maxPutAttempts: @json(config('resumes.network_resilience.max_put_attempts', 3)),
            putBackoffMs:   @json(config('resumes.network_resilience.put_backoff_ms', 1000)),
        };
    }

    // ── v2 state → existing progress bar UI bridge ───────────────────────────
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.ResumePresign.onStateChange(function (data) {
            if (data.state === 'uploading') {
                $('#upWrap').show();
                $('#upBar').css('width', data.progress + '%');
                $('#upStats').html(data.progress + '% &middot; Uploading to cloud&hellip;');
            } else if (data.state === 'uploaded') {
                $('#upBar').css('width', '100%');
                $('#upStats').html('100% &middot; Upload complete');
                $('#upPill').css('display', 'inline-flex');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
                    + ' stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    + '<span><strong>Upload complete!</strong> Resume file stored securely.</span>'
                ).show();
                $('#uploaded_file_path').val('');   // v2 uses session_uuid, not file path
                $('#resume_original_name').val('');
                $('#resumeCheck').val('1');
                // Storage upload is isolated: do NOT clear ri.value or re-call showUploadedCard to avoid parser conflicts
            } else if (data.state === 'failed') {
                if (typeof _showUploadError === 'function') {
                    _showUploadError(data.error || 'Upload failed. Please remove and try again.');
                }
            }
        });
    }
    // ─────────────────────────────────────────────────────────────────────────

    // Tab-scoped instance ID ensures multiple open tabs never collide or clear each other's upload sessions
    window.__tabInstanceId = window.__tabInstanceId || (window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('tab_' + Math.random().toString(36).slice(2) + Date.now()));

    // Generate submission & update UUIDs on page load if not already set
    if (!$('#client_submission_uuid').val()) {
        $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));
    }
    if (!$('#client_update_uuid').val()) {
        $('#client_update_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('upd_' + Math.random().toString(36).slice(2) + Date.now()));
    }

    // Build a unique contextKey for this company + job + application + tab context
    function getUpContextKey() {
        var companyId = @json(auth()->user()->company_id ?? 0);
        var jobId = $('#job_id').val() || '0';
        var appId = $('#appId').val() || '0';
        return 'comp' + companyId + '_job' + jobId + '_app' + appId + '_tab' + window.__tabInstanceId;
    }

    /**
     * Intent: Clean resume state initialization for ADD vs EDIT candidate flows.
     * Prevents previous session resume leakage on page load/refresh for ADD Candidate mode.
     * In ADD mode ($page_event != 'EDIT' or appId == 0), start with a completely empty resume state,
     * clear any stale browser-side upload sessions from localStorage, and never auto-recover old sessions.
     * In EDIT mode ($page_event == 'EDIT' and appId > 0), preserve existing database resume state and
     * clear uncommitted temporary V2 upload sessions so they do not interfere with saved data.
     */
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        var isEditMode = String(@json($page_event ?? '')).toUpperCase() === 'EDIT' && parseInt($('#appId').val() || '0', 10) > 0;
        if (!isEditMode) {
            // ADD mode: clear stale session for current context & reset form fields & UI
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
            $('#uploaded_file_path').val('');
            $('#resume_original_name').val('');
            $('#resumeCheck').val('');
            $('#upInfo').removeClass('show');
            $('#uploadBox').show();
            $('#fileInfo').hide();
            $('#fileName').text('');
            var ri = document.getElementById('resume'); if (ri) ri.value = '';
        } else {
            // EDIT mode: clear uncommitted temporary V2 session; DB resume is rendered via Blade HTML if present
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
        }
    }
    // ─────────────────────────────────────────────────────────────────────────

    const uploadBox = document.getElementById('uploadBox');
    const resumeInput = document.getElementById('resume');
    var resumeUploadGeneration = 0;

    /**
     * Intent: Use the existing same-origin raw-byte uploader as a circuit breaker
     * when this browser or network cannot complete the direct Cloudflare R2 PUT.
     */
    function shouldUseResumeUploadFallback(error) {
        var message = String((error && error.message) || '');

        return message.indexOf('R2 upload failed') !== -1
            || message.indexOf('Network error during upload') !== -1
            || message.indexOf('Upload timed out') !== -1
            || message.indexOf('Upload feature is not enabled') !== -1
            || message.indexOf('Presign request failed') !== -1;
    }

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

        // Server is `max:6048` KB = 6 MB. Keep the JS check in sync.
        if (sizeMB > 6) {
            alert('Maximum file size is 6 MB.');
            return;
        }
        var uploadGeneration = ++resumeUploadGeneration;

        // Show human-readable size: KB for small files (avoids "0.07 MB" display)
        const sizeStr = sizeMB >= 1
            ? sizeMB.toFixed(2) + ' MB'
            : Math.round(file.size / 1024) + ' KB';

        $('#fileName').text(file.name + ' (' + sizeStr + ')');

        $('#fileInfo').css('display', 'flex');

        $('#resumeCheck').val('1');
        $('#resume_intent').val('ATTACH_IF_AVAILABLE');

        $('.download-file').addClass('d-none');

        // Phase 1: Capture selected File immediately; decouple R2 / server upload from parsing path
        const selectedFile = file;
        window.__upXhrChannelClaimed = true;
        window._currentActiveResumeFile = selectedFile;
        window._clientParserState = 'parsing';

        // Immediately display the Resume Card so the recruiter sees file info and live parser progress
        showUploadedCard(selectedFile, null);

        $('#upDetailParserStatus').html(
            '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
            '<span>Parsing in browser&hellip;</span>'
        );

        // ── Phase 1-5: 100% Client-Side Resume Parsing via https://parser.jero.works/ ──────────
        // Zero PHP/Laravel execution, zero MySQL/ES strain, zero R2 during parsing, runs 100% in Recruiter Browser RAM
        if (window.JeroClientParser && typeof window.JeroClientParser.parseAndPopulate === 'function') {
            var reqSkills = [];
            if (typeof candidateRequiredSkills !== 'undefined' && Array.isArray(candidateRequiredSkills)) {
                reqSkills = candidateRequiredSkills.map(function (s) { return s.name; });
            }

            window.JeroClientParser.parseAndPopulate(selectedFile, {
                isEditMode: IS_EDIT_MODE,
                requiredSkills: reqSkills,
                onProgress: function (stage, pct) {
                    $('#upDetailParserStatus').html(
                        '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                        '<span>' + $('<div>').text(stage).html() + ' (' + pct + '%)</span>'
                    );
                },
                onSuccess: function (result, totalMs) {
                    window._clientParserState = 'completed';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #41c08e; font-weight: 600;">✓ Parsed in ' + totalMs + 'ms</span>'
                    );
                    toastr.success('Resume parsed in browser and fields populated!');
                },
                onError: function (err) {
                    window._clientParserState = 'error';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #f59e0b; font-weight: 500;">Manual Entry</span>'
                    );
                    toastr.info('Resume parsing unavailable. Please enter candidate details manually.');
                }
            });
        }

        // ── PIPELINE 2: Independent Background Storage Upload ──────────
        setTimeout(function () {
            var uploadGeneration = ++resumeUploadGeneration;
            if (RESUMES_FEATURE_BROWSER_UPLOAD) {
                // v2 path: direct browser → R2 storage (runs independently in background)
                window.ResumePresign.startUpload(selectedFile, getUpContextKey()).catch(function (error) {
                    if (uploadGeneration !== resumeUploadGeneration
                        || !shouldUseResumeUploadFallback(error)
                        || typeof uploadWithProgress !== 'function') {
                        return;
                    }
                    window.ResumePresign.cancelUpload(getUpContextKey());
                    uploadWithProgress(selectedFile);
                });
            } else if (typeof uploadWithProgress === 'function') {
                // v1 path: same-origin multipart POST fallback
                uploadWithProgress(selectedFile);
            }
        }, 30);

        window.__upXhrChannelClaimed = false;
    }

    // ==========================
    // XHR progress helpers
    // ==========================
    let upXhr = null;

    function fmtKB(b) {
        return (b / 1024).toFixed(1) + ' KB';
    }

    function fmtSize(b) {
        return b >= 1024 * 1024
            ? (b / (1024 * 1024)).toFixed(1) + ' MB'
            : Math.round(b / 1024) + ' KB';
    }

    function extLabel(file) {
        var e = (file.name.split('.').pop() || '').toUpperCase();
        if (e !== 'PDF' && e !== 'DOC' && e !== 'DOCX') e = 'PDF';
        return e;
    }

    function resetProgress() {
        if (upXhr) { upXhr.abort(); upXhr = null; }
        window.__upXhrChannelClaimed = false;
        $('#upWrap').hide();
        $('#upBar').css('width', '0%');
        $('#upStats').text('0% &middot; 0 KB / 0 KB');
        $('#upPill').hide();
        const $b = $('#upBanner');
        $b.hide().removeClass('ok err').empty();
        $('#save-form, #update-form').prop('disabled', false);
    }

    // Go back to the dropzone / progress-possible state. Called by
    // the #removeFile button and the card's Replace/Remove buttons.
    function backToDropzone() {
        resumeUploadGeneration++;
        resetProgress();
        // Cancel v2 upload if in progress (aborts R2 XHR + clears session UUID)
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            window.ResumePresign.cancelUpload(getUpContextKey());
        }
        // Race condition 3 defense: abort any in-flight WASM client parsing
        if (window.JeroClientParser && typeof window.JeroClientParser.cancelActiveParse === 'function') {
            window.JeroClientParser.cancelActiveParse();
        }
        window._clientParserState = null;
        window._currentActiveResumeFile = null;
        $('#resume_session_uuid').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('.resume-card').removeClass('has-uploaded-resume');
        $('#upInfo').removeClass('show');
        $('#uploadBox').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        var isEdit = String(@json($page_event ?? '')).toUpperCase() === 'EDIT' && parseInt($('#appId').val() || '0', 10) > 0;
        $('#resume_intent').val(isEdit ? 'REMOVE' : 'NONE');
        $('.download-file').addClass('d-none');
        window.__upLoc = null;
    }

    // Populate the post-upload Resume Information card from the picked
    // file. Sets the initial details (Uploaded On, File Type, File Size,
    // Parser Status = Pending). When parserResponse is provided (from
    // re_test), it refreshes skills count, parser status, and pages.
    function showUploadedCard(file, parserResponse) {
        var ext = extLabel(file);
        var $info = $('#upInfo');
        $('#upFileBadgeExt').text(ext);
        if (ext === 'DOC' || ext === 'DOCX') {
            $('.up-file-badge').css({'background': '#dbeafe', 'color': '#2563eb'});
            $('.up-file-badge i').attr('class', 'fa-regular fa-file-word').css('color', '#2563eb');
            $('#upFileBadgeExt').css('color', '#2563eb');
        } else {
            $('.up-file-badge').css({'background': '#fee2e2', 'color': '#dc2626'});
            $('.up-file-badge i').attr('class', 'fa-regular fa-file-pdf').css('color', '#dc2626');
            $('#upFileBadgeExt').css('color', '#dc2626');
        }
        $('#upFileName').text(file.name);
        $('#upFileSub').html(fmtSize(file.size) + ' &bull; ' + ext);

        var now = new Date();
        var day = String(now.getDate()).padStart(2, '0');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var month = months[now.getMonth()];
        var year = now.getFullYear();
        var hh = String(now.getHours()).padStart(2, '0');
        var mm = String(now.getMinutes()).padStart(2, '0');
        $('#upDetailUploadedOn').text(day + ' ' + month + ' ' + year + ', ' + hh + ':' + mm);

        if (!window._clientParserState) {
            $('#upDetailParserStatus').text('Pending');
        }
        $('#upDetailFileType').text(ext);
        $('#upDetailFileSize').text(fmtSize(file.size));

        if (parserResponse) {
            window._clientParserState = 'completed';
            $('#upDetailParserStatus').html('<span style="color: #059669; font-weight: 700;">✓ Parsed</span>');
        }

        // Hide the dropzone + file-info strip and show the new card.
        $('#uploadBox').hide();
        $('#fileInfo').hide();
        $('.resume-card').addClass('has-uploaded-resume');
        $info.find('.up-info-banner').show();
        $info.addClass('show');
    }

    // ─── Zero-temp upload pipeline ───────────────────────────────────────────
    // xhr.send(file) sends raw bytes — PHP reads php://input stream directly
    // to R2. No multipart = no PHP upload_tmp_dir write. No CORS needed.
    // Flow: browser → POST raw bytes → Laravel streams to R2 → ParseResumeJob
    // ─────────────────────────────────────────────────────────────────────────

    var _parseStatusInterval = null;
    var _resumeUploadId      = null;

    function _showUploadError(msg) {
        $('#upBanner').removeClass('ok').addClass('err').html(
            '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            + '<circle cx="12" cy="12" r="10"/>'
            + '<line x1="15" y1="9" x2="9" y2="15"/>'
            + '<line x1="9" y1="9" x2="15" y2="15"/>'
            + '</svg><span><strong>Upload failed:</strong> ' + msg + '</span>'
        ).show();
        upXhr = null;
    }

    function _startParsePoller(uploadId) {
        // Zero server-side polling: Resume parsing is 100% client-side via JeroClientParser.
        _resumeUploadId = uploadId;
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
        resetProgress();
        window.__upXhrChannelClaimed = true;
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        _resumeUploadId = null;

        $('#upWrap').show();
        $('#upStats').text('0% &middot; ' + fmtKB(0) + ' / ' + fmtKB(file.size));
        window.__upLoc = { file: file };

        var mimeMap = {
            pdf:  'application/pdf',
            doc:  'application/msword',
            docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        };
        var ext         = (file.name.split('.').pop() || '').toLowerCase();
        var contentType = mimeMap[ext] || file.type || 'application/octet-stream';

        // Send raw file bytes — PHP reads php://input, no multipart, no temp file
        upXhr = new XMLHttpRequest();
        upXhr.open('POST', '{{ route("service_company.job-applications.uploadresume") }}', true);
        upXhr.setRequestHeader('Content-Type', contentType);
        upXhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        upXhr.setRequestHeader('X-File-Name', encodeURIComponent(file.name));
        upXhr.setRequestHeader('X-File-Size', file.size);
        upXhr.setRequestHeader('X-Candidate-Id', $('#appId').val() || '');
        upXhr.setRequestHeader('X-Job-Id', $('#job_id').val() || '');

        upXhr.upload.onprogress = function (e) {
            if (!e.lengthComputable) return;
            var pct = Math.round(e.loaded * 100 / e.total);
            $('#upBar').css('width', pct + '%');
            $('#upStats').text(pct + '% &middot; ' + fmtKB(e.loaded) + ' / ' + fmtKB(file.size));
        };

        upXhr.onload = function () {
            var r = null;
            try { r = JSON.parse(upXhr.responseText); } catch (_) {}
            upXhr = null;

            if (this.status >= 200 && this.status < 300 && r && r.success) {
                $('#upBar').css('width', '100%');
                $('#upStats').text('100% &middot; ' + fmtKB(file.size) + ' / ' + fmtKB(file.size));
                $('#upPill').css('display', 'inline-flex');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    + '<span><strong>Upload complete!</strong> Resume uploaded successfully.</span>'
                ).show();

                $('#uploaded_file_path').val(r.file_path || '');
                $('#resume_original_name').val(r.original_name || file.name || '');
                // showUploadedCard already invoked on initial selection; do not re-invoke here to keep parser isolated

                // Zero server polling: 100% of resume parsing runs client-side
            } else {
                _showUploadError((r && r.message) ? r.message : 'Upload failed. Try again.');
            }
        };

        upXhr.onerror = function () {
            _showUploadError('Network error during upload. Click Remove and try again.');
        };

        upXhr.send(file); // raw bytes → php://input → R2, zero local temp
    }

    // Remove button inside the post-upload Resume Information card.
    // Remove = reset state and stay on the empty dropzone.
    $(document).on('click', '#upRemoveBtn', function (e) {
        e.preventDefault();
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        backToDropzone();
    });

    $(document).on('click', '.up-info-banner-close', function (e) {
        e.preventDefault();
        $(this).closest('.up-info-banner').slideUp(180);
    });

    // ==========================
    // Choose File Button
    // Direct binding so stopPropagation() fires BEFORE the click reaches
    // #uploadBox — prevents resumeInput.click() from being called twice.
    // ==========================
    document.getElementById('chooseFileBtn').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resumeInput.click();
    });

    document.addEventListener('paste', function (e) {

        const files = e.clipboardData?.files;

        if (files && files.length) {

            const file = files[0];

            // Assign to input so form submit sees the file
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;

            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
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
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        backToDropzone();
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

            // Assign to input so form submit sees the file
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;

            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
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
        // char / word counters
        document.querySelectorAll('textarea').forEach((t) => {
            const c = t.parentElement ? t.parentElement.querySelector('.cc') : null;
            if (c) {
                if (t.id === 'careersummary') {
                    const updateCsCount = () => {
                        const words = t.value.trim().split(/\s+/).filter(Boolean);
                        c.textContent = words.length + ' / 100';
                    };
                    t.addEventListener('input', updateCsCount);
                    updateCsCount();
                } else {
                    const m = c.textContent.includes('1000') ? 1000 : 500;
                    t.addEventListener('input', () => {
                        c.textContent = t.value.length + ' / ' + m;
                    });
                }
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
                '<td><input type="text" placeholder="Enter skill"></td><td><input type="number" value="0" style="width:80px"></td><td><input type="number" value="0" style="width:80px"></td><td><select style="width:140px"><option>Currently</option><option>Within 6 Months</option><option>Within 1 Year</option><option>Within 2 Years</option><option>More than 2 Years</option></select></td><td><div class="sr"><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span></div></td><td><button class="ab" type="button">🗑️</button></td>';
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
        document.querySelectorAll('.ab').forEach((b) => {
            b.addEventListener('click', () => b.closest('tr').remove());
        });
</script>

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


        





        $('#current_ctc_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updatePayUI();
            updateBillUI();
        });
        $('#current_ectc_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updateBillUI();
            updatePayUI();
        });
        $('#current_th_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updateBillUI();
            updatePayUI();
        });
        setTimeout(() => {
              billRateUpdate();
              payRateUpdate();
          }, 1000);








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

<script>
// Internet connectivity check used by both save and update handlers.
async function isInternetAvailable() {
    try {
        await fetch("https://www.gstatic.com/generate_204", {
            method: "GET",
            cache: "no-cache",
            mode: "no-cors"
        });
        return true;
    } catch (error) {
        return false;
    }
}
</script>

@php if($page_event == 'EDIT'){ @endphp
<script>

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
                return; // Prevent duplicate submission
            }

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
                    if (!navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        return;
                    }
                    $("#update-form").prop("disabled", true);
                    $("#update-form").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
                        CKEDITOR.instances['recruiter_note'].updateElement();
                    }

                    // Ensure unique client_update_uuid for idempotency
                    if (!$('#client_update_uuid').val()) {
                        $('#client_update_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('upd_' + Math.random().toString(36).slice(2) + Date.now()));
                    }

                    var formData = new FormData($('#createForm')[0]);
                    var candidateEditId = '{{ @$application->id ?: @$appId }}';
                    $.ajax({
                        url         : '{{ url("service_company/job-applications/update") }}/' + candidateEditId + '?_token=' + '{{ csrf_token() }}',
                        type        : 'POST',
                        data        : formData,
                        processData : false,
                        contentType : false,
                        redirect    : true,
                        success     : function (response) {
                            $("#update-form").prop("disabled", false);
                            if (response.status == 'mailcheck') {
                                if (!navigator.onLine) {
                                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    $("#save-form").prop("disabled", false).html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    return;
                                }
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                Swal.fire("Warning", response.message , "warning");
                                return false;
                            }
                            if (response.status == 'notauthorized') {
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Not Authorized", response.message, "warning");
                                return false;
                            }

                            if (response.status !== 'success') {
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Update Failed", response.message || "The candidate could not be updated. Please try again.", "error");
                                return false;
                            }

                            if (typeof response.url !== 'string' || response.url.trim() === '') {
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Update Saved", "The candidate was updated, but the return page could not be opened. Please return to the candidate tracker.", "warning");
                                return false;
                            }

                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            Swal.fire({
                                text: "You have successfully updated",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    var emailField = form.querySelector('[name="email1"]');
                                    var passwordField = form.querySelector('[name="password1"]');
                                    if (emailField) emailField.value = "";
                                    if (passwordField) passwordField.value = "";
                                    window.location.assign(response.url);
                                }
                            });
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $("#update-form").prop("disabled", false);
                            $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            if (xhr.status === 0 || !navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                return;
                            }
                            console.error('Update AJAX error:', textStatus, errorThrown, xhr.responseText);
                            var serverMsg = '';
                            try {
                                var body = xhr.responseText || '';
                                var jsonStart = body.indexOf('{');
                                if (jsonStart >= 0) {
                                    var parsed = JSON.parse(body.substring(jsonStart));
                                    serverMsg = parsed.message || parsed.error || '';
                                }
                            } catch (e) {}

                            if (xhr.status === 409) {
                                Swal.fire({
                                    title: "Conflict Detected",
                                    text: serverMsg || "This candidate was modified in another session (or a concurrent update is currently processing). Please refresh the page.",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Refresh Page",
                                    cancelButtonText: "Stay Here"
                                }).then(function (res) {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                                return;
                            }

                            var display = serverMsg
                                ? serverMsg
                                : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                            Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
                        },
                        complete: function () {
                            if ($("#update-form").length) {
                                $("#update-form").prop("disabled", false);
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                            }
                        }
                    });
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
  var tagifyskill = null;
  var tagifylocation = null;

  function candidateNormalizeSuggestions(data) {
      if (!Array.isArray(data)) {
          return [];
      }

      return data.map(function (item) {
          if (typeof item === 'string') {
              return item.trim();
          }

          return String((item && (item.value || item.name || item.text)) || '').trim();
      }).filter(Boolean);
  }

  function candidateDebounce(callback, delay) {
      var timer;

      return function () {
          var context = this;
          var args = arguments;
          window.clearTimeout(timer);
          timer = window.setTimeout(function () {
              callback.apply(context, args);
          }, delay);
      };
  }

  var candidateSkillInput = document.querySelector('#kt_tagify_2');
  var candidateLocationInput = document.querySelector('#kt_tagify_work_location');

  if (typeof window.Tagify === 'function' && candidateSkillInput) {
      tagifyskill = new window.Tagify(candidateSkillInput, {
          whitelist: [],
          maxTags: Infinity,
          duplicates: false,
          dropdown: {
              maxItems: 50,
              enabled: 1,
              closeOnSelect: true,
              highlightFirst: true
          }
      });
      window.tagifyskill = tagifyskill;

      // The custom visual layer delegates all mutations to Tagify so the submitted contract stays unchanged.
      var candidateRequiredSkills = @json($candidateRequiredSkills ?? []);
      var candidatePrimarySkillQuickInput = document.querySelector('#candidatePrimarySkillInput');
      var candidatePrimarySkillAdd = document.querySelector('#candidatePrimarySkillAdd');
      var candidatePrimarySkillSuggestions = document.querySelector('#candidatePrimarySkillSuggestions');
      var candidatePrimarySkillsCard = document.querySelector('.primary-skills-card');
      var candidatePrimarySkillChips = document.querySelector('#candidatePrimarySkillChips');
      var candidatePrimarySkillEmpty = document.querySelector('#candidatePrimarySkillEmpty');
      var candidateSkillMatch = document.querySelector('#candidateSkillMatch');
      var candidateSkillMatchRing = document.querySelector('#candidateSkillMatchRing');
      var candidateSkillMatchPercentage = document.querySelector('#candidateSkillMatchPercentage');
      var candidateSkillMatchDetail = document.querySelector('#candidateSkillMatchDetail');
      var candidateSuggestedSkills = document.querySelector('#candidateSuggestedSkills');
      var candidateSuggestedSkillsList = document.querySelector('#candidateSuggestedSkillsList');
      var candidateRemainingSkills = document.querySelector('#candidateRemainingSkills');
      var candidateSkillSuggestionItems = [];
      var candidateSkillLastSuggestions = [];
      var candidateSkillActiveIndex = -1;
      var candidateSkillRequest = null;
      var candidateSkillRequestSequence = 0;

      if (tagifyskill.DOM && tagifyskill.DOM.scope) {
          tagifyskill.DOM.scope.classList.add('candidate-primary-skill-tagify-source');
          tagifyskill.DOM.scope.setAttribute('aria-hidden', 'true');
      }

      function candidateSkillKey(value) {
          return String(value || '').trim().replace(/\s+/g, ' ').toLocaleLowerCase();
      }

      function candidateSkillName(skill) {
          return String((skill && (skill.name || skill.skill_name || skill.value)) || '').trim();
      }

      function candidateUniqueSkills(skills) {
          var unique = new Map();
          (Array.isArray(skills) ? skills : []).forEach(function (skill) {
              var name = candidateSkillName(skill);
              var id = String((skill && skill.id) || '').trim();
              if (!name) {
                  return;
              }
              var key = 'name:' + candidateSkillKey(name);
              if (!unique.has(key)) {
                  unique.set(key, { id: id, name: name, value: name });
              }
          });
          return Array.from(unique.values());
      }

      function candidateSelectedSkills() {
          return candidateUniqueSkills((tagifyskill.value || []).map(function (skill) {
              return { id: skill.id || '', name: skill.value || '' };
          }));
      }

      function candidateSkillsMatch(requiredSkill, selectedSkill) {
          var requiredId = String(requiredSkill.id || '').trim();
          var selectedId = String(selectedSkill.id || '').trim();
          if (requiredId && selectedId) {
              return requiredId === selectedId;
          }
          return candidateSkillKey(requiredSkill.name) === candidateSkillKey(selectedSkill.name);
      }

      function closeCandidateSkillSuggestions() {
          if (!candidatePrimarySkillSuggestions || !candidatePrimarySkillQuickInput) {
              return;
          }

          candidatePrimarySkillSuggestions.hidden = true;
          candidatePrimarySkillSuggestions.replaceChildren();
          if (candidatePrimarySkillsCard) {
              candidatePrimarySkillsCard.classList.remove('has-open-skill-suggestions');
          }
          candidatePrimarySkillQuickInput.setAttribute('aria-expanded', 'false');
          candidatePrimarySkillQuickInput.removeAttribute('aria-activedescendant');
          candidateSkillSuggestionItems = [];
          candidateSkillActiveIndex = -1;
      }

      function setActiveCandidateSkillSuggestion(index) {
          if (!candidateSkillSuggestionItems.length || !candidatePrimarySkillQuickInput) {
              candidateSkillActiveIndex = -1;
              candidatePrimarySkillQuickInput.removeAttribute('aria-activedescendant');
              return;
          }

          candidateSkillActiveIndex = (index + candidateSkillSuggestionItems.length) % candidateSkillSuggestionItems.length;
          candidateSkillSuggestionItems.forEach(function (item, itemIndex) {
              var isActive = itemIndex === candidateSkillActiveIndex;
              item.element.classList.toggle('is-active', isActive);
              item.element.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });

          var activeItem = candidateSkillSuggestionItems[candidateSkillActiveIndex];
          candidatePrimarySkillQuickInput.setAttribute('aria-activedescendant', activeItem.element.id);
          activeItem.element.scrollIntoView({ block: 'nearest' });
      }

      function renderCandidateSkillSuggestions(suggestions, state, query) {
          if (!candidatePrimarySkillSuggestions || !candidatePrimarySkillQuickInput) {
              return;
          }

          candidateSkillLastSuggestions = Array.isArray(suggestions) ? suggestions : [];
          var normalizedQuery = candidateSkillKey(query);
          var selected = candidateSelectedSkills();
          var combined = candidateUniqueSkills(candidateRequiredSkills.concat(candidateSkillLastSuggestions.map(function (name) {
              return { name: name };
          }))).filter(function (skill) {
              var matchesQuery = !normalizedQuery || candidateSkillKey(skill.name).indexOf(normalizedQuery) !== -1;
              var isSelected = selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(skill, selectedSkill);
              });
              return matchesQuery && !isSelected;
          }).slice(0, 20);

          candidatePrimarySkillSuggestions.replaceChildren();
          candidateSkillSuggestionItems = [];
          candidateSkillActiveIndex = -1;

          combined.forEach(function (skill, index) {
              var option = document.createElement('button');
              option.type = 'button';
              option.id = 'candidate-primary-skill-option-' + index;
              option.className = 'candidate-primary-skill-suggestion';
              option.setAttribute('role', 'option');
              option.setAttribute('aria-selected', 'false');
              option.textContent = skill.name;
              option.addEventListener('mousedown', function (event) {
                  event.preventDefault();
              });
              option.addEventListener('click', function () {
                  addCandidatePrimarySkill(skill.name);
                  closeCandidateSkillSuggestions();
              });
              candidatePrimarySkillSuggestions.appendChild(option);
              candidateSkillSuggestionItems.push({ element: option, skill: skill });
          });

          if (state === 'loading' || state === 'error' || (!combined.length && normalizedQuery)) {
              var status = document.createElement('div');
              status.className = 'candidate-primary-skill-suggestion-status';
              status.setAttribute('role', 'status');
              status.textContent = state === 'loading'
                  ? 'Searching skills...'
                  : (state === 'error' ? 'Unable to load suggestions. You can still add this skill.' : 'No matching skills found.');
              candidatePrimarySkillSuggestions.appendChild(status);
          }

          if (!candidatePrimarySkillSuggestions.childElementCount) {
              closeCandidateSkillSuggestions();
              return;
          }

          candidatePrimarySkillSuggestions.hidden = false;
          if (candidatePrimarySkillsCard) {
              candidatePrimarySkillsCard.classList.add('has-open-skill-suggestions');
          }
          candidatePrimarySkillQuickInput.setAttribute('aria-expanded', 'true');
      }

      function addCandidatePrimarySkill(value) {
          var name = String(value || '').trim();
          if (!name) {
              return;
          }
          var requiredSkill = candidateUniqueSkills(candidateRequiredSkills).find(function (skill) {
              return candidateSkillKey(skill.name) === candidateSkillKey(name);
          });
          tagifyskill.addTags([requiredSkill ? { value: requiredSkill.name, id: requiredSkill.id } : name]);
          if (candidatePrimarySkillQuickInput) {
              candidatePrimarySkillQuickInput.value = '';
              candidatePrimarySkillQuickInput.focus();
          }
          closeCandidateSkillSuggestions();
      }

      function refreshCandidatePrimarySkills() {
          var required = candidateUniqueSkills(candidateRequiredSkills);
          var selected = candidateSelectedSkills();
          var matched = required.filter(function (requiredSkill) {
              return selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(requiredSkill, selectedSkill);
              });
          });
          var missing = required.filter(function (requiredSkill) {
              return !selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(requiredSkill, selectedSkill);
              });
          });
          var percentage = required.length ? Math.round((matched.length / required.length) * 100) : 0;

          candidatePrimarySkillChips.replaceChildren();
          selected.forEach(function (skill) {
              var chip = document.createElement('span');
              chip.className = 'candidate-primary-skill-chip';
              var label = document.createElement('span');
              label.textContent = skill.name;
              var remove = document.createElement('button');
              remove.type = 'button';
              remove.setAttribute('aria-label', 'Remove ' + skill.name);
              remove.title = 'Remove ' + skill.name;
              remove.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
              remove.addEventListener('click', function () {
                  tagifyskill.removeTag(skill.name);
              });
              chip.append(label, remove);
              candidatePrimarySkillChips.appendChild(chip);
          });
          candidatePrimarySkillEmpty.hidden = selected.length > 0;

          candidateSkillMatch.classList.toggle('is-empty', required.length === 0);
          candidateSkillMatch.classList.toggle('is-complete', required.length > 0 && percentage === 100);
          candidateSkillMatchRing.style.setProperty('--candidate-skill-match', percentage + '%');
          candidateSkillMatchPercentage.textContent = required.length ? percentage + '%' : '--';
          candidateSkillMatchDetail.textContent = !required.length
              ? 'No job skills defined'
              : (percentage === 100
                  ? 'All required skills matched'
                  : matched.length + ' of ' + required.length + ' required skills matched');

          candidateSuggestedSkillsList.replaceChildren();
          missing.forEach(function (skill) {
              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'candidate-suggested-skill';
              var label = document.createElement('span');
              label.textContent = skill.name;
              var icon = document.createElement('span');
              icon.className = 'candidate-suggested-skill-icon';
              icon.setAttribute('aria-hidden', 'true');
              icon.innerHTML = '<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M10 4.25v11.5M4.25 10h11.5" /></svg>';
              button.append(label, icon);
              button.addEventListener('click', function () {
                  addCandidatePrimarySkill(skill.name);
              });
              candidateSuggestedSkillsList.appendChild(button);
          });
          candidateRemainingSkills.textContent = missing.length + ' remaining ' + (missing.length === 1 ? 'skill' : 'skills');
          candidateSuggestedSkills.hidden = required.length === 0 || missing.length === 0;

          if (candidatePrimarySkillQuickInput && document.activeElement === candidatePrimarySkillQuickInput) {
              renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', candidatePrimarySkillQuickInput.value);
          }
      }

      tagifyskill.on('add', refreshCandidatePrimarySkills);
      tagifyskill.on('remove', refreshCandidatePrimarySkills);
      window.refreshCandidatePrimarySkills = refreshCandidatePrimarySkills;

      if (candidatePrimarySkillAdd) {
          candidatePrimarySkillAdd.addEventListener('click', function () {
              addCandidatePrimarySkill(candidatePrimarySkillQuickInput.value);
              closeCandidateSkillSuggestions();
          });
      }
      if (candidatePrimarySkillQuickInput) {
          candidatePrimarySkillQuickInput.addEventListener('keydown', function (event) {
              if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                  event.preventDefault();
                  if (candidatePrimarySkillSuggestions.hidden) {
                      renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', candidatePrimarySkillQuickInput.value);
                  }
                  setActiveCandidateSkillSuggestion(candidateSkillActiveIndex + (event.key === 'ArrowDown' ? 1 : -1));
                  return;
              }

              if (event.key === 'Escape') {
                  event.preventDefault();
                  closeCandidateSkillSuggestions();
                  return;
              }

              if (event.key === 'Enter') {
                  event.preventDefault();
                  if (candidateSkillActiveIndex >= 0 && candidateSkillSuggestionItems[candidateSkillActiveIndex]) {
                      addCandidatePrimarySkill(candidateSkillSuggestionItems[candidateSkillActiveIndex].skill.name);
                  } else {
                      addCandidatePrimarySkill(candidatePrimarySkillQuickInput.value);
                  }
              }
          });

          candidatePrimarySkillQuickInput.addEventListener('focus', function () {
              var query = candidatePrimarySkillQuickInput.value.trim();
              renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', query);
          });

          candidatePrimarySkillQuickInput.addEventListener('blur', function () {
              window.setTimeout(closeCandidateSkillSuggestions, 120);
          });
      }

      var _candidateSkillCache = new Map();
      var fetchCandidateSkillSuggestions = candidateDebounce(function (query) {
          if (!candidatePrimarySkillQuickInput || candidatePrimarySkillQuickInput.value.trim() !== query) {
              return;
          }

          var cacheKey = query.toLowerCase();
          if (_candidateSkillCache.has(cacheKey)) {
              var cached = _candidateSkillCache.get(cacheKey);
              tagifyskill.settings.whitelist = cached;
              renderCandidateSkillSuggestions(cached, 'idle', query);
              return;
          }

          if (candidateSkillRequest && candidateSkillRequest.readyState !== 4) {
              candidateSkillRequest.abort();
          }

          var requestSequence = ++candidateSkillRequestSequence;
          candidateSkillRequest = window.jQuery.get("{{ url('/') }}/skill-suggestions", { q: query })
              .done(function (data) {
                  if (requestSequence !== candidateSkillRequestSequence || candidatePrimarySkillQuickInput.value.trim() !== query) {
                      return;
                  }
                  var suggestions = candidateNormalizeSuggestions(data);
                  _candidateSkillCache.set(cacheKey, suggestions);
                  tagifyskill.settings.whitelist = suggestions;
                  renderCandidateSkillSuggestions(suggestions, 'idle', query);
              })
              .fail(function (_xhr, status) {
                  if (status !== 'abort' && requestSequence === candidateSkillRequestSequence && candidatePrimarySkillQuickInput.value.trim() === query) {
                      renderCandidateSkillSuggestions([], 'error', query);
                  }
              });
      }, 250);

      if (candidatePrimarySkillQuickInput) {
          candidatePrimarySkillQuickInput.addEventListener('input', function () {
              var query = candidatePrimarySkillQuickInput.value.trim();
              if (query.length >= 1) {
                  renderCandidateSkillSuggestions([], 'loading', query);
                  fetchCandidateSkillSuggestions(query);
              } else {
                  if (candidateSkillRequest && candidateSkillRequest.readyState !== 4) {
                      candidateSkillRequest.abort();
                  }
                  candidateSkillRequestSequence++;
                  renderCandidateSkillSuggestions([], 'idle', '');
              }
          });
      }

      window.setTimeout(refreshCandidatePrimarySkills, 0);
  }

  if (typeof window.Tagify === 'function' && candidateLocationInput) {
      var initialLocations = candidateNormalizeSuggestions(@json($cities));
      var defaultLocation = String(@json(@$default_loc) || '').trim();
      if (defaultLocation && initialLocations.indexOf(defaultLocation) === -1) {
          initialLocations.push(defaultLocation);
      }

      tagifylocation = new window.Tagify(candidateLocationInput, {
          mode: 'select',
          whitelist: initialLocations,
          enforceWhitelist: true,
          maxTags: 1,
          duplicates: false,
          editTags: false,
          skipInvalid: true,
          dropdown: {
              classname: 'candidate-location-dropdown',
              maxItems: 20,
              enabled: 0,
              closeOnSelect: true,
              highlightFirst: true,
              searchKeys: ['value']
          }
      });

      if (tagifylocation.DOM && tagifylocation.DOM.scope) {
          tagifylocation.DOM.scope.classList.add('candidate-location-tagify');
          tagifylocation.DOM.scope.setAttribute('aria-label', 'Current Location');
          tagifylocation.DOM.scope.addEventListener('scroll', function () {
              if (tagifylocation.DOM.scope.scrollLeft !== 0) {
                  tagifylocation.DOM.scope.scrollLeft = 0;
              }
          });
          tagifylocation.DOM.scope.addEventListener('click', function (e) {
              if (!e.target.closest('.tagify__tag__removeBtn')) {
                  tagifylocation.dropdown.show('');
              }
          });
      }

      var candidateLocationSyncing = false;
      var candidateLocationRequest = null;
      var candidateLocationRequestSequence = 0;
      var candidateLocationLatestQuery = '';

      function candidateLocationParent() {
          return (tagifylocation && tagifylocation.DOM && tagifylocation.DOM.scope)
              ? tagifylocation.DOM.scope.closest('.candidate-field-control--location')
              : null;
      }

      function updateCandidateLocationTagState() {
          var parentEl = candidateLocationParent();
          var hasTag = tagifylocation && Array.isArray(tagifylocation.value) && tagifylocation.value.length > 0;
          if (parentEl) {
              parentEl.classList.toggle('has-tag', hasTag);
          }
          if (tagifylocation && tagifylocation.DOM && tagifylocation.DOM.scope) {
              tagifylocation.DOM.scope.scrollLeft = 0;
          }
      }

      var candidateInvalidCityKeywords = [
          'remote', 'hybrid', 'onsite', 'on-site', 'india', 'united states', 'usa', 'uk', 'united kingdom',
          'unknown', 'anywhere', 'work from home', 'wfh', 'telecommute', 'relocate', 'open to relocate',
          'worldwide', 'global', 'na', 'n/a', 'null', 'undefined'
      ];

      function findAuthoritativeCityMatch(candidateTerm, suggestions) {
          if (!candidateTerm || !Array.isArray(suggestions) || !suggestions.length) {
              return null;
          }

          var term = String(candidateTerm || '').trim().toLowerCase();
          if (!term || term.length < 2 || candidateInvalidCityKeywords.indexOf(term) !== -1) {
              return null;
          }

          for (var i = 0; i < suggestions.length; i++) {
              var item = String(suggestions[i] || '').trim();
              if (!item) continue;
              var parts = item.split('|').map(function (p) { return p.trim(); });
              var cityName = (parts[0] || '').toLowerCase();

              // Exact match on city name: e.g. "pune" === "pune"
              if (cityName === term) {
                  return item;
              }

              // Check slash aliases: e.g. "bengaluru / bangalore"
              var aliases = cityName.split(/[\/\(\)]/).map(function (a) { return a.trim(); }).filter(Boolean);
              if (aliases.indexOf(term) !== -1) {
                  return item;
              }
          }

          return null;
      }

      function currentCandidateLocation() {
          return String((tagifylocation.value[0] && tagifylocation.value[0].value) || '').trim();
      }

      var _candidateLocationCache = new Map();
      function getCandidateLocationSuggestions(term) {
          var key = String(term || '').toLowerCase().trim();
          if (!key) return Promise.resolve([]);
          if (_candidateLocationCache.has(key)) {
              return Promise.resolve(_candidateLocationCache.get(key));
          }
          return new Promise(function (resolve) {
              window.jQuery.get("{{ url('/') }}/city-job-suggestions", { q: term })
                  .done(function (data) {
                      var norm = candidateNormalizeSuggestions(data);
                      _candidateLocationCache.set(key, norm);
                      resolve(norm);
                  })
                  .fail(function () {
                      resolve([]);
                  });
          });
      }

      function resolveAndSetCandidateLocation(rawInput) {
          candidateLocationSyncing = true;
          tagifylocation.removeAllTags();
          candidateLocationSyncing = false;
          updateCandidateLocationTagState();

          if (!rawInput) {
              return;
          }

          var candidateTerms = [];
          if (typeof rawInput === 'object') {
              if (rawInput.city) candidateTerms.push(String(rawInput.city).trim());
              if (rawInput.location && typeof rawInput.location === 'string') candidateTerms.push(String(rawInput.location).trim());
          } else if (typeof rawInput === 'string') {
              var clean = String(rawInput || '').replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ').trim();
              var chunks = clean.split(/[,|\/\-]/).map(function (c) {
                  return c.trim();
              }).filter(function (c) {
                  return c.length >= 2 && candidateInvalidCityKeywords.indexOf(c.toLowerCase()) === -1;
              });

              if (chunks.length > 0) {
                  candidateTerms = candidateTerms.concat(chunks);
              } else if (clean.length >= 2 && candidateInvalidCityKeywords.indexOf(clean.toLowerCase()) === -1) {
                  candidateTerms.push(clean);
              }
          }

          if (!candidateTerms.length) {
              return;
          }

          // 1. Try matching against current whitelist / initial cities first
          for (var t = 0; t < candidateTerms.length; t++) {
              var immediateMatch = findAuthoritativeCityMatch(candidateTerms[t], tagifylocation.settings.whitelist);
              if (immediateMatch) {
                  applyVerifiedCity(immediateMatch);
                  return;
              }
          }

          // 2. Query Elasticsearch /city-job-suggestions for candidate terms (using in-memory cache)
          var primaryQuery = candidateTerms[0];
          getCandidateLocationSuggestions(primaryQuery).then(function (suggestions) {
              var foundMatch = null;
              for (var i = 0; i < candidateTerms.length; i++) {
                  foundMatch = findAuthoritativeCityMatch(candidateTerms[i], suggestions);
                  if (foundMatch) break;
              }

              if (!foundMatch && candidateTerms.length > 1) {
                  var secondaryQuery = candidateTerms[1];
                  getCandidateLocationSuggestions(secondaryQuery).then(function (suggestions2) {
                      var match2 = findAuthoritativeCityMatch(secondaryQuery, suggestions2);
                      if (match2) {
                          applyVerifiedCity(match2);
                      } else {
                          candidateLocationSyncing = true;
                          tagifylocation.removeAllTags();
                          candidateLocationSyncing = false;
                          updateCandidateLocationTagState();
                      }
                  });
                  return;
              }

              if (foundMatch) {
                  applyVerifiedCity(foundMatch);
              } else {
                  candidateLocationSyncing = true;
                  tagifylocation.removeAllTags();
                  candidateLocationSyncing = false;
                  updateCandidateLocationTagState();
              }
          });

          function applyVerifiedCity(verifiedCity) {
              if (tagifylocation.settings.whitelist.indexOf(verifiedCity) === -1) {
                  tagifylocation.settings.whitelist.unshift(verifiedCity);
              }
              candidateLocationSyncing = true;
              tagifylocation.removeAllTags();
              tagifylocation.addTags([verifiedCity]);
              candidateLocationSyncing = false;
              updateCandidateLocationTagState();
              tagifylocation.dropdown.hide();
          }
      }

      function setSingleCandidateLocation(value, isVerified) {
          var normalizedValue = String(value || '').trim();
          if (!normalizedValue) {
              candidateLocationSyncing = true;
              tagifylocation.removeAllTags();
              candidateLocationSyncing = false;
              updateCandidateLocationTagState();
              return;
          }

          if (isVerified || (Array.isArray(tagifylocation.settings.whitelist) && tagifylocation.settings.whitelist.indexOf(normalizedValue) !== -1)) {
              candidateLocationSyncing = true;
              tagifylocation.removeAllTags();
              if (tagifylocation.settings.whitelist.indexOf(normalizedValue) === -1) {
                  tagifylocation.settings.whitelist.unshift(normalizedValue);
              }
              tagifylocation.addTags([normalizedValue]);
              candidateLocationSyncing = false;
              updateCandidateLocationTagState();
          } else {
              resolveAndSetCandidateLocation(normalizedValue);
          }
      }
      window.setSingleCandidateLocation = setSingleCandidateLocation;
      window.resolveAndSetCandidateLocation = resolveAndSetCandidateLocation;
      window.tagifylocation = tagifylocation;

      var fetchCandidateLocationSuggestions = candidateDebounce(function (query) {
          if (candidateLocationLatestQuery !== query) {
              return;
          }

          var cacheKey = query.toLowerCase();
          if (_candidateLocationCache.has(cacheKey)) {
              var cached = _candidateLocationCache.get(cacheKey);
              var selectedLocation = currentCandidateLocation();
              if (selectedLocation && cached.indexOf(selectedLocation) === -1) {
                  cached = [selectedLocation].concat(cached);
              }
              tagifylocation.settings.whitelist = cached;
              if (!selectedLocation) {
                  tagifylocation.dropdown.show(query);
              }
              return;
          }

          if (candidateLocationRequest && candidateLocationRequest.readyState !== 4) {
              candidateLocationRequest.abort();
          }

          var requestSequence = ++candidateLocationRequestSequence;
          candidateLocationRequest = window.jQuery.get("{{ url('/') }}/city-job-suggestions", { q: query })
              .done(function (data) {
                  if (requestSequence !== candidateLocationRequestSequence || candidateLocationLatestQuery !== query) {
                      return;
                  }
                  var suggestions = candidateNormalizeSuggestions(data);
                  _candidateLocationCache.set(cacheKey, suggestions);
                  var selectedLocation = currentCandidateLocation();
                  if (selectedLocation && suggestions.indexOf(selectedLocation) === -1) {
                      suggestions.unshift(selectedLocation);
                  }
                  tagifylocation.settings.whitelist = suggestions;
                  if (!selectedLocation) {
                      tagifylocation.dropdown.show(query);
                  }
              });
      }, 250);

      tagifylocation.on('focus', function () {
          tagifylocation.dropdown.show('');
      });

      tagifylocation.on('input', function (event) {
          var query = String(event.detail.value || '').trim();
          candidateLocationLatestQuery = query;
          if (query.length < 2) {
              if (candidateLocationRequest && candidateLocationRequest.readyState !== 4) {
                  candidateLocationRequest.abort();
              }
              candidateLocationRequestSequence++;
              tagifylocation.dropdown.show(query);
              return;
          }

          fetchCandidateLocationSuggestions(query);
      });

      tagifylocation.on('add', function (event) {
          var selectedLocation = String((event.detail.data && event.detail.data.value) || '').trim();
          candidateLocationLatestQuery = '';
          if (candidateLocationRequest && candidateLocationRequest.readyState !== 4) {
              candidateLocationRequest.abort();
          }
          candidateLocationRequestSequence++;
          if (selectedLocation && tagifylocation.settings.whitelist.indexOf(selectedLocation) === -1) {
              tagifylocation.settings.whitelist.unshift(selectedLocation);
          }
          if (!candidateLocationSyncing && (tagifylocation.value.length !== 1 || currentCandidateLocation() !== selectedLocation)) {
              setSingleCandidateLocation(selectedLocation, true);
          }
          updateCandidateLocationTagState();
          tagifylocation.dropdown.hide();
      });

      tagifylocation.on('remove', function () {
          candidateLocationLatestQuery = '';
          if (candidateLocationRequest && candidateLocationRequest.readyState !== 4) {
              candidateLocationRequest.abort();
          }
          candidateLocationRequestSequence++;
          updateCandidateLocationTagState();
          tagifylocation.dropdown.hide();
          if (tagifylocation.DOM && tagifylocation.DOM.input) {
              setTimeout(function () {
                  tagifylocation.DOM.input.focus();
              }, 50);
          }
      });

      if (defaultLocation) {
          setSingleCandidateLocation(defaultLocation, true);
          tagifylocation.dropdown.hide();
      }

      var candidateLocationClearBtn = document.querySelector('.candidate-location-clear');
      if (candidateLocationClearBtn) {
          candidateLocationClearBtn.addEventListener('click', function (e) {
              e.preventDefault();
              e.stopPropagation();
              candidateLocationSyncing = true;
              tagifylocation.removeAllTags();
              candidateLocationSyncing = false;
              updateCandidateLocationTagState();
              tagifylocation.dropdown.hide();
              if (tagifylocation.DOM && tagifylocation.DOM.input) {
                  setTimeout(function () {
                      tagifylocation.DOM.input.focus();
                  }, 50);
              }
          });
      }
      updateCandidateLocationTagState();
  }
   $('body').on('change', '#current_ctc_cur', function(){
   			var	current_ctc_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_ctc_cur;
			document.getElementById("current_th_cur").selectedIndex = current_ctc_cur;
   });
   $('body').on('change', '#current_ectc_cur', function(){
   			var	current_ectc_cur	=	$(this).val();
			document.getElementById("current_ctc_cur").selectedIndex = current_ectc_cur;
			document.getElementById("current_th_cur").selectedIndex = current_ectc_cur;
   });
   $('body').on('change', '#current_th_cur', function(){
   			var	current_th_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_th_cur;
			document.getElementById("current_ctc_cur").selectedIndex = current_th_cur;
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").value = current_ctc_mode;
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
            

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


		
</script>


<script>
  

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
                    if (!navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        submitButton.disabled = false;
                        return;
                    }
                    $("#save-form").prop("disabled", true);
                    $("#save-form").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
                        CKEDITOR.instances['recruiter_note'].updateElement();
                    }

                    // Ensure unique client_submission_uuid for idempotency
                    if (!$('#client_submission_uuid').val()) {
                        $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));
                    }

                    var formData = new FormData($('#createForm')[0]);
                    $.ajax({
                        url         : '{{route('service_company.job-applications.store')}}'+'?_token=' + '{{ csrf_token() }}',
                        type        : 'POST',
                        data        : formData,
                        processData : false,
                        contentType : false,
                        redirect    : true,
                        success     : function (response) {
                            $("#save-form").prop("disabled", false);
                            if (response.status == 'coinCheck') {
                                Swal.fire("Success", response.message , "warning");
                                return false;
                            }
                            if (response.status == 'mailcheck') {
                                if (!navigator.onLine) {
                                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    $("#save-form").prop("disabled", false).html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    return;
                                }

                                $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                Swal.fire("Warning", response.message , "warning");
                                return false;
                            }

                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = true;
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
                                    var e1 = form.querySelector('[name="email1"]'); if (e1) e1.value = "";
                                    var p1 = form.querySelector('[name="password1"]'); if (p1) p1.value = "";
                                }
                            });

                            // Clear v2 session from localStorage so the next ADD form does not recover this already-completed session.
                            if (typeof RESUMES_FEATURE_BROWSER_UPLOAD !== 'undefined' && RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) { window.ResumePresign.cancelUpload(); }

                            // Rotate client_submission_uuid for any subsequent submission
                            $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));

                            if (response.url) {
                                window.location.href = response.url;
                            }
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $("#save-form").prop("disabled", false);
                            $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            if (xhr.status === 0 || !navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                return;
                            }
                            console.error('Save AJAX error:', textStatus, errorThrown, xhr.responseText);
                            var serverMsg = '';
                            try {
                                var body = xhr.responseText || '';
                                var jsonStart = body.indexOf('{');
                                if (jsonStart >= 0) {
                                    var parsed = JSON.parse(body.substring(jsonStart));
                                    serverMsg = parsed.message || parsed.error || '';
                                }
                            } catch (e) {}

                            if (xhr.status === 409) {
                                Swal.fire("Concurrent Submission", serverMsg || "This candidate submission is already being processed. Please wait a moment.", "warning");
                                return;
                            }

                            var display = serverMsg
                                ? serverMsg
                                : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                            Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
                        },
                        complete: function () {
                            if ($("#save-form").length) {
                                $("#save-form").prop("disabled", false);
                                $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                            }
                        }
                    });
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
    // Deprecated: Resume parsing is executed 100% client-side via JeroClientParser.
    // Zero server POST requests to extract.resume-job-applications or file.unset.
    return;
}


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

          

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
        // Deprecated: Resume parsing is executed 100% client-side in processFile() via JeroClientParser.
        // Zero duplicate uploads to /uploadresume, zero server parse requests.
        return;
    }

    // Attach event listener to file input
    // document.getElementById('resume').addEventListener('change', handleFileInputChange);

    
$(document).ready(function() {
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






       


// Initialize CKEditor for recruiter_note
if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.replace('recruiter_note', { height: 150 });

    // When CKEditor is ready, hook into its iframe paste events.
    // If the user pastes a file (e.g. a resume) while the editor has focus,
    // intercept it and route to the resume upload handler instead of letting
    // CKEditor consume the event (CKEditor's iframe swallows document-level paste).
    CKEDITOR.instances['recruiter_note'].on('instanceReady', function () {
        var editorDoc = this.document.$;
        editorDoc.addEventListener('paste', function (e) {
            var files = e.clipboardData && e.clipboardData.files;
            if (files && files.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                var file = files[0];
                var dt = new DataTransfer();
                dt.items.add(file);
                resumeInput.files = dt.files;
                processFile(file);
            }
        }, true);
    });
}

// Recruiter note templates keyed by id
var recruiterNoteTemplates = @json(isset($recruiterNoteTemplates) ? $recruiterNoteTemplates->keyBy('id') : collect());

// Template selector → load content into CKEditor
$(document).on('change', '#recruiter_note_template_select', function () {
    var id = $(this).val();
    if (!id || !recruiterNoteTemplates[id]) return;
    var content = recruiterNoteTemplates[id].content;
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
        CKEDITOR.instances['recruiter_note'].setData(content);
    } else {
        $('#recruiter_note').val(content);
    }
});

// Add Candidate presentation only: card visibility, salary display tabs, and summaries.
(function () {
    'use strict';

    var root = document.querySelector('.job-candidate-create');
    if (!root) {
        return;
    }

    var noticePeriodSelect = root.querySelector('#serving_notice_period');
    var noticePeriodOptions = root.querySelectorAll('.candidate-notice-option');
    var noticeDateField = root.querySelector('#candidateNoticeDateField');
    var noticeDaysField = root.querySelector('#candidateNoticeDaysField');
    var noticeDateLabel = root.querySelector('#candidateNoticeDateLabel');
    var noticeDateInput = root.querySelector('#np_id');
    var noticeDateDisplay = root.querySelector('#np_id_display');
    var noticeDaysSelect = root.querySelector('#ddl_np_id');
    var previousNoticeMode = noticePeriodSelect ? String(noticePeriodSelect.value) : '0';

    function formatNoticeDate(value) {
        if (!value) {
            return '';
        }

        var parts = value.split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : value;
    }

    function syncNoticePeriodComponent(clearPreviousValue) {
        if (!noticePeriodSelect) {
            return;
        }

        var mode = String(noticePeriodSelect.value);
        var usesDate = mode === '1' || mode === '2';
        var today = new Date().toISOString().split('T')[0];

        if (clearPreviousValue && mode !== previousNoticeMode) {
            if (noticeDateInput) noticeDateInput.value = '';
            if (noticeDateDisplay) noticeDateDisplay.value = '';
            if (noticeDaysSelect) noticeDaysSelect.value = '';
        }

        noticePeriodOptions.forEach(function (option) {
            var selected = option.getAttribute('data-notice-period-value') === mode;
            option.classList.toggle('is-selected', selected);
            option.setAttribute('aria-checked', selected ? 'true' : 'false');
        });

        if (noticeDateField) noticeDateField.hidden = !usesDate;
        if (noticeDaysField) noticeDaysField.hidden = mode !== '0';
        if (noticeDateInput) {
            noticeDateInput.disabled = !usesDate;
            noticeDateInput.required = usesDate;
            noticeDateInput.min = mode === '1' ? today : '';
            noticeDateInput.max = mode === '2' ? today : '';
        }
        if (noticeDateDisplay) {
            noticeDateDisplay.disabled = !usesDate;
            noticeDateDisplay.placeholder = mode === '1'
                ? 'Select expected last working date'
                : 'Select last working date';
        }
        if (noticeDaysSelect) {
            noticeDaysSelect.disabled = mode !== '0';
            noticeDaysSelect.required = mode === '0';
        }
        if (noticeDateLabel) {
            noticeDateLabel.innerHTML = (mode === '1' ? 'Expected Last Working Date' : 'Last Working Date') + ' <span class="rq">*</span>';
        }

        previousNoticeMode = mode;
    }

    noticePeriodOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            if (!noticePeriodSelect) {
                return;
            }

            noticePeriodSelect.value = option.getAttribute('data-notice-period-value');
            noticePeriodSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    if (noticeDateDisplay && noticeDateInput) {
        noticeDateDisplay.addEventListener('click', function () {
            if (typeof noticeDateInput.showPicker === 'function') {
                noticeDateInput.showPicker();
            } else {
                noticeDateInput.click();
            }
        });
        noticeDateInput.addEventListener('change', function () {
            noticeDateDisplay.value = formatNoticeDate(noticeDateInput.value);
        });
    }

    if (noticePeriodSelect) {
        noticePeriodSelect.addEventListener('change', function () {
            syncNoticePeriodComponent(true);
        });
        syncNoticePeriodComponent(false);
    }
    var salaryFieldMap = {
        current: {
            annual: 'pay_annual',
            monthly: 'pay_monthly',
            hourly: 'ctc'
        },
        expected: {
            annual: 'bill_annual',
            monthly: 'bill_monthly',
            hourly: 'ectc'
        }
    };
    var salaryPeriodLabels = {
        annual: 'Annual',
        monthly: 'Monthly',
        hourly: 'Hourly'
    };
    var salaryHoursPerMonth = 160;
    var salaryMonthsPerYear = 12;
    var salaryCurrencySelect = root.querySelector('#current_ctc_cur');
    var salaryCurrencyMirrors = [
        root.querySelector('#current_ectc_cur'),
        root.querySelector('#current_th_cur')
    ];

    function fieldValue(id) {
        var field = root.querySelector('#' + id);
        var value = field ? parseFloat(field.value) : 0;
        return Number.isFinite(value) ? value : 0;
    }

    function currencyLabel() {
        if (!salaryCurrencySelect || salaryCurrencySelect.selectedIndex < 0) {
            return '';
        }

        return salaryCurrencySelect.options[salaryCurrencySelect.selectedIndex].text.trim();
    }

    function formatAmount(value) {
        return new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(value || 0);
    }

    function activeSalaryPeriod() {
        var activeButton = root.querySelector('.candidate-salary-tabs button.is-active[data-salary-group="current"]');
        return activeButton ? activeButton.getAttribute('data-salary-period') : 'annual';
    }

    function salaryValuesFromPeriod(period, amount) {
        var values = {
            annual: 0,
            monthly: 0,
            hourly: 0
        };

        if (period === 'annual') {
            values.annual = amount;
            values.monthly = amount / salaryMonthsPerYear;
            values.hourly = values.monthly / salaryHoursPerMonth;
        } else if (period === 'monthly') {
            values.monthly = amount;
            values.annual = amount * salaryMonthsPerYear;
            values.hourly = amount / salaryHoursPerMonth;
        } else {
            values.hourly = amount;
            values.monthly = amount * salaryHoursPerMonth;
            values.annual = values.monthly * salaryMonthsPerYear;
        }

        return values;
    }

    function updateSalaryAutoValues(group, activePeriod) {
        var activeAmount = fieldValue(salaryFieldMap[group][activePeriod]);
        var values = salaryValuesFromPeriod(activePeriod, activeAmount);
        var otherPeriods = ['annual', 'monthly', 'hourly'].filter(function (period) {
            return period !== activePeriod;
        });
        var prefix = group === 'current' ? 'candidate-current-' : 'candidate-expected-';
        var labelOne = root.querySelector('#' + prefix + 'auto-label-one');
        var labelTwo = root.querySelector('#' + prefix + 'auto-label-two');
        var outputOne = root.querySelector('#' + prefix + 'monthly-auto');
        var outputTwo = root.querySelector('#' + prefix + 'hourly-auto');

        if (labelOne) {
            labelOne.textContent = salaryPeriodLabels[otherPeriods[0]] + ' Salary';
        }
        if (labelTwo) {
            labelTwo.textContent = salaryPeriodLabels[otherPeriods[1]] + ' Salary';
        }
        if (outputOne) {
            outputOne.textContent = formatAmount(values[otherPeriods[0]]);
        }
        if (outputTwo) {
            outputTwo.textContent = formatAmount(values[otherPeriods[1]]);
        }
    }

    function updateSalarySummary() {
        var period = activeSalaryPeriod();
        var currentAmount = fieldValue(salaryFieldMap.current[period]);
        var expectedAmount = fieldValue(salaryFieldMap.expected[period]);
        var currentTarget = root.querySelector('#candidateSalaryCurrentSummary');
        var expectedTarget = root.querySelector('#candidateSalaryExpectedSummary');
        var currentLabel = root.querySelector('#candidateSalaryCurrentLabel');
        var expectedLabel = root.querySelector('#candidateSalaryExpectedLabel');
        var differenceOutput = root.querySelector('#candidateSalaryDifferenceSummary');
        var increaseOutput = root.querySelector('#candidateSalaryHikeSummary');
        var hikeInput = root.querySelector('#percentage_hike');
        var currency = currencyLabel();
        var difference = expectedAmount - currentAmount;
        var calculatedIncrease = currentAmount > 0 && expectedAmount > 0
            ? (difference / currentAmount) * 100
            : 0;

        if (currentTarget) {
            currentTarget.textContent = currency + ' ' + formatAmount(currentAmount);
        }
        if (expectedTarget) {
            expectedTarget.textContent = currency + ' ' + formatAmount(expectedAmount);
        }
        if (currentLabel) {
            currentLabel.textContent = 'Current Salary (' + salaryPeriodLabels[period] + ')';
        }
        if (expectedLabel) {
            expectedLabel.textContent = 'Expected Salary (' + salaryPeriodLabels[period] + ')';
        }
        if (differenceOutput) {
            differenceOutput.textContent = currency + ' ' + formatAmount(difference);
        }
        if (increaseOutput) {
            increaseOutput.textContent = formatAmount(calculatedIncrease) + '%';
        }
        if (hikeInput) {
            hikeInput.value = calculatedIncrease ? String(Math.round(calculatedIncrease * 100) / 100) : '0';
        }

        updateSalaryAutoValues('current', period);
        updateSalaryAutoValues('expected', period);
    }

    function syncSalaryPeriod(period) {
        var modeByPeriod = {
            annual: '0',
            monthly: '1',
            hourly: '2'
        };
        var normalizedPeriod = Object.prototype.hasOwnProperty.call(modeByPeriod, period) ? period : 'annual';

        root.querySelectorAll('.candidate-salary-tabs button').forEach(function (button) {
            var selected = button.getAttribute('data-salary-period') === normalizedPeriod;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        root.querySelectorAll('.candidate-salary-period').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-salary-period') === normalizedPeriod);
        });

        var currentMode = root.querySelector('#current_ctc_mode');
        var expectedMode = root.querySelector('#current_ectc_mode');
        if (currentMode) {
            currentMode.value = modeByPeriod[normalizedPeriod];
        }
        if (expectedMode) {
            expectedMode.value = modeByPeriod[normalizedPeriod];
        }

        var currentHint = root.querySelector('#candidateCurrentSalaryHint');
        var expectedHint = root.querySelector('#candidateExpectedSalaryHint');
        if (currentHint) {
            currentHint.textContent = 'Enter your current ' + normalizedPeriod + ' salary.';
        }
        if (expectedHint) {
            expectedHint.textContent = 'Enter your expected ' + normalizedPeriod + ' salary.';
        }

        updateSalarySummary();
    }

    function syncSalaryCurrency() {
        if (!salaryCurrencySelect) {
            return;
        }

        salaryCurrencyMirrors.forEach(function (select) {
            if (select) {
                select.value = salaryCurrencySelect.value;
            }
        });

        root.querySelectorAll('.changeTextCurrency').forEach(function (label) {
            label.textContent = currencyLabel();
        });

        updateSalarySummary();
    }

    root.querySelectorAll('.candidate-salary-tabs button[data-salary-period]').forEach(function (button) {
        button.addEventListener('click', function () {
            syncSalaryPeriod(button.getAttribute('data-salary-period'));
        });
    });

    [
        'pay_annual', 'pay_monthly', 'ctc',
        'bill_annual', 'bill_monthly', 'ectc'
    ].forEach(function (id) {
        var field = root.querySelector('#' + id);
        if (field) {
            field.addEventListener('input', function () {
                window.setTimeout(updateSalarySummary, 0);
            });
            field.addEventListener('change', function () {
                window.setTimeout(updateSalarySummary, 0);
            });
        }
    });
    if (salaryCurrencySelect) {
        salaryCurrencySelect.addEventListener('change', syncSalaryCurrency);
    }

    syncSalaryPeriod(activeSalaryPeriod());
    syncSalaryCurrency();
})();
</script>

<script>
// Render the existing numeric proficiency values as accessible five-star Select2 options.
$(function initializeCandidateSkillRatings() {
    function renderCandidateRating(option) {
        var value = Number.parseInt(option.id, 10) || 0;
        var wrapper = document.createElement('span');
        wrapper.className = 'candidate-rating-option';
        var stars = document.createElement('span');
        stars.className = 'candidate-rating-stars';

        for (var index = 1; index <= 5; index++) {
            var star = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            var starPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            star.setAttribute('viewBox', '0 0 24 24');
            star.setAttribute('focusable', 'false');
            star.setAttribute('aria-hidden', 'true');
            star.classList.add('candidate-rating-star');
            if (index <= value) {
                star.classList.add('is-filled');
            }
            starPath.setAttribute('d', 'M12 2.75 14.84 8.5l6.35.92-4.59 4.48 1.08 6.32L12 17.23l-5.68 2.99 1.08-6.32-4.59-4.48 6.35-.92L12 2.75Z');
            star.appendChild(starPath);
            stars.appendChild(star);
        }

        var label = document.createElement('span');
        label.className = value ? 'candidate-rating-value' : 'candidate-rating-placeholder';
        label.textContent = value ? value + ' ' + (value === 1 ? 'Star' : 'Stars') : 'Select rating';
        wrapper.append(stars, label);
        return $(wrapper);
    }

    if (!$.fn.select2) {
        return;
    }

    $('.job-candidate-create .candidate-rating-select').each(function () {
        var ratingSelect = $(this);
        if (ratingSelect.data('select2')) {
            ratingSelect.select2('destroy');
        }
        ratingSelect.select2({
            width: '100%',
            dropdownParent: $(document.body),
            dropdownCssClass: 'candidate-rating-dropdown',
            minimumResultsForSearch: Infinity,
            templateResult: renderCandidateRating,
            templateSelection: renderCandidateRating
        });
    });
});
</script>


<script>
(function initializeCandidateAvailabilityPickers() {
    'use strict';

    var dateFields = [
        { id: 'interviewDateOne', offset: 0 },
        { id: 'interviewDateTwo', offset: 1 },
        { id: 'interviewDateThree', offset: 2 }
    ];
    var timeFieldIds = ['interviewTimeOne', 'interviewTimeTwo', 'interviewTimeThree'];
    var timeFormat = @json($defaultTimeFormat == 1 ? 'h:i K' : 'H:i');

    function parseDisplayDate(value) {
        var match = String(value || '').match(/^(\d{2})-(\d{2})-(\d{4})$/);
        return match ? new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1])) : null;
    }

    function futureDate(offset) {
        var date = new Date();
        date.setHours(0, 0, 0, 0);
        date.setDate(date.getDate() + offset);
        return date;
    }

    function nativeDateValue(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    function nativeTimeValue(value) {
        var match = String(value || '').trim().match(/^(\d{1,2}):(\d{2})(?:\s*(AM|PM))?$/i);
        if (!match) {
            return '';
        }

        var hour = Number(match[1]);
        var suffix = String(match[3] || '').toUpperCase();
        if (suffix === 'PM' && hour < 12) {
            hour += 12;
        } else if (suffix === 'AM' && hour === 12) {
            hour = 0;
        }

        return String(hour).padStart(2, '0') + ':' + match[2];
    }

    if (typeof window.flatpickr === 'function') {
        timeFieldIds.forEach(function (id) {
            var field = document.getElementById(id);
            if (!field || field._flatpickr) {
                return;
            }

            window.flatpickr(field, {
                enableTime: true,
                noCalendar: true,
                dateFormat: timeFormat,
                time_24hr: timeFormat === 'H:i',
                minuteIncrement: 1,
                allowInput: true,
                clickOpens: true,
                disableMobile: true
            });
        });

        dateFields.forEach(function (config) {
            var field = document.getElementById(config.id);
            if (!field || field._flatpickr) {
                return;
            }

            var savedDate = parseDisplayDate(field.value);
            window.flatpickr(field, {
                dateFormat: 'd-m-Y',
                altInput: true,
                altFormat: 'd-M-Y',
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                defaultDate: savedDate || futureDate(config.offset),
                minDate: savedDate ? null : 'today',
                disable: savedDate ? [function (date) {
                    var today = futureDate(0);
                    return date < today && date.getTime() !== savedDate.getTime();
                }] : []
            });
        });

        return;
    }

    dateFields.forEach(function (config) {
        var field = document.getElementById(config.id);
        if (!field) {
            return;
        }

        var savedDate = parseDisplayDate(field.value);
        field.type = 'date';
        field.value = nativeDateValue(savedDate || futureDate(config.offset));
        if (!savedDate) {
            field.min = nativeDateValue(futureDate(0));
        }
    });

    timeFieldIds.forEach(function (id) {
        var field = document.getElementById(id);
        if (!field) {
            return;
        }

        var existingValue = nativeTimeValue(field.value);
        field.type = 'time';
        field.value = existingValue;
    });
})();
</script>
@endpush
