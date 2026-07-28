@php 
   use App\Helper\GeneralMethod;
@endphp 
<style>
.text-justify{text-align: justify!important;}
#send_email .modal-dialog{margin: .3rem auto!important;}
#fwd_resume .modal-dialog{margin: .3rem auto!important;}
@media  only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr{width: max-content!important;}
  .table > :not(caption) > * > *{padding: 0rem;}
  td:nth-child(n+2),
  th:nth-child(n+2){width: revert!important;}
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesBenchApplication{width: 1025px;}}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesBenchApplication{width: 1200px;}}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesPublicBenchApplication{width: 1025px;}}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesPublicBenchApplication{width: 1200px;} }
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesCopy{width: 1025px;}}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesCopy{width: 1200px;}}
</style>
 <link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
   				  
   $resume_url			=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
   $photo  				=	url('public/avatar.png');
   if(!empty($application->photo)){
      $photo			=	 $application->photo;	
   }   
   $current_ctc_cur	=	$current_ectc_cur	=	$current_cur = "";
   $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	$posted_by_company	=	'';	
   $country_id			=	@$benchApplication->country;
   $state				=	@$benchApplication->state;
   if(@$country_id > 0)
   {
			$country_name_Arr	= GeneralMethod::getCountryName($country_id);	
			foreach($country_name_Arr as $cval){
				$country_name	= ucwords($cval['name']);	
			}
			
			if($country_id == 101){
					$state_name	= @$benchApplication->state_name;
					$city_name	= @$benchApplication->city_name; 	
			}else{		
					if( $state > 0){
						$state_name_Arr		= GeneralMethod::getStateName($country_id, $state);	
						foreach($state_name_Arr as $sval){
							$state_name	= ucwords($sval['name']);	
						}	
					}
					$city_name	= @$benchApplication->city;
			}
			$currencySymbole 	=	GeneralMethod::currencySymbole(); 
      $currencySymboleforexcell 	=	GeneralMethod::currencySymboleforexcell(); 
			$current_ctc_cur	=	$currencySymbole[@$benchApplication->pay_rate_currency];
			$current_ectc_cur	=	$currencySymbole[@$benchApplication->bill_rate_currency]; 
      $current_cur	=	$currencySymboleforexcell[@$benchApplication->bill_rate_currency]; 
   }	
   
   	$posted_by_company		=	@$benchApplication->posted_by; 
