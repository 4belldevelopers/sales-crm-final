@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
@endphp

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
    <style>
        /* ===== Manage Top Gap: collapse Metronic's toolbar-fixed padding and content gap ===== */
        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: 65px !important;
        }
        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .header-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper,
            #kt_wrapper {
                padding-top: 55px !important;
            }
        }
        #kt_toolbar {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #kt_content.bench-dash,
        .bench-dash.content,
        .content.bench-dash {
            padding-top: 10px !important;
        }

        /* ===== Bench Drawer Responsive Width & Layout (Balanced Side Gaps, Zero Dead Gutters) ===== */
        #kt_activitiesBenchApplication {
            padding: 0 !important;
            margin: 0 !important;
            background: #f1f5f9 !important;
            display: block !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            box-sizing: border-box !important;
        }
        #kt_activitiesBenchApplication #bench_application_show_right_bar {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            background: #f1f5f9 !important;
            box-sizing: border-box !important;
            display: block !important;
        }
        @media only screen and (max-width: 1023px) {
            #kt_activitiesBenchApplication { width: 95% !important; max-width: 100% !important; }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesBenchApplication { width: 1025px !important; max-width: 100% !important; }
        }
        @media only screen and (min-width: 1440px) {
            #kt_activitiesBenchApplication { width: 1200px !important; max-width: 100% !important; }
        }

        /* ===== Super Admin Bench Dashboard — Card + Sub-Card Design (Poppins / max 13px body) ===== */
        .bench-dash, .bench-dash * { font-family: 'Poppins', sans-serif !important; font-size: 13px; }
        .bench-dash { color: #3f4254; }
        .bench-dash .tiny { font-size: 11px !important; }
        .bench-dash .xtiny { font-size: 10px !important; }
        .bench-dash .page-title { font-size: 16px !important; line-height: 1.25; }
        .bench-dash .num { font-size: 16px !important; font-weight: 700; color: #181c32; line-height: 1.1; }

        /* Card + sub-card system */
        .bench-dash .jw-card { background: #fff; border: 1px solid #eef0f4; border-radius: 10px; }
        .bench-dash .jw-sub { background: #fff; border: 1px solid #f0f1f5; border-radius: 8px; }

        /* Top Hero Card */
        .bench-dash .talent-hero-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .bench-dash .talent-hero-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .bench-dash .talent-hero-heading { min-width: 0; }
        .bench-dash .talent-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .bench-dash .talent-title { margin: 0; color: #181c32; font-size: 20px !important; font-weight: 700; line-height: 1.2; }
        .bench-dash .talent-subtitle { margin: 4px 0 0; color: #7e8299; font-size: 12px !important; }
        .bench-dash .talent-hero-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        /* Hero Overview KPI Card */
        .talent-overview-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            padding: 16px 20px 20px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }
        .overview-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }
        .overview-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .overview-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #eef6ff;
            color: #1b84ff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .overview-title {
            margin: 0;
            color: #181c32;
            font-size: 15px !important;
            font-weight: 700;
            line-height: 1.25;
        }
        .overview-subtitle {
            margin: 2px 0 0;
            color: #7e8299;
            font-size: 11.5px !important;
        }

        /* 3 Sections Grid */
        .overview-sections-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            align-items: stretch;
        }
        .overview-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }
        .overview-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 6px;
            min-height: 30px;
            height: 30px;
        }
        .overview-section-header.banner-blue    { background: #eff6ff; }
        .overview-section-header.banner-purple  { background: #faf5ff; }
        .overview-section-header.banner-emerald { background: #f0fdf4; }
        .banner-title {
            font-size: 11px !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .banner-title.text-blue   { color: #1e40af; }
        .banner-title.text-purple { color: #7c3aed; }
        .banner-title.text-green  { color: #059669; }

        .overview-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            height: 100%;
        }

        /* KPI Card */
        .overview-kpi-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid transparent;
            min-height: 68px;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }
        .overview-kpi-card:hover {
            box-shadow: 0 3px 8px rgba(24, 28, 50, 0.08);
            transform: translateY(-1px);
        }
        .overview-kpi-card.active {
            border-color: #3e97ff;
            box-shadow: 0 0 0 1px #3e97ff inset;
        }
        .overview-kpi-card .kpi-row-top {
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1;
        }
        .overview-kpi-card .kpi-circle-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .overview-kpi-card .kpi-circle-icon i {
            font-size: 11px !important;
        }
        .overview-kpi-card .kpi-num {
            font-size: 18px !important;
            font-weight: 700;
            color: #181c32;
            line-height: 1;
        }
        .overview-kpi-card .kpi-row-bottom {
            margin-top: 5px;
            width: 100%;
        }
        .overview-kpi-card .kpi-label {
            font-size: 11px !important;
            font-weight: 500;
            color: #5e6278;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Color Tokens */
        .kpi-bg-blue    { background: #f0f7ff; }
        .circle-blue    { background: #e0efff; color: #1b84ff; }
        .kpi-bg-amber   { background: #fffbeb; }
        .circle-amber   { background: #fef3c7; color: #d97706; }
        .kpi-bg-sky     { background: #f0f9ff; }
        .circle-sky     { background: #e0f2fe; color: #0284c7; }
        .kpi-bg-green   { background: #f0fdf4; }
        .circle-green   { background: #dcfce7; color: #16a34a; }
        .kpi-bg-red     { background: #fff1f2; }
        .circle-red     { background: #ffe4e6; color: #e11d48; }
        .kpi-bg-violet  { background: #faf5ff; }
        .circle-violet  { background: #f3e8ff; color: #9333ea; }

        /* Filter / Sub-Tabs Strip */
        .bench-dash .tab-strip { display: flex; align-items: stretch; flex-wrap: nowrap; width: 100%; overflow-x: auto; }
        .bench-dash .tab-pill { flex: 1 1 0; min-width: 110px; border: 0; border-bottom: 2px solid transparent; background: #fff; border-radius: 0; padding: 10px 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; color: #637083; text-decoration: none; }
        .bench-dash .tab-pill .tab-ico { color: #7e8aa0; font-size: 12px !important; }
        .bench-dash .tab-pill .lbl { color: #4b5675; font-weight: 500; white-space: nowrap; }
        .bench-dash .tab-pill .cnt { background: #f1f2f6; color: #3f4254; border-radius: 999px; padding: 1px 7px; font-size: 10.5px !important; font-weight: 700; line-height: 1.35; }
        .bench-dash .tab-pill:not(.active):hover { background: #fafbfc; }
        .bench-dash .tab-pill.active { background: #fff; border-bottom-color: var(--tab-color); }
        .bench-dash .tab-pill.active .lbl, .bench-dash .tab-pill.active .tab-ico { color: var(--tab-color); font-weight: 600; }
        .bench-dash .tab-pill.active .cnt { background: var(--tab-bg); color: var(--tab-color); }
        .bench-dash .tab-bench  { --tab-color: #009ef7; --tab-bg: #eef3ff; }
        .bench-dash .tab-all    { --tab-color: #f59e0b; --tab-bg: #fffbeb; }
        .bench-dash .tab-hold   { --tab-color: #ef4444; --tab-bg: #fef2f2; }
        .bench-dash .tab-placed { --tab-color: #10b981; --tab-bg: #ecfdf5; }
        .bench-dash .tab-pool   { --tab-color: #8b5cf6; --tab-bg: #f5f3ff; }

        /* Search input & toolbar */
        .bench-dash .dash-search { border: 1px solid #eef0f4; border-radius: 8px; padding: 7px 12px; font-size: 12px !important; height: 34px; }
        .bench-dash .form-select-solid { font-size: 12px !important; height: 34px; padding: 5px 10px; border: 1px solid #eef0f4 !important; background: #fff !important; }

        /* Hidden multi-selection bar */
        .bench-table-header-toolbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            background: #F0FDF4;
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 8px 16px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .bench-table-header-toolbar.show { display: flex !important; }
        .bench-th-left { display: flex; align-items: center; gap: 8px; }
        .bench-th-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 11px; background: #0D9488; color: #fff; font-size: 11px; font-weight: 700; }
        .bench-th-text { font-size: 12.5px; font-weight: 600; color: #065F46; }
        .bench-th-clear-btn { font-size: 11.5px; font-weight: 500; color: #047857; text-decoration: underline; background: none; border: none; padding: 0 4px; cursor: pointer; }
        .bench-th-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }

        /* Table layout */
        .bench-dash #benchListingTable { width: 100% !important; margin: 0; table-layout: fixed; }
        .bench-dash #benchListingTable thead th { font-size: 10px !important; text-transform: uppercase; letter-spacing: .2px; color: #a1a5b7; font-weight: 600; border-bottom: 1px solid #eef0f4; padding: 8px 6px !important; line-height: 1.2; background: #fff; }
        .bench-dash #benchListingTable tbody td { font-size: 12px !important; border-bottom: 1px solid #f4f5f8; padding: 8px 6px !important; vertical-align: middle; min-width: 0; overflow: hidden; }
        .bench-dash #benchListingTable tbody tr:hover td { background: #fafbfc; }

        /* Talent Name Clamping */
        .bench-dash .talent-name-main, .bench-dash .talent-name-role {
            display: -webkit-box !important;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.25;
            max-width: 100%;
        }
        .bench-dash .wrap-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.3;
        }

        /* Job Tracker style meta strip */
        .bench-dash #benchListingTable tbody td.col-talent-name { position: relative; padding-bottom: 24px !important; }
        .bench-dash #benchListingTable .job-meta-strip { position: absolute; bottom: 4px; left: 6px; right: 6px; overflow: hidden; }
        .bench-dash #benchListingTable .job-meta-line { display: flex; align-items: center; gap: 4px; white-space: nowrap; overflow: hidden; }
        .bench-dash #benchListingTable .job-date-tag, .bench-dash #benchListingTable .skill-tag {
            display: inline-flex;
            align-items: center;
            min-height: 17px;
            border-radius: 5px;
            padding: 1px 5px;
            font-size: 9.5px !important;
            line-height: 1.25;
            font-weight: 500;
            background: #f1f3f8;
            color: #5e6278;
            flex-shrink: 0;
        }
        .bench-dash #benchListingTable .job-date-tag { color: #181c32; font-weight: 500; }
        .bench-dash #benchListingTable .job-date-tag svg { width: 11px; height: 11px; margin-right: 3px; color: #3f4254; flex-shrink: 0; }
        .bench-dash #benchListingTable .skill-tag { color: #7e8299; font-weight: 500; }

        @media (max-width: 1200px) {
            .overview-sections-grid { grid-template-columns: 1fr; gap: 12px; }
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-0 bench-dash" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-4">

            {{-- 1. Top Hero Card with Title, Total Badge, and Auto-Approval Switch --}}
            <div class="jw-card talent-hero-card">
                <div class="talent-hero-header">
                    <div class="talent-hero-heading">
                        <div class="talent-title-row">
                            <h1 class="talent-title">Bench Candidates Approval Hub</h1>
                            <span class="badge badge-light-primary fw-bolder fs-7 ms-2" id="header_total_badge">{{ number_format($total ?? 0) }} Candidates</span>
                        </div>
                        <p class="talent-subtitle">Review candidate profiles, verify qualifications, and govern marketplace clearance across all companies.</p>
                    </div>
                    <div class="talent-hero-actions">
                        @if(@$reject)
                            <div class="d-flex align-items-center bg-light-primary px-3 py-2 rounded-2 border border-primary border-opacity-25" title="When enabled, new bench uploads skip manual review and go live instantly.">
                                <span class="fw-bold fs-7 text-gray-800 me-2">Auto Approved</span>
                                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid mb-0">
                                    <input class="form-check-input chkStatus" type="checkbox" value="{{ @$bench_status->id }}" name="status" {{ (@$bench_status->bench_status) ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endif

                        <button type="button" data-type="approve" class="btn btn-sm btn-primary clickbtn approve fw-bolder me-2 d-none">
                            <i class="fa fa-check me-1"></i> Approve Selected
                        </button>
                        @if(@$reject)
                            <button type="button" data-type="disapprove" class="btn btn-sm btn-danger reject_mail disapprove fw-bolder d-none" data-toggle="modal" data-target="#disapprove">
                                <i class="fa fa-times me-1"></i> Reject Selected
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Hero Overview KPI Card (3-Section Grid) --}}
            <div class="jw-card talent-overview-card" id="talentOverviewCard">
                <div class="overview-header-row">
                    <div class="overview-title-group">
                        <div class="overview-icon-box" aria-hidden="true">
                            <i class="fa fa-chart-pie" style="font-size: 16px !important;"></i>
                        </div>
                        <div>
                            <h2 class="overview-title">Marketplace Clearance &amp; Platform Metrics</h2>
                            <p class="overview-subtitle">Real-time status counts across all tenant bench talent pools.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="btn_refresh_counts" title="Refresh Platform Metrics">
                            <i class="fa fa-sync-alt me-1 text-primary"></i> Refresh Metrics
                        </button>
                    </div>
                </div>

                <div class="overview-sections-grid">
                    {{-- Section 1: MARKET CLEARANCE --}}
                    <div class="overview-section">
                        <div class="overview-section-header banner-blue">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-shield-halved text-blue"></i>
                                <span class="banner-title text-blue">MARKET CLEARANCE</span>
                            </div>
                        </div>
                        <div class="overview-cards-grid">
                            <div class="overview-kpi-card kpi-bg-amber kpi-filter {{ $statusType === 'pending' ? 'active' : '' }}" data-rel="pending" id="kpi_pending" title="Filter to Pending Approvals">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-amber">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <span class="kpi-num" id="kpival_pending">{{ number_format($counts['pending'] ?? 0) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">Pending Approval</span>
                                </div>
                            </div>
                            <div class="overview-kpi-card kpi-bg-blue kpi-filter {{ $statusType === 'approve' ? 'active' : '' }}" data-rel="approve" id="kpi_approve" title="Filter to Approved Bench">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-blue">
                                        <i class="fa fa-circle-check"></i>
                                    </div>
                                    <span class="kpi-num" id="kpival_approve">{{ number_format($counts['approve'] ?? 0) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">Market Approved</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: PLATFORM GOVERNANCE --}}
                    <div class="overview-section">
                        <div class="overview-section-header banner-purple">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-building-columns text-purple"></i>
                                <span class="banner-title text-purple">PLATFORM GOVERNANCE</span>
                            </div>
                        </div>
                        <div class="overview-cards-grid">
                            <div class="overview-kpi-card kpi-bg-violet kpi-filter {{ $statusType === 'pool' ? 'active' : '' }}" data-rel="pool" id="kpi_pool" title="Filter to Pool Candidates">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-violet">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <span class="kpi-num" id="kpival_pool">{{ number_format($counts['pool'] ?? 0) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">On Talent Pool</span>
                                </div>
                            </div>
                            <div class="overview-kpi-card kpi-bg-red kpi-filter {{ $statusType === 'reject' ? 'active' : '' }}" data-rel="reject" id="kpi_reject" title="Filter to Rejected Bench">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-red">
                                        <i class="fa fa-circle-xmark"></i>
                                    </div>
                                    <span class="kpi-num" id="kpival_reject">{{ number_format($counts['reject'] ?? 0) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">Rejected / Exited</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: MARKETPLACE DYNAMICS --}}
                    <div class="overview-section">
                        <div class="overview-section-header banner-emerald">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-trophy text-green"></i>
                                <span class="banner-title text-green">MARKETPLACE DYNAMICS</span>
                            </div>
                        </div>
                        <div class="overview-cards-grid">
                            <div class="overview-kpi-card kpi-bg-green kpi-filter {{ $statusType === 'place' ? 'active' : '' }}" data-rel="place" id="kpi_place" title="Filter to Placed Candidates">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-green">
                                        <i class="fa fa-trophy"></i>
                                    </div>
                                    <span class="kpi-num" id="kpival_place">{{ number_format($counts['place'] ?? 0) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">Placed via Market</span>
                                </div>
                            </div>
                            <div class="overview-kpi-card kpi-bg-sky kpi-filter" data-rel="approve" id="kpi_total" title="Total Active Bench">
                                <div class="kpi-row-top">
                                    <div class="kpi-circle-icon circle-sky">
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                    @php
                                        $allTotal = ($counts['pending'] ?? 0) + ($counts['approve'] ?? 0) + ($counts['reject'] ?? 0) + ($counts['place'] ?? 0) + ($counts['pool'] ?? 0);
                                    @endphp
                                    <span class="kpi-num" id="kpival_total">{{ number_format($allTotal) }}</span>
                                </div>
                                <div class="kpi-row-bottom">
                                    <span class="kpi-label">Total Platform Talent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Tier 1 Search & Sort Row --}}
            <div class="jw-card p-2 mb-2 d-flex align-items-center gap-2">
                <div class="position-relative flex-grow-1">
                    <i class="fa fa-search position-absolute text-muted" style="left:12px; top:11px; font-size:12px !important;"></i>
                    <input type="text" class="form-control dash-search w-100" style="padding-left:32px; padding-right:32px;" name="inputJobSearch"
                        placeholder="Search by candidate name, skills, role, company, location..." id="kt_tagify_2" autocomplete="off" />
                    <button type="button" class="btn btn-sm btn-icon position-absolute text-muted d-none" id="clearSearchBtn" style="right:8px; top:5px; height:24px; width:24px;" title="Clear search">
                        <i class="fa fa-times fs-7"></i>
                    </button>
                </div>
                <select class="form-select form-select-solid w-200px flex-shrink-0" id="sortingfilter" title="Sort listing">
                    <option value="" selected>Sort by</option>
                    <option value="1">Newest to Oldest (Upload Date)</option>
                    <option value="2">Oldest to Newest (Upload Date)</option>
                    <option value="7">High to Low (Experience)</option>
                    <option value="8">Low to High (Experience)</option>
                    <option value="9">Notice Period (Immediate)</option>
                </select>
            </div>

            {{-- 4. Tier 2 Sub-Tabs Strip --}}
            <div class="jw-card mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="overflow:hidden;">
                <div class="tab-strip">
                    <a href="javascript:void(0);" rel="approve" class="tab-pill tab-bench application_sttus_nav {{ $statusType === 'approve' ? 'active' : '' }}" id="tab_approve">
                        <i class="fa fa-briefcase tab-ico"></i><span class="lbl">All Market Bench</span><span class="cnt" id="cnt_approve">{{ $counts['approve'] ?? 0 }}</span>
                    </a>
                    <a href="javascript:void(0);" rel="pending" class="tab-pill tab-all application_sttus_nav {{ $statusType === 'pending' ? 'active' : '' }}" id="tab_pending">
                        <i class="fa fa-clock tab-ico"></i><span class="lbl">Pending Approval</span><span class="cnt" id="cnt_pending">{{ $counts['pending'] ?? 0 }}</span>
                    </a>
                    <a href="javascript:void(0);" rel="reject" class="tab-pill tab-hold application_sttus_nav {{ $statusType === 'reject' ? 'active' : '' }}" id="tab_reject">
                        <i class="fa fa-circle-xmark tab-ico"></i><span class="lbl">Rejected</span><span class="cnt" id="cnt_reject">{{ $counts['reject'] ?? 0 }}</span>
                    </a>
                    <a href="javascript:void(0);" rel="place" class="tab-pill tab-placed application_sttus_nav {{ $statusType === 'place' ? 'active' : '' }}" id="tab_place">
                        <i class="fa fa-trophy tab-ico"></i><span class="lbl">Placed</span><span class="cnt" id="cnt_place">{{ $counts['place'] ?? 0 }}</span>
                    </a>
                    <a href="javascript:void(0);" rel="pool" class="tab-pill tab-pool application_sttus_nav {{ $statusType === 'pool' ? 'active' : '' }}" id="tab_pool">
                        <i class="fa fa-user-group tab-ico"></i><span class="lbl">On Pool</span><span class="cnt" id="cnt_pool">{{ $counts['pool'] ?? 0 }}</span>
                    </a>
                </div>
                <input type="hidden" id="current_status_type" value="{{ $statusType }}" />
            </div>

            {{-- 5. Tier 3 Table Container with Selection Bar --}}
            <div class="jw-card p-2 position-relative">
                <div id="benchListingTable_processing" class="dataTables_processing card" style="display:none; position:absolute; top:40%; left:45%; z-index:10; background:rgba(255,255,255,0.95); padding:10px 20px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); font-weight:600; color:#009ef7;">
                    <i class="fa fa-spinner fa-spin me-2"></i> Loading candidates...
                </div>

                {{-- Hidden Multi-Select Floating Toolbar --}}
                <div class="bench-table-header-toolbar" id="bench_selection_bar">
                    <div class="bench-th-left">
                        <span class="bench-th-badge" id="bench_selected_count_badge">0</span>
                        <span class="bench-th-text" id="bench_selected_text_wrap"><span id="bench_selected_count">0</span> Candidates Selected</span>
                        <button type="button" class="bench-th-clear-btn" id="bench_selection_close" title="Clear selection">
                            (Deselect All)
                        </button>
                    </div>
                    <div class="bench-th-right">
                        <button type="button" class="btn btn-sm btn-primary clickbtn approve fw-bolder me-2" data-type="approve">
                            <i class="fa fa-check me-1"></i> Approve Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-danger reject_mail disapprove fw-bolder" data-type="disapprove" data-toggle="modal" data-target="#disapprove">
                            <i class="fa fa-times me-1"></i> Reject Selected
                        </button>
                    </div>
                </div>

                {{-- Responsive Fixed Table --}}
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="benchListingTable" style="table-layout:fixed; width:100% !important;">
                        <thead>
                            <tr class="fw-bolder text-muted" role="row">
                                <th class="col-select pe-0" style="width: 3%;">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input chk_all_bench_list" type="checkbox" id="chk_all_bench_list" value="1" title="Select all on this page">
                                    </div>
                                </th>
                                <th class="col-talent-name" style="width: 22%;">Talent Name &amp; Role</th>
                                <th class="col-experience" style="width: 8%;">Exp</th>
                                <th class="col-salary" style="width: 12%;">Bill &amp; Pay Rate</th>
                                <th class="col-availability" style="width: 9%;">Availability</th>
                                <th class="col-location" style="width: 11%;">Location &amp; Edu</th>
                                <th class="col-status" style="width: 11%;">Bench Status</th>
                                <th class="col-company" style="width: 14%;">Company &amp; Uploaded</th>
                                <th class="col-action text-end" style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold" id="BenchCandidatesListAjax">
                            {{-- Rows dynamically loaded via AJAX --}}
                        </tbody>
                    </table>
                </div>

                <div id="paginationBenchCandidates" class="mt-2"></div>
            </div>

        </div>
    </div>
</div>

{{-- Reject Mail Modal --}}
<div class="modal fade" id="disapprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header flex-stack">
                <h2 class="fw-bolder fs-4">Bench Candidate Rejection Notice</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal">
                    <i class="fa fa-times fs-4"></i>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_reject_mail_body_content"></div>
        </div>
    </div>
</div>

{{-- Metronic Slide-In Drawer for Candidate Details --}}
<div id="kt_activitiesBenchApplication_toggle" style="display:none;"></div>
<div id="kt_activitiesBenchApplication" class="p-0 m-0" style="background:#f1f5f9 !important;" data-kt-drawer="true"
    data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle"
    data-kt-drawer-close="#kt_activitiesBenchApplication_close">
    <div class="w-100 h-100 p-0 m-0" id="bench_application_show_right_bar" style="margin:0 !important; padding:0 !important; background:#f1f5f9 !important; width:100% !important; max-width:100% !important;"></div>
</div>
@endsection

@push('footer-script')
<script>
    /**
     * Phase 4: Client-First Browser RAM Store for 10,000 Concurrent Users
     * Caches up to 30 recent page responses with 60s TTL to achieve 0% server/DB load
     * during navigation, pagination, and instant tab flips.
     */
    const SuperAdminBenchRamStore = (function() {
        const _cache = new Map();
        const MAX_ENTRIES = 30;
        const TTL_MS = 60000;

        return {
            getKey: function(status, page, search, sort) {
                return `${status || 'approve'}_p${page || 1}_s${search || ''}_srt${sort || ''}`;
            },
            get: function(key) {
                const item = _cache.get(key);
                if (!item) return null;
                if (Date.now() - item.time > TTL_MS) {
                    _cache.delete(key);
                    return null;
                }
                return item.data;
            },
            set: function(key, data) {
                if (_cache.size >= MAX_ENTRIES) {
                    const firstKey = _cache.keys().next().value;
                    _cache.delete(firstKey);
                }
                _cache.set(key, { data: data, time: Date.now() });
            },
            remove: function(key) {
                _cache.delete(key);
            },
            clear: function() {
                _cache.clear();
            }
        };
    })();

    /**
     * Phase 4: IndexedDB Local Snapshot Store (Area 3: IndexedDB)
     * Provides persistent local caching and offline resilience for cold visits
     * using the stale-while-revalidate pattern without blocking the main thread.
     */
    const SuperAdminBenchIdb = (function() {
        const DB_NAME = 'JERO_BENCH_STORAGE';
        const DB_VERSION = 1;
        const STORE_NAME = 'snapshots';
        let dbPromise = null;

        function getDb() {
            if (dbPromise) return dbPromise;
            dbPromise = new Promise(function(resolve) {
                if (!window.indexedDB) {
                    resolve(null);
                    return;
                }
                try {
                    const req = indexedDB.open(DB_NAME, DB_VERSION);
                    req.onupgradeneeded = function(e) {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains(STORE_NAME)) {
                            db.createObjectStore(STORE_NAME, { keyPath: 'key' });
                        }
                    };
                    req.onsuccess = function(e) { resolve(e.target.result); };
                    req.onerror = function() { resolve(null); };
                } catch(e) {
                    resolve(null);
                }
            });
            return dbPromise;
        }

        return {
            get: function(key) {
                return getDb().then(function(db) {
                    if (!db) return null;
                    return new Promise(function(resolve) {
                        try {
                            const tx = db.transaction(STORE_NAME, 'readonly');
                            const store = tx.objectStore(STORE_NAME);
                            const req = store.get(key);
                            req.onsuccess = function() {
                                const result = req.result;
                                if (!result || !result.data) {
                                    resolve(null);
                                    return;
                                }
                                // 10-minute snapshot TTL
                                if (Date.now() - result.time > 600000) {
                                    resolve(null);
                                    return;
                                }
                                resolve(result.data);
                            };
                            req.onerror = function() { resolve(null); };
                        } catch(e) { resolve(null); }
                    });
                });
            },
            set: function(key, data) {
                return getDb().then(function(db) {
                    if (!db) return;
                    try {
                        const tx = db.transaction(STORE_NAME, 'readwrite');
                        const store = tx.objectStore(STORE_NAME);
                        store.put({ key: key, data: data, time: Date.now() });
                    } catch(e) {}
                });
            },
            clear: function() {
                return getDb().then(function(db) {
                    if (!db) return;
                    try {
                        const tx = db.transaction(STORE_NAME, 'readwrite');
                        tx.objectStore(STORE_NAME).clear();
                    } catch(e) {}
                });
            }
        };
    })();

    let currentPage = 1;
    let searchDebounceTimer = null;
    let activeRequest = null;

    // Cross-tab Synchronization Channel
    let benchBroadcastChannel = null;
    try {
        benchBroadcastChannel = new BroadcastChannel('superadmin_bench_channel');
        benchBroadcastChannel.onmessage = function(e) {
            if (e.data && e.data.action === 'mutation') {
                SuperAdminBenchRamStore.clear();
                SuperAdminBenchIdb.clear();
                fetchallBenchCandidatesAjax(currentPage, true);
            }
        };
    } catch(err) {}

    // Memory leak protection on tab close/unload
    window.addEventListener('beforeunload', function() {
        SuperAdminBenchRamStore.clear();
    });

    const swalButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-light'
        },
        buttonsStyling: false
    });

    $(document).ready(function() {
        // Initial Fetch
        fetchallBenchCandidatesAjax(1);

        // 1. Debounced Search (250ms) with Instant Client-Side Row Matching in RAM
        $('#kt_tagify_2').on('input keyup', function() {
            const query = $(this).val().trim();
            if (query.length > 0) {
                $('#clearSearchBtn').removeClass('d-none');
            } else {
                $('#clearSearchBtn').addClass('d-none');
            }

            // Client-Side Instant Substring Matching on currently loaded RAM rows (0ms feedback)
            if (query.length >= 2) {
                const lower = query.toLowerCase();
                $('#BenchCandidatesListAjax tr[data-bench-row-id]').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    if (rowText.indexOf(lower) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else if (query.length === 0) {
                $('#BenchCandidatesListAjax tr[data-bench-row-id]').show();
            }

            // Debounced Server/ES Authoritative Query (250ms)
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                SuperAdminBenchRamStore.clear();
                fetchallBenchCandidatesAjax(1);
            }, 250);
        });

        // Clear Search Button
        $('#clearSearchBtn').on('click', function() {
            $('#kt_tagify_2').val('').trigger('input');
        });

        // 2. Sort Dropdown Change
        $('#sortingfilter').on('change', function() {
            SuperAdminBenchRamStore.clear();
            fetchallBenchCandidatesAjax(1);
        });

        // 3. Status Tabs Navigation
        $('.application_sttus_nav').on('click', function() {
            const relStatus = $(this).attr('rel');
            if (!relStatus) return;

            $('.tab-pill').removeClass('active');
            $(this).addClass('active');

            $('.overview-kpi-card').removeClass('active');
            $('#kpi_' + relStatus).addClass('active');

            $('#current_status_type').val(relStatus);
            clearSelection();
            fetchallBenchCandidatesAjax(1);
        });

        // 4. Hero Overview KPI Card Click
        $('.overview-kpi-card.kpi-filter').on('click', function() {
            const relStatus = $(this).data('rel');
            if (!relStatus) return;

            const targetTab = $('#tab_' + relStatus);
            if (targetTab.length > 0) {
                targetTab.trigger('click');
            }
        });

        // 5. Refresh Metrics Button
        $('#btn_refresh_counts').on('click', function() {
            SuperAdminBenchRamStore.clear();
            SuperAdminBenchIdb.clear();
            fetchallBenchCandidatesAjax(currentPage, true);
        });

        // 6. Pagination Click Handling
        $(document).on('click', '#paginationBenchCandidates .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled') || $(this).hasClass('dots')) return;

            fetchallBenchCandidatesAjax(page);
            const tableElem = document.getElementById('benchListingTable');
            if (tableElem) {
                tableElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // 7. Multi-selection Checkbox Handling
        $('#chk_all_bench_list').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.chk_list_item').prop('checked', isChecked);
            updateSelectionBar();
        });

        $(document).on('change', '.chk_list_item', function() {
            updateSelectionBar();
        });

        $('#bench_selection_close').on('click', function() {
            clearSelection();
        });

        // 8. Auto Approval Switch Change (Optimistic UI with Rollback)
        $('.chkStatus').on('change', function() {
            const $chk = $(this);
            const previousState = !$chk.is(':checked');
            const id = $chk.val();
            const token = "{{ csrf_token() }}";
            const idempotencyKey = 'autoapp_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);

            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.auto-approval') }}",
                data: { '_token': token, 'id': id, 'idempotency_key': idempotencyKey },
                headers: { 'X-Idempotency-Key': idempotencyKey },
                success: function(response) {
                    if (response.status === 'success') {
                        swalButtons.fire('Updated!', response.message, 'success');
                        SuperAdminBenchRamStore.clear();
                        SuperAdminBenchIdb.clear();
                        if (benchBroadcastChannel) {
                            benchBroadcastChannel.postMessage({ action: 'mutation' });
                        }
                    } else {
                        swalButtons.fire('Notice', response.message || 'Auto-approval toggled.', 'info');
                    }
                },
                error: function() {
                    // Rollback optimistic toggle state
                    $chk.prop('checked', previousState);
                    swalButtons.fire('Error', 'Failed to update auto-approval status.', 'error');
                }
            });
        });

        // 9. Bulk & Single Approve (Client-First Optimistic Execution)
        $(document).on('click', '.clickbtn.approve, .row-quick-approve', function() {
            let candidateIds = [];
            const isRowClick = $(this).hasClass('row-quick-approve');

            if (isRowClick) {
                candidateIds = [$(this).data('id')];
            } else {
                $('.chk_list_item:checked').each(function() {
                    candidateIds.push($(this).val());
                });
            }

            if (candidateIds.length === 0) {
                swalButtons.fire('Warning', 'Please select at least one candidate to approve.', 'warning');
                return;
            }

            swalButtons.fire({
                title: 'Confirm Approval?',
                text: `Are you sure you want to approve ${candidateIds.length} bench candidate(s) for the marketplace?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Step 1: Apply Optimistic DOM & Counter Updates (0ms latency)
                    const rollbackApproval = applyOptimisticApproval(candidateIds);

                    const token = "{{ csrf_token() }}";
                    const idempotencyKey = 'sa_app_' + candidateIds.join('_') + '_' + Date.now();

                    // Step 2: Authoritative Outbox Mutation
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('superadmin.company.candidate-approval') }}",
                        data: {
                            '_token': token,
                            'candidate_id': candidateIds,
                            'datatype': 'approve',
                            'idempotency_key': idempotencyKey
                        },
                        headers: { 'X-Idempotency-Key': idempotencyKey },
                        success: function(response) {
                            if (response.status === 'success') {
                                swalButtons.fire('Approved!', response.message, 'success');
                                SuperAdminBenchRamStore.clear();
                                SuperAdminBenchIdb.clear();
                                if (benchBroadcastChannel) {
                                    benchBroadcastChannel.postMessage({ action: 'mutation' });
                                }
                            } else {
                                rollbackApproval();
                                swalButtons.fire('Error', response.message || 'Approval failed.', 'error');
                            }
                        },
                        error: function(xhr) {
                            rollbackApproval();
                            swalButtons.fire('Error', xhr.responseJSON?.message || 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        });

        // 10. Bulk & Single Reject (Opens Rejection Mail Modal)
        $(document).on('click', '.reject_mail.disapprove, .row-quick-reject', function(e) {
            e.preventDefault();
            let candidateIds = [];
            let jobIdArr = [];
            const isRowClick = $(this).hasClass('row-quick-reject');

            if (isRowClick) {
                candidateIds = [$(this).data('id')];
                jobIdArr = [$(this).data('jobid') || $(this).data('id')];
            } else {
                $('.chk_list_item:checked').each(function() {
                    candidateIds.push($(this).val());
                    jobIdArr.push($(this).attr('data-jobid') || $(this).val());
                });
            }

            if (candidateIds.length === 0) {
                swalButtons.fire('Warning', 'Please select at least one candidate to reject.', 'warning');
                return;
            }

            $('#application_reject_mail_body_content').html('<div class="text-center p-10"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Loading rejection template...</p></div>');
            $('#disapprove').modal('show');

            const firstId = jobIdArr[0] || candidateIds[0];
            let url = "{{ route('superadmin.company.reject_mail', ':id') }}".replace(':id', firstId);
            const token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, 'arr': candidateIds, 'jobIdArr': jobIdArr },
                cache: false,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#application_reject_mail_body_content').html(response.view);
                    } else if (response.view) {
                        $('#application_reject_mail_body_content').html(response.view);
                    }
                },
                error: function() {
                    $('#application_reject_mail_body_content').html('<div class="alert alert-danger m-5">Failed to load rejection form.</div>');
                }
            });
        });

        // 11. Slide-In Drawer for Candidate Details
        $(document).on('click', '.bench_show_application_detail', function() {
            const id = $(this).data('id') || $(this).attr('data-id');
            if (!id) return;

            $('#kt_activitiesBenchApplication_toggle').trigger('click');
            const url = "{{ route('superadmin.company.showdetails-bench-candidates', ':id') }}".replace(':id', id);

            $('#bench_application_show_right_bar').html(
                '<div class="d-flex flex-column align-items-center justify-content-center p-20" style="min-height:300px;">' +
                '<i class="fa fa-spinner fa-spin fa-2x text-primary mb-3"></i>' +
                '<span class="fw-bold text-gray-700 fs-6">Loading candidate profile...</span></div>'
            );

            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    $('#bench_application_show_right_bar').html(response.view || response);
                },
                error: function() {
                    $('#bench_application_show_right_bar').html(
                        '<div class="p-10 text-center"><div class="alert alert-danger">Unable to load candidate details.</div></div>'
                    );
                }
            });
        });

        // Close Drawer Handler
        $(document).on('click', '#kt_activitiesBenchApplication_close', function() {
            var drawerEl = document.getElementById('kt_activitiesBenchApplication');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) {
                    drawer.hide();
                    return;
                }
            }
            $('#kt_activitiesBenchApplication').removeClass('drawer-on');
            $('.drawer-overlay').remove();
        });

        // Global Drawer Approve Action (Outbox Idempotent Mutation)
        window.approveCandidateFromDrawer = function(candidateId) {
            if (!candidateId) return;

            var $btn = $('#btnDrawerApproveCandidate');
            if ($btn.length) {
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Approving...');
            }

            applyOptimisticApproval([candidateId]);

            var idempotencyKey = 'approve_' + candidateId + '_' + Date.now();
            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.candidate-approval') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: candidateId,
                    type: 'approve',
                    idempotency_key: idempotencyKey
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Candidate approved for marketplace clearance!', 'Clearance Updated');
                    }
                    var detailUrl = "{{ route('superadmin.company.showdetails-bench-candidates', ':id') }}".replace(':id', candidateId);
                    $.get(detailUrl, function(res) {
                        if (res && res.view) {
                            $('#bench_application_show_right_bar').html(res.view);
                        }
                    });
                },
                error: function() {
                    rollbackOptimisticApproval([candidateId]);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to approve candidate. Please try again.', 'Error');
                    }
                    if ($btn.length) {
                        $btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="20 6 9 17 4 12"/></svg> Approve Clearance');
                    }
                }
            });
        };

        // Global Drawer Reject Action
        window.rejectCandidateFromDrawer = function(candidateId) {
            if (!candidateId) return;
            $('#application_reject_mail_body_content').html('<div class="text-center p-10"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Loading rejection template...</p></div>');
            $('#disapprove').modal('show');

            var url = "{{ route('superadmin.company.reject_mail', ':id') }}".replace(':id', candidateId);
            var token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: token,
                    candidate_id: [candidateId]
                },
                success: function(response) {
                    if (response && response.view) {
                        $('#application_reject_mail_body_content').html(response.view);
                    }
                },
                error: function() {
                    $('#application_reject_mail_body_content').html('<div class="alert alert-danger m-5">Failed to load rejection form.</div>');
                }
            });
        };
    });

    /**
     * Phase 4: Optimistic Approval Execution
     * Instantly updates DOM rows and counters in 0ms with automatic rollback on error.
     */
    function applyOptimisticApproval(candidateIds) {
        const originalRows = [];
        const currentStatus = $('#current_status_type').val() || 'approve';

        // 1. Capture snapshot of affected rows
        candidateIds.forEach(function(id) {
            const $row = $('tr[data-bench-row-id="' + id + '"]');
            if ($row.length) {
                originalRows.push({ id: id, html: $row.prop('outerHTML') });

                if (currentStatus === 'pending') {
                    // Smoothly fade out approved candidate from pending view
                    $row.css('background', '#e8fff3').fadeOut(220, function() {
                        $(this).remove();
                        if ($('#BenchCandidatesListAjax tr[data-bench-row-id]').length === 0) {
                            $('#BenchCandidatesListAjax').html(
                                '<tr><td colspan="9" class="text-center text-muted py-10">' +
                                '<div class="d-flex flex-column align-items-center justify-content-center">' +
                                '<i class="fa fa-circle-check text-success mb-2" style="font-size:28px !important;"></i>' +
                                '<span class="fs-6 fw-bold text-gray-700">All pending candidates on this page approved!</span>' +
                                '</div></td></tr>'
                            );
                        }
                    });
                } else {
                    // Update row badge to Approved
                    $row.find('.badge')
                        .removeClass('badge-light-warning text-warning badge-light-danger text-danger')
                        .addClass('badge-light-success text-success')
                        .text('Approved (Live)');
                    $row.find('.row-quick-approve').remove();
                }
            }
        });

        // 2. Optimistically update count badges
        const count = candidateIds.length;
        const currentPending = parseInt($('#cnt_pending').text().replace(/,/g, '')) || 0;
        const currentApprove = parseInt($('#cnt_approve').text().replace(/,/g, '')) || 0;

        const newPending = Math.max(0, currentPending - count);
        const newApprove = currentApprove + count;

        $('#cnt_pending').text(newPending);
        $('#kpival_pending').text(newPending.toLocaleString());
        $('#cnt_approve').text(newApprove);
        $('#kpival_approve').text(newApprove.toLocaleString());

        clearSelection();

        // Rollback function returned in case of network/server failure
        return function rollback() {
            originalRows.forEach(function(item) {
                const $target = $('tr[data-bench-row-id="' + item.id + '"]');
                if ($target.length) {
                    $target.replaceWith(item.html);
                } else {
                    $('#BenchCandidatesListAjax').prepend(item.html);
                }
            });
            $('#cnt_pending').text(currentPending);
            $('#kpival_pending').text(currentPending.toLocaleString());
            $('#cnt_approve').text(currentApprove);
            $('#kpival_approve').text(currentApprove.toLocaleString());
        };
    }

    /**
     * Update selection counter and toggle the selection toolbar
     */
    function updateSelectionBar() {
        const checkedCount = $('.chk_list_item:checked').length;
        const totalVisible = $('.chk_list_item').length;

        $('#bench_selected_count').text(checkedCount);
        $('#bench_selected_count_badge').text(checkedCount);

        if (checkedCount > 0) {
            $('#bench_selection_bar').addClass('show');
            $('.talent-hero-actions .approve, .talent-hero-actions .disapprove').removeClass('d-none');
        } else {
            $('#bench_selection_bar').removeClass('show');
            $('.talent-hero-actions .approve, .talent-hero-actions .disapprove').addClass('d-none');
        }

        if (totalVisible > 0 && checkedCount === totalVisible) {
            $('#chk_all_bench_list').prop('checked', true);
        } else {
            $('#chk_all_bench_list').prop('checked', false);
        }
    }

    /**
     * Clear all selected checkboxes
     */
    function clearSelection() {
        $('#chk_all_bench_list').prop('checked', false);
        $('.chk_list_item').prop('checked', false);
        updateSelectionBar();
    }

    /**
     * Fetch Bench Candidates via AJAX with Client-First RAM Store Check & IndexedDB Fallback
     */
    function fetchallBenchCandidatesAjax(page = 1, forceRefresh = false) {
        currentPage = page;
        const statusType = $('#current_status_type').val() || 'approve';
        const searchVal = $('#kt_tagify_2').val() || '';
        const sortVal = $('#sortingfilter').val() || '';
        const cacheKey = SuperAdminBenchRamStore.getKey(statusType, page, searchVal, sortVal);

        // Step 1: Check Browser RAM Store (0ms, 0% server/DB read strain)
        if (!forceRefresh) {
            const cached = SuperAdminBenchRamStore.get(cacheKey);
            if (cached) {
                renderCandidateResponse(cached);
                return;
            }
        }

        // Step 2: Check IndexedDB local snapshot store for instant cold rendering
        if (!forceRefresh) {
            SuperAdminBenchIdb.get(cacheKey).then(function(idbData) {
                if (idbData && !activeRequest) {
                    renderCandidateResponse(idbData);
                }
            });
        }

        if (activeRequest) {
            activeRequest.abort();
        }

        $('#benchListingTable_processing').show();

        activeRequest = $.ajax({
            url: "{!! route('superadmin.company.ajax-new-bench-candidates') !!}",
            type: 'GET',
            data: {
                page: page,
                status: statusType,
                custom_search: searchVal,
                sort: sortVal
            },
            success: function(response) {
                $('#benchListingTable_processing').hide();
                if (response) {
                    // Populate both RAM Store and IndexedDB
                    SuperAdminBenchRamStore.set(cacheKey, response);
                    SuperAdminBenchIdb.set(cacheKey, response);
                    renderCandidateResponse(response);
                }
            },
            error: function(xhr, status) {
                if (status === 'abort') return;
                $('#benchListingTable_processing').hide();
                $('#BenchCandidatesListAjax').html('<tr><td colspan="9" class="text-center text-danger py-8">Failed to fetch candidates. Please try again.</td></tr>');
            }
        });
    }

    /**
     * Render the candidate AJAX response into the DOM
     */
    function renderCandidateResponse(response) {
        $('#BenchCandidatesListAjax').html(response.html);
        $('#paginationBenchCandidates').html(response.pagination);
        $('#header_total_badge').text(Number(response.total || 0).toLocaleString() + ' Candidates');

        // Update Tab Badges & KPI counters if provided
        if (response.counts) {
            updateCountBadges(response.counts);
        }

        clearSelection();

        // Initialize tooltips if Bootstrap tooltip is active
        if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover', placement: 'top' });
        }
    }

    /**
     * Update tab count badges and hero KPI cards
     */
    function updateCountBadges(counts) {
        if (!counts) return;

        if (counts.pending !== undefined) {
            $('#cnt_pending').text(counts.pending);
            $('#kpival_pending').text(Number(counts.pending).toLocaleString());
        }
        if (counts.approve !== undefined) {
            $('#cnt_approve').text(counts.approve);
            $('#kpival_approve').text(Number(counts.approve).toLocaleString());
        }
        if (counts.reject !== undefined) {
            $('#cnt_reject').text(counts.reject);
            $('#kpival_reject').text(Number(counts.reject).toLocaleString());
        }
        if (counts.place !== undefined) {
            $('#cnt_place').text(counts.place);
            $('#kpival_place').text(Number(counts.place).toLocaleString());
        }
        if (counts.pool !== undefined) {
            $('#cnt_pool').text(counts.pool);
            $('#kpival_pool').text(Number(counts.pool).toLocaleString());
        }

        const totalCalc = (Number(counts.pending || 0) + Number(counts.approve || 0) + Number(counts.reject || 0) + Number(counts.place || 0) + Number(counts.pool || 0));
        $('#kpival_total').text(totalCalc.toLocaleString());
    }

    // Phase 5: Production-Grade Real-Time Reverb WebSocket Synchronization
    let lastProcessedVersion = 0;

    function handleRealtimeApprovalDelta(e) {
        if (!e) return;

        // Sequence / Out-of-Order Event Protection (Area 7: Concurrency & Race Conditions)
        if (e.entity_version && e.entity_version <= lastProcessedVersion) {
            console.warn('[Reverb Delta] Discarded out-of-order event. Version:', e.entity_version, 'Current:', lastProcessedVersion);
            return;
        }
        if (e.entity_version) {
            lastProcessedVersion = e.entity_version;
        }

        // 1. Invalidate Client-First RAM & IndexedDB Caches
        SuperAdminBenchRamStore.clear();
        SuperAdminBenchIdb.clear();

        // 2. Authoritative KPI & Tab Badge Delta Update (0ms, 0 HTTP network requests)
        if (e.counts) {
            updateCountBadges(e.counts);
        }

        // 3. In-Place DOM Patching for visible candidates
        if (e.candidate_ids && Array.isArray(e.candidate_ids)) {
            const currentTab = $('#current_status_type').val() || 'approve';
            let rowsModified = 0;

            e.candidate_ids.forEach(function(candId) {
                const $row = $('tr[data-bench-row-id="' + candId + '"]');
                if ($row.length) {
                    rowsModified++;
                    if (currentTab === 'pending' && e.superadmin_status === 1) {
                        // Smoothly transition approved candidate out of pending view
                        $row.css('background', '#e8fff3').fadeOut(280, function() {
                            $(this).remove();
                            if ($('#BenchCandidatesListAjax tr[data-bench-row-id]').length === 0) {
                                fetchallBenchCandidatesAjax(currentPage, true);
                            }
                        });
                    } else if (e.superadmin_status === 1) {
                        $row.find('.badge')
                            .removeClass('badge-light-warning text-warning badge-light-danger text-danger')
                            .addClass('badge-light-success text-success')
                            .text('Approved (Live)');
                        $row.find('.row-quick-approve').remove();
                    } else if (e.superadmin_status === 2) {
                        $row.find('.badge')
                            .removeClass('badge-light-warning text-warning badge-light-success text-success')
                            .addClass('badge-light-danger text-danger')
                            .text('Rejected');
                        $row.find('.row-quick-reject').remove();
                    }
                }
            });

            // 4. Subtle toast notice if mutation originated from another administrator session
            if (e.updated_by) {
                if (typeof toastr !== 'undefined' && typeof toastr.info === 'function') {
                    const noticeText = `${e.candidate_ids.length} candidate(s) marked as ${e.status_label || 'updated'} by ${e.updated_by}.`;
                    toastr.info(noticeText, 'Real-Time Sync');
                }
            }

            // If none of the mutated rows are currently visible and we are on an affected tab, refresh
            if (rowsModified === 0 && (currentTab === 'approve' || currentTab === 'pending')) {
                fetchallBenchCandidatesAjax(currentPage, true);
            }
        } else {
            fetchallBenchCandidatesAjax(currentPage, true);
        }
    }

    if (typeof window.Echo !== 'undefined') {
        try {
            // Subscribe to private channel with automatic authorization
            const benchChannel = window.Echo.private('superadmin.bench-candidates');

            benchChannel
                .listen('.bench.approval.updated', handleRealtimeApprovalDelta)
                .listen('.bench.approval.delta', handleRealtimeApprovalDelta);

            // Reconnection & Missed-Events Recovery (Area 6 & 7 Audit)
            if (window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                    if (states.current === 'connected' && (states.previous === 'unavailable' || states.previous === 'disconnected')) {
                        console.log('[Reverb Connection] Reconnected to cluster. Revalidating authoritative state...');
                        SuperAdminBenchRamStore.clear();
                        SuperAdminBenchIdb.clear();
                        fetchallBenchCandidatesAjax(currentPage, true);
                    }
                });
            }
        } catch(err) {
            console.warn('[Reverb Sync] Initialization notice:', err.message);
        }
    }
</script>
@endpush
