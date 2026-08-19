

@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<style>
    .tagify {
        --tags-border-color: #E4E6EF;
        --tags-hover-border-color: #B5B5C3;
        --tags-focus-border-color: #3E97FF;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
    }
    .tagify__tag .star {
        cursor: pointer;
    }
    .tagify__tag .star.checked {
        color: #eab308 !important;
    }
    .tagify__tag__removeBtn,
    .tagify__tag__removeBtn::before,
    .tagify__tag__removeBtn::after {
        mask-image: none !important;
        -webkit-mask-image: none !important;
        content: none !important;
    }
    .tagify__tag__removeBtn:hover {
        background: rgba(37,99,235,.28) !important;
        color: #1d4ed8 !important;
    }
    body > .tagify__dropdown {
        z-index: 1400 !important;
    }
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

    /* ══════════════════════════════════════════
       Market Jobs Filter Drawer  (simf-* reused, mktf-* for scoping)
    ══════════════════════════════════════════ */
    #kt_market_filter_drawer.simf-drawer {
        position: fixed !important;
        top: 0 !important; right: 0 !important; bottom: 0 !important; left: auto !important;
        height: 100vh !important; max-height: 100vh !important;
        width: 80% !important; max-width: 80% !important;
        overflow: hidden !important; z-index: 1200;
    }
    #mktf_card.simf-card {
        display: flex !important; flex-direction: column !important;
        font-family: 'Poppins', sans-serif;
        position: relative !important;
        height: 100vh !important; max-height: 100vh !important; overflow: hidden !important;
        background: #fff;
    }
    #mktf_card .simf-body {
        flex: 1 1 auto !important; min-height: 0 !important;
        overflow-y: auto !important; overflow-x: hidden !important;
        padding: 8px 24px 24px !important;
    }
    #mktf_card #mktf_form {
        margin: 0;
        padding: 0;
    }
    #mktf_card .simf-section {
        padding: 18px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    #mktf_card .simf-section-last {
        border-bottom: none;
        padding-bottom: 8px;
    }
    #mktf_card .simf-header, #mktf_card .simf-footer { flex-shrink: 0 !important; }

    .simf-skill-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.2;
    }
    .simf-skill-chip:hover {
        background: #EEF2FF;
        border-color: #C7D2FE;
        color: #4F46E5;
    }
    .simf-skill-chip.active {
        background: #EEF2FF;
        border-color: #818CF8;
        color: #3730A3;
        font-weight: 600;
    }

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

</style>

