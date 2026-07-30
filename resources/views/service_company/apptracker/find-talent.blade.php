@extends('layouts.service_company_layout')
@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
    
    <style>
         #kt_activitiesCopy {
            width: 80% !important;
            max-width: 80% !important;
        }

        .selectednotice {
            background-color: #007bff !important;
            /* Example color */
            color: white !important;
        }

        .table-custom-class {
            table-layout: fixed;
            width: 100%;
        }
        .table-custom-class tbody tr {
            height: 102px;
            position: relative;
        }
        .table-custom-class td, .table-custom-class th {
            overflow: hidden;
        }
        
    </style>

<style id="jero-v2-responsive">
/* ===== JERO WORKS V2 RESPONSIVE ENGINE ===== */
#kt_content_container,.content-area,.main-content{width:100%;max-width:100%;}

#kt_activitiesCopy{
 width:80%!important;
 max-width:80%!important;
}

.table-container,
.table-scroll{
 width:100%;
 overflow-x:auto;
 -webkit-overflow-scrolling:touch;
}

table{
 width:100%!important;
 min-width:950px!important;
}

.hero-section{
 display:flex;
 justify-content:space-between;
 align-items:flex-start;
 gap:24px;
 flex-wrap:wrap;
}

.hero-left{flex:1 1 700px;}
.illus-svg-wrap{flex:0 0 auto;}

.stats-row{
 display:grid;
 grid-template-columns:repeat(4,minmax(180px,1fr));
 gap:16px;
}

.stat-card{
 min-width:0;
 width:100%;
}

tbody td{
 white-space:normal;
 word-break:normal;
}

.candidate-cell{
 display:flex;
 gap:12px;
 min-width:220px;
}

.submission-item,
.skills-list{
 overflow-wrap:anywhere;
}

img,svg{
 max-width:100%;
 height:auto;
}

@media (max-width:1440px){
.stats-row{
 grid-template-columns:repeat(2,minmax(180px,1fr));
}
table{
 min-width:850px!important;
}
}

@media (max-width:1024px){
.hero-section{
 flex-direction:column;
}

.stats-row{
 grid-template-columns:repeat(2,1fr);
}

.d-flex.flex-stack.gap-5{
 flex-wrap:wrap!important;
}

.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-8,.col-lg-12{
 width:100%!important;
 max-width:100%!important;
 flex:0 0 100%!important;
}
}

