@extends('layouts.superadmin_layout')
@push('head-script')
    <style>
        #send_email .modal-dialog {
            margin: .3rem auto !important;
        }

        #fwd_resume .modal-dialog {
            margin: .3rem auto !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {
            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0rem;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesApplication {
                width: 1025px;
            }
            #kt_client_activitiesApplication {
                width: 1025px;
            }
            #kt_activitiesCopy {
                width: 1025px;
            }
            #kt_activitiesClient {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
            }
            #kt_client_activitiesApplication {
                width: 1200px;
            }
            #kt_activitiesCopy {
                width: 1200px;
            }
            #kt_activitiesClient {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
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

        #cvh_print_area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #cvh_print_area, #cvh_print_area * { visibility: visible; }
            #cvh_print_area {
                display: block;
                position: absolute;
                top: 0; left: 0;
                width: 100%;
                padding: 20px;
            }
            #cvh_print_area h2 { font-size: 16px; margin: 0 0 4px; }
            #cvh_print_area .cvh-print-meta { font-size: 11px; color: #666; margin-bottom: 14px; }
            #cvh_print_area table { width: 100%; border-collapse: collapse; }
            #cvh_print_area th, #cvh_print_area td {
                border: 1px solid #ccc; padding: 6px 8px; font-size: 11px; text-align: left;
                vertical-align: top;
            }
            #cvh_print_area .form-check { display: none !important; }
        }

        /* Hero KPI stats (ES-only, ride existing allcandidates query) */
        .cvh-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin: 14px 0; font-family: 'Poppins', sans-serif; }
        .cvh-card { background: #fff; border-radius: 14px; padding: 13px 16px; border: 1px solid #f1f5f9; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .cvh-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .cvh-icon svg { width: 18px; height: 18px; }
        .cvh-body { flex: 1; min-width: 0; }
        .cvh-label { font-size: 11px; color: #64748b; font-weight: 500; margin: 0 0 4px; }
        .cvh-value { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.15; }
        .cvh-delta { font-size: 11px; margin-top: 4px; font-weight: 500; }
        .cvh-delta.up   { color: #16a34a; }
        .cvh-delta.info { color: #64748b; }
        .cvh-delta.warn { color: #dc2626; }

        @media (max-width: 1440px) { .cvh-value { font-size: 16px; } }
        @media (max-width: 1200px) { .cvh-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .cvh-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }
        @media (max-width: 480px)  { .cvh-grid { grid-template-columns: 1fr; } }

        /* Stage tabs */
        .cvh-tabs-card { margin-bottom: 14px; border-width: 1px; transition: border-color .15s; }
        .cvh-tabs-card .card-body { padding: 4px 0; }
        .cvh-tabs { display: flex; align-items: stretch; font-family: 'Poppins', sans-serif; }
        .cvh-tab { flex: 1 1 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; padding: 11px 6px; border: none; border-right: 1px solid #f1f5f9; background: transparent; color: #64748b; white-space: nowrap; cursor: pointer; transition: background .15s, color .15s; }
        .cvh-tab:last-child { border-right: none; }
        .cvh-tab:hover { background: #f8fafc; }
        .cvh-tab-top { display: flex; align-items: center; gap: 5px; }
        .cvh-tab-icon { display: flex; }
        .cvh-tab-icon svg { width: 14px; height: 14px; }
        .cvh-tab-label { line-height: 1.1; font-size: 11px; font-weight: 500; }
        .cvh-tab-count { font-size: 13px; font-weight: 700; line-height: 1.1; color: inherit; }
        .cvh-tab.active .cvh-tab-label { font-weight: 700; }
        .cvh-tab.stage-total.active       { color: #2563eb; background: rgba(37,99,235,.10); }
        .cvh-tab.stage-inreview.active    { color: #bdbd26; background: rgba(189,189,38,.14); }
        .cvh-tab.stage-sourcing.active    { color: #7e8299; background: rgba(126,130,153,.14); }
        .cvh-tab.stage-screening.active   { color: #c000ff; background: rgba(192,0,255,.10); }
        .cvh-tab.stage-evaluation.active  { color: #2896ff; background: rgba(40,150,255,.12); }
        .cvh-tab.stage-negotiation.active { color: #ff4098; background: rgba(255,64,152,.12); }
        .cvh-tab.stage-offered.active     { color: #ffab00; background: rgba(255,171,0,.14); }
        .cvh-tab.stage-joined.active      { color: #06c732; background: rgba(6,199,50,.12); }
        .cvh-tab.stage-rejected.active    { color: #ff0202; background: rgba(255,2,2,.10); }
        @media (max-width: 768px) { .cvh-tabs { flex-wrap: wrap; } .cvh-tab { flex: 1 1 33%; border-right: none; border-bottom: 1px solid #f1f5f9; padding: 8px 4px; } .cvh-tab-label { font-size: 10.5px; } }

        /* Row Selection Action Bar */
        .cvh-selection-bar { display: none; align-items: center; gap: 14px; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; padding: 10px 16px; margin-bottom: 14px; font-family: 'Poppins', sans-serif; flex-wrap: wrap; }
        .cvh-selection-bar.show { display: flex; }
        .cvh-selection-count { font-size: 13px; font-weight: 600; color: #334155; white-space: nowrap; }
        .cvh-selection-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .cvh-selection-actions .btn { font-size: 12px; font-weight: 500; background: #fff !important; border: 1px solid #bfdbfe !important; border-radius: 8px !important; padding: 6px 14px !important; box-shadow: none !important; }
        .cvh-selection-actions .btn:hover { background: #eff6ff !important; border-color: #93c5fd !important; }
        .cvh-selection-close { width: 28px; height: 28px; border-radius: 50%; border: none; background: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cvh-selection-close:hover { background: #f1f5f9; color: #334155; }
        .cvh-legacy-hide { display: none !important; }

        /* Header typography */
        .cvh-page-title, .cvh-page-title a { font-size: 14px !important; font-weight: 700 !important; color: #0f172a !important; font-family: 'Poppins', sans-serif; }
        .cvh-page-sub { font-size: 11px; color: #64748b; margin: 2px 0 0; font-family: 'Poppins', sans-serif; }

        /* Enterprise Toolbar */
        .cvh-tb-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .cvh-tb-search-wrap,
        .cvh-tb-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .cvh-tb-search-wrap { flex: 1 1 auto; min-width: 0; }
        .cvh-tb-sort-wrap { flex-shrink: 0; width: 220px; position: relative; }
        .cvh-tb-search-icon,
        .cvh-tb-sort-icon { color: #99a1b7; flex-shrink: 0; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; }
        .cvh-tb-search-icon svg,
        .cvh-tb-sort-icon svg { flex-shrink: 0; display: block; }
        .cvh-tb-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        .cvh-tb-sort-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3f4254;
            text-align: left;
        }
        .cvh-tb-sort-btn:focus { outline: none; }
        .cvh-tb-sort-btn span { flex: 1 1 auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cvh-tb-sort-btn .chevron { flex-shrink: 0; color: #99a1b7; transition: transform .15s; }
        .cvh-tb-sort-btn.open .chevron { transform: rotate(180deg); }
        .cvh-tb-sort-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 100%;
            width: max-content;
            max-width: 280px;
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(16,24,40,.12), 0 4px 6px -2px rgba(16,24,40,.06);
            padding: 6px;
            z-index: 60;
            max-height: 320px;
            overflow-y: auto;
        }
        .cvh-tb-sort-item { padding: 9px 12px; font-size: 12.5px; font-weight: 500; color: #3f4254; border-radius: 8px; cursor: pointer; white-space: nowrap; }
        .cvh-tb-sort-item:hover { background: #f8f9fb; }
        .cvh-tb-sort-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .cvh-tb-filter-btn {
            height: 44px;
            border-radius: 10px !important;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            padding: 0 16px !important;
            flex-shrink: 0;
            background: #2563eb !important;
            border: 1px solid #2563eb !important;
            color: #fff !important;
        }
        .cvh-tb-filter-btn:hover { background: #1d4ed8 !important; }
        @media (max-width: 992px) { .cvh-tb-sort-wrap { width: 160px; } }
        @media (max-width: 768px) { .cvh-tb-search-wrap { flex-basis: 100%; } .cvh-tb-sort-wrap { width: 140px; } }

        /* Applied Filters Card */
        .cvh-af-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; padding: 16px 18px; margin-bottom: 16px; font-family: 'Poppins', sans-serif; }
        .cvh-af-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
        .cvh-af-title { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #181c32; }
        .cvh-af-count { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; min-width: 19px; height: 19px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }
        .cvh-af-clearall { font-size: 12px; font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }
        .cvh-af-clearall:hover { color: #b91c1c; }
        .cvh-af-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .cvh-af-chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
        .cvh-af-chip-remove { cursor: pointer; opacity: .55; display: inline-flex; align-items: center; margin-left: 2px; }
        .cvh-af-chip-remove:hover { opacity: 1; }
        .cvh-af-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .cvh-af-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .cvh-af-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .cvh-af-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .cvh-af-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .cvh-af-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .cvh-af-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
        .cvh-af-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }

        /* Filter Drawer */
        #kt_cvtracker_filter_drawer.cvh-flt-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: auto !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            z-index: 1200;
        }
        #cvh_flt_card.cvh-flt-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .cvh-flt-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 20px 20px 16px; border-bottom: 1px solid #eef0f4; flex-shrink: 0; }
        .cvh-flt-header-icon { width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-title { font-size: 17px; font-weight: 700; color: #181c32; margin: 0; }
        .cvh-flt-count-pill { background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; border-radius: 50%; min-width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }
        .cvh-flt-subtitle { font-size: 12px; color: #7e8299; margin: 2px 0 0; }
        .cvh-flt-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: #7e8299; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-close:hover { background: #f1f5f9; color: #334155; }
        #cvh_flt_card .cvh-flt-body { flex: 1 1 auto !important; min-height: 0 !important; overflow-y: auto !important; overflow-x: hidden !important; padding: 4px 20px 16px; }
        #cvh_flt_card .cvh-flt-header,
        #cvh_flt_card .cvh-flt-footer { flex-shrink: 0 !important; }
        .cvh-flt-section { padding: 16px 0; border-bottom: 1px solid #f1f2f6; }
        .cvh-flt-section-last { border-bottom: none; }
        .cvh-flt-section-head { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
        .cvh-flt-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-icon-blue { background: #eef2ff; color: #4f46e5; }
        .cvh-flt-icon-green { background: #ecfdf5; color: #059669; }
        .cvh-flt-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .cvh-flt-icon-amber { background: #fffbeb; color: #b45309; }
        .cvh-flt-icon-rose { background: #fff1f2; color: #be123c; }
        .cvh-flt-icon-pink { background: #fdf2f8; color: #be185d; }
        .cvh-flt-icon-sky { background: #f0f9ff; color: #0369a1; }
        .cvh-flt-section-title { font-size: 13.5px; font-weight: 600; color: #181c32; display: flex; align-items: center; gap: 6px; }
        .cvh-flt-section-desc { font-size: 11.5px; color: #99a1b7; margin-top: 1px; }
        .cvh-flt-badge-primary { font-size: 9.5px; font-weight: 600; color: #2563eb; background: #dbeafe; border-radius: 4px; padding: 1px 6px; }
        .cvh-flt-mini-label { font-size: 11px; font-weight: 500; color: #7e8299; margin-bottom: 4px; display: block; }
        .cvh-flt-pill-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .cvh-flt-pill-row .btn-check { position: absolute; opacity: 0; pointer-events: none; }
        .cvh-flt-pill { display: inline-flex; align-items: center; background: #fff; border: 1px solid #e4e6ef; border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 500; color: #3f4254; cursor: pointer; transition: border-color .15s, background-color .15s, color .15s; }
        .cvh-flt-pill-row .btn-check:checked + .cvh-flt-pill { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        .cvh-flt-footer { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 20px; border-top: 1px solid #eef0f4; background: #f8f9fb; flex-shrink: 0; flex-wrap: wrap; }
        .cvh-flt-footer-count { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .cvh-flt-footer-icon { width: 36px; height: 36px; border-radius: 8px; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cvh-flt-footer-number { font-size: 15px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .cvh-flt-footer-label { font-size: 10.5px; color: #7e8299; }
        .cvh-flt-footer-actions { display: flex; gap: 6px; }
        .cvh-flt-footer-actions .btn { font-size: 11.5px; padding: 6px 10px; display: inline-flex; align-items: center; gap: 5px; }

        /* Stage sliding drawer */
        #csd_drawer { z-index: 1000000 !important; }
        #csd_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
        #csd_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        #csd_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
        #csd_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
        #csd_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
        #csd_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
        #csd_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
        #csd_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
        #csd_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 8px; padding: 9px 20px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: filter .15s; }
        #csd_drawer .sd-btn-confirm:hover { filter: brightness(0.94); }
        #csd_drawer .sd-btn-confirm:disabled { background: #c2c9d6 !important; cursor: not-allowed; }
        #csd_drawer .sd-btn-confirm .indicator-progress { display: none; }
        #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e4e6ef; }
        #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
        #csd_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
        .fa-inr:before, .fa-rupee:before { content: "₹" !important; font-family: inherit !important; font-style: normal !important; font-weight: 600 !important; }

        /* Manage top gap: collapse Metronic's toolbar-fixed padding and content gap */
        .header-fixed.toolbar-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper {
            padding-top: 65px !important;
        }
        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper {
                padding-top: 55px !important;
            }
        }
        #kt_content,
        .content {
            padding-top: 4px !important;
        }
        .cvh-grid {
            margin-top: 4px !important;
            margin-bottom: 12px !important;
        }
    </style>
    <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">
                {{-- Hidden legacy triggers and state indicators --}}
                <span class="text-gray-600 d-none" id="total_record">0</span>
                <input type="hidden" class="form-control form-control-solid" id="kt_daterangepicker" />
                <a href="javascript:void(0);" class="mass_mail d-none cvh-legacy-hide" data-toggle="modal" data-target="#mass_mail"></a>
                <a href="javascript:void(0);" class="fwd_resume d-none cvh-legacy-hide" data-toggle="modal" data-target="#fwd_resume"></a>
                <a href="javascript:void(0);" class="application_copy d-none cvh-legacy-hide" data-toggle="modal" data-target="#application_copy"></a>
                <button type="button" class="exportRecord d-none cvh-legacy-hide"></button>

                {{-- 1. Hero KPI Stat Cards (ES-only, live dynamic binding) --}}
                @php
                    $cvHeroCards = [
                        ['key'=>'total', 'label'=>'Total Candidates', 'bg'=>'#dbeafe', 'ic'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['key'=>'today', 'label'=>'Added Today', 'bg'=>'#dcfce7', 'ic'=>'#16a34a', 'tone'=>'info', 'text'=>'Since 12:00 AM',
                         'icon'=>'M12 4.5v15m7.5-7.5h-15'],
                        ['key'=>'this_week', 'label'=>'Added This Week', 'bg'=>'#ede9fe', 'ic'=>'#7c3aed', 'tone'=>'info', 'text'=>'Mon &ndash; Today',
                         'icon'=>'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
                        ['key'=>'immediate', 'label'=>'Immediate Joiners', 'bg'=>'#d1fae5', 'ic'=>'#059669', 'tone'=>'up', 'text'=>'Notice = 0 Days',
                         'icon'=>'M13 2 3 14h7l-1 8 10-12h-7l1-8Z'],
                        ['key'=>'without_resume', 'label'=>'Without Resume', 'bg'=>'#fee2e2', 'ic'=>'#dc2626', 'tone'=>'warn', 'text'=>'Needs Attention',
                         'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l4.414 4.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z'],
                    ];
                @endphp
                <div class="cvh-grid">
                    @foreach($cvHeroCards as $card)
                    <div class="cvh-card">
                        <div class="cvh-icon" style="background:{{ $card['bg'] }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $card['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $card['icon'] }}"/></svg>
                        </div>
                        <div class="cvh-body">
                            <p class="cvh-label">{{ $card['label'] }}</p>
                            <p class="cvh-value" data-cvh="{{ $card['key'] }}">{{ number_format($candidateStats[$card['key']] ?? 0) }}</p>
                            @if($card['key'] === 'total')
                                <div class="cvh-delta up">&uarr; <span data-cvh="this_week_delta">{{ number_format($candidateStats['this_week'] ?? 0) }}</span> this week</div>
                            @else
                                <div class="cvh-delta {{ $card['tone'] }}">{!! $card['text'] !!}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- 2. Enterprise Search + Sort + Filter Toolbar Card --}}
                <div class="card cvh-tb-card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="cvh-tb-search-wrap" style="position: relative;">
                                <span class="cvh-tb-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control cvh-tb-search-input" name="inputJobSearch"
                                    value="" placeholder="Search by name, skills, role, experience, location..." id="kt_tagify_2" autocomplete="off" />
                                <div style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%);">
                                    <div id="loader_search" class="spinner-border spinner-border-sm text-primary d-none"></div>
                                </div>
                            </div>
                            <div class="cvh-tb-sort-wrap" id="cvh_sort_wrap">
                                <span class="cvh-tb-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="cvh-tb-sort-btn" id="cvh_sort_btn">
                                    <span id="cvh_sort_label">Sort</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                                    id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="Show All" selected="">Sort</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="7">High to Low (Experience)</option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="cvh-tb-sort-panel d-none" id="cvh_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_cvtracker_filter_toggle" class="btn cvh-tb-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter</span>
                                <span class="badge badge-circle badge-primary cvh-tb-count-badge d-none" id="cvh_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. Applied Filters Summary Card --}}
                <div class="cvh-af-card d-none" id="cvh_applied_card">
                    <div class="cvh-af-head">
                        <span class="cvh-af-title">
                            Active Filters
                            <span class="cvh-af-count" id="cvh_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="cvh-af-clearall" id="cvh_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="cvh-af-chips" id="cvh_applied_chips"></div>
                </div>

                {{-- 4. Stage Tabs Card (All Candidates / InReview / Sourcing / Screening / Evaluation / Negotiation / Offered / Joined / Rejected) --}}
                @php
                    $cvStageTabs = [
                        ['stage'=>'all', 'label'=>'All Candidates', 'countKey'=>'total', 'color'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['stage'=>'8', 'label'=>'InReview', 'countKey'=>'inreview', 'color'=>'#bdbd26',
                         'icon'=>'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                        ['stage'=>'1', 'label'=>'Sourcing', 'countKey'=>'sourcing', 'color'=>'#7e8299',
                         'icon'=>'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
                        ['stage'=>'2', 'label'=>'Screening', 'countKey'=>'screening', 'color'=>'#c000ff',
                         'icon'=>'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
                        ['stage'=>'3', 'label'=>'Evaluation', 'countKey'=>'evaluation', 'color'=>'#2896ff',
                         'icon'=>'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
                        ['stage'=>'4', 'label'=>'Negotiation', 'countKey'=>'negotiation', 'color'=>'#ff4098',
                         'icon'=>'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
                        ['stage'=>'5', 'label'=>'Offered', 'countKey'=>'offered', 'color'=>'#ffab00',
                         'icon'=>'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
                        ['stage'=>'6', 'label'=>'Joined', 'countKey'=>'joined', 'color'=>'#06c732',
                         'icon'=>'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4 4 0 0 0-7.533-2.348M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25-3 3-1.5-1.5'],
                        ['stage'=>'7,9', 'label'=>'Rejected', 'countKey'=>'rejected', 'color'=>'#ff0202',
                         'icon'=>'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ];
                @endphp
                <div class="card cvh-tabs-card" id="cvh_tabs_card">
                    <div class="card-body">
                        <div class="cvh-tabs">
                            @foreach($cvStageTabs as $i => $tab)
                                @php
                                    $tabCount = $tab['countKey'] === 'total'
                                        ? ($candidateStats['total'] ?? 0)
                                        : ($candidateStats['tabs'][$tab['countKey']] ?? 0);
                                @endphp
                                <button type="button" class="cvh-tab stage-{{ $tab['countKey'] }} {{ $i === 0 ? 'active' : '' }}" data-stage="{{ $tab['stage'] }}" data-color="{{ $tab['color'] }}">
                                    <span class="cvh-tab-top">
                                        <span class="cvh-tab-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $tab['icon'] }}"/></svg>
                                        </span>
                                        <span class="cvh-tab-label">{{ $tab['label'] }}</span>
                                    </span>
                                    <span class="cvh-tab-count" data-cvh-tab="{{ $tab['countKey'] }}">{{ number_format($tabCount) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 5. Row Selection Action Bar --}}
                <div class="cvh-selection-bar" id="cvh_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="cvh_selection_master_chk" checked title="Clear selection" />
                    </div>
                    <span class="cvh-selection-count"><span id="cvh_selected_count">0</span> candidates selected</span>
                    <div class="cvh-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" id="kt_mass_mail_drawer_toggle">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success fwd_resume" id="kt_fwd_resume_drawer_toggle">
                            <span class="svg-icon svg-icon-success svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                            </span> Forward Resume
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary application_copy" data-toggle="modal" data-target="#application_copy">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                            </span> Copy
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary exportRecord" title="Export the selected candidates">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            </span> Export
                        </a>
                    </div>
                    <button type="button" class="cvh-selection-close" id="cvh_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- 6. Candidate Results Table Card --}}
                <div class="card" id="cvh_table_card">
                    <div class="card-body pt-3 px-5 position-relative">
                        <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                            <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                        </div>
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="myTable">
                            <thead>
                                <tr class="fw-bolder text-muted">
                                    <th class="w-10px pe-0">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input datatable_chk chk_list" type="checkbox"
                                                data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th>Candidate Name</th>
                                    <th data-toggle="tooltip" title="Experience">Exp.</th>
                                    <th>Salary</th>
                                    <th data-toggle="tooltip" title="Notice Period">Notice</th>
                                    <th>Location &amp; Education</th>
                                    <th>Ownership / Source</th>
                                    <th>Jobs</th>
                                    <th>Status</th>
                                    <th>Stage</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold" id="candidatetList">
                            </tbody>
                        </table>
                        <div id="paginationcandidate"></div>
                        <div id="cvh_print_area"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Filter Drawer (Right-side sliding drawer) --}}
    <div id="kt_cvtracker_filter_drawer" class="bg-body cvh-flt-drawer" data-kt-drawer="true" data-kt-drawer-name="cvtracker-filter"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_cvtracker_filter_toggle" data-kt-drawer-close="#kt_cvtracker_filter_close">
        <div id="cvh_flt_card" class="card shadow-none rounded-0 w-100 h-100 cvh-flt-card">
            <div class="cvh-flt-header">
                <div class="d-flex align-items-center gap-3">
                    <span class="cvh-flt-header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                    </span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="cvh-flt-title">Filter Candidates</h3>
                            <span class="cvh-flt-count-pill" id="cvh_header_count">0</span>
                        </div>
                        <p class="cvh-flt-subtitle">Refine search and find platform talent</p>
                    </div>
                </div>
                <button type="button" class="cvh-flt-close" id="kt_cvtracker_filter_close" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="cvh-flt-body">
                <form id="createSchedule" class="ajax-form" method="post">
                    {{-- Skills --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Skills <span class="cvh-flt-badge-primary">Primary</span></div>
                                <div class="cvh-flt-section-desc">Add skills to find candidates — click ★ to make mandatory</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <input class="form-control form-control-solid" name="kt_tagify_users" value="{{ @$default_skill }}" id="kt_tagify_users" placeholder="Enter skills (e.g. PHP, Laravel, React)..." />
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Location</div>
                                <div class="cvh-flt-section-desc">Preferred cities or regions</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <input class="form-control form-control-solid" id="kt_tagify_location" placeholder="Search location (e.g. Bangalore, Mumbai, Remote)..." />
                        </div>
                    </div>

                    {{-- Education --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-purple">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Education</div>
                                <div class="cvh-flt-section-desc">Highest qualification</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <input type="hidden" id="kt_tagify_education" value="" />
                            <div class="cvh-flt-select-dd" id="cvh_edu_dd">
                                <button type="button" class="form-select form-select-solid text-start" id="cvh_edu_dd_btn">
                                    <span id="cvh_edu_dd_label">Any Education</span>
                                </button>
                                <div class="cvh-flt-select-dd-panel d-none p-3 border rounded bg-white shadow-sm" id="cvh_edu_dd_panel" style="max-height: 200px; overflow-y: auto;">
                                    <label class="d-flex align-items-center gap-2 mb-2 cursor-pointer">
                                        <input type="checkbox" class="cvh-flt-edu-check" value="" checked>
                                        <span>Any</span>
                                    </label>
                                    @if(!empty($qualificationTag))
                                        @foreach ($qualificationTag as $qval)
                                            <label class="d-flex align-items-center gap-2 mb-2 cursor-pointer">
                                                <input type="checkbox" class="cvh-flt-edu-check" value="{{ $qval }}">
                                                <span class="text-uppercase">{{ $qval }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Experience --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Experience (Years)</div>
                                <div class="cvh-flt-section-desc">Total work experience</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="cvh-flt-mini-label">Minimum Experience</label>
                                    <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0">Freshers</option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="cvh-flt-mini-label">Maximum Experience</label>
                                    <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0">Freshers</option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Salary --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-amber">
                                <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Salary (CTC)</div>
                                <div class="cvh-flt-section-desc">Salary range</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="cvh-flt-mini-label">Min Salary</label>
                                    <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="e.g. 500000" />
                                </div>
                                <div class="col-6">
                                    <label class="cvh-flt-mini-label">Max Salary</label>
                                    <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="e.g. 2000000" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notice Period --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-rose">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Notice Period / Availability</div>
                                <div class="cvh-flt-section-desc">Time to join</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <div class="cvh-flt-pill-row" data-radio-group="app_int_notice">
                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="1" id="app_int_notice1" checked>
                                <label class="cvh-flt-pill" for="app_int_notice1">Any</label>

                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="8" id="app_int_notice8">
                                <label class="cvh-flt-pill" for="app_int_notice8">Immediate</label>

                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="2" id="app_int_notice2">
                                <label class="cvh-flt-pill" for="app_int_notice2">0-15 Days</label>

                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="3" id="app_int_notice3">
                                <label class="cvh-flt-pill" for="app_int_notice3">1 Month</label>

                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="4" id="app_int_notice4">
                                <label class="cvh-flt-pill" for="app_int_notice4">2 Months</label>

                                <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="7" id="app_int_notice7">
                                <label class="cvh-flt-pill" for="app_int_notice7">Currently Serving</label>
                            </div>
                        </div>
                    </div>

                    {{-- Stage --}}
                    <div class="cvh-flt-section">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-pink">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Stages</div>
                                <div class="cvh-flt-section-desc">Current stage in the pipeline</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <div class="cvh-flt-pill-row" data-radio-group="app_int_status">
                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="0" id="app_int_status0" checked>
                                <label class="cvh-flt-pill" for="app_int_status0">All</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="8" id="app_int_status8">
                                <label class="cvh-flt-pill" for="app_int_status8">InReview</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="1" id="app_int_status1">
                                <label class="cvh-flt-pill" for="app_int_status1">Sourcing</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="2" id="app_int_status2">
                                <label class="cvh-flt-pill" for="app_int_status2">Screening</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="3" id="app_int_status3">
                                <label class="cvh-flt-pill" for="app_int_status3">Evaluation</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="5" id="app_int_status5">
                                <label class="cvh-flt-pill" for="app_int_status5">Offered</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="6" id="app_int_status6">
                                <label class="cvh-flt-pill" for="app_int_status6">Joined</label>

                                <input type="radio" class="btn-check app_int_status" name="app_int_status" value="7" id="app_int_status7">
                                <label class="cvh-flt-pill" for="app_int_status7">Rejected</label>
                            </div>
                        </div>
                    </div>

                    {{-- Activity --}}
                    <div class="cvh-flt-section cvh-flt-section-last">
                        <div class="cvh-flt-section-head">
                            <span class="cvh-flt-icon cvh-flt-icon-sky">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                            </span>
                            <div>
                                <div class="cvh-flt-section-title">Activity</div>
                                <div class="cvh-flt-section-desc">Last active time</div>
                            </div>
                        </div>
                        <div class="cvh-flt-section-body">
                            <div class="cvh-flt-pill-row" data-radio-group="app_int_activity">
                                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                                <label class="cvh-flt-pill" for="app_int_activity1">All</label>

                                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                <label class="cvh-flt-pill" for="app_int_activity2">30 Days</label>

                                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                <label class="cvh-flt-pill" for="app_int_activity3">60 Days</label>

                                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                <label class="cvh-flt-pill" for="app_int_activity4">90 Days</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="cvh-flt-footer">
                <div class="cvh-flt-footer-count">
                    <span class="cvh-flt-footer-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <div>
                        <div class="cvh-flt-footer-number" id="cvh_footer_number">0</div>
                        <div class="cvh-flt-footer-label">Candidates Match</div>
                    </div>
                </div>
                <div class="cvh-flt-footer-actions">
                    <button type="button" class="btn btn-sm btn-light" id="cvh_close_btn">Close</button>
                    <button type="button" class="btn btn-sm btn-light" id="cvh_reset_btn">Reset</button>
                    <button type="button" class="btn btn-sm btn-primary" id="cvh_apply_btn">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Sliding Candidate Stage Drawer (#csd_drawer) --}}
    <div id="csd_drawer_toggle" style="display:none;"></div>
    <div id="csd_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="candidate-stage"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#csd_drawer_toggle" data-kt-drawer-close="#csd_drawer_close">
        <div class="d-flex flex-column h-100 w-100">
            <div class="sd-topbar">
                <div class="sd-topbar-left">
                    <span class="sd-topbar-ico"><i class="fa fa-random"></i></span>
                    <div>
                        <div class="sd-topbar-title">Change Candidate Stage</div>
                        <div class="sd-topbar-sub" id="csd_drawer_subtitle">&nbsp;</div>
                    </div>
                </div>
                <div class="sd-topbar-right">
                    <button type="button" class="sd-btn-cancel" id="stage_sub_stage_close">Cancel</button>
                    <button type="button" class="sd-btn-confirm" id="custom_stage_submit">
                        <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right ms-1"></i></span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <span class="sd-divider"></span>
                    <span class="sd-close" id="csd_drawer_close"><i class="fa fa-times"></i></span>
                </div>
            </div>
            <div class="flex-grow-1 px-6 py-4" style="overflow-y:auto;">
                <div class="stage_body_content" id="stage_body_content"></div>
                <div class="client_stage_body_content" id="client_stage_body_content"></div>
            </div>
        </div>
    </div>

    {{-- Copy Application Drawer --}}
    <div id="kt_activitiesCopy_toggle" style="display:none;"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'80%'}"
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100 h-100">
            <div class="card-toolbar px-4 py-2 d-flex justify-content-end" id="kt_activities_header">
                <button type="button" id="submit-frm-btn" class="btn btn-primary btn-sm copy_current_selected_applicant d-none me-2">
                    <span class="indicator-label">Save to Jobs</span>
                    <span class="indicator-progress">Please wait...</span>
                </button>
                <button type="button" class="btn btn-sm btn-light kt_activitiesCopy_close">Close</button>
            </div>
            <div class="card-body position-relative px-4 py-2" id="application_copy_body_content"></div>
        </div>
    </div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script>
    // In-RAM Global State Store (0ms instant access, bounded size)
    window.SuperAdminCVTrackerState = {
        candidates: [],
        total: 0,
        page: 1,
        size: 10,
        activeStageTab: 'all',
        activeSort: '1',
        searchQuery: '',
        selectedIds: new Set(),
        abortCtrl: null,
        drawerCache: new Map()
    };

    // Client-side IndexedDB Caching (jero_superadmin_candidates_db)
    const IDB_NAME = 'jero_superadmin_candidates_db';
    const IDB_VER = 1;
    const IDB_TTL_MS = 10 * 60 * 1000; // 10 minutes

    function openCandidatesDB() {
        return new Promise((resolve, reject) => {
            if (!('indexedDB' in window)) return resolve(null);
            const req = indexedDB.open(IDB_NAME, IDB_VER);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains('candidate_pages')) {
                    db.createObjectStore('candidate_pages', { keyPath: 'cacheKey' });
                }
                if (!db.objectStoreNames.contains('candidate_drawers')) {
                    db.createObjectStore('candidate_drawers', { keyPath: 'id' });
                }
            };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => resolve(null);
        });
    }

    async function getCachedPage(cacheKey) {
        try {
            const db = await openCandidatesDB();
            if (!db) return null;
            return new Promise((resolve) => {
                const tx = db.transaction('candidate_pages', 'readonly');
                const req = tx.objectStore('candidate_pages').get(cacheKey);
                req.onsuccess = () => {
                    const rec = req.result;
                    if (rec && (Date.now() - rec.timestamp < IDB_TTL_MS)) {
                        resolve(rec.data);
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = () => resolve(null);
            });
        } catch(e) {
            return null;
        }
    }

    async function putCachedPage(cacheKey, data) {
        try {
            const db = await openCandidatesDB();
            if (!db) return;
            const tx = db.transaction('candidate_pages', 'readwrite');
            tx.objectStore('candidate_pages').put({
                cacheKey: cacheKey,
                data: data,
                timestamp: Date.now()
            });
        } catch(e) {}
    }

    // Helper: generate canonical idempotency key
    function generateIdempotencyKey() {
        return 'idemp_' + (window.crypto && crypto.randomUUID ? crypto.randomUUID() : (Date.now() + '_' + Math.random().toString(36).substring(2)));
    }

    // Initialize Tagify for Skills & Location
    let tagifySkills = null;
    let tagifyLocation = null;

    document.addEventListener('DOMContentLoaded', function() {
        const skillsEl = document.querySelector('#kt_tagify_users');
        if (skillsEl && typeof Tagify !== 'undefined') {
            tagifySkills = new Tagify(skillsEl, {
                templates: {
                    tag: function(tagData) {
                        tagData = tagData || {};
                        const val = tagData.value || '';
                        const isChecked = tagData.checked ? 'checked' : '';
                        const starGlyph = tagData.checked ? '★' : '☆';
                        const chipBg = tagData.checked ? '#dbeafe' : '#eef2ff';
                        const starColor = tagData.checked ? '#eab308' : '#94a3b8';
                        const textColor = tagData.checked ? '#1d4ed8' : '#3730a3';
                        return `
                            <tag title="${val}" contenteditable='false' spellcheck="false"
                                class="tagify__tag ${tagData.checked ? 'cvh-flt-mandatory-tag' : ''}"
                                style="display:inline-flex;align-items:center;gap:6px;background:${chipBg};border-radius:8px;padding:4px 8px;margin:3px;">
                                <span class="star ${isChecked}" style="cursor:pointer;font-size:14px;color:${starColor};">${starGlyph}</span>
                                <span class="tagify__tag-text" style="cursor:pointer;font-size:11.5px;color:${textColor};">${val}</span>
                                <x title='Remove' class="tagify__tag__removeBtn" role="button">&times;</x>
                            </tag>
                        `;
                    }
                },
                transformTag: function(tagData) {
                    tagData.checked = tagData.checked || false;
                    return tagData;
                }
            });

            // Toggle mandatory (star) on click
            document.addEventListener('click', function(e) {
                const target = e.target.closest('.star, .tagify__tag-text');
                if (target && target.closest('#kt_cvtracker_filter_drawer')) {
                    const tagElm = target.closest('.tagify__tag');
                    if (!tagElm) return;
                    const starEl = tagElm.querySelector('.star');
                    const textEl = tagElm.querySelector('.tagify__tag-text');
                    if (!starEl || !textEl) return;
                    const val = textEl.textContent.trim();
                    starEl.classList.toggle('checked');
                    const isChecked = starEl.classList.contains('checked');
                    starEl.textContent = isChecked ? '★' : '☆';
                    starEl.style.color = isChecked ? '#eab308' : '#94a3b8';
                    tagElm.style.background = isChecked ? '#dbeafe' : '#eef2ff';
                    textEl.style.color = isChecked ? '#1d4ed8' : '#3730a3';
                    const tagData = tagifySkills.value.find(t => t.value === val);
                    if (tagData) {
                        tagData.checked = isChecked;
                        tagifySkills.updateValueByDOMTags();
                        renderAppliedFilters();
                    }
                }
            });
        }

        const locEl = document.querySelector('#kt_tagify_location');
        if (locEl && typeof Tagify !== 'undefined') {
            tagifyLocation = new Tagify(locEl, {
                whitelist: [],
                maxTags: 8,
                dropdown: { enabled: 2, maxItems: 15, closeOnSelect: true }
            });
        }

        // Initialize sort panel options
        initSortControls();

        // Initial Data Fetch
        fetchCandidates(1);
    });

    // Custom Sort UI Controls
    function initSortControls() {
        const sortOptions = [
            { id: '1', label: 'Newest to Oldest (Upload Date)' },
            { id: '2', label: 'Oldest to Newest (Upload Date)' },
            { id: '21', label: 'Notice Period (Immediate)' },
            { id: '7', label: 'High to Low (Experience)' },
            { id: '8', label: 'Low to High (Experience)' }
        ];

        const panel = $('#cvh_sort_panel');
        panel.empty();
        sortOptions.forEach(opt => {
            const item = $(`<div class="cvh-tb-sort-item ${opt.id === '1' ? 'active' : ''}" data-sort="${opt.id}">${opt.label}</div>`);
            item.on('click', function() {
                $('.cvh-tb-sort-item').removeClass('active');
                $(this).addClass('active');
                $('#sortingfilter').val(opt.id);
                $('#cvh_sort_label').text(opt.label);
                panel.addClass('d-none');
                $('#cvh_sort_btn').removeClass('open');
                window.SuperAdminCVTrackerState.activeSort = opt.id;
                fetchCandidates(1);
            });
            panel.append(item);
        });

        $('#cvh_sort_btn').on('click', function(e) {
            e.stopPropagation();
            $(this).toggleClass('open');
            panel.toggleClass('d-none');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#cvh_sort_wrap').length) {
                panel.addClass('d-none');
                $('#cvh_sort_btn').removeClass('open');
            }
        });
    }

    // Education checklist interaction
    var cvhEducationSelected = [];
    $(document).on('change', '.cvh-flt-edu-check', function() {
        var val = $(this).val();
        if (val === '') {
            if ($(this).is(':checked')) {
                $('.cvh-flt-edu-check').not(this).prop('checked', false);
                cvhEducationSelected = [];
            }
        } else {
            $('.cvh-flt-edu-check[value=""]').prop('checked', false);
            if ($(this).is(':checked')) {
                if (cvhEducationSelected.indexOf(val) === -1) cvhEducationSelected.push(val);
            } else {
                cvhEducationSelected = cvhEducationSelected.filter(v => v !== val);
                if (cvhEducationSelected.length === 0) $('.cvh-flt-edu-check[value=""]').prop('checked', true);
            }
        }
        var payload = cvhEducationSelected.map(v => ({ value: v }));
        $('#kt_tagify_education').val(payload.length ? JSON.stringify(payload) : '');
        $('#cvh_edu_dd_label').text(cvhEducationSelected.length ? cvhEducationSelected.join(', ') : 'Any Education');
        renderAppliedFilters();
    });

    $(document).on('click', '#cvh_edu_dd_btn', function(e) {
        e.stopPropagation();
        $('#cvh_edu_dd_panel').toggleClass('d-none');
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#cvh_edu_dd').length) {
            $('#cvh_edu_dd_panel').addClass('d-none');
        }
    });

    // Stage Tabs interaction
    $(document).on('click', '.cvh-tab', function() {
        $('.cvh-tab').removeClass('active');
        $(this).addClass('active');
        window.SuperAdminCVTrackerState.activeStageTab = $(this).data('stage');
        fetchCandidates(1);
    });

    // Debounced search
    let searchTimeout = null;
    $('#kt_tagify_2').on('input', function() {
        clearTimeout(searchTimeout);
        $('#loader_search').removeClass('d-none');
        searchTimeout = setTimeout(() => {
            fetchCandidates(1);
        }, 300);
    });

    // Filter drawer Apply & Reset
    $('#cvh_apply_btn').on('click', function() {
        $('#kt_cvtracker_filter_close').trigger('click');
        renderAppliedFilters();
        fetchCandidates(1);
    });

    $('#cvh_reset_btn').on('click', function() {
        if (tagifySkills) tagifySkills.removeAllTags();
        if (tagifyLocation) tagifyLocation.removeAllTags();
        $('.cvh-flt-edu-check').prop('checked', false);
        $('.cvh-flt-edu-check[value=""]').prop('checked', true);
        cvhEducationSelected = [];
        $('#kt_tagify_education').val('');
        $('#cvh_edu_dd_label').text('Any Education');
        $('#min_exp').val('');
        $('#max_exp').val('');
        $('#min_sal').val('');
        $('#max_sal').val('');
        $('#app_int_notice1').prop('checked', true);
        $('#app_int_status0').prop('checked', true);
        $('#app_int_activity1').prop('checked', true);
        renderAppliedFilters();
        fetchCandidates(1);
    });

    $('#cvh_close_btn').on('click', function() {
        $('#kt_cvtracker_filter_close').trigger('click');
    });

    $('#cvh_applied_clear_all').on('click', function() {
        $('#cvh_reset_btn').trigger('click');
    });

    function renderAppliedFilters() {
        const chipsContainer = $('#cvh_applied_chips');
        chipsContainer.empty();
        let count = 0;

        // Skills
        if (tagifySkills && tagifySkills.value) {
            tagifySkills.value.forEach(s => {
                count++;
                const chip = $(`<span class="cvh-af-chip cat-skill">${s.checked ? '★ ' : ''}${s.value} <span class="cvh-af-chip-remove" data-action="remove-skill" data-val="${s.value}">&times;</span></span>`);
                chipsContainer.append(chip);
            });
        }

        // Locations
        if (tagifyLocation && tagifyLocation.value) {
            tagifyLocation.value.forEach(l => {
                count++;
                const chip = $(`<span class="cvh-af-chip cat-location">${l.value} <span class="cvh-af-chip-remove" data-action="remove-loc" data-val="${l.value}">&times;</span></span>`);
                chipsContainer.append(chip);
            });
        }

        // Education
        if (cvhEducationSelected.length) {
            cvhEducationSelected.forEach(e => {
                count++;
                const chip = $(`<span class="cvh-af-chip cat-education">${e} <span class="cvh-af-chip-remove" data-action="remove-edu" data-val="${e}">&times;</span></span>`);
                chipsContainer.append(chip);
            });
        }

        // Exp
        const minE = $('#min_exp').val();
        const maxE = $('#max_exp').val();
        if (minE || maxE) {
            count++;
            const label = `Exp: ${minE || '0'} - ${maxE || '30'} Y`;
            const chip = $(`<span class="cvh-af-chip cat-experience">${label} <span class="cvh-af-chip-remove" data-action="clear-exp">&times;</span></span>`);
            chipsContainer.append(chip);
        }

        // Notice
        const noticeVal = $('input[name="app_int_notice"]:checked').val();
        if (noticeVal && noticeVal !== '1') {
            count++;
            const label = $('label[for="app_int_notice' + noticeVal + '"]').text();
            const chip = $(`<span class="cvh-af-chip cat-notice">${label} <span class="cvh-af-chip-remove" data-action="clear-notice">&times;</span></span>`);
            chipsContainer.append(chip);
        }

        $('#cvh_applied_count').text(count);
        $('#cvh_header_count').text(count);
        if (count > 0) {
            $('#cvh_applied_card').removeClass('d-none');
            $('#cvh_count_badge').removeClass('d-none').text(count);
        } else {
            $('#cvh_applied_card').addClass('d-none');
            $('#cvh_count_badge').addClass('d-none');
        }
    }

    // Active chip removal click handler
    $(document).on('click', '.cvh-af-chip-remove', function() {
        const action = $(this).data('action');
        const val = $(this).data('val');
        if (action === 'remove-skill' && tagifySkills) {
            tagifySkills.removeTags(val);
        } else if (action === 'remove-loc' && tagifyLocation) {
            tagifyLocation.removeTags(val);
        } else if (action === 'remove-edu') {
            $(`.cvh-flt-edu-check[value="${val}"]`).prop('checked', false).trigger('change');
        } else if (action === 'clear-exp') {
            $('#min_exp').val('');
            $('#max_exp').val('');
        } else if (action === 'clear-notice') {
            $('#app_int_notice1').prop('checked', true);
        }
        renderAppliedFilters();
        fetchCandidates(1);
    });

    // Authoritative Fetch Candidates (Client-First / In-RAM + IndexedDB + ES Backend)
    async function fetchCandidates(page = 1) {
        window.SuperAdminCVTrackerState.page = page;
        const searchQuery = $('#kt_tagify_2').val() || '';
        const sort = $('#sortingfilter').val() || '1';
        const stageTab = window.SuperAdminCVTrackerState.activeStageTab;

        const skills = tagifySkills ? JSON.stringify(tagifySkills.value) : '';
        const locations = tagifyLocation ? JSON.stringify(tagifyLocation.value) : '';
        const education = $('#kt_tagify_education').val() || '';
        const minExp = $('#min_exp').val() || '';
        const maxExp = $('#max_exp').val() || '';
        const minSal = $('#min_sal').val() || '';
        const maxSal = $('#max_sal').val() || '';
        const appIntNotice = $('input[name="app_int_notice"]:checked').val() || '1';
        const appIntStatus = $('input[name="app_int_status"]:checked').val() || '0';
        const appIntActivity = $('input[name="app_int_activity"]:checked').val() || '1';

        const requestParams = {
            page: page,
            size: 10,
            sortingfilter: sort,
            cus_search: searchQuery,
            stage_tab: stageTab,
            kt_tagify_users: skills,
            kt_tagify_location: locations,
            kt_tagify_education: education,
            min_exp: minExp,
            max_exp: maxExp,
            min_sal: minSal,
            max_sal: maxSal,
            app_int_notice: appIntNotice,
            app_int_status: appIntStatus,
            app_int_activity: appIntActivity
        };

        const cacheKey = 'sa_cand_' + btoa(JSON.stringify(requestParams));

        // 1. Check IndexedDB cache for instant hydration (< 20ms)
        const cached = await getCachedPage(cacheKey);
        if (cached) {
            applyResponseData(cached);
        } else {
            $('.loadingMessage').css({ display: 'flex' });
        }

        // 2. Cancel in-flight request if user rapidly types/filters
        if (window.SuperAdminCVTrackerState.abortCtrl) {
            window.SuperAdminCVTrackerState.abortCtrl.abort();
        }
        window.SuperAdminCVTrackerState.abortCtrl = new AbortController();

        try {
            const resp = await $.ajax({
                url: "{{ route('superadmin.company.canndidateListAjax') }}",
                type: 'GET',
                data: requestParams,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            $('#loader_search').addClass('d-none');
            $('.loadingMessage').css({ display: 'none' });

            applyResponseData(resp);
            putCachedPage(cacheKey, resp);
        } catch(err) {
            $('#loader_search').addClass('d-none');
            $('.loadingMessage').css({ display: 'none' });
            if (err.statusText !== 'abort') {
                console.error('Candidate fetch error:', err);
            }
        }
    }

    function applyResponseData(response) {
        if (!response) return;
        $('#candidatetList').html(response.html);
        $('#total_record').html(response.total);
        $('#paginationcandidate').html(response.pagination);

        // Update live Hero KPI counts
        if (response.candidateStats) {
            $('[data-cvh="total"]').text(Number(response.candidateStats.total || 0).toLocaleString());
            $('[data-cvh="today"]').text(Number(response.candidateStats.today || 0).toLocaleString());
            $('[data-cvh="this_week"]').text(Number(response.candidateStats.this_week || 0).toLocaleString());
            $('[data-cvh="this_week_delta"]').text(Number(response.candidateStats.this_week || 0).toLocaleString());
            $('[data-cvh="immediate"]').text(Number(response.candidateStats.immediate || 0).toLocaleString());
            $('[data-cvh="without_resume"]').text(Number(response.candidateStats.without_resume || 0).toLocaleString());
            $('#cvh_footer_number').text(Number(response.candidateStats.total || 0).toLocaleString());

            // Update live Stage Tab badges
            if (response.candidateStats.tabs) {
                const tabs = response.candidateStats.tabs;
                $('[data-cvh-tab="total"]').text(Number(response.candidateStats.total || 0).toLocaleString());
                $('[data-cvh-tab="inreview"]').text(Number(tabs.inreview || 0).toLocaleString());
                $('[data-cvh-tab="sourcing"]').text(Number(tabs.sourcing || 0).toLocaleString());
                $('[data-cvh-tab="screening"]').text(Number(tabs.screening || 0).toLocaleString());
                $('[data-cvh-tab="evaluation"]').text(Number(tabs.evaluation || 0).toLocaleString());
                $('[data-cvh-tab="negotiation"]').text(Number(tabs.negotiation || 0).toLocaleString());
                $('[data-cvh-tab="offered"]').text(Number(tabs.offered || 0).toLocaleString());
                $('[data-cvh-tab="joined"]').text(Number(tabs.joined || 0).toLocaleString());
                $('[data-cvh-tab="rejected"]').text(Number(tabs.rejected || 0).toLocaleString());
            }
        }

        updateSelectionBar();
    }

    // Pagination link click handler
    $(document).on('click', '#paginationcandidate .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (!page || $(this).hasClass('disabled') || $(this).parent().hasClass('active')) return;
        fetchCandidates(page);
    });

    // Row selection and Floating Action Bar
    $(document).on('change', '.chk_list', function() {
        const isChecked = $(this).prop('checked');
        $('.chk_list_item').prop('checked', isChecked);
        updateSelectionBar();
    });

    $(document).on('change', '.chk_list_item', function() {
        updateSelectionBar();
    });

    $('#cvh_selection_master_chk, #cvh_selection_close').on('click', function() {
        $('.chk_list_item, .chk_list').prop('checked', false);
        updateSelectionBar();
    });

    function updateSelectionBar() {
        const checkedItems = $('.chk_list_item:checked');
        const count = checkedItems.length;
        $('#cvh_selected_count').text(count);
        if (count > 0) {
            $('#cvh_selection_bar').addClass('show');
            $('#cvh_selection_master_chk').prop('checked', true);
        } else {
            $('#cvh_selection_bar').removeClass('show');
            $('#cvh_selection_master_chk').prop('checked', false);
            $('.chk_list').prop('checked', false);
        }
    }

    // Forward Resume click handler
    $(document).on('click', '.fwd_resume', function(e) {
        e.preventDefault();
        const selectedIds = [];
        const selectedJobIds = [];
        $('.chk_list_item:checked').each(function() {
            selectedIds.push($(this).val());
            selectedJobIds.push($(this).attr('data-jobId'));
        });

        if (selectedIds.length === 0) {
            Swal.fire('No Selection', 'Please select at least one candidate.', 'warning');
            return;
        }
        if (selectedIds.length > 5) {
            Swal.fire('Limit Exceeded', 'You can forward a maximum of 5 candidates at a time.', 'warning');
            return;
        }

        $('#application_fwd_resume_body_content').html('<div class="py-10 text-center"><span class="spinner-border text-primary"></span> Loading...</div>');
        $('#fwd_resume').modal('show');

        const firstJobId = selectedJobIds[0] || 0;
        let url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}".replace(':id', firstJobId);

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': "{{ csrf_token() }}",
                'arr': selectedIds,
                'jobIdArr': selectedJobIds
            },
            success: function(resp) {
                if (resp.status === 'success') {
                    $('#application_fwd_resume_body_content').html(resp.view);
                }
            }
        });
    });

    // Mass Email click handler
    $(document).on('click', '.mass_mail', function(e) {
        e.preventDefault();
        const selectedIds = [];
        const selectedJobIds = [];
        $('.chk_list_item:checked').each(function() {
            selectedIds.push($(this).val());
            selectedJobIds.push($(this).attr('data-jobId'));
        });

        if (selectedIds.length === 0) {
            Swal.fire('No Selection', 'Please select at least one candidate.', 'warning');
            return;
        }

        $('#application_mass_mail_body_content').html('<div class="py-10 text-center"><span class="spinner-border text-primary"></span> Loading...</div>');
        $('#mass_mail').modal('show');

        const firstJobId = selectedJobIds[0] || 0;
        let url = "{{ route('service_company.cvtracker.mass_mail', ':id') }}".replace(':id', firstJobId);

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': "{{ csrf_token() }}",
                'arr': selectedIds,
                'jobIdArr': selectedJobIds
            },
            success: function(resp) {
                if (resp.status === 'success') {
                    $('#application_mass_mail_body_content').html(resp.view);
                }
            }
        });
    });

    // Copy Applicant click handler
    $(document).on('click', '.application_copy', function(e) {
        e.preventDefault();
        $('#application_copy_body_content').html('<div class="py-10 text-center"><span class="spinner-border text-primary"></span> Loading...</div>');
        $('#kt_activitiesCopy_toggle').trigger('click');

        let url = "{{ route('service_company.cvtracker.application_copy', ':id') }}".replace(':id', '1');
        $.ajax({
            type: 'GET',
            url: url,
            success: function(resp) {
                $('#application_copy_body_content').html(resp);
                $('#submit-frm-btn').removeClass('d-none');
            }
        });
    });

    // Export click handler
    $(document).on('click', '.exportRecord', function(e) {
        e.preventDefault();
        const selectedIds = [];
        const selectedJobIds = [];
        $('.chk_list_item:checked').each(function() {
            selectedIds.push($(this).val());
            selectedJobIds.push($(this).attr('data-jobId'));
        });

        if (selectedIds.length === 0) {
            Swal.fire('No Selection', 'Please select at least one candidate to export.', 'warning');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            type: 'GET',
            url: "{{ route('superadmin.company.candidatedata') }}",
            data: {
                '_token': "{{ csrf_token() }}",
                'arr': selectedIds,
                'jobIdArr': selectedJobIds
            },
            success: function(resp) {
                btn.prop('disabled', false);
                if (resp.file_url) {
                    window.location.href = resp.file_url;
                } else {
                    Swal.fire('Export Notice', resp.message || 'Export generated.', 'info');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                Swal.fire('Export Error', 'Unable to generate export file.', 'error');
            }
        });
    });

    // Stage Drawer Interaction (#csd_drawer)
    $(document).on('click', '.client_stage_sub_stage', function(e) {
        e.preventDefault();
        const wiseId = $(this).data('row-id');
        window.SuperAdminCVTrackerState.activeWiseId = wiseId;

        $('#stage_body_content').html('').hide();
        $('#client_stage_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading stage options...</div>').show();
        $('#csd_drawer_toggle').trigger('click');

        let url = "{{ route('service_company.job-applications.client_stage_body_content', ':id') }}".replace(':id', wiseId);
        $.ajax({
            type: 'GET',
            url: url,
            success: function(resp) {
                if (resp.status === 'success') {
                    $('#client_stage_body_content').html(resp.view);
                }
            }
        });
    });

    $('#stage_sub_stage_close, #csd_drawer_close').on('click', function() {
        $('#csd_drawer_close').trigger('click');
    });

    // Submit stage with Idempotency Key & Optimistic UI Update
    $(document).on('click', '#custom_stage_submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this);
        const form = $('#status_frm');
        const idempotencyKey = generateIdempotencyKey();

        submitBtn.prop('disabled', true);
        submitBtn.find('.indicator-label').hide();
        submitBtn.find('.indicator-progress').show();

        $.ajax({
            url: "{{ route('service_company.job-applications.stage_submit') }}",
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-Idempotency-Key': idempotencyKey,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(resp) {
                submitBtn.prop('disabled', false);
                submitBtn.find('.indicator-label').show();
                submitBtn.find('.indicator-progress').hide();

                if (resp.status === 'success') {
                    $('#csd_drawer_close').trigger('click');
                    Swal.fire('Success', 'Application stage updated successfully!', 'success');

                    // Patch the row locally with light green pulse
                    const wiseId = window.SuperAdminCVTrackerState.activeWiseId;
                    const row = $(`tr[data-wise-id="${wiseId}"]`);
                    if (row.length) {
                        row.css('transition', 'background-color 0.4s ease');
                        row.css('background-color', '#f0fdf4');
                        setTimeout(() => row.css('background-color', ''), 1200);
                    }

                    // Invalidate IndexedDB cache and re-fetch counts
                    openCandidatesDB().then(db => {
                        if (db) {
                            const tx = db.transaction('candidate_pages', 'readwrite');
                            tx.objectStore('candidate_pages').clear();
                        }
                    });
                    fetchCandidates(window.SuperAdminCVTrackerState.page);
                } else {
                    Swal.fire('Notice', resp.message || 'Unable to update stage.', 'warning');
                }
            },
            error: function(err) {
                submitBtn.prop('disabled', false);
                submitBtn.find('.indicator-label').show();
                submitBtn.find('.indicator-progress').hide();
                Swal.fire('Error', 'Stage submission failed. Please try again.', 'error');
            }
        });
    });
</script>
@endpush