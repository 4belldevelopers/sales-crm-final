@php 
   use App\Helper\GeneralMethod;
       GeneralMethod::benchAutoPause(auth()->user()->company_id);
       $profilesCompletion = GeneralMethod::profilesCompletion();	
       $company_id = auth()->user()->company_id;
      //  print_r($profilesCompletion->clientsprofiles->tags);
      $popup_tags = 1;//($profilesCompletion->clientsprofiles) ? $profilesCompletion->clientsprofiles->tags : '';
      $popup_address = ($profilesCompletion->clientsprofiles) ? $profilesCompletion->clientsprofiles->address : '';
      $popup_company_name = ($profilesCompletion->profilesCompletion) ? $profilesCompletion->profilesCompletion->company_name : '';
      $popup_industries = ($profilesCompletion->profilesCompletion) ? $profilesCompletion->profilesCompletion->industries : '';
      $popup_description = ($profilesCompletion->profilesCompletion) ? $profilesCompletion->profilesCompletion->description : '';

      $popup_company_type = ($profilesCompletion->profilesCompletion) ? $profilesCompletion->profilesCompletion->company_type : '';


      $total = 5;

      $popup_percentage_tags = ($popup_tags) ? 1 : 0;
      $popup_percentage_address = ($popup_address) ? 1 : 0;
      $popup_percentage_company_name = ($popup_company_name) ? 1 : 0;
      $popup_percentage_industries = ($popup_industries) ? 1 : 0;
      $popup_percentage_description = ($popup_description) ? 1 : 0;

      $sub_totale = $popup_percentage_tags+$popup_percentage_address+$popup_percentage_company_name+$popup_percentage_industries+$popup_percentage_description;
      $profile_completion_percentage = ($sub_totale / $total) * 100;

      $profile_completion_percentage = 100-$profile_completion_percentage;

      $bordercolor = "#ff6b6b";
      $textcolor = "#ff6b6b";
      if($profile_completion_percentage <= 20){
        $bordercolor = "#ff6b6b";
        $textcolor = "#ff6b6b";
      }elseif($profile_completion_percentage <= 50 && $profile_completion_percentage >=20){
        $bordercolor = "#ff6b6b";
        $textcolor = "#ff6b6b";
      }elseif($profile_completion_percentage <=80 && $profile_completion_percentage >=50){
        $bordercolor = "#ff6b6b";
        $textcolor = "#ff6b6b";
      }elseif($profile_completion_percentage >= 80){
        $bordercolor = "#32e911";
        $textcolor = "#32e911";

      }

      

      $currentSlug = request()->segment(2);
      if($currentSlug==='apptracker'){
         $currentSlug = request()->segment(3);
         if($currentSlug!='similar_candidates'){
           $currentSlug = request()->segment(2);
         }
      }


       
      // dd($popup_tags,$popup_address,$popup_company_name,$popup_industries,$popup_description);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<base href="">
<title>
@if (auth()->user()->is_superadmin === 1)
		@lang('app.superAdminPanel')
@else
		@lang('JERO')
@endif | 
		{{ __($pageTitle) }}
</title>
<meta name="description" content="{{ __($pageTitle) }}" />
<meta name="keywords" content="{{ __($pageTitle) }}" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta charset="utf-8" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="Jobmilli-ATS" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="" />
<link rel="canonical" href="" />
<link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
 @vite(['resources/css/app.css', 'resources/js/app.js'])
<link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

{{-- <link href="{{ asset('demo/css/intlTelInput.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('demo/js/pdf.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/mammoth.browser.min.js') }}{{ TM_VERSION }}"></script> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
  
<link href="{{ asset('demo/css/font-awesome.min.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/all.min.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.10.0/dist/tabler-icons.min.css">

  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> --}}

@stack('head-script')
<style>         
      @media  only screen and (min-width: 1024px) and (max-width: 1440px)
      {#kt_activitiesApplication{
        width: 1025px;
        z-index: 999999 !important;
      } 
      #kt_activitiesIntTeam{width: 1025;}}
      @media  only screen and (min-width: 1440px) and (max-width: 2560px){	#kt_activitiesApplication{
        width: 1200px;
        z-index: 999999 !important;
      }
       #kt_activitiesIntTeam{width: 1200px;} }
      #kt_activitiesIntTeam{z-index:9999;}

      @media  only screen and (min-width: 1024px) and (max-width: 1440px)
      {
        #kt_activitiesIntTeamtra{width: 1180px;}
      }
      @media  only screen and (min-width: 1440px) and (max-width: 2560px){
        #kt_activitiesIntTeamtra{width: 1380px;}
      }
      /* Opened on top of #interview_schedule_drawer (z-index 99999999999 !important
         below), so this needs to sit unambiguously higher — see the matching
         overlay z-index bump in the .in_tr_email_panel click handler below. */
      #kt_activitiesIntTeamtra{z-index:999999999999 !important;}

       @media only screen and (min-width: 1024px) and (max-width: 1440px){
        #kt_activitiesJob_show{width: 1025px;}
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px){
        #kt_activitiesJob_show {width: 1200px;}
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
          #kt_activitiesClientjobTeam_toggle {
              width: 1025px;
          }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClientjobTeam_toggle {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px){
        #kt_activitiesClientTeam {width: 1025px;}
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px){
        #kt_activitiesClientTeam {width: 1200px;}
        }
        .star{
          position: absolute !important;
          top: 1px !important;
          left: 0 !important;
        }
        .tagify__tag-text{
              margin-left: 0px !important;
        }
        .tagify {
            background: none !important;
        }



      .loadingMessage{
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 10px;
        border-radius: 5px;
        z-index: 9999;
        position: fixed;
        top: 75%;
        left: 50%;
        transform: translate(-50%, -50%);
      }
      .iti {
        position: relative;
        display: block !important;
      }
      
      .page-loader {
        justify-content: center;
        align-items: center;
        flex-direction: column; /* Stack the spinner and text vertically */
        height: 100vh; /* Full viewport height */
        width: 100vw; /* Full viewport width */
      }

      .spinner-border-full {
        width: 3rem; /* Adjust size as needed */
        height: 3rem;
      }

      .text-gray-800 {
        margin-top: 1rem; /* Space between spinner and text */
      }

      .loader_search{
            border: 5px solid #c5c2c2;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
      }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hide Loader Initially */
        .hidden_search {
            display: none;
        }

         .second_header{
                padding: 5px 16px 5px 16px !important;
                margin-top: 1px;
                margin-bottom: 9px;
                background: #fff;
                border-radius: 6px;
        }
        .customDivWidthdirect{
              width: 20% !important;
        }
        .customDivWidthClient{
              width: 33% !important;
        }
         .customDivWidthJobs{
              /* width: 25% !important; */
        }
        .kt_content_job{
          margin-top: -60px
        }
        .kt_content_client{
          margin-top: -60px
        }

        .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
            cursor: not-allowed;
            color: var(--bs-gray-500);
            /* visibility: inherit; */
            opacity: 0.2 !important;
        }

        .disablesectionOn{
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 99;
        }
        .disablesectionOnRow{
          position: relative;
        }

        /* Chrome, Safari, Edge, Opera */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

    
        .toast-custom-width {
        background: #ffffff !important;
        color: #333 !important;
        padding: 15px 18px !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
        width: 420px !important;
        border-left: 6px solid #4CAF50 !important; /* green accent */
        border-top: 1px solid #4CAF50 !important; /* green accent */
        border-bottom: 1px solid #4CAF50 !important; /* green accent */
        cursor: pointer !important;
        opacity: 1 !important;
    }

    .toast-custom-width a {
        color: #333 !important;
        text-decoration: none !important;
        display: block;
        width: 100%;
        height: 100%;
    }

    .toast-custom-width strong {
        color: #111 !important;
        font-weight: 700 !important;
    }
     /* Make the close (X) button dark & visible */
    .toast-custom-width .toast-close-button {
        color: #000 !important;
        font-size: 26px !important;
        font-weight: bold !important;
        opacity: 1 !important;
        right: 10px !important;
        top: 0px !important;
        z-index: 9999999 !important;
    }

    /* Optional: Change hover effect */
    .toast-custom-width .toast-close-button:hover {
        color: #ff0000 !important;
        opacity: 1 !important;
    }

    .toast-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        margin-bottom: 6px !important;
        color: #848edf !important;
    }

    .toast-message {
        font-size: 14px !important;
        line-height: 1.5;
    }
    @media only screen and (min-width: 1024px) and (max-width: 1440px){
      #kt_activitiesStaffing {width: 1250px;}
    }
    @media only screen and (min-width: 1440px) and (max-width: 2560px){
      #kt_activitiesStaffing {width: 1350px;}
    }

    @media only screen and (min-width: 1024px) and (max-width: 1440px){
      #kt_activitiesHM {width: 1025px;}
    }
    @media only screen and (min-width: 1440px) and (max-width: 2560px){
      #kt_activitiesHM {width: 1200px;}
    }
    @media only screen and (min-width: 1024px) and (max-width: 1440px){
      #kt_activitiesTerms {width: 1025px;}
    }
    @media only screen and (min-width: 1440px) and (max-width: 2560px){
      #kt_activitiesTerms {width: 1200px;}
    }

    .modal-backdrop.show {
        opacity: 0 !important;
        display: none;
    }

    .hidden-button {
    display: none; /* Initially hidden */
    position: absolute;
    bottom: 10px; /* Adjust position as needed */
    left: 50%;
    transform: translateX(-50%);
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  .show_hidden:hover .hidden-button {
    display: block; /* Show the button on hover */
  }

#kt_activitiesStaffing_close svg{
     color: red;
}

#kt_activitiesStaffing_close svg {
        color: red;
}
#kt_activitiesTerms_close svg {
        color: red;
}
#kt_activitiesHM_close svg {
        color: red;
}
.btn-active-light-danger svg{
   color: red;
}

#agreeBtn:disabled {
        cursor: not-allowed !important;
        opacity: 0.6;
    }
#agreeBtn:not(:disabled):hover {
    background: #218838;
}

    .swal2-container {
        z-index: 2247483647 !important;
    }

    
    .hasTime{
        top: 309.344px;
        left: 483.25px !important;
        right: auto;
    }
    .flatpickr-calendar.open {
        position: absolute !important;
        z-index: 2147483647 !important
    }

</style>


</head><!--end::Head-->


