@extends('layouts.service_company_layout')
@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
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

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_client_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_client_activitiesApplication {
                width: 1200px;
            }
        }

        .table-custom-class tbody tr {
            height: 82px;
            position: relative;
        }
        .tagify__tag {
            display: flex;
            align-items: center;
        }

        .tagify__tag .star {
            margin-left: 5px;
            cursor: pointer;
            font-size: 21px;
            position: relative;
            top: -5px;
        }

        .tagify__tag .star.checked {
            color: #ef1632;
        }

        /* dsimilar_candidates filter drawer (replaces the old #apptrackerfilter
           modal) — ported 1:1 from similar_candidates.blade.php's simf-* drawer,
           renamed to the dsf-* prefix per this page's naming convention. */
        #kt_dsimilar_filter_drawer.dsf-drawer {
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
        #dsf_card.dsf-card {
            display: flex !important;
            flex-direction: column !important;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
        .dsf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid #eef0f4;
            flex-shrink: 0;
        }
        .dsf-header-icon {
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
        .dsf-title {
            font-size: 17px;
            font-weight: 700;
            color: #181c32;
            margin: 0;
        }
        .dsf-count-pill {
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
        .dsf-subtitle {
            font-size: 12px;
            color: #7e8299;
            margin: 2px 0 0;
        }
        .dsf-close {
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
        .dsf-close:hover {
            background: #f1f5f9;
            color: #334155;
        }
        .dsf-activebar {
            padding: 12px 0;
            border-bottom: 1px solid #eef0f4;
        }
        .dsf-activebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .dsf-activebar-label {
            font-size: 12px;
            font-weight: 600;
            color: #181c32;
        }
        .dsf-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .dsf-clearall:hover {
            color: #b91c1c;
        }
        .dsf-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .dsf-chip {
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
        .dsf-chip.mandatory {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }
        .dsf-chip-remove {
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
        .dsf-chip-remove:hover {
            background: rgba(0,0,0,.14);
        }
        #dsf_card .dsf-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 4px 20px 16px;
        }
        #dsf_card .dsf-header,
        #dsf_card .dsf-footer {
            flex-shrink: 0 !important;
        }
        .dsf-section {
            padding: 16px 0;
            border-bottom: 1px solid #f1f2f6;
        }
        .dsf-section-last {
            border-bottom: none;
        }
        .dsf-section-head {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }
        .dsf-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .dsf-icon-blue { background: #eef2ff; color: #4f46e5; }
        .dsf-icon-green { background: #ecfdf5; color: #059669; }
        .dsf-icon-purple { background: #f5f3ff; color: #7c3aed; }
        .dsf-icon-teal { background: #ecfeff; color: #0e7490; }
        .dsf-icon-amber { background: #fffbeb; color: #b45309; }
        .dsf-icon-rose { background: #fff1f2; color: #be123c; }
        .dsf-icon-pink { background: #fdf2f8; color: #be185d; }
        .dsf-icon-sky { background: #f0f9ff; color: #0369a1; }
        .dsf-section-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #181c32;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dsf-section-desc {
            font-size: 11.5px;
            color: #99a1b7;
            margin-top: 1px;
        }
        .dsf-badge-primary {
            font-size: 9.5px;
            font-weight: 600;
            color: #2563eb;
            background: #dbeafe;
            border-radius: 4px;
            padding: 1px 6px;
        }
        .dsf-mini-label {
            font-size: 11px;
            font-weight: 500;
            color: #7e8299;
            margin-bottom: 4px;
            display: block;
        }
        .dsf-salary-row {
            flex-wrap: nowrap;
        }
        .dsf-salary-row > [class^="col-"] {
            padding-left: 4px;
            padding-right: 4px;
            min-width: 0;
        }
        .dsf-salary-row .form-select,
        .dsf-salary-row .form-control {
            font-size: 11px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .dsf-salary-row .form-select {
            padding-right: 20px;
        }
        .dsf-select-dd {
            position: relative;
        }
        .dsf-select-dd-btn {
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
        .dsf-select-dd-btn span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 8px;
            text-transform: uppercase;
        }
        .dsf-edu-name {
            text-transform: uppercase;
        }
        .dsf-select-dd-btn svg {
            flex-shrink: 0;
            color: #99a1b7;
        }
        .dsf-select-dd-panel {
            margin-top: 6px;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }
        .dsf-select-dd-item {
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
        .dsf-select-dd-item:last-child {
            border-bottom: none;
        }
        .dsf-select-dd-item:hover {
            background: #f8f9fb;
        }
        .dsf-select-dd-item input {
            accent-color: #2563eb;
        }
        .dsf-loc-inputwrap {
            position: relative;
        }
        .dsf-loc-chevron {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            color: #99a1b7;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: transform .15s;
        }
        .dsf-loc-chevron.open {
            transform: translateY(-50%) rotate(180deg);
        }
        .dsf-loc-suggestions {
            margin-top: 10px;
            border: 1px solid #eef0f4;
            border-radius: 8px;
            padding: 10px;
            background: #fafbfc;
        }
        .dsf-loc-label {
            font-size: 11px;
            font-weight: 600;
            color: #99a1b7;
            margin-bottom: 6px;
        }
        .dsf-loc-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 4px;
        }
        .dsf-loc-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 11.5px;
            font-weight: 500;
            color: #3f4254;
        }
        .dsf-loc-chip:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .dsf-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .dsf-pill-row .btn-check {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .dsf-pill {
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
        .dsf-pill-row .btn-check:checked + .dsf-pill {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .dsf-footer {
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
        .dsf-footer-count {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .dsf-footer-icon {
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
        .dsf-footer-number {
            font-size: 15px;
            font-weight: 700;
            color: #181c32;
            line-height: 1.2;
        }
        .dsf-footer-label {
            font-size: 10.5px;
            color: #7e8299;
        }
        .dsf-footer-hint {
            cursor: help;
        }
        .dsf-footer-actions {
            display: flex;
            gap: 6px;
        }
        .dsf-footer-actions .btn {
            font-size: 11.5px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 576px) {
            .dsf-footer { flex-direction: column; align-items: stretch; }
            .dsf-footer-actions { justify-content: stretch; }
            .dsf-footer-actions .btn { flex: 1 1 auto; justify-content: center; }
        }

        /* Toolbar redesign: search / sort / filter — #kt_tagify_2 is a plain
           free-text field, the Filter button is the drawer's only entry
           point, and the Active Filters card below is the single place
           structured filters are summarized. */
        .dsf-toolbar-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
            font-family: 'Poppins', sans-serif;
        }
        .dsf-search-wrap,
        .dsf-sort-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
        }
        .dsf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .dsf-sort-wrap {
            flex-shrink: 0;
            width: 220px;
            position: relative;
        }
        .dsf-search-icon,
        .dsf-sort-icon {
            color: #99a1b7;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dsf-search-icon svg,
        .dsf-sort-icon svg {
            flex-shrink: 0;
            display: block;
        }
        .dsf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13.5px;
            line-height: 42px;
            height: 42px;
        }
        .dsf-sort-btn {
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
        .dsf-sort-btn:focus { outline: none; }
        .dsf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .dsf-sort-btn .chevron {
            flex-shrink: 0;
            color: #99a1b7;
            transition: transform .15s;
        }
        .dsf-sort-btn.open .chevron {
            transform: rotate(180deg);
        }
        .dsf-sort-panel {
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
        .dsf-sort-item {
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #3f4254;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .dsf-sort-item:hover {
            background: #f8f9fb;
        }
        .dsf-sort-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .dsf-filter-btn {
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
        .dsf-filter-btn:hover {
            background: #1d4ed8 !important;
        }
        @media (max-width: 992px) {
            .dsf-sort-wrap { width: 160px; }
        }
        @media (max-width: 768px) {
            .dsf-search-wrap { flex-basis: 100%; }
            .dsf-sort-wrap { width: 140px; }
        }

        /* Applied Filters summary card — lives on the main page above the
           results table, hidden until at least one filter has been applied. */
        .dsf-applied-card {
            border: 1px solid #eef0f4;
            border-radius: 14px;
            background: #fff;
            padding: 16px 18px;
            margin-bottom: 16px;
            font-family: 'Poppins', sans-serif;
        }
        .dsf-applied-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .dsf-applied-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #181c32;
        }
        .dsf-applied-count {
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
        .dsf-applied-clearall {
            font-size: 12px;
            font-weight: 500;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        .dsf-applied-clearall:hover { color: #b91c1c; }
        .dsf-applied-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .dsf-applied-chip {
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
        .dsf-applied-chip .ic { display: inline-flex; align-items: center; flex-shrink: 0; }
        .dsf-applied-chip .star { font-size: 12px; line-height: 1; }
        .dsf-applied-chip-remove {
            cursor: pointer;
            opacity: .55;
            display: inline-flex;
            align-items: center;
            margin-left: 2px;
        }
        .dsf-applied-chip-remove:hover { opacity: 1; }
        .dsf-applied-chip.cat-skill      { background: #f5f3ff; color: #7c3aed; border-color: #ede9fe; }
        .dsf-applied-chip.cat-location   { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .dsf-applied-chip.cat-education  { background: #eff6ff; color: #2563eb; border-color: #dbeafe; text-transform: uppercase; }
        .dsf-applied-chip.cat-experience { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .dsf-applied-chip.cat-salary     { background: #ecfeff; color: #0e7490; border-color: #cffafe; }
        .dsf-applied-chip.cat-notice     { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .dsf-applied-chip.cat-status     { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
        .dsf-applied-chip.cat-activity   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .dsf-applied-more {
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
        .dsf-applied-more:hover { background: #e5e7eb; }
        body > .tagify__dropdown {
            z-index: 1400 !important;
        }
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        @php
             use App\Helper\GeneralMethod;
            $req_path = url(
                '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt(@$job->id) . '/all',
            );
            //$public_bench_path	=	url('/service_company/apptracker/public_bench/'. GeneralMethod::value_encrypt(@$job->id));
            $internal_bench_path = url(
                '/service_company/apptracker/dinternal_bench/' . GeneralMethod::value_encrypt(@$job->id),
            );

            $career_url = url(
                '/service_company/career-apptracker/career-apply/' . GeneralMethod::value_encrypt(@$job->id),
            );
            $existing_path = url(
                '/service_company/apptracker/dsimilar_candidates/' . GeneralMethod::value_encrypt(@$job->id),
            );
             $tabactive = [
                    'pool_active'=>'',
                    'pool_url'=> $req_path,
                    'pool_id'=>'pool_counter',
                    'similar_active'=>'tabactive',
                    'similar_url'=>$existing_path,
                    'similar_id'=>'similar_counter',
                    'inhouseBench_active'=>'',
                    'inhouseBench_url'=> $internal_bench_path,
                    'inhouseBench_id'=>'inhouseBench_counter',
                    'career_active'=>'',
                    'career_url'=>$career_url,
                    'career_id'=>'career_counter',
                    'career_counter' => @$career_counter,
                    'pool_counter' => @$pool_counter,
                    'existing_counter' =>@$existing_counter,
                    'internal_bench_counter' =>@$internal_bench_counter
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
                    <div class="d-flex align-items-center position-relative my-1 me-3">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->

                        <!--end::Svg Icon-->
                        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                            placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

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
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">

                <div id="" class="container-fluid d-flex flex-stack second_header">
                    @include('sections.service_company.clientappHeader',['tabactive'=>$tabactive])
                </div>
      

                <div class="card mb-4 dsf-toolbar-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="dsf-search-wrap" style="position: relative;">
                                <span class="dsf-search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                </span>
                                <input type="text" class="form-control dsf-search-input" name="inputJobSearch"
                                    placeholder="Search by candidate name, skills, education or location..." id="kt_tagify_2" autocomplete="off" />
                            </div>
                            <div class="dsf-sort-wrap" id="dsf_sort_wrap">
                                <span class="dsf-sort-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                </span>
                                <button type="button" class="dsf-sort-btn" id="dsf_sort_btn">
                                    <span id="dsf_sort_label">Best Match</span>
                                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                    <option value="10" selected="">Best Match</option>
                                    <option value="1">Newest to Oldest (Upload Date)</option>
                                    <option value="2">Oldest to Newest (Upload Date)</option>
                                    <option value="3">Low to High (Salary)</option>
                                    <option value="4">High to Low (Salary)</option>
                                    <option value="21">Notice Period (Immediate)</option>
                                    <option value="22">Notice Period (Serving)</option>
                                    <option value="7">High to Low (Experience) </option>
                                    <option value="8">Low to High (Experience)</option>
                                </select>
                                <div class="dsf-sort-panel d-none" id="dsf_sort_panel"></div>
                            </div>
                            <button type="button" id="kt_dsimilar_filter_toggle"
                                class="btn dsf-filter-btn position-relative">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                <span>Filter<span id="dsf_filter_count_text"></span></span>
                                <span class="badge badge-circle badge-primary dsf-count-badge d-none" id="dsf_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!--begin::Applied Filters summary card — hidden until Apply Filters is clicked-->
                <div class="dsf-applied-card d-none" id="dsf_applied_card">
                    <div class="dsf-applied-head">
                        <span class="dsf-applied-title">
                            Active Filters
                            <span class="dsf-applied-count" id="dsf_applied_count">0</span>
                        </span>
                        <a href="javascript:void(0);" class="dsf-applied-clearall" id="dsf_applied_clear_all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                            Clear All
                        </a>
                    </div>
                    <div class="dsf-applied-chips" id="dsf_applied_chips"></div>
                </div>
                <!--end::Applied Filters summary card-->

                <!--Start Filter Drawer-->
                <div id="kt_dsimilar_filter_drawer" class="bg-body dsf-drawer" data-kt-drawer="true" data-kt-drawer-name="dsimilar-filter"
                    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_dsimilar_filter_toggle" data-kt-drawer-close="#kt_dsimilar_filter_close">
                    <div id="dsf_card" class="card shadow-none rounded-0 w-100 h-100 dsf-card">
                        <!--begin::Header-->
                        <div class="dsf-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="dsf-header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="dsf-title">Filter Candidates</h3>
                                        <span class="dsf-count-pill" id="dsf_header_count">0</span>
                                    </div>
                                    <p class="dsf-subtitle">Refine your search and find the best talent</p>
                                </div>
                            </div>
                            <button type="button" class="dsf-close" id="kt_dsimilar_filter_close" title="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="dsf-body">
                            <form id="createSchedule" class="ajax-form" method="post">

                                <!--begin::Active filters — inside scroll area so it flows with the sections -->
                                <div class="dsf-activebar d-none" id="dsf_active_bar">
                                    <div class="dsf-activebar-head">
                                        <span class="dsf-activebar-label">Active Filters <span id="dsf_active_count">(0)</span></span>
                                        <a href="javascript:void(0);" class="dsf-clearall" id="dsf_clear_all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                                            Clear All
                                        </a>
                                    </div>
                                    <div class="dsf-chips" id="dsf_active_chips"></div>
                                </div>
                                <!--end::Active filters-->

                                <!--begin::Skills-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Skills <span class="dsf-badge-primary">Primary</span></div>
                                            <div class="dsf-section-desc">Add skills to find relevant candidates — click ★ to make a skill mandatory</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <input class="form-control form-control-solid d-flex align-items-center"
                                            name="kt_tagify_users" value="{{ @$default_skill }}" placeholder=""
                                            id="kt_tagify_users" />
                                        <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
                                        <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
                                        <script>
                                            const inputElm = document.querySelector('#kt_tagify_users');

                                            const tagify = new Tagify(inputElm, {
                                                templates: {
                                                    tag: function(tagData) {
                                                        tagData = tagData || {};
                                                        const value = tagData.value || '';
                                                        const isChecked = tagData.checked ? 'checked' : '';
                                                        const mandatoryClass = tagData.checked ? 'dsf-mandatory-tag' : '';
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

                                            // Toggle mandatory (star) on click of EITHER the star itself OR the
                                            // keyword text next to it — matches similar_candidates.blade.php's
                                            // drawer so the bigger, more obvious label is clickable too.
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
                                                    tagElm.classList.toggle('dsf-mandatory-tag', nowChecked);

                                                    const tagData = tagify.value.find(tag => tag.value === value);
                                                    if (tagData) {
                                                        setTimeout(() => {
                                                            tagData.checked = nowChecked;
                                                            tagify.updateValueByDOMTags();
                                                            dsfUpdateActiveFiltersUI();
                                                        }, 500);
                                                    }
                                                }
                                            });

                                            // #kt_tagify_2 (the top toolbar's search box) is a plain free-text
                                            // field, deliberately NOT Tagify-wrapped and NOT synced with the
                                            // drawer's Skills tagify — it previously mirrored the drawer's skill
                                            // chips two-way (updateInputJobSearch/updateTagifyUsers/autoclickstar),
                                            // which duplicated what the Active Filters card and drawer already
                                            // show. Its one job now is free-text search, read directly by the
                                            // backend's `search` param, kept separate from structured filtering.

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
                                                        if (Array.isArray(data)) {
                                                            tagify.settings.whitelist = data; // Update whitelist
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
                                            tagify.on('add', dsfUpdateActiveFiltersUI);
                                            tagify.on('remove', dsfUpdateActiveFiltersUI);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Skills-->

                                <!--begin::Location-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Preferred Location</div>
                                            <div class="dsf-section-desc">Preferred cities or work locations</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <div class="dsf-loc-inputwrap" id="dsf_loc_inputwrap">
                                            <input class="form-control form-control-solid"
                                                value="{{ $default_loc }}" id="kt_tagify_location"
                                                placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
                                            <button type="button" class="dsf-loc-chevron" id="dsf_loc_chevron" title="Show suggestions">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        </div>
                                        <div class="d-flex mt-3">
                                            <div class="fw-semibold me-5 d-flex">
                                                <input class="form-check-input h-20px w-20px mr-2" type="checkbox"
                                                    name="communication[]" value="email" checked="checked">
                                                <div class="fs-7 text-muted">Include Candidate who prefer to relocate
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            // The DOM elements you wish to replace with Tagify
                                            var inputloc = document.querySelector("#kt_tagify_location");
                                            var cities = @json($cities);
                                            // Same blue/indigo chip + blue remove "×" as Skills (custom template
                                            // required — Tagify's default styling is plain grey otherwise).
                                            var tagifylocation = new Tagify(inputloc, {
                                                whitelist: [],
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
                                                    // appendTarget escapes .dsf-body's overflow:hidden/auto — the body
                                                    // is position:absolute (set by dsfEnforceDrawerLayout), which
                                                    // would otherwise clip Tagify's own position:absolute dropdown.
                                                    appendTarget: document.body
                                                }
                                            });
                                            // Create tags only on select
                                            tagifylocation.on('dropdown:select', function(e) {
                                                tagifylocation.addTags([e.detail.data.value]);
                                                dsfCloseLocPanel();
                                            });
                                            tagifylocation.on('add', function(e) {
                                                dsfUpdateActiveFiltersUI();
                                                dsfCloseLocPanel();
                                            });
                                            tagifylocation.on('remove', function(e) {
                                                dsfUpdateActiveFiltersUI();
                                            });

                                            function debounce(func, delay) {
                                                let timer;
                                                return function (...args) {
                                                    clearTimeout(timer);
                                                    timer = setTimeout(() => func.apply(this, args), delay);
                                                };
                                            }

                                            // Debounced function to fetch suggestions. Whitelist is refreshed
                                            // FIRST, then the dropdown is shown — showing it before the whitelist
                                            // arrives renders an empty, stuck dropdown that never repopulates.
                                            const fetchSuggestions = debounce(function(query) {
                                                $.ajax({
                                                    url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                                                    method: 'GET',
                                                    success: function(data) {
                                                        if (Array.isArray(data)) {
                                                            tagifylocation.settings.whitelist = data;
                                                            dsfCloseLocPanel();
                                                            tagifylocation.dropdown.show.call(tagifylocation, query);
                                                        } else {
                                                            console.error("Invalid API response format: Expected array.");
                                                        }
                                                    },
                                                    error: function(err) {
                                                        console.error("Error fetching cities:", err);
                                                    }
                                                });
                                            }, 200);

                                            // Listen for user input to trigger API call
                                            tagifylocation.on('input', function(e) {
                                                var query = e.detail.value.trim();
                                                if (query.length < 2) return;
                                                dsfCloseLocPanel();
                                                fetchSuggestions(query);
                                            });

                                            // $default_locations is EVERY city configured on this job (not just
                                            // the first), same source similar_candidates.blade.php's drawer uses.
                                            setTimeout(() => {
                                                var defaultLocations = {!! json_encode(@$default_locations ?? []) !!};
                                                var firstCity = defaultLocations.length ? defaultLocations[0].split(" | ")[0] : '';
                                                if (firstCity !== '') {
                                                    fetchSuggestions(firstCity);
                                                }
                                            }, 100);
                                            setTimeout(() => {
                                                var defaultLocations = {!! json_encode(@$default_locations ?? []) !!};
                                                if (defaultLocations.length) {
                                                    tagifylocation.addTags(defaultLocations);
                                                    tagifylocation.dropdown.hide();
                                                }
                                                dsfUpdateActiveFiltersUI();
                                            }, 5000);
                                        </script>

                                        <div class="dsf-loc-suggestions d-none" id="dsf_loc_panel">
                                            <div class="dsf-loc-label">Popular Locations</div>
                                            <div class="dsf-loc-chip-row">
                                                <button type="button" class="dsf-loc-chip" data-city="Bangalore">Bangalore</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Hyderabad">Hyderabad</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Pune">Pune</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Mumbai">Mumbai</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Chennai">Chennai</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Remote">Remote</button>
                                            </div>
                                            <div class="dsf-loc-label mt-2">Other Suggestions</div>
                                            <div class="dsf-loc-chip-row">
                                                <button type="button" class="dsf-loc-chip" data-city="Noida">Noida</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Gurgaon">Gurgaon</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Kolkata">Kolkata</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Ahmedabad">Ahmedabad</button>
                                                <button type="button" class="dsf-loc-chip" data-city="Chandigarh">Chandigarh</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Location-->

                                <!--begin::Education-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Education</div>
                                            <div class="dsf-section-desc">Highest qualification</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <input type="hidden" id="kt_tagify_education" value="{{ $qualification }}" />
                                        <div class="dsf-select-dd" id="dsf_edu_dd">
                                            <button type="button" class="dsf-select-dd-btn" id="dsf_edu_dd_btn">
                                                <span id="dsf_edu_dd_label">Any Education</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div class="dsf-select-dd-panel d-none" id="dsf_edu_dd_panel">
                                                <label class="dsf-select-dd-item">
                                                    <input type="checkbox" class="dsf-edu-check" value="" checked>
                                                    <span>Any</span>
                                                </label>
                                                @foreach ($qualificationTag as $qval)
                                                    <label class="dsf-select-dd-item">
                                                        <input type="checkbox" class="dsf-edu-check" value="{{ $qval }}">
                                                        <span class="dsf-edu-name">{{ $qval }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <script>
                                            // Drives the same #kt_tagify_education hidden field the backend
                                            // already reads (JSON array of {value:"..."} objects, matched
                                            // against qualification_master.name) — only the picker UI changed
                                            // from free-typed Tagify chips to a checklist dropdown.
                                            var dsfEducationSelected = (function() {
                                                var initial = @json($qualification ? [$qualification] : []);
                                                return initial.filter(Boolean);
                                            })();

                                            function dsfSyncEducationInput() {
                                                var payload = dsfEducationSelected.map(function(v) { return { value: v }; });
                                                $('#kt_tagify_education').val(JSON.stringify(payload));
                                                $('#dsf_edu_dd_label').text(dsfEducationSelected.length ? dsfEducationSelected.join(', ') : 'Any Education');
                                                dsfUpdateActiveFiltersUI();
                                            }

                                            $(document).on('change', '.dsf-edu-check', function() {
                                                var val = $(this).val();
                                                if (val === '') {
                                                    if ($(this).is(':checked')) {
                                                        $('.dsf-edu-check').not(this).prop('checked', false);
                                                        dsfEducationSelected = [];
                                                    }
                                                } else {
                                                    $('.dsf-edu-check[value=""]').prop('checked', false);
                                                    if ($(this).is(':checked')) {
                                                        if (dsfEducationSelected.indexOf(val) === -1) dsfEducationSelected.push(val);
                                                    } else {
                                                        dsfEducationSelected = dsfEducationSelected.filter(function(v) { return v !== val; });
                                                        if (dsfEducationSelected.length === 0) $('.dsf-edu-check[value=""]').prop('checked', true);
                                                    }
                                                }
                                                dsfSyncEducationInput();
                                            });

                                            $(document).on('click', '#dsf_edu_dd_btn', function() {
                                                $('#dsf_edu_dd_panel').toggleClass('d-none');
                                            });
                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#dsf_edu_dd').length) {
                                                    $('#dsf_edu_dd_panel').addClass('d-none');
                                                }
                                            });

                                            $(document).ready(dsfSyncEducationInput);
                                        </script>
                                    </div>
                                </div>
                                <!--end::Education-->

                                <!--begin::Experience-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Experience (Years)</div>
                                            <div class="dsf-section-desc">Total work experience</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="dsf-mini-label">Minimum Experience</label>
                                                <select class="form-select form-select-solid" name="min_exp"
                                                    id="min_exp">
                                                    <option value="" selected="">Min Experience</option>
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
                                            <div class="col-6">
                                                <label class="dsf-mini-label">Maximum Experience</label>
                                                <select class="form-select form-select-solid" name="max_exp"
                                                    id="max_exp">
                                                    <option value="" selected="">Max Experience</option>
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
                                    </div>
                                </div>
                                <!--end::Experience-->

                                <!--begin::Salary-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-amber">
                                            <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Salary (CTC)</div>
                                            <div class="dsf-section-desc">Salary range with currency &amp; mode</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <div class="row g-2 dsf-salary-row">
                                            <div class="col-3">
                                                <label class="dsf-mini-label">Currency</label>
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
                                            <div class="col-3">
                                                <label class="dsf-mini-label">Mode</label>
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
                                            <div class="col-3">
                                                <label class="dsf-mini-label">Min Salary</label>
                                                <input type="number"
                                                    class="form-control form-control-solid"
                                                    name="min_sal" id="min_sal" placeholder="Any"
                                                    value="0">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted minimum_salary_word"></span>
                                            </div>
                                            <div class="col-3">
                                                <label class="dsf-mini-label">Max Salary</label>
                                                <input type="number"
                                                    class="form-control form-control-solid"
                                                    name="max_sal" id="max_sal" placeholder="Any"
                                                    value="{{ $max_salary }}">
                                                <div class="fv-plugins-message-container"></div>
                                                <span class="form-text text-muted maximum_salary_word"></span>
                                            </div>
                                            <span class="form-text text-muted" id="salary_error" style="color: red !important;text-align: center;"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Salary-->

                                <!--begin::Notice Period-->
                                <div class="dsf-section">
                                    <div class="dsf-section-head">
                                        <span class="dsf-icon dsf-icon-rose">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-section-title">Notice Period / Availability</div>
                                            <div class="dsf-section-desc">Time to join</div>
                                        </div>
                                    </div>
                                    <div class="dsf-section-body">
                                        <div class="dsf-pill-row" data-radio-group="app_int_notice">

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
                                    </div>
                                <!--end::Notice Period-->

                                <!--begin::Stages-->
                                <div class="dsf-section">
                                    <div class="d-flex flex-column mb-8">
                                            <label class="form-label required">Stages</label>
                                            <div class="row py-5 mb-3">
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="0" id="app_int_status0"
                                                        checked="&quot;checked&quot;">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status0">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="8" id="app_int_status8">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status8">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Inreview</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="1" id="app_int_status1">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status1">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Sourcing</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="2" id="app_int_status2">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status2">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Screening</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="3" id="app_int_status3">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status3">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Evaluation</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="5" id="app_int_status5">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status5">
                                                        <span class="d-block fw-bold text-start">
                                                            <span class="text-dark-800 fw-bolder d-block fs-5">Offer</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 mt-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="4" id="app_int_status4">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status4">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Negotiation</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 mt-2">
                                                    <input type="radio" class="btn-check app_int_status"
                                                        name="app_int_status" value="7" id="app_int_status7">
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                                        for="app_int_status7">
                                                        <span class="d-block fw-bold text-start">
                                                            <span
                                                                class="text-dark-800 fw-bolder d-block fs-5">Rejected</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <!--end::Stages-->

                                <!--begin::Activity-->
                                <div class="dsf-section dsf-section-last">
                                    <div class="d-flex flex-column mb-8">
                                            <label class="form-label required">Activity</label>
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
                                    </div>
                                <!--end::Activity-->






                                    </form>
                                </div>
                                <!--end::Body-->

                                <!--begin::Footer-->
                                <div class="dsf-footer">
                                    <div class="dsf-footer-count">
                                        <span class="dsf-footer-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </span>
                                        <div>
                                            <div class="dsf-footer-number" id="dsf_footer_number">{{ @$existing_counter ?? 0 }}</div>
                                            <div class="dsf-footer-label">Candidates Match <span class="dsf-footer-hint" title="Updates after Preview or Apply">ⓘ</span></div>
                                        </div>
                                    </div>
                                    <div class="dsf-footer-actions">
                                        <button type="button" class="btn btn-sm btn-light" id="dsf_close_btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            Close
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light" id="dsf_reset_btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                            Reset
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" id="dsf_apply_btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                                            Apply Filters
                                        </button>
                                    </div>
                                </div>
                                <!--end::Footer-->
                            </div>
                        </div>
                        <!--End Filter Drawer-->

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5" style="position: relative;">
                      <!-- Circle loader only (Bootstrap/Metronic's own spinner-border) —
                           position:fixed + viewport-centered so it stays centered on
                           screen regardless of scroll position or row count, same
                           pattern as Similar Candidates'/Internal Bench's filter drawer. -->
                      <div class="loadingMessage" style="display: none; position: fixed; inset: 0; z-index: 1050; align-items: center; justify-content: center; background: rgba(255,255,255,.55);">
                          <div class="spinner-border text-primary" role="status" style="width: 2.75rem; height: 2.75rem;"></div>
                      </div>
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="myTable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="fw-bolder text-muted">
                                    <th class="w-10px pe-0">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input chk_list" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th class="">Candidate Name</th>
                                    {{-- <th class="">Contacts</th> --}}
                                    <th class="">Exp.</th>
                                    <th class="">Salary</th>
                                    <th class="">Notice</th>
                                    <th class="">Location</th>
                                    <th class="">Jobs</th>
                                    <th class="">Status</th>
                                    <th class="">Ownership</th>
                                    {{-- <th class="">Date</th> --}}
                                    <th class="">Stage</th>
                                    <th class="">Action</th>
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
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <script>
        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);

        $(document).on('change', '#min_sal', function() { salary_check(); });
        $(document).on('change', '#min_exp', function() { experience_check(); });
        $(document).on('change', '#max_exp', function() { experience_check(); });
        $(document).on('keyup', '#min_sal', function() {
            var number = parseInt($('#min_sal').val());
            var result = convertToWords(number);
            $('.minimum_salary_word').text(capitalizeFirstLetter(result));
        });
        $(document).on('keyup', '#max_sal', function() {
            var number = parseInt($('#max_sal').val());
            var result = convertToWords(number);
            $('.maximum_salary_word').text(capitalizeFirstLetter(result));
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
    </script>
    <script>
        var table;
        setTimeout(() => {
            // tableLoad('load');
        }, 500);

        function filtersdataget() {
            // Used by the sort dropdown's onchange — the filter drawer itself
            // now has its own Close/Reset/Apply buttons that call
            // fetchCandidates() directly; this just covers the sort-only
            // auto-apply case.
            fetchCandidates();
        }

        function tableLoad(type) {

            var jobid = '{{ $job->id }}';
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
            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',

                ajax: '{!! route('service_company.apptracker.dsimilar_existing_data') !!}?jobid=' + jobid + '&search=' + inputSearch + '&sortingfilter=' +
                    sortingfilter + '&country=' + country + '&cus_search=' + encodeURIComponent(cus_search) +
                    '&min_exp=' + min_exp + '&max_exp=' + max_exp + '&kt_tagify_location=' + kt_tagify_location +
                    '&min_sal=' + min_sal + '&max_sal=' + max_sal + '&min_salary_currency=' + min_salary_currency +
                    '&kt_tagify_education=' + kt_tagify_education + '&app_int_status=' + app_int_status +
                    '&app_int_activity=' + app_int_activity + '&app_int_notice=' + app_int_notice + '&sal_mode=' +
                    sal_mode,
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
                    // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '10%'},						
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
                        width: '5%'
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
                    {
                        data: 'Source',
                        name: 'Source',
                        orderable: false
                    },
                    {
                        data: 'Date',
                        name: 'Date',
                        orderable: false
                    },

                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        orderable: false
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        orderable: false
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
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
            countexist();

        }

        function countexist() {
            setTimeout(() => {
                // var rowCount = $('#myTable tbody tr:not(.dataTables_empty)').length;
                var allRowCount = $('#myTable tbody tr').length;

                // Count rows that have a cell with the class dataTables_empty
                var emptyRowCount = $('#myTable tbody tr:has(td.dataTables_empty)').length;
                var existingDataCount = allRowCount - emptyRowCount;
                $('.existing_counter').text(existingDataCount);


            }, 3500);
        }

        // Small top-right toast, auto-dismissing, pause-on-hover — SweetAlert2's
        // own documented toast mode. Only non-chained success/fail notices are
        // converted to this; the "Are you sure?" confirmation below stays a
        // real blocking modal since it gates the actual AJAX call.
        const dsfToast = Swal.mixin({
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

        $(document).on('click', '.add_to_inreview', function() {

            var id = $(this).data('row-id');
            var source_from_type = $(this).data('row-source_from_type');
            var source_jobid = $(this).data('row-jobid');
            var source_tab = $(this).data('row-from');
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

                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            'id': id,
                            'source_from_type': source_from_type,
                            'source_jobid': source_jobid,
                            'source_tab': source_tab
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                if (response.status == 'coinCheck') {
                                    dsfToast.fire({ icon: 'warning', title: response.message });
                                    return false;
                                }

                                // tableLoad('load');
                                var page = $('#paginationcandidate .active_disabled').data('page');
                                fetchCandidates(page);
                                dsfToast.fire({ icon: 'success', title: 'Selected request has been In-Review' });
                            }
                        }
                    });



                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    dsfToast.fire({ icon: 'error', title: 'Selected request is safe' });
                }
            })
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

            var jobid = '{{ $job->id }}';
            // #inputSearch is a hidden, never-set field — the real free-text
            // search box is #kt_tagify_2 (see the dsf-toolbar-card above),
            // same fix already applied on Similar Candidates/Internal Bench.
            var inputSearch = $('#kt_tagify_2').val();
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
            
            $('.loadingMessage').css({ display: 'flex' });
            $.ajax({
                url: `{!! route('service_company.apptracker.dsimilar_existing_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  jobid:jobid,
                  search:inputSearch,
                  sortingfilter:sortingfilter ,
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
                  app_int_notice:app_int_notice,
                  sal_mode: sal_mode
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    if (!response || typeof response.html === 'undefined') {
                        console.error('dsimilar_existing_data_ajax: unexpected response', response);
                        return;
                    }
                    $('#candidatetList').html(response.html);
                    $('#similar_counter').html(response.total);
                    $('#dsf_footer_number').text(response.total);
                    $('#paginationcandidate').html(response.pagination);
                    // The header card's hint (see clientappHeader.blade.php)
                    // says "job defaults" by default — correct everywhere
                    // EXCEPT here, since this specific number IS the live,
                    // current-filter result once fetched.
                    $('#apth_hint_similar_counter').attr('title',
                        'This is your current filter selection on this page — the same card on other tabs shows this job\'s default count instead.');
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    console.error('dsimilar_existing_data_ajax failed:', xhr.status, xhr.responseText);
                }
            });
        }

        // Free-text search box: Enter re-runs the same fetchCandidates() call
        // the sort dropdown and pagination already use — no separate endpoint.
        $(document).on('keydown', '#kt_tagify_2', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                fetchCandidates();
            }
        });

        // Custom Sort dropdown — reads options straight from the real
        // #sortingfilter <select> (single source of truth), keeps that
        // select's value in sync so filtersdataget()/fetchCandidates() are
        // unaffected, and only replaces how the option list is PRESENTED.
        function dsfRenderSortPanel() {
            var $panel = $('#dsf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="dsf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#dsf_sort_label').text(label);
                        dsfCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function dsfOpenSortPanel() {
            dsfRenderSortPanel();
            $('#dsf_sort_panel').removeClass('d-none');
            $('#dsf_sort_btn').addClass('open');
        }
        function dsfCloseSortPanel() {
            $('#dsf_sort_panel').addClass('d-none');
            $('#dsf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#dsf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#dsf_sort_panel').hasClass('d-none')) dsfOpenSortPanel();
            else dsfCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#dsf_sort_wrap').length) dsfCloseSortPanel();
        });
    </script>
    <script>
        /* Drawer width = 80% of the actual content area, same measurement
           approach as Similar Candidates'/Internal Bench's filter drawer. */
        function dsfSetDrawerWidth() {
            var wrapper = document.getElementById('kt_wrapper');
            var contentWidth = wrapper ? wrapper.getBoundingClientRect().width : window.innerWidth;
            if (!contentWidth) contentWidth = window.innerWidth;
            var drawerWidth = Math.round(contentWidth * 0.8);
            var drawerEl = document.getElementById('kt_dsimilar_filter_drawer');
            if (drawerEl) {
                drawerEl.style.width = drawerWidth + 'px';
            }
        }
        $(document).ready(dsfSetDrawerWidth);
        $(window).on('resize', dsfSetDrawerWidth);
        $(document).on('click', '#kt_dsimilar_filter_toggle', dsfSetDrawerWidth);

        /* Forces the drawer's header/body/footer into a fixed viewport-bound
           box via genuine inline !important styles — Metronic's base .drawer
           class ships its own non-!important "overflow: auto" loaded after
           this page's inline <style> block, so stylesheet-only overrides
           aren't reliable here. */
        function dsfEnforceDrawerLayout() {
            var drawerEl = document.getElementById('kt_dsimilar_filter_drawer');
            var cardEl = document.getElementById('dsf_card');
            var bodyEl = cardEl ? cardEl.querySelector('.dsf-body') : null;
            var headerEl = cardEl ? cardEl.querySelector('.dsf-header') : null;
            var footerEl = cardEl ? cardEl.querySelector('.dsf-footer') : null;
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
        $(document).ready(dsfEnforceDrawerLayout);
        $(window).on('resize', dsfEnforceDrawerLayout);
        $(document).on('click', '#kt_dsimilar_filter_toggle, #kt_dsimilar_filter_close', dsfEnforceDrawerLayout);

        /* ------------------------------------------------------------------
           Filter drawer glue: popular-location quick-add chips, the Active
           Filters chip bar, and the Close/Reset/Apply footer buttons.
        ------------------------------------------------------------------ */
        $(document).on('click', '.dsf-loc-chip', function() {
            var city = $(this).data('city');
            var $btn = $(this);
            if (typeof tagifylocation === 'undefined' || !city) return;
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
                        tagifylocation.addTags([match]);
                        dsfCloseLocPanel();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        function dsfOpenLocPanel() {
            $('#dsf_loc_panel').removeClass('d-none');
            $('#dsf_loc_chevron').addClass('open');
        }
        function dsfCloseLocPanel() {
            $('#dsf_loc_panel').addClass('d-none');
            $('#dsf_loc_chevron').removeClass('open');
        }
        $(document).ready(function() {
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.on('focus', dsfOpenLocPanel);
            }
        });
        $(document).on('click', '#dsf_loc_chevron', function() {
            if ($('#dsf_loc_panel').hasClass('d-none')) {
                dsfOpenLocPanel();
            } else {
                dsfCloseLocPanel();
            }
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#dsf_loc_inputwrap, #dsf_loc_panel').length) {
                dsfCloseLocPanel();
            }
        });

        // Notice/Stages/Activity kept their original btn-check/btn-outline-dashed
        // markup (not restyled to the .dsf-pill look) — the label text sits in a
        // nested <span class="text-dark-800"> whose source is line-wrapped, so
        // .text() alone returns runs of internal whitespace/newlines; collapse
        // them to single spaces for a clean chip label.
        function dsfPillLabel($checkedInput) {
            var raw = $checkedInput.closest('.col-lg-2, .col-lg-3, .col-lg-4').find('.text-dark-800').text();
            return $.trim(raw).replace(/\s+/g, ' ');
        }

        function dsfUpdateActiveFiltersUI() {
            var chips = [];

            // Skills (starred = mandatory)
            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    chips.push({
                        label: tag.value,
                        mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }

            // Location
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.value.forEach(function(tag) {
                    chips.push({ label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }

            // Education — label is displayed uppercase (education data
            // convention, see docs/UI_UX_STANDARDS.md); val itself stays
            // exact-case since it's what the clear() lookup and the
            // backend payload match against.
            if (typeof dsfEducationSelected !== 'undefined') {
                dsfEducationSelected.slice().forEach(function(val) {
                    chips.push({
                        label: val.toUpperCase(),
                        clear: function() {
                            $('.dsf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
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

            // Notice / Stage / Activity — only show when not the default "Any"/"All" pill
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                chips.push({
                    label: dsfPillLabel(noticeEl) || noticeEl.val(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                chips.push({
                    label: dsfPillLabel(statusEl) || statusEl.val(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                chips.push({
                    label: dsfPillLabel(activityEl) || activityEl.val(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }

            var $chips = $('#dsf_active_chips').empty();
            chips.forEach(function(c, idx) {
                var $chip = $('<span class="dsf-chip"></span>')
                    .toggleClass('mandatory', !!c.mandatory);
                if (c.mandatory) {
                    $chip.append($('<span style="color:#eab308;"></span>').text('★ ')).append($('<span></span>').text(c.label));
                } else {
                    $chip.append($('<span></span>').text(c.label));
                }
                $chip
                    .append($('<span class="dsf-chip-remove">&times;</span>').on('click', function() {
                        c.clear();
                        dsfUpdateActiveFiltersUI();
                    }));
                $chips.append($chip);
            });

            var n = chips.length;
            $('#dsf_active_bar').toggleClass('d-none', n === 0);
            $('#dsf_active_count').text('(' + n + ')');
            $('#dsf_header_count').text(n);
            $('#dsf_count_badge').toggleClass('d-none', n === 0).text(n);
            $('#dsf_filter_count_text').text(n > 0 ? ' (' + n + ')' : '');
        }

        /* ------------------------------------------------------------------
           Applied Filters summary card (main page, above the results table).
           Reflects only the last-APPLIED state, rendered on Apply/Reset and
           on initial load — reads the same underlying sources as
           dsfUpdateActiveFiltersUI() so the two never disagree.
        ------------------------------------------------------------------ */
        var DSF_APPLIED_VISIBLE_CAP = 8;
        var dsfAppliedExpanded = false;

        function dsfCategoryIcon(cat) {
            var paths = {
                skill: '<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>',
                location: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                education: '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"/>',
                experience: '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                notice: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
                status: '<circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/>',
                activity: '<path d="M3 3v18h18M7 14l4-4 3 3 5-6"/>'
            };
            if (cat === 'salary') {
                return '<span style="font-size:11px;font-weight:700;line-height:1;">₹</span>';
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' + (paths[cat] || '') + '</svg>';
        }

        function dsfBuildAppliedItems() {
            var items = [];

            if (typeof tagify !== 'undefined') {
                tagify.value.forEach(function(tag) {
                    items.push({
                        cat: 'skill', label: tag.value, mandatory: !!tag.checked,
                        clear: function() { tagify.removeTags(tag.value); }
                    });
                });
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.value.forEach(function(tag) {
                    items.push({ cat: 'location', label: tag.value, clear: function() { tagifylocation.removeTags(tag.value); } });
                });
            }
            if (typeof dsfEducationSelected !== 'undefined') {
                dsfEducationSelected.slice().forEach(function(val) {
                    items.push({
                        cat: 'education', label: val,
                        clear: function() {
                            $('.dsf-edu-check[value="' + val.replace(/"/g, '\\"') + '"]').prop('checked', false).trigger('change');
                        }
                    });
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
            var noticeEl = $('[name="app_int_notice"]:checked');
            if (noticeEl.val() && noticeEl.val() !== '1') {
                items.push({
                    cat: 'notice', label: dsfPillLabel(noticeEl) || noticeEl.val(),
                    clear: function() { $('#app_int_notice1').prop('checked', true); }
                });
            }
            var statusEl = $('[name="app_int_status"]:checked');
            if (statusEl.val() && statusEl.val() !== '0') {
                items.push({
                    cat: 'status', label: dsfPillLabel(statusEl) || statusEl.val(),
                    clear: function() { $('#app_int_status0').prop('checked', true); }
                });
            }
            var activityEl = $('[name="app_int_activity"]:checked');
            if (activityEl.val() && activityEl.val() !== '1') {
                items.push({
                    cat: 'activity', label: dsfPillLabel(activityEl) || activityEl.val(),
                    clear: function() { $('#app_int_activity1').prop('checked', true); }
                });
            }
            return items;
        }

        // Removing a chip from this card acts on already-APPLIED filters, so
        // (unlike drawer edits) it re-fetches immediately instead of waiting
        // for an Apply click, and refreshes the drawer's own state/snapshot
        // so reopening the drawer shows the same, now-current picture.
        function dsfAppliedChipRemoved() {
            fetchCandidates();
            if (typeof dsfUpdateActiveFiltersUI === 'function') dsfUpdateActiveFiltersUI();
            if (typeof dsfCaptureSnapshot === 'function') dsfCaptureSnapshot();
            dsfRenderAppliedFiltersCard();
        }

        function dsfRenderAppliedFiltersCard() {
            var items = dsfBuildAppliedItems();
            var $chips = $('#dsf_applied_chips').empty();
            var visible = dsfAppliedExpanded ? items : items.slice(0, DSF_APPLIED_VISIBLE_CAP);

            visible.forEach(function(it) {
                var $chip = $('<span class="dsf-applied-chip"></span>').addClass('cat-' + it.cat);
                $chip.append('<span class="ic">' + dsfCategoryIcon(it.cat) + '</span>');
                if (it.mandatory) {
                    $chip.append($('<span class="star"></span>').text('★'));
                }
                $chip.append($('<span></span>').text(it.label));
                $chip.append(
                    $('<span class="dsf-applied-chip-remove">&times;</span>').on('click', function() {
                        it.clear();
                        dsfAppliedChipRemoved();
                    })
                );
                $chips.append($chip);
            });

            if (!dsfAppliedExpanded && items.length > DSF_APPLIED_VISIBLE_CAP) {
                var remaining = items.length - DSF_APPLIED_VISIBLE_CAP;
                $chips.append(
                    $('<span class="dsf-applied-more"></span>').text('+' + remaining + ' More').on('click', function() {
                        dsfAppliedExpanded = true;
                        dsfRenderAppliedFiltersCard();
                    })
                );
            }

            $('#dsf_applied_card').toggleClass('d-none', items.length === 0);
            $('#dsf_applied_count').text(items.length);
        }

        $(document).on('click', '#dsf_applied_clear_all', function() {
            $('#dsf_clear_all').trigger('click');
            fetchCandidates();
            if (typeof dsfCaptureSnapshot === 'function') dsfCaptureSnapshot();
            dsfAppliedExpanded = false;
            dsfRenderAppliedFiltersCard();
        });

        $(document).on('change', '[name="app_int_notice"], [name="app_int_status"], [name="app_int_activity"]', function() {
            dsfUpdateActiveFiltersUI();
            // Selecting a Notice/Stage/Activity pill can add a chip to the Active
            // Filters bar, changing its height — re-assert the drawer's layout so
            // the flex column recalculates and the footer/body stay correctly
            // sized instead of drifting after that reflow.
            if (typeof dsfEnforceDrawerLayout === 'function') dsfEnforceDrawerLayout();
        });
        $(document).on('change keyup', '#min_exp, #max_exp, #min_sal, #max_sal', dsfUpdateActiveFiltersUI);

        $(document).on('click', '#dsf_clear_all', function() {
            if (typeof tagify !== 'undefined') tagify.removeAllTags();
            if (typeof tagifylocation !== 'undefined') tagifylocation.removeAllTags();
            $('.dsf-edu-check').prop('checked', false);
            $('.dsf-edu-check[value=""]').prop('checked', true);
            dsfEducationSelected = [];
            dsfSyncEducationInput();
            $('#min_exp, #max_exp, #max_sal, #min_salary_currency, #sal_mode').val('');
            $('#min_sal').val(0);
            $('#app_int_notice1, #app_int_status0, #app_int_activity1').prop('checked', true);
            dsfUpdateActiveFiltersUI();
        });

        $(document).on('click', '#dsf_reset_btn', function() {
            $('#dsf_clear_all').trigger('click');
            fetchCandidates();
            // Reset is an explicit apply of the cleared state, so make the
            // cleared filters the new baseline — otherwise closing the drawer
            // afterwards would "restore" the pre-reset values.
            dsfCaptureSnapshot();
            dsfAppliedExpanded = false;
            dsfRenderAppliedFiltersCard();
        });

        /* ------------------------------------------------------------------
           Draft-until-Apply behaviour.
           Nothing the user changes inside the drawer takes effect until they
           click "Apply Filters". If they close the drawer by any other means
           (Close button, the header X, the overlay, or ESC) their unsaved
           edits are discarded and every field reverts to the last-applied
           state. We snapshot on open (kt.drawer.shown) and restore on close
           (kt.drawer.after.hidden) using Metronic's own drawer lifecycle
           events, so overlay/ESC closes are covered too — not just the
           buttons we control.
        ------------------------------------------------------------------ */
        var dsfSnapshot = null;
        var dsfApplying = false;

        function dsfCaptureSnapshot() {
            dsfSnapshot = {
                skills: (typeof tagify !== 'undefined')
                    ? tagify.value.map(function(t) { return { value: t.value, checked: !!t.checked }; }) : [],
                locations: (typeof tagifylocation !== 'undefined')
                    ? tagifylocation.value.map(function(t) { return t.value; }) : [],
                education: (typeof dsfEducationSelected !== 'undefined') ? dsfEducationSelected.slice() : [],
                min_exp: $('#min_exp').val(), max_exp: $('#max_exp').val(),
                min_sal: $('#min_sal').val(), max_sal: $('#max_sal').val(),
                min_salary_currency: $('#min_salary_currency').val(), sal_mode: $('#sal_mode').val(),
                notice: $('[name="app_int_notice"]:checked').val(),
                status: $('[name="app_int_status"]:checked').val(),
                activity: $('[name="app_int_activity"]:checked').val()
            };
        }

        function dsfRestoreSnapshot() {
            if (!dsfSnapshot) return;
            if (typeof tagify !== 'undefined') {
                tagify.removeAllTags();
                if (dsfSnapshot.skills.length) tagify.addTags(dsfSnapshot.skills);
            }
            if (typeof tagifylocation !== 'undefined') {
                tagifylocation.removeAllTags();
                if (dsfSnapshot.locations.length) tagifylocation.addTags(dsfSnapshot.locations);
            }
            dsfEducationSelected = dsfSnapshot.education.slice();
            $('.dsf-edu-check').prop('checked', false);
            if (dsfEducationSelected.length === 0) {
                $('.dsf-edu-check[value=""]').prop('checked', true);
            } else {
                dsfEducationSelected.forEach(function(v) {
                    $('.dsf-edu-check[value="' + String(v).replace(/"/g, '\\"') + '"]').prop('checked', true);
                });
            }
            dsfSyncEducationInput();
            $('#min_exp').val(dsfSnapshot.min_exp);
            $('#max_exp').val(dsfSnapshot.max_exp);
            $('#min_sal').val(dsfSnapshot.min_sal);
            $('#max_sal').val(dsfSnapshot.max_sal);
            $('#min_salary_currency').val(dsfSnapshot.min_salary_currency);
            $('#sal_mode').val(dsfSnapshot.sal_mode);
            $('.minimum_salary_word, .maximum_salary_word').text('');
            if (dsfSnapshot.notice) $('[name="app_int_notice"][value="' + dsfSnapshot.notice + '"]').prop('checked', true);
            if (dsfSnapshot.status) $('[name="app_int_status"][value="' + dsfSnapshot.status + '"]').prop('checked', true);
            if (dsfSnapshot.activity) $('[name="app_int_activity"][value="' + dsfSnapshot.activity + '"]').prop('checked', true);
            dsfUpdateActiveFiltersUI();
        }

        function dsfBindDrawerLifecycle() {
            var el = document.getElementById('kt_dsimilar_filter_drawer');
            if (!el || typeof KTDrawer === 'undefined' || !KTDrawer.getInstance) {
                return setTimeout(dsfBindDrawerLifecycle, 300);
            }
            var inst = KTDrawer.getInstance(el);
            if (!inst) { return setTimeout(dsfBindDrawerLifecycle, 300); }
            inst.on('kt.drawer.shown', function() {
                dsfApplying = false;
                dsfCaptureSnapshot();
                dsfEnforceDrawerLayout();
                setTimeout(dsfEnforceDrawerLayout, 50);
            });
            inst.on('kt.drawer.after.hidden', function() {
                if (!dsfApplying) dsfRestoreSnapshot();
                dsfApplying = false;
            });
        }
        $(document).ready(dsfBindDrawerLifecycle);

        $(document).on('click', '#dsf_close_btn', function() {
            $('#kt_dsimilar_filter_close').trigger('click');
        });
        $(document).on('click', '#dsf_apply_btn', function() {
            dsfApplying = true;
            fetchCandidates();
            dsfAppliedExpanded = false;
            dsfRenderAppliedFiltersCard();
            $('#kt_dsimilar_filter_close').trigger('click');
        });

        $(document).ready(function() {
            setTimeout(dsfUpdateActiveFiltersUI, 600);
            setTimeout(dsfCaptureSnapshot, 800);
            setTimeout(dsfRenderAppliedFiltersCard, 700);
        });
    </script>
@endpush