@media (max-width:768px){
.stats-row{
 grid-template-columns:1fr;
}

table{
 min-width:700px!important;
}

.hero-title{
 font-size:22px!important;
}

.candidate-cell{
 flex-direction:column;
}

.btn{
 white-space:normal;
}

.modal-body{
 padding:1rem!important;
}
}
</style>


    <style>
        :root {
            --sidebar-bg: #0b1120;
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --green: #22c55e;
            --green-light: #dcfce7;
            --green-text: #166534;
            --orange: #f97316;
            --orange-light: #ffedd5;
            --orange-text: #9a3412;
            --red: #ef4444;
            --red-light: #fee2e2;
            --red-text: #991b1b;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --blue-light: #dbeafe;
            --blue-text: #1e40af;
            --bg-page: #f8fafc;
            --bg-white: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        .view-toggle {
            display: flex;
            gap: 4px;
        }

        .view-btn {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .view-btn:hover {
            border-color: var(--text-muted);
            color: var(--text-primary);
        }

        .view-btn.active {
            background: var(--bg-page);
            border-color: var(--text-muted);
            color: var(--text-primary);
        }

        .table-container {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width:950px;
        }

        thead th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        thead th:first-child {
            width: 40px;
            padding-left: 20px;
        }

        tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        tbody td {
            padding: 16px;
            vertical-align: top;
            white-space: nowrap;
        }

        tbody td:first-child {
            padding-left: 20px;
        }

        .checkbox {
            width: 16px;
            height: 16px;
            border: 1.5px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            appearance: none;
            position: relative;
            transition: all 0.2s;
        }

        .checkbox:checked {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .checkbox:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .candidate-cell {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .candidate-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 15px;
            color: white;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .avatar-green { background: #10b981; }
        .avatar-blue { background: #3b82f6; }
        .avatar-pink { background: #f472b6; }
        .avatar-lightblue { background: #60a5fa; }

        .candidate-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .candidate-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }

        .candidate-role {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .candidate-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge svg {
            width: 10px;
            height: 10px;
        }

        .badge-green {
            background: var(--green-light);
            color: var(--green-text);
        }

        .badge-blue {
            background: var(--blue-light);
            color: var(--blue-text);
        }

        .badge-purple {
            background: var(--purple-light);
            color: #5b21b6;
        }

        .badge-orange {
            background: var(--orange-light);
            color: var(--orange-text);
        }

        .badge-red {
            background: var(--red-light);
            color: var(--red-text);
        }

        .experience-main {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-primary);
        }

        .experience-sub {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .skills-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .skill-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .skill-name {
            font-size: 12px;
            color: var(--text-primary);
            width: 70px;
            font-weight: 500;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .star_skill {
            width: 14px;
            height: 14px;
            color: #fbbf24;
        }

        .star_skill.empty {
            color: #e2e8f0;
        }

        .more-skills {
            font-size: 11px;
            color: var(--primary-blue);
            font-weight: 500;
            cursor: pointer;
            margin-top: 2px;
            display: inline-block;
        }

        .more-skills:hover {
            text-decoration: underline;
        }

        .availability-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .avail-immediate {
            background: var(--green-light);
            color: var(--green-text);
        }

        .avail-days {
            background: var(--orange-light);
            color: var(--orange-text);
        }

        .avail-notice {
            background: var(--red-light);
            color: var(--red-text);
        }

        .avail-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .verified-text {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: var(--text-secondary);
        }

        .verified-text svg {
            width: 12px;
            height: 12px;
            color: var(--green);
        }

        .verified-text.orange svg {
            color: var(--orange);
        }

        .location-main {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .location-sub {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 1px;
        }

        .salary-amount {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .salary-type {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 1px;
        }

        .last-active-time {
            font-size: 13px;
            color: var(--text-primary);
            font-weight: 500;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            font-size: 12px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-green { background: var(--green); }
        .status-orange { background: var(--orange); }

        .status-text {
            color: var(--text-secondary);
        }

        .status-text.active {
            color: var(--green-text);
            font-weight: 500;
        }

        .submissions-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .submission-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .submission-item svg {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
        }

        .submission-count {
            font-weight: 600;
            color: var(--text-primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-blue-hover);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        .btn-dropdown {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-dropdown svg {
            width: 14px;
            height: 14px;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            padding: 0 4px;
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            padding: 0 8px;
        }

        .page-btn:hover:not(:disabled) {
            border-color: var(--text-muted);
            color: var(--text-primary);
            background: var(--bg-page);
        }

        .page-btn.active {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
            font-weight: 600;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-btn svg {
            width: 16px;
            height: 16px;
        }

        .page-size {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .page-size select {
            padding: 6px 28px 6px 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 13px;
            background: var(--bg-white);
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 6px center;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .mobile-menu-btn {
                display: flex !important;
            }
            
            .header-search input {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            
            .header-search {
                display: none;
            }
            
            .toolbar {
                flex-wrap: wrap;
            }
            
            .search-box {
                max-width: 100%;
                order: -1;
                width: 100%;
            }
            
            .user-info {
                display: none;
            }
        }

        .shield-icon {
            width: 22px;
            height: 22px;
            vertical-align: middle;
            margin-left: 6px;
            flex-shrink: 0;
        }

        /* ── Hero section redesign ── */
        .hero-section-wrap {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .hero-head-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .hero-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.3;
        }
        .hero-sub {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
        }
        .hero-stats-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .h-stat-card {
            flex: 1 1 160px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            min-width: 140px;
        }
        .h-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .h-stat-icon svg {
            width: 20px !important;
            height: 20px !important;
        }
        .h-icon-blue   { background: #eff6ff; color: #2563eb; }
        .h-icon-green  { background: #f0fdf4; color: #16a34a; }
        .h-icon-orange { background: #fff7ed; color: #ea580c; }
        .h-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .h-stat-num {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .h-stat-label {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
        }

        .mobile-menu-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--text-secondary);
            margin-right: 8px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }

        @media (max-width: 1024px) {
            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
    <style>
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
    </style>

    <link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar d-none" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0" style="position: relative;">
            <h3>Find and Hire Bench Talents</h3>
            </div>
            <!--end::Page title-->
            <form action="" id="filter-form" style="">
                @csrf
                <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
                <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                    <!--begin::Search-->
                    <div class="d-none d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                    transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

                            <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 d-none application_copy"
                        data-toggle="modal" data-target="#application_copy">
                        <!--begin::Svg icon-->
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                    <path
                                        d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        Add to job </a>

                        </div>
                        <!--end::Toolbar-->
                    </div>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    @php
        //echo '<pre>'; print_r($candStatusClass);
        $sourcing_bg = $screening_bg = $interview_bg = $negotation_bg = $offer_bg = $joining_bg = $rejected_bg = $All_candidate_bg =
            'alert bg-gray text-center circle_boxes';
        $sourcing_text = $screening_text = $interview_text = $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text =
            'text-center color-all';

        if (!empty(@$candStatusClass['Index'])) {
            if (@$candStatusClass['Index'] == 'Sourcing') {
                $sourcing_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $sourcing_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Screening') {
                $screening_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $screening_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Interview') {
                $interview_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $interview_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Negotiation') {
                $negotation_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $negotation_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Offered') {
                $offer_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $offer_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Joined') {
                $joining_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $joining_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } elseif (@$candStatusClass['Index'] == 'Rejected') {
                $rejected_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $rejected_text = 'text-center  ' . @$candStatusClass['nav_text'];
            } else {
                $All_candidate_bg = 'alert text-center circle_boxes ' . @$candStatusClass['bg_class'];
                $All_candidate_text = 'text-center  ' . @$candStatusClass['nav_text'];
            }
        }

        $tr_class = 'bg-gray';
        if (!empty($mstatus_id)) {
            if ($mstatus_id == 'all') {
                $tr_class = 'bg-total-active';
            } else {
                $tr_class = 'bg-gray';
            }
        }

    @endphp

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" id="kt_content" style="margin-top: -60px;">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">

            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">

                
                <!--Start Filter Drawer-->
                <div id="fd-overlay" onclick="fdClose()"></div>
                <div id="apptrackerfilter" tabindex="-1">
                            <div class="modal-header flex-stack">
                                <h2>Filter</h2>
                                <div class="btn btn-sm btn-icon btn-active-color-primary" onclick="fdClose()"
                                    id="apptrackerfilter_close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                transform="rotate(45 7.41422 6)" fill="black"></rect>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="apptrackerfilter_body_content" id="">
                                <div class="modal-body" style="padding: 2em;">
                                    <form id="createSchedule" class="ajax-form" method="post">
                                        <div class="section">
                                            <p class="section-label">1. Enter skills to filter candidate</p>
                                                <div class="suggestions-wrap search-input-wrap">
                                                    <input class="search-input"
                                                        name="kt_tagify_users" value="{{ @$default_skill }}" placeholder="Enter Skills to search Bench Talents....."
                                                        id="kt_tagify_users" />
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
                                            <p class="section-label">2. Select location for candidate</p>
                                            <div class="search-input-wrap">
                                                <input class="search-input"
                                                    value="{{ $default_loc }}" id="kt_tagify_location"
                                                    placeholder="Enter your Location..." />
                                            </div>
                                        </div>

                                        <div class="section">
                                            <p class="section-label">3. Education</p>
                                            <div class="search-input-wrap">
                                             <input class="search-input" value="{{ $qualification }}"
                                                id="kt_tagify_education" placeholder="About Education..." />
                                            </div>
                                        </div>
                                        <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                              <p class="section-label">4. Experience</p>
                                            <div class="col-lg-6">
                                                <label class="form-label required">Select minimum in years:</label>
                                                <select class="form-select form-select-solid" name="min_exp"
                                                    id="min_exp">
                                                    <option value="" selected="">Select
                                                        Experience</option>
                                                    <option value="0" {{ 0 == $min_exp ? 'selected' : '' }}>Freshers
                                                    </option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == $min_exp ? 'selected' : '' }}>{{ $i }}
                                                            &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label required">Select maximum in years:</label>
                                                <select class="form-select form-select-solid" name="max_exp"
                                                    id="max_exp">
                                                    <option value="" selected="">Select
                                                        Experience</option>
                                                    <option value="0" {{ 0 == $max_exp ? 'selected' : '' }}>Freshers
                                                    </option>
                                                    @for ($i = 1; $i <= 30; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == $max_exp ? 'selected' : '' }}>{{ $i }}
                                                            &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                                    @endfor
                                                </select>
                                                <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                            </div>
                                        </div>

                                        <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                              <p class="section-label">5. Salary</p>
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Currency</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="min_salary_currency"
                                                                    id="min_salary_currency">
                                                                    <option value="">Select currency</option>
                                                                    <option value="0"
                                                                        {{ 0 == $min_salary_currency ? 'selected' : '' }}>INR
                                                                    </option>
                                                                    <option value="1"
                                                                        {{ 1 == $min_salary_currency ? 'selected' : '' }}>EUR
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ 2 == $min_salary_currency ? 'selected' : '' }}>JPY
                                                                    </option>
                                                                    <option value="3"
                                                                        {{ 3 == $min_salary_currency ? 'selected' : '' }}>GBP
                                                                    </option>
                                                                    <option value="4"
                                                                        {{ 4 == $min_salary_currency ? 'selected' : '' }}>USD
                                                                    </option>
                                                                    <option value="5"
                                                                        {{ 5 == $min_salary_currency ? 'selected' : '' }}>AED
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Mode</label>
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                                    <option value="">Select Mode</option>
                                                                    <option value="0"
                                                                        {{ 0 == $min_sal_mode ? 'selected' : '' }}>Annual</option>
                                                                    <option value="1"
                                                                        {{ 1 == $min_sal_mode ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="2"
                                                                        {{ 2 == $min_sal_mode ? 'selected' : '' }}>Hourly</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="row"> 
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Minimum Salary:</label>
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="min_sal" id="min_sal" placeholder="Enter min salary"
                                                                    value="{{ $min_salary }}">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Maximum Salary:</label>
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="max_sal" id="max_sal" placeholder="Enter max salary"
                                                                    value="{{ $max_salary }}">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div> 
                                                            <span class="form-text text-muted" id="salary_error" style="    color: red !important;text-align: center;"></span>                                                             
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="section flex-column mb-8 fv-row fv-plugins-icon-container">
                                            <p class="section-label">6. Notice Period/Availability to join</p>
                                            <div class="d-flex flex-stack gap-5 mb-3">

                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="1" id="app_int_notice1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Any</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="8" id="app_int_notice8">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice8">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-6">Immediate</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-3">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="9" id="app_int_notice9">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice9">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">Served
                                                                Notice</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-4">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="7" id="app_int_notice7">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice7">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">Currently
                                                                Serving Notice</span>
                                                        </span>
                                                    </label>
                                                </div>







                                            </div>
                                            <div class="d-flex flex-stack gap-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="2" id="app_int_notice2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">0-15
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="3" id="app_int_notice3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">1
                                                                Month</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="4" id="app_int_notice4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">2
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="5" id="app_int_notice5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">3
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col-lg-3">
                                                    <input type="radio" class="btn-check app_int_notice"
                                                        name="app_int_notice" value="6" id="app_int_notice6">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_notice6">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-6">More than 3
                                                                Months</span>
                                                        </span>
                                                    </label>
                                                </div>







                                            </div>
                                        </div>
                                        
                                        <div class="section d-flex flex-column mb-8">
                                            <p class="section-label">7. Hiring type</p>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="0" id="app_int_outsource1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="1" id="app_int_outsource2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="radio" class="btn-check app_int_outsource"
                                                        name="app_int_outsource" value="2" id="app_int_outsource3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_outsource3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Contract</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="section d-flex flex-column mb-8">
                                            <p class="section-label">8. Activity</p>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="1" id="app_int_activity1"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="2" id="app_int_activity2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">30
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="3" id="app_int_activity3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">60
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="4" id="app_int_activity4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">90
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="5" id="app_int_activity5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">180
                                                                Days</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_activity"
                                                        name="app_int_activity" value="6" id="app_int_activity6">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_activity6">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">1
                                                                Year</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <!--<div class="modal-footer" style="display: table;">-->
                                <!--  <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal">Close <i class="fa fa-circle fa-sm"></i> </button>-->
                                <!--  <button type="button" class="btn btn-light-success btn-sm p-1" id="" onclick="newUpdateJobstatus()">Update <i class="fa fa-spinner fa-sm"></i> </button>-->
                                <!--</div>-->
                                <div class="modal-footer flex-center">
                                    <div class="div1">
                                        <button type="button" class="btn btn-secondary me-3"
                                            onclick="fdClose()">Close</button>
                                        <button type="button" id="changetextupdate"
                                            class="btn btn-primary changetextupdate" onclick="filtersdataget()">
                                            <span class="indicator-label">Submit</span></button>
                                    </div>
                                </div>
                                <script src="https://servicetesting.4bell.com/assets/node_modules/moment/moment.js?ver=1553620716"
                                    type="text/javascript"></script>
                                <script src="https://servicetesting.4bell.com/assets/node_modules/multiselect/js/jquery.multi-select.js?ver=1553620716">
                                </script>
                                <script
                                    src="https://servicetesting.4bell.com/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js?ver=1553620716"
                                    type="text/javascript"></script>
                            </div>
                </div>
                <!--End Filter Drawer-->

                <main class="main-content">
                    <!-- Content Area -->
                    <div class="content-area">

                        <!-- HERO -->
                        <div class="hero-section-wrap">
                            <div class="hero-head-row">
                                <div>
                                    <div class="hero-title">
                                        Bench Candidates
                                        <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                            <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
                                        </svg>
                                    </div>
                                    <div class="hero-sub">Discover verified bench candidates posted by agencies and add them to your open jobs</div>
                                </div>
                            </div>
                            <div class="hero-stats-row">
                                <div class="h-stat-card">
                                    <div class="h-stat-icon h-icon-blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="h-stat-num">344</div>
                                        <div class="h-stat-label">Total Agencies</div>
                                    </div>
                                </div>
                                <div class="h-stat-card">
                                    <div class="h-stat-icon h-icon-blue">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                            <path d="M9 9h6M9 13h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="h-stat-num">3333</div>
                                        <div class="h-stat-label">Verified Agencies</div>
                                    </div>
                                </div>
                                <div class="h-stat-card">
                                    <div class="h-stat-icon h-icon-green">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="h-stat-num">92</div>
                                        <div class="h-stat-label">Highly Rated (4★+)</div>
                                    </div>
                                </div>
                                <div class="h-stat-card">
                                    <div class="h-stat-icon h-icon-orange">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                            <line x1="12" y1="12" x2="12" y2="16"/>
                                            <line x1="10" y1="14" x2="14" y2="14"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="h-stat-num">24</div>
                                        <div class="h-stat-label">AI Matched for You</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FILTER BAR -->
                        <div class="filter-bar">
                            <div class="search-input-wrap">
                                <input type="text" class="search-input" name="inputJobSearch"
                                    value="{{ @$default_skill }}" placeholder="Enter Skills to search Bench Talents....." id="kt_tagify_2" />
                                {{-- <div data-toggle="modal" data-target="#apptrackerfilter"
                                    style="position: absolute; z-index: 99; width: 100%; height: 100%; top: 0;"> </div> --}}
                            </div>
                            <button class="ai-match-btn" onclick="filtersdataget()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                </svg>
                                Search
                            </button>
                            <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                                <select class="form-select form-select-lg sortingfilterselect" data-hide-search="true"
                                    id="sortingfilter" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" onchange="filtersdataget()">
                                    <option value="Show All" selected="">Sort</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice Period (Serving)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                            </div>
                            
                            <div class="more-filters-btn" onclick="fdOpen()" style="cursor:pointer;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
                                </svg>
                                More Filters
                            </div>
                        </div>


                        <!--begin::Card-->
                        <div class="card">
                            <!--begin::Card body-->
                            <div class="card-body pt-3 px-5">
                                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                                <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                                    <!--begin::Table head-->
                                    
                                    {{-- id="myTable" --}}
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="fw-bolder text-muted">
                                            <th style="width:18%;">Talent</th>
                                            <th style="width:8%;">Experience</th>
                                            <th style="width:13%;">Skills</th>
                                            <th style="width:12%;">Availability</th>
                                            <th style="width:13%;">Location</th>
                                            <th style="width:14%;">Expected Salary</th>
                                            <th style="width:15%;">Action</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <tbody class="text-gray-600 fw-bold" id="candidatetList">
                                    </tbody>
                                </table>
                                <div id="paginationcandidate"></div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                </main>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->

    




    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close" style="z-index: 9999 !important;">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-toolbar px-4 py-2 d-flex justify-content-end" id="kt_activities_header">
                <button type="button" id="submit-frm-btn" class="btn btn-primary btn-sm copy_current_selected_applicant d-none me-2">
                    <span class="indicator-label">Save to Jobs</span>
                    <span class="indicator-progress">Please wait...</span>
                </button>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>



@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
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

        
        // The DOM elements you wish to replace with Tagify
        var inputloc = document.querySelector("#kt_tagify_location");
        var cities = @json($cities);
        // Initialize Tagify script on the above inputs
        var tagifylocation = new Tagify(inputloc, {
            whitelist: cities,
            enforceWhitelist: true, // Prevents users from adding custom values
            maxTags: 1,
            dropdown: {
                maxItems: 20, // <- mixumum allowed rendered suggestions
                classname: "", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
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
        // Prevent creating tags on blur or enter key
        tagifylocation.on('input', function(e) {
            tagifylocation.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifylocation.on('dropdown:select', function(e) {
            tagifylocation.addTags([e.detail.data.value]);
        });
        tagifylocation.on('add', function(e) {

        });

        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

            // Debounced function to fetch suggestions
        const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                        tagifylocation.settings.whitelist = data; // Update whitelist
                        // tagifylocation.dropdown.show(query); // Show suggestions
                        tagifylocation.dropdown.show.call(tagifylocation);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay

        // Listen for user input to trigger API call
        tagifylocation.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 2) return;
            fetchSuggestions(query);
        });

        // The DOM elements you wish to replace with Tagify
        var inputdeducation = document.querySelector("#kt_tagify_education");
        var qualification = @json($qualificationTag);
        var tagifyqualification = new Tagify(inputdeducation, {
            whitelist: qualification,
            maxTags: 10,
            dropdown: {
                maxItems: 20, // <- mixumum allowed rendered suggestions
                classname: "", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: false // <- do not hide the suggestions dropdown once an item has been selected
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

        // Prevent creating tags on blur or enter key
        tagifyqualification.on('input', function(e) {
            tagifyqualification.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifyqualification.on('dropdown:select', function(e) {
            tagifyqualification.addTags([e.detail.data.value]);
        });
        tagifyqualification.on('add', function(e) {
            // alert(qualification);
            // Remove the tag if it wasn't selected from the dropdown
            if (!qualification.includes(e.detail.data.value)) {
                setTimeout(() => {
                    tagifyqualification.removeTag(e.detail.data.value);
                }, 200);

            }
        });

        document.addEventListener('click', function (e) {

            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {

                // get tag element
                const tagElm = e.target.closest('.tagify__tag');

                // remove tag from tagify
                tagify.removeTags(tagElm);
                tagify2.removeTags(tagElm);
                tagifylocation.removeTags(tagElm);
                tagifyqualification.removeTags(tagElm);

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


        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        $(document).ready(function() {

            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_sal", function() {
                salary_check()
            });
            $(document).on("change", "#min_exp", function() {
                experience_check()
            });
            $(document).on("change", "#max_exp", function() {
                experience_check()
            });

            $(document).on('keyup', '#min_sal', function() {
                // alert("dkd");
                var number = parseInt($('#min_sal').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));

            });
            $(document).on('keyup', '#max_sal', function() {
                var number = parseInt($('#max_sal').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));

            });

        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function convertToWords(num) {
            if (num < 10) {
                return ones[num];
            } else if (num < 20) {
                return teens[num - 10];
            } else if (num < 100) {
                return tens[Math.floor(num / 10)] + ' ' + ones[num % 10];
            } else if (num < 1000) {
                return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100);
            } else if (num < 100000) {
                return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000);
            } else if (num < 10000000) {
                return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000);
            } else if (num < 1000000000) {
                return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        function salary_check() {
            var minimum_salary = parseInt($("#min_sal").val());
            var maximum_salary = parseInt($("#max_sal").val());
            if (minimum_salary != '') {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Enter the maximum salary greater than the minimum salary',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_sal").val('');
                }
            }
        }

        function experience_check() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (min_exp != '') {
                if (min_exp > max_exp) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Select the maximum experience greater than the minimum experience',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_exp").val('');
                }
            }
        }


        var input2 = document.querySelector("#kt_tagify_2");
        new Tagify(input2);
    </script>
    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');
        }, 500);

        function filtersdataget() {
            $('.changetextupdate').text('Searching....!');
            fdClose();
            setTimeout(() => {
                fetchCandidates();
                $('.changetextupdate').text('Submit');
            }, 500);
        }

        function tableLoad(type) {

            var jobid = '{{ @$job->id }}';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();

            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.public_bench_data') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&kt_tagify_company=' + kt_tagify_company +
                    '&app_int_notice=' + app_int_notice + '&sal_mode=' + sal_mode + '&app_int_outsource=' +
                    app_int_outsource,
                "fnDrawCallback": function(oSettings) {},
                "initComplete": function(settings, json) {

                    $('.clientStatusLoader').html('');
                    $('.jobLoader').html('');
                    $('.sourceLoader').html('');

                    $('.search_np_idLoader').html('');
                    $('.search_passportLoader').html('');
                    $('.statusLoader').html('');


                },



                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        orderable: false,
                        searchable: false,
                        width: '17%'
                    },
                    {
                        data: 'Exp',
                        name: 'Exp',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        searchable: false,
                        orderable: false,
                        width: '10%'
                    },
                    {
                        data: 'NP',
                        name: 'NP',
                        searchable: false,
                        orderable: false
                    },

                    {
                        data: 'Qualification',
                        name: 'Qualification',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false
                    },
                    // {data: 'Type', name: 'Type', orderable : false},   
                    // {data: 'OfferBy', name: 'OfferBy', orderable: false},
                    {
                        data: 'LastActive',
                        name: 'LastActive',
                        orderable: false
                    },

                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false,
                        width: '10%'
                    }
                ]

            });
            new $.fn.dataTable.FixedHeader(table);

            countexist();

        }

        function countexist() {
            setTimeout(() => {
                // var rowCount = $('#myTable tbody tr:not(.dataTables_empty)').length;
                var allRowCount = $('#myTable tbody tr').length;

                // Count rows that have a cell with the class dataTables_empty
                var emptyRowCount = $('#myTable tbody tr:has(td.dataTables_empty)').length;
                var existingDataCount = allRowCount - emptyRowCount;
                $('.public_bench_data').text(existingDataCount);


            }, 2500);
        }
    </script>

    <script>



        window._candidateSkills = '';
        window._candidateInfo   = {};
        $(document).on('click', '.openSideBar', function() {
            window._candidateSkills = $(this).data('skills') || '';
            window._candidateInfo   = {
                name  : $(this).data('cname')  || '',
                desig : $(this).data('cdesig') || '',
                exp   : $(this).data('cexp')   || '',
                ctc   : $(this).data('cctc')   || '',
                np    : $(this).data('cnp')    || '',
                loc   : $(this).data('cloc')   || '',
                type  : $(this).data('ctype')  || '',
                id    : $(this).data('row-id') || ''
            };
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.talent-add', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var idid = $(this).data('row-id');
            // alert(idid);
            var arr = [idid];
           
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
                                $("#kt_activitiesCopy_close").click();
                            });
                        return false;
                    }

                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                        setTimeout(function(){ if(typeof updateBenchHero==='function') updateBenchHero(); }, 80);
                    }
                }
            });
        });




        $(document).on('click', '.application_copy', function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.talent-add', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
                                $("#kt_activitiesCopy_close").click();
                            });
                        return false;
                    }

                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                }
            });
        });

        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
            } else {
                $('.chk_list_item').prop('checked', false);	
            }
        });
        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });

        $(document).on('click', '.chk_list_item', function() {
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });


        $(document).on('click', '.add_to_inreview', function() {
            var id = $(this).data('row-id');
            var source_from_type = $(this).data('row-source_from_type');
            var source_jobid = $(this).data('row-jobid');

            var url = '{{ route('service_company.apptracker.rec_candidate_assign') }}';
            var token = "{{ csrf_token() }}";


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
                confirmButtonText: 'Yes, Add to In-Review!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {



                    var inner_url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                    var inner_token = "{{ csrf_token() }}";
                    var inner_baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    var eventId = 10;

                    $.ajax({
                        type: 'POST',
                        url: inner_url,
                        beforeSend: function() {},
                        data: {
                            '_token': inner_token,
                            'event': 'Bench_Sales',
                            'app_id': id,
                            'source_jobid': source_jobid,
                            'eventId': eventId
                        },
                        //data		: {'_token': inner_token, 'event':'CVACCESS'}, NEW_BENCH_CLIENT_REQUEST
                        success: function(response) {




                            if (response.status == "success") {
                                $.ajax({
                                    type: 'GET',
                                    url: url,
                                    data: {
                                        'id': id,
                                        'source_from_type': source_from_type,
                                        'source_jobid': source_jobid
                                    },
                                    success: function(response) {

                                        if (response.status == 'coinCheck') {
                                            Swal.fire("Success",
                                                ` ${response.message}`,
                                                "warning");
                                            return false;
                                        }


                                        if (response.status == 'success') {
                                            // tableLoad('load');
                                            fetchCandidates();
                                            swalWithBootstrapButtons.fire(
                                                'In-Review!',
                                                'Selected request has been In-Review.',
                                                'success'
                                            )
                                        }
                                    }
                                });




                            } else if (response.status == "info") {
                                swalWithBootstrapButtons.fire('Fail!', response.message,
                                    'error')
                            }

                        }
                    });


                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Selected request is safe',
                        'error'
                    )
                }
            })


        });

        $(document).on('click', '.downloadCount', function() {
            var inner_url = "{{ route('service_company.bench-candidates.downloadResumeBenchCandidate') }}";
            var inner_token = "{{ csrf_token() }}";
            var inner_baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var candidateId = $(this).attr('data-id');

            $.ajax({
                type: 'POST',
                url: inner_url,
                beforeSend: function() {},
                data: {
                    '_token': inner_token,
                    'candidateId': candidateId
                },
                success: function(response) {

                }

            });
        });
        

        $(document).ready(function() {
            // $.noConflict();
            
            setTimeout(() => {
                fetchCandidates();
            }, 200);
            
            
            $(document).on('click', '#paginationcandidate .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcandidate .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcandidate .page-link').removeClass('disabled');
                });
            });


        });


        function fetchCandidates(page = 1, search = '') {
            var jobid = '0';
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var kt_tagify_location = $('#kt_tagify_location').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_status = $('[name="app_int_status"]:checked').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();
            var kt_tagify_company = $('#kt_tagify_company').val();

            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.apptracker.findTalentData') !!}?page=${page}`,
                type: 'GET',
                data: {
                jobid:jobid, 
                search:inputSearch,
                sortingfilter:sortingfilter, 
                country:country,
                cus_search:(cus_search),
                min_exp:min_exp,
                max_exp:max_exp, 
                kt_tagify_location:kt_tagify_location,
                min_sal:min_sal,
                max_sal:max_sal, 
                min_salary_currency:min_salary_currency,
                kt_tagify_education:kt_tagify_education,
                app_int_status:app_int_status,
                app_int_activity:app_int_activity,
                kt_tagify_company:kt_tagify_company,
                app_int_notice:app_int_notice, 
                sal_mode:sal_mode,
                app_int_outsource:app_int_outsource
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('#candidatetList').html(response.html);
                    // $('.public_bench_data').html(response.total);
                    $('#paginationcandidate').html(response.pagination);
                }
            });
        }


        $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job Selected !', 'Success');
            } else {
                showToastr('error', 'Job Removed!', 'Removed');
            }

            // Show/hide the bulk action button based on overall selection
            if (checkedCount > 0) {
                $('.copy_current_selected_applicant').removeClass("d-none");
            } else {
                $('.copy_current_selected_applicant').addClass("d-none");
            }
        });






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