<!--begin::Body-->
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

  <div class="page-loader flex-column bg-dark bg-opacity-25">
    <span class="spinner-border spinner-border-full text-primary" role="status"></span>
    <span class="text-gray-800 fs-6 fw-semibold mt-5">Loading...</span>
  </div>

  
<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
  <!--begin::Page-->
  <div class="page d-flex flex-row flex-column-fluid">
  
    <!--begin::Aside-->
    @include('sections.service_company.aside-header-base')
    <!--end::Aside-->
	
    <!--begin::Wrapper-->
    <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
	
      <!--begin::Header-->
      @include('sections.service_company.header-base')
      <!--end::Header-->
	  
      <!--begin::Content-->
      @yield('content')
      <!--end::Content-->
	  
      <!--begin::Footer-->
      @include('sections.service_company.footer')
      <!--end::Footer-->
	  
    </div>
    <!--end::Wrapper-->
  </div>
  <!--end::Page-->
</div>
<!--end::Root--> 
<!--begin::Drawers-->
<!--begin::Activities drawer-->
@include('sections.service_company.activities-drawer')

<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
  <span class="svg-icon">
  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
      <polygon points="0 0 24 0 24 24 0 24" />
      <rect fill="#000000" opacity="0.5" x="11" y="10" width="2" height="10" rx="1" />
      <path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
    </g>
  </svg>
  </span>
</div>

<!--begin::Global Javascript Bundle(used by all pages)-->
<div id="kt_activitiesApplication_toggle"></div>
<div id="kt_activitiesApplication" class="bg-body bg-custom-bar" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesApplication_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
  <div class="card shadow-none bg-custom-bar rounded-0 w-100" id="application_show_right_bar" ></div>
</div>
<div id="kt_create_offer_toggle"></div>
<div id="kt_create_offer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="create_offer" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default: '100%', lg: '82%'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_create_offer_toggle" data-kt-drawer-close="#kt_offercreate_close">
  <div class="card shadow-none rounded-0 w-100" id="offer_create_show_right_bar" ></div>
</div>

<style>
  /* Interview Schedule drawer shell — scoped so it never collides with page-local styles. */
  #interview_schedule_drawer .isd-shell-topbar { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 24px; border-bottom:1px solid #eef0f4; flex-wrap:wrap; flex-shrink:0; font-family:'Poppins',sans-serif; }
  #interview_schedule_drawer .isd-shell-topbar-left { display:flex; align-items:center; gap:12px; min-width:0; }
  #interview_schedule_drawer .isd-shell-topbar-ico { width:48px; height:48px; border-radius:12px; background:#eef4ff; color:#3e97ff; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:22px; }
  #interview_schedule_drawer .isd-shell-topbar-title { font-size:16px; font-weight:700; color:#181c32; line-height:1.3; }
  #interview_schedule_drawer .isd-shell-topbar-sub { font-size:11.5px; font-weight:400; color:#7e8299; margin-top:1px; }
  #interview_schedule_drawer .isd-shell-topbar-right { display:flex; align-items:center; gap:10px; }
  #interview_schedule_drawer .isd-shell-btn-cancel { border:1px solid #e4e6ef; background:#fff; color:#3f4254; border-radius:8px; padding:9px 18px; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:500; cursor:pointer; }
  #interview_schedule_drawer .isd-shell-btn-cancel:hover { background:#f8f9fb; }
  #interview_schedule_drawer .isd-shell-btn-back { border:1px solid #e4e6ef; background:#fff; color:#3e97ff; border-radius:8px; padding:9px 16px; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:600; cursor:pointer; }
  #interview_schedule_drawer .isd-shell-btn-back:hover { background:#f5f9ff; }
  #interview_schedule_drawer .isd-shell-btn-primary { border:none; background:#3e97ff; color:#fff; border-radius:8px; padding:8px 18px; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:600; cursor:pointer; text-align:left; line-height:1.3; }
  #interview_schedule_drawer .isd-shell-btn-primary small { display:block; font-size:10px; font-weight:400; opacity:.85; }
  #interview_schedule_drawer .isd-shell-btn-primary:hover { filter:brightness(.94); }
  #interview_schedule_drawer .isd-shell-btn-primary:disabled { background:#c2c9d6 !important; cursor:not-allowed; }
  #interview_schedule_drawer .isd-shell-divider { width:1px; height:22px; background:#e4e6ef; }
  #interview_schedule_drawer .isd-shell-close { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#7e8299; cursor:pointer; flex-shrink:0; }
  #interview_schedule_drawer .isd-shell-close:hover { background:#f8f9fb; color:#f64e60; }

  /* This drawer is opened from *inside* the already-open Job Details drawer
     (#kt_activitiesApplication, z-index 999999) — it must render above it,
     not behind. Its own overlay is deliberately disabled below (data-kt-drawer-overlay="false"):
     a second shared .drawer-overlay would collide (same z-index) with the Job
     Details drawer's overlay that's already dimming the page, which made both
     drawers fight over the same backdrop and close unexpectedly. The page is
     already dimmed by whichever drawer this one is nested inside; this one
     just needs to render above it. */
  #interview_schedule_drawer { z-index: 99999999999 !important; }
  /* KTDrawer sets height via inline style at runtime (full viewport height,
     same as every other drawer in the app). That's fine when content is long,
     but this drawer's content is often shorter than the screen, leaving a
     large blank white gap below it. Cap it to content height instead, with
     the viewport as a ceiling so it still scrolls normally for long content. */
  #interview_schedule_drawer { height: auto !important; max-height: 100vh !important; bottom: auto !important; }
  #interview_schedule_drawer > .d-flex.flex-column { height: auto !important; max-height: 100vh !important; }
  #interview_schedule_drawer #app_interview_schedule_content { flex-grow: 0 !important; max-height: calc(100vh - 76px) !important; }
</style>
<div id="interview_schedule_drawer_toggle" style="display:none;"></div>
<div id="interview_schedule_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="interview-schedule"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
    data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'82%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#interview_schedule_drawer_toggle" data-kt-drawer-close="#interview_schedule_drawer_close">
  <div class="d-flex flex-column h-100 w-100">
    <div class="isd-shell-topbar">
      <div class="isd-shell-topbar-left">
        <span class="isd-shell-topbar-ico"><i class="ti ti-calendar-clock"></i></span>
        <div>
          <div class="isd-shell-topbar-title" id="interview_schedule_drawer_title">Interview Schedule</div>
          <div class="isd-shell-topbar-sub" id="interview_schedule_drawer_subtitle">Schedule and invite candidate &amp; interview panel</div>
        </div>
      </div>
      <div class="isd-shell-topbar-right">
        <button type="button" class="isd-shell-btn-back" id="interview_schedule_drawer_back" style="display:none;">Back</button>
        <button type="button" class="isd-shell-btn-cancel" id="interview_schedule_drawer_cancel">Cancel</button>
        <button type="button" class="isd-shell-btn-primary" id="interview_schedule_drawer_primary">
          <span class="indicator-label">Schedule Interview<small>Confirm &amp; send invitations</small></span>
          <span class="indicator-progress" style="display:none;">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        </button>
        <span class="isd-shell-divider"></span>
        <span class="isd-shell-close" id="interview_schedule_drawer_close"><i class="ti ti-x"></i></span>
      </div>
    </div>
    <div class="flex-grow-1" style="overflow-y:auto;min-height:0;" id="app_interview_schedule_content"></div>
  </div>
</div>
<script>
  // Single header contract for both steps of the AJAX-loaded interview-schedule
  // content: the content calls window.isdShellSetStep('main'|'email') when it
  // switches steps, and this shell adapts its one primary button accordingly
  // instead of the content drawing its own duplicate header/footer.
  window.isdShellSetStep = function(step){
    if (step === 'email') {
      $('#interview_schedule_drawer_title').text('Interview Schedule Email');
      $('#interview_schedule_drawer_back').show();
      $('#interview_schedule_drawer_primary .indicator-label').html('Send Email');
    } else {
      $('#interview_schedule_drawer_title').text('Interview Schedule');
      $('#interview_schedule_drawer_back').hide();
      $('#interview_schedule_drawer_primary .indicator-label').html('Schedule Interview<small>Confirm &amp; send invitations</small>');
    }
  };
  $(document).on('click', '#interview_schedule_drawer_back', function(){
    $('.divback').trigger('click');
  });
  $(document).on('click', '#interview_schedule_drawer_primary', function(){
    if ($('#div_interview_schedule_second').is(':visible')) {
      $('#sendEmailBtn').trigger('click');
    } else if (typeof submitForm === 'function') {
      submitForm();
    }
  });

  // Safe open helper — always uses the drawer element (not the toggle div),
  // so it works whether the drawer was previously opened, closed, or never init'd.
  window.openInterviewScheduleDrawer = function() {
    var el = document.getElementById('interview_schedule_drawer');
    if (!el) return;
    var inst = KTDrawer.getInstance(el);
    if (!inst) { KTDrawer.createInstances(); inst = KTDrawer.getInstance(el); }
    if (inst && !inst.isShown()) inst.show();
  };
</script>
<!--end::Interview Schedule drawer-->

<div id="kt_activitiesIntTeam_toggle"></div>
<div id="kt_activitiesIntTeam" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle" data-kt-drawer-close="#kt_activitiesIntTeam_close">
  <div class="card shadow-none rounded-0 w-100" id="kt_activitiesIntTeam_body"></div>
</div>

<div id="kt_activitiesIntTeamtra_toggle"></div>
<div id="kt_activitiesIntTeamtra" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeamtra_toggle" data-kt-drawer-close="#kt_activitiesIntTeamtra_close">
  <div class="card shadow-none rounded-0 w-100" id="kt_activitiesIntTeamtra_body"></div>
</div>

<div id="kt_client_activitiesApplication_toggle"></div>
<div id="kt_client_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="client_activitiesApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_client_activitiesApplication_toggle" data-kt-drawer-close="#kt_client_activitiesApplication_close">
  <div class="card shadow-none rounded-0 w-100" id="client_application_show_right_bar" ></div>
</div>
<div class="modal fade" id="client_interview_schedule" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" id="client_app_interview_schedule_content"></div>
  </div>
</div>
<div id="kt_activitiesBenchApplication_toggle"></div>
<div id="kt_activitiesBenchApplication" class="bg-body bg-custom-bar" data-kt-drawer="true" data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesBenchApplication_close">
  <div class="card bg-custom-bar shadow-none rounded-0 w-100" id="bench_application_show_right_bar" ></div>
