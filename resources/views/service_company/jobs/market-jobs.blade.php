

@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    @media only screen and (min-width: 900px) and (max-width: 2560px){
        .table td, .table th, .table tr
        {width: max-content!important;}
        .table > :not(caption) > * > *
        { padding: 0rem; }
        td:nth-child(n+2),
        th:nth-child(n+2)
        { width: revert!important;}
    }
    .classHide{
     display: none;
    }
    .table-custom-class tbody tr {
        height: 82px; /* Adjust the height as per your requirement */
        position: relative;
    }
    .selectednotice {
        background-color: #007bff !important; /* Example color */
        color: white !important;
    }
    /* Bench Match box is clickable (opens the same bench-talents list as "Send Bench Request") */
    .bench-match-box.open-bench-talents {
        cursor: pointer;
        transition: background-color .15s ease, box-shadow .15s ease;
    }
    .bench-match-box.open-bench-talents:hover {
        background-color: #eef2ff;
        box-shadow: inset 0 0 0 1px #c7d2fe;
    }
    .bench-match-box.open-bench-talents:focus-visible {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
    }

</style>

<link href="{{ asset('demo/css/find-market-jobs.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

@endpush
@section('content')
@php	
     use App\Helper\GeneralMethod;
     
      $productAllowCompany = GeneralMethod::companiesType();
     
