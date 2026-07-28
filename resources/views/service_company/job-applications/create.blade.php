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
@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">


  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
      @include('sections.service_company.toolbar',['id' => $jobDetail->id])
      <div class="d-flex align-items-center py-1">
          <div class="before-event d-flex align-items-center position-relative my-1 me-3">
            <!--begin::Buttons-->
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                  @php 
                      if($page_event == 'EDIT'){ 
                        echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update Candidate</button>';
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
                                    <svg class="up-info-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="9 12 11 14 16 9"/>
                                    </svg>
                                    <span><strong>Resume uploaded successfully!</strong></span>
                                </div>

                                <div class="up-file-card">
                                    <span class="up-file-icon">PDF</span>
                                    <div class="up-file-meta">
                                        <div class="up-file-name" id="upFileName">&nbsp;</div>
                                        <div class="up-file-sub" id="upFileSub">&nbsp;</div>
                                    </div>
                                    <span class="up-file-pill">
                                        <svg class="up-file-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                        Uploaded
                                    </span>
                                </div>

                                <div class="up-actions">
                                    <button type="button" id="upReplaceBtn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="23 4 23 10 17 10"/>
                                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                        </svg>
                                        Replace Resume
                                    </button>
                                    <button type="button" id="upRemoveBtn" class="up-remove">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>

                                <h4 class="up-details-h">Resume Details</h4>
                                <div class="up-details-grid">
                                    <div class="up-detail">
                                        <span class="up-detail-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div class="up-detail-lbl">Uploaded On</div>
                                            <div class="up-detail-val" id="upDetailUploadedOn">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="up-detail">
                                        <span class="up-detail-icon ok">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div class="up-detail-lbl">Parser Status</div>
                                            <div class="up-detail-val" id="upDetailParserStatus">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="up-detail">
                                        <span class="up-detail-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                                <line x1="8" y1="13" x2="16" y2="13"/>
                                                <line x1="8" y1="17" x2="16" y2="17"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div class="up-detail-lbl">File Type</div>
                                            <div class="up-detail-val" id="upDetailFileType">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="up-detail">
                                        <span class="up-detail-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div class="up-detail-lbl">Pages</div>
                                            <div class="up-detail-val" id="upDetailPages">&mdash;</div>
                                        </div>
                                    </div>
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
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.</span> </div>
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
            <div class="fs fv-row">
              <div class="fsh"><h3>Primary Skills *</h3></div>
              <div class="fsc">
              <div id="skillTags" class="skill-tags"></div>
               <input type="text" class="" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
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
                    <div class="fg fv-row">
                      <label>Reason for Job Change </label>
                      <input type="text" name="reason_for_job_change" maxlength="55" id="reason_for_job_change" value="{{ @$application->reason_for_job_change }}" placeholder="Enter current company" />
                    </div>                   
                </div>
              </div>
            </div>

            <!-- Salary Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Salary Details</h3>
              </div>
              <div class="fsc">
                <div class="sg">
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Annual Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color:var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
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


                      <select id="current_ctc_cur" name="current_ctc_cur">
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
                    <div class="fg fv-row"><label>Current CTC (Annual) <span  style="float: right; color: #ef560a;"id="pay_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payrateannual"></span>
                    </div>
                    <div class="fg fv-row"><label>Expected CTC (Annual) <span style="float: right; color: #ef560a;" id="bill_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billrateannual"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Monthly Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="current_ectc_cur" id="current_ectc_cur">
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
                    <div class="fg fv-row"><label>Current (Monthly) <span style="float: right; color: #ef560a;" id="pay_rate_monthly_usd"></span></label>
                      <div class="ig">
                        <input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payratemonthly"></span>
                    </div>
                    <div class="fg fv-row"><label>Expected (Monthly) <span style="float: right; color: #ef560a;" id="bill_rate_monthly_usd"> </span></label>
                      <div class="ig"><input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billratemonthly"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row"><label style="color: var(--p)">Hourly Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="current_th_cur" id="current_th_cur">
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
                    <div class="fg fv-row"><label>Current (Hourly) <span style="float: right; color: #ef560a;" id="pay_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="ctc" id="ctc" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted CurrentCTC"></span> 
                    </div>
                    <div class="fg fv-row"><label>Expected (Hourly) <span  style="float: right; color: #ef560a;"id="bill_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="ectc" id="ectc" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted expectedctc"></span> 
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                          <label style="color: var(--p)">Other Salary Details</label>
                        <div style="display: flex;">
                            <div class="">
                              <label>Hike Percentage</label>
                              <div class="ig">
                                <input type="number" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" style="text-align: center" />
                                <span class="ig-a">%</span>
                              </div>
                              <div style="font-size: 0.95rem; color: var(--t-l); margin-top: 3px;">(Calculated from Annual CTC)</div>
                            </div>
                            <div class="" style="margin-left: 10px;">
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
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($jobSkill as $skill_val)
                        @if(!empty($skill_val->skill_name))

                            @php
                                  if($skill_val->skill_value){
                                    $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                    // dd($skillValue);
                                    if(!is_null($value) && !empty($value)){
                                      $total_skill_exp =  GeneralMethod::separateNumberParts($skillValue);
                                      $skill_exp_years = ($total_skill_exp[0]);
                                      $skill_exp_months = ($total_skill_exp[1]); 

                                      $selected_skill_exp_year = $skill_exp_years;
                                      $selected_skill_exp_month = $skill_exp_months;
                                      $selected_skill_rating = $skill_val->rating;
                                    }else{
                                      $selected_skill_exp_year = null;
                                      $selected_skill_exp_month =null;
                                      $selected_skill_rating = null;
                                    }
                                    //  dd($skillValue,$skill_val->skill_value);
                                  }else{
                                    $selected_skill_exp_year = null;
                                    $selected_skill_exp_month =null;
                                    $selected_skill_rating = null;
                                  }

                            @endphp

                            <tr>
                              <td><input type="text" value="{{ strtoupper($skill_val->skill_name) }}" placeholder="Enter skill" readonly /></td>
                              <td>
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
                              </td>
                              <td>
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
                              </td>
                              <td>
                                 <select class="form-select form-select-solid" arial-label="Select rating" id="rating" name="rating[{{ $skill_val->id }}]">
                                      <option value="0">Select rating</option>
                                      @php 
                                          for ($i = 1; $i <= 5; $i++) {
                                               $selected = '';
                                                $s = 'Star';
                                                if($i > 1){
                                                    $s = 'Stars';
                                                }
                                                if(is_null($selected_skill_rating)){
                                                $selected = '';
                                                }else{
                                                  if($i==$selected_skill_rating){
                                                    $selected =  'selected';
                                                }
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

             <!-- Skills Details -->
          @if(!empty($job_additional_detail))
            <div class="fs">
              <div class="fsh">
                <h3>Job Questions</h3>
              </div>
              <div class="fsc">

                <!--begin::Custom Details-->
                <div class="" id="job_additional_detail">
                  @php 
                    if(!empty($job_additional_detail)){
                    
                    $customTitleId	=	1;
                    $html			=	'';
                   
                    $inc_first		=	0;
                    $inc_last		=	1;
                     $html	.=	'<div class="form-group row mb-10">';	
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
              </div>
            </div>
            @endif


            <!-- Availability -->
            <div class="fs">
              <div class="fsh">
                <h3>Availability for Next 3 Days
                  <span style="color: var(--r)">*</span>
                </h3>
              </div>
              <div class="fsc">
                <div class="ag">
                  <div class="ai">
                            <div class="ad">Date and Time(Slot-1)</div>
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

                          <div class="ai">
                            <div class="ad">Date and Time(Slot-2)</div>
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

                          <div class="ai">
                              <div class="ad">Date and Time(Slot-3)</div>
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

        // Server is `max:6048` KB = 6 MB. Keep the JS check in sync.
        if (sizeMB > 6) {
            alert('Maximum file size is 6 MB.');
            return;
        }

        // Show human-readable size: KB for small files (avoids "0.07 MB" display)
        const sizeStr = sizeMB >= 1
            ? sizeMB.toFixed(2) + ' MB'
            : Math.round(file.size / 1024) + ' KB';

        $('#fileName').text(file.name + ' (' + sizeStr + ')');

        $('#fileInfo').css('display', 'flex');

        $('#resumeCheck').val('1');

        $('.download-file').addClass('d-none');

        // Drive the real-time XHR progress bar first; the legacy pipeline
        // (hFIC) uploads the same blob in parallel. Setting this flag tells
        // hFIC to skip its jQuery POST — keeping same-origin POSTs to one
        // for the file, so the parser POST `/extract-resume-applications`
        // is never queued behind an upload on the HTTP/1.1 6-conn cap.
        window.__upXhrChannelClaimed = true;
        if (typeof uploadWithProgress === 'function') {
            uploadWithProgress(file);
        }
        if (typeof handleFileInputChange === 'function' && event) {
            handleFileInputChange(event);
        }
        // Reset so the legacy channel is open for the *next* pick.
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
        resetProgress();
        $('#upInfo').removeClass('show');
        $('#uploadBox').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
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
        $info.find('.up-file-icon').text(ext);
        $('#upFileName').text(file.name);
        $('#upFileSub').text(fmtSize(file.size) + ' &middot; ' + ext);

        var now = new Date();
        var day = String(now.getDate()).padStart(2, '0');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var month = months[now.getMonth()];
        var year = now.getFullYear();
        var hh = String(now.getHours()).padStart(2, '0');
        var mm = String(now.getMinutes()).padStart(2, '0');
        $('#upDetailUploadedOn').text(day + ' ' + month + ' ' + year + ', ' + hh + ':' + mm);

        $('#upDetailParserStatus').text('Pending');
        $('#upDetailFileType').text(ext);
        $('#upDetailPages').html('&mdash;');

        if (parserResponse) {
            $('#upDetailParserStatus').text('Completed');
            if (typeof parserResponse.pages === 'number' && parserResponse.pages > 0) {
                $('#upDetailPages').text(parserResponse.pages);
            }
        }

        // Hide the dropzone + file-info strip and show the new card.
        $('#uploadBox').hide();
        $('#fileInfo').hide();
        $info.addClass('show');
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
        resetProgress();
        window.__upXhrChannelClaimed = true;

        $('#upWrap').show();
        $('#upStats').text(
            '0% &middot; ' + fmtKB(0) + ' / ' + fmtKB(file.size)
        );

        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '{{ csrf_token() }}');

        window.__upLoc = { file: file };

        upXhr = new XMLHttpRequest();
        upXhr.open('POST', '{{ route("service_company.job-applications.uploadresume") }}', true);

        upXhr.upload.onprogress = function (e) {
            if (!e.lengthComputable) return;
            const pct = Math.round(e.loaded * 100 / e.total);
            $('#upBar').css('width', pct + '%');
            $('#upStats').text(
                pct + '% &middot; ' + fmtKB(e.loaded) + ' / ' + fmtKB(e.total)
            );
        };
        upXhr.onload = function () {
            let r = null;
            try { r = JSON.parse(upXhr.responseText); } catch (_) { r = null; }
            if (upXhr.status >= 200 && upXhr.status < 300 && r && r.success) {
                $('#upBar').css('width', '100%');
                $('#upStats').text(
                    '100% &middot; ' + fmtKB(file.size) + ' / ' + fmtKB(file.size)
                );
                $('#upPill').css('display', 'inline-flex');
                const $b = $('#upBanner');
                $b.removeClass('err').addClass('ok')
                    .html(
                        '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                        + '<circle cx="12" cy="12" r="10"/>'
                        + '<polyline points="9 12 11 14 16 9"/>'
                        + '</svg>'
                        + '<span><strong>Upload complete!</strong> '
                        + 'Your resume has been uploaded successfully.</span>'
                    ).show();
                // Set the hidden input the controller's `clientappstore`
                // reads from session path. We do NOT clear #resume —
                // `hasFile('resume')` is the trigger that fires the
                // session-based UploadResumeToTestRTwo dispatch.
                $('#uploaded_file_path').val(r.file_path || '');

                // Swap to the Resume Information card immediately.
                showUploadedCard(file, null);
            } else {
                const msg = (r && r.message) ? r.message : upXhr.statusText;
                $('#upBanner')
                    .removeClass('ok').addClass('err')
                    .html(
                        '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                        + '<circle cx="12" cy="12" r="10"/>'
                        + '<line x1="15" y1="9" x2="9" y2="15"/>'
                        + '<line x1="9" y1="9" x2="15" y2="15"/>'
                        + '</svg>'
                        + '<span><strong>Upload failed:</strong> ' + msg + '</span>'
                    ).show();
            }
            upXhr = null;
        };

        upXhr.onerror = function () {
            $('#upBanner')
                .removeClass('ok').addClass('err')
                .html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/>'
                    + '<line x1="15" y1="9" x2="9" y2="15"/>'
                    + '<line x1="9" y1="9" x2="15" y2="15"/>'
                    + '</svg>'
                    + '<span><strong>Network error</strong> during upload. '
                    + 'Click Remove and try again.</span>'
                ).show();
            upXhr = null;
        };

        upXhr.send(fd);
    }

    // Replace / Remove buttons inside the post-upload Resume Information card.
    // Replace = reset state and reopen the OS file picker.
    // Remove  = reset state and stay on the empty dropzone.
    // NOTE: `resumeInput.click()` MUST run synchronously inside this
    // user-gesture handler. Wrapping it in setTimeout() / Promise
    // .then() breaks the user-gesture chain and modern Chrome silently
    // refuses to open the file picker for a programmatic .click().
    $(document).on('click', '#upReplaceBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        backToDropzone();
        resumeInput.click();
    });

    $(document).on('click', '#upRemoveBtn', function (e) {
        e.preventDefault();
        backToDropzone();
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

        if (typeof resetProgress === 'function') {
            resetProgress();
        }

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



          function clearAll() {
            timeOnePicker.clear();
            timeTwoPicker.clear();
            timeThreePicker.clear();

            dateOnePicker.clear();
            dateTwoPicker.clear();
            dateThreePicker.clear();
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
                                if(response.status == 'notauthorized'){
                                   $("#update-form").html(`Update`);
                                   submitButton.removeAttribute('data-kt-indicator');
                                   submitButton.disabled = false;
                                   Swal.fire("Not Authorized", response.message, "warning");
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
															error			: function (xhr, textStatus, errorThrown) {
                                                                            $("#update-form").prop("disabled", false);
                                                                            $("#update-form").html(`Update`);
                                                                            submitButton.removeAttribute('data-kt-indicator');
                                                                            submitButton.disabled = false;
                                                                            console.error('Update AJAX error:', textStatus, errorThrown, xhr.responseText);
                                                                            // Try to extract a useful server-side message; fall back to the bare status.
                                                                            var serverMsg = '';
                                                                            try {
                                                                                var body = xhr.responseText || '';
                                                                                var jsonStart = body.indexOf('{');
                                                                                if (jsonStart >= 0) {
                                                                                    var parsed = JSON.parse(body.substring(jsonStart));
                                                                                    serverMsg = parsed.message || parsed.error || '';
                                                                                }
                                                                            } catch (e) {}
                                                                            var display = serverMsg
                                                                                ? serverMsg
                                                                                : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                                                                            Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
																			},
                                                            complete       : function () {
                                                                            // Safety net: if the success handler didn't fire (e.g. session timeout, 5xx, or an exception
                                                                            // thrown mid-redirect), make sure the button doesn't stay stuck on the spinner.
                                                                            // We re-enable the button only if the page is still here (success handler that
                                                                            // triggered window.location would have started navigation by now).
                                                                            if ($("#update-form").length) {
                                                                                $("#update-form").prop("disabled", false);
                                                                                $("#update-form").html(`Update`);
                                                                                submitButton.removeAttribute('data-kt-indicator');
                                                                                submitButton.disabled = false;
                                                                            }
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
        validate: function(tagData) {
            // Prevent empty tags after cleaning
            return tagData.value.trim().length > 0 ? true : false;
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                const value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
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
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                const value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
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

   document.addEventListener('click', function (e) {
            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {

                // get tag element
                const tagElm = e.target.closest('.tagify__tag');

                // remove tag from tagify
                tagifyskill.removeTags(tagElm);
                tagifylocation.removeTags(tagElm);
            }
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
        // document.getElementById('np_id_display').value = datess;
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
															error			: function (xhr, textStatus, errorThrown) {
                                $("#save-form").prop("disabled", false);
                                $("#save-form").html(`Submit`);
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
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
                                var display = serverMsg
                                    ? serverMsg
                                    : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                                Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
																			},
                                                            complete       : function () {
                                                                        // Safety net: if the success handler didn't fire, make sure
                                                                        // the button doesn't stay stuck on the spinner.
                                                                        if ($("#save-form").length) {
                                                                            $("#save-form").prop("disabled", false);
                                                                            $("#save-form").html(`Submit`);
                                                                            submitButton.removeAttribute('data-kt-indicator');
                                                                            submitButton.disabled = false;
                                                                        }
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
                      $('#careersummary').val(response.careerSummary);
                      $('#phone').val(response.number);
                      $('#email').val(response.email);
                      $('#dob').val(response.dob);
                      $('#current_company').val(response.currentjob);
                      $('#qualification').val(response.qualification).change();

                      tagifyskill.removeAllTags(); // Clear existing tags first
                      tagifyskill.addTags(response.skills); // Add new tags
                      tagifylocation.removeAllTags(); // Clear existing tags first
                      tagifylocation.addTags(response.country); // Add new tags

                      // Refresh the post-upload Resume Information card
                      // with the parser's per-file metadata. Skills count +
                      // Parser Status flip from defaults to actual values.
                      if (window.__upLoc && window.__upLoc.file) {
                          try { showUploadedCard(window.__upLoc.file, response); } catch (e) {}
                      }

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
      if (!file) {
          return;
      }

      // uploadWithProgress() already POSTed this blob to /uploadresume
      // with real progress feedback. Skip the redundant jQuery POST.
      // The PDFjs / mammoth parse path below still runs so Career
      // Summary fills in.
      var __skipUpload = !!window.__upXhrChannelClaimed;

      if (!__skipUpload) {
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

            if(response.success){

                $('#resume_path').val(response.file_path);

                toastr.success('Resume uploaded successfully');

            } else {

                toastr.error(response.message || 'CV upload failed. Please try again.');

            }

        },
        error: function (xhr) {

            var msg = 'CV upload failed. Please try again.';
            try {
                var res = JSON.parse(xhr.responseText);
                if(res.message) msg = res.message;
                else if(res.errors && res.errors.file) msg = res.errors.file[0];
            } catch(e){}
            toastr.error(msg);

        },
        complete: function () {

            $('#fileUploadLoader').hide();

        }
    });
      } // end if (!__skipUpload)



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
    // document.getElementById('resume').addEventListener('change', handleFileInputChange);

    
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