</div>
<div id="kt_activitiesPublicBenchApplication_toggle"></div>
<div id="kt_activitiesPublicBenchApplication" class="bg-body bg-custom-bar" data-kt-drawer="true" data-kt-drawer-name="activitiesPublicBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesPublicBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesPublicBenchApplication_close">
  <div class="shadow-none bg-custom-bar rounded-0 w-100" id="public_bench_application_show_right_bar" ></div>
</div>

    <div id="kt_activitiesJob_show_toggle"></div>
    <div id="kt_activitiesJob_show" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesJob_show_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
                <div class="card shadow-none rounded-0 w-100">
                    
                    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_show_body"></div>
                </div>
    </div>


      <div id="kt_activitiesClientTeam_toggle"></div>
        <div id="kt_activitiesClientTeam" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
            data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
            data-kt-drawer-toggle="#kt_activitiesClientTeam_toggle" data-kt-drawer-close="#kt_activitiesClientTeam_close">
            <div class="card shadow-none rounded-0 w-100">
                <div class="card-body position-relative px-0 py-0" id="kt_activitiesClientTeam_body"> &nbsp; </div>
            </div>
      </div>


  <div id="kt_activitiesHM_toggle"></div>
    <div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
      <div class="card shadow-none rounded-0 w-100">
        <div class="card-header px-5 py-0" id="kt_activities_header">
          <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark">Team Members</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
          <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                <rect x="0" y="7" width="16" height="2" rx="1"></rect>
                <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
              </g>
            </svg>
            </span> </button>
          </div>
        </div>
        <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
      </div>
    </div>

    <div id="kt_activitiesStaffing_toggle"></div>
<div id="kt_activitiesStaffing" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesStaffing_toggle" data-kt-drawer-close="#kt_activitiesStaffing_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0 d-none" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Partners</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesStaffing_close">
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
            <rect x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body position-relative px-0 py-0" id="kt_activities_body"> </div>
    <!--end::Body-->
  </div>
</div>


<!--start terms::Content-->

<div id="kt_activitiesTerms_toggle"></div>
<div id="kt_activitiesTerms" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTerms_toggle" data-kt-drawer-close="#kt_activitiesTerms_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_or_client">Terms & Condition</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesTerms_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                        <rect x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                     </g>
                  </svg>
               </span>
            </button>
         </div>
      </div>
      <div class="card-body position-relative px-0 py-0"> 

        <div class="card-body position-relative px-0 py-0" id="kt_terms_body">
          <div id="kt_terms_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_terms_body" data-kt-scroll-dependencies="#kt_terms_header, #kt_terms_footer" data-kt-scroll-offset="5px" style="">
            <div class="card card-custom mx-2">
              <div class="card-body py-7" id="kt_activitiesTerms_body">

              </div>
            </div>
          </div>
        </div>

      </div>
   </div>
</div>

<!--end terms ::Content-->



<!--begin::Modal - Invite Friends-->
<div class="modal fade" id="kt_modal_meeting_comment" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
	<!--begin::Modal dialog-->
	<div class="modal-dialog mw-650px">
	   <!--begin::Modal content-->
	   <div class="modal-content">
		  <!--begin::Modal header-->
		  <div class="modal-header pb-0 border-0 justify-content-end">
			 <!--begin::Close-->
			 <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
				<!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
				<span class="svg-icon svg-icon-1">
				   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					  <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
						 <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
						 <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
					  </g>
				   </svg>
				</span>
				<!--end::Svg Icon-->
			 </div>
			 <!--end::Close-->
		  </div>
		  <!--begin::Modal header-->
		  <!--begin::Modal body-->
		  <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
			 <!--begin::Heading-->
			 <div class="text-center mb-13">
				<!--begin::Title-->
				<h1 class="mb-3">Meeting Comments</h1>
				<!--end::Title-->
				<!--end::Description-->
			 </div>
			 <!--end::Heading-->                  
			 
			 <!--end::Separator-->
			 <!--begin::Textarea-->
			 <textarea class="form-control form-control-solid mb-8" rows="3" id="comments" name="comments" placeholder="Type or paste new comment here"></textarea>

			 <input type="hidden" id="meet_id" name="meet_id" value="">

			 <center><button type="button" id="comments-form" class="btn btn-success waves-effect waves-light">New Comment</button></center>
			 <!--end::Textarea-->
			 <!--begin::Users-->
			 <div class="mb-10">
				<!--begin::Heading-->
				<div class="fs-6 fw-bold mb-2">Comments</div>
				<!--end::Heading-->
				<!--begin::List-->
				<div class="mh-300px scroll-y me-n7 pe-7" id="app_meeting_schedule_commnet">

				  
				   <!--end::User-->
				</div>
				<!--end::List-->
			 </div>
			 <!--end::Users-->
		  </div>
		  <!--end::Modal body-->
	   </div>
	   <!--end::Modal content-->
	</div>
	<!--end::Modal dialog-->
 </div>
 <!--end::Modal - Invite Friend-->
 
 
 <!--begin::Mass Mail - Modal-->
<div class="modal fade" id="send_mail" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Send Email</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
          </svg>
          </span>
        </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="application_send_mail_body_content"></div>
    </div>
  </div>
</div>
<!--end::Mass Mail - Modal-->
 <!--begin::Mass Mail - Modal-->
<div class="modal fade" id="send_whatsapp" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Send Whatsapp</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
          </svg>
          </span>
        </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="application_send_whatsapp_body_content"></div>
    </div>
  </div>
</div>
<!--end::Mass Mail - Modal-->

<div class="modal fade" id="send_qrBox" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered" style="max-width:700px;">
    <div class="modal-content" style="border-radius:12px; padding:10px;">

      <div class="modal-body" style="padding:20px;">
        <div class="row align-items-center">

          <!-- LEFT SIDE -->
          <div class="col-md-7">

            <h4 style="font-weight:600; margin-bottom:20px;">
              Steps to Connect WhatsApp
            </h4>

            <!-- Step 1 -->
            <div style="display:flex; align-items:start; margin-bottom:15px;">
              <div style="width:28px;height:28px;border:1px solid #000;border-radius:50%;text-align:center;line-height:28px;margin-right:10px;">
                1
              </div>
              <div>Open WhatsApp and scan the QR code on Jero.works.</div>
            </div>

            <!-- Step 2 -->
            <div style="display:flex; align-items:start; margin-bottom:15px;">
              <div style="width:28px;height:28px;border:1px solid #000;border-radius:50%;text-align:center;line-height:28px;margin-right:10px;">
                2
              </div>
              <div>
                Wait for “CONNECTED” to securely link your account.
              </div>
            </div>

            <!-- Step 3 -->
            <div style="display:flex; align-items:start; margin-bottom:15px;">
              <div style="width:28px;height:28px;border:1px solid #000;border-radius:50%;text-align:center;line-height:28px;margin-right:10px;">
                3
              </div>
              <div>Start automating candidate communication and managing chats on Jero.works.</div>
            </div>

            <div style="margin-top:10px;">
              <div class="form-check d-inline-block me-3">
                  <input class="form-check-input" type="checkbox" id="agreeCheck" checked>
                  <label class="form-check-label" for="agreeCheck">
                      I confirm that I have read and agree to the <a href="{{ url('/whatsapp-terms-and-conditions') }}" target="_blank" style="text-decoration:none; font-size:14px;">
              Terms & Conditions.
              </a>
                  </label>
              </div>
              
            </div>

            <div style="margin-top:20px;">
              <div style="margin-top:15px; display:flex; align-items:center; gap:15px;">

                    <!-- Agree Button -->
                          <button id="agreeBtn" type="button" style="background:#28a745; color:#fff;border:none; padding:6px 14px; border-radius:6px;font-size:14px; cursor:pointer;"> Agree </button>

                    <!-- Disagree Button -->
                        <button id="disagreeBtn" type="button" data-bs-dismiss="modal" style=" background:#dc3545; color:#fff; border:none; padding:6px 14px; border-radius:6px; font-size:14px; cursor:pointer;
                      "> Disagree </button>

              </div>
            </div>

          </div>

          <!-- RIGHT SIDE -->
          <div class="col-md-5 text-center">

            <div id="dummyQr" class="" style="width: 235px; height: 235px; background: #000; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0px 4px 0px 4px; color:#fff;cursor: pointer;">
              <div style="font-size:30px;" id="loadingText">⟳</div>
               <div style="font-size:13px; margin-top:10px;">
                Click Agree to show QR Code
               </div>
            </div>

            <!-- Your QR render -->
            <div class="d-none" id="qrBox" style="margin-top:10px;"></div>

          </div>

        </div>
      </div>

    </div>
  </div>
</div>

{{-- <div class="modal fade" id="send_qrBox" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Scan the QR code with WhatsApp</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body d-flex justify-content-center align-items-center" style="min-height:300px;">

          <div id="qrBox" class="text-center"></div>

      </div>
    </div>
  </div>
</div> --}}



