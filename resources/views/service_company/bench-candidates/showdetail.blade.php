@php
    use App\Helper\GeneralMethod;
@endphp
<style>
    .text-justify {
        text-align: justify !important;
    }
    .iti__flag {
    margin-top: 4px !important;
    }
</style>
 <link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<script>
    $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php

    $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
    $photo = url('public/avatar.png');
    if (!empty($benchApplication->photo)) {
        $photo = url('/public/user-uploads/bench_photos/' . $benchApplication->photo);
    }
    $country_id = $country_name = $state_name = $state = $city_name = $posted_by_company = '';
    $country_id = @$benchApplication->country;
    $state = @$benchApplication->state;
    // if (@$country_id > 0) {
        $country_name_Arr = GeneralMethod::getCountryName($country_id);
        foreach ($country_name_Arr as $cval) {
            $country_name = ucwords($cval['name']);
        }

        if ($country_id == 101) {
            $state_name = @$benchApplication->state_name;
            $city_name = @$benchApplication->city_name;
        } else {
            if ($state > 0) {
                $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
                foreach ($state_name_Arr as $sval) {
                    $state_name = ucwords($sval['name']);
                }
            }
            $city_name = @$benchApplication->city;
        }
        $currencySymbole = GeneralMethod::currencySymbole();
        $currencySymboleforexcell 	=	GeneralMethod::currencySymboleforexcell();
        $current_ctc_cur = $current_ectc_cur = '';
        $current_ctc_cur = $currencySymbole[(@$benchApplication->pay_rate_currency) ? @$benchApplication->pay_rate_currency : 0];
        $current_ectc_cur = $currencySymbole[(@$benchApplication->bill_rate_currency) ? @$benchApplication->bill_rate_currency : 0];
        $current_cur	=	$currencySymboleforexcell[@$benchApplication->bill_rate_currency]; 
    // }
    $posted_by_company = @$benchApplication->company_name;
