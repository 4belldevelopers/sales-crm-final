@php 
   use App\Helper\GeneralMethod;
       GeneralMethod::benchAutoPause(auth()->user()->company_id);
       $profilesCompletion = GeneralMethod::profilesCompletion();	
       $company_id = auth()->user()->company_id;
      //  print_r($profilesCompletion->clientsprofiles->tags);
      $popup_tags = 1;//($profilesCompletion->clientsprofiles) ? $profilesCompletion->clientsprofiles->tags : '';
      $popup_address = ($profilesCompletion->clientsprofiles) ? $profilesCompletion->clientsprofiles->address : '';
      $popup_company_name =  ($profilesCompletion->profilesCompletion) ? $profilesCompletion->profilesCompletion->company_name : '';
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
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="Jobmilli-ATS" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="" />
<link rel="canonical" href="" />
<link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />
@php
    try {
        echo app(\Illuminate\Foundation\Vite::class)(['resources/css/app.css', 'resources/js/app.js']);
    } catch (\Throwable $e) {
        try {
            echo app(\Illuminate\Foundation\Vite::class)(['resources/js/app.js']);
        } catch (\Throwable) {
            // Graceful resilience fallback if Vite assets have not been built on the server
        }
    }
@endphp
<link rel="preload" href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"></noscript>
<link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

