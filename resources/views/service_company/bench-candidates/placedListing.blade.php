@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
    <style>
        /* ---------------------------------------------------------------
           Talent Placed — placement-tracker redesign.
           All rules are scoped under #kt_post so nothing leaks to other
           pages that share the same layout/theme.
        ----------------------------------------------------------------*/
        #send_email .modal-dialog { margin: .3rem auto !important; }
        #fwd_resume .modal-dialog { margin: .3rem auto !important; }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {
            .table td, .table th, .table tr { width: max-content !important; }
            .table > :not(caption) > * > * { padding: 0rem; }
            td:nth-child(n+2), th:nth-child(n+2) { width: revert !important; }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesBenchApplication { width: 1025px; }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesBenchApplication { width: 1200px; }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy { width: 1025px; }
        }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy { width: 1200px; }
        }

        #kt_post * { font-family: 'Poppins', sans-serif; }

        /* ---- stat cards ---- */
        .placed-stat-card {
            border: 1px solid #eef0f4;
            border-radius: .75rem;
            padding: .85rem 1rem;
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            height: 100%;
            background: #fff;
        }
        .placed-stat-icon {
            width: 40px; height: 40px; min-width: 40px;
            border-radius: .6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .placed-stat-label { font-size: 10.8px; font-weight: 500; color: #7e8299; }
        .placed-stat-value { font-size: 18px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .placed-stat-caption { font-size: 10px; color: #a1a5b7; }
        .placed-stat-delta { font-size: 10px; font-weight: 600; }
        .placed-stat-delta.up { color: #17c653; }
        .placed-stat-delta.down { color: #f1416c; }

        /* ---- filter bar ---- */
        .placed-filter-bar {
            display: flex; flex-wrap: wrap; align-items: center; gap: .5rem;
            padding: .6rem .25rem;
        }
        .placed-filter-btn {
            border: 1px solid #e4e6ef; background: #f9f9f9; color: #3f4254;
            border-radius: .5rem; font-size: 11.5px; font-weight: 500;
            padding: .45rem .75rem; white-space: nowrap;
            display: inline-flex; align-items: center; gap: .35rem;
        }
        .placed-filter-btn:hover { background: #f1f1f4; }

        /* ---- table ---- */
        #placedTableWrap { overflow-x: auto; }
        #myTable thead th {
            position: sticky; top: 0; z-index: 2;
            background: #fff; font-size: 11px; font-weight: 500; color: #7e8299;
            border-bottom: 1px solid #eef0f4; white-space: nowrap; padding: .75rem .6rem;
        }
        #myTable tbody td { padding: .6rem; vertical-align: middle; font-size: 11.5px; }
        #myTable tbody tr { border-bottom: 1px solid #f4f4f8; }

        .placed-avatar {
            width: 40px; height: 40px; border-radius: .55rem;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }
        .placed-skill-badge {
            background: #f4f4f8; color: #5e6278; font-weight: 400; font-size: 9.5px;
            margin-right: 2px;
        }
        .fs-8 { font-size: 10.5px !important; }
        .fs-9 { font-size: 9.5px !important; }

        .placed-timeline { min-width: 190px; }
        .placed-timeline-track { display: flex; align-items: flex-start; }
        .placed-timeline-node-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .placed-timeline-node { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .placed-timeline-node.is-static { background: #fff; border: 2px solid #e4e6ef; }
        .placed-timeline-node.is-done { background: #17c653; border: 2px solid #17c653; }
        .placed-timeline-node-label { font-size: 8.5px; font-weight: 600; color: #7e8299; margin-top: 4px; white-space: nowrap; }
        .placed-timeline-line { flex: 1; height: 2px; background: #e4e6ef; min-width: 14px; margin: 4px 1px 0; }
        .placed-timeline-line.is-done { background: #17c653; }
        .placed-timeline-labels { display: flex; justify-content: space-between; margin-top: 1px; }
        .placed-timeline-labels span { font-size: 8px; color: #a1a5b7; }

        .placed-detail-line { display: flex; align-items: center; gap: .35rem; margin-bottom: 1px; }
        .placed-detail-line i { font-size: 9px; width: 12px; text-align: center; }

        .placed-status-pill {
            display: inline-flex; align-items: center; width: fit-content;
            font-size: 10.5px; font-weight: 600; padding: .25rem .55rem; border-radius: .45rem;
        }
        .placed-status-pill.is-success { background: #e8fff3; color: #17c653; }
        .placed-status-pill.is-info { background: #eef3ff; color: #009ef7; }
        .placed-status-pill.is-danger { background: #fff5f8; color: #f1416c; }
        .placed-action-btn { width: 30px; height: 30px; }

        .placed-commercial-line { display: flex; align-items: center; gap: .35rem; margin-top: 3px; }
        .placed-commercial-line i { font-size: 9px; width: 12px; text-align: center; }
        .placed-change-link {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: 10px; font-weight: 600; color: #009ef7; margin-top: 4px; width: fit-content;
        }
        .placed-change-link i { font-size: 8.5px; }
        .placed-change-link:hover { color: #0077b5; }

        .placed-row.row-reverb-updated {
            transition: background-color 0.6s ease;
            background-color: rgba(59, 91, 219, 0.08) !important;
        }

        .rows-per-page-select { cursor: pointer; }

        /* ── Pagination bar (Standard Metronic/JERO styling) ───────────────────── */
        .pagi-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid #F0F2F5;
            background: #fff;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }
        .pagi-info {
            font-size: 13px;
            font-weight: 500;
            color: #64748B;
        }
        .pagi-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        /* Prev / Next arrow buttons */
        .pagi-arrow {
            width: 32px;
            height: 32px;
            border: 1.5px solid #E5E9F0;
            border-radius: 8px;
            background: #fff;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all .15s ease;
            outline: none;
            padding: 0;
        }
        .pagi-arrow:hover:not(.pagi-disabled) {
            background: #2563EB;
            color: #fff;
            border-color: #2563EB;
        }
        .pagi-arrow.pagi-disabled {
            opacity: .35;
            pointer-events: none;
            cursor: not-allowed;
        }
        /* Page number buttons */
        .pagi-page {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1.5px solid #E5E9F0;
            border-radius: 8px;
            background: #fff;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all .15s ease;
            font-family: 'Poppins', sans-serif;
            outline: none;
        }
        .pagi-page:hover:not(.pagi-active-disabled) {
            background: #F5F8FF;
            border-color: #2563EB;
            color: #2563EB;
        }
        .pagi-page.pagi-active {
            background: #2563EB;
            color: #fff;
            border-color: #2563EB;
            pointer-events: none;
        }
        .pagi-page.pagi-active-disabled {
            pointer-events: none;
        }
        .pagi-dots {
            font-size: 13px;
            color: #94A3B8;
            padding: 0 4px;
            display: flex;
            align-items: center;
        }
        /* Page-size dropdown */
        .pagi-perpage {
            height: 32px;
            padding: 0 26px 0 10px;
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #E5E9F0;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            color: #374151;
            background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 8px center/10px;
            font-family: 'Poppins', sans-serif;
            margin-left: 8px;
            cursor: pointer;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .pagi-perpage:hover, .pagi-perpage:focus {
            border-color: #2563EB;
        }

        /* ---- commercial configuration drawer ---- */
        #kt_commercialConfig .card-header { border-bottom: 1px solid #eef0f4; }
        .cc-step-head { display: flex; align-items: center; gap: .5rem; margin-bottom: .85rem; }
        .cc-step-num {
            width: 22px; height: 22px; border-radius: 50%; background: #009ef7; color: #fff;
            font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .cc-step-title { font-size: 14px; font-weight: 700; color: #181c32; }
        .cc-step-title .cc-step-suffix { font-weight: 500; color: #7e8299; font-size: 12.5px; }

        .cc-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .cc-option-card {
            display: flex; align-items: center; gap: .6rem; cursor: pointer;
            border: 1.5px solid #e4e6ef; border-radius: .65rem; padding: .8rem .9rem;
            transition: border-color .15s ease, background .15s ease;
        }
        .cc-option-card:hover { border-color: #b5d9f7; }
        .cc-option-card.is-selected { border-color: #009ef7; background: #f1faff; }
        .cc-option-icon {
            width: 30px; height: 30px; border-radius: .5rem; background: #f4f4f8; color: #5e6278;
            display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;
        }
        .cc-option-card.is-selected .cc-option-icon { background: #dbf3ff; color: #009ef7; }
        .cc-option-label { font-size: 13px; font-weight: 600; color: #3f4254; flex-grow: 1; }
        .cc-option-radio {
            width: 18px; height: 18px; border-radius: 50%; border: 2px solid #d7dae1; flex-shrink: 0; position: relative;
        }
        .cc-option-card.is-selected .cc-option-radio { border-color: #009ef7; }
        .cc-option-card.is-selected .cc-option-radio::after {
            content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #009ef7;
        }

        .cc-info-banner {
            display: flex; align-items: center; gap: .5rem; background: #f1faff; color: #3f4254;
            border-radius: .55rem; padding: .65rem .85rem; font-size: 12.5px; font-weight: 500;
        }
        .cc-info-banner i { color: #009ef7; }
        .cc-info-banner.is-success { background: #eafff3; }
        .cc-info-banner.is-success i { color: #17c653; }
        .cc-info-banner.is-danger { background: #fff5f8; }
        .cc-info-banner.is-danger i { color: #f1416c; }

        .cc-tabs { display: flex; gap: 1.25rem; border-bottom: 1px solid #eef0f4; margin: 1.25rem 0 1.25rem; }
        .cc-tab {
            display: flex; align-items: center; gap: .4rem; padding-bottom: .6rem; cursor: pointer;
            font-size: 12.5px; font-weight: 600; color: #7e8299; border-bottom: 2px solid transparent; margin-bottom: -1px;
        }
        .cc-tab.is-active { color: #009ef7; border-bottom-color: #009ef7; }

        .cc-section { border: 1px solid #eef0f4; border-radius: .75rem; padding: 1.1rem 1.2rem; margin-bottom: 1rem; }
        .cc-section-title { font-size: 13.5px; font-weight: 700; color: #181c32; display: flex; align-items: center; gap: .4rem; }
        .cc-section-sub { font-size: 11.5px; color: #a1a5b7; margin: 2px 0 1rem; }
        .cc-field-label { font-size: 12px; font-weight: 600; color: #3f4254; margin-bottom: .35rem; display: block; }
        .cc-helper-text { font-size: 11.5px; color: #7e8299; margin-top: .35rem; }
        .cc-auto-badge {
            display: inline-block; font-size: 10px; font-weight: 700; color: #17c653; background: #eafff3;
            border-radius: .35rem; padding: 1px 6px; margin-top: .35rem;
        }

        .cc-calc-preview { background: #f9f9fb; border-radius: .6rem; padding: .85rem 1rem; }
        .cc-calc-preview-title { font-size: 12px; font-weight: 700; color: #181c32; margin-bottom: .6rem; }
        .cc-calc-preview-row { display: flex; align-items: center; gap: .85rem; flex-wrap: wrap; }
        .cc-calc-cell { display: flex; flex-direction: column; }
        .cc-calc-label { font-size: 10.5px; color: #a1a5b7; }
        .cc-calc-value { font-size: 13px; font-weight: 700; color: #181c32; }
        .cc-calc-op { font-size: 13px; color: #a1a5b7; font-weight: 600; }

        .cc-total-box {
            background: #f9f9fb; border-radius: .6rem; padding: .6rem .9rem;
            display: flex; flex-direction: column; align-items: flex-end; justify-content: center; height: 100%;
        }

        .cc-toggle-link {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: 12px; font-weight: 600; color: #009ef7;
        }
        .cc-toggle-link:hover { color: #0077b5; }

        .cc-subsection { background: #f9f9fb; border-radius: .65rem; padding: 1rem 1.1rem; }
        .cc-progress-row {
            display: flex; align-items: center; justify-content: space-between;
            border: 1px solid #e4e6ef; border-radius: .5rem; padding: .55rem .8rem; font-size: 12.5px; font-weight: 600; color: #3f4254;
        }

        .cc-summary-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem;
            background: #f9f9fb; border-radius: .6rem; padding: .9rem 1rem;
        }
        .cc-summary-cell { display: flex; flex-direction: column; gap: .2rem; }

        /* ---- assignment details drawer ---- */
        .ad-candidate-card {
            display: flex; align-items: center; gap: .75rem;
            border: 1px solid #eef0f4; border-radius: .7rem; padding: .85rem 1rem;
        }
        .ad-candidate-avatar { width: 44px; height: 44px; border-radius: .6rem; flex-shrink: 0; }

        .ad-status-list { display: flex; flex-direction: column; gap: .6rem; }
        .ad-status-card {
            display: flex; align-items: flex-start; gap: .65rem; cursor: pointer;
            border: 1.5px solid #e4e6ef; border-radius: .65rem; padding: .8rem .95rem;
            transition: border-color .15s ease, background .15s ease;
        }
        .ad-status-card:hover { border-color: #d7dae1; }
        .ad-status-radio {
            width: 18px; height: 18px; border-radius: 50%; border: 2px solid #d7dae1; flex-shrink: 0; margin-top: 2px; position: relative;
        }
        .ad-status-title { display: block; font-size: 13px; font-weight: 700; color: #181c32; }
        .ad-status-sub { display: block; font-size: 11.5px; color: #a1a5b7; margin-top: 1px; }

        .ad-status-card[data-tone="success"].is-selected { border-color: #17c653; background: #eafff3; }
        .ad-status-card[data-tone="success"].is-selected .ad-status-radio { border-color: #17c653; }
        .ad-status-card[data-tone="success"].is-selected .ad-status-radio::after { background: #17c653; }
        .ad-status-card[data-tone="info"].is-selected { border-color: #009ef7; background: #f1faff; }
        .ad-status-card[data-tone="info"].is-selected .ad-status-radio { border-color: #009ef7; }
        .ad-status-card[data-tone="info"].is-selected .ad-status-radio::after { background: #009ef7; }
        .ad-status-card[data-tone="danger"].is-selected { border-color: #f1416c; background: #fff5f8; }
        .ad-status-card[data-tone="danger"].is-selected .ad-status-radio { border-color: #f1416c; }
        .ad-status-card[data-tone="danger"].is-selected .ad-status-radio::after { background: #f1416c; }
        .ad-status-card.is-selected .ad-status-radio::after {
            content: ''; position: absolute; inset: 3px; border-radius: 50%;
        }

        .ad-summary-box {
            display: flex; align-items: flex-start; gap: .65rem;
            border-radius: .6rem; padding: .85rem 1rem; margin-top: 1rem; font-size: 12.5px;
        }
        .ad-summary-box i { font-size: 15px; margin-top: 2px; }
        .ad-summary-box.is-success { background: #eafff3; color: #17c653; }
        .ad-summary-box.is-info { background: #f1faff; color: #009ef7; }
        .ad-summary-box.is-danger { background: #fff5f8; color: #f1416c; }
        .ad-summary-box .fw-bold, .ad-summary-box .text-muted { color: inherit; }
        .ad-summary-box .fs-9.text-muted, .ad-summary-box .text-muted { opacity: .85; }
        .ad-summary-kv { display: flex; justify-content: space-between; gap: .75rem; font-size: 11.5px; color: #3f4254; padding: 1px 0; }
        .ad-summary-kv span { color: #7e8299; }

        .ad-yesno-group { display: flex; gap: .6rem; }
        .ad-yesno-option {
            display: flex; align-items: center; gap: .4rem; cursor: pointer;
            border: 1.5px solid #e4e6ef; border-radius: .5rem; padding: .5rem .9rem; font-size: 12.5px; font-weight: 600; color: #3f4254;
        }
        .ad-yesno-radio { width: 15px; height: 15px; border-radius: 50%; border: 2px solid #d7dae1; position: relative; }
        .ad-yesno-option.is-selected.is-danger { border-color: #f1416c; background: #fff5f8; color: #f1416c; }
        .ad-yesno-option.is-selected.is-danger .ad-yesno-radio { border-color: #f1416c; }
        .ad-yesno-option.is-selected .ad-yesno-radio::after {
            content: ''; position: absolute; inset: 2px; border-radius: 50%; background: #f1416c;
        }
        .ad-yesno-option.is-selected:not(.is-danger) { border-color: #009ef7; background: #f1faff; color: #009ef7; }
        .ad-yesno-option.is-selected:not(.is-danger) .ad-yesno-radio { border-color: #009ef7; }
        .ad-yesno-option.is-selected:not(.is-danger) .ad-yesno-radio::after { background: #009ef7; }
    </style>
@endpush

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="javascript:void(0);" class="text-primary">Talent Placed</a>
                    </h5>
                    <span class="text-gray-600 fs-8">Track bench candidates who got placed and their project / placement journey.
                        <span class="fw-bolder text-dark" id="totalPlace"></span> Placed</span>
                </div>
            </div>
            <div class="d-flex align-items-center py-1 gap-2">
                {{-- Date range display — placeholder only, no backend date-range filter exists yet --}}
                <div class="placed-filter-btn">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span id="placedDateRangeLabel">This Month</span>
                </div>
                {{-- Export — placeholder only, no export endpoint exists yet for this page --}}
                <button type="button" class="btn btn-sm btn-light-primary placed-filter-btn" disabled title="Coming soon">
                    <i class="fa-solid fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>
    @if (in_array('view_bench', $userPermissions))
        <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-fluid px-3">

                    {{-- =====================================================
                         Overview stat cards — populated dynamically from
                         placedListingStats_v2 (Elasticsearch aggregations).
                    ====================================================== --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-primary text-primary"><i class="fa-solid fa-user-check"></i></div>
                                <div>
                                    <div class="placed-stat-label">Currently Working</div>
                                    <div class="placed-stat-value" id="placedStatWorking">--</div>
                                    <div class="placed-stat-caption">Active Assignments</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> Active</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-success text-success"><i class="fa-solid fa-circle-check"></i></div>
                                <div>
                                    <div class="placed-stat-label">Projects Completed</div>
                                    <div class="placed-stat-value" id="placedStatCompleted">--</div>
                                    <div class="placed-stat-caption">Completed</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> Total</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-purple text-purple" style="background:#f8f5ff;color:#7239ea;"><i class="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <div class="placed-stat-label">Replacement Active</div>
                                    <div class="placed-stat-value" id="placedStatReplacement">--</div>
                                    <div class="placed-stat-caption">Permanent Hiring</div>
                                    <div class="placed-stat-delta down mt-1"><i class="fa-solid fa-arrow-down"></i> Under Review</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-warning text-warning"><i class="fa-solid fa-calendar-week"></i></div>
                                <div>
                                    <div class="placed-stat-label">Joining This Week</div>
                                    <div class="placed-stat-value" id="placedStatJoining">--</div>
                                    <div class="placed-stat-caption">Confirmed</div>
                                    <div class="placed-stat-delta up mt-1">Next 7 Days</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-info text-info"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                <div>
                                    <div class="placed-stat-label">Available Soon</div>
                                    <div class="placed-stat-value" id="placedStatAvailable">--</div>
                                    <div class="placed-stat-caption">Project Ending &lt; 30 Days</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> Rolling Off</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-danger text-danger"><i class="fa-solid fa-building"></i></div>
                                <div>
                                    <div class="placed-stat-label">Companies Served</div>
                                    <div class="placed-stat-value" id="placedStatCompanies">--</div>
                                    <div class="placed-stat-caption">Active Clients</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> Client Network</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =====================================================
                         Filter bar — Search and Sort only; both are real and
                         wired to the existing AJAX endpoint.
                    ====================================================== --}}
                    <div class="card mb-3">
                        <div class="card-body py-3 px-4">
                            <div class="placed-filter-bar">
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control form-control-solid" name="inputJobSearch"
                                        value="" placeholder="Search by name, job, client, skills..." id="kt_tagify_2" onchange="filtersdataget()" />
                                </div>
                                <select class="form-select form-select-solid w-auto" data-hide-search="true"
                                    id="sortingfilter" data-dropdown-css-class="w-150px"
                                    data-placeholder="Select an option" onchange="filtersdataget()">
                                    <option value="Show All" selected="">Sort</option>
                                    <option value="10">Newest to Oldest (Offer Date)</option>
                                    <option value="11">Oldest to Newest (Offer Date)</option>
                                    <option value="12">Newest to Oldest (Join Date)</option>
                                    <option value="13">Oldest to Newest (Join Date)</option>
                                    <option value="14">Low to High (Offer Amount)</option>
                                    <option value="15">High to Low (Offer Amount)</option>
                                </select>

                                {{-- kept for functional parity with the old country/location filter (hidden, unused today) --}}
                                <select class="d-none" id="country">
                                    <option value="110" selected="selected">Location</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body pt-3 px-0">
                            <div class="loadingMessage loadingMessagebenchCandidatePlaceAjax" style="display: none; position: absolute;">Processing...</div>
                            <div id="placedTableWrap">
                                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0" id="myTable">
                                    <thead>
                                        <tr class="fw-bolder text-muted" role="row">
                                            <th class="w-10px pe-0">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" value="1"></div>
                                            </th>
                                            <th>Candidate</th>
                                            <th>Placement Timeline</th>
                                            <th>Client &amp; Job Details</th>
                                            <th>Recruiter</th>
                                            <th>Commercials</th>
                                            <th>Work Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="benchCandidatePlaceAjax"> </tbody>
                                </table>
                            </div>
                            <div id="paginationbenchCandidatePlace"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Content-->

    <div id="kt_activitiesHM_toggle"></div>
    <div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">HM | CP</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
        </div>
    </div>

    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Inhouse Talent Status</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="stage_sub_stage_close">
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
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         Commercial Configuration drawer — opened from the "Change" link
         in the Commercials column. There is no backend endpoint to persist
         these terms yet (no commercial_configuration table/route exists),
         so this is a view-only calculator: every field, the Indian-numbering
         amount-in-words line, and the margin math run entirely client-side
         (no AJAX calls at all), and "Save Configuration" just closes the
         drawer with a heads-up rather than pretending to persist anything.
         Only the initial Billing Amount and the pre-selected Commercial
         Model are real, seeded from the clicked row's actual offer amount
         and employment type.
    ====================================================== --}}
    <div id="kt_commercialConfig_toggle"></div>
    <div id="kt_commercialConfig" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="commercial-config"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '480px', 'lg': '640px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_commercialConfig_toggle" data-kt-drawer-close="#kt_commercialConfig_close">
        <div class="card shadow-none rounded-0 w-100 h-100" style="display:flex; flex-direction:column;">
            <div class="card-header align-items-start px-6 py-5">
                <div class="d-flex flex-column">
                    <h2 class="fw-bolder text-dark mb-1">Commercial Configuration</h2>
                    <div class="text-muted fs-8">Configure commercial terms for this placement.</div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_commercialConfig_close">
                        <i class="fa-solid fa-xmark fs-3"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-6 py-5" id="kt_commercialConfig_body" style="overflow-y:auto; flex-grow:1;">

                {{-- Step 1: Service Type --}}
                <div class="cc-step-head">
                    <span class="cc-step-num">1</span>
                    <span class="cc-step-title">Service Type</span>
                </div>
                <div class="cc-option-grid mb-5" id="ccServiceTypeGrid">
                    <label class="cc-option-card is-selected" data-value="free">
                        <span class="cc-option-icon"><i class="fa-regular fa-calendar"></i></span>
                        <span class="cc-option-label">Free Service<br><small class="text-muted fw-normal">No charges for this placement</small></span>
                        <span class="cc-option-radio"></span>
                    </label>
                    <label class="cc-option-card" data-value="paid">
                        <span class="cc-option-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                        <span class="cc-option-label">Paid Service<br><small class="text-muted fw-normal">Charges applicable</small></span>
                        <span class="cc-option-radio"></span>
                    </label>
                </div>

                <div id="ccPaidServiceSection">
                    {{-- Step 2: Commercial Model — stays visible regardless of Free/Paid, since the
                         staffing model (contract vs full-time) is independent of whether it's billed. --}}
                    <div class="cc-step-head">
                        <span class="cc-step-num">2</span>
                        <span class="cc-step-title">Commercial Model <span class="cc-step-suffix" id="ccModelStepSuffix">(Free Service)</span></span>
                    </div>
                    <div class="cc-option-grid mb-4" id="ccModelGrid">
                        <label class="cc-option-card is-selected" data-value="contract">
                            <span class="cc-option-icon"><i class="fa-solid fa-people-group"></i></span>
                            <span class="cc-option-label">Contract Staffing</span>
                            <span class="cc-option-radio"></span>
                        </label>
                        <label class="cc-option-card" data-value="fulltime">
                            <span class="cc-option-icon"><i class="fa-regular fa-calendar-check"></i></span>
                            <span class="cc-option-label">Full-time Hiring</span>
                            <span class="cc-option-radio"></span>
                        </label>
                    </div>

                    <div class="cc-info-banner mb-4">
                        <i class="fa-solid fa-circle-info"></i>
                        <span id="ccModelBanner">You are configuring a <strong>Free Service</strong> for <strong id="ccModelBannerText">Contract Staffing</strong>.</span>
                    </div>

                    <div class="cc-tabs">
                        <div class="cc-tab is-active" data-tab="general"><i class="fa-regular fa-user"></i> General</div>
                        <div class="cc-tab" data-tab="notes"><i class="fa-regular fa-note-sticky"></i> Notes</div>
                        <div class="cc-tab" data-tab="history"><i class="fa-solid fa-clock-rotate-left"></i> History</div>
                    </div>

                    <div id="ccTabGeneral">
                        {{-- Free Service fields: no billing, just a reason/effective-date record --}}
                        <div id="ccFreeServiceFields">
                            <div class="cc-section mb-0">
                                <div class="cc-section-title">2. Commercial Configuration</div>
                                <div class="cc-info-banner is-success mt-3 mb-4" style="display:block;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa-solid fa-circle-check mt-1"></i>
                                        <div>
                                            <div class="fw-bold">This placement is marked as a Free Service.</div>
                                            <div>No fees or margin will be applicable.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cc-section-title fs-8 mb-3">Service Details</div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="cc-field-label">Service Type *</label>
                                        <select class="form-select form-select-solid" id="ccFreeServiceTypeField">
                                            <option value="free" selected>Free Service</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="cc-field-label">Reason / Purpose *</label>
                                        <select class="form-select form-select-solid" id="ccFreeReason">
                                            <option>Goodwill</option>
                                            <option>Internal Placement</option>
                                            <option>Trial Period</option>
                                            <option>Partner Referral</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <label class="cc-field-label">Effective Date *</label>
                                <input type="date" class="form-control form-control-solid mb-4" id="ccFreeEffectiveDate">

                                <div class="cc-summary-grid">
                                    <div class="cc-summary-cell">
                                        <span class="cc-calc-label">Service Fee</span>
                                        <span class="cc-calc-value">₹0</span>
                                    </div>
                                    <div class="cc-summary-cell">
                                        <span class="cc-calc-label">Gross Margin</span>
                                        <span class="cc-calc-value">0%</span>
                                    </div>
                                    <div class="cc-summary-cell">
                                        <span class="cc-calc-label">Billing Amount</span>
                                        <span class="cc-calc-value">₹0</span>
                                    </div>
                                    <div class="cc-summary-cell">
                                        <span class="cc-calc-label">Billing Cycle</span>
                                        <span class="cc-calc-value">-</span>
                                    </div>
                                </div>
                                <div class="cc-info-banner is-success mt-3">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>No billing or fees applicable for this placement.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Contract Staffing fields: billed recurring, margin-based --}}
                        <div id="ccContractFields" class="d-none">
                            <div class="cc-section">
                                <div class="cc-section-title"><i class="fa-solid fa-file-invoice-dollar"></i> 1. Billing Details</div>
                                <div class="cc-section-sub">Define the billing amount and billing cycle for this contract.</div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="cc-field-label">Billing Amount (₹) *</label>
                                        <input type="text" inputmode="numeric" class="form-control form-control-solid" id="ccBillingAmount" value="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="cc-field-label">Billing Cycle *</label>
                                        <select class="form-select form-select-solid" id="ccBillingCycle">
                                            <option value="monthly">Monthly</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="hourly">Hourly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cc-helper-text" id="ccAmountWords">Zero Only</div>
                                <div class="cc-info-banner mt-3">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>This amount will be billed to the client as per the selected billing cycle.</span>
                                </div>
                            </div>

                            <div class="cc-section mb-0">
                                <div class="cc-section-title"><i class="fa-solid fa-calculator"></i> 2. Commercial Details</div>
                                <div class="cc-section-sub">Margin and profit are calculated automatically.</div>
                                <div class="row g-3">
                                    <div class="col-4">
                                        <label class="cc-field-label">Gross Margin (%) *</label>
                                        <input type="text" inputmode="numeric" class="form-control form-control-solid" id="ccMarginPct" value="20">
                                    </div>
                                    <div class="col-4">
                                        <label class="cc-field-label">Profit (₹)</label>
                                        <input type="text" class="form-control form-control-solid" id="ccProfitAmt" value="0" disabled>
                                        <span class="cc-auto-badge">Auto Calculated</span>
                                    </div>
                                    <div class="col-4">
                                        <label class="cc-field-label">Margin Amount (₹)</label>
                                        <input type="text" class="form-control form-control-solid" id="ccMarginAmt" value="0" disabled>
                                        <span class="cc-auto-badge">Auto Calculated</span>
                                    </div>
                                </div>
                                <div class="cc-info-banner is-success mt-3" id="ccMarginHealthBanner">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span id="ccMarginHealthText">Gross Margin is healthy (Above 20%).</span>
                                </div>
                            </div>
                        </div>

                        {{-- Full-time Hiring fields: one-time placement fee + replacement policy --}}
                        <div id="ccFulltimeFields" class="d-none">
                            <div class="cc-section">
                                <div class="cc-section-title"><i class="fa-solid fa-file-invoice-dollar"></i> 1. Placement Fee</div>
                                <div class="cc-section-sub">Define the placement fee and how it will be calculated.</div>

                                <label class="cc-field-label">Fee Method *</label>
                                <div class="cc-option-grid mb-4" id="ccFeeMethodGrid">
                                    <label class="cc-option-card is-selected" data-value="percentage">
                                        <span class="cc-option-icon">%</span>
                                        <span class="cc-option-label">Percentage<br><small class="text-muted fw-normal">Calculate fee as a percentage</small></span>
                                        <span class="cc-option-radio"></span>
                                    </label>
                                    <label class="cc-option-card" data-value="fixed">
                                        <span class="cc-option-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                                        <span class="cc-option-label">Fixed Amount<br><small class="text-muted fw-normal">Set a fixed placement fee</small></span>
                                        <span class="cc-option-radio"></span>
                                    </label>
                                </div>

                                <div id="ccFeePercentageBlock">
                                    <label class="cc-field-label">Calculation Base *</label>
                                    <div class="cc-option-grid mb-4" id="ccCalcBaseGrid">
                                        <label class="cc-option-card is-selected" data-value="offered">
                                            <span class="cc-option-label">Offered Amount<br><small class="text-muted fw-normal">Calculate on candidate offered amount</small></span>
                                            <span class="cc-option-radio"></span>
                                        </label>
                                        <label class="cc-option-card" data-value="agreement">
                                            <span class="cc-option-label">Agreement Amount<br><small class="text-muted fw-normal">Calculate on agreed amount</small></span>
                                            <span class="cc-option-radio"></span>
                                        </label>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="cc-field-label" id="ccCalcBaseLabel">Offered Amount (CTC) <span class="text-muted fw-normal">(Read Only)</span></label>
                                            <input type="text" class="form-control form-control-solid" id="ccCalcBaseAmount" value="0" disabled>
                                        </div>
                                        <div class="col-6">
                                            <label class="cc-field-label">Placement Fee (%) *</label>
                                            <input type="text" inputmode="numeric" class="form-control form-control-solid" id="ccFeePct" value="15">
                                        </div>
                                    </div>
                                    <div class="cc-calc-preview mt-3">
                                        <div class="cc-calc-preview-title">Calculation Preview</div>
                                        <div class="cc-calc-preview-row">
                                            <div class="cc-calc-cell">
                                                <span class="cc-calc-label">Base Amount</span>
                                                <span class="cc-calc-value">₹<span id="ccCalcBaseDisplay">0</span></span>
                                            </div>
                                            <span class="cc-calc-op">x</span>
                                            <div class="cc-calc-cell">
                                                <span class="cc-calc-label">Fee %</span>
                                                <span class="cc-calc-value"><span id="ccCalcPctDisplay">15</span>%</span>
                                            </div>
                                            <span class="cc-calc-op">=</span>
                                            <div class="cc-calc-cell">
                                                <span class="cc-calc-label">Placement Fee</span>
                                                <span class="cc-calc-value text-success" id="ccCalcResult">₹0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="ccFeeFixedBlock" class="d-none">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="cc-field-label">Placement Fee (₹) *</label>
                                            <input type="text" inputmode="numeric" class="form-control form-control-solid" id="ccFixedFee" value="0">
                                        </div>
                                        <div class="col-6">
                                            <div class="cc-total-box">
                                                <span class="cc-field-label mb-1">Total Placement Fee</span>
                                                <span class="text-success fw-bolder fs-4" id="ccFixedFeeTotal">₹0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cc-section">
                                <div class="cc-section-title"><i class="fa-solid fa-shield-halved"></i> 2. Replacement Policy</div>
                                <div class="cc-section-sub">Define the replacement period in case of early exit.</div>
                                <label class="cc-field-label">Replacement Period *</label>
                                <select class="form-select form-select-solid mb-3" id="ccReplacementPeriod">
                                    <option value="30">30 Days</option>
                                    <option value="60">60 Days</option>
                                    <option value="90" selected>90 Days</option>
                                    <option value="180">180 Days</option>
                                </select>
                                <div class="cc-info-banner is-success mb-3">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>If the candidate leaves within <strong id="ccReplacementPeriodText">90</strong> days, a free replacement will be provided.</span>
                                </div>

                                <a href="javascript:void(0);" class="cc-toggle-link" id="ccToggleReplacementRequest">
                                    <i class="fa-solid fa-plus"></i> Log a Replacement Request
                                </a>

                                <div id="ccReplacementRequestBlock" class="cc-subsection d-none mt-3">
                                    <div class="cc-section-title fs-8 mb-0">Replacement Request</div>
                                    <div class="cc-section-sub">Manage replacement requests and track the status.</div>
                                    <label class="cc-field-label">Replacement Status *</label>
                                    <select class="form-select form-select-solid mb-3" id="ccReplacementStatus">
                                        <option value="requested">Requested</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="cc-field-label">Reason for Replacement *</label>
                                            <select class="form-select form-select-solid" id="ccReplacementReason">
                                                <option>Candidate Absconded</option>
                                                <option>Performance Issue</option>
                                                <option>Skill Mismatch</option>
                                                <option>Personal Reasons</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="cc-field-label">Requested On *</label>
                                            <input type="date" class="form-control form-control-solid" id="ccReplacementRequestedOn">
                                        </div>
                                    </div>
                                    <label class="cc-field-label mt-3">Replacement Status (Progress)</label>
                                    <div class="cc-progress-row">
                                        <span>Request Received</span>
                                        <span class="badge badge-light-warning">In Progress</span>
                                    </div>
                                    <div class="cc-info-banner mt-3">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>You will be able to manage the detailed replacement workflow from the Placement details page.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="cc-section mb-0">
                                <div class="cc-section-title">3. Additional Details <span class="cc-step-suffix">(Optional)</span></div>
                                <div class="cc-section-sub">Add any optional notes related to this commercial configuration.</div>
                                <label class="cc-field-label">Internal Note</label>
                                <textarea class="form-control form-control-solid" id="ccInternalNote" maxlength="500" rows="3" placeholder="Add any internal notes here..."></textarea>
                                <div class="text-end text-muted fs-9 mt-1"><span id="ccNoteCount">0</span> / 500</div>
                            </div>
                        </div>
                    </div>

                    <div id="ccTabNotes" class="d-none text-center text-muted py-10 fs-8">
                        <i class="fa-regular fa-note-sticky fs-2 d-block mb-2"></i>
                        No notes yet for this placement's commercial terms.
                    </div>
                    <div id="ccTabHistory" class="d-none text-center text-muted py-10 fs-8">
                        <i class="fa-solid fa-clock-rotate-left fs-2 d-block mb-2"></i>
                        No configuration history yet.
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 px-6 py-4">
                <input type="hidden" id="ccCandidateId" value="">
                <input type="hidden" id="ccIsBench" value="0">
                <button type="button" class="btn btn-light" id="kt_commercialConfig_cancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="kt_commercialConfig_save">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Save Configuration
                </button>
            </div>
        </div>
    </div>

    {{-- =====================================================
         Assignment Details drawer — opened from the "Change" link in the
         Current Assignment / Replacement column. No assignment_details /
         replacement_request table or endpoint exists yet, so — same as the
         Commercial Configuration drawer — this is a view-only calculator:
         no AJAX calls, "Save Changes" just shows a heads-up and closes.
         The candidate summary card, the pre-selected status, Employment
         Type, Client Location, and the "Started On / Total Duration"
         figures are real, seeded from the clicked row's actual data.
    ====================================================== --}}
    <div id="kt_assignmentDetails_toggle"></div>
    <div id="kt_assignmentDetails" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="assignment-details"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '480px', 'lg': '640px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_assignmentDetails_toggle" data-kt-drawer-close="#kt_assignmentDetails_close">
        <div class="card shadow-none rounded-0 w-100 h-100" style="display:flex; flex-direction:column;">
            <div class="card-header align-items-start px-6 py-5">
                <div class="d-flex flex-column">
                    <h2 class="fw-bolder text-dark mb-1">Assignment Details</h2>
                    <div class="text-muted fs-8">Manage current project and assignment details.</div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_assignmentDetails_close">
                        <i class="fa-solid fa-xmark fs-3"></i>
                    </button>
                </div>
            </div>
            <div class="card-body px-6 py-5" id="kt_assignmentDetails_body" style="overflow-y:auto; flex-grow:1;">

                <div class="ad-candidate-card mb-5">
                    <div class="ad-candidate-avatar placed-avatar" id="adCandAvatar"></div>
                    <div>
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary" id="adCandName">-</a>
                        <div class="text-muted fs-8" id="adCandDesignation">-</div>
                        <div class="d-flex align-items-center gap-3 mt-1 fs-9 text-muted">
                            <span><i class="fa-solid fa-briefcase me-1"></i><span id="adCandExpLoc">-</span></span>
                            <span><i class="fa-solid fa-building me-1"></i><span id="adCandClient">-</span></span>
                        </div>
                    </div>
                </div>

                {{-- Step 1: Assignment Status --}}
                <div class="cc-step-head">
                    <span class="cc-step-num">1</span>
                    <span class="cc-step-title">Assignment Status</span>
                </div>
                <div class="ad-status-list mb-5" id="adStatusGrid">
                    <label class="ad-status-card is-selected" data-value="working" data-tone="success">
                        <span class="ad-status-radio"></span>
                        <span>
                            <span class="ad-status-title">Currently Working</span>
                            <span class="ad-status-sub">Candidate is currently working with the client.</span>
                        </span>
                    </label>
                    <label class="ad-status-card" data-value="completed" data-tone="info">
                        <span class="ad-status-radio"></span>
                        <span>
                            <span class="ad-status-title">Project Completed</span>
                            <span class="ad-status-sub">Project has been successfully completed.</span>
                        </span>
                    </label>
                    <label class="ad-status-card" data-value="left" data-tone="danger">
                        <span class="ad-status-radio"></span>
                        <span>
                            <span class="ad-status-title">Candidate Left</span>
                            <span class="ad-status-sub">Candidate is no longer working with the client.</span>
                        </span>
                    </label>
                </div>

                {{-- Step 2: Currently Working fields --}}
                <div id="adWorkingFields">
                    <div class="cc-step-head">
                        <span class="cc-step-num">2</span>
                        <span class="cc-step-title">Assignment Details</span>
                    </div>
                    <div class="cc-section">
                        <label class="cc-field-label">Expected End Date *</label>
                        <input type="date" class="form-control form-control-solid mb-3" id="adExpectedEndDate">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="cc-field-label">Employment Type</label>
                                <select class="form-select form-select-solid" id="adEmploymentType">
                                    <option value="Full-time">Full-time</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Hourly">Hourly</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="cc-field-label">Client Location</label>
                                <select class="form-select form-select-solid" id="adClientLocation">
                                    <option>Bangalore</option>
                                    <option>Pune</option>
                                    <option>Hyderabad</option>
                                    <option>Mumbai</option>
                                    <option>Remote</option>
                                </select>
                            </div>
                        </div>
                        <label class="cc-field-label">Remarks (Optional)</label>
                        <textarea class="form-control form-control-solid mb-1" id="adWorkingRemarks" maxlength="300" rows="2" placeholder="Add any remarks (optional)..."></textarea>
                        <div class="text-end text-muted fs-9 mb-3"><span id="adWorkingRemarksCount">0</span> / 300</div>

                        <div class="ad-summary-box is-success">
                            <i class="fa-solid fa-circle-check"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Assignment Active</div>
                                <div class="fs-9">Candidate is currently working at the client location.</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-9 text-muted">Started On</div>
                                <div class="fw-bold" id="adStartedOn">-</div>
                                <div class="fs-9 text-muted mt-1">Total Duration</div>
                                <div class="fw-bold" id="adTotalDuration">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Project Completed fields --}}
                <div id="adCompletedFields" class="d-none">
                    <div class="cc-step-head">
                        <span class="cc-step-num">2</span>
                        <span class="cc-step-title">Assignment Details</span>
                    </div>
                    <div class="cc-section">
                        <label class="cc-field-label">Completed On *</label>
                        <input type="date" class="form-control form-control-solid mb-3" id="adCompletedOn">
                        <label class="cc-field-label">Project Outcome</label>
                        <select class="form-select form-select-solid mb-3" id="adProjectOutcome">
                            <option>Successful</option>
                            <option>Partially Successful</option>
                            <option>Ended Early</option>
                        </select>
                        <label class="cc-field-label">Remarks (Optional)</label>
                        <textarea class="form-control form-control-solid mb-1" id="adCompletedRemarks" maxlength="300" rows="2" placeholder="Add any remarks (optional)..."></textarea>
                        <div class="text-end text-muted fs-9 mb-3"><span id="adCompletedRemarksCount">0</span> / 300</div>

                        <div class="ad-summary-box is-info">
                            <i class="fa-solid fa-circle-check"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Project Completed</div>
                                <div class="fs-9">This assignment has been marked as completed.</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-9 text-muted">Started On</div>
                                <div class="fw-bold" id="adCompletedStartedOn">-</div>
                                <div class="fs-9 text-muted mt-1">Total Duration</div>
                                <div class="fw-bold" id="adCompletedTotalDuration">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 + 3: Candidate Left fields + Replacement Management --}}
                <div id="adLeftFields" class="d-none">
                    <div class="cc-step-head">
                        <span class="cc-step-num">2</span>
                        <span class="cc-step-title">Assignment Details</span>
                    </div>
                    <div class="cc-section">
                        <label class="cc-field-label">Last Working Day *</label>
                        <input type="date" class="form-control form-control-solid mb-3" id="adLastWorkingDay">
                        <label class="cc-field-label">Reason *</label>
                        <select class="form-select form-select-solid mb-3" id="adExitReason">
                            <option>Candidate Resigned</option>
                            <option>Performance Issue</option>
                            <option>Client Ended Contract</option>
                            <option>Candidate Absconded</option>
                            <option>Other</option>
                        </select>
                        <label class="cc-field-label">Exit Remarks (Optional)</label>
                        <textarea class="form-control form-control-solid mb-1" id="adExitRemarks" maxlength="300" rows="2" placeholder="Add any exit remarks (optional)..."></textarea>
                        <div class="text-end text-muted fs-9" id="adExitRemarksCountWrap"><span id="adExitRemarksCount">0</span> / 300</div>
                    </div>

                    <div class="cc-step-head">
                        <span class="cc-step-num">3</span>
                        <span class="cc-step-title">Replacement Management</span>
                    </div>
                    <div class="cc-section">
                        <label class="cc-field-label">Replacement Required *</label>
                        <div class="ad-yesno-group mb-3" id="adReplacementRequired">
                            <label class="ad-yesno-option" data-value="no">
                                <span class="ad-yesno-radio"></span> No
                            </label>
                            <label class="ad-yesno-option is-selected is-danger" data-value="yes">
                                <span class="ad-yesno-radio"></span> Yes
                            </label>
                        </div>

                        <div id="adReplacementBlock">
                            <label class="cc-field-label">Replacement Status *</label>
                            <select class="form-select form-select-solid mb-3" id="adReplacementStatus">
                                <option>Requested</option>
                                <option selected>In Progress</option>
                                <option>Fulfilled</option>
                            </select>
                            <label class="cc-field-label">Replacement Requested On *</label>
                            <input type="date" class="form-control form-control-solid mb-3" id="adReplacementRequestedOn">
                            <label class="cc-field-label">Replacement Reason (Optional)</label>
                            <textarea class="form-control form-control-solid mb-1" id="adReplacementReason" maxlength="300" rows="2" placeholder="Add a replacement reason (optional)..."></textarea>
                            <div class="text-end text-muted fs-9 mb-3"><span id="adReplacementReasonCount">0</span> / 300</div>
                        </div>

                        <div class="ad-summary-box is-danger">
                            <i class="fa-solid fa-user-xmark"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold mb-1">Candidate Left</div>
                                <div class="ad-summary-kv"><span>Last Working Day</span><strong id="adSummaryLastDay">-</strong></div>
                                <div class="ad-summary-kv"><span>Reason</span><strong id="adSummaryReason">-</strong></div>
                                <div class="ad-summary-kv" id="adSummaryReplacementStatusRow"><span>Replacement Status</span><strong id="adSummaryReplacementStatus">-</strong></div>
                                <div class="ad-summary-kv" id="adSummaryReplacementDateRow"><span>Replacement Requested On</span><strong id="adSummaryReplacementDate">-</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Internal Notes — common to every status --}}
                <div class="cc-step-head">
                    <span class="cc-step-num" id="adNotesStepNum">3</span>
                    <span class="cc-step-title">Internal Notes <span class="cc-step-suffix">(Optional)</span></span>
                </div>
                <div class="cc-section mb-0">
                    <label class="cc-field-label">Internal Notes</label>
                    <textarea class="form-control form-control-solid" id="adInternalNotes" maxlength="500" rows="3" placeholder="Add any internal notes..."></textarea>
                    <div class="text-end text-muted fs-9 mt-1"><span id="adNotesCount">0</span> / 500</div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 px-6 py-4">
                <input type="hidden" id="adCandidateId" value="">
                <input type="hidden" id="adIsBench" value="0">
                <button type="button" class="btn btn-light" id="kt_assignmentDetails_cancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="kt_assignmentDetails_save">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
@endsection



@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

    <script>
        $(document).ready(function() {
            var searchDebounceTimer = null;
            $('#kt_tagify_2').on('input keyup', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    fetchBenchBenchCandidatesCandidateList(1);
                }, 300);
            });
        });

        function filtersdataget() {
            fetchBenchBenchCandidatesCandidateList(1);
        }
    </script>

    <script>
        $(document).on('click', '.hm-listing', function() {
            $('#kt_activitiesHM_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesHM_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesHM_body').html(response.view);
                            }
                        }, 500);
                }
            });
        });

        $(document).on('click', '.stage_sub_stage', function() {
            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.bench_application_status', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                        $('#stage_sub_stage').modal('show');
                    }
                }
            });
        });

        $(document).ready(function() {
            fetchBenchBenchCandidatesCandidateList(1);
            fetchPlacedListingStats();
            initPlacedListingReverbSync();

            $(document).on('click', '#paginationbenchCandidatePlace .pagi-page, #paginationbenchCandidatePlace .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
                fetchBenchBenchCandidatesCandidateList(page);
            });

            $(document).on('change', '#paginationbenchCandidatePlace .pagi-perpage, #paginationbenchCandidatePlace #rowsPerPageSelect', function() {
                const selectedPerPage = $(this).val();
                if (selectedPerPage) {
                    localStorage.setItem('candidatePerPage', selectedPerPage);
                }
                fetchBenchBenchCandidatesCandidateList(1);
            });

            // Master checkbox toggle for table
            $(document).on('change', '#myTable thead .form-check-input', function() {
                $('.chk_list_item').prop('checked', $(this).is(':checked'));
            });
        });

        // =====================================================
        // Commercial Configuration drawer — pure client-side calculator.
        // No AJAX calls here at all: opening the drawer, switching options,
        // and recalculating margin/profit all happen instantly in-browser.
        // =====================================================
        function ccNumberToWordsIndian(num) {
            num = Math.floor(Math.abs(num || 0));
            if (num === 0) return 'Zero';
            var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            function twoDigits(n) {
                if (n < 20) return ones[n];
                return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
            }
            function threeDigits(n) {
                var s = '';
                if (n >= 100) {
                    s += ones[Math.floor(n / 100)] + ' Hundred';
                    n = n % 100;
                    if (n) s += ' ';
                }
                if (n) s += twoDigits(n);
                return s;
            }

            var crore = Math.floor(num / 10000000); num %= 10000000;
            var lakh = Math.floor(num / 100000); num %= 100000;
            var thousand = Math.floor(num / 1000); num %= 1000;
            var rest = num;

            var parts = [];
            if (crore) parts.push(threeDigits(crore) + ' Crore');
            if (lakh) parts.push(twoDigits(lakh) + ' Lakh');
            if (thousand) parts.push(twoDigits(thousand) + ' Thousand');
            if (rest) parts.push(threeDigits(rest));

            return parts.join(' ') || 'Zero';
        }

        function ccRecalc() {
            var amount = parseFloat(($('#ccBillingAmount').val() || '0').toString().replace(/,/g, '')) || 0;
            var marginPct = parseFloat($('#ccMarginPct').val()) || 0;
            var marginAmt = Math.round(amount * marginPct / 100);

            $('#ccProfitAmt').val(marginAmt.toLocaleString('en-IN'));
            $('#ccMarginAmt').val(marginAmt.toLocaleString('en-IN'));
            $('#ccAmountWords').text(ccNumberToWordsIndian(amount) + ' Only');

            var isContract = $('#ccModelGrid .cc-option-card[data-value="contract"]').hasClass('is-selected');
            var termLabel = isContract ? 'Gross Margin' : 'Placement Fee';
            var banner = $('#ccMarginHealthBanner');
            if (marginPct >= 20) {
                banner.removeClass('is-danger').addClass('is-success');
                banner.find('i').attr('class', 'fa-solid fa-circle-check');
                $('#ccMarginHealthText').text(termLabel + ' is healthy (Above 20%).');
            } else {
                banner.removeClass('is-success').addClass('is-danger');
                banner.find('i').attr('class', 'fa-solid fa-triangle-exclamation');
                $('#ccMarginHealthText').text(termLabel + ' is low (Below 20%).');
            }
        }

        // Service Type (Free/Paid) and Commercial Model (Contract/Full-time) are independent
        // selections — the model stays pickable even for a Free Service, per the design.
        // Together they decide which single General-tab field block is visible.
        function ccUpdateGeneralTab() {
            var isFree = $('#ccServiceTypeGrid .cc-option-card[data-value="free"]').hasClass('is-selected');
            var isContract = $('#ccModelGrid .cc-option-card[data-value="contract"]').hasClass('is-selected');
            var modelName = isContract ? 'Contract Staffing' : 'Full-time Hiring';

            $('#ccFreeServiceFields').toggleClass('d-none', !isFree);
            $('#ccContractFields').toggleClass('d-none', isFree || !isContract);
            $('#ccFulltimeFields').toggleClass('d-none', isFree || isContract);

            $('#ccModelStepSuffix').text('(' + (isFree ? 'Free Service' : 'Paid Service') + ')');
            $('#ccModelBanner').html(isFree
                ? 'You are configuring a <strong>Free Service</strong> for <strong id="ccModelBannerText">' + modelName + '</strong>.'
                : 'You are configuring terms for <strong id="ccModelBannerText">' + modelName + '</strong>.');

            if (!isFree) {
                if (isContract) { ccRecalc(); } else { ccFulltimeRecalc(); }
            }
        }

        $(document).on('click', '#ccServiceTypeGrid .cc-option-card', function() {
            $('#ccServiceTypeGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            ccUpdateGeneralTab();
        });

        $(document).on('click', '#ccModelGrid .cc-option-card', function() {
            $('#ccModelGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            ccUpdateGeneralTab();
        });

        $(document).on('click', '.cc-tab', function() {
            $('.cc-tab').removeClass('is-active');
            $(this).addClass('is-active');
            var tab = $(this).data('tab');
            $('#ccTabGeneral, #ccTabNotes, #ccTabHistory').addClass('d-none');
            $('#ccTab' + tab.charAt(0).toUpperCase() + tab.slice(1)).removeClass('d-none');
        });

        $(document).on('input', '#ccBillingAmount, #ccMarginPct', ccRecalc);
        $(document).on('blur', '#ccBillingAmount', function() {
            var amount = parseFloat(($(this).val() || '0').toString().replace(/,/g, '')) || 0;
            $(this).val(amount.toLocaleString('en-IN'));
        });

        // ---- Full-time Hiring: Placement Fee ----
        function ccFulltimeRecalc() {
            var isPercentage = $('#ccFeeMethodGrid .cc-option-card[data-value="percentage"]').hasClass('is-selected');
            $('#ccFeePercentageBlock').toggleClass('d-none', !isPercentage);
            $('#ccFeeFixedBlock').toggleClass('d-none', isPercentage);

            if (isPercentage) {
                var base = parseFloat(($('#ccCalcBaseAmount').val() || '0').toString().replace(/,/g, '')) || 0;
                var pct = parseFloat($('#ccFeePct').val()) || 0;
                var fee = Math.round(base * pct / 100);
                $('#ccCalcBaseDisplay').text(base.toLocaleString('en-IN'));
                $('#ccCalcPctDisplay').text(pct);
                $('#ccCalcResult').text('₹' + fee.toLocaleString('en-IN'));
            } else {
                var fixed = parseFloat(($('#ccFixedFee').val() || '0').toString().replace(/,/g, '')) || 0;
                $('#ccFixedFeeTotal').text('₹' + fixed.toLocaleString('en-IN'));
            }
        }

        $(document).on('click', '#ccFeeMethodGrid .cc-option-card', function() {
            $('#ccFeeMethodGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            ccFulltimeRecalc();
        });

        $(document).on('click', '#ccCalcBaseGrid .cc-option-card', function() {
            $('#ccCalcBaseGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            var isOffered = $(this).data('value') === 'offered';
            var amountField = $('#ccCalcBaseAmount');
            if (isOffered) {
                amountField.prop('disabled', true);
                amountField.val($('.commercial-change-btn.is-active-row').data('amount') ? Math.round($('.commercial-change-btn.is-active-row').data('amount')).toLocaleString('en-IN') : amountField.val());
                $('#ccCalcBaseLabel').html('Offered Amount (CTC) <span class="text-muted fw-normal">(Read Only)</span>');
            } else {
                amountField.prop('disabled', false);
                $('#ccCalcBaseLabel').html('Agreement Amount (₹)');
            }
            ccFulltimeRecalc();
        });

        $(document).on('input', '#ccCalcBaseAmount, #ccFeePct, #ccFixedFee', ccFulltimeRecalc);

        $(document).on('change', '#ccReplacementPeriod', function() {
            $('#ccReplacementPeriodText').text($(this).find('option:selected').text().replace(' Days', ''));
        });

        $(document).on('click', '#ccToggleReplacementRequest', function() {
            var block = $('#ccReplacementRequestBlock');
            var isHidden = block.hasClass('d-none');
            block.toggleClass('d-none');
            $(this).html(isHidden
                ? '<i class="fa-solid fa-minus"></i> Hide Replacement Request'
                : '<i class="fa-solid fa-plus"></i> Log a Replacement Request');
        });

        $(document).on('input', '#ccInternalNote', function() {
            $('#ccNoteCount').text($(this).val().length);
        });

        $(document).on('click', '.commercial-change-btn', function(e) {
            e.preventDefault();
            $('.commercial-change-btn').removeClass('is-active-row');
            $(this).addClass('is-active-row');

            var $btn        = $(this);
            var amount      = parseFloat($btn.attr('data-amount')) || 0;
            var empType     = ($btn.attr('data-employment-type') || '').toLowerCase();
            var isContract  = empType.indexOf('contract') !== -1 || empType.indexOf('hour') !== -1;

            // Saved values from ES (empty string if not yet saved)
            var savedServiceType  = $btn.attr('data-service-type') || '';
            var savedModel        = $btn.attr('data-commercial-model') || '';
            var savedFeeMethod    = $btn.attr('data-fee-method') || '';
            var savedFeeBase      = $btn.attr('data-fee-base') || '';
            var savedFeePct       = parseFloat($btn.attr('data-fee-pct')) || 0;
            var savedFeeFixed     = parseFloat($btn.attr('data-fee-fixed')) || 0;
            var savedBillingAmt   = parseFloat($btn.attr('data-billing-amount')) || 0;
            var savedBillingCycle = $btn.attr('data-billing-cycle') || '';
            var savedMarginPct    = parseFloat($btn.attr('data-margin-pct')) || 20;
            var savedRepPeriod    = $btn.attr('data-replacement-period') || '90';
            var savedNote         = $btn.attr('data-internal-note') || '';

            $('#ccCandidateId').val($btn.attr('data-candidate-id') || 0);
            $('#ccIsBench').val($btn.attr('data-is-bench') || 0);

            // Service type
            var serviceType = savedServiceType || 'paid';
            $('#ccServiceTypeGrid .cc-option-card').removeClass('is-selected');
            $('#ccServiceTypeGrid .cc-option-card[data-value="' + serviceType + '"]').addClass('is-selected');

            // Free Service fields
            $('#ccFreeReason').val('Goodwill');
            $('#ccFreeEffectiveDate').val(new Date().toISOString().slice(0, 10));

            // Contract fields
            $('#ccMarginPct').val(savedMarginPct || 20);
            $('#ccBillingAmount').val(savedBillingAmt ? Math.round(savedBillingAmt).toLocaleString('en-IN') : (amount ? Math.round(amount).toLocaleString('en-IN') : '0'));
            if (savedBillingCycle) { $('#ccBillingCycle').val(savedBillingCycle); }

            // Full-time fields
            var feeMethod = savedFeeMethod || 'percentage';
            $('#ccFeeMethodGrid .cc-option-card').removeClass('is-selected');
            $('#ccFeeMethodGrid .cc-option-card[data-value="' + feeMethod + '"]').addClass('is-selected');

            var feeBase = savedFeeBase || 'offered';
            $('#ccCalcBaseGrid .cc-option-card').removeClass('is-selected');
            $('#ccCalcBaseGrid .cc-option-card[data-value="' + feeBase + '"]').addClass('is-selected');
            $('#ccCalcBaseLabel').html('Offered Amount (CTC) <span class="text-muted fw-normal">(Read Only)</span>');
            $('#ccCalcBaseAmount').prop('disabled', true).val(amount ? Math.round(amount).toLocaleString('en-IN') : '0');
            $('#ccFeePct').val(savedFeePct || 15);
            $('#ccFixedFee').val(savedFeeFixed ? Math.round(savedFeeFixed).toLocaleString('en-IN') : (amount ? Math.round(amount).toLocaleString('en-IN') : '0'));

            $('#ccReplacementPeriod').val(savedRepPeriod || '90');
            $('#ccReplacementPeriodText').text(savedRepPeriod || '90');
            $('#ccReplacementRequestBlock').addClass('d-none');
            $('#ccToggleReplacementRequest').html('<i class="fa-solid fa-plus"></i> Log a Replacement Request');

            $('#ccInternalNote').val(savedNote);
            $('#ccNoteCount').text(savedNote.length);

            // Trigger model selection (contract/fulltime/etc.) using saved value or fallback
            var modelToSelect = savedModel || (isContract ? 'contract' : 'fulltime');
            $('#ccModelGrid .cc-option-card[data-value="' + modelToSelect + '"]').click();

            $('.cc-tab').removeClass('is-active');
            $('.cc-tab[data-tab="general"]').addClass('is-active');
            $('#ccTabGeneral').removeClass('d-none');
            $('#ccTabNotes, #ccTabHistory').addClass('d-none');

            $('#kt_commercialConfig_toggle').trigger('click');
        });

        $('#kt_commercialConfig_cancel').on('click', function() {
            $('#kt_commercialConfig_close').trigger('click');
        });

        $('#kt_commercialConfig_save').on('click', function() {
            var candidateId = $('#ccCandidateId').val();
            if (!candidateId || candidateId == '0') {
                Swal.fire({ icon: 'warning', text: 'Could not identify candidate. Please try again.', confirmButtonText: 'OK' });
                return;
            }
            var serviceType = $('#ccServiceTypeGrid .cc-option-card.is-selected').attr('data-value') || 'paid';
            var commercialModel = $('#ccModelGrid .cc-option-card.is-selected').attr('data-value') || 'fulltime';
            var feeMethod = $('#ccFeeMethodGrid .cc-option-card.is-selected').attr('data-value') || 'percentage';
            var feeBase = $('#ccCalcBaseGrid .cc-option-card.is-selected').attr('data-value') || 'offered';
            var feePct = $('#ccFeePct').val() || 0;
            var feeFixed = $('#ccFixedFee').val() || 0;
            var billingAmount = $('#ccBillingAmount').val() || 0;
            var billingCycle = $('#ccBillingCycle').val() || '';
            var marginPct = $('#ccMarginPct').val() || 0;
            var replacementPeriod = $('#ccReplacementPeriod').val() || '';
            var replacementStatus = $('#ccReplacementStatus').val() || '';
            var replacementRequestedOn = $('#ccReplacementRequestedOn').val() || '';
            var replacementReason = $('#ccReplacementReason').val() || '';
            var freeServiceType = $('#ccFreeServiceTypeField').val() || '';
            var freeReason = $('#ccFreeReason').val() || '';
            var freeEffectiveDate = $('#ccFreeEffectiveDate').val() || '';
            var internalNote = $('#ccInternalNote').val() || '';
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Saving...');
            $.ajax({
                url: '{!! route("service_company.bench-candidates.savePlacedCommercialConfig") !!}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    client_submission_uuid: (window.crypto && crypto.randomUUID ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).substring(2))),
                    candidate_id: candidateId,
                    is_bench: $('#ccIsBench').val(),
                    service_type: serviceType,
                    commercial_model: commercialModel,
                    fee_method: feeMethod,
                    fee_base: feeBase,
                    fee_pct: feePct,
                    fee_fixed: feeFixed,
                    billing_amount: billingAmount,
                    billing_cycle: billingCycle,
                    margin_pct: marginPct,
                    replacement_period: replacementPeriod,
                    replacement_status: replacementStatus,
                    replacement_requested_on: replacementRequestedOn,
                    replacement_reason: replacementReason,
                    free_service_type: freeServiceType,
                    free_reason: freeReason,
                    free_effective_date: freeEffectiveDate,
                    internal_note: internalNote
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i>Save Configuration');
                    if (res.status === 'success') {
                        if (window.PlacedListingStore && window.PlacedListingStore.cache) {
                            window.PlacedListingStore.cache.clear();
                        }
                        if (typeof PlacedListingDB !== 'undefined') {
                            PlacedListingDB.clearSnapshots();
                        }
                        if (typeof PlacedListingChannel !== 'undefined') {
                            PlacedListingChannel.broadcast({
                                type: 'COMMERCIAL_UPDATED',
                                candidateId: candidateId,
                                serviceType: serviceType,
                                commercialModel: commercialModel,
                                feeMethod: feeMethod,
                                feeBase: feeBase,
                                feePct: feePct,
                                feeFixed: feeFixed,
                                billingAmount: billingAmount,
                                billingCycle: billingCycle,
                                marginPct: marginPct,
                                replacementPeriod: replacementPeriod,
                                internalNote: internalNote
                            });
                        }
                        // Update commercials cell in the matching row
                        var $row = $('tr.placed-row[data-candidate-id="' + candidateId + '"]');
                        if ($row.length) {
                            // Build fee label from saved values
                            var feeLabel = '';
                            if (serviceType === 'free') {
                                feeLabel = 'Free Placement';
                            } else if (feeMethod === 'percentage' && feePct) {
                                feeLabel = feePct + '% (' + (feeBase === 'offered' ? 'CTC' : 'Billing') + ')';
                            } else if (feeMethod === 'fixed' && feeFixed) {
                                feeLabel = '₹' + parseInt(feeFixed).toLocaleString('en-IN') + ' Fixed Fee';
                            } else {
                                feeLabel = 'Fee Configured';
                            }
                            var modelLabel = commercialModel === 'contract' ? 'Contract' : (commercialModel === 'fulltime' ? 'Full-time' : commercialModel);
                            $row.find('[data-cc-fee]').html(feeLabel + ' <span class="text-muted fw-normal">(Placement Fee)</span>');
                            $row.find('[data-cc-model]').text(modelLabel);
                        }
                        // Update data-* on the active "Change" button so re-opening shows latest values
                        var $activeBtn = $('.commercial-change-btn.is-active-row');
                        $activeBtn.attr('data-service-type', serviceType);
                        $activeBtn.attr('data-commercial-model', commercialModel);
                        $activeBtn.attr('data-fee-method', feeMethod);
                        $activeBtn.attr('data-fee-base', feeBase);
                        $activeBtn.attr('data-fee-pct', feePct);
                        $activeBtn.attr('data-fee-fixed', feeFixed);
                        $activeBtn.attr('data-billing-amount', billingAmount);
                        $activeBtn.attr('data-billing-cycle', billingCycle);
                        $activeBtn.attr('data-margin-pct', marginPct);
                        $activeBtn.attr('data-replacement-period', replacementPeriod);
                        $activeBtn.attr('data-internal-note', internalNote);
                        Swal.fire({ icon: 'success', text: res.message, timer: 1500, showConfirmButton: false });
                        setTimeout(function() { $('#kt_commercialConfig_close').trigger('click'); }, 1600);
                    } else {
                        Swal.fire({ icon: 'error', text: res.message || 'Save failed.', confirmButtonText: 'OK' });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i>Save Configuration');
                    Swal.fire({ icon: 'error', text: 'Server error. Please try again.', confirmButtonText: 'OK' });
                }
            });
        });

        // =====================================================
        // Assignment Details drawer — pure client-side, no AJAX.
        // Assignment Status / Replacement Management / Internal Notes have
        // no backing table yet, so nothing here persists; "Save Changes"
        // is an honest preview-only action, same as Commercial Configuration.
        // =====================================================
        function adDaysBetween(fromIso, toIso) {
            var from = new Date(fromIso);
            var to = toIso ? new Date(toIso) : new Date();
            if (isNaN(from.getTime())) return null;
            return Math.max(0, Math.round((to - from) / 86400000));
        }

        function adFormatDate(iso) {
            if (!iso) return '-';
            var d = new Date(iso);
            if (isNaN(d.getTime())) return '-';
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        function adRecalcSummaries() {
            var joinedIso = $('#kt_assignmentDetails').data('joined-iso') || '';

            // Currently Working summary
            $('#adStartedOn').text(adFormatDate(joinedIso));
            var workingDays = adDaysBetween(joinedIso, '');
            $('#adTotalDuration').text(workingDays !== null ? workingDays + ' Days' : '-');

            // Project Completed summary
            $('#adCompletedStartedOn').text(adFormatDate(joinedIso));
            var completedOnIso = $('#adCompletedOn').val();
            var completedDays = adDaysBetween(joinedIso, completedOnIso || '');
            $('#adCompletedTotalDuration').text(completedDays !== null ? completedDays + ' Days' : '-');

            // Candidate Left summary
            $('#adSummaryLastDay').text($('#adLastWorkingDay').val() ? adFormatDate($('#adLastWorkingDay').val()) : '-');
            $('#adSummaryReason').text($('#adExitReason').val() || '-');
            var replacementRequired = $('#adReplacementRequired .ad-yesno-option.is-selected').data('value') === 'yes';
            $('#adSummaryReplacementStatusRow, #adSummaryReplacementDateRow').toggleClass('d-none', !replacementRequired);
            $('#adSummaryReplacementStatus').text(replacementRequired ? ($('#adReplacementStatus').val() || '-') : '-');
            $('#adSummaryReplacementDate').text(replacementRequired && $('#adReplacementRequestedOn').val() ? adFormatDate($('#adReplacementRequestedOn').val()) : '-');
        }

        $(document).on('click', '#adStatusGrid .ad-status-card', function() {
            $('#adStatusGrid .ad-status-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            var status = $(this).data('value');

            $('#adWorkingFields').toggleClass('d-none', status !== 'working');
            $('#adCompletedFields').toggleClass('d-none', status !== 'completed');
            $('#adLeftFields').toggleClass('d-none', status !== 'left');
            $('#adNotesStepNum').text(status === 'left' ? '4' : '3');

            adRecalcSummaries();
        });

        $(document).on('click', '#adReplacementRequired .ad-yesno-option', function() {
            $('#adReplacementRequired .ad-yesno-option').removeClass('is-selected');
            $(this).addClass('is-selected');
            $('#adReplacementBlock').toggleClass('d-none', $(this).data('value') !== 'yes');
            adRecalcSummaries();
        });

        $(document).on('input change', '#adCompletedOn, #adLastWorkingDay, #adExitReason, #adReplacementStatus, #adReplacementRequestedOn', adRecalcSummaries);

        // Character counters — shared pattern for every "(Optional)" textarea in this drawer.
        [
            ['#adWorkingRemarks', '#adWorkingRemarksCount'],
            ['#adCompletedRemarks', '#adCompletedRemarksCount'],
            ['#adExitRemarks', '#adExitRemarksCount'],
            ['#adReplacementReason', '#adReplacementReasonCount'],
            ['#adInternalNotes', '#adNotesCount']
        ].forEach(function(pair) {
            $(document).on('input', pair[0], function() {
                $(pair[1]).text($(this).val().length);
            });
        });

        $(document).on('click', '.assignment-change-btn', function(e) {
            e.preventDefault();

            var $btn = $(this);

            $('#adCandidateId').val($btn.attr('data-candidate-id') || 0);
            $('#adIsBench').val($btn.attr('data-is-bench') || 0);

            var name           = $btn.attr('data-name') || '-';
            var designation    = $btn.attr('data-designation') || '-';
            var expLoc         = $btn.attr('data-exp-loc') || '-';
            var client         = $btn.attr('data-client') || '-';
            var employmentType = ($btn.attr('data-employment-type') || '').toString();
            var joinedIso      = ($btn.attr('data-joined-date') || '').toString();
            var avatarClass    = ($btn.attr('data-avatar-class') || '').toString();
            var avatarLetter   = ($btn.attr('data-avatar-letter') || '').toString();

            // Saved assignment values
            var savedStatus           = $btn.attr('data-assignment-status') || 'working';
            var savedEndDate          = $btn.attr('data-expected-end-date') || '';
            var savedClientLocation   = $btn.attr('data-client-location') || '';
            var savedExitReason       = $btn.attr('data-exit-reason') || '';
            var savedProjectOutcome   = $btn.attr('data-project-outcome') || '';
            var savedRemarks          = $btn.attr('data-remarks') || '';
            var savedInternalNotes    = $btn.attr('data-internal-notes') || '';
            var savedRepRequired      = parseInt($btn.attr('data-replacement-required')) || 0;
            var savedRepStatus        = $btn.attr('data-replacement-status') || '';
            var savedRepRequestedOn   = $btn.attr('data-replacement-requested-on') || '';
            var savedRepReason        = $btn.attr('data-replacement-reason') || '';

            $('#adCandName').text(name);
            $('#adCandDesignation').text(designation);
            $('#adCandExpLoc').text(expLoc || '-');
            $('#adCandClient').text(client || '-');
            $('#adCandAvatar').attr('class', 'ad-candidate-avatar placed-avatar ' + avatarClass).text(avatarLetter);
            $('#kt_assignmentDetails').data('joined-iso', joinedIso);

            // Select saved status tab
            $('#adStatusGrid .ad-status-card').removeClass('is-selected');
            $('#adStatusGrid .ad-status-card[data-value="' + savedStatus + '"]').addClass('is-selected');
            $('#adWorkingFields, #adCompletedFields, #adLeftFields').addClass('d-none');
            $('#adNotesStepNum').text('3');

            if (savedStatus === 'working') {
                $('#adWorkingFields').removeClass('d-none');
                $('#adExpectedEndDate').val(savedEndDate);
                if (employmentType && $('#adEmploymentType option').filter(function() { return $(this).val().toLowerCase() === employmentType.toLowerCase(); }).length) {
                    $('#adEmploymentType').val($('#adEmploymentType option').filter(function() { return $(this).val().toLowerCase() === employmentType.toLowerCase(); }).val());
                } else {
                    $('#adEmploymentType').prop('selectedIndex', 0);
                }
                if (savedClientLocation) { $('#adClientLocation').val(savedClientLocation); } else { $('#adClientLocation').prop('selectedIndex', 0); }
                $('#adWorkingRemarks').val(savedRemarks).trigger('input');

            } else if (savedStatus === 'completed') {
                $('#adCompletedFields').removeClass('d-none');
                $('#adCompletedOn').val(savedEndDate);
                if (savedProjectOutcome) { $('#adProjectOutcome').val(savedProjectOutcome); } else { $('#adProjectOutcome').prop('selectedIndex', 0); }
                $('#adCompletedRemarks').val(savedRemarks).trigger('input');

            } else if (savedStatus === 'left') {
                $('#adLeftFields').removeClass('d-none');
                $('#adLastWorkingDay').val(savedEndDate);
                if (savedExitReason) { $('#adExitReason').val(savedExitReason); } else { $('#adExitReason').prop('selectedIndex', 0); }
                $('#adExitRemarks').val(savedRemarks).trigger('input');
                // Replacement
                $('#adReplacementRequired .ad-yesno-option').removeClass('is-selected');
                $('#adReplacementRequired .ad-yesno-option[data-value="' + (savedRepRequired ? 'yes' : 'no') + '"]').addClass('is-selected');
                if (savedRepRequired) {
                    $('#adReplacementBlock').removeClass('d-none');
                    if (savedRepStatus) { $('#adReplacementStatus').val(savedRepStatus); }
                    $('#adReplacementRequestedOn').val(savedRepRequestedOn);
                    $('#adReplacementReason').val(savedRepReason).trigger('input');
                } else {
                    $('#adReplacementBlock').addClass('d-none');
                }
            } else {
                $('#adWorkingFields').removeClass('d-none');
            }

            $('#adInternalNotes').val(savedInternalNotes).trigger('input');

            adRecalcSummaries();
            $('#kt_assignmentDetails_toggle').trigger('click');
        });

        $('#kt_assignmentDetails_cancel').on('click', function() {
            $('#kt_assignmentDetails_close').trigger('click');
        });

        $('#kt_assignmentDetails_save').on('click', function() {
            var candidateId = $('#adCandidateId').val();
            if (!candidateId || candidateId == '0') {
                Swal.fire({ icon: 'warning', text: 'Could not identify candidate. Please try again.', confirmButtonText: 'OK' });
                return;
            }
            var assignmentStatus = $('#adStatusGrid .ad-status-card.is-selected').attr('data-value') || 'working';
            var expectedEndDate = '';
            var clientLocation = '';
            var remarks = '';
            var exitReason = '';
            var projectOutcome = '';
            var employmentType = '';
            var replacementRequired = 0;
            var replacementStatus = '';
            var replacementRequestedOn = '';
            var replacementReason = '';
            if (assignmentStatus === 'working') {
                expectedEndDate = $('#adExpectedEndDate').val() || '';
                employmentType = $('#adEmploymentType').val() || '';
                clientLocation = $('#adClientLocation').val() || '';
                remarks = $('#adWorkingRemarks').val() || '';
            } else if (assignmentStatus === 'completed') {
                expectedEndDate = $('#adCompletedOn').val() || '';
                projectOutcome = $('#adProjectOutcome').val() || '';
                remarks = $('#adCompletedRemarks').val() || '';
            } else if (assignmentStatus === 'left') {
                expectedEndDate = $('#adLastWorkingDay').val() || '';
                exitReason = $('#adExitReason').val() || '';
                remarks = $('#adExitRemarks').val() || '';
                replacementRequired = $('#adReplacementRequired .ad-yesno-btn.is-selected').attr('data-value') || 0;
                if (parseInt(replacementRequired)) {
                    replacementStatus = $('#adReplacementStatus').val() || '';
                    replacementRequestedOn = $('#adReplacementRequestedOn').val() || '';
                    replacementReason = $('#adReplacementReason').val() || '';
                }
            }
            var internalNotes = $('#adInternalNotes').val() || '';
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Saving...');
            $.ajax({
                url: '{!! route("service_company.bench-candidates.savePlacedAssignmentDetails") !!}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    client_submission_uuid: (window.crypto && crypto.randomUUID ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).substring(2))),
                    candidate_id: candidateId,
                    is_bench: $('#adIsBench').val(),
                    expected_end_date: expectedEndDate,
                    assignment_status: assignmentStatus,
                    employment_type: employmentType,
                    client_location: clientLocation,
                    exit_reason: exitReason,
                    project_outcome: projectOutcome,
                    remarks: remarks,
                    internal_notes: internalNotes,
                    replacement_required: replacementRequired,
                    replacement_status: replacementStatus,
                    replacement_requested_on: replacementRequestedOn,
                    replacement_reason: replacementReason
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i>Save Changes');
                    if (res.status === 'success') {
                        if (window.PlacedListingStore && window.PlacedListingStore.cache) {
                            window.PlacedListingStore.cache.clear();
                        }
                        if (typeof PlacedListingDB !== 'undefined') {
                            PlacedListingDB.clearSnapshots();
                        }
                        if (typeof fetchPlacedListingStats === 'function') {
                            fetchPlacedListingStats();
                        }
                        if (typeof PlacedListingChannel !== 'undefined') {
                            PlacedListingChannel.broadcast({
                                type: 'ASSIGNMENT_UPDATED',
                                candidateId: candidateId,
                                assignmentStatus: assignmentStatus,
                                expectedEndDate: expectedEndDate,
                                employmentType: employmentType,
                                clientLocation: clientLocation,
                                exitReason: exitReason,
                                projectOutcome: projectOutcome,
                                remarks: remarks,
                                internalNotes: internalNotes,
                                replacementRequired: replacementRequired,
                                replacementStatus: replacementStatus,
                                replacementRequestedOn: replacementRequestedOn,
                                replacementReason: replacementReason
                            });
                        }
                        // Update work status cell in the matching row
                        var $row = $('tr.placed-row[data-candidate-id="' + candidateId + '"]');
                        if ($row.length) {
                            var statusMap = {
                                working:   { label: 'Currently Working', icon: 'fa-circle-check', cls: 'is-success' },
                                completed: { label: 'Project Completed',  icon: 'fa-check-double',  cls: 'is-info'    },
                                left:      { label: 'Candidate Left',     icon: 'fa-circle-xmark', cls: 'is-danger'  }
                            };
                            var s = statusMap[assignmentStatus] || statusMap.working;
                            var $pill = $row.find('[data-ad-status]');
                            $pill.removeClass('is-success is-info is-danger').addClass(s.cls);
                            $pill.find('[data-ad-status-icon]').removeClass('fa-circle-check fa-check-double fa-circle-xmark').addClass(s.icon);
                            $pill.find('[data-ad-status-label]').text(s.label);
                            // Update expected end date
                            if (expectedEndDate) {
                                var d = new Date(expectedEndDate);
                                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                var formatted = String(d.getDate()).padStart(2,'0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                                $row.find('[data-ad-end-date]').text(formatted);
                            }
                        }
                        // Update data-* on the active "Change" button so re-opening shows latest values
                        var $activeAssignBtn = $('.assignment-change-btn[data-candidate-id="' + candidateId + '"]');
                        $activeAssignBtn.attr('data-assignment-status', assignmentStatus);
                        $activeAssignBtn.attr('data-expected-end-date', expectedEndDate);
                        $activeAssignBtn.attr('data-client-location', clientLocation);
                        $activeAssignBtn.attr('data-exit-reason', exitReason);
                        $activeAssignBtn.attr('data-project-outcome', projectOutcome);
                        $activeAssignBtn.attr('data-remarks', remarks);
                        $activeAssignBtn.attr('data-internal-notes', internalNotes);
                        $activeAssignBtn.attr('data-replacement-required', replacementRequired);
                        $activeAssignBtn.attr('data-replacement-status', replacementStatus);
                        $activeAssignBtn.attr('data-replacement-requested-on', replacementRequestedOn);
                        $activeAssignBtn.attr('data-replacement-reason', replacementReason);
                        Swal.fire({ icon: 'success', text: res.message, timer: 1500, showConfirmButton: false });
                        setTimeout(function() { $('#kt_assignmentDetails_close').trigger('click'); }, 1600);
                    } else {
                        Swal.fire({ icon: 'error', text: res.message || 'Save failed.', confirmButtonText: 'OK' });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i>Save Changes');
                    Swal.fire({ icon: 'error', text: 'Server error. Please try again.', confirmButtonText: 'OK' });
                }
            });
        });

        // Row delete — no delete route exists for this listing, so this confirms then shows
        // an honest preview notice. It deliberately never removes the row from the table,
        // since that would look like a real deletion succeeded when nothing was persisted.
        $(document).on('click', '.placed-row-delete-btn', function(e) {
            e.preventDefault();
            var name = $(this).data('candidate-name') || 'this candidate';
            Swal.fire({
                icon: 'warning',
                title: 'Delete this placement record?',
                text: 'This will permanently remove ' + name + ' from the Talent Placed list.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                confirmButtonColor: '#f1416c',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Preview Only',
                        text: 'Delete captured for preview — connect this to a delete endpoint to remove it for real.',
                        confirmButtonText: 'Got it'
                    });
                }
            });
        });

        // =========================================================================
        // CLIENT-FIRST ARCHITECTURE: Bounded Browser RAM Store, Helpers, & Renderers
        // =========================================================================
        window.PlacedListingStore = {
            candidates: [],
            total: 0,
            page: 1,
            perPage: parseInt(localStorage.getItem('candidatePerPage') || '10', 10),
            lastPage: 1,
            inFlightRequest: null,
            cache: new Map(), // LRU memory store
            maxCacheEntries: 20,
            companyId: {{ (int) ($company_id ?? auth()->user()->company_id ?? 0) }},
            currencySymbols: {!! json_encode($currencySymbols ?? \App\Helper\GeneralMethod::currencySymbole()) !!},
            salaryModes: {!! json_encode($salaryModes ?? \App\Helper\GeneralMethod::salaryModeOffer()) !!}
        };

        // =========================================================================
        // INDEXEDDB L2 CACHE & MULTI-TAB BROADCASTCHANNEL ARCHITECTURE
        // =========================================================================
        var PlacedListingDB = (function() {
            var DB_NAME = 'JeroPlacedListingDB_' + (window.PlacedListingStore ? window.PlacedListingStore.companyId : 0);
            var DB_VERSION = 1;
            var dbPromise = null;

            function getDB() {
                if (dbPromise) return dbPromise;
                if (!window.indexedDB) {
                    return Promise.resolve(null);
                }
                dbPromise = new Promise(function(resolve) {
                    try {
                        var req = indexedDB.open(DB_NAME, DB_VERSION);
                        req.onupgradeneeded = function(e) {
                            var db = e.target.result;
                            if (!db.objectStoreNames.contains('lookups')) {
                                db.createObjectStore('lookups', { keyPath: 'key' });
                            }
                            if (!db.objectStoreNames.contains('snapshots')) {
                                var store = db.createObjectStore('snapshots', { keyPath: 'key' });
                                store.createIndex('timestamp', 'timestamp', { unique: false });
                            }
                        };
                        req.onsuccess = function(e) {
                            resolve(e.target.result);
                        };
                        req.onerror = function(err) {
                            console.warn('[PlacedListingDB] Open error, falling back to RAM cache:', err);
                            resolve(null);
                        };
                    } catch (err) {
                        console.warn('[PlacedListingDB] Exception opening IndexedDB:', err);
                        resolve(null);
                    }
                });
                return dbPromise;
            }

            return {
                getSnapshot: function(key) {
                    return getDB().then(function(db) {
                        if (!db) return null;
                        return new Promise(function(resolve) {
                            try {
                                var tx = db.transaction('snapshots', 'readonly');
                                var store = tx.objectStore('snapshots');
                                var req = store.get(key);
                                req.onsuccess = function(e) {
                                    var record = e.target.result;
                                    if (!record) { resolve(null); return; }
                                    var now = Date.now();
                                    if (record.ttlMs && (now - record.timestamp > record.ttlMs)) {
                                        PlacedListingDB.deleteSnapshot(key);
                                        resolve(null);
                                    } else {
                                        resolve(record.data);
                                    }
                                };
                                req.onerror = function() { resolve(null); };
                            } catch (e) {
                                resolve(null);
                            }
                        });
                    });
                },

                setSnapshot: function(key, data, ttlMs) {
                    ttlMs = ttlMs || 300000; // 5 minutes TTL
                    return getDB().then(function(db) {
                        if (!db) return;
                        try {
                            var tx = db.transaction('snapshots', 'readwrite');
                            var store = tx.objectStore('snapshots');
                            store.put({
                                key: key,
                                data: data,
                                timestamp: Date.now(),
                                ttlMs: ttlMs
                            });

                            // Bound snapshot storage to max 30 entries (LRU cleanup)
                            var countReq = store.count();
                            countReq.onsuccess = function() {
                                if (countReq.result > 30) {
                                    var index = store.index('timestamp');
                                    var cursorReq = index.openCursor();
                                    cursorReq.onsuccess = function(e) {
                                        var cursor = e.target.result;
                                        if (cursor) {
                                            store.delete(cursor.primaryKey);
                                        }
                                    };
                                }
                            };
                        } catch (e) {
                            console.warn('[PlacedListingDB] setSnapshot error:', e);
                        }
                    });
                },

                deleteSnapshot: function(key) {
                    return getDB().then(function(db) {
                        if (!db) return;
                        try {
                            var tx = db.transaction('snapshots', 'readwrite');
                            tx.objectStore('snapshots').delete(key);
                        } catch (e) {}
                    });
                },

                clearSnapshots: function() {
                    return getDB().then(function(db) {
                        if (!db) return;
                        try {
                            var tx = db.transaction('snapshots', 'readwrite');
                            tx.objectStore('snapshots').clear();
                        } catch (e) {}
                    });
                },

                getLookup: function(key) {
                    return getDB().then(function(db) {
                        if (!db) return null;
                        return new Promise(function(resolve) {
                            try {
                                var tx = db.transaction('lookups', 'readonly');
                                var req = tx.objectStore('lookups').get(key);
                                req.onsuccess = function(e) {
                                    resolve(e.target.result ? e.target.result.value : null);
                                };
                                req.onerror = function() { resolve(null); };
                            } catch (e) { resolve(null); }
                        });
                    });
                },

                setLookup: function(key, value) {
                    return getDB().then(function(db) {
                        if (!db) return;
                        try {
                            var tx = db.transaction('lookups', 'readwrite');
                            tx.objectStore('lookups').put({ key: key, value: value });
                        } catch (e) {}
                    });
                }
            };
        })();

        // =========================================================================
        // MULTI-TAB SYNCHRONIZATION VIA BROADCASTCHANNEL
        // =========================================================================
        var PlacedListingChannel = (function() {
            var channelName = 'jero_placed_listing_' + (window.PlacedListingStore ? window.PlacedListingStore.companyId : 0);
            var channel = null;

            if (typeof window.BroadcastChannel !== 'undefined') {
                try {
                    channel = new BroadcastChannel(channelName);
                    channel.onmessage = function(event) {
                        handleChannelMessage(event.data);
                    };
                } catch (e) {
                    console.warn('[PlacedListingChannel] BroadcastChannel init error:', e);
                }
            } else {
                // Fallback via localStorage storage event for older browsers
                window.addEventListener('storage', function(e) {
                    if (e.key === channelName && e.newValue) {
                        try {
                            var data = JSON.parse(e.newValue);
                            handleChannelMessage(data);
                        } catch (err) {}
                    }
                });
            }

            function handleChannelMessage(msg) {
                if (!msg || !msg.type) return;

                // Invalidate local in-memory L1 cache and IndexedDB L2 snapshots
                if (window.PlacedListingStore && window.PlacedListingStore.cache) {
                    window.PlacedListingStore.cache.clear();
                }
                if (typeof PlacedListingDB !== 'undefined') {
                    PlacedListingDB.clearSnapshots();
                }

                if (msg.type === 'COMMERCIAL_UPDATED') {
                    var candidateId = msg.candidateId;
                    var $row = $('tr.placed-row[data-candidate-id="' + candidateId + '"]');
                    if ($row.length) {
                        var feeLabel = '';
                        if (msg.serviceType === 'free') {
                            feeLabel = 'Free Placement';
                        } else if (msg.feeMethod === 'percentage' && msg.feePct) {
                            feeLabel = msg.feePct + '% (' + (msg.feeBase === 'offered' ? 'CTC' : 'Billing') + ')';
                        } else if (msg.feeMethod === 'fixed' && msg.feeFixed) {
                            feeLabel = '₹' + parseInt(msg.feeFixed).toLocaleString('en-IN') + ' Fixed Fee';
                        } else {
                            feeLabel = 'Fee Configured';
                        }
                        var modelLabel = msg.commercialModel === 'contract' ? 'Contract' : (msg.commercialModel === 'fulltime' ? 'Full-time' : msg.commercialModel);
                        $row.find('[data-cc-fee]').html(escapeHtml(feeLabel) + ' <span class="text-muted fw-normal">(Placement Fee)</span>');
                        $row.find('[data-cc-model]').text(modelLabel);

                        var $btn = $row.find('.commercial-change-btn');
                        if ($btn.length) {
                            $btn.attr('data-service-type', msg.serviceType);
                            $btn.attr('data-commercial-model', msg.commercialModel);
                            $btn.attr('data-fee-method', msg.feeMethod);
                            $btn.attr('data-fee-base', msg.feeBase);
                            $btn.attr('data-fee-pct', msg.feePct);
                            $btn.attr('data-fee-fixed', msg.feeFixed);
                            $btn.attr('data-billing-amount', msg.billingAmount);
                            $btn.attr('data-billing-cycle', msg.billingCycle);
                            $btn.attr('data-margin-pct', msg.marginPct);
                            $btn.attr('data-replacement-period', msg.replacementPeriod);
                            $btn.attr('data-internal-note', msg.internalNote);
                        }
                    }
                } else if (msg.type === 'ASSIGNMENT_UPDATED') {
                    var candidateId = msg.candidateId;
                    var $row = $('tr.placed-row[data-candidate-id="' + candidateId + '"]');
                    if ($row.length) {
                        var statusMap = {
                            working:   { label: 'Currently Working', icon: 'fa-circle-check', cls: 'is-success' },
                            completed: { label: 'Project Completed',  icon: 'fa-check-double',  cls: 'is-info'    },
                            left:      { label: 'Candidate Left',     icon: 'fa-circle-xmark', cls: 'is-danger'  }
                        };
                        var s = statusMap[msg.assignmentStatus] || statusMap.working;
                        var $pill = $row.find('[data-ad-status]');
                        $pill.removeClass('is-success is-info is-danger').addClass(s.cls);
                        $pill.find('[data-ad-status-icon]').removeClass('fa-circle-check fa-check-double fa-circle-xmark').addClass(s.icon);
                        $pill.find('[data-ad-status-label]').text(s.label);
                        if (msg.expectedEndDate) {
                            var d = new Date(msg.expectedEndDate);
                            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                            var formatted = String(d.getDate()).padStart(2,'0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                            $row.find('[data-ad-end-date]').text(formatted);
                        }

                        var $btn = $row.find('.assignment-change-btn');
                        if ($btn.length) {
                            $btn.attr('data-assignment-status', msg.assignmentStatus);
                            $btn.attr('data-expected-end-date', msg.expectedEndDate);
                            $btn.attr('data-client-location', msg.clientLocation);
                            $btn.attr('data-exit-reason', msg.exitReason);
                            $btn.attr('data-project-outcome', msg.projectOutcome);
                            $btn.attr('data-remarks', msg.remarks);
                            $btn.attr('data-internal-notes', msg.internalNotes);
                            $btn.attr('data-replacement-required', msg.replacementRequired);
                            $btn.attr('data-replacement-status', msg.replacementStatus);
                            $btn.attr('data-replacement-requested-on', msg.replacementRequestedOn);
                            $btn.attr('data-replacement-reason', msg.replacementReason);
                        }
                    }
                } else if (msg.type === 'STATS_UPDATED') {
                    if (msg.stats) {
                        $('#placedStatWorking').text(msg.stats.currently_working || 0);
                        $('#placedStatCompleted').text(msg.stats.projects_completed || 0);
                        $('#placedStatReplacement').text(msg.stats.replacement_active || 0);
                        $('#placedStatJoining').text(msg.stats.joining_this_week || 0);
                        $('#placedStatAvailable').text(msg.stats.available_soon || 0);
                        $('#placedStatCompanies').text(msg.stats.companies_served || 0);
                    }
                }
            }

            return {
                broadcast: function(messageObj) {
                    if (!messageObj) return;
                    if (channel) {
                        try {
                            channel.postMessage(messageObj);
                        } catch (e) {
                            console.warn('[PlacedListingChannel] postMessage error:', e);
                        }
                    } else {
                        try {
                            localStorage.setItem(channelName, JSON.stringify(messageObj));
                            localStorage.removeItem(channelName);
                        } catch (e) {}
                    }
                }
            };
        })();

        // Seed store lookups from IndexedDB if available
        if (typeof PlacedListingDB !== 'undefined') {
            PlacedListingDB.getLookup('currency_symbols').then(function(syms) {
                if (syms && (!window.PlacedListingStore.currencySymbols || Object.keys(window.PlacedListingStore.currencySymbols).length === 0)) {
                    window.PlacedListingStore.currencySymbols = syms;
                }
            });
            PlacedListingDB.getLookup('salary_modes').then(function(modes) {
                if (modes && (!window.PlacedListingStore.salaryModes || Object.keys(window.PlacedListingStore.salaryModes).length === 0)) {
                    window.PlacedListingStore.salaryModes = modes;
                }
            });
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatTimelineDate(dateStr) {
            if (!dateStr || String(dateStr).indexOf('0000-00-00') === 0) return '--';
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return '--';
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + String(d.getFullYear()).slice(-2);
        }

        function formatDisplayDate(dateStr) {
            if (!dateStr || String(dateStr).indexOf('0000-00-00') === 0) return '--';
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return '--';
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        function calcDurationDays(startStr, endStr) {
            if (!startStr || !endStr || String(startStr).indexOf('0000-00-00') === 0 || String(endStr).indexOf('0000-00-00') === 0) return null;
            var d1 = new Date(startStr);
            var d2 = new Date(endStr);
            if (isNaN(d1.getTime()) || isNaN(d2.getTime())) return null;
            return Math.floor(Math.abs((d2 - d1) / 86400000));
        }

        function getAvatarClass(name) {
            var str = (name || '').trim();
            if (!str) return 'fs-3 bg-light-secondary text-dark';
            var code = str.toLowerCase().charCodeAt(0);
            if (code >= 97 && code <= 101) return 'fs-3 bg-light-danger text-danger';
            if (code >= 102 && code <= 106) return 'fs-3 bg-light-warning text-warning';
            if (code >= 107 && code <= 111) return 'fs-3 bg-light-primary text-primary';
            if (code >= 112 && code <= 116) return 'fs-3 bg-light-success text-success';
            if (code >= 117 && code <= 121) return 'fs-3 bg-dark text-secondary';
            return 'fs-3 bg-light-secondary text-dark';
        }

        function renderBenchTimeline(row) {
            var benchRaw = row.created_at || '';
            var hasBench = !!benchRaw;
            var offerRaw = row.offer_released_at || row.of_created_date || row.of_offer_release || row.of_offer_approved || row.of_offer_acceptance || row.send_accept_date_time || row.of_joined_date || '';
            var hasOffer = !!offerRaw;
            var joinedRaw = (row.of_joined_date && String(row.of_joined_date).indexOf('0000-00-00') !== 0) ? row.of_joined_date : '';
            var hasJoined = !!joinedRaw;

            var benchLabel = formatTimelineDate(benchRaw);
            var offerLabel = formatTimelineDate(offerRaw);
            var joinedLabel = formatTimelineDate(joinedRaw);

            var benchNodeCls = hasBench ? 'is-done' : 'is-static';
            var lineAfterBench = hasBench ? 'is-done' : '';
            var offerNodeCls = hasOffer ? 'is-done' : 'is-static';
            var lineAfterOffer = hasOffer ? 'is-done' : '';
            var joinedNodeCls = hasJoined ? 'is-done' : 'is-static';
            var lineAfterJoined = hasJoined ? 'is-done' : '';
            var placedNodeCls = hasJoined ? 'is-done' : 'is-static';

            var durationDays = calcDurationDays(benchRaw, joinedRaw);
            var durationText = durationDays !== null ? ('Total Duration: ' + durationDays + ' Days') : '';

            return '<div class="placed-timeline">' +
                '<div class="placed-timeline-track">' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + benchNodeCls + '" data-toggle="tooltip" title="Bench"></div><span class="placed-timeline-node-label">' + benchLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterBench + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + offerNodeCls + '" data-toggle="tooltip" title="Offer"></div><span class="placed-timeline-node-label">' + offerLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterOffer + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + joinedNodeCls + '" data-toggle="tooltip" title="Joined"></div><span class="placed-timeline-node-label">' + joinedLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterJoined + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + placedNodeCls + '" data-toggle="tooltip" title="Placed"></div><span class="placed-timeline-node-label">' + joinedLabel + '</span></div>' +
                '</div>' +
                '<div class="placed-timeline-labels"><span>Bench</span><span>Offer</span><span>Joined</span><span>Placed</span></div>' +
                '<div class="text-muted fw-bold fs-9 mt-1">' + durationText + '</div>' +
            '</div>';
        }

        function renderDirectTimeline(row) {
            var sourcingRaw = row.sourcing_date || row.created_at || '';
            var hasSourcing = !!sourcingRaw;
            var evalRaw = row.evaluation_date || row.schedule_date || row.interview_schedule_date || '';
            var hasEval = !!evalRaw;
            var tlOfferRaw = row.offer_released_at || row.of_created_date || row.of_offer_release || row.of_offer_approved || row.of_offer_acceptance || '';
            var hasTlOffer = !!tlOfferRaw;
            var tlJoinedRaw = (row.of_joined_date && String(row.of_joined_date).indexOf('0000-00-00') !== 0) ? row.of_joined_date : '';
            var hasTlJoined = !!tlJoinedRaw;

            var sourcingLabel = formatTimelineDate(sourcingRaw);
            var evalLabel = formatTimelineDate(evalRaw);
            var tlOfferLabel = formatTimelineDate(tlOfferRaw);
            var tlJoinedLabel = formatTimelineDate(tlJoinedRaw);

            var sourcingNodeCls = hasSourcing ? 'is-done' : 'is-static';
            var lineAfterSourcing = hasSourcing ? 'is-done' : '';
            var evalNodeCls = hasEval ? 'is-done' : 'is-static';
            var lineAfterEval = hasEval ? 'is-done' : '';
            var tlOfferNodeCls = hasTlOffer ? 'is-done' : 'is-static';
            var lineAfterTlOffer = hasTlOffer ? 'is-done' : '';
            var tlJoinedNodeCls = hasTlJoined ? 'is-done' : 'is-static';

            var durationDays = calcDurationDays(sourcingRaw, tlJoinedRaw);
            var durationText = durationDays !== null ? ('Total Duration: ' + durationDays + ' Days') : '';

            return '<div class="placed-timeline">' +
                '<div class="placed-timeline-track">' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + sourcingNodeCls + '" data-toggle="tooltip" title="Sourcing"></div><span class="placed-timeline-node-label">' + sourcingLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterSourcing + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + evalNodeCls + '" data-toggle="tooltip" title="Evaluation"></div><span class="placed-timeline-node-label">' + evalLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterEval + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + tlOfferNodeCls + '" data-toggle="tooltip" title="Offer"></div><span class="placed-timeline-node-label">' + tlOfferLabel + '</span></div>' +
                    '<span class="placed-timeline-line ' + lineAfterTlOffer + '"></span>' +
                    '<div class="placed-timeline-node-wrap"><div class="placed-timeline-node ' + tlJoinedNodeCls + '" data-toggle="tooltip" title="Joined"></div><span class="placed-timeline-node-label">' + tlJoinedLabel + '</span></div>' +
                '</div>' +
                '<div class="placed-timeline-labels"><span>Sourcing</span><span>Evaluation</span><span>Offer</span><span>Joined</span></div>' +
                '<div class="text-muted fw-bold fs-9 mt-1">' + durationText + '</div>' +
            '</div>';
        }

        function renderCandidateRow(row) {
            var store = window.PlacedListingStore;
            var companyId = store.companyId;

            // 1. Candidate Info & Avatar
            var fullName = (row.full_name || '').trim();
            var strVal = fullName.length > 0 ? fullName[0].toUpperCase() : '';
            var bgClass = getAvatarClass(fullName);
            var flag = (row.is_duplicate || row.duplicate == 1) ? 1 : 0;
            var isBench = (row.condidate_type === 'bench_condidate');
            var tredmark = isBench ? '<span style="display:inline-block;font-size:9px;font-weight:600;padding:1px 6px;border-radius:4px;background:#e8f0fe;color:#3b5bdb;margin-left:4px;vertical-align:middle;letter-spacing:.3px;">Bench</span>' : '';
            var profileSidebarCss = 'show-application-detail';
            var nameImg = '';

            if (row.photo) {
                nameImg = '<img src="' + escapeHtml(row.photo) + '" alt="image" style="width: 40px; height: 40px; object-fit: cover;">' +
                          '<div class="position-absolute translate-middle bottom-0 start-100 mb-1 bg-success rounded-circle border border-2 border-white h-10px w-10px"></div>';
            } else {
                if (flag === 1) {
                    nameImg = '<span class="fs-6 fw-bolder">' + escapeHtml(strVal) + '</span>';
                } else {
                    nameImg = '<div class="symbol-label placed-avatar ' + bgClass + '">' + escapeHtml(strVal) + '</div>';
                }
            }

            var skilltag = '';
            if (row.skills) {
                var skillsArr = String(row.skills).split(',').map(function(s) { return s.trim(); }).filter(Boolean);
                var shown = 0;
                for (var sIdx = 0; sIdx < skillsArr.length; sIdx++) {
                    if (shown >= 2) break;
                    skilltag += '<span class="badge placed-skill-badge">' + escapeHtml(skillsArr[sIdx]) + '</span>';
                    shown++;
                }
                if (skillsArr.length > 2) {
                    skilltag += '<span class="badge placed-skill-badge">+' + (skillsArr.length - 2) + '</span>';
                }
            }

            var expLocBits = [];
            if (row.total_exp) expLocBits.push(String(row.total_exp).replace(/\.+$/, '') + ' Yrs');
            var cityName = row.city || row.city_name || '';
            if (cityName) expLocBits.push(cityName);
            var expLocLine = expLocBits.join(' &middot; ');

            var nameLinkClass = flag === 1 ? 'text-danger' : 'text-dark';
            var subLinkClass = flag === 1 ? 'text-danger' : 'text-muted';
            var designation = row.current_designation || '-';

            // 2. Timeline
            var timelineHtml = isBench ? renderBenchTimeline(row) : renderDirectTimeline(row);

            // 3. Client & Job
            var clientName = row.client_name || '-';
            var jobTitle = row.title || '-';
            var jobLocation = row.location || [row.city_name, row.state_name].filter(Boolean).join(', ');

            var recId = parseInt(row.recommendation_id, 10) || 0;
            var fromCompId = parseInt(row.from_company_id, 10) || 0;
            var isDirectClient = (recId > 0 && fromCompId > 0 && fromCompId !== companyId);
            var recText = isDirectClient ? 'Direct Client' : (row.client_self_company == 1 ? 'Internal' : 'Internal Client');
            var recIcon = isDirectClient ? 'fa-handshake text-warning' : 'fa-building-shield text-primary';

            // 4. Recruiter
            var agencyCompName = row.submitted_by_company_name || row.post_company_name || row.from_company_name || row.uc_company_name || '';
            var agencyCompId = parseInt(row.thirdparty_company_id || row.other_company || 0, 10);
            var isAgency = (agencyCompId > 0 && agencyCompId !== companyId) || !!agencyCompName;
            var recruiterName = isAgency ? (row.agency_root_name || row.posted_by || row.submitted_by_name || 'Recruiter') : (row.posted_by || row.submitted_by_name || row.spokperson_name || 'Recruiter');
            var recruiterSub = isAgency ? (agencyCompName || row.company_name || 'Recruiter') : 'Recruiter';

            // 5. Commercials
            var empType = (row.of_employment_type || '-----').toString();
            var offerCurSym = (store.currencySymbols && store.currencySymbols[row.of_offer_currency]) ? store.currencySymbols[row.of_offer_currency] : '₹';
            var offerAmtParsed = parseFloat(row.of_offer_amount) || 0;
            var offerAmtFormatted = Math.round(offerAmtParsed).toLocaleString('en-IN');
            var offerMode = (store.salaryModes && store.salaryModes[row.of_offer_mode]) ? store.salaryModes[row.of_offer_mode] : 'Annual';
            var offerDisplay = row.confidential == 1 ? 'Confidential' : (offerCurSym + ' ' + offerAmtFormatted + '/' + offerMode.charAt(0));

            var ccServiceType = row.commercial_service_type || '';
            var ccFeeMethod = row.commercial_fee_method || '';
            var ccFeeBase = row.commercial_fee_base || 'offered';
            var ccFeePct = parseFloat(row.commercial_fee_pct) || 0;
            var ccFeeFixed = parseFloat(row.commercial_fee_fixed) || 0;
            var ccModel = row.commercial_model || '';

            var ccFeeLabel = '--';
            if (ccServiceType === 'free') {
                ccFeeLabel = 'Free Placement';
            } else if (ccFeeMethod === 'percentage' && ccFeePct > 0) {
                var baseLabel = ccFeeBase === 'offered' ? 'CTC' : 'Billing';
                ccFeeLabel = ccFeePct + '% (' + baseLabel + ')';
            } else if (ccFeeMethod === 'fixed' && ccFeeFixed > 0) {
                ccFeeLabel = '₹' + Math.round(ccFeeFixed).toLocaleString('en-IN') + ' Fixed Fee';
            }

            var isContract = empType.toLowerCase().indexOf('contract') !== -1 || empType.toLowerCase().indexOf('hour') !== -1;
            var marginLabel = isContract ? 'Gross Margin' : '(Placement Fee)';
            var ccModelLabel = ccModel ? (ccModel.charAt(0).toUpperCase() + ccModel.slice(1)) : empType;

            // 6. Work Status
            var savedAssignStatus = row.assignment_status || 'working';
            var adStatusMap = {
                working:   { label: 'Currently Working', icon: 'fa-circle-check',  cls: 'is-success' },
                completed: { label: 'Project Completed',  icon: 'fa-check-double', cls: 'is-info'    },
                left:      { label: 'Candidate Left',     icon: 'fa-circle-xmark', cls: 'is-danger'  }
            };
            var adStatus = adStatusMap[savedAssignStatus] || adStatusMap.working;
            var adEndDateDisp = formatDisplayDate(row.assignment_expected_end_date);
            var joinedIsoForAssign = (row.of_joined_date && String(row.of_joined_date).indexOf('0000-00-00') !== 0) ? String(row.of_joined_date).slice(0, 10) : '';

            // Assemble row HTML
            return '<tr class="placed-row" data-candidate-id="' + escapeHtml(row.id) + '">' +
                '<td>' +
                    '<div class="form-check form-check-sm form-check-custom form-check-solid">' +
                        '<input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" data-jobId="' + escapeHtml(row.jobId || '') + '" value="' + escapeHtml(row.candidate_id || '') + '" />' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="d-flex align-items-start">' +
                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-3 position-relative flex-shrink-0">' +
                            '<a href="javascript:void(0);" data-row-id="' + escapeHtml(row.wiseId || '') + '" class="' + profileSidebarCss + '">' + nameImg + '</a>' +
                        '</div>' +
                        '<div class="d-flex flex-column">' +
                            '<a href="javascript:void(0);" data-row-id="' + escapeHtml(row.wiseId || '') + '" class="' + nameLinkClass + ' fw-bolder text-hover-primary fs-6 ' + profileSidebarCss + '" data-toggle="tooltip" title="' + escapeHtml(fullName) + '">' + escapeHtml(fullName.substring(0, 22)) + tredmark + '</a>' +
                            '<a href="javascript:void(0);" data-row-id="' + escapeHtml(row.wiseId || '') + '" class="' + subLinkClass + ' fw-bold d-block fs-8 text-hover-primary ' + profileSidebarCss + '" data-toggle="tooltip" title="' + escapeHtml(designation) + '">' + escapeHtml(designation.substring(0, 24)) + '</a>' +
                            '<span class="text-muted fw-semibold fs-9 d-block">' + expLocLine + '</span>' +
                            '<div class="mt-1">' + skilltag + '</div>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td>' + timelineHtml + '</td>' +
                '<td>' +
                    '<div class="d-flex flex-column">' +
                        '<span class="placed-detail-line"><i class="fa-solid fa-building text-muted"></i>' +
                            '<span class="text-muted fw-bold fs-8" data-toggle="tooltip" title="' + escapeHtml(clientName) + '">' + escapeHtml(clientName.substring(0, 18)) + '</span>' +
                        '</span>' +
                        '<span class="placed-detail-line"><i class="fa-solid fa-briefcase text-muted"></i>' +
                            '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' + escapeHtml(jobTitle) + '">' + escapeHtml(jobTitle.substring(0, 18)) + '</a>' +
                        '</span>' +
                        (jobLocation ? '<span class="placed-detail-line"><i class="fa-solid fa-location-dot text-muted"></i><span class="text-muted fw-semibold fs-9">' + escapeHtml(jobLocation) + '</span></span>' : '') +
                        '<span class="placed-detail-line mt-1"><i class="fa-solid ' + recIcon + '"></i>' +
                            '<span class="fw-bold fs-9 text-muted">' + recText + ' &middot; ' + escapeHtml(row.jobId || '') + '</span>' +
                        '</span>' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="d-flex flex-column">' +
                        '<span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' + escapeHtml(recruiterName) + '">' + escapeHtml(recruiterName.substring(0, 20)) + '</span>' +
                        '<span class="text-muted fw-semibold d-block fs-9" data-toggle="tooltip" title="' + escapeHtml(recruiterSub) + '">' + escapeHtml(recruiterSub.substring(0, 20)) + '</span>' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="d-flex flex-column">' +
                        '<span class="text-dark fw-bolder fs-6">' + offerDisplay + '</span>' +
                        '<span class="placed-commercial-line">' +
                            '<i class="fa-solid fa-circle-check text-success"></i>' +
                            '<span class="text-dark fw-semibold fs-9" data-cc-fee>' + escapeHtml(ccFeeLabel) + ' <span class="text-muted fw-normal">' + marginLabel + '</span></span>' +
                        '</span>' +
                        '<span class="placed-commercial-line">' +
                            '<i class="fa-solid fa-user-tie text-muted"></i>' +
                            '<span class="text-muted fw-semibold fs-9" data-cc-model>' + escapeHtml(ccModelLabel) + '</span>' +
                        '</span>' +
                        '<a href="javascript:void(0);" class="placed-change-link commercial-change-btn"' +
                            ' data-candidate-id="' + escapeHtml(row.id) + '"' +
                            ' data-is-bench="' + (isBench ? 1 : 0) + '"' +
                            ' data-amount="' + escapeHtml(row.of_offer_amount || 0) + '"' +
                            ' data-employment-type="' + escapeHtml(empType) + '"' +
                            ' data-service-type="' + escapeHtml(row.commercial_service_type || '') + '"' +
                            ' data-commercial-model="' + escapeHtml(row.commercial_model || '') + '"' +
                            ' data-fee-method="' + escapeHtml(row.commercial_fee_method || '') + '"' +
                            ' data-fee-base="' + escapeHtml(row.commercial_fee_base || '') + '"' +
                            ' data-fee-pct="' + escapeHtml(row.commercial_fee_pct || 0) + '"' +
                            ' data-fee-fixed="' + escapeHtml(row.commercial_fee_fixed || 0) + '"' +
                            ' data-billing-amount="' + escapeHtml(row.commercial_billing_amount || 0) + '"' +
                            ' data-billing-cycle="' + escapeHtml(row.commercial_billing_cycle || '') + '"' +
                            ' data-margin-pct="' + escapeHtml(row.commercial_margin_pct || 0) + '"' +
                            ' data-replacement-period="' + escapeHtml(row.commercial_replacement_period || '') + '"' +
                            ' data-internal-note="' + escapeHtml(row.commercial_internal_note || '') + '"' +
                        '><i class="fa-solid fa-pen"></i>Change</a>' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="d-flex flex-column">' +
                        '<span class="placed-status-pill ' + adStatus.cls + '" data-ad-status>' +
                            '<i class="fa-solid ' + adStatus.icon + ' me-1" data-ad-status-icon></i><span data-ad-status-label>' + adStatus.label + '</span>' +
                        '</span>' +
                        '<span class="text-muted fw-semibold fs-9 mt-2">Expected End</span>' +
                        '<span class="placed-commercial-line">' +
                            '<i class="fa-solid fa-calendar-days text-muted"></i>' +
                            '<span class="text-dark fw-semibold fs-9" data-ad-end-date>' + escapeHtml(adEndDateDisp) + '</span>' +
                        '</span>' +
                        '<a href="javascript:void(0);" class="placed-change-link assignment-change-btn"' +
                            ' data-candidate-id="' + escapeHtml(row.id) + '"' +
                            ' data-is-bench="' + (isBench ? 1 : 0) + '"' +
                            ' data-name="' + escapeHtml(fullName) + '"' +
                            ' data-designation="' + escapeHtml(designation) + '"' +
                            ' data-exp-loc="' + escapeHtml(expLocLine) + '"' +
                            ' data-client="' + escapeHtml(clientName) + '"' +
                            ' data-employment-type="' + escapeHtml(empType) + '"' +
                            ' data-joined-date="' + escapeHtml(joinedIsoForAssign) + '"' +
                            ' data-avatar-class="' + bgClass + '"' +
                            ' data-avatar-letter="' + escapeHtml(strVal) + '"' +
                            ' data-assignment-status="' + escapeHtml(row.assignment_status || '') + '"' +
                            ' data-expected-end-date="' + escapeHtml(row.assignment_expected_end_date || '') + '"' +
                            ' data-client-location="' + escapeHtml(row.assignment_client_location || '') + '"' +
                            ' data-exit-reason="' + escapeHtml(row.assignment_exit_reason || '') + '"' +
                            ' data-project-outcome="' + escapeHtml(row.assignment_project_outcome || '') + '"' +
                            ' data-remarks="' + escapeHtml(row.assignment_remarks || '') + '"' +
                            ' data-internal-notes="' + escapeHtml(row.assignment_internal_notes || '') + '"' +
                            ' data-replacement-required="' + escapeHtml(row.assignment_replacement_required || 0) + '"' +
                            ' data-replacement-status="' + escapeHtml(row.assignment_replacement_status || '') + '"' +
                            ' data-replacement-requested-on="' + escapeHtml(row.assignment_replacement_requested_on || '') + '"' +
                            ' data-replacement-reason="' + escapeHtml(row.assignment_replacement_reason || '') + '"' +
                        '><i class="fa-solid fa-pen"></i>Change</a>' +
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="dropdown">' +
                        '<button class="btn btn-sm btn-icon btn-light placed-action-btn" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="fa-solid fa-ellipsis-vertical fs-6"></i>' +
                        '</button>' +
                        '<ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a href="javascript:void(0);" class="dropdown-item stage_sub_stage" data-row-id="' + escapeHtml(row.wiseId || '') + '">View Placement Status</a></li>' +
                            '<li><a href="javascript:void(0);" class="dropdown-item text-danger placed-row-delete-btn" data-candidate-name="' + escapeHtml(fullName) + '"><i class="fa-solid fa-trash-can me-2"></i>Delete</a></li>' +
                        '</ul>' +
                    '</div>' +
                '</td>' +
            '</tr>';
        }

        function renderCandidatesTable(candidates) {
            if (!candidates || candidates.length === 0) {
                $('#benchCandidatePlaceAjax').html('<tr style="height: 102px;"><td colspan="8" class="text-center text-muted">No data available in table</td></tr>');
                return;
            }
            var rowsHtml = '';
            for (var i = 0; i < candidates.length; i++) {
                rowsHtml += renderCandidateRow(candidates[i]);
            }
            $('#benchCandidatePlaceAjax').html(rowsHtml);
            $('[data-toggle="tooltip"]').tooltip();
        }

        function renderPaginationHtml(total, curPage, lastPage, perPage) {
            if (total <= 0) return '';
            curPage = parseInt(curPage, 10) || 1;
            lastPage = parseInt(lastPage, 10) || 1;
            perPage = parseInt(perPage, 10) || 10;
            var from = (curPage - 1) * perPage + 1;
            var to = Math.min(curPage * perPage, total);

            var html = '<div class="pagi-bar">';
            html += '<div class="pagi-info">Showing ' + from + ' to ' + to + ' of ' + total + ' records</div>';
            html += '<div class="pagi-right">';

            var prevDisabled = curPage <= 1 ? 'pagi-disabled' : '';
            html += '<button class="pagi-arrow ' + prevDisabled + '" data-page="' + (curPage - 1) + '" type="button" aria-label="Previous page">' +
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>' +
                '</button>';

            for (var i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= curPage - 2 && i <= curPage + 2)) {
                    var activeCls = i === curPage ? 'pagi-active pagi-active-disabled' : '';
                    html += '<button class="pagi-page ' + activeCls + '" data-page="' + i + '" type="button">' + i + '</button>';
                } else if (i === curPage - 3 || i === curPage + 3) {
                    html += '<span class="pagi-dots">&hellip;</span>';
                }
            }

            var nextDisabled = curPage >= lastPage ? 'pagi-disabled' : '';
            html += '<button class="pagi-arrow ' + nextDisabled + '" data-page="' + (curPage + 1) + '" type="button" aria-label="Next page">' +
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>' +
                '</button>';

            html += '<select class="pagi-perpage" id="rowsPerPageSelect" aria-label="Records per page">' +
                '<option value="10"' + (perPage === 10 ? ' selected' : '') + '>10 / page</option>' +
                '<option value="25"' + (perPage === 25 ? ' selected' : '') + '>25 / page</option>' +
                '<option value="50"' + (perPage === 50 ? ' selected' : '') + '>50 / page</option>' +
                '<option value="100"' + (perPage === 100 ? ' selected' : '') + '>100 / page</option>' +
            '</select>';

            html += '</div></div>';
            return html;
        }

        function fetchPlacedListingStats() {
            $.ajax({
                url: `{!! route('service_company.bench-candidates.placedListingStats_v2') !!}`,
                type: 'GET',
                success: function(res) {
                    if (res && res.status === 'success') {
                        window.PlacedListingStore.stats = res;
                        $('#placedStatWorking').text(res.currently_working || 0);
                        $('#placedStatCompleted').text(res.projects_completed || 0);
                        $('#placedStatReplacement').text(res.replacement_active || 0);
                        $('#placedStatJoining').text(res.joining_this_week || 0);
                        $('#placedStatAvailable').text(res.available_soon || 0);
                        $('#placedStatCompanies').text(res.companies_served || 0);

                        // Broadcast to other tabs so they sync with 0 extra queries
                        if (typeof PlacedListingChannel !== 'undefined') {
                            PlacedListingChannel.broadcast({
                                type: 'STATS_UPDATED',
                                stats: res
                            });
                        }
                    }
                },
                error: function(err) {
                    console.warn('Placed listing stats fetch error:', err);
                }
            });
        }

        function fetchBenchBenchCandidatesCandidateList(page = 1, bypassCache = false) {
            page = parseInt(page, 10) || 1;
            var sortingfilter = $('#sortingfilter').val() || '';
            var country = $('#country').val() || '';
            var cus_search = ($('#kt_tagify_2').val() || '').trim();
            var size = parseInt(localStorage.getItem('candidatePerPage') || $('#rowsPerPageSelect').val() || 10, 10);

            var cacheKey = 'p_' + page + '_s_' + size + '_q_' + cus_search + '_sort_' + sortingfilter + '_c_' + country;

            // 1. Check L1 in-memory RAM cache (0 ms)
            if (!bypassCache && window.PlacedListingStore.cache.has(cacheKey)) {
                var cached = window.PlacedListingStore.cache.get(cacheKey);
                window.PlacedListingStore.candidates = cached.candidates;
                window.PlacedListingStore.page = cached.page;
                window.PlacedListingStore.total = cached.total;
                window.PlacedListingStore.lastPage = cached.last_page;
                $('#totalPlace').text(cached.total);
                renderCandidatesTable(cached.candidates);
                $('#paginationbenchCandidatePlace').html(renderPaginationHtml(cached.total, cached.page, cached.last_page, cached.per_page));
                return Promise.resolve(cached);
            }

            // 2. Check L2 IndexedDB persistent snapshot cache (0 network)
            if (!bypassCache && typeof PlacedListingDB !== 'undefined') {
                return PlacedListingDB.getSnapshot(cacheKey).then(function(idbCached) {
                    if (idbCached) {
                        // Populate L1 cache for subsequent synchronous hits
                        if (window.PlacedListingStore.cache.size >= window.PlacedListingStore.maxCacheEntries) {
                            var firstKey = window.PlacedListingStore.cache.keys().next().value;
                            window.PlacedListingStore.cache.delete(firstKey);
                        }
                        window.PlacedListingStore.cache.set(cacheKey, idbCached);

                        window.PlacedListingStore.candidates = idbCached.candidates;
                        window.PlacedListingStore.page = idbCached.page;
                        window.PlacedListingStore.total = idbCached.total;
                        window.PlacedListingStore.lastPage = idbCached.last_page;
                        $('#totalPlace').text(idbCached.total);
                        renderCandidatesTable(idbCached.candidates);
                        $('#paginationbenchCandidatePlace').html(renderPaginationHtml(idbCached.total, idbCached.page, idbCached.last_page, idbCached.per_page));
                        return idbCached;
                    }
                    return executeNetworkFetch();
                }).catch(function() {
                    return executeNetworkFetch();
                });
            }

            return executeNetworkFetch();

            function executeNetworkFetch() {
                if (window.PlacedListingStore.inFlightRequest && typeof window.PlacedListingStore.inFlightRequest.abort === 'function') {
                    window.PlacedListingStore.inFlightRequest.abort();
                }

                $('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'block' });

                window.PlacedListingStore.inFlightRequest = $.ajax({
                    url: `{!! route('service_company.bench-candidates.placedListingData_v2') !!}?page=${page}`,
                    type: 'GET',
                    data: {
                        cus_search: cus_search,
                        country: country,
                        sortingfilter: sortingfilter,
                        size: size,
                        per_page: size
                    },
                    success: function(response) {
                        $('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'none' });
                        if (response && response.status === 'success') {
                            if (response.currency_symbols) {
                                window.PlacedListingStore.currencySymbols = response.currency_symbols;
                                if (typeof PlacedListingDB !== 'undefined') {
                                    PlacedListingDB.setLookup('currency_symbols', response.currency_symbols);
                                }
                            }
                            if (response.salary_modes) {
                                window.PlacedListingStore.salaryModes = response.salary_modes;
                                if (typeof PlacedListingDB !== 'undefined') {
                                    PlacedListingDB.setLookup('salary_modes', response.salary_modes);
                                }
                            }
                            if (response.company_id) window.PlacedListingStore.companyId = response.company_id;

                            // Store in L1 in-memory LRU cache
                            if (window.PlacedListingStore.cache.size >= window.PlacedListingStore.maxCacheEntries) {
                                var firstKey = window.PlacedListingStore.cache.keys().next().value;
                                window.PlacedListingStore.cache.delete(firstKey);
                            }
                            window.PlacedListingStore.cache.set(cacheKey, response);

                            // Store in L2 IndexedDB snapshot cache (5 minutes TTL)
                            if (typeof PlacedListingDB !== 'undefined') {
                                PlacedListingDB.setSnapshot(cacheKey, response, 300000);
                            }

                            window.PlacedListingStore.candidates = response.candidates || [];
                            window.PlacedListingStore.page = response.page;
                            window.PlacedListingStore.total = response.total;
                            window.PlacedListingStore.lastPage = response.last_page;

                            $('#totalPlace').text(response.total);
                            renderCandidatesTable(response.candidates || []);
                            $('#paginationbenchCandidatePlace').html(renderPaginationHtml(response.total, response.page, response.last_page, response.per_page));
                        }
                    },
                    error: function(xhr, status) {
                        if (status !== 'abort') {
                            $('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'none' });
                            console.error('Placed listing fetch error:', xhr);
                        }
                    }
                });

                return window.PlacedListingStore.inFlightRequest;
            }
        }

        // =====================================================
        // Phase 4: Laravel Reverb Real-Time WebSocket Synchronization
        // In-place DOM cell updates with zero page reload & reconnection recovery
        // =====================================================
        function initPlacedListingReverbSync() {
            if (typeof window.Echo === 'undefined') {
                return;
            }
            var store = window.PlacedListingStore;
            var companyId = store.companyId || {{ (int) auth()->user()->company_id }};
            if (!companyId) return;

            try {
                window.Echo.private('company.' + companyId)
                    .listen('.placement.record.updated', function(event) {
                        if (!event) return;
                        handlePlacementRecordUpdatedEvent(event);
                    });

                // WebSocket Reconnection Recovery: if socket connection re-establishes, quietly sync
                if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                    window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                        if (states.current === 'connected' && (states.previous === 'unavailable' || states.previous === 'connecting')) {
                            if (window.PlacedListingStore && window.PlacedListingStore.cache) {
                                window.PlacedListingStore.cache.clear();
                            }
                            if (typeof PlacedListingDB !== 'undefined') {
                                PlacedListingDB.clear();
                            }
                            fetchPlacedListingStats();
                        }
                    });
                }
            } catch (echoEx) {
                console.warn('[Reverb] Placed listing subscription error:', echoEx);
            }
        }

        function handlePlacementRecordUpdatedEvent(event) {
            var candidateId = event.candidate_id;
            var wiseId = event.wise_id;
            var action = event.action;
            var payload = event.payload || {};

            // 1. Invalidate L1 in-memory LRU store and L2 IndexedDB cache
            if (window.PlacedListingStore && window.PlacedListingStore.cache) {
                window.PlacedListingStore.cache.clear();
            }
            if (typeof PlacedListingDB !== 'undefined') {
                PlacedListingDB.clear();
            }

            // 2. Refresh live overview stats
            fetchPlacedListingStats();

            // 3. Find target candidate row in DOM
            var $row = $('tr.placed-row').filter(function() {
                var rowId = String($(this).data('candidate-id'));
                return (candidateId && rowId === String(candidateId)) || (wiseId && rowId === String(wiseId));
            });

            if (!$row.length) {
                return;
            }

            // 4. In-place DOM update without page reload
            if (action === 'commercial_updated') {
                var store = window.PlacedListingStore;
                var ccServiceType = payload.commercial_service_type || '';
                var ccFeeMethod = payload.commercial_fee_method || '';
                var ccFeeBase = payload.commercial_fee_base || 'offered';
                var ccFeePct = parseFloat(payload.commercial_fee_pct) || 0;
                var ccFeeFixed = parseFloat(payload.commercial_fee_fixed) || 0;
                var ccModel = payload.commercial_model || '';

                var ccFeeLabel = '--';
                if (ccServiceType === 'free') {
                    ccFeeLabel = 'Free Placement';
                } else if (ccFeeMethod === 'percentage' && ccFeePct > 0) {
                    var baseLabel = ccFeeBase === 'offered' ? 'CTC' : 'Billing';
                    ccFeeLabel = ccFeePct + '% (' + baseLabel + ')';
                } else if (ccFeeMethod === 'fixed' && ccFeeFixed > 0) {
                    ccFeeLabel = '₹' + Math.round(ccFeeFixed).toLocaleString('en-IN') + ' Fixed Fee';
                }

                var $cBtn = $row.find('.commercial-change-btn');
                var empType = ($cBtn.data('employment-type') || '').toString();
                var isContract = empType.toLowerCase().indexOf('contract') !== -1 || empType.toLowerCase().indexOf('hour') !== -1;
                var marginLabel = isContract ? 'Gross Margin' : '(Placement Fee)';
                var ccModelLabel = ccModel ? (ccModel.charAt(0).toUpperCase() + ccModel.slice(1)) : (empType || '--');

                $row.find('[data-cc-fee]').html(escapeHtml(ccFeeLabel) + ' <span class="text-muted fw-normal">' + marginLabel + '</span>');
                $row.find('[data-cc-model]').text(ccModelLabel);

                $cBtn.data('service-type', ccServiceType)
                    .data('commercial-model', ccModel)
                    .data('fee-method', ccFeeMethod)
                    .data('fee-base', ccFeeBase)
                    .data('fee-pct', ccFeePct)
                    .data('fee-fixed', ccFeeFixed)
                    .data('billing-amount', payload.commercial_billing_amount || 0)
                    .data('billing-cycle', payload.commercial_billing_cycle || '')
                    .data('margin-pct', payload.commercial_margin_pct || 0)
                    .data('replacement-period', payload.commercial_replacement_period || '')
                    .data('internal-note', payload.commercial_internal_note || '');
            } else if (action === 'assignment_updated') {
                var savedAssignStatus = payload.assignment_status || 'working';
                var adStatusMap = {
                    working:   { label: 'Currently Working', icon: 'fa-circle-check',  cls: 'is-success' },
                    completed: { label: 'Project Completed',  icon: 'fa-check-double', cls: 'is-info'    },
                    left:      { label: 'Candidate Left',     icon: 'fa-circle-xmark', cls: 'is-danger'  }
                };
                var adStatus = adStatusMap[savedAssignStatus] || adStatusMap.working;
                var adEndDateDisp = formatDisplayDate(payload.assignment_expected_end_date);

                var $pill = $row.find('[data-ad-status]');
                $pill.removeClass('is-success is-info is-danger').addClass(adStatus.cls);
                $pill.find('[data-ad-status-icon]').attr('class', 'fa-solid ' + adStatus.icon + ' me-1');
                $pill.find('[data-ad-status-label]').text(adStatus.label);

                $row.find('[data-ad-end-date]').text(adEndDateDisp);

                var $aBtn = $row.find('.assignment-change-btn');
                $aBtn.data('assignment-status', payload.assignment_status || '')
                    .data('expected-end-date', payload.assignment_expected_end_date || '')
                    .data('employment-type', payload.assignment_employment_type || '')
                    .data('client-location', payload.assignment_client_location || '')
                    .data('exit-reason', payload.assignment_exit_reason || '')
                    .data('project-outcome', payload.assignment_project_outcome || '')
                    .data('remarks', payload.assignment_remarks || '')
                    .data('internal-notes', payload.assignment_internal_notes || '')
                    .data('replacement-required', payload.assignment_replacement_required ? 1 : 0)
                    .data('replacement-status', payload.assignment_replacement_status || '')
                    .data('replacement-requested-on', payload.assignment_replacement_requested_on || '')
                    .data('replacement-reason', payload.assignment_replacement_reason || '');
            }

            // Visual feedback: smooth highlight fade
            $row.addClass('row-reverb-updated');
            setTimeout(function() {
                $row.removeClass('row-reverb-updated');
            }, 2500);
        }
    </script>
@endpush
