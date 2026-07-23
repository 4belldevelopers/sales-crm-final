@php 
   use App\Helper\GeneralMethod;
   $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0	=>	"INR",
        1	=>	"EUR",
        2	=>	"JPY",
        3	=>	"GBP",
        4	=>	"USD",
        5	=>	"AED"
     ];
@endphp
<style>
.text-justify { text-align: justify!important; }
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr{	width: max-content!important;}
  .table > :not(caption) > * > *{	padding: 0.75em 0rem!important;}
  td:nth-child(n+2),
  th:nth-child(n+2){	width: revert!important;	border-bottom: 1px dashed #f5f8fa!important;}
}
.slot-card {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.2s ease;
    }
@media only screen and (min-width: 900px) and (max-width: 1400px)
{
 .slot-card {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.2s ease;
    }
}
.iti__flag {
    margin-top: 2px !important;
}

    .slot-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 5px;
    }
    
    .slot-card:hover {
      transform: translateY(-3px);
    }
  
    .book-btn {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }
    .book-btn:hover {
      background: #0056b3;
    }
    .book-btnoff {
      background: #aeaeaf;
      color: #fff;
      border: none;
      padding: 10px 16px;
      cursor: not-allowed !important;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }
    .book-btnoff:hover {
      background: #aeaeaf;
    }
    .wordtext{
      margin-bottom: 5px;
    }
    .book-btnoff-booked{
      background: #06c732;
      color: #fff;
      cursor: not-allowed !important;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }

    #kt_activitiesApplication_close svg{
      color: red;
    }

</style>

 <link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
      
    $jobId = $application->jobId ?? $application->job_id;
    $jobData = DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$jobId)->first();
    $jobMode = $jobData->jobMode ?? @$application->current_ctc_mode;
    $jobCurrency = $jobData->jobCurrency ?? @$application->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";
      
   $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
   $photo  					=	''; //url('public/avatar.png');
   if(!empty($application->photo)){
      $photo			=	 $application->photo;	
   }  
   $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	'';	
   $country_id			=	@$application->country;
   $state				=	@$application->state;
  //  if(@$country_id > 0)
  //  {
  
     $city_name	= @$application->city_name ? ucwords(@$application->city_name).',' : '';
     $state_name	= @$application->state_name ? ucwords(@$application->state_name).',' : ''; 
     $country_name	= ucwords(@$application->country_name);	
   		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
      // dd($currencySymbole);
   		$current_ctc_cur	=	$current_ectc_cur	=	"";
       if($application->condidate_type=='other'){
        $current_ctc_cur	=	$currencySymbole[@$jobCurrency];
        $current_ectc_cur	=	$currencySymbole[@$jobCurrency];
       }else{
        $current_ctc_cur	=	$currencySymbole[@$jobCurrency];
        $current_ectc_cur	=	$currencySymbole[@$jobCurrency];
       }
   		
  //  }			
  //  rjobid from_company_id	
  // dd( $current_ctc_cur);

      $posted_by_image			=	$posted_by			=	'';

      if($CaptainCompanyid != $application->ClientComID && $application->ClientComID != 0 && $application->ClientComID > 0){
            $spokperson = DB::table('job_assign')->select('users.name')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $jobId)->where('job_assign.company_id', $application->ClientComID)->first();
            $posted_by = (@$spokperson->name) ? @$spokperson->name : @$application->posted_by;
        }else{
          // dd($application->posted_by);
          $posted_by = @$application->posted_by;
          // dd( $posted_by);
        }
        

      if(!empty($application->posted_by_image)){	
        $posted_by_image		=@$application->posted_by_image;
      }			

      // dd($CaptainCompanyid,$application->ClientComID,$posted_by);
      if(!empty($posted_by)){
        $bgTextDecore				=	GeneralMethod::icoinText($posted_by);
      }
                  	
                  // dd("done");	  

  							
@endphp
@php
    $dnone = "d-none";
    if($application->master_status_id==8 && $application->rjobid > 0 && $application->from_company_id == auth()->user()->company_id){
      $dnone = "";
    }elseif($application->master_status_id !=8 && $application->rjobid > 0 && $application->from_company_id== auth()->user()->company_id){
      $dnone = "";   
    }elseif(in_array("add_schedule", $userPermissions) && $application->rjobid < 0){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->company_id== auth()->user()->company_id){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->master_status_id==8 && $application->from_company_id == auth()->user()->company_id){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->master_status_id==8 && $application->to_company_id == auth()->user()->company_id){
      $dnone = "";
    } 

@endphp



