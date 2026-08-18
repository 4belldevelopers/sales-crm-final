

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

    /* ── Filter Drawer ── */
    #fd-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.4);
        z-index: 1045;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s ease;
    }
    #fd-overlay.fd-open { opacity: 1; pointer-events: auto; }

    #apptrackerfilter {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        width: 80% !important;
        height: 100vh !important;
        z-index: 1050 !important;
        background: #fff !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: -4px 0 24px rgba(0,0,0,.13) !important;
        transform: translateX(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
    }
    #apptrackerfilter.fd-open { transform: translateX(0); }

    #apptrackerfilter .modal-header.flex-stack {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f3f8;
        flex-shrink: 0;
    }
    #apptrackerfilter .apptrackerfilter_body_content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    #apptrackerfilter .modal-body { flex: 1; overflow-y: auto; padding: 20px !important; }
    #apptrackerfilter .modal-footer { flex-shrink: 0; padding: 14px 20px; border-top: 1px solid #f1f3f8; }

    @media (max-width: 768px) { #apptrackerfilter { width: 380px !important; } }
    @media (max-width: 480px) { #apptrackerfilter { width: 100% !important; } }

    /* ══════════════════════════════════════════
       Market Jobs Hero  (mjh-*)
    ══════════════════════════════════════════ */
    .mjh-wrap {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 22px 24px 20px;
        margin-bottom: 14px;
    }
    .mjh-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 20px;
    }
    .mjh-title-block { flex: 0 0 auto; min-width: 0; }
    .mjh-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 5px;
        line-height: 1.25;
    }
    .mjh-sub {
        font-size: 13px;
        font-weight: 400;
        color: #6B7280;
        max-width: 640px;
        line-height: 1.45;
        text-align: right;
        flex: 1;
        white-space: nowrap;
    }
    .mjh-btn-post {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #fff;
        color: #2563EB;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 8px;
        border: 1.5px solid #BFDBFE;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        font-family: 'Poppins', sans-serif;
        transition: background 0.15s;
    }
    .mjh-btn-post:hover { background: #EFF6FF; }

    /* Stat cards */
    .mjh-stats { display: flex; gap: 14px; }
    .mjh-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex: 1;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 16px 18px;
        transition: box-shadow 0.15s, transform 0.15s;
        min-width: 0;
    }
    .mjh-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .mjh-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .mjh-icon svg { width: 24px; height: 24px; }
    .mjh-icon-blue   { background: #EFF6FF; color: #2563EB; }
    .mjh-icon-green  { background: #DCFCE7; color: #16A34A; }
    .mjh-icon-purple { background: #F3F0FF; color: #7C3AED; }
    .mjh-icon-orange { background: #FFF7ED; color: #EA580C; }
    .mjh-card-label {
        font-size: 13px;
        color: #6B7280;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .mjh-card-num {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        line-height: 1.2;
    }
    .mjh-card-sub {
        font-size: 12px;
        font-weight: 400;
        color: #6B7280;
        margin-top: 2px;
    }
    .mjh-trend { display: none; }

    /* Filter bar */
    .mjh-filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 10px;
        flex-wrap: nowrap;
    }
    .mjh-search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .mjh-search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        pointer-events: none;
    }
    .mjh-search-input {
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 9px 10px 9px 32px;
        font-size: 13px;
        color: #374151;
        font-family: 'Poppins', sans-serif;
        outline: none;
        background: #F9FAFB;
        transition: border-color 0.15s;
    }
    .mjh-search-input:focus { border-color: #93C5FD; background: #fff; }
    .mjh-search-input::placeholder { color: #9CA3AF; }
    .mjh-search-btn {
        background: #2563EB;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 22px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        font-family: 'Poppins', sans-serif;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .mjh-search-btn:hover { background: #1d4ed8; }
    .mjh-filter-dropdowns {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .mjh-drop-wrap {
        display: flex;
        align-items: center;
        gap: 5px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 7px 10px;
        background: #fff;
        color: #374151;
        position: relative;
        cursor: pointer;
    }
    .mjh-drop,
    select.mjh-drop {
        appearance: none !important;
        -webkit-appearance: none !important;
        border: none !important;
        background: transparent !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #374151 !important;
        font-family: 'Poppins', sans-serif !important;
        outline: none !important;
        cursor: pointer !important;
        padding-right: 4px !important;
        box-shadow: none !important;
        width: auto !important;
        height: auto !important;
    }
    /* Hide Select2 wrapper if it gets created anyway */
    .mjh-drop-wrap .select2-container { display: none !important; }
    .mjh-caret { color: #9CA3AF; flex-shrink: 0; pointer-events: none; }
    .mjh-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F3F4F6;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid #E5E7EB;
        cursor: pointer;
        white-space: nowrap;
        font-family: 'Poppins', sans-serif;
        transition: background 0.15s;
    }
    .mjh-more-btn:hover { background: #E5E7EB; }

    /* Popular searches */
    .mjh-popular {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 0 4px;
        margin-bottom: 14px;
        font-size: 12.5px;
    }
    .mjh-popular-label { color: #6B7280; font-weight: 500; flex-shrink: 0; }
    .mjh-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #F3F4F6;
        color: #374151;
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.12s, border-color 0.12s;
    }
    .mjh-tag:hover { background: #EFF6FF; border-color: #BFDBFE; color: #2563EB; }
    .mjh-tag.pop-added { background: #DBEAFE; border-color: #93C5FD; color: #1d4ed8; }
    .mjh-tag-hot { color: #92400E; background: #FFFBEB; border-color: #FDE68A; }
    .mjh-tag-hot:hover { background: #FEF3C7; }
    .mjh-clear-all {
        margin-left: auto;
        color: #2563EB;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .mjh-clear-all:hover { text-decoration: underline; }
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
                    {{-- ── Market Jobs Hero Card ── --}}
                    <div class="mjh-wrap">
                        <div class="mjh-top">
                            <div class="mjh-title-block">
                                <div class="mjh-title">
                                    Job Marketplace for Agencies
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <div class="mjh-sub">Access verified hiring opportunities from trusted clients across full-time, contract, and bench hiring.</div>
                            </div>
                        </div>

                        <div class="mjh-stats">
                            {{-- All Jobs --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">All Jobs</div>
                                    <div class="mjh-card-num">{{ number_format($all_jobs) }}</div>
                                    <div class="mjh-card-sub">Live Opportunities</div>
                                </div>
                            </div>

                            {{-- Agency Jobs (Full-Time / Direct) --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                                        <line x1="8" y1="21" x2="16" y2="21"/>
                                        <line x1="12" y1="17" x2="12" y2="21"/>
                                        <line x1="6" y1="8" x2="6" y2="13"/>
                                        <line x1="10" y1="8" x2="10" y2="13"/>
                                        <line x1="14" y1="8" x2="14" y2="13"/>
                                        <line x1="18" y1="8" x2="18" y2="13"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Agency Jobs</div>
                                    <div class="mjh-card-num">{{ number_format($all_jobs - $market_place) }}</div>
                                    <div class="mjh-card-sub">Direct Roles</div>
                                </div>
                            </div>

                            {{-- Bench Talent Jobs --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Bench Talent Jobs</div>
                                    <div class="mjh-card-num">{{ number_format($market_place) }}</div>
                                    <div class="mjh-card-sub">Contract Roles</div>
                                </div>
                            </div>

                            {{-- Newly Added --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-orange">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Newly Added</div>
                                    <div class="mjh-card-num">{{ number_format($newly_add) }}</div>
                                    <div class="mjh-card-sub">Ready to Submit</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Filter Bar ── --}}
                    <div class="mjh-filter-bar">
                        <div class="mjh-search-wrap">
                            <svg class="mjh-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input class="mjh-search-input" name="inputJobSearch" id="kt_tagify_2" value="" type="text" placeholder="Search by job title, skills, company, or job ID..."/>
                        </div>
                        <button class="mjh-search-btn" onclick="filtersdataget()">Search</button>

                        <div class="mjh-filter-dropdowns">
                            <div class="mjh-drop-wrap">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <select class="mjh-drop" id="defaultCurrency" data-kt-select2="false" data-select2-id="false" onchange="filtersdata()">
                                    <option value="">Currency</option>
                                    <option value="0">INR</option><option value="1">EUR</option>
                                    <option value="2">JPY</option><option value="3">GBP</option>
                                    <option value="4">USD</option><option value="5">AED</option>
                                </select>
                                <svg class="mjh-caret" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                            </div>

                            <div class="mjh-drop-wrap">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                <select class="mjh-drop" id="hiringtype" data-kt-select2="false" data-select2-id="false" onchange="filtersdata()">
                                    <option value="All">Work Mode</option>
                                    <option value="2">Agency Jobs</option>
                                    <option value="1">Bench Talent Jobs</option>
                                </select>
                                <svg class="mjh-caret" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                            </div>

                            <div class="mjh-drop-wrap">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                <select class="mjh-drop" id="defaultMode" data-kt-select2="false" data-select2-id="false" onchange="filtersdata()">
                                    <option value="">Salary</option>
                                    <option value="0">Annual</option>
                                    <option value="1">Monthly</option>
                                    <option value="2">Hourly</option>
                                </select>
                                <svg class="mjh-caret" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                            </div>

                            <button class="mjh-more-btn" onclick="fdOpen()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                More Filters
                            </button>
                        </div>
                    </div>

                    {{-- ── Popular Searches ── --}}
                    <div class="mjh-popular">
                        <span class="mjh-popular-label">Popular Searches:</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="React">React</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="Java Developer">Java Developer</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="Python">Python</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value=".NET">.NET</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="UI/UX">UI/UX</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="AWS">AWS</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="DevOps">DevOps</span>
                        <span class="mjh-tag" onclick="addFromPopular(this)" data-value="Data Analyst">Data Analyst</span>
                        <span class="mjh-clear-all" onclick="clearAllTags()">Clear All</span>
                    </div>

                    <!-- ACTIVE FILTER TAGS -->
                    <div class="active-filters" id="selectedFilters" style="display:none;"></div>    

                <!-- Job List -->
                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                <div class="job-list" id="marketJobLists"></div>

            </div>
            
            <div id="pagination"></div>
        </main>

        <!--Start Filter Drawer-->
        <div id="fd-overlay" onclick="fdClose()"></div>
        <div id="apptrackerfilter" tabindex="-1">
                <div class="modal-header flex-stack">
                    <h2>Filter</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" onclick="fdClose()" id="apptrackerfilter_close">
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
                            <button type="button" id="" class="btn btn-secondary me-3" onclick="fdClose()">Close</button>
                            <button type="button" id="changetextupdate" class="btn btn-primary changetextupdate">
                            <span class="indicator-label">Submit</span></button>
                        </div>
                    </div>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/moment/moment.js?ver=1553620716" type="text/javascript"></script>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/multiselect/js/jquery.multi-select.js?ver=1553620716"></script>
                    <script src="https://servicetesting.4bell.com/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js?ver=1553620716" type="text/javascript"></script>
                </div>
        </div>
        <!--End Filter Drawer-->

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
      <div class="card-header px-5 py-0 d-none" id="kt_activitiesbt_header">
         <div class="d-flex align-items-center gap-3 py-2" id="company_bt_client">
            <div class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; flex-shrink: 0;">
               <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2C12 2 13.5 7.5 17 9C13.5 10.5 12 16 12 16C12 16 10.5 10.5 7 9C10.5 7.5 12 2 12 2Z" fill="#4F46E5"/>
                  <path d="M19 1C19 1 19.6 3.4 21 4C19.6 4.6 19 7 19 7C19 7 18.4 4.6 17 4C18.4 3.4 19 1 19 1Z" fill="#6366F1"/>
                  <path d="M5 16C5 16 5.5 18 7 18.5C5.5 19 5 21 5 21C5 21 4.5 19 3 18.5C4.5 18 5 16 5 16Z" fill="#818CF8"/>
               </svg>
            </div>
            <div>
               <h3 class="card-title fw-bolder text-dark m-0" style="font-size: 18px; font-weight: 700; color: #1E293B; font-family: 'Poppins', sans-serif;">AI Recommended Bench Candidates</h3>
               <span class="text-muted d-block" style="font-size: 13px; color: #64748B; font-family: 'Poppins', sans-serif;">Top bench talents matching your job requirements</span>
            </div>
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

  // Prevent Metronic/Select2 from hijacking the filter bar selects
  $(document).ready(function() {
      ['#defaultCurrency','#defaultMode','#hiringtype'].forEach(function(sel) {
          try {
              if ($(sel).data('select2')) { $(sel).select2('destroy'); }
          } catch(e) {}
          $(sel).addClass('mjh-drop').css({ display: 'inline-block' });
      });
  });

  function clearAllTags() {
      if (typeof tagify !== 'undefined') { tagify.removeAllTags(); }
      if (typeof tagify2 !== 'undefined') { tagify2.removeAllTags(); }
      document.querySelectorAll('.mjh-tag.pop-added').forEach(el => el.classList.remove('pop-added'));
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
  fetchCandidates();
  $('.changetextupdate').text('Searching....!');
  fdClose();
  setTimeout(() => { $('.changetextupdate').text('Submit'); }, 500);
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

	// Market-jobs "View Requirement" — same drawer but hides hiring-manager, action-buttons and pipeline sections
	$(document).on('click', '.market-show-detail', function (){
			$('#kt_activitiesClientjob_toggle').trigger('click');
			var id      = $(this).data('row-id');
			var url     = "{{ route('service_company.jobs.viewmore',':id') }}?jobfindhide=1&hideMarketSections=1";
			url         = url.replace(':id', id);
			var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
			$.ajax({
				type     : 'GET',
				url      : url,
				beforeSend: function() {
					$('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
				},
				success  : function (response) {
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


        function renderMarketJobCard(row) {
            // ── Col 1: avatar initial colour derived from first char of title ──
            let strVal = (row.title_short || 'J').charAt(0).toLowerCase();
            let bgTextDecore = 'fs-3 bg-light-primary text-primary';
            let code = strVal.charCodeAt(0);
            if (code >= 97 && code <= 101) bgTextDecore = 'fs-3 bg-light-danger text-danger';
            else if (code >= 102 && code <= 106) bgTextDecore = 'fs-3 bg-light-warning text-warning';
            else if (code >= 107 && code <= 111) bgTextDecore = 'fs-3 bg-light-primary text-primary';
            else if (code >= 112 && code <= 116) bgTextDecore = 'fs-3 bg-light-success text-success';
            else if (code >= 117 && code <= 121) bgTextDecore = 'fs-3 bg-dark text-secondary';
            else bgTextDecore = 'fs-3 bg-light-secondary text-dark';

            // ── Status badge (OPEN / HOLD) ──
            let statusClass = 'jc-badge-open';
            if (row.status_label === 'HOLD') statusClass = 'jc-badge-hold';

            // ── Employment type badge ──
            let typeClass = row.employment_type === 'Contract' ? 'jc-badge-ct' : 'jc-badge-ft';

            // ── Skills HTML ──
            let skillTagsHtml = (row.skills || []).map(s => `<span class="skill-tag">${s}</span>`).join('');
            if (row.remaining_skills_count > 0) {
                skillTagsHtml += `<span class="skill-tag skill-more client-show-detail" data-row-id="${row.id}">+${row.remaining_skills_count}</span>`;
            }

            // ── Action button (Col 5 primary CTA) ──
            let actionBtnHtml = '';
            let actionBtnClass = 'btn-submit btn btn-sm btn-primary';
            if (row.action_type === 'vendor_fulfilled') {
                actionBtnHtml = `<button data-row-status="" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-submit btn btn-sm btn-danger w-100">Vendor Fulfilled</button>`;
            } else if (row.action_type === 'apply_agency') {
                actionBtnHtml = `<button class="btn-submit btn btn-sm w-100 request_approval" style="background:#7C3AED;" data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">Submit Candidates</button>`;
            } else if (row.action_type === 'reject') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-submit btn btn-sm btn-danger w-100">Reject</button>`;
            } else if (row.action_type === 'under_review') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-submit btn btn-sm btn-warning w-100">Under Review</button>`;
            } else if (row.action_type === 'vendor_limit') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-submit btn btn-sm btn-danger w-100">Vendor Limit</button>`;
            } else if (row.action_type === 'view_job' && row.view_job_url) {
                actionBtnHtml = `<a href="${row.view_job_url}" class="btn-submit btn btn-sm btn-info w-100 text-center">View Job</a>`;
            } else if (row.employment_type === 'Contract') {
                actionBtnHtml = `<button data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}" class="btn-submit btn btn-sm w-100 open-bench-talents" style="background:#7C3AED;">Submit Contract Candidates</button>`;
            } else {
                actionBtnHtml = `<button data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}" class="btn-submit btn btn-sm btn-primary w-100 open-bench-talents">Match from Your Bench</button>`;
            }

            // ── Commercial Terms block (Col 5 lower section) ──
            let commercialHtml = '';
            const ct = row.commercial_terms;
            if (ct) {
                let termsRows = '';
                if (row.employment_type === 'Contract') {
                    // Contract job: show billing model + rate + payment terms
                    let billingRate = ct.billing_rate ? `₹${(ct.billing_rate/1000).toFixed(0)}K/Mo` : 'Per Contract';
                    termsRows = `
                        <div class="jc-ct-row"><span class="jc-ct-label">Billing Model</span><span class="jc-ct-value">${ct.billing_model || 'Monthly'}</span></div>
                        <div class="jc-ct-row"><span class="jc-ct-label">Billing Rate</span><span class="jc-ct-value">${billingRate}</span></div>
                        <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-value">${ct.payment_days || 30} Days</span></div>`;
                } else {
                    // Full-Time job: show hiring fee % + replacement + payment terms
                    termsRows = `
                        <div class="jc-ct-row"><span class="jc-ct-label">Hiring Fee</span><span class="jc-ct-value">${ct.hiring_fee_pct || 8.33}% of CTC</span></div>
                        <div class="jc-ct-row"><span class="jc-ct-label">Replacement</span><span class="jc-ct-value">${ct.replacement_days || 90} Days</span></div>
                        <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-value">${ct.payment_days || 30} Days</span></div>`;
                }
                commercialHtml = `
                <div class="jc-commercial-block">
                    <div class="jc-commercial-title">Commercial Terms</div>
                    ${termsRows}
                    <a class="jc-view-terms-link client-terms-listing-find-job" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">View Full Terms →</a>
                </div>`;
            } else {
                // Graceful fallback when terms not cached yet
                commercialHtml = `
                <div class="jc-commercial-block">
                    <div class="jc-commercial-title">Commercial Terms</div>
                    <a class="jc-view-terms-link client-terms-listing-find-job" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">View Full Terms →</a>
                </div>`;
            }

            // ── Salary display ──
            let salaryDisplay = `${row.salary_currency}${row.min_salary} – ${row.max_salary} / ${row.salary_mode_short}`;

            // ── Company size display ──
            let companySizeText = (row.company_size_from >= 10000)
                ? `${row.company_size_from}+ Employees`
                : `${row.company_size_from || '–'} – ${row.company_size || '–'} Employees`;

            // ── Submission deadline colour: red if date has passed, else normal ──
            let deadlineHtml = row.submission_deadline_formatted
                ? `<span class="jc-date-deadline">${row.submission_deadline_formatted}</span>`
                : `<span>–</span>`;

            // ── Status pill for dates column ──
            let statusPillClass = row.status_label === 'HOLD' ? 'jc-status-hold' : 'jc-status-open';
            let statusPillText  = row.status_label === 'HOLD' ? 'Hold' : 'Active – Open';

            return `
            <div class="job-card">

                <!-- ── COL 1: Title, Badges, Skills ── -->
                <div class="jc-col1">
                    <div class="jc-badges-row">
                        <span class="jc-badge ${statusClass}">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            ${row.status_label}
                        </span>
                        <span class="jc-badge ${typeClass}">${row.employment_type_badge || row.employment_type}</span>
                        <span class="jc-req-id">${row.req_id}</span>
                    </div>

                    <div class="job-header-row">
                        <div class="job-icon ${bgTextDecore}">${strVal.toUpperCase()}</div>
                        <div class="job-details">
                            <h2>${row.title_short}</h2>
                            <div class="job-dept">${row.department || '—'}</div>
                        </div>
                    </div>

                    <div class="skills-row">${skillTagsHtml}</div>
                </div>

                <!-- ── COL 2: Location & Job Meta ── -->
                <div class="jc-col2">
                    <div class="meta-row">
                        <i class="fa-regular fa-location-dot"></i>
                        <div class="meta-content"><p>Location</p><span>${row.work_type === 'Remote' ? 'Remote' : (row.industry_name ? row.industry_name : row.work_type)}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-laptop-house"></i>
                        <div class="meta-content"><p>Work Mode</p><span>${row.work_type}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-regular fa-clock"></i>
                        <div class="meta-content"><p>Experience</p><span>${row.min_exp} – ${row.max_exp} Years</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-regular fa-circle-user"></i>
                        <div class="meta-content"><p>Openings</p><span>${row.total_positions}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                        <div class="meta-content"><p>Salary</p><span>${salaryDisplay}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-industry"></i>
                        <div class="meta-content"><p>Industry</p><span>${row.industry_name || '—'}</span></div>
                    </div>
                </div>

                <!-- ── COL 3: Posted, Updated, Deadline, Status ── -->
                <div class="jc-col3">
                    <div class="meta-row">
                        <i class="fa-regular fa-calendar-plus"></i>
                        <div class="meta-content"><p>Posted</p><span>${row.created_at_formatted}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-rotate"></i>
                        <div class="meta-content"><p>Last Updated</p><span class="jc-date-warn">${row.updated_at_ago}</span></div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-regular fa-calendar-xmark"></i>
                        <div class="meta-content"><p>Submission Deadline</p>${deadlineHtml}</div>
                    </div>
                    <div class="meta-row">
                        <i class="fa-regular fa-circle-dot"></i>
                        <div class="meta-content"><p>Status</p>
                            <span class="jc-status-pill ${statusPillClass}">
                                <span class="jc-status-pill-dot"></span>${statusPillText}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ── COL 4: Bench Match ── -->
                <div class="jc-col4">
                    <div class="jc-bench-box open-bench-talents"
                         data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}"
                         role="button" tabindex="0" title="View matching bench talents">
                        <div class="jc-bench-header">
                            <i class="fa-solid fa-user-group"></i>
                            <span>Your Bench Match</span>
                        </div>
                        <div class="jc-bench-count">${row.bench_match_count}</div>
                        <p class="jc-bench-sub">Matching candidates</p>
                        <a class="jc-bench-link">View Matches <i class="fa-solid fa-arrow-right" style="font-size:9px;"></i></a>
                    </div>
                </div>

                <!-- ── COL 5: Commercial Terms + Actions ── -->
                <div class="jc-col5">
                    ${actionBtnHtml}
                    <button class="btn-view market-show-detail" data-row-id="${row.id}">View Requirement</button>
                    <button class="btn-view client-terms-listing-find-job" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">View Terms</button>
                    ${commercialHtml}
                    <div class="jc-card-footer">
                        <i class="fa-regular fa-bookmark" title="Save job"></i>
                        <i class="fa-solid fa-ellipsis" title="More options"></i>
                    </div>
                </div>

            </div>`;
        }

        function fetchCandidates(page = 1, search = '') {
            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.jobs.marketJobListV2_ajax') !!}?page=${page}`,
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
                    if (response.items && Array.isArray(response.items)) {
                        if (response.items.length > 0) {
                            let html = response.items.map(item => renderMarketJobCard(item)).join('');
                            $('#marketJobLists').html(html);
                        } else {
                            $('#marketJobLists').html('No data available in table');
                        }
                    } else if (response.html) {
                        $('#marketJobLists').html(response.html);
                    }
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



								
        /* ── Send Bench Request — bound once here to prevent duplicate calls when drawer reloads ── */
        $(document).on("click", ".request-send", function () {
            var ele   = $(this);
            var jobId = ele.attr('data-jobId');
            var conId = ele.attr('data-conId');
            ele.prop('disabled', true).text('Sending…');
            var url = "{{ route('service_company.jobs.recommandedrequestcondidate', ':id') }}";
            url = url.replace(':id', jobId) + "?conId=" + conId;
            $.ajax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == 'success') {
                        showToastr('success', 'Bench request sent successfully!', 'Success');
                    } else {
                        showToastr('error', response.message || 'Request failed.', 'Error');
                        ele.prop('disabled', false).text('Send Bench Request');
                    }
                    if (typeof window.fetchCandidatesbench === 'function') {
                        window.fetchCandidatesbench();
                    }
                },
                error: function () {
                    showToastr('error', 'Something went wrong.', 'Error');
                    ele.prop('disabled', false).text('Send Bench Request');
                }
            });
        });

    /* ── Filter Drawer open/close ── */
    function fdOpen() {
        document.getElementById('apptrackerfilter').classList.add('fd-open');
        document.getElementById('fd-overlay').classList.add('fd-open');
        document.body.style.overflowY = 'hidden';
    }
    function fdClose() {
        document.getElementById('apptrackerfilter').classList.remove('fd-open');
        document.getElementById('fd-overlay').classList.remove('fd-open');
        document.body.style.overflowY = '';
    }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') fdClose(); });
</script>
@endpush