@endphp 	



  <div class="page-wrapper">
    <button type="button" class="page-close" aria-label="Close page" id="kt_activitiesPublicBenchApplication_close">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>

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
            <span class="badge-available">{{ @$benchApplication->master_status_name }}</span>
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
        <button type="button" class="btn btn-primary openSideBar" data-row-source_from_type="2" data-row-id="{{ @$benchApplication->id }}" data-row-jobid="">
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Add To Job
        </button>

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

                <div style="position:relative;display:inline-block;" id="hdrDlWrapPub">
                  <button type="button" class="btn btn-secondary" id="hdrDlTogglePub" style="background:no-repeat;border:1px solid #e2e8f0;display:flex;align-items:center;gap:5px;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:#475569">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                      <polyline points="7 10 12 15 17 10"/>
                      <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span style="color:#475569">Download Resume</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;color:#475569"><polyline points="6 9 12 15 18 9"/></svg>
                  </button>
                  <div id="hdrDlMenuPub" style="display:none;position:absolute;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:230px;z-index:999;overflow:hidden;">
                    <a target="_blank" data-id="{{ $benchApplication->id }}" class="downloadCount" href="{{ route('download.resume', ['id' => $benchApplication->id,'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]) }}" style="display:block;padding:9px 13px;font-size:12px;font-weight:500;color:#334155;text-decoration:none;border-bottom:1px solid #f1f5f9;">
                      Download Resume
                    </a>
                    <button type="button" onclick="hdrDownloadWithLogoPub()" style="display:flex;align-items:center;gap:6px;width:100%;padding:9px 13px;font-size:12px;font-weight:600;color:#15803d;background:#f0fdf4;border:0;border-left:3px solid #22c55e;cursor:pointer;text-align:left;">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                      Resume with Company Logo
                      <span style="font-size:10px;background:#22c55e;color:#fff;border-radius:4px;padding:1px 5px;margin-left:auto;font-weight:700;letter-spacing:.3px;">LOGO</span>
                    </button>
                  </div>
                </div>
                <script>
                (function(){
                  var t=document.getElementById('hdrDlTogglePub'),m=document.getElementById('hdrDlMenuPub');
                  if(t&&m){
                    t.addEventListener('click',function(e){e.stopPropagation();m.style.display=m.style.display==='none'?'block':'none';});
                    document.addEventListener('click',function(){m.style.display='none';});
                  }
                })();
                function hdrDownloadWithLogoPub(){
                  document.getElementById('hdrDlMenuPub').style.display='none';
                  var rcvTab=document.querySelector('button[data-target="resume_cv"], button[data-rcv-tab], a[href="#resume_cv"].tab');
                  if(rcvTab) rcvTab.click();
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
        @endif
       
      </div>
    </headerShow>

    <nav class="page-tabs" id="benchTabs" aria-label="Profile sections">
      <button type="button" class="tab tab-active" data-target="profile_overview" aria-selected="true">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Profile Overview
      </button>
      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
      <button type="button" class="tab" data-target="resume_cv" data-rcv-tab aria-selected="false">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        Resume / CV
      </button>
      @endif
    </nav>

    <main class="page-content">
      <div class="tab-pane active" id="profile_overview" role="tabpanel">
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
            <div class="info-value">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);display: ruby; padding: 1px 7px;">
                <svg class="hidden-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
                 <span style="color: black;">Details are hidden</span>
              </div>
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
                                $bill_rate_mode = '';
                                $bill_rate_mode = (@$benchApplication->bill_rate_mode) ? @$benchApplication->bill_rate_mode : 0;
                            @endphp
                <span class="comp-label">Expected Salary</span>
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
        @if($agencyAndRelationshipDetails->exists)
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <path d="M9 22v-4h6v4"/>
                  <line x1="8" y1="6" x2="8" y2="6.01"/>
                  <line x1="16" y1="6" x2="16" y2="6.01"/>
                  <line x1="8" y1="10" x2="8" y2="10.01"/>
                  <line x1="16" y1="10" x2="16" y2="10.01"/>
                </svg>
              </div>
              <h3 class="card-title">Agency Details</h3>
              @php
                $companyDetailsGet = GeneralMethod::companyDetailsGet(@$benchApplication->company_id);
                $city = $companyDetailsGet->locations_address[0]['locations_address'][0]['city_name'] ?? '';
                $state = $companyDetailsGet->locations_address[0]['locations_address'][0]['state_name'] ?? '';
                $country = $companyDetailsGet->locations_address[0]['locations_address'][0]['country_name'] ?? '';
              @endphp
      
            </div>
            <div class="agency-grid">
              <span class="agency-label">Agency Name</span>
              <span class="agency-value">{{ @$companyDetailsGet->company_name }}</span>

              <span class="agency-label">Website</span>
              <span class="agency-value"><a href="{{ @$companyDetailsGet->website }}" target="_blank" rel="noopener noreferrer">{{ @$companyDetailsGet->website }}</a></span>

              <span class="agency-label">Industry</span>
              <span class="agency-value">{{ @$companyDetailsGet->industry['industry_name'] }}</span>

              <span class="agency-label">Company Size</span>
              <span class="agency-value">
                @if(@$companyDetailsGet->company_size_from >=10000)
                {{ @$companyDetailsGet->company_size_from }}+
                @else
                {{ @$companyDetailsGet->company_size_from }} - {{ @$companyDetailsGet->company_size }}
                @endif
                 Employees</span>

              <span class="agency-label">Headquarter</span>
              <span class="agency-value">{{ $city }}, {{ $state }}, {{ $country }}</span>

              <span class="agency-label">Company Type</span>
              <span class="agency-value">{{ $accountCompanyType[@$companyDetailsGet->company_type] }}</span>

              <span class="agency-label">Founded</span>
              
              <span class="agency-value">{{ @$companyDetailsGet->founded }}</span>

              <span class="agency-label">Specialties</span>
              <span class="agency-value">IT Staffing, Contract Staffing, Recruitment</span>
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
        @else
          <section class="cardshow hidden-card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <path d="M9 22v-4h6v4"/>
                  <line x1="8" y1="6" x2="8" y2="6.01"/>
                  <line x1="16" y1="6" x2="16" y2="6.01"/>
                  <line x1="8" y1="10" x2="8" y2="10.01"/>
                  <line x1="16" y1="10" x2="16" y2="10.01"/>
                </svg>
              </div>
              <h3 class="card-title">Agency Details</h3>
            </div>
            <div class="hidden-blur-content">
              <div class="hidden-placeholder-rows">
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
                <div class="hidden-placeholder-row"><div class="hidden-placeholder-label"></div><div class="hidden-placeholder-value"></div></div>
              </div>
            </div>
            <div class="hidden-overlay">
              <svg class="hidden-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <p class="hidden-title">Details are hidden</p>
              <p class="hidden-subtitle">Add to job to view agency details</p>
              <button type="button" class="btn btn-primary btn-sm openSideBar" data-row-source_from_type="2" data-row-id="{{ @$benchApplication->id }}" data-row-jobid="">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <line x1="12" y1="8" x2="12" y2="16"/>
                  <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Add To Job
              </button>
            </div>
          </section>

          <section class="cardshow hidden-card">
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
            <div class="hidden-blur-content">
              <div class="rm-hidden-layout">
                <div class="hidden-placeholder-avatar"></div>
                <div class="rm-hidden-info">
                  <div class="rm-hidden-line"></div>
                  <div class="rm-hidden-line short"></div>
                  <div class="rm-hidden-line short"></div>
                </div>
              </div>
            </div>
            <div class="hidden-overlay">
              <svg class="hidden-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <p class="hidden-title">Details are hidden</p>
              <p class="hidden-subtitle">Add to job to view relationship manager details</p>
              <button type="button" class="btn btn-primary btn-sm openSideBar" data-row-source_from_type="2" data-row-id="{{ @$benchApplication->id }}" data-row-jobid="">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <line x1="12" y1="8" x2="12" y2="16"/>
                  <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Add To Job
              </button>
            </div>
          </section>
        @endif
        </aside>
      </div>

      <div class="confidentiality-banner" role="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <p>All candidate information is confidential and intended for internal use only.</p>
      </div>
      </div><!-- /#profile_overview -->

      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
        @php
            // viewing company logo + name for the banner
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
            // banner stats
            $__te = trim((string) @$benchApplication->total_exp);
            $rcvBExp = $__te !== '' ? $__te.' Years' : '--';
            if((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1)){
                $rcvBNotice = 'Available '.date('d M Y', strtotime(@$benchApplication->np_id));
            } else { $rcvBNotice = trim((string) @$benchApplication->np_id); }
            if($rcvBNotice === '') $rcvBNotice = '--';
            $rcvBLoc = trim((string) @$city_name);
            if($rcvBLoc === '') $rcvBLoc = trim(ucwords((string) @$country_name));
            if($rcvBLoc === '') $rcvBLoc = '--';
            $__cur = html_entity_decode(strip_tags((string) @$current_ectc_cur), ENT_QUOTES, 'UTF-8');
            $rcvBEctc = trim($__cur.' '.@$benchApplication->bill_rate_amount);
            if($rcvBEctc === '') $rcvBEctc = '--';
            $rcvBCctc = '--';
            $rcvBPresented = ucwords((string) (auth()->user()->name ?? ''));
            if($rcvBPresented === '') $rcvBPresented = '--';
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
    (function(){
      var tabs = document.querySelectorAll('#benchTabs .tab');
      tabs.forEach(function(btn){
        btn.addEventListener('click', function(){
          var target = this.getAttribute('data-target');
          tabs.forEach(function(b){ b.classList.remove('tab-active'); b.setAttribute('aria-selected','false'); });
          this.classList.add('tab-active'); this.setAttribute('aria-selected','true');
          document.querySelectorAll('.page-content > .tab-pane').forEach(function(p){ p.classList.remove('active','show'); });
          var el = document.getElementById(target);
          if(el){ el.classList.add('active','show'); }
        });
      });
    })();
  </script>

	 												



<script>
	$( document ).ready(function() {

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


