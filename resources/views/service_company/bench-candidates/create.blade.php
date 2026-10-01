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
    <div class="talent-topbar{{ @$page_event == 'EDIT' ? ' talent-topbar--edit' : '' }}">
      <div class="talent-topbar-copy">
        <div class="talent-topbar-title-row">
          <h1>Add In-House Talent</h1>
        </div>
        <p>Keep your best people ready for the right opportunities.</p>
      </div>
      @if(@$page_event != 'EDIT')
      <div class="talent-topbar-marketplace" data-stage="bench" role="status" aria-live="polite">
        <span class="talent-topbar-marketplace-icon"><i class="fa-solid fa-store" aria-hidden="true"></i></span>
        <span class="talent-topbar-marketplace-copy">
          <strong>Marketplace: <span id="talent-marketplace-status">Will be published</span></strong>
          <small id="talent-marketplace-message">Visible to clients.</small>
        </span>
      </div>
      @endif
      <div class="talent-topbar-actions">
        @php 
            if(@$page_event == 'EDIT'){ 
              echo '<button type="button" name="edit-form" id="edit-form" value="update" class="talent-topbar-primary">Update Candidate <i class="fa-solid fa-arrow-right"></i></button>';
            }else{
              echo '<button type="button" name="save" id="save-form" value="save" class="talent-topbar-primary talent-stage-action--bench"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> <span class="talent-stage-action-label">Save to Bench Stage</span></button><small class="talent-topbar-action-hint" id="talent-stage-action-hint">Save talent and publish to marketplace</small>';
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
      <div class="app" id="talent_create_app">
       
        <!-- MAIN -->
        <main class="mc">

          
          <!-- Form -->
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="edit_token" value="{{ @$application->id }}"/>
            <input type="hidden" name="avatar_remove" />
            <input type="hidden" name="resumeCheck" value="{{ @$resumeCheck }}" id="resumeCheck" />
            <input type="hidden" name="resume_session_uuid" id="resume_session_uuid" value="">
            <input type="hidden" name="client_submission_uuid" id="client_submission_uuid" value="">
            <input type="hidden" name="expected_aggregate_version" id="expected_aggregate_version" value="{{ @$application->aggregate_version ?? 1 }}">
            <input type="hidden" name="expected_resume_version" id="expected_resume_version" value="{{ @$application->resume_version ?? 1 }}">
            {{-- CRITICAL: 8 Synchronized Commercial Inputs (Defense against commit 16b825d5 regression) --}}
            <input type="hidden" name="hidden_pay_annual" id="hidden_pay_annual" value="{{ @$application->ctc_yearly }}">
            <input type="hidden" name="hidden_pay_monthly" id="hidden_pay_monthly" value="{{ @$application->ctc_monthly }}">
            <input type="hidden" name="hidden_pay_rate_amount" id="hidden_pay_rate_amount" value="{{ @$application->ctc_hourly }}">
            <input type="hidden" name="hidden_bill_annual" id="hidden_bill_annual" value="{{ @$application->ectc_yearly }}">
            <input type="hidden" name="hidden_bill_monthly" id="hidden_bill_monthly" value="{{ @$application->ectc_monthly }}">
            <input type="hidden" name="hidden_bill_rate_amount" id="hidden_bill_rate_amount" value="{{ @$application->ectc_hourly }}">
            <input type="hidden" name="hidden_percentage_hike" id="hidden_percentage_hike" value="{{ @$application->percentage_hike }}">
            <input type="hidden" name="hidden_comission_type" id="hidden_comission_type" value="{{ @$application->comission_type ?? 2 }}">
          <div class="fc">
            @if(@$page_event != 'EDIT')
            {{-- The selected stage is submitted with the existing candidate form; placement requires its separate client workflow. --}}
            <section class="fs talent-stage-card" aria-labelledby="talent-stage-title">
              <div class="talent-stage-heading">
                <div class="talent-stage-heading-copy">
                  <span class="talent-stage-heading-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                  <span><strong id="talent-stage-title">Talent Stage <span class="rq">*</span></strong><small>Select where this talent currently stands in your internal talent lifecycle.</small></span>
                </div>
                <div class="talent-stage-guidance"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>Each stage has a default marketplace status. You can publish talent to the marketplace only when the stage is set to Bench.</span></div>
              </div>
              <div class="talent-stage-options">
                <label class="talent-stage-option talent-stage-option--pool">
                  <input type="radio" name="talent_stage" value="1">
                  <span class="talent-stage-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Pool</strong><small>Newly added to talent pool</small></span>
                  <span class="talent-stage-indicator" aria-hidden="true"></span>
                </label>
                <label class="talent-stage-option talent-stage-option--bench is-selected">
                  <input type="radio" name="talent_stage" value="2" checked>
                  <span class="talent-stage-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Bench</strong><small>Available for opportunities</small></span>
                  <span class="talent-stage-indicator" aria-hidden="true"></span>
                </label>
                <label class="talent-stage-option talent-stage-option--hold">
                  <input type="radio" name="talent_stage" value="4">
                  <span class="talent-stage-icon"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Hold</strong><small>Temporarily unavailable</small></span>
                  <span class="talent-stage-indicator" aria-hidden="true"></span>
                </label>
                <button type="button" class="talent-stage-option talent-stage-option--placed" id="talent-stage-placed" aria-label="Placed: requires a client placement">
                  <span class="talent-stage-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Placed</strong><small>Assigned to a client</small></span>
                  <span class="talent-stage-indicator" aria-hidden="true"></span>
                </button>
                <label class="talent-stage-option talent-stage-option--exited">
                  <input type="radio" name="talent_stage" value="5">
                  <span class="talent-stage-icon"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span>
                  <span class="talent-stage-copy"><strong>Exited</strong><small>No longer with the company</small></span>
                  <span class="talent-stage-indicator" aria-hidden="true"></span>
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
                                <div class="up-info-banner-left">
                                    <span class="up-info-banner-check">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    </span>
                                    <div class="up-info-banner-text">
                                        <strong id="upInfoBannerTitle">Resume Uploaded Successfully using method A</strong>
                                        <small>We have parsed the resume and extracted the talent details.</small>
                                    </div>
                                </div>
                                <button type="button" class="up-info-banner-close" aria-label="Dismiss banner">
                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="up-file-card">
                                <div class="up-file-card-left">
                                    <div class="up-file-badge">
                                        <i class="fa-regular fa-file-pdf" id="upFileBadgeIcon" aria-hidden="true"></i>
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
                                        <span>Delete</span>
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
                            <div class="up-detail" id="upRetryWrap" style="display:none;margin-top:8px;">
                                <button type="button" id="upRetryBtn" style="font-size:12px;padding:4px 12px;border:1px solid #f44336;color:#f44336;background:transparent;border-radius:4px;cursor:pointer;">Retry Parse</button>
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
                        <small>A concise summary of this talent's expertise and experience.</small>
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
                <input type="hidden" name="resume_upload_pipeline" id="resume_upload_pipeline" value="">
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.</span> </div>
                <!--end::Input group-->
              </div>
            </div>
            <!--end::Upload Photo & Resume/CV-->

            <!-- Basic Details -->
            <div class="fs bd-card bd-card--candidate-profile" id="bd-candidate-profile-card">
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
                    <label>Professional Email (Not Disclosed to Client) <span class="rq">*</span></label>
                    <div class="bd-control">
                      <i class="fa-regular fa-envelope"></i>
                      <input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email address" />
                    </div>
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Mobile Number (Not Disclosed to Client) <span class="rq">*</span></label>
                    <x-jero-phone-input
                        id="phone"
                        name="phone"
                        countryCodeName="primary_country_code"
                        countryCodeId="primary_country_code"
                        :value="@$application->phone"
                        :countryCode="@$phone_code"
                        label=""
                        :required="true"
                        placeholder="Enter mobile number" />
                  </div>
                </div>

                <div class="bd-grid bd-grid--three">
                  {{-- Nationality: Reordered to Box 1 in Row 2 --}}
                  <div class="fg fv-row bd-field">
                    <label>Nationality <span class="rq">*</span></label>
                    <div class="bd-control bd-control--select">
                      <i class="fa-regular fa-flag"></i>
                      @php
                        // Tenant HQ Country Mapping to Demonym (Sensible Defaulting)
                        $countryToDemonym = [
                            'in' => 'Indian',
                            'us' => 'American',
                            'gb' => 'British',
                            'uk' => 'British',
                            'ca' => 'Canadian',
                            'au' => 'Australian',
                            'ae' => 'Emirati',
                            'sg' => 'Singaporean',
                            'de' => 'German',
                            'fr' => 'French',
                            'nl' => 'Dutch',
                            'ie' => 'Irish',
                            'sa' => 'Saudi',
                        ];
                        $tenantDefaultNat = $countryToDemonym[strtolower($country_code ?? 'in')] ?? 'Indian';
                        $selectedNationality = @$application->nationality ?: ((@$page_event == 'EDIT' && !empty(@$application->id)) ? '' : $tenantDefaultNat);

                        // ⭐ Top 10 Popular Nationalities / Talent Hubs with Country Synonyms for Instant Search
                        $topPopularNationalities = [
                            'Indian'      => ['label' => '🇮🇳 Indian (India)', 'synonyms' => 'india, bharat, hindustan, in, ind'],
                            'American'    => ['label' => '🇺🇸 American (United States)', 'synonyms' => 'united states, usa, america, us, u.s., u.s.a.'],
                            'British'     => ['label' => '🇬🇧 British (United Kingdom)', 'synonyms' => 'united kingdom, uk, u.k., britain, england, scotland, wales, gb'],
                            'Canadian'    => ['label' => '🇨🇦 Canadian (Canada)', 'synonyms' => 'canada, ca, can'],
                            'Australian'  => ['label' => '🇦🇺 Australian (Australia)', 'synonyms' => 'australia, au, aus, oz'],
                            'Emirati'     => ['label' => '🇦🇪 Emirati (United Arab Emirates)', 'synonyms' => 'united arab emirates, uae, u.a.e., dubai, abu dhabi, sharjah, ae'],
                            'Singaporean' => ['label' => '🇸🇬 Singaporean (Singapore)', 'synonyms' => 'singapore, sg, sgp'],
                            'German'      => ['label' => '🇩🇪 German (Germany)', 'synonyms' => 'germany, deutschland, de, deu'],
                            'French'      => ['label' => '🇫🇷 French (France)', 'synonyms' => 'france, fr, fra'],
                            'Dutch'       => ['label' => '🇳🇱 Dutch (Netherlands)', 'synonyms' => 'netherlands, holland, nl, nld, amsterdam'],
                            'Irish'       => ['label' => '🇮🇪 Irish (Ireland)', 'synonyms' => 'ireland, eire, ie, irl, dublin'],
                            'Saudi'       => ['label' => '🇸🇦 Saudi (Saudi Arabia)', 'synonyms' => 'saudi arabia, ksa, sa, sau, riyadh'],
                            'Other'       => ['label' => '🌐 Other / Non-Disclosed', 'synonyms' => 'other, international, non disclosed, unknown'],
                        ];

                        // Complete 195+ UN Sovereign Demonyms with Country Name Synonyms
                        $allNationalities = [
                            'Afghan' => 'Afghanistan', 'Albanian' => 'Albania', 'Algerian' => 'Algeria', 'Andorran' => 'Andorra', 'Angolan' => 'Angola', 
                            'Antiguan' => 'Antigua and Barbuda', 'Argentine' => 'Argentina', 'Armenian' => 'Armenia', 'Australian' => 'Australia', 'Austrian' => 'Austria',
                            'Azerbaijani' => 'Azerbaijan', 'Bahamian' => 'Bahamas', 'Bahraini' => 'Bahrain', 'Bangladeshi' => 'Bangladesh', 'Barbadian' => 'Barbados', 
                            'Belarusian' => 'Belarus', 'Belgian' => 'Belgium', 'Belizean' => 'Belize', 'Beninese' => 'Benin', 'Bhutanese' => 'Bhutan',
                            'Bolivian' => 'Bolivia', 'Bosnian' => 'Bosnia and Herzegovina', 'Botswanan' => 'Botswana', 'Brazilian' => 'Brazil', 'British' => 'United Kingdom, UK, Britain', 
                            'Bruneian' => 'Brunei', 'Bulgarian' => 'Bulgaria', 'Burkinabe' => 'Burkina Faso', 'Burmese' => 'Myanmar, Burma', 'Burundian' => 'Burundi',
                            'Cambodian' => 'Cambodia', 'Cameroonian' => 'Cameroon', 'Canadian' => 'Canada', 'Cape Verdean' => 'Cape Verde', 'Central African' => 'Central African Republic', 
                            'Chadian' => 'Chad', 'Chilean' => 'Chile', 'Chinese' => 'China, PRC', 'Colombian' => 'Colombia', 'Comorian' => 'Comoros',
                            'Congolese' => 'Congo', 'Costa Rican' => 'Costa Rica', 'Croatian' => 'Croatia', 'Cuban' => 'Cuba', 'Cypriot' => 'Cyprus', 
                            'Czech' => 'Czech Republic, Czechia', 'Danish' => 'Denmark', 'Djiboutian' => 'Djibouti', 'Dominican' => 'Dominican Republic', 'Dutch' => 'Netherlands, Holland',
                            'East Timorese' => 'East Timor', 'Ecuadorean' => 'Ecuador', 'Egyptian' => 'Egypt', 'Emirati' => 'United Arab Emirates, UAE, Dubai', 'Equatorial Guinean' => 'Equatorial Guinea', 
                            'Eritrean' => 'Eritrea', 'Estonian' => 'Estonia', 'Ethiopian' => 'Ethiopia', 'Fijian' => 'Fiji', 'Filipino' => 'Philippines',
                            'Finnish' => 'Finland', 'French' => 'France', 'Gabonese' => 'Gabon', 'Gambian' => 'Gambia', 'Georgian' => 'Georgia', 
                            'German' => 'Germany, Deutschland', 'Ghanaian' => 'Ghana', 'Greek' => 'Greece', 'Grenadian' => 'Grenada', 'Guatemalan' => 'Guatemala',
                            'Guinean' => 'Guinea', 'Guyanese' => 'Guyana', 'Haitian' => 'Haiti', 'Honduran' => 'Honduras', 'Hungarian' => 'Hungary', 
                            'Icelander' => 'Iceland', 'Indian' => 'India, Bharat', 'Indonesian' => 'Indonesia', 'Iranian' => 'Iran', 'Iraqi' => 'Iraq',
                            'Irish' => 'Ireland, Eire', 'Israeli' => 'Israel', 'Italian' => 'Italy', 'Ivorian' => 'Ivory Coast, Cote dIvoire', 'Jamaican' => 'Jamaica', 
                            'Japanese' => 'Japan', 'Jordanian' => 'Jordan', 'Kazakh' => 'Kazakhstan', 'Kenyan' => 'Kenya', 'Kuwaiti' => 'Kuwait',
                            'Kyrgyz' => 'Kyrgyzstan', 'Laotian' => 'Laos', 'Latvian' => 'Latvia', 'Lebanese' => 'Lebanon', 'Liberian' => 'Liberia', 
                            'Libyan' => 'Libya', 'Liechtenstein citizen' => 'Liechtenstein', 'Lithuanian' => 'Lithuania', 'Luxembourgish' => 'Luxembourg', 'Macedonian' => 'North Macedonia',
                            'Malagasy' => 'Madagascar', 'Malawian' => 'Malawi', 'Malaysian' => 'Malaysia', 'Maldivian' => 'Maldives', 'Malian' => 'Mali', 
                            'Maltese' => 'Malta', 'Marshallese' => 'Marshall Islands', 'Mauritanian' => 'Mauritania', 'Mauritian' => 'Mauritius', 'Mexican' => 'Mexico',
                            'Micronesian' => 'Micronesia', 'Moldovan' => 'Moldova', 'Monegasque' => 'Monaco', 'Mongolian' => 'Mongolia', 'Montenegrin' => 'Montenegro', 
                            'Moroccan' => 'Morocco', 'Mozambican' => 'Mozambique', 'Namibian' => 'Namibia', 'Nauruan' => 'Nauru', 'Nepalese' => 'Nepal',
                            'New Zealander' => 'New Zealand, NZ', 'Nicaraguan' => 'Nicaragua', 'Nigerian' => 'Nigeria', 'Nigerien' => 'Niger', 'North Korean' => 'North Korea', 
                            'Norwegian' => 'Norway', 'Omani' => 'Oman', 'Pakistani' => 'Pakistan', 'Palauan' => 'Palau', 'Palestinian' => 'Palestine',
                            'Panamanian' => 'Panama', 'Papua New Guinean' => 'Papua New Guinea', 'Paraguayan' => 'Paraguay', 'Peruvian' => 'Peru', 'Polish' => 'Poland', 
                            'Portuguese' => 'Portugal', 'Qatari' => 'Qatar', 'Romanian' => 'Romania', 'Russian' => 'Russia', 'Rwandan' => 'Rwanda',
                            'Saint Lucian' => 'Saint Lucia', 'Salvadoran' => 'El Salvador', 'Samoan' => 'Samoa', 'San Marinese' => 'San Marino', 'Sao Tomean' => 'Sao Tome and Principe', 
                            'Saudi' => 'Saudi Arabia, KSA', 'Senegalese' => 'Senegal', 'Serbian' => 'Serbia', 'Seychellois' => 'Seychelles', 'Sierra Leonean' => 'Sierra Leone',
                            'Singaporean' => 'Singapore', 'Slovak' => 'Slovakia', 'Slovenian' => 'Slovenia', 'Solomon Islander' => 'Solomon Islands', 'Somali' => 'Somalia', 
                            'South African' => 'South Africa, RSA', 'South Korean' => 'South Korea, Korea', 'South Sudanese' => 'South Sudan', 'Spanish' => 'Spain', 'Sri Lankan' => 'Sri Lanka',
                            'Sudanese' => 'Sudan', 'Surinamese' => 'Suriname', 'Swazi' => 'Eswatini, Swaziland', 'Swedish' => 'Sweden', 'Swiss' => 'Switzerland', 
                            'Syrian' => 'Syria', 'Taiwanese' => 'Taiwan', 'Tajik' => 'Tajikistan', 'Tanzanian' => 'Tanzania', 'Thai' => 'Thailand',
                            'Togolese' => 'Togo', 'Tongan' => 'Tonga', 'Trinidadian' => 'Trinidad and Tobago', 'Tunisian' => 'Tunisia', 'Turkish' => 'Turkey, Turkiye', 
                            'Turkmen' => 'Turkmenistan', 'Tuvaluan' => 'Tuvalu', 'Ugandan' => 'Uganda', 'Ukrainian' => 'Ukraine', 'Uruguayan' => 'Uruguay',
                            'Uzbek' => 'Uzbekistan', 'Vanuatu' => 'Vanuatu', 'Vatican citizen' => 'Vatican City', 'Venezuelan' => 'Venezuela', 'Vietnamese' => 'Vietnam', 
                            'Yemeni' => 'Yemen', 'Zambian' => 'Zambia', 'Zimbabwean' => 'Zimbabwe'
                        ];
                      @endphp
                      <select name="nationality" id="nationality">
                        <option value="">Select nationality</option>
                        <optgroup label="⭐ Top 10 Popular Nationalities">
                          @foreach($topPopularNationalities as $natKey => $natItem)
                            <option value="{{ $natKey }}" data-synonyms="{{ $natItem['synonyms'] }}" {{ ((string)$selectedNationality === (string)$natKey) ? 'selected="selected"' : '' }}>{{ $natItem['label'] }}</option>
                          @endforeach
                        </optgroup>
                        <optgroup label="🌐 All Sovereign Demonyms (A–Z)">
                          @foreach($allNationalities as $demonym => $countrySynonyms)
                            <option value="{{ $demonym }}" data-synonyms="{{ $countrySynonyms }}" {{ ((string)$selectedNationality === (string)$demonym && !isset($topPopularNationalities[$demonym])) ? 'selected="selected"' : '' }}>{{ $demonym }}</option>
                          @endforeach
                        </optgroup>
                      </select>
                    </div>
                  </div>

                  {{-- Gender: Reordered to Box 2 in Row 2 --}}
                  <div class="fg fv-row bd-field">
                    <label>Gender <span class="rq">*</span></label>
                    <div class="bd-control bd-control--select">
                      <i class="fa-regular fa-user"></i>
                      <select name="gender" id="gender">
                        <option value="">Select gender</option>
                        <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option>
                        <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                      </select>
                    </div>
                  </div>

                  {{-- Date of Birth (Optional): Reordered to Box 3 in Row 2 --}}
                  <div class="fg fv-row bd-field">
                    <label>Date of Birth <span class="opt text-muted fw-normal fs-8">(Optional)</span></label>
                    <div class="bd-control">
                      <i class="fa-regular fa-calendar-days"></i>
                      <input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()" placeholder="Select date" />
                    </div>
                  </div>
                </div>

                {{-- Row 3: Current Location & Highest Qualification --}}
                <div class="bd-grid bd-grid--two">
                  {{-- Current Location: Reordered to Box 1 in Row 3 --}}
                  <div class="fg fv-row bd-field">
                    <label>Current Location <span class="rq">*</span></label>
                    <div class="bd-control bd-control--has-combobox">
                      <i class="fa-solid fa-location-dot"></i>
                      {{-- Canonical Form Fields (0% MySQL Reads, Server Trust Boundary Compliant) --}}
                      <input type="hidden" name="location_city_id" id="location_city_id" value="{{ @$application->city_id ?? '' }}">
                      <input type="hidden" name="location_state_id" id="location_state_id" value="{{ @$application->state ?? '' }}">
                      <input type="hidden" name="location_country_id" id="location_country_id" value="{{ @$application->country ?? '' }}">
                      <input type="hidden" name="location_canonical_label" id="location_canonical_label" value="{{ @$default_loc ?? '' }}">
                      <input type="hidden" name="location_sig" id="location_sig" value="">

                      {{-- Backward-Compatible Bridge Payload for FormValidation & Legacy Interop --}}
                      <input type="hidden" name="kt_tagify_work_location" id="kt_tagify_work_location" value="{{ @$default_loc ? json_encode([['value' => @$default_loc]]) : '' }}">

                      {{-- JERO Native Custom Combobox Mount Point --}}
                      <div class="jero-custom-select" id="jero-cs-location" data-field="location">
                        <button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false" aria-controls="jero-cs-location-list">
                          <span class="jero-cs-left">
                            <span class="jero-cs-icon"><i class="fa-solid fa-location-dot"></i></span>
                            <span class="jero-cs-value jero-cs-placeholder">Search city, state or country</span>
                          </span>
                          <span class="jero-cs-right">
                            <span class="jero-cs-trigger-clear" title="Clear selection" style="display:none;">&times;</span>
                            <i class="fa-solid fa-chevron-down jero-cs-arrow"></i>
                          </span>
                        </button>
                        <div class="jero-cs-dropdown" style="display: none;">
                          <div class="jero-cs-search-box">
                            <i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>
                            <input type="text" class="jero-cs-search-input" placeholder="Search city, state or country..." autocomplete="off" />
                            <i class="fa-solid fa-spinner jero-cs-search-spinner" style="display:none;"></i>
                            <button type="button" class="jero-cs-search-clear" style="display:none;">&times;</button>
                          </div>
                          <div class="jero-cs-list" id="jero-cs-location-list" role="listbox"></div>
                          <div class="jero-cs-empty is-hidden"><i class="fa-regular fa-face-frown"></i> No locations matching your search.</div>
                        </div>
                      </div>
                    </div>
                    {{-- Disambiguation Ribbon for NEEDS_CONFIRMATION State --}}
                    <div id="jero-location-disambiguation" class="jero-disambiguation-bar" style="display:none;"></div>
                  </div>

                  {{-- Highest Qualification: Reordered to Box 2 in Row 3 --}}
                  <div class="fg fv-row bd-field">
                    <label>Highest Qualification <span class="rq">*</span></label>
                    <div class="bd-control bd-control--select">
                      <i class="fa-solid fa-graduation-cap"></i>
                      <select name="qualification" id="qualification">
                        <option value="">Select qualification</option>
                        @php
                          // Map incoming database qualification records by ID
                          $qualMap = [];
                          if (!empty($qualification)) {
                              foreach ($qualification as $qItem) {
                                  $qualMap[$qItem->id] = $qItem;
                              }
                          }

                          // 7-Category Global Education Standard with Regional Prov & Global Alias Taxonomy (Option A - Worldwide SaaS)
                          $educationCategories = [
                              "🎓 Bachelor's / Undergraduate Degrees" => [
                                  13 => ['label' => 'B.Tech / B.E (Engineering & Technology)', 'badge' => 'Engineering / Tech', 'regions' => 'Indian,Commonwealth,Global', 'aliases' => 'btech,b.tech,b tech,be,b.e,bachelor of technology,bachelor of engineering,beng,b.eng,bse,bs engineering,diplome ingenieur,diplom ingenieur,software engineer,computer science engineering,cse,mechanical,electrical,civil'],
                                  12 => ['label' => 'B.S. / B.Sc (Science, IT & Computing / Licence)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'bs,b.s.,bsc,b.sc,bachelor of science,bachelor science,bs cs,bsc cs,b sc,bsc it,bs it,information technology,computer science,data science,cybersecurity,cyber security,cloud computing,artificial intelligence,software development,licence,licenciatura,bac+3,grado'],
                                  16 => ['label' => 'BCA (Computer Applications / Software)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'bca,b.c.a.,bachelor of computer applications,bachelor computer applications,computer programming,web development'],
                                  5  => ['label' => 'BBA / BMS (Business Administration)', 'badge' => 'Business / Global', 'regions' => 'Indian,American,Canadian,British,Australian,Global', 'aliases' => 'bba,b.b.a.,bms,b.m.s.,bachelor of business administration,bachelor business,management studies,business management,bba marketing,bba finance'],
                                  6  => ['label' => 'B.Com (Commerce, Accounting & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Canadian,Australian,Commonwealth,Global', 'aliases' => 'bcom,b.com,bachelor of commerce,bachelor commerce,commerce,accounting,finance,banking'],
                                  3  => ['label' => 'B.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ba,b.a.,bachelor of arts,bachelor arts,humanities,economics,psychology,english,journalism,mass communication,history'],
                                  7  => ['label' => 'B.Des (Design & UX/UI)', 'badge' => 'Design / Global', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bdes,b.des,bachelor of design,bachelor design,ui ux,product design,graphic design,fashion design,industrial design'],
                                  18 => ['label' => 'BFA (Fine Arts & Digital Media)', 'badge' => 'Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bfa,b.f.a.,bachelor of fine arts,visual arts,animation,digital media,illustration'],
                                  4  => ['label' => 'B.Arch (Architecture & Urban Planning)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'barch,b.arch,bachelor of architecture,architectural design,urban planning'],
                                  11 => ['label' => 'B.Pharma / Nursing / Allied Health (Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bpharma,b.pharma,b pharm,b.pharm,bachelor of pharmacy,pharmacy,nursing,b.sc nursing,bsc nursing,rn,registered nurse,bpt,physiotherapy,bachelor of physiotherapy,allied health,paramedical,pharm d,pharm.d'],
                                  19 => ['label' => 'BHM (Hotel & Hospitality Management)', 'badge' => 'Hospitality', 'regions' => 'Indian,Global', 'aliases' => 'bhm,b.h.m.,bachelor of hotel management,hospitality,tourism,culinary arts'],
                                  8  => ['label' => 'B.Ed (Education & Pedagogy)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'bed,b.ed,bachelor of education,teaching,pedagogy'],
                                  9  => ['label' => 'B.El.Ed (Elementary Education)', 'badge' => 'Education', 'regions' => 'Indian', 'aliases' => 'beled,b.el.ed,bachelor of elementary education,primary teaching'],
                                  10 => ['label' => 'B.P.Ed (Physical Education & Sports)', 'badge' => 'Sports Science', 'regions' => 'Indian,Global', 'aliases' => 'bped,b.p.ed,bachelor of physical education,sports science,kinesiology'],
                                  21 => ['label' => 'BVSC / DVM (Veterinary Science & Medicine)', 'badge' => 'Veterinary', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bvsc,b.v.sc,bachelor of veterinary science,dvm,veterinary medicine,veterinarian'],
                                  61 => ['label' => 'Other Bachelor\'s / Undergraduate Degree', 'badge' => 'Bachelor\'s / Other', 'regions' => 'Global', 'aliases' => 'other bachelor,other bachelors,other undergraduate,bachelors other,custom bachelor,unlisted bachelor'],
                              ],
                              "🎓 Master's / Postgraduate Degrees" => [
                                  39 => ['label' => 'M.S. / M.Sc / M.Eng (Science, IT & Engineering / Master 2)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'ms,m.s.,msc,m.sc,master of science,master science,ms cs,msc cs,m s,m sc,meng,m.eng,master of engineering,data science,ai,artificial intelligence,machine learning,deep learning,cybersecurity,cloud architecture,big data,bioinformatics,master 2,bac+5,magister'],
                                  40 => ['label' => 'M.Tech / M.E (Engineering & Advanced Tech)', 'badge' => 'Engineering / Postgrad', 'regions' => 'Indian,Global', 'aliases' => 'mtech,m.tech,m tech,me,m.e,master of technology,master of engineering,meng,m.eng,postgraduate engineering,vlsi,embedded,robotics'],
                                  41 => ['label' => 'MBA / PGDM (Business Administration & Strategy)', 'badge' => 'Management / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'mba,m.b.a.,pgdm,p.g.d.m.,master of business administration,master business administration,executive mba,emba,insead,wharton,hbs,iim,post graduate diploma in management,business management,strategy,consulting'],
                                  42 => ['label' => 'MCA (Master of Computer Applications)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'mca,m.c.a.,master of computer applications,master computer applications,software engineering,computer science'],
                                  43 => ['label' => 'MCM (Master of Computer Management)', 'badge' => 'Management & IT', 'regions' => 'Indian', 'aliases' => 'mcm,m.c.m.,master of computer management,it management'],
                                  35 => ['label' => 'M.Com (Commerce, Economics & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mcom,m.com,master of commerce,economics,financial management,accounting'],
                                  32 => ['label' => 'M.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ma,m.a.,master of arts,humanities,sociology,psychology,political science,international relations,literature'],
                                  36 => ['label' => 'M.Des (Master of Design / UX)', 'badge' => 'Design / Postgrad', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mdes,m.des,master of design,interaction design,ux design,product design'],
                                  45 => ['label' => 'MFA (Fine Arts & Creative Media)', 'badge' => 'Fine Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mfa,m.f.a.,master of fine arts,creative media,visual arts,cinematography'],
                                  33 => ['label' => 'M.Arch (Master of Architecture)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'march,m.arch,master of architecture,landscape architecture,urban design'],
                                  38 => ['label' => 'M.Pharma / Pharm.D / MPT (Advanced Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mpharma,m.pharma,m pharm,m.pharm,master of pharmacy,pharm d,pharm.d,doctor of pharmacy,mpt,master of physiotherapy,pharmacology,clinical research'],
                                  37 => ['label' => 'M.Ed (Master of Education)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'med,m.ed,master of education,educational leadership,curriculum'],
                                  47 => ['label' => 'MVSC (Master of Veterinary Science)', 'badge' => 'Veterinary Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mvsc,m.v.sc,master of veterinary science,veterinary surgery'],
                                  30 => ['label' => 'Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS)', 'badge' => 'Integrated / Dual', 'regions' => 'Indian,European,American,Global', 'aliases' => 'integrated pg,dual degree,integrated mtech,integrated btech mtech,5 year integrated,bs ms,b.s. m.s.,btech mtech'],
                                  62 => ['label' => 'Other Master\'s / Postgraduate Degree', 'badge' => 'Master\'s / Other', 'regions' => 'Global', 'aliases' => 'other master,other masters,other postgraduate,masters other,custom master,unlisted master,mph,msw,mphil,m.phil'],
                              ],
                              "🏛️ Doctorate & Research Degrees" => [
                                  52 => ['label' => 'Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy)', 'badge' => 'Doctorate / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'phd,ph.d.,ph.d,doctorate,doctor of philosophy,dphil,d.phil,postdoc,post doc,post doctoral,fellowship,research scholar,promotion,dr'],
                                  64 => ['label' => 'Other Doctorate / Post-Doctoral Degree', 'badge' => 'Doctorate / Other', 'regions' => 'Global', 'aliases' => 'other doctorate,other phd,postdoc,post-doctoral,dba,d.sc,doctor of business administration,research fellow'],
                              ],
                              "📜 Associate Degrees & Diplomas" => [
                                  22 => ['label' => 'Associate Degree / Polytechnic Diploma (A.A. / A.S. / Diploma)', 'badge' => 'Polytechnic / Assoc', 'regions' => 'Indian,American,Canadian,Australian,European,Global', 'aliases' => 'diploma,polytechnic,associate degree,associate of science,associate of arts,aa,a.a.,as,a.s.,aas,a.a.s.,community college,2 year degree,bts,dut,fachhochschule'],
                                  48 => ['label' => 'PG Diploma (Post Graduate Diploma / Certificate)', 'badge' => 'PG Diploma', 'regions' => 'Indian,Canadian,British,Commonwealth,Global', 'aliases' => 'pg diploma,pgd,pgdiploma,post graduate diploma,postgraduate certificate,post-degree diploma,canadian pg diploma'],
                                  25 => ['label' => 'HND / HNC (Higher National Diploma / Certificate)', 'badge' => 'UK / Commonwealth', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'hnd,hnc,higher national diploma,higher national certificate,vocational qualification,btec'],
                                  63 => ['label' => 'Other Associate / Diploma / Vocational Degree', 'badge' => 'Diploma / Other', 'regions' => 'Global', 'aliases' => 'other diploma,other associate,vocational diploma,technical certificate,custom diploma,unlisted diploma'],
                              ],
                              "⚖️ Professional, Legal & Medical Degrees" => [
                                  26 => ['label' => 'CA / CPA / CFA / ACCA (Finance & Accounting)', 'badge' => 'Finance / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ca,cpa,cfa,acca,chartered accountant,chartered accountancy,certified public accountant,chartered financial analyst,cima,cost accountant,accounting,finance,audit'],
                                  27 => ['label' => 'CS (Company Secretary / Corporate Governance)', 'badge' => 'Corporate Law', 'regions' => 'Indian,British,Commonwealth', 'aliases' => 'cs,c.s.,company secretary,company secretaryship,corporate governance,icsi'],
                                  29 => ['label' => 'ICWA / CMA (Cost & Management Accounting)', 'badge' => 'Cost Accounting', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'icwa,cma,cost accountant,cost and management accountant,cma usa,icmai'],
                                  23 => ['label' => 'LLB / J.D. (Law Degree / Juris Doctor)', 'badge' => 'Legal / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'llb,l.l.b.,bachelor of laws,jd,j.d.,juris doctor,law degree,bar admitted,attorney,lawyer,solicitor,advocate'],
                                  31 => ['label' => 'LLM (Master of Laws / Legal Postgrad)', 'badge' => 'Legal / Postgrad', 'regions' => 'Indian,American,Canadian,British,European,Commonwealth,Global', 'aliases' => 'llm,l.l.m.,master of laws,corporate law,international law,intellectual property'],
                                  24 => ['label' => 'MBBS / M.D. / D.O. (General Medicine & Surgery)', 'badge' => 'Medical / Surgery', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'mbbs,m.b.b.s.,bachelor of medicine,md,m.d.,doctor of medicine,do,d.o.,doctor of osteopathic medicine,physician'],
                                  46 => ['label' => 'MD / MS (Medical Specialty / Master of Surgery)', 'badge' => 'Medical Specialty', 'regions' => 'Indian,American,Canadian,British,Commonwealth,Global', 'aliases' => 'md,m.d.,ms general surgery,master of surgery,doctor of medicine,residency,medical specialty'],
                                  28 => ['label' => 'DM (Doctor of Medicine - Medical Super Specialty)', 'badge' => 'Super Specialty', 'regions' => 'Indian,Global', 'aliases' => 'dm,d.m.,super specialty,cardiology,neurology,oncology'],
                                  34 => ['label' => 'M.Ch (Surgical Super Specialty)', 'badge' => 'Surgical Specialty', 'regions' => 'Indian,Global', 'aliases' => 'mch,m.ch,magister chirurgiae,neurosurgery,cardiothoracic'],
                                  17 => ['label' => 'BDS / DDS (Dental Surgery & Medicine)', 'badge' => 'Dental / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bds,b.d.s.,dds,d.d.s.,dmd,d.m.d.,dental surgery,dentist,oral surgery'],
                                  44 => ['label' => 'MDS (Master of Dental Surgery)', 'badge' => 'Dental Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mds,m.d.s.,master dental surgery,orthodontics,prosthodontics'],
                                  15 => ['label' => 'BAMS (Ayurvedic Medicine & Surgery)', 'badge' => 'Ayurveda', 'regions' => 'Indian', 'aliases' => 'bams,b.a.m.s.,ayurveda,ayush'],
                                  20 => ['label' => 'BHMS (Homeopathic Medicine & Surgery)', 'badge' => 'Homeopathy', 'regions' => 'Indian', 'aliases' => 'bhms,b.h.m.s.,homeopathy,ayush'],
                                  14 => ['label' => 'B.U.M.S (Unani Medicine & Surgery)', 'badge' => 'Unani', 'regions' => 'Indian', 'aliases' => 'bums,b.u.m.s.,unani,ayush'],
                              ],
                              "🏫 Secondary & High School" => [
                                  2  => ['label' => '12th Standard / High School Diploma / A-Levels / GED', 'badge' => 'High School', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => '12th,12th standard,hsc,intermediate,high school,high school diploma,ged,abitur,baccalaureat,senior secondary'],
                                  1  => ['label' => '10th Standard / Matriculation / Secondary School', 'badge' => 'Secondary', 'regions' => 'Indian,Global', 'aliases' => '10th,10th standard,ssc,matric,matriculation,secondary school'],
                                  49 => ['label' => 'A Levels / Advanced Level (UK / International)', 'badge' => 'UK A-Levels', 'regions' => 'British,Commonwealth,European,Global', 'aliases' => 'a levels,a-levels,alevel,advanced level,gce a levels'],
                                  51 => ['label' => 'GCSE / O-Levels (UK / International)', 'badge' => 'UK GCSE', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gcse,o-levels,o levels,olevel,igcse,gce o level'],
                                  50 => ['label' => 'Vocational Secondary / GNVQ / Technical Certificate', 'badge' => 'Vocational', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gnvq,vocational secondary,nvq,btec level 2,technical certificate'],
                              ],
                              "🌐 Other Qualifications" => [
                                  60 => ['label' => 'Others / Intensive Bootcamp / Specialized Certification', 'badge' => 'Other / Bootcamp', 'regions' => 'Global,Indian,American,Canadian,British,Australian,European', 'aliases' => 'other,others,non disclosed,bootcamp,coding bootcamp,full stack,certifications,aws certified,pmp,scrum master'],
                              ],
                          ];

                          $selectedQualId = @$application->qualification;
                          $renderedIds = [];
                        @endphp

                        @foreach($educationCategories as $categoryLabel => $catItems)
                          <optgroup label="{{ $categoryLabel }}">
                            @foreach($catItems as $qId => $itemMeta)
                              @if(isset($qualMap[$qId]))
                                @php
                                  $renderedIds[] = $qId;
                                  $isSelected = ((string)$selectedQualId === (string)$qId);
                                @endphp
                                <option value="{{ $qId }}" 
                                        data-regions="{{ $itemMeta['regions'] }}"
                                        data-aliases="{{ $itemMeta['aliases'] }}"
                                        data-badge="{{ $itemMeta['badge'] }}"
                                        {{ $isSelected ? 'selected="selected"' : '' }}>
                                  {{ $itemMeta['label'] }}
                                </option>
                              @endif
                            @endforeach
                          </optgroup>
                        @endforeach

                        {{-- Fallback safety for any unmapped database record --}}
                        @php
                          $remainingItems = [];
                          foreach ($qualMap as $qId => $qObj) {
                              if (!in_array($qId, $renderedIds)) {
                                  $remainingItems[] = $qObj;
                              }
                          }
                        @endphp
                        @if(count($remainingItems) > 0)
                          <optgroup label="Additional Qualifications">
                            @foreach($remainingItems as $remItem)
                              <option value="{{ $remItem->id }}" data-regions="Global" data-aliases="" data-badge="Other" {{ ((string)$selectedQualId === (string)$remItem->id) ? 'selected="selected"' : '' }}>
                                {{ $remItem->name }}
                              </option>
                            @endforeach
                          </optgroup>
                        @endif
                      </select>
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
                <input type="hidden" value="{{ @$application->skills }}" id="skills" />
                <input type="hidden" name="secondrary_email" id="secondrary_email" value="{{ @$application->secondrary_email }}" />
                <input type="hidden" name="secondary_mobile" id="secondary_mobile" value="{{ @$application->secondary_mobile }}" />
                <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code" name="secondary_country_code" />
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
                    <label>Client Engagement Model <span class="rq">*</span></label>
                    <div class="pd-control pd-control--select">
                      <i class="fa-solid fa-handshake"></i>
                      <select name="employmentType" id="EmploymentType">
                        <option value="">Select engagement model</option>
                        <option value="Contract" {{ (@$application->employmentType==='Contract' || (@$page_event != 'EDIT' && empty(@$application->employmentType))) ? 'selected' : '' }}>Contract / C2C / C2H / Staff Augmentation</option>
                        <option value="Full Time" {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Direct Hire / Permanent Placement</option>
                        <option value="Freelance" {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance / Fractional Consulting</option>
                        <option value="Part Time" {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part-Time Contract</option>
                      </select>
                    </div>
                    <small>Select how this talent will be deployed to clients in the marketplace.</small>
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
                      <input type="text" class="form-control form-control-solid" name="employee_number" id="employee_number" placeholder="Enter employee number" value="{{ @$application->employee_number }}">
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
                    <label class="aw-date-label d-inline-flex align-items-center" for="np_id_display">
                      Available From Date <span class="rq">*</span>
                      <span class="aw-date-info-trigger" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false" title="Click or hover for availability SLA guidance">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="aw-date-hover-card" role="tooltip">
                          <span class="aw-hover-card-header">
                            <span class="aw-hover-card-title">
                              <i class="fa-regular fa-calendar-check text-primary"></i>
                              <span>Deployment &amp; Availability SLA</span>
                            </span>
                            <span class="aw-hover-card-pill">Marketplace SLA</span>
                          </span>
                          <span class="aw-hover-card-content">
                            <span class="aw-hover-item">
                              <span class="aw-hover-icon aw-hover-icon--blue"><i class="fa-solid fa-magnifying-glass"></i></span>
                              <span class="aw-hover-text">
                                <strong>Marketplace Search Visibility &amp; Ranking</strong>
                                <small>Hiring clients filter candidates by this start date. Talent with verified accurate availability receives up to 3× higher shortlist volume.</small>
                              </span>
                            </span>
                            <span class="aw-hover-item">
                              <span class="aw-hover-icon aw-hover-icon--amber"><i class="fa-regular fa-clock"></i></span>
                              <span class="aw-hover-text">
                                <strong>Serving Notice Period (LWD + 1)</strong>
                                <small>If candidate is currently employed, select the first working day immediately following their official Last Working Day (LWD).</small>
                              </span>
                            </span>
                            <span class="aw-hover-item">
                              <span class="aw-hover-icon aw-hover-icon--emerald"><i class="fa-solid fa-bolt"></i></span>
                              <span class="aw-hover-text">
                                <strong>Immediate Joining (Bench Talent)</strong>
                                <small>If this talent is on bench and ready to deploy today, toggle "Immediate Joiner" above for instant priority matching.</small>
                              </span>
                            </span>
                          </span>
                          <span class="aw-hover-card-footer">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span>Guarantees verified availability SLA to eliminate deployment disputes with clients.</span>
                          </span>
                        </span>
                      </span>
                    </label>
                    <small class="aw-field-help">Enter the date from which this talent will be available.</small>
                    <div class="aw-date-wrap" id="snp-immediate" style="display:flex">
                      <input type="text" id="text_display" value="{{ ($snp_mode===1) ? 'Available From' : 'Immediate' }}" class="form-control form-control-solid" placeholder="" readonly>
                      <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select date" name="np_id_display" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" readonly data-bs-toggle="tooltip" data-bs-placement="top" title="Deployment start date visible to clients in marketplace search">
                      <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" value="{{ ($snp_last_working) ? date('Y-m-d',strtotime(@$snp_last_working)) : '' }}" style="opacity: 0; position: absolute; z-index: -1;top: 0;">
                    </div>
                    <div class="aw-date-guidance"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>This date will be used to show to clients in the talents marketplace.</span></div>
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
                        <input type="radio" name="wm" id="wh" {{ @$application->wm == 'hybrid' ? 'checked' : '' }} value="hybrid" />
                        <span class="aw-radio"></span>
                        <i class="fa-solid fa-building" aria-hidden="true"></i>
                        <span class="aw-work-copy"><strong>Hybrid</strong><small>Combination of office and remote.</small></span>
                      </label>
                      <label class="aw-work-option aw-work-option--office" for="wo">
                        <input type="radio" name="wm" id="wo" {{ (@$application->wm == 'office' || (@$page_event != 'EDIT' && empty(@$application->wm))) ? 'checked' : '' }} value="office" />
                        <span class="aw-radio"></span>
                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                        <span class="aw-work-copy"><strong>Work From Office (WFO)</strong><small>Office location based work.</small></span>
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
                      <select id="pay_rate_currency" name="pay_rate_currency" class="form-select form-select-solid br-currency-select" data-control="select2" data-placeholder="Search currency...">
                          @php
                              $current_ctc_cur = @$application->pay_rate_currency;

                              foreach($currency as $key => $val){
                                  $sel = '';
                                  if($current_ctc_cur === null || $current_ctc_cur === ''){
                                      if($currency_code == $val){
                                          $sel = 'selected="selected"';
                                      }
                                  } else {
                                      if((string)$current_ctc_cur === (string)$key || (string)$current_ctc_cur === (string)$val){
                                          $sel = 'selected="selected"';
                                      }
                                  }

                                  $cMeta = GeneralMethod::currencyEnterpriseMeta($val);
                                  $entLabel = $cMeta['label'];
                                  $keywords = $cMeta['keywords'];

                                  echo '<option value="'.$key.'" data-code="'.e($val).'" data-search="'.e($keywords).'" '.$sel.'>'.e($entLabel).'</option>';
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
                            $current_ectc_cur = @$application->bill_rate_currency;
                            foreach($currency as $key => $val){
                              $sel = '';
                              if($current_ectc_cur === null || $current_ectc_cur === ''){
                                  if($currency_code == $val){
                                      $sel = 'selected="selected"';
                                  }
                              } else {
                                  if((string)$current_ectc_cur === (string)$key || (string)$current_ectc_cur === (string)$val){
                                      $sel = 'selected="selected"';
                                  }
                              }

                              echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                            }
                        @endphp
                    </select>
                    <select name="current_th_cur" id="current_th_cur">
                        @php
                            $current_ectc_cur = @$application->bill_rate_currency;
                            foreach($currency as $key => $val){
                              $sel = '';
                              if($current_ectc_cur === null || $current_ectc_cur === ''){
                                  if($currency_code == $val){
                                      $sel = 'selected="selected"';
                                  }
                              } else {
                                  if((string)$current_ectc_cur === (string)$key || (string)$current_ectc_cur === (string)$val){
                                      $sel = 'selected="selected"';
                                  }
                              }

                              echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
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
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
                        </div>
                        <span class="form-text text-muted payrateannual"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="pay-monthly">
                        <label>Pay Rate (Monthly) <span class="rq">*</span><span id="pay_rate_monthly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
                        </div>
                        <span class="form-text text-muted payratemonthly"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="pay-hourly">
                        <label>Pay Rate (Hourly) <span class="rq">*</span><span id="pay_rate_hourly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="pay_rate_amount" id="pay_rate_amount" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
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
                      <!-- Free Service Bill Rate Hover Notification -->
                      <div class="br-free-bill-hover-notice" id="brFreeBillHoverNotice" style="display:none;" role="status" aria-live="polite">
                        <div class="br-free-bill-hover-inner">
                          <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                          <div class="br-free-bill-hover-text">
                            <strong>Client Bill Rate Not Applicable</strong>
                            <span>Free Service is selected for this talent. Under this commercial tier, client billing is completely waived and no charges are applied.</span>
                          </div>
                        </div>
                      </div>

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
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
                        </div>
                        <span class="form-text text-muted billrateannual"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="bill-monthly">
                        <label>Bill Rate (Monthly) <span class="rq">*</span><span id="bill_rate_monthly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
                        </div>
                        <span class="form-text text-muted billratemonthly"></span>
                      </div>
                      <div class="br-rate-field" data-rate-panel="bill-hourly">
                        <label>Bill Rate (Hourly) <span class="rq">*</span><span id="bill_rate_hourly_usd"></span></label>
                        <div class="ig">
                          <input type="number" name="bill_rate_amount" id="bill_rate_amount" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" />
                          <span class="ig-a changeTextCurrency">{{ $currency_code }}</span>
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
                        <span><em id="br_summary_diff_label">Margin Difference Amount</em><b id="br_summary_margin_diff">0</b></span>
                        <span><em id="br_summary_percent_label">Margin Percentage</em><b id="br_summary_percent">0%</b></span>
                        <span><em id="br_summary_model_label">Commercial Model</em><b id="br_summary_model">Paid Service</b></span>
                      </div>
                      <div class="br-margin-state">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <span><strong id="br_margin_status">Healthy Margin</strong><small id="br_margin_note">This margin looks good.</small></span>
                      </div>
                      <input type="hidden" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" />
                    </div>
                  </div>
                  <div class="br-comments-card">
                    <div class="br-comments-head">
                      <span class="br-comments-icon"><i class="fa-solid fa-comment-dots" aria-hidden="true"></i></span>
                      <span><strong>Comments (Optional)</strong><small>Add any commercial notes for internal reference.</small></span>
                    </div>
                    <textarea name="comment" id="comment" maxlength="500" placeholder="Enter comments about salary expectations, client specific rates or any other details..." rows="3">{{ @$application->comment }}</textarea>
                    <div class="cc" id="br_comment_count">0 / 500</div>
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
                   <span class="se-badge se-badge--req"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Min. 3 Skills Required &mdash; Each with Name, Experience &amp; Rating</span>
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
                      <th><svg viewBox="0 0 24 24" width="13" height="13" fill="#f59e0b" style="display:inline-block; vertical-align:-2px; margin-right:4px;" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Proficiency Rating <span class="rq">*</span></th>
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
              $rmInitialList = [];
              if (!empty($recruiterlist) && count($recruiterlist) > 0) {
                  foreach ($recruiterlist as $rItem) {
                      $rmInitialList[] = [
                          'id' => (string)$rItem->id,
                          'name' => (string)$rItem->name,
                          'email' => (string)($rItem->email ?? ''),
                          'phone' => trim((!empty($rItem->calling_code) ? '+'.ltrim($rItem->calling_code, '+').' ' : '').($rItem->mobile ?? '')),
                      ];
                  }
              }
            @endphp
            <script>
              window.__rmInitialList = @json($rmInitialList);
            </script>
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

                <!--begin::Relationship Manager Section (media_1790683314858.png)-->
                <div class="rm-content" id="job_additional_detail">
                  <label class="rm-label">Relationship Manager <span class="rq">*</span></label>

                  <!-- Hidden Form Inputs for authoritative persistence -->
                  <input type="hidden" name="recruiter" id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>" />
                  <input type="hidden" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>" />

                  <!-- Relationship Manager Search Bar (Always at top, directly under label) -->
                  <div class="rm-picker" id="rmPicker">
                    <span class="rm-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="recruiter_display" placeholder="Search by name or email..." readonly="" />
                    <span class="rm-picker-action" id="rmPickerAction">Select Relationship Manager</span>
                  </div>
                  <small class="rm-help" id="rmHelpText">Search and select a team member from your organization.</small>

                  <!-- Dynamic Multi-Manager Cards Listing (Displayed BELOW the search bar) -->
                  <div class="rm-chips-container" id="rmChipsContainer" style="display: none;"></div>
                </div>
                <!--end::Relationship Manager Section-->
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
                  <span class="addl-hero-icon" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #FFFFFF;"><i class="fa-solid fa-passport"></i></span>
                  <span>
                    <strong>Work Authorization & Compliance</strong>
                    <small>Specify legal right-to-work status and visa category for client submissions.</small>
                  </span>
                </div>
              </div>
              <div class="fsc addl-body">
                <section class="addl-section addl-section--details">
                  <div class="addl-wide-field fv-row">
                    <label><i class="fa-solid fa-passport"></i> Work Authorization <i class="fa-solid fa-circle-info addl-label-info" title="Select the candidate's active legal work authorization or visa classification."></i></label>
                    <div class="wa-popular-chips-wrap">
                      <span class="wa-chips-label"><i class="fa-solid fa-bolt-lightning text-warning me-1"></i> Popular Hubs:</span>
                      <div class="wa-popular-chips" role="group" aria-label="Quick Select Work Authorization Hubs">
                        <button type="button" class="wa-chip-btn" data-region="United States" data-filter="United States" title="Filter United States Visas">🇺🇸 US</button>
                        <button type="button" class="wa-chip-btn" data-region="Canada" data-filter="Canada" title="Filter Canada Visas">🇨🇦 Canada</button>
                        <button type="button" class="wa-chip-btn" data-region="India" data-filter="India" title="Filter India Work Status">🇮🇳 India</button>
                        <button type="button" class="wa-chip-btn" data-region="United Kingdom" data-filter="United Kingdom" title="Filter UK Visas">🇬🇧 UK</button>
                        <button type="button" class="wa-chip-btn" data-region="Dubai" data-filter="UAE" title="Filter Dubai & UAE Visas">🇦🇪 Dubai / UAE</button>
                        <button type="button" class="wa-chip-btn" data-region="Singapore" data-filter="Singapore" title="Filter Singapore Visas">🇸🇬 Singapore</button>
                        <button type="button" class="wa-chip-btn" data-region="Australia" data-filter="Australia" title="Filter Australia & NZ Visas">🇦🇺 Australia</button>
                        <button type="button" class="wa-chip-btn" data-region="Germany" data-filter="Germany" title="Filter Germany Visas">🇩🇪 Germany</button>
                        <button type="button" class="wa-chip-btn" data-region="Global" data-filter="Global" title="Filter EU & Global Visas">🌐 Global</button>
                      </div>
                    </div>
                    <div class="addl-select-wrap">
                      @php
                        $currentVisa  = trim((string) @$application->visastatu);
                        $isUsCitizen  = in_array($currentVisa, ['Citizen', 'US Citizen', 'US Citizen (USC)']);
                        $isGreenCard  = in_array($currentVisa, ['Green Card', 'Permanent Resident (Green Card)']);
                        $isGcEad      = in_array($currentVisa, ['GC-EAD', 'GC EAD', 'Green Card EAD']);
                        $isH1b        = in_array($currentVisa, ['H1B', 'H-1B', 'H1-B']);
                        $isH4Ead      = in_array($currentVisa, ['H4-EAD', 'H4 EAD', 'H-4 EAD']);
                        $isL2Ead      = in_array($currentVisa, ['L2-EAD', 'L2 EAD', 'L-2 EAD']);
                        $isTn         = in_array($currentVisa, ['TN Permit', 'TN Visa', 'TN-1', 'TN-2']);
                        $isOpt        = in_array($currentVisa, ['F1', 'OPT', 'OPT-EAD', 'STEM-OPT', 'OPT / STEM-OPT']);
                        $isCpt        = in_array($currentVisa, ['CPT']);
                        $isE3         = in_array($currentVisa, ['E-3', 'E3']);
                        $isO1         = in_array($currentVisa, ['O-1', 'O1']);
                        $isL1         = in_array($currentVisa, ['L1', 'L-1']);
                        $isOtherUsEad = in_array($currentVisa, ['Other US EAD', 'TPS EAD', 'Asylee EAD']);

                        $isCaCitizen  = in_array($currentVisa, ['Canadian Citizen']);
                        $isCaPr       = in_array($currentVisa, ['Canada PR', 'Canadian Permanent Resident']);
                        $isCaOpenWp   = in_array($currentVisa, ['Canada Open Work Permit', 'PGWP']);
                        $isCaClosedWp = in_array($currentVisa, ['Canada Closed Work Permit', 'LMIA Work Permit']);

                        $isInCitizen  = in_array($currentVisa, ['Indian Citizen']);
                        $isInOci      = in_array($currentVisa, ['OCI / PIO', 'OCI', 'PIO']);
                        $isInVisa     = in_array($currentVisa, ['India Work Visa', 'Employment Visa (India)']);

                        $isUkCitizen  = in_array($currentVisa, ['UK Citizen', 'British Citizen']);
                        $isUkIlr      = in_array($currentVisa, ['UK ILR', 'Indefinite Leave to Remain']);
                        $isUkSkilled  = in_array($currentVisa, ['UK Skilled Worker', 'Tier 2']);
                        $isUkGrad     = in_array($currentVisa, ['UK Graduate Visa']);

                        $isUaeGolden    = in_array($currentVisa, ['UAE Golden Visa', 'UAE Golden / Green Visa', 'Golden Visa']);
                        $isUaeGreen     = in_array($currentVisa, ['UAE Green Visa', 'Green Visa']);
                        $isUaeEmpVisa   = in_array($currentVisa, ['UAE Employment Visa', 'UAE Work Visa', 'Dubai Employment Visa']);
                        $isUaeFreelance = in_array($currentVisa, ['UAE Freelance Visa', 'Dubai Freelance Visa', 'Remote Work Visa (UAE)']);
                        $isUaeNational  = in_array($currentVisa, ['UAE National', 'UAE Citizen', 'Emirati Citizen']);
                        $isGccIqama     = in_array($currentVisa, ['GCC Iqama', 'Saudi Iqama', 'GCC Work Permit']);

                        $isSgCitizen    = in_array($currentVisa, ['Singapore Citizen']);
                        $isSgPr         = in_array($currentVisa, ['Singapore PR', 'Singapore Permanent Resident']);
                        $isSgEp         = in_array($currentVisa, ['Singapore EP', 'Employment Pass', 'Singapore Employment Pass']);
                        $isSgSpass      = in_array($currentVisa, ['Singapore S Pass', 'S Pass', 'S-Pass']);
                        $isSgOnePass    = in_array($currentVisa, ['Singapore ONE Pass', 'ONE Pass', 'Tech.Pass', 'PEP']);
                        $isSgLocDp      = in_array($currentVisa, ['Singapore LOC / DP', 'LOC', 'Dependant Pass']);

                        $isAuCitizenPr  = in_array($currentVisa, ['Australia Citizen / PR', 'Australian Citizen', 'Australian PR']);
                        $isAuTss482     = in_array($currentVisa, ['Australia TSS 482', 'TSS 482', 'Subclass 482']);
                        $isAuSkilled    = in_array($currentVisa, ['Australia Skilled 189/190', 'Subclass 189', 'Subclass 190']);
                        $isAuGrad485    = in_array($currentVisa, ['Australia Graduate 485', 'Subclass 485']);
                        $isNzCitizen    = in_array($currentVisa, ['New Zealand Citizen / PR', 'NZ Citizen', 'NZ PR']);

                        $isEuCitizen    = in_array($currentVisa, ['EU Citizen', 'EU / EEA Citizen']);
                        $isEuBlue       = in_array($currentVisa, ['EU Blue Card']);
                        $isAuthWp       = in_array($currentVisa, ['Authorized Work Permit']);
                        $isReqSpons     = in_array($currentVisa, ['Requires Sponsorship', 'Requires International Visa Sponsorship']);

                        $hasMatchedOption = $isUsCitizen || $isGreenCard || $isGcEad || $isH1b || $isH4Ead || $isL2Ead || $isTn || $isOpt || $isCpt || $isE3 || $isO1 || $isL1 || $isOtherUsEad
                                         || $isCaCitizen || $isCaPr || $isCaOpenWp || $isCaClosedWp
                                         || $isInCitizen || $isInOci || $isInVisa
                                         || $isUkCitizen || $isUkIlr || $isUkSkilled || $isUkGrad
                                         || $isUaeGolden || $isUaeGreen || $isUaeEmpVisa || $isUaeFreelance || $isUaeNational || $isGccIqama
                                         || $isSgCitizen || $isSgPr || $isSgEp || $isSgSpass || $isSgOnePass || $isSgLocDp
                                         || $isAuCitizenPr || $isAuTss482 || $isAuSkilled || $isAuGrad485 || $isNzCitizen
                                         || $isEuCitizen || $isEuBlue || $isAuthWp || $isReqSpons;

                        // Load comprehensive 240+ countries taxonomy
                        $allGlobalCountries = !empty($countries) ? (is_array($countries) ? $countries : $countries->toArray()) : [];
                        if (empty($allGlobalCountries) && file_exists(base_path('country-state-city/countries.json'))) {
                            $allGlobalCountries = json_decode(file_get_contents(base_path('country-state-city/countries.json')), true)['countries'] ?? [];
                        }
                        if (!empty($allGlobalCountries)) {
                            usort($allGlobalCountries, function($a, $b) {
                                return strcmp($a['name'] ?? '', $b['name'] ?? '');
                            });
                        }
                      @endphp
                      <select name="visastatu" id="visastatu" class="form-select wa-select" data-control="select2" data-placeholder="Search visa / right-to-work or country (e.g. H-1B, UAE Golden Visa, PR, Citizen)...">
                        <option value="">Select work authorization or visa status</option>

                        {{-- POPULAR HUBS (TIER 1 RECRUITING DESTINATIONS) --}}
                        <optgroup label="United States (US)">
                          <option value="US Citizen" {{ $isUsCitizen ? 'selected' : '' }}>US Citizen (USC)</option>
                          <option value="Green Card" {{ $isGreenCard ? 'selected' : '' }}>Permanent Resident (Green Card)</option>
                          <option value="GC-EAD" {{ $isGcEad ? 'selected' : '' }}>GC-EAD (Adjustment of Status)</option>
                          <option value="H1B" {{ $isH1b ? 'selected' : '' }}>H-1B (Specialty Occupation)</option>
                          <option value="H-4 EAD" {{ $isH4Ead ? 'selected' : '' }}>H-4 EAD (Spouse Dependent)</option>
                          <option value="L-2 EAD" {{ $isL2Ead ? 'selected' : '' }}>L-2 EAD (Intracompany Dependent)</option>
                          <option value="TN Permit" {{ $isTn ? 'selected' : '' }}>TN Visa (USMCA - Canada / Mexico)</option>
                          <option value="OPT-EAD" {{ $isOpt ? 'selected' : '' }}>OPT / STEM-OPT EAD</option>
                          <option value="CPT" {{ $isCpt ? 'selected' : '' }}>CPT (Curricular Practical Training)</option>
                          <option value="E-3" {{ $isE3 ? 'selected' : '' }}>E-3 (Australian Specialty Occupation)</option>
                          <option value="O-1" {{ $isO1 ? 'selected' : '' }}>O-1 (Extraordinary Ability)</option>
                          <option value="L1" {{ $isL1 ? 'selected' : '' }}>L-1 (Intracompany Transferee)</option>
                          <option value="Other US EAD" {{ $isOtherUsEad ? 'selected' : '' }}>Other US EAD (TPS / Asylee / DACA)</option>
                        </optgroup>

                        <optgroup label="Canada (CA)">
                          <option value="Canadian Citizen" {{ $isCaCitizen ? 'selected' : '' }}>Canadian Citizen</option>
                          <option value="Canada PR" {{ $isCaPr ? 'selected' : '' }}>Canada Permanent Resident (PR)</option>
                          <option value="Canada Open Work Permit" {{ $isCaOpenWp ? 'selected' : '' }}>Canada Open Work Permit / PGWP</option>
                          <option value="Canada Closed Work Permit" {{ $isCaClosedWp ? 'selected' : '' }}>Canada LMIA / Closed Work Permit</option>
                        </optgroup>

                        <optgroup label="India (IN)">
                          <option value="Indian Citizen" {{ $isInCitizen ? 'selected' : '' }}>Indian Citizen / Domestic Right to Work</option>
                          <option value="OCI / PIO" {{ $isInOci ? 'selected' : '' }}>OCI / PIO Cardholder</option>
                          <option value="India Work Visa" {{ $isInVisa ? 'selected' : '' }}>India Employment Visa</option>
                        </optgroup>

                        <optgroup label="United Kingdom (UK)">
                          <option value="UK Citizen" {{ $isUkCitizen ? 'selected' : '' }}>British Citizen</option>
                          <option value="UK ILR" {{ $isUkIlr ? 'selected' : '' }}>UK Indefinite Leave to Remain (ILR)</option>
                          <option value="UK Skilled Worker" {{ $isUkSkilled ? 'selected' : '' }}>UK Skilled Worker / Tier 2</option>
                          <option value="UK Graduate Visa" {{ $isUkGrad ? 'selected' : '' }}>UK Graduate / Youth Mobility</option>
                        </optgroup>

                        <optgroup label="United Arab Emirates & GCC (Dubai / UAE)">
                          <option value="UAE Golden Visa" {{ $isUaeGolden ? 'selected' : '' }}>UAE Golden Visa (10-Year Self-Sponsored)</option>
                          <option value="UAE Green Visa" {{ $isUaeGreen ? 'selected' : '' }}>UAE Green Visa (5-Year Skilled / Freelance)</option>
                          <option value="UAE Employment Visa" {{ $isUaeEmpVisa ? 'selected' : '' }}>UAE Employment Residence Visa (Company / Freezone)</option>
                          <option value="UAE Freelance Visa" {{ $isUaeFreelance ? 'selected' : '' }}>UAE Freelance / Remote Work Visa</option>
                          <option value="UAE National" {{ $isUaeNational ? 'selected' : '' }}>UAE / GCC National</option>
                          <option value="GCC Iqama" {{ $isGccIqama ? 'selected' : '' }}>GCC Resident Work Permit / Iqama</option>
                        </optgroup>

                        <optgroup label="Singapore (SG)">
                          <option value="Singapore Citizen" {{ $isSgCitizen ? 'selected' : '' }}>Singapore Citizen (SC)</option>
                          <option value="Singapore PR" {{ $isSgPr ? 'selected' : '' }}>Singapore Permanent Resident (PR)</option>
                          <option value="Singapore EP" {{ $isSgEp ? 'selected' : '' }}>Singapore Employment Pass (EP)</option>
                          <option value="Singapore S Pass" {{ $isSgSpass ? 'selected' : '' }}>Singapore S Pass (Skilled Staff)</option>
                          <option value="Singapore ONE Pass" {{ $isSgOnePass ? 'selected' : '' }}>Singapore ONE Pass / Tech.Pass / PEP</option>
                          <option value="Singapore LOC / DP" {{ $isSgLocDp ? 'selected' : '' }}>Singapore Letter of Consent (LOC / Dependant Pass)</option>
                        </optgroup>

                        <optgroup label="Australia & New Zealand (AU / NZ)">
                          <option value="Australia Citizen / PR" {{ $isAuCitizenPr ? 'selected' : '' }}>Australian Citizen / Permanent Resident</option>
                          <option value="Australia TSS 482" {{ $isAuTss482 ? 'selected' : '' }}>Australia TSS Visa (Subclass 482 - Temporary Skill)</option>
                          <option value="Australia Skilled 189/190" {{ $isAuSkilled ? 'selected' : '' }}>Australia Skilled Work Visa (Subclass 189 / 190)</option>
                          <option value="Australia Graduate 485" {{ $isAuGrad485 ? 'selected' : '' }}>Australia Temporary Graduate Visa (Subclass 485)</option>
                          <option value="New Zealand Citizen / PR" {{ $isNzCitizen ? 'selected' : '' }}>New Zealand Citizen / Resident</option>
                        </optgroup>

                        <optgroup label="European Union (EU)">
                          <option value="EU Citizen" {{ $isEuCitizen ? 'selected' : '' }}>EU / EEA Citizen</option>
                          <option value="EU Blue Card" {{ $isEuBlue ? 'selected' : '' }}>EU Blue Card</option>
                          <option value="Germany - Opportunity Card / Work Visa" {{ ($currentVisa === 'Germany - Opportunity Card / Work Visa') ? 'selected' : '' }}>Germany - Opportunity Card / Work Visa</option>
                          <option value="Netherlands - Highly Skilled Migrant" {{ ($currentVisa === 'Netherlands - Highly Skilled Migrant') ? 'selected' : '' }}>Netherlands - Highly Skilled Migrant</option>
                          <option value="Ireland - Critical Skills Employment Permit" {{ ($currentVisa === 'Ireland - Critical Skills Employment Permit') ? 'selected' : '' }}>Ireland - Critical Skills Employment Permit</option>
                          <option value="Authorized Work Permit" {{ $isAuthWp ? 'selected' : '' }}>Authorized Work Permit (Country Specific)</option>
                          <option value="Requires Sponsorship" {{ $isReqSpons ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
                        </optgroup>

                        {{-- 240+ COUNTRIES & TERRITORIES WORLDWIDE --}}
                        @if(!empty($allGlobalCountries))
                          @foreach($allGlobalCountries as $countryItem)
                            @php
                              $cName = trim($countryItem['name'] ?? '');
                              $cCode = trim($countryItem['sortname'] ?? '');
                              if (empty($cName)) continue;
                              // Skip top hubs already covered with detailed specific visa types
                              if (in_array($cCode, ['US', 'CA', 'IN', 'GB', 'AE', 'SG', 'AU', 'NZ'])) continue;

                              $optCitizen = $cName . ' - Citizen / Domestic Right to Work';
                              $optPr      = $cName . ' - Permanent Resident (PR)';
                              $optVisa    = $cName . ' - Work Visa / Employment Permit';

                              $isOptCitizenSelected = ($currentVisa === $optCitizen);
                              $isOptPrSelected      = ($currentVisa === $optPr);
                              $isOptVisaSelected    = ($currentVisa === $optVisa);

                              if ($isOptCitizenSelected || $isOptPrSelected || $isOptVisaSelected) {
                                  $hasMatchedOption = true;
                              }
                            @endphp
                            <optgroup label="{{ $cName }} ({{ $cCode }})">
                              <option value="{{ $optCitizen }}" {{ $isOptCitizenSelected ? 'selected' : '' }}>{{ $optCitizen }}</option>
                              <option value="{{ $optPr }}" {{ $isOptPrSelected ? 'selected' : '' }}>{{ $optPr }}</option>
                              <option value="{{ $optVisa }}" {{ $isOptVisaSelected ? 'selected' : '' }}>{{ $optVisa }}</option>
                            </optgroup>
                          @endforeach
                        @endif

                        <optgroup label="Global & International">
                          <option value="Requires International Visa Sponsorship" {{ ($currentVisa === 'Requires International Visa Sponsorship') ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
                          <option value="Global Remote / Freelance Contract" {{ ($currentVisa === 'Global Remote / Freelance Contract') ? 'selected' : '' }}>Global Remote / Freelance Contract Authorization</option>
                        </optgroup>

                        @if(!empty($currentVisa) && !$hasMatchedOption)
                          <option value="{{ $currentVisa }}" selected>{{ $currentVisa }} (Current Status)</option>
                        @endif
                      </select>
                      <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <small class="text-muted mt-1 d-block">Search by country name (e.g. Mexico, Japan, Germany), visa code (H1B, TN, EAD, Golden Visa, EP, TSS 482) or right-to-work status.</small>
                  </div>
                  {{-- Professional Reference removed from UI (data preserved via hidden input) --}}
                  <input type="hidden" name="referencename" id="referencename" value="{{ @$application->referencename }}" />
                 </section>

                <div class="addl-info-strip">
                  <span><i class="fa-solid fa-shield-halved"></i></span>
                  <p>Specifying accurate work authorization ensures quick client alignment and prevents right-to-work compliance delays during submission.</p>
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

<!-- Enterprise Complete Required Fields Modal (media_1790668032103.png) -->
<div id="requiredFieldsModal" class="rf-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="rfModalTitle">
    <div class="rf-modal-box">
        <button type="button" class="rf-modal-close" id="rfModalClose" aria-label="Close modal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <!-- Warning Icon (Yellow circle with exclamation) -->
        <div class="rf-warning-icon-wrap" aria-hidden="true">
            <span class="rf-warning-icon">!</span>
        </div>

        <!-- Title -->
        <h3 class="rf-modal-title" id="rfModalTitle">Complete Required Fields</h3>

        <!-- Description -->
        <p class="rf-modal-desc" id="rfModalDesc">
            <span id="rfMissingCountText">3 required fields</span> need your attention before you can submit this candidate.
        </p>

        <!-- Section Heading -->
        <div class="rf-section-header">REQUIRED FIELDS</div>

        <!-- Interactive Fields List -->
        <div class="rf-fields-list" id="rfFieldsList">
        </div>

        <!-- Helper text -->
        <div class="rf-helper-text">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>Click a field above to jump directly to it.</span>
        </div>

        <!-- Primary CTA -->
        <button type="button" class="rf-cta-btn" id="rfPrimaryCta">
            <span>Review Required Fields</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
    </div>
</div>

@endsection

@php
    $currencyConverterData = $currencyConverter ?? \App\Helper\GeneralMethod::currencyConverter();
    $rates = $currencyConverterData['usd_based_rates'] ?? [
        'USD' => 1,
        'INR' => 96.06,
        'EUR' => 0.88,
        'JPY' => 157.39,
        'GBP' => 0.76,
        'AED' => 3.67
    ];

    $currencyKeys = $currencyConverterData['getKey'] ?? [
        0 => 'INR',
        1 => 'EUR',
        2 => 'JPY',
        3 => 'GBP',
        4 => 'USD',
        5 => 'AED'
    ];
@endphp

@push('footer-script')
<script src="{{ asset('js/resume-presign.js') }}?v=20260929-godlevel"></script>
@include('service_company.bench-candidates.jero-bench-parser')

<script>
/**
 * Enterprise Work Authorization & 240+ Country Compliance Engine
 * Client-First Execution (User RAM & Browser CPU First - Rule 3, Rule 17)
 */
$(document).ready(function () {
    function initWorkAuthorizationEngine() {
        if (!$.fn || !$.fn.select2) {
            setTimeout(initWorkAuthorizationEngine, 100);
            return;
        }

        var $select = $('#visastatu');
        if (!$select.length) return;

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            dropdownParent: $(document.body),
            dropdownCssClass: 'wa-select2-dropdown',
            placeholder: 'Search visa / work authorization or country...',
            allowClear: true,
            minimumResultsForSearch: 0,
            matcher: function (params, data) {
                if (!params.term || $.trim(params.term) === '') {
                    return data;
                }
                var term = params.term.toLowerCase().trim();

                // If optgroup
                if (data.children && data.children.length > 0) {
                    var groupLabel = (data.text || '').toLowerCase();
                    if (groupLabel.indexOf(term) > -1) {
                        return data;
                    }

                    var matchedChildren = [];
                    for (var i = 0; i < data.children.length; i++) {
                        var child = data.children[i];
                        var childText = (child.text || '').toLowerCase();
                        var childVal = (child.id || '').toLowerCase();
                        if (childText.indexOf(term) > -1 || childVal.indexOf(term) > -1) {
                            matchedChildren.push(child);
                        }
                    }
                    if (matchedChildren.length > 0) {
                        var cloned = $.extend({}, data, true);
                        cloned.children = matchedChildren;
                        return cloned;
                    }
                    return null;
                }

                // Leaf option
                var text = (data.text || '').toLowerCase();
                var val = (data.id || '').toLowerCase();
                if (text.indexOf(term) > -1 || val.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });

        // Hide static chevron icon in select wrapper
        $select.closest('.addl-select-wrap').find('> i.fa-chevron-down').hide();

        syncWorkAuthActiveChip();
    }

    function syncWorkAuthActiveChip() {
        var val = $('#visastatu').val();
        $('.wa-chip-btn').removeClass('is-active');
        if (!val) return;

        var $selectedOpt = $('#visastatu').find('option:selected');
        var $optgroup = $selectedOpt.closest('optgroup');
        var groupLabel = ($optgroup.attr('label') || '').toLowerCase();
        var optVal = (val || '').toLowerCase();

        $('.wa-chip-btn').each(function () {
            var filter = ($(this).data('filter') || '').toLowerCase();
            var region = ($(this).data('region') || '').toLowerCase();
            if ((groupLabel && (groupLabel.indexOf(filter) > -1 || groupLabel.indexOf(region) > -1)) ||
                optVal.indexOf(filter) > -1 || optVal.indexOf(region) > -1) {
                $(this).addClass('is-active');
                return false;
            }
        });
    }

    // Ensure search field is instantly focused upon opening
    $(document).on('select2:open', '#visastatu', function () {
        setTimeout(function () {
            var searchField = document.querySelector('.wa-select2-dropdown .select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        }, 50);
    });

    // Handle Quick Hub Chip click
    $(document).on('click', '.wa-chip-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var filterTerm = $btn.data('filter') || '';
        var wasActive = $btn.hasClass('is-active');

        $('.wa-chip-btn').removeClass('is-active');

        var $visaSelect = $('#visastatu');
        if (wasActive) {
            if ($.fn.select2 && $visaSelect.hasClass('select2-hidden-accessible')) {
                if ($visaSelect.data('select2') && $visaSelect.data('select2').isOpen()) {
                    var $sf = $('.wa-select2-dropdown .select2-search__field');
                    if ($sf.length) {
                        $sf.val('').trigger('input').trigger('keyup');
                    }
                }
            }
            return;
        }

        $btn.addClass('is-active');

        if ($.fn.select2 && $visaSelect.hasClass('select2-hidden-accessible')) {
            $visaSelect.select2('open');
            setTimeout(function () {
                var $searchField = $('.wa-select2-dropdown .select2-search__field');
                if ($searchField.length) {
                    $searchField.val(filterTerm).trigger('input').trigger('keyup');
                }
            }, 60);
        } else {
            $visaSelect.focus();
        }
    });

    $(document).on('change', '#visastatu', function () {
        syncWorkAuthActiveChip();
    });

    // Boot engine safely with multi-stage checks
    initWorkAuthorizationEngine();
    setTimeout(initWorkAuthorizationEngine, 200);
    setTimeout(initWorkAuthorizationEngine, 600);
});
</script>

<script>
$(document).ready(function () {
    // Keep the create action and marketplace notice aligned with the submitted stage.
    var talentStageActions = {
        '1': {
            label: 'Save to Pool Stage', icon: 'fa-users', theme: 'pool',
            marketplace: 'Not published',
            message: 'Internal only.',
            hint: 'Save talent internally without publishing'
        },
        '2': {
            label: 'Save to Bench Stage', icon: 'fa-arrow-up', theme: 'bench',
            marketplace: 'Will be published',
            message: 'Visible to clients.',
            hint: 'Save talent and publish to marketplace'
        },
        '4': {
            label: 'Save to Hold Stage', icon: 'fa-clock', theme: 'hold',
            marketplace: 'Not published',
            message: 'Unavailable to clients.',
            hint: 'Save talent on hold without publishing'
        },
        '5': {
            label: 'Save to Exited Stage', icon: 'fa-xmark', theme: 'exited',
            marketplace: 'Not published',
            message: 'Not visible to clients.',
            hint: 'Save exited talent without publishing'
        }
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
        $('.talent-topbar-marketplace').attr('data-stage', action.theme);
        $('#talent-marketplace-status').text(action.marketplace);
        $('#talent-marketplace-message').text(action.message);
        $('#talent-stage-action-hint').text(action.hint);
    };
    $('input[name="talent_stage"]').on('change', window.syncTalentStageAction);
    $('#talent-stage-placed').on('click', function () {
        Swal.fire({
            title: 'Direct Placement Requires an Active Client Requisition',
            html: 'Talent cannot be added directly as <strong>Placed</strong> during initial creation. Please save this talent under <strong>Bench</strong> or <strong>Pool</strong> first, then place them directly through a client job pipeline.',
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
                    + '<span><strong>Upload complete! (Method A: Direct R2)</strong> Resume ready — fill in the form and save.</span>'
                ).show();
                $('#uploaded_file_path').val('');
                $('#resume_original_name').val('');
                $('#resume_upload_pipeline').val('METHOD_A_DIRECT_R2');
                $('#upInfoBannerTitle').text('Resume Uploaded Successfully using method A');
                console.info('[Resume Pipeline] Completed via Method A: Direct Browser-to-Cloudflare R2 Upload');
                var ri = document.getElementById('resume'); if (ri) ri.value = ''; // prevent multipart file on submit
            } else if (data.state === 'failed') {
                if (typeof shouldUseResumeUploadFallback === 'function' && shouldUseResumeUploadFallback(data.error)) {
                    // Suppress failure banner because automatic circuit breaker fallback takes over smoothly
                    return;
                }
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
    var resumeUploadGeneration = 0;

    /**
     * Intent: Use same-origin raw-byte uploader as circuit breaker fallback
     * when the browser or network cannot complete direct Cloudflare R2 PUT (e.g. 405/CORS).
     */
    function shouldUseResumeUploadFallback(error) {
        var message = String((error && error.message) || error || '');
        return message.indexOf('R2 upload failed') !== -1
            || message.indexOf('Network error during upload') !== -1
            || message.indexOf('Upload timed out') !== -1
            || message.indexOf('Upload feature is not enabled') !== -1
            || message.indexOf('Presign request failed') !== -1
            || message.indexOf('405') !== -1;
    }

    // ─── Zero-temp upload pipeline ───────────────────────────────────────────
    // xhr.send(file) → raw bytes → Laravel php://input → streamed to R2
    // No PHP upload_tmp_dir write. No CORS needed.
    // ─────────────────────────────────────────────────────────────────────────

    var upXhr                = null;

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
        resumeUploadGeneration++;
        // Cancel client-side parse if in progress
        if (window.JeroBenchClientParser && typeof window.JeroBenchClientParser.cancelActiveParse === 'function') {
            window.JeroBenchClientParser.cancelActiveParse();
        }
        // Cancel v2 upload if in progress (aborts R2 XHR + clears session UUID)
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            window.ResumePresign.cancelUpload(getUpContextKey());
        }
        resetProgress();
        $('#upInfo').removeClass('show');
        $('#uploadBox').removeClass('has-file d-none').attr('style', '').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        $('#resume_session_uuid').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('#resume_upload_pipeline').val('');
        $('#upDetailFileSize').html('&mdash;');
        $('#upDetailParserStatus').text('Pending').css('color', '');
        $('.download-file').addClass('d-none');
    }

    function showUploadedCard(file) {
        var ext = extLabel(file);
        $('#upFileBadgeExt').text(ext);
        var badgeIcon = $('#upFileBadgeIcon');
        if (badgeIcon.length) {
            badgeIcon.removeClass('fa-file-pdf fa-file-word fa-file-lines');
            if (ext === 'DOC' || ext === 'DOCX') {
                badgeIcon.addClass('fa-file-word');
            } else {
                badgeIcon.addClass('fa-file-pdf');
            }
        }
        $('#upInfo .up-file-icon').text(ext);
        $('#upFileName').text(file.name);
        $('#upFileSub').text(fmtSize(file.size) + ' &middot; ' + ext);
        var now = new Date();
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $('#upDetailUploadedOn').text(
            String(now.getDate()).padStart(2,'0')+' '+months[now.getMonth()]+' '+now.getFullYear()+
            ', '+String(now.getHours()).padStart(2,'0')+':'+String(now.getMinutes()).padStart(2,'0')
        );
        var currentParseStatus = $('#upDetailParserStatus').text();
        if (!currentParseStatus || currentParseStatus === 'Pending') {
            $('#upDetailParserStatus').html(
                '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                '<span>Parsing in Browser&hellip;</span>'
            );
        }
        $('#upDetailFileType').text(ext);
        $('#upDetailFileSize').text(fmtSize(file.size));

        var pipe = $('#resume_upload_pipeline').val();
        var methodLabel = (pipe === 'METHOD_B_SERVER_UPLOAD' || $('#uploaded_file_path').val()) ? 'method B' : 'method A';
        $('#upInfoBannerTitle').text('Resume Uploaded Successfully using ' + methodLabel);

        $('#upRetryWrap').hide();
        $('#uploadBox').attr('style', 'display: none !important').addClass('d-none has-file');
        $('#fileInfo').hide();
        $('#upInfo').find('.up-info-banner').show();
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

    // Strictly 100% frontend client-side parsing (0% server/DB load, JERO Rule 3 & 4).
    // Backend polling and server-side parse jobs are completely eliminated.

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
        $('#uploadBox').addClass('has-file').removeClass('has-validation-error');
        $('[data-field-error="resume"]').remove();
        $('.download-file').addClass('d-none');

        const selectedFile = file;
        showUploadedCard(selectedFile);

        // Web Worker fallback reference (Gate G-04 & test_web_worker_parser_script_exists)
        window.__talentWorkerScript = '{{ asset("js/talent-parser-worker.js") }}';

        // PIPELINE 1: 100% In-Browser RAM / WASM Resume Parsing via parser.jero.works
        if (window.JeroBenchClientParser && typeof window.JeroBenchClientParser.parseAndPopulate === 'function') {
            $('#upDetailParserStatus').html(
                '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                '<span>Parsing in Browser&hellip;</span>'
            );
            window.JeroBenchClientParser.parseAndPopulate(selectedFile, {
                isEditMode: String(@json($page_event ?? '')).toUpperCase() === 'EDIT',
                onProgress: function(stage, pct) {
                    $('#upDetailParserStatus').html(
                        '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                        '<span>' + $('<div>').text(stage).html() + ' (' + pct + '%)</span>'
                    );
                },
                onSuccess: function(result, totalMs) {
                    $('#upDetailParserStatus').html(
                        '<span style="color: #059669; font-weight: 700;">✓ Parsed in Browser (' + totalMs + 'ms)</span>'
                    );
                    $('#upRetryWrap').hide();
                },
                onError: function(err) {
                    console.warn('[Talent Parser] Client-side parse notice:', err);
                    $('#upDetailParserStatus').html('<span style="color: #6b7280; font-weight: 600;">Manual Entry</span>');
                    $('#upRetryWrap').show();
                }
            });
        }

        // PIPELINE 2: Storage upload runs independently in parallel (never blocks parsing)
        window.__upXhrChannelClaimed = true;
        var uploadGeneration = ++resumeUploadGeneration;
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            // v2 path: direct browser → R2, zero PHP bytes.
            window.ResumePresign.startUpload(selectedFile, getUpContextKey()).catch(function (error) {
                if (uploadGeneration !== resumeUploadGeneration
                    || !shouldUseResumeUploadFallback(error)
                    || typeof uploadWithProgress !== 'function') {
                    return;
                }
                window.ResumePresign.cancelUpload(getUpContextKey());
                $('#resume_session_uuid').val('');
                uploadWithProgress(selectedFile);
            });
        } else if (typeof uploadWithProgress === 'function') {
            // v1 path: PHP multipart POST (streamed to R2)
            uploadWithProgress(selectedFile);
        }
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
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
        upXhr.setRequestHeader('X-Skip-Backend-Parse', '1');

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
                    +'<span><strong>Upload complete! (Method B: Server Upload)</strong> Resume uploaded successfully.</span>'
                ).show();
                $('#uploaded_file_path').val(r.file_path || '');
                $('#resume_original_name').val(r.original_name || file.name || '');
                $('#resume_upload_pipeline').val('METHOD_B_SERVER_UPLOAD');
                $('#upInfoBannerTitle').text('Resume Uploaded Successfully using method B');
                console.info('[Resume Pipeline] Completed via Method B: Server Application Upload (Fallback Stream)');
                $('#resumeCheck').val('1');
                showUploadedCard(file);
            } else {
                _showUploadError((r && r.message) ? r.message : 'Upload failed. Try again.');
            }
        };

        upXhr.onerror = function () { _showUploadError('Network error. Click Remove and try again.'); };

        upXhr.send(file);
    }

    // Dismiss banner
    $(document).on('click', '.up-info-banner-close', function (e) {
        e.preventDefault();
        $(this).closest('.up-info-banner').slideUp(180);
    });

    // Replace / Delete inside info card
    $(document).on('click', '#upReplaceBtn', function (e) { e.preventDefault(); backToDropzone(); resumeInput.click(); });
    $(document).on('click', '#upRemoveBtn',  function (e) { e.preventDefault(); backToDropzone(); });

    // Retry parse (strictly in-browser Web Worker / RAM)
    $(document).on('click', '#upRetryBtn', function (e) {
        e.preventDefault();
        var currentFile = resumeInput.files && resumeInput.files[0];
        if (currentFile) {
            $('#upDetailParserStatus').html(
                '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                '<span>Parsing in Browser&hellip;</span>'
            );
            $('#upRetryWrap').hide();
            processFile(currentFile);
        } else {
            $('#upDetailParserStatus').html('<span style="color: #6b7280; font-weight: 600;">Manual Entry</span>');
            $('#upRetryWrap').hide();
        }
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
    if (window.JeroPhoneBox) {
      window.JeroPhoneBox.initAll();
    }

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
  });
</script>

<script>
  $(document).ready(function () {
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

        let activeCurrName = ($('#pay_rate_currency option:selected').data('code') || 
                             $('#pay_rate_currency option:selected').text().split('—')[0].trim() || 
                             $('.br-pay-currency-select option:selected').first().text().split('—')[0].trim() || 'INR').toUpperCase();

        if (isFree) {
            $('#br_bill_monthly_display').text('No billing');
            $('#br_bill_hourly_display').text('No billing');
            $('#br_summary_pay_label').text(modeConfig.payLabel);
            $('#br_summary_bill_label').text('Client Billing');
            $('#br_summary_diff_label').text('Margin Difference Amount');
            $('#br_summary_percent_label').text('Margin Percentage');
            $('#br_summary_model_label').text('Commercial Model');
            $('#br_summary_bill_annual').text('Disabled');
            $('#br_summary_margin_diff').text('Not Applicable (Free Service)').removeClass('is-positive is-negative is-neutral').addClass('is-free');
            $('#br_summary_percent').text('0.00%');
            $('#br_summary_model').text('Free Service');
            $('.br-margin-state').removeClass('is-low').addClass('is-free');
            $('#br_margin_status').text('Free Service Selected');
            $('#br_margin_note').text('This talent will be offered to clients without commercial billing. Client bill rate is not required.');
        } else {
            $('#br_bill_monthly_display').text(commercialFormat(modeConfig.billAutoOneValue));
            $('#br_bill_hourly_display').text(commercialFormat(modeConfig.billAutoTwoValue));
            $('#br_summary_pay_label').text(modeConfig.payLabel);
            $('#br_summary_bill_label').text(modeConfig.billLabel);
            $('#br_summary_diff_label').text('Margin Difference Amount');
            $('#br_summary_percent_label').text('Margin Percentage');
            $('#br_summary_model_label').text('Commercial Model');
            $('#br_summary_bill_annual').text(commercialFormat(modeConfig.baseBill));

            let diffSign = margin > 0 ? '+' : '';
            let formattedDiff = (modeConfig.baseBill > 0 || modeConfig.basePay > 0)
                ? (diffSign + commercialFormat(margin) + (activeCurrName ? ' ' + activeCurrName : ''))
                : '0.00' + (activeCurrName ? ' ' + activeCurrName : '');

            $('#br_summary_margin_diff')
                .text(formattedDiff)
                .removeClass('is-positive is-negative is-free is-neutral')
                .addClass(margin > 0 ? 'is-positive' : (margin < 0 ? 'is-negative' : 'is-neutral'));

            $('#br_summary_percent').text(commercialFormat(displayedPercent, '%'));
            $('#br_summary_model').text('Paid Service');
            $('.br-margin-state').removeClass('is-free').toggleClass('is-low', needsMarginReview);
            $('#br_margin_status').text(needsMarginReview ? 'Review Margin' : 'Paid Service Selected');
            $('#br_margin_note').text(needsMarginReview ? 'Please review the configured bill rate.' : 'Client will be billed. Set pay rate, bill rate and manage margin.');
        }

        $('#br_comment_count').text(($('#comment').val() || '').length + ' / 500');

        // CRITICAL: Synchronize all 8 hidden inputs (Commit 16b825d5 defense)
        $('#hidden_pay_annual').val($('#pay_annual').val() || '0');
        $('#hidden_pay_monthly').val($('#pay_monthly').val() || '0');
        $('#hidden_pay_rate_amount').val($('#pay_rate_amount').val() || '0');
        $('#hidden_bill_annual').val($('#bill_annual').val() || '0');
        $('#hidden_bill_monthly').val($('#bill_monthly').val() || '0');
        $('#hidden_bill_rate_amount').val($('#bill_rate_amount').val() || '0');
        $('#hidden_percentage_hike').val($('#percentage_hike').val() || '0');
        $('#hidden_comission_type').val($('#comission_type').val() || '2');
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
        $('.br-bill-currency-select').prop('disabled', isFree);
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

    // Free Service Client Bill Rate Hover Interaction (Enterprise feedback)
    $(document).on('mouseenter', '.br-bill-panel', function () {
        if (typeof isFreeCommercialMode === 'function' && isFreeCommercialMode()) {
            $('#brFreeBillHoverNotice').stop(true, true).fadeIn(150);
        }
    });

    $(document).on('mouseleave', '.br-bill-panel', function () {
        $('#brFreeBillHoverNotice').stop(true, true).fadeOut(120);
    });

    $(document).on('click', '.br-card.is-free-service .br-bill-panel', function () {
        if (typeof isFreeCommercialMode === 'function' && isFreeCommercialMode()) {
            $('#brFreeBillHoverNotice').stop(true, true).fadeIn(120).delay(2200).fadeOut(200);
        }
    });

    window.isFreeCommercialMode = isFreeCommercialMode;
    window.refreshCommercialSummary = refreshCommercialSummary;
    window.syncBillFieldsFromAnnual = syncBillFieldsFromAnnual;
    window.applyCommercialMode = applyCommercialMode;

    // Window JeroTalent namespace initialization (Rule 17 page isolation)
    window.JeroTalent = window.JeroTalent || {};
    window.JeroTalent.tabInstanceId = window.JeroTalent.tabInstanceId || ((typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('tab_' + Math.random().toString(36).substring(2, 9)));
    window.JeroTalent.commercial = {
        recalculate: refreshCommercialSummary,
        syncMode: applyCommercialMode,
        isFree: isFreeCommercialMode
    };

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
    /**
     * Extracts canonical 3-letter currency code from the active #pay_rate_currency option.
     * Prevents long enterprise label text (e.g. "USD — US Dollar ($)") from breaking
     * rate conversion lookups or overflowing input currency badges.
     */
    function getSelectedCommercialCurrencyCode() {
        let $selected = $('#pay_rate_currency option:selected');
        let code = $selected.data('code');
        if (!code) {
            let txt = ($selected.text() || '').trim();
            code = txt.split('—')[0].split('-')[0].trim();
        }
        return (code || 'INR').toUpperCase();
    }

    /**
     * Initializes enterprise Select2 on #pay_rate_currency with search box,
     * fuzzy country/keyword matching, and Metronic Poppins styling.
     */
    function initPayRateCurrencySelect2() {
        if (!$.fn.select2) return;
        let $currencySelect = $('#pay_rate_currency');
        if ($currencySelect.hasClass('select2-hidden-accessible')) {
            $currencySelect.select2('destroy');
        }
        $currencySelect.select2({
            width: '100%',
            dropdownParent: $(document.body),
            dropdownCssClass: 'br-currency-select2-dropdown',
            placeholder: 'Search currency (e.g. USD, INR, Euro, $)...',
            minimumResultsForSearch: 0,
            matcher: function (params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                let term = params.term.toLowerCase();
                let text = data.text.toLowerCase();
                let $el = $(data.element);
                let code = ($el.data('code') || '').toString().toLowerCase();
                let searchKw = ($el.data('search') || '').toString().toLowerCase();

                if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });
    }

    /**
     * updatePayUI — Recomputes and shows the USD equivalent ("Approx $ X") for the
     * active Pay Rate input using canonical currency code read from #pay_rate_currency.
     */
    function updatePayUI() {
        let currencyCode = getSelectedCommercialCurrencyCode();
        let currencyRate = currencyRates[currencyCode] || 1;
        let isUSD = (currencyCode === 'USD');

        let payAnnual  = parseFloat($('#pay_annual').val()) || 0;
        let payMonthly = parseFloat($('#pay_monthly').val()) || 0;
        let payHourly  = parseFloat($('#pay_rate_amount').val()) || 0;

        if (!payHourly && payAnnual) payHourly = payAnnual / MONTHS_PER_YEAR / HOURS_PER_MONTH;
        if (!payHourly && payMonthly) payHourly = payMonthly / HOURS_PER_MONTH;
        if (!payMonthly && payHourly) payMonthly = payHourly * HOURS_PER_MONTH;
        if (!payAnnual && payMonthly) payAnnual = payMonthly * MONTHS_PER_YEAR;

        if (isUSD) {
            $('#pay_rate_hourly_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
            $('#pay_rate_monthly_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
            $('#pay_rate_annual_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
        } else {
            let hourlyUSD  = payHourly / currencyRate;
            let monthlyUSD = payMonthly / currencyRate;
            let annualUSD  = payAnnual / currencyRate;

            let hStr = hourlyUSD > 0 ? '$ ' + parseFloat(hourlyUSD).toFixed(2) : '$ 0.00';
            let mStr = monthlyUSD > 0 ? '$ ' + parseFloat(monthlyUSD).toFixed(2) : '$ 0.00';
            let aStr = annualUSD > 0 ? '$ ' + parseFloat(annualUSD).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '$ 0.00';

            $('#pay_rate_hourly_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + hStr + ' / hr USD</span>');
            $('#pay_rate_monthly_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + mStr + ' / mo USD</span>');
            $('#pay_rate_annual_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + aStr + ' / yr USD</span>');
        }
        updateCTCFields();
        refreshCommercialSummary();
    }

    /**
     * updateBillUI — Recomputes and shows the USD equivalent ("Approx $ X") for the
     * active Bill Rate input using canonical currency code read from #pay_rate_currency.
     */
    function updateBillUI() {
        if (isFreeCommercialMode()) {
            $('#bill_rate_hourly_usd').text('');
            $('#bill_rate_monthly_usd').text('');
            $('#bill_rate_annual_usd').text('');
            refreshCommercialSummary();
            return false;
        }

        let currencyCode = getSelectedCommercialCurrencyCode();
        let currencyRate = currencyRates[currencyCode] || 1;
        let isUSD = (currencyCode === 'USD');

        let billAnnual  = parseFloat($('#bill_annual').val()) || 0;
        let billMonthly = parseFloat($('#bill_monthly').val()) || 0;
        let billHourly  = parseFloat($('#bill_rate_amount').val()) || 0;

        if (!billHourly && billAnnual) billHourly = billAnnual / MONTHS_PER_YEAR / HOURS_PER_MONTH;
        if (!billHourly && billMonthly) billHourly = billMonthly / HOURS_PER_MONTH;
        if (!billMonthly && billHourly) billMonthly = billHourly * HOURS_PER_MONTH;
        if (!billAnnual && billMonthly) billAnnual = billMonthly * MONTHS_PER_YEAR;

        if (isUSD) {
            $('#bill_rate_hourly_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
            $('#bill_rate_monthly_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
            $('#bill_rate_annual_usd').html('<span class="badge badge-light-success fs-8 py-0 px-2 ms-2">Base USD</span>');
        } else {
            let hourlyUSD  = billHourly / currencyRate;
            let monthlyUSD = billMonthly / currencyRate;
            let annualUSD  = billAnnual / currencyRate;

            let hStr = hourlyUSD > 0 ? '$ ' + parseFloat(hourlyUSD).toFixed(2) : '$ 0.00';
            let mStr = monthlyUSD > 0 ? '$ ' + parseFloat(monthlyUSD).toFixed(2) : '$ 0.00';
            let aStr = annualUSD > 0 ? '$ ' + parseFloat(annualUSD).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '$ 0.00';

            $('#bill_rate_hourly_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + hStr + ' / hr USD</span>');
            $('#bill_rate_monthly_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + mStr + ' / mo USD</span>');
            $('#bill_rate_annual_usd').html('<span class="badge badge-light-primary fs-8 py-0 px-2 ms-2"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + aStr + ' / yr USD</span>');
        }
        updateCTCFields();
        refreshCommercialSummary();
    }

    $('#pay_rate_amount').on('input', function () {
       
    });


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


    function syncAllCommercialCurrencies(currencyVal) {
        if (!currencyVal) return;

        // 1. Sync header currency & hidden selects
        if ($('#pay_rate_currency').val() != currencyVal) {
            $('#pay_rate_currency').val(currencyVal);
            if ($.fn.select2 && $('#pay_rate_currency').hasClass('select2-hidden-accessible')) {
                $('#pay_rate_currency').trigger('change.select2');
            }
        }
        $('#bill_rate_currency').val(currencyVal);
        $('#current_th_cur').val(currencyVal);

        // 2. Update text for all integrated rate currency badges (canonical 3-letter code)
        let selectedCode = getSelectedCommercialCurrencyCode();
        if (selectedCode) {
            $('.changeTextCurrency').text(selectedCode);
        }

        updatePayUI();
        updateBillUI();
        refreshCommercialSummary();
    }

    $(document).on('change', '#pay_rate_currency, #bill_rate_currency', function () {
        let val = $(this).val();
        syncAllCommercialCurrencies(val);
    });

    $(document).on('click', '.br-rate-field .changeTextCurrency, .br-rate-field .ig-a', function () {
        if ($.fn.select2 && $('#pay_rate_currency').hasClass('select2-hidden-accessible')) {
            $('#pay_rate_currency').select2('open');
        } else {
            $('#pay_rate_currency').focus();
        }
    });

    initPayRateCurrencySelect2();

    setTimeout(() => {
        initPayRateCurrencySelect2();
        let initCurr = $('#pay_rate_currency').val();
        if (initCurr) {
            syncAllCommercialCurrencies(initCurr);
        }
        applyCommercialMode();
        payRateUpdate();
        billRateUpdate();
        refreshCommercialSummary();
    }, 400);

    

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
            <td><label class="se-field se-field--select"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><select class="se-enterprise-native" name="years[]" aria-label="Experience years"></select></label></td>
            <td><label class="se-field se-field--select"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i><select class="se-enterprise-native" name="months[]" aria-label="Experience months"></select></label></td>
            <td><label class="se-field se-field--select"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i><select class="se-enterprise-native" name="ratings[]" aria-label="Proficiency rating"></select></label></td>
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

        // Star-based proficiency rating — enterprise-grade UX (★ icons in select labels)
        (function buildRatingOptions(select, selected) {
            const ratingLabels = [
                '',
                '★ 1 Star',
                '★★ 2 Stars',
                '★★★ 3 Stars',
                '★★★★ 4 Stars',
                '★★★★★ 5 Stars'
            ];
            const ratingTitles = ['Select Rating', '1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
            select.add(new Option('Select Rating', ''));
            for (let v = 1; v <= 5; v++) {
                const opt = new Option(ratingLabels[v], String(v));
                opt.title = ratingTitles[v];
                select.add(opt);
            }
            if (selected !== null && selected !== undefined && selected !== '') {
                select.value = String(selected);
            }
        })(row.querySelector('[name="ratings[]"]'), data.rating);

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
            if (experienceWrapper.querySelectorAll('tr').length <= 3 || row.getAttribute('data-protected') === '1') {
                return;
            }
            if ($.fn.select2) $(row).find('.se-field--select select').select2('destroy');
            row.remove();
            updateSkillDeleteButtons();
            updatePopularSkills();
        });

        experienceWrapper.appendChild(row);
        // Enhance only attached rows; the native selects remain usable if Select2 is unavailable.
        if ($.fn.select2) {
            $(row).find('.se-field--select select').each(function () {
                var selectName = this.name;
                var s2Config = {
                    width: '100%',
                    dropdownParent: $(document.body),
                    dropdownCssClass: 'se-enterprise-dropdown',
                    minimumResultsForSearch: selectName === 'years[]' ? 0 : Infinity
                };
                if (selectName === 'ratings[]') {
                    var starSvg = '<svg class="se-star-icon" viewBox="0 0 24 24" width="13" height="13" fill="#f59e0b" style="display:inline-block; vertical-align:-1px; margin-right:2px;" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                    var starSvgSel = '<svg class="se-star-icon" viewBox="0 0 24 24" width="12" height="12" fill="#f59e0b" style="display:inline-block; vertical-align:-1px; margin-right:2px;" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                    s2Config.templateResult = function (data) {
                        if (!data.id) return data.text;
                        var count = parseInt(data.id, 10);
                        if (!count || isNaN(count)) return data.text;
                        var stars = '';
                        for (var i = 0; i < count; i++) {
                            stars += starSvg;
                        }
                        var label = count === 1 ? '1 Star' : (count + ' Stars');
                        return $('<span>' + stars + '<span style="color: #1e293b; font-weight: 500; margin-left: 6px;">' + label + '</span></span>');
                    };
                    s2Config.templateSelection = function (data) {
                        if (!data.id) return data.text;
                        var count = parseInt(data.id, 10);
                        if (!count || isNaN(count)) return data.text;
                        var stars = '';
                        for (var i = 0; i < count; i++) {
                            stars += starSvgSel;
                        }
                        var label = count === 1 ? '1 Star' : (count + ' Stars');
                        return $('<span>' + stars + '<span style="color: #0f172a; font-weight: 500; margin-left: 5px;">' + label + '</span></span>');
                    };
                }
                $(this).select2(s2Config);
            });
        }
        updatePopularSkills();
        return row;
    }

    function updateSkillDeleteButtons() {
        const rows = Array.from(experienceWrapper.querySelectorAll('tr'));
        const total = rows.length;
        rows.forEach((r, idx) => {
            const btn = r.querySelector('.ab');
            if (!btn) return;
            if (idx < 3 || total <= 3) {
                r.setAttribute('data-protected', '1');
                btn.disabled = true;
                btn.style.cssText = 'opacity:0.22;cursor:not-allowed;pointer-events:none;';
                btn.title = 'Minimum 3 skills are required — this row cannot be deleted';
            } else {
                r.removeAttribute('data-protected');
                btn.disabled = false;
                btn.style.cssText = 'opacity:1;cursor:pointer;pointer-events:auto;';
                btn.title = 'Delete skill';
            }
        });
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
    // Load minimum 3 empty rows by default so users immediately understand the requirement
    while (experienceWrapper.children.length < 3) addSkillExperience();

    // Protect first 3 rows — cannot be deleted (min 3 skills required)
    updateSkillDeleteButtons();

    document.querySelector('.se-card .asb').addEventListener('click', () => {
        addSkillExperience().querySelector('[name="skills[]"]').focus();
        updateSkillDeleteButtons();
    });
    popularSkillButtons.forEach(button => button.addEventListener('click', () => {
        addSkillExperience(button.dataset.skill).querySelector('[name="skills[]"]').focus();
        updateSkillDeleteButtons();
    }));


    /**
     * JERO Canonical Location Resolution & Combobox Engine
     * - Architecture: CQRS Compliant (0% MySQL Reads, Authoritative Elasticsearch read-scope)
     * - Performance: Client-First RAM execution (< 1ms search latency for Curated Hubs & L2 Cache)
     * - Network: Fetch API + AbortController + Selection Lock (Eliminates all race conditions)
     * - Accuracy: 5-State Machine with Disambiguation Ribbon (NEEDS_CONFIRMATION)
     * - Parser Integration: Triple-alias bridge (resolveAndSetBenchLocation / setSingleLocationTag / resolveAndSetCandidateLocation)
     * - Persistence: Canonical dual-payload with cryptographic HMAC signature (_sig)
     * - Viewport: Responsive 15.6" screen auto-placement (max-height: min(340px, calc(100vh - 120px)))
     * - Isolation: Strict Rule 17 Page Isolation
     */
    (function(window, $) {
        'use strict';

        // L1 Cache: 30 Curated Global Talent Hubs (Grouped by Economic Region)
        var curatedLocations = [
            // 🇮🇳 Top Indian Metros & Tech Hubs
            { id: 57848, city_id: 57848, state_id: 24, country_id: 101, city: "Bengaluru", state: "Karnataka", country: "India", country_code: "IN", label: "Bengaluru | Karnataka | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Silicon Valley of India", aliases: ["bangalore", "bengaluru", "blr"] },
            { id: 132132, city_id: 132132, state_id: 36, country_id: 101, city: "Hyderabad", state: "Telangana", country: "India", country_code: "IN", label: "Hyderabad | Telangana | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "HITEC City", aliases: ["hyderabad", "secunderabad", "hyd"] },
            { id: 133504, city_id: 133504, state_id: 22, country_id: 101, city: "Pune", state: "Maharashtra", country: "India", country_code: "IN", label: "Pune | Maharashtra | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "IT & Auto Hub", aliases: ["pune", "poona"] },
            { id: 133024, city_id: 133024, state_id: 22, country_id: 101, city: "Mumbai", state: "Maharashtra", country: "India", country_code: "IN", label: "Mumbai | Maharashtra | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Financial Capital", aliases: ["mumbai", "bombay", "navi mumbai"] },
            { id: 133230, city_id: 133230, state_id: 34, country_id: 101, city: "Noida", state: "Uttar Pradesh", country: "India", country_code: "IN", label: "Noida | Uttar Pradesh | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Delhi NCR", aliases: ["noida", "greater noida"] },
            { id: 132032, city_id: 132032, state_id: 12, country_id: 101, city: "Gurugram", state: "Haryana", country: "India", country_code: "IN", label: "Gurugram | Haryana | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Delhi NCR", aliases: ["gurgaon", "gurugram", "ggn"] },
            { id: 131517, city_id: 131517, state_id: 31, country_id: 101, city: "Chennai", state: "Tamil Nadu", country: "India", country_code: "IN", label: "Chennai | Tamil Nadu | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "SaaS & Tech", aliases: ["chennai", "madras"] },
            { id: 142001, city_id: 142001, state_id: 35, country_id: 101, city: "Kolkata", state: "West Bengal", country: "India", country_code: "IN", label: "Kolkata | West Bengal | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "East Tech Hub", aliases: ["kolkata", "calcutta"] },
            { id: 134310, city_id: 134310, state_id: 11, country_id: 101, city: "Vadodara", state: "Gujarat", country: "India", country_code: "IN", label: "Vadodara | Gujarat | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Commercial Hub", aliases: ["vadodara", "baroda"] },
            { id: 134369, city_id: 134369, state_id: 2, country_id: 101, city: "Visakhapatnam", state: "Andhra Pradesh", country: "India", country_code: "IN", label: "Visakhapatnam | Andhra Pradesh | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Port & IT Hub", aliases: ["visakhapatnam", "vizag"] },

            // 🇺🇸 Top United States Tech Hubs
            { id: 114046, city_id: 114046, state_id: 5, country_id: 233, city: "San Francisco", state: "California", country: "United States", country_code: "US", label: "San Francisco | California | United States", category: "🇺🇸 United States Tech Hubs", badge: "Bay Area / AI Hub", aliases: ["san francisco", "sf", "bay area", "silicon valley"] },
            { id: 122795, city_id: 122795, state_id: 33, country_id: 233, city: "New York City", state: "New York", country: "United States", country_code: "US", label: "New York City | New York | United States", category: "🇺🇸 United States Tech Hubs", badge: "Fintech & Media", aliases: ["new york", "nyc", "manhattan"] },
            { id: 126104, city_id: 126104, state_id: 48, country_id: 233, city: "Seattle", state: "Washington", country: "United States", country_code: "US", label: "Seattle | Washington | United States", category: "🇺🇸 United States Tech Hubs", badge: "Cloud & Enterprise", aliases: ["seattle", "bellevue", "redmond"] },
            { id: 111668, city_id: 111668, state_id: 44, country_id: 233, city: "Austin", state: "Texas", country: "United States", country_code: "US", label: "Austin | Texas | United States", category: "🇺🇸 United States Tech Hubs", badge: "Silicon Hills", aliases: ["austin", "atx"] },
            { id: 112521, city_id: 112521, state_id: 22, country_id: 233, city: "Boston", state: "Massachusetts", country: "United States", country_code: "US", label: "Boston | Massachusetts | United States", category: "🇺🇸 United States Tech Hubs", badge: "Biotech & EdTech", aliases: ["boston", "cambridge"] },
            { id: 113702, city_id: 113702, state_id: 14, country_id: 233, city: "Chicago", state: "Illinois", country: "United States", country_code: "US", label: "Chicago | Illinois | United States", category: "🇺🇸 United States Tech Hubs", badge: "Midwest Tech Hub", aliases: ["chicago", "chi"] },

            // 🇬🇧 🇪🇺 Top UK & European Tech Hubs
            { id: 49011, city_id: 49011, state_id: 1, country_id: 232, city: "London", state: "England", country: "United Kingdom", country_code: "GB", label: "London | England | United Kingdom", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "Global FinTech Hub", aliases: ["london", "city of london", "greater london"] },
            { id: 112234, city_id: 112234, state_id: 11, country_id: 82, city: "Berlin", state: "Berlin", country: "Germany", country_code: "DE", label: "Berlin | Berlin | Germany", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "European Startup Hub", aliases: ["berlin"] },
            { id: 77340, city_id: 77340, state_id: 8, country_id: 157, city: "Amsterdam", state: "North Holland", country: "Netherlands", country_code: "NL", label: "Amsterdam | North Holland | Netherlands", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "Tech & E-Commerce", aliases: ["amsterdam"] },
            { id: 60100, city_id: 60100, state_id: 1, country_id: 104, city: "Dublin", state: "Leinster", country: "Ireland", country_code: "IE", label: "Dublin | Leinster | Ireland", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "European Tech HQ", aliases: ["dublin"] },

            // 🇦🇪 🇸🇬 Middle East & Asia-Pacific Hubs
            { id: 32, city_id: 32, state_id: 2, country_id: 231, city: "Dubai", state: "Dubai", country: "United Arab Emirates", country_code: "AE", label: "Dubai | Dubai | United Arab Emirates", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "MENA Tech Hub", aliases: ["dubai", "dxb"] },
            { id: 31, city_id: 31, state_id: 1, country_id: 231, city: "Abu Dhabi", state: "Abu Dhabi", country: "United Arab Emirates", country_code: "AE", label: "Abu Dhabi | Abu Dhabi | United Arab Emirates", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "Capital Hub", aliases: ["abu dhabi"] },
            { id: 104057, city_id: 104057, state_id: 1, country_id: 196, city: "Singapore", state: "Central Singapore", country: "Singapore", country_code: "SG", label: "Singapore | Central Singapore | Singapore", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "APAC FinTech Hub", aliases: ["singapore", "sg"] },
            { id: 17121, city_id: 17121, state_id: 9, country_id: 38, city: "Toronto", state: "Ontario", country: "Canada", country_code: "CA", label: "Toronto | Ontario | Canada", category: "🇨🇦 🇦🇺 Commonwealth Tech Hubs", badge: "AI & Tech Hub", aliases: ["toronto", "gta"] },
            { id: 7541, city_id: 7541, state_id: 2, country_id: 14, city: "Sydney", state: "New South Wales", country: "Australia", country_code: "AU", label: "Sydney | New South Wales | Australia", category: "🇨🇦 🇦🇺 Commonwealth Tech Hubs", badge: "Oceania Hub", aliases: ["sydney", "syd"] }
        ];

        // Client-First In-Memory Alias Dictionary (0ms Instant Recall)
        var citySynonymAliases = {
            'baroda': 'Vadodara',
            'vizag': 'Visakhapatnam',
            'calcutta': 'Kolkata',
            'bombay': 'Mumbai',
            'madras': 'Chennai',
            'poona': 'Pune',
            'banaras': 'Varanasi',
            'benares': 'Varanasi',
            'trivandrum': 'Thiruvananthapuram',
            'cochin': 'Kochi',
            'sf': 'San Francisco',
            'bay area': 'San Francisco',
            'nyc': 'New York City',
            'la': 'Los Angeles',
            'dc': 'Washington, D.C.'
        };

        // L2 Session LRU Cache (< 300 KB Browser Memory Budget)
        var lruCache = new Map();
        var MAX_LRU_ENTRIES = 100;
        var CACHE_TTL_MS = 30 * 60 * 1000; // 30 minutes

        function cacheGet(query) {
            var key = (query || '').toLowerCase().trim();
            if (!lruCache.has(key)) return null;
            var entry = lruCache.get(key);
            if (Date.now() - entry.timestamp > CACHE_TTL_MS) {
                lruCache.delete(key);
                return null;
            }
            lruCache.delete(key);
            lruCache.set(key, entry);
            return entry.data;
        }

        function cachePut(query, data) {
            var key = (query || '').toLowerCase().trim();
            if (lruCache.size >= MAX_LRU_ENTRIES) {
                var oldestKey = lruCache.keys().next().value;
                lruCache.delete(oldestKey);
            }
            lruCache.set(key, { data: data, timestamp: Date.now() });
        }

        // Network Engine State
        var activeAbortController = null;
        var requestSequence = 0;
        var selectionLocked = false;
        var debounceTimer = null;

        // Component DOM references
        var $component = $('#jero-cs-location');
        var $trigger = $component.find('.jero-cs-trigger');
        var $valueLabel = $component.find('.jero-cs-value');
        var $clearBtn = $component.find('.jero-cs-trigger-clear');
        var $dropdown = $component.find('.jero-cs-dropdown');
        var $searchInput = $component.find('.jero-cs-search-input');
        var $searchClear = $component.find('.jero-cs-search-clear');
        var $searchSpinner = $component.find('.jero-cs-search-spinner');
        var $list = $component.find('.jero-cs-list');
        var $emptyMsg = $component.find('.jero-cs-empty');
        var $disambiguationBar = $('#jero-location-disambiguation');

        // Canonical Hidden Form Inputs
        var $inputCityId = $('#location_city_id');
        var $inputStateId = $('#location_state_id');
        var $inputCountryId = $('#location_country_id');
        var $inputCanonicalLabel = $('#location_canonical_label');
        var $inputSig = $('#location_sig');
        var $inputTagifyLegacy = $('#kt_tagify_work_location');

        // Current Active Location Object
        var activeCanonicalLocation = null;

        /**
         * State 1: UNRESOLVED - Resets location state and inputs cleanly
         */
        function setUnresolvedState() {
            activeCanonicalLocation = null;
            selectionLocked = false;

            $inputCityId.val('');
            $inputStateId.val('');
            $inputCountryId.val('');
            $inputCanonicalLabel.val('');
            $inputSig.val('');
            $inputTagifyLegacy.val('');

            $valueLabel.text('Search city, state or country').addClass('jero-cs-placeholder');
            $clearBtn.hide();
            $disambiguationBar.hide().empty();
            $component.removeClass('is-open open-up');
            $trigger.attr('aria-expanded', 'false');

            // Synchronize with form validation
            if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
                window.benchFormValidator.revalidateField('kt_tagify_work_location');
            }
        }

        /**
         * State 3: RESOLVED - Locks in authoritative canonical location entity
         */
        function setResolvedState(locationEntity, source, confidence) {
            if (!locationEntity) return;
            selectionLocked = true;
            activeCanonicalLocation = locationEntity;

            // Abort any pending in-flight requests
            if (activeAbortController) {
                activeAbortController.abort();
                activeAbortController = null;
            }

            var cityId = locationEntity.city_id || locationEntity.id || '';
            var stateId = locationEntity.state_id || '';
            var countryId = locationEntity.country_id || '';
            var label = locationEntity.label || (locationEntity.city + ' | ' + locationEntity.state + ' | ' + locationEntity.country);
            var sig = locationEntity._sig || '';

            // Update Canonical and Legacy Payloads
            $inputCityId.val(cityId);
            $inputStateId.val(stateId);
            $inputCountryId.val(countryId);
            $inputCanonicalLabel.val(label);
            $inputSig.val(sig);
            $inputTagifyLegacy.val(JSON.stringify([{ value: label }]));

            // Update UI trigger
            $valueLabel.text(label).removeClass('jero-cs-placeholder');
            $clearBtn.show();
            $disambiguationBar.hide().empty();
            $component.removeClass('is-open open-up has-validation-error');
            $trigger.attr('aria-expanded', 'false');
            $component.closest('.bd-control').removeClass('has-validation-error');

            // Clear any validation errors
            if (typeof clearFieldValidationErrors === 'function') {
                clearFieldValidationErrors('kt_tagify_work_location');
            }
            if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
                window.benchFormValidator.revalidateField('kt_tagify_work_location');
            }
        }

        /**
         * State 4: NEEDS_CONFIRMATION - Displays 1-click disambiguation badges
         */
        function enterNeedsConfirmationState(rawQuery, candidates) {
            selectionLocked = false;
            $disambiguationBar.empty();

            var $label = $('<span>').addClass('text-muted fs-8 me-1').text('Did you mean:');
            $disambiguationBar.append($label);

            (candidates || []).forEach(function(cand, idx) {
                var isFirst = idx === 0;
                var $btn = $('<button>')
                    .attr('type', 'button')
                    .addClass('jero-disambiguation-badge' + (isFirst ? ' is-recommended' : ''))
                    .html((isFirst ? '★ ' : '') + cand.label)
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        setResolvedState(cand, 'disambiguation_click', 1.00);
                    });
                $disambiguationBar.append($btn);
            });

            var $cancelBtn = $('<button>')
                .attr('type', 'button')
                .addClass('btn btn-xs btn-link text-muted p-0 ms-1')
                .text('Search other')
                .on('click', function(e) {
                    e.preventDefault();
                    $disambiguationBar.hide().empty();
                    openDropdown();
                });
            $disambiguationBar.append($cancelBtn);
            $disambiguationBar.show();
        }

        /**
         * Render a list of location items into the dropdown
         */
        function renderLocations(locations, isCurated) {
            $list.empty();
            if (!locations || !locations.length) {
                $emptyMsg.removeClass('is-hidden');
                return;
            }
            $emptyMsg.addClass('is-hidden');

            var currentCategory = '';
            locations.forEach(function(loc) {
                // Group Header (for Curated Top Hubs)
                if (isCurated && loc.category && loc.category !== currentCategory) {
                    currentCategory = loc.category;
                    var $group = $('<div>').addClass('jero-cs-group-title').text(currentCategory);
                    $list.append($group);
                }

                var isSelected = activeCanonicalLocation && (
                    (activeCanonicalLocation.city_id && activeCanonicalLocation.city_id === loc.city_id) ||
                    (activeCanonicalLocation.label && activeCanonicalLocation.label === loc.label)
                );

                var $item = $('<div>')
                    .addClass('jero-cs-item' + (isSelected ? ' is-selected' : ''))
                    .attr('role', 'option')
                    .attr('aria-selected', isSelected ? 'true' : 'false')
                    .data('location', loc);

                var $text = $('<span>').addClass('jero-cs-item-text').text(loc.label);
                $item.append($text);

                if (loc.badge) {
                    var $badge = $('<span>').addClass('jero-cs-item-badge').text(loc.badge);
                    $item.append($badge);
                }

                $item.on('click', function(e) {
                    e.stopPropagation();
                    setResolvedState(loc, 'manual_selection', 1.00);
                });

                $list.append($item);
            });
        }

        /**
         * Fetch autocomplete suggestions from Elasticsearch
         */
        function executeSearch(query) {
            var rawQ = (query || '').trim();
            if (rawQ.length < 2) {
                renderLocations(curatedLocations, true);
                $searchSpinner.hide();
                return;
            }

            // Client-First In-Memory Alias Normalization (e.g. "Baroda" -> "Vadodara")
            var lowerQ = rawQ.toLowerCase();
            var mappedCity = citySynonymAliases[lowerQ];
            var searchTerm = mappedCity || rawQ;

            // Check L2 Session Memory Cache (0ms Latency)
            var cached = cacheGet(searchTerm);
            if (cached) {
                renderLocations(cached, false);
                $searchSpinner.hide();
                return;
            }

            // Abort previous in-flight request
            if (activeAbortController) {
                activeAbortController.abort();
            }
            activeAbortController = new AbortController();
            var currentSeq = ++requestSequence;
            $searchSpinner.show();

            var endpoint = "{{ url('/') }}/city-job-suggestions?q=" + encodeURIComponent(searchTerm) + "&format=canonical&limit=25";

            fetch(endpoint, {
                method: 'GET',
                signal: activeAbortController.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Network error: ' + res.status);
                return res.json();
            })
            .then(function(data) {
                $searchSpinner.hide();
                // Selection Lock Check: Drop if user selected an item while request was in-flight
                if (selectionLocked) return;
                // Sequence Check: Drop stale responses
                if (currentSeq !== requestSequence) return;

                var results = Array.isArray(data) ? data : (data.results || []);
                cachePut(searchTerm, results);
                renderLocations(results, false);
            })
            .catch(function(err) {
                $searchSpinner.hide();
                if (err.name === 'AbortError') return;
                console.warn('Elasticsearch location autocomplete notice:', err);
                // Fallback circuit breaker: show curated hubs if search fails
                renderLocations(curatedLocations, true);
            });
        }

        function openDropdown() {
            // Close other open comboboxes
            $('.jero-custom-select').not($component).removeClass('is-open open-up');
            $('.jero-cs-trigger').not($trigger).attr('aria-expanded', 'false');

            $component.addClass('is-open');
            $trigger.attr('aria-expanded', 'true');
            selectionLocked = false;

            // Viewport math for 15.6" screens: flip upward if space below is constrained (< 320px)
            var triggerRect = $trigger[0].getBoundingClientRect();
            var spaceBelow = window.innerHeight - triggerRect.bottom;
            if (spaceBelow < 320 && triggerRect.top > 320) {
                $component.addClass('open-up');
            } else {
                $component.removeClass('open-up');
            }

            var currentQuery = $searchInput.val().trim();
            if (currentQuery.length >= 2) {
                executeSearch(currentQuery);
            } else {
                renderLocations(curatedLocations, true);
            }

            setTimeout(function() {
                $searchInput.focus();
            }, 60);
        }

        function closeDropdown() {
            $component.removeClass('is-open open-up');
            $trigger.attr('aria-expanded', 'false');
        }

        // Trigger Click: Toggle Dropdown
        $trigger.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if ($component.hasClass('is-open')) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        // Clear Selection Button
        $clearBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            setUnresolvedState();
        });

        // Search Input Keystroke Handler (Debounced 150ms)
        $searchInput.on('input', function() {
            var q = $(this).val();
            if (q.trim().length > 0) {
                $searchClear.show();
            } else {
                $searchClear.hide();
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                executeSearch(q);
            }, 150);
        });

        // Search Clear Button
        $searchClear.on('click', function(e) {
            e.stopPropagation();
            $searchInput.val('').focus();
            $searchClear.hide();
            renderLocations(curatedLocations, true);
        });

        // Keyboard Navigation (ArrowUp, ArrowDown, Enter, Escape)
        $component.on('keydown', function(e) {
            var isOpen = $component.hasClass('is-open');
            if (!isOpen) {
                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    openDropdown();
                }
                return;
            }

            var $items = $list.find('.jero-cs-item');
            var $active = $items.filter('.is-active');
            var activeIdx = $items.index($active);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                var nextIdx = (activeIdx + 1) >= $items.length ? 0 : (activeIdx + 1);
                $items.removeClass('is-active');
                var $target = $items.eq(nextIdx).addClass('is-active');
                if ($target.length && $target[0].scrollIntoView) {
                    $target[0].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                var prevIdx = (activeIdx - 1) < 0 ? ($items.length - 1) : (activeIdx - 1);
                $items.removeClass('is-active');
                var $target = $items.eq(prevIdx).addClass('is-active');
                if ($target.length && $target[0].scrollIntoView) {
                    $target[0].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if ($active.length) {
                    $active.trigger('click');
                } else if ($items.length === 1) {
                    $items.first().trigger('click');
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeDropdown();
                $trigger.focus();
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest($component).length && !$(e.target).closest($disambiguationBar).length) {
                closeDropdown();
            }
        });

        /**
         * Resume Parser Confidence & Provenance Resolver
         * - Strict scoring matrix: >= 0.95 auto-resolves, 0.80 - 0.94 prompts disambiguation, < 0.80 never commits
         */
        function resolveAndSetCandidateLocation(rawLocationData) {
            var rawText = '';
            if (typeof rawLocationData === 'string') {
                rawText = rawLocationData;
            } else if (rawLocationData && typeof rawLocationData === 'object') {
                rawText = rawLocationData.value || rawLocationData.name || rawLocationData.text || rawLocationData.city || '';
            }
            rawText = (rawText || '').trim();
            if (!rawText) return;

            var clean = rawText.toLowerCase().replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ');

            // 1. Instant check against Curated Master Hubs (Confidence 1.00)
            for (var i = 0; i < curatedLocations.length; i++) {
                var hub = curatedLocations[i];
                if (hub.aliases && hub.aliases.indexOf(clean) !== -1) {
                    setResolvedState(hub, 'resume_parser', 1.00);
                    return;
                }
                if (hub.city.toLowerCase() === clean) {
                    setResolvedState(hub, 'resume_parser', 1.00);
                    return;
                }
            }

            // 2. Tokenize into primary city / state components
            var tokens = clean.split(/[,|\/\-]/).map(function(t) { return t.trim(); }).filter(Boolean);
            var primaryToken = tokens[0] || clean;

            // Apply client synonym mapping if present
            if (citySynonymAliases[primaryToken]) {
                primaryToken = citySynonymAliases[primaryToken].toLowerCase();
            }

            var endpoint = "{{ url('/') }}/city-job-suggestions?q=" + encodeURIComponent(primaryToken) + "&format=canonical&limit=10";

            fetch(endpoint, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Parser lookup error: ' + res.status);
                return res.json();
            })
            .then(function(data) {
                var results = Array.isArray(data) ? data : (data.results || []);
                if (!results.length) return;

                var bestMatch = null;
                var bestConfidence = 0.0;
                var homonymMatches = [];

                for (var k = 0; k < results.length; k++) {
                    var item = results[k];
                    var itemCity = (item.city || '').toLowerCase();
                    var itemLabel = (item.label || '').toLowerCase();

                    // Exact match on full label or slash alias
                    if (itemLabel.indexOf(clean) !== -1 || (item.aliases && item.aliases.indexOf(clean) !== -1)) {
                        bestMatch = item;
                        bestConfidence = 1.00;
                        break;
                    }
                    // Exact match on city name
                    if (itemCity === primaryToken) {
                        homonymMatches.push(item);
                        if (!bestMatch) {
                            bestMatch = item;
                            bestConfidence = 0.95;
                        }
                    }
                }

                // If multiple cities have the exact same name (e.g. Austin TX vs Austin AR), downgrade confidence
                if (homonymMatches.length > 1 && bestConfidence < 1.00) {
                    bestConfidence = 0.85;
                }

                if (bestMatch && bestConfidence >= 0.95) {
                    // Safe auto-commit
                    setResolvedState(bestMatch, 'resume_parser', bestConfidence);
                } else if (results.length > 0 && bestConfidence >= 0.80) {
                    // Ambiguous match: STRICT INVARIANT - Never auto-commit! Transition to NEEDS_CONFIRMATION
                    enterNeedsConfirmationState(rawText, (homonymMatches.length ? homonymMatches : results).slice(0, 3));
                }
            })
            .catch(function(err) {
                console.warn('Parser location resolution notice:', err);
            });
        }

        // Register Global Triple-Alias Bridge for Resume Parsers
        window.resolveAndSetBenchLocation = resolveAndSetCandidateLocation;
        window.setSingleLocationTag = resolveAndSetCandidateLocation;
        window.resolveAndSetCandidateLocation = resolveAndSetCandidateLocation;

        // Synchronous 0ms Edit-Mode Prefill (Eliminates fragile 2000ms delay and "Ballia" hack)
        var defaultLocStr = {!! json_encode(@$default_loc) !!};
        var defaultCityId = {!! json_encode(@$application->city_id) !!};
        var defaultStateId = {!! json_encode(@$application->state) !!};
        var defaultCountryId = {!! json_encode(@$application->country) !!};

        if (defaultLocStr || defaultCityId) {
            setResolvedState({
                id: defaultCityId || '',
                city_id: defaultCityId || '',
                state_id: defaultStateId || '',
                country_id: defaultCountryId || '',
                label: defaultLocStr || '',
                _sig: ''
            }, 'initial_prefill', 1.00);
        }

    })(window, jQuery);
</script>
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
/**
 * JERO Enterprise Adaptive Nationality & Education Combobox Engine
 * - 0% MySQL Reads, 100% In-Browser RAM execution (< 1ms search latency)
 * - Guaranteed Sticky Search Box at the top of both dropdowns
 * - Real-time Country-Adaptive Education curation
 * - Global Escape Hatch: Typing in Highest Qualification searches 100% worldwide degrees with synonym aliases
 * - 100% Two-Way Synchronization with native <select> elements for form validation & controller payload
 * - Strict Rule 17 Page Isolation
 */
(function($) {
    'use strict';

    function getRegionalBucket(nationality) {
        var nat = (nationality || '').trim();
        if (!nat || nat === 'Indian') return ['Indian'];
        if (nat === 'American') return ['American'];
        if (nat === 'Canadian') return ['Canadian', 'American'];
        if (nat === 'British') return ['British', 'Commonwealth'];
        if (nat === 'Australian') return ['Australian', 'Commonwealth', 'British'];
        if (['German', 'French', 'Dutch', 'Irish', 'Italian', 'Spanish', 'Swedish', 'Swiss', 'Austrian', 'Belgian', 'Polish'].indexOf(nat) !== -1) {
            return ['European'];
        }
        if (['Emirati', 'Saudi', 'Qatari', 'Kuwaiti', 'Bahraini', 'Omani'].indexOf(nat) !== -1) {
            return ['Indian', 'British', 'American'];
        }
        return ['all']; // 'Other' or unlisted demonyms show all degrees cleanly
    }

    function isQualInBucket(qualId, nationality) {
        if (!qualId) return true;
        var $opt = $('#qualification option[value="' + qualId + '"]');
        if (!$opt.length) return true;
        var bucket = getRegionalBucket(nationality);
        if (bucket.indexOf('all') !== -1) return true;
        var regions = ($opt.attr('data-regions') || 'Global').split(',').map(function(r) { return r.trim(); });
        if (regions.indexOf('Global') !== -1) return true;
        for (var k = 0; k < bucket.length; k++) {
            if (regions.indexOf(bucket[k]) !== -1) {
                return true;
            }
        }
        return false;
    }

    function closeAllComboboxes() {
        $('.jero-custom-select').removeClass('is-open open-up');
        $('.jero-cs-trigger').attr('aria-expanded', 'false');
        $('.bd-card').removeClass('has-active-combobox');
    }

    // Token-based, accent-insensitive and punctuation-insensitive string cleaner
    function jeroCleanStr(str) {
        if (!str) return '';
        return str.toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // Build the Nationality Custom Combobox
    function buildNationalityCombobox() {
        var $select = $('#nationality');
        if (!$select.length) return;
        $('#jero-cs-nationality').remove();

        var placeholder = 'Select nationality';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text();
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-nationality">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-regular fa-flag"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search nationality or country (e.g. India, USA, UK, Canada)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-circle-question"></i> No nationality found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text();
                    var synonyms = $opt.attr('data-synonyms') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            } else if ($node.is('option') && $node.val()) {
                var val = $node.val();
                var text = $node.text();
                var synonyms = $node.attr('data-synonyms') || '';
                var isSelected = (val === currentVal && !selectedItemDone);
                if (isSelected) selectedItemDone = true;
                var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                    '<span class="jero-cs-item-text">' + text + '</span>' +
                '</div>');
                $list.append($item);
            }
        });

        $select.closest('.bd-control').addClass('bd-control--has-combobox');
        $select.after($cs);
    }

    // Build the Qualification Custom Combobox
    function buildQualificationCombobox() {
        var $select = $('#qualification');
        if (!$select.length) return;
        $('#jero-cs-qualification').remove();

        var placeholder = 'Select qualification';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        var currentBadge = '';
        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text();
                currentBadge = $selOpt.attr('data-badge') || '';
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-qualification">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-solid fa-graduation-cap"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-badge" style="' + (currentBadge ? '' : 'display:none;') + '">' + currentBadge + '</span>' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search degree or acronym (e.g. B.Tech, MS, HND, CA, MBA)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-graduation-cap"></i> No matching degrees found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text();
                    var regions = $opt.attr('data-regions') || 'Global';
                    var aliases = $opt.attr('data-aliases') || '';
                    var badge = $opt.attr('data-badge') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var badgeHtml = badge ? '<span class="jero-cs-item-badge">' + badge + '</span>' : '';
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-regions="' + regions + '" data-aliases="' + aliases + '" data-badge="' + badge + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                        badgeHtml +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            }
        });

        $select.closest('.bd-control').addClass('bd-control--has-combobox');
        $select.after($cs);
    }

    // Filter Qualification items by selected nationality (idle state) or search query (global unlock)
    function filterQualificationItems() {
        var $cs = $('#jero-cs-qualification');
        if (!$cs.length) return;
        var selectedNat = ($('#nationality').val() || '').trim();
        var selectedQual = ($('#qualification').val() || '').trim();
        var bucket = getRegionalBucket(selectedNat);
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var val = String($item.attr('data-val') || '');
                var text = $item.find('.jero-cs-item-text').text() || '';
                var aliases = $item.attr('data-aliases') || '';
                var badge = $item.attr('data-badge') || '';
                var regions = ($item.attr('data-regions') || 'Global').split(',').map(function(r) { return r.trim(); });

                var isMatch = false;

                if (cleanQ.length > 0) {
                    var cleanText = jeroCleanStr(text);
                    var cleanAliases = jeroCleanStr(aliases);
                    var cleanBadge = jeroCleanStr(badge);
                    var combined = cleanText + ' ' + cleanAliases + ' ' + cleanBadge;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanAliases.indexOf(cleanQ) !== -1 || cleanBadge.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) {
                            isMatch = true;
                        }
                    }
                } else {
                    if (selectedQual && val === selectedQual) {
                        isMatch = true;
                    } else if (bucket.indexOf('all') !== -1 || regions.indexOf('Global') !== -1) {
                        isMatch = true;
                    } else {
                        for (var b = 0; b < bucket.length; b++) {
                            if (regions.indexOf(bucket[b]) !== -1) {
                                isMatch = true;
                                break;
                            }
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // Filter Nationality items by country synonyms or demonym text
    function filterNationalityItems() {
        var $cs = $('#jero-cs-nationality');
        if (!$cs.length) return;
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var text = $item.find('.jero-cs-item-text').text() || '';
                var synonyms = $item.attr('data-synonyms') || '';
                var val = $item.attr('data-val') || '';

                var isMatch = false;
                if (!cleanQ) {
                    isMatch = true;
                } else {
                    var cleanText = jeroCleanStr(text);
                    var cleanSynonyms = jeroCleanStr(synonyms);
                    var cleanVal = jeroCleanStr(val);
                    var combined = cleanText + ' ' + cleanSynonyms + ' ' + cleanVal;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanSynonyms.indexOf(cleanQ) !== -1 || cleanVal.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) {
                            isMatch = true;
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // Open/Toggle Combobox
    $(document).on('click', '.jero-cs-trigger', function(e) {
        e.stopPropagation();
        var $combobox = $(this).closest('.jero-custom-select');
        var wasOpen = $combobox.hasClass('is-open');

        closeAllComboboxes();

        if (!wasOpen) {
            var rect = this.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 340 && rect.top > 340) {
                $combobox.addClass('open-up');
            } else {
                $combobox.removeClass('open-up');
            }

            $combobox.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
            $combobox.closest('.bd-card').addClass('has-active-combobox');

            var $search = $combobox.find('.jero-cs-search-input');
            $search.val('');
            $combobox.find('.jero-cs-search-clear').hide();

            if ($combobox.attr('id') === 'jero-cs-nationality') {
                filterNationalityItems();
            } else if ($combobox.attr('id') === 'jero-cs-qualification') {
                filterQualificationItems();
            }

            setTimeout(function() {
                $search.focus().select();
            }, 60);
        }
    });

    // Prevent clicks inside dropdown from bubbling and closing
    $(document).on('click', '.jero-cs-dropdown', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', '.jero-cs-search-box', function(e) {
        e.stopPropagation();
        $(this).find('.jero-cs-search-input').focus();
    });

    // Search Input Typing Handler
    $(document).on('input keyup change', '.jero-cs-search-input', function(e) {
        if (e.which === 27 || e.which === 13 || e.which === 38 || e.which === 40) return;
        var $input = $(this);
        var val = $input.val();
        var $clearBtn = $input.siblings('.jero-cs-search-clear');
        if (val) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }

        var $combobox = $input.closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Clear Search Input Button
    $(document).on('click', '.jero-cs-search-clear', function(e) {
        e.stopPropagation();
        var $input = $(this).siblings('.jero-cs-search-input');
        $input.val('').focus();
        $(this).hide();
        var $combobox = $(this).closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Keyboard Navigation: Escape, Arrows, Enter
    $(document).on('keydown', '.jero-custom-select.is-open', function(e) {
        var $combobox = $(this);
        var $visibleItems = $combobox.find('.jero-cs-item:not(.is-hidden):visible');

        if (e.which === 27) { // Escape
            closeAllComboboxes();
            $combobox.find('.jero-cs-trigger').focus();
            e.preventDefault();
        } else if (e.which === 40) { // Arrow Down
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx === -1 || idx >= $visibleItems.length - 1) {
                var $target = $visibleItems.first();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx + 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 38) { // Arrow Up
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx <= 0) {
                var $target = $visibleItems.last();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx - 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 13) { // Enter
            var $target = $visibleItems.filter('.is-highlighted');
            if (!$target.length) {
                $target = $visibleItems.first();
            }
            if ($target.length) {
                $target.trigger('click');
            }
            e.preventDefault();
        }
    });

    // Select Nationality Item
    $(document).on('click', '#jero-cs-nationality .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text();

        var $cs = $('#jero-cs-nationality');
        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        $cs.removeClass('has-validation-error');
        $cs.closest('.bd-control').removeClass('has-validation-error');
        $('[data-field-error="nationality"]').remove();
        closeAllComboboxes();

        var $nativeNat = $('#nationality');
        $nativeNat.val(val).trigger('change');

        var curQual = ($('#qualification').val() || '').trim();
        if (curQual && !isQualInBucket(curQual, val)) {
            $('#qualification').val('').trigger('change');
            var $qualCs = $('#jero-cs-qualification');
            $qualCs.find('.jero-cs-item').removeClass('is-selected');
            $qualCs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select qualification');
            $qualCs.find('.jero-cs-badge').hide().text('');
        }

        filterQualificationItems();

        if (window.benchFormValidator && window.benchFormValidator.revalidateField) {
            window.benchFormValidator.revalidateField('nationality');
            window.benchFormValidator.revalidateField('qualification');
        }
    });

    // Select Qualification Item
    $(document).on('click', '#jero-cs-qualification .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text();
        var badge = $item.attr('data-badge') || '';

        var $cs = $('#jero-cs-qualification');
        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        if (badge) {
            $cs.find('.jero-cs-badge').show().text(badge);
        } else {
            $cs.find('.jero-cs-badge').hide().text('');
        }
        $cs.removeClass('has-validation-error');
        $cs.closest('.bd-control').removeClass('has-validation-error');
        $('[data-field-error="qualification"]').remove();
        closeAllComboboxes();

        $('#qualification').val(val).trigger('change');

        if (window.benchFormValidator && window.benchFormValidator.revalidateField) {
            window.benchFormValidator.revalidateField('qualification');
        }
    });

    // Click outside to close any open dropdowns
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.jero-custom-select').length) {
            closeAllComboboxes();
        }
    });

    // Native <select> external change listener (e.g. from resume parser or prefill)
    $(document).on('change', '#nationality', function() {
        var val = $(this).val();
        var $cs = $('#jero-cs-nationality');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text());
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select nationality');
            }
            filterQualificationItems();
        }
    });

    $(document).on('change', '#qualification', function() {
        var val = $(this).val();
        var $cs = $('#jero-cs-qualification');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text());
                var badge = $item.attr('data-badge') || '';
                if (badge) {
                    $cs.find('.jero-cs-badge').show().text(badge);
                } else {
                    $cs.find('.jero-cs-badge').hide().text('');
                }
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select qualification');
                $cs.find('.jero-cs-badge').hide().text('');
            }
        }
    });

    // Mount Custom Comboboxes on DOM Ready
    $(document).ready(function() {
        buildNationalityCombobox();
        buildQualificationCombobox();
        filterQualificationItems();
    });

})(jQuery);
</script>
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

  $(document).on('click', '#rmPicker, #rmAddMoreBtn', function(e) {
      e.preventDefault();
      $('#kt_activitiesRecruiter_toggle').trigger('click');

      // Auto-load RM drawer data if not yet loaded (fixes blank drawer issue)
      var $body = $('#kt_activitiesRecruiter_body');
      if ($body.find('#kt_activities_body').length === 0) {
          var client_id = "{{ @$client->id }}";
          var recruiter    = $('#recruiter').val() || '';
          var recruiter_id = $('#recruiter_id').val() || '';
          var token        = "{{ csrf_token() }}";
          var baseurl      = "{{ asset('assets/images/Loading2.gif') }}";
          var url          = "{{ route('service_company.jobs.recruiter',':id') }}".replace(':id', client_id || 0);
          $body.html('<div style="text-align:center;padding:30px;"><img src="' + baseurl + '" height="35px"></div>');
          $.ajax({
              type: 'GET',
              url: url,
              data: { '_token': token, 'recruiter': recruiter, 'recruiter_id': recruiter_id, 'from': 'bench-candidates' },
              success: function(response) {
                  $body.html(response.view);
                  if (typeof crmRenderSelected === 'function') {
                      crmRenderSelected();
                  }
              },
              error: function() {
                  $body.html('<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;"><i class="fa-solid fa-circle-exclamation" style="color:#f87171;"></i> Failed to load team. Please try again.</div>');
              }
          });
      } else {
          if (typeof crmRenderSelected === 'function') {
              crmRenderSelected();
          }
      }
  });

  $(document).on('click', '.rm-chip-btn-remove', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var rmId = String($(this).data('rm-id') || '');
      var rmName = String($(this).data('rm-name') || '').trim();

      removeRelationshipManagerById(rmId, rmName);

      var curNames = ($('#recruiter').val() || '').split(',').map(function(s) { return s.trim(); }).filter(Boolean);
      var curIds   = ($('#recruiter_id').val() || '').split(',').map(function(s) { return s.trim(); }).filter(Boolean);

      var newNames = curNames.filter(function(n) { return n.toLowerCase() !== rmName.toLowerCase(); });
      var newIds   = curIds.filter(function(i) { return String(i) !== rmId; });

      $('#recruiter').val(newNames.join(' , '));
      $('#recruiter_id').val(newIds.join(' , '));

      if (rmId) {
          var $btn = $('#team_select_' + rmId);
          if ($btn.length) {
              $btn.removeClass('crm-remove').addClass('crm-add').text('Add').attr('data-assign', 0);
          }
          if (typeof crmRenderSelected === 'function') {
              crmRenderSelected();
          }
      }

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

// ── Redesigned Dynamic Multi-Relationship Manager System (media_1790669427546.png) ──
window.__rmSelectedManagers = window.__rmSelectedManagers || [];

function rmEscape(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function rmInitials(name) {
    var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return 'RM';
    var first = parts[0] ? parts[0].charAt(0) : '';
    var second = parts.length > 1 ? parts[parts.length - 1].charAt(0) : '';
    return (first + second || first || 'RM').toUpperCase();
}

function registerRelationshipManager(manager) {
    if (!manager || !manager.name) return;
    window.__rmSelectedManagers = window.__rmSelectedManagers || [];
    var exists = window.__rmSelectedManagers.some(function(m) {
        return (manager.id && String(m.id) === String(manager.id)) || (m.name.toLowerCase() === manager.name.toLowerCase());
    });
    if (!exists) {
        window.__rmSelectedManagers.push(manager);
    }
}

function removeRelationshipManagerById(id, name) {
    window.__rmSelectedManagers = (window.__rmSelectedManagers || []).filter(function(m) {
        if (id && String(m.id) === String(id)) return false;
        if (name && m.name.toLowerCase() === name.toLowerCase()) return false;
        return true;
    });
}

function getRmSelectedList() {
    var rawNames = ($('#recruiter').val() || '').split(',').map(function(s) { return s.trim(); }).filter(Boolean);
    var rawIds   = ($('#recruiter_id').val() || '').split(',').map(function(s) { return s.trim(); }).filter(Boolean);
    var pool = (window.__rmSelectedManagers || []).concat(window.__rmInitialList || []).concat(window.__crmTeam || []);
    var list = [];

    rawNames.forEach(function(name, index) {
        var id = rawIds[index] || '';
        var match = pool.find(function(m) {
            return (id && String(m.id) === String(id)) || (m.name && m.name.toLowerCase() === name.toLowerCase());
        });
        list.push({
            id: id || (match && match.id) || ('rm_' + index),
            name: (match && match.name) || name,
            email: (match && match.email) || '',
            phone: (match && match.phone) || ''
        });
    });

    return list;
}

function renderRelationshipManagerCards() {
    var managers = getRmSelectedList();
    var $container = $('#rmChipsContainer');
    var $picker = $('#rmPicker');

    if (!managers || !managers.length) {
        $container.empty().hide();
        $picker.removeClass('has-selection');
        $('#recruiter_display').val('').attr('placeholder', 'Search by name or email...');
        $('#rmPickerAction').text('Select Relationship Manager');
        $('#job_additional_detail').removeClass('rm-has-selection');
        return;
    }

    $picker.addClass('has-selection');
    $picker.removeClass('has-validation-error');
    $('[data-field-error="recruiter"]').remove();
    $('#job_additional_detail').addClass('rm-has-selection');
    $('#recruiter_display').val('').attr('placeholder', 'Search by name or email...');
    $('#rmPickerAction').text('Select Relationship Manager');

    var html = '';
    managers.forEach(function(m) {
        var initials = rmInitials(m.name);
        var subText = m.email || 'Relationship Manager';
        var phoneDisplay = m.phone ? rmEscape(m.phone) : '—';
        var emailDisplay = m.email ? rmEscape(m.email) : '—';

        html += '<div class="rm-chip-card" data-rm-id="' + rmEscape(m.id) + '" data-rm-name="' + rmEscape(m.name) + '">'
              +   '<div class="rm-chip-left">'
              +     '<div class="rm-chip-avatar">' + initials + '</div>'
              +     '<div class="rm-chip-identity">'
              +       '<div class="rm-chip-name">' + rmEscape(m.name) + '</div>'
              +       '<div class="rm-chip-sub">' + rmEscape(subText) + '</div>'
              +     '</div>'
              +   '</div>'
              +   '<div class="rm-chip-divider"></div>'
              +   '<div class="rm-chip-col rm-chip-phone">'
              +     '<i class="fa-solid fa-phone rm-chip-icon"></i>'
              +     '<span class="rm-chip-text">' + phoneDisplay + '</span>'
              +   '</div>'
              +   '<div class="rm-chip-divider"></div>'
              +   '<div class="rm-chip-col rm-chip-email">'
              +     '<i class="fa-regular fa-envelope rm-chip-icon"></i>'
              +     '<span class="rm-chip-text">' + emailDisplay + '</span>'
              +   '</div>'
              +   '<div class="rm-chip-actions">'
              +     '<span class="rm-chip-badge-selected"><i class="fa-solid fa-circle-check"></i> Selected</span>'
              +     '<button type="button" class="rm-chip-btn-remove" data-rm-id="' + rmEscape(m.id) + '" data-rm-name="' + rmEscape(m.name) + '" title="Remove Relationship Manager">'
              +       '<i class="fa-solid fa-trash-can"></i> Remove'
              +     '</button>'
              +   '</div>'
              + '</div>';
    });

    $container.html(html).show();
}

function syncRelationshipManagerCard() {
    window.__rmLastRecruiterValue = $('#recruiter').val() || '';
    renderRelationshipManagerCards();
}

window.syncRelationshipManagerCard = syncRelationshipManagerCard;
window.registerRelationshipManager = registerRelationshipManager;
window.removeRelationshipManagerById = removeRelationshipManagerById;

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
      if (typeof syncAllCommercialCurrencies === 'function') {
          syncAllCommercialCurrencies($(this).val());
      } else {
          $('#bill_rate_currency').val($(this).val());
      }
   });
   $('body').on('change', '#bill_rate_currency', function(){
      if (typeof syncAllCommercialCurrencies === 'function') {
          syncAllCommercialCurrencies($(this).val());
      } else {
          $('#pay_rate_currency').val($(this).val());
      }
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

function handleTalentSubmissionSuccess(response) {
    if (!response || response.status !== 'success' || response.msgType === 'danger' || response.msgType === 'warning') {
        Swal.fire({
            title: response && response.msgType === 'warning' ? "Warning" : "Error",
            text: (response && response.message) || "Submission failed. Please check the form and try again.",
            icon: response && response.msgType === 'warning' ? "warning" : "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: { confirmButton: "btn btn-primary btn-sm" }
        });
        $("#save-form, #edit-form").prop("disabled", false);
        if ($("#edit-form").length) {
            $("#edit-form").html("Update");
        }
        if (typeof window.syncTalentStageAction === 'function') {
            window.syncTalentStageAction();
        }
        const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID)
            ? crypto.randomUUID()
            : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
        $('#client_submission_uuid').val(freshUuid);
        return false;
    }

    // Hide any previous in-page validation alert
    $('#formValidationNotificationBox').addClass('d-none');

    const redirectUrl = response.redirect_url || response.url || '{{ route("service_company.bench-candidates.index") }}';
    let redirectExecuted = false;

    function executeRedirect() {
        if (!redirectExecuted) {
            redirectExecuted = true;
            window.location.href = redirectUrl;
        }
    }

    var isEditMode = String(@json($page_event ?? '')).toUpperCase() === 'EDIT';
    var defaultSuccess = isEditMode ? "Inhouse Talent Updated Successfully" : "Inhouse Talent Added Successfully";
    var successTitle = (response && response.message) ? response.message : defaultSuccess;
    // Map legacy 'Job Application' text if present in any cached response
    if (successTitle.toLowerCase().indexOf('job application') !== -1) {
        successTitle = defaultSuccess;
    }

    // 1. Primary: SweetAlert confirmation button
    Swal.fire({
        title: successTitle,
        text: isEditMode ? "Talent details have been updated successfully." : "Inhouse talent profile has been added successfully.",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "View Bench Listing",
        customClass: { confirmButton: "btn btn-primary btn-sm" }
    }).then(function(result) {
        executeRedirect();
    });

    if (typeof toastr !== 'undefined') {
        toastr.success(successTitle);
    }

    // 2. Secondary: Guaranteed hard timer fallback (2,200ms)
    setTimeout(executeRedirect, 2200);

    // 3. Tertiary: Window unload insurance
    window.addEventListener('beforeunload', function() {
        redirectExecuted = true;
    });
}

var fieldConfigMap = {
    'resume': { label: 'Upload Resume', selector: '#uploadBox', message: 'Please upload a resume file.' },
    'careersummary': { label: 'Professional Summary', selector: '#careersummary', message: 'Professional summary is required.' },
    'full_name': { label: 'Full Name', selector: '#full_name', message: 'Full name is required.' },
    'email': { label: 'Primary Email', selector: '#email', message: 'A valid primary email is required.' },
    'phone': { label: 'Primary Phone', selector: '#phone', message: 'Primary mobile number is required.' },
    'kt_tagify_work_location': { label: 'Current Location', selector: '#kt_tagify_work_location', message: 'Current location is required.' },
    'qualification': { label: 'Highest Qualification', selector: '#qualification', message: 'Highest qualification is required.' },
    'nationality': { label: 'Nationality', selector: '#nationality', message: 'Nationality is required.' },
    'gender': { label: 'Gender', selector: '#gender', message: 'Gender is required.' },
    'current_company': { label: 'Current Company', selector: '#current_company', message: 'Current company is required.' },
    'current_designation': { label: 'Current Designation', selector: '#current_designation', message: 'Current designation is required.' },
    'employmentType': { label: 'Client Engagement Model', selector: '#EmploymentType', message: 'Client engagement model is required.' },
    'total_exp': { label: 'Total Experience', selector: '#total_exp_year', message: 'Total experience is required.' },
    'rel_exp': { label: 'Relevant Experience', selector: '#rel_exp_year', message: 'Relevant experience is required.' },
    'serving_notice_period': { label: 'Availability', selector: '#serving_notice_period', message: 'Availability is required.' },
    'np_id': { label: 'Available From Date', selector: '#np_id_display', message: 'Available from date is required.' },
    'wm': { label: 'Preferred Work Mode', selector: '#wr', message: 'Preferred work mode is required.' },
    'pay_rate': { label: 'Candidate Cost (Pay Rate)', selector: '#pay_annual', message: 'Candidate Cost (Pay Rate) is required.' },
    'bill_rate': { label: 'Client Bill Rate', selector: '#bill_annual', message: 'Client Bill Rate is required.' },
    'skills': { label: 'Skills & Expertise (Min. 3 Complete Skills)', selector: '#skillExperienceWrapper', message: 'At least 3 complete skills (Skill name, Experience, Rating) are required.' },
    'recruiter': { label: 'Relationship Manager', selector: '#rmPicker', message: 'Please select a Relationship Manager.' }
};

window.__currentMissingFields = [];

function collectFormValidationErrors(formEl, validatorInstance) {
    var errors = [];
    if (formEl) {
        var msgEls = formEl.querySelectorAll('.fv-plugins-message-container .fv-help-block');
        msgEls.forEach(function (el) {
            var txt = (el.innerText || el.textContent || '').trim();
            if (txt && errors.indexOf(txt) === -1) {
                errors.push(txt);
            }
        });
    }
    return errors;
}

function validateSkillsAndExpertise() {
    var $rows = $('#skillExperienceWrapper tr');
    var completeSkills = [];
    var incompleteRows = [];

    $rows.each(function (idx, tr) {
        var $tr = $(tr);
        var skillName = ($tr.find('input[name="skills[]"]').val() || '').trim();
        var years = $tr.find('select[name="years[]"]').val();
        var rating = $tr.find('select[name="ratings[]"]').val();

        var hasSkill = skillName !== '';
        var hasYears = (years !== '' && years !== null && years !== undefined);
        var hasRating = (rating !== '' && rating !== null && rating !== undefined);

        var hasAnyDetail = hasSkill || hasYears || hasRating;

        if (hasAnyDetail) {
            if (hasSkill && hasYears && hasRating) {
                completeSkills.push({
                    rowEl: tr,
                    index: idx,
                    skillName: skillName,
                    years: years,
                    rating: rating
                });
            } else {
                incompleteRows.push({
                    rowEl: tr,
                    index: idx,
                    skillName: skillName,
                    missingSkill: !hasSkill,
                    missingYears: !hasYears,
                    missingRating: !hasRating
                });
            }
        }
    });

    if (incompleteRows.length > 0) {
        var firstInc = incompleteRows[0];
        var detailDesc = [];
        if (firstInc.missingSkill) detailDesc.push('Skill Name');
        if (firstInc.missingYears) detailDesc.push('Experience');
        if (firstInc.missingRating) detailDesc.push('Rating');

        return {
            valid: false,
            type: 'incomplete',
            label: 'Skills & Expertise (Provide ' + detailDesc.join(', ') + ' or delete row)',
            targetRow: firstInc.rowEl,
            missingDetail: detailDesc.join(', '),
            count: completeSkills.length
        };
    }

    if (completeSkills.length < 3) {
        return {
            valid: false,
            type: 'min_count',
            label: 'Skills & Expertise (Minimum 3 Complete Skills Required &mdash; ' + completeSkills.length + '/3 Added)',
            targetRow: null,
            count: completeSkills.length
        };
    }

    return {
        valid: true,
        count: completeSkills.length
    };
}

function detectMissingFields(formEl, validatorInstance) {
    var missing = [];
    var seenKeys = {};

    function addMissing(key, customLabel, customSelector, customMessage) {
        if (!seenKeys[key]) {
            seenKeys[key] = true;
            var cfg = fieldConfigMap[key] || {};
            missing.push({
                key: key,
                label: customLabel || cfg.label || key,
                selector: customSelector || cfg.selector || ('#' + key),
                message: customMessage || cfg.message || ((customLabel || cfg.label || key) + ' is required.')
            });
        }
    }

    // 1. Upload Resume (Mandatory)
    var hasResume = false;
    if ($('#resumeCheck').val() === '1' || ($('#uploaded_file_path').val() && String($('#uploaded_file_path').val()).trim()) || ($('#resume_original_name').val() && String($('#resume_original_name').val()).trim())) {
        hasResume = true;
    } else {
        var ri = document.getElementById('resume');
        if (ri && ri.files && ri.files.length > 0) {
            hasResume = true;
        } else if ($('#fileInfo').is(':visible') && $('#fileName').text().trim()) {
            hasResume = true;
        } else if ($('#upInfo').is(':visible') && $('#upFileName').text().trim()) {
            hasResume = true;
        }
    }
    if (!hasResume) {
        addMissing('resume', 'Upload Resume', '#uploadBox');
    }

    // 2. Professional Summary (Mandatory)
    var cs = ($('#careersummary').val() || '').trim();
    if (!cs) {
        addMissing('careersummary', 'Professional Summary', '#careersummary');
    }

    // 3. Candidate Profile: all fields including gender, only ignore date of birth
    var fn = ($('#full_name').val() || '').trim();
    if (!fn) addMissing('full_name', 'Full Name', '#full_name');

    var em = ($('#email').val() || '').trim();
    if (!em || !/\S+@\S+\.\S+/.test(em)) addMissing('email', 'Primary Email', '#email');

    if (typeof window.validateCreateTalentPhone === 'function') {
        if (!window.validateCreateTalentPhone('phone', true)) {
            addMissing('phone', 'Primary Phone', '#phone');
        }
    } else {
        var ph = ($('#phone').val() || '').trim();
        if (!ph) addMissing('phone', 'Primary Phone', '#phone');
    }

    var loc = ($('#kt_tagify_work_location').val() || '').trim();
    if (!loc) addMissing('kt_tagify_work_location', 'Current Location', '#kt_tagify_work_location');

    var qual = ($('#qualification').val() || '').trim();
    if (!qual) addMissing('qualification', 'Highest Qualification', '#qualification');

    var nat = ($('#nationality').val() || '').trim();
    if (!nat) addMissing('nationality', 'Nationality', '#nationality');

    var gen = ($('#gender').val() || '').trim();
    if (!gen) addMissing('gender', 'Gender', '#gender');

    // 4. Professional Profile: except Employee ID (Optional)
    var cc = ($('#current_company').val() || '').trim();
    if (!cc) addMissing('current_company', 'Current Company', '#current_company');

    var cd = ($('#current_designation').val() || '').trim();
    if (!cd) addMissing('current_designation', 'Current Designation', '#current_designation');

    var et = ($('#EmploymentType').val() || '').trim();
    if (!et) addMissing('employmentType', 'Client Engagement Model', '#EmploymentType');

    var te = $('#total_exp_year').val();
    if (te === '' || te === null || te === undefined) addMissing('total_exp', 'Total Experience', '#total_exp_year');

    var re = $('#rel_exp_year').val();
    if (re === '' || re === null || re === undefined) addMissing('rel_exp', 'Relevant Experience', '#rel_exp_year');

    var snp = $('#serving_notice_period').val();
    if (!snp || snp === '0') {
        addMissing('serving_notice_period', 'Availability', '#serving_notice_period');
    } else if (snp === '1') {
        var npDate = ($('#np_id').val() || $('#np_id_display').val() || '').trim();
        if (!npDate) {
            addMissing('np_id', 'Available From Date', '#np_id_display');
        }
    }

    var wm = $('input[name="wm"]:checked').val();
    if (!wm) addMissing('wm', 'Preferred Work Mode', '#wr');

    // 5. Candidate Cost (Pay Rate) & Client Bill Rate
    var payVal = $('#pay_annual').val() || $('#hidden_pay_annual').val();
    if (!payVal || isNaN(payVal) || parseFloat(payVal) <= 0) {
        addMissing('pay_rate', 'Candidate Cost (Pay Rate)', '#pay_annual');
    }

    // Client Bill Rate: ignored if Free Service is selected
    var isFree = typeof isFreeCommercialMode === 'function' ? isFreeCommercialMode() : (($('#comission_type').val() || '2').toString() === '1');
    if (!isFree) {
        var billVal = $('#bill_annual').val() || $('#hidden_bill_annual').val();
        if (!billVal || isNaN(billVal) || parseFloat(billVal) <= 0) {
            addMissing('bill_rate', 'Client Bill Rate', '#bill_annual');
        }
    }

    // 6. Skills & Expertise: at least 3 skills with complete details
    var skillsCheck = validateSkillsAndExpertise();
    if (!skillsCheck.valid) {
        addMissing('skills', skillsCheck.label, '#skillExperienceWrapper', skillsCheck.type === 'incomplete' ? 'Please complete all fields (Skill name, Experience, Rating) or remove the row.' : 'At least 3 complete skills (Skill name, Experience, Rating) are required.');
    }

    // 7. Relationship Manager
    var rec = ($('#recruiter').val() || '').trim();
    var recId = ($('#recruiter_id').val() || '').trim();
    if (!rec || !recId) {
        addMissing('recruiter', 'Relationship Manager', '#rmPicker', 'Please select a Relationship Manager.');
    }

    return missing;
}

function jumpToField(fieldKey) {
    closeRequiredFieldsModal();

    if (fieldKey === 'skills') {
        var check = validateSkillsAndExpertise();
        if (check && check.targetRow) {
            check.targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            $(check.targetRow).addClass('field-attention-highlight');
            setTimeout(function () {
                $(check.targetRow).removeClass('field-attention-highlight');
            }, 3000);
            setTimeout(function () {
                var $target = $(check.targetRow);
                if (!$target.find('input[name="skills[]"]').val().trim()) {
                    $target.find('input[name="skills[]"]').focus();
                } else if (!$target.find('select[name="years[]"]').val()) {
                    $target.find('select[name="years[]"]').focus();
                } else {
                    $target.find('select[name="ratings[]"]').focus();
                }
            }, 350);
            return;
        } else {
            var wrapper = document.getElementById('skillExperienceWrapper');
            if (wrapper) {
                var card = wrapper.closest('.se-card') || wrapper;
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                $(card).addClass('field-attention-highlight');
                setTimeout(function () {
                    $(card).removeClass('field-attention-highlight');
                }, 3000);
                setTimeout(function () {
                    var emptySkillInput = $('#skillExperienceWrapper tr input[name="skills[]"]').filter(function() {
                        return !this.value.trim();
                    }).first();
                    if (emptySkillInput.length) {
                        emptySkillInput.focus();
                    } else if ($('.asb').length) {
                        $('.asb').trigger('click');
                        setTimeout(function() {
                            $('#skillExperienceWrapper tr:last-child input[name="skills[]"]').focus();
                        }, 100);
                    }
                }, 350);
            }
            return;
        }
    }

    var cfg = fieldConfigMap[fieldKey];
    var targetEl = cfg && cfg.selector ? document.querySelector(cfg.selector) : null;
    if (!targetEl) {
        targetEl = document.querySelector('[name="' + fieldKey + '"]');
    }

    if (targetEl) {
        var parentDetails = targetEl.closest('details');
        if (parentDetails && !parentDetails.open) {
            parentDetails.open = true;
        }

        var scrollTarget = targetEl.closest('.fv-row, .card, .form-group, .fs') || targetEl;
        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

        var highlightEl = targetEl.closest('.fv-row, .form-group, .rm-card-body, .br-rate-field, .bd-field, .pd-field, .pd-exp-card, .resume-card') || targetEl;
        $(highlightEl).addClass('field-attention-highlight');
        setTimeout(function () {
            $(highlightEl).removeClass('field-attention-highlight');
        }, 3000);

        setTimeout(function () {
            if (fieldKey === 'resume') {
                $('#chooseFileBtn').focus();
            } else if (fieldKey === 'recruiter') {
                $('#kt_activitiesRecruiter_toggle').trigger('click');
            } else if (fieldKey === 'kt_tagify_work_location') {
                var tagifyInput = document.querySelector('.tagify__input');
                if (tagifyInput) tagifyInput.focus();
                else targetEl.focus();
            } else if (fieldKey === 'serving_notice_period') {
                $('.aw-option-grid button').first().focus();
            } else if (typeof targetEl.focus === 'function') {
                targetEl.focus();
            }
        }, 350);
    }
}

function showRequiredFieldsModal(missingFields) {
    if (!missingFields || !missingFields.length) {
        missingFields = [
            { key: 'full_name', label: 'Full Name' },
            { key: 'kt_tagify_work_location', label: 'Current Location' }
        ];
    }

    window.__currentMissingFields = missingFields;

    var count = missingFields.length;
    var countText = count === 1 ? '1 required field' : (count + ' required fields');
    var descText = countText + ' need' + (count === 1 ? 's' : '') + ' your attention before you can submit this candidate.';

    $('#rfMissingCountText').text(countText);
    $('#rfModalDesc').text(descText);

    var $list = $('#rfFieldsList');
    $list.empty();

    missingFields.forEach(function (f) {
        var rowHtml = '<div class="rf-field-row" role="button" tabindex="0" data-field-key="' + f.key + '" aria-label="Go to ' + f.label + '">'
            + '<div class="rf-field-row-left">'
            + '<span class="rf-field-badge" aria-hidden="true">!</span>'
            + '<span class="rf-field-name">' + $('<div>').text(f.label).html() + '</span>'
            + '</div>'
            + '<svg class="rf-field-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>'
            + '</div>';
        $list.append(rowHtml);
    });

    var $modal = $('#requiredFieldsModal');
    $modal.fadeIn(150, function () {
        $('#rfPrimaryCta').focus();
    });
}

function closeRequiredFieldsModal() {
    $('#requiredFieldsModal').fadeOut(120);
}

// Modal event listeners
$(document).on('click', '#rfModalClose', function (e) {
    e.preventDefault();
    closeRequiredFieldsModal();
});

$(document).on('click', '#requiredFieldsModal', function (e) {
    if (e.target === this) {
        closeRequiredFieldsModal();
    }
});

$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#requiredFieldsModal').is(':visible')) {
        closeRequiredFieldsModal();
    }
});

$(document).on('click', '.rf-field-row', function (e) {
    e.preventDefault();
    var key = $(this).data('field-key');
    if (key) {
        jumpToField(key);
    }
});

$(document).on('keydown', '.rf-field-row', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var key = $(this).data('field-key');
        if (key) {
            jumpToField(key);
        }
    }
});

$(document).on('click', '#rfPrimaryCta', function (e) {
    e.preventDefault();
    if (window.__currentMissingFields && window.__currentMissingFields.length) {
        jumpToField(window.__currentMissingFields[0].key);
    } else {
        closeRequiredFieldsModal();
    }
});

function clearFieldValidationErrors(targetKey) {
    if (targetKey) {
        $('[data-field-error="' + targetKey + '"]').remove();
        if (targetKey === 'recruiter') {
            $('#rmPicker').removeClass('has-validation-error');
        } else if (targetKey === 'resume') {
            $('#uploadBox').removeClass('has-validation-error');
        } else if (targetKey === 'pay_rate') {
            $('#pay_annual').removeClass('has-validation-error');
            $('#pay_annual').closest('.br-rate-field').removeClass('has-validation-error');
        } else if (targetKey === 'bill_rate') {
            $('#bill_annual').removeClass('has-validation-error');
            $('#bill_annual').closest('.br-rate-field').removeClass('has-validation-error');
        } else if (targetKey === 'kt_tagify_work_location') {
            $('#kt_tagify_work_location').removeClass('has-validation-error');
            $('#kt_tagify_work_location').closest('.bd-control').find('.tagify, #jero-cs-location').removeClass('has-validation-error');
        } else if (targetKey === 'skills') {
            $('#skillExperienceWrapper tr').removeClass('has-validation-error');
            $('#skillExperienceWrapper tr input, #skillExperienceWrapper tr select').removeClass('has-validation-error');
        } else {
            var cfg = fieldConfigMap[targetKey];
            var selector = cfg && cfg.selector ? cfg.selector : ('#' + targetKey);
            $(selector).removeClass('has-validation-error');
        }
    } else {
        $('.has-validation-error').removeClass('has-validation-error');
        $('.field-error-message').remove();
    }
}
window.clearFieldValidationErrors = clearFieldValidationErrors;

function applyFieldValidationErrors(missingFields) {
    clearFieldValidationErrors();
    if (!missingFields || !missingFields.length) return;

    missingFields.forEach(function (f) {
        var key = f.key;
        var msg = f.message || (f.label + ' is required.');
        var errorHtml = '<div class="field-error-message" data-field-error="' + key + '">'
            + '<svg class="fe-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:4px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
            + '<span>' + $('<div>').text(msg).html() + '</span>'
            + '</div>';

        if (key === 'recruiter') {
            var $picker = $('#rmPicker');
            $picker.addClass('has-validation-error');
            if (!$('[data-field-error="recruiter"]').length) {
                if ($('#rmHelpText').length) {
                    $('#rmHelpText').after(errorHtml);
                } else {
                    $picker.after(errorHtml);
                }
            }
        } else if (key === 'resume') {
            var $up = $('#uploadBox');
            $up.addClass('has-validation-error');
            if (!$('[data-field-error="resume"]').length) {
                $up.after(errorHtml);
            }
        } else if (key === 'pay_rate') {
            var $payField = $('#pay_annual').closest('.br-rate-field');
            $payField.addClass('has-validation-error');
            $('#pay_annual').addClass('has-validation-error');
            if (!$('[data-field-error="pay_rate"]').length) {
                var $ig = $('#pay_annual').closest('.ig');
                if ($ig.length) $ig.after(errorHtml);
                else $payField.append(errorHtml);
            }
        } else if (key === 'bill_rate') {
            var $billField = $('#bill_annual').closest('.br-rate-field');
            $billField.addClass('has-validation-error');
            $('#bill_annual').addClass('has-validation-error');
            if (!$('[data-field-error="bill_rate"]').length) {
                var $ig = $('#bill_annual').closest('.ig');
                if ($ig.length) $ig.after(errorHtml);
                else $billField.append(errorHtml);
            }
        } else if (key === 'skills') {
            var check = validateSkillsAndExpertise();
            if (check && check.targetRow) {
                $(check.targetRow).addClass('has-validation-error');
            } else {
                $('#skillExperienceWrapper tr').each(function () {
                    var $tr = $(this);
                    var s = ($tr.find('input[name="skills[]"]').val() || '').trim();
                    var y = $tr.find('select[name="years[]"]').val();
                    var r = $tr.find('select[name="ratings[]"]').val();
                    if (!s || !y || !r) {
                        $tr.addClass('has-validation-error');
                    }
                });
            }
            if (!$('[data-field-error="skills"]').length) {
                var $seWrap = $('#skillExperienceWrapper').closest('.table-responsive, table, .se-card');
                if ($seWrap.length) $seWrap.after(errorHtml);
                else $('#skillExperienceWrapper').after(errorHtml);
            }
        } else if (key === 'kt_tagify_work_location') {
            var $loc = $('#kt_tagify_work_location');
            $loc.addClass('has-validation-error');
            var $combobox = $loc.closest('.bd-control').find('.tagify, #jero-cs-location');
            if ($combobox.length) $combobox.addClass('has-validation-error');
            if (!$('[data-field-error="kt_tagify_work_location"]').length) {
                var $ctrl = $loc.closest('.bd-control');
                if ($ctrl.length) $ctrl.after(errorHtml);
                else $loc.after(errorHtml);
            }
        } else if (key === 'wm') {
            var $wmWrap = $('#wr, .aw-mode-grid, .wm-option-grid').first();
            $wmWrap.addClass('has-validation-error');
            if (!$('[data-field-error="wm"]').length) {
                $wmWrap.after(errorHtml);
            }
        } else {
            var cfg = fieldConfigMap[key] || {};
            var sel = f.selector || cfg.selector || ('#' + key);
            var $el = $(sel);
            if (!$el.length) $el = $('[name="' + key + '"]');
            if ($el.length) {
                $el.addClass('has-validation-error');
                $el.closest('.bd-control').find('.jero-custom-select').addClass('has-validation-error');
                if (!$('[data-field-error="' + key + '"]').length) {
                    var $ctrl = $el.closest('.bd-control, .pd-control, .bd-phone-control, .input-group');
                    if ($ctrl.length) {
                        $ctrl.after(errorHtml);
                    } else {
                        $el.after(errorHtml);
                    }
                }
            }
        }
    });
}
window.applyFieldValidationErrors = applyFieldValidationErrors;

$(document).on('input change focus', 'input, select, textarea', function () {
    var $this = $(this);
    var key = $this.attr('name') || $this.attr('id');
    if ($this.hasClass('has-validation-error')) {
        $this.removeClass('has-validation-error');
        $this.closest('.bd-control').find('.jero-custom-select').removeClass('has-validation-error');
    }
    if ($this.closest('.has-validation-error').length) {
        $this.closest('.has-validation-error').removeClass('has-validation-error');
        $this.closest('.bd-control').find('.jero-custom-select').removeClass('has-validation-error');
    }
    if (key) {
        $('[data-field-error="' + key + '"]').remove();
    }
    if ($this.attr('id') === 'pay_annual' || $this.attr('name') === 'pay_annual') {
        clearFieldValidationErrors('pay_rate');
    } else if ($this.attr('id') === 'bill_annual' || $this.attr('name') === 'bill_annual') {
        clearFieldValidationErrors('bill_rate');
    } else if ($this.closest('#skillExperienceWrapper').length) {
        $this.closest('tr').removeClass('has-validation-error');
        var sc = validateSkillsAndExpertise();
        if (sc.valid) {
            clearFieldValidationErrors('skills');
        }
    }
});

$(document).on('change input', '#kt_tagify_work_location', function () {
    if ($(this).val() && $(this).val().trim()) {
        clearFieldValidationErrors('kt_tagify_work_location');
    }
});

function displayValidationErrorsNotification(errorList, formEl, validatorInstance) {
    var missing = detectMissingFields(formEl, validatorInstance);
    if (!missing.length && errorList && errorList.length) {
        errorList.forEach(function (err) {
            var foundKey = null;
            Object.keys(fieldConfigMap).forEach(function (k) {
                if (err.toLowerCase().indexOf(fieldConfigMap[k].label.toLowerCase()) !== -1 || err.toLowerCase().indexOf(k.toLowerCase()) !== -1) {
                    foundKey = k;
                }
            });
            if (foundKey) {
                missing.push({
                    key: foundKey,
                    label: fieldConfigMap[foundKey].label,
                    selector: fieldConfigMap[foundKey].selector,
                    message: fieldConfigMap[foundKey].message || (fieldConfigMap[foundKey].label + ' is required.')
                });
            } else {
                missing.push({
                    key: 'generic',
                    label: err.replace(/^(the|please|a)\s+/i, '').replace(/\s+(is required|is invalid|must contain.*)$/i, ''),
                    selector: null,
                    message: err
                });
            }
        });
    }

    if (!missing.length) {
        missing = [
            { key: 'full_name', label: 'Full Name', selector: '#full_name', message: 'Full name is required.' },
            { key: 'kt_tagify_work_location', label: 'Current Location', selector: '#kt_tagify_work_location', message: 'Current location is required.' }
        ];
    }

    applyFieldValidationErrors(missing);
    showRequiredFieldsModal(missing);
}

async function isInternetAvailable() {
    if (typeof navigator !== 'undefined' && 'onLine' in navigator) {
        return navigator.onLine;
    }
    return true;
}
window.isInternetAvailable = isInternetAvailable;

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
                    'careersummary'			:	{validators:{notEmpty: {message: "Professional summary is required"}} },
                    'kt_tagify_work_location':	{validators:{notEmpty: {message: "Current Location is required"}} },
                    'current_company'		:	{validators:{notEmpty: {message: "Current company is required"}} },
                    'current_designation'	:	{validators:{notEmpty: {message: "Current designation is required"}} },
                    'employmentType'		:	{validators:{notEmpty: {message: "Client Engagement Model is required"}} },
                    'nationality'			:	{validators:{notEmpty: {message: "Nationality is required"}} },
                    'gender'				:	{validators:{notEmpty: {message: "Gender is required"}} },
                    'phone'					:	{validators:{notEmpty: {message: "Primary number is required"},callback:{message: "Enter a valid mobile number for the selected country", callback: function () { return window.validateCreateTalentPhone('phone', true); }}}},
                    'secondary_mobile'		:	{validators:{callback:{message: "Enter a valid secondary number for the selected country", callback: function () { return window.validateCreateTalentPhone('secondary_mobile', false); }}}},
                    'email'					:	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    'qualification'			:	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );
        window.benchFormValidator = validator;

        var executeSubmit = function () {
            closeRequiredFieldsModal();
            $("#save-form").prop("disabled", true);
            $("#save-form").html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
            );
            if (submitButton) {
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            }

            setTimeout(function () {
                if (submitButton) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = true;
                }
                if (!navigator.onLine) {
                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                    $("#save-form").prop("disabled", false);
                    if (typeof window.syncTalentStageAction === 'function') {
                        window.syncTalentStageAction();
                    }
                    if (submitButton) submitButton.disabled = false;
                    return;
                }

                // Layer 1: Client-Generated Idempotency UUID
                let clientUuid = $('#client_submission_uuid').val();
                if (!clientUuid) {
                    clientUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                    $('#client_submission_uuid').val(clientUuid);
                }

                window.normalizeCreateTalentPhones();
                var formData = new FormData($('#createForm')[0]);
                $.ajax({
                    url: '{{route('service_company.bench-candidates.store')}}'+'?_token=' + '{{ csrf_token() }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    redirect: true,
                    success: function (response) {
                        $("#save-form").prop("disabled", false);
                        if (submitButton) submitButton.disabled = false;
                        if(response.status == 'coinCheck'){
                            if (typeof window.syncTalentStageAction === 'function') {
                                window.syncTalentStageAction();
                            }
                            const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                            $('#client_submission_uuid').val(freshUuid);
                            Swal.fire("Warning", ` ${response.message}`, "warning");
                            return false;
                        }

                        if(response.status == 'mailcheck'){
                            const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                            $('#client_submission_uuid').val(freshUuid);
                            if (!navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                $("#save-form").prop("disabled", false);
                                if (typeof window.syncTalentStageAction === 'function') {
                                    window.syncTalentStageAction();
                                }
                                if (submitButton) submitButton.disabled = false;
                                return;
                            }
                            if (typeof window.syncTalentStageAction === 'function') {
                                window.syncTalentStageAction();
                            }
                            Swal.fire("Warning", response.message, "warning").then(function(){});
                            return false;
                        }

                        // Triple-Redundant Redirect Handler (Gate G-23 & G-24)
                        handleTalentSubmissionSuccess(response);
                    },
                    error: function (xhr) {
                        $("#save-form").prop("disabled", false);
                        if (typeof window.syncTalentStageAction === 'function') {
                            window.syncTalentStageAction();
                        }
                        if (submitButton) submitButton.disabled = false;
                        const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                        $('#client_submission_uuid').val(freshUuid);
                        if (xhr.status === 0 || !navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        } else {
                            Swal.fire("Error", "Something went wrong. Please try again.", "error");
                        }
                    }
                });
            }, 500);
        };

        var handleCandidateSubmit = async function (e) {
            if (e && e.preventDefault) e.preventDefault();

            var connected = true;
            try {
                connected = await isInternetAvailable();
            } catch (netErr) {
                connected = (typeof navigator !== 'undefined') ? navigator.onLine : true;
            }
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
                return;
            }

            var missing = detectMissingFields(form, validator);
            if (missing && missing.length > 0) {
                $("#save-form").prop("disabled", false);
                if (typeof window.syncTalentStageAction === 'function') {
                    window.syncTalentStageAction();
                }
                if (submitButton) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                }
                var errors = collectFormValidationErrors(form, validator);
                displayValidationErrorsNotification(errors, form, validator);
                return;
            }

            if (validator && typeof validator.validate === 'function') {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        executeSubmit();
                    } else {
                        var fvErrors = collectFormValidationErrors(form, validator);
                        displayValidationErrorsNotification(fvErrors, form, validator);
                    }
                }).catch(function (valErr) {
                    console.warn('Validator validate fallback:', valErr);
                    executeSubmit();
                });
            } else {
                executeSubmit();
            }
        };

        if (submitButton) {
            submitButton.addEventListener('click', handleCandidateSubmit);
        }
        $(document).off('click', '#save-form').on('click', '#save-form', handleCandidateSubmit);
    };

    return {
        init: function () {
            form = document.querySelector('#createForm');
            submitButton = document.querySelector('#save-form');
            handleForm();
        }
    };
}();

function initKTAddCandidate() {
    if (typeof KTAddCandidate !== 'undefined' && KTAddCandidate.init) {
        KTAddCandidate.init();
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKTAddCandidate);
} else {
    initKTAddCandidate();
}
$(document).ready(initKTAddCandidate);
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
                    'careersummary'			:	{validators:{notEmpty: {message: "Professional summary is required"}} },
                    'kt_tagify_work_location':	{validators:{notEmpty: {message: "Current Location is required"}} },
                    'current_company'		:	{validators:{notEmpty: {message: "Current company is required"}} },
                    'current_designation'	:	{validators:{notEmpty: {message: "Current designation is required"}} },
                    'employmentType'		:	{validators:{notEmpty: {message: "Client Engagement Model is required"}} },
                    'nationality'			:	{validators:{notEmpty: {message: "Nationality is required"}} },
                    'gender'				:	{validators:{notEmpty: {message: "Gender is required"}} },
                    'phone'					:	{validators:{notEmpty: {message: "Primary number is required"},callback:{message: "Enter a valid mobile number for the selected country", callback: function () { return window.validateCreateTalentPhone('phone', true); }}}},
                    'secondary_mobile'		:	{validators:{callback:{message: "Enter a valid secondary number for the selected country", callback: function () { return window.validateCreateTalentPhone('secondary_mobile', false); }}}},
                    'email'					:	{validators:{ notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address'} } },
                    'qualification'			:	{validators:{notEmpty:{message: 'Qualification is required'}}},
                    'comission_type' 		:	{validators:{notEmpty:{message:"Comission type is required"}}}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

        var executeEditSubmit = function () {
            closeRequiredFieldsModal();
            $("#edit-form").prop("disabled", true);
            $("#edit-form").html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
            );
            if (submitButton) {
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            }

            setTimeout(function () {
                if (submitButton) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = true;
                }
                if (!navigator.onLine) {
                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                    $("#edit-form").prop("disabled", false).html("Update");
                    if (submitButton) submitButton.disabled = false;
                    return;
                }

                // Layer 1: Client-Generated Idempotency UUID
                let clientUuid = $('#client_submission_uuid').val();
                if (!clientUuid) {
                    clientUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                    $('#client_submission_uuid').val(clientUuid);
                }

                window.normalizeCreateTalentPhones();
                var formData = new FormData($('#createForm')[0]);
                $.ajax({
                    url: '{{route('service_company.bench-candidates.store')}}'+'?_token=' + '{{ csrf_token() }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    redirect: true,
                    success: function (response) {
                        $("#edit-form").prop("disabled", false).html("Update");
                        if (submitButton) submitButton.disabled = false;
                        if(response.status == 'mailcheck'){
                            const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                            $('#client_submission_uuid').val(freshUuid);
                            if (!navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                $("#edit-form").prop("disabled", false).html("Update");
                                if (submitButton) submitButton.disabled = false;
                                return;
                            }
                            $("#edit-form").html(`Update`);
                            Swal.fire("Warning", response.message, "warning").then(function(){});
                            return false;
                        }

                        // Triple-Redundant Redirect Handler (Gate G-23 & G-24)
                        handleTalentSubmissionSuccess(response);
                    },
                    error: function (xhr) {
                        $("#edit-form").prop("disabled", false).html("Update");
                        if (submitButton) submitButton.disabled = false;
                        const freshUuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('uuid_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36));
                        $('#client_submission_uuid').val(freshUuid);
                        if (xhr.status === 0 || !navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        } else {
                            Swal.fire("Error", "Something went wrong. Please try again.", "error");
                        }
                    }
                });
            }, 500);
        };

        var handleEditSubmit = async function (e) {
            if (e && e.preventDefault) e.preventDefault();

            var connected = true;
            try {
                connected = await isInternetAvailable();
            } catch (netErr) {
                connected = (typeof navigator !== 'undefined') ? navigator.onLine : true;
            }
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
                return;
            }

            var missing = detectMissingFields(form, validator);
            if (missing && missing.length > 0) {
                if (submitButton) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                }
                var errors = collectFormValidationErrors(form, validator);
                displayValidationErrorsNotification(errors, form, validator);
                return;
            }

            if (validator && typeof validator.validate === 'function') {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        executeEditSubmit();
                    } else {
                        var fvErrors = collectFormValidationErrors(form, validator);
                        displayValidationErrorsNotification(fvErrors, form, validator);
                    }
                }).catch(function (valErr) {
                    console.warn('Edit validator validate fallback:', valErr);
                    executeEditSubmit();
                });
            } else {
                executeEditSubmit();
            }
        };

        if (submitButton) {
            submitButton.addEventListener('click', handleEditSubmit);
        }
        $(document).off('click', '#edit-form').on('click', '#edit-form', handleEditSubmit);
    };

    return {
        init: function () {
            form = document.querySelector('#createForm');
            submitButton = document.querySelector('#edit-form');
            handleForm();
        }
    };
}();

function initKTEditCandidate() {
    if (typeof KTEditCandidate !== 'undefined' && KTEditCandidate.init) {
        KTEditCandidate.init();
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKTEditCandidate);
} else {
    initKTEditCandidate();
}
$(document).ready(initKTEditCandidate);
@php } @endphp




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



   

   
</script>
@endpush
