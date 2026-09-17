@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/apptracker/_skeleton.css') }}{{ TM_VERSION }}">
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
                z-index: 999999 !important;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
                z-index: 999999 !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy {
                width: 1200px;
            }
        }

        .error {
            color: #FF0000;
            font-weight: bold;
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }

        .btn.custombtn {
            padding: 6px 6px 6px 6px !important;
        }

        /* Mass Email / Fwd Resume / Copy / Export — same pill style as CV Tracker's
           row-selection action buttons. */
        .aptk-action-btn.btn {
            font-size: 12px;
            font-weight: 500;
            background: #fff !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            box-shadow: none !important;
        }
        .aptk-action-btn.btn:hover {
            background: #eff6ff !important;
            border-color: #93c5fd !important;
        }
        /* The original toolbar buttons stay in the DOM (so the mass_mail/fwd_resume/
           application_copy/exportRecord modal-trigger classes keep working unchanged)
           but are permanently hidden now — their restyled equivalent lives in
           #aptk_selection_bar below. */
        .aptk-legacy-hide {
            display: none !important;
        }

        /* Print — the "Print" button clones the already-rendered checked rows
           (no extra server request) into this hidden container, then hands
           off to the browser's own print dialog. Same pattern as CV
           Tracker's #cvh_print_area. */
        #aptk_print_area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #aptk_print_area, #aptk_print_area * { visibility: visible; }
            #aptk_print_area {
                display: block;
                position: absolute;
                top: 0; left: 0;
                width: 100%;
                padding: 20px;
            }
            #aptk_print_area h2 { font-size: 16px; margin: 0 0 4px; }
            #aptk_print_area .aptk-print-meta { font-size: 11px; color: #666; margin-bottom: 14px; }
            #aptk_print_area table { width: 100%; border-collapse: collapse; }
            #aptk_print_area th, #aptk_print_area td {
                border: 1px solid #ccc; padding: 6px 8px; font-size: 11px; text-align: left;
                vertical-align: top;
            }
            #aptk_print_area .form-check { display: none !important; }
        }

        /* Row selection action bar — same design/position as CV Tracker's
           #cvh_selection_bar: hidden until candidates are checked, then sits directly
           above the results table. */
        .aptk-selection-bar {
            display: none;
            align-items: center;
            gap: 14px;
            background: #fff;
            border: 1px solid #e5e9f2;
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 14px;
            font-family: 'Poppins', sans-serif;
            flex-wrap: wrap;
        }
        .aptk-selection-bar.show {
            display: flex;
        }
        .aptk-selection-count {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }
        .aptk-selection-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
        }
        .aptk-selection-actions .btn {
            font-size: 12px;
            font-weight: 500;
            background: #fff !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            box-shadow: none !important;
        }
        .aptk-selection-actions .btn:hover {
            background: #eff6ff !important;
            border-color: #93c5fd !important;
        }
        .aptk-selection-close {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .aptk-selection-close:hover {
            background: #f1f5f9;
            color: #334155;
        }

        /* Search + Sort toolbar — same visual language as
           similar_candidates.blade.php's simf-toolbar-card (no Filter
           button here, this page keeps its existing Kanban stage pills
           instead of a filter drawer). plf- prefix (Pool List Filter) to
           keep this page's styles/JS distinct from the ibf-/abf-/simf-
           prefixed ones on other AppTracker tabs. */
        .plf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .plf-search-wrap,
        .plf-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .plf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .plf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .plf-search-icon,
        .plf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .plf-search-icon svg,
        .plf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .plf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        /* Custom Sort control — the native <select> can't be styled
           cross-browser once its dropdown is open, so the visible control
           is this button + an owned dropdown panel; the real
           <select id="sortingfilter"> stays hidden underneath purely so
           filtersdataget()/fetchCandidates() keep reading it unchanged. */
        .plf-sort-btn {
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
        .plf-sort-btn:focus { outline: none; }
        .plf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .plf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .plf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .plf-sort-panel {
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
        .plf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .plf-sort-item:hover {
            background: #f8f9fb;
        }
        .plf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        @media (max-width: 992px) {
            .plf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .plf-search-wrap { flex-basis: 100%; }
            .plf-sort-wrap { width: 140px; }
        }

        /* Hiring Pipeline card */
        .hp-toolbar-card .card-body {
            padding: 14px 16px;
        }
        .hp-card .card-body {
            padding: 16px 20px;
        }
        .hp-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 2px;
            font-family: 'Poppins', sans-serif;
        }
        .hp-total-link {
            text-decoration: none;
            flex-shrink: 0;
        }
        .hp-total {
            width: 82px;
            height: 82px;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            transition: background-color .15s;
        }
        .hp-total.bg-total-active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }
        .hp-total.bg-gray {
            background: #e4e6ef;
        }
        .hp-total-icon {
            color: #fff;
            display: flex;
        }
        .hp-total.bg-gray .hp-total-icon {
            color: #7e8299;
        }
        .hp-total-icon svg {
            width: 16px;
            height: 16px;
        }
        .hp-total-count {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .hp-total.bg-gray .hp-total-count {
            color: #3f4254;
        }
        .hp-total-label {
            font-size: 9px;
            font-weight: 500;
            color: #dbeafe;
            text-align: center;
            line-height: 1.2;
            padding: 0 4px;
        }
        .hp-total.bg-gray .hp-total-label {
            color: #7e8299;
        }
        .hp-eq {
            font-size: 18px;
            font-weight: 600;
            color: #b5b5c3;
            flex-shrink: 0;
        }
        .hp-chevron {
            display: flex;
            color: #c2c5cc;
            flex-shrink: 0;
        }
        .hp-chevron svg {
            width: 16px;
            height: 16px;
        }
        .hp-divider {
            width: 1px;
            height: 44px;
            background: #e4e6ef;
            flex-shrink: 0;
            margin: 0 4px;
        }
        .hp-stage {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .hp-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            background: #f2f3f7;
            border: 2px solid #e4e6ef;
            transition: border-color .15s, background-color .15s;
        }
        .hp-circle .hp-icon {
            display: flex;
            color: #99a1b7;
            transition: color .15s;
        }
        .hp-circle .hp-icon svg {
            width: 14px;
            height: 14px;
        }
        .hp-circle .hp-count {
            font-size: 13px;
            font-weight: 700;
            color: #5e6278;
            line-height: 1.2;
            transition: color .15s;
        }
        .hp-circle.active {
            border-color: var(--hp-color);
            background: var(--hp-bg);
        }
        .hp-circle.active .hp-icon {
            color: var(--hp-color);
        }
        .hp-circle.active .hp-count {
            color: var(--hp-color);
        }
        .hp-label {
            font-size: 11px;
            font-weight: 500;
            color: #7e8299;
            text-align: center;
            white-space: nowrap;
            transition: color .15s, font-weight .15s;
        }
        .hp-label.active {
            font-weight: 700;
            color: #181c32;
        }
        @media (max-width: 1440px) {
            .hp-total { width: 72px; height: 72px; }
            .hp-circle { width: 52px; height: 52px; }
        }
        @media (max-width: 576px) {
            .hp-flow { justify-content: flex-start; }
        }

    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
             @php
                    use App\Helper\GeneralMethod;
                    $lock_unlock = GeneralMethod::checksubscription();
                    $jobIdEncrypt = GeneralMethod::value_encrypt($job->id);


                     $pool_path = "javascript:void(0);";
                     $public_bench_path = url('/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id));
                     $internal_bench_path = url('/service_company/apptracker/internal_bench/' . GeneralMethod::value_encrypt(@$job->id));
                     $existing_path = url('/service_company/apptracker/similar_candidates/' . GeneralMethod::value_encrypt(@$job->id));

                        $career_url = url(
                            '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
                        );
                     $candidates_request_path = route('service_company.apptracker.candidates_request');

                     $tabactive = [
                    'pool_active'=>'tabactive',
                    'pool_url'=> $pool_path,
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
                    'benchTalentReq_active'=>'',
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

                    // dd($tabactive);


            @endphp

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
                    @if (in_array('add_job_applications', $userPermissions))
                        <a href="javascript:void(0);">
                            <button type="button"
                                class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3"
                                id="addServiceCandidate">
                                <span class="svg-icon svg-icon-primary svg-icon-1x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path
                                                d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                            <path
                                                d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                fill="#000000" fill-rule="nonzero" />
                                        </g>
                                    </svg>
                                </span>
                                Add Candidate
                            </button>
                        </a>
                    @endif
                    <!--begin::Forward CV-->
                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-1 custombtn aptk-action-btn aptk-legacy-hide d-none" data-toggle="modal"
                        data-target="#mass_mail">
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                        </span> Mass Email
                    </a>

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder me-1 custombtn aptk-action-btn aptk-legacy-hide d-none fwd_resume" data-toggle="modal"
                        data-target="#fwd_resume">
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/><path d="m9 14 3 3 3-3"/><path d="M12 17v-6"/></svg>
                        </span> Fwd Resume
                    </a>

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-success fw-bolder me-1 custombtn aptk-action-btn aptk-legacy-hide d-none test_fwd_resume"
                        id="kt_test_fwd_resume_drawer_toggle_aptk_legacy">
                        <span class="svg-icon svg-icon-success svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                        </span> Test Fwd Resume
                    </a>

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder me-1 custombtn aptk-action-btn aptk-legacy-hide d-none application_copy"
                        data-toggle="modal" data-target="#application_copy">
                        <!--begin::Svg icon-->
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                        </span>
                        <!--end::Svg Icon-->
                        Copy
                    </a>
                    <!--end::Copy-->

                    <a href="javascript:void(0);" type="button"
                        class="btn btn-sm btn-flex btn-light-primary fw-bolder before-event downloadexcell exportRecord d-flex align-items-center position-relative my-1 me-1 custombtn aptk-action-btn aptk-legacy-hide d-none"
                        data-toggle="modal" data-target="#application_copy" style="width: 100px;">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"/>
                            </svg>
                        </span>
                        Export
                    </a>

                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1 me-3 custom_hide">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <!--end::Svg Icon-->
                        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end custom_hide" data-kt-user-table-toolbar="base">








                        </div>
                        <!--end::Toolbar-->

                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-user-table-toolbar="selected">
                            <div class="fw-bolder me-5"> <span class="me-2"
                                    data-kt-user-table-select="selected_count"></span>Selected</div>
                            <button type="button" class="btn btn-danger"
                                data-kt-user-table-select="delete_selected">Delete Selected</button>
                        </div>
                        <!--begin::Modal - Adjust Balance-->
                        <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
                            <!--begin::Modal dialog-->
                            <div class="modal-dialog modal-dialog-centered mw-650px">
                                <!--begin::Modal content-->
                                <div class="modal-content">
                                    <!--begin::Modal header-->
                                    <div class="modal-header">
                                        <!--begin::Modal title-->
                                        <h2 class="fw-bolder">Export Users</h2>
                                        <!--end::Modal title-->
                                        <!--begin::Close-->
                                        <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                            data-kt-users-modal-action="close">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                        rx="1" transform="rotate(-45 6 17.3137)"
                                                        fill="black" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                        transform="rotate(45 7.41422 6)" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Close-->
                                    </div>
                                    <!--end::Modal header-->
                                    <!--begin::Modal body-->
                                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                        <!--begin::Form-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-10">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-bold form-label mb-2">Select Roles:</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <select name="role" data-control="select2"
                                                data-placeholder="Select a role" data-hide-search="true"
                                                class="form-select form-select-solid fw-bolder">
                                                <option></option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="Analyst">Analyst</option>
                                                <option value="Developer">Developer</option>
                                                <option value="Support">Support</option>
                                                <option value="Trial">Trial</option>
                                            </select>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-10">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-bold form-label mb-2">Select Export
                                                Format:</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <select name="format"
                                                data-placeholder="Select a format" data-hide-search="true"
                                                class="form-select form-select-solid fw-bolder">
                                                <option></option>
                                                <option value="excel">Excel</option>
                                                <option value="pdf">PDF</option>
                                                <option value="cvs">CVS</option>
                                                <option value="zip">ZIP</option>
                                            </select>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="text-center">
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-users-modal-action="cancel">Discard</button>
                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit"> <span
                                                    class="indicator-label">Submit</span> <span
                                                    class="indicator-progress">Please wait... <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Modal body-->
                                </div>
                                <!--end::Modal content-->
                            </div>
                            <!--end::Modal dialog-->
                        </div>
                        <!--end::Modal - New Card-->
                    </div>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    @php
        // Hiring Pipeline card: which stage counts as "active" for this page load is
        // resolved server-side from $candStatusClass (set by the controller from the
        // mstatus_id route segment) — clicking a stage re-fetches this same page's data
        // via ajax (see fetchCandidates()/countder() below) and toggles the active look
        // client-side, it never triggers a full reload.
        $hpKnownStages = ['Sourcing', 'Screening', 'Interview', 'Negotiation', 'Offered', 'Joined', 'Rejected'];
        $hpActiveStage = (!empty(@$candStatusClass['Index']) && in_array(@$candStatusClass['Index'], $hpKnownStages))
            ? $candStatusClass['Index']
            : null;
        $hpTotalActive = !empty($mstatus_id) && $mstatus_id == 'all';

        // Colors/icons reuse this app's existing "Pipeline Stages" legend
        // (resources/views/sections/service_company/pipeline.blade.php) rather than
        // inventing new ones, so this card matches every other pipeline-stage display.
        $hpStages = [
            ['key' => 'Sourcing', 'rel' => '1', 'label' => 'Sourcing', 'color' => '#7e8299', 'bg' => '#eef0f2',
                'icon' => 'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
            ['key' => 'Screening', 'rel' => '2', 'label' => 'Screening', 'color' => '#c000ff', 'bg' => '#f9e6ff',
                'icon' => 'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
            ['key' => 'Interview', 'rel' => '3', 'label' => 'Evaluation', 'color' => '#2896ff', 'bg' => '#e8f4ff',
                'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
            ['key' => 'Negotiation', 'rel' => '4', 'label' => 'Negotiation', 'color' => '#ff4098', 'bg' => '#ffe6f2',
                'icon' => 'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
            ['key' => 'Offered', 'rel' => '5', 'label' => 'Offer', 'color' => '#ffab00', 'bg' => '#fff4e0',
                'icon' => 'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
            ['key' => 'Joined', 'rel' => '6', 'label' => 'Joined', 'color' => '#06c732', 'bg' => '#e3fbe9',
                'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
        ];
        $hpRejected = ['key' => 'Rejected', 'rel' => '7', 'label' => 'Rejected', 'color' => '#ff0202', 'bg' => '#ffe3e3',
            'icon' => 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'];
    @endphp

    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

        <div id="" class="container-fluid d-flex flex-stack second_header">

            @include('sections.service_company.apptrackerHeader',['tabactive'=>$tabactive])

        </div>
                
                


                <div class="card mb-4 plf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="plf-search-wrap" style="position: relative;">
                                <span class="plf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control plf-search-input" name="inputJobSearch"
                                    value="" placeholder="Search by candidate name, skills, recruiter, education or location..." id="kt_tagify_2" autocomplete="off" />
                                <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);"></div>
                            </div>
                            <div class="plf-sort-wrap" id="plf_sort_wrap">
                                <span class="plf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="plf-sort-btn" id="plf_sort_btn">
                                    <span id="plf_sort_label">Sort</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="Show All" selected="">Sort</option>
                                    <option value="10">Best Match</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice Period (Serving)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="plf-sort-panel d-none" id="plf_sort_panel"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card hp-card mb-4">
                    <div class="card-body">
                        <div class="hp-flow">
                            <a class="application_sttus_nav hp-total-link" href="javascript:void(0);" rel="all">
                                <div class="{{ $hpTotalActive ? 'bg-total-active' : 'bg-gray' }} text-center hp-total" role="alert" id="bg_all">
                                    <span class="hp-total-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                        </svg>
                                    </span>
                                    <span class="hp-total-count" id="totalcnt">0</span>
                                    <span class="hp-total-label">Total Candidates</span>
                                </div>
                            </a>
                            <span class="hp-eq">=</span>
                            @foreach ($hpStages as $stage)
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="{{ $stage['rel'] }}">
                                    <div class="hp-circle {{ $hpActiveStage === $stage['key'] ? 'active' : '' }}"
                                        data-hp="{{ $stage['rel'] }}"
                                        style="--hp-color: {{ $stage['color'] }}; --hp-bg: {{ $stage['bg'] }};">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="{{ $stage['icon'] }}" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_{{ $stage['rel'] }}">0</span>
                                    </div>
                                    <div class="hp-label {{ $hpActiveStage === $stage['key'] ? 'active' : '' }}" id="tx_{{ $stage['rel'] }}">
                                        {{ $stage['label'] }}
                                    </div>
                                </a>
                                @if (!$loop->last)
                                    <span class="hp-chevron">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 6 6 6-6 6" />
                                        </svg>
                                    </span>
                                @endif
                            @endforeach
                            <span class="hp-divider"></span>
                            <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="{{ $hpRejected['rel'] }}">
                                <div class="hp-circle {{ $hpActiveStage === $hpRejected['key'] ? 'active' : '' }}"
                                    data-hp="{{ $hpRejected['rel'] }}"
                                    style="--hp-color: {{ $hpRejected['color'] }}; --hp-bg: {{ $hpRejected['bg'] }};">
                                    <span class="hp-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="{{ $hpRejected['icon'] }}" />
                                        </svg>
                                    </span>
                                    <span class="hp-count" id="bg_{{ $hpRejected['rel'] }}">0</span>
                                </div>
                                <div class="hp-label {{ $hpActiveStage === $hpRejected['key'] ? 'active' : '' }}" id="tx_{{ $hpRejected['rel'] }}">
                                    {{ $hpRejected['label'] }}
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Row selection action bar — hidden until candidates are checked.
                     Reuses the exact same trigger classes as the legacy toolbar buttons
                     (mass_mail / fwd_resume / application_copy / exportRecord), so every
                     existing modal + ajax handler works unchanged; only the presentation
                     and position (directly above the results table) are new. --}}
                <div class="aptk-selection-bar" id="aptk_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="aptk_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="aptk-selection-count"><span id="aptk_selected_count">0</span> candidates selected</span>
                    <div class="aptk-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" id="kt_mass_mail_drawer_toggle_aptk">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success test_fwd_resume" id="kt_test_fwd_resume_drawer_toggle_aptk">
                            <span class="svg-icon svg-icon-success svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                            </span> Forward Resume
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary application_copy" data-toggle="modal" data-target="#application_copy">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                            </span> Copy
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary exportRecord" title="Export the selected candidates using your browser's print dialog">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            </span> Export
                        </a>
                    </div>
                    <button type="button" class="aptk-selection-close" id="aptk_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="card" id="sc_table_card" style="display:none;">
                    <div class="card-body pt-3 px-5">
                        @if (in_array('view_job_applications', $userPermissions))
                           <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                               <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                           </div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input chk_list" type="checkbox"
                                                    data-kt-check="true"
                                                    data-kt-check-target="#kt_table_users .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Candidate Name</th>
                                        {{-- <th class="">Contacts</th> --}}
                                        <th class="">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="">Notice</th>
                                        <th class="">Interview</th>
                                        <th class="">Location</th>
                                        <th class="">Ownership</th>
                                        <th class="">Stage</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="candidatetList">
                                </tbody>
                            </table>
                            <div id="paginationcandidate"></div>
                            {{-- Print target — populated client-side from the checked rows already
                                 on this page (see the .exportRecord click handler); no server
                                 round-trip involved. --}}
                            <div id="aptk_print_area"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Stage sub stage drawer-->
    <style>
      /* Must render above #kt_activitiesApplication (z-index: 999999) when opened from showdetail */
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
      #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e4e6ef; }
      #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
      #csd_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
    </style>
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
              <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>
            </button>
            <span class="sd-divider"></span>
            <span class="sd-close" id="csd_drawer_close"><i class="fa fa-times"></i></span>
          </div>
        </div>
        <div class="flex-grow-1 px-6 py-4" style="overflow-y:auto;">
          <div class="stage_body_content" id="stage_body_content"></div>
        </div>
      </div>
    </div>
    <!--end::Stage sub stage drawer-->


    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
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

    <!--begin::Test Fwd Resume Drawer - Apptracker-->
    <div id="kt_test_fwd_resume_drawer_aptk" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="test-fwd-resume-aptk"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
        data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_test_fwd_resume_drawer_toggle_aptk" data-kt-drawer-close="#kt_test_fwd_resume_drawer_close_aptk">
        <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
            <div class="card-header px-5 py-0 flex-shrink-0" style="display:none!important;">
                <h3 class="card-title fw-bolder text-dark mb-0">Forward Resume</h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="kt_test_fwd_resume_drawer_close_aptk">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_test_fwd_resume_body_content"></div>
        </div>
    </div>
    <!--end::Test Fwd Resume Drawer - Apptracker-->


    <div id="kt_activitiesBA_toggle"></div>
    <div id="kt_activitiesBA" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBA_toggle" data-kt-drawer-close="#kt_activitiesBA_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Relationship Manager Listing</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesBA_body"> </div>
        </div>
    </div>



    <div class="modal fade" id="bulk_upload_condidate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Bulk Upload condidate</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger closebtn" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="card-body position-relative px-0 py-0" id="application_bulk_upload_condidate_body_content">


                    <div class="modal-body">
                        <!-- Upload form -->
                        <form action="{{ route('service_company.import.sample-job-applications') }}" method="POST"
                            enctype="multipart/form-data" id="application_bulk_upload_condidate">
                            @csrf
                            <div class="form-group">
                                <label for="file">Choose File(s):</label>
                                <input type="hidden" name="job_id" value="{{ @$job->id }}">
                                <input type="file" class="form-control-file" id="file" name="file" multiple>
                            </div>
                            <button type="submit" id="uploadBtn" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary closebtn" data-dismiss="modal">Close</button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!--begin::Mass Mail - Drawer-->
    <div id="kt_mass_mail_drawer_aptk" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="mass-mail-aptk"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
        data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_mass_mail_drawer_toggle_aptk" data-kt-drawer-close="#kt_mass_mail_drawer_close_aptk">
        <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
            <div class="card-header px-5 py-0 flex-shrink-0">
                <h3 class="card-title fw-bolder text-dark mb-0">Send Email</h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="kt_mass_mail_drawer_close_aptk">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_mass_mail_body_content"></div>
        </div>
    </div>
    <!--end::Mass Mail - Drawer-->

    <!-- Job Status Drawer (mirrors jobs/index.blade.php) -->
    <style>
      /* Status drawer opens while job-details (#kt_activitiesApplication, z-index 999999)
         is already open — must render above it. Overlay disabled to avoid backdrop
         collision (same pattern as #interview_schedule_drawer in the main layout). */
      #status_drawer { z-index: 1000000 !important; }
      #status_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
      #status_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
      #status_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
      #status_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
      #status_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
      #status_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
      #status_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 8px; padding: 9px 16px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
      #status_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
      #status_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background .15s; }
      #status_drawer .sd-btn-confirm:hover { background: #2f7fe0; }
      #status_drawer .sd-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
      #status_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
      #status_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
    </style>
    <div id="status_drawer_toggle" style="display:none;"></div>
    <div id="status_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="job-status"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '92%', 'lg': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#status_drawer_toggle" data-kt-drawer-close="#status_drawer_close"
        data-kt-drawer-overlay="false">
      <div class="d-flex flex-column h-100 w-100">
        <div class="sd-topbar">
          <div class="sd-topbar-left">
            <span class="sd-topbar-ico"><i class="fas fa-briefcase"></i></span>
            <div>
              <div class="sd-topbar-title">Job Status</div>
              <div class="sd-topbar-sub">Update the job status and manage its visibility across the platform.</div>
            </div>
          </div>
          <div class="sd-topbar-right">
            <button type="button" class="sd-btn-cancel" id="status_sub_stage_close">Cancel</button>
            <button type="button" class="sd-btn-confirm" id="status_confirm_btn" disabled onclick="newUpdateJobstatus()">
              <span class="indicator-label changetextupdate">Update Status</span> <i class="fas fa-arrow-right"></i>
            </button>
            <span class="sd-close" id="status_drawer_close"><i class="fa fa-xmark"></i></span>
          </div>
        </div>
        <div class="status_body_content flex-grow-1" id="status_body_content" style="overflow-y:auto;"></div>
      </div>
    </div>
    <!-- end::Job Status Drawer -->
@endsection
@push('footer-script')
    {{-- AppTracker resilience layer: connection monitor, visibility handler, resilient fetch --}}
    <script src="{{ asset('js/apptracker/_conn.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_visibility.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_fetch.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_tabSwap.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_filterParse.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_optimistic.js') }}{{ TM_VERSION }}"></script>
    <script src="{{ asset('js/apptracker/_echo.js') }}{{ TM_VERSION }}"></script>
    <script>
    // Step 13: initialise the Reverb WebSocket subscription for this job's pipeline.
    // StageMoved events call countder() which re-fetches the counter badges via AJAX.
    $(function () {
        var jobId = parseInt($('#jobid').val(), 10);
        if (jobId > 0) {
            AppTracker.echo.init({
                jobId: jobId,
                onStageMove: function (payload) {
                    // payload.newStatus is null for polling-triggered refreshes.
                    if (typeof countder === 'function') {
                        countder();
                    }
                }
            });
        }
    });
    </script>
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        // Non-blocking toast for simple pass/fail notifications, same pattern
        // as similar_candidates.blade.php's simfToast — reserved for
        // notifications with no dependent UI logic waiting on them. Stage
        // transitions and "No Internet" hard-stops elsewhere on this page
        // stay as blocking Swal.fire() dialogs since they gate a chained
        // .then() (closing a modal) or need explicit acknowledgement before
        // the user can retry.
        const poolToast = Swal.mixin({
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

        @if (session('packageStatus'))
            poolToast.fire({ icon: 'warning', title: "{{ session('packageStatus') }}" });
        @endif

        // Custom Sort dropdown, same pattern as similar_candidates.blade.php's
        // simfRenderSortPanel — reads options straight from the real
        // #sortingfilter <select> (single source of truth), keeps that
        // select's value in sync so filtersdataget()/fetchCandidates() are
        // unaffected, only replaces how the option list is PRESENTED since a
        // native <select>'s open dropdown can't be styled cross-browser.
        function plfRenderSortPanel() {
            var $panel = $('#plf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="plf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#plf_sort_label').text(label);
                        plfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function plfOpenSortPanel() {
            plfRenderSortPanel();
            $('#plf_sort_panel').removeClass('d-none');
            $('#plf_sort_btn').addClass('open');
        }
        function plfCloseSortPanel() {
            $('#plf_sort_panel').addClass('d-none');
            $('#plf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#plf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#plf_sort_panel').hasClass('d-none')) plfOpenSortPanel();
            else plfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#plf_sort_wrap').length) plfCloseSortPanel();
        });

        $(document).ready(function() {




            var current_nav_active = $('#mstatus_id').val();
            if (current_nav_active == 'all') {
                $('.nav_status').html('All Candidate');
                $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                $('.nav_status').css('color', '#fff');
            }
            
            $(document).on('click', '.application_sttus_nav', function() {

                $('.hp-circle').removeClass('active');
                $('.hp-label').removeClass('active');
                $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');

                var page = $(this).attr('rel');
                var jobid = $('#jobid').val();

                var jobIdEncrypt = '{{ $jobIdEncrypt }}';

                if (page == 1) {
                    $('.hp-circle[data-hp="1"]').addClass('active');
                    $('#tx_1').addClass('active');
                    $('.nav_status').html('Sourcing');
                    $('.nav_status').css('background', '#efefef');
                    $('.nav_status').css('color', '#000000');
                } else if (page == 2) {
                    $('.hp-circle[data-hp="2"]').addClass('active');
                    $('#tx_2').addClass('active');
                    $('.nav_status').html('Screening');
                    $('.nav_status').css('background', '#fff9ed');
                    $('.nav_status').css('color', '#ffab00');
                } else if (page == 3) {
                    $('.hp-circle[data-hp="3"]').addClass('active');
                    $('#tx_3').addClass('active');
                    $('.nav_status').html('Interview');
                    $('.nav_status').css('background', '#f0f8ff');
                    $('.nav_status').css('color', '#2896ff');
                } else if (page == 4) {
                    $('.hp-circle[data-hp="4"]').addClass('active');
                    $('#tx_4').addClass('active');
                    $('.nav_status').html('Negotiation');
                    $('.nav_status').css('background', '#ffe6f2');
                    $('.nav_status').css('color', '#ff4098');
                } else if (page == 5) {
                    $('.hp-circle[data-hp="5"]').addClass('active');
                    $('#tx_5').addClass('active');
                    $('.nav_status').html('Offered');
                    $('.nav_status').css('background', '#d4ffde');
                    $('.nav_status').css('color', '#06c732');
                } else if (page == 6) {
                    $('.hp-circle[data-hp="6"]').addClass('active');
                    $('#tx_6').addClass('active');
                    $('.nav_status').html('Joined');
                    $('.nav_status').css('background', '#f7deff');
                    $('.nav_status').css('color', '#c000ff');
                } else if (page == 7) {
                    $('.hp-circle[data-hp="7"]').addClass('active');
                    $('#tx_7').addClass('active');
                    $('.nav_status').html('Rejected');
                    $('.nav_status').css('background', '#ffe2e5');
                    $('.nav_status').css('color', '#ff0202');
                } else if (page == 'all') {
                    $('.nav_status').html('All Candidate');
                    $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                    $('.nav_status').css('color', '#FFFFFF');
                    $('#bg_all').removeClass('bg-gray').addClass('bg-total-active');
                }

                $('#mstatus_id').val(page);
                var mstatus_id = page;
                if (mstatus_id) {
                    localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
                }
                localStorage.setItem('currentPage', 1);
                page_no = 1;
                var url = "{{ url('/service_company/apptracker/') }}";
                url = url + '/' + jobIdEncrypt + '/' + page;
                window.history.pushState('page2', 'Title', url);
                fetchCandidates(1);

            })

        });
    </script>

    <script>
        //  var table;   
        //    tableLoad('load');

        document.addEventListener("DOMContentLoaded", function() {
            var inputJobSearch = document.querySelector("#kt_tagify_2");

            inputJobSearch.addEventListener("keyup", function() {
                // filtersdataget();
                fetchCandidates();
            });
        });


        function filtersdataget() {
            // tableLoad('load');	
            fetchCandidates();
        }
        // function tableLoad(type) 
        // 	{

        var jobid = $('#jobid').val();
        var mstatus_id = $('#mstatus_id').val();
        var inputSearch = $('#inputSearch').val();
        var sortingfilter = $('#sortingfilter').val();
        var country = $('#country').val();
        var cus_search = $('#kt_tagify_2').val();

        // 
        //  } 


        // Toggle the dropdown on button click



        function countder() {
            var jobid = $('#jobid').val();
            var mstatus_id = $('#mstatus_id').val();
            var inputSearch = $('#inputSearch').val();
            var url = '{{ route('service_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
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

                    if (response.status === 'success' && response.data) {
                        allCandidate = response.data.allCandidate || 0;
                        Sourcing = response.data.Sourcing || 0;
                        Screening = response.data.Screening || 0;
                        Rejected = response.data.Rejected || 0;
                        Offered = response.data.Offered || 0;

                        Negotiation = response.data.Negotiation || 0;
                        Joined = response.data.Joined || 0;
                        Interview = response.data.Interview || 0;
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
        }
        countder();
        $(document).on('click', '#stage_sub_stage_close', function() {
            $('#csd_drawer_close').trigger('click');
        });

        $(document).on('click', '.stage_sub_stage', async function(e) {

            e.preventDefault();
            const connectedsub = await isInternetAvailable();
                if (!connectedsub) {
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

            $('#stage_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
            $('#csd_drawer_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                        $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
                    }
                }
            });

        });

        // Stage-pill click handling now lives in stage_body_content.blade.php
        // itself (csdSelectStage) — the single shared partial used by all 5
        // pages — so it no longer needs its own copy of this handler here.

        $(document).on('click', '#hdr_move_stage_btn', async function(e) {
            e.preventDefault();
            const connected = await isInternetAvailable();
            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: { confirmButton: "btn btn-warning" }
                });
                return;
            }
            var id = $(this).attr('rel');
            $('#stage_body_content').html(
                '<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>'
            ).show();
            $('#custom_stage_submit').show();
            $('#csd_drawer_toggle').trigger('click');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                        $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
                    }
                }
            });
        });

        $(document).ready(function() {

          // Replaces the old server-side Excel export — purely client-side:
          // clones the checked rows' already-rendered markup (no new
          // request, no server load) into #aptk_print_area and hands off to
          // the browser's own print dialog. Same pattern as CV Tracker.
          $(document).on('click', '.exportRecord', function() {

                var $checked = $('.chk_list_item:checked');
                if ($checked.length === 0) {
                    poolToast.fire({ icon: 'warning', title: 'Select at least one candidate first' });
                    return false;
                }

                var $sourceTable = $('#candidatetList').closest('table');
                var $table = $('<table></table>');
                var $thead = $sourceTable.find('thead tr').clone();
                $thead.find('th').first().remove(); // drop the checkbox header column
                $table.append($('<thead></thead>').append($thead));

                var $tbody = $('<tbody></tbody>');
                $checked.each(function() {
                    var $row = $(this).closest('tr').clone();
                    $row.find('td').first().remove(); // drop the checkbox cell
                    $tbody.append($row);
                });
                $table.append($tbody);

                var $printArea = $('#aptk_print_area').empty();
                $printArea.append('<h2>Candidates Summary</h2>');
                $printArea.append('<div class="aptk-print-meta">Printed ' +
                    new Date().toLocaleString() + ' — ' + $checked.length + ' candidate(s)</div>');
                $printArea.append($table);

                window.print();
            });

            $(document).on('click', '#custom_stage_submit', async function(e) {

                e.preventDefault();
                 const connected = await isInternetAvailable();
                    if (!connected) {
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

                var master_status = $('input[name="master_status"]:checked').val();
                var comment = $('#comment').val();
                if (master_status == 8 && comment=='') {
                    poolToast.fire({ icon: 'info', title: 'InReview Comment Required!' });
                } else if ($('#main_substage').is(':visible') && $('input[name="sub_stages"]:checked').length === 0) {
                    poolToast.fire({ icon: 'info', title: 'Please select a sub stage to continue.' });
                } else {
                    $("#custom_stage_submit").prop("disabled", true);
                    $("#custom_stage_submit").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                        );

                    $.ajax({
                        url: '{{ route('service_company.job-applications.stage_submit') }}',
                        container: '#status_frm',
                        type: "POST",
                        data: $('#status_frm').serialize(),
                        success: function(response) {
                            $("#custom_stage_submit").prop("disabled", false);
                            $("#custom_stage_submit").html(
                                `<span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>`);
                            csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
                            if (response.status == 'success') {
                                Swal.fire("Success", 'Application stage update successfully !',
                                    "success").then(function() {
                                    // $('#stage_sub_stage').modal('hide');
                                    $("#stage_sub_stage_close").click();
                                });
                                countder();
                                var page = $('#paginationcandidate .pagi-page.pagi-active').data('page') || 1;
                                fetchCandidates(page);
                                //  	var table;
                                // 				tableLoad('load');
                                // table._fnDraw();
                                load_interviewSchedule();
                            } else if (response.status == 'cvpending') {
                                Swal.fire("Warning", 'Resume pending this job application.',
                                    "warning").then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            } else if (response.status == 'candidatevalidation') {
                                Swal.fire("Warning", response.message, "warning").then(
                                function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            }
                        }
                    })
                }


            });
        })

        $(document).ready(function() {
          $(document).on('click', '#addServiceCandidate', function() {

                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var eventId = 4;
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'event': 'CVACCESS',
                        'eventId': eventId
                    },
                    success: function(response) {

                        if (response.status == "success") {
                            var url =
                                "{{ url('service_company/job-applications/create/' . GeneralMethod::value_encrypt(@$job->id) . '/0') }}";
                            window.location = url;
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });

            });

            $(document).on('change', '#action_type', function() {
                var action_type = $(this).val();
                var id = "{{ @$job->id }}";
                var url = "{{ route('service_company.export.sample-job-applications', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";
                if (action_type == 1) {
                    // alert(action_type);
                    // return false;
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'GET',
                            'job_id': id
                        },
                        beforeSend: function() {
                            //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                        },
                        success: function(response) {

                            window.location.href = response.file_url;
                            setTimeout(() => {

                                $.ajax({
                                    type: 'GET',
                                    url: "{{ route('service_company.unlink.sample-job-applications', ':id') }}",
                                    data: {
                                        '_token': token,
                                        '_method': 'GET',
                                        'file_name': response.file_name
                                    },
                                    beforeSend: function() {},
                                    success: function(response) {
                                        $('#action_type').val($(
                                            '#action_type option:first'
                                            ).val());
                                    }
                                });

                            }, 2000);
                        }
                    });
                } else {
                    if (action_type == 2) {
                        // alert("djd");
                        $('#bulk_upload_condidate').modal('show');
                    }

                }

            })
            $(document).on('change', '.closebtn', function() {
                $('#action_type').val($('#action_type option:first').val());
                $('#bulk_upload_condidate').modal('hide');
            });
            $(document).on('submit', '#application_bulk_upload_condidate', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                document.getElementById('uploadBtn').disabled = true;
                $('#uploadBtn').text('Uploading....');
                $.ajax({
                    url: "{{ route('service_company.import.sample-job-applications') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = (evt.loaded / evt.total) * 100;
                                console.log(
                                percentComplete); // Update your UI here with the progress percentage
                            }
                        }, false);

                        return xhr;
                    },
                    success: function(response) {
                        document.getElementById('uploadBtn').disabled = false;
                        if (response.status) {
                            alert(response.message);
                            $('#uploadBtn').text('Uploading....');
                            $('#bulk_upload_condidate').modal('hide');
                        }
                        // Handle success response here
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
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
            $('#application_test_fwd_resume_body_content').html('');
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.test_fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.test_fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.send_email').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
            updateAptkSelectionBar();
        });
        $(document).on('click', '.chk_list_item', function() {
            $('#application_test_fwd_resume_body_content').html('');
            var i = 0;
            var arr = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });
            if (arr.length > 0) {
                $('.test_fwd_resume').removeClass("d-none");
                $('.custom_hide').addClass("d-none");
                $('.mass_mail').removeClass("d-none");
                $('.downloadexcell').removeClass("d-none");
                $('.send_email').removeClass("d-none");
                $('.app_copy').removeClass("d-none");
                $('.application_copy').removeClass("d-none");
            } else {
                $('.test_fwd_resume').addClass("d-none");
                $('.custom_hide').removeClass("d-none");
                $('.send_email').addClass("d-none");
                $('.downloadexcell').addClass("d-none");
                $('.mass_mail').addClass("d-none");
                $('.app_copy').addClass("d-none");
                $('.application_copy').addClass("d-none");
            }
            updateAptkSelectionBar();
        });

        /* Selection bar: live count + clear-selection controls (checkbox + close X) —
           same behavior as CV Tracker's #cvh_selection_bar. */
        function updateAptkSelectionBar() {
            var n = $('.chk_list_item:checked').length;
            $('#aptk_selected_count').text(n);
            $('#aptk_selection_bar').toggleClass('show', n > 0);
            $('#aptk_selection_master_chk').prop('checked', n > 0);
        }
        function clearAptkSelection() {
            $('.chk_list, .chk_list_item').prop('checked', false);
            $('.test_fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
            $('.custom_hide').removeClass('d-none');
            updateAptkSelectionBar();
        }
        $(document).on('click', '#aptk_selection_close', clearAptkSelection);
        $(document).on('change', '#aptk_selection_master_chk', function() {
            if (!$(this).is(':checked')) clearAptkSelection();
        });

        $(document).on('click', '.test_fwd_resume', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
                try { CKEDITOR.instances['test_editor1'].destroy(true); } catch(e) {}
            }

            var id = {{ $job->id }};
            var url = "{{ route('service_company.cvtracker.test_fwd_resume', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var arr = [];
            var jobIdArr = [id];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
            });

            if (arr.length === 0) {
                Swal.fire({
                    title: 'No Candidate Selected',
                    text: 'Please select at least one candidate before testing forward resumes.',
                    icon: 'warning',
                    confirmButtonColor: '#0D9488'
                });
                return;
            }

            if (arr.length > 5) {
                Swal.fire({
                    title: 'Maximum 5 Candidates Allowed',
                    html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                          '<p style="margin-bottom:8px;">You’ve selected <strong>' + arr.length + ' candidates</strong>. The forward resume engine supports up to 5 candidates.</p>' +
                          '<p style="color:#6B7280;margin:0;">Please select 5 candidates or fewer.</p>' +
                          '</div>',
                    icon: 'warning',
                    confirmButtonText: 'OK, I Understand',
                    confirmButtonColor: '#0D9488'
                });
                return;
            }

            var drawerEl = document.getElementById('kt_test_fwd_resume_drawer_aptk');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) { drawer.show(); }
            }
            $('#application_test_fwd_resume_body_content').html(
                '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
                '<span class="spinner-border spinner-border-sm text-success me-2"></span> Loading Forward Resume Engine...</div>'
            );

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'jobIdArr': jobIdArr
                },
                success: function(response) {
                    if (response.status == "success") {
                        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
                            try { CKEDITOR.instances['test_editor1'].destroy(true); } catch(e) {}
                        }
                        $('#application_test_fwd_resume_body_content').html(response.view);
                    } else {
                        toastr.error(response.message || 'Failed to initialize Forward Resume');
                    }
                },
                error: function() {
                    toastr.error('Server error initializing Forward Resume');
                }
            });
        });

        $(document).on('click', '#kt_test_fwd_resume_drawer_close_aptk', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
                try { CKEDITOR.instances['test_editor1'].destroy(true); } catch(e) {}
            }
            var drawerEl = document.getElementById('kt_test_fwd_resume_drawer_aptk');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) { drawer.hide(); }
            }
            $('#application_test_fwd_resume_body_content').html('');
        });

        $(document).on('click', '.mass_mail', function() {
            var drawerEl = document.getElementById('kt_mass_mail_drawer_aptk');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) { drawer.show(); }
            }
            $('#application_mass_mail_body_content').html(
                '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
                '<span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading...</div>'
            );
            $('#application_fwd_resume_body_content').html('');
            var id = {{ $job->id }};
            var arr = [];
            var jobIdArr = [id];
            $('.chk_list_item:checked').each(function() {

                var jobId = $(this).attr('data-jobId');
                arr.push($(this).val());
                jobIdArr.push(jobId);
            });

            console.log(jobIdArr);

            var id = jobIdArr[0];
            var url = "{{ route('service_company.cvtracker.mass_mail', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'jobIdArr': jobIdArr
                },
                cache: false,
                success: function(response) {
                    if (response.status == "success") {
                        $('#application_mass_mail_body_content').html(response.view);
                    }
                }
            });

        });
    </script>

    <script>
       $(document).on('click', '.application_copy', function() {
            window._currentCandidateName = '';
            var candSkills = [];
            var firstCandName = '';
            var checkedCount = $('.chk_list_item:checked').length;

            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.application_copy', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var arr = [];
            var wiseId = [];
            $('.chk_list_item:checked').each(function() {
                arr.push($(this).val());
                wiseId.push($(this).data('wiseid'));
                var sk = $(this).attr('data-skills') || $(this).data('skills');
                if (sk) {
                    var sArr = String(sk).split(',');
                    sArr.forEach(function(s) {
                        var cleaned = $.trim(s).toLowerCase();
                        if (cleaned && candSkills.indexOf(cleaned) === -1) {
                            candSkills.push(cleaned);
                        }
                    });
                }
                var cn = $(this).attr('data-cname') || $(this).data('cname');
                if (cn && !firstCandName) {
                    firstCandName = cn;
                }
            });

            window._candidateSkills = candSkills;
            if (checkedCount === 1 && firstCandName) {
                window._currentCandidateName = firstCandName;
            } else if (checkedCount > 1) {
                window._currentCandidateName = checkedCount + ' candidates';
            }
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'wiseId': wiseId
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

        $(document).ready(function() {
            // $.noConflict();

            fetchCandidates();

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
                localStorage.setItem('currentPage', 1);
                page_no = 1;
                fetchCandidates(1);
            });
        });


        var page_no = localStorage.getItem('currentPage') || 1;

        function fetchCandidates(page, search = '') {
            var targetPage = page || page_no || 1;
            page_no = targetPage;
            localStorage.setItem('currentPage', targetPage);

            var perPage = $('#paginationcandidate .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;

            var mstatus_id = localStorage.getItem('mstatusValueSet');
          
            if (mstatus_id) {
                try {
                    mstatus_id = JSON.parse(mstatus_id);
                } catch (e) {
                      var mstatus_id = $('#mstatus_id').val();
                   localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
                }
            } else {
                var mstatus_id = $('#mstatus_id').val();
                localStorage.setItem('mstatusValueSet', JSON.stringify(mstatus_id));
            }
           

            var clientid = '1';
            $('.loadingMessage').css({ display: 'flex' });
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            return $.ajax({
                url: `{!! route('service_company.apptracker.allDataAjax') !!}?page=${targetPage}`,
                type: 'GET',
                data: {
                  jobid : $('#jobid').val(),
                  mstatus_id : mstatus_id,
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  country : $('#country').val(),
                  cus_search : $('#kt_tagify_2').val(),
                  size : perPage
                },
                success: function(response) {
                    countder();
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    $('#candidatetList').html(response.html);
                    $('#pool_counter').html(response.total);
                    localStorage.setItem('currentPage', targetPage);
                    var mstatusvalue = $('#mstatus_id').val();
                    localStorage.setItem('mstatusValueSet', mstatusvalue);
                    
                    $('#paginationcandidate').html(response.pagination);


                    $('.hp-circle').removeClass('active');
                    $('.hp-label').removeClass('active');
                    $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');

                    if (mstatus_id == 1) {
                    $('.hp-circle[data-hp="1"]').addClass('active');
                    $('#tx_1').addClass('active');
                    $('.nav_status').html('Sourcing');
                    $('.nav_status').css('background', '#efefef');
                    $('.nav_status').css('color', '#000000');
                } else if (mstatus_id == 2) {
                    $('.hp-circle[data-hp="2"]').addClass('active');
                    $('#tx_2').addClass('active');
                    $('.nav_status').html('Screening');
                    $('.nav_status').css('background', '#fff9ed');
                    $('.nav_status').css('color', '#ffab00');
                } else if (mstatus_id == 3) {
                    $('.hp-circle[data-hp="3"]').addClass('active');
                    $('#tx_3').addClass('active');
                    $('.nav_status').html('Interview');
                    $('.nav_status').css('background', '#f0f8ff');
                    $('.nav_status').css('color', '#2896ff');
                } else if (mstatus_id == 4) {
                    $('.hp-circle[data-hp="4"]').addClass('active');
                    $('#tx_4').addClass('active');
                    $('.nav_status').html('Negotiation');
                    $('.nav_status').css('background', '#ffe6f2');
                    $('.nav_status').css('color', '#ff4098');
                } else if (mstatus_id == 5) {
                    $('.hp-circle[data-hp="5"]').addClass('active');
                    $('#tx_5').addClass('active');
                    $('.nav_status').html('Offered');
                    $('.nav_status').css('background', '#d4ffde');
                    $('.nav_status').css('color', '#06c732');
                } else if (mstatus_id == 6) {
                    $('.hp-circle[data-hp="6"]').addClass('active');
                    $('#tx_6').addClass('active');
                    $('.nav_status').html('Joined');
                    $('.nav_status').css('background', '#f7deff');
                    $('.nav_status').css('color', '#c000ff');
                } else if (mstatus_id == 7) {
                    $('.hp-circle[data-hp="7"]').addClass('active');
                    $('#tx_7').addClass('active');
                    $('.nav_status').html('Rejected');
                    $('.nav_status').css('background', '#ffe2e5');
                    $('.nav_status').css('color', '#ff0202');
                } else if (mstatus_id == 'all') {
                    $('.nav_status').html('All Candidate');
                    $('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
                    $('.nav_status').css('color', '#FFFFFF');
                    $('#bg_all').removeClass('bg-gray').addClass('bg-total-active');
                }

                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#sc_table_card').show();
                    console.error('allDataAjax failed:', xhr.status, xhr.responseText);
                }
            });
        }

        // ── Job Status drawer (same as jobs/index.blade.php) ──────────────────
        $(document).on('click', '#status_sub_stage_close', function () {
            $('#status_drawer_close').trigger('click');
        });

        $(document).on('click', '.status_stage', function () {
            if ($(this).data('direct-client') == 1) {
                Swal.fire({ icon: 'warning', title: 'Restricted', text: 'Direct Client Job Status cannot be changed by Agencies' });
                return false;
            }
            $('#status_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
            $('#status_confirm_btn').prop('disabled', true).css('background', '');
            $('.changetextupdate').text('Update Status');
            $('#status_drawer_toggle').trigger('click');
            var id  = $(this).data('row-id');
            var url = "{{ route('service_company.jobs.editJobStatus',':id') }}";
            url     = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url:  url,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#status_body_content').html(response.view);
                    } else {
                        $('#status_body_content').html('<div class="text-center py-20 text-danger">Could not load the job status details. Please close and try again.</div>');
                    }
                },
                error: function () {
                    $('#status_body_content').html('<div class="text-center py-20 text-danger">Something went wrong loading this job\'s status. Please close and try again.</div>');
                }
            });
        });
        // ───────────────────────────────────────────────────────────────────────

        $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job Selected!', 'Selected');
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


    </script>
@endpush