{{-- <link href="{{ asset('demo/css/intlTelInput.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('demo/js/pdf.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/mammoth.browser.min.js') }}{{ TM_VERSION }}"></script> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
  
<link rel="stylesheet" href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}{{ TM_VERSION }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.10.0/dist/tabler-icons.min.css">

{{-- jQuery must load BEFORE @stack('head-script') so pages that push inline
     <script>$(function(){...})</script> into the head (datepicker initialiser on
     service_company/job-applications/create, and similar inline jQuery snippets
     site-wide) find window.$/jQuery defined. Without this, those scripts run
     synchronously during the head parse and throw "Uncaught ReferenceError: $ is
     not defined", which cascades into select2 (null.offsetWidth) and tagify
     (null.addEventListener) failures on every page that uses jQuery pushed
     into the head. We keep the body-bottom copy commented as a one-liner with
     a debug note for future readers instead of deleting it; if you delete it,
     please delete this too. --}}
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
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

      /* Candidate drawer — V2 hero redesign: full structural layout rules here so the
         drawer renders correctly even when candidates-details.css is cached (old version).
         These rules are always in the browser CSSOM and apply the moment AJAX inserts HTML. */
      #kt_activitiesApplication .cdr-dl-menu{display:none;}
      #kt_activitiesApplication .cdr-dl-menu.open{display:block;}
      #kt_activitiesApplication .cdr-dl-item-logo svg{width:13px;height:13px;flex-shrink:0;}
      /* Topbar: horizontal flex row, close button pushed to the right */
      #kt_activitiesApplication .cdr-topbar{display:flex!important;align-items:center;flex-wrap:nowrap;gap:8px;padding:12px 16px;background:#fff;border-bottom:1px solid #e2e8f0;justify-content:space-between;}
      #kt_activitiesApplication .cdr-topbar-group{display:flex!important;align-items:center;gap:8px;flex:1 1 auto;min-width:0;overflow-x:auto;flex-wrap:nowrap;}
      /* Action buttons: outlined inline-flex pill */
      #kt_activitiesApplication .cdr-action-btn{display:inline-flex!important;align-items:center;gap:6px;padding:7px 14px;border:1.5px solid #d1d5db;border-radius:8px;background:#fff;color:#374151;font-family:'Poppins',sans-serif;font-size:12.5px;font-weight:500;cursor:pointer;text-decoration:none;white-space:nowrap;line-height:1;}
      #kt_activitiesApplication .cdr-action-btn.cdr-btn-wa{background:#25D366;color:#fff;border-color:#25D366;}
      #kt_activitiesApplication .cdr-action-btn svg{width:14px;height:14px;flex-shrink:0;}
      #kt_activitiesApplication .cdr-action-btn .cdr-caret{width:12px;height:12px;flex-shrink:0;color:#9ca3af;}
      /* Close button */
      #kt_activitiesApplication .cdr-topbar-close{width:34px;height:34px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;background:none;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;color:#6b7280;padding:0;margin-left:4px;}
      #kt_activitiesApplication .cdr-topbar-close svg{width:16px;height:16px;}
      /* Hero card v3: 2-column grid — identity left (wider), metrics right */
      #kt_activitiesApplication .cdr-hero-card{display:grid!important;grid-template-columns:1.4fr 1fr;background:#fff;border-bottom:1px solid #f0f4f8;}
      /* Identity sub-card: flex row — avatar-wrap left + id-body right */
      #kt_activitiesApplication .cdr-subcard-id{display:flex!important;gap:16px;align-items:flex-start;padding:18px 20px;border-right:1px solid #f1f5f9;}
      /* Rounded-square avatar + availability dot */
      #kt_activitiesApplication .cdr-avatar-wrap{position:relative;flex-shrink:0;align-self:flex-start;}
      #kt_activitiesApplication .cdr-avatar-sq{width:88px;height:88px;border-radius:16px;background:#eef2ff;color:#1e3a8a;font-family:'Poppins',sans-serif;font-weight:700;font-size:26px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
      #kt_activitiesApplication .cdr-avail-dot-v2{position:absolute;bottom:4px;right:4px;width:14px;height:14px;border-radius:50%;background:#22c55e;border:2.5px solid #fff;}
      /* Name / role */
      #kt_activitiesApplication .cdr-id-body{flex:1 1 auto;min-width:0;}
      #kt_activitiesApplication .cdr-name-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
      #kt_activitiesApplication .cdr-name{margin:0;font-size:18px;font-weight:700;color:#0f172a;line-height:1.2;font-family:'Poppins',sans-serif;}
      #kt_activitiesApplication .cdr-verified-v2{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:#2563eb;margin-left:6px;flex-shrink:0;}
      #kt_activitiesApplication .cdr-verified-v2 svg{width:11px;height:11px;color:#fff;stroke-width:3;}
      #kt_activitiesApplication .cdr-role{margin:3px 0 0 0;font-size:13px;font-weight:600;color:#2563eb;line-height:1.3;font-family:'Poppins',sans-serif;}
      /* 2-col meta grid */
      #kt_activitiesApplication .cdr-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px 12px;margin-top:10px;}
      #kt_activitiesApplication .cdr-meta-grid .cdr-meta-item{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:#475569;font-family:'Poppins',sans-serif;white-space:nowrap;overflow:hidden;min-width:0;}
      #kt_activitiesApplication .cdr-meta-grid .cdr-meta-item svg{width:13px;height:13px;flex-shrink:0;color:#94a3b8;}
      #kt_activitiesApplication .cdr-meta-grid .cdr-meta-item.cmi-green{color:#16a34a;}
      /* Skills */
      #kt_activitiesApplication .cdr-skills-sec{margin-top:14px;}
      #kt_activitiesApplication .cdr-skills-lbl{font-size:10.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;font-family:'Poppins',sans-serif;}
      #kt_activitiesApplication .cdr-skills{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
      #kt_activitiesApplication .cdr-skill-pill{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:500;border:1px solid #e2e8f0;font-family:'Poppins',sans-serif;line-height:1.2;}
      #kt_activitiesApplication .cdr-skill-more{background:#eef2ff;color:#4338ca;border-color:#c7d2fe;font-weight:600;}
      /* Metrics sub-card v3: 4-row vertical list */
      #kt_activitiesApplication .cdr-subcard-metrics{padding:0;}
      #kt_activitiesApplication .cdr-metric-list{display:flex;flex-direction:column;height:100%;justify-content:space-evenly;padding:8px 20px 8px 16px;}
      #kt_activitiesApplication .cdr-metric-row{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid #f0f4f8;}
      #kt_activitiesApplication .cdr-metric-row:last-child{border-bottom:none;}
      #kt_activitiesApplication .cdr-metric-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
      #kt_activitiesApplication .cdr-metric-icon svg{width:16px;height:16px;}
      #kt_activitiesApplication .cdr-metric-icon.ci-green{background:#ecfdf5;color:#16a34a;}
      #kt_activitiesApplication .cdr-metric-icon.ci-red{background:#fef2f2;color:#dc2626;}
      #kt_activitiesApplication .cdr-metric-icon.ci-blue{background:#eff6ff;color:#2563eb;}
      #kt_activitiesApplication .cdr-metric-label{flex:1 1 auto;font-size:12.5px;font-weight:500;color:#475569;font-family:'Poppins',sans-serif;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      #kt_activitiesApplication .cdr-metric-val{font-size:13px;font-weight:700;color:#0f172a;text-align:right;white-space:nowrap;font-family:'Poppins',sans-serif;}
      #kt_activitiesApplication .cdr-metric-val.cv-green{color:#16a34a;}
      #kt_activitiesApplication .cdr-metric-val.cv-red{color:#dc2626;}
      #kt_activitiesApplication .cdr-metric-frac{font-size:12px;font-weight:500;color:#64748b;}
      #kt_activitiesApplication .cdr-metric-val-block{text-align:right;min-width:0;}
      #kt_activitiesApplication .cdr-metric-sub{font-size:11px;color:#94a3b8;font-weight:400;display:block;margin-top:1px;white-space:nowrap;font-family:'Poppins',sans-serif;}
      /* Card/subcard system: page-content background + section-card display (FOUC-safe) */
      #kt_activitiesApplication .page-content{background:#eef2f7;padding:16px 20px;display:flex;flex-direction:column;gap:16px;}
      #kt_activitiesApplication .section-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.03);}
      #kt_activitiesApplication .section-card-header{display:flex;align-items:center;gap:10px;padding:14px 20px;background:#fff;border-bottom:1px solid #f0f4f8;}
      #kt_activitiesApplication .section-card-body{padding:16px 20px 20px;background:#fff;}
      #kt_activitiesApplication .cardshow{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.03);}
      #kt_activitiesApplication .card-header-sidebar{display:flex;align-items:center;gap:10px;background:#fff;border-bottom:1px solid #f0f4f8;padding:13px 24px;margin:-24px -24px 16px;}
      #kt_activitiesApplication .info-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:0;}
      #kt_activitiesApplication .info-card{background:#fff;border:1px solid #e8edf5;border-radius:10px;padding:14px 16px;display:flex;align-items:flex-start;gap:12px;box-shadow:0 1px 2px rgba(0,0,0,.04);}
      #kt_activitiesApplication .content-split{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
      #kt_activitiesApplication .content-main,#kt_activitiesApplication .content-sidebar{display:flex;flex-direction:column;gap:16px;}

      /* Select-Interviewer picker: 60% desktop / 75% tablet / 100% mobile is
         set via data-kt-drawer-width on the element itself (KTDrawer computes
         the responsive width in JS) — capped here so it never exceeds
         ~1080px on very large monitors, and never drops below a usable
         minimum on the "lg" breakpoint. */
      #kt_activitiesIntTeamtra{ max-width: 1080px !important; }
      @media only screen and (min-width: 992px) {
        #kt_activitiesIntTeamtra{ min-width: 640px !important; }
      }
      /* Opened on top of #interview_schedule_drawer (z-index: 1000000
         !important, see its own definition further down). This must sit
         unambiguously higher — see the matching overlay z-index bump in the
         .in_tr_email_panel click handler below. */
      #kt_activitiesIntTeamtra{ z-index: 1000020 !important; }

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

/* Remove online-status green dot from all avatar/image icons */
.bg-success.rounded-circle.h-20px.w-20px{display:none!important;}

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
     just needs to render above it.
     Kept comfortably within the 32-bit z-index range browsers actually honor
     (values like 99999999999 get silently clamped to the same internal max
     as any other huge number, which ties stacking order instead of ordering
     it — that clamping broke the Select-Interviewer picker drawer nested
     inside this one, since both had "huge" but effectively-equal z-indices). */
  #interview_schedule_drawer { z-index: 1000000 !important; }
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
        <button type="button" class="isd-shell-btn-cancel" id="interview_schedule_drawer_cancel" onclick="(function(){var el=document.getElementById('interview_schedule_drawer');if(el){el.classList.remove('drawer-on');el.style.removeProperty('transform');}})()">Cancel</button>
        <button type="button" class="isd-shell-btn-primary" id="interview_schedule_drawer_primary" onclick="if(jQuery('#div_interview_schedule_second').is(':visible')){jQuery('#sendEmailBtn').trigger('click');}else if(typeof window.submitForm==='function'){window.submitForm();}">
          <span class="indicator-label">Schedule Interview<small>Confirm &amp; send invitations</small></span>
          <span class="indicator-progress" style="display:none;">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        </button>
        <span class="isd-shell-divider"></span>
        <span class="isd-shell-close" id="interview_schedule_drawer_close" onclick="(function(){var el=document.getElementById('interview_schedule_drawer');if(el){el.classList.remove('drawer-on');el.style.removeProperty('transform');}})()"><i class="ti ti-x"></i></span>
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
  // Clean up the inline transform we force-set when opening, so KTDrawer's
  // CSS (.drawer.drawer-end { transform: translateX(100%) }) takes over once closed.
  $(document).on('click', '#interview_schedule_drawer_close', function(){
    var el = document.getElementById('interview_schedule_drawer');
    if (el) { el.classList.remove('drawer-on'); el.style.removeProperty('transform'); }
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
    if (!el.classList.contains('drawer')) el.classList.add('drawer', 'drawer-end');
    el.classList.add('drawer-on');
    el.style.transform = 'none';
  };
</script>
<!--end::Interview Schedule drawer-->

<div id="kt_activitiesIntTeam_toggle"></div>
<div id="kt_activitiesIntTeam" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle" data-kt-drawer-close="#kt_activitiesIntTeam_close">
  <div class="card shadow-none rounded-0 w-100" id="kt_activitiesIntTeam_body"></div>
</div>

<div id="kt_activitiesIntTeamtra_toggle"></div>
<div id="kt_activitiesIntTeamtra" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default: '100%', 'md': '75%', 'lg': '60%'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeamtra_toggle" data-kt-drawer-close="#kt_activitiesIntTeamtra_close">
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
<style>
/* ── Profile Popup Modal ── */
.ppi-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
.ppi-wrap .modal-content { border-radius: 16px; border: none; box-shadow: 0 24px 64px rgba(0,0,0,0.15); overflow: hidden; }

/* Top banner */
.ppi-banner { display: flex; align-items: stretch; gap: 0; padding: 24px 28px 24px; background: #fff; border-bottom: 1px solid #F1F5F9; position: relative; }
.ppi-banner-left { flex: 1; display: flex; align-items: flex-start; gap: 18px; }
.ppi-banner-illo { flex-shrink: 0; width: 100px; }
.ppi-banner-illo svg { width: 100%; height: auto; }
.ppi-banner-text { flex: 1; }
.ppi-banner-title { font-size: 17px; font-weight: 700; color: #0F172A; margin: 0 0 5px; line-height: 1.3; }
.ppi-banner-sub { font-size: 11.5px; color: #64748B; margin: 0 0 14px; line-height: 1.5; max-width: 340px; }
.ppi-benefits { display: flex; gap: 8px; flex-wrap: wrap; }
.ppi-benefit-chip { display: inline-flex; align-items: center; gap: 6px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 5px 10px; font-size: 10.5px; font-weight: 600; color: #374151; }
.ppi-benefit-chip svg { width: 14px; height: 14px; flex-shrink: 0; }

/* Completion card */
.ppi-comp-card { flex-shrink: 0; width: 230px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px 18px; margin-left: 20px; }
.ppi-comp-label { font-size: 10.5px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.ppi-comp-pct-row { display: flex; align-items: baseline; gap: 8px; margin-bottom: 10px; }
.ppi-comp-pct { font-size: 28px; font-weight: 700; color: #0F172A; line-height: 1; }
.ppi-comp-badge { font-size: 11px; font-weight: 600; color: #16A34A; background: #DCFCE7; border-radius: 20px; padding: 2px 8px; }
.ppi-comp-bar-bg { background: #E2E8F0; border-radius: 100px; height: 7px; margin-bottom: 10px; }
.ppi-comp-bar-fill { height: 7px; border-radius: 100px; background: #2563EB; }
.ppi-comp-meta { display: flex; align-items: center; justify-content: space-between; }
.ppi-comp-meta span { font-size: 10px; color: #64748B; display: flex; align-items: center; gap: 4px; }
.ppi-comp-meta svg { width: 11px; height: 11px; }

/* Close btn */
.ppi-close-btn { position: absolute; top: 12px; right: 12px; background: #F1F5F9; border: none; border-radius: 8px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748B; transition: background 0.13s; z-index: 10; }
.ppi-close-btn:hover { background: #E2E8F0; }

/* Body 3-col */
.ppi-body { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0; background: #F8FAFC; }

/* Left col – Profile Overview */
.ppi-overview { padding: 20px 18px; border-right: 1px solid #E2E8F0; }
.ppi-col-title { font-size: 12px; font-weight: 700; color: #0F172A; margin-bottom: 14px; }
.ppi-ov-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #F1F5F9; }
.ppi-ov-item:last-child { border-bottom: none; }
.ppi-ov-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ppi-ov-icon svg { width: 15px; height: 15px; }
.ppi-ov-info { flex: 1; min-width: 0; }
.ppi-ov-name { font-size: 11.5px; font-weight: 600; color: #1E293B; }
.ppi-ov-sub  { font-size: 10px; color: #94A3B8; }
.ppi-ov-status-ok   { color: #16A34A; flex-shrink: 0; }
.ppi-ov-status-fail { color: #EF4444; flex-shrink: 0; }
.ppi-ov-status-ok svg, .ppi-ov-status-fail svg { width: 16px; height: 16px; }

/* Middle col – Complete These Sections */
.ppi-sections { padding: 20px 18px; }
.ppi-sec-card { background: #fff; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; display: grid; grid-template-columns: 40px 1fr auto; gap: 12px; align-items: center; }
.ppi-sec-card:last-child { margin-bottom: 0; }
.ppi-sec-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ppi-sec-icon svg { width: 18px; height: 18px; }
.ppi-sec-content {}
.ppi-sec-head { display: flex; align-items: center; gap: 8px; margin-bottom: 3px; }
.ppi-sec-name { font-size: 12.5px; font-weight: 700; color: #0F172A; }
.ppi-sec-badge-req  { font-size: 9.5px; font-weight: 700; background: #FEE2E2; color: #DC2626; border-radius: 4px; padding: 1px 7px; }
.ppi-sec-badge-rec  { font-size: 9.5px; font-weight: 700; background: #DBEAFE; color: #2563EB; border-radius: 4px; padding: 1px 7px; }
.ppi-sec-desc { font-size: 10.5px; color: #64748B; line-height: 1.4; margin-bottom: 5px; }
.ppi-sec-note { display: flex; align-items: flex-start; gap: 4px; font-size: 10px; color: #F59E0B; font-weight: 500; }
.ppi-sec-note svg { width: 11px; height: 11px; flex-shrink: 0; margin-top: 1px; }
.ppi-sec-btn { display: inline-flex; align-items: center; gap: 5px; background: #2563EB; color: #fff; font-size: 11px; font-weight: 600; padding: 8px 14px; border-radius: 8px; text-decoration: none; white-space: nowrap; flex-shrink: 0; transition: background 0.13s; }
.ppi-sec-btn:hover { background: #1D4ED8; color: #fff; text-decoration: none; }

/* Right col – Why */
.ppi-why { padding: 20px 16px; border-left: 1px solid #E2E8F0; }
.ppi-why-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; }
.ppi-why-item:last-child { margin-bottom: 0; }
.ppi-why-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ppi-why-icon svg { width: 16px; height: 16px; }
.ppi-why-title { font-size: 11.5px; font-weight: 700; color: #0F172A; margin-bottom: 2px; }
.ppi-why-desc  { font-size: 10px; color: #64748B; line-height: 1.4; }

/* Footer */
.ppi-footer { display: flex; align-items: center; padding: 14px 24px; background: #fff; border-top: 1px solid #E2E8F0; }
.ppi-footer-sec { display: flex; align-items: center; gap: 6px; font-size: 10.5px; color: #94A3B8; margin-right: auto; }
.ppi-footer-sec svg { width: 13px; height: 13px; flex-shrink: 0; }
.ppi-skip-btn { background: none; border: 1px solid #E2E8F0; border-radius: 8px; padding: 8px 18px; font-size: 12px; font-weight: 600; color: #64748B; cursor: pointer; margin-right: 10px; font-family: 'Poppins', sans-serif; transition: background 0.13s; }
.ppi-skip-btn:hover { background: #F8FAFC; }
.ppi-complete-btn { display: inline-flex; align-items: center; gap: 7px; background: #2563EB; color: #fff; font-size: 12px; font-weight: 600; padding: 9px 20px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Poppins', sans-serif; text-decoration: none; transition: background 0.13s; }
.ppi-complete-btn:hover { background: #1D4ED8; color: #fff; text-decoration: none; }
.ppi-complete-btn svg { width: 14px; height: 14px; }
</style>

<div class="modal fade ppi-wrap" id="profile_popup_modal" tabindex="-1" aria-hidden="true" style="z-index: 99999999999 !important;">
  <div class="modal-dialog modal-dialog-centered" style="max-width:860px;">
    <div class="modal-content">
    @php
      $ppiCompleted = collect([$popup_company_name,$popup_industries,$popup_description,$popup_address,$popup_tags])->filter()->count();
      $ppiTotal     = 5;
      $ppiRemain    = $ppiTotal - $ppiCompleted;
      $ppiMins      = max(1, $ppiRemain * 2);
      $ppiAccEdit   = route('service_company.account.edit', GeneralMethod::value_encrypt($company_id));
    @endphp

    {{-- ── TOP BANNER ── --}}
    <div class="ppi-banner">
      <div class="ppi-banner-left">
        {{-- Illustration --}}
        <div class="ppi-banner-illo">
          <svg viewBox="0 0 120 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="30" width="80" height="70" rx="6" fill="#DBEAFE"/>
            <rect x="20" y="20" width="60" height="80" rx="5" fill="#EFF6FF" stroke="#93C5FD" stroke-width="1.5"/>
            <rect x="30" y="35" width="14" height="14" rx="3" fill="#BFDBFE"/>
            <rect x="52" y="35" width="14" height="14" rx="3" fill="#BFDBFE"/>
            <rect x="30" y="57" width="14" height="14" rx="3" fill="#BFDBFE"/>
            <rect x="52" y="57" width="14" height="14" rx="3" fill="#BFDBFE"/>
            <rect x="38" y="78" width="20" height="22" rx="3" fill="#93C5FD"/>
            <rect x="8" y="65" width="16" height="35" rx="3" fill="#DBEAFE" stroke="#93C5FD" stroke-width="1"/>
            <rect x="11" y="72" width="5" height="5" rx="1" fill="#93C5FD"/>
            <rect x="11" y="82" width="5" height="5" rx="1" fill="#93C5FD"/>
            <circle cx="85" cy="38" r="18" fill="#DCFCE7"/>
            <path d="M77 38l5 5 8-10" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="0" y="99" width="120" height="3" rx="1.5" fill="#E2E8F0"/>
            <rect x="5" y="99" width="110" height="3" rx="1.5" fill="#CBD5E1"/>
          </svg>
        </div>
        <div class="ppi-banner-text">
          <div class="ppi-banner-title">Complete Your Company Profile ✨</div>
          <div class="ppi-banner-sub">A complete profile builds trust, increases visibility and helps you get better results on Jero Marketplace.</div>
          <div class="ppi-benefits">
            <span class="ppi-benefit-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
              Verified Company Badge
            </span>
            <span class="ppi-benefit-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
              Higher Marketplace Ranking
            </span>
            <span class="ppi-benefit-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
              Better AI Recommendations
            </span>
          </div>
        </div>
      </div>

      {{-- Completion card --}}
      <div class="ppi-comp-card">
        <div class="ppi-comp-label">Profile Completion</div>
        <div class="ppi-comp-pct-row">
          <span class="ppi-comp-pct">{{ round($profile_completion_percentage) }}%</span>
          <span class="ppi-comp-badge">Complete</span>
        </div>
        <div class="ppi-comp-bar-bg">
          <div class="ppi-comp-bar-fill" style="width:{{ $profile_completion_percentage }}%;"></div>
        </div>
        <div class="ppi-comp-meta">
          <span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ $ppiCompleted }} of {{ $ppiTotal }} sections completed
          </span>
          <span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $ppiMins }} min remaining
          </span>
        </div>
      </div>

      <button class="ppi-close-btn profile_popup_modal" data-bs-dismiss="modal" data-dismiss="modal">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    {{-- ── BODY 3-COL ── --}}
    <div class="ppi-body">

      {{-- LEFT: Profile Overview --}}
      <div class="ppi-overview">
        <div class="ppi-col-title">Profile Overview</div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#FEF3C7;color:#D97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Company Name</div><div class="ppi-ov-sub">Your organization name</div></div>
          @if($popup_company_name)
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
          @else
          <span class="ppi-ov-status-fail"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></span>
          @endif
        </div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#EDE9FE;color:#7C3AED;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Company Logo</div><div class="ppi-ov-sub">Your company logo</div></div>
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
        </div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#E0F2FE;color:#0284C7;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Website</div><div class="ppi-ov-sub">Your company website</div></div>
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
        </div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#FEE2E2;color:#DC2626;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Headquarters</div><div class="ppi-ov-sub">Your company location</div></div>
          @if($popup_address)
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
          @else
          <span class="ppi-ov-status-fail"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></span>
          @endif
        </div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#DCFCE7;color:#16A34A;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Industry</div><div class="ppi-ov-sub">Your company industry</div></div>
          @if($popup_industries)
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
          @else
          <span class="ppi-ov-status-fail"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></span>
          @endif
        </div>

        <div class="ppi-ov-item">
          <div class="ppi-ov-icon" style="background:#FFF7ED;color:#EA580C;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <div class="ppi-ov-info"><div class="ppi-ov-name">Company Description</div><div class="ppi-ov-sub">About your organization</div></div>
          @if($popup_description)
          <span class="ppi-ov-status-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
          @else
          <span class="ppi-ov-status-fail"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></span>
          @endif
        </div>

      </div>

      {{-- MIDDLE: Complete These Sections --}}
      <div class="ppi-sections">
        <div class="ppi-col-title">Complete These Sections</div>

        @if(!$popup_industries)
        <div class="ppi-sec-card">
          <div class="ppi-sec-icon" style="background:#EDE9FE;color:#7C3AED;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="ppi-sec-content">
            <div class="ppi-sec-head"><span class="ppi-sec-name">Company Industry</span><span class="ppi-sec-badge-req">Required</span></div>
            <div class="ppi-sec-desc">Select the industry that best describes your organization.</div>
            <div class="ppi-sec-note">
              <svg viewBox="0 0 24 24" fill="#F59E0B"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Required for AI job matching and marketplace visibility
            </div>
          </div>
          <a href="{{ $ppiAccEdit }}" class="ppi-sec-btn">Complete →</a>
        </div>
        @endif

        @if(!$popup_description)
        <div class="ppi-sec-card">
          <div class="ppi-sec-icon" style="background:#FFF7ED;color:#EA580C;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <div class="ppi-sec-content">
            <div class="ppi-sec-head"><span class="ppi-sec-name">Company Description</span><span class="ppi-sec-badge-rec">Recommended</span></div>
            <div class="ppi-sec-desc">Add a short description about your company, mission and expertise.</div>
            <div class="ppi-sec-note">
              <svg viewBox="0 0 24 24" fill="#F59E0B"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Helps build trust and improves your marketplace visibility
            </div>
          </div>
          <a href="{{ $ppiAccEdit }}?switchSection=1" class="ppi-sec-btn">Complete →</a>
        </div>
        @endif

        @if(!$popup_company_name)
        <div class="ppi-sec-card">
          <div class="ppi-sec-icon" style="background:#FEF3C7;color:#D97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
          </div>
          <div class="ppi-sec-content">
            <div class="ppi-sec-head"><span class="ppi-sec-name">Company Name</span><span class="ppi-sec-badge-req">Required</span></div>
            <div class="ppi-sec-desc">Add your registered company name to get verified on the platform.</div>
            <div class="ppi-sec-note">
              <svg viewBox="0 0 24 24" fill="#F59E0B"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Required to activate your company profile
            </div>
          </div>
          <a href="{{ $ppiAccEdit }}" class="ppi-sec-btn">Complete →</a>
        </div>
        @endif

        @if(!$popup_address)
        <div class="ppi-sec-card">
          <div class="ppi-sec-icon" style="background:#FEE2E2;color:#DC2626;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div class="ppi-sec-content">
            <div class="ppi-sec-head"><span class="ppi-sec-name">Company Address</span><span class="ppi-sec-badge-rec">Recommended</span></div>
            <div class="ppi-sec-desc">Add your office location to improve local talent matching.</div>
            <div class="ppi-sec-note">
              <svg viewBox="0 0 24 24" fill="#F59E0B"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Improves local search visibility on the marketplace
            </div>
          </div>
          <a href="{{ $ppiAccEdit }}?switchSection=2" class="ppi-sec-btn">Complete →</a>
        </div>
        @endif

        @if(!$popup_tags)
        <div class="ppi-sec-card">
          <div class="ppi-sec-icon" style="background:#E0F2FE;color:#0284C7;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          </div>
          <div class="ppi-sec-content">
            <div class="ppi-sec-head"><span class="ppi-sec-name">Company Services</span><span class="ppi-sec-badge-rec">Recommended</span></div>
            <div class="ppi-sec-desc">Tag services you offer to match the right opportunities.</div>
            <div class="ppi-sec-note">
              <svg viewBox="0 0 24 24" fill="#F59E0B"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Helps AI match you with relevant companies
            </div>
          </div>
          <a href="{{ $ppiAccEdit }}?switchSection=1" class="ppi-sec-btn">Complete →</a>
        </div>
        @endif

      </div>

      {{-- RIGHT: Why --}}
      <div class="ppi-why">
        <div class="ppi-col-title">Why complete your profile?</div>

        <div class="ppi-why-item">
          <div class="ppi-why-icon" style="background:#DCFCE7;color:#16A34A;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
          <div><div class="ppi-why-title">Get discovered by more companies</div><div class="ppi-why-desc">Complete profiles rank higher in search results.</div></div>
        </div>

        <div class="ppi-why-item">
          <div class="ppi-why-icon" style="background:#FCE7F3;color:#DB2777;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <div><div class="ppi-why-title">Increase trust &amp; credibility</div><div class="ppi-why-desc">Verified and complete profiles build stronger relationships.</div></div>
        </div>

        <div class="ppi-why-item">
          <div class="ppi-why-icon" style="background:#DBEAFE;color:#2563EB;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          </div>
          <div><div class="ppi-why-title">Better AI recommendations</div><div class="ppi-why-desc">AI understands your company better and recommends the right matches.</div></div>
        </div>

        <div class="ppi-why-item">
          <div class="ppi-why-icon" style="background:#FFF7ED;color:#EA580C;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          </div>
          <div><div class="ppi-why-title">Higher marketplace visibility</div><div class="ppi-why-desc">Stand out from other companies and attract top agencies.</div></div>
        </div>

      </div>
    </div>{{-- end ppi-body --}}

    {{-- ── FOOTER ── --}}
    <div class="ppi-footer">
      <span class="ppi-footer-sec">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Your information is secure and will never be shared.
      </span>
      <button class="ppi-skip-btn profile_popup_modal" data-bs-dismiss="modal" data-dismiss="modal">Skip for now</button>
      <a href="{{ $ppiAccEdit }}" class="ppi-complete-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Complete Profile
      </a>
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
<script>
// Send the CSRF token as a header on every jQuery AJAX request so endpoints
// that don't include _token in their POST body are still protected.
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
{{-- jquery.min.js intentionally only loaded in the head (above @stack('head-script'))
     to keep pages that push inline $(function(){...}) into the head from throwing
     "$ is not defined". See the head-side comment for context. --}}
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

            // Current session ID for this browser tab
            const currentTabSessionId = "{{ session()->getId() }}";

            // --- Single-Device Real-time Auto-Logout Handler ---
            function triggerAutoLogout() {
                window.location.href = "{{ url('/login') }}";
            }

            function checkDeviceSession() {
                if (!userId) return;
                fetch("{{ route('check-device-session') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        triggerAutoLogout();
                    }
                })
                .catch(() => {});
            }

            // 1. Check every 30 seconds
            setInterval(checkDeviceSession, 30000);

            // 2. Check immediately when user switches back to this browser tab
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    checkDeviceSession();
                }
            });

            // 3. WebSockets (Echo) instant logout broadcast listener
            if (userId && window.Echo) {
                window.Echo.private(`App.Models.User.${userId}`)
                    .listen('.SingleDeviceLogout', (data) => {
                        // If this event was broadcasted by our own fresh login, ignore it!
                        if (data && data.newSessionId && data.newSessionId === currentTabSessionId) {
                            return;
                        }
                        console.warn("⚠️ Single device logout event received for previous session:", data);
                        triggerAutoLogout();
                    });
            }
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



        $.post('{{ url('/whatsapp/create') }}',
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
      
        $.get('{{ url('/whatsapp/qr') }}',{ userid: userid }, function(res){
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
            $.get('{{ url('/whatsapp/status') }}',{ userid: userid },function(res){
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

    $(document).on('click', '.spartner-listing', function (e){
        if (e.isPropagationStopped() || $('#kt_activitiesRecruiter').length) {
            return;
        }
  
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
              (function(){ var _el=document.getElementById('interview_schedule_drawer'); if(!_el)return; if(!_el.classList.contains('drawer'))_el.classList.add('drawer','drawer-end'); _el.classList.add('drawer-on'); _el.style.transform='none'; })();
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
            // deliberately disabled and sits at z-index 1000000 !important, so
            // KTDrawer's own overlay for THIS drawer would otherwise render
            // underneath it and never visibly dim it. Bump just the overlay
            // this drawer created (the most recently appended one) above that,
            // but below #kt_activitiesIntTeamtra itself (1000020 !important).
            setTimeout(function(){ $('.drawer-overlay').last().css('z-index', 1000010); }, 0);

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
											cache		:	false,
											data		:	{ _t: Date.now() },
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
  let activeBenchDrawerXhr = null;
  let currentBenchDrawerSeq = 0;

  $(document).on('click', '.bench_show_application_detail', function () {
    $('#kt_activitiesBenchApplication_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.bench-candidates.show', ':id') }}".replace(':id', id);

    if (activeBenchDrawerXhr && typeof activeBenchDrawerXhr.abort === 'function') {
      activeBenchDrawerXhr.abort();
    }

    var thisSeq = ++currentBenchDrawerSeq;

    $('#bench_application_show_right_bar').html(
      '<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:400px; gap:16px;">' +
      '  <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">' +
      '    <span class="visually-hidden">Loading...</span>' +
      '  </div>' +
      '  <span style="font-family: \'Poppins\', sans-serif; font-size: 13px; color: #64748b; font-weight: 500;">Loading candidate profile...</span>' +
      '</div>'
    );

    activeBenchDrawerXhr = $.ajax({
      type: 'GET',
      url: url,
      cache: false,
      data: { _t: Date.now() },
      success: function (response) {
        if (thisSeq === currentBenchDrawerSeq) {
          if (response && response.view) {
            $('#bench_application_show_right_bar').html(response.view);
          } else if (response && response.message) {
            $('#bench_application_show_right_bar').html('<div class="p-8 text-center text-danger">' + response.message + '</div>');
          }
        }
      },
      error: function(xhr, status) {
        if (status !== 'abort' && thisSeq === currentBenchDrawerSeq) {
          $('#bench_application_show_right_bar').html('<div class="p-8 text-center text-danger">Failed to load candidate details. Please try again.</div>');
        }
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

  // GOD-Level Candidate Drawer Opening Engine
  function openMetronicDrawer(drawerId, toggleId) {
      drawerId = drawerId || 'kt_activitiesApplication';
      toggleId = toggleId || ('#' + drawerId + '_toggle');
      var drawerEl = document.getElementById(drawerId);
      if (!drawerEl) return;

      // 1. Official Metronic 8 KTDrawer instance show()
      var drawer = (typeof KTDrawer !== 'undefined') ? KTDrawer.getInstance(drawerEl) : null;
      if (drawer) {
          drawer.show();
          return;
      }

      // 2. Trigger toggle if registered in DOM
      var $toggle = $(toggleId);
      if ($toggle.length) {
          $toggle.trigger('click');
      }

      // 3. Guaranteed DOM fallback: ensure Metronic drawer classes and visibility are applied
      if (!drawerEl.classList.contains('drawer')) {
          drawerEl.classList.add('drawer', 'drawer-end');
      }
      drawerEl.classList.add('drawer-on');
      drawerEl.style.transform = 'none';
      drawerEl.style.zIndex = '999999';

      // 4. Ensure overlay backdrop is present
      if (!document.querySelector('.drawer-overlay')) {
          var overlay = document.createElement('div');
          overlay.className = 'drawer-overlay custom-cand-drawer-overlay';
          overlay.style.zIndex = '999998';
          overlay.onclick = function() {
              closeMetronicDrawer(drawerId);
          };
          document.body.appendChild(overlay);
      }
  }

  function closeMetronicDrawer(drawerId) {
      drawerId = drawerId || 'kt_activitiesApplication';
      if (drawerId === 'kt_activitiesApplication') {
          window.__activeCandidateDrawerId = null;
      }
      var drawerEl = document.getElementById(drawerId);
      if (drawerEl) {
          var drawer = (typeof KTDrawer !== 'undefined') ? KTDrawer.getInstance(drawerEl) : null;
          if (drawer) {
              drawer.hide();
          } else {
              drawerEl.classList.remove('drawer-on');
          }
      }
      $('.custom-cand-drawer-overlay').remove();
  }

  $(document).on('click', '#kt_activitiesApplication_close, #kt_client_activitiesApplication_close', function() {
      closeMetronicDrawer('kt_activitiesApplication');
      closeMetronicDrawer('kt_client_activitiesApplication');
  });

  // Intent: V4/V5 Zero-Read-Path Candidate Drawer Engine (L1 Browser RAM Cache + L2 Scoped IndexedDB + Cross-Tab Lifecycle)
  window.__candidateDrawerCache = window.__candidateDrawerCache || new Map();
  window.__candidateSensitiveMap = window.__candidateSensitiveMap || new Map();
  window.__candidateDrawerInFlight = window.__candidateDrawerInFlight || new Set();
  var drawerAbortControllers = new Map();

  @php
    $atsTenantId = auth()->check() ? (int) auth()->user()->company_id : 0;
    $atsUserId   = auth()->check() ? (int) auth()->user()->id : 0;
    $atsAuthContextHash = auth()->check() ? (new \App\Services\CandidateDrawerSecurityService())->getAuthContextForUser(auth()->user())['auth_context_hash'] ?? '' : '';
  @endphp
  window.__atsAuthContextHash = "{{ $atsAuthContextHash }}";

  // Phase 5: L2 Scoped IndexedDB Cache Engine (LRU cap 200 items, scoped to tenant & user)
  var AtsCandidateL2DB = (function() {
      var DB_NAME = 'jero_ats_cache_{{ $atsTenantId }}_{{ $atsUserId }}';
      var DB_VERSION = 1;
      var STORE_NAME = 'candidate_drawers';
      var MAX_ITEMS = 200;
      var dbInstance = null;

      function getDB() {
          if (dbInstance) return Promise.resolve(dbInstance);
          return new Promise(function(resolve) {
              if (!window.indexedDB) {
                  resolve(null);
                  return;
              }
              try {
                  var request = window.indexedDB.open(DB_NAME, DB_VERSION);
                  request.onupgradeneeded = function(e) {
                      var db = e.target.result;
                      if (!db.objectStoreNames.contains(STORE_NAME)) {
                          var store = db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                          store.createIndex('timestamp', 'timestamp', { unique: false });
                      }
                  };
                  request.onsuccess = function(e) {
                      dbInstance = e.target.result;
                      resolve(dbInstance);
                  };
                  request.onerror = function() {
                      resolve(null);
                  };
              } catch(err) {
                  resolve(null);
              }
          });
      }

      return {
          get: function(id) {
              return getDB().then(function(db) {
                  if (!db) return null;
                  return new Promise(function(resolve) {
                      try {
                          var tx = db.transaction(STORE_NAME, 'readonly');
                          var store = tx.objectStore(STORE_NAME);
                          var req = store.get(String(id));
                          req.onsuccess = function() {
                              var res = req.result;
                              resolve(res ? res.payload : null);
                          };
                          req.onerror = function() { resolve(null); };
                      } catch(e) {
                          resolve(null);
                      }
                  });
              });
          },
          set: function(id, payload) {
              return getDB().then(function(db) {
                  if (!db) return;
                  try {
                      var tx = db.transaction(STORE_NAME, 'readwrite');
                      var store = tx.objectStore(STORE_NAME);
                      store.put({
                          id: String(id),
                          payload: payload,
                          timestamp: Date.now()
                      });
                      var countReq = store.count();
                      countReq.onsuccess = function() {
                          if (countReq.result > MAX_ITEMS) {
                              var index = store.index('timestamp');
                              var cursorReq = index.openCursor();
                              var deleted = 0;
                              var toDelete = countReq.result - MAX_ITEMS;
                              cursorReq.onsuccess = function(e) {
                                  var cursor = e.target.result;
                                  if (cursor && deleted < toDelete) {
                                      cursor.delete();
                                      deleted++;
                                      cursor.continue();
                                  }
                              };
                          }
                      };
                  } catch(e) {}
              });
          },
          delete: function(id) {
              return getDB().then(function(db) {
                  if (!db) return;
                  try {
                      var tx = db.transaction(STORE_NAME, 'readwrite');
                      var store = tx.objectStore(STORE_NAME);
                      store.delete(String(id));
                  } catch(e) {}
              });
          },
          clear: function() {
              return getDB().then(function(db) {
                  if (!db) return;
                  try {
                      var tx = db.transaction(STORE_NAME, 'readwrite');
                      var store = tx.objectStore(STORE_NAME);
                      store.clear();
                  } catch(e) {}
              });
          }
      };
  })();
  window.__atsCandidateL2DB = AtsCandidateL2DB;

  // Cross-Tab Lifecycle Synchronization via BroadcastChannel
  var candidateBroadcastChannel = (typeof window.BroadcastChannel !== 'undefined') ?
      new BroadcastChannel('jero_ats_candidate_lifecycle_{{ $atsTenantId }}') : null;

  if (candidateBroadcastChannel) {
      candidateBroadcastChannel.onmessage = function(e) {
          if (!e || !e.data) return;
          if (e.data.type === 'invalidate' && e.data.candidate_id) {
              var cid = String(e.data.candidate_id);
              if (window.__candidateDrawerCache) window.__candidateDrawerCache.delete(cid);
              if (window.__atsCandidateL2DB) window.__atsCandidateL2DB.delete(cid);
          } else if (e.data.type === 'purge') {
              if (window.__candidateDrawerCache) window.__candidateDrawerCache.clear();
              if (window.__atsCandidateL2DB) window.__atsCandidateL2DB.clear();
          } else if (e.data.type === 'match_score_updated' && window.JWRealtimeSync) {
              window.JWRealtimeSync.updateMatchScore(e.data.candidate_id, e.data.wise_ids, e.data.score, false);
          } else if (e.data.type === 'recruiter_note_updated' && window.JWRealtimeSync) {
              window.JWRealtimeSync.updateRecruiterNote(e.data.candidate_id, e.data.note, false);
          } else if (e.data.type === 'tracker_stage_updated' && window.JWRealtimeSync) {
              window.JWRealtimeSync.updateTrackerStage(e.data.candidate_id, e.data.wise_ids, e.data.new_stage_id, e.data.old_stage_id, e.data.job_id, false);
          } else if (e.data.type === 'SECURITY_CONTEXT_CHANGED' || e.data.event === 'SECURITY_CONTEXT_CHANGED') {
              // Invariant: Mid-Session Immediate Salary Redaction Protocol (docs/drawer_candidate.md)
              if (window.__candidateSensitiveMap) {
                  window.__candidateSensitiveMap.clear();
              }
              // Redact salary and compensation elements in currently open drawer DOM immediately
              $('#application_show_right_bar').find('.candidate-salary-field, [data-salary-field], .salary-item, .ctc-display, .current-ctc, .expected-ctc, .offer-amount').each(function() {
                  $(this).text('[Confidential]');
              });
              if (e.data.auth_context_hash) {
                  window.__atsAuthContextHash = e.data.auth_context_hash;
              }
              // Invalidate old IndexedDB partitions
              if (window.__atsCandidateL2DB) {
                  window.__atsCandidateL2DB.clear();
              }
          }
      };
  }

  // Realtime Security Context listener via Laravel Echo / Reverb
  if (typeof window.Echo !== 'undefined' && {{ $atsTenantId }} > 0) {
      try {
          window.Echo.private('jero_ats_candidate_lifecycle_{{ $atsTenantId }}')
              .listen('.SECURITY_CONTEXT_CHANGED', function(data) {
                  if (candidateBroadcastChannel) {
                      candidateBroadcastChannel.postMessage(data);
                  }
                  if (window.__candidateSensitiveMap) window.__candidateSensitiveMap.clear();
                  $('#application_show_right_bar').find('.candidate-salary-field, [data-salary-field], .salary-item, .ctc-display, .current-ctc, .expected-ctc, .offer-amount').text('[Confidential]');
                  if (data && data.auth_context_hash) window.__atsAuthContextHash = data.auth_context_hash;
                  if (window.__atsCandidateL2DB) window.__atsCandidateL2DB.clear();
              });
      } catch(err) {}
  }

  window.__candidateDrawerPromises = window.__candidateDrawerPromises || new Map();
  window.__activeCandidateDrawerId = window.__activeCandidateDrawerId || null;

  function replenishDrawerCsrfTokens() {
      var activeCsrf = $('meta[name="csrf-token"]').attr('content');
      if (activeCsrf) {
          $('#application_show_right_bar').find('input[name="_token"]').val(activeCsrf);
      }
  }

  function getCandidateDrawerLoader() {
      return '<div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height: 380px;">' +
             '  <div class="spinner-border text-primary mb-4" role="status" style="width: 2.8rem; height: 2.8rem;"></div>' +
             '  <div class="text-gray-800 fw-bold fs-5 mb-1">Loading Candidate Profile…</div>' +
             '  <div class="text-muted fs-7">Retrieving verified application details</div>' +
             '</div>';
  }

  function renderCandidateDrawerView(response, id) {
      if (id) {
          window.__activeCandidateDrawerId = String(id);
      }
      if (response && response.view) {
          $('#application_show_right_bar').html(response.view);
          replenishDrawerCsrfTokens();
          if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
      } else if (response && response.status === 'error') {
          $('#application_show_right_bar').html(
              '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
              '  <div class="text-danger fw-bold fs-4 mb-2">Notice</div>' +
              '  <div class="text-gray-700 fs-6">' + (response.message || 'Candidate details could not be loaded.') + '</div>' +
              '</div>'
          );
      } else if (typeof response === 'string' && response.trim() !== '') {
          $('#application_show_right_bar').html(response);
          replenishDrawerCsrfTokens();
      }
  }

  function fetchCandidateDrawerPayload(id, currentSlug, callback) {
      var sid = String(id);
      if (window.__candidateDrawerPromises.has(sid)) {
          var existingPromise = window.__candidateDrawerPromises.get(sid);
          if (callback) {
              existingPromise.then(function(res) { callback(null, res); }).catch(function(err) { callback(err); });
          }
          return existingPromise;
      }

      var currentPage = $('.custom-pagination .page-link.active').data('page') || 1;
      var url = "{{ route('service_company.job-applications.show',':id') }}?slug=" + encodeURIComponent(currentSlug || '') + "&currentPage=" + currentPage;
      url = url.replace(':id', sid);

      var controller = (typeof AbortController !== 'undefined') ? new AbortController() : null;
      if (controller) drawerAbortControllers.set(sid, controller);

      window.__candidateDrawerInFlight.add(sid);

      var promise = new Promise(function(resolve, reject) {
          $.ajax({
              type: 'GET',
              url: url,
              signal: controller ? controller.signal : undefined,
              success: function(response) {
                  window.__candidateDrawerInFlight.delete(sid);
                  window.__candidateDrawerPromises.delete(sid);
                  drawerAbortControllers.delete(sid);
                  if (response && (response.view || typeof response === 'string')) {
                      window.__candidateDrawerCache.set(sid, response);
                      if (window.__atsCandidateL2DB) {
                          window.__atsCandidateL2DB.set(sid, response);
                      }
                  }
                  if (callback) callback(null, response);
                  resolve(response);
              },
              error: function(xhr, status, error) {
                  window.__candidateDrawerInFlight.delete(sid);
                  window.__candidateDrawerPromises.delete(sid);
                  drawerAbortControllers.delete(sid);
                  if (callback) callback(xhr);
                  reject(xhr);
              }
          });
      });

      window.__candidateDrawerPromises.set(sid, promise);
      return promise;
  }

  window.invalidateCandidateDrawerCache = function(candidateId, broadcast) {
      if (!candidateId) return;
      var cid = String(candidateId);
      if (window.__activeCandidateDrawerId === cid) {
          window.__activeCandidateDrawerId = null;
      }
      if (window.__candidateDrawerCache) {
          window.__candidateDrawerCache.delete(cid);
      }
      if (window.__atsCandidateL2DB) {
          window.__atsCandidateL2DB.delete(cid);
      }
      if (broadcast !== false && candidateBroadcastChannel) {
          try {
              candidateBroadcastChannel.postMessage({ type: 'invalidate', candidate_id: cid });
          } catch(e) {}
      }
  };

  window.purgeCandidateDrawerCaches = function(broadcast) {
      window.__activeCandidateDrawerId = null;
      if (window.__candidateDrawerCache) {
          window.__candidateDrawerCache.clear();
      }
      if (window.__atsCandidateL2DB) {
          window.__atsCandidateL2DB.clear();
      }
      if (broadcast !== false && candidateBroadcastChannel) {
          try {
              candidateBroadcastChannel.postMessage({ type: 'purge' });
          } catch(e) {}
      }
  };

  // Unified Real-Time Synchronization Engine (10K Concurrency / Zero Stale Data / Client-First)
  window.JWRealtimeSync = {
      getBadgeStyle: function(pct) {
          var num = parseInt(pct, 10);
          if (isNaN(num)) num = 0;
          if (num >= 85) {
              return { bg: '#dcfce7', color: '#16a34a', border: '#bbf7d0', cls: 'badge-light-success' };
          } else if (num >= 50) {
              return { bg: '#fef3c7', color: '#d97706', border: '#fde68a', cls: 'badge-light-warning' };
          } else {
              return { bg: '#fee2e2', color: '#dc2626', border: '#fecaca', cls: 'badge-light-danger' };
          }
      },

      updateMatchScore: function(candidateId, wiseIds, score, broadcast) {
          if (!candidateId && (!wiseIds || !wiseIds.length)) return;
          var num = parseInt(score, 10);
          if (isNaN(num)) num = 0;
          var style = this.getBadgeStyle(num);
          var cid = candidateId ? String(candidateId) : null;
          var wids = Array.isArray(wiseIds) ? wiseIds.map(String) : (wiseIds ? [String(wiseIds)] : []);

          // 1. Update row match badges on all table lists (App Tracker, Client App Tracker, CV Tracker)
          var rowBadges = $();
          if (cid) {
              rowBadges = rowBadges.add($('.jw-row-match-badge[data-candidate-id="' + cid + '"]'));
          }
          wids.forEach(function(wid) {
              rowBadges = rowBadges.add($('.jw-row-match-badge[data-wise-id="' + wid + '"]'));
          });

          rowBadges.each(function() {
              var $badge = $(this);
              $badge.text('Match ' + num + '%');
              $badge.attr('data-match-pct', num);
              $badge.css({
                  'background': style.bg,
                  'color': style.color,
                  'font-weight': '600'
              });
          });

          // 2. Update checkboxes data-match attribute
          if (cid) {
              $('input.chk_list_item[data-candidate-id="' + cid + '"], input.chk_list_item[value="' + cid + '"]').attr('data-match', num);
          }
          wids.forEach(function(wid) {
              $('input.chk_list_item[data-wise-id="' + wid + '"], input.chk_list_item[data-wiseId="' + wid + '"]').attr('data-match', num);
          });

          // 3. Update candidate drawer match badge
          var drawerBadges = $('.jw-drawer-match-badge');
          if (cid) {
              drawerBadges = drawerBadges.filter('[data-candidate-id="' + cid + '"], [data-candidate-id=""]');
          }
          drawerBadges.each(function() {
              var $db = $(this);
              $db.text(num + '% Match');
              $db.attr('data-match-pct', num);
              $db.removeClass('badge-light-success badge-light-warning badge-light-danger').addClass(style.cls);
          });

          // 4. Invalidate ForwardResumeManager attachment and recipient cache
          if (window.ForwardResumeManager) {
              window.ForwardResumeManager._compiledAttachments = {};
              window.ForwardResumeManager._recipientsCache = null;
          }

          // 5. Broadcast to other recruiter tabs if local action
          if (broadcast !== false && candidateBroadcastChannel) {
              try {
                  candidateBroadcastChannel.postMessage({
                      type: 'match_score_updated',
                      candidate_id: cid,
                      wise_ids: wids,
                      score: num
                  });
              } catch(e) {}
          }
      },

      updateRecruiterNote: function(candidateId, note, broadcast) {
          if (!candidateId) return;
          var cid = String(candidateId);
          var cleanNote = (note || '').trim();

          // 1. Update Candidate Drawer display
          if (cleanNote) {
              if ($('.po-recruiter-note-text').length) {
                  $('.po-recruiter-note-text').html(cleanNote).show();
              } else if ($('.po-no-notes').length) {
                  $('.po-no-notes').replaceWith('<div class="po-recruiter-note-text jw-recruiter-note-display" data-candidate-id="' + cid + '" style="font-size:12px;line-height:1.7;color:#475569;">' + cleanNote + '</div>');
              }
              $('.po-no-notes').hide();
          } else {
              $('.po-recruiter-note-text').hide();
              if ($('.po-no-notes').length === 0) {
                  $('.po-recruiter-note-text').after('<p class="po-no-notes jw-recruiter-note-display" data-candidate-id="' + cid + '" style="font-size:12px;color:#94a3b8;font-style:italic;margin:0;">No recruiter comments added yet.</p>');
              }
              $('.po-no-notes').show();
          }

          // 2. Update editor if open
          if ($('#jw_recruiter_note_editor').length && !$('#jw_recruiter_note_editor').is(':focus')) {
              $('#jw_recruiter_note_editor').html(cleanNote);
          }

          // 3. Invalidate ForwardResumeManager attachments
          if (window.ForwardResumeManager) {
              window.ForwardResumeManager._compiledAttachments = {};
              window.ForwardResumeManager._recipientsCache = null;
          }

          // 4. Invalidate drawer cache for this candidate so future drawer opens fetch fresh comments
          window.invalidateCandidateDrawerCache(cid, false);

          // 5. Broadcast to other recruiter tabs if local action
          if (broadcast !== false && candidateBroadcastChannel) {
              try {
                  candidateBroadcastChannel.postMessage({
                      type: 'recruiter_note_updated',
                      candidate_id: cid,
                      note: cleanNote
                  });
              } catch(e) {}
          }
      },

      updateTrackerStage: function(candidateId, wiseIds, newStageId, oldStageId, jobId, broadcast) {
          var cid = candidateId ? String(candidateId) : null;
          var wids = Array.isArray(wiseIds) ? wiseIds.map(String) : (wiseIds ? [String(wiseIds)] : []);
          var nStage = parseInt(newStageId, 10);
          var oStage = parseInt(oldStageId, 10);

          // 1. If on App Tracker: refresh counters
          if (typeof window.countder === 'function') {
              window.countder();
          }

          // 2. If on Client App Tracker: adjust stage count badges
          if (nStage && oStage && nStage !== oStage) {
              var $oldEl = $('#bg_' + oStage);
              var $newEl = $('#bg_' + nStage);
              if ($oldEl.length) {
                  var oCnt = Math.max(0, parseInt($oldEl.text(), 10) - 1);
                  $oldEl.text(oCnt);
              }
              if ($newEl.length) {
                  var nCnt = (parseInt($newEl.text(), 10) || 0) + 1;
                  $newEl.text(nCnt);
              }
          }

          // 3. If on CV Tracker: refresh hero stats
          if (typeof window.updateCvHeroStats === 'function' && typeof window.fetchCvHeroStats === 'function') {
              window.fetchCvHeroStats();
          }

          // 4. Invalidate drawer cache for candidate & wise IDs
          if (cid) window.invalidateCandidateDrawerCache(cid, false);
          wids.forEach(function(wid) {
              window.invalidateCandidateDrawerCache(wid, false);
          });

          // 5. Broadcast to other tabs
          if (broadcast !== false && candidateBroadcastChannel) {
              try {
                  candidateBroadcastChannel.postMessage({
                      type: 'tracker_stage_updated',
                      candidate_id: cid,
                      wise_ids: wids,
                      new_stage_id: nStage,
                      old_stage_id: oStage,
                      job_id: jobId
                  });
              } catch(e) {}
          }
      }
  };

  // Intent: Phase 3 Real-time Reverb WebSocket synchronization across recruiter tabs
  @if(auth()->check() && auth()->user()->company_id)
  $(document).ready(function() {
      if (typeof window.Echo !== 'undefined') {
          window.Echo.private('company.{{ (int) auth()->user()->company_id }}')
              .listen('.candidate.drawer.updated', function(e) {
                  if (!e) return;
                  if (e.candidate_id) {
                      window.invalidateCandidateDrawerCache(e.candidate_id, false);
                  }
                  if (e.wise_ids && Array.isArray(e.wise_ids)) {
                      e.wise_ids.forEach(function(wid) {
                          window.invalidateCandidateDrawerCache(wid, false);
                      });
                  }
                  if (e.skill_match !== null && e.skill_match !== undefined && window.JWRealtimeSync) {
                      window.JWRealtimeSync.updateMatchScore(e.candidate_id, e.wise_ids, e.skill_match, false);
                  }
                  if (e.recruiter_note !== null && e.recruiter_note !== undefined && window.JWRealtimeSync) {
                      window.JWRealtimeSync.updateRecruiterNote(e.candidate_id, e.recruiter_note, false);
                  }
                  if (e.stage_id !== null && e.stage_id !== undefined && window.JWRealtimeSync) {
                      window.JWRealtimeSync.updateTrackerStage(e.candidate_id, e.wise_ids, e.stage_id, e.old_stage_id, e.job_id, false);
                  }
              })
              .listen('.job.skills.updated', function(e) {
                  if (e && e.candidate_ids && Array.isArray(e.candidate_ids)) {
                      e.candidate_ids.forEach(function(cid) {
                          window.invalidateCandidateDrawerCache(cid, false);
                      });
                  }
                  if (e && e.wise_ids && Array.isArray(e.wise_ids)) {
                      e.wise_ids.forEach(function(wid) {
                          window.invalidateCandidateDrawerCache(wid, false);
                      });
                  }
                  if (window.ForwardResumeManager) {
                      window.ForwardResumeManager._compiledAttachments = {};
                      window.ForwardResumeManager._recipientsCache = null;
                  }
              });
      }
  });
  @endif

  // Auto-invalidate RAM & IndexedDB caches and sync DOM when candidate mutations succeed
  $(document).ajaxSuccess(function(event, xhr, settings) {
      if (!settings || !settings.url) return;
      var url = settings.url;
      if (url.indexOf('skill-experience') !== -1 || url.indexOf('statusupdate') !== -1 || 
          url.indexOf('stage_submit') !== -1 || url.indexOf('job-question-update') !== -1 || 
          url.indexOf('recruiter-note-update') !== -1 || url.indexOf('slots-update') !== -1 ||
          url.indexOf('job-applications/update') !== -1 || url.indexOf('quick_upload_resume') !== -1) {
          var activeCoreId = $('#candidate_core_id').val() || $('#appId').val() || $('#application_id').val() || $('input[name="appId"]').val();
          var activeWiseId = $('#candidate_wise_id').val() || window.__activeCandidateDrawerId;
          if (activeCoreId) {
              window.invalidateCandidateDrawerCache(activeCoreId);
          }
          if (activeWiseId) {
              window.invalidateCandidateDrawerCache(activeWiseId);
          }

          if (xhr && xhr.responseJSON) {
              var res = xhr.responseJSON;
              if (res.skill_match !== undefined && window.JWRealtimeSync) {
                  window.JWRealtimeSync.updateMatchScore(res.candidate_id || activeCoreId, res.wise_ids || (activeWiseId ? [activeWiseId] : []), res.skill_match, true);
              }
              if (res.recruiter_note !== undefined && window.JWRealtimeSync) {
                  window.JWRealtimeSync.updateRecruiterNote(res.candidate_id || activeCoreId, res.recruiter_note, true);
              }
              if ((url.indexOf('stage_submit') !== -1 || url.indexOf('client_stage_submit') !== -1) && window.JWRealtimeSync) {
                  var sId = $('#master_status').val() || $('select[name="master_status"]').val();
                  window.JWRealtimeSync.updateTrackerStage(activeCoreId, activeWiseId ? [activeWiseId] : [], sId, null, null, true);
              }
          }
      }
      if (url.indexOf('jobs/update') !== -1 || url.indexOf('jobs/store') !== -1 ||
          url.indexOf('rec_candidate_assign') !== -1 || url.indexOf('copy_current_selected_applicant') !== -1 || 
          url.indexOf('add_to_list') !== -1) {
          if (window.ForwardResumeManager) {
              window.ForwardResumeManager._compiledAttachments = {};
              window.ForwardResumeManager._recipientsCache = null;
          }
          window.purgeCandidateDrawerCaches(false);
      }
  });

  // Security Lifecycle: Invalidate all caches on logout or 401/419 auth errors
  $(document).on('click', 'a[href*="logout"]', function() {
      window.purgeCandidateDrawerCaches(true);
  });

  $(document).ajaxError(function(event, jqXHR) {
      if (jqXHR.status === 401 || jqXHR.status === 419) {
          window.purgeCandidateDrawerCaches(true);
      }
  });

  // Note: Hover prefetch / pre-warming intentionally removed to prevent unwanted background network I/O and PII exposure

  $(document).on('click', '.show-application-detail, .custom-show-application-detail', async function(e){
      // Prevent bubbling so nested candidate containers don't trigger duplicate execution
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();

      const connected = await isInternetAvailable();
      if (!connected) {
          Swal.fire({
              title: "No Internet",
              text: "You are currently offline. Please check your network connection.",
              icon: "warning",
              confirmButtonText: "Ok, got it!",
              customClass: { confirmButton: "btn btn-warning" }
          });
          return;
      }

      var id = $(this).attr('data-row-id') || $(this).data('row-id') ||
               $(this).attr('data-wise-id') || $(this).data('wise-id') ||
               $(this).attr('data-id') || $(this).data('id') ||
               $(this).closest('[data-row-id]').attr('data-row-id') ||
               $(this).closest('[data-wise-id]').attr('data-wise-id') ||
               $(this).attr('rel');

      if (!id) {
          console.warn('Candidate drawer: Unable to determine candidate ID from element', this);
          return;
      }

      id = String(id);

      // If this exact candidate is already displayed in the drawer and drawer is open, keep it open
      if (window.__activeCandidateDrawerId === id && $('#kt_activitiesApplication').hasClass('drawer-on')) {
          openMetronicDrawer('kt_activitiesApplication');
          return;
      }

      openMetronicDrawer('kt_activitiesApplication');

      // 1. Check L1 Browser RAM Cache (Zero-Read-Path Instant Render: 0ms, 0 HTTP, 0 MySQL, 0 ES)
      if (window.__candidateDrawerCache.has(id)) {
          var cachedResponse = window.__candidateDrawerCache.get(id);
          renderCandidateDrawerView(cachedResponse, id);
          return;
      }

      // 2. Check L2 Scoped IndexedDB Cache (<15ms Instant Render across page loads)
      if (window.__atsCandidateL2DB) {
          var l2Cached = await window.__atsCandidateL2DB.get(id);
          if (l2Cached) {
              window.__candidateDrawerCache.set(id, l2Cached);
              renderCandidateDrawerView(l2Cached, id);
              return;
          }
      }

      // 3. Switching to a candidate not in cache: clear previous candidate DOM to prevent ghost flash
      if (window.__activeCandidateDrawerId !== id) {
          window.__activeCandidateDrawerId = null;
          $('#application_show_right_bar').html(getCandidateDrawerLoader());
      }

      // 4. Fetch or attach to in-flight promise
      try {
          var response = await fetchCandidateDrawerPayload(id, "{{ $currentSlug ?? '' }}");
          renderCandidateDrawerView(response, id);
      } catch(err) {
          var msg = (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Unable to connect to server. Please refresh and try again.';
          $('#application_show_right_bar').html(
              '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
              '  <div class="text-danger fw-bold fs-4 mb-2">Error Loading Profile</div>' +
              '  <div class="text-gray-700 fs-6">' + msg + '</div>' +
              '</div>'
          );
      }
  });

  $(document).on('click', '.website-show-application-detail', async function(e){
      e.preventDefault();
      const connected = await isInternetAvailable();
      if (!connected) {
          Swal.fire({
              title: "No Internet",
              text: "You are currently offline. Please check your network connection.",
              icon: "warning",
              confirmButtonText: "Ok, got it!",
              customClass: { confirmButton: "btn btn-warning" }
          });
          return;
      }

      var id = $(this).attr('data-row-id') || $(this).data('row-id') ||
               $(this).attr('data-wise-id') || $(this).data('wise-id') ||
               $(this).attr('data-id') || $(this).data('id') ||
               $(this).closest('[data-row-id]').attr('data-row-id') ||
               $(this).attr('rel');

      if (!id) return;

      openMetronicDrawer('kt_activitiesApplication');

      var currentPage = $('.custom-pagination .page-link.active').data('page') || 1;
      var url = "{{ route('service_company.job-applications.websiteshow',':id') }}?slug={{ $currentSlug }}&currentPage=" + currentPage;
      url = url.replace(':id', id);

      $.ajax({
          type: 'GET',
          url: url,
          beforeSend: function() {
              $('#application_show_right_bar').html(getCandidateDrawerLoader());
          },
          success: function(response) {
              if (response && response.view) {
                  $('#application_show_right_bar').html(response.view);
                  replenishDrawerCsrfTokens();
                  if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
              } else if (response && response.status === 'error') {
                  $('#application_show_right_bar').html(
                      '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                      '  <div class="text-danger fw-bold fs-4 mb-2">Notice</div>' +
                      '  <div class="text-gray-700 fs-6">' + (response.message || 'Candidate details could not be loaded.') + '</div>' +
                      '</div>'
                  );
              }
          },
          error: function(xhr) {
              var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to connect to server. Please try again.';
              $('#application_show_right_bar').html(
                  '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                  '  <div class="text-danger fw-bold fs-4 mb-2">Error Loading Profile</div>' +
                  '  <div class="text-gray-700 fs-6">' + msg + '</div>' +
                  '</div>'
              );
          }
      });
  });

  $(document).on('click', '.client-show-application-detail', async function(e){
      e.preventDefault();
      const connected = await isInternetAvailable();
      if (!connected) {
          Swal.fire({
              title: "No Internet",
              text: "You are currently offline. Please check your network connection.",
              icon: "warning",
              confirmButtonText: "Ok, got it!",
              customClass: { confirmButton: "btn btn-warning" }
          });
          return;
      }

      var id = $(this).attr('data-row-id') || $(this).data('row-id') ||
               $(this).attr('data-wise-id') || $(this).data('wise-id') ||
               $(this).attr('data-id') || $(this).data('id') ||
               $(this).closest('[data-row-id]').attr('data-row-id') ||
               $(this).attr('rel');

      if (!id) return;

      openMetronicDrawer('kt_client_activitiesApplication');

      var currentPage = $('.custom-pagination .page-link.active').data('page') || 1;
      var url = "{{ route('service_company.job-applications.clientshow',':id') }}?currentPage=" + currentPage;
      url = url.replace(':id', id);

      $.ajax({
          type: 'GET',
          url: url,
          beforeSend: function() {
              $('#client_application_show_right_bar').html(getCandidateDrawerLoader());
          },
          success: function(response) {
              if (response && response.view) {
                  $('#client_application_show_right_bar').html(response.view);
                  if (typeof KTMenu !== 'undefined') KTMenu.createInstances();
              } else if (response && response.status === 'error') {
                  $('#client_application_show_right_bar').html(
                      '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                      '  <div class="text-danger fw-bold fs-4 mb-2">Notice</div>' +
                      '  <div class="text-gray-700 fs-6">' + (response.message || 'Client candidate details could not be loaded.') + '</div>' +
                      '</div>'
                  );
              }
          },
          error: function(xhr) {
              var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to connect to server. Please try again.';
              $('#client_application_show_right_bar').html(
                  '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                  '  <div class="text-danger fw-bold fs-4 mb-2">Error Loading Profile</div>' +
                  '  <div class="text-gray-700 fs-6">' + msg + '</div>' +
                  '</div>'
              );
          }
      });
  });   
        $('body').on('click', '#app_interview_schedule', function(){
         			var id    		=	$(this).attr('rel');
         			var url 		=	"{{ route('service_company.job-applications.create-schedule',':id') }}";
         			url     		=	url.replace(':id', id);
         			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
              (function(){ var _el=document.getElementById('interview_schedule_drawer'); if(!_el)return; if(!_el.classList.contains('drawer'))_el.classList.add('drawer','drawer-end'); _el.classList.add('drawer-on'); _el.style.transform='none'; })();
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
              (function(){ var _el=document.getElementById('interview_schedule_drawer'); if(!_el)return; if(!_el.classList.contains('drawer'))_el.classList.add('drawer','drawer-end'); _el.classList.add('drawer-on'); _el.style.transform='none'; })();
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



               

                
    
    async function isInternetAvailable() {
        if (typeof navigator !== 'undefined' && 'onLine' in navigator) {
            return navigator.onLine;
        }
        return true;
    }




   setTimeout(function() {
        $('.noCalendar').each(function(index) {
            $(this).addClass('calendarHasTime' + (index + 1));
        });
   }, 1500);
      
</script>
</body>
</html>