@endphp
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -60px;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

        <main class="main-content">
            <!-- Content Area -->
            <div class="content-area">
                

                    <!-- HERO -->
                    <div class="hero-section">
                        <div class="hero-left">
                            <div class="hero-title">
                            Find and Apply for Jobs
                            <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
                            </svg>
                            </div>
                            <div class="hero-sub">Discover verified job requirements posted by clients and apply as an agency</div>
                            <div class="stats-row">
                                <div class="stat-card">
                                    <div class="stat-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    </div>
                                    <div class="stat-info">
                                    <div class="stat-num">{{$all_jobs}}</div>
                                    <div class="stat-label">Total Jobs</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                    </div>
                                    <div class="stat-info">
                                    <div class="stat-num">{{$all_jobs-$market_place}}</div>
                                    <div class="stat-label">Agency Jobs</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon yellow">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    </div>
                                    <div class="stat-info">
                                    <div class="stat-num">{{$market_place}}</div>
                                    <div class="stat-label">Bench Talent Job</div>
                                    </div>
                                </div>
                                <div class="stat-card with-badge">
                                    <span class="new-badge">New</span>
                                    <div class="stat-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    </div>
                                    <div class="stat-info">
                                    <div class="stat-num">{{$newly_add}}</div>
                                    <div class="stat-label">Newly Added</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Illustration -->
                        <div class="illus-svg-wrap">
                            <svg viewBox="0 0 220 160" xmlns="http://www.w3.org/2000/svg" width="220" height="160">
                            <!-- Background floating card (left) -->
                            <rect x="0" y="30" width="90" height="60" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1"/>
                            <circle cx="16" cy="60" r="9" fill="#94A3B8" transform="translate(8,0)"/>
                            <rect x="34" y="52" width="46" height="6" rx="3" fill="#CBD5E1"/>
                            <rect x="34" y="62" width="32" height="4" rx="2" fill="#E2E8F0"/>
                            <rect x="34" y="70" width="22" height="4" rx="2" fill="#BFDBFE"/>
                            <!-- Stars -->
                            <text x="64" y="76" font-size="8" fill="#F59E0B">★★★★★</text>

                            <!-- Female figure -->
                            <ellipse cx="120" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                            <!-- Body -->
                            <rect x="108" y="90" width="24" height="38" rx="4" fill="#2563EB"/>
                            <!-- Head -->
                            <circle cx="120" cy="82" r="12" fill="#FBBF24"/>
                            <!-- Hair -->
                            <path d="M108 80 Q120 68 132 80" fill="#1E293B"/>
                            <rect x="108" y="78" width="4" height="14" rx="2" fill="#1E293B"/>
                            <!-- Collar -->
                            <path d="M113 90 L120 98 L127 90" fill="white" opacity=".6"/>
                            <!-- Arms -->
                            <rect x="100" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                            <rect x="130" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                            <!-- Legs -->
                            <rect x="112" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                            <rect x="121" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                            <!-- Shoes -->
                            <ellipse cx="115" cy="143" rx="5" ry="3" fill="#111827"/>
                            <ellipse cx="124" cy="143" rx="5" ry="3" fill="#111827"/>

                            <!-- Male figure -->
                            <ellipse cx="175" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                            <!-- Body -->
                            <rect x="163" y="90" width="24" height="38" rx="4" fill="#1E40AF"/>
                            <!-- Head -->
                            <circle cx="175" cy="82" r="12" fill="#D97706"/>
                            <!-- Hair -->
                            <path d="M163 78 Q175 68 187 78 Q185 72 175 70 Q165 72 163 78Z" fill="#1E293B"/>
                            <!-- Beard -->
                            <path d="M168 88 Q175 94 182 88 Q179 92 175 93 Q171 92 168 88Z" fill="#92400E" opacity=".7"/>
                            <!-- Collar -->
                            <path d="M168 90 L175 98 L182 90" fill="white" opacity=".6"/>
                            <!-- Tie -->
                            <path d="M175 94 L173 108 L175 110 L177 108 Z" fill="#EF4444"/>
                            <!-- Arms -->
                            <rect x="155" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                            <rect x="185" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                            <!-- Legs -->
                            <rect x="167" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                            <rect x="176" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                            <!-- Shoes -->
                            <ellipse cx="170" cy="143" rx="5" ry="3" fill="#111827"/>
                            <ellipse cx="179" cy="143" rx="5" ry="3" fill="#111827"/>

                            <!-- Handshake -->
                            <path d="M131 108 Q148 114 164 108" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" fill="none"/>
                            <circle cx="131" cy="108" r="4" fill="#FBBF24"/>
                            <circle cx="164" cy="108" r="4" fill="#FBBF24"/>

                            <!-- Floating review card (right) -->
                            <rect x="150" y="10" width="68" height="38" rx="8" fill="white" stroke="#E5E7EB" stroke-width="1"/>
                            <circle cx="162" cy="22" r="5" fill="#94A3B8"/>
                            <rect x="171" y="16" width="36" height="5" rx="2.5" fill="#CBD5E1"/>
                            <text x="171" y="34" font-size="9" fill="#F59E0B">★★★★★</text>
                            <!-- shield check floating -->
                            <circle cx="205" cy="58" r="13" fill="#DCFCE7" stroke="#86EFAC" stroke-width="1.5"/>
                            <path d="M199 58l4 4 7-7" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </svg>
                        </div>
                    </div>

                    <!-- FILTER BAR -->
                    <div class="filter-bar">
                        <div class="search-input-wrap">
                            <input class="search-input" name="inputJobSearch" id="kt_tagify_2" value="" type="text" placeholder="Search by skills..."/>
                        </div>
                        <button class="ai-match-btn" onclick="filtersdataget()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                            Search
                        </button>
                        
                        <div class="align-items-center">
                            <select class="form-select form-select-lg sortingfilterselect" data-hide-search="true" id="hiringtype" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdata()">
                                <option value="All">All Jobs</option>
                                <option value="2">Agency Jobs</option>
                                <option value="1">Bench Talent Jobs</option> 
                            </select>
                        </div>
                        <div class="align-items-center">
                            <select class="form-select form-select-lg sortingfilterselect" data-hide-search="true" id="defaultCurrency" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdata()">
                                <option value="">Currency</option>
                                <option value="0">INR</option>
                                <option value="1">EUR</option>
                                <option value="2">JPY</option>
                                <option value="3">GBP</option>
                                <option value="4">USD</option>
                                <option value="5">AED</option>
                            </select>
                        </div>
                        <div class="align-items-center">
                            <select class="form-select form-select-lg sortingfilterselect" data-hide-search="true" id="defaultMode" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdata()">
                                <option value="">Mode</option>
                                <option value="0">Annual</option>
                                <option value="1">Monthly</option>
                                <option value="2">Hourly</option>
                            </select>
                        </div>
                        <div class="more-filters-btn" data-toggle="modal" data-target="#apptrackerfilter">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
                            </svg>
                            More Filters
                        </div>
                    </div>

                    <!-- ACTIVE FILTER TAGS -->
                    <div class="active-filters" id="selectedFilters" style="display:none;"></div>    

                <!-- Job List -->
                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                <div class="job-list" id="marketJobLists"></div>

            </div>
            
            <div id="pagination"></div>
        </main>

        <!--Start Popup-->
        <div class="modal fade show" id="apptrackerfilter" tabindex="-1" style="">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Filter</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal" id="apptrackerfilter_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="apptrackerfilter_body_content" id="">
                    <div class="modal-body" style="padding: 2em;">
                        <form id="createSchedule" class="ajax-form" method="post">
                            <div class="section">
                                        <p class="section-label">1. Search Skills / Keywords</span></p> 
                                        {{-- <span class="required"></span> --}}

                                            <div class="suggestions-wrap search-input-wrap">
                                            <input class="search-input" name="kt_tagify_users" value="" placeholder="Search by skills..." id="kt_tagify_users" />
                                            </div>

                                            <p class="input-hint">Press <span>Enter</span> or <span>,</span> to add a keyword &nbsp;·&nbsp; Press <span>Backspace</span> to remove last</p>

                                            <div class="tags-row" id="popularTagsRow">
                                                <span class="label">Popular searches:</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="Java Developer">Java Developer</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value=".NET Developers">.NET Developers</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="SAP Consultants">SAP Consultants</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="AWS">AWS</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="React Developers">React Developers</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="Node.js">Node.js</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="Python Developer">Python Developer</span>
                                                <span class="pop-tag" onclick="addFromPopular(this)" data-value="DevOps Engineer">DevOps Engineer</span>
                                                <span class="pop-tag more">+ More</span>
                                            </div>
                            </div>


                            <div class="section">
                                <p class="section-label">2. Select the Country</p>
                                <div class="search-input-wrap">
                                    <input class="search-input" value="{{ @$default_loc }}" id="kt_tagify_location" placeholder="Enter your Location..."/>
                                </div>
                            </div>
                                
                            <div class="section">
                                <p class="section-label">3. Experience</p>
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                    <div class="col-lg-6">
                                        <label class="form-label required">Minimum years:</label>
                                        <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0" {{ (0==$min_exp) ? 'selected' : '' }}>Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ ($i==$min_exp) ? 'selected' : '' }}>{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                    
                                        </select>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label required">Maximum years:</label>
                                        <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0" {{ (0==$max_exp) ? '' : '' }}>Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ ($i==$max_exp) ? 'selected' : '' }}>{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                                
                                        </select>
                                        <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                    </div>
                                </div> 
                            </div>   
                                
                            <div class="section">
                                <p class="section-label">4. Salary</p>
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                    <div class="col-lg-12">
                                        <div class="row">

                                                        <div class="col-lg-4">
                                                            <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Currency</label>
                                                                    <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency">
                                                                    <option value="">Select currency</option>
                                                                    <option value="0" {{ (0==$min_salary_currency) ? 'selected' : '' }}>INR</option>
                                                                    <option value="1" {{ (1==$min_salary_currency) ? 'selected' : '' }}>EUR</option>
                                                                    <option value="2" {{ (2==$min_salary_currency) ? 'selected' : '' }}>JPY</option>
                                                                    <option value="3" {{ (3==$min_salary_currency) ? 'selected' : '' }}>GBP</option>
                                                                    <option value="4" {{ (4==$min_salary_currency) ? 'selected' : '' }}>USD</option>
                                                                    <option value="5" {{ (5==$min_salary_currency) ? 'selected' : '' }}>AED</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                <label class="form-label required">Mode</label>
                                                                    <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                                    <option value="">Select Mode</option>
                                                                    <option value="0" {{ (0==$min_sal_mode) ? 'selected' : '' }}>Annual</option>
                                                                    <option value="1" {{ (1==$min_sal_mode) ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="2" {{ (2==$min_sal_mode) ? 'selected' : '' }}>Hourly</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <div class="row"> 
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Minimum Salary:</label>
                                                                <input type="number" class="form-control form-control-lg form-control-solid" name="min_sal" id="min_sal" placeholder="Enter min salary" value="{{ $min_salary }}">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Maximum Salary:</label>
                                                                <input type="number" class="form-control form-control-lg form-control-solid" name="max_sal" id="max_sal" placeholder="Enter max salary" value="{{ $max_salary }}">
                                                            <div class="fv-plugins-message-container"></div>
                                                            <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div> 
                                                                <span class="form-text text-muted" id="salary_error" style="    color: red !important;text-align: center;"></span>                                                             
                                                            </div>
                                                        </div>

                                            
                                            

                                            


                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="section">
                                <p class="section-label">5. Hiring type</p>
                                <div class="d-flex flex-column mb-8">
                                <div class="row py-5 mb-3">
                                    <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="" id="app_int_outsource1" checked="&quot;checked&quot;">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource1"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">All</span> 
                                        </span> 
                                        </label>
                                    </div>
                                    <div class="col-lg-5">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource2">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource2"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime </span> 
                                        </span> 
                                        </label>
                                    </div>
                                    <div class="col-lg-5">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource3">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource3"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">Contract</span> 
                                        </span> 
                                        </label>
                                    </div>
                                </div>
                                </div>
                            </div>


                            <div class="d-flex flex-column mb-8 d-none">
                                <label class="form-label required">Activity</label>
                                <div class="row py-5 mb-3">
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked="&quot;checked&quot;">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity1"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">All</span> 
                                    </span> 
                                    </label>
                                    </div>
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity2"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">30 Days</span> 
                                    </span> 
                                    </label>
                                    </div>
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity3"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">60 Days</span> 
                                    </span> 
                                    </label>
                                    </div>
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity4"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">90 Days</span> 
                                    </span> 
                                    </label>
                                    </div>
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity5"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">180 Days</span> 
                                    </span> 
                                    </label>
                                    </div>
                                    <div class="col-lg-2">
                                    <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity6"> 
                                    <span class="d-block fw-bold text-start"> 
                                    <span class="text-dark-800 fw-bolder d-block fs-5">1 Year</span> 
                                    </span> 
                                    </label>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer flex-center">
                        <div class="div1">
                            <button type="button" id="" class="btn btn-secondary me-3" data-dismiss="modal">Close</button>
                            <button type="button" id="changetextupdate" class="btn btn-primary changetextupdate"> 
                            <span class="indicator-label">Submit</span></button>
                        </div>
                    </div>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/moment/moment.js?ver=1553620716" type="text/javascript"></script>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/multiselect/js/jquery.multi-select.js?ver=1553620716"></script>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js?ver=1553620716" type="text/javascript"></script>
                </div>
                </div>
            </div>
        </div>	
        <!--End Popup--> 

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->



<div id="kt_activitiesClientjob_toggle"></div> 
<div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
  <div class="card shadow-none rounded-0 w-100">
   
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client Jobs</h3>
        
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        
      </div>
     
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClientjob_close">
        
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span>
       
        </button>
      </div>
    </div>
    
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>
   
  </div>
</div>

<div id="kt_activitiesBT_toggle"></div>
<div id="kt_activitiesBT" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBT_toggle" data-kt-drawer-close="#kt_activitiesBT_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activitiesbt_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_bt_client">Bench Recommended Talents</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesBT_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                     </g>
                  </svg>
               </span>
            </button>
         </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="kt_activitiesBT_body"> </div>
   </div>
</div>



<div id="kt_activitiesHM_toggle"></div>
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_or_client">Direct Client Contact Person</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesHM_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                     </g>
                  </svg>
               </span>
            </button>
         </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
   </div>
</div>
<!--end::Content-->


<!--start terms::Content-->

<div id="kt_activitiesTermsFindjob_toggle"></div>
<div id="kt_activitiesTermsFindjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTermsFindjob_toggle" data-kt-drawer-close="#kt_activitiesTermsFindJob_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_or_client">Terms & Condition</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTermsFindJob_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
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
              <div class="card-body py-7" id="kt_activitiesTermsFindJob_body">

              </div>
            </div>
          </div>
        </div>

      </div>
   </div>
</div>

<!--end terms ::Content-->




@endsection

@push('footer-script') 
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

    <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
    <script>
        const inputElm = document.querySelector('#kt_tagify_users');
        const inputJobSearch = document.querySelector('#kt_tagify_2');

        const tagify = new Tagify(inputElm, {
            templates: {
                tag: function(tagData) {
                    tagData = tagData || {};
                    const value = tagData.value || '';
                    const isChecked = tagData.checked ? 'checked' : '';
                    return `
                <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                    <div>
                        <span class="star ${isChecked}">🟌</span>
                        <span class="tagify__tag-text">${value}</span>
                        <span style="width: 16px; height: 16px; border-radius: 50%; background: #A5B4FC !important; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #3730A3; line-height: 1; flex-shrink: 0; transition: background 0.12s;"><x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><span>
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

        // Add event listener for star click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('star')) {
                const tagElm = e.target.closest('.tagify__tag');
                const value = tagElm.querySelector('.tagify__tag-text').textContent;

                e.target.classList.toggle('checked');

                // Update the tag data
                const tagData = tagify.value.find(tag => tag.value === value);
                if (tagData) {
                    setTimeout(() => {
                        tagData.checked = e.target.classList.contains('checked');
                        tagify.updateValueByDOMTags();
                    }, 500);

                }
            }
        });

        
        const tagify2 = new Tagify(inputJobSearch, {
            maxTags: 300,
            templates: {
                tag: function(tagData) {
                    tagData = tagData || {};
                    const value = tagData.value || '';
                    const isChecked = tagData.checked ? 'checked' : '';
                    return `
                <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                    <div style="padding: 2px 6px;">
                        <span class="tagify__tag-text">${value}</span>
                        <span style="width: 16px; height: 16px; border-radius: 50%; background: #A5B4FC !important; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #3730A3; line-height: 1; flex-shrink: 0; transition: background 0.12s;"><x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><span>
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

        // Prevent infinite loop
        let isSyncing = false;

        // Sync tagify -> tagify2
        function syncToTagify2() {

            if (isSyncing) return;

            isSyncing = true;

            const tags = tagify.value.map(tag => ({
                value: tag.value
            }));

            tagify2.removeAllTags();
            tagify2.addTags(tags);

            isSyncing = false;
        }

        function syncToTagify1() {

            if (isSyncing) return;

            isSyncing = true;

            const tags = tagify2.value.map(tag => ({
                value: tag.value
            }));

            tagify.removeAllTags();
            tagify.addTags(tags);
            isSyncing = false;
        }



        // Function to update inputJobSearch based on tagify's value
        function updateInputJobSearch() {
            const tags = tagify.value.map(tag => tag.value).join(',');
            inputJobSearch.value = tags;
            autoclickstar();
            syncToTagify2();
            
        }

        function updateTagifyUsers() {
            const newTags = inputJobSearch.value.split(',').map(tag => ({
                value: tag.trim()
            }));
            syncToTagify1();
            // tagify.removeAllTags();
            // tagify.addTags(newTags);
            

        }
        // Listen for tag removal
        tagify.on('add', updateInputJobSearch);
        tagify.on('remove', updateInputJobSearch);
        inputJobSearch.addEventListener('change', updateTagifyUsers);

        // Initial sync
        updateInputJobSearch();

        // Sync tagify_users with inputJobSearch
        inputJobSearch.addEventListener('change', function() {
            const newTags = inputJobSearch.value.split(',').map(tag => ({
                value: tag.trim()
            }));
            tagify.loadOriginalValues(newTags);
        });


        function autoclickstar() {
            setTimeout(() => {
                var starElement = document.querySelector('.star');
                starElement.click();
                setTimeout(() => {
                    starElement.click();
                }, 200);
            }, 500);
        }

        function debounceskill(func, delay) {
            let timeoutskill;
            return function(...args) {
                clearTimeout(timeoutskill);
                timeoutskill = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const fetchSuggestionsSkills = debounceskill(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Skill: ", data);

                    if (Array.isArray(data)) {
                        tagify.settings.whitelist = data; // Update whitelist
                        // tagify.dropdown.show(query); // Show suggestions
                        tagify.dropdown.show.call(tagify);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching Skill:", err);
                }
            });
        }, 200); 

        // Listen for user input to trigger API call
        tagify.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 1) return;
            fetchSuggestionsSkills(query);
        });

        document.addEventListener('click', function (e) {

            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {

                // get tag element
                const tagElm = e.target.closest('.tagify__tag');

                // remove tag from tagify
                tagify.removeTags(tagElm);
                tagify2.removeTags(tagElm);

            }

        });

        /* ── Add from popular row ── */
        function addFromPopular(el) {
                let value = el.getAttribute('data-value');
                /*
                |--------------------------------------------------------------------------
                | CHECK EXISTING TAG
                |--------------------------------------------------------------------------
                */
                let existing = tagify.value.find(
                    tag => tag.value.toLowerCase() === value.toLowerCase()
                );
                /*
                |--------------------------------------------------------------------------
                | REMOVE TAG
                |--------------------------------------------------------------------------
                */
                if (existing) {

                    tagify.removeTags(value);

                    el.classList.remove('pop-added');
                }
                /*
                |--------------------------------------------------------------------------
                | ADD TAG
                |--------------------------------------------------------------------------
                */
                else {

                    tagify.addTags([value]);

                    el.classList.add('pop-added');
                }
                inputElm.focus();
        }
    </script>

    <script>
        // The DOM elements you wish to replace with Tagify
        var inputloc = document.querySelector("#kt_tagify_location");
        var cities = @json($cities);
        
        // Initialize Tagify script on the above inputs
        const tagifylocation = new Tagify(inputloc, {
            whitelist: cities,
            maxTags: 1,
            dropdown: {
                maxItems: 200,
                classname: "",
                enabled: 0,
                closeOnSelect: true
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

        // Prevent creating tags on "Enter" key
          inputloc.addEventListener('keydown', function(e) {
              if (e.key === "Enter") {
                  e.preventDefault(); // Prevents the default behavior of adding a tag
              }
          });

          // Prevent creating tags on blur or enter key
          tagifylocation.on('input', function(e) {
              tagifylocation.dropdown.show(); // Show the dropdown immediately
          });

          // Create tags only on select
          tagifylocation.on('dropdown:select', function(e) {
              tagifylocation.addTags([e.detail.data.value]);
          });

          document.addEventListener('click', function (e) {
            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {
                // get tag element
                const tagElm = e.target.closest('.tagify__tag');
                // remove tag from tagify
                tagifylocation.removeTags(tagElm);
            }

          });

    </script>

<script>
  
  function filtersdata(){
         fetchCandidates(); 
  }

$(document).ready(function() {	


   

  

  $(document).on('keyup', '#inputSearch', function (){
					   	//  tableLoad('load');  
               fetchCandidates();    
		   		});
   });
   
var table;
// setTimeout(() => {
  // tableLoad('load'); 
// }, 500);
     

$(document).on('click', '#changetextupdate', function (){
  //  tableLoad('load');
  fetchCandidates();
  $('.changetextupdate').text('Searching....!');
  var $this = $('#apptrackerfilter');
  $this.modal('hide');
  setTimeout(() => {
    $('#apptrackerfilter_close').click();
    $('.changetextupdate').text('Submit')
  
  }, 500);

  
});





// function tableLoad(type) 
// 	{
// 			table 						= $('#job_request').dataTable({
  
// 																			responsive     		: true, 
// 																			processing      	: true,
// 																			ordering			: false, //disable ordering      			
// 																			destroy       		: true,   
// 																			stateSave     		: true,      
// 																			"bLengthChange"   	: false,   
// 																			sDom				: 'lrtip',       
																			
// 			ajax			: 	{'url' : '{!! route('service_company.jobs.marketJobList') !!}', 
//       "data": function ( d ){ 
//         d.search = $('#inputSearch').val(),
//         d.custom_search = $('#kt_tagify_2').val(),
//         d.custom_status = 'active',
//         d.custom_hiringtype = $('#hiringtype').val(),
//         d.cus_search = $('#kt_tagify_users').val(),
//         d.min_exp = $('#min_exp').val(),
//         d.max_exp = $('#max_exp').val(),
//         d.kt_tagify_location = $('#kt_tagify_location').val(),
//         d.min_sal = $('#min_sal').val(),
//         d.max_sal = $('#max_sal').val(),
//         d.min_salary_currency =  $('#min_salary_currency').val(),
//         d.sal_mode = $('#sal_mode').val(),
//         d.app_int_activity = $('[name="app_int_activity"]:checked').val(),
//         d.app_int_outsource = $('[name="app_int_outsource"]:checked').val()
//       } },           
// 		   "fnDrawCallback" : 	function (oSettings) {
// 														$("body").tooltip({selector: '[data-toggle="tooltip"]' });
// 								},				
// 		  "initComplete"	:	function( settings, json){ }, 		  
// 		   columns			: [             
// 									{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
// 									{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},
// 									{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},
// 									{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
// 									{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},
// 									{data: 'Status', name: 'Status', searchable : false, orderable : false},
// 									{data: 'Date', name: 'Date',  searchable : false, orderable : false},
// 									// {data: 'HM', name: 'HM', orderable: false},
//                   {data: 'Terms', name: 'Terms', orderable: false},
// 									{data: 'Action', name: 'Action', orderable: false}
								
// 							 ]         
// 		 }); 
// 		 new $.fn.dataTable.FixedHeader(table);
	   
// 	   } 

$(document).on('click', '.client-show-detail', function (){
			$('#kt_activitiesClientjob_toggle').trigger('click');	
			var id    		=	$(this).data('row-id');
      // var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}";
			var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?jobfindhide=1";
			url     		=	url.replace(':id', id);
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
										 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
							},
							success 	: function (response) {
									$('#kt_activitiesclientjob_body').html(response.view);									
							}
					});
   		});

       $(document).on('click', '.client-hm-listing', function (){
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.clienthmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				$('#kt_activitiesHM_body').html(response.view);
               $('#company_or_client').text(response.company_or_client);			
   						}
   	  			});
   	});

    $(document).on('click', '.open-bench-talents', function (){
   		$('#kt_activitiesBT_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
      var mod    		= $(this).data('mod');
      var cur    		= $(this).data('cur');
   		var url   		= "{{ route('service_company.jobs.btlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
              data: {
                mod:mod,
                cur:cur
              },
   						 beforeSend	: function() {
                                     $('#kt_activitiesBT_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				$('#kt_activitiesBT_body').html(response.view);
                $('#company_or_client').text(response.company_or_client);
   						}
   	  			});
   	});

    // Keyboard accessibility for the clickable "Your Bench Match" box (role="button")
    $(document).on('keydown', '.open-bench-talents[role="button"]', function (e){
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });


     $(document).on('click', '.client-terms-listing-find-job', function (){
   		$('#kt_activitiesTermsFindjob_toggle').trigger('click');		
   		  var id    		= $(this).data('row-id');
        var recommendation_relation_id    		= $(this).data('row-recommendation_relation_id');
        var url   		= "{{ route('service_company.jobs.termshmlisting',':id') }}";
        url     		= url.replace(':id', id)+"?id="+id;
   		  var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
   						type  		: 'GET',
   						url   		: url,
              data: {
                recommendation_relation_id:recommendation_relation_id
              },
   						 beforeSend	: function() {
                                     $('#kt_activitiesTermsFindJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				       // $('#kt_activitiesTermsFindJob_body').html(response.view);
               $('#kt_activitiesTermsFindJob_body').html(response.terms);			
   						}
   	  			});
            
   	});



    $(document).on('click', '.request_approval', function ()
   	{

   		var jobId    					=	$(this).data('row-id');
		  var status    					=	'1';
		  var kt_tagify_location = $('#kt_tagify_location').val();
   		var url   						=	"{{ route('service_company.jobs.marketrequestaadded',':jobId') }}";
   		url     						=	url.replace(':jobId', jobId);	
		  var baseurl    					=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		
		                     
													const swalWithBootstrapButtons = Swal.mixin({
																									customClass: {
																														confirmButton	: 'btn btn-success',
																														cancelButton	: 'btn btn-danger'
																												},
																									buttonsStyling	: false
													})
													swalWithBootstrapButtons.fire({
																					  title				: 'Are you sure?',
																					  text				: "You won't be able to revert this!",
																					  icon				: 'warning',
																					  showCancelButton	: true,
																					  confirmButtonText	: 'Yes, Add',
																					  cancelButtonText	: 'No, cancel!',
																					  reverseButtons	: true
																				})
													.then((result) => {
																				
													  if (result.isConfirmed) {
                                   $(this).prop('disabled',true);
                                   $(this).text('Waiting.....');
                                 
                                // return false;
															
																$.ajax({
																			type  		: 'GET',
																			url   		: url,
																			data		: {'jobId': jobId, 'status':status,'kt_tagify_location': kt_tagify_location },
																			success 	: function (response) {
																				
																						if(response.status == 'success'){
																							swalWithBootstrapButtons.fire
																							('Accept!', 'Job has been added successfully.', 'success')	
																						}else if(response.status == 'closeJob'){
																							swalWithBootstrapButtons.fire
																							("Ignore!",response.message, "error")
																						}else if(response.status == 'ignoreClient'){
																							swalWithBootstrapButtons.fire
																							('Ignore!', 'Selected request has been ignored successfully.', 'error')
																						}		
                                            setTimeout(() => {
                                              // table._fnDraw();
																						  // tableLoad('load');	
                                              fetchCandidates();
                                            }, 1500);	
                                            																							
																			}
																	});
														} 
														else if (result.dismiss === Swal.DismissReason.cancel) {
															swalWithBootstrapButtons.fire('Cancelled', 'Selected request is safe :)', 'error')
														}
													
													})}); 


        $(document).ready(function() {

          fetchCandidates();

          $(document).on('click', '#pagination .page-link', function(e) {
              e.preventDefault();

              const page = $(this).data('page');
              if (!page || $(this).hasClass('disabled')) return;

              // Disable the clicked pagination link
              $(this).addClass('disabled');

              // Fetch data
              fetchCandidates(page, $('#search').val()).then(() => {
                  // Re-enable the links after successful data fetch
                  $('#pagination .page-link').removeClass('disabled');
              }).catch(() => {
                  // Re-enable the links even if there's an error
                  $('#pagination .page-link').removeClass('disabled');
              });
          });

        });


        function fetchCandidates(page = 1, search = '') {
            var clientid = '1';
            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.jobs.marketJobList_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search:$('#inputSearch').val(),
                  custom_search:$('#kt_tagify_2').val(),
                  custom_status:'active',
                  custom_hiringtype:$('#hiringtype').val(),
                  cus_search:$('#kt_tagify_users').val(),
                  min_exp:$('#min_exp').val(),
                  max_exp:$('#max_exp').val(),
                  kt_tagify_location:$('#kt_tagify_location').val(),
                  min_sal:$('#min_sal').val(),
                  max_sal:$('#max_sal').val(),
                  min_salary_currency: $('#min_salary_currency').val(),
                  sal_mode:$('#sal_mode').val(),
                  app_int_activity:$('[name="app_int_activity"]:checked').val(),
                  app_int_outsource:$('[name="app_int_outsource"]:checked').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                  //   alert(response.total);
                    $('#marketJobLists').html(response.html);
                    $('#span_jobMarket').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }


         $('body').on('change', '#defaultCurrency', function() {
            var currency = $(this).val();
            $('#min_salary_currency').val(currency);
            setTimeout(() => {
              fetchCandidates(); 
            }, 500);
             
        });
        $('body').on('change', '#min_salary_currency', function() {
            var currency = $(this).val();
            $('#defaultCurrency').val(currency).trigger('change');
        });
        $('body').on('change', '#defaultMode', function() {
            var mode = $(this).val();
            $('#sal_mode').val(mode);
            setTimeout(() => {
              fetchCandidates(); 
            }, 500);
        });
        $('body').on('change', '#sal_mode', function() {
          var mode = $(this).val();
          $('#defaultMode').val(mode).trigger('change');
        
        });



																      
</script>
@endpush

