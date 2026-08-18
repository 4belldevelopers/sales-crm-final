

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
    .mjh-sub-desc {
        font-size: 13px;
        font-weight: 400;
        color: #6B7280;
        max-width: 680px;
        line-height: 1.45;
        margin-bottom: 8px;
    }
    .mjh-header-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 6px;
    }
    .mjh-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
        font-family: 'Poppins', sans-serif;
    }
    .mjh-pill-green { background: #DCFCE7; color: #15803D; }
    .mjh-pill-green-dot { background: #DCFCE7; color: #15803D; }
    .mjh-pill-purple { background: #F3F0FF; color: #6D28D9; }
    .mjh-dot-live {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #16A34A;
        display: inline-block;
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
        align-items: center;
        gap: 14px;
        flex: 1;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 14px 18px;
        transition: box-shadow 0.15s, transform 0.15s;
        min-width: 0;
    }
    .mjh-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .mjh-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .mjh-icon svg { width: 22px; height: 22px; }
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
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

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
                                    Agency Job Marketplace
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <div class="mjh-sub-desc">Verified hiring requirements shared directly by client companies. Find opportunities, review terms, and submit qualified candidates.</div>
                                <div class="mjh-header-pills">
                                    <span class="mjh-pill mjh-pill-green"><i class="fa-solid fa-circle-check"></i> Verified Clients</span>
                                    <span class="mjh-pill mjh-pill-green-dot"><span class="mjh-dot-live"></span> Active Requirements</span>
                                    <span class="mjh-pill mjh-purple" style="background:#F3F0FF;color:#6D28D9;"><i class="fa-solid fa-users"></i> Agency Eligible</span>
                                </div>
                            </div>
                        </div>

                        <div class="mjh-stats">
                            {{-- All Jobs / All Opportunities --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">All Opportunities</div>
                                    <div class="mjh-card-num">{{ number_format($all_jobs ?? 0) }}</div>
                                </div>
                            </div>

                            {{-- Fulltime Jobs / Full-Time Hiring --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Full-Time Hiring</div>
                                    <div class="mjh-card-num">{{ number_format($fulltime_jobs ?? 0) }}</div>
                                </div>
                            </div>

                            {{-- Contract Jobs / Contract Hiring --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Contract Hiring</div>
                                    <div class="mjh-card-num">{{ number_format($contract_jobs ?? 0) }}</div>
                                </div>
                            </div>

                            {{-- Agency Request / Bench Talent Requests --}}
                            <div class="mjh-card">
                                <div class="mjh-icon mjh-icon-orange">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div class="mjh-card-body">
                                    <div class="mjh-card-label">Bench Talent Requests</div>
                                    <div class="mjh-card-num">{{ number_format($agency_requests ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Apptracker Search / Sort / Filter Toolbar ── --}}
                    <div class="simf-toolbar-card">
                        <div class="simf-toolbar-row">
                            {{-- Search Input --}}
                            <div class="simf-search-wrap">
                                <span class="simf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="simf-search-input" name="inputJobSearch" id="kt_tagify_2"
                                    placeholder="Search by job title, skills, requirement ID or client..." autocomplete="off" />
                            </div>

                            {{-- Sort Dropdown Control --}}
                            <div class="simf-sort-wrap" id="simf_sort_wrap">
                                <span class="simf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="simf-sort-btn" id="simf_sort_btn">
                                    <span id="simf_sort_label">Newest to Oldest</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="hiringtype" class="d-none" data-kt-select2="false" style="display:none !important;" onchange="filtersdata()">
                                    <option value="1" selected>Newest to Oldest</option>
                                    <option value="2">Oldest to Newest</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="7">High to Low (Experience)</option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="simf-sort-panel d-none" id="simf_sort_panel"></div>
                            </div>

                            {{-- Filter Button --}}
                            <button type="button" class="simf-filter-btn" onclick="fdOpen()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filters</span>
                                <span class="badge simf-count-badge d-none" id="simf_count_badge">0</span>
                            </button>
                        </div>
                    </div>

                    {{-- ── Applied Filters Summary Card (Appears when filters applied) ── --}}
                    <div class="simf-applied-card d-none" id="simf_applied_card">
                        <div class="simf-applied-head">
                            <span class="simf-applied-title">
                                Active Filters
                                <span class="simf-applied-count" id="simf_applied_count">0</span>
                            </span>
                            <a href="javascript:void(0);" class="simf-applied-clearall" id="simf_applied_clear_all" onclick="clearAllAppliedFilters()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                Clear All
                            </a>
                        </div>
                        <div class="simf-applied-chips" id="simf_applied_chips"></div>
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
                        <!-- ACTIVE FILTER TAGS -->
                        <div class="active-filters" id="selectedFilters" style="display:none;"></div>
                    </div>{{-- ← closes mjh-popular --}}

                    {{-- ── Horizontal Filter Tabs ── --}}
                    <div class="mjh-tabs-bar">
                        <div class="mjh-tab-item active" data-tab="all">
                            <i class="fa-solid fa-briefcase mjh-tab-icon"></i>
                            <span>All Agencies Jobs</span>
                            <span class="mjh-tab-badge mjh-badge-blue">{{ number_format($all_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="fulltime">
                            <i class="fa-solid fa-user-tie mjh-tab-icon"></i>
                            <span>Fulltime Hiring Jobs</span>
                            <span class="mjh-tab-badge mjh-badge-green">{{ number_format($fulltime_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="contract">
                            <i class="fa-solid fa-clock mjh-tab-icon"></i>
                            <span>Contract Hiring Jobs</span>
                            <span class="mjh-tab-badge mjh-badge-purple">{{ number_format($contract_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="agency_request">
                            <i class="fa-solid fa-handshake mjh-tab-icon"></i>
                            <span>Agency Requests</span>
                            <span class="mjh-tab-badge mjh-badge-orange">{{ number_format($agency_requests ?? 0) }}</span>
                        </div>
                    </div>

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
        let tagify = null;

        if (inputElm) {
            tagify = new Tagify(inputElm, {
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
                if (e.target.classList.contains('star') && tagify && Array.isArray(tagify.value)) {
                    const tagElm = e.target.closest('.tagify__tag');
                    if (!tagElm) return;
                    const value = tagElm.querySelector('.tagify__tag-text')?.textContent;

                    e.target.classList.toggle('checked');

                    const tagData = tagify.value.find(tag => tag.value === value);
                    if (tagData) {
                        setTimeout(() => {
                            tagData.checked = e.target.classList.contains('checked');
                            tagify.updateValueByDOMTags();
                        }, 500);
                    }
                }
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
                        if (Array.isArray(data) && tagify) {
                            tagify.settings.whitelist = data;
                            tagify.dropdown.show.call(tagify);
                        }
                    },
                    error: function(err) {
                        console.error("Error fetching Skill:", err);
                    }
                });
            }, 200);

            tagify.on('input', function(e) {
                var query = (e.detail.value || '').trim();
                if (query.length < 1) return;
                fetchSuggestionsSkills(query);
            });

            tagify.on('change', function() {
                if (typeof filtersdata === 'function') {
                    filtersdata();
                }
            });
        }

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('tagify__tag__removeBtn')) {
                const tagElm = e.target.closest('.tagify__tag');
                if (tagElm && tagify) {
                    tagify.removeTags(tagElm);
                }
            }
        });

        /* ── Add from popular searches row ── */
        function addFromPopular(el) {
            let value = el.getAttribute('data-value') || '';
            let $search = $('#kt_tagify_2');
            let current = $search.val().trim();

            if (current.toLowerCase() === value.toLowerCase()) {
                $search.val('');
                el.classList.remove('pop-added');
            } else {
                $search.val(value);
                document.querySelectorAll('.mjh-popular .mjh-tag').forEach(t => t.classList.remove('pop-added'));
                el.classList.add('pop-added');
            }

            if (tagify && Array.isArray(tagify.value)) {
                let existing = tagify.value.find(
                    tag => tag.value.toLowerCase() === value.toLowerCase()
                );
                if (existing) {
                    tagify.removeTags(value);
                } else {
                    tagify.addTags([value]);
                }
            }

            if (typeof filtersdata === 'function') {
                filtersdata();
            } else if (typeof fetchCandidates === 'function') {
                fetchCandidates(1);
            }
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

  function filtersdata() {
      if (typeof updateAppliedFiltersBar === 'function') {
          updateAppliedFiltersBar();
      }
      fetchCandidates();
  }

  function filtersdataget() {
      if (typeof updateAppliedFiltersBar === 'function') {
          updateAppliedFiltersBar();
      }
      fetchCandidates();
  }

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
        function showKtDrawer(drawerSelector, toggleSelector) {
        var drawerEl = document.querySelector(drawerSelector);
        if (drawerEl && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (!drawer && typeof KTDrawer === 'function') {
                drawer = new KTDrawer(drawerEl);
            }
            if (drawer && typeof drawer.show === 'function') {
                drawer.show();
                return;
            }
        }
        if (toggleSelector) {
            var toggleEl = document.querySelector(toggleSelector);
            if (toggleEl) {
                toggleEl.click();
            }
        }
    }

    $(document).on('click', '.client-show-detail', function (e){
        e.preventDefault();
        showKtDrawer('#kt_activitiesClientjob', '#kt_activitiesClientjob_toggle');
        var id = $(this).data('row-id');
        var url = "{{ route('service_company.jobs.viewmore',':id') }}?jobfindhide=1";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesclientjob_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                $('#kt_activitiesclientjob_body').html(response.view);
            },
            error: function(xhr) {
                $('#kt_activitiesclientjob_body').html('<div class="alert alert-danger m-4">Failed to load details.</div>');
            }
        });
    });

    // Market-jobs "View Requirement" — same drawer but hides hiring-manager, action-buttons and pipeline sections
    $(document).on('click', '.market-show-detail', function (e){
        e.preventDefault();
        showKtDrawer('#kt_activitiesClientjob', '#kt_activitiesClientjob_toggle');
        var id = $(this).data('row-id');
        var url = "{{ route('service_company.jobs.viewmore',':id') }}?jobfindhide=1&hideMarketSections=1";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesclientjob_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                $('#kt_activitiesclientjob_body').html(response.view);
            },
            error: function(xhr) {
                $('#kt_activitiesclientjob_body').html('<div class="alert alert-danger m-4">Failed to load requirement details.</div>');
            }
        });
    });

    $(document).on('click', '.client-hm-listing', function (e){
        e.preventDefault();
        showKtDrawer('#kt_activitiesHM', '#kt_activitiesHM_toggle');
        var id = $(this).data('row-id');
        var url = "{{ route('service_company.jobs.clienthmlisting',':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesHM_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                $('#kt_activitiesHM_body').html(response.view);
                $('#company_or_client').text(response.company_or_client);
            },
            error: function(xhr) {
                $('#kt_activitiesHM_body').html('<div class="alert alert-danger m-4">Failed to load hiring manager info.</div>');
            }
        });
    });

    $(document).on('click', '.open-bench-talents', function (e){
        e.preventDefault();
        showKtDrawer('#kt_activitiesBT', '#kt_activitiesBT_toggle');
        var id = $(this).data('row-id');
        var mod = $(this).data('mod') || '';
        var cur = $(this).data('cur') || '';
        var url = "{{ route('service_company.jobs.btlisting',':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                mod: mod,
                cur: cur
            },
            beforeSend: function() {
                $('#kt_activitiesBT_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                $('#kt_activitiesBT_body').html(response.view);
                $('#company_or_client').text(response.company_or_client);
            },
            error: function(xhr) {
                $('#kt_activitiesBT_body').html('<div class="alert alert-danger m-4">Failed to load bench candidates.</div>');
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

    $(document).on('click', '.client-terms-listing-find-job', function (e){
        e.preventDefault();
        showKtDrawer('#kt_activitiesTermsFindjob', '#kt_activitiesTermsFindjob_toggle');
        var id = $(this).data('row-id');
        var recommendation_relation_id = $(this).data('row-recommendation_relation_id') || '';
        var url = "{{ route('service_company.jobs.termshmlisting',':id') }}";
        url = url.replace(':id', id) + "?id=" + id;
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                recommendation_relation_id: recommendation_relation_id
            },
            beforeSend: function() {
                $('#kt_activitiesTermsFindJob_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                $('#kt_activitiesTermsFindJob_body').html(response.terms || response.view || '');
            },
            error: function(xhr) {
                $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">Failed to load terms & conditions.</div>');
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

          $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
              e.preventDefault();

              const page = $(this).data('page');
              if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

              // Disable the clicked pagination link
              $(this).addClass('disabled');

              // Fetch data
              fetchCandidates(page, $('#search').val()).then(() => {
                  // Re-enable the links after successful data fetch
                  $('#pagination .pagi-page, #pagination .pagi-arrow').removeClass('disabled');
              }).catch(() => {
                  // Re-enable the links even if there's an error
                  $('#pagination .pagi-page, #pagination .pagi-arrow').removeClass('disabled');
              });
          });

          $(document).on('change', '#pagination .pagi-perpage', function(e) {
              const selectedPerPage = $(this).val();
              if (selectedPerPage) {
                  localStorage.setItem('candidatePerPage', selectedPerPage);
                  fetchCandidates(1, $('#search').val());
              }
          });

        });


        function renderMarketJobCard(row) {
            // ── Status badge (OPEN / HOLD) ──
            let statusBadgeClass = 'jc-badge-open';
            let statusDotClass   = 'jc-dot-open';
            let statusText       = 'Active · Open';
            if (row.status_label === 'HOLD') {
                statusBadgeClass = 'jc-badge-hold';
                statusDotClass   = 'jc-dot-hold';
                statusText       = 'Hold';
            }

            // ── Employment type badge ──
            let typeBadgeClass = 'jc-badge-ft';
            let typeLabel = row.employment_type_badge || row.employment_type || 'Full-Time';
            if (typeLabel === 'Contract') {
                typeBadgeClass = 'jc-badge-ct';
            } else if (typeLabel === 'Bench Talent' || row.public_market_status === 2) {
                typeBadgeClass = 'jc-badge-bench';
            }

            // ── Skills HTML ──
            let skillTagsHtml = (row.skills || []).map(s => `<span class="skill-tag">${s}</span>`).join('');
            if (row.remaining_skills_count > 0) {
                skillTagsHtml += `<span class="skill-tag skill-more client-show-detail" data-row-id="${row.id}">+${row.remaining_skills_count}</span>`;
            }

            // ── Action button (Col 6 primary CTA) ──
            let actionBtnHtml = '';
            if (row.action_type === 'vendor_fulfilled') {
                actionBtnHtml = `<button data-row-status="" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-cta-primary" style="background:#EF4444;">Vendor Fulfilled</button>`;
            } else if (row.action_type === 'apply_agency') {
                actionBtnHtml = `<button class="btn-cta-primary request_approval" data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">Submit Candidates</button>`;
            } else if (row.action_type === 'reject') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-cta-primary" style="background:#EF4444;">Reject</button>`;
            } else if (row.action_type === 'under_review') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-cta-primary" style="background:#F59E0B;">Under Review</button>`;
            } else if (row.action_type === 'vendor_limit') {
                actionBtnHtml = `<button data-row-status="accept" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}" class="btn-cta-primary" style="background:#EF4444;">Vendor Limit</button>`;
            } else if (row.action_type === 'view_job' && row.view_job_url) {
                actionBtnHtml = `<a href="${row.view_job_url}" class="btn-cta-primary text-center">View Job</a>`;
            } else if (typeLabel === 'Contract') {
                actionBtnHtml = `<button data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}" class="btn-cta-primary btn-cta-purple open-bench-talents">Submit Contract Candidates</button>`;
            } else if (typeLabel === 'Bench Talent' || row.public_market_status === 2) {
                actionBtnHtml = `<button data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}" class="btn-cta-primary btn-cta-orange open-bench-talents">Match from Your Bench</button>`;
            } else {
                actionBtnHtml = `<button data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}" class="btn-cta-primary open-bench-talents">Submit Candidates</button>`;
            }

            // ── Commercial Terms subcard (Col 5) ──
            let termsTitle = 'Commercial Terms';
            let termsRows = '';
            const ct = row.commercial_terms;

            if (typeLabel === 'Contract') {
                let billingRate = (ct && ct.billing_rate) ? `₹${(ct.billing_rate/1000).toFixed(0)}K – ₹1.2L / Month` : '₹80K – ₹1.2L / Month';
                let billingModel = (ct && ct.billing_model) ? ct.billing_model : 'Monthly';
                let payTerms = (ct && ct.payment_days) ? `${ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Model</span><span class="jc-ct-val">${billingModel}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Rate</span><span class="jc-ct-val">${billingRate}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            } else if (typeLabel === 'Bench Talent' || row.public_market_status === 2) {
                termsTitle = 'Requirement Details';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Deployment Type</span><span class="jc-ct-val">Bench</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Work Mode</span><span class="jc-ct-val">${row.work_type || 'Remote'}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Availability</span><span class="jc-ct-val">Immediate</span></div>`;
            } else {
                let hiringFee = (ct && ct.hiring_fee_pct) ? `${ct.hiring_fee_pct}% of CTC` : '8.33% of CTC';
                let replacement = (ct && ct.replacement_days) ? `${ct.replacement_days} Days` : '90 Days';
                let payTerms = (ct && ct.payment_days) ? `${ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Hiring Fee</span><span class="jc-ct-val">${hiringFee}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Replacement</span><span class="jc-ct-val">${replacement}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            }

            // ── Posted Date display ──
            let postedDate = row.created_at_formatted || row.created_at_date || 'Recently';

            // ── Salary display ──
            let salaryDisplay = `${row.salary_currency}${row.min_salary} – ${row.max_salary} / ${row.salary_mode_short}`;

            // ── Location display (dynamically resolved from ES nested client_city_jobs) ──
            let locationText = row.location_display || (row.work_type === 'Remote' ? 'Remote' : (row.industry_name || row.work_type || '—'));

            // ── Card left border highlight accent (Green / Yellow / Orange) ──
            let cardAccentClass = 'jc-accent-green';
            if (row.status_label === 'HOLD' || typeLabel === 'Contract') {
                cardAccentClass = 'jc-accent-yellow';
            } else if (typeLabel === 'Bench Talent' || row.public_market_status === 2) {
                cardAccentClass = 'jc-accent-green';
            }

            return `
            <div class="job-card ${cardAccentClass}">

                <!-- ── COL 1: Title, Badges, Subtitle, Skills ── -->
                <div class="jc-col-main">
                    <div class="jc-badges-row">
                        <span class="jc-badge ${statusBadgeClass}">
                            <span class="jc-dot-mini"></span>${row.status_label || 'OPEN'}
                        </span>
                        <span class="jc-badge ${typeBadgeClass}">${typeLabel}</span>
                        <span class="jc-req-id">${row.req_id}</span>
                    </div>

                    <h2 class="jc-job-title" title="${row.title}">${row.title_short || row.title}</h2>
                    <div class="jc-job-dept" title="${row.department || ''}">${row.department || '—'}</div>
                    <div class="skills-row">${skillTagsHtml}</div>
                </div>

                <!-- ── COL 2: Meta Info (Icons + Data ONLY, NO Headings) ── -->
                <div class="jc-col-meta">
                    <div class="jc-meta-item" title="Location">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>${locationText}</span>
                    </div>
                    <div class="jc-meta-item" title="Work Mode">
                        <i class="fa-solid fa-laptop"></i>
                        <span>${row.work_type}</span>
                    </div>
                    <div class="jc-meta-item" title="Experience">
                        <i class="fa-regular fa-user"></i>
                        <span>${row.min_exp} – ${row.max_exp} Years</span>
                    </div>
                    <div class="jc-meta-item" title="Openings">
                        <i class="fa-solid fa-users"></i>
                        <span>${row.total_positions} Openings</span>
                    </div>
                    <div class="jc-meta-item" title="Salary">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                        <span>${salaryDisplay}</span>
                    </div>
                    <div class="jc-meta-item" title="Industry">
                        <i class="fa-regular fa-building"></i>
                        <span>${row.industry_name || row.department || 'IT Services'}</span>
                    </div>
                </div>

                <!-- ── COL 3: Dates & Status ── -->
                <div class="jc-col-dates">
                    <div class="jc-date-item">
                        <i class="fa-regular fa-calendar"></i>
                        <div class="jc-date-content">
                            <span class="jc-date-label">Posted</span>
                            <span class="jc-date-val">${postedDate}</span>
                        </div>
                    </div>
                    <div class="jc-date-item">
                        <i class="fa-solid fa-rotate"></i>
                        <div class="jc-date-content">
                            <span class="jc-date-label">Last Updated</span>
                            <span class="jc-date-val jc-text-green">${row.updated_at_ago || 'Recently'}</span>
                        </div>
                    </div>
                    <div class="jc-date-item">
                        <i class="fa-regular fa-clock"></i>
                        <div class="jc-date-content">
                            <span class="jc-date-label">Submission Deadline</span>
                            <span class="jc-date-val ${row.submission_deadline_formatted ? 'jc-text-red' : 'jc-text-green'}">
                                ${row.submission_deadline_formatted || 'Open till filled'}
                            </span>
                        </div>
                    </div>
                    <div class="jc-status-row">
                        <span class="jc-date-label">Status</span>
                        <span class="jc-status-indicator">
                            <span class="${statusDotClass}"></span>
                            ${statusText}
                        </span>
                    </div>
                </div>

                <!-- ── COL 4: Sub-card 1 (Your Bench Match) ── -->
                <div class="jc-subcard jc-bench-subcard open-bench-talents"
                     data-row-id="${row.id}" data-mod="${row.mod}" data-cur="${row.cur}"
                     role="button" tabindex="0" title="View matching bench talents">
                    <div class="jc-bench-header">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Your Bench Match</span>
                    </div>
                    <div class="jc-bench-num">${row.bench_match_count || 0}</div>
                    <div class="jc-bench-desc">Matching candidates</div>
                    <div class="jc-subcard-link">View Matches &rarr;</div>
                </div>

                <!-- ── COL 5: Sub-card 2 (Commercial Terms / Details) ── -->
                <div class="jc-subcard jc-terms-subcard">
                    <div class="jc-subcard-title">${termsTitle}</div>
                    <div class="jc-terms-rows">
                        ${termsRows}
                    </div>
                    <a class="jc-subcard-link client-terms-listing-find-job"
                       data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">
                       View Full Terms &rarr;
                    </a>
                </div>

                <!-- ── COL 6: Action Buttons Column ── -->
                <div class="jc-col-actions">
                    ${actionBtnHtml}
                    <button class="btn-cta-secondary market-show-detail" data-row-id="${row.id}">View Requirement</button>
                    <button class="btn-cta-secondary client-terms-listing-find-job" data-row-id="${row.id}" data-row-recommendation_relation_id="${row.recommendation_relation_id || ''}">View Terms</button>
                    <div class="jc-actions-footer">
                        <i class="fa-regular fa-bookmark jc-icon-btn" title="Save Job"></i>
                        <i class="fa-solid fa-ellipsis-vertical jc-icon-btn" title="More Options"></i>
                    </div>
                </div>

            </div>`;
        }

        let currentActiveTab = 'all';

        // ── Custom Sort dropdown (Apptracker style) ──
        function simfRenderSortPanel() {
            var $panel = $('#simf_sort_panel').empty();
            var current = $('#hiringtype').val();
            $('#hiringtype option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="simf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#hiringtype').val(val);
                        $('#simf_sort_label').text(label);
                        simfCloseSortPanel();
                        filtersdata();
                    })
                    .appendTo($panel);
            });
        }
        function simfOpenSortPanel() {
            simfRenderSortPanel();
            $('#simf_sort_panel').removeClass('d-none');
            $('#simf_sort_btn').addClass('open');
        }
        function simfCloseSortPanel() {
            $('#simf_sort_panel').addClass('d-none');
            $('#simf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#simf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#simf_sort_panel').hasClass('d-none')) simfOpenSortPanel();
            else simfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#simf_sort_wrap').length) simfCloseSortPanel();
        });

        // ── Live Search with Debounce (Apptracker style) ──
        let searchDebounceTimer;
        $(document).on('keyup input', '#kt_tagify_2', function(e) {
            clearTimeout(searchDebounceTimer);
            if (e.keyCode === 13) {
                filtersdataget();
                return;
            }
            searchDebounceTimer = setTimeout(function() {
                filtersdataget();
            }, 350);
        });

        // ── Active Filters Summary Bar ──
        function updateAppliedFiltersBar() {
            let activeFilters = [];
            let searchVal = $('#kt_tagify_2').val();
            if (searchVal && searchVal.trim()) {
                activeFilters.push({ key: 'search', label: `Search: "${searchVal.trim()}"`, clear: function() { $('#kt_tagify_2').val(''); } });
            }
            let minExp = $('#min_exp').val();
            let maxExp = $('#max_exp').val();
            if (minExp || maxExp) {
                activeFilters.push({ key: 'exp', label: `Experience: ${minExp || 0} – ${maxExp || '+'} Yrs`, clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); } });
            }
            let minSal = $('#min_sal').val();
            let maxSal = $('#max_sal').val();
            if (minSal || maxSal) {
                activeFilters.push({ key: 'sal', label: `Salary: ₹${minSal || 0} – ₹${maxSal || '+'}`, clear: function() { $('#min_sal').val(''); $('#max_sal').val(''); } });
            }
            let locVal = $('#kt_tagify_location').val();
            if (locVal) {
                activeFilters.push({ key: 'loc', label: `Location: ${locVal}`, clear: function() { $('#kt_tagify_location').val(''); } });
            }

            let $card = $('#simf_applied_card');
            let $chips = $('#simf_applied_chips').empty();
            if (activeFilters.length > 0) {
                $('#simf_applied_count').text(activeFilters.length);
                $('#simf_count_badge').text(activeFilters.length).removeClass('d-none');
                activeFilters.forEach(function(f) {
                    $(`<div class="simf-applied-chip"><span>${f.label}</span><span class="simf-applied-chip-remove">&times;</span></div>`)
                        .find('.simf-applied-chip-remove')
                        .on('click', function() {
                            f.clear();
                            updateAppliedFiltersBar();
                            filtersdata();
                        })
                        .end()
                        .appendTo($chips);
                });
                $card.removeClass('d-none');
            } else {
                $('#simf_count_badge').text('0').addClass('d-none');
                $card.addClass('d-none');
            }
        }

        function clearAllAppliedFilters() {
            $('#kt_tagify_2').val('');
            $('#min_exp').val('');
            $('#max_exp').val('');
            $('#min_sal').val('');
            $('#max_sal').val('');
            $('#kt_tagify_location').val('');
            updateAppliedFiltersBar();
            filtersdata();
        }

        $(document).on('click', '.mjh-tab-item', function() {
            $('.mjh-tab-item').removeClass('active');
            $(this).addClass('active');
            currentActiveTab = $(this).data('tab') || 'all';
            fetchCandidates(1);
        });

        function fetchCandidates(page = 1, search = '') {
            $('.loadingMessage').css({ display: 'block' });
            var perPage = localStorage.getItem('candidatePerPage') || 10;
            return $.ajax({
                url: `{!! route('service_company.jobs.marketJobListV2_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  custom_tab: currentActiveTab,
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
                  app_int_outsource:$('[name="app_int_outsource"]:checked').val(),
                  per_page: perPage,
                  size: perPage
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    console.log('[MarketJobs] AJAX success. status=' + response.status + ' total=' + response.total);
                    console.log('[MarketJobs] items type=' + typeof response.items + ' isArray=' + Array.isArray(response.items));
                    if (response.items) { console.log('[MarketJobs] items length=' + (response.items.length || 'N/A (not array)')); console.log('[MarketJobs] first item:', response.items[0] || response.items.data && response.items.data[0]); }

                    var items = response.items;
                    // Handle both plain array [] and ResourceCollection {data:[]} wrapping
                    if (items && !Array.isArray(items) && Array.isArray(items.data)) {
                        items = items.data;
                    }
                    if (items && Array.isArray(items)) {
                        if (items.length > 0) {
                            try {
                                let html = items.map(function(item, idx) {
                                    try {
                                        return renderMarketJobCard(item);
                                    } catch(cardErr) {
                                        console.error('[MarketJobs] renderMarketJobCard failed for item #' + idx, cardErr, item);
                                        return '<div class="alert alert-warning m-2">Card render error: ' + cardErr.message + '</div>';
                                    }
                                }).join('');
                                console.log('[MarketJobs] Rendered HTML length=' + html.length);
                                $('#marketJobLists').html(html);
                                $('#marketJobLists').css({ display: 'block', minHeight: '100px' });
                            } catch(renderErr) {
                                console.error('[MarketJobs] Render pipeline error:', renderErr);
                                $('#marketJobLists').html('<div class="alert alert-danger m-4">Render error: ' + renderErr.message + '</div>');
                            }
                        } else {
                            $('#marketJobLists').html('<div class="text-center py-5 text-muted">No job listings found matching your criteria.</div>');
                        }
                    } else if (response.html) {
                        $('#marketJobLists').html(response.html);
                    } else {
                        $('#marketJobLists').html('<div class="text-center py-5 text-muted">No job listings found.</div>');
                    }
                    $('#span_jobMarket').html(response.total);
                    $('#pagination').html(response.pagination);
                },
                error: function(xhr, status, error) {
                    $('.loadingMessage').css({ display: 'none' });
                    var msg = 'Error loading jobs.';
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        msg = resp.message || resp.error || msg;
                    } catch(e) {
                        msg = xhr.status + ' ' + error;
                    }
                    $('#marketJobLists').html('<div class="alert alert-danger m-4">Failed to load jobs: ' + msg + '</div>');
                    console.error('[MarketJobs AJAX Error]', xhr.status, error, xhr.responseText);
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