<!--start profile is incomplete section-->
<div class="modal fade" id="profile_popup_modal" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
      <div class="modal-header" style="padding: 20px 30px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin: 0;">Your company profile is incomplete</h2>
        <button class="btn profile_popup_modal" data-bs-dismiss="modal" data-dismiss="modal" style="padding: 8px; border-radius: 50%; background: transparent; border: none; cursor: pointer; transition: background-color 0.2s;">
          <span class="svg-icon svg-icon-1" style="display: flex; align-items: center; justify-content: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
            </svg>
          </span>
        </button>
      </div>
      
      <div class="card-body" style="padding: 40px 30px; position: relative;">
        <div style="font-family: system-ui, -apple-system, sans-serif;">


          <div style="display: flex; align-items: center; gap: 20px; padding: 20px;">

            <!-- Progress Circle -->
            <div style="position: relative; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
              <!-- Outer Circle with Progress -->
              <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; 
                  background: conic-gradient(
                      #f31b4d {{ $profile_completion_percentage }}%, /* Progress color */
                      #e2e8f0 {{ $profile_completion_percentage }}% /* Remaining color */
                  );">
              </div>
              <!-- Inner Circle (Hollow Center) -->
              <div style="position: absolute; width: 80%; height: 80%; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 20px; font-weight: bold; color: #475569;">
                  {{ round($profile_completion_percentage, 2) }}%
                </span>
              </div>
            </div>
          
            <!-- Steps -->
            <div style="display: flex; align-items: center; gap: 12px;">
              @if(!$popup_company_name)
                <!-- Step 1: Add Company Name -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                  @if($popup_company_name)
                  <span style="color: #22c55e; font-size: 18px;">✓</span>
                  @else
                  <span style="color: #ef4444; font-size: 18px;">✗</span>
                  @endif
                  <span style="color: #475569; font-size: 14px; font-weight: 500;">
                    <a href="{{ ($popup_company_name) ? 'javascript:;' : route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}">Add{{ $popup_company_name ? 'ed' :'' }} Company Name</a>
                    <br><span style="margin-left: 22%;"> <a href="{{route('service_company.account.edit', GeneralMethod::value_encrypt($company_id))}}"> (Click Here) </a> </span></span>
                </div>
              @endif
              @if(!$popup_industries)
              <!-- Step 2: Add Industry -->
              <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                @if($popup_industries)
                <span style="color: #22c55e; font-size: 18px;">✓</span>
                @else
                <span style="color: #ef4444; font-size: 18px;">✗</span>
                @endif
                <span style="color: #475569; font-size: 14px; font-weight: 500;"><a href="{{ ($popup_industries) ? 'javascript:;' : route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}">Add{{ $popup_industries ? 'ed' :'' }} Company Industry</a><br><span style="margin-left: 22%;"> <a href="{{route('service_company.account.edit', GeneralMethod::value_encrypt($company_id))}}"> (Click Here) </a> </span></span>
              </div>
              @endif
              @if(!$popup_description)
              <!-- Step 3: Add Description -->
              <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                @if($popup_description)
                <span style="color: #22c55e; font-size: 18px;">✓</span>
                @else
                <span style="color: #ef4444; font-size: 18px;">✗</span>
                @endif
                <span style="color: #475569; font-size: 14px; font-weight: 500;"><a href="{{ ($popup_description) ? 'javascript:;' : route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}?switchSection=1">Add{{ $popup_description ? 'ed' :'' }} Company Description</a> <br><span style="margin-left: 22%;"> <a href="{{route('service_company.account.edit', GeneralMethod::value_encrypt($company_id))}}"> (Click Here) </a> </span></span>

               
              </div>
           @endif
             @if(!$popup_address)
              <!-- Step 4: Add Address -->
              <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                @if($popup_address)
                <span style="color: #22c55e; font-size: 18px;">✓</span>
                @else
                <span style="color: #ef4444; font-size: 18px;">✗</span>
                @endif
                <span style="color: #475569; font-size: 14px; font-weight: 500;"><a href="{{ ($popup_address) ? 'javascript:;' :  route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}?switchSection=2">Add{{ $popup_address ? 'ed' :'' }} Company Address</a> <br><span style="margin-left: 22%;"> <a href="{{route('service_company.account.edit', GeneralMethod::value_encrypt($company_id))}}"> (Click Here) </a> </span></span>
              </div>
          @endif
           @if(!$popup_tags)
              <!-- Step 5: Add Services -->
              <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                @if($popup_tags)
                <span style="color: #22c55e; font-size: 18px;">✓</span>
                @else
                <span style="color: #ef4444; font-size: 18px;">✗</span>
                @endif
                <span style="color: #475569; font-size: 14px; font-weight: 500;"><a href="{{ ($popup_tags) ? 'javascript:;' : route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}?switchSection=1">Add{{ $popup_tags ? 'ed' :'' }} Company Services</a> <br><span style="margin-left: 22%;"> <a href="{{route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}"> (Click Here) </a> </span></span>
              </div>
            @endif
          
            </div>
          
          </div>
          
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end profile is incomplete section-->


<div href="javascript:void(0);" id="skill_intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#skill_interview_feedback"></div>
<div class="modal fade" id="skill_interview_feedback" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content custmbg">
      <div class="modal-header flex-stack" style="position: relative;">
        <h2>Interview Feedback</h2>
        <span style="position: absolute;right: 88px;display: flex;">

          <a href="javascript:;" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2 urlchange" target="_blank" style="display: flex">
              Download &nbsp;<i class="fa fa-download text-muted fw-bold d-block fs-6"></i>
          </a> 
				</span>
        <div class="btn btn-sm btn-icon btn-active-color-primary data-bs-dismiss" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div id="skill_load_interview_feedback">&nbsp;</div>
    </div>
  </div>
</div>




<script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}{{ TM_VERSION }}"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script> --}}
<script src="{{ asset('demo/js/intlTelInput.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('front/assets/js/number-to-words.js') }}"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>
<script src="{{ asset('demo/js/daterangepicker.min.js') }}{{ TM_VERSION }}"></script>
{{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
{{-- <script src="{{ asset('demo/js/select2.min.js') }}{{ TM_VERSION }}"></script> --}}
 
<script>
    const checkboxAgree = document.getElementById('agreeCheck');
    const buttonAgree = document.getElementById('agreeBtn');

    checkboxAgree.addEventListener('change', function() {
        buttonAgree.disabled = !this.checked;
        if (!this.checked) {
            // Show dummy QR again
            $('#dummyQr').removeClass('d-none');

            // Hide real QR
            $('#qrBox').addClass('d-none');

        } else {

        }
    });
</script>

<script>
        const userId = {{ auth()->id() ?? 'null' }};
        const interviewTableUrl = "{{ route('service_company.interview-schedule.table-view') }}";

        let audioContext;
        let audioUnlocked = false;
        let pendingNotificationSound = false;

        function unlockAudio() {
            if (audioUnlocked) return;

            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            audioContext.resume().then(() => {
                audioUnlocked = true;
                removeUnlockListeners();
                console.log("🔓 Audio unlocked after user interaction");

                // If there was a notification waiting → play it now
                if (pendingNotificationSound) {
                    playNotificationSound();
                    pendingNotificationSound = false;
                }
            });
        }

        function addUnlockListeners() {
            window.addEventListener('click', unlockAudio);
            window.addEventListener('keydown', unlockAudio);
            window.addEventListener('mousemove', unlockAudio);
            window.addEventListener('scroll', unlockAudio);
        }

        function removeUnlockListeners() {
            window.removeEventListener('click', unlockAudio);
            window.removeEventListener('keydown', unlockAudio);
            window.removeEventListener('mousemove', unlockAudio);
            window.removeEventListener('scroll', unlockAudio);
        }

        function playNotificationSound() {
            const audio = new Audio('/sounds/notification.wav');
            audio.volume = 1.0;
            audio.play().catch(err => console.warn("🔇 Sound blocked:", err));
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (!userId || !window.Echo) return;

            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {

                    console.log("📩 Notification Received:", notification);

                    // When a notification arrives:
                    // if audio still locked → enable listeners
                    if (!audioUnlocked) {
                        console.log("🟡 Audio locked → adding unlock listeners...");
                        pendingNotificationSound = true;
                        addUnlockListeners();
                    } else {
                        // Already unlocked → play sound immediately
                        playNotificationSound();
                    }

                    // --- Show Toast ---
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                        "tapToDismiss": false,
                        "toastClass": "toast toast-custom-width"
                    };

                    let htmlMessage = `
                        <a href="${interviewTableUrl}" target="_blank">
                            <div>
                                <div class="toast-title" style="position: relative;">
                                    <i class="fa fa-bell" style="font-size: 18px; color: #848edf; position: absolute; margin-top: 3; margin-left: 0px;"></i>
                                    <span style="margin-left: 23px;">${notification.title}</span>
                                </div>
                                <div class="toast-message">
                                    <strong>Candidate:</strong> ${notification.candidate_name}<br>
                                    <strong>Designation:</strong> ${notification.job_title}<br>
                                    <strong>Company:</strong> ${notification.company_name}<br>
                                    <strong>Date & Time:</strong> ${notification.interview_datetime}
                                </div>
                            </div>
                        </a>
                    `;

                    toastr.success(htmlMessage);
                });
        });
</script>



<script>

$(document).on('input', '#min_sal', function () {

    let minSalary = parseInt($(this).val()) || 0;

    $('#max_sal').attr('min', minSalary);

    let maxSalary = parseInt($('#max_sal').val()) || 0;

    if (maxSalary && maxSalary < minSalary) {

        $('#salary_error').text('Max salary cannot be less than Min salary');
        $('#max_sal').val('');
        // $(this).focus();

    } else {

        $('#salary_error').text('');
    }
});


$(document).on('blur', '#max_sal', function () {

    let minSalary = parseInt($('#min_sal').val()) || 0;
    let maxSalary = parseInt($(this).val()) || 0;

    if (maxSalary < minSalary) {
         if(maxSalary !=''){
           $('#salary_error').text('Max salary cannot be less than Min salary');
         }else{
          $('#salary_error').text('');
         }
        $(this).val('');
        // $(this).focus();

    } else {

        $('#salary_error').text('');
    }
});

$(document).on('change', '#min_exp', function () {
  
    let minExp = parseInt($(this).val()) || 0;
    let maxExp = parseInt($('#max_exp').val()) || 0;

    if (maxExp && maxExp < minExp) {

        $('#experience_error').text('Maximum experience cannot be less than Minimum experience');

        $('#max_exp').val('');

    } else {

        $('#experience_error').text('');
    }
});


$(document).on('change', '#max_exp', function () {

    let minExp = parseInt($('#min_exp').val()) || 0;
    let maxExp = parseInt($(this).val()) || 0;

    if (maxExp < minExp) {

        $('#experience_error').text('Maximum experience cannot be less than Minimum experience');

        $(this).val('');

    } else {

        $('#experience_error').text('');
    }
});

$(document).on('input', '#min_sal_bench', function () {

    let minSalary = parseInt($(this).val()) || 0;

    $('#max_sal_bench').attr('min', minSalary);

    let maxSalary = parseInt($('#max_sal_bench').val()) || 0;

    if (maxSalary && maxSalary < minSalary) {

        $('#salary_bench_error').text('Max salary cannot be less than Min salary');
        $('#max_sal_bench').val('');
        // $(this).focus();

    } else {

        $('#salary_bench_error').text('');
    }
});


$(document).on('blur', '#max_sal_bench', function () {

    let minSalary = parseInt($('#min_sal_bench').val()) || 0;
    let maxSalary = parseInt($(this).val()) || 0;

    if (maxSalary < minSalary) {
         if(maxSalary !=''){
           $('#salary_bench_error').text('Max salary cannot be less than Min salary');
         }else{
          $('#salary_bench_error').text('');
         }
        $(this).val('');
        // $(this).focus();

    } else {

        $('#salary_bench_error').text('');
    }
});