<div class="page-wrapper">
  <button type="button" class="page-close" aria-label="Close page" id="kt_activitiesApplication_close">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
  </button>

  <div class="card-header-top px-5 py-0" id="kt_activities_header">
    <div class="d-flex align-items-center py-1">

      @if(in_array("add_schedule", $userPermissions)) 
        <a href="javascript:void(0);" id="app_interview_schedule" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 {{ $dnone }}"
        rel="{{@$application->wiseId}}"> <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24"></polygon>
              <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
              <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
            </g>
          </svg>
          </span> Interview </a> 
      @endif 
      @if(@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id)

      @else
            <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary sendmaimodal btn-active-primary fw-bolder me-3"> <span class="svg-icon svg-icon-primary svg-icon-1x">
              <i class="fa fa-envelope" style=" font-size: 19px;"></i>
                    </span> Email
              </a>
              
            @if(@$application->condidate_type=='other' && $notVisiableBench)  
            {{-- https://api.whatsapp.com/send?phone=91{{@$application->phone}} --}}
              <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-success btn-active-primary sendwhatsappmodal fw-bolder me-3"> <span class="svg-icon svg-icon-success svg-icon-1x">
                  <i class="fa fa-whatsapp" style=" font-size: 22px;"></i>
                </span> Whatsapp
              </a>
          @endif
      @endif
      
      
    </div>
  </div>
  <headershow class="page-header">
    <div class="candidate-identity">
      <figure class="avatar">
        <svg class="avatar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span class="avatar-status" aria-label="Available"></span>
      </figure>

      <div class="candidate-header-content">
        <div class="candidate-name-row">
          <h1 class="candidate-name-show">{{ ucwords(@$application->full_name) }}</h1>
          <span class="{{ (@$application->css_style) }}" aria-label="Available" style="padding:2px 7px;">{{ ucwords(@$application->name) }}</span>
        </div>
        <p class="candidate-role-show">{{ ucwords(@$application->current_designation) }}</p>
        <div class="candidate-meta-bar">
          {{-- <span class="meta-item">
            <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Gender: {{ ucwords(@$application->gender) }}</span>
          </span> --}}
          <span class="meta-item">
            <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            @php 
                $originalDate 	= @$application->created_at;
                $newDate 		= date("d-M-Y h:i a", strtotime($originalDate)); 
            @endphp

            <span>Current Company: {{ (@$application->current_company) ? ucwords(@$application->current_company) :  ucwords(@$application->cur_company_name) }} </span>
          </span>
        </div>
        <div class="candidate-skills">
           


          <span class="skills-label">Skills:</span>
          <ul class="skills-list">
              @php
                  $skills				=	'';
                  $skills				=	@$application->skills;
                  if(!empty($skills)){
                    $skillsArr		=	'';
                    $skillsArr		=	explode(',', $skills);
                    foreach($skillsArr as $sval){
                      echo '<span class="skill-tag">'.@$sval.'</span>';
                    }
                  } 
              @endphp
          </ul>
        </div>
      </div>
    </div>
    <div class="header-actions">
      @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other' && $editprofile===true)
          @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other') 
            <button type="button" class="btn btn-primary {{ $dnone }}" data-row-source_from_type="2" data-row-id="1382" data-row-jobid=""
                style="">
                <a href="{{route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)])}}?currentPage={{GeneralMethod::value_encrypt($currentPage)}}&currentSlug={{GeneralMethod::value_encrypt($currentSlug)}}" class="{{ $dnone }}" style="display: contents;color:#ffffff">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                  </svg>
                  <span>Edit Profile</span>
                </a>
              </button>
          @endif
      @endif
       @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf', 'doc', 'docx']))
          @php
            $url = @$application->resume;

            $info = pathinfo($url);
            // dd($info,$url);
          @endphp
          @if($resumeAwsPath)
          
            <div style="position:relative;display:inline-block;" id="hdrDlWrapApp">
              <button type="button" class="btn btn-secondary" id="hdrDlToggleApp" style="background:no-repeat;border:1px solid #e2e8f0;display:flex;align-items:center;gap:5px;">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:#475569">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="7 10 12 15 17 10"/>
                  <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span style="color:#475569">Download Resume</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;color:#475569"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div id="hdrDlMenuApp" style="display:none;position:absolute;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:230px;z-index:999;overflow:hidden;">
                <a href="{{ route('download.resume', ['id' => $application->id,'path' => urlencode($url)]) }}" target="_blank" style="display:block;padding:9px 13px;font-size:12px;font-weight:500;color:#334155;text-decoration:none;border-bottom:1px solid #f1f5f9;">
                  Download Resume
                </a>
                <button type="button" onclick="hdrDownloadWithLogoApp()" style="display:flex;align-items:center;gap:6px;width:100%;padding:9px 13px;font-size:12px;font-weight:600;color:#15803d;background:#f0fdf4;border:0;border-left:3px solid #22c55e;cursor:pointer;text-align:left;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                  Resume with Company Logo
                  <span style="font-size:10px;background:#22c55e;color:#fff;border-radius:4px;padding:1px 5px;margin-left:auto;font-weight:700;letter-spacing:.3px;">LOGO</span>
                </button>
              </div>
            </div>
            <script>
            (function(){
              var t=document.getElementById('hdrDlToggleApp'),m=document.getElementById('hdrDlMenuApp');
              if(t&&m){
                t.addEventListener('click',function(e){e.stopPropagation();m.style.display=m.style.display==='none'?'block':'none';});
                document.addEventListener('click',function(){m.style.display='none';});
              }
            })();
            function hdrDownloadWithLogoApp(){
              document.getElementById('hdrDlMenuApp').style.display='none';
              // the job-application viewer is always visible (no tab), so just poll for pages
              var tries=0;
              var poll=setInterval(function(){
                if(document.querySelectorAll('#rcvPages .rcv-page').length){
                  clearInterval(poll);
                  var b=document.querySelector('[data-dl="logo"]');
                  if(b) b.click();
                } else if(++tries>80){
                  clearInterval(poll);
                  alert('Resume is taking too long to load. Please scroll down to the Resume / CV viewer and try again.');
                }
              },100);
            }
            </script>
          @else 
            @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other' && $editprofile===true)
                    @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other')  
                    <button type="button" class="btn btn-secondary" style="background: no-repeat; border: 1px solid #e2e8f0;">                        
                      <a target="_blank" data-id="1382" class="downloadCount"
                        href="{{route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)])}}?currentPage={{GeneralMethod::value_encrypt($currentPage)}}&currentSlug={{GeneralMethod::value_encrypt($currentSlug)}}"
                        style="display: contents;color:#475569">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                          <polyline points="7 10 12 15 17 10"></polyline>
                          <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Download Resume</span>
                      </a>
                    </button>
                  @endif 
            @endif 
          @endif
         @else
         <button type="button" class="btn btn-secondary" style="background: no-repeat; border: 1px solid #e2e8f0;">                       
              <a  href="{{route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)])}}?currentPage={{GeneralMethod::value_encrypt($currentPage)}}&currentSlug={{GeneralMethod::value_encrypt($currentSlug)}}" class="btn btn-light-primary btn-sm py-1" style="display: contents;color:#475569">
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
  </headershow>

  <nav class="page-tabs tab-content nav" aria-label="Profile sections" role="tablist">
    <a type="button" class="tab tab-active" data-toggle="tab" href="#personal_details" aria-selected="true">
      <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      Profile Overview
    </a>
    <a type="button" class="tab" data-toggle="tab" href="#notes" aria-selected="false">
      <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
        Notes
    </a>
    @if($editComman)
      <a type="button" class="tab" data-toggle="tab" href="#timeline" aria-selected="false">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        Timeline
      </a>
     @endif
     @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
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

    @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
    @php
        $rcvJobSkills = [];
        if(!empty($jobSkill)){
            foreach($jobSkill as $js){ $js=(object)$js; $sn=trim(@$js->skill_name); if($sn!=='') $rcvJobSkills[]=$sn; }
        }
        $rcvJobSkills = array_values(array_unique($rcvJobSkills));
        $rcvCandSkills = [];
        if(!empty(@$application->skills)){
            foreach(explode(',', $application->skills) as $cs){ $cs=trim($cs); if($cs!=='') $rcvCandSkills[]=$cs; }
        }
        $rcvCandSkills = array_values(array_unique($rcvCandSkills));
        $__rcvPath = parse_url((string) @$application->resume, PHP_URL_PATH);
        $__rcvPath = $__rcvPath ?: (string) @$application->resume;
        $rcvExt = strtolower(pathinfo($__rcvPath, PATHINFO_EXTENSION));
        $rcvIsPdf = ($rcvExt === 'pdf');
        // same-origin URL pdf.js / mammoth can fetch: r2 -> download route, full URL -> direct, relative -> url()
        $rcvFetchUrl = '';
        if(!empty($application->resume)){
            $__r = $application->resume;
            if($resumeAwsPath || strpos($__r, 'file.jero.works') !== false){
                $rcvFetchUrl = route('download.resume', ['id' => $application->id, 'path' => urlencode($__r)]);
            } elseif(filter_var($__r, FILTER_VALIDATE_URL)){
                $rcvFetchUrl = $__r;
            } else {
                $rcvFetchUrl = url($__r);
            }
        }
        $rcvPublicUrl = @$application->resume;
        $rcvFileName  = basename(@$application->resume);
        // doc/docx -> server converts to PDF (LibreOffice, cached) so the full PDF pipeline can render + highlight it
        $rcvConvertUrl = in_array($rcvExt, ['doc','docx'])
            ? route('service_company.job-applications.resume-as-pdf', ['id' => $application->id])
            : '';
        // hide contact/social only for other-company bench candidates
        $rcvMaskContact = (@$application->condidate_type == 'bench_condidate'
            && (int) @$application->other_company !== (int) (auth()->user()->company_id ?? 0));
        // recruiting company logo + name (single cheap DB read; r2 logos streamed same-origin to avoid canvas CORS taint)
        $rcvCompanyLogo = '';
        $rcvCompanyName = '';
        $__rcvCid = auth()->user()->company_id ?? 0;
        if($__rcvCid){
            $__rcvC = DB::table('companies')->select('logo','company_name')->where('id', $__rcvCid)->first();
            $rcvCompanyName = ucwords((string) (@$__rcvC->company_name ?? ''));
            $__rcvLogo = $__rcvC->logo ?? '';
            if(!empty($__rcvLogo) && strlen($__rcvLogo) > 5){
                // same-origin proxy returns the logo bytes so they embed without CORS taint
                $rcvCompanyLogo = route('service_company.job-applications.company-logo-proxy');
            }
        }
        // candidate summary for the submission banner
        $__te = trim((string) @$application->total_exp);
        $rcvBExp = ($__te !== '') ? ($__te . ' Years') : '--';
        if((@$application->serving_notice_period_mode == 0) || (@$application->serving_notice_period_mode == 1)){
            $rcvBNotice = 'Available ' . date('d M Y', strtotime(@$application->np_id));
        } else {
            $rcvBNotice = trim((string) @$application->np_id);
        }
        if($rcvBNotice === '') $rcvBNotice = '--';
        $rcvBLoc = trim(@$city_name, ', ');
        if($rcvBLoc === '') $rcvBLoc = trim(ucwords((string) @$country_name), ', ');
        if($rcvBLoc === '') $rcvBLoc = '--';
        $__pay  = GeneralMethod::formatViewSalary(@$application->ctc_yearly, $symbols, $jobMode);
        $__bill = GeneralMethod::formatViewSalary(@$application->ectc_yearly, $symbols, $jobMode);
        $__cur1 = html_entity_decode(strip_tags((string) @$current_ctc_cur), ENT_QUOTES, 'UTF-8');
        $__cur2 = html_entity_decode(strip_tags((string) @$current_ectc_cur), ENT_QUOTES, 'UTF-8');
        $rcvBCctc = trim($__cur1 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__pay->amount));
        $rcvBEctc = trim($__cur2 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__bill->amount));
        if($rcvBCctc === '') $rcvBCctc = '--';
        if($rcvBEctc === '') $rcvBEctc = '--';
        $rcvBPresented = ucwords((string) (auth()->user()->name ?? ''));
        if($rcvBPresented === '') $rcvBPresented = '--';
    @endphp
    <div class="tab-pane fade" id="resume_cv" role="tabpanel">
      <style>
        #resume_cv, #resume_cv * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
        #resume_cv { font-size:13px; }
        #resume_cv .rcv-wrap { display:flex; flex-direction:column; gap:16px; }
        #resume_cv .rcv-main { order:2; }
        #resume_cv .rcv-side { order:1; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; }
        #resume_cv .rcv-side-head { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
        #resume_cv .rcv-side-head .rcv-head-txt h3 { font-size:14px; font-weight:700; color:#0f172a; margin:0; }
        #resume_cv .rcv-sub { font-size:11px; color:#94a3b8; }
        #resume_cv .rcv-summary { display:flex; gap:8px; margin:0; }
        #resume_cv .rcv-sumitem { min-width:78px; background:#f8fafc; border:1px solid #eef2f7; border-radius:8px; padding:8px 10px; text-align:center; }
        #resume_cv .rcv-sumnum { display:block; font-size:14px; font-weight:700; color:#0f172a; line-height:1; }
        #resume_cv .rcv-sumlbl { font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
        #resume_cv .rcv-kw-toolbar { display:flex; align-items:center; gap:10px; margin:14px 0 4px; flex-wrap:wrap; }
        #resume_cv .rcv-kw-add { flex:1; min-width:200px; }
        #resume_cv .rcv-kw-add input { width:100%; padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; outline:none; }
        #resume_cv .rcv-kw-add input:focus { border-color:#6366f1; }
        #resume_cv .rcv-kw-actions { display:flex; gap:8px; }
        #resume_cv .rcv-btn { padding:7px 14px; border:1px solid #e2e8f0; background:#fff; border-radius:7px; font-size:11px; font-weight:600; color:#475569; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-btn:hover { background:#f1f5f9; }
        #resume_cv .rcv-kw-cols { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin-top:14px; }
        #resume_cv .rcv-kw-group { margin-top:0; }
        #resume_cv .rcv-kw-title { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin-bottom:8px; }
        #resume_cv .rcv-chips { display:flex; flex-wrap:wrap; gap:6px; }
        #resume_cv .rcv-chip { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:600; cursor:default; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; user-select:none; transition:all .12s; }
        #resume_cv .rcv-chip .rcv-cnt { font-size:10px; background:#e2e8f0; color:#475569; border-radius:10px; padding:0 5px; min-width:15px; text-align:center; }
        #resume_cv .rcv-chip.found { border-color:#bbf7d0; background:#f0fdf4; color:#15803d; }
        #resume_cv .rcv-chip.found .rcv-cnt { background:#bbf7d0; color:#15803d; }
        #resume_cv .rcv-chip.missing { opacity:.6; }
        #resume_cv .rcv-chip.active { background:#fef3c7; border-color:#fcd34d; color:#92400e; box-shadow:0 0 0 2px rgba(251,191,36,.25); }
        #resume_cv .rcv-chip.active .rcv-cnt { background:#fcd34d; color:#92400e; }
        #resume_cv .rcv-chip-x { margin-left:3px; cursor:pointer; font-weight:700; color:#92400e; opacity:.6; font-size:13px; line-height:1; }
        #resume_cv .rcv-chip-x:hover { opacity:1; }
        #resume_cv .rcv-kw-add-row { display:flex; gap:8px; margin:14px 0 2px; }
        #resume_cv .rcv-kw-add-row input { flex:1; min-width:0; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; outline:none; }
        #resume_cv .rcv-kw-add-row input:focus { border-color:#6366f1; }
        #resume_cv .rcv-add-btn { padding:8px 16px; border:none; background:#6366f1; color:#fff; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-add-btn:hover { background:#4f46e5; }
        #resume_cv .rcv-hl-dl { display:inline-flex; align-items:center; gap:5px; width:auto !important; padding:0 10px !important; height:30px; border:1px solid #c7d2fe !important; background:#eef2ff !important; color:#4f46e5 !important; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-hl-dl:hover { background:#6366f1 !important; color:#fff !important; border-color:#6366f1 !important; }
        #resume_cv .rcv-hl-dl[disabled] { opacity:.6; cursor:default; }
        #resume_cv .rcv-dl { position:relative; }
        #resume_cv .rcv-dl-menu { position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); min-width:248px; z-index:60; display:none; overflow:hidden; }
        #resume_cv .rcv-dl.open .rcv-dl-menu { display:block; }
        #resume_cv .rcv-dl-menu .rcv-dl-item { display:block; width:100%; height:auto; min-height:0; justify-content:flex-start; text-align:left; padding:9px 13px; font-size:12px; font-weight:500; color:#334155; background:#fff; border:0; border-radius:0; cursor:pointer; text-decoration:none; white-space:nowrap; }
        #resume_cv .rcv-dl-menu .rcv-dl-item:hover { background:#eef2ff; color:#4f46e5; }
        #resume_cv .rcv-dl-menu .rcv-dl-item + .rcv-dl-item { border-top:1px solid #f1f5f9; }
        #resume_cv .rcv-main { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; min-height:520px; }
        #resume_cv .rcv-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; border-bottom:1px solid #eef2f7; background:#fafbff; flex-wrap:wrap; }
        #resume_cv .rcv-file { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; min-width:0; }
        #resume_cv .rcv-file svg { width:16px; height:16px; color:#6366f1; flex-shrink:0; }
        #resume_cv .rcv-file span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:230px; }
        #resume_cv .rcv-tools { display:flex; align-items:center; gap:6px; }
        #resume_cv .rcv-tools button, #resume_cv .rcv-tools a { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid #e2e8f0; background:#fff; border-radius:7px; color:#475569; cursor:pointer; text-decoration:none; }
        #resume_cv .rcv-tools button:hover, #resume_cv .rcv-tools a:hover { background:#f1f5f9; color:#6366f1; }
        #resume_cv .rcv-tools svg { width:15px; height:15px; }
        #resume_cv .rcv-hitnav { display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600; color:#64748b; padding:0 4px; }
        #resume_cv .rcv-hlwrap { display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b; }
        #resume_cv .rcv-switch { position:relative; width:34px; height:18px; background:#cbd5e1; border-radius:20px; cursor:pointer; transition:.15s; }
        #resume_cv .rcv-switch.on { background:#6366f1; }
        #resume_cv .rcv-switch::after { content:''; position:absolute; top:2px; left:2px; width:14px; height:14px; background:#fff; border-radius:50%; transition:.15s; }
        #resume_cv .rcv-switch.on::after { left:18px; }
        #resume_cv .rcv-viewport { flex:1; overflow:auto; background:#525659; padding:18px; display:flex; flex-direction:column; align-items:center; gap:14px; }
        #resume_cv .rcv-page { position:relative; box-shadow:0 4px 16px rgba(0,0,0,.35); background:#fff; }
        #resume_cv .rcv-page canvas { display:block; }
        #resume_cv .rcv-textlayer { position:absolute; left:0; top:0; right:0; bottom:0; overflow:hidden; line-height:1; opacity:1; }
        #resume_cv .rcv-textlayer span { position:absolute; white-space:pre; color:transparent; transform-origin:0% 0%; }
        #resume_cv .rcv-textlayer mark.rcv-hit { background:rgba(251,191,36,.45); color:transparent; border-radius:2px; }
        #resume_cv .rcv-textlayer mark.rcv-hit-active { background:rgba(249,115,22,.6); box-shadow:0 0 0 2px rgba(249,115,22,.5); }
        #resume_cv .rcv-loading { color:#e2e8f0; font-size:13px; padding:40px; }
        #resume_cv .rcv-iframe { width:100%; height:70vh; border:0; background:#fff; }
        #resume_cv .rcv-html { background:#fff; width:100%; max-width:820px; margin:0 auto; padding:44px 52px; color:#1f2937; font-size:13px; line-height:1.65; box-shadow:0 4px 16px rgba(0,0,0,.35); word-wrap:break-word; }
        #resume_cv .rcv-html * { max-width:100%; font-size:inherit; }
        #resume_cv .rcv-html p { margin:0 0 8px; }
        #resume_cv .rcv-html h1, #resume_cv .rcv-html h2, #resume_cv .rcv-html h3 { font-size:14px; font-weight:700; margin:14px 0 6px; color:#0f172a; }
        #resume_cv .rcv-html ul, #resume_cv .rcv-html ol { margin:0 0 8px 20px; }
        #resume_cv .rcv-html table { border-collapse:collapse; width:100%; }
        #resume_cv .rcv-html td, #resume_cv .rcv-html th { border:1px solid #e2e8f0; padding:4px 6px; }
        #resume_cv .rcv-html img { height:auto; }
        #resume_cv .rcv-textlayer mark.rcv-redact { background:#111827; color:transparent !important; border-radius:2px; user-select:none !important; -webkit-user-select:none !important; -moz-user-select:none !important; pointer-events:none; }
        #resume_cv .rcv-html mark.rcv-redact { background:#111827; color:#111827; border-radius:2px; user-select:none !important; -webkit-user-select:none !important; -moz-user-select:none !important; }
        #resume_cv .rcv-html mark.rcv-hit { background:rgba(251,191,36,.5); color:inherit; border-radius:2px; padding:0 1px; }
        #resume_cv .rcv-html mark.rcv-hit-active { background:rgba(249,115,22,.65); box-shadow:0 0 0 2px rgba(249,115,22,.4); }
        #resume_cv .rcv-note { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; padding:10px 14px; border-radius:8px; margin:12px; }
        @media (max-width:640px){ #resume_cv .rcv-summary { width:100%; } #resume_cv .rcv-sumitem { flex:1; } }
      </style>

      <div class="content-split">
       <div class="content-main-full">
      <div class="rcv-wrap"
           data-url="{{ $rcvFetchUrl }}"
           data-public="{{ $rcvPublicUrl }}"
           data-ext="{{ $rcvExt }}"
           data-logo="{{ $rcvCompanyLogo }}"
           data-company="{{ $rcvCompanyName }}"
           data-pdfconv="{{ $rcvConvertUrl }}"
           data-mask="{{ $rcvMaskContact ? '1' : '0' }}"
           data-b-exp="{{ $rcvBExp }}"
           data-b-cctc="{{ $rcvBCctc }}"
           data-b-ectc="{{ $rcvBEctc }}"
           data-b-notice="{{ $rcvBNotice }}"
           data-b-loc="{{ $rcvBLoc }}"
           data-b-presented="{{ $rcvBPresented }}"
           data-ispdf="{{ $rcvIsPdf ? '1' : '0' }}">
        <!-- Keyword panel -->
        <aside class="rcv-side">
          <div class="rcv-side-head">
            <div class="rcv-head-txt">
              <h3>Keyword Highlights</h3>
              <span class="rcv-sub">Job skills matched against this résumé</span>
            </div>
            <div class="rcv-summary">
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMatched">0</span><span class="rcv-sumlbl">Matched</span></div>
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMissing">0</span><span class="rcv-sumlbl">Missing</span></div>
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvHits">0</span><span class="rcv-sumlbl">Highlights</span></div>
            </div>
          </div>
          <div class="rcv-kw-add-row">
            <input type="text" id="rcvKwInput" placeholder="Add a keyword to check &amp; highlight…" autocomplete="off">
            <button type="button" class="rcv-add-btn" id="rcvKwAddBtn">Add &amp; Check</button>
          </div>
          <div class="rcv-kw-cols">
            <div class="rcv-kw-group">
              <div class="rcv-kw-title">Job Key Skills</div>
              <div class="rcv-chips" id="rcvJobChips"></div>
            </div>
            <div class="rcv-kw-group" id="rcvCustomGroup" style="display:none;">
              <div class="rcv-kw-title">Added Keywords</div>
              <div class="rcv-chips" id="rcvCustomChips"></div>
            </div>
          </div>
        </aside>

        <!-- Viewer -->
        <section class="rcv-main">
          <div class="rcv-toolbar">
            <div class="rcv-file">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <span title="{{ $rcvFileName }}">{{ $rcvFileName }}</span>
            </div>
            <div class="rcv-tools">
              <div class="rcv-hitnav">
                <button type="button" id="rcvPrevHit" title="Previous match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></button>
                <span id="rcvHitNav">0/0</span>
                <button type="button" id="rcvNextHit" title="Next match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
              </div>
              <a id="rcvOpenNew" href="{{ $rcvPublicUrl }}" target="_blank" title="Open in new tab"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>
              <div class="rcv-dl" id="rcvDl">
                <button type="button" class="rcv-hl-dl" id="rcvDlToggle">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  <span>Download</span>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="rcv-dl-menu" id="rcvDlMenu">
                  <a class="rcv-dl-item" href="{{ $rcvFetchUrl ?: $rcvPublicUrl }}" target="_blank">Download Resume</a>
                  <button type="button" class="rcv-dl-item" data-dl="logo">Resume with Company Logo</button>
                  <button type="button" class="rcv-dl-item" data-dl="hl">Resume with Highlighted Keywords</button>
                  <button type="button" class="rcv-dl-item" data-dl="both">Highlighted Keywords + Company Logo</button>
                </div>
              </div>
            </div>
          </div>
          <div class="rcv-viewport" id="rcvViewport">
            <div class="rcv-loading" id="rcvLoading">Loading résumé…</div>
            <div id="rcvPages" style="display:flex;flex-direction:column;align-items:center;gap:14px;width:100%;"></div>
            <iframe class="rcv-iframe" id="rcvIframe" style="display:none;"></iframe>
          </div>
        </section>
      </div>
       </div>
      </div>

      <script>
      (function(){
        var inited = false;
        var jobSkills = @json($rcvJobSkills);
        var candSkills = @json($rcvCandSkills);
        var resumeText = '';
        var keywords = {};   // name -> {type, active, count}
        var hits = [];       // mark elements in document order
        var curHit = -1;
        var hlOn = true;
        var hideContact = true;   // redact email/phone/social for other-company bench candidates
        var rcvMode = 'pdf';      // 'pdf' (canvas pages) or 'html' (docx via mammoth)
        var htmlOriginal = '';    // pristine docx HTML for re-highlighting
        var pdfDoc = null;        // loaded PDF.js doc, re-rendered hi-res for downloads
        var EMAIL_RE=/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/g;
        var PHONE_RE=/\+?\d[\d\s().\-]{7,}\d/g;
        var URL_RE=/(?:https?:\/\/|www\.)[^\s<>"']+/gi;
        var SOCIAL_RE=/\b(?:linkedin|github|gitlab|twitter|facebook|fb|instagram|insta|behance|dribbble|medium|stackoverflow|youtube|t)\.(?:com|me|io|in|co)[^\s<>"']*/gi;
        function redactHtml(html){
          var wrap=function(m){ return '<mark class="rcv-redact">'+m+'</mark>'; };
          return html.replace(EMAIL_RE,wrap).replace(URL_RE,wrap).replace(SOCIAL_RE,wrap).replace(PHONE_RE,wrap);
        }

        function esc(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
        function escRe(s){ return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
        function $id(id){ return document.getElementById(id); }

        function activeList(){ return Object.keys(keywords).filter(function(k){ return keywords[k].active; }); }

        function buildRegex(){
          var act = activeList();
          if(!act.length) return null;
          var pat = act.map(escRe).sort(function(a,b){ return b.length-a.length; }).join('|');
          return new RegExp('('+pat+')','gi');
        }

        function countOccurrences(){
          Object.keys(keywords).forEach(function(k){
            var re = new RegExp(escRe(k),'gi');
            var m = resumeText.match(re);
            keywords[k].count = m ? m.length : 0;
          });
        }

        function chipHtml(name){
          var k = keywords[name];
          var cls = 'rcv-chip' + (k.count>0 ? ' found' : ' missing') + (k.active ? ' active' : '');
          var rm = (k.type==='custom') ? '<span class="rcv-chip-x" data-rm="'+esc(name)+'" title="Remove">&times;</span>' : '';
          return '<span class="'+cls+'" data-kw="'+esc(name)+'">'+esc(name)+'<span class="rcv-cnt">'+k.count+'</span>'+rm+'</span>';
        }

        function renderChips(){
          var jc = '', xc = '';
          Object.keys(keywords).forEach(function(name){
            var t = keywords[name].type;
            if(t==='job') jc += chipHtml(name);
            else if(t==='custom') xc += chipHtml(name);
          });
          $id('rcvJobChips').innerHTML = jc || '<span class="rcv-sub">No job skills</span>';
          var cg = $id('rcvCustomGroup');
          if(cg){ if(xc){ cg.style.display='block'; $id('rcvCustomChips').innerHTML = xc; } else { cg.style.display='none'; $id('rcvCustomChips').innerHTML=''; } }
          var matched=0, missing=0;
          Object.keys(keywords).forEach(function(k){ if(keywords[k].count>0) matched++; else missing++; });
          $id('rcvMatched').textContent = matched;
          $id('rcvMissing').textContent = missing;
        }

        var REDACT_RE = new RegExp(EMAIL_RE.source+'|'+URL_RE.source+'|'+SOCIAL_RE.source+'|'+PHONE_RE.source, 'gi');
        function highlightInNode(node, re, cls){
          cls = cls || 'rcv-hit';
          if(node.nodeType === 3){
            var text = node.nodeValue;
            if(!text) return;
            re.lastIndex = 0;
            if(!re.test(text)){ re.lastIndex = 0; return; }
            re.lastIndex = 0;
            var frag = document.createDocumentFragment(), last = 0, m;
            while((m = re.exec(text)) !== null){
              if(m.index > last) frag.appendChild(document.createTextNode(text.slice(last, m.index)));
              var mk = document.createElement('mark'); mk.className = cls; mk.textContent = m[0];
              frag.appendChild(mk);
              last = m.index + m[0].length;
              if(m.index === re.lastIndex) re.lastIndex++;
            }
            if(last < text.length) frag.appendChild(document.createTextNode(text.slice(last)));
            node.parentNode.replaceChild(frag, node);
          } else if(node.nodeType === 1 && !/^(script|style|mark)$/i.test(node.nodeName)){
            Array.prototype.slice.call(node.childNodes).forEach(function(k){ highlightInNode(k, re, cls); });
          }
        }
        function applyHighlights(){
          var re = (hlOn ? buildRegex() : null);
          if(rcvMode === 'html'){
            var cont = $id('rcvHtml');
            if(cont){
              cont.innerHTML = htmlOriginal;
              if(hideContact) highlightInNode(cont, REDACT_RE, 'rcv-redact');
              if(re) highlightInNode(cont, re);
            }
            hits = Array.prototype.slice.call(document.querySelectorAll('#rcvHtml mark.rcv-hit'));
          } else {
            var spans = document.querySelectorAll('#rcvPages .rcv-textlayer span');
            spans.forEach(function(sp){
              if(sp.dataset.orig === undefined) sp.dataset.orig = sp.textContent;
              var t = sp.dataset.orig;
              if(!re && !hideContact){ sp.textContent = t; return; }
              var html = esc(t);
              if(re){ re.lastIndex = 0; html = html.replace(re, '<mark class="rcv-hit">$1</mark>'); }
              if(hideContact){ html = redactHtml(html); }
              sp.innerHTML = html;
            });
            hits = Array.prototype.slice.call(document.querySelectorAll('#rcvPages mark.rcv-hit'));
          }
          curHit = -1;
          $id('rcvHits').textContent = hits.length;
          $id('rcvHitNav').textContent = hits.length ? ('0/'+hits.length) : '0/0';
        }

        function gotoHit(dir){
          if(!hits.length) return;
          if(curHit>=0 && hits[curHit]) hits[curHit].classList.remove('rcv-hit-active');
          curHit = (curHit + dir + hits.length) % hits.length;
          var el = hits[curHit];
          el.classList.add('rcv-hit-active');
          el.scrollIntoView({ behavior:'smooth', block:'center' });
          $id('rcvHitNav').textContent = (curHit+1)+'/'+hits.length;
        }

        function toggleKw(name){
          if(!keywords[name]) return;
          keywords[name].active = !keywords[name].active;
          renderChips(); applyHighlights();
        }

        function addKeyword(name, type){
          name = (name||'').trim();
          if(!name) return;
          var key = Object.keys(keywords).filter(function(k){ return k.toLowerCase()===name.toLowerCase(); })[0];
          if(key){ keywords[key].active = true; }
          else {
            var re = new RegExp(escRe(name),'gi'); var m = resumeText.match(re);
            keywords[name] = { type: type||'custom', active:true, count: m?m.length:0 };
          }
          renderChips(); applyHighlights();
        }

        function loadScript(src){
          return new Promise(function(res, rej){
            var s = document.createElement('script'); s.src = src; s.onload = res; s.onerror = rej;
            document.head.appendChild(s);
          });
        }

        function loadLogo(){
          if(window.__rcvLogoTried) return Promise.resolve(window.__rcvLogo || null);
          window.__rcvLogoTried = true;
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var src = (wrap && wrap.getAttribute('data-logo')) || '';
          if(!src) return Promise.resolve(null);
          // fetch the bytes same-origin and turn them into a data URI — data URIs never taint the canvas
          return fetch(src, { credentials:'include' })
            .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.blob(); })
            .then(function(blob){
              return new Promise(function(res){
                var fr = new FileReader();
                fr.onload = function(){
                  var dataUri = fr.result;
                  var im = new Image();
                  im.onload = function(){ im.__dataUri = dataUri; window.__rcvLogo = im; res(im); };
                  im.onerror = function(){ res(null); };
                  im.src = dataUri;
                };
                fr.onerror = function(){ res(null); };
                fr.readAsDataURL(blob);
              });
            })
            .catch(function(){ return null; });
        }
        function setDlBusy(b){
          var t = $id('rcvDlToggle'); if(!t) return;
          t.disabled = b; var sp = t.querySelector('span'); if(sp) sp.textContent = b ? 'Preparing…' : 'Download';
        }
        function xmlEsc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
        function clip(s,n){ s = String(s==null?'':s); return s.length>n ? s.slice(0,n-1)+'…' : s; }
        function svgToImage(svg){
          return new Promise(function(res, rej){
            var blob = new Blob([svg], { type:'image/svg+xml;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var img = new Image();
            img.onload = function(){ URL.revokeObjectURL(url); res(img); };
            img.onerror = function(e){ URL.revokeObjectURL(url); rej(e); };
            img.src = url;
          });
        }
        function buildBannerSvg(wrap, logoUri){
          var g = function(a){ var v = wrap.getAttribute(a); return (v==null || v==='') ? '--' : v; };
          var cols = [
            { ic:'bag',   l:'EXPERIENCE',       v: clip(g('data-b-exp'),14) },
            { ic:'rupee', l:'CURRENT CTC',      v: clip(g('data-b-cctc'),14) },
            { ic:'rupee', l:'EXPECTED CTC',     v: clip(g('data-b-ectc'),14) },
            { ic:'clock', l:'NOTICE PERIOD',    v: clip(g('data-b-notice'),16) },
            { ic:'pin',   l:'CURRENT LOCATION', v: clip(g('data-b-loc'),16) },
            { ic:'user',  l:'PRESENTED BY',     v: clip(g('data-b-presented'),16) }
          ];
          var company = wrap.getAttribute('data-company') || '';
          var W=1500, H=110, navy='#0b1f3a', blue='#2563eb', gray='#64748b';
          function icon(type, cx, cy){
            var t = 'transform="translate('+(cx-9)+','+(cy-9)+') scale(0.78)"';
            var st = 'fill="none" stroke="'+blue+'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
            if(type==='bag')   return '<g '+t+'><rect x="2" y="7" width="20" height="14" rx="2" '+st+'/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" '+st+'/></g>';
            if(type==='clock') return '<g '+t+'><circle cx="12" cy="12" r="9" '+st+'/><polyline points="12 7 12 12 16 14" '+st+'/></g>';
            if(type==='pin')   return '<g '+t+'><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z" '+st+'/><circle cx="12" cy="10" r="3" '+st+'/></g>';
            if(type==='user')  return '<g '+t+'><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" '+st+'/><circle cx="12" cy="7" r="4" '+st+'/></g>';
            if(type==='rupee') return '<g '+t+'><circle cx="12" cy="12" r="10" fill="none" stroke="'+blue+'" stroke-width="1.6"/><text x="12" y="16.5" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="13" font-weight="700" fill="'+blue+'">₹</text></g>';
            return '';
          }
          var s = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 '+W+' '+H+'">';
          s += '<rect width="'+W+'" height="'+H+'" fill="#ffffff"/>';
          s += '<rect x="6" y="7" width="'+(W-12)+'" height="96" rx="12" fill="#ffffff" stroke="'+navy+'" stroke-width="2"/>';
          s += '<path d="M456 7 L494 7 L462 103 L424 103 Z" fill="'+navy+'"/>';
          s += '<path d="M482 7 L493 7 L461 103 L450 103 Z" fill="'+blue+'"/>';
          // logo + company name (left)
          if(logoUri){
            s += '<image xlink:href="'+logoUri+'" href="'+logoUri+'" x="22" y="20" width="92" height="44" preserveAspectRatio="xMinYMid meet"/>';
            if(company) s += '<text x="126" y="48" font-family="Poppins,Arial,sans-serif" font-size="18" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company,16))+'</text>';
          } else {
            s += '<text x="28" y="52" font-family="Poppins,Arial,sans-serif" font-size="22" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company||'Company',18))+'</text>';
          }
          // verified badge
          s += '<rect x="22" y="70" width="312" height="26" rx="13" fill="#eaf1ff"/>';
          s += '<circle cx="40" cy="83" r="8" fill="'+blue+'"/><path d="M36 83 l3 3 6 -7" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
          s += '<text x="54" y="87" font-family="Poppins,Arial,sans-serif" font-size="11" font-weight="700" letter-spacing="0.3" fill="'+navy+'">PRESCREENED VERIFIED CANDIDATE</text>';
          var x0=515, x1=W-24, cw=(x1-x0)/cols.length;
          cols.forEach(function(c, i){
            var cx = x0 + cw*i + cw/2;
            if(i>0){ var dx = x0 + cw*i; s += '<line x1="'+dx+'" y1="18" x2="'+dx+'" y2="92" stroke="#e2e8f0" stroke-width="1.4"/>'; }
            s += icon(c.ic, cx, 34);
            s += '<text x="'+cx+'" y="62" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="10.5" font-weight="600" letter-spacing="0.3" fill="'+gray+'">'+xmlEsc(c.l)+'</text>';
            s += '<text x="'+cx+'" y="84" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="15" font-weight="700" fill="'+navy+'">'+xmlEsc(c.v)+'</text>';
          });
          s += '</svg>';
          return s;
        }
        async function buildPdf(opts){
          var pages = document.querySelectorAll('#rcvPages .rcv-page');
          if(!pages.length){ alert('This option is available once the PDF résumé has loaded.'); return; }
          setDlBusy(true);
          try {
            if(!(window.jspdf && window.jspdf.jsPDF)){
              await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
            }
            var JsPDF = window.jspdf.jsPDF;
            var wrap = document.querySelector('#resume_cv .rcv-wrap');
            var bannerImg = null, logoFailed = false;
            if(opts.logo){
              var logo = await loadLogo();
              var logoUri = (logo && logo.__dataUri) ? logo.__dataUri : '';
              if(!logoUri) logoFailed = true;
              try { bannerImg = await svgToImage(buildBannerSvg(wrap, logoUri)); } catch(e){ bannerImg = null; }
            }
            var QUALITY = 2.5, MAXW = 2200;
            var pdf = null;
            for(var idx=0; idx<pages.length; idx++){
              var pageWrap = pages[idx];
              // re-render the page from the PDF at high resolution for a crisp export
              var cssW = pageWrap.clientWidth || 760, cssH = pageWrap.clientHeight || 1000, hi;
              if(pdfDoc){
                var page = await pdfDoc.getPage(idx+1);
                var base = page.getViewport({ scale:1 });
                var renderW = Math.min(cssW * QUALITY, MAXW);
                var vp = page.getViewport({ scale: renderW / base.width });
                hi = document.createElement('canvas'); hi.width = Math.floor(vp.width); hi.height = Math.floor(vp.height);
                await page.render({ canvasContext: hi.getContext('2d'), viewport: vp }).promise;
              } else { hi = pageWrap.querySelector('canvas'); if(!hi) continue; }
              var w = hi.width, h = hi.height;
              var headerH = (bannerImg && idx === 0) ? Math.round(w * 0.073) : 0;
              var tmp = document.createElement('canvas'); tmp.width = w; tmp.height = h + headerH;
              var ctx = tmp.getContext('2d');
              ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, w, tmp.height);
              if(headerH){ ctx.drawImage(bannerImg, 0, 0, w, headerH); }
              ctx.drawImage(hi, 0, headerH, w, h);
              var pr = pageWrap.getBoundingClientRect();
              if(opts.highlights){
                ctx.fillStyle = 'rgba(255, 210, 0, 0.4)';
                pageWrap.querySelectorAll('mark.rcv-hit').forEach(function(mk){
                  var r = mk.getBoundingClientRect();
                  ctx.fillRect((r.left - pr.left)/pr.width*w, headerH + (r.top - pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h, 6));
                });
              }
              if(hideContact){
                ctx.fillStyle = '#111827';
                pageWrap.querySelectorAll('mark.rcv-redact').forEach(function(mk){
                  var r = mk.getBoundingClientRect();
                  ctx.fillRect((r.left - pr.left)/pr.width*w, headerH + (r.top - pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h, 6));
                });
              }
              var img = tmp.toDataURL('image/jpeg', 0.96);
              var headerCssH = headerH * (cssW / w);
              var pw = Math.round(cssW), ph = Math.round(cssH + headerCssH);
              var orient = pw > ph ? 'l' : 'p';
              if(!pdf){ pdf = new JsPDF({ orientation: orient, unit: 'px', format: [pw, ph] }); }
              else { pdf.addPage([pw, ph], orient); }
              pdf.addImage(img, 'JPEG', 0, 0, pw, ph, undefined, 'FAST');
            }
            if(pdf){
              var fn = ((document.querySelector('#resume_cv .rcv-file span') || {}).textContent || 'resume').replace(/\.[^.]+$/, '');
              var sfx = (opts.highlights && opts.logo) ? '_highlighted_logo' : opts.highlights ? '_highlighted' : opts.logo ? '_logo' : '';
              pdf.save(fn + sfx + '.pdf');
            }
            if(opts.logo && !bannerImg){ alert('Could not add the company banner; downloaded the résumé without it.'); }
            else if(opts.logo && logoFailed){ alert('Banner added without the company logo — the logo image could not be embedded due to its security settings.'); }
          } catch(err){
            alert('Could not generate the PDF.');
          } finally {
            setDlBusy(false);
          }
        }

        function wireControls(){
          $id('rcvPrevHit').addEventListener('click', function(){ gotoHit(-1); });
          $id('rcvNextHit').addEventListener('click', function(){ gotoHit(1); });
          var dl = $id('rcvDl'), dlToggle = $id('rcvDlToggle'), dlMenu = $id('rcvDlMenu');
          if(dl && dlToggle && dlMenu){
            dlToggle.addEventListener('click', function(e){ e.stopPropagation(); dl.classList.toggle('open'); });
            document.addEventListener('click', function(e){ if(!dl.contains(e.target)) dl.classList.remove('open'); });
            dlMenu.addEventListener('click', function(e){
              var item = e.target.closest('.rcv-dl-item');
              if(!item) return;
              dl.classList.remove('open');
              var t = item.getAttribute('data-dl');
              if(t==='logo') buildPdf({ highlights:false, logo:true });
              else if(t==='hl') buildPdf({ highlights:true, logo:false });
              else if(t==='both') buildPdf({ highlights:true, logo:true });
              // no data-dl => the plain "Download Resume" link downloads the original natively
            });
          }
          var kwInput = $id('rcvKwInput');
          if(kwInput){
            kwInput.addEventListener('keydown', function(e){
              if(e.key==='Enter' || e.key===','){ e.preventDefault(); addKeyword(this.value,'custom'); this.value=''; }
            });
          }
          var addBtn = $id('rcvKwAddBtn');
          if(addBtn){
            addBtn.addEventListener('click', function(){ var i=$id('rcvKwInput'); addKeyword(i.value,'custom'); i.value=''; i.focus(); });
          }
          // remove an added keyword (× on custom chips only)
          document.addEventListener('click', function(e){
            var x = e.target.closest && e.target.closest('#resume_cv .rcv-chip-x');
            if(x){ e.preventDefault(); var nm = x.getAttribute('data-rm'); if(keywords[nm]){ delete keywords[nm]; renderChips(); applyHighlights(); } }
          });
          // chip click toggles highlight on/off
          document.addEventListener('click', function(e){
            if(e.target.closest && e.target.closest('#resume_cv .rcv-chip-x')) return;
            var chip = e.target.closest && e.target.closest('#resume_cv .rcv-chip[data-kw]');
            if(chip){ toggleKw(chip.getAttribute('data-kw')); }
          });
        }

        function withTimeout(promise, ms){
          return Promise.race([ promise, new Promise(function(_,rej){ setTimeout(function(){ rej(new Error('timeout')); }, ms); }) ]);
        }
        async function renderPdf(url, fallback){
          var fail = function(msg){ if(typeof fallback === 'function'){ fallback(); } else { showIframe(msg || 'Showing résumé in preview mode.'); } };
          if(typeof pdfjsLib === 'undefined'){ fail('PDF viewer library not available.'); return; }
          try { var ver = pdfjsLib.version || '2.11.338'; pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js'; } catch(e){}
          var pagesEl = $id('rcvPages');
          // Fetch the bytes ourselves — avoids PDF.js range-request/worker hangs and gives clear errors.
          var bytes;
          try {
            var resp = await withTimeout(fetch(url, { credentials:'include' }), 35000);
            if(!resp.ok) throw new Error('HTTP '+resp.status);
            var ab = await resp.arrayBuffer();
            bytes = new Uint8Array(ab);
            var sig = String.fromCharCode.apply(null, bytes.subarray(0,5));
            if(sig.indexOf('%PDF') === -1) throw new Error('not-pdf');
          } catch(err){ fail(); return; }
          var doc;
          try { doc = await withTimeout(pdfjsLib.getDocument({ data: bytes }).promise, 25000); }
          catch(err){ fail(); return; }
          pdfDoc = doc;
          rcvMode = 'pdf';
          $id('rcvPages').style.display='flex';
          $id('rcvLoading').style.display='none';
          var containerW = Math.min(pagesEl.clientWidth || 760, 820);
          pagesEl.innerHTML = ''; hits = [];
          resumeText = '';
          for(var p=1; p<=doc.numPages; p++){
            var page = await doc.getPage(p);
            var base = page.getViewport({ scale:1 });
            var scale = (containerW-2)/base.width;
            var viewport = page.getViewport({ scale:scale });
            var wrap = document.createElement('div'); wrap.className='rcv-page';
            wrap.style.width = Math.floor(viewport.width)+'px';
            wrap.style.height = Math.floor(viewport.height)+'px';
            var canvas = document.createElement('canvas');
            var outScale = window.devicePixelRatio || 1;
            canvas.width = Math.floor(viewport.width*outScale);
            canvas.height = Math.floor(viewport.height*outScale);
            canvas.style.width = Math.floor(viewport.width)+'px';
            canvas.style.height = Math.floor(viewport.height)+'px';
            wrap.appendChild(canvas);
            var tl = document.createElement('div'); tl.className='rcv-textlayer';
            tl.style.width = Math.floor(viewport.width)+'px';
            tl.style.height = Math.floor(viewport.height)+'px';
            wrap.appendChild(tl);
            pagesEl.appendChild(wrap);
            var ctx = canvas.getContext('2d');
            var transform = outScale!==1 ? [outScale,0,0,outScale,0,0] : null;
            await page.render({ canvasContext:ctx, viewport:viewport, transform:transform }).promise;
            var tc = await page.getTextContent();
            resumeText += tc.items.map(function(i){ return i.str; }).join(' ') + '\n';
            try {
              var task = pdfjsLib.renderTextLayer({ textContent:tc, container:tl, viewport:viewport, textDivs:[] });
              if(task && task.promise) await task.promise;
            } catch(e){}
          }
          countOccurrences();
          // default: highlight job skills that are present
          Object.keys(keywords).forEach(function(k){ keywords[k].active = (keywords[k].type==='job' && keywords[k].count>0); });
          renderChips();
          applyHighlights();
        }

        async function renderDocx(url){
          if(typeof mammoth === 'undefined'){ showIframe('Showing résumé in preview mode.'); return; }
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var pub = wrap.getAttribute('data-public') || '';
          var sources = [];
          if(url) sources.push(url);
          if(pub && pub !== url) sources.push(pub);
          var bytes = null;
          for(var i=0; i<sources.length && !bytes; i++){
            try {
              var resp = await withTimeout(fetch(sources[i], { credentials:'include' }), 15000);
              if(resp.ok) bytes = await resp.arrayBuffer();
            } catch(e){}
          }
          if(!bytes){ showIframe('Could not load the Word file for highlighting; shown in preview mode.'); return; }
          var html = '';
          try {
            var result = await mammoth.convertToHtml({ arrayBuffer: bytes });
            html = (result && result.value) ? result.value : '';
          } catch(err){ showIframe('This file could not be converted for highlighting (it may be an older .doc); shown in preview mode.'); return; }
          if(!html.trim()){ showIframe('This file could not be converted for highlighting; shown in preview mode.'); return; }
          rcvMode = 'html';
          $id('rcvLoading').style.display = 'none';
          $id('rcvPages').style.display = 'none';
          var cont = document.createElement('div'); cont.id = 'rcvHtml'; cont.className = 'rcv-html';
          cont.innerHTML = html;
          $id('rcvViewport').appendChild(cont);
          htmlOriginal = html;
          resumeText = cont.innerText || cont.textContent || '';
          countOccurrences();
          Object.keys(keywords).forEach(function(k){ keywords[k].active = (keywords[k].type==='job' && keywords[k].count>0); });
          renderChips();
          applyHighlights();
        }

        function showIframe(msg){
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var pub = wrap.getAttribute('data-public') || '';
          $id('rcvLoading').style.display='none';
          $id('rcvPages').style.display='none';
          var ifr = $id('rcvIframe');
          if(pub){
            ifr.src = 'https://docs.google.com/gview?url=' + encodeURIComponent(pub) + '&embedded=true';
            ifr.style.display='block';
          } else {
            $id('rcvLoading').style.display='block';
            $id('rcvLoading').textContent = 'Résumé file is not available for preview.';
          }
          if(!document.getElementById('rcvNote')){
            var note = document.createElement('div'); note.id='rcvNote'; note.className='rcv-note';
            note.textContent = msg || 'Keyword highlighting works for PDF and Word (.docx) résumés. This file is shown in preview mode.';
            $id('rcvViewport').parentNode.insertBefore(note, $id('rcvViewport'));
          }
          // keep keyword chips usable (counts will be 0 without extractable text)
          countOccurrences(); renderChips();
        }

        function seedKeywords(){
          jobSkills.forEach(function(s){ if(s) keywords[s] = { type:'job', active:false, count:0 }; });
        }

        function init(){
          if(inited) return;
          var pane = document.getElementById('resume_cv');
          if(!pane || pane.getAttribute('data-rcv-inited') === '1') return;  // another (stale) instance already owns this DOM
          pane.setAttribute('data-rcv-inited', '1');
          inited = true;
          var wrap = pane.querySelector('.rcv-wrap');
          hideContact = wrap.getAttribute('data-mask') !== '0';
          var url = wrap.getAttribute('data-url');
          var isPdf = wrap.getAttribute('data-ispdf')==='1';
          seedKeywords();
          renderChips();
          wireControls();
          var ext = (wrap.getAttribute('data-ext') || '').toLowerCase();
          var pdfConv = wrap.getAttribute('data-pdfconv') || '';
          if(isPdf && url){ renderPdf(url); }
          else if(pdfConv){
            // doc/docx: render the server-converted PDF (full highlight + download pipeline);
            // if conversion fails, fall back to in-browser Word rendering, then preview.
            $id('rcvLoading').textContent = 'Converting document to PDF…';
            renderPdf(pdfConv, function(){ if(url){ renderDocx(url); } else { showIframe(); } });
          }
          else if(url){ renderDocx(url); }
          else { showIframe(); renderChips(); }
        }

        // only the most-recently-injected instance responds (avoids stale listeners after AJAX re-injection)
        var MY_TOKEN = (window.__rcvSeq = (window.__rcvSeq || 0) + 1);
        document.addEventListener('click', function(e){
          if(MY_TOKEN !== window.__rcvSeq) return;
          var a = e.target.closest && e.target.closest('a[href="#resume_cv"]');
          if(a){ setTimeout(init, 120); }
        });
        // if the tab is already visible on load
        if(document.querySelector('#resume_cv.active, #resume_cv.show.active')) setTimeout(init, 250);
      })();
      </script>
    </div>
    @endif

    <div class="tab-pane fade" id="notes" role="tabpanel">
      <div class="content-split">
        <div class="content-main-full">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Notes</h3>
            </div>
            <div class="form-group">
              
              @if($notVisiableBench)
              <div class="row pt-7">

                <!--begin::Timeline-->
                <div class="timeline timeline-3">
                  <div class="timeline-items">
                    <h3 class="fw-bolder m-0">Notes</h3>
                    <div id="applicant-notes" class="col-sm-12">
                      {{-- style=" max-height: 250px; overflow-y: auto; padding-right: 8px; scrollbar-width: thin; scrollbar-color: #c1c1c1 transparent; " --}}
                      <ul class="list-unstyled">
                          <?php 
                            $cnt	=	0;
                            foreach($notesArr as $key => $notes )
                            {
                              $date		=	$notes->created_at;
                              $noteDate 	= date("d-M-Y", strtotime($date));
                              $noteTitme 	= date("h:i A", strtotime($date));
                              $posted_by_image			=	'';
                              if(!empty($notes->posted_by_image)){	
                                $posted_by_image			=	@$notes->posted_by_image;
                              }  
                            
                              $bgTextDecore				=	GeneralMethod::icoinText(@$notes->name);	 
                                                                 
                          ?>
                        <li class="media" id="note-{{ $notes->id }}">
                          <div class="timeline-item">
                            <?php if($cnt%3==0){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"></rect>
                                  <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
                                  <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php }else if($cnt%3==1){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                  <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                  <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php  }else if($cnt%3==2){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"></path>
                                  <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"></rect>
                                </g>
                              </svg>
                              </span> </div>
                            <?php } ?>
                            <div class="timeline-content">
                              <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="me-2 fs-6"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder me-2">New Note Added</a> <span class="text-muted me-2"><?php echo @$noteTitme; ?> | <?php echo @$noteDate; ?></span>
                                  <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" title="{{ ucwords($notes->name) }}"> 
                                   
                                    <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$notes->name)}}" >
                                      <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                                      <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                                        <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($notes->name, 0, 1)}}</div>
                                      </a>		 
                                      </div>
                                      </div>
                                  

                                   </div>
                                </div>
                                <div class="dropdown ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="New Project"> <a href="javascript:void(0);" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <a href="javascript:void(0);" class="edit-note" data-note-id="{{ $notes->id }}"><i class="fa fa-edit ml-2"></i></a> <a href="javascript:;" class="delete-note" data-note-id="{{ $notes->id }}"><i class="fa fa-trash ml-1 text-danger"></i></a> </div>
                              </div>
                              <p class="p-0 fs-6 comment-para note-text">{{ ucfirst($notes->note_text) }}</p>
                              <div class="note-textarea"></div>
                            </div>
                          </div>
                        </li>
                         <?php 	$cnt++;	}	?>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--eng::Timeline-->
                
                <div class="separator separator-dashed my-10"></div>
                 @if($editComman)
                <form class="form">
                  <div class="form-group">
                    <textarea class="form-control form-control-lg form-control-solid" name="note" id="note_text" rows="3" placeholder="Type notes" style="margin-top: 0px; margin-bottom: 0px; height: 88px;"></textarea>
                  </div>
                  <div class="row pt-4">
                    <div class="col"> <a href="javascript:void(0);" id="add-note" class="btn btn-sm btn-light-primary fw-bold">Add notes</a> </div>
                  </div>
                </form>
                @endif
                
              </div>
              @endif
              

            </div>
            <br>

         

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
                    $experience = trim((string) @$application->total_exp);
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
              $rel_experience = trim((string) @$application->rel_exp);
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
            <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Gender</div>
            <div class="info-value">{{ ucwords(@$application->gender) }}</div>
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
            <div class="info-value">{{ ucwords(substr(@$application->nationality, 0, 30)) }}</div>
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
            <div class="info-value">{{ ucwords(substr(@$application->wm, 0, 30)) }}</div>
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
                          if((@$application->serving_notice_period_mode == 0) || (@$application->serving_notice_period_mode == 1) )
                          {
                            $np				=	@$application->np_id;
                                              $np 			=	'Available '.date("dM Y", strtotime($np)); 
                        
                          }else{
                            $np				=	@$application->np_id;
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
            <div class="info-value">{{ ucwords(@$application->qualification_name) }}</div>
          </div>
        </article>
      </section>



      <div class="content-split">
        <div class="content-main">

              <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Professional Summary</h3>
            </div>
            <p class="summary-text">{{@$application->careersummary}}</p>
            {{-- <span class="word-count-badge">184 / 200 Words</span> --}}
          </section>
         @if($application->condidate_type !='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Additional Details</h3>
            </div>
            <div class="skills-table">
              <div class="skills-thead">
                <div>Skill</div>
                <div>Experience</div>
                <div>Self Rating</div>
              </div>

                 @php 
                 
                          if(!empty($skillExperiencesCount))
                          {
                              $customTitleId	=	1;
                              $html			=	'';
                              $inc_first		=	0;
                              $inc_last		=	1;

                            $html		=	'<div class="row">';	
                          foreach($skillExperiences as $exp)
                          {  
                               
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper($exp->skill_key).'
                          </div>
                          <div class="skill-exp-cell">'.($exp->year ?? '0') .'.'. ($exp->month ?? '0').' Year</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                                aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$exp->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp
            </div>
          </section>
          @endif

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Key Skills &amp; Experience</h3>
               @if($skillAndQuestions)
                  @if($editComman)
                  <span class="fw-bold fs-6"> 
                    <a type="button" class="py-0 editSkill" href="javascript:void(0);" style="margin-left: 4px;">Edit&nbsp;&nbsp;<i class="fa fa-edit"></i>
                    </a>
                  </span>
                  @endif

                  <span class="fw-bold fs-6"> 
                    <a type="button" class="btn btn-light-primary btn-sm py-0 editSkillUpdate d-none" id="skill-update-form" href="javascript:void(0);" style="margin-left: 4px;">Save</a>
                  </span>
                  <span class="fw-bold fs-6"> 
                    <a type="button" class="btn btn-light-danger btn-sm py-0 editSkillCancel d-none" href="javascript:void(0);" style="margin-left: 4px;">Cancel</a>
                  </span>
              @endif
            </div>
             <div class="job_skill_edit d-none">
              
              <form class="ajax-form" method="POST" id="skillUpdateForm" name="skillUpdateForm" enctype="multipart/form-data">
                @csrf
                <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Exp.(Years)</th>
                      <th>Exp.(Months)</th>
                      <th>Rating</span></th>
                    </tr>
                  </thead>
                  <tbody>                        
                        <input type="hidden" name="job_id" value="{{ @$job_id }}" id="job_id" />
                        <input type="hidden" name="appId" value="{{ @$application->id }}" id="appId" />
                        
                            @foreach($jobSkill as $skill_val)
                            @php
                             $skill_val = (object) $skill_val;                            
                            @endphp
                                @if(!empty($skill_val->skill_name))
                               
                                  {{-- <label class="form-label">{{ strtoupper($skill_val->skill_name) }} EXPERIENCE</label> --}}
                                  {{-- <input type="text" class="form-control form-control-solid" name="skillId[{{$skill_val->id }}]" placeholder="Enter detail" value="{{ ($skillShow) ? $skill_val->skill_value : '' }}"> --}}
                                  
                                 
                                      @php

                                      //  dd($skill_val->skill_value);
                                      if($skill_val->skill_value){
                                        $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                        // dd($skillValue);
                                        if(!is_null($skillValue) && !empty($skillValue)){
                                         
                                          $total_skill_exp =  GeneralMethod::separateNumberParts($skillValue);
                                          $skill_exp_years = ($total_skill_exp[0]);
                                          $skill_exp_months = ($total_skill_exp[1]); 

                                          $selected_skill_exp_year = $skill_exp_years;
                                          $selected_skill_exp_month = $skill_exp_months;
                                          $selected_skill_rating = $skill_val->rating ?? null;
                                          
                                        }else{
                                          $selected_skill_exp_year = null;
                                          $selected_skill_exp_month =null;
                                          $selected_skill_rating =null;
                                        }
                                        //  dd($skillValue,$skill_val->skill_value);
                                      }else{
                                        $selected_skill_exp_year = null;
                                        $selected_skill_exp_month =null;
                                        $selected_skill_rating =null;
                                      }
                                      
                                      // dd($skill_exp_years,$skill_exp_months);
                
                                    @endphp
                                    <tr>
                                    <td>
                                      <label class="form-label">{{ strtoupper($skill_val->skill_name) }} EXPERIENCE</label>
                                    </td>
                
                                      <td>
                                        <select class="form-select" arial-label="Select rel_exp_year" id="rel_exp_year" name="YearskillId[{{ $skill_val->job_skill_id }}]">
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
                                        <select class="form-select" arial-label="Select rel_exp_month" id="rel_exp_month" name="MonthskillId[{{ $skill_val->job_skill_id }}]">
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
                                        <select class="form-select" arial-label="Select rel_exp_month" id="rating" name="rating[{{ $skill_val->job_skill_id }}]">
                                          <option value="">Select Month</option>
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
                </form> 
             </div>
            <div class="skills-table job_skill_view">
              <div class="skills-thead">
                <div>Skill</div>
                <div>Experience</div>
                <div>Self Rating</div>
              </div>

                @php 
                          if(!empty($jobSkill))
                          {
                            $html		=	'<div class="row">';	
                          foreach($jobSkill as $skill_val)
                          {  
                             $skill_val = (object) $skill_val;                            
                            
                            $exp = 'EXP.';  
                            $yrs = '';      
                              if(!empty($skill_val->skill_value)){
                              
                                $yrs = ''; 
                              }
                              $skillShowView = '';
                              if($skillShow){
                                $skillShowView = $skill_val->skill_value;
                                if (is_int($skill_val->skill_value)) {
                                  $skillShowView = $skill_val->skill_value.' '.$yrs;
                                }
                              
                              }
                            
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper($skill_val->skill_name).'
                          </div>
                          <div class="skill-exp-cell">'.$skillShowView.'</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$skill_val->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                                aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$skill_val->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp

            </div>
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Job Questions</h3>
                @if($skillAndQuestions)
                @if($editComman)
                    <span class="fw-bold fs-6"> 
                      <a type="button" class="py-0 editJobQuestion" href="javascript:void(0);" style="margin-left: 4px;">Edit&nbsp;&nbsp;<i class="fa fa-edit"></i>
                      </a>
                    </span>
                    @endif
                    <span class="fw-bold fs-6"> 
                      <a type="button" class="btn btn-light-primary btn-sm py-0 editJobQuestionUpdate d-none" id="job-question-update-form" href="javascript:void(0);" style="margin-left: 4px;">Save</a>
                    </span>
                    <span class="fw-bold fs-6"> 
                      <a type="button" class="btn btn-light-danger btn-sm py-0 editJobQuestionCancel d-none" href="javascript:void(0);" style="margin-left: 4px;">Cancel</a>
                    </span>
                @endif

            </div>
              <div class="skills-table job_additional_edit d-none">
                <form class="ajax-form" method="POST" id="jobQuestionUpdateForm" name="jobQuestionUpdateForm" enctype="multipart/form-data">
                @csrf  
                <input type="hidden" name="job_id" value="{{ @$job_id }}" />
                <input type="hidden" name="appId" value="{{ @$application->id }}"/>
                <table class="sk">
                  <thead>
                    <tr>
                      <th style="width: 280px;">Job Questions<span class="rq">*</span></th>
                      <th>Answered</th>
                    </tr>
                  </thead>
                  <tbody> 

                    @php 
                                if(!empty($job_additional_detail)){
                                  
                                  $customTitleId	=	1;
                                  $html			=	'';
                                  $inc_first		=	0;
                                  $inc_last		=	1;
                          
                                  foreach($job_additional_detail as $aDetailArr){
                                    $aDetailArr = (object) $aDetailArr;
                                      if(!empty($aDetailArr->job_detail_value)){
                                      
                                          $ans_value	=	$aDetailArr->application_detail_value;	
                                            $html		.=	'<tr>
                                                       <td>
                                                      <label class="form-label">'.strtoupper($aDetailArr->job_detail_value).':</label>
                                                      <input type="hidden" name="job_question[]" value="'.$aDetailArr->question_id.'">
                                                      </td>
                                                      <td>
                                                      <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
                                                      <span class="text-muted form-text">Please enter your details</span>
                                                    </td>
                                                    </tr>';
                                      }
                                          $inc_first++;
                                          $inc_last++;
                                          $customTitleId++;				
                                    }
                        
                                    echo $html;	
                                }	                               
                            @endphp


                     
                </tbody>
                </table> 
              </form> 
            </div>
            <div class="skills-table job_additional_view">
              <div class="skills-thead">
                <div>Job Questions</div>
                <div>Answered</div>
              </div>
                 @php 
                          if(!empty($job_additional_detail))
                          {
                            $html		=	'<div class="row">';	
                          foreach($job_additional_detail as $skill_val)
                          {  
                            $aDetailArr = (object) $skill_val;
                            $ans_value	=	@$aDetailArr->application_detail_value;                         
                            
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper(@$aDetailArr->job_detail_value).'
                          </div>
                          <div class="skill-exp-cell">'.@$ans_value.'</div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp
            </div>
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50); color: var(--color-primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <h3 class="card-title">Interview Slots <span style="font-weight: 500; color: var(--color-text-tertiary);">(Next 3 Days)</span></h3>
                  @if($skillAndQuestions)
                      <span class="fw-bold fs-6"> 
                        <a type="button" class="py-0 editSlots" href="javascript:void(0);" style="margin-left: 4px;">Edit&nbsp;&nbsp;<i class="fa fa-edit"></i>
                        </a>
                      </span>
                      <span class="fw-bold fs-6"> 
                        <a type="button" class="btn btn-light-primary btn-sm py-0 editSlotsUpdate d-none" id="job-slots-update-form" href="javascript:void(0);" style="margin-left: 4px;">Save</a>
                      </span>
                      <span class="fw-bold fs-6"> 
                        <a type="button" class="btn btn-light-danger btn-sm py-0 editSlotsCancel d-none" href="javascript:void(0);" style="margin-left: 4px;">Cancel</a>
                      </span>
                  @endif
            </div>
                @php
                    $book1 = 'Book Now';
                    $book2 = 'Book Now';
                    $book3 = 'Book Now';
                    $bookbtn1 = 'book-btn';
                    $bookbtn2 = 'book-btn';
                    $bookbtn3 = 'book-btn';
                    $modal1 = '';
                    $modal2 = '';
                    $modal3 = '';
                    $date1 =  \Carbon\Carbon::parse(@$application->interviewDateOne)->format('Y-m-d h:i:s');
                      $date2 =  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('Y-m-d h:i:s');
                      $date3 =  \Carbon\Carbon::parse(@$application->interviewDateThree)->format('Y-m-d h:i:s');
                    if(count($scheduleDateArr)>0){
                          if(in_array($date1,$scheduleDateArr)){
                          $book1 = 'Booked';
                          $bookbtn1 = 'book-btnoff-booked';
                          $modal1 ='';
                          }
                          if(in_array($date2,$scheduleDateArr)){
                          $book2 = 'Booked';
                          $bookbtn2 = 'book-btnoff-booked';
                          $modal2 ='';
                          }
                          if(in_array($date3,$scheduleDateArr)){
                          $book3 = 'Booked';
                          $bookbtn3 = 'book-btnoff-booked';
                          $modal3 ='';
                          }
                    }            
              @endphp
            <div class="slots-grid job_slots_edit d-none">

              <form class="ajax-form" method="POST" id="jobSlotsUpdateForm" name="jobSlotsUpdateForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="appId" value="{{ @$application->wiseId }}"/>
                      
                            
                              
                              <div class="row">
                                <label class="form-label">Date and Time(Slot-1)</label>
                                 <div class="slot-day" style="display: -webkit-inline-box !important;">
                                <div class="">
                                  <input class="form-control" placeholder="Date" name="interviewDateOne" id="interviewDateOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <input class="form-control" placeholder="Time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <select name="interview_mode_one" id="interview_mode_one"
                                      onchange="getInterviewModeChild(this.value, {{ $job_id }});"
                                      class="form-select">
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

                              
                          <div class="row mt-5">
                                <label class="form-label">Date and Time(Slot-2)</label>
                          <div class="slot-day" style="display: -webkit-inline-box !important;">
                                <div class="">
                                  <input class="form-control" placeholder="Date" name="interviewDateTwo" id="interviewDateTwo" value="{{ (@$application->interviewDateTwo)  ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y'): '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <input class="form-control" placeholder="Time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <select name="interview_mode_two" id="interview_mode_two"
                                      onchange="getInterviewModeChild(this.value, {{ $job_id }});"
                                      class="form-select">
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


                              
                          <div class="row mt-5">
                                <label class="form-label">Date and Time(Slot-3)</label>
                          <div class="slot-day" style="display: -webkit-inline-box !important;">
                                <div class="">
                                  <input class="form-control" placeholder="Date" name="interviewDateThree" id="interviewDateThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <input class="form-control" placeholder="Time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="">
                                  <select name="interview_mode_three" id="interview_mode_three"
                                      onchange="getInterviewModeChild(this.value, {{ $job_id }});"
                                      class="form-select">
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
                      </form>
                      

            </div>
            <div class="slots-grid job_slots_view">

              <div class="slot-day">
                <div class="slot-header">
                  <div class="slot-day-name">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('D') : 'N/A' }}</div>
                  <div class="slot-date">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d M') : 'N/A' }}</div>
                </div>
                <div class="slot-time">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A') : 'N/A' }}(IST)</div>
                <div class="slot-time">{{ @$application->interview_mode_one ?? 'N/A' }}</div>
                <button {{ ((@$application->interviewDateOne && $date1 >= date('Y-m-d'))) ? $modal1:'' }} data-mode="{{@$application->interview_mode_one}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A') }}" class="{{ ((@$application->interviewDateOne && $date1 >= date('Y-m-d'))) ? $bookbtn1 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book1}}</button>
              </div>

              <div class="slot-day">
                <div class="slot-header">
                  <div class="slot-day-name">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('D') : 'N/A' }}</div>
                  <div class="slot-date">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d M') : 'N/A' }}</div>
                </div>
                <div class="slot-time">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : 'N/A' }}(IST)</div>
                <div class="slot-time"> {{ @$application->interview_mode_two ?? 'N/A' }}</div>
                <button {{ ((@$application->interviewDateTwo && $date2 >= date('Y-m-d'))) ? $modal2:'' }} data-mode="{{@$application->interview_mode_two}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') }}" class="{{ ((@$application->interviewDateTwo && $date2 >= date('Y-m-d'))) ? $bookbtn2 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book2}}</button>
              </div>
              <div class="slot-day">
                <div class="slot-header">
                  <div class="slot-day-name">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('D') : 'N/A' }}</div>
                  <div class="slot-date">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d M ') : 'N/A' }}</div>
                </div>
                <div class="slot-time">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') : 'N/A' }}(IST)</div>
                <div class="slot-time">{{ @$application->interview_mode_three ?? 'N/A' }}</div>
                <button {{ ((@$application->interviewDateThree && $date3 >= date('Y-m-d'))) ? $modal3:'' }} data-mode="{{@$application->interview_mode_three}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') }}" class="{{ ((@$application->interviewDateThree && $date3 >= date('Y-m-d'))) ? $bookbtn3 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book3}}</button>
              </div>

            </div>

          </section>
        </div>

        <aside class="content-sidebar">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-warning-50); color: var(--color-warning-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                  <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
              </div>
              <h3 class="card-title">Compensation Details</h3>
            </div>
            <div class="comp-list">
              @php
                $payFormatViewSalary = GeneralMethod::formatViewSalary($application->ctc_yearly,$symbols,$jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($application->ectc_yearly,$symbols,$jobMode);
                $ctc = '';
                $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                $ectc = '';
                $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
              @endphp

               @if($application->condidate_type=='other')
                <div class="comp-row">

                  @if($application->condidate_type=='other')
                        @php 
                            $current_ctc_mode	=	"";
                            $current_ctc_mode	=	@$jobMode;	
                            
                              
                        @endphp
                        @elseif((@$application->condidate_type=='bench_condidate'))
                        @php 
                            $current_ctc_mode	=	"";
                            $current_ctc_mode	=	@$application->pay_rate_mode;		
                            $ctc =  '*******';
                        @endphp
                    @endif
                  <span class="comp-label">{{ (@$application->condidate_type=='other') ? 'Current Salary' : 'Pay Rate' }}</span>
                  <span class="comp-value">{!! @$current_ctc_cur !!}  {{ $ctc }} {{ $salaryMode[$current_ctc_mode ?? 0] }}  </span>
                </div>
              @endif

              <div class="comp-row">
                            @if($application->condidate_type=='other')
                            @php 
                                $current_ectc_mode	=	"";
                                $current_ectc_mode	=	@$jobMode;		
                            @endphp
                            @else
                            @php 
                                $current_ectc_mode	=	"";
                                $current_ectc_mode	=	@$jobMode;		
                            @endphp
                          @endif
                <span class="comp-label">{{ ($application->condidate_type=='other') ? 'Expected Salary' : 'Bill Rate' }}</span>
                <span class="comp-value">{!! @$current_ectc_cur !!} {{  @$ectc }} {{ $salaryMode[$current_ectc_mode ?? 0] }} </span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Compensation Type</span>
                <span class="comp-badge">{{ $salaryMode[$current_ectc_mode ?? 0] }}</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Salary Type</span>
                <span class="comp-value">Fixed</span>
              </div>
              {{-- <div class="comp-row">
                <span class="comp-label">Currency</span>
                <span class="comp-value">INR</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Paid Service / Free Service</span>
                <span class="comp-value">Paid Service</span>
              </div> --}}
              <div class="comp-row">

                <span class="comp-label">Employment Type</span>
                <span class="comp-value">{{ @$application->employmentType }}</span>
              </div>
              @if($application->condidate_type=='other')
                <div class="comp-row">
                  <span class="comp-label">Offer in Hand</span>
                  <span class="comp-value">{{ ucwords(@$application->oh) }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Hike Percentage</span>
                  <span class="comp-value">{{ @$application->percentage_hike }}%</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label"><b style="color: #0f172a;">Comments : </b> {{ ucwords(@$application->comment) }}</span>
                </div>

              @endif
            </div>
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <h3 class="card-title">Ownership</h3>
            </div>
            <div class="ownership-list">
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  Hiring HR
                </span>
                <span class="own-value">
                  @php 
                      if(@$clientjobAssignListCaptain){
                          echo @$clientjobAssignListCaptain;
                      }else{
                        if(!empty($client_contuser_arr)){
                          
                          foreach($client_contuser_arr as $key=> $cpn){
                            $cpn = (object) $cpn;
                            if($key==0){
                                echo @$cpn->contact_person_name .'';
                            }else{
                                echo ', '.@$cpn->contact_person_name ;
                            }
                            
                          }	
                        }
                      }
                  @endphp
                </span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  Recruiter Name
                </span>
                <span class="own-value">{{ ucwords(@$posted_by) }}</span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                  Company Name 
                </span>
                @php
                $companies = DB::table('companies')->select('companies.company_name')->join('users','users.company_id','companies.id')->where('users.id', $application->submitted_by)->first();
                @endphp
                <span class="own-value">{{ @$companies->company_name ?? '' }}</span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  Upload Date &amp; Time
                </span>
                  @php 
                      $originalDate 	= @$application->created_at;
                      $newDate 		= date("d-M-Y h:i a", strtotime($originalDate)); 
                  @endphp
                <span class="own-value">{{@$newDate.'(IST)'}}</span>
              </div>
            </div>
          </section>


          @if($application->condidate_type=='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Others</h3>
            </div>
             <div class="comp-list">
                <div class="comp-row">
                  <span class="comp-label">Offer Amount</span>
                   @php
                        $offerAmont = "Confidential";
                          $allow =  GeneralMethod::confidentialAmontShow($jobId);  
                        @endphp

                        @if($allow)
                          @if(@$application->offer_amount > 0)
                            <span class="comp-value"> {{@$offer_currency_offer}} {{ @$application->offer_amount }}   {{ @$offer_mode_offer }}</span>
                          @endif
                        @else
                          <span class="comp-value">{{ $offerAmont }}</span>
                        @endif
                </div>
                <div class="comp-row">
                  <span class="comp-label">Joining Date</span>
                  <span class="comp-value">{{ @$application->joined_date ? date("d-m-Y H:i", strtotime(@$application->joined_date)).'(IST)' : '' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Visa Status</span>
                  <span class="comp-value">{{@$application->visastatu}}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Reference Name</span>
                  <span class="comp-value">{{ ucwords(@$application->referencename) }}</span>
                </div>
                 <div class="comp-row">
                  <span class="comp-label"><b style="color: #0f172a;">Reason For Job Change : </b> {{@$application->reason_for_job_change}}</span>
                </div>
             </div>
          </section>
          @endif


          @if($application->condidate_type=='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50); color: var(--color-primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h3 class="card-title">Contact Details</h3>
            </div>
            <div class="contact-list">
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  Email ID-1
                </span>
                <a href="mailto:{{ @$application->email }}" class="contact-value">{{ @$application->email }}</a>
              </div>
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  Email ID-2
                </span>
                <a href="mailto:{{ @$application->secondrary_email }}" class="contact-value">{{ @$application->secondrary_email }}</a>
              </div>
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                  </svg>
                  Contact Number-1 
                </span>
                <a href="javascript:;" class="contact-value" style="display: flex;">
                  <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6" style="display: flex;"> </div>
                  <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$application->phone }}</span>
                </a>
              </div>
            </div>
            <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                  </svg>
                  Contact Number-2
                </span>
                <a href="javascript:;" class="contact-value" style="display: flex;">
                  <div id="secondPhoneDisplay" class="fw-bolder text-gray-800 fs-6" style="display: flex;"> </div>
                  <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$application->secondary_mobile }}</span>
                </a>
            </div>
            <div class="contact-actions">
              <button type="button" class="btn btn-sm btn-whatsapp">
                  <a href="https://api.whatsapp.com/send?phone=91{{@$application->phone}}" target="_blank" class="contact-value" style="display: flex;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                      </path>
                    </svg>
                    Chat on WhatsApp
                  </a>
              </button>
              @php
                $linkedin = @$application->linkedin;

                if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                    $linkedin = 'https://' . $linkedin;
                }
              @endphp
              @if(!empty(@$application->linkedin))
              <button type="button" class="btn btn-sm btn-linkedin">
                <a href="{{ $linkedin ?? 'javascript:;' }}" target="{{ $linkedin ? '_blank' :'_self' }}" class="contact-value" style="display: flex;color: #fff;">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  Connect on LinkedIn
                </a>
              </button>
              @endif
            </div>
          </section>
          @else
                <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
                <div id="secondPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
          @endif

          @if(@$application->condidate_type=='bench_condidate')

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
                $companyDetailsGet = GeneralMethod::companyDetailsGet(@$application->company_id);
                $city = $companyDetailsGet->locations_address[0]['locations_address'][0]['city_name'] ?? '';
                $state = $companyDetailsGet->locations_address[0]['locations_address'][0]['state_name'] ?? '';
                $country = $companyDetailsGet->locations_address[0]['locations_address'][0]['country_name'] ?? '';
		            $accountCompanyType = GeneralMethod::accountCompanyType();
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
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
                       <div class="iti__flag-container" data-code="{{ @$assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex;position: relative;"> </div>
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
          @endif
        </aside>
      </div>
    </div>



    <div class="tab-pane fade" id="timeline" role="tabpanel">
      <div class="content-split">
        <div class="content-main-full">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Timeline</h3>
            </div>
                 

             <!--end::Timeline header-->
        
          <div class="card-body px-0 py-7">
                <div class="timeline">
                  <?php
                    if(!empty($updateTimeLine)){
                      foreach($updateTimeLine as $tdata){
                        $tdata = (object) $tdata;
                        $tdata->update_at =   $tdata->updated_at;
                        $company_or_client = GeneralMethod::companyDetailsByClient($tdata->job_id);
                        $create_at	=	$newDate	=	$newTime	= $prev_tiles_bg	=	$timeline_type = $current_tiles_bg =  "";
                        $create_at	=	$tdata->created_at;
                        $newDate 							=	date("d-M-Y", strtotime($create_at));
                        $newTime 							=	date("H:i A", strtotime($create_at));
                        $posted_by_image					=	$comment							=	"";	
                        if(!empty(@$tdata->image)){
                          $posted_by_image				=	@$tdata->image;
                        }
                        if(!empty(@$tdata->prev_process_name)){
                          $prevCandStatusArr				= 	GeneralMethod::candidateStatusColorCode(@$tdata->prev_process_name);$prev_tiles_bg					=	$prevCandStatusArr['tiles_bg'];
                        }
                        if(!empty(@$tdata->master_process_name)){
                          $currentCandStatusArr			= 	GeneralMethod::candidateStatusColorCode(@$tdata->master_process_name);	
                          $current_tiles_bg				=	$currentCandStatusArr['tiles_bg']; 
                        }
                        $master_process_name				=	@$tdata->master_process_name;
                        $prev_process_name					=	@$tdata->prev_process_name;
                        if($tdata->event_id == 1){
                          $prev_process_name				=	@$tdata->note;	
                          $prev_tiles_bg					=	'sourcing-bg';	
                        }
                        if($tdata->event_id == 2){
                          $comment						=	@$tdata->note;		
                        }
                        if($tdata->event_id == 5 || $tdata->event_id == 6 || $tdata->event_id == 7 || $tdata->event_id == 8){
                          $comment						=	@$tdata->note;	
                        }
                        $offer_status = $management_status = '----';
                        $downloadOffer = '';
                        if($tdata->candidate_status==1){                          
                         
                          $offer_status = 'Offer Released';
                          $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id); 
                          if($allow){
                             $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download download_offer"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                          }else{
                            $offer_job_applications = DB::table('offer_job_applications')->where('id','=',@$application->offer_id)->first();
                            if($offer_job_applications->submitted_by != auth()->user()->id){
                                $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download ConfidentialClass"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                            }else{
                             $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download download_offer"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                            }

                          }

                        }elseif($tdata->candidate_status==2){
                          $offer_status = 'Offer Accepted';
                        }elseif($tdata->candidate_status==3){
                          $offer_status = 'Offer Rejected';
                        }elseif($tdata->candidate_status==4){
                          $offer_status = 'Offer Expired';
                        }elseif($tdata->candidate_status==0){
                          $offer_status = 'Offer Created';
                        }
                        if($tdata->management_status==1){
                          $management_status = 'In Process';
                        }elseif($tdata->management_status==2){
                          $management_status = 'Approved';
                        }elseif($tdata->management_status==3){
                          $management_status = 'Not Approved';
                        }
                        $feedback_status = GeneralMethod::feedback_status();
                        $available_status = GeneralMethod::available_status();
                        $available_status_name = '';
                        if ($tdata->available_status > 0) {
                          $available_status_name = @$available_status[@$tdata->available_status]['name'];
                        } 
                        $feedback = ucwords(substr(@$feedback_status[@$tdata->feedback]['name'], 0, 12));
                        if($CaptainCompanyid == $tdata->event_by_company_id){
                                $rowpostedby = $tdata->name;
                        }else{
                            $rowpostedby = $tdata->name;
                            $userC = DB::table('users')->select('company_id')->where('id',$tdata->event_by_user_id)->first();
                            if($CaptainCompanyid==$userC->company_id){
                                    $rowpostedby = $tdata->name;
                              }else{
                                $spokperson = DB::table('job_assign')->select('users.name','job_assign.company_id')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $tdata->job_id)->where('job_assign.company_id', $tdata->event_by_company_id)->first();
                                if($spokperson){
                                    if(!empty($spokperson->name)){
                                      if($CaptainCompanyid !=$spokperson->company_id){
                                        $rowpostedby = (@$spokperson->name) ? @$spokperson->name : $tdata->name;
                                      }else{
                                        $rowpostedby = $tdata->name;
                                      }
                                  }
                                }else{
                                  $rowpostedby = $tdata->name;
                                }
                              }
                        }
                    
                        $rowpostedby = ($rowpostedby) ? $rowpostedby : $tdata->name;
                        $bgTextDecore				=	GeneralMethod::icoinText($rowpostedby);				
        
                        if(($tdata->event_id == 1) || ($tdata->event_id == 2))
                        {		
                        ?>
                              <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                  <div class="symbol-label bg-light"> 
                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                          <polygon points="0 0 24 0 24 24 0 24"/>
                                          <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                                          <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                                          <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                                          <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                                        </g>
                                      </svg>
                                    </span> 
                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                          <rect x="0" y="0" width="24" height="24"/>
                                          <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                      </svg>
                                    </span>
                                  </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                  <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                    <tbody class="text-gray-600 fw-bold">
                                      <tr>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a>
                                            <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$prev_tiles_bg }} px-2 py-1">{{ ucwords(@$prev_process_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->prev_process_child_name) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6"> To </span> 
                                          </div>
                                        </td>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->child_process_name) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-120px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>
                                          </div>
                                        </td>
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                                 <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                                  <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                                </div>
                                              </div>
                                            </div>
                                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  @if(!empty($comment))
                                    <div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <div class="mb-1">
                                          <p class="<?php //echo @$current_tiles_bg; ?>">
                                            <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                            {{ @$comment }}
                                          </p>
                                        </div>
                                      </div>
                                    </div>
                                  @endif
                                </div>
                              </div>
                        <?php
                        }else if($tdata->event_id==3){
                            $interview_schedule_date	=	$newDate	=	$newTime =	$interview_mode	=	$interview_mode_child_level	= $interviewers = $panellistArr = 	"";
                            $interview_schedule_date	=	$tdata->interview_schedule_date;
                            $interview_to_time	=	$tdata->interview_to_time;
                            $newDate 					=	date("d-M-Y", strtotime($interview_schedule_date));
                            $newTime 					=	date("h:i A", strtotime(@$interview_to_time));
                            if($tdata->interview_mode > 0){
                              $interview_mode_Arr		= 	GeneralMethod::interview_mode();
                              $interview_mode			= 	$interview_mode_Arr[@$tdata->interview_mode];				
                            }
                            $interview_mode_child_level	=	$tdata->interview_mode_child_level;	
                            if($tdata->interview_mode==3){
                              $interview_mode_child_level	=	@$tdata->interview_mention_link;	
                            }
                            $interviewers				=	$tdata->interviewers;
                            if(!empty($interviewers)){
                              $panellistArr 			=	explode(',', $interviewers);	
                            }	
                            $average_star_rating	=	'';
                            $average_star_rating	=	$tdata->average_star_rating;	
                            $imagePath				=	$text = 	'';
                            if($average_star_rating == 1){
                              $imagePath	=	url('public/img/star/1.png');
                              $text		=	'Bad';
                            }else if($average_star_rating == 2){
                              $imagePath	=	url('public/img/star/2.png');
                              $text		=	'Poor';
                            }else if($average_star_rating == 3){
                              $imagePath	=	url('public/img/star/3.png');
                              $text		=	'Average';
                            }else if($average_star_rating == 4){
                              $imagePath	=	url('public/img/star/4.png');
                              $text		=	'Best';
                            }else if($average_star_rating == 5){
                              $imagePath	=	url('public/img/star/5.png');	
                              $text		=	'Very Best';
                            }else{
                              $imagePath	=	url('public/img/star/5-blank.png');
                              $text		=	'Blank';
                            }
                        ?>
                            <div class="timeline-item">
                              <!--begin::Timeline line-->
                              <div class="timeline-line w-40px"></div>
                              <!--end::Timeline line-->
                              <!--begin::Timeline icon-->
                              <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <div class="symbol-label bg-light">
                                  <!--begin::Svg Icon-->
                                  <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                      </g>
                                    </svg>
                                  </span>
                                  <!--end::Svg Icon-->
                                </div>
                              </div>
                              <!--end::Timeline icon-->
                              <!--begin::Timeline content-->
                              <div class="timeline-content mb-10 mt-n1">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                  <!--begin::Table body-->
                                  <tbody class="text-gray-600 fw-bold">
                                    <!--begin::Table row-->
                                    <tr>
                                      <!--begin::Requisitions=-->
                                      <td class="min-w-150px">
                                        <div class="d-flex flex-column">
                                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"> {{ ucwords(@$tdata->title)}} </a>
                                          <span class="fw-bold text-muted d-block fs-7"> {{ ucwords(@$company_or_client) }}  </span>
                                        </div>
                                        <!--end::Requisitions=-->
                                        <!--begin::Interview Mode=-->
                                        <td class="min-w-100px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ @$interview_mode }}</span>
                                            <span class="fw-bold text-muted d-block fs-7">{{ @$interview_mode_child_level }}</span> 
                                          </div>
                                        </td>
                                        <!--end::Interview Mode=-->
                                        <!--begin::Interviewers=-->
                                        <td class="min-w-90px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover">
                                               @php
                                                  if(!empty($panellistArr)){
                                                    foreach($panellistArr as $pval)
                                                    {
                                                      $bgTextDecore = $strVal = $team_full_name	=	 '';
                                                      $str 			= $team_full_name =  trim(@$pval);
                                                      $str 			= $strVal = strtolower($str[0]);
                                                      $str = ord($str);
                                                      if ($str >= 97 && $str <= 101) {
                                                        $bgTextDecore = 'fs-8 bg-light-danger text-danger';
                                                      } 
                                                      else if ($str >= 102 && $str <= 106) {
                                                        $bgTextDecore = 'fs-8 bg-light-warning text-warning';
                                                      } 
                                                      else if ($str >= 107 && $str <= 111) {
                                                        $bgTextDecore = 'fs-8 bg-light-primary text-primary';
                                                      } 
                                                      else if ($str >= 112 && $str <= 116) {
                                                        $bgTextDecore = 'fs-8 bg-light-success text-success';
                                                      } 
                                                      else if ($str >= 117 && $str <= 121) {
                                                        $bgTextDecore = 'fs-8 bg-dark text-secondary';
                                                      } else {
                                                        $bgTextDecore = 'fs-8 bg-light-secondary text-dark';
                                                      }
                                                      echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="'.ucwords(@$team_full_name).'">
                                                        <div class="symbol-label '.@$bgTextDecore.'">'.strtoupper(@$strVal).'</div>
                                                        </div>';
                                                    }
                                                  }
                                                @endphp
                                            </div>Interviewer
                                          </div>
                                        </td>
                                        <!--end::Interviewers=-->
                                        <!--begin::Interviewer Feedback=-->
                                        <td class="min-w-70px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder  fs-6">{{ ($feedback) ? $feedback : '----' }}</span> <span class="fw-bold text-muted d-block fs-7">{{ ($available_status_name ? $available_status_name : '----') }}</span> 
                                          </div>
                                        </td>
                                        <!--end::Interviewer Feedback-->
                                        <!--begin::Date-->
                                        <td class="min-w-140px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6"> {{ @$newDate}} </span>
                                            <span class="text-muted fw-bold d-block fs-7"> {{ @$newTime }} {{ (@$tdata->timezone_abbreviation) ? '('.@$tdata->timezone_abbreviation.')' :'' }} 
                                            </span>
                                          </div>
                                        </td>
                                        <!--end::Date-->
                                        <!--begin::Image-->
                                        <td class="min-w-90px">
                                          <img src="{{  str_replace('index.php','',@$imagePath) }}" alt="image" style="width: 5.5rem"> <span class="fw-bold text-muted d-block fs-7">{{ @$text }}</span> 
                                        </td>
                                        <!--end::Image-->
                                        <!--begin::Soruce-->
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name)}}">
                                                <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}">
                                                  <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                                </div>
                                              </div>
                                            </div>
                                            <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ ucwords(@$tdata->cur_companyname) }}" style="text-align: center;">{{ substr(ucwords(@$tdata->cur_companyname), 0, 20) }}</span>
                                          </div>
                                        </td>
                                        <!--end::Soruce-->
                                        <!--begin::Status-->
                                        <td class="min-w-120px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$tdata->css_style }} px-2 py-1">{{ ucwords(@$tdata->status_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->process_child_name) }}</span>
                                          </div>
                                        </td>
                                        <!--end::Stage-->
                                    </tr>
                                      <!--end::Table row-->
                                  </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                          @php 
                          //interview_feedback  application_id    interview_schedules_id    
                                $interview_feedback 	= DB::table('interview_feedback')
                                ->select('interview_feedback.created_at', 'interview_feedback.message', 'interview_feedback.star_ratings','users.name', 'users.image')
                                ->join('users', 'users.id', '=', 'interview_feedback.update_by')
                                ->where('interview_feedback.application_id', @$tdata->application_id)
                                ->where('interview_feedback.interview_id', @$tdata->interview_schedules_id)
                                ->get(); 
                                $feedbackdataView ='';
                                $feedbackdata = DB::table('interview_skills_feedback')->where('interview_id',$tdata->interview_schedules_id)->orderByDesc('feedback_id')->count();
                                if($feedbackdata>0){
                                    $feedbackdataView = '<div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                          &nbsp;Interview Feedback 
                                          </span>  <span class="badge skill-feedback fw-bolder fs-6 interview px-2 py-1" data-row-id="' .$tdata->interview_schedules_id .'" style="cursor: pointer; margin-left: 4px; margin-top: 5px;">View</span>
                                      </div>
                                  </div>';
                                }else{
                                  $feedbackdataView = '<div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                          &nbsp;Interview Feedback Pending
                                          </span> 
                                      </div>
                                  </div>';
                                }
                        @endphp
                                {!! $feedbackdataView !!}
                        @php
                                if(count($interview_feedback) > 0){
                                  foreach($interview_feedback as $intfeed){
                                    $newDate 		=	date("d-m-Y", strtotime($intfeed->created_at));
                                    $newTime 		=	date("H:i A", strtotime($intfeed->created_at));
                                    $bgTextDecore				=	GeneralMethod::icoinText(@$intfeed->name);
                                    $imagePath	=	'';
                                    if(!empty($intfeed->image)){
                                      $imagePath	=	@$intfeed->image;
                                    }   
                                    $feedback_star_imagePath	=	$feedback_star_text =  '';
                                    $feedbackstar_ratings				=	$intfeed->star_ratings;
                                    if($feedbackstar_ratings == 1){
                                      $feedback_star_imagePath	=	url('public/img/star/1.png');
                                      $feedback_star_text			=	'Bad';
                                    }else if($feedbackstar_ratings == 2){
                                      $feedback_star_imagePath	=	url('public/img/star/2.png');
                                      $feedback_star_text			=	'Poor';
                                    }else if($feedbackstar_ratings == 3){
                                      $feedback_star_imagePath	=	url('public/img/star/3.png');
                                      $feedback_star_text			=	'Average';
                                    }else if($feedbackstar_ratings == 4){
                                      $feedback_star_imagePath	=	url('public/img/star/4.png');
                                      $feedback_star_text			=	'Best';
                                    }else if($feedbackstar_ratings == 5){
                                      $feedback_star_imagePath	=	url('public/img/star/5.png');	
                                      $feedback_star_text			=	'Very Best';
                                    }else{
                                      $feedback_star_imagePath	=	url('public/img/star/5-blank.png');
                                      $feedback_star_text			=	'Blank';
                                    }
                                  }
                                }
                        @endphp
                        </div>
                          <!--end::Timeline content-->
                        </div>
                        <?php
                        }else if($tdata->event_id == 4){
                          // Offer Section 
                          $currency 				=	GeneralMethod::currency();
                          $salaryMode 			=	GeneralMethod::salaryModeOffer();
                          $offer_currency			=	'';
                          if($tdata->offer_currency >= 0){
                            $offer_currency			= $currency[(int)@$tdata->offer_currency];
                          }
                          $offer_mode			=	'';
                          if($tdata->offer_mode >= 0){
                             $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                          }
                          $joined_time = '';
                          if(!empty($tdata->joined_date)){
                            $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                          }
                        ?>
                        <div class="timeline-item">
                          <!--begin::Timeline line-->
                          <div class="timeline-line w-40px"></div>
                          <!--end::Timeline line-->
                          <!--begin::Timeline icon-->
                          <div class="timeline-icon symbol symbol-circle symbol-40px">
                            <div class="symbol-label bg-light">
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                  </g>
                                </svg>
                              </span>
                              <!--end::Svg Icon-->
                            </div>
                          </div>
                          <!--end::Timeline icon-->
                          <!--begin::Timeline content-->
                          <div class="timeline-content mb-10 mt-n1">
                            <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                              <thead>
                                <tr class="fw-bolder text-muted" role="row">
                                  <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Created</th>
                                  <th class="sorting_disabled">Offer Amount</th>
                                  <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                                  <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                                  <th class="sorting_disabled" style="width: 79px;">Create Date</th>
                                  <th class="sorting_disabled" style="width: 79px;">Stage</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr role="row" class="odd">
                                  <td class="min-w-150px">
                                      <div class="d-flex flex-column"> 
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a> 
                                        <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span> 
                                      </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                       @php
                                        $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                     <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->yearly_final_gross) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif

                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-150px">
                                    <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->created_at) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->created_at)) }}(IST)</span>
                                    </div>
                                  </td>
                                  <td>
                                      <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                  </td>
                                  <td class="min-w-50px">
                                    <div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover justify-content-center">
                                        <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                          <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                            <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                          </div>
                                        </div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                <?php 
                  }else if($tdata->event_id == 10){
                    $currency 				=	GeneralMethod::currency();
                    $salaryMode 			=	GeneralMethod::salaryModeOffer();
                    $offer_currency			=	'';
                    if($tdata->offer_currency >= 0){
                      $offer_currency			= $currency[(int)@$tdata->offer_currency];
                    }
                    $offer_mode			=	'';
                    if($tdata->offer_mode >= 0){
                      $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                    }
            
                  $joined_time = '';
                  if(!empty($tdata->joined_date)){
                    $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                  }
                ?>
                    <div class="timeline-item">
                      <!--begin::Timeline line-->
                      <div class="timeline-line w-40px"></div>
                      <!--end::Timeline line-->
                      <!--begin::Timeline icon-->
                      <div class="timeline-icon symbol symbol-circle symbol-40px">
                        <div class="symbol-label bg-light">
                          <!--begin::Svg Icon-->
                          <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                              </g>
                            </svg>
                          </span>
                          <!--end::Svg Icon-->
                        </div>
                      </div>
                      <!--end::Timeline icon-->
                      <!--begin::Timeline content-->
                      <div class="timeline-content mb-10 mt-n1">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                          <thead>
                            <tr class="fw-bolder text-muted" role="row">
                              <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Expired</th>
                              <th class="sorting_disabled">Offer Amount</th>
                              <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                              <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                              <th class="sorting_disabled" style="width: 79px;">Expired Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr role="row" class="odd">
                              <td class="min-w-150px">
                                <div class="d-flex flex-column">
                                  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a>
                                  <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                    @php
                                      $offerAmont = "Confidential";
                                      $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                    @endphp
                                    @if($allow)
                                    <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                    @else
                                    <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                    @endif
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                  <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                  <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                </div>
                              </td>
                              <td class="min-w-150px">
                                <div class="d-flex flex-column">
                                  <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==4) ? 'rejected' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                  <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->created_at) }}</span>
                                  <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->created_at)) }}(IST)</span>
                                </div>
                              </td>
                              <td class="min-w-50px"><div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover justify-content-center">
                                      <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                        <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                          <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                        </div>
                                      </div>
                                    </div>
                                    <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                  </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                <?php 
                }else if($tdata->event_id == 5){
                    $currency 				=	GeneralMethod::currency();
                    $salaryMode 			=	GeneralMethod::salaryModeOffer();
                    $offer_currency			=	'';
                    if($tdata->offer_currency >= 0){
                      $offer_currency			= $currency[(int)@$tdata->offer_currency];
                    }
                    $offer_mode			=	'';
                    if($tdata->offer_mode >= 0){
                      $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                    }
                    $joined_time = '';
                    if(!empty($tdata->joined_date)){
                      $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                    }
                ?>
                    <div class="timeline-item">
                      <div class="timeline-line w-40px"></div>
                      <div class="timeline-icon symbol symbol-circle symbol-40px">
                        <div class="symbol-label bg-light"> 
                          <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" iewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1" />
                                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1" />
                                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span>
                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> 
                        </div>
                      </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                     <div class="d-flex flex-column">

                                      @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->send_accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>

                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
                <?php 
                   }else if($tdata->event_id == 6){ 
                ?>
                      <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1" />
                                  <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> 
                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> 
                          </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->send_approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
           
            <?php }else if($tdata->event_id == 7){
              
                $currency 				=	GeneralMethod::currency();
                $salaryMode 			=	GeneralMethod::salaryModeOffer();
              
                $offer_currency			=	'';
                if($tdata->offer_currency >= 0){
                  $offer_currency			= $currency[(int)@$tdata->offer_currency];
                }
                $offer_mode			=	'';
                if($tdata->offer_mode >= 0){
                  $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                }
                $joined_time = '';
                if(!empty($tdata->joined_date)){
                   $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                 }

              ?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                   <div class="d-flex flex-column">
                                    @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif
                                      
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
         
            <?php }elseif($tdata->event_id == 45){ ?>

               <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column"> 
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Whatsapp</a> 
                          <span class="fw-bold text-muted d-block fs-7">Send</span> 
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column"> 
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a> 
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span> 
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column"> 
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>    
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
							
							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>     

           <?php }else if($tdata->event_id == 8){?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
            <?php }else if($tdata->event_id == 9){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column"> 
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Mail</a> 
                          <span class="fw-bold text-muted d-block fs-7">Send</span> 
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column"> 
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a> 
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span> 
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column"> 
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>    
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
							
							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>      

            <?php }else if($tdata->event_id == 11){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column"> 
                          <a href="javascript:void(0);" class="text-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fw-bolder text-hover-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fs-6">{{@$tdata->note}}</a> 
                          <span class="fw-bold text-muted d-block fs-7">Mail</span> 
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column"> 
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a> 
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span> 
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column"> 
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>    
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
							
							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>      
            <?php } ?>

       <?php }
      } ?>

            </div>
          </div>
            
            

         

            <p class="summary-text summary-text-width"></p>
          </section>
        </div>
      </div>
    </div>


    <div class="confidentiality-banner" role="note">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <p>All candidate information is confidential and intended for internal use only.</p>
    </div>
  </main>
