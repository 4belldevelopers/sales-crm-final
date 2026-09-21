@extends('layouts.service_company_layout')
@php 
	use App\Helper\GeneralMethod;	
	$customTitleId	=	0;
@endphp
@push('head-script')
@include('service_company.bench-candidates.partials._form_styles')
@endpush

@section('content')

@php 	
	$country_code =  GeneralMethod::countryCodeFun();
  $currency_code =  GeneralMethod::defaultCurrency();

  $phone_code = $country_code;
  $second_phone_code = $country_code;
  $url = '';
  $account_company_name = @$company_name ?: '';

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
          $resume_url			=	route('download.resume', ['id' => $application->id,'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]);
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
$snp_mode = (int) (@$application->serving_notice_period_mode ?: 2);
if($snp_mode== 1){
  $snp_one		=	'checked="checked"';
  $snp_text_one	=	'display:flex'; 
  $snp_text_dummy = 'Available From';   
  $snp_last_working = @$application->np_id;                                  	
}else if($snp_mode== 2){
  $snp_two	=	'checked="checked"';
  $snp_text_one	=	'display:flex';
  $snp_text_dummy = 'Immediate';  
  $snp_last_working = @$application->last_working_date; 
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
<div class="toolbar talent-top-toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid">
    <div class="talent-topbar">
      <div class="talent-topbar-copy">
        <div class="talent-topbar-title-row">
          <h1>Add In-House Talent</h1>
          <span class="talent-topbar-badge"><i class="fa-solid fa-briefcase"></i> In-House Talent</span>
        </div>
        <p>Add internal talent to the marketplace and make them discoverable for client opportunities.</p>
      </div>
      <div class="talent-topbar-actions">
        @php 
            if(@$page_event == 'EDIT'){ 
              echo '<button type="button" name="edit-form" id="edit-form" value="update" class="talent-topbar-primary">Update Candidate <i class="fa-solid fa-arrow-right"></i></button>';
            }else{
              echo '<button type="button" name="save" id="save-form" value="save" class="talent-topbar-primary talent-stage-action--bench"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> <span class="talent-stage-action-label">Publish to Talent Marketplace</span></button>';
            }
        @endphp
      </div>
    </div>
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
            <input type="hidden" name="resume_session_uuid" id="resume_session_uuid" value="">
          <div class="fc">
            @if(@$page_event != 'EDIT')
            {{-- The selected stage is submitted with the existing candidate form; placement requires its separate client workflow. --}}
            <section class="fs talent-stage-card" aria-labelledby="talent-stage-title">
              <div class="talent-stage-heading">
                <strong id="talent-stage-title">Talent Stage <span class="rq">*</span></strong>
                <small>Select this talent's current stage to reflect their availability.</small>
              </div>
              <div class="talent-stage-options">
                <label class="talent-stage-option talent-stage-option--pool">
                  <input type="radio" name="talent_stage" value="1">
                  <span class="talent-stage-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Pool</strong><small>In your internal talent pool, not yet available.</small></span>
                  <i class="fa-solid fa-circle-check talent-stage-check" aria-hidden="true"></i>
                </label>
                <label class="talent-stage-option talent-stage-option--bench is-selected">
                  <input type="radio" name="talent_stage" value="2" checked>
                  <span class="talent-stage-icon"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Bench</strong><small>Available for client opportunities.</small></span>
                  <i class="fa-solid fa-circle-check talent-stage-check" aria-hidden="true"></i>
                </label>
                <label class="talent-stage-option talent-stage-option--hold">
                  <input type="radio" name="talent_stage" value="4">
                  <span class="talent-stage-icon"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Hold</strong><small>Temporarily unavailable for opportunities.</small></span>
                  <i class="fa-solid fa-circle-check talent-stage-check" aria-hidden="true"></i>
                </label>
                <button type="button" class="talent-stage-option talent-stage-option--placed" id="talent-stage-placed" aria-label="Placed: requires a client placement">
                  <span class="talent-stage-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Placed</strong><small>Assigned to a client through placement.</small></span>
                </button>
                <label class="talent-stage-option talent-stage-option--exited">
                  <input type="radio" name="talent_stage" value="5">
                  <span class="talent-stage-icon"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Exited</strong><small>No longer in your talent inventory.</small></span>
                  <i class="fa-solid fa-circle-check talent-stage-check" aria-hidden="true"></i>
                </label>
              </div>
            </section>
            @endif
            <!-- Upload + Career Summary -->
            <div class="g2 resume-summary-grid" style="margin-bottom:16px">
                <!-- Resume Upload -->
                <div class="fs fv-row resume-card" style="margin-bottom:0">
                    <div class="resume-card-header">
                        <span class="resume-card-icon"><i class="fa-solid fa-file-lines"></i></span>
                        <span class="resume-card-title">
                            <strong>Upload Resume <span class="rq">*</span></strong>
                            <small>Upload the resume and we'll automatically extract the talent details.</small>
                        </span>
                    </div>

                    <div class="fsc resume-card-body">
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

                            <button class="ub" type="button" id="chooseFileBtn">Choose File</button>

                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display:none;">

                            <div class="uf">Supports PDF, DOC, DOCX (Max 6 MB)</div>

                            <!-- Progress bar -->
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

                            <!-- Selected File strip -->
                            @php
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
                                <a href="{{ $resume_url }}" target="_blank" class="download-file"
                                   style="color:#fff;margin-left:250px;display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-download" aria-hidden="true" style="color:#fff"></i>
                                </a>
                                @endif
                                <button type="button" class="remove-file" id="removeFile">Remove</button>
                            </div>

                        </div>
                        <div class="resume-note marketplace-privacy">
                            <span class="resume-note-icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                            <div class="marketplace-privacy-content">
                                <strong>Marketplace Privacy Check</strong>
                                <small>Before uploading, remove sensitive details from the resume. Resumes with these details cannot be published.</small>
                                <ul class="marketplace-privacy-list" aria-label="Details to remove">
                                    <li><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Phone numbers</li>
                                    <li><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Email addresses</li>
                                    <li><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Current company name</li>
                                    <li><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Company logos</li>
                                </ul>
                            </div>
                        </div>

                        <!-- After-upload Resume Information card -->
                        <div class="up-info" id="upInfo">
                            <div class="up-info-banner">
                                <svg class="up-info-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/>
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
                                    <span class="up-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                                    <div><div class="up-detail-lbl">Uploaded On</div><div class="up-detail-val" id="upDetailUploadedOn">&nbsp;</div></div>
                                </div>
                                <div class="up-detail">
                                    <span class="up-detail-icon ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <div><div class="up-detail-lbl">Parser Status</div><div class="up-detail-val" id="upDetailParserStatus">&nbsp;</div></div>
                                </div>
                                <div class="up-detail">
                                    <span class="up-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                                    <div><div class="up-detail-lbl">File Type</div><div class="up-detail-val" id="upDetailFileType">&nbsp;</div></div>
                                </div>
                                <div class="up-detail" id="upRetryWrap" style="display:none">
                                    <button type="button" id="upRetryBtn" style="font-size:12px;padding:4px 12px;border:1px solid #f44336;color:#f44336;background:transparent;border-radius:4px;cursor:pointer;">Retry Parse</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fs fv-row summary-card" style="margin-bottom:0">
                  <div class="resume-card-header summary-card-header">
                    <div class="summary-title-wrap">
                      <span class="resume-card-icon"><i class="fa-solid fa-file-lines"></i></span>
                      <span class="resume-card-title">
                        <strong>Professional Summary <span class="rq">*</span></strong>
                        <small>A concise summary of this talent's expertise, experience and value to clients.</small>
                      </span>
                    </div>
                  </div>
                  <div class="fsc summary-card-body">
                    <textarea placeholder="Enter professional summary..." id="careersummary" name="careersummary" rows="10">{{ @$application->careersummary }}</textarea>
                    <div class="cc summary-counter">0 / 500</div>
                    <div class="summary-tip">
                      <span class="summary-tip-icon"><i class="fa-solid fa-lightbulb"></i></span>
                      <div>
                        <strong>Tips for a great summary:</strong>
                        <ul>
                          <li>Highlight key skills, years of experience and major achievements.</li>
                          <li>Keep it concise and client-facing.</li>
                        </ul>
                      </div>
                    </div>
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

            <!-- Basic Details -->
            <div class="fs bd-card">
              <div class="bd-hero">
                <div class="bd-hero-title">
                  <span class="bd-hero-icon"><i class="fa-regular fa-user"></i></span>
                  <span>
                    <strong>Candidate Profile</strong>
                    <small>Basic information to identify and contact this internal talent.</small>
                  </span>
                </div>
                <span class="bd-talent-badge"><i class="fa-regular fa-building"></i> In-House Talent</span>
              </div>
              <div class="fsc bd-body">
                <div class="bd-grid bd-grid--three">
                  <div class="fg fv-row bd-field">
                    <label>Full Name <span class="rq">*</span></label>
                    <div class="bd-control">
                      <i class="fa-regular fa-user"></i>
                      <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}" placeholder="Enter full name" />
                    </div>
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Professional Email <span class="rq">*</span></label>
                    <div class="bd-control">
                      <i class="fa-regular fa-envelope"></i>
                      <input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email address" />
                    </div>
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Mobile Number <span class="rq">*</span></label>
                    <div class="bd-phone-control">
                      <i class="fa-solid fa-mobile-screen-button"></i>
                      <input type="tel" name="phone" id="phone" value="{{ @$application->phone }}" placeholder="Enter mobile number" inputmode="tel" autocomplete="tel" />
                      <input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code">
                    </div>
                  </div>
                </div>

                <div class="bd-grid bd-grid--three">
                  <div class="fg fv-row bd-field">
                    <label>Current Location <span class="rq">*</span></label>
                    <div class="bd-control bd-control--tagify">
                      <i class="fa-solid fa-location-dot"></i>
                      <input type="text" class="" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Search city, state or country" />
                    </div>
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Highest Qualification <span class="rq">*</span></label>
                    <div class="bd-control bd-control--select">
                      <i class="fa-solid fa-graduation-cap"></i>
                      <select name="qualification" id="qualification">
                        <option value="">Select qualification</option>
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
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Nationality</label>
                    <div class="bd-control bd-control--select">
                      <i class="fa-regular fa-flag"></i>
                      <select name="nationality" id="nationality">
                        <option value="">Select nationality</option>
                        <option {{ (@$application->nationality==='Indian') ? 'selected' : '' }}>Indian</option>
                        <option {{ (@$application->nationality==='American') ? 'selected' : '' }}>American</option>
                        <option {{ (@$application->nationality==='British') ? 'selected' : '' }}>British</option>
                        <option {{ (@$application->nationality==='Other') ? 'selected' : '' }}>Other</option>
                      </select>
                    </div>
                  </div>
                </div>

                @php
                  $hasSecondaryContact = !empty(@$application->secondrary_email) || !empty(@$application->secondary_mobile);
                @endphp
                <details class="bd-secondary" {{ $hasSecondaryContact ? 'open' : '' }}>
                  <summary><i class="fa-solid fa-plus"></i> Add Secondary Contact (Optional)</summary>
                  <div class="bd-grid bd-grid--two bd-secondary-grid">
                    <div class="fg fv-row bd-field">
                      <label>Secondary Email</label>
                      <div class="bd-control">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="secondrary_email" id="secondrary_email" value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" />
                      </div>
                    </div>
                    <div class="fg fv-row bd-field">
                      <label>Secondary Number</label>
                      <div class="bd-phone-control">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <input type="tel" name="secondary_mobile" id="secondary_mobile" value="{{ @$application->secondary_mobile }}" placeholder="Enter secondary number" inputmode="tel" autocomplete="tel" />
                        <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code">
                      </div>
                    </div>
                  </div>
                </details>

                <details class="bd-extra" open>
                  <summary>
                    <span class="bd-extra-title">
                      <span class="bd-extra-icon"><i class="fa-solid fa-ellipsis"></i></span>
                      <span>
                        <strong>Additional Personal Information</strong>
                        <small>These details are optional and can be added if required.</small>
                      </span>
                    </span>
                    <span class="bd-extra-action">Show Details <i class="fa-solid fa-chevron-down"></i></span>
                  </summary>
                  <div class="bd-grid bd-grid--two bd-extra-grid">
                    <div class="fg fv-row bd-field">
                      <label>Gender</label>
                      <div class="bd-control bd-control--select">
                        <i class="fa-regular fa-user"></i>
                        <select name="gender" id="gender">
                          <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option>
                          <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                          <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                        </select>
                      </div>
                    </div>
                    <div class="fg fv-row bd-field">
                      <label>Date of Birth</label>
                      <div class="bd-control">
                        <i class="fa-regular fa-calendar-days"></i>
                        <input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()" placeholder="Select date" />
                      </div>
                    </div>
                  </div>
                </details>
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs fv-row d-none">
              <div class="fsh"><h3>Primary Skills *</h3></div>
              <div class="fsc">
               <div id="skillTags" class="skill-tags"></div>
                <input type="hidden" value="{{ @$application->skills }}" id="skills" />
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs pd-card">
              <div class="pd-hero">
                <div class="pd-hero-title">
                  <span class="pd-hero-icon"><i class="fa-solid fa-briefcase"></i></span>
                  <span>
                    <strong>Professional Profile</strong>
                    <small>Define the professional experience and role for this talent. This helps position them correctly in the marketplace.</small>
                  </span>
                </div>
                <span class="pd-talent-badge"><i class="fa-regular fa-building"></i> In-House Talent</span>
              </div>
              <div class="fsc pd-body">
                <div class="pd-info-strip">
                  <i class="fa-regular fa-circle-info"></i>
                  <span>Some details may be pre-filled from your internal employee records. Please review and update if needed.</span>
                </div>

                <div class="pd-field-grid pd-field-grid--two">
                  <div class="fg fv-row pd-field">
                    <label>Current Company <span class="rq">*</span></label>
                    <div class="pd-control">
                      <i class="fa-regular fa-building"></i>
                      <input type="text" name="current_company" id="current_company" value="{{ @$application->current_company ?: $account_company_name }}" placeholder="Enter current company" />
                    </div>
                    <small>Auto-filled from employee records</small>
                  </div>
                  <div class="fg fv-row pd-field">
                    <label>Current Designation <span class="rq">*</span></label>
                    <div class="pd-control">
                      <i class="fa-solid fa-briefcase"></i>
                      <input type="text" name="current_designation" id="current_designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}" placeholder="Enter current designation" />
                    </div>
                  </div>
                </div>

                <div class="pd-field-grid pd-field-grid--two pd-field-grid--employee-row">
                  <div class="fg fv-row pd-field">
                    <label>Employment Type <span class="rq">*</span></label>
                    <div class="pd-control pd-control--select">
                      <i class="fa-solid fa-users"></i>
                      <select name="employmentType" id="EmploymentType">
                        <option value="">Select employment type</option>
                        <option {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Full Time</option>
                        <option {{ (@$application->employmentType==='Contract') ? 'selected' : '' }}>Contract</option>
                        <option {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part Time</option>
                        <option {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance</option>
                      </select>
                    </div>
                  </div>
                  <div class="fg fv-row d-none">
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
                        <option value="2" {{ (@$application->comission_type==2 || @$application->comission_type=='') ? 'selected' : '' }}>Paid Service</option>
                      </select>
                  </div>
                  <div class="fg fv-row pd-field">
                    <label>Employee ID <span>(Optional)</span></label>
                    <div class="pd-control">
                      <i class="fa-regular fa-id-card"></i>
                      <input type="email" class="form-control form-control-solid" name="employee_number" id="employee_number" placeholder="Enter employee number" value="{{ @$application->employee_number }}">
                    </div>
                    <small>Internal employee identifier</small>
                  </div>
                </div>

              </div>
            </div>

            <div class="fs pd-experience-section-card">
              <div class="pd-experience-hero">
                <div class="pd-experience-hero-title">
                  <span class="pd-experience-hero-icon"><i class="fa-solid fa-chart-line"></i></span>
                  <span>
                    <strong>Experience Details</strong>
                    <small>Capture total and relevant experience for marketplace positioning.</small>
                  </span>
                </div>
                <span class="pd-experience-badge"><i class="fa-regular fa-building"></i> In-House Talent</span>
              </div>
              <div class="fsc pd-experience-body">
                <div class="pd-experience-grid">
                  <div class="fg fv-row pd-exp-card">
                    <div class="pd-exp-head">
                      <span><i class="fa-regular fa-clock"></i></span>
                      <strong>Total Experience <span class="rq rq-star" aria-hidden="true"><i class="fa-solid fa-star"></i></span></strong>
                    </div>
                    <div class="ei pd-exp-inputs">
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

                  <div class="fg fv-row pd-exp-card pd-exp-card--blue">
                    <div class="pd-exp-head">
                      <span><i class="fa-regular fa-file-lines"></i></span>
                      <strong>Relevant Experience <span class="rq rq-star" aria-hidden="true"><i class="fa-solid fa-star"></i></span></strong>
                    </div>
                    <div class="ei pd-exp-inputs">
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
                    <small>Relevant experience in the recommended/primary skills.</small>
                  </div>

                  <div class="pd-tip-card">
                    <span class="pd-tip-icon"><i class="fa-regular fa-lightbulb"></i></span>
                    <div>
                      <strong>Tip</strong>
                      <p>Relevant experience should match the skills you want to position this talent for in the marketplace.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="fs aw-card {{ ($snp_mode===1) ? 'is-date-availability' : 'is-immediate-availability' }}">
              <div class="aw-hero">
                <div class="aw-hero-title">
                  <span class="aw-hero-icon"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></span>
                  <span>
                    <strong>Availability &amp; Work Preferences</strong>
                    <small>Define the availability and work preferences for this talent.</small>
                  </span>
                </div>
                <span class="aw-talent-badge"><i class="fa-solid fa-house" aria-hidden="true"></i> In-House Talent</span>
              </div>
              <div class="fsc aw-body">
                <div class="aw-grid">
                  <div class="fg fv-row aw-field aw-field--wide">
                    <label>Availability <span class="rq">*</span></label>
                    <small class="aw-field-help">Select when this talent can start.</small>
                    <select name="serving_notice_period" id="serving_notice_period" class="aw-native-select">
                      <option value="0" {{ ($snp_mode==0) ? 'selected' : '' }} hidden></option>
                      <option value="1" {{ ($snp_mode===1) ? 'selected' : '' }}>Available From Date</option>
                      <option value="2" {{ ($snp_mode===2) ? 'selected' : '' }}>Immediate Joiner</option>
                    </select>
                    <div class="aw-option-grid" data-aw-options>
                      <button type="button" class="aw-option aw-option--now {{ ($snp_mode===2) ? 'is-selected' : '' }}" data-snp-value="2" aria-pressed="{{ ($snp_mode===2) ? 'true' : 'false' }}">
                        <span class="aw-radio"></span>
                        <span class="aw-option-icon aw-option-icon--now"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
                        <span class="aw-option-copy">
                          <strong>Immediate Joiner</strong>
                          <small>This talent can start immediately.</small>
                        </span>
                        <span class="aw-recommended">Recommended</span>
                      </button>
                      <button type="button" class="aw-option aw-option--date {{ ($snp_mode===1) ? 'is-selected' : '' }}" data-snp-value="1" aria-pressed="{{ ($snp_mode===1) ? 'true' : 'false' }}">
                        <span class="aw-radio"></span>
                        <span class="aw-option-icon aw-option-icon--date"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></span>
                        <span class="aw-option-copy">
                          <strong>Available From (Date)</strong>
                          <small>This talent will be available from a specific date.</small>
                        </span>
                      </button>
                    </div>
                  </div>

                  <div class="fg fv-row aw-field">
                    <label class="aw-date-label" for="np_id_display">Available From Date <span class="rq">*</span></label>
                    <small class="aw-field-help">Enter the date from which this talent will be available.</small>
                    <div class="aw-date-wrap" id="snp-immediate" style="display:flex">
                      <input type="text" id="text_display" value="{{ ($snp_mode===1) ? 'Available From' : 'Immediate' }}" class="form-control form-control-solid" placeholder="" readonly>
                      <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select date" name="np_id_display" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" readonly>
                      <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" value="{{ ($snp_last_working) ? date('Y-m-d',strtotime(@$snp_last_working)) : '' }}" style="opacity: 0; position: absolute; z-index: -1;top: 0;">
                    </div>
                    <div class="aw-date-guidance"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>This date will be used to show the talent's availability to clients in the marketplace.</span></div>
                    <select class="form-select form-select-solid aw-notice-days" arial-label="Select ddl_np_id" id="ddl_np_id" name="ddl_np_id" style="display:none;">
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

                <div class="aw-grid aw-grid--work">
                  <div class="fg fv-row aw-field aw-field--wide">
                    <label>Preferred Work Mode <span class="rq">*</span></label>
                    <small class="aw-field-help">Select the preferred work mode for this talent.</small>
                    <div class="aw-work-grid">
                      <label class="aw-work-option aw-work-option--remote" for="wr">
                        <input type="radio" name="wm" id="wr" {{ @$application->wm == 'remote' ? 'checked' : '' }} value="remote" />
                        <span class="aw-radio"></span>
                        <i class="fa-solid fa-laptop" aria-hidden="true"></i>
                        <span class="aw-work-copy"><strong>Remote</strong><small>Can work from any location.</small></span>
                      </label>
                      <label class="aw-work-option aw-work-option--hybrid" for="wh">
                        <input type="radio" name="wm" id="wh" {{ (@$application->wm == 'hybrid' || empty(@$application->wm)) ? 'checked' : '' }} value="hybrid" />
                        <span class="aw-radio"></span>
                        <i class="fa-solid fa-building" aria-hidden="true"></i>
                        <span class="aw-work-copy"><strong>Hybrid</strong><small>Combination of office and remote.</small></span>
                      </label>
                      <label class="aw-work-option aw-work-option--office" for="wo">
                        <input type="radio" name="wm" id="wo" {{ @$application->wm == 'office' ? 'checked' : '' }} value="office" />
                        <span class="aw-radio"></span>
                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                        <span class="aw-work-copy"><strong>Work From Office</strong><small>Office location based work.</small></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="aw-info-strip"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span><strong>Why this information is important?</strong><small>Availability and work preferences help us match the right opportunities and show accurate information to clients in the marketplace.</small></span></div>

              </div>
            </div>

            <!-- Salary Details -->
            <div class="fs br-card {{ (@$application->comission_type == 1) ? 'is-free-service' : 'is-paid-service' }}">
              <div class="br-hero">
                <div class="br-hero-title">
                  <span class="br-hero-icon"><i class="fa-solid fa-coins"></i></span>
                  <span>
                    <strong>Commercials &amp; Bill Rates</strong>
                    <small>Define the commercial model and rates for this talent. Fields will adjust based on your selection.</small>
                  </span>
                </div>
                <div class="br-hero-help">
                  <i class="fa-regular fa-circle-info"></i>
                  <span>Choose how this talent will be offered to clients.</span>
                </div>
              </div>

              <div class="fsc br-body">
                <div class="br-step">
                  <div class="br-step-head">
                    <span class="br-step-num">1</span>
                    <span>
                      <strong>Commercial Model <span class="rq">*</span></strong>
                      <small>Select how this talent will be offered to clients</small>
                    </span>
                  </div>
                  <div class="br-model-grid">
                    <button type="button" class="br-model-option {{ (@$application->comission_type == 1) ? 'is-selected' : '' }}" data-commercial-value="1" role="radio" aria-checked="{{ (@$application->comission_type == 1) ? 'true' : 'false' }}">
                      <span class="br-radio"></span>
                      <span class="br-option-icon"><i class="fa-solid fa-gift" aria-hidden="true"></i></span>
                      <span class="br-option-copy">
                        <strong>Free Service</strong>
                        <em>No charge to client</em>
                        <small>This talent will be provided to clients at no commercial charge.</small>
                      </span>
                    </button>
                    <button type="button" class="br-model-option {{ (@$application->comission_type == 2 || @$application->comission_type == '') ? 'is-selected' : '' }}" data-commercial-value="2" role="radio" aria-checked="{{ (@$application->comission_type == 2 || @$application->comission_type == '') ? 'true' : 'false' }}">
                      <span class="br-radio"></span>
                      <span class="br-option-icon"><i class="fa-solid fa-credit-card" aria-hidden="true"></i></span>
                      <span class="br-option-copy">
                        <strong>Paid Service</strong>
                        <em>Client will be billed</em>
                        <small>Set pay rate, bill rate and manage margin.</small>
                      </span>
                    </button>
                  </div>
                </div>

                <div class="br-step br-rates-step">
                  <div class="br-rates-head">
                    <div class="br-step-head">
                      <span class="br-step-num">2</span>
                      <span>
                        <strong>Rates &amp; Cost Details</strong>
                        <small>Enter candidate cost and client bill rate. Other values will be calculated automatically.</small>
                      </span>
                    </div>
                    <div class="br-currency">
                      <label for="pay_rate_currency">Currency</label>
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
                  </div>

                  <div class="d-none">
                    <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="pay_rate_mode" id="pay_rate_mode">
                    @foreach($salaryMode as $key=>$val)
                      <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                    </select>
                    <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="bill_rate_mode" id="bill_rate_mode">
                    @foreach($salaryMode as $key=>$val)
                      <option value="{{ $key }}" {{ (2==$key) ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                    </select>
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

                  <div class="br-rate-grid">
                    <div class="br-rate-panel br-pay-panel">
                      <div class="br-panel-head">
                        <span class="br-panel-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                        <span>
                          <strong>Candidate Cost (Pay Rate)</strong>
                          <small>What you pay to the candidate</small>
                        </span>
                      </div>
                      <div class="br-rate-tabs" data-rate-tabs="pay">
                        <button type="button" class="is-active" data-rate-tab="annual">Annual</button>
                        <button type="button" data-rate-tab="monthly">Monthly</button>
                        <button type="button" data-rate-tab="hourly">Hourly</button>
                      </div>
                      <div class="br-rate-field is-active" data-rate-panel="pay-annual">
                        <label>Pay Rate (Annual) <span class="rq">*</span><span id="pay_rate_annual_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted payrateannual"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="pay-monthly">
                        <label>Pay Rate (Monthly) <span class="rq">*</span><span id="pay_rate_monthly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted payratemonthly"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="pay-hourly">
                        <label>Pay Rate (Hourly) <span class="rq">*</span><span id="pay_rate_hourly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="pay_rate_amount" id="pay_rate_amount" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted payratehourly"></span>
                      </div>
                      <div class="br-auto-box">
                        <strong>Auto Calculated</strong>
                        <div>
                          <span><em id="br_pay_auto_label_one">Monthly Cost</em><b id="br_pay_monthly_display">0</b></span>
                          <span><em id="br_pay_auto_label_two">Hourly Cost</em><b id="br_pay_hourly_display">0</b></span>
                        </div>
                      </div>
                      <div class="br-formula"><i class="fa-solid fa-calculator" aria-hidden="true"></i> Monthly = Annual / 12 <span>|</span> Hourly = Annual / 2080 hours</div>
                    </div>

                    <div class="br-rate-panel br-bill-panel">
                      <div class="br-panel-head">
                        <span class="br-panel-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                        <span>
                          <strong>Client Bill Rate</strong>
                          <small>What you charge to the client</small>
                        </span>
                      </div>
                      <div class="br-rate-tabs" data-rate-tabs="bill">
                        <button type="button" class="is-active" data-rate-tab="annual">Annual</button>
                        <button type="button" data-rate-tab="monthly">Monthly</button>
                        <button type="button" data-rate-tab="hourly">Hourly</button>
                      </div>
                      <div class="br-rate-field is-active" data-rate-panel="bill-annual">
                        <label>Bill Rate (Annual) <span class="rq">*</span><span id="bill_rate_annual_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted billrateannual"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="bill-monthly">
                        <label>Bill Rate (Monthly) <span class="rq">*</span><span id="bill_rate_monthly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted billratemonthly"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="bill-hourly">
                        <label>Bill Rate (Hourly) <span class="rq">*</span><span id="bill_rate_hourly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="bill_rate_amount" id="bill_rate_amount" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">INR</span>
                        </div>
                        <span class="form-text text-muted billratehourly"></span>
                      </div>
                      <div class="br-auto-box">
                        <strong>Auto Calculated</strong>
                        <div>
                          <span><em id="br_bill_auto_label_one">Monthly Bill Rate</em><b id="br_bill_monthly_display">0</b></span>
                          <span><em id="br_bill_auto_label_two">Hourly Bill Rate</em><b id="br_bill_hourly_display">0</b></span>
                        </div>
                      </div>
                      <div class="br-formula"><i class="fa-solid fa-calculator" aria-hidden="true"></i> Monthly = Annual / 12 <span>|</span> Hourly = Annual / 2080 hours</div>
                    </div>

                    <div class="br-margin-panel">
                      <div class="br-panel-head">
                        <span class="br-panel-icon"><i class="fa-solid fa-chart-simple"></i></span>
                        <span>
                          <strong>Margin Summary</strong>
                          <small>Calculated automatically</small>
                        </span>
                      </div>
                      <div class="br-margin-list">
                        <span><em id="br_summary_pay_label">Annual Pay Rate</em><b id="br_summary_pay_annual">0</b></span>
                        <span><em id="br_summary_bill_label">Annual Bill Rate</em><b id="br_summary_bill_annual">0</b></span>
                        <span><em id="br_summary_margin_label">Annual Margin</em><b id="br_summary_margin">0</b></span>
                        <span><em id="br_summary_percent_label">Margin Percentage</em><b id="br_summary_percent">0%</b></span>
                      </div>
                      <div class="br-margin-state">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <span><strong id="br_margin_status">Healthy Margin</strong><small id="br_margin_note">This margin looks good.</small></span>
                      </div>
                      <input type="hidden" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" />
                      <div class="fg fv-row">
                        <label>Comments (Optional)</label>
                        <textarea name="comment" id="comment" maxlength="500" placeholder="Enter comments about salary expectations, client specific rates or any other details..." rows="4">{{ @$application->comment }}</textarea>
                        <div class="cc" id="br_comment_count">0 / 500</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Skills Details -->
            <div class="fs se-card">
              <div class="se-hero">
                <div class="se-title">
                  <span class="se-icon"><i class="fa-solid fa-lightbulb"></i></span>
                  <span>
                    <strong>Skills &amp; Expertise</strong>
                    <small>Add the skills this talent can be positioned and searched for in the marketplace.</small>
                  </span>
                </div>
                <div class="se-actions">
                   <span class="se-badge"><i class="fa-solid fa-house" aria-hidden="true"></i> In-House Talent</span>
                </div>
              </div>
              <div class="fsc">
                <div class="se-table-scroll">
                 <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Experience (Years) <span class="rq">*</span></th>
                      <th>Experience (Months)</th>
                      <th>Proficiency Rating <span class="rq">*</span></th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="skillExperienceWrapper">
                  </tbody>
                 </table>
                </div>
                <datalist id="skillSuggestions"></datalist>
                <button class="asb" type="button"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add Skill</button>
                <div class="se-popular">
                  <span class="se-popular-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                  <div class="se-popular-copy"><strong>Popular Skills</strong><small>Click to quickly add common skills</small></div>
                  <div class="se-popular-list">
                    @foreach (['Java', 'Python', 'React', 'Node.js', 'AWS', 'Azure', 'SQL', 'DevOps', 'Salesforce'] as $popularSkill)
                      <button type="button" class="se-popular-skill" data-skill="{{ $popularSkill }}"><i class="fa-solid fa-plus" aria-hidden="true"></i> {{ $popularSkill }}</button>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

             <!-- Relationship Manager -->
            @php
              $rmSelectedValue = trim((string) @$recruiterData);
              $rmSelectedNames = array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', str_replace(' , ', ',', $rmSelectedValue)))));
              $rmPrimaryName = $rmSelectedNames[0] ?? '';
              $rmInitialParts = array_values(array_filter(explode(' ', trim($rmPrimaryName))));
              $rmInitials = $rmPrimaryName ? strtoupper(substr($rmInitialParts[0] ?? $rmPrimaryName, 0, 1).substr($rmInitialParts[1] ?? '', 0, 1)) : '';
            @endphp
            <div class="fs rm-card">
              <div class="rm-hero">
                <div class="rm-hero-title">
                  <span class="rm-hero-icon"><i class="fa-solid fa-user"></i></span>
                  <span>
                    <strong>Client Relationship Manager</strong>
                    <small>Select the person who will coordinate with clients regarding this talent.</small>
                  </span>
                </div>
              </div>
              <div class="fsc fv-row rm-body">

                <!--begin::Custom Details-->
                <div class="rm-content" id="job_additional_detail">
                  <label class="rm-label">Relationship Manager <span class="rq">*</span></label>
                  <div class="rm-picker" id="rmPicker">
                    <span class="rm-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input class="form-control form-control-solid" name="recruiter" id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>" placeholder="Search by name or email..." readonly="" />
                    <span class="rm-input-placeholder">Search by name or email...</span>
                    <span class="rm-picker-divider"></span>
                    <span class="rm-picker-action" id="rmPickerAction">Select Relationship Manager</span>
                    <span class="rm-picker-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                  </div>
                  <small class="rm-help">Search and select a team member from your organization.</small>
                  <input type="hidden" class="form-control form-control-solid" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>" readonly="" />

                  <div class="rm-selected-card" id="rmSelectedCard" style="{{ $rmPrimaryName ? 'display:flex' : 'display:none' }}">
                    <button type="button" class="rm-selected-clear" id="rmClearSelection" aria-label="Clear relationship manager"><i class="fa-solid fa-xmark"></i></button>
                    <div class="rm-selected-eyebrow">SELECTED MANAGER</div>
                    <div class="rm-selected-content">
                      <span class="rm-avatar" id="rmAvatar">{{ $rmInitials }}</span>
                      <div class="rm-selected-copy">
                        <strong id="rmSelectedName">{{ $rmPrimaryName }}</strong>
                        <span class="rm-selected-role" id="rmSelectedRole">Relationship Manager</span>
                        <span class="rm-selected-contact" id="rmSelectedContact"></span>
                      </div>
                    </div>
                  </div>

                  <div class="rm-info-card">
                    <span class="rm-info-icon"><i class="fa-solid fa-info"></i></span>
                    <span>
                      <strong>Client Contact Information</strong>
                      <small>The selected manager's business phone number and email address may be shared with clients for coordination regarding this talent.</small>
                    </span>
                  </div>

                </div>
                <!--end::Custom Details-->
              </div>
            </div>

            <!-- Additional Information -->
            <div class="fs addl-card">
              <div class="addl-hero">
                <div class="addl-hero-title">
                  <span class="addl-hero-icon"><i class="fa-solid fa-link"></i></span>
                  <span>
                    <strong>Professional Links</strong>
                    <small>Add online profiles that help clients evaluate this talent.</small>
                  </span>
                </div>
              </div>
              <div class="fsc addl-body">
                <section class="addl-section">
                  <div class="addl-field fv-row">
                    <span class="addl-brand-icon addl-brand-linkedin">in</span>
                    <label>LinkedIn <span>(Optional)</span></label>
                    <div class="addl-input-wrap">
                      <i class="fa-solid fa-link"></i>
                      <input type="url" id="linkedin" name="linkedin" value="{{ @$application->linkedin }}" placeholder="Enter LinkedIn profile URL" />
                    </div>
                    <small>e.g. https://www.linkedin.com/in/yourname</small>
                  </div>

                  <div class="addl-field fv-row">
                    <span class="addl-brand-icon addl-brand-github">GH</span>
                    <label>GitHub <span>(Optional)</span></label>
                    <div class="addl-input-wrap">
                      <i class="fa-solid fa-link"></i>
                      <input type="url" value="{{ @$application->github }}" id="github" name="github" placeholder="Enter GitHub profile URL" />
                    </div>
                    <small>e.g. https://github.com/yourusername</small>
                  </div>

                  <div class="addl-field fv-row">
                    <span class="addl-brand-icon addl-brand-behance">Be</span>
                    <label>Behance <span>(Optional)</span></label>
                    <div class="addl-input-wrap">
                      <i class="fa-solid fa-link"></i>
                      <input type="url" id="behance" name="behance" value="{{ @$application->behance }}" placeholder="Enter Behance profile URL" />
                    </div>
                    <small>e.g. https://www.behance.net/yourname</small>
                  </div>

                  <div class="addl-field fv-row">
                    <span class="addl-brand-icon addl-brand-website"><i class="fa-solid fa-globe"></i></span>
                    <label>Portfolio / Website <span>(Optional)</span></label>
                    <div class="addl-input-wrap">
                      <i class="fa-solid fa-link"></i>
                      <input type="url" value="{{ @$application->otherLink }}" id="otherLink" name="otherLink" placeholder="Enter website or portfolio URL" />
                    </div>
                    <small>e.g. https://yourportfolio.com</small>
                  </div>
                </section>
              </div>
            </div>

            <div class="fs addl-card addl-card--details">
              <div class="addl-hero">
                <div class="addl-hero-title">
                  <span class="addl-hero-icon"><i class="fa-solid fa-user"></i></span>
                  <span>
                    <strong>Additional Details</strong>
                    <small>Provide any other relevant information about your professional background.</small>
                  </span>
                </div>
              </div>
              <div class="fsc addl-body">
                <section class="addl-section addl-section--details">
                  <div class="addl-wide-field fv-row">
                    <label><i class="fa-solid fa-passport"></i> Work Authorization <span>(Optional)</span> <i class="fa-solid fa-circle-info addl-label-info"></i></label>
                    <div class="addl-select-wrap">
                      <select name="visastatu" id="visastatu">
                        <option value="">Select work authorization status</option>
                        <option {{ @$application->visastatu == 'H1B' ? 'selected' : '' }}>H1B</option>
                        <option {{ @$application->visastatu == 'L1' ? 'selected' : '' }}>L1</option>
                        <option {{ @$application->visastatu == 'F1' ? 'selected' : '' }}>F1</option>
                        <option {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Green Card</option>
                        <option {{ @$application->visastatu == 'Citizen' ? 'selected' : '' }}>Citizen</option>
                      </select>
                      <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <small>Select your current visa or work authorization status, if applicable.</small>
                  </div>

                  <div class="addl-wide-field fv-row">
                    <label><i class="fa-solid fa-user-tie"></i> Professional Reference <span>(Optional)</span></label>
                    <input type="text" name="referencename" value="{{ @$application->referencename}}" id="referencename" placeholder="Enter reference name" />
                    <small>You can provide a reference name who can vouch for your work experience.</small>
                  </div>
                </section>

                <div class="addl-info-strip">
                  <span><i class="fa-solid fa-info"></i></span>
                  <p>All fields in this section are optional. Adding relevant links and details can help clients better understand your expertise and background.</p>
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
<script src="{{ asset('js/resume-presign.js') }}"></script>

<script>
$(document).ready(function () {
    // Keep the create action aligned with the submitted bench stage.
    var talentStageActions = {
        '1': { label: 'Add to Talent Pool', icon: 'fa-users', theme: 'pool' },
        '2': { label: 'Publish to Talent Marketplace', icon: 'fa-arrow-up', theme: 'bench' },
        '4': { label: 'Save Talent on Hold', icon: 'fa-clock', theme: 'hold' },
        '5': { label: 'Save Exited Talent', icon: 'fa-right-from-bracket', theme: 'exited' }
    };
    window.syncTalentStageAction = function () {
        var selected = $('input[name="talent_stage"]:checked');
        if (!selected.length) return;
        var action = talentStageActions[selected.val()] || talentStageActions['2'];
        $('.talent-stage-option').removeClass('is-selected');
        selected.closest('.talent-stage-option').addClass('is-selected');
        $('#save-form')
            .removeClass('talent-stage-action--pool talent-stage-action--bench talent-stage-action--hold talent-stage-action--exited')
            .addClass('talent-stage-action--' + action.theme)
            .html('<i class="fa-solid ' + action.icon + '" aria-hidden="true"></i> <span class="talent-stage-action-label">' + action.label + '</span>');
    };
    $('input[name="talent_stage"]').on('change', window.syncTalentStageAction);
    $('#talent-stage-placed').on('click', function () {
        Swal.fire({
            title: 'Talent already placed',
            text: 'Placed talent is assigned to a client. To offer this talent for new opportunities, select Bench and publish to the Talent Marketplace.',
            icon: 'info',
            confirmButtonText: 'Got it'
        });
    });
    window.syncTalentStageAction();

    // ── v2 upload: presign URL injected from PHP; flag computed at runtime ──────
    @if(config('resumes.features.browser_upload') && \Illuminate\Support\Facades\Schema::hasTable('resume_uploads'))
    window.RESUME_PRESIGN_URL = @json(route('service_company.resume.v2.sessions.store'));
    @endif
    var RESUMES_FEATURE_BROWSER_UPLOAD = !!(window.ResumePresign && window.RESUME_PRESIGN_URL);
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.RESUMES_FEATURE_BROWSER_UPLOAD = true;
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
                    + '<span><strong>Upload complete!</strong> Resume ready — fill in the form and save.</span>'
                ).show();
                $('#uploaded_file_path').val('');
                $('#resume_original_name').val('');
                var ri = document.getElementById('resume'); if (ri) ri.value = ''; // prevent multipart file on submit
            } else if (data.state === 'failed') {
                if (typeof _showUploadError === 'function') {
                    _showUploadError(data.error || 'Upload failed. Please remove and try again.');
                }
            }
        });
    }

    // Build a unique contextKey for this company + job + application context
    function getUpContextKey() {
        var companyId = @json(auth()->user()->company_id ?? 0);
        var jobId = $('#job_id').val() || '0';
        var appId = $('#appId').val() || '0';
        return 'comp' + companyId + '_job' + jobId + '_app' + appId;
    }

    /**
     * Intent: Clean resume state initialization for ADD vs EDIT bench candidates.
     * Prevents previous session resume leakage on page refresh.
     */
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        var isEditMode = @json(($page_event ?? '') === 'EDIT') || (($('#appId').val() || '0') !== '0');
        if (!isEditMode) {
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
            $('#uploaded_file_path').val('');
            $('#resume_original_name').val('');
            $('#resumeCheck').val('');
            $('#uploadBox').show();
            $('#fileInfo').hide();
            $('#fileName').text('');
            var ri = document.getElementById('resume'); if (ri) ri.value = '';
        } else {
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
        }
    }
    // ─────────────────────────────────────────────────────────────────────────

    const uploadBox  = document.getElementById('uploadBox');
    const resumeInput = document.getElementById('resume');

    // ─── Zero-temp upload pipeline ───────────────────────────────────────────
    // xhr.send(file) → raw bytes → Laravel php://input → streamed to R2
    // No PHP upload_tmp_dir write. No CORS needed.
    // ─────────────────────────────────────────────────────────────────────────

    var upXhr                = null;
    var _parseStatusInterval = null;
    var _resumeUploadId      = null;

    function fmtKB(b) { return (b / 1024).toFixed(1) + ' KB'; }
    function fmtSize(b) { return b >= 1048576 ? (b/1048576).toFixed(1)+' MB' : Math.round(b/1024)+' KB'; }
    function extLabel(file) { var e=(file.name.split('.').pop()||'').toUpperCase(); return ['PDF','DOC','DOCX'].includes(e)?e:'PDF'; }

    function resetProgress() {
        if (upXhr) { upXhr.abort(); upXhr = null; }
        $('#upWrap').hide();
        $('#upBar').css('width','0%');
        $('#upStats').text('0% &middot; 0 KB / 0 KB');
        $('#upPill').hide();
        $('#upBanner').hide().removeClass('ok err').empty();
        $('#save-form, #update-form').prop('disabled', false);
    }

    function backToDropzone() {
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        // Cancel v2 upload if in progress (aborts R2 XHR + clears session UUID)
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            window.ResumePresign.cancelUpload(getUpContextKey());
        }
        resetProgress();
        $('#upInfo').removeClass('show');
        $('#uploadBox').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('.download-file').addClass('d-none');
    }

    function showUploadedCard(file) {
        var ext = extLabel(file);
        $('#upInfo .up-file-icon').text(ext);
        $('#upFileName').text(file.name);
        $('#upFileSub').text(fmtSize(file.size) + ' &middot; ' + ext);
        var now = new Date();
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $('#upDetailUploadedOn').text(
            String(now.getDate()).padStart(2,'0')+' '+months[now.getMonth()]+' '+now.getFullYear()+
            ', '+String(now.getHours()).padStart(2,'0')+':'+String(now.getMinutes()).padStart(2,'0')
        );
        $('#upDetailParserStatus').text('Pending').css('color','');
        $('#upDetailFileType').text(ext);
        $('#upRetryWrap').hide();
        $('#uploadBox').hide();
        $('#fileInfo').hide();
        $('#upInfo').addClass('show');
    }

    function _showUploadError(msg) {
        $('#upBanner').removeClass('ok').addClass('err').html(
            '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            +'<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'
            +'</svg><span><strong>Upload failed:</strong> '+msg+'</span>'
        ).show();
        upXhr = null;
    }

    function _startParsePoller(uploadId) {
        _resumeUploadId = uploadId;
        if (_parseStatusInterval) clearInterval(_parseStatusInterval);
        _parseStatusInterval = setInterval(function () {
            $.get('{{ route("service_company.resume.status", ["id" => "__ID__"]) }}'.replace('__ID__', uploadId))
                .done(function (r) {
                    var map = { Queued:'Queued…', Processing:'Parsing resume…', Completed:'Parsed ✓', Failed:'Parse failed' };
                    $('#upDetailParserStatus').text(map[r.status] || r.status);
                    if (r.status === 'Completed' || r.status === 'Failed') {
                        clearInterval(_parseStatusInterval); _parseStatusInterval = null;
                        if (r.status === 'Failed') { $('#upDetailParserStatus').css('color','#f44336'); $('#upRetryWrap').show(); }
                        else {
                            $('#upRetryWrap').hide();
                            if (r.parsed_data && r.parsed_data.skills && window.addParsedBenchSkills) {
                                window.addParsedBenchSkills(r.parsed_data.skills);
                            }
                        }
                    }
                });
        }, 4000);
    }

    function processFile(file, event = null) {
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        if ($.inArray(ext, ['pdf', 'doc', 'docx']) === -1) {
            alert('Only PDF, DOC and DOCX files are allowed.');
            return;
        }

        const sizeMB = file.size / (1024 * 1024);

        if (sizeMB > 6) {
            alert('Maximum file size is 6 MB.');
            return;
        }

        const sizeStr = sizeMB >= 1 ? sizeMB.toFixed(2) + ' MB' : Math.round(file.size / 1024) + ' KB';
        $('#fileName').text(file.name + ' (' + sizeStr + ')');
        $('#fileInfo').css('display', 'flex');
        $('#resumeCheck').val('1');
        $('.download-file').addClass('d-none');

        // Drive the zero-temp XHR first; guard tells handleFileInputChange to skip its jQuery POST.
        window.__upXhrChannelClaimed = true;
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            // v2 path: direct browser → R2, zero PHP bytes.
            window.ResumePresign.startUpload(file, getUpContextKey()).catch(function () {
                // error already surfaced via onStateChange 'failed'
            });
        } else if (typeof uploadWithProgress === 'function') {
            // v1 path: PHP multipart POST (unchanged)
            uploadWithProgress(file);
        }
        if (typeof handleFileInputChange === 'function') {
            // For drag-drop / paste, no native input-change event exists.
            // resumeInput.files was already assigned above, so a synthetic
            // event pointing to it is sufficient for handleFileInputChange.
            handleFileInputChange(event || { target: resumeInput });
        }
        // Reset so the legacy channel is open for the next pick.
        window.__upXhrChannelClaimed = false;
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        _resumeUploadId = null;
        resetProgress();
        window.__upXhrChannelClaimed = true;

        $('#upWrap').show();
        $('#upStats').text('0% &middot; ' + fmtKB(0) + ' / ' + fmtKB(file.size));

        var mimeMap = { pdf:'application/pdf', doc:'application/msword', docx:'application/vnd.openxmlformats-officedocument.wordprocessingml.document' };
        var ext         = (file.name.split('.').pop()||'').toLowerCase();
        var contentType = mimeMap[ext] || file.type || 'application/octet-stream';

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
                $('#upBar').css('width','100%');
                $('#upStats').text('100% &middot; ' + fmtKB(file.size) + ' / ' + fmtKB(file.size));
                $('#upPill').css('display','inline-flex');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    +'<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    +'<span><strong>Upload complete!</strong> Resume uploaded &amp; queued for parsing.</span>'
                ).show();
                $('#uploaded_file_path').val(r.file_path || '');
                $('#resume_original_name').val(r.original_name || file.name || '');
                $('#resumeCheck').val('1');
                showUploadedCard(file);
                if (r.resume_upload_id) _startParsePoller(r.resume_upload_id);
            } else {
                _showUploadError((r && r.message) ? r.message : 'Upload failed. Try again.');
            }
        };

        upXhr.onerror = function () { _showUploadError('Network error. Click Remove and try again.'); };

        upXhr.send(file);
    }

    // Replace / Remove inside info card
    $(document).on('click', '#upReplaceBtn', function (e) { e.preventDefault(); backToDropzone(); resumeInput.click(); });
    $(document).on('click', '#upRemoveBtn',  function (e) { e.preventDefault(); backToDropzone(); });

    // Retry parse
    $(document).on('click', '#upRetryBtn', function (e) {
        e.preventDefault();
        if (!_resumeUploadId) return;
        $('#upDetailParserStatus').text('Re-queuing…');
        $('#upRetryWrap').hide();
        $.post('{{ route("service_company.resume.retry", ["id" => "__ID__"]) }}'.replace('__ID__', _resumeUploadId), { _token: '{{ csrf_token() }}' })
            .done(function () { _startParsePoller(_resumeUploadId); })
            .fail(function () { $('#upDetailParserStatus').text('Retry failed'); $('#upRetryWrap').show(); });
    });

    // Direct binding so stopPropagation() fires BEFORE the click reaches
    // #uploadBox — prevents resumeInput.click() from being called twice.
    document.getElementById('chooseFileBtn').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resumeInput.click();
    });

    // Remove file strip button
    $(document).on('click', '#removeFile', function (e) { e.preventDefault(); e.stopPropagation(); backToDropzone(); });

    // File input change
    $('#resume').on('change', function (e) {
        if (this.files.length > 0) {
            processFile(this.files[0], e);
        } else {
            $('#fileInfo').hide();
            $('#resumeCheck').val('');
            $('.download-file').addClass('d-none');
        }
    });

    // Click upload box
    $('#uploadBox').on('click', function (e) {
        if ($(e.target).closest('#chooseFileBtn, #removeFile, .download-file').length) return;
        resumeInput.click();
    });

    // Paste
    document.addEventListener('paste', function (e) {
        var files = e.clipboardData?.files;
        if (files && files.length) {
            var file = files[0];
            var dt = new DataTransfer(); dt.items.add(file); resumeInput.files = dt.files;
            processFile(file);
        }
    });

    // Drag & Drop
    ['dragenter','dragover','dragleave','drop'].forEach(function (ev) {
        uploadBox.addEventListener(ev, function (e) { e.preventDefault(); e.stopPropagation(); });
    });
    uploadBox.addEventListener('dragenter', function () { this.classList.add('dragging'); });
    uploadBox.addEventListener('dragover',  function () { this.classList.add('dragging'); });
    uploadBox.addEventListener('dragleave', function () { this.classList.remove('dragging'); });
    uploadBox.addEventListener('drop', function (e) {
        this.classList.remove('dragging');
        var files = e.dataTransfer.files;
        if (files.length > 0) {
            var file = files[0];
            var dt = new DataTransfer(); dt.items.add(file); resumeInput.files = dt.files;
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
    window.createTalentPhoneInputs = {
      primary: primaryPhoneInput,
      secondary: secondaryPhoneInput
    };

    window.normalizeCreateTalentPhoneDigits = function (value) {
      return String(value || '').replace(/[^\d]/g, '');
    };

    window.validateCreateTalentPhone = function (inputId, required) {
      const input = document.getElementById(inputId);
      if (!input) {
        return !required;
      }

      const rawValue = String(input.value || '').trim();
      if (!rawValue) {
        return !required;
      }

      const itiInstance = inputId === 'secondary_mobile'
        ? window.createTalentPhoneInputs?.secondary
        : window.createTalentPhoneInputs?.primary;

      if (window.intlTelInputUtils && itiInstance && typeof itiInstance.isValidNumber === 'function') {
        return itiInstance.isValidNumber();
      }

      const digits = window.normalizeCreateTalentPhoneDigits(rawValue);
      return digits.length >= 6 && digits.length <= 15;
    };

    window.normalizeCreateTalentPhones = function () {
      ['phone', 'secondary_mobile'].forEach(function (inputId) {
        const input = document.getElementById(inputId);
        if (input) {
          input.value = window.normalizeCreateTalentPhoneDigits(input.value);
        }
      });
    };

      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);
      
    }
    // Update country code on input or country flag selection
    $("#phone").on("countrychange input change", function () {
      updateCountryCode(primaryPhoneInput, "#primary_country_code");
    });

    $("#secondary_mobile").on("countrychange input change", function () {
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

    // Keeps the redesigned commercials UI in sync with the original submitted rate fields.
    function commercialNumber(value) {
        return parseFloat((value || '0').toString().replace(/,/g, '')) || 0;
    }

    function commercialFormat(value, suffix) {
        let numeric = commercialNumber(value);
        if (!numeric) {
            return suffix ? '0' + suffix : '0';
        }
        let formatted = numeric.toLocaleString('en-IN', {
            maximumFractionDigits: 2
        });
        return suffix ? formatted + suffix : formatted;
    }

    let commercialSavedPaidValues = null;

    function isFreeCommercialMode() {
        return ($('#comission_type').val() || '2').toString() === '1';
    }

    function snapshotPaidBillValues() {
        commercialSavedPaidValues = {
            annual: $('#bill_annual').val(),
            monthly: $('#bill_monthly').val(),
            hourly: $('#bill_rate_amount').val(),
            profit: $('#percentage_hike').val()
        };
    }

    function restorePaidBillValues() {
        if (!commercialSavedPaidValues) {
            return;
        }

        $('#bill_annual').val(commercialSavedPaidValues.annual || '');
        $('#bill_monthly').val(commercialSavedPaidValues.monthly || '');
        $('#bill_rate_amount').val(commercialSavedPaidValues.hourly || '');
        $('#percentage_hike').val(commercialSavedPaidValues.profit || '');
    }

    function syncBillFieldsFromAnnual() {
        let annual = commercialNumber($('#bill_annual').val());
        let monthly = annual / MONTHS_PER_YEAR;
        let hourly = monthly / HOURS_PER_MONTH;

        $('#bill_monthly').val(format(monthly));
        $('#bill_rate_amount').val(format(hourly));
        updateBillUI();
    }

    function syncCommercialRateTab(tab) {
        $('.br-rate-tabs button').removeClass('is-active');
        $('.br-rate-tabs button[data-rate-tab="' + tab + '"]').addClass('is-active');
        $('.br-rate-field').removeClass('is-active');
        $('[data-rate-panel="pay-' + tab + '"], [data-rate-panel="bill-' + tab + '"]').addClass('is-active');
    }

    function activeCommercialRateTab() {
        return $('.br-rate-tabs button.is-active').first().data('rate-tab') || 'annual';
    }

    function commercialModeConfig(tab, values) {
        let config = {
            annual: {
                payLabel: 'Annual Pay Rate',
                billLabel: 'Annual Bill Rate',
                marginLabel: 'Annual Margin',
                basePay: values.payAnnual,
                baseBill: values.billAnnual,
                payAutoOneLabel: 'Monthly Cost',
                payAutoOneValue: values.payMonthly,
                payAutoTwoLabel: 'Hourly Cost',
                payAutoTwoValue: values.payHourly,
                billAutoOneLabel: 'Monthly Bill Rate',
                billAutoOneValue: values.billMonthly,
                billAutoTwoLabel: 'Hourly Bill Rate',
                billAutoTwoValue: values.billHourly
            },
            monthly: {
                payLabel: 'Monthly Pay Rate',
                billLabel: 'Monthly Bill Rate',
                marginLabel: 'Monthly Margin',
                basePay: values.payMonthly,
                baseBill: values.billMonthly,
                payAutoOneLabel: 'Annual Cost',
                payAutoOneValue: values.payAnnual,
                payAutoTwoLabel: 'Hourly Cost',
                payAutoTwoValue: values.payHourly,
                billAutoOneLabel: 'Annual Bill Rate',
                billAutoOneValue: values.billAnnual,
                billAutoTwoLabel: 'Hourly Bill Rate',
                billAutoTwoValue: values.billHourly
            },
            hourly: {
                payLabel: 'Hourly Pay Rate',
                billLabel: 'Hourly Bill Rate',
                marginLabel: 'Hourly Margin',
                basePay: values.payHourly,
                baseBill: values.billHourly,
                payAutoOneLabel: 'Annual Cost',
                payAutoOneValue: values.payAnnual,
                payAutoTwoLabel: 'Monthly Cost',
                payAutoTwoValue: values.payMonthly,
                billAutoOneLabel: 'Annual Bill Rate',
                billAutoOneValue: values.billAnnual,
                billAutoTwoLabel: 'Monthly Bill Rate',
                billAutoTwoValue: values.billMonthly
            }
        };

        return config[tab] || config.annual;
    }

    function refreshCommercialAfterSplit() {
        updatePayUI();
        updateBillUI();
        refreshCommercialSummary();
    }

    function refreshCommercialSummary() {
        let payAnnual = commercialNumber($('#pay_annual').val());
        let payMonthly = commercialNumber($('#pay_monthly').val());
        let payHourly = commercialNumber($('#pay_rate_amount').val());
        let billAnnual = commercialNumber($('#bill_annual').val());
        let billMonthly = commercialNumber($('#bill_monthly').val());
        let billHourly = commercialNumber($('#bill_rate_amount').val());
        if (payAnnual > 0) {
            payMonthly = payMonthly || (payAnnual / MONTHS_PER_YEAR);
            payHourly = payHourly || (payAnnual / MONTHS_PER_YEAR / HOURS_PER_MONTH);
        }
        if (billAnnual > 0) {
            billMonthly = billMonthly || (billAnnual / MONTHS_PER_YEAR);
            billHourly = billHourly || (billAnnual / MONTHS_PER_YEAR / HOURS_PER_MONTH);
        }
        let modeConfig = commercialModeConfig(activeCommercialRateTab(), {
            payAnnual: payAnnual,
            payMonthly: payMonthly,
            payHourly: payHourly,
            billAnnual: billAnnual,
            billMonthly: billMonthly,
            billHourly: billHourly
        });
        let margin = modeConfig.baseBill - modeConfig.basePay;
        let computedPercent = modeConfig.basePay > 0 ? (margin / modeConfig.basePay) * 100 : 0;
        let profitValue = $('#percentage_hike').val();
        let displayedPercent = profitValue !== '' ? commercialNumber(profitValue) : computedPercent;
        let isFree = isFreeCommercialMode();
        let needsMarginReview = modeConfig.basePay > 0 && modeConfig.baseBill > 0 && (margin < 0 || displayedPercent < 10);

        $('#br_pay_auto_label_one').text(modeConfig.payAutoOneLabel);
        $('#br_pay_auto_label_two').text(modeConfig.payAutoTwoLabel);
        $('#br_bill_auto_label_one').text(modeConfig.billAutoOneLabel);
        $('#br_bill_auto_label_two').text(modeConfig.billAutoTwoLabel);
        $('#br_pay_monthly_display').text(commercialFormat(modeConfig.payAutoOneValue));
        $('#br_pay_hourly_display').text(commercialFormat(modeConfig.payAutoTwoValue));
        $('#br_summary_pay_annual').text(commercialFormat(modeConfig.basePay));

        if (isFree) {
            $('#br_bill_monthly_display').text('No billing');
            $('#br_bill_hourly_display').text('No billing');
            $('#br_summary_pay_label').text(modeConfig.payLabel);
            $('#br_summary_bill_label').text('Client Billing');
            $('#br_summary_margin_label').text('Commercial Margin');
            $('#br_summary_percent_label').text('Selected Model');
            $('#br_summary_bill_annual').text('Disabled');
            $('#br_summary_margin').text('Not billed');
            $('#br_summary_percent').text('Free Service');
            $('.br-margin-state').removeClass('is-low').addClass('is-free');
            $('#br_margin_status').text('Free Service Selected');
            $('#br_margin_note').text('This talent will be offered to clients without commercial billing. Client bill rate is not required.');
        } else {
            $('#br_bill_monthly_display').text(commercialFormat(modeConfig.billAutoOneValue));
            $('#br_bill_hourly_display').text(commercialFormat(modeConfig.billAutoTwoValue));
            $('#br_summary_pay_label').text(modeConfig.payLabel);
            $('#br_summary_bill_label').text('Client Billing');
            $('#br_summary_margin_label').text('Commercial Margin');
            $('#br_summary_percent_label').text('Selected Model');
            $('#br_summary_bill_annual').text(commercialFormat(modeConfig.baseBill));
            $('#br_summary_margin').text(commercialFormat(margin) + ' (' + commercialFormat(displayedPercent, '%') + ')');
            $('#br_summary_percent').text('Paid Service');
            $('.br-margin-state').removeClass('is-free').toggleClass('is-low', needsMarginReview);
            $('#br_margin_status').text(needsMarginReview ? 'Review Margin' : 'Paid Service Selected');
            $('#br_margin_note').text(needsMarginReview ? 'Please review the configured bill rate.' : 'Client will be billed. Set pay rate, bill rate and manage margin.');
        }

        $('#br_comment_count').text(($('#comment').val() || '').length + ' / 500');
    }

    function syncCommercialCards() {
        let currentValue = ($('#comission_type').val() || '2').toString();
        $('.br-model-option').removeClass('is-selected').attr('aria-checked', 'false');
        $('.br-model-option[data-commercial-value="' + currentValue + '"]').addClass('is-selected').attr('aria-checked', 'true');
    }

    function applyCommercialMode() {
        let currentValue = ($('#comission_type').val() || '2').toString();
        if (currentValue !== '1' && currentValue !== '2') {
            currentValue = '2';
        }

        $('#comission_type').val(currentValue);

        let isFree = currentValue === '1';
        let wasFree = $('.br-card').hasClass('is-free-service');
        let billFields = $('#bill_annual, #bill_monthly, #bill_rate_amount, #percentage_hike');

        if (isFree && !wasFree) {
            snapshotPaidBillValues();
            $('#bill_annual, #bill_monthly, #bill_rate_amount, #percentage_hike').val('');
            $('#bill_rate_hourly_usd, #bill_rate_monthly_usd, #bill_rate_annual_usd').text('');
        } else if (!isFree && wasFree) {
            restorePaidBillValues();
            syncCommercialRateTab($('.br-rate-tabs button.is-active').first().data('rate-tab') || 'annual');
            updateBillUI();
            percentage_hike();
        }

        billFields.prop('readonly', isFree).attr('aria-readonly', isFree ? 'true' : 'false');
        $('.br-card').toggleClass('is-free-service', isFree).toggleClass('is-paid-service', !isFree);
        $('.br-bill-panel').toggleClass('is-locked', isFree);
        syncCommercialCards();
        refreshCommercialSummary();
    }

    $(document).on('click', '.br-model-option', function () {
        $('#comission_type').val($(this).attr('data-commercial-value'));
        applyCommercialMode();
    });

    $(document).on('click', '.br-rate-tabs button', function () {
        let group = $(this).closest('.br-rate-tabs').data('rate-tabs');
        let tab = $(this).data('rate-tab');

        syncCommercialRateTab(tab);
        $('[data-rate-panel="' + group + '-' + tab + '"] input').trigger('focus');
    });

    $(document).on('change', '#comission_type', applyCommercialMode);
    $(document).on('input change', '#pay_annual, #pay_monthly, #pay_rate_amount, #bill_annual, #bill_monthly, #bill_rate_amount, #percentage_hike, #comment', refreshCommercialSummary);

    window.isFreeCommercialMode = isFreeCommercialMode;
    window.refreshCommercialSummary = refreshCommercialSummary;
    window.syncBillFieldsFromAnnual = syncBillFieldsFromAnnual;
    window.applyCommercialMode = applyCommercialMode;

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
        refreshCommercialAfterSplit();
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
        refreshCommercialAfterSplit();
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
        refreshCommercialAfterSplit();
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
        refreshCommercialAfterSplit();
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
        refreshCommercialAfterSplit();
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
        refreshCommercialAfterSplit();
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
          refreshCommercialSummary();
          return false;
        }

        let hourly = fromUSD(pay_rate_amount, currSet);
        let monthly = hourly * HOURS_PER_MONTH;
        let annual = monthly * MONTHS_PER_YEAR;
        
        $('#pay_rate_hourly_usd').text('Approx $ '+ format(hourly));
        $('#pay_rate_monthly_usd').text('Approx $ '+ format(monthly));
        $('#pay_rate_annual_usd').text('Approx $ '+ format(annual));
        updateCTCFields();
        refreshCommercialSummary();
    }
    function updateBillUI() {
        if (isFreeCommercialMode()) {
          $('#bill_rate_hourly_usd').text('');
          $('#bill_rate_monthly_usd').text('');
          $('#bill_rate_annual_usd').text('');
          refreshCommercialSummary();
          return false;
        }

        let currency = $('#pay_rate_currency').val();
        let bill_rate_amount = $('#bill_rate_amount').val();
        let currSet = currencyRateschar[currency];
        if(currency==4){ 
          $('#bill_rate_hourly_usd').text('');
          $('#bill_rate_monthly_usd').text('');
          $('#bill_rate_annual_usd').text('');
          updateCTCFields();
          refreshCommercialSummary();
          return false;
        }

        let hourly = fromUSD(bill_rate_amount, currSet);
        let monthly = hourly * HOURS_PER_MONTH;
        let annual = monthly * MONTHS_PER_YEAR;

        $('#bill_rate_hourly_usd').text('Approx $ '+ format(hourly));
        $('#bill_rate_monthly_usd').text('Approx $ '+ format(monthly));
        $('#bill_rate_annual_usd').text('Approx $ '+ format(annual));
        updateCTCFields();
        refreshCommercialSummary();
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
          applyCommercialMode();
          payRateUpdate();
          billRateUpdate();
          refreshCommercialSummary();
      }, 1000);

    

  });


