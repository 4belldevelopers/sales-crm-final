@extends('layouts.service_company_layout')
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
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
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

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesApplication {
                width: 1200px;
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
            #kt_activitiesClient {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesClient {
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

        /* ============================================================
           CV Tracker — Hero KPI stats
           Data source: Elasticsearch only, riding the existing
           allcandidates() fetch (no extra request, no MySQL load).
           Poppins per UI standard, 14px hard cap.
        ============================================================ */
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

        /* Stage tabs — own card, full-width stretch row (each tab takes an equal share
           of the row, edge to edge) with icon + label on one line and the count below.
           Colors match the app's own "Pipeline Stages" legend
           (resources/views/sections/service_company/pipeline.blade.php) exactly, so a
           given stage reads as the same color everywhere. Inactive tabs stay neutral
           gray; a tab only takes on its stage color once it's the active/selected tab. */
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

        /* Search + filter — own card, trimmed to hug the search/sort/filter row only.
           The Sort <select> (form-select-lg) and Filter button (py-4 utility) are taller
           than the search input itself and were dictating the whole row's height — both
           get scoped down here to match. */
        .cvh-search-card { margin-bottom: 14px; }
        .cvh-search-card .card-body { padding: 10px 14px; }
        .cvh-search-card .custm-bg { margin: 0 !important; }
        .cvh-search-card .form-control,
        .cvh-search-card .form-select-lg { padding-top: .55rem !important; padding-bottom: .55rem !important; font-size: 13px !important; }
        .cvh-search-card .btn.py-4 { padding-top: .55rem !important; padding-bottom: .55rem !important; }

        /* Selected-rows action bar */
        .cvh-selection-bar { display: none; align-items: center; gap: 14px; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; padding: 10px 16px; margin-bottom: 14px; font-family: 'Poppins', sans-serif; flex-wrap: wrap; }
        .cvh-selection-bar.show { display: flex; }
        .cvh-selection-count { font-size: 13px; font-weight: 600; color: #334155; white-space: nowrap; }
        .cvh-selection-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .cvh-selection-actions .btn { font-size: 12px; font-weight: 500; background: #fff !important; border: 1px solid #bfdbfe !important; border-radius: 8px !important; padding: 6px 14px !important; box-shadow: none !important; }
        .cvh-selection-actions .btn:hover { background: #eff6ff !important; border-color: #93c5fd !important; }
        .cvh-selection-close { width: 28px; height: 28px; border-radius: 50%; border: none; background: transparent; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cvh-selection-close:hover { background: #f1f5f9; color: #334155; }

        /* The original Mass Email/Fwd Resume/Copy/Export buttons stay in the DOM (so all
           existing modal/ajax click handlers keep firing unmodified) but are permanently
           hidden here — their restyled equivalents now live in #cvh_selection_bar. */
        .cvh-legacy-hide { display: none !important; }

    </style>
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);"
                            class="text-primary-800">All Candidates</a> </h5>
                    <span class="text-gray-600" id="total_record"></span>
                    {{-- {{ @$applicationCounter }} --}}
                </div>
            </div>
            <div class="d-flex align-items-center py-1">
                <!--  <a href="javascript:void(0);" class="me-3 daterange"><span class="svg-icon svg-icon-primary svg-icon-1x">-->
                <input type="hidden" class="form-control form-control-solid" placeholder="Pick date rage"
                    id="kt_daterangepicker" />
                <!--</span> </a>-->

                {{-- Mass Email / Fwd Resume / Copy / Export now live in #cvh_selection_bar
                     (a dedicated selection card shown on row selection, styled per the
                     approved design) — see just above the candidates table. Classes and
                     data-target attributes are unchanged so every existing modal/ajax
                     handler still fires from their new location. --}}
                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary mass_mail fw-bolder me-3 d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#mass_mail"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                    fill="#000000" opacity="0.3" />
                                <path
                                    d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                    fill="#000000" />
                            </g>
                        </svg>
                    </span> Mass Email </a>


                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fwd_resume fw-bolder me-3 d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#fwd_resume"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path
                                    d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z"
                                    fill="#000000" opacity="0.3" />
                                <path
                                    d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                    fill="#000000" />
                            </g>
                        </svg>
                    </span> Fwd Resume </a>
                {{-- <a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 send_email d-none" data-toggle="modal" data-target="#send_email"> <span class="svg-icon svg-icon-primary svg-icon-1x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <rect x="0" y="0" width="24" height="24"/>
          <path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
          <path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
        </g>
      </svg>
      </span> Send Email </a> --}}
                <a href="javascript:void(0);" type="button"
                    class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 application_copy d-none cvh-legacy-hide" data-toggle="modal"
                    data-target="#application_copy">
                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                            height="24px" viewBox="0 0 24 24" version="1.1">
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
                    Copy </a>

                <div class="before-event downloadexcell d-flex align-items-center position-relative my-1 me-3 d-none cvh-legacy-hide">
                   
                    <button type="button"
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 exportRecord"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" style="width: 100px;">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                    fill="black" />
                            </svg>
                        </span>
                        Export </button>
                </div>

                <div class="before-event d-flex align-items-center position-relative my-1 me-3 custom_hide">

                    <input type="hidden" id="inputSearch" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;"
                        placeholder="Search..." />
                </div>




                <div class="card-toolbar">
                    <div class="before-event d-flex justify-content-end" data-kt-user-table-toolbar="base">

                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-user-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Role:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="role" data-hide-search="true">
                                        <option></option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Analyst">Analyst</option>
                                        <option value="Developer">Developer</option>
                                        <option value="Support">Support</option>
                                        <option value="Trial">Trial</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Select option" data-allow-clear="true"
                                        data-kt-user-table-filter="two-step" data-hide-search="true">
                                        <option></option>
                                        <option value="Enabled">Enabled</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-bold px-6"
                                        data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!--Start Popup-->
     <div class="modal fade show" id="apptrackerfilter" tabindex="-1" style="">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Filter</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="apptrackerfilter_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                    rx="1" transform="rotate(-45 6 17.3137)"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)"></rect>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="apptrackerfilter_body_content" id="">
                    <div class="modal-body" style="padding: 2em;">
                        <form id="createSchedule" class="ajax-form" method="post">
                            <div class="d-flex flex-column mb-8">
                                <label class="form-label required">Enter skills to filter candidate</label>
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
                                                return `
                                            <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                                                <x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x>
                                                <div>
                                                    <span class="star ${isChecked}">🟌</span>
                                                    <span class="tagify__tag-text">${value}</span>
                                                </div>
                                            </tag>
                                        `;
                                            }
                                        },
                                        transformTag: function(tagData) {
                                           if (!tagData.hasOwnProperty('checked')) {
                                                tagData.checked = true; // ⭐ auto select
                                            }
                                            return tagData;
                                        }
                                    });

                                    tagify.on('add', function(e) {

                                        const tagElm = e.detail.tag;

                                        setTimeout(() => {

                                            // get correct data reference from Tagify
                                            const tagData = tagify.getSetTagData(tagElm);

                                            if (tagData) {
                                                tagData.checked = false; // ✅ update real data
                                            }

                                            // update UI
                                            const star = tagElm.querySelector('.star');
                                            if (star) {
                                                star.classList.remove('checked');
                                            }

                                            // sync everything
                                            tagify.updateValueByDOMTags();

                                        }, 200);

                                    });

                                    // ⭐ CLICK STAR TO TOGGLE
                                    document.addEventListener('click', function(e) {

                                        if (e.target.classList.contains('star')) {

                                            const tagElm = e.target.closest('.tagify__tag');

                                            // toggle UI
                                            e.target.classList.toggle('checked');

                                            // get correct tag data
                                            const tagData = tagify.getSetTagData(tagElm);

                                            if (tagData) {
                                                tagData.checked = e.target.classList.contains('checked');
                                                tagify.updateValueByDOMTags();
                                            }
                                        }
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
                                    // new Tagify(inputElm);   
                                </script>
                                <!--<textarea class="form-control form-control-solid" rows="4" name=""
                                    placeholder="Enter your Skills/Keywords..."></textarea>-->
                            </div>

                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                <div class="d-flex flex-column mb-3 fv-row">
                                    <label class="form-label">Select location for candidate</label>
                                    <input class="form-control form-control-solid"
                                        value="" id="kt_tagify_location"
                                        placeholder="Enter your Location..." />
                                    <!--<input class="form-control form-control-solid" value="" name="tags" placeholder="Enter your Location...">-->
                                    <script>
                                        // The DOM elements you wish to replace with Tagify
                                        var inputloc = document.querySelector("#kt_tagify_location");
                                        var cities = @json($cities);
                                        // Initialize Tagify script on the above inputs
                                        var tagifylocation = new Tagify(inputloc, {
                                            whitelist: [],
                                            // enforceWhitelist: true, // Prevents users from adding custom values  
                                            maxTags: 10,
                                            dropdown: {
                                                maxItems: 20, // <- mixumum allowed rendered suggestions
                                                classname: "", // <- custom classname for this dropdown, so it could be targeted
                                                enabled: 0, // <- show suggestions on focus
                                                closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
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
                                            // alert(cities);
                                            // Remove the tag if it wasn't selected from the dropdown
                                            // if (!cities.includes(e.detail.data.value)) {
                                            //     setTimeout(() => {
                                            //         tagifylocation.removeTag(e.detail.data.value);
                                            //     }, 500);

                                            // }
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

                                        setTimeout(() => {
                                        var city = "{{@$default_loc}}";
                                        if(city !=''){
                                            var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
                                            fetchSuggestions(cityName);
                                        }
                                        }, 100);
                                        setTimeout(() => {
                                            var defaultLocation = {!! json_encode(@$default_loc) !!}; // Ensure proper Blade syntax for JavaScript
                            
                                            if (defaultLocation) {
                                                tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
                                                tagifylocation.dropdown.hide();
                                            }
                                        }, 5000); // 3 seconds delay

                                    </script>
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-8">
                                <label class="form-label">Education</label>


                                <input class="form-control form-control-solid" value=""
                                    id="kt_tagify_education" placeholder="About Education..." />
                                <!--<input class="form-control form-control-solid" value="" name="tags" placeholder="Enter your Location...">-->
                                <script>
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
                                </script>
                            </div>



                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                <div class="col-lg-6">
                                    <label class="form-label required">Minimum Experience in years:</label>
                                    <select class="form-select form-select-solid" name="min_exp"
                                        id="min_exp">
                                        <option value="" selected="">Select
                                            Experience</option>
                                        <option value="0">Freshers
                                        </option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}
                                                &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label required">Maximum Experience in years:</label>
                                    <select class="form-select form-select-solid" name="max_exp"
                                        id="max_exp">
                                        <option value="" selected="">Select
                                            Experience</option>
                                        <option value="0">Freshers
                                        </option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}
                                                &nbsp; Year{{ $i >= 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                </div>
                            </div>

                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Currency</label>
                                                    <select class="form-select form-select-solid"
                                                        arial-label="Select Currency" name="min_salary_currency"
                                                        id="min_salary_currency">
                                                        <option value="">Select currency</option>
                                                        <option value="0">INR</option>
                                                        <option value="1">EUR</option>
                                                        <option value="2">JPY</option>
                                                        <option value="3">GBP</option>
                                                        <option value="4">USD</option>
                                                        <option value="5">AED</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Mode</label>
                                                    <select class="form-select form-select-solid"
                                                        arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                        <option value="">Select Mode</option>
                                                        <option value="0">Annual</option>
                                                        <option value="1">Monthly</option>
                                                        <option value="2">Hourly</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Minimum Salary:</label>
                                                    <input type="number"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="min_sal" id="min_sal" placeholder="Enter min salary"
                                                        value="">
                                                    <div class="fv-plugins-message-container"></div>
                                                    <span class="form-text text-muted minimum_salary_word"></span>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="form-label required">Maximum Salary:</label>
                                                    <input type="number"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="max_sal" id="max_sal" placeholder="Enter max salary"
                                                        value="">
                                                    <div class="fv-plugins-message-container"></div>
                                                </div>
                                                <span class="form-text text-muted" id="salary_error" style="    color: red !important;text-align: center;"></span>
                                            </div>
                                        </div>




                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                                <label class="form-label required">Notice Period/Availability to join</label>
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
                                            name="app_int_status" value="6" id="app_int_status6">
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                            for="app_int_status6">
                                            <span class="d-block fw-bold text-start">
                                                <span class="text-dark-800 fw-bolder d-block fs-5">Joined</span>
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







                        </form>
                    </div>
                    <!--<div class="modal-footer" style="display: table;">-->
                    <!--  <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal">Close <i class="fa fa-circle fa-sm"></i> </button>-->
                    <!--  <button type="button" class="btn btn-light-success btn-sm p-1" id="" onclick="newUpdateJobstatus()">Update <i class="fa fa-spinner fa-sm"></i> </button>-->
                    <!--</div>-->
                    <div class="modal-footer flex-center">
                        <div class="div1">
                            <button type="button" id="" class="btn btn-secondary me-3"
                                data-dismiss="modal">Close</button>
                            <button type="button" id="changetextupdate" class="btn btn-primary"
                                onclick="filtersdataget()">
                                <span class="indicator-label changetextupdate">Submit</span></button>
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
        </div>
    </div>
    <!--End Popup-->


    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                {{-- ── Hero KPI stats (ES-only, seeded server-side, refreshed live) ── --}}
                @php
                    $cvHeroCards = [
                        ['key'=>'total','label'=>'Total Candidates','bg'=>'#dbeafe','ic'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['key'=>'today','label'=>'Added Today','bg'=>'#dcfce7','ic'=>'#16a34a','tone'=>'info','text'=>'Since 12:00 AM',
                         'icon'=>'M12 4.5v15m7.5-7.5h-15'],
                        ['key'=>'this_week','label'=>'Added This Week','bg'=>'#ede9fe','ic'=>'#7c3aed','tone'=>'info','text'=>'Mon &ndash; Today',
                         'icon'=>'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
                        ['key'=>'immediate','label'=>'Immediate Joiners','bg'=>'#d1fae5','ic'=>'#059669','tone'=>'up','text'=>'Notice = 0 Days',
                         'icon'=>'M13 2 3 14h7l-1 8 10-12h-7l1-8Z'],
                        ['key'=>'without_resume','label'=>'Without Resume','bg'=>'#fee2e2','ic'=>'#dc2626','tone'=>'warn','text'=>'Needs Attention',
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

                {{-- ── Search + Filter (separate card) ── --}}
                <div class="card cvh-search-card">
                <div class="card-body">
                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                    <div class="col-lg-4 d-flex align-items-center px-2" style="width:65%;position: relative;">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                            value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                            <div style="position: absolute; top: 0px; right: 36px;">
                               <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                            </div>

                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="sortingfilter" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdataget()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Upload Date)</option>
                            <option value="2">Oldest to Newest (Upload Date)</option>
                            <!--<option value="3">Low to High (Salary)</option>-->
                            <!--<option value="4">High to Low (Salary)</option>-->
                            <!--<option value="9">Notice Period (Immediate)</option>-->
                            <option value="21">Notice Period (Immediate)</option>
                            <option value="22">Notice Period (Serving)</option>
                            <option value="7">High to Low (Experience) </option>
                            <option value="8">Low to High (Experience)</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                        <button type="button"
                            class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end"
                            data-toggle="modal" data-target="#apptrackerfilter">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                        fill="black"></path>
                                </svg>
                            </span>
                            Filter
                        </button>
                    </div>

                    <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="country" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" data-kt-table-widget-3="filter_status"
                            style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                            <option value="" selected="selected">Location</option>
                            @if (@$countries)
                                @foreach ($countries as $val_co)
                                    <option value="{{ $val_co->id }}">{{ $val_co->country_name }}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
                        <button type="button"
                            class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                        fill="black"></path>
                                </svg>
                            </span>
                            Filter
                        </button>
                    </div>
                </div>
                </div>
                </div>

                {{-- ── Stage Tabs (separate card, ES-backed counts) — sits directly above
                     the candidate list, below the search/filter bar. ── --}}
                @php
                    // Tab key => the ID(s) sent to the backend as `stage_tab` (comma-separated
                    // for "Rejected", which spans two master_status rows both named "Rejected"),
                    // the matching key in $candidateStats['tabs'] for the live badge count, an
                    // SVG icon path (inline, not an icon-font class — Keenicons is no longer
                    // mandatory per docs/ENTERPRISE_PLAYBOOK.md §4.2), and the stage's solid
                    // color — taken from the app's own "Pipeline Stages" legend
                    // (resources/views/sections/service_company/pipeline.blade.php) so this
                    // tab bar matches that legend exactly rather than inventing new colors.
                    $cvStageTabs = [
                        ['stage'=>'all', 'label'=>'All Candidates', 'countKey'=>'total', 'color'=>'#2563eb',
                         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['stage'=>'8',   'label'=>'InReview',       'countKey'=>'inreview', 'color'=>'#bdbd26',
                         'icon'=>'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                        ['stage'=>'1',   'label'=>'Sourcing',       'countKey'=>'sourcing', 'color'=>'#7e8299',
                         'icon'=>'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
                        ['stage'=>'2',   'label'=>'Screening',      'countKey'=>'screening', 'color'=>'#c000ff',
                         'icon'=>'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
                        ['stage'=>'3',   'label'=>'Evaluation',     'countKey'=>'evaluation', 'color'=>'#2896ff',
                         'icon'=>'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
                        ['stage'=>'4',   'label'=>'Negotiation',    'countKey'=>'negotiation', 'color'=>'#ff4098',
                         'icon'=>'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
                        ['stage'=>'5',   'label'=>'Offered',        'countKey'=>'offered', 'color'=>'#ffab00',
                         'icon'=>'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
                        ['stage'=>'6',   'label'=>'Joined',         'countKey'=>'joined', 'color'=>'#06c732',
                         'icon'=>'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4 4 0 0 0-7.533-2.348M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25-3 3-1.5-1.5'],
                        ['stage'=>'7,9', 'label'=>'Rejected',       'countKey'=>'rejected', 'color'=>'#ff0202',
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

                {{-- ── Row selection action bar — hidden until candidates are checked.
                     Reuses the exact same trigger classes/data-target attributes as the
                     legacy toolbar buttons (mass_mail / fwd_resume / application_copy /
                     exportRecord), so every existing modal + ajax handler works unchanged;
                     only the presentation is new. ── --}}
                <div class="cvh-selection-bar" id="cvh_selection_bar">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="cvh_selection_master_chk" checked
                            title="Clear selection" />
                    </div>
                    <span class="cvh-selection-count"><span id="cvh_selected_count">0</span> candidates selected</span>
                    <div class="cvh-selection-actions">
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary mass_mail" data-toggle="modal" data-target="#mass_mail">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                            </span> Send Email
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary fwd_resume" data-toggle="modal" data-target="#fwd_resume">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/><path d="m9 14 3 3 3-3"/><path d="M12 17v-6"/></svg>
                            </span> Forward Resume
                        </a>
                        <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-primary application_copy" data-toggle="modal" data-target="#application_copy">
                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M4 16V4a2 2 0 0 1 2-2h10"/></svg>
                            </span> Copy
                        </a>
                        <button type="button" class="btn btn-sm btn-light-primary exportRecord" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"/>
                                </svg>
                            </span> Export
                        </button>
                    </div>
                    <button type="button" class="cvh-selection-close" id="cvh_selection_close" title="Clear selection">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="card">
                    <div class="card-body pt-3 px-5" style="position: relative">
                        @if (in_array('view_job_applications', $userPermissions))
                           <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class"
                                id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input datatable_chk chk_list" type="checkbox"
                                                    data-kt-check="true" data-kt-check-target="#myTable .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Name</th>
                                        {{-- <th class="">Contacts</th> --}}
                                        <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="" data-toggle="tooltip" title="Notice Period">Notice</th>
                                        <th class="">Qualification</th>
                                        <th class="">Location</th>
                                        <th class="">Source</th>
                                        {{-- <th class="" data-toggle="tooltip" title="Upload Date">Date</th> --}}
                                        <th class="">Jobs</th>
                                        <th class="">Status</th>
                                        <th class="">Stage</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold" id="candidatetList">
                                </tbody>
                            </table>
                            <div id="paginationcandidate"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Stage and sub stage</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="stage_sub_stage_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)"/>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="client_stage_sub_stage_new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Stage and sub stage</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close   client_stage_sub_stage_new_close "
                        data-dismiss="modal" id="client_stage_sub_stage_new_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span> </div>
                </div>
                <div class="client_stage_body_content" id="client_stage_body_content"></div>
            </div>
        </div>
    </div>


    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Select the job checkbox, then click "Save" to add the candidate</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">

                    <div class="">
                        <button type="button" id="submit-frm-btn" class="btn btn-primary copy_current_selected_applicant d-none"> 
                            <span class="indicator-label">Save to Jobs</span> 
                            <span class="indicator-progress">
                                Please wait...
                            </span> 
                        </button>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-danger me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>


    <!--begin::Fwd Resume - Modal-->
    <div class="modal fade" id="fwd_resume" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Forward Resume</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
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
                <div class="card-body position-relative px-0 py-0" id="application_fwd_resume_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Fwd Resume - Modal-->

    <!--begin::Mass Mail - Modal-->
    <div class="modal fade" id="mass_mail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Send Email</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
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
                <div class="card-body position-relative px-0 py-0" id="application_mass_mail_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Mass Mail - Modal-->



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


@endsection
@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

   

    <script>
        // var input2 = document.querySelector("#kt_tagify_2");
        // new Tagify(input2);
    </script>

    <script>
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
        }

        $("#kt_daterangepicker").daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);

        $(document).on('click', '.chk_list', function() {
            if ($(".chk_list").prop('checked') == true) {
                var chk_list = $('.chk_list').val();
                $('.chk_list_item').prop('checked', true);
                $('.custom_hide').addClass("d-none");
            } else {
                $('.chk_list_item').prop('checked', false);	
                $('.custom_hide').removeClass("d-none");
            }
        });
    </script>

    <script>
        $(document).ready(function() {




          $(document).on('click', '.exportRecord', function() {

                var action_type = $(this).val();
                var id = 0;
                var url = "{{ route('service_company.candidateexport.candidate-job-applications', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    var wiseid = $(this).attr('data-wiseid');
                    arr.push(wiseid);
                    jobIdArr.push(jobId);
                });

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'GET',
                        'arr': arr,
                        'jobIdArr': jobIdArr
                    },
                    beforeSend: function() {
                        $('.exportRecord').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {

                        if (response.status == 'info') {
                            Swal.fire("Success", ` ${response.message}`, "warning");
                            $('.exportRecord').html(
                                '<span class="svg-icon svg-icon-primary svg-icon-1x"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <rect x="0" y="0" width="24" height="24"></rect><path d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z" fill="#000000" fill-rule="nonzero"></path><path d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z" fill="#000000" opacity="0.3"></path></g></svg></span> Export '
                                );
                            return false;
                        }

                        window.location.href = response.file_url;
                        //  return false;

                        setTimeout(() => {
                            $('.exportRecord').text('Export');
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


                                }
                            });

                        }, 2000);
                    }
                });


            });

            $('#inputSearch').keyup(function() {
                // tableLoad('filter');
            });


            $(document).on('click', '.fwd_resume', function(e) {

                e.preventDefault();
                // localStorage.setItem('toggleOpen', 'true');
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function() {

                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id = jobIdArr[0];
                var url = "{{ route('service_company.cvtracker.fwd_resume', ':id') }}";
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
                            $('#application_fwd_resume_body_content').html(response.view);
                        }
                    }
                });

            });


            $(document).on('click', '.mass_mail', function(e) {

                e.preventDefault();
                $('#application_mass_mail_body_content').html('');
                $('#application_fwd_resume_body_content').html('');
                var arr = [];
                var jobIdArr = [];
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



        });

        var table;
        
        document.getElementById('kt_tagify_2').addEventListener('keyup', function() {
               fetchCandidates();
        });

        function filtersdataget() {
            $('.changetextupdate').text('Searching....!');
            setTimeout(() => {
                // tableLoad('load');
                fetchCandidates();
            }, 1000);
            var $this = $('#apptrackerfilter');
            // $this.fadeOut(2000, function() {
            $this.modal('hide');
            // });
            setTimeout(() => {
                $('#apptrackerfilter_close').click();
                $('.changetextupdate').text('Submit')
            }, 2000);


        }

        function tableLoad(type) {

            var status = 'all';
            var jobs = 'all';
            var clients = 'all';
            var source = 'all';
            var search_passport = 'all';
            var search_np_id = 'all';
            var inputSearch = $('#inputSearch').val();
            var kt_daterangepicker = $('#kt_daterangepicker').text();
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            // alert(kt_daterangepicker);
            table = $('#myTable').dataTable({
                serverSide: true, // Enable server-side processing
                responsive: true,
                processing: true,
                ordering: false, //disable ordering      			
                destroy: true,
                stateSave: true,
                "bLengthChange": false,
                sDom: 'lrtip',
                //  ajax			: '{!! route('service_company.cvtracker.alldata') !!}?search='+inputSearch,
                ajax: {
                    'url': '{!! route('service_company.cvtracker.alldata') !!}',
                    "data": function(d) {
                        d.search = $('#inputSearch').val(),
                            d.sortingfilter = $('#sortingfilter').val(),
                            d.country = $('#country').val(),
                            d.cus_search = $('#kt_tagify_2').val()
                    }
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        width: '1%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        width: '15%',
                        orderable: true
                    },
                    {
                        data: 'Exp',
                        name: 'Exp',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'Salary',
                        name: 'Salary',
                        width: '5%',
                        orderable: true
                    },
                    {
                        data: 'NoticePeriod',
                        name: 'NoticePeriod',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Qualifaction',
                        name: 'Qualifaction',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Location',
                        name: 'Location',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        width: '2%',
                        orderable: true
                    },
                    // { data: 'UploadDate', name: 'UploadDate', width: '10%', orderable: true },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        width: '2%',
                        orderable: true
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        width: '10%',
                        orderable: true
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        width: '2%',
                        orderable: true
                    }
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $(document).on('click', '.application_copy', function() {
            $('#application_copy_body_content').html('');
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
            });
            $('.chk_list_item:checked').each(function() {
                wiseId.push($(this).data('wiseid'));
            });
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr,
                    'wiseId': wiseId
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
    </script>

    <script>
        $(document).on('click', '.client_stage_sub_stage', async function() {

            e.preventDefault();
            const connectedsubclient = await isInternetAvailable();
                if (!connectedsubclient) {
                    Swal.fire({
                        title: "No Internet",
                        text: "You are currently offline or the server is unreachable.",
                        icon: "warning",
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    return; // ⛔ STOP EXECUTION
            }

            var app_id = $(this).data('row-id');
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
                    var inner_url =
                        "{{ route('service_company.auth-custom-package.validateClientPackage') }}";
                    var inner_token = "{{ csrf_token() }}";
                    var inner_baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    var eventId = 4;
                    $.ajax({
                        type: 'POST',
                        url: inner_url,
                        beforeSend: function() {},
                        data: {
                            '_token': inner_token,
                            'event': 'CVACCESS',
                            'app_id': app_id,
                            'eventId': eventId
                        },
                        success: function(response) {

                            if (response.status == "success") {

                                $('#client_stage_sub_stage_new').modal('show');
                                var url =
                                    "{{ route('service_company.job-applications.client_stage_body_content', ':id') }}";
                                url = url.replace(':id', app_id);
                                $.ajax({
                                    type: 'GET',
                                    url: url,
                                    success: function(response) {
                                        if (response.status == "success") {
                                            $('#client_stage_body_content').html(
                                                response.view);
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
                        'Selected request is safe :)',
                        'error'
                    )
                }

            })
        });

        $(document).on('click', '.client_stage_sub_stage_new_close', function() {
            $('#client_stage_sub_stage_new').modal('hide');
        });

        $(document).on('click', '#int_stage_submit', function(e) {

            e.preventDefault();
            var master_status = $('input[name="master_status"]:checked').val();
            if ((master_status == 1) || (master_status == 9)) {
                $.ajax({
                    url: '{{ route('service_company.job-applications.client_stage_submit') }}',
                    container: '#status_frm',
                    type: "POST",
                    data: $('#status_frm').serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success");
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // $.unblockUI();
                            // table._fnDraw();

                        } else if (response.status == "info") {
                            swalWithBootstrapButtons.fire('Fail!', response.message, 'error')
                        }

                    }
                })
            } else {
                Swal.fire("Warning", 'Only Sourcing stage update!', "info");
            }

            e.run();

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
                  $('body').removeClass('modal-open');
                  $('.modal-backdrop').remove();
                  return; // ⛔ STOP EXECUTION
            }
            $('#stage_body_content').html('');
            $('#client_stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                    }
                }
            });
        });

        $(document).on('click', '.master_status_div', function(e) {

            e.preventDefault();

            var id = $(this).data('master-status-id');
            $("#master_status_" + id).attr('checked', true);
            $('#main_substage').show();
            $('#comment_div').show();

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
                // $('#main_substage').hide();	
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
                            if (response.html == '') {
                                $('#main_substage').hide();
                            }
                            $('#substage').html('');
                            $('#substage').html(response.html);
                        }
                    }
                });
            }

            e.run();
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
                Swal.fire("Warning", 'InReview Comment Required !', "info");
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
                        $("#custom_stage_submit").html(`<span class="indicator-label">Submit</span>`);
                        if (response.status == 'success') {
                            Swal.fire("Success", 'Application stage update successfully !', "success")
                                .then(function() {
                                    // $('#stage_sub_stage').modal('hide'); 
                                    $("#stage_sub_stage_close").click();
                                });
                            // $('#myTable').DataTable().draw();
                            // var table;
                            var page = $('#paginationcandidate .active_disabled').data('page');
                            fetchCandidates(page);
                            // table._fnDraw();
                            load_interviewSchedule();
                        } else if (response.status == 'cvpending') {
                            Swal.fire("Warning", 'Resume pending this job application.', "warning")
                                .then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                        } else if (response.status == 'candidatevalidation') {
                            Swal.fire("Warning", response.message, "warning").then(function() {
                                $("#stage_sub_stage_close").click();
                            });
                        }
                    }
                })
            }

        });
    </script>

    <script>
        $(document).ready(function() {

          $(document).on('click', '.chk_list', function() {
           
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').prop('checked', true);
                } else {
                    $('.chk_list_item').prop('checked', false);	
                }
            });

            $(document).on('click', '.chk_list', function(e) {
                $('#application_fwd_resume_body_content').html('');

                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.fwd_resume').removeClass("d-none");
                    $('.custom_hide').addClass("d-none");
                    $('.mass_mail').removeClass("d-none");
                    $('.send_email').removeClass("d-none");
                    $('.downloadexcell').removeClass("d-none");
                    $('.app_copy').removeClass("d-none");
                    $('.application_copy').removeClass("d-none");

                } else {
                    $('.fwd_resume').addClass("d-none");
                    $('.send_email').addClass("d-none");
                    $('.mass_mail').addClass("d-none");
                    $('.app_copy').addClass("d-none");
                    $('.downloadexcell').addClass("d-none");
                    $('.application_copy').addClass("d-none");
                    $('.custom_hide').removeClass("d-none");
                }
                updateCvSelectionBar();
            });

            $(document).on('click', '.chk_list_item', function() {
                  
                $('#application_fwd_resume_body_content').html('');
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.fwd_resume').removeClass("d-none");
                        $('.mass_mail').removeClass("d-none");
                        $('.send_email').removeClass("d-none");
                        $('.custom_hide').addClass("d-none");
                        $('.downloadexcell').removeClass("d-none");
                        $('.app_copy').removeClass("d-none");
                        $('.application_copy').removeClass("d-none");
                    } else {
                        $('.fwd_resume').addClass("d-none");
                        $('.mass_mail').addClass("d-none");
                        $('.downloadexcell').addClass("d-none");
                        $('.send_email').addClass("d-none");
                        $('.app_copy').addClass("d-none");
                        $('.application_copy').addClass("d-none");
                        $('.custom_hide').removeClass("d-none");
                    }
                    updateCvSelectionBar();
                }, 500);

            });

            /* Selection bar: live count + clear-selection controls (checkbox + close X) */
            function updateCvSelectionBar() {
                var n = $('.chk_list_item:checked').length;
                $('#cvh_selected_count').text(n);
                $('#cvh_selection_bar').toggleClass('show', n > 0);
                $('#cvh_selection_master_chk').prop('checked', n > 0);
            }
            function clearCvSelection() {
                $('.chk_list, .chk_list_item').prop('checked', false);
                $('.fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
                $('.custom_hide').removeClass('d-none');
                updateCvSelectionBar();
            }
            $(document).on('click', '#cvh_selection_close', clearCvSelection);
            $(document).on('change', '#cvh_selection_master_chk', function() {
                if (!$(this).is(':checked')) clearCvSelection();
            });

            //  $.noConflict();
           
            fetchCandidates();
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
        var page_no = localStorage.getItem('currentPage');
        if (!page_no) {
            page_no = 1;
            localStorage.setItem('currentPage', page_no);
        }
        var activeStageTab = 'all';
        function setCvTabsCardBorder(color) {
            $('#cvh_tabs_card').css('border-color', color || '#e4e6ef');
        }
        $(document).on('click', '.cvh-tab', function() {
            $('.cvh-tab').removeClass('active');
            $(this).addClass('active');
            activeStageTab = $(this).attr('data-stage');
            setCvTabsCardBorder($(this).attr('data-color'));
            fetchCandidates();
        });
        setCvTabsCardBorder($('.cvh-tab.active').attr('data-color'));
        function fetchCandidates(page = page_no, search = '') {
            var clientid = '1';
           
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            
            // document.getElementById("loader_search").classList.remove("hidden");
            $.ajax({
                url: `{!! route('service_company.cvtracker.allcandidates') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: search,
                    search : $('#inputSearch').val(),
                    sortingfilter : $('#sortingfilter').val(),
                    country : $('#country').val(),
                    cus_search : $('#kt_tagify_2').val(),
                    cus_search_skill : $('#kt_tagify_users').val(),
                    min_exp : $('#min_exp').val(),
                    max_exp : $('#max_exp').val(),
                    kt_tagify_location : $('#kt_tagify_location').val(),
                    min_sal : $('#min_sal').val(),
                    max_sal : $('#max_sal').val(),
                    min_salary_currency : $('#min_salary_currency').val(),
                    sal_mode : $('#sal_mode').val(),
                    kt_tagify_education : $('#kt_tagify_education').val(),
                    app_int_status : $('[name="app_int_status"]:checked').val(),
                    app_int_activity : $('[name="app_int_activity"]:checked').val(),
                    app_int_notice : $('[name="app_int_notice"]:checked').val(),
                    stage_tab : activeStageTab === 'all' ? '' : activeStageTab
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#total_record').html(response.total);
                    $('#candidatetList').html(response.html);
                    localStorage.setItem('currentPage', 1);
                    $('#paginationcandidate').html(response.pagination);
                    updateCvHeroStats(response.candidateStats);
                }
            });
        }

        /* Sync hero KPI cards + stage tab badges from the same ES aggregation the table fetch already returns */
        function updateCvHeroStats(stats) {
            if (!stats) return;
            document.querySelectorAll('[data-cvh]').forEach(function (el) {
                var k = el.getAttribute('data-cvh');
                if (k === 'this_week_delta') {
                    if (stats.this_week != null) el.textContent = stats.this_week.toLocaleString();
                } else if (stats[k] != null) {
                    el.textContent = stats[k].toLocaleString();
                }
            });
            document.querySelectorAll('[data-cvh-tab]').forEach(function (el) {
                var k = el.getAttribute('data-cvh-tab');
                if (k === 'total') {
                    if (stats.total != null) el.textContent = stats.total.toLocaleString();
                } else if (stats.tabs && stats.tabs[k] != null) {
                    el.textContent = stats.tabs[k].toLocaleString();
                }
            });
        }

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