$(document).on('change', '#min_exp_bench', function () {
  
    let minExp = parseInt($(this).val()) || 0;
    let maxExp = parseInt($('#max_exp_bench').val()) || 0;

    if (maxExp && maxExp < minExp) {

        $('#experience_bench_error').text('Maximum experience cannot be less than Minimum experience');

        $('#max_exp_bench').val('');

    } else {

        $('#experience_bench_error').text('');
    }
});


$(document).on('change', '#max_exp_bench', function () {

    let minExp = parseInt($('#min_exp_bench').val()) || 0;
    let maxExp = parseInt($(this).val()) || 0;

    if (maxExp < minExp) {

        $('#experience_bench_error').text('Maximum experience cannot be less than Minimum experience');

        $(this).val('');

    } else {

        $('#experience_bench_error').text('');
    }
});



$(document).on('click', '.deleteBtn', function (e){
    e.preventDefault();
    var userid = $(this).data('userid');
    $.post('{{ url('/whatsapp/delete') }}',{
        _token:'{{ csrf_token() }}',
         userid: userid
    },function(res){
        $('.connectBtn').css('right', '10px');
        $('.sendwhatsapp_connected_'+userid).addClass('d-none');
        $('.sendwhatsapp_connect_'+userid).removeClass('d-none');
        $('#f_phone_'+userid).text(res.connectedNumberGet);
    });
});

    $(document).on('click', '.sendwhatsapp_panel', function (){
        var btn = $(this);
        var userid = $(this).data('userid');
        var seconds = 10;
         // show processing
         buttonAgree.disabled = !this.checked;
         $('#loadingText').text('Loading......');
         $('#send_qrBox').modal('show');
        btn.html('<i class="fa fa-spinner fa-spin"></i> QR Generating....');
        

        var timer = setInterval(function(){

            seconds--;

            // btn.html('<i class="fa fa-spinner fa-spin"></i> QR Generating...');

            if(seconds <= 0){

                clearInterval(timer);

                // $('#send_qrBox').modal('hide');

                // toggle buttons
                // alert('sendwhatsapp_connected_'+ userid);
                // $('.sendwhatsapp_connected_' + userid).addClass('d-none');
                // $('.sendwhatsapp_connect_' + userid).removeClass('d-none');
            }

        },1000);



        $.post('/whatsapp/create',
        {_token:'{{ csrf_token() }}',
        userid: userid
        },function(){
            loadQR(userid);
        });
    });

     $(document).on('click', '#agreeBtn', function (){

        // Hide dummy QR
        $('#dummyQr').addClass('d-none');

        // Show real QR
        $('#qrBox').removeClass('d-none');

        // OPTIONAL: Load QR dynamically
        // $('#qrBox').html('<img src="your-qr-api-url" />');

    });
         $(document).on('click', '#disagreeBtn', function (){

        // Hide dummy QR
        $('.sendwhatsapp_panel').html('<i class="fab fa-whatsapp text-white"></i> <span id="">Click to Connect</span>');
        $('#dummyQr').removeClass('d-none');

        // Show real QR
        $('#qrBox').addClass('d-none');

        // OPTIONAL: Load QR dynamically
        // $('#qrBox').html('<img src="your-qr-api-url" />');

    });


    function loadQR(userid){
      
        $.get('/whatsapp/qr',{ userid: userid }, function(res){
            if(res.qr){
               audioUnlocked
                $('#qrBox').html(
                    '<img width="250" src="'+res.qr+'">'
                );
                checkStatus(userid);
                buttonAgree.disabled = this.checked;
                $('#loadingText').text('⟳');
            }
        });
    }
    // checkStatus();
    function checkStatus(userid){
        setInterval(function(){
            $.get('/whatsapp/status',{ userid: userid },function(res){
                if(res.instance && res.instance.state){
                    $('#statusBox').html(res.instance.state);
                    if(res.instance.state === "open"){
                        console.log("Connected!"+userid+"jdgkdjkgdf");
                        // console.log(res.instance.connectedNumber);
                        // Change button color
                        $('#send_qrBox').modal('hide');
                        $('.sendwhatsapp_connected_'+userid).removeClass('d-none');
                        $('.sendwhatsapp_connect_'+userid).addClass('d-none');
                        $('.sendwhatsapp_text_'+userid).html('<i class="fab fa-whatsapp"></i> Click to Connect');
                        $('#f_phone_'+userid).val(res.instance.connectedNumber);
                        $('.connectBtn').css('right', '90px');
                        $('#f_phone_user'+userid).text(res.instance.connectedNumber);
                        // Change text

                    }
                }
            });
        },5000);
    }




  function fresherrbutton(cvalue)
   {
       if(cvalue == 0){
            $('.disablesection').removeClass('disablesectionOn');
       }else if(cvalue==1){
           $('.disablesection').addClass('disablesectionOn');
       }
   }

  // Detect click outside modal
  $(document).on('mousedown', function (e) {
      let modal = $('#send_qrBox'); // change ID
      if (modal.hasClass('show')) {
          if (!$(e.target).closest('.modal-content').length) {
            $("#disagreeBtn").click();
          }
      }
  });

   const inputs = document.querySelectorAll("#ctc, #ectc, #phone, #secondary_mobile");

    inputs.forEach(input => {
        // Disable arrow key increment/decrement
        input.addEventListener("keydown", function (e) {
            if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                e.preventDefault();
            }
        });

        // Disable scroll wheel changing the value
        input.addEventListener("wheel", function (e) {
            e.preventDefault();
        }, { passive: false });
    });




  function showToastr(type, message, title) {
    switch(type) {
        case 'success':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.success(message, title);
            break;

        case 'error':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error(message, title);
            break;

        case 'info':
            toastr.options.positionClass = 'toast-top-left';
            toastr.info(message, title);
            break;

        case 'warning':
            toastr.options.positionClass = 'toast-bottom-left';
            toastr.warning(message, title);
            break;
        case 'successTop':
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(message, title);
            break; 

        default:
            toastr.options.positionClass = 'toast-top-center';
            toastr.info("Unknown status", "Notice");
    }
}

$(document).on('click', '.hm-listing', function ()
  {
			
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.hmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {		
								$('#kt_activitiesHM_body').html(response.view);		
   						}
   	  			});
   	});

    $(document).on('click', '.spartner-listing', function (){
  
	$('#kt_activitiesStaffing_toggle').trigger('click');		
	var id    		= $(this).data('row-id');
	var url   		= "{{ route('service_company.jobs.spartnerlisting',':id') }}";
	url     		= url.replace(':id', id);
	var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
	$.ajax({
					type  : 'GET',
					url   : url,
					 beforeSend: function() {
								 $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
					},
					success : function (response) {
							$('#kt_activities_body').html(response.view);		
					}
			});
}); 

    $(document).on('click', '.client-terms-listing', function (){
   		$('#kt_activitiesTerms_toggle').trigger('click');		
   		  var id    		= $(this).data('row-id');
        var recommendation_relation_id    		= $(this).data('row-recommendation_relation_id');
        var url   		= "{{ route('service_company.jobs.termshmlisting',':id') }}";
        url     		= url.replace(':id', id)+"?recommendation_relation_id="+recommendation_relation_id;
   		  var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesTerms_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				       // $('#kt_activitiesTerms_body').html(response.view);
               $('#kt_activitiesTerms_body').html(response.terms);			
   						}
   	  			});
            
   	});

        $(document).on('click', '.client-terms-accept', function (){
   		$('#kt_activitiesTerms_toggle').trigger('click');		
   		  var id    		= $(this).data('row-id');
        var recommendation_relation_id    		= $(this).data('row-relation_id');
        var url   		= "{{ route('service_company.jobs.termshmlistingaccept',':id') }}";
        url     		= url.replace(':id', id)+"?recommendation_relation_id="+recommendation_relation_id;
   		  var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesTerms_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
               $('#kt_activitiesTerms_body').html(response.terms);			
   						}
   	  			});
            
   	});



            $(document).on('click', '.show-detail-team', function handler(e) {
                e.preventDefault();
                $('#kt_activitiesClientTeam_toggle').trigger('click');
                var id = $(this).data('row-id');
                var url = "{{ route('service_company.team.viewmore', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var currentPageTeam = $('.custom-pagination .page-link.active').data('page');
                localStorage.setItem('currentPageTeam', currentPageTeam);
                
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#kt_activitiesClientTeam_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                    success: function(response) {
                        console.log("Team data" + response);
                        $("#kt_activitiesClientTeam").removeAttr("style");
                        $(".closeclass").addClass("drawer-overlay");
                        $(".closeclass").attr("style", "z-index: 109");
                        
                        $('#kt_activitiesClientTeam_body').html(response.view);
                    }
                });
            });

$(document).on("click", "#shareJobBtn", function () {
    let jobUrl = $(this).data("url");

    // Copy to clipboard
    navigator.clipboard.writeText(jobUrl).then(() => {
        showToastr('successTop', 'Job link copied', 'Success');
        $(this).text('Copied');
        setTimeout(() => {
            $(this).text('Share Job');
        }, 500);
    }).catch(err => {
        showToastr('errorTop', 'Failed to copy', 'Error');
    });

    // Open in new tab
    setTimeout(() => {
          //  window.open(jobUrl, "_blank");
        }, 1000);
    
});

$(document).on("click", "#shareJobsBtn", function () {
    let jobUrl = $(this).data("url");

    // Copy to clipboard
    navigator.clipboard.writeText(jobUrl).then(() => {
        showToastr('successTop', 'Jobs link copied', 'Success');
        $("#changeTextShare").text('Career Website');
        setTimeout(() => {
            $("#changeTextShare").text('Career Website');
        }, 500);
    }).catch(err => {
        showToastr('errorTop', 'Failed to copy', 'Error');
    });

    // Open in new tab
    setTimeout(() => {
           window.open(jobUrl, "_blank");
        }, 1000);
    
});