@endphp

  <div class="page-wrapper">
    <button type="button" class="page-close" aria-label="Close page" id="kt_activitiesBenchApplication_close">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>

    <div class="card-header-top px-5 py-0" id="kt_activities_header">
        <div class="d-flex align-items-center py-1">
            <a href="javascript:void(0);" data-row-id="{{ $benchApplication->id }}" type="button" class="btn btn-sm btn-flex btn-primary stage_sub_stage btn-active-primary fw-bolder me-3" data-toggle="modal" data-target="#stage_sub_stage">
                <span class="svg-icon svg-icon-primary svg-icon-1x">
                    <svg class="svg-inline--fa fa-rotate fa-spin" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="rotate" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M449.9 39.96l-48.5 48.53C362.5 53.19 311.4 32 256 32C161.5 32 78.59 92.34 49.58 182.2c-5.438 16.81 3.797 34.88 20.61 40.28c16.97 5.5 34.86-3.812 40.3-20.59C130.9 138.5 189.4 96 256 96c37.96 0 73 14.18 100.2 37.8L311.1 178C295.1 194.8 306.8 223.4 330.4 224h146.9C487.7 223.7 496 215.3 496 204.9V59.04C496 34.99 466.9 22.95 449.9 39.96zM441.8 289.6c-16.94-5.438-34.88 3.812-40.3 20.59C381.1 373.5 322.6 416 256 416c-37.96 0-73-14.18-100.2-37.8L200 334C216.9 317.2 205.2 288.6 181.6 288H34.66C24.32 288.3 16 296.7 16 307.1v145.9c0 24.04 29.07 36.08 46.07 19.07l48.5-48.53C149.5 458.8 200.6 480 255.1 480c94.45 0 177.4-60.34 206.4-150.2C467.9 313 458.6 294.1 441.8 289.6z"></path></svg><!-- <i class="fas fa-sync-alt fa-spin"></i> Font Awesome fontawesome.com -->
                </span> Change Status 
            </a>
            <a href="javascript:void(0);" data-row-id="{{ $benchApplication->id }}" type="button" class="btn btn-sm btn-flex btn-primary hmb-listing btn-active-primary fw-bolder me-3">
                <span class="svg-icon svg-icon-primary svg-icon-1x">
                    <svg class="svg-inline--fa fa-users" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M319.9 320c57.41 0 103.1-46.56 103.1-104c0-57.44-46.54-104-103.1-104c-57.41 0-103.1 46.56-103.1 104C215.9 273.4 262.5 320 319.9 320zM369.9 352H270.1C191.6 352 128 411.7 128 485.3C128 500.1 140.7 512 156.4 512h327.2C499.3 512 512 500.1 512 485.3C512 411.7 448.4 352 369.9 352zM512 160c44.18 0 80-35.82 80-80S556.2 0 512 0c-44.18 0-80 35.82-80 80S467.8 160 512 160zM183.9 216c0-5.449 .9824-10.63 1.609-15.91C174.6 194.1 162.6 192 149.9 192H88.08C39.44 192 0 233.8 0 285.3C0 295.6 7.887 304 17.62 304h199.5C196.7 280.2 183.9 249.7 183.9 216zM128 160c44.18 0 80-35.82 80-80S172.2 0 128 0C83.82 0 48 35.82 48 80S83.82 160 128 160zM551.9 192h-61.84c-12.8 0-24.88 3.037-35.86 8.24C454.8 205.5 455.8 210.6 455.8 216c0 33.71-12.78 64.21-33.16 88h199.7C632.1 304 640 295.6 640 285.3C640 233.8 600.6 192 551.9 192z"></path></svg><!-- <i class="fas fa-sync-alt fa-users"></i> Font Awesome fontawesome.com -->
                </span> Add Relationship Manager
            </a> 
        </div>
    </div>
    <headerShow class="page-header">
      <div class="candidate-identity">
        <figure class="avatar">
          <svg class="avatar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span class="avatar-status" aria-label="Available"></span>
        </figure>
       
        <div class="candidate-header-content">
          <div class="candidate-name-row">
            <h1 class="candidate-name-show">{{ ucwords(@$benchApplication->full_name) }}</h1>
            <span class="">{{ @$benchApplication->master_status_name }}</span>
          </div>
          <p class="candidate-role-show">{{ ucwords(substr(@$benchApplication->current_designation, 0, 30)) }}</p>
          <div class="candidate-meta-bar">
            <span class="meta-item">
              <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span>Gender: {{@$benchApplication->gender}}</span>
            </span>
            <span class="meta-item">
              <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              @php  
                    $created_at			=	@$benchApplication->created_at;
                    $addedDate 		=	date("d-M-Y h:i:s", strtotime($created_at)); 
              @endphp
              
              <span>Upload Date & Time: {{ @$addedDate }}</span>
            </span>
            <span class="meta-item">
              <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
              </svg>
              <span>Bench ID: BJ-{{ @$benchApplication->id }}</span>
            </span>
          </div>
          <div class="candidate-skills">
              @php 
                $skills				=	'';
                $skills				=	@$benchApplication->skills;
              @endphp 
              @if(!empty($skills))
                @php
                  $skillsArr		=	'';
                  $skillsArr		=	explode(',', $skills);
                @endphp
              @endif
              <span class="skills-label">Skills:</span>
              <ul class="skills-list">
                 @foreach($skillsArr as $sval)
                   <span class="skill-tag">{{ @$sval }}</span>
                 @endforeach
              </ul>
          </div>
        </div>
      </div>
      <div class="header-actions">
        @if (in_array('edit_job_applications', $userPermissions) && $benchApplication->company_id == auth()->user()->company_id)
            <button type="button" class="btn btn-primary" data-row-source_from_type="2" data-row-id="{{ @$benchApplication->id }}" data-row-jobid="" style="">
            <a href="{{ route('service_company.bench-candidates.edit', [GeneralMethod::value_encrypt($benchApplication->id)]) }}" class="" style="display: contents;color:#ffffff">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                <span>Edit Profile</span>
            </a>
            </button>
        @endif

        @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf', 'doc', 'docx']))
              @php 
                $resumePath			=	@$benchApplication->resume;
                $resume_status		=	0;
              @endphp 
              @if(file_exists($resumePath))
                @php 
                    $resume_status		=	1;
                @endphp 
              @endif
              @php                                
                  $url = @$benchApplication->resume;

                  $info = pathinfo($url);
              @endphp

                <div style="position:relative;display:inline-block;" id="hdrDlWrapBench">
                  <button type="button" class="btn btn-secondary" id="hdrDlToggleBench" style="background:no-repeat;border:1px solid #e2e8f0;display:flex;align-items:center;gap:5px;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:#475569">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                      <polyline points="7 10 12 15 17 10"/>
                      <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span style="color:#475569">Download Resume</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;color:#475569"><polyline points="6 9 12 15 18 9"/></svg>
                  </button>
                  <div id="hdrDlMenuBench" style="display:none;position:absolute;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:230px;z-index:999;overflow:hidden;">
                    <a target="_blank" data-id="{{ $benchApplication->id }}" class="downloadCount" href="{{ route('download.resume', ['id' => $benchApplication->id,'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]) }}" style="display:block;padding:9px 13px;font-size:12px;font-weight:500;color:#334155;text-decoration:none;border-bottom:1px solid #f1f5f9;">
                      Download Resume
                    </a>
                    <button type="button" onclick="hdrDownloadWithLogoBench()" style="display:flex;align-items:center;gap:6px;width:100%;padding:9px 13px;font-size:12px;font-weight:600;color:#15803d;background:#f0fdf4;border:0;border-left:3px solid #22c55e;cursor:pointer;text-align:left;">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                      Resume with Company Logo
                      <span style="font-size:10px;background:#22c55e;color:#fff;border-radius:4px;padding:1px 5px;margin-left:auto;font-weight:700;letter-spacing:.3px;">LOGO</span>
                    </button>
                  </div>
                </div>
                <script>
                (function(){
                  var t=document.getElementById('hdrDlToggleBench'),m=document.getElementById('hdrDlMenuBench');
                  if(t&&m){
                    t.addEventListener('click',function(e){e.stopPropagation();m.style.display=m.style.display==='none'?'block':'none';});
                    document.addEventListener('click',function(){m.style.display='none';});
                  }
                })();
                function hdrDownloadWithLogoBench(){
                  document.getElementById('hdrDlMenuBench').style.display='none';
                  // activate the Resume/CV tab so the viewer loads
                  var rcvTab=document.querySelector('a[href="#resume_cv"].tab, button[data-target="resume_cv"], button[data-rcv-tab]');
                  if(rcvTab) rcvTab.click();
                  // poll until PDF pages are rendered, then fire the logo download
                  var tries=0;
                  var poll=setInterval(function(){
                    if(document.querySelectorAll('#rcvPages .rcv-page').length){
                      clearInterval(poll);
                      var b=document.querySelector('[data-dl="logo"]');
                      if(b) b.click();
                    } else if(++tries>80){
                      clearInterval(poll);
                      alert('Resume is taking too long to load. Please open the Resume / CV tab and try again.');
                    }
                  },100);
                }
                </script> 
                
        @else
          <button type="button" class="btn btn-secondary" style="background: no-repeat; border: 1px solid #e2e8f0;">                       
                <a  href="{{ route('service_company.bench-candidates.edit', [GeneralMethod::value_encrypt($benchApplication->id)]) }}" class="btn btn-light-primary btn-sm py-1" style="display: contents;color:#475569">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                      <polyline points="17 8 12 3 7 8"></polyline>
                      <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Upload Resume</span>
                </a>
              </button>        
        @endif
       
      </div>
    </headerShow>

    <nav class="page-tabs tab-content nav" aria-label="Profile sections" role="tablist">
        <a type="button" class="tab tab-active" data-toggle="tab" href="#personal_details" aria-selected="true">
            <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
            </svg>
            Profile Overview
          </a>
        @if(@$benchApplication->status_type !='DIRECT')
        <a type="button" class="tab" data-toggle="tab" href="#boostkeywords" aria-selected="false">
            <svg class="tab-icon" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                            1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09
                            a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33
                            l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06
                            a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3
                            a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1
                            1.65 1.65 0 0 0-.33-1.82l-.06-.06
                            a2 2 0 1 1 2.83-2.83l.06.06
                            a1.65 1.65 0 0 0 1.82.33h.01
                            a1.65 1.65 0 0 0 1-1.51V3
                            a2 2 0 1 1 4 0v.09
                            a1.65 1.65 0 0 0 1 1.51
                            1.65 1.65 0 0 0 1.82-.33l.06-.06
                            a2 2 0 1 1 2.83 2.83l-.06.06
                            a1.65 1.65 0 0 0-.33 1.82v.01
                            a1.65 1.65 0 0 0 1.51 1H21
                            a2 2 0 1 1 0 4h-.09
                            a1.65 1.65 0 0 0-1.51 1z">
                    </path>
                </svg>
            Ads Setting
              </a>
        @endif
        @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
        <a type="button" class="tab" data-toggle="tab" href="#resume_cv" aria-selected="false">
            <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            Resume / CV
        </a>
        @endif
    </nav>

    <main class="page-content">

      <div class="tab-pane fade" id="boostkeywords" role="tabpanel">
             <div class="content-split">
                <div class="content-main">
                  <section class="cardshow">
                    <div class="card-header-sidebar">
                      <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                          <polyline points="14 2 14 8 20 8"/>
                          <line x1="16" y1="13" x2="8" y2="13"/>
                          <line x1="16" y1="17" x2="8" y2="17"/>
                          <polyline points="10 9 9 9 8 9"/>
                        </svg>
                      </div>
                      <h3 class="card-title">Boostkeywords</h3>
                    </div>
                      <div class="form-group">
                                  <label class="control-label col-sm-8"></label>
                                    <div class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input type="checkbox" class="js-switch form-check-input checkStatusSales" name="checkStatusSales" value="{{$benchApplication->id}}" @if (@$benchApplication->ads_switch == 'Yes') checked @endif data-color="#99d683" data-size="small"/>
                                    </div>
                                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 checkStatusSales_text">{{ @$benchApplication->ads_switch == 'Yes' ? 'Ads Active' : 'Ads Block' }}</a>
                                  
                    </div>
                      <br>

                      <label class="form-label required fw-bolder">Description</label>

                      <p class="summary-text summary-text-width"></p>
                  </section>
                </div>
            </div>
      </div>

        
      <div class="tab-pane fade show active" id="personal_details" role="tabpanel">

      <h2 class="overview-title">Profile Overview</h2>


      <section class="info-grid" aria-label="Candidate quick information">
        <article class="info-card">
            <div class="info-icon-wrap blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
                @php
                    $experience = trim((string) @$benchApplication->total_exp);
                    if (strpos($experience, '.') !== false) {
                        $parts = explode('.', $experience);
                        if (isset($parts[1])) {
                            if ($parts[1] === '00') {
                                $formattedExperience = $parts[0];
                            } else {
                                $formattedExperience = $experience; 
                            }
                        } else {
                            $formattedExperience = $experience;
                        }
                    } else {
                        $formattedExperience = $experience;
                    }

                @endphp
            <div class="info-content">
                <div class="info-label">Total Experience</div>
                <div class="info-value">{{ @$formattedExperience }} Years</div>
            </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="12" y1="20" x2="12" y2="10"/>
              <line x1="18" y1="20" x2="18" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="16"/>
            </svg>
          </div>
          @php	
              $rel_experience = trim((string) @$benchApplication->rel_exp);
              if (strpos($rel_experience, '.') !== false) {
                $parts = explode('.', $rel_experience);
                if (isset($parts[1])) {
                  // If decimals are exactly "00", remove them
                  if ($parts[1] === '00') {
                    $rel_formattedExperience = $parts[0];
                  } else {
                    $rel_formattedExperience = $rel_experience; 
                  }
                } else {
                  $rel_formattedExperience = $rel_experience;
                }
              } else {
                $rel_formattedExperience = $rel_experience;
              }          
          @endphp

          <div class="info-content">
            <div class="info-label">Relevant Experience</div>
            <div class="info-value">{{ @$rel_formattedExperience }} Years </div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
              <path d="M9 22v-4h6v4"/>
              <line x1="8" y1="6" x2="8" y2="6.01"/>
              <line x1="16" y1="6" x2="16" y2="6.01"/>
              <line x1="8" y1="10" x2="8" y2="10.01"/>
              <line x1="16" y1="10" x2="16" y2="10.01"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Current Company</div>
            <div class="info-value">{{ ucwords(@$benchApplication->company_name) }}
            </div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Nationality</div>
            <div class="info-value">{{ ucwords(substr(@$benchApplication->nationality, 0, 30)) }}</div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Work Mode</div>
            <div class="info-value">{{ ucwords(substr(@$benchApplication->wm, 0, 30)) }}</div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="info-content">
                    @php
                          $np	=	'';
                          if((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1) )
                          {
                            $np				=	@$benchApplication->np_id;
                                              $np 			=	'Available '.date("dM Y", strtotime($np)); 
                        
                          }else{
                            $np				=	@$benchApplication->np_id;
                          }
                          	
                    @endphp 
            <div class="info-label">Notice Period</div>
            <div class="info-value">{{ @$np }}</div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Preferred Location</div>
            <div class="info-value">
              {{ @$city_name }},&nbsp;{{ ucwords(@$state_name) }},&nbsp;{{ ucwords(@$country_name) }} </div>
          </div>
        </article>

        <article class="info-card">
          <div class="info-icon-wrap blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
              <path d="M6 12v5c0 1.66 1.79 3 4 3s4-1.34 4-3v-5"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Education</div>
            <div class="info-value">{{ ucwords(@$benchApplication->qualification_name) }}</div>
          </div>
        </article>
      </section>

      

      <div class="content-split">
        <div class="content-main">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                  <polyline points="10 9 9 9 8 9"/>
                </svg>
              </div>
              <h3 class="card-title">Professional Summary</h3>
            </div>
            <p class="summary-text">{{@$benchApplication->careersummary}}</p>
            {{-- <span class="word-count-badge">184 / 200 Words</span> --}}
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <h3 class="card-title">Key Skills & Experience</h3>
            </div>
            <div class="skills-table">
              <div class="skills-thead">
                <div>Skill</div>
                <div>Experience</div>
                <div>Self Rating (Out of 5)</div>
              </div>
               @if(@$benchApplication->status_type !='DIRECT')
                @php 
                    if(!empty($skillExperiencesCount)){
                    $customTitleId	=	1;
                    $html			=	'';
                    $inc_first		=	0;
                    $inc_last		=	1;
                    foreach($skillExperiences as $exp)
                    {
                
                  $html		.=	'<div class="skills-row">
                    <div class="skill-name-cell">
                      <div class="skill-logo" style="color: #f59e0b;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                          <path d="M2 17l10 5 10-5"/>
                          <path d="M2 12l10 5 10-5"/>
                        </svg>
                      </div>
                      '.$exp->skill_key.'
                    </div>
                    <div class="skill-exp-cell">'.($exp->year ?? '0') .'.'. ($exp->month ?? '0').' Years</div>
                    <div class="skill-rating-cell">
                      <div class="stars">
                        <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg class="star-skill '.(@$exp->rating >= 2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg class="star-skill '.(@$exp->rating >= 3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg class="star-skill '.(@$exp->rating >= 4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg class="star-skill '.(@$exp->rating >= 5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                      </div>
                      <span class="rating-text">'.(@$exp->rating ?? 0).' / 5</span>
                    </div>
                  </div>';
            
                } 
                 echo $html;

                }		
            @endphp
               @endif
          </section>
        </div>

        <aside class="content-sidebar">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-warning-50); color: var(--color-warning-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                  <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
              </div>
              <h3 class="card-title">Compensation Details</h3>
            </div>
            <div class="comp-list">

                <div class="comp-row">
                            @php
                            $pay_rate_mode = '';
                            $pay_rate_mode = (@$benchApplication->pay_rate_mode) ? @$benchApplication->pay_rate_mode : 0;
                            @endphp
                <span class="comp-label">Pay Rate</span>
                <span class="comp-value">{!! $current_ctc_cur !!}  {{ @$benchApplication->pay_rate_amount }} {{ $salaryMode[$pay_rate_mode] }} </span>
              </div>

              <div class="comp-row">
                            @php
                                $bill_rate_mode = '';
                                $bill_rate_mode = (@$benchApplication->bill_rate_mode) ? @$benchApplication->bill_rate_mode : 0;
                            @endphp
                <span class="comp-label">Bill Rate</span>
                <span class="comp-value">{!! @$current_ectc_cur !!}  {{ @$benchApplication->bill_rate_amount }} {{ $salaryMode[$bill_rate_mode] }} </span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Compensation Type</span>
                <span class="comp-badge">{{ $salaryMode[$bill_rate_mode] }}</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Salary Type</span>
                <span class="comp-value">Fixed</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Currency</span>
                <span class="comp-value">{{ $current_cur }}</span>
              </div>
              <div class="comp-row">
                @php
                    $comission_type = $comission_type_name = '';
                    $comission_type = @$benchApplication->comission_type;
                    if ($comission_type == 1) {
                        $comission_type_name = 'Free';
                    } elseif ($comission_type == 2) {
                        $comission_type_name = 'Paid';
                    }
                   
                @endphp
                <span class="comp-label">Paid Service / Free Service</span>
                <span class="comp-value">{{ @$comission_type_name }} Service</span>
              </div>
              <div class="comp-row">
                 @php
                      $job_type = $job_type_name = '';
                      $job_type = @$benchApplication->outsourcing_type;
                      if ($job_type == 1) {
                          $job_type_name = 'Fulltime ';
                      } elseif ($job_type == 2) {
                          $job_type_name = 'Contract';
                      }
                     
                  @endphp

                <span class="comp-label">Job Type</span>
                <span class="comp-value">{{ @$benchApplication->employmentType }}</span>
              </div>
            </div>
          </section>   
          

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50); color: var(--color-primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <h3 class="card-title">Contact Details</h3>
            </div>
            <div class="contact-list">
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                  Email ID 
                </span>
                <a href="mailto:{{ @$benchApplication->email }}" class="contact-value">{{ @$benchApplication->email }}</a>
              </div>
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                  Contact Number 
                </span>
                <a href="javascript:;" class="contact-value" style="display: flex;">
                    <div id="firstPhoneDisplaybench" class="fw-bolder text-gray-800 fs-6" style="display: flex;"></div>
                    <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$benchApplication->phone }}</span>
                </a>
              </div>
            </div>
            <div class="contact-actions">
              <button type="button" class="btn btn-sm btn-whatsapp">
                  <a href="https://api.whatsapp.com/send?phone=91{{@$benchApplication->phone}}" target="_blank" class="contact-value" style="display: flex;">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                Chat on WhatsApp
                </a>
              </button>
              @php
                $linkedin = @$benchApplication->linkedin;

                if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                    $linkedin = 'https://' . $linkedin;
                }
              @endphp
              @if(!empty(@$benchApplication->linkedin))
              <button type="button" class="btn btn-sm btn-linkedin">
                <a href="{{ $linkedin ?? 'javascript:;' }}" target="{{ $linkedin ? '_blank' :'_self' }}" class="contact-value" style="display: flex;color: #fff;">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                  <rect x="2" y="9" width="4" height="12"/>
                  <circle cx="4" cy="4" r="2"/>
                </svg>
                Connect on LinkedIn
                </a>
              </button>
              @endif
            </div>
          </section>   
          
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <h3 class="card-title">Relationship Manager Details</h3>
            </div>
             @foreach( $bench_assign_row as $key=> $assignVal)
            <div class="rm-card">
              <figure class="rm-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </figure>
            
               
                  <div class="rm-details">
                    <h4>{{ ucwords(@$assignVal->name) }}</h4>
                    <div class="rm-contact-list">
                      <div class="rm-contact-item" style="position: relative;display: flex;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <div class="iti__flag-container" data-code="{{ $assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex;position: relative;"> </div>
                        {{ ucwords(@$assignVal->mobile) }}
                      </div>
                      <div class="rm-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                          <polyline points="22,6 12,13 2,6"/>
                        </svg>
                         {{ @$assignVal->email }}
                      </div>
                    </div>
                  </div>
            </div><hr>
            
             @endforeach	
          </section>
        </aside>
      </div>
    </div>

      <div class="confidentiality-banner" role="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <p>All candidate information is confidential and intended for internal use only.</p>
      </div>

      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
        @php
            $__cid = auth()->user()->company_id ?? 0;
            $rcvCompanyLogo = ''; $rcvCompanyName = '';
            if($__cid){
                $__c = \DB::table('companies')->select('logo','company_name')->where('id', $__cid)->first();
                $rcvCompanyName = ucwords((string) (@$__c->company_name ?? ''));
                $__l = $__c->logo ?? '';
                if(!empty($__l) && strlen($__l) > 5){ $rcvCompanyLogo = route('service_company.job-applications.company-logo-proxy'); }
            }
            // candidate skills — merge comma-string field + bench_candidate_skill_experience rows
            $rcvSkills = [];
            if(!empty(@$benchApplication->skills)){
                foreach(explode(',', $benchApplication->skills) as $s){ $s=trim($s); if($s!=='') $rcvSkills[]=$s; }
            }
            if(!empty($skillExperiences)){
                foreach($skillExperiences as $__se){ $__sk=trim((string)(@$__se->skill_key)); if($__sk!=='') $rcvSkills[]=$__sk; }
            }
            $rcvSkills = array_values(array_unique($rcvSkills));
            $__te = trim((string) @$benchApplication->total_exp);
            $rcvBExp = $__te !== '' ? $__te.' Years' : '--';
            if((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1)){
                $rcvBNotice = 'Available '.date('d M Y', strtotime(@$benchApplication->np_id));
            } else { $rcvBNotice = trim((string) @$benchApplication->np_id); }
            if($rcvBNotice === '') $rcvBNotice = '--';
            $rcvBLoc = trim((string) @$city_name);
            if($rcvBLoc === '') $rcvBLoc = trim(ucwords((string) @$country_name));
            if($rcvBLoc === '') $rcvBLoc = '--';
            $__cur1 = html_entity_decode(strip_tags((string) @$current_ctc_cur), ENT_QUOTES, 'UTF-8');
            $__cur2 = html_entity_decode(strip_tags((string) @$current_ectc_cur), ENT_QUOTES, 'UTF-8');
            $rcvBCctc = trim($__cur1.' '.@$benchApplication->pay_rate_amount); if($rcvBCctc === '') $rcvBCctc = '--';
            $rcvBEctc = trim($__cur2.' '.@$benchApplication->bill_rate_amount); if($rcvBEctc === '') $rcvBEctc = '--';
            $rcvBPresented = ucwords((string) (auth()->user()->name ?? '')); if($rcvBPresented === '') $rcvBPresented = '--';
            $rcvDownloadUrl = route('download.resume', ['id' => $benchApplication->id, 'path' => rtrim(strtr(base64_encode((string)@$benchApplication->resume), '+/', '-_'), '=')]);
            // own-company candidate -> reveal contact/social; otherwise mask
            $rcvMaskContact = ((int) @$benchApplication->company_id !== (int) (auth()->user()->company_id ?? 0));
        @endphp
        @include('service_company.partials.resume_cv_viewer', [
            'rcvResume'      => @$benchApplication->resume,
            'rcvMaskContact' => $rcvMaskContact,
            'rcvSkills'      => $rcvSkills,
            'rcvCompanyLogo' => $rcvCompanyLogo,
            'rcvCompanyName' => $rcvCompanyName,
            'rcvBExp'        => $rcvBExp,
            'rcvBCctc'       => $rcvBCctc,
            'rcvBEctc'       => $rcvBEctc,
            'rcvBNotice'     => $rcvBNotice,
            'rcvBLoc'        => $rcvBLoc,
            'rcvBPresented'  => $rcvBPresented,
            'rcvDownloadUrl' => $rcvDownloadUrl,
        ])
      @endif
    </main>
  </div>


<script>

    $('.page-tabs .tab').on('click', function () {
        $('.page-tabs .tab').removeClass('tab-active').attr('aria-selected', 'false');

        $(this).addClass('tab-active').attr('aria-selected', 'true');
    });

    var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input)

   var input_show = document.querySelector('input[name=tags_show]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input_show)

    $( document ).ready(function() {
 
      
       const firstSavedCountryCode = "{{ @$benchApplication->phone_code }}"; // Example country code (lowercase)
      
       const firstPhoneDisplaybenchDiv = document.getElementById("firstPhoneDisplaybench");
 
         const flagDiv = document.createElement("div");
         flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;
 
         // Get the dial code for the selected country code
         const dialCode = countryDialCodes[firstSavedCountryCode] || ""; // Default to empty string if not found
 
         // Create a span to display the dial code
         const dialCodeSpan = document.createElement("span");
         dialCodeSpan.textContent = dialCode;
 
         // Append the flag and the dial code to the display div
         firstPhoneDisplaybenchDiv.appendChild(flagDiv);
         firstPhoneDisplaybenchDiv.appendChild(dialCodeSpan);


        setTimeout(() => {
            // alert("k");
            $('.iti__flag-container').each(function(index) {
                var cnt = $(this).attr('data-row');
                const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
                var calling_code = $(this).attr('data-code');
                
                const flagDiv = document.createElement("div");
                flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

                const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
                const dialCodeSpan = document.createElement("span");
                dialCodeSpan.textContent = dialCode;

                // Append the flag and dial code
                flagContainer.appendChild(flagDiv);
                flagContainer.appendChild(dialCodeSpan);


            });
        }, 2000);


        });
</script> 