</div>


<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

  $('.page-tabs .tab').on('click', function () {
        $('.page-tabs .tab').removeClass('tab-active').attr('aria-selected', 'false');

        $(this).addClass('tab-active').attr('aria-selected', 'true');
    });


function scrollNotesToBottom() {

    const notesBox = document.getElementById("applicant-notes");
    if (notesBox) {
        notesBox.scrollTo({
            top: notesBox.scrollHeight,
            behavior: "smooth"
        });
    }
}
scrollNotesToBottom();

setTimeout(() => {
          
          var defaultTimeFormat = '{{$defaultTimeFormat}}';
         
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


   $( document ).ready(function() {

     
      const firstSavedCountryCode  = @json($application->phone_code ?? 'in');
      const secondSavedCountryCode = @json($application->second_phone_code ?? 'in');
      const firstPhoneDisplayDiv = document.getElementById("firstPhoneDisplay");
      const secondPhoneDisplayDiv = document.getElementById("secondPhoneDisplay");

        const flagDiv = document.createElement("div");
        flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;

        const secondflagDiv = document.createElement("div");
        secondflagDiv.className = `iti__flag iti__${secondSavedCountryCode}`;
        // Get the dial code for the selected country code
        const dialCode = countryDialCodes[firstSavedCountryCode] || ""; // Default to empty string if not found
        const seconddialCode = countryDialCodes[secondSavedCountryCode] || ""; // Default to empty string if not found

        // Create a span to display the dial code
        const dialCodeSpan = document.createElement("span");
        dialCodeSpan.textContent = dialCode;

        const seconddialCodeSpan = document.createElement("span");
        seconddialCodeSpan.textContent = seconddialCode;

        // Append the flag and the dial code to the display div
        firstPhoneDisplayDiv.appendChild(flagDiv);
        firstPhoneDisplayDiv.appendChild(dialCodeSpan);

        // Append the flag and the dial code to the display div
        secondPhoneDisplayDiv.appendChild(secondflagDiv);
        secondPhoneDisplayDiv.appendChild(seconddialCodeSpan);



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


   
   		$('body').on('click', '#cancel-note', function(){
   			$('#note_text').val(''); 
   		});
   
   		$('#add-note').click(function () {
   				var url 	= "{{ route('service_company.applicant-note.store') }}";
   				var id 		= {{$application->id}};
          var wiseId 		= {{$application->wiseId}};
   				var note 	= $('#note_text').val();
   				var token 	= '{{ csrf_token() }}';
   				$.ajax({ 
   								type	: 'POST',
   								url		: url,
   								data	: {'_token':token, 'id':id, 'note': note,'wiseId':wiseId},
   								success	: function (response) {
   																if(response.status == 'success') {
   																	$('#applicant-notes').html(response.view);
                                    scrollNotesToBottom();
   																	$('#note_text').val('');
   																}
   								}
   				});
   		});
   
   		$('body').on('click', '.delete-note', function(){
   				let noteId = $(this).data('note-id');
   				
   								
   const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      
   						var url 		= "{{ route('service_company.applicant-note.destroy', ':id') }}";
   						url 			= url.replace(':id', noteId);
   						var token 		= '{{ csrf_token() }}';
   		
   						$.ajax({
   							type: 'POST',
   							url: url,
   							data: {'_token':token, '_method': 'DELETE'},
   							success: function (response) {
   								if(response.status == 'success') {
   									$('#applicant-notes').html(response.view);
   									
   									swalWithBootstrapButtons.fire(
   										'Deleted!',
   										'Your text note has been deleted.',
   										'success'
   									)
   	
   								}
   							}
   						});
   						
   	
   	
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
		   swalWithBootstrapButtons.fire(
			 'Cancelled',
			 'Your text note is safe :)',
			 'error'
		   )
     }
   })
   
   
});	
   		
   		$('body').on('click', '.edit-note', function() {
   	
         														$(this).hide();
         														let noteId = $(this).data('note-id');
         														$('body').find('#note-'+noteId+' .note-text').hide();
   
         														let noteText = $('body').find('#note-'+noteId+' .note-text').html();
         let textArea = '<textarea id="edit-note-text-'+noteId+'" class="form-control" row="4">'+noteText+'</textarea><a class="update-note btn btn-sm btn-light-success py-1 mt-2" data-note-id="'+noteId+'" href="javascript:void(0);">@lang("app.save")</a>';
         $('body').find('#note-'+noteId+' .note-textarea').html(textArea);
     
   });
   
   
   $('body').on('click', '.update-note', function () {
   	
         let noteId 	= $(this).data('note-id');
         var url 		= "{{ route('service_company.applicant-note.update', ':id') }}";
         url 			= url.replace(':id', noteId);
         var note 		= $('#edit-note-text-'+noteId).val();
         var token 		= '{{ csrf_token() }}';   
         $.ajax({
						  type		: 'POST',
						  url		: url,
						  data		: {'_token':token, 'noteId':noteId, 'note': note, '_method': 'PUT'},
						  success	: function (response) {
							  if(response.status == 'success') {
								  $('#applicant-notes').html(response.view);
							  }
						  }
         		});
     
   
   });


   
   $('body').on('click', '.resume_delete', function(){
   
   	
   			var application_id  		=	$(this).attr('rel');
     		var token       			=	'{{ csrf_token() }}';
   				 				
   				
		   const swalWithBootstrapButtons = Swal.mixin({
			 customClass: {
			   confirmButton: 'btn btn-success',
			   cancelButton: 'btn btn-danger'
			 },
			 buttonsStyling: false
		   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      						var url     			=	"{{ route('service_company.job-applications.resume_delete') }}";
                     		var token     			=	"{{ csrf_token() }}";
                     		$.ajax({
   									  type  		: 'POST',
   									  url   		: url,
   									  data  		: {'_token': token, '_method': 'POST', 'application_id':application_id },
   									  success 		: function (response) {
   										  if (response.status == "success") {
   											
   											swalWithBootstrapButtons.fire(
   																			'Deleted!',
   																			'Your resume has been deleted.',
   																			'success'
   																		)
   									
   									
   										  } 
               						 }
             					});
   							
     } else if (
       				result.dismiss === Swal.DismissReason.cancel 
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Your text note is safe :)',
         'error'
       )
     }
   })

  });
   $('.sendwhatsappmodal').click(function(){
          // Trigger hidden toggle — KTDrawer binds to this on createInstances()
          $('#kt_send_whatsapp_sd_toggle').trigger('click');

          $('#application_send_whatsapp_body_content').html(
              '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
              '<span class="spinner-border spinner-border-sm text-success me-2"></span> Loading...</div>'
          );
          var id        	=	{{ @$jobId }};
          var sid        	=	{{ @$application->id }};
          var arr 		=	[sid];
          var jobIdArr = [id];
          $('.chk_list_item:checked').each(function () {
              
              var jobId = $(this).attr('data-jobId');
              arr.push($(this).val());
              jobIdArr.push(jobId);
          });
          var id        	=	jobIdArr[0];
          var url       	=	"{{ route('service_company.cvtracker.send_whatsapp',':id') }}";
          url         	=	url.replace(':id', id);
          var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
          var token       =	"{{ csrf_token() }}";
          $.ajax({
                  type		: 'POST',
                  url			: url,
                  data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
                  cache		: false,
                  success 	: function (response) {
                                      if (response.status == "success") {
                                          $('#application_send_whatsapp_body_content').html(response.view);
                                          
                                          $('#kt_activities_scroll').removeAttr('style');
                                      }
                          }
          });

   });
  
  $('.sendmaimodal').click(function(){

        // Trigger hidden toggle — KTDrawer binds to this on createInstances()
        $('#kt_send_mail_sd_toggle').trigger('click');

        $('#application_send_mail_body_content').html(
            '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
            '<span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading...</div>'
        );
        $('#application_mass_mail_body_content').html('');
        $('#application_fwd_resume_body_content').html('');
        var id        	=	{{ @$jobId }};
        var sid        	=	{{ @$application->id }};
        var arr 		=	[sid];
        var jobIdArr = [id];
        $('.chk_list_item:checked').each(function () {
            
            var jobId = $(this).attr('data-jobId');
            arr.push($(this).val());
            jobIdArr.push(jobId);
        });

        console.log(jobIdArr);

        var id        	=	jobIdArr[0];
        var url       	=	"{{ route('service_company.cvtracker.send_mail',':id') }}";
        url         	=	url.replace(':id', id);
        var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
        var token       =	"{{ csrf_token() }}";

      
          $.ajax({
                type		: 'POST',
                url			: url,
                data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
                cache		: false,
                success 	: function (response) {
                                    if (response.status == "success") {
                                        $('#application_send_mail_body_content').html(response.view);
                                        
                                        $('#kt_activities_scroll').removeAttr('style');
                                    }
                        }
          });
              
  });


   $(document).on('click', '.editSkill', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSkillUpdate').removeClass('d-none');
       $('.editSkillCancel').removeClass('d-none');
       $('.editSkill').addClass('d-none');
       $('.job_skill_edit').removeClass('d-none');
       $('.job_skill_view').addClass('d-none');

    });
    $(document).on('click', '.editSkillCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSkillUpdate').addClass('d-none');
       $('.editSkillCancel').addClass('d-none');
       $('.editSkill').removeClass('d-none');
       $('.job_skill_edit').addClass('d-none');
       $('.job_skill_view').removeClass('d-none');

    });



    $(document).on('click', '#skill-update-form', function(e) { 
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#skillUpdateForm')[0]);	
          $("#skill-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.skillExperienceUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#skill-update-form").html('Save');
                    $('.editSkillUpdate').addClass('d-none');
                    $('.editSkillCancel').addClass('d-none');
                    $('.job_skill_edit').addClass('d-none');
                    $('.job_skill_view').removeClass('d-none');
                    $('.editSkill').removeClass('d-none');
                          Swal.fire("Success", 'Candidate Skill Experience updated successfully !',"success");
                          $('.job_skill_view').html(response.html)
                        },
                error			: function (response) {
                  $("#skill-update-form").html('Save');
                          Swal.fire("Warning", 'Something is the issue.',"warning");
                        }
            });

    });


       $(document).on('click', '.editJobQuestion', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editJobQuestionUpdate').removeClass('d-none');
       $('.editJobQuestionCancel').removeClass('d-none');
       $('.editJobQuestion').addClass('d-none');
       $('.job_additional_edit').removeClass('d-none');
       $('.job_additional_view').addClass('d-none');

    });
    $(document).on('click', '.editJobQuestionCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editJobQuestionUpdate').addClass('d-none');
       $('.editJobQuestionCancel').addClass('d-none');
       $('.editJobQuestion').removeClass('d-none');
       $('.job_additional_edit').addClass('d-none');
       $('.job_additional_view').removeClass('d-none');

    });

    $(document).on('click', '.editSlots', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSlotsUpdate').removeClass('d-none');
       $('.editSlotsCancel').removeClass('d-none');
       $('.editSlots').addClass('d-none');
       $('.job_slots_edit').removeClass('d-none');
       $('.job_slots_view').addClass('d-none');

    });
    $(document).on('click', '.editSlotsCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSlotsUpdate').addClass('d-none');
       $('.editSlotsCancel').addClass('d-none');
       $('.editSlots').removeClass('d-none');
       $('.job_slots_edit').addClass('d-none');
       $('.job_slots_view').removeClass('d-none');

    });



    $(document).on('click', '#job-question-update-form', function(e) { 
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#jobQuestionUpdateForm')[0]);	
          $("#job-question-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.JobQuestionUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#job-question-update-form").html('Save');
                    $('.editJobQuestionUpdate').addClass('d-none');
                    $('.editJobQuestionCancel').addClass('d-none');
                    $('.job_additional_edit').addClass('d-none');
                    $('.job_additional_view').removeClass('d-none');
                    $('.editJobQuestion').removeClass('d-none');
                          Swal.fire("Success", 'Candidate Job Question updated successfully !',"success");
                          $('.job_additional_view').html(response.html)
                        },
                error			: function (response) {
                  $("#job-question-update-form").html('Save');
                          Swal.fire("Warning", 'Something is the issue.',"warning");
                        }
            });

    });


    $(document).on('click', '#job-slots-update-form', function(e) { 
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#jobSlotsUpdateForm')[0]);	
          $("#job-slots-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.SlotsUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#job-slots-update-form").html('Save');
                    $('.editSlotsUpdate').addClass('d-none');
                    $('.editSlotsCancel').addClass('d-none');
                    $('.job_slots_edit').addClass('d-none');
                    $('.job_slots_view').removeClass('d-none');
                    $('.editSlots').removeClass('d-none');
                          Swal.fire("Success", 'Candidate Interview Slots updated successfully !',"success");
                          $('.job_slots_view').html(response.html)
                        },
                error			: function (response) {
                  $("#job-slots-update-form").html('Save');
                          Swal.fire("Warning", 'Something is the issue.',"warning");
                        }
            });

    });

    



});



    
</script> 
<!--begin::Send Mail - Drawer (hidden toggle pattern — works in AJAX-loaded content) -->
<div id="kt_send_mail_sd_toggle" style="display:none;"></div>
<div id="kt_send_mail_showdetail_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="send-mail-showdetail"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
    data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_send_mail_sd_toggle" data-kt-drawer-close="#kt_send_mail_showdetail_close">
    <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
        <div class="card-header px-5 py-0 flex-shrink-0">
            <h3 class="card-title fw-bolder text-dark mb-0">Send Email</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="kt_send_mail_showdetail_close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_send_mail_body_content"></div>
    </div>