$(document).on("click", "#shareBenchCandidateBtn", function () {
    let jobUrl = $(this).data("url");

    // Copy to clipboard
    navigator.clipboard.writeText(jobUrl).then(() => {
        showToastr('successTop', 'Inhouse Talents link copied', 'Success');
        // $(this).text('Copied');
        // setTimeout(() => {
        //     $(this).text('Open Public');
        // }, 500);
    }).catch(err => {
        showToastr('errorTop', 'Failed to copy', 'Error');
    });

    // Open in new tab
    setTimeout(() => {
           window.open(jobUrl, "_blank");
        }, 1000);
    
});


      $(document).on('click','.download_offer', function(e){			
            e.preventDefault();	
            localStorage.setItem('toggleOpen', 'true');
            $('#application_mass_mail_body_content').html('');
            $('#application_fwd_offer_body_content').html('');
            var jobId = $(this).attr('data-jobid');
            var candidate = $(this).attr('data-candidate');
            var mtype = $(this).attr('data-mtype');
            var offerId = $(this).attr('data-offerid');
            var arr 		=	[candidate];
            var jobIdArr = [jobId];
            var id        	=	jobIdArr[0];
            var url       	=	"{{ route('service_company.cvtracker.offer_download_mail',':id') }}";
            url         	=	url.replace(':id', id);
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var token       =	"{{ csrf_token() }}";
            $.ajax({
                    type		: 'POST',
                    url			: url,
                    data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr,'mtype':mtype,'offerId':offerId},
                    cache		: false,
                    success 	: function (response) {
                                        if (response && response.status == "success") {
                                          const link = document.createElement('a');
                                          link.href = response.url; // URL to the PDF file
                                          link.download = ''; // Specify the filename for the download, or leave it empty to use the default file name
                                          link.target = '_blank'; // Open in a new window
                                          document.body.appendChild(link);
                                          link.click(); // Trigger the download
                                          document.body.removeChild(link); // Clean up
                                        } else {
                                          // Backend returns 'errors' (not 'error') on coin/package failures — catch
                                          // any non-success status here instead of silently doing nothing.
                                          Swal.fire("Warning", (response && response.message) ? response.message : 'Offer Letter not ready', "warning");
                                        }
                            },
                    error       : function () {
                                        Swal.fire("Error", "Could not download the offer letter. Please try again.", "error");
                            }
              });
      });
      







  localStorage.removeItem('toggleOpen');
function comment_modal(id){
                var url 		=	"{{ route('service_company.dashboard.meeting-comment',':id') }}";
               url     		=	url.replace(':id', id);		   
               var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                     $.ajax({
         							type  		: 'GET',
         							url   		: url,
         							beforeSend	: function() {
         														$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
         							},
         							success : function (response) 
         							{
                                 console.log(response);
                                 setTimeout(() => {
                                    $('#app_meeting_schedule_commnet').html(response.html);
                                 }, 500);
         										
         							}
         				});

         }

         var whatsappWindow = null;

          function openWhatsAppPopup(event) {
              event.preventDefault(); // Prevent default link behavior

              // WhatsApp URL
              var whatsappUrl = event.currentTarget.href;

              // Popup window dimensions
              var width = 1000;
              var height = 1000;

              // Calculate the center of the screen
              var left = (screen.width / 2) - (width / 2);
              var top = (screen.height / 2) - (height / 2);

              // If the window is already open, focus on it
              if (whatsappWindow && !whatsappWindow.closed) {
                  whatsappWindow.focus();
              } else {
                  // Open a new centered window
                  whatsappWindow = window.open(
                      whatsappUrl, 
                      'whatsappWindow', 
                      `width=${width},height=${height},top=${top},left=${left}`
                  );
              }
          }



