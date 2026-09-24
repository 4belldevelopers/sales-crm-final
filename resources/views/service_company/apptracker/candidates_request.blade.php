@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" crossorigin="anonymous">
    <style>
        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        /* Search + Sort toolbar — same visual language as
           similar_candidates.blade.php's simf-toolbar-card (no Filter
           button/drawer here, this page has no skill/location/salary
           search). brf- prefix (Bench Request Filter) to keep this page's
           styles/JS distinct from the ibf-/abf-/simf-/plf- prefixed ones on
           other AppTracker tabs. */
        .brf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .brf-search-wrap,
        .brf-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .brf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .brf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .brf-search-icon,
        .brf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brf-search-icon svg,
        .brf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .brf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        .brf-sort-btn {
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
        .brf-sort-btn:focus { outline: none; }
        .brf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .brf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .brf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .brf-sort-panel {
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
        .brf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .brf-sort-item:hover {
            background: #f8f9fb;
        }
        .brf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        @media (max-width: 992px) {
            .brf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .brf-search-wrap { flex-basis: 100%; }
            .brf-sort-wrap { width: 140px; }
        }

        /* Filter button (new — toolbar previously had none) */
        .brf-filter-btn {
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
        .brf-filter-btn:hover {
            background: #1d4ed8 !important;
        }

        /* Bench Talent Requests filter drawer — mirrors In-house Bench's
           ibf-* drawer 1:1 (renamed brf-*), minus the Stages/Company
           sections this index has no fields for. */
        #kt_brf_filter_drawer.brf-drawer {
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
        #brf_card.brf-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .brf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid #eef0f4;
            flex-shrink: 0;
        }
        .brf-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brf-title {
            font-size: 17px;
            font-weight: 700;
            color: #181c32;
            margin: 0;
        }
        .brf-count-pill {
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .brf-subtitle {
            font-size: 12px;
            color: #7e8299;
            margin: 2px 0 0;
        }
        .brf-close {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #7e8299;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brf-close:hover {
            background: #f1f5f9;
            color: #334155;
        }
        #brf_card .brf-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 4px 20px 16px;
        }
        #brf_card .brf-header,
        #brf_card .brf-footer {
            flex-shrink: 0 !important;
        }
        .brf-section {
            padding: 16px 0;
            border-bottom: 1px solid #f1f2f6;
        }
        .brf-section-last {
            border-bottom: none;
        }
        .brf-section-head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }
        .brf-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brf-icon-blue { background: #eef2ff; color: #4f46e5; }
        .brf-icon-green { background: #ecfdf5; color: #059669; }
        .brf-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .brf-icon-amber { background: #fffbeb; color: #b45309; }
        .brf-icon-rose { background: #fff1f2; color: #be123c; }
        .brf-icon-slate { background: #f1f5f9; color: #475569; }
        .brf-icon-sky { background: #f0f9ff; color: #0369a1; }
        .brf-section-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #181c32;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .brf-section-desc {
            font-size: 11.5px;
            color: #99a1b7;
            margin-top: 1px;
        }
        .brf-badge-primary {
            font-size: 9.5px;
            font-weight: 600;
            color: #2563eb;
            background: #dbeafe;
            border-radius: 4px;
            padding: 1px 6px;
        }
        .brf-mini-label {
            font-size: 11px;
            font-weight: 500;
            color: #7e8299;
            margin-bottom: 4px;
            display: block;
        }
        .brf-salary-row {
            flex-wrap: nowrap;
        }
        .brf-salary-row > [class^="col-"] {
            padding-left: 4px;
            padding-right: 4px;
            min-width: 0;
        }
        .brf-salary-row .form-select,
        .brf-salary-row .form-control {
            font-size: 11px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .brf-salary-row .form-select {
            padding-right: 20px;
        }
        .brf-select-dd {
            position: relative;
        }
        .brf-select-dd-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 12.5px;
            color: #3f4254;
            text-align: left;
        }
        .brf-select-dd-btn span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 8px;
            text-transform: uppercase;
        }
        .brf-edu-name {
            text-transform: uppercase;
        }
        .brf-select-dd-btn svg {
            flex-shrink: 0;
            color: #99a1b7;
        }
        .brf-select-dd-panel {
            margin-top: 6px;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }
        .brf-select-dd-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            font-size: 12.5px;
            color: #3f4254;
            cursor: pointer;
            margin: 0;
            border-bottom: 1px solid #f5f6f8;
        }
        .brf-select-dd-item:last-child {
            border-bottom: none;
        }
        .brf-select-dd-item:hover {
            background: #f8f9fb;
        }
        .brf-select-dd-item input {
            accent-color: #2563eb;
        }
        .brf-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .brf-pill-row .btn-check {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .brf-pill {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #3f4254;
            cursor: pointer;
            transition: border-color .15s, background-color .15s, color .15s;
        }
        .brf-pill-row .btn-check:checked + .brf-pill {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .brf-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid #eef0f4;
            background: #f8f9fb;
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .brf-footer-count {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .brf-footer-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brf-footer-number {
            font-size: 15px;
            font-weight: 700;
            color: #181c32;
            line-height: 1.2;
        }
        .brf-footer-label {
            font-size: 10.5px;
            color: #7e8299;
        }
        .brf-footer-hint {
            cursor: help;
        }
        .brf-footer-actions {
            display: flex;
            gap: 6px;
        }
        .brf-footer-actions .btn {
            font-size: 11.5px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 576px) {
            .brf-footer { flex-direction: column; align-items: stretch; }
            .brf-footer-actions { justify-content: stretch; }
            .brf-footer-actions .btn { flex: 1 1 auto; justify-content: center; }
        }

        /* Active-filters bar inside the drawer body */
        .brf-activebar {
            padding: 12px 0;
            border-bottom: 1px solid #eef0f4;
        }
        .brf-activebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .brf-activebar-label {
            font-size: 12px;
            font-weight: 600;
            color: #181c32;
        }
        .brf-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .brf-clearall:hover {
            color: #b91c1c;
        }
        .brf-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .brf-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #3730a3;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
            padding: 5px 7px 5px 10px;
            font-size: 11.5px;
            font-weight: 500;
        }
        .brf-chip.mandatory {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }
        .brf-chip-remove {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,.06);
            cursor: pointer;
            flex-shrink: 0;
        }
        .brf-chip-remove:hover {
            background: rgba(0,0,0,.14);
        }

        /* Applied Filters summary card — lives on the main page above the
           results table, hidden until Apply Filters is clicked. */
        .brf-applied-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            background: #fff;
            padding: 16px 18px;
            margin-bottom: 16px;
            font-family: 'Poppins', sans-serif;
        }
        .brf-applied-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .brf-applied-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #181c32;
        }
        .brf-applied-count {
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .brf-applied-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .brf-applied-clearall:hover { color: #b91c1c; }
        .brf-applied-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .brf-applied-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .brf-applied-chip .ic { display: inline-flex; align-items: center; flex-shrink: 0; }
        .brf-applied-chip .star { font-size: 12px; line-height: 1; }
        .brf-applied-chip-remove {
            cursor: pointer;
            opacity: .55;
            display: inline-flex;
            align-items: center;
            margin-left: 2px;
        }
        .brf-applied-chip-remove:hover { opacity: 1; }
        .brf-applied-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .brf-applied-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .brf-applied-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .brf-applied-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .brf-applied-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .brf-applied-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .brf-applied-chip.cat-hiring     { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
        .brf-applied-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .brf-applied-more {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }
        .brf-applied-more:hover { background: #e5e7eb; }
        /* Location autocomplete dropdown is appended to <body> so it needs a
           z-index above the filter drawer (z-index:1200) and its backdrop. */
        body > .tagify__dropdown {
            z-index: 1400 !important;
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
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        @php
            use App\Helper\GeneralMethod;
            $req_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt(@$job->id) . '/all');
            $public_bench_path = url(
                '/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id),
            );
            $internal_bench_path = url(
                '/service_company/apptracker/internal_bench/' . GeneralMethod::value_encrypt(@$job->id),
            );
            $existing_path = url(
                '/service_company/apptracker/similar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
            );

            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );

            $candidates_request_path = route('service_company.apptracker.candidates_request');

             $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'agencyBench_active'=>'',
                    'agencyBench_url'=>$public_bench_path,
                    'agencyBench_id'=>'agencyBench_counter',
                    'benchTalentReq_active'=>'tabactive',
                    'benchTalentReq_url'=>$candidates_request_path,
                    'benchTalentReq_id'=>'benchTalentReq_counter',
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter,
                    'public_bench_counter' =>@$public_bench_counter,
                    'recomnded_counter' => @$recomnded_counter,
                    ];
        @endphp
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
        @include('sections.service_company.toolbar',['id' => $job->id])
        <!--end::Page title-->
        <form action="" id="filter-form" style="">
            @csrf
            <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
            <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
                @if (in_array('view_jobs', $userPermissions))
                    {{-- Opens the same right-side job-details drawer (#kt_activitiesJob_show,
                         defined once in the shared layout) that the job title/logo already
                         uses elsewhere — .show-detail-open's click handler in
                         service_company_layout.blade.php ajax-loads jobs.viewmore into it. --}}
                    <a href="javascript:void(0);" data-id="{{ $job->id }}"
                        class="btn btn-sm btn-flex btn-outline btn-outline-primary fw-bolder me-2 show-detail-open">
                        View Job
                    </a>
                @endif
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1 me-3 d-none">
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

                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1 me-3">

                            <!--end::Svg Icon-->
                            <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                                class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                                placeholder="Search..." />
                        </div>
                        <!--end::Search-->

                        <!--begin::Export-->
                        <button type="button"
                            class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none"
                            data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1"
                                        transform="rotate(90 12.75 4.25)" fill="black" />
                                    <path
                                        d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                        fill="black" />
                                    <path
                                        d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                        fill="#C4C4C4" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            Export</button>
                        <!--end::Export-->
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
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">


                <div id="" class="container-fluid d-flex flex-stack second_header">
                    @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])
                </div>

                <div class="card mb-4 brf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="brf-search-wrap" style="position: relative;">
                                <span class="brf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control brf-search-input" name="inputJobSearch"
                                    value="" placeholder="Search by candidate name, skills, education or location..." id="kt_tagify_2" autocomplete="off" />
                            </div>
                            <div class="brf-sort-wrap" id="brf_sort_wrap">
                                <span class="brf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="brf-sort-btn" id="brf_sort_btn">
                                    <span id="brf_sort_label">Best Match</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="Show All">Sort</option>
                                    <option value="10" selected="">Best Match</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice (Serving)</option>
                                    <option value="5">Low to High (Notice Period)</option>
                                    <option value="6">High to Low (Notice Period)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="brf-sort-panel d-none" id="brf_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_brf_filter_toggle"
                                class="btn brf-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="brf_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary brf-count-badge d-none" id="brf_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!--begin::Applied Filters summary card — hidden until Apply Filters is clicked-->
                <div class="brf-applied-card d-none" id="brf_applied_card">
                    <div class="brf-applied-head">
                        <span class="brf-applied-title">
                            Active Filters
                            <span class="brf-applied-count" id="brf_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="brf-applied-clearall" id="brf_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="brf-applied-chips" id="brf_applied_chips"></div>
                </div>
                <!--end::Applied Filters summary card-->

                <!--Start Filter Drawer-->
                <div id="kt_brf_filter_drawer" class="bg-body brf-drawer" data-kt-drawer="true" data-kt-drawer-name="bench-request-filter"
                    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_brf_filter_toggle" data-kt-drawer-close="#kt_brf_filter_close">
                    <div id="brf_card" class="card shadow-none rounded-0 w-100 h-100 brf-card">
                        <!--begin::Header-->
                        <div class="brf-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="brf-header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="brf-title">Filter Candidates</h3>
                                        <span class="brf-count-pill" id="brf_header_count">0</span>
                                    </div>
                                    <p class="brf-subtitle">Refine your search across bench talent requests</p>
                                </div>
                            </div>
                            <button type="button" class="brf-close" id="kt_brf_filter_close" title="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="brf-body">
                            <form id="brfFilterForm" class="ajax-form" method="post">

                                <!--begin::Active filters — inside scroll area so it flows with the sections -->
                                <div class="brf-activebar d-none" id="brf_active_bar">
                                    <div class="brf-activebar-head">
                                        <span class="brf-activebar-label">Active Filters <span id="brf_active_count">(0)</span></span>
                                        <a href="javascript:void(0);" class="brf-clearall" id="brf_clear_all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                            Clear All
                                        </a>
                                    </div>
                                    <div class="brf-chips" id="brf_active_chips"></div>
                                </div>
                                <!--end::Active filters-->

                                <div id="brf_sections_wrap">
                                <!--begin::Skills-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Skills <span class="brf-badge-primary">Primary</span></div>
                                            <div class="brf-section-desc">Add skills to find relevant candidates — click ★ to make a skill mandatory</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <input class="form-control form-control-solid d-flex align-items-center"
                                            name="kt_tagify_users" value="" placeholder=""
                                            id="kt_tagify_users" />
                                        <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
                                        <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
                                        <script>
                                            const brfSkillInputElm = document.querySelector('#kt_tagify_users');

                                            const tagify = new Tagify(brfSkillInputElm, {
                                                templates: {
                                                    tag: function(tagData) {
                                                        tagData = tagData || {};
                                                        const value = tagData.value || '';
                                                        const isChecked = tagData.checked ? 'checked' : '';
                                                        const mandatoryClass = tagData.checked ? 'brf-mandatory-tag' : '';
                                                        const starGlyph = tagData.checked ? '★' : '☆';
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
                                                }
                                            });

                                            // Toggle mandatory (star) on click of EITHER the star itself
                                            // OR the keyword text next to it.
                                            document.addEventListener('click', function(e) {
                                                const toggleTarget = e.target.closest('.star, .tagify__tag-text');
                                                if (toggleTarget) {
                                                    const tagElm = toggleTarget.closest('.tagify__tag');
                                                    if (!tagElm) return;
                                                    const starEl = tagElm.querySelector('.star');
                                                    const textEl = tagElm.querySelector('.tagify__tag-text');
                                                    if (!starEl || !textEl) return;
                                                    const value = textEl.textContent;

                                                    starEl.classList.toggle('checked');
                                                    const nowChecked = starEl.classList.contains('checked');

                                                    starEl.textContent = nowChecked ? '★' : '☆';
                                                    starEl.style.color = nowChecked ? '#eab308' : '#94a3b8';
                                                    tagElm.style.background = nowChecked ? '#dbeafe' : '#eef2ff';
                                                    tagElm.style.borderColor = nowChecked ? '#bfdbfe' : '#e0e7ff';
                                                    textEl.style.color = nowChecked ? '#1d4ed8' : '#3730a3';
                                                    tagElm.classList.toggle('brf-mandatory-tag', nowChecked);

                                                    const tagData = tagify.value.find(tag => tag.value === value);
                                                    if (tagData) {
                                                        setTimeout(() => {
                                                            tagData.checked = nowChecked;
                                                            tagify.updateValueByDOMTags();
                                                            brfUpdateActiveFiltersUI();
                                                        }, 500);
                                                    }
                                                }
                                            });

                                            function brfDebounceSkill(func, delay) {
                                                let timeoutskill;
                                                return function(...args) {
                                                    clearTimeout(timeoutskill);
                                                    timeoutskill = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            const brfFetchSuggestionsSkills = brfDebounceSkill(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            tagify.settings.whitelist = data;
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

                                            tagify.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 1) return;
                                                brfFetchSuggestionsSkills(query);
                                            });
                                            tagify.on('add', function() { brfUpdateActiveFiltersUI(); });
                                            tagify.on('remove', function() { brfUpdateActiveFiltersUI(); });
                                        </script>
                                    </div>
                                </div>
                                <!--end::Skills-->


                                <!--begin::Education-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Education</div>
                                            <div class="brf-section-desc">Highest qualification</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <input type="hidden" id="kt_tagify_education" value="" />
                                        @php
                                            // candidates_request() (the page-load controller method) does
                                            // not supply a qualification list — unlike internal_bench()'s
                                            // $qualificationTag — because this is a leaner index with no
                                            // job-scoped defaults. Fetched here (view-only, read-only master
                                            // data, no tenancy concerns) rather than in the controller, per
                                            // this task's "do not touch the controller" constraint.
                                            $brfQualificationTag = DB::table('qualification_master')
                                                ->select('*')
                                                ->whereNotIn('id', [60])
                                                ->orderBy('qualification_master.display_order', 'ASC')
                                                ->get()
                                                ->map(function ($row) {
                                                    return $row->name;
                                                });
                                        @endphp
                                        <div class="brf-select-dd" id="brf_edu_dd">
                                            <button type="button" class="brf-select-dd-btn" id="brf_edu_dd_btn">
                                                <span id="brf_edu_dd_label">Any Education</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div class="brf-select-dd-panel d-none" id="brf_edu_dd_panel">
                                                <label class="brf-select-dd-item">
                                                    <input type="checkbox" class="brf-edu-check" value="" checked>
                                                    <span>Any</span>
                                                </label>
                                                @foreach ($brfQualificationTag as $qval)
                                                    <label class="brf-select-dd-item">
                                                        <input type="checkbox" class="brf-edu-check" value="{{ $qval }}">
                                                        <span class="brf-edu-name">{{ $qval }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <script>
                                            // Drives the same #kt_tagify_education hidden field the backend
                                            // already reads (JSON array of {value:"..."} objects, matched
                                            // against qualification_master.name).
                                            var brfEducationSelected = [];

                                            function brfSyncEducationInput() {
                                                var payload = brfEducationSelected.map(function(v) { return { value: v }; });
                                                $('#kt_tagify_education').val(JSON.stringify(payload));
                                                $('#brf_edu_dd_label').text(brfEducationSelected.length ? brfEducationSelected.join(', ') : 'Any Education');
                                                brfUpdateActiveFiltersUI();
                                            }

                                            $(document).on('change', '.brf-edu-check', function() {
                                                var val = $(this).val();
                                                if (val === '') {
                                                    if ($(this).is(':checked')) {
                                                        $('.brf-edu-check').not(this).prop('checked', false);
                                                        brfEducationSelected = [];
                                                    }
                                                } else {
                                                    $('.brf-edu-check[value=""]').prop('checked', false);
                                                    if ($(this).is(':checked')) {
                                                        if (brfEducationSelected.indexOf(val) === -1) brfEducationSelected.push(val);
                                                    } else {
                                                        brfEducationSelected = brfEducationSelected.filter(function(v) { return v !== val; });
                                                        if (brfEducationSelected.length === 0) $('.brf-edu-check[value=""]').prop('checked', true);
                                                    }
                                                }
                                                brfSyncEducationInput();
                                            });

                                            $(document).on('click', '#brf_edu_dd_btn', function() {
                                                $('#brf_edu_dd_panel').toggleClass('d-none');
                                            });
                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#brf_edu_dd').length) {
                                                    $('#brf_edu_dd_panel').addClass('d-none');
                                                }
                                            });

                                            $(document).ready(brfSyncEducationInput);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Education-->

                                <!--begin::Experience-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Experience (Years)</div>
                                            <div class="brf-section-desc">Total work experience</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="brf-mini-label">Minimum Experience</label>
                                                <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                                    <option value="" selected="">Min Experience</option>
                                                    <option value="0">Freshers</option>
                                                    @include('service_company.apptracker._partials.experience_options')
                                                </select>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                            <div class="col-6">
                                                <label class="brf-mini-label">Maximum Experience</label>
                                                <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                                    <option value="" selected="">Max Experience</option>
                                                    <option value="0">Freshers</option>
                                                    @include('service_company.apptracker._partials.experience_options')
                                                </select>
                                                <div class="fv-plugins-message-container" id="experience_error" style="color:red !important;text-align:center;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Experience-->

                                <!--begin::Salary-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-amber">
                                            <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Salary (CTC)</div>
                                            <div class="brf-section-desc">Salary range with currency &amp; mode</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <div class="row g-2 brf-salary-row">
                                            <div class="col-3">
                                                <label class="brf-mini-label">Currency</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency">
                                                    <option value="" selected="">Select currency</option>
                                                    <option value="0">INR (₹)</option>
                                                    <option value="1">EUR</option>
                                                    <option value="2">JPY</option>
                                                    <option value="3">GBP</option>
                                                    <option value="4">USD</option>
                                                    <option value="5">AED</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="brf-mini-label">Mode</label>
                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                    <option value="" selected="">Select Mode</option>
                                                    <option value="0">Annual</option>
                                                    <option value="1">Monthly</option>
                                                    <option value="2">Hourly</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="brf-mini-label">Min Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="0">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted minimum_salary_word"></span>
                                            </div>
                                            <div class="col-3">
                                                <label class="brf-mini-label">Max Salary</label>
                                                <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Any" value="">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted maximum_salary_word"></span>
                                            </div>
                                            <span class="form-text text-muted" id="salary_error" style="color:red !important;text-align:center;"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Salary-->

                                <!--begin::Notice Period-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-rose">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Notice Period / Availability</div>
                                            <div class="brf-section-desc">Time to join</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <div class="brf-pill-row" data-radio-group="app_int_notice">
                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="1" id="app_int_notice1" checked>
                                            <label class="brf-pill" for="app_int_notice1">Any</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="8" id="app_int_notice8">
                                            <label class="brf-pill" for="app_int_notice8">Immediate</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="2" id="app_int_notice2">
                                            <label class="brf-pill" for="app_int_notice2">0-15 Days</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="3" id="app_int_notice3">
                                            <label class="brf-pill" for="app_int_notice3">1 Month</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="4" id="app_int_notice4">
                                            <label class="brf-pill" for="app_int_notice4">2 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="5" id="app_int_notice5">
                                            <label class="brf-pill" for="app_int_notice5">3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="6" id="app_int_notice6">
                                            <label class="brf-pill" for="app_int_notice6">More than 3 Months</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="9" id="app_int_notice9">
                                            <label class="brf-pill" for="app_int_notice9">Served Notice Period</label>

                                            <input type="radio" class="btn-check app_int_notice" name="app_int_notice" value="7" id="app_int_notice7">
                                            <label class="brf-pill" for="app_int_notice7">Currently Serving Notice</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Notice Period-->

                                <!--begin::Hiring Type-->
                                <div class="brf-section">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-slate">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Hiring Type</div>
                                            <div class="brf-section-desc">Fulltime or contract engagement</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <div class="brf-pill-row" data-radio-group="app_int_outsource">
                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource1" checked>
                                            <label class="brf-pill" for="app_int_outsource1">All</label>

                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource2">
                                            <label class="brf-pill" for="app_int_outsource2">Fulltime</label>

                                            <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="2" id="app_int_outsource3">
                                            <label class="brf-pill" for="app_int_outsource3">Contract</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Hiring Type-->

                                <!--begin::Activity-->
                                <div class="brf-section brf-section-last">
                                    <div class="brf-section-head">
                                        <span class="brf-icon brf-icon-sky">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
                                        </span>
                                        <div>
                                            <div class="brf-section-title">Activity</div>
                                            <div class="brf-section-desc">Last active on JeroATS</div>
                                        </div>
                                    </div>
                                    <div class="brf-section-body">
                                        <div class="brf-pill-row" data-radio-group="app_int_activity">
                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                                            <label class="brf-pill" for="app_int_activity1">Any</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                            <label class="brf-pill" for="app_int_activity2">30 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                            <label class="brf-pill" for="app_int_activity3">60 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                            <label class="brf-pill" for="app_int_activity4">90 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                            <label class="brf-pill" for="app_int_activity5">180 Days</label>

                                            <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                            <label class="brf-pill" for="app_int_activity6">1 Year</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Activity-->
                                </div>

                            </form>
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="brf-footer">
                            <div class="brf-footer-count">
                                <span class="brf-footer-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </span>
                                <div>
                                    <div class="brf-footer-number" id="brf_footer_number">0</div>
                                    <div class="brf-footer-label">Candidates Match <span class="brf-footer-hint" title="Updates after Apply">ⓘ</span></div>
                                </div>
                            </div>
                            <div class="brf-footer-actions">
                                <button type="button" class="btn btn-sm btn-light" id="brf_close_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    Close
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="brf_reset_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                    Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="brf_apply_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </div>
                </div>
                <!--End Filter Drawer-->

                <!-- Loader outside card so it shows even before card is revealed -->
                <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                    <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                </div>

                <!--begin::Card-->
                <div class="card" id="sc_table_card" style="display:none;">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                            id="candidates_request">
                            <thead>
                                <tr class="fw-bolder text-muted" role="row">
                                    <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1"
                                        style="width: 30px;">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#candidates_request .form-check-input"
                                                value="1">
                                        </div>
                                    </th>
                                    <th class="">Candidate Name</th>
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Notice</th>
                                    <th class="">Location</th>
                                    {{-- <th class="">Type</th> --}}
                                    <th class="">Last Active</th>
                                    <th class="">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold" id="candidatetList">
                            </tbody>
                        </table>
                        <div id="paginationcandidate"></div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        // #kt_tagify_2 is a plain free-text field (candidate name/skills/
        // education/location search box), sent as 'search' — it is
        // deliberately NOT Tagify-wrapped and NOT the same field as the
        // Skills Tagify (#kt_tagify_users) added inside the filter drawer,
        // which is sent as cus_search.
    </script>

    <script>
        // The candidate stays visible in THIS list after a successful add —
        // swapping the button to the same "Added" (btn-success) label the
        // row partial already renders server-side for ac_status==1 (see
        // candidates_request_ajax.blade.php) — rather than the previous
        // location.reload(), which made the row (and every other row's
        // scroll position/filter state) vanish instantly. The row only
        // actually disappears once the list is genuinely re-fetched
        // (pagination, filter, sort, or a real page reload).
        function benchReqMarkCandidateRowAdded($btn) {
            $btn.replaceWith('<a href="javascript:void(0);" class="btn btn-sm btn-success">Added</a>');
        }

        $(document).ready(function() {
            $(document).on("click", ".add_to_list", function(e) {
                e.preventDefault();

                var $btn = $(this);

                // 🔒 Prevent double click
                if ($btn.hasClass('processing')) {
                    return false;
                }

                var jobId = $btn.attr('data-row-id');
                var recomndedId = $btn.attr('data-recomnded-id');
                var condidateId = $btn.attr('data-condidate-id');

                // No confirmation dialog — a single click starts the add
                // immediately; the spinner below is the only "processing"
                // feedback needed, and the result comes back as a toast.
                var originalHtml = $btn.html();
                $btn.addClass('processing')
                    .prop('disabled', true)
                    .css('pointer-events', 'none')
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');

                function restoreButton() {
                    $btn.removeClass('processing')
                        .prop('disabled', false)
                        .css('pointer-events', '')
                        .html(originalHtml);
                }

                var url = "{{ route('service_company.jobs.add_to_list', ':id') }}";
                url = url.replace(':id', jobId) + "?recomndedId=" + recomndedId +
                    "&condidateId=" + condidateId;

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        benchReqMarkCandidateRowAdded($btn);
                        benchReqToast.fire({ icon: 'success', title: 'Added successfully to job' });

                        // Dynamically update KPI header counters on page
                        var $poolCounter = $('#pool_counter');
                        if ($poolCounter.length) {
                            var currentPool = parseInt($poolCounter.text()) || 0;
                            $poolCounter.text(currentPool + 1);
                        }
                        var $reqCounter = $('#benchTalentReq_counter');
                        if ($reqCounter.length) {
                            var currentReq = parseInt($reqCounter.text()) || 0;
                            if (currentReq > 0) {
                                $reqCounter.text(currentReq - 1);
                            }
                        }
                    },
                    error: function() {
                        restoreButton();
                        benchReqToast.fire({ icon: 'error', title: 'Something went wrong — please try again' });
                    }
                });
            });

        });
    </script>

    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');
        }, 500);

        function filtersdataget() {
            // tableLoad('load');
            fetchCandidates();
        }

        function tableLoad(type) {
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            table = $('#candidates_request').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',
                ajax: '{!! route('service_company.apptracker.candidates_request_data') !!}?jobid=' + jobid + '&mstatus_id=' + mstatus_id + '&search=' +
                    inputSearch + '&sortingfilter=' + sortingfilter + '&country=' + country + '&cus_search=' +
                    cus_search,
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });


                    var url =
                        '{{ route('service_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
                    url = url.replace(':jobid', jobid);
                    url = url.replace(':mstatus_id', mstatus_id);
                    url = url.replace(':inputSearch', inputSearch);

                    $.ajax({
                        type: 'GET',
                        url: url,
                        success: function(response) {

                            var allCandidate = 0;
                            var Sourcing = 0;
                            var Screening = 0;
                            var Rejected = 0;
                            var Offered = 0;

                            var Negotiation = 0;
                            var Joined = 0;
                            var Interview = 0;

                            if (response.allCounter > 0) {
                                allCandidate = response.data.allCandidate;
                                Sourcing = response.data.Sourcing;
                                Screening = response.data.Screening;
                                Rejected = response.data.Rejected;
                                Offered = response.data.Offered;

                                Negotiation = response.data.Negotiation;
                                Joined = response.data.Joined;
                                Interview = response.data.Interview;
                            }

                            setTimeout(() => {
                                $('#bg_1').html(Sourcing);
                                $('#bg_2').html(Screening);
                                $('#bg_3').html(Interview);
                                $('#bg_4').html(Negotiation);
                                $('#bg_5').html(Offered);

                                $('#bg_6').html(Joined);
                                $('#bg_7').html(Rejected);
                                $('#totalcnt').html(allCandidate);

                            }, 100);



                        }
                    });





                },

                "drawCallback": function(settings) {
                    alert("call" + settings.json);
                },


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
                        width: '15%'
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
                        data: 'NoticePeriod',
                        name: 'NoticePeriod',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Qualifaction',
                        name: 'Qualifaction',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        orderable: false
                    },
                    // {
                    //     data: 'Contacts',
                    //     name: 'Contacts',
                    //     searchable: false,
                    //     orderable: false,
                    //     width: '15%'
                    // },
                    {
                        data: 'Source',
                        name: 'Source',
                        orderable: false
                    },
                    {
                        data: 'Action',
                        name: 'Action',
                        orderable: false
                    }
                ]

            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $(document).on("click", ".stage_sub_stage", function() {

            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html('');
                        $('#stage_body_content').html(response.view);
                    }
                }
            });

        });

        $(document).on("click", ".master_status_div", function(e) {

            e.preventDefault();



            var id = $(this).data('master-status-id');
            $("#master_status_" + id).attr('checked', true);
            $('#main_substage').show();
            $('#comment_div').hide();

            $("#inreview").attr('checked', false);
            $("#sourcing").attr('checked', false);
            $("#screening").attr('checked', false);
            $("#evaluation").attr('checked', false);
            $("#negotation").attr('checked', false);
            $("#offer").attr('checked', false);
            $("#joined").attr('checked', false);
            $("#rejected").attr('checked', false);

            if (id == 1) {
                $("#sourcing").attr('checked', true);
                $('lbl_master_status_' + id).addClass('sourcing-bg');
            } else if (id == 2) {
                $("#screening").attr('checked', true);
                $('lbl_master_status_' + id).addClass('screening-bg');
            } else if (id == 3) {
                $("#evaluation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('interview');
            } else if (id == 4) {
                $("#negotation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-negotation');
            } else if (id == 5) {
                $("#offer").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-offer');
            } else if (id == 6) {
                $("#joined").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-joined');
            } else if (id == 7) {
                $("#rejected").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-rejected');
                $('#main_substage').hide();
                $('#comment_div').show();
            } else if (id == 8) {
                $("#inreview").attr('checked', true);
                $('lbl_master_status_' + id).addClass('bg-light-warning text-warning');
                $('#main_substage').hide();
            }

            if (id > 0 && (id != 8)) {

                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('service_company.job-applications.child_stage', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#substage').html('<img src="' + baseurl + '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#substage').html('');
                            $('#substage').html(response.html);
                        }
                    }
                });

            }

            e.run();

        });

        // Non-blocking toast for simple pass/fail notifications, same pattern
        // as similar_candidates.blade.php's simfToast. The stage-update
        // success below stays a blocking Swal.fire() since it gates a
        // chained .then() (closing the stage modal).
        const benchReqToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2600,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Custom Sort dropdown, same pattern as similar_candidates.blade.php's
        // simfRenderSortPanel — reads options straight from the real
        // #sortingfilter <select>, keeps that select's value in sync so
        // filtersdataget()/fetchCandidates() are unaffected, only replaces
        // how the option list is PRESENTED.
        function brfRenderSortPanel() {
            var $panel = $('#brf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="brf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#brf_sort_label').text(label);
                        brfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function brfOpenSortPanel() {
            brfRenderSortPanel();
            $('#brf_sort_panel').removeClass('d-none');
            $('#brf_sort_btn').addClass('open');
        }
        function brfCloseSortPanel() {
            $('#brf_sort_panel').addClass('d-none');
            $('#brf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#brf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#brf_sort_panel').hasClass('d-none')) brfOpenSortPanel();
            else brfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#brf_sort_wrap').length) brfCloseSortPanel();
        });

        $(document).ready(function() {
            $(document).on("click", "#custom_stage_submit", function(e) {
                e.preventDefault();

                var master_status = $('input[name="master_status"]:checked').val();
                if (master_status == 8) {
                    benchReqToast.fire({ icon: 'info', title: 'Stage can not be updated, please select other option!' });
                } else {
                    $.ajax({
                        url: '{{ route('service_company.job-applications.stage_submit') }}',
                        container: '#status_frm',
                        type: "POST",
                        data: $('#status_frm').serialize(),
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire("Success", 'Application stage update successfully !',
                                    "success").then(function() {
                                    // $('#stage_sub_stage').modal('hide');
                                    $("#stage_sub_stage_close").click();
                                });
                                // var table;
                                // tableLoad('load');
                                var page = $('#paginationcandidate .active_disabled').data('page');
                                fetchCandidates(page);
                                // table._fnDraw();
                                load_interviewSchedule();
                            }
                        }
                    })
                }


            });
            e.run();
        })

        $(document).ready(function() {
            $(document).on("click", "#addServiceCandidate", function() {

                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'event': 'CVACCESS'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            var url =
                                "{{ url('service_company/job-applications/create/' . @$job->id . '/0') }}";
                            window.location = url;
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });

            });

        });



        $(document).on("click", ".chk_list", function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').attr('checked', 'checked');
            } else {
                $('.chk_list_item').removeAttr('checked');
            }
        });
        $(document).on("click", ".chk_list", function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.custom_hide').addClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });
        $(document).on("click", ".chk_list_item", function() {
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.custom_hide').addClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
        });
    </script>

    <script>
        $(document).on("click", ".application_copy", function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
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
                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                }
            });
        });


        $(document).ready(function() {
            // $.noConflict();
            
            setTimeout(() => {
                fetchCandidates();
            }, 200);
            
            
            var aptkPaginationLoading = false;
            $(document).on('click', '#paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
                e.preventDefault();

                if (aptkPaginationLoading) return;
                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                aptkPaginationLoading = true;
                fetchCandidates(page, $('#search').val()).then(() => {
                    aptkPaginationLoading = false;
                }).catch(() => {
                    aptkPaginationLoading = false;
                });
            });

            $(document).on('change', '#paginationcandidate .pagi-perpage', function() {
                var selectedPerPage = $(this).val();
                localStorage.setItem('candidatePerPage', selectedPerPage);
                fetchCandidates(1);
            });


        });

        function fetchCandidates(page = 1, search = '') {
            var perPage = $('#paginationcandidate .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            // #kt_tagify_2 is the toolbar's genuine free-text search box
            // (candidate name/skills/education/location) — previously sent
            // as cus_search, which was backwards; #inputSearch is a hidden,
            // never-set field and is no longer read at all.
            var inputSearch = $('#kt_tagify_2').val();
            var sortingfilter = $('#sortingfilter').val();
            // New Skills Tagify field — JSON array of {value, checked},
            // matching what candidates_request_data_ajax() decodes as
            // cus_search via json_decode(..., true).
            var cus_search = $('#kt_tagify_users').val();
            var min_exp = $('#min_exp').val();
            var max_exp = $('#max_exp').val();
            var min_sal = $('#min_sal').val();
            var max_sal = $('#max_sal').val();
            var min_salary_currency = $('#min_salary_currency').val();
            var sal_mode = $('#sal_mode').val();
            var kt_tagify_education = $('#kt_tagify_education').val();
            var app_int_activity = $('[name="app_int_activity"]:checked').val();
            var app_int_notice = $('[name="app_int_notice"]:checked').val();
            var app_int_outsource = $('[name="app_int_outsource"]:checked').val();

            $('.loadingMessage').css({ display: 'none' });
            $('.page-loader').css('display', 'flex');
            return $.ajax({
                url: `{!! route('service_company.apptracker.candidates_request_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                jobid:jobid,
                mstatus_id:mstatus_id,
                search:inputSearch,
                sortingfilter:sortingfilter,
                cus_search:(cus_search),
                min_exp:min_exp,
                max_exp:max_exp,
                min_sal:min_sal,
                max_sal:max_sal,
                min_salary_currency:min_salary_currency,
                sal_mode:sal_mode,
                kt_tagify_education:kt_tagify_education,
                app_int_activity:app_int_activity,
                app_int_notice:app_int_notice,
                app_int_outsource:app_int_outsource,
                size:perPage
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    $('#candidatetList').html(response.html);
                    $('.benchTalentReq_counter').html(response.total);
                    $('#brf_footer_number').text(response.total);
                    $('#paginationcandidate').html(response.pagination);
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    console.error('candidates_request_data_ajax failed:', xhr.status, xhr.responseText);
                }
            });
        }

        // Free-text search box: Enter re-runs the same fetchCandidates() call
        // the sort dropdown and pagination already use.
        $(document).on('keydown', '#kt_tagify_2', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                fetchCandidates();
            }
        });
    </script>

    <script>
        /* ------------------------------------------------------------------
           Bench Talent Requests filter drawer glue — mirrors In-house
           Bench's ibf* drawer script 1:1 (renamed brf*), minus the Stages
           and Company Tagify pieces this index has no fields for.
        ------------------------------------------------------------------ */

        function brfSetDrawerWidth() {
            var wrapper = document.getElementById('kt_wrapper');
            var contentWidth = wrapper ? wrapper.getBoundingClientRect().width : window.innerWidth;
            if (!contentWidth) contentWidth = window.innerWidth;
            var drawerWidth = Math.round(contentWidth * 0.8);
            var drawerEl = document.getElementById('kt_brf_filter_drawer');
            if (drawerEl) {
                drawerEl.style.width = drawerWidth + 'px';
            }
        }
        $(document).ready(brfSetDrawerWidth);
        $(window).on('resize', brfSetDrawerWidth);
        $(document).on('click', '#kt_brf_filter_toggle', brfSetDrawerWidth);

        function brfEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_brf_filter_drawer');
            var cardEl = document.getElementById('brf_card');
            var bodyEl = cardEl ? cardEl.querySelector('.brf-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.brf-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.brf-footer') : null;
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
        $(document).ready(brfEnforceDrawerLayout);
        $(window).on('resize', brfEnforceDrawerLayout);
        $(document).on('click', '#kt_brf_filter_toggle, #kt_brf_filter_close', brfEnforceDrawerLayout);

        // Salary-in-words hints (min_sal/max_sal) — same helper set as
        // In-house Bench's drawer.
        var brfOnes = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var brfTeens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        var brfTens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        function brfCapitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function brfConvertToWords(num) {
            if (isNaN(num)) return '';
            if (num < 10) {
                return brfOnes[num];
            } else if (num < 20) {
                return brfTeens[num - 10];
            } else if (num < 100) {
                return brfTens[Math.floor(num / 10)] + ' ' + brfOnes[num % 10];
            } else if (num < 1000) {
                return brfOnes[Math.floor(num / 100)] + ' hundred ' + brfConvertToWords(num % 100);
            } else if (num < 100000) {
                return brfConvertToWords(Math.floor(num / 1000)) + ' thousand ' + brfConvertToWords(num % 1000);
            } else if (num < 10000000) {
                return brfConvertToWords(Math.floor(num / 100000)) + ' lakh ' + brfConvertToWords(num % 100000);
            } else if (num < 1000000000) {
                return brfConvertToWords(Math.floor(num / 10000000)) + ' crore ' + brfConvertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        function brfSalaryCheck() {
            var minimum_salary = parseInt($("#min_sal").val());
            var maximum_salary = parseInt($("#max_sal").val());
            if (!isNaN(minimum_salary) && !isNaN(maximum_salary)) {
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

        function brfExperienceCheck() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (!isNaN(min_exp) && !isNaN(max_exp)) {
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

        $(document).ready(function() {
            $(document).on('change', '#min_sal, #max_sal', brfSalaryCheck);
            $(document).on('change', '#min_exp', brfExperienceCheck);
            $(document).on('change', '#max_exp', brfExperienceCheck);
            $(document).on('keyup', '#min_sal', function() {
                var number = parseInt($('#min_sal').val());
                $('.minimum_salary_word').text(isNaN(number) ? '' : brfCapitalizeFirstLetter(brfConvertToWords(number)));
            });
            $(document).on('keyup', '#max_sal', function() {
                var number = parseInt($('#max_sal').val());
                $('.maximum_salary_word').text(isNaN(number) ? '' : brfCapitalizeFirstLetter(brfConvertToWords(number)));
            });
        });

        function brfCategoryIcon(cat) {
            var paths = {
                skill: '<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>',
                location: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                education: '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/>',
                experience: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                notice: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
                hiring: '<line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/>',
                activity: '<path d="M3 3v18h18M7 14l4-4 3 3 5-6"/>'
            };
            if (cat === 'salary') {
                return '<span style="font-size:11px;font-weight:700;line-height:1;">₹</span>';
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' + (paths[cat] || '') + '</svg>';
        }

        function brfUpdateActiveFiltersUI() {
            var chips = [];

            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }

            if (typeof brfEducationSelected !== 'undefined') {
                brfEducationSelected.slice().forEach(function(val) {
                    chips.push({
                        label: val.toUpperCase(),
                        clear: function() {
                            $('.brf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }

            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp !== '' || maxExp !== '') {
                chips.push({
                    label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }

            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var minSalChanged = minSal !== '' && minSal !== '0';
            var maxSalChanged = maxSal !== '';
            if (minSalChanged || maxSalChanged) {
                chips.push({
                    label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val('0'); $('#max_sal').val(''); }
                });
            }

            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                chips.push({
                    label: noticeEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var outsourceEl = $('[name="app_int_outsource"]:checked');
            if (outsourceEl.val() && outsourceEl.val() !== '0') {
                chips.push({
                    label: outsourceEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_outsource1').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                chips.push({
                    label: activityEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }

            var $chips = $('#brf_active_chips').empty();
            chips.forEach(function(c) {
                var $chip = $('<span class="brf-chip"></span>')
                    .toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').text('★ ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip
                    .append($('<span class="brf-chip-remove">&times;</span>').on('click', function() {
                        c.clear();
                        brfUpdateActiveFiltersUI();
                    }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#brf_active_bar').toggleClass('d-none', n === 0);
            $('#brf_active_count').text('(' + n + ')');
            $('#brf_header_count').text(n);
            $('#brf_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#brf_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        var BRF_APPLIED_VISIBLE_CAP = 8;
        var brfAppliedExpanded = false;

        function brfBuildAppliedItems() {
            var items = [];

            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    items.push({
                        cat: 'skill', label: tag.value, mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }
            if (typeof brfEducationSelected !== 'undefined') {
                brfEducationSelected.slice().forEach(function(val) {
                    items.push({
                        cat: 'education', label: val,
                        clear: function() {
                            $('.brf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
                });
            }
            var minExp = $('#min_exp').val(), maxExp = $('#max_exp').val();
            if (minExp !== '' || maxExp !== '') {
                items.push({
                    cat: 'experience', label: (minExp || '0') + '-' + (maxExp || 'Any') + ' Yrs',
                    clear: function() { $('#min_exp').val(''); $('#max_exp').val(''); }
                });
            }
            var minSal = $('#min_sal').val(), maxSal = $('#max_sal').val();
            var minSalChanged = minSal !== '' && minSal !== '0';
            var maxSalChanged = maxSal !== '';
            if (minSalChanged || maxSalChanged) {
                items.push({
                    cat: 'salary', label: (minSal || '0') + '-' + (maxSal || 'Any'),
                    clear: function() { $('#min_sal').val('0'); $('#max_sal').val(''); }
                });
            }
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                items.push({
                    cat: 'notice', label: noticeEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var outsourceEl = $('[name="app_int_outsource"]:checked');
            if (outsourceEl.val() && outsourceEl.val() !== '0') {
                items.push({
                    cat: 'hiring', label: outsourceEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_outsource1').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                items.push({
                    cat: 'activity', label: activityEl.next('.brf-pill').text(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }
            return items;
        }

        function brfAppliedChipRemoved() {
            fetchCandidates();
            if (typeof brfUpdateActiveFiltersUI === 'function') brfUpdateActiveFiltersUI();
            if (typeof brfCaptureSnapshot === 'function') brfCaptureSnapshot();
            brfRenderAppliedFiltersCard();
        }

        function brfRenderAppliedFiltersCard() {
            var items = brfBuildAppliedItems();
            var $chips = $('#brf_applied_chips').empty();
            var visible = brfAppliedExpanded ? items : items.slice(0, BRF_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="brf-applied-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + brfCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').text('★'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="brf-applied-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        brfAppliedChipRemoved();
                    })
                );
                $chips.append($chip);
            });

            if (!brfAppliedExpanded && items.length > BRF_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - BRF_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="brf-applied-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        brfAppliedExpanded = true;
                        brfRenderAppliedFiltersCard();
                    })
                );
            }

            $('#brf_applied_card').toggleClass('d-none', items.length === 0);
            $('#brf_applied_count').text(items.length);
        }

        $(document).on('click', '#brf_applied_clear_all', function() {
            $('#brf_clear_all').trigger('click');
            fetchCandidates();
            if (typeof brfCaptureSnapshot === 'function') brfCaptureSnapshot();
            brfAppliedExpanded = false;
            brfRenderAppliedFiltersCard();
        });

        $(document).on('change', '[name="app_int_notice"], [name="app_int_outsource"], [name="app_int_activity"]', function() {
            brfUpdateActiveFiltersUI();
            if (typeof brfEnforceDrawerLayout === 'function') brfEnforceDrawerLayout();
        });
        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', brfUpdateActiveFiltersUI);

        $(document).on('click', '#brf_clear_all', function() {
            if (typeof tagify !== 'undefined') tagify.removeAllTags();
            $('.brf-edu-check').prop('checked', false);
            $('.brf-edu-check[value=""]').prop('checked', true);
            brfEducationSelected = [];
            brfSyncEducationInput();
            $('#min_exp, #max_exp, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#min_sal').val(0);
            $('#app_int_notice1, #app_int_outsource1, #app_int_activity1').prop('checked', true);
            brfUpdateActiveFiltersUI();
        });

        $(document).on('click', '#brf_reset_btn', function() {
            $('#brf_clear_all').trigger('click');
            fetchCandidates();
            brfCaptureSnapshot();
            brfAppliedExpanded = false;
            brfRenderAppliedFiltersCard();
        });

        /* ------------------------------------------------------------------
           Draft-until-Apply behaviour. Nothing the user changes inside the
           drawer takes effect until "Apply Filters" is clicked. Closing the
           drawer any other way (Close button, header X, overlay, ESC)
           discards unsaved edits and reverts every field to the
           last-applied state, via Metronic's own drawer lifecycle events.
        ------------------------------------------------------------------ */
        var brfSnapshot = null;
        var brfApplying = false;

        function brfCaptureSnapshot() {
            brfSnapshot = {
                skills: (typeof tagify !== 'undefined')
                    ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                education: (typeof brfEducationSelected !== 'undefined') ? brfEducationSelected.slice() : [],
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val(),
                notice: $('[name="app_int_notice"]:checked').val(),
                outsource: $('[name="app_int_outsource"]:checked').val(),
                activity: $('[name="app_int_activity"]:checked').val()
            };
        }

        function brfRestoreSnapshot() {
            if (!brfSnapshot) return;
            if (typeof tagify !== 'undefined') {
                tagify.removeAllTags();
                if (brfSnapshot.skills.length) tagify.addTags(brfSnapshot.skills);
            }
            brfEducationSelected = brfSnapshot.education.slice();
            $('.brf-edu-check').prop('checked', false);
            if (brfEducationSelected.length === 0) {
                $('.brf-edu-check[value=""]').prop('checked', true);
            } else {
                brfEducationSelected.forEach(function(v) {
                    $('.brf-edu-check[value="' + String(v).replace(/"/g, '\\"') + '"]').prop('checked', true);
                });
            }
            brfSyncEducationInput();
            $('#min_exp').val(brfSnapshot.min_exp);
            $('#max_exp').val(brfSnapshot.max_exp);
            $('#min_sal').val(brfSnapshot.min_sal);
            $('#max_sal').val(brfSnapshot.max_sal);
            $('#min_salary_currency').val(brfSnapshot.min_salary_currency);
            $('#sal_mode').val(brfSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            if (brfSnapshot.notice) $('[name="app_int_notice"][value="' + brfSnapshot.notice + '"]').prop('checked', true);
            if (brfSnapshot.outsource) $('[name="app_int_outsource"][value="' + brfSnapshot.outsource + '"]').prop('checked', true);
            if (brfSnapshot.activity) $('[name="app_int_activity"][value="' + brfSnapshot.activity + '"]').prop('checked', true);
            brfUpdateActiveFiltersUI();
        }

        function brfBindDrawerLifecycle() {
            var el = document.getElementById('kt_brf_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(brfBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst) { return setTimeout(brfBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                brfApplying = false;
                brfCaptureSnapshot();
                brfEnforceDrawerLayout();
                setTimeout(brfEnforceDrawerLayout, 50);
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!brfApplying) brfRestoreSnapshot();
                brfApplying = false;
            });
        }
        $(document).ready(brfBindDrawerLifecycle);

        $(document).on('click', '#brf_close_btn', function() {
            $('#kt_brf_filter_close').trigger('click');
        });
        $(document).on('click', '#brf_apply_btn', function() {
            brfApplying = true;
            fetchCandidates();
            brfAppliedExpanded = false;
            brfRenderAppliedFiltersCard();
            $('#kt_brf_filter_close').trigger('click');
        });

        $(document).ready(function() {
            setTimeout(brfUpdateActiveFiltersUI, 600);
            setTimeout(brfCaptureSnapshot, 800);
            setTimeout(brfRenderAppliedFiltersCard, 700);
        });
    </script>
@endpush