<link href="{{ asset('demo/css/find-market-jobs.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
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
                                <input type="text" class="simf-search-input search-input" name="inputJobSearch" id="kt_tagify_2"
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

                            {{-- Filter Button — KTDrawer toggle --}}
                            <button type="button" id="kt_market_filter_toggle" class="simf-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="mktf_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary simf-count-badge d-none" id="simf_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
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

                    <!-- ACTIVE FILTER TAGS -->
                    <div class="active-filters" id="selectedFilters" style="display:none;"></div>

                    {{-- ── Horizontal Filter Tabs ── --}}
                    <div class="mjh-tabs-bar">
                        <div class="mjh-tab-item active" data-tab="all">
                            <i class="fa-solid fa-briefcase mjh-tab-icon"></i>
                            <span>All Jobs</span>
                            <span class="mjh-tab-badge mjh-badge-blue">{{ number_format($all_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="fulltime">
                            <i class="fa-solid fa-user-tie mjh-tab-icon"></i>
                            <span>Full-Time</span>
                            <span class="mjh-tab-badge mjh-badge-green">{{ number_format($fulltime_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="contract">
                            <i class="fa-solid fa-file-contract mjh-tab-icon"></i>
                            <span>Contract</span>
                            <span class="mjh-tab-badge mjh-badge-amber">{{ number_format($contract_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="freelance">
                            <i class="fa-solid fa-laptop-code mjh-tab-icon"></i>
                            <span>Freelance</span>
                            <span class="mjh-tab-badge mjh-badge-purple">{{ number_format($freelance_jobs ?? 0) }}</span>
                        </div>
                        <div class="mjh-tab-item" data-tab="parttime">
                            <i class="fa-solid fa-business-time mjh-tab-icon"></i>
                            <span>Part-Time</span>
                            <span class="mjh-tab-badge mjh-badge-indigo">{{ number_format($parttime_jobs ?? 0) }}</span>
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

        <!--Start Filter Drawer — KTDrawer enterprise design (mirrors similar_candidates)-->
        <div id="kt_market_filter_drawer" class="bg-body simf-drawer" data-kt-drawer="true" data-kt-drawer-name="market-filter"
            data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
            data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
            data-kt-drawer-toggle="#kt_market_filter_toggle" data-kt-drawer-close="#mktf_close_x">
            <div id="mktf_card" class="card shadow-none rounded-0 w-100 h-100 simf-card">

                <!--begin::Header-->
                <div class="simf-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="simf-header-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="simf-title">Filter Jobs</h3>
                                <span class="simf-count-pill" id="mktf_header_count">0</span>
                            </div>
                            <p class="simf-subtitle">Refine your search and find the best opportunities</p>
                        </div>
                    </div>
                    <button type="button" class="simf-close" id="mktf_close_x" title="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <!--end::Header-->

                <!--begin::Body-->
                <div class="simf-body">
                    <form id="mktf_form">

                        <!--begin::Active filters bar — live draft preview inside drawer-->
                        <div class="simf-activebar d-none" id="mktf_active_bar">
                            <div class="simf-activebar-head">
                                <span class="simf-activebar-label">Active Filters <span id="mktf_active_count">(0)</span></span>
                                <a href="javascript:void(0);" class="simf-clearall" id="mktf_clear_all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                    Clear All
                                </a>
                            </div>
                            <div class="simf-chips" id="mktf_active_chips"></div>
                        </div>
                        <!--end::Active filters bar-->

                        <!--begin::Skills-->
                        <div class="simf-section">
                            <div class="simf-section-head">
                                <span class="simf-icon simf-icon-blue">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                </span>
                                <div>
                                    <div class="simf-section-title">Skills / Keywords <span class="simf-badge-primary">Primary</span></div>
                                    <div class="simf-section-desc">Add skills to find relevant jobs &mdash; click &#9733; to make a skill mandatory</div>
                                </div>
                            </div>
                            <div class="simf-section-body">
                                <input class="form-control form-control-solid search-input" name="kt_tagify_users" value="" placeholder="Search by skills..." id="kt_tagify_users" />
                                <div class="simf-subtext" style="font-size:11.5px;color:#94a3b8;margin-top:6px;">
                                    Press <strong style="color:#6366f1;">Enter</strong> or <strong style="color:#6366f1;">,</strong> to add a keyword &middot; Press <strong style="color:#6366f1;">Backspace</strong> to remove last
                                </div>
                                <div class="simf-popular-searches" style="margin-top:10px;display:flex;flex-wrap:wrap;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:500;color:#64748b;">Popular searches:</span>
                                    <button type="button" class="simf-skill-chip" data-skill="Java Developer">Java Developer</button>
                                    <button type="button" class="simf-skill-chip" data-skill=".NET Developers">.NET Developers</button>
                                    <button type="button" class="simf-skill-chip" data-skill="SAP Consultants">SAP Consultants</button>
                                    <button type="button" class="simf-skill-chip" data-skill="AWS">AWS</button>
                                    <button type="button" class="simf-skill-chip" data-skill="React Developers">React Developers</button>
                                    <button type="button" class="simf-skill-chip" data-skill="Node.js">Node.js</button>
                                    <button type="button" class="simf-skill-chip" data-skill="Python Developer">Python Developer</button>
                                    <button type="button" class="simf-skill-chip" data-skill="DevOps Engineer">DevOps Engineer</button>
                                </div>
                            </div>
                        </div>
                        <!--end::Skills-->

                        <!--begin::Location-->
                        <div class="simf-section">
                            <div class="simf-section-head">
                                <span class="simf-icon simf-icon-green">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <div>
                                    <div class="simf-section-title">Preferred Location</div>
                                    <div class="simf-section-desc">Preferred cities or work locations</div>
                                </div>
                            </div>
                            <div class="simf-section-body">
                                <div class="simf-loc-inputwrap" id="mktf_loc_inputwrap">
                                    <input class="form-control form-control-solid search-input" value="{{ @$default_loc }}" id="kt_tagify_location" placeholder="Enter your Location..." />
                                    <button type="button" class="simf-loc-chevron" id="mktf_loc_chevron" title="Show suggestions">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                </div>
                                <div class="simf-subtext" style="font-size:11.5px;color:#94a3b8;margin-top:6px;">
                                    Press <strong style="color:#6366f1;">Enter</strong> or <strong style="color:#6366f1;">,</strong> to add a location &middot; Press <strong style="color:#6366f1;">Backspace</strong> to remove last
                                </div>
                                <div class="simf-loc-suggestions d-none" id="mktf_loc_panel">
                                    <div class="simf-loc-label">Popular Locations</div>
                                    <div class="simf-loc-chip-row">
                                        <button type="button" class="simf-loc-chip" data-city="India">India</button>
                                        <button type="button" class="simf-loc-chip" data-city="Bangalore">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                            Bangalore
                                        </button>
                                        <button type="button" class="simf-loc-chip" data-city="Hyderabad">Hyderabad</button>
                                        <button type="button" class="simf-loc-chip" data-city="Pune">Pune</button>
                                        <button type="button" class="simf-loc-chip" data-city="Mumbai">Mumbai</button>
                                        <button type="button" class="simf-loc-chip" data-city="Chennai">Chennai</button>
                                        <button type="button" class="simf-loc-chip" data-city="Remote">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>
                                            Remote
                                        </button>
                                    </div>
                                    <div class="simf-loc-label mt-2">Other Suggestions</div>
                                    <div class="simf-loc-chip-row">
                                        <button type="button" class="simf-loc-chip" data-city="Noida">Noida</button>
                                        <button type="button" class="simf-loc-chip" data-city="Gurgaon">Gurgaon</button>
                                        <button type="button" class="simf-loc-chip" data-city="Kolkata">Kolkata</button>
                                        <button type="button" class="simf-loc-chip" data-city="Ahmedabad">Ahmedabad</button>
                                        <button type="button" class="simf-loc-chip" data-city="Chandigarh">Chandigarh</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Location-->

                        <!--begin::Experience-->
                        <div class="simf-section">
                            <div class="simf-section-head">
                                <span class="simf-icon simf-icon-green">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                </span>
                                <div>
                                    <div class="simf-section-title">Experience (Years)</div>
                                    <div class="simf-section-desc">Total work experience required</div>
                                </div>
                            </div>
                            <div class="simf-section-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="simf-mini-label">Minimum Experience</label>
                                        <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                            <option value="" selected="">Min Experience</option>
                                            <option value="0" {{ 0 == $min_exp ? 'selected' : '' }}>Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}" {{ $i == $min_exp ? 'selected' : '' }}>{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="simf-mini-label">Maximum Experience</label>
                                        <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                            <option value="" selected="">Max Experience</option>
                                            <option value="0" {{ 0 == $max_exp ? 'selected' : '' }}>Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}" {{ $i == $max_exp ? 'selected' : '' }}>{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                        <div class="fv-plugins-message-container" id="experience_error" style="color:red !important;text-align:center;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Experience-->

                        <!--begin::Salary-->
                        <div class="simf-section simf-section-last">
                            <div class="simf-section-head">
                                <span class="simf-icon simf-icon-amber">
                                    <span style="font-size:15px;font-weight:700;line-height:1;">&#8377;</span>
                                </span>
                                <div>
                                    <div class="simf-section-title">Salary (CTC)</div>
                                    <div class="simf-section-desc">Salary range with currency &amp; mode</div>
                                </div>
                            </div>
                            <div class="simf-section-body">
                                <div class="row g-2 simf-salary-row">
                                    <div class="col-3">
                                        <label class="simf-mini-label">Currency</label>
                                        <select class="form-select form-select-solid" name="min_salary_currency" id="min_salary_currency">
                                            <option value="">Select currency</option>
                                            <option value="0" {{ 0 == $min_salary_currency ? 'selected' : '' }}>INR (&#8377;)</option>
                                            <option value="1" {{ 1 == $min_salary_currency ? 'selected' : '' }}>EUR</option>
                                            <option value="2" {{ 2 == $min_salary_currency ? 'selected' : '' }}>JPY</option>
                                            <option value="3" {{ 3 == $min_salary_currency ? 'selected' : '' }}>GBP</option>
                                            <option value="4" {{ 4 == $min_salary_currency ? 'selected' : '' }}>USD</option>
                                            <option value="5" {{ 5 == $min_salary_currency ? 'selected' : '' }}>AED</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="simf-mini-label">Mode</label>
                                        <select class="form-select form-select-solid" name="sal_mode" id="sal_mode">
                                            <option value="">Select Mode</option>
                                            <option value="0" {{ 0 == $min_sal_mode ? 'selected' : '' }}>Annual</option>
                                            <option value="1" {{ 1 == $min_sal_mode ? 'selected' : '' }}>Monthly</option>
                                            <option value="2" {{ 2 == $min_sal_mode ? 'selected' : '' }}>Hourly</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="simf-mini-label">Min Salary</label>
                                        <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="{{ $min_salary }}">
                                        <span class="form-text text-muted minimum_salary_word"></span>
                                    </div>
                                    <div class="col-3">
                                        <label class="simf-mini-label">Max Salary</label>
                                        <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Any" value="{{ $max_salary }}">
                                        <span class="form-text text-muted maximum_salary_word"></span>
                                    </div>
                                    <span class="form-text text-muted" id="salary_error" style="color:red !important;text-align:center;"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Salary-->

                    </form>
                </div>
                <!--end::Body-->

                <!--begin::Footer-->
                <div class="simf-footer">
                    <div class="simf-footer-count">
                        <span class="simf-footer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        </span>
                        <div>
                            <div class="simf-footer-number" id="mktf_footer_number">—</div>
                            <div class="simf-footer-label">Jobs Match <span class="simf-footer-hint" title="Updates after Apply">&#9432;</span></div>
                        </div>
                    </div>
                    <div class="simf-footer-actions">
                        <button type="button" class="btn btn-sm btn-light" id="mktf_close_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            Close
                        </button>
                        <button type="button" class="btn btn-sm btn-light" id="mktf_reset_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                            Reset
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="mktf_apply_btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                            Apply Filters
                        </button>
                    </div>
                </div>
                <!--end::Footer-->

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
        /* ── Skills Tagify Initialization ── */
        var inputElm = document.querySelector('#kt_tagify_users');
        window.tagify = null;

        if (inputElm) {
<<<<<<< HEAD
            tagify = new Tagify(inputElm, {
                enforceWhitelist: false,
                dropdown: {
                    enabled: 1,
                    maxItems: 20,
                    closeOnSelect: true,
                    appendTarget: document.body
                },
=======
            window.tagify = new Tagify(inputElm, {
                whitelist: [],
>>>>>>> 557ee10dcc4207d9dac0e24c633fa4f491f3cfaf
                templates: {
                    tag: function(tagData) {
                        tagData = tagData || {};
                        const value = tagData.value || '';
                        const isChecked = tagData.checked ? 'checked' : '';
                        const mandatoryClass = tagData.checked ? 'simf-mandatory-tag' : '';
                        const starGlyph = tagData.checked ? '&#9733;' : '&#9734;';
                        const chipBg    = tagData.checked ? '#dbeafe' : '#eef2ff';
                        const chipBdr   = tagData.checked ? '#bfdbfe' : '#e0e7ff';
                        const starColor = tagData.checked ? '#eab308' : '#94a3b8';
                        const textColor = tagData.checked ? '#1d4ed8' : '#3730a3';
                        return `
                            <tag title="${value}" contenteditable='false' spellcheck="false"
                                class="tagify__tag ${mandatoryClass} ${tagData.class ? tagData.class : ""}"
                                ${this.getAttributes(tagData)}
                                style="display:inline-flex;align-items:center;gap:6px;background:${chipBg};border:1px solid ${chipBdr};border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                                <span class="star ${isChecked}" style="cursor:pointer;font-size:15px;line-height:1;color:${starColor};flex-shrink:0;margin: 1px 3px 0px 3px;">${starGlyph}</span>
                                <span class="tagify__tag-text" style="cursor:pointer;font-size:11.5px;font-weight:500;color:${textColor};line-height:1;white-space:nowrap;padding: 0px 0px 0px 6px;">${value}</span>
                                <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
                            </tag>
                        `;
                    }
                },
                transformTag: function(tagData) {
                    tagData.checked = tagData.checked || false;
                    return tagData;
                },
                dropdown: {
                    maxItems: 20,
                    classname: "",
                    enabled: 0,
                    closeOnSelect: true,
                    appendTarget: document.body
                }
            });

            tagify.on('blur', function() {
                if (typeof mktfSyncPendingInputs === 'function') {
                    mktfSyncPendingInputs();
                }
            });

            // Toggle mandatory (star) on click of EITHER the star itself OR the keyword text
            document.addEventListener('click', function(e) {
                const toggleTarget = e.target.closest('.star, .tagify__tag-text');
                if (toggleTarget && window.tagify) {
                    const tagElm = toggleTarget.closest('.tagify__tag');
                    if (!tagElm) return;
                    const starEl = tagElm.querySelector('.star');
                    const textEl = tagElm.querySelector('.tagify__tag-text');
                    if (!starEl || !textEl) return;
                    const value = textEl.textContent;

                    starEl.classList.toggle('checked');
                    const nowChecked = starEl.classList.contains('checked');

                    starEl.innerHTML = nowChecked ? '&#9733;' : '&#9734;';
                    starEl.style.color = nowChecked ? '#eab308' : '#94a3b8';
                    tagElm.style.background = nowChecked ? '#dbeafe' : '#eef2ff';
                    tagElm.style.borderColor = nowChecked ? '#bfdbfe' : '#e0e7ff';
                    textEl.style.color = nowChecked ? '#1d4ed8' : '#3730a3';
                    tagElm.classList.toggle('simf-mandatory-tag', nowChecked);

                    if (Array.isArray(window.tagify.value)) {
                        const tagData = window.tagify.value.find(tag => tag.value === value);
                        if (tagData) {
                            setTimeout(() => {
                                tagData.checked = nowChecked;
                                window.tagify.updateValueByDOMTags();
                                if (typeof mktfUpdateActiveFiltersUI === 'function') mktfUpdateActiveFiltersUI();
                            }, 300);
                        }
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
                    url: "{{ url('/') }}" + "/skill-suggestions?q=" + encodeURIComponent(query),
                    method: 'GET',
                    success: function(data) {
                        if (Array.isArray(data) && window.tagify) {
                            window.tagify.settings.whitelist = data;
                            window.tagify.dropdown.show.call(window.tagify, query);
                        }
                    },
                    error: function(err) {
                        console.error("Error fetching Skill:", err);
                    }
                });
            }, 200);

            window.tagify.on('input', function(e) {
                var query = (e.detail.value || '').trim();
                if (query.length < 1) return;
                fetchSuggestionsSkills(query);
            });

<<<<<<< HEAD
            $(document).on('click', '.simf-skill-chip', function() {
                var skill = $(this).data('skill');
                if (skill && tagify) {
                    tagify.addTags([skill]);
                    $(this).addClass('active');
                }
            });

            tagify.on('add', function() {
=======
            window.tagify.on('add', function() {
>>>>>>> 557ee10dcc4207d9dac0e24c633fa4f491f3cfaf
                if (typeof mktfUpdateActiveFiltersUI === 'function') mktfUpdateActiveFiltersUI();
            });
            window.tagify.on('remove', function() {
                if (typeof mktfUpdateActiveFiltersUI === 'function') mktfUpdateActiveFiltersUI();
            });
        }

        /* ── Location Tagify Initialization ── */
        var inputloc = document.querySelector("#kt_tagify_location");
        window.tagifylocation = null;

        if (inputloc) {
<<<<<<< HEAD
            tagifylocation = new Tagify(inputloc, {
                whitelist: cities,
                enforceWhitelist: false,
=======
            window.tagifylocation = new Tagify(inputloc, {
                whitelist: [],
>>>>>>> 557ee10dcc4207d9dac0e24c633fa4f491f3cfaf
                maxTags: 10,
                templates: {
                    tag: function(tagData) {
                        const value = tagData.value || '';
                        return `
                            <tag title="${value}" contenteditable='false' spellcheck="false"
                                class="tagify__tag ${tagData.class ? tagData.class : ""}"
                                ${this.getAttributes(tagData)}
                                style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #e0e7ff;border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                                <span class="tagify__tag-text" style="font-size:11.5px;font-weight:500;color:#3730a3;line-height:1;white-space:nowrap;">${value}</span>
                                <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
                            </tag>
                        `;
                    }
                },
                dropdown: {
                    maxItems: 20,
                    classname: "",
                    enabled: 0,
                    closeOnSelect: true,
                    appendTarget: document.body
                }
            });

            tagifylocation.on('blur', function() {
                if (typeof mktfSyncPendingInputs === 'function') {
                    mktfSyncPendingInputs();
                }
            });

            function mktfCloseLocPanel() {
                $('#mktf_loc_panel').addClass('d-none');
                $('#mktf_loc_chevron').removeClass('open');
            }

            $(document).on('click', '#mktf_loc_chevron', function(e) {
                e.stopPropagation();
                $('#mktf_loc_panel').toggleClass('d-none');
                $(this).toggleClass('open');
            });

            $(document).on('click', '.simf-loc-chip', function() {
                var city = $(this).data('city');
                var $btn = $(this);
                if (!city || !window.tagifylocation) return;
                $btn.prop('disabled', true);
                $.ajax({
                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(city),
                    method: 'GET',
                    success: function(data) {
                        $btn.prop('disabled', false);
                        if (Array.isArray(data) && data.length > 0) {
                            var match = data.find(function(item) {
                                return String(item).toLowerCase().indexOf(city.toLowerCase()) === 0;
                            }) || data[0];
                            window.tagifylocation.addTags([match]);
                            mktfCloseLocPanel();
                        } else {
                            window.tagifylocation.addTags([city]);
                            mktfCloseLocPanel();
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        window.tagifylocation.addTags([city]);
                        mktfCloseLocPanel();
                    }
                });
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#mktf_loc_inputwrap, #mktf_loc_panel').length) {
                    mktfCloseLocPanel();
                }
            });

            window.tagifylocation.on('dropdown:select', function(e) {
                window.tagifylocation.addTags([e.detail.data.value]);
                mktfCloseLocPanel();
            });
            window.tagifylocation.on('add', function() {
                if (typeof mktfUpdateActiveFiltersUI === 'function') mktfUpdateActiveFiltersUI();
                mktfCloseLocPanel();
            });
            window.tagifylocation.on('remove', function() {
                if (typeof mktfUpdateActiveFiltersUI === 'function') mktfUpdateActiveFiltersUI();
            });

            function debounceloc(func, delay) {
                let timer;
                return function (...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), delay);
                };
            }

            const fetchSuggestionsLocation = debounceloc(function(query) {
                $.ajax({
                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                    method: 'GET',
                    success: function(data) {
                        if (Array.isArray(data) && window.tagifylocation) {
                            window.tagifylocation.settings.whitelist = data;
                            mktfCloseLocPanel();
                            window.tagifylocation.dropdown.show.call(window.tagifylocation, query);
                        }
                    },
                    error: function(err) {
                        console.error("Error fetching cities:", err);
                    }
                });
            }, 200);

            window.tagifylocation.on('input', function(e) {
                var query = (e.detail.value || '').trim();
                if (query.length < 2) return;
                mktfCloseLocPanel();
                fetchSuggestionsLocation(query);
            });
        }

        /* ── Enforce Drawer Layout (Fix Vertical Scroller & Pin Header/Footer) ── */
        function simfEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_market_filter_drawer');
            var cardEl = document.getElementById('mktf_card');
            var bodyEl = cardEl ? cardEl.querySelector('.simf-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.simf-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.simf-footer') : null;
            if (drawerEl) {
                drawerEl.style.setProperty('overflow', 'hidden', 'important');
                drawerEl.style.setProperty('position', 'fixed', 'important');
                drawerEl.style.setProperty('top', '0', 'important');
                drawerEl.style.setProperty('bottom', '0', 'important');
                drawerEl.style.setProperty('height', '100vh', 'important');
            }
            if (cardEl) {
                cardEl.style.setProperty('display', 'block', 'important');
                cardEl.style.setProperty('position', 'relative', 'important');
                cardEl.style.setProperty('overflow', 'hidden', 'important');
                cardEl.style.setProperty('height', '100vh', 'important');
                cardEl.style.setProperty('max-height', '100vh', 'important');
            }
            if (bodyEl) {
                var headerH = headerEl ? headerEl.offsetHeight : 0;
                var footerH = footerEl ? footerEl.offsetHeight : 0;
                var available = Math.max(window.innerHeight - headerH - footerH, 100);
                bodyEl.style.setProperty('position', 'absolute', 'important');
                bodyEl.style.setProperty('left', '0', 'important');
                bodyEl.style.setProperty('right', '0', 'important');
                bodyEl.style.setProperty('top', headerH + 'px', 'important');
                bodyEl.style.setProperty('height', available + 'px', 'important');
                bodyEl.style.setProperty('max-height', available + 'px', 'important');
                bodyEl.style.setProperty('overflow-y', 'auto', 'important');
                bodyEl.style.setProperty('overflow-x', 'hidden', 'important');
            }
            if (footerEl) {
                footerEl.style.setProperty('position', 'absolute', 'important');
                footerEl.style.setProperty('left', '0', 'important');
                footerEl.style.setProperty('right', '0', 'important');
                footerEl.style.setProperty('bottom', '0', 'important');
            }
        }
        $(document).ready(simfEnforceDrawerLayout);
        $(window).on('resize', simfEnforceDrawerLayout);
        $(document).on('click', '#kt_market_filter_toggle, #mktf_close_x, #mktf_close_btn', function() {
            setTimeout(simfEnforceDrawerLayout, 50);
        });

        function filtersdata() {
            if (typeof renderAppliedFiltersCard === 'function') {
                renderAppliedFiltersCard();
            }
            fetchCandidates();
        }

        function filtersdataget() {
            if (typeof renderAppliedFiltersCard === 'function') {
                renderAppliedFiltersCard();
            }
            fetchCandidates();
        }

        function clearAllTags() {
            if (typeof tagify !== 'undefined' && tagify) { tagify.removeAllTags(); }
            if (typeof tagifylocation !== 'undefined' && tagifylocation) { tagifylocation.removeAllTags(); }
            fetchCandidates();
        }

        $(document).ready(function() {
            $(document).on('keyup', '#inputSearch', function () {
                fetchCandidates();
            });
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
        var $btn = $(this);
        var id = $btn.data('row-id') || $btn.attr('data-row-id');
        var mod = $btn.data('mod') || $btn.attr('data-mod') || '';
        var cur = $btn.data('cur') || $btn.attr('data-cur') || '';
        if (!id) {
            id = $btn.closest('[data-row-id]').attr('data-row-id');
        }
        showKtDrawer('#kt_activitiesBT', '#kt_activitiesBT_toggle');
        if (!id) {
            $('#kt_activitiesBT_body').html('<div class="alert alert-danger m-4">Unable to find job requirement ID.</div>');
            return;
        }
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
                if (response && response.view) {
                    $('#kt_activitiesBT_body').html(response.view);
                    if (response.company_or_client) {
                        $('#company_or_client').text(response.company_or_client);
                    }
                } else if (response && response.message) {
                    $('#kt_activitiesBT_body').html('<div class="alert alert-danger m-4">' + response.message + '</div>');
                } else {
                    $('#kt_activitiesBT_body').html('<div class="alert alert-danger m-4">Unable to load bench candidates for this requirement.</div>');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to load bench candidates (' + xhr.status + ').';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += ' ' + xhr.responseJSON.message;
                }
                $('#kt_activitiesBT_body').html('<div class="alert alert-danger m-4">' + errorMsg + '</div>');
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
        var $btn = $(this);
        var id = $btn.data('row-id') || $btn.attr('data-row-id');
        var recommendation_relation_id = $btn.data('row-recommendation_relation_id') || $btn.attr('data-row-recommendation_relation_id') || '';
        if (!id) {
            id = $btn.closest('[data-row-id]').attr('data-row-id');
        }
        showKtDrawer('#kt_activitiesTermsFindjob', '#kt_activitiesTermsFindjob_toggle');
        if (!id) {
            $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">Unable to find job requirement ID.</div>');
            return;
        }
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
                if (response && (response.terms || response.view)) {
                    $('#kt_activitiesTermsFindJob_body').html(response.terms || response.view);
                } else if (response && response.message) {
                    $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">' + response.message + '</div>');
                } else {
                    $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">No terms & conditions found.</div>');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to load terms & conditions (' + xhr.status + ').';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += ' ' + xhr.responseJSON.message;
                }
                $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">' + errorMsg + '</div>');
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
            } else if (typeLabel === 'Freelance') {
                typeBadgeClass = 'jc-badge-fl';
            } else if (typeLabel === 'Part-Time' || typeLabel === 'Part Time') {
                typeBadgeClass = 'jc-badge-pt';
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

            // ── Salary display ──
            let salaryDisplay = (row.min_salary && row.max_salary)
                ? `${row.salary_currency}${row.min_salary} – ${row.max_salary} / ${row.salary_mode_short}`
                : 'As per Market Norms';

            // ── Commercial Terms subcard (Col 5) ──
            let termsTitle = 'Commercial Terms';
            let termsRows = '';
            const ct = row.commercial_terms;

            if (typeLabel === 'Contract') {
                let billingRate = (ct && ct.resolved_rate) ? ct.resolved_rate : salaryDisplay;
                let billingModel = (ct && ct.billing_model) ? ct.billing_model : 'Monthly';
                let payTerms = (ct && (ct.contract_payment_days || ct.payment_days)) ? `${ct.contract_payment_days || ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Model</span><span class="jc-ct-val">${billingModel}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Rate</span><span class="jc-ct-val">${billingRate}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            } else if (typeLabel === 'Part-Time' || typeLabel === 'Part Time') {
                let resolvedRate = (ct && ct.resolved_rate) ? ct.resolved_rate : salaryDisplay;
                let payTerms = (ct && (ct.contract_payment_days || ct.payment_days)) ? `${ct.contract_payment_days || ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Engagement</span><span class="jc-ct-val">Part-Time</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Rate / Budget</span><span class="jc-ct-val">${resolvedRate}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            } else if (typeLabel === 'Freelance') {
                let resolvedRate = (ct && ct.resolved_rate) ? ct.resolved_rate : salaryDisplay;
                let payTerms = (ct && (ct.contract_payment_days || ct.payment_days)) ? `${ct.contract_payment_days || ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Engagement</span><span class="jc-ct-val">Freelance</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Rate / Budget</span><span class="jc-ct-val">${resolvedRate}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            } else if (typeLabel === 'Bench Talent' || row.public_market_status === 2) {
                termsTitle = 'Requirement Details';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">Deployment</span><span class="jc-ct-val">Bench</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Work Mode</span><span class="jc-ct-val">${row.work_type || 'Remote'}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Availability</span><span class="jc-ct-val">Immediate</span></div>`;
            } else {
                let feeLabel = (ct && ct.fulltime_fee_label) ? ct.fulltime_fee_label : 'Hiring Fee';
                let hiringFee = (ct && ct.resolved_fulltime_fee) ? ct.resolved_fulltime_fee : ((ct && ct.hiring_fee_pct) ? `${ct.hiring_fee_pct}% of CTC` : '8.33% of CTC');
                let replacement = (ct && ct.replacement_days) ? `${ct.replacement_days} Days` : '90 Days';
                let payTerms = (ct && ct.payment_days) ? `${ct.payment_days} Days` : '30 Days';
                termsRows = `
                    <div class="jc-ct-row"><span class="jc-ct-label">${feeLabel}</span><span class="jc-ct-val">${hiringFee}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Replacement</span><span class="jc-ct-val">${replacement}</span></div>
                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">${payTerms}</span></div>`;
            }

            // ── Posted Date display ──
            let postedDate = row.created_at_formatted || row.created_at_date || 'Recently';

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

        function renderEnterpriseEmptyState(tabKey) {
            let icon = 'fa-briefcase';
            let title = 'No Opportunities Found';
            let desc = 'There are currently no active job requisitions matching your search criteria or selected filters.';

            if (tabKey === 'agency_request') {
                icon = 'fa-handshake';
                title = 'No Agency Requests at this time';
                desc = 'You currently have no incoming direct client requests or bench matchmaking demands from other companies.';
            } else if (tabKey === 'fulltime') {
                icon = 'fa-user-tie';
                title = 'No Full-Time Positions Found';
                desc = 'There are no open full-time permanent hiring positions available under this filter.';
            } else if (tabKey === 'contract') {
                icon = 'fa-file-contract';
                title = 'No Contract Positions Found';
                desc = 'There are no open contract or temporary staffing opportunities available right now.';
            } else if (tabKey === 'freelance') {
                icon = 'fa-laptop-code';
                title = 'No Freelance Projects Found';
                desc = 'There are no active freelance or gig project requirements matching your selection.';
            } else if (tabKey === 'parttime') {
                icon = 'fa-business-time';
                title = 'No Part-Time Openings Found';
                desc = 'There are no active part-time requisitions available under this category.';
            }

            let primaryBtn = '';
            if (tabKey !== 'all') {
                primaryBtn = `
                    <button type="button" class="mjh-empty-btn-primary" onclick="resetTabToAll()">
                        <i class="fa-solid fa-briefcase"></i> View All Open Jobs
                    </button>`;
            }

            return `
            <div class="mjh-empty-state">
                <div class="mjh-empty-icon-wrap">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <h3 class="mjh-empty-title">${title}</h3>
                <p class="mjh-empty-desc">${desc}</p>
                <div class="mjh-empty-actions">
                    ${primaryBtn}
                    <button type="button" class="mjh-empty-btn-secondary" onclick="clearAllTags()">
                        <i class="fa-solid fa-rotate-left"></i> Reset Search &amp; Filters
                    </button>
                </div>
            </div>`;
        }

        function resetTabToAll() {
            $('.mjh-tab-item').removeClass('active');
            $('.mjh-tab-item[data-tab="all"]').addClass('active');
            currentActiveTab = 'all';
            fetchCandidates(1);
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

        /* ══════════════════════════════════════════════════════════════════
           Active Filters (Drawer Preview & Main Page Summary Card)
           ══════════════════════════════════════════════════════════════════ */
        function mktfSyncPendingInputs() {
            function commitInst(inst) {
                if (!inst) return;
                var val = '';
                if (inst.DOM && inst.DOM.input) {
                    val = (inst.DOM.input.textContent || inst.DOM.input.innerText || '').trim();
                }
                if (!val && inst.state && inst.state.inputText) {
                    val = inst.state.inputText.trim();
                }
                var placeholder = (inst.settings && inst.settings.placeholder) ? inst.settings.placeholder.trim() : '';
                if (val && val !== placeholder) {
                    inst.addTags([val]);
                    if (inst.DOM && inst.DOM.input) {
                        inst.DOM.input.textContent = '';
                    }
                    if (inst.state) {
                        inst.state.inputText = '';
                    }
                }
            }
            if (typeof tagify !== 'undefined') commitInst(tagify);
            if (typeof tagifylocation !== 'undefined') commitInst(tagifylocation);
        }

        function mktfUpdateActiveFiltersUI() {
            mktfSyncPendingInputs();
            var chips = [];

            // Skills (starred = mandatory)
            if (typeof tagify !== 'undefined' && tagify && Array.isArray(tagify.value)) {
                tagify.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }

            // Location
            if (typeof tagifylocation !== 'undefined' && tagifylocation && Array.isArray(tagifylocation.value)) {
                tagifylocation.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        clear: function() { tagifylocation.removeTags(tag.value); }
                    });
                });
            }

            // Experience range
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp || maxExp) {
                chips.push({
                    label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }

            // Salary range
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            if (minSal || maxSal) {
                chips.push({
                    label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(''); $('#max_sal').val(''); }
                });
            }

            var $chips = $('#mktf_active_chips').empty();
            chips.forEach(function(c) {
                var $chip = $('<span class="simf-chip"></span>').toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').html('&#9733; ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip.append($('<span class="simf-chip-remove">&times;</span>').on('click', function() {
                    c.clear();
                    mktfUpdateActiveFiltersUI();
                }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#mktf_active_bar').toggleClass('d-none', n === 0);
            $('#mktf_active_count').text('(' + n + ')');
            $('#mktf_header_count').text(n);
            $('#simf_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#mktf_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        /* ── Applied Filters Summary Card on Main Page ── */
        var MKTF_APPLIED_VISIBLE_CAP = 8;
        var mktfAppliedExpanded = false;

        function mktfCategoryIcon(cat) {
            var paths = {
                skill: '<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>',
                location: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                experience: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>'
            };
            if (cat === 'salary') {
                return '<span style="font-size:11px;font-weight:700;line-height:1;">&#8377;</span>';
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' + (paths[cat] || '') + '</svg>';
        }

        function mktfBuildAppliedItems() {
            var items = [];

            var searchVal = $('#kt_tagify_2').val();
            if (searchVal && searchVal.trim()) {
                items.push({
                    cat: 'search', label: searchVal.trim(),
                    clear: function() { $('#kt_tagify_2').val(''); }
                });
            }

            if (typeof tagify !== 'undefined' && tagify && Array.isArray(tagify.value)) {
                tagify.value.forEach(function(tag) {
                    items.push({
                        cat: 'skill', label: tag.value, mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }
            if (typeof tagifylocation !== 'undefined' && tagifylocation && Array.isArray(tagifylocation.value)) {
                tagifylocation.value.forEach(function(tag) {
                    items.push({ cat: 'location', label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp || maxExp) {
                items.push({
                    cat: 'experience', label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            if (minSal || maxSal) {
                items.push({
                    cat: 'salary', label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val(''); $('#max_sal').val(''); }
                });
            }
            return items;
        }

        function renderAppliedFiltersCard() {
            var items = mktfBuildAppliedItems();
            var $chips = $('#simf_applied_chips').empty();
            var visible = mktfAppliedExpanded ? items : items.slice(0, MKTF_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="simf-applied-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + mktfCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').html('&#9733;'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="simf-applied-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        fetchCandidates(1);
                        mktfUpdateActiveFiltersUI();
                        mktfCaptureSnapshot();
                        renderAppliedFiltersCard();
                    })
                );
                $chips.append($chip);
            });

            if (!mktfAppliedExpanded && items.length > MKTF_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - MKTF_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="simf-applied-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        mktfAppliedExpanded = true;
                        renderAppliedFiltersCard();
                    })
                );
            }

            $('#simf_applied_card').toggleClass('d-none', items.length === 0);
            $('#simf_applied_count').text(items.length);
            $('#simf_count_badge').toggleClass('d-none', items.length === 0).text(items.length);
        }

        /* ── Draft-until-Apply Lifecycle (Snapshot / Restore) ── */
        var mktfSnapshot = null;
        var mktfApplying = false;

        function mktfCaptureSnapshot() {
            mktfSnapshot = {
                skills: (typeof tagify !== 'undefined' && tagify && Array.isArray(tagify.value))
                    ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                locations: (typeof tagifylocation !== 'undefined' && tagifylocation && Array.isArray(tagifylocation.value))
                    ? tagifylocation.value.map(function(t) { return t.value; }) : [],
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val()
            };
        }

        function mktfRestoreSnapshot() {
            if (!mktfSnapshot) return;
            if (typeof tagify !== 'undefined' && tagify) {
                tagify.removeAllTags();
                if (mktfSnapshot.skills && mktfSnapshot.skills.length) tagify.addTags(mktfSnapshot.skills);
            }
            if (typeof tagifylocation !== 'undefined' && tagifylocation) {
                tagifylocation.removeAllTags();
                if (mktfSnapshot.locations && mktfSnapshot.locations.length) tagifylocation.addTags(mktfSnapshot.locations);
            }
            $('#min_exp').val(mktfSnapshot.min_exp);
            $('#max_exp').val(mktfSnapshot.max_exp);
            $('#min_sal').val(mktfSnapshot.min_sal);
            $('#max_sal').val(mktfSnapshot.max_sal);
            $('#min_salary_currency').val(mktfSnapshot.min_salary_currency);
            $('#sal_mode').val(mktfSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            mktfUpdateActiveFiltersUI();
        }

        function mktfBindDrawerLifecycle() {
            var el = document.getElementById('kt_market_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(mktfBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst && typeof KTDrawer === 'function') {
                inst = new KTDrawer(el);
            }
            if (!inst) { return setTimeout(mktfBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                mktfApplying = false;
                mktfCaptureSnapshot();
                if (typeof simfEnforceDrawerLayout === 'function') {
                    simfEnforceDrawerLayout();
                    setTimeout(simfEnforceDrawerLayout, 50);
                }
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!mktfApplying) mktfRestoreSnapshot();
                mktfApplying = false;
            });
        }
        $(document).ready(mktfBindDrawerLifecycle);

        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', mktfUpdateActiveFiltersUI);

        $(document).on('click', '#mktf_clear_all', function() {
            if (typeof tagify !== 'undefined' && tagify) tagify.removeAllTags();
            if (typeof tagifylocation !== 'undefined' && tagifylocation) tagifylocation.removeAllTags();
            $('#min_exp, #max_exp, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#min_sal').val(0);
            mktfUpdateActiveFiltersUI();
        });

        $(document).on('click', '#mktf_reset_btn', function() {
            $('#mktf_clear_all').trigger('click');
            fetchCandidates(1);
            mktfCaptureSnapshot();
            mktfAppliedExpanded = false;
            renderAppliedFiltersCard();
        });

        $(document).on('click', '#mktf_close_btn', function() {
            $('#mktf_close_x').trigger('click');
        });

        $(document).on('click', '#mktf_apply_btn', function() {
            mktfSyncPendingInputs();
            mktfApplying = true;
            fetchCandidates(1);
            mktfAppliedExpanded = false;
            renderAppliedFiltersCard();
            $('#mktf_close_x').trigger('click');
        });

        $(document).on('click', '#simf_applied_clear_all', function() {
            $('#mktf_clear_all').trigger('click');
            fetchCandidates(1);
            mktfCaptureSnapshot();
            mktfAppliedExpanded = false;
            renderAppliedFiltersCard();
        });

        $(document).ready(function() {
            setTimeout(mktfUpdateActiveFiltersUI, 600);
            setTimeout(mktfCaptureSnapshot, 800);
            setTimeout(renderAppliedFiltersCard, 700);
        });

        $(document).on('click', '.mjh-tab-item', function() {
            $('.mjh-tab-item').removeClass('active');
            $(this).addClass('active');
            currentActiveTab = $(this).data('tab') || 'all';
            fetchCandidates(1);
        });

        function fetchCandidates(page = 1, search = '') {
            if (typeof mktfSyncPendingInputs === 'function') {
                mktfSyncPendingInputs();
            }
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
                            $('#marketJobLists').html(renderEnterpriseEmptyState(currentActiveTab));
                        }
                    } else if (response.html) {
                        $('#marketJobLists').html(response.html);
                    } else {
                        $('#marketJobLists').html(renderEnterpriseEmptyState(currentActiveTab));
                    }
                    $('#span_jobMarket').html(response.total);
                    $('#mktf_footer_number').text(response.total || 0);
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
</script>
@endpush