$(document).ready(function(){
  
          var profile_completion_percentage = "{{ $profile_completion_percentage }}";
          var popup_company_type = "{{ $popup_company_type }}";
          var  profile_popup_modal_show = "{{ $_SESSION['profile_popup_modal_show'] }}";
          
          if(profile_completion_percentage !=0 && popup_company_type !=3 && profile_popup_modal_show==1){
             $('#profile_popup_modal').modal('show');
          }
         
      $(document).on('click', '.drawer-overlay', function () {
        // Get the z-index value of the clicked element
        var zIndexValue = $(this).css('z-index');
        
        // Remove all drawer-overlay elements with the same z-index value
        $('.drawer-overlay').filter(function () {
            return $(this).css('z-index') === zIndexValue;
        }).remove();
    });

    
    
      $('#kt_header_user_menu_toggle').click(function(event) {
             if (localStorage.getItem('toggleOpen')) {
            event.stopPropagation(); // Prevent the click event from bubbling up to the body
            var $dropdown = $('.menu-sub-dropdown');
            if ($dropdown.hasClass('menu-sub-dropdown-hidden')) {
                 $dropdown.removeClass('menu-sub-dropdown-hidden');
                 $dropdown.attr('style','z-index: 105; position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(1278px, 65px);display: inline-block;');
                 $dropdown.css('display', 'inline-block');
            } else {
                $dropdown.addClass('menu-sub-dropdown-hidden');
                $dropdown.css('display', 'none');
            }
             }
        });
        
        // Hide the dropdown when clicking outside of it
        $('body').click(function(event) {
            if (localStorage.getItem('toggleOpen')) {
            var $dropdown = $('.menu-sub-dropdown');
            if (!$dropdown.hasClass('menu-sub-dropdown-hidden')) {
                $dropdown.addClass('menu-sub-dropdown-hidden');
                $dropdown.css('display', 'none');
                $('.menu-sub-accordion').removeClass('show'); 
            }
            }
        });
        
        
         $('.menu-accordion').click(function(){
             if (localStorage.getItem('toggleOpen')) {
              var $dropdown = $(this);
                if (!$dropdown.hasClass('show')) {
                     $('.menu-accordion').removeClass('show'); 
                     $dropdown.addClass('show');
                }else{
                    $dropdown.removeClass('show'); 
                }
             }
           
       }); 
       
        $('body').on('click', '.openModalTeam', function(){
            $('#add_team_member').modal('show');
            $('#kt_activitiesIntTeamtra_close').trigger('click');
        });
       
    
        $(document).on('click', '.book-btn', function(){
         			var id    		=	$(this).attr('rel');
              var datadate    		=	$(this).attr('data-date');
              var datatime    		=	$(this).attr('data-time');
              var datatmode    		=	$(this).attr('data-mode');
         			var url 		=	"{{ route('service_company.job-applications.create-schedule',':id') }}";
         			url     		=	url.replace(':id', id);
              if (datadate && datatime) {
                  let query = [];
                  if (datadate) query.push('datadate=' + encodeURIComponent(datadate));
                  if (datatime) query.push('datatime=' + encodeURIComponent(datatime));
                  if (datatmode) query.push('datatmode=' + encodeURIComponent(datatmode));
                  url += '?' + query.join('&');
              }
         			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
              openInterviewScheduleDrawer();
              window.isdShellSetStep('main');
         			$.ajax({
         							type  		: 'GET',
         							url   		: url,
         							beforeSend	: function() {
         														$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
         							},
         							success : function (response)
         							{
         								$('#app_interview_schedule_content').html(response.view);
         							}
         				});
         });


    $(document).on('click','.in_tr_email_panel', function(){
     //  $('body').on('click', '.in_tr_email_panel', function(){
       
           $('#kt_activitiesIntTeamtra_toggle').trigger('click');

             KTDrawer.createInstances();

            // Open the drawer programmatically
            const drawerElement = document.querySelector('#kt_activitiesIntTeamtra_toggle');
            const drawerInstance = KTDrawer.getInstance(drawerElement);

            if (drawerInstance) {
                drawerInstance.show();
            }

            // #interview_schedule_drawer (behind this one) has its own overlay
            // deliberately disabled and sits at z-index 99999999999 !important,
            // so KTDrawer's own overlay for THIS drawer would otherwise render
            // underneath it and never visibly dim it. Bump just the overlay
            // this drawer created (the most recently appended one) above that,
            // but below #kt_activitiesIntTeamtra itself (999999999999 !important).
            setTimeout(function(){ $('.drawer-overlay').last().css('z-index', 999999999998); }, 0);

                var client_id	=	$('#client_idMail').val();
         				var token 		=	"{{ csrf_token() }}";
         				var panel    		=	$(this).data('panel');
                $('#panelValue').val(panel);
         				var job_id    		=	$('#jobIdMail').val();
                var self_company    		=	$('#self_company').val();
                var client_name    		=	$('#client_name').val();
                var cc_email = $('#cc_email').val();
         				var url   		=	"{{ route('service_company.job-applications.contactPersionWithTeam',':id') }}"; 
         				url     		=	url.replace(':id', client_id);  		
         				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
         				$.ajax({
         							type  		:	'POST',
         							data		:	{'_token': token, 'client_id' : client_id, 'job_id' : job_id, 'cc_email' : cc_email,'panel' : panel,'self_company':self_company,'client_name':client_name},
         							url   		:	url,
         							beforeSend	:	function() {
         														$('#kt_activitiesIntTeamtra_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
         							},
         							success 	:	function (response) {
         								$('#kt_activitiesIntTeamtra_body').html(response.view);
         							}
         				}); 	


         			
      });

    $(document).on('click','.in_tr_select', function(e){

        var rel = $(this).attr('rel');
        var assign = $('#in_tr_team_select_' + rel).attr("data-assign");
        var title = $('#in_tr_team_select_' + rel).attr("data-title");
        var email = $('#in_tr_team_select_' + rel).attr("data-email");

        if (assign == 0) {
            $(this).addClass("btn-light-danger");
            $(this).removeClass("btn-light-primary");
            $(this).text('Remove');
            $(this).attr("data-assign", 1);
            AddEvent(title, email);
        } else if (assign == 1) {
            $(this).addClass("btn-light-primary");
            $(this).removeClass("btn-light-danger");
            $(this).text('Add');
            $(this).attr("data-assign", 0);
            DeleteEvent(title, email);
        }

    });

    function AddEvent(name, email) {
        var cc_email = $('#cc_email').val();
        if (cc_email != '') {
            var includeStr = cc_email.includes(email);
            if (includeStr === false) {
                var assignNewEmail = cc_email + ' , ' + email;
                $('#cc_email').val(assignNewEmail);
            }
        } else {
            $('#cc_email').val(email);
        }
    }

    function DeleteEvent(contact_person_name, email) {

        var team_hiring_manager_email = '';
        var tempEmail = [];
        var cc_email = $('#cc_email').val();

        if (cc_email != '') {

            var emailArr = cc_email.split(',');
            for (var inc = 0; inc < emailArr.length; inc++) {
                if (email.trim() != emailArr[inc].trim()) {
                    tempEmail.push(emailArr[inc]);

                }
            }
            team_hiring_manager_email = tempEmail.toString();


        }

        $('#cc_email').val(team_hiring_manager_email);
    }

  $(document).on('click','.cp_select_panel', function(e){   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#team_select_'+rel).attr("data-assign");
			var title					=	$('#team_select_'+rel).attr("data-title");
			var email					=	$('#team_select_'+rel).attr("data-email");
			var designation				=	$('#team_select_'+rel).attr("data-designation");

			if(assign ==0)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				AddEventPanel(title, email, designation);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEventPanel(title, email);
			}
			
	});

  function AddEventPanel(name, email, designation)
  {
      var currentValue			=	$('#panellist').val();
      var cc_email				=	$('#cc_email').val();
      if(currentValue !='')
      {
        var includeStr			=	currentValue.includes(name);
        if(includeStr  === false)
        {
          var assignNewValue	=	currentValue +' , '+name;
          $('#panellist').val(assignNewValue);

          var assignNewEmail	=	cc_email +' , '+email;
          $('#cc_email').val(assignNewEmail);
        }
      }
      else
      {
        $('#panellist').val(name);
        $('#cc_email').val(email);
      }
      if (typeof isdRegisterPanelMeta === 'function') { isdRegisterPanelMeta(name, email, designation); }
      if (typeof isdSyncChipsFromField === 'function') { isdSyncChipsFromField(); }
  }

  function DeleteEventPanel(contact_person_name, email)
  {	 
        var tem_hiring_namager		=	'';  
        var temp					=	[]; 
        var hiring_namager			=	$('#panellist').val(); 
        
        var team_hiring_manager_email		=	''; 
        var tempEmail						=	[]; 
        var cc_email						=	$('#cc_email').val();
        
        if(hiring_namager !='')
        {
          var hiring_namagerArr = hiring_namager.split(',');
          for(var cnt=0; cnt<hiring_namagerArr.length; cnt++)
          {
            if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
              temp.push(hiring_namagerArr[cnt]);
            
            }			
          }
          tem_hiring_namager	=	temp.toString();
              
          var emailArr 	= cc_email.split(',');
          for(var inc=0; inc<emailArr.length; inc++)
          {
            if(email.trim() != emailArr[inc].trim()){
              tempEmail.push(emailArr[inc]);
            
            }			
          }
          team_hiring_manager_email	=	tempEmail.toString();
          
              
        }	
        
        $('#panellist').val(tem_hiring_namager);
        $('#cc_email').val(team_hiring_manager_email);
        if (typeof isdSyncChipsFromField === 'function') { isdSyncChipsFromField(); }
  }




        
        $('body').on('click', '.sendmail_panel', function(){

               $('#kt_activitiesIntTeam_toggle_sendmail').trigger('click');
                KTDrawer.createInstances();

              // Open the drawer programmatically
              const drawerElement = document.querySelector('#kt_activitiesIntTeam_toggle_sendmail');
              const drawerInstance = KTDrawer.getInstance(drawerElement);

              if (drawerInstance) {
                  drawerInstance.show();
              }
         			
        });
        
       
        $(document).on('click','.cc_email_panel', function(){
           //  $('body').on('click', '.cc_email_panel', function(){
          
               $('#kt_activitiesIntTeam_toggle_cc').trigger('click');
                KTDrawer.createInstances();

              // Open the drawer programmatically
              const drawerElement = document.querySelector('#kt_activitiesIntTeam_toggle_cc');
              const drawerInstance = KTDrawer.getInstance(drawerElement);

              if (drawerInstance) {
                  drawerInstance.show();
              }
         			
        });
         $(document).on('click','.to_email_panel', function(){
        // $('body').on('click', '.to_email_panel', function(){
          
            $('#kt_activitiesIntTeam_toggle_to').trigger('click');
              KTDrawer.createInstances();

              // Open the drawer programmatically
              const drawerElement = document.querySelector('#kt_activitiesIntTeam_toggle_to');
              const drawerInstance = KTDrawer.getInstance(drawerElement);

              if (drawerInstance) {
                  drawerInstance.show();
              }
         			
        });
        

	@if (session('message_paid'))
                 Swal.fire({
									text				: "Sorry, This is a paid feature please connect to administrator.",
									icon				: "warning",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
                @endif

                @if (session('candidate_edit_denied'))
                 Swal.fire({
									title				: "Not Authorized",
									text				: @json(session('candidate_edit_denied')),
									icon				: "warning",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
                @endif

				
                  $(document).on('click','.lock_unlock', function(){
                  Swal.fire({
									text				: "Sorry, This is a paid feature please connect to administrator.",
									icon				: "warning",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
                });
                })

$( document ).ready(function() {

	$(document).on('click','.modal_show', function(){
               var id = $(this).attr('id');
               $('#meet_id').val(id);
               comment_modal(id);
               $('#kt_modal_meeting_comment').modal('show');
                              
              
            })


            $(document).on('click','#comments-form	', function(){
         
            
                  var meet_id	=	$('#meet_id').val();
                  var comments	=	$('#comments').val();
                  var token 		=	"{{ csrf_token() }}";

                  var id    		=	1;
                  var url   		=	"{{ route('service_company.dashboard.meeting-comment-new',':id') }}"; 
                  url     		=	url.replace(':id', id);  		
                  var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";  
                  if(comments !='' && meet_id !=''){  		
                  $.ajax({
                           type  		: 'POST',
                           data		: {'_token': token, 'comments' : comments, 'meet_id':meet_id},
                           url   		: url,
                           beforeSend	: function() {
                                                $('#comments-form').html('<img src="'+baseurl+'" height="35px" width="100%">');
                           },
                           success 	: function (response) 
                           {
                              if(response.status){
                                 comment_modal(meet_id);
                                 $('#comments').val('');
                                 $('#comments-form').text('New Comment');
                              }
                           }
                  }); 	
               }			
      
            });

        $(document).on('click','.pbench_show_application_detail',function(){
			//  table.on('click', '.pbench_show_application_detail', function (){
							$('#kt_activitiesPublicBenchApplication_toggle').trigger('click');
							var id    		=	$(this).data('row-id');
							var url 		=	"{{ route('service_company.bench-candidates.public_show',':id') }}";
							url     		=	url.replace(':id', id);		
							var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
							$.ajax({
											type  		:	'GET',
											url   		:	url,
											beforeSend	:	function() {
																$('#public_bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
											},
											success 	:	function (response) {
																$('#public_bench_application_show_right_bar').html(response.view);
											}
									});
			 });  
});
</script>
<script>
$( document ).ready(function() {

  $(document).on('click','.bench_show_application_detail',function(){
			//  table.on('click', '.bench_show_application_detail', function (){
						$('#kt_activitiesBenchApplication_toggle').trigger('click');
						var id    		=	$(this).data('row-id');
						var url 		=	"{{ route('service_company.bench-candidates.show',':id') }}";
						url     		=	url.replace(':id', id);		
						var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
						$.ajax({
										type  		:	'GET',
										url   		:	url,
										beforeSend	:	function() {
													  				$('#bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
														},
										success 	:	function (response) {
																			$('#bench_application_show_right_bar').html(response.view);
														}
								});
			 });  
});
</script>
<script>
         $('form input').keydown(function (e){         
             if (e.keyCode == 13){         
                 e.preventDefault();         
                 $( "#save-form" ).click();         
                 return false;         
             }         
         });
         $('body').on('click', '.right-side-toggle', function (){         
            $("body").removeClass("control-sidebar-slide-open");
         })
         $(function (){         
            $('.selectpicker').selectpicker({         	
				style	: 'btn-info',
                size	: 4
            });         
         });
         function languageOptions()
		 {         
             return
			 {         
                 processing		:   "@lang('modules.datatables.processing')",         
                 search			:   "@lang('modules.datatables.search')",         
                 lengthMenu		:   "@lang('modules.datatables.lengthMenu')",         
                 info			:   "@lang('modules.datatables.info')",         
                 infoEmpty		:   "@lang('modules.datatables.infoEmpty')",         
                 infoFiltered	:   "@lang('modules.datatables.infoFiltered')",         
                 infoPostFix	:   "@lang('modules.datatables.infoPostFix')",         
                 loadingRecords	: 	"@lang('modules.datatables.loadingRecords')",         
                 zeroRecords	:   "@lang('modules.datatables.zeroRecords')",         
                 emptyTable		:	"@lang('modules.datatables.emptyTable')",         
                 paginate		:	{         
                     					first		:   "@lang('modules.datatables.paginate.first')",         
                     					previous	:   "@lang('modules.datatables.paginate.previous')",         
                     					next		:   "@lang('modules.datatables.paginate.next')",         
                     					last		:	"@lang('modules.datatables.paginate.last')",         
                 					},         
                 aria			:	{         
                    					sortAscending:  "@lang('modules.datatables.aria.sortAscending')",
                    					sortDescending: "@lang('modules.datatables.aria.sortDescending')",
                 					},         
             }         
         }
         @if($user->hasRole('admin'))

             function selectColor(id){
                 $('.icolors li.active ').removeClass('active');
                 $('#'+id).addClass('active');
                 $('#stickyColor').val(id);
             }
			 
             function search($input, doneTypingInterval, type) {
         
                 var $anchor 		=	$input.siblings('a');
                 var typingTimer, fn;
         
                 if (type == 'data') {
                     fn = loadData;
                 }
                 if (type == 'table') {
                     fn = redrawTable;                    
                 }
                 $input.on('keyup', function (e) {         
                     if ($(this).val() !== '' || ($(this).val().length >= 0 && e.key === 'Backspace')) {
                         clearTimeout(typingTimer);
                         typingTimer = setTimeout(() => {
                             fn();
                         }, doneTypingInterval);         
                     }
                     $(this).val() !== '' ? $anchor.removeClass('d-none') : $anchor.addClass('d-none');         
                 })
         
                 $input.on('keydown', function () {
                     clearTimeout(typingTimer);
                 });
                 $anchor.click(function(e) {
                     $(this).siblings('input').val('');
                     fn();
                     $anchor.addClass('d-none');
                     $(this).siblings('input').focus();
                 })         
             }
					 //    sticky notes script
					 var stickyNoteOpen 			=	$('#open-sticky-bar');
					 var stickyNoteClose 			=	$('#close-sticky-bar');
					 var stickyNotes 				=	$('#footer-sticky-notes');
					 var viewportHeight 			=	Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
					 var stickyNoteHeaderHeight 	=	stickyNotes.height();
					 $('#sticky-note-list').css('max-height', viewportHeight-150);
					 stickyNoteOpen.click(function () {
						 $('#sticky-note-list').toggle(function () {
							 $(this).animate({
								 height: (viewportHeight-150)
							 })
						 });
						 stickyNoteClose.toggle();
						 stickyNoteOpen.toggle();
					 })
					 stickyNoteClose.click(function () {
					 $('#sticky-note-list').toggle(function () {
						 $(this).animate({
							 height: 0
						 })
					 });
					 stickyNoteOpen.toggle();
					 stickyNoteClose.toggle();
         	})
         @endif        
</script>
<script>
         $( document ).ready(function() {
             var currentControllerName   =   $('#currentControllerName').val();
             if(currentControllerName == 'AdminAdvancedSearchController'){
                 $('#btnFiterSubmitSearch').hide();
                  $('#common_search').keyup(function() {
                                 tableLoad('filter');
                     });
             }else{         
				$('#btnFiterSubmitSearch').click(function () {
					var common_search       =   $('#common_search').val();          
					var url                 =   '{{ url('service_company/advsearch?common_search=') }}'+common_search;                  
					window.location.href    =	url;
				});  
             }         
        });  
</script>
@stack('footer-script')
<script>	

  $(document).on('click','.ConfidentialClass', function(){
      Swal.fire({
        title: "Confidential Information",
        text: "The salary details for this position are confidential and cannot be displayed.",
        icon: "warning",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
  });

  $(document).on('click','.create_offer', function(){
          // alert("hi");
          $('#kt_create_offer_toggle').trigger('click');
				var id    		=	$(this).data('row-offerid');
				var url 		=	"{{ route('service_company.job-applications.create_offer',':id') }}";
				url     		=	url.replace(':id', id);		
				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  		:	'GET',
								url   		:	url,
								beforeSend	:	function() {
											  				$('#offer_create_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
											    },
								success 	:	function (response) {
                                  if(response.status=='permisionInfo'){
                                    Swal.fire({
                                      text				: response.view,
                                      icon				: "warning",
                                      buttonsStyling		: false,
                                      confirmButtonText	: "Ok",
                                      customClass			: {
                                                    confirmButton: "btn btn-primary"
                                                  }
                                        });
                                        $('#kt_create_offer_toggle').trigger('click');
                                    return false;
                                  }
																		$('#offer_create_show_right_bar').html(response.view);
												}
						});

        })

  $(document).on('click','.show-application-detail', async function(e){
 		    // table.on('click', '.show-application-detail', function (){
          e.preventDefault();
          const connectedCandidateShowCustom = await isInternetAvailable();
              if (!connectedCandidateShowCustom) {
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
            //  flatpickrfunDefine();

        var currentPage = $('.custom-pagination .page-link.active').data('page');
  
				$('#kt_activitiesApplication_toggle').trigger('click');
				var id    		=	$(this).data('row-id');
				var url 		=	"{{ route('service_company.job-applications.show',':id') }}?slug={{ $currentSlug }}&currentPage="+currentPage;
				url     		=	url.replace(':id', id);		
				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  		:	'GET',
								url   		:	url,
								beforeSend	:	function() {
											  				$('#application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
											    },
								success 	:	function (response) {
																		$('#application_show_right_bar').html(response.view);
												}
						});
 		}); 

    $(document).on('click','.website-show-application-detail', async function(e){
 		    // table.on('click', '.show-application-detail', function (){
          e.preventDefault();
          const connectedCandidateShowCustom = await isInternetAvailable();
              if (!connectedCandidateShowCustom) {
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
            //  flatpickrfunDefine();

        var currentPage = $('.custom-pagination .page-link.active').data('page');
  
				$('#kt_activitiesApplication_toggle').trigger('click');
				var id    		=	$(this).data('row-id');
				var url 		=	"{{ route('service_company.job-applications.websiteshow',':id') }}?slug={{ $currentSlug }}&currentPage="+currentPage;
				url     		=	url.replace(':id', id);		
				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  		:	'GET',
								url   		:	url,
								beforeSend	:	function() {
											  				$('#application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
											    },
								success 	:	function (response) {
																		$('#application_show_right_bar').html(response.view);
												}
						});
 		}); 

     $(document).on('click','.client-show-application-detail', async function(e){		 
		// table.on('click', '.client-show-application-detail', function (){

          e.preventDefault();
          const connectedCandidateShowdirect = await isInternetAvailable();
              if (!connectedCandidateShowdirect) {
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
      //  flatpickrfunDefine();
       var currentPage = $('.custom-pagination .page-link.active').data('page');
				$('#kt_client_activitiesApplication_toggle').trigger('click');
				var id    		=	$(this).data('row-id');
				var url 		=	"{{ route('service_company.job-applications.clientshow',':id') }}?currentPage="+currentPage;
				url     		=	url.replace(':id', id);		
				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  		:	'GET',
								url   		:	url,
								beforeSend	:	function() {
											  				$('#client_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
												},
								success 	:	function (response) {
																		$('#client_application_show_right_bar').html(response.view);
												}
						});
		 });   
        $('body').on('click', '#app_interview_schedule', function(){
         			var id    		=	$(this).attr('rel');
         			var url 		=	"{{ route('service_company.job-applications.create-schedule',':id') }}";
         			url     		=	url.replace(':id', id);
         			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
              openInterviewScheduleDrawer();
              window.isdShellSetStep('main');
         			$.ajax({
         							type  		:	'GET',
         							url   		:	url,
         							beforeSend	:	function() {
         														$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
         											},
         							success 	:	function (response) {
         																$('#app_interview_schedule_content').html(response.view);
                                        setTimeout(() => {
                                          $('#kt_activities_scroll').removeAttr('style');
                                        }, 500);
         											}
         				});
         });

         $('body').on('click', '.app_mail_schedule', function(){
                    localStorage.setItem('toggleOpen', 'true');
         			var id    		=	$(this).attr('rel');
         			var relint    		=	$(this).attr('relint');
         			var url 		=	"{{ route('service_company.job-applications.create-schedule',':id') }}"+"?relint="+relint;
         			url     		=	url.replace(':id', id);
         			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
              openInterviewScheduleDrawer();
              window.isdShellSetStep('main');
         			$.ajax({
         							type  		:	'GET',
         							url   		:	url,
         							beforeSend	:	function() {
         														$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
         											},
         							success 	:	function (response) {

         																$('#app_interview_schedule_content').html(response.view);

         																$('#div_interview_schedule_first').hide();
   			                                $('#div_interview_schedule_second').show();
         											}
         				});
         });

        $(document).on('click', '#interview_schedule_drawer_cancel', function(){
              $('#interview_schedule_drawer_close').trigger('click');
        });
         
        $('body').on('click', '.add_panel', function(){
         				$('#kt_activitiesIntTeam_toggle').trigger('click');
         				var panellist	=	$('#panellist').val();
         				var token 		=	"{{ csrf_token() }}";
         				
         				var id    		=	1;
         				var url   		=	"{{ route('service_company.job-applications.recruiter',':id') }}"; 
         				url     		=	url.replace(':id', id);  		
         				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
         				$.ajax({
         							type  		:	'POST',
         							data		:	{'_token': token, 'panellist' : panellist},
         							url   		:	url,
         							beforeSend	:	function() {
         														$('#kt_activitiesIntTeam_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
         							},
         							success 	:	function (response) {
         								$('#kt_activitiesIntTeam_body').html(response.view);
         							}
         				}); 				
         		
         });


         $(document).on('click', '.skill-feedback', function () {   
					$('#skill_intervied_feedback_toggle').trigger('click');
					var id      	=	$(this).data('row-id');
          var urlchange     	=	"{{ route('service_company.interview-schedule.skillshowfeedbackdownload',':id') }}";
          urlchange       		=	urlchange.replace(':id', id);
           $('.urlchange').attr('href', urlchange);

					var url     	=	"{{ route('service_company.interview-schedule.skillshowfeedback',':id') }}";
					url       		=	url.replace(':id', id);
					var baseurl    	= "{{ asset('assets/images/Loading2.gif') }}"; 
					$.ajax({
								type		: 'GET',
								url			: url,
								beforeSend	: function() {
											  $('#skill_load_interview_feedback').html('<img src="'+baseurl+'" height="35px" width="100%">');
								},
								success	: function (response) {
																if (response.status == "success") {
																	$('#skill_load_interview_feedback').html(response.view);
																}
								}
						});			 
		});

    $(document).on('click', '.show-detail-open', function ()
                {
                    $('#kt_activitiesJob_show_toggle').trigger('click');	
                    
                    var id = $(this).data('id');	
                
                    var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
                    url     		=	url.replace(':id', id);
                    var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                    $.ajax({
                                    type  		: 'GET',
                                    url   		: url,
                                    beforeSend	: function() {
                                                $('#kt_activitiesJob_show_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                    },
                                    success 	: function (response) {
                                                $('#kt_activitiesJob_show_body').html(response.view);	
                                    }
                            });
                });



                function flatpickrfunDefine(){
                          var id    		=	'1';
                          var url 		=	"{{ route('service_company.job-applications.create-schedule',':id') }}";
                          url     		=	url.replace(':id', id);
                          var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
                          openInterviewScheduleDrawer();
              window.isdShellSetStep('main');
                          $.ajax({
                                  type  		:	'GET',
                                  url   		:	url,
                                  beforeSend	:	function() {
                                                $('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                          },
                                  success 	:	function (response) {
                                                   localStorage.setItem('interviewShow', true);
                                                    $('#app_interview_schedule_content').html(response.view);	
                                                    setTimeout(() => {
                                                      $('#kt_activities_scroll').removeAttr('style');
                                                    }, 5);	
                                                  
                                          }
                          });
                }
                if (localStorage.getItem('interviewShow') === 'true') {
                    console.log('interviewShow is set to true');
                } else {
                    flatpickrfunDefine();
                    console.log('interviewShow is not set or not true');
                }
               

                
    
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




   setTimeout(function() {
        $('.noCalendar').each(function(index) {
            $(this).addClass('calendarHasTime' + (index + 1));
        });
   }, 1500);
      
</script>
</body>
</html>