</script>


<script>

    window.existingSkillExperience = @json($skillExperiences);
    const experienceWrapper = document.getElementById('skillExperienceWrapper');
    const skillSuggestions = document.getElementById('skillSuggestions');
    const popularSkillButtons = document.querySelectorAll('.se-popular-skill');
    let skillSuggestionTimer;

    function skillKey(value) {
        return String(value || '').trim().toLocaleLowerCase();
    }

    function findSkillRow(value, except = null) {
        return Array.from(experienceWrapper.querySelectorAll('tr')).find(row =>
            row !== except && skillKey(row.querySelector('[name="skills[]"]').value) === skillKey(value)
        );
    }

    function updatePopularSkills() {
        popularSkillButtons.forEach(button => {
            const selected = !!findSkillRow(button.dataset.skill);
            button.classList.toggle('is-added', selected);
            button.setAttribute('aria-pressed', String(selected));
        });
    }

    // Keep skills and their experience fields in the same submitted row.
    function addSkillExperience(skill = '', data = {}) {
        const name = String(skill || '').trim();
        const existing = name ? findSkillRow(name) : null;
        if (existing) return existing;

        const emptyRow = name && Array.from(experienceWrapper.querySelectorAll('tr')).find(row =>
            !row.querySelector('[name="skills[]"]').value.trim() &&
            Array.from(row.querySelectorAll('select')).every(select => !select.value)
        );
        if (emptyRow) {
            const input = emptyRow.querySelector('[name="skills[]"]');
            input.value = name;
            input.dataset.previousSkill = name;
            updatePopularSkills();
            return emptyRow;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <label class="se-field se-field--skill">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="text" name="skills[]" list="skillSuggestions" autocomplete="off" placeholder="Search or type skill (e.g. Java)" aria-label="Skill" />
                </label>
                <small class="se-skill-hint">Start typing to see suggestions</small>
            </td>
            <td><label class="se-field se-field--select"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><select name="years[]" aria-label="Experience years"></select></label></td>
            <td><label class="se-field se-field--select"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i><select name="months[]" aria-label="Experience months"></select></label></td>
            <td><label class="se-field se-field--select"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i><select name="ratings[]" aria-label="Proficiency rating"></select></label></td>
            <td><button class="ab" type="button" aria-label="Delete skill" title="Delete skill"><i class="fa-regular fa-trash-can" aria-hidden="true"></i></button></td>`;
        const input = row.querySelector('[name="skills[]"]');
        input.value = name;
        input.dataset.previousSkill = name;

        function fillOptions(select, label, start, end, selected, suffix) {
            select.add(new Option(label, ''));
            for (let value = start; value <= end; value++) {
                select.add(new Option(value + ' ' + suffix(value), String(value)));
            }
            if (selected !== null && selected !== undefined && selected !== '') {
                select.value = String(selected);
            }
        }

        fillOptions(row.querySelector('[name="years[]"]'), 'Select Year', 1, 60, data.year, n => n === 1 ? 'Year' : 'Years');
        fillOptions(row.querySelector('[name="months[]"]'), 'Select Month', 0, 11, data.month, n => n === 1 ? 'Month' : 'Months');
        fillOptions(row.querySelector('[name="ratings[]"]'), 'Select Rating', 1, 5, data.rating, n => n === 1 ? 'Rating' : 'Ratings');

        input.addEventListener('change', () => {
            input.value = input.value.trim();
            const duplicate = input.value ? findSkillRow(input.value, row) : null;
            if (duplicate) {
                input.value = input.dataset.previousSkill || '';
                duplicate.querySelector('[name="skills[]"]').focus();
            } else {
                input.dataset.previousSkill = input.value;
            }
            updatePopularSkills();
        });
        input.addEventListener('input', () => {
            updatePopularSkills();
            clearTimeout(skillSuggestionTimer);
            const query = input.value.trim();
            if (query.length < 2) return;
            skillSuggestionTimer = setTimeout(() => {
                $.get('{{ url('/') }}/skill-suggestions', { q: query }).done(results => {
                    if (document.activeElement !== input || input.value.trim() !== query || !Array.isArray(results)) return;
                    skillSuggestions.replaceChildren();
                    results.forEach(result => {
                        const value = typeof result === 'string' ? result : (result.value || result.name || '');
                        if (!value) return;
                        const option = document.createElement('option');
                        option.value = value;
                        skillSuggestions.appendChild(option);
                    });
                });
            }, 250);
        });
        row.querySelector('.ab').addEventListener('click', () => {
            row.remove();
            updatePopularSkills();
        });

        experienceWrapper.appendChild(row);
        updatePopularSkills();
        return row;
    }

    function addParsedBenchSkills(values) {
        if (typeof values === 'string') {
            try { values = JSON.parse(values); } catch (_) { values = values.split(','); }
        }
        if (!Array.isArray(values)) return;
        values.forEach(value => {
            const skill = typeof value === 'string' ? value : (value && (value.value || value.name));
            if (skill && String(skill).trim()) addSkillExperience(skill);
        });
    }
    window.addParsedBenchSkills = addParsedBenchSkills;

    Object.entries(window.existingSkillExperience || {}).forEach(([skill, data]) => {
        addSkillExperience(skill, data || {});
    });
    const storedSkills = document.getElementById('skills').value;
    if (storedSkills) addParsedBenchSkills(storedSkills);
    if (!experienceWrapper.children.length) addSkillExperience();

    document.querySelector('.se-card .asb').addEventListener('click', () => {
        addSkillExperience().querySelector('[name="skills[]"]').focus();
    });
    popularSkillButtons.forEach(button => button.addEventListener('click', () => {
        addSkillExperience(button.dataset.skill).querySelector('[name="skills[]"]').focus();
    }));


    var inputloc = document.querySelector("#kt_tagify_work_location");
    var cities = @json($cities);
                             
    // Initialize Tagify script on the above inputs
    var tagifylocation =  new Tagify(inputloc, {
        whitelist: cities,
        enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        placeholder: 'Search and select one verified location',
        dropdown: {
            maxItems: 20,           // <- maximum allowed rendered suggestions
            classname: "bd-location-dropdown", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true,
            highlightFirst: true // First suggestion active
        }
    });

    function setSingleLocationTag(locationValue) {
        var selectedLocation = Array.isArray(locationValue) ? locationValue[0] : locationValue;
        if (selectedLocation && typeof selectedLocation === 'object') {
            selectedLocation = selectedLocation.value || selectedLocation.name || selectedLocation.text || '';
        }
        selectedLocation = (selectedLocation || '').toString().trim();
        tagifylocation.removeAllTags();
        if (selectedLocation) {
            tagifylocation.addTags([selectedLocation]);
        }
    }

    // Keep location as a single verified selection while preserving the existing Tagify field payload.
    tagifylocation.on('input', function(e) {
        tagifylocation.dropdown.show.call(tagifylocation);
    });

    tagifylocation.on('dropdown:select', function(e) {
        setSingleLocationTag(e.detail.data.value);
    });

    tagifylocation.on('add', function(e) {
        if (tagifylocation.value.length > 1) {
            setSingleLocationTag(e.detail.data.value);
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
            setSingleLocationTag(defaultLocation); // Add the default location as a tag
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

  $(document).on('click', '#rmPicker', function(e) {
      if ($(e.target).is('#recruiter')) {
          return;
      }
      $('#recruiter').trigger('click');
  });

  $(document).on('click', '#rmClearSelection', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $('#recruiter').val('');
      $('#recruiter_id').val('');
      syncRelationshipManagerCard();
  });

  syncRelationshipManagerCard();
  setInterval(function() {
      var currentValue = $('#recruiter').val() || '';
      if (currentValue !== window.__rmLastRecruiterValue) {
          syncRelationshipManagerCard();
      }
  }, 400);
});

// Keep the redesigned Relationship Manager summary in sync with the existing drawer-owned recruiter fields.
function rmEscape(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function rmTitleCase(value) {
    return String(value || '').replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
}

function rmSelectedNames() {
    var value = $('#recruiter').val() || '';
    return value.split(',').map(function(item) {
        return item.trim();
    }).filter(function(item) {
        return item.length;
    });
}

function rmInitials(name) {
    var parts = String(name || '').trim().split(/\s+/);
    var first = parts[0] ? parts[0].charAt(0) : '';
    var second = parts[1] ? parts[1].charAt(0) : '';
    return (first + second || first || 'RM').toUpperCase();
}

function rmFindContact(name) {
    var contacts = window.__teamContacts || [];
    var normalized = String(name || '').trim().toLowerCase();
    for (var index = 0; index < contacts.length; index++) {
        if (String(contacts[index].name || '').trim().toLowerCase() === normalized) {
            return contacts[index];
        }
    }
    return {};
}

function syncRelationshipManagerCard() {
    var names = rmSelectedNames();
    var selected = names[0] || '';
    window.__rmLastRecruiterValue = $('#recruiter').val() || '';
    $('#rmPicker').toggleClass('has-selection', names.length > 0);
    $('#job_additional_detail').toggleClass('rm-has-selection', names.length > 0);

    if (!selected) {
        $('#rmSelectedCard').hide();
        $('#rmPickerAction').text('Select Relationship Manager');
        return;
    }

    var contact = rmFindContact(selected);
    var role = contact.role || contact.display_name || 'Relationship Manager';
    var email = contact.email || '';
    var phone = contact.number || contact.mobile || contact.phone || '';
    var contactHtml = '';

    if (email) {
        contactHtml += '<span><i class="fa-solid fa-envelope"></i>' + rmEscape(email) + '</span>';
    }
    if (phone) {
        contactHtml += '<span><i class="fa-solid fa-phone"></i>' + rmEscape(phone) + '</span>';
    }
    if (names.length > 1) {
        contactHtml += '<span class="rm-more-count">+' + (names.length - 1) + ' more selected</span>';
    }

    $('#rmPickerAction').text('Select Relationship Manager');
    $('#rmAvatar').text(rmInitials(selected));
    $('#rmSelectedName').text(rmTitleCase(selected));
    $('#rmSelectedRole').text(rmTitleCase(role));
    $('#rmSelectedContact').html(contactHtml).toggle(contactHtml.length > 0);
    $('#rmSelectedCard').css('display', 'flex');
}

function isAvailableFromDateMode() {
    return String($('#serving_notice_period').val() || '') === '1';
}

function syncAvailableFromDateControl() {
    var editable = isAvailableFromDateMode();
    $('.aw-card').toggleClass('is-date-availability', editable).toggleClass('is-immediate-availability', !editable);
    $('#np_id').prop('disabled', !editable);
    $('#snp-immediate').css('display', 'flex');
    $('#np_id_display')
        .toggleClass('is-disabled', !editable)
        .attr('aria-disabled', editable ? 'false' : 'true')
        .attr('placeholder', editable ? 'Select date' : 'Select Available From (Date) first');
    $('#snp-immediate').toggleClass('is-disabled', !editable);
}

function openAvailableFromDatePicker() {
    if (!isAvailableFromDateMode()) {
        return;
    }
    var dateInput = document.getElementById('np_id');
    if (!dateInput) {
        return;
    }
    if (dateInput.showPicker) {
        try {
            dateInput.showPicker();
            return;
        } catch (error) {
            dateInput.focus();
        }
    }
    if (!dateInput.showPicker) {
        dateInput.focus();
    }
    dateInput.click();
}

$(document).on('change','#serving_notice_period',function(){
    var cvalue = $('#serving_notice_period').val();
    $('.aw-option').removeClass('is-selected').attr('aria-pressed', 'false');
    $('.aw-option[data-snp-value="' + cvalue + '"]').addClass('is-selected').attr('aria-pressed', 'true');
		$('#np_id').val('');
		$('#np_id_display').val('');
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
      $('#text_display').val('Available From');
			$("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });		
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}else if(cvalue ==2){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$("#np_id").attr('type','date');
			$('#np_id').attr("disabled", true);
			$('#text_display').val('Immediate');
      $("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });	
      $("#snp-immediate").attr('style','display:flex');
      dateManageFunc();
		}	
    syncAvailableFromDateControl();
  })

$(document).on('click', '.aw-option', function(){
    $('#serving_notice_period').val($(this).data('snp-value')).trigger('change');
});

$(document).on('click', '#snp-immediate', function(){
    openAvailableFromDatePicker();
});

$(function(){
    var selectedAvailability = $('#serving_notice_period').val() || '2';
    if (selectedAvailability === '0') {
        selectedAvailability = '2';
        $('#serving_notice_period').val('2');
    }
    $('.aw-option').removeClass('is-selected').attr('aria-pressed', 'false');
    $('.aw-option[data-snp-value="' + selectedAvailability + '"]').addClass('is-selected').attr('aria-pressed', 'true');
    syncAvailableFromDateControl();
});

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
                        'phone':{validators:{notEmpty:{message: "Primary number is required"},callback:{message: "Enter a valid mobile number for the selected country", callback: function () { return window.validateCreateTalentPhone('phone', true); }}}},
							          'secondary_mobile':{validators:{callback:{message: "Enter a valid secondary number for the selected country", callback: function () { return window.validateCreateTalentPhone('secondary_mobile', false); }}}},
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
                        if (!navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                            $("#save-form").prop("disabled", false);
                            window.syncTalentStageAction();
                            submitButton.disabled = false;
                            return;
                        }


                          window.normalizeCreateTalentPhones();
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
                                    window.syncTalentStageAction();
   																	Swal.fire("Warning",` ${response.message}` , "warning");	
                                    return false;
   																}

                                  
                                if(response.status == 'mailcheck'){
                                  if (!navigator.onLine) {
                                      Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                      $("#save-form").prop("disabled", false);
                                      window.syncTalentStageAction();
                                      submitButton.disabled = false;
                                      return;
                                  }
                                   window.syncTalentStageAction();
   																	Swal.fire("Warning", response.message , "warning").then(function(){
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
																error			: function (xhr) {
                                    $("#save-form").prop("disabled", false);
                                    window.syncTalentStageAction();
                                    submitButton.disabled = false;
                                    if (xhr.status === 0 || !navigator.onLine) {
                                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    } else {
                                        Swal.fire("Error", "Something went wrong. Please try again.", "error");
                                    }
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
                        'phone':{validators:{notEmpty:{message: "Primary number is required"},callback:{message: "Enter a valid mobile number for the selected country", callback: function () { return window.validateCreateTalentPhone('phone', true); }}}},
							          'secondary_mobile':{validators:{callback:{message: "Enter a valid secondary number for the selected country", callback: function () { return window.validateCreateTalentPhone('secondary_mobile', false); }}}},
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
                        if (!navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                            $("#edit-form").prop("disabled", false).html("Update");
                            submitButton.disabled = false;
                            return;
                        }


                          window.normalizeCreateTalentPhones();
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
                                    if (!navigator.onLine) {
                                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                        $("#save-form").prop("disabled", false).html("Submit");
                                        submitButton.disabled = false;
                                        return;
                                    }
                                   $("#edit-form").html(`Update`);
   																	Swal.fire("Warning", response.message , "warning").then(function(){
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
															error			: function (xhr) {
                                    $("#edit-form").prop("disabled", false).html("Update");
                                    submitButton.disabled = false;
                                    if (xhr.status === 0 || !navigator.onLine) {
                                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    } else {
                                        Swal.fire("Error", "Something went wrong. Please try again.", "error");
                                    }
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
                    if (window.addParsedBenchSkills) window.addParsedBenchSkills(response.skills);
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


                    setSingleLocationTag(response.country); // Keep current location to one selected value

                    
                    

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
     if (window.isFreeCommercialMode && window.isFreeCommercialMode()) {
        $('#percentage_hike').val('');
        if (window.refreshCommercialSummary) {
          window.refreshCommercialSummary();
        }
        return;
     }
     
     var currentCTC = parseFloat($('#pay_annual').val());
     var expectedCTC = parseFloat($('#bill_annual').val());
     
     var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;

     if (currentCTC > 0) {
        $('#percentage_hike').val(percentageIncrease.toFixed(0));
        }else{
          $('#percentage_hike').val('');
      }
      if (window.refreshCommercialSummary) {
        window.refreshCommercialSummary();
      }
 }

 function ectc(){
     if (window.isFreeCommercialMode && window.isFreeCommercialMode()) {
        $('#percentage_hike').val('');
        if (window.refreshCommercialSummary) {
          window.refreshCommercialSummary();
        }
        return;
     }

     var currentCTC = parseFloat($('#pay_annual').val());
     var percentage_hike = $('#percentage_hike').val();
     if (isNaN(currentCTC)) {
        if (window.refreshCommercialSummary) {
          window.refreshCommercialSummary();
        }
        return;
     }
     var amount = currentCTC*percentage_hike/100;
     $('#bill_annual').val(amount+currentCTC);
     if (window.syncBillFieldsFromAnnual) {
        window.syncBillFieldsFromAnnual();
     }
     if (window.refreshCommercialSummary) {
        window.refreshCommercialSummary();
     }
     
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
    // handleFileInputChange replaced by uploadWithProgress zero-temp pipeline above
    // document.getElementById('resume').addEventListener('change', handleFileInputChange);



   

   
</script>
@endpush