</div>
<!--end::Send Mail - Drawer-->
<script>
(function() {
    if (typeof KTDrawer !== 'undefined') {
        KTDrawer.createInstances();
    }

    // Manual overlay for the send-mail drawer (avoids conflicts with parent drawer overlay)
    var $overlay = null;

    function showSendMailOverlay() {
        if ($overlay) return;
        $overlay = $('<div class="sd-mail-overlay"></div>').css({
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            zIndex: 109, cursor: 'pointer'
        }).appendTo('body');
        $overlay.on('click', hideSendMailDrawer);
    }

    function hideSendMailDrawer() {
        if ($overlay) { $overlay.remove(); $overlay = null; }
        var drawerEl = document.getElementById('kt_send_mail_showdetail_drawer');
        if (drawerEl && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) { drawer.hide(); }
        }
    }

    $(document).on('click', '.sendmaimodal', function() {
        showSendMailOverlay();
    });

    $(document).on('click', '#kt_send_mail_showdetail_close', function() {
        hideSendMailDrawer();
    });
})();
</script>

<!--begin::Send Whatsapp - Drawer (hidden toggle pattern) -->
<div id="kt_send_whatsapp_sd_toggle" style="display:none;"></div>
<div id="kt_send_whatsapp_showdetail_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="send-whatsapp-showdetail"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
    data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_send_whatsapp_sd_toggle" data-kt-drawer-close="#kt_send_whatsapp_showdetail_close">
    <div class="shadow-none rounded-0 w-100 h-100" style="display:flex;flex-direction:column;overflow:hidden;">
        <div style="flex:1;overflow:hidden;" id="application_send_whatsapp_body_content"></div>
    </div>
</div>
<!--end::Send Whatsapp - Drawer -->

<script>
(function() {
    if (typeof KTDrawer !== 'undefined') { KTDrawer.createInstances(); }
    var $waOverlay = null;

    function showWhatsappOverlay() {
        if ($waOverlay) return;
        $waOverlay = $('<div class="sd-whatsapp-overlay"></div>').css({
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            zIndex: 109, cursor: 'pointer'
        }).appendTo('body');
        $waOverlay.on('click', hideWhatsappDrawer);
    }

    function hideWhatsappDrawer() {
        if ($waOverlay) { $waOverlay.remove(); $waOverlay = null; }
        var drawerEl = document.getElementById('kt_send_whatsapp_showdetail_drawer');
        if (drawerEl && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) { drawer.hide(); }
        }
    }

    $(document).on('click', '.sendwhatsappmodal', function() {
        showWhatsappOverlay();
    });

    $(document).on('click', '#kt_send_whatsapp_showdetail_close', function() {
        hideWhatsappDrawer();
    });
})();
</script>
