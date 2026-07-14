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

        .rows-per-page-select { cursor: pointer; }

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
                         Overview stat cards.
                         No aggregation endpoint exists for these yet, so the
                         values below are static placeholders — swap for real
                         numbers once a backend summary API is available.
                    ====================================================== --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-primary text-primary"><i class="fa-solid fa-user-check"></i></div>
                                <div>
                                    <div class="placed-stat-label">Currently Working</div>
                                    <div class="placed-stat-value">186</div>
                                    <div class="placed-stat-caption">Active Assignments</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> 14 vs Apr 2025 (172)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-success text-success"><i class="fa-solid fa-circle-check"></i></div>
                                <div>
                                    <div class="placed-stat-label">Projects Completed</div>
                                    <div class="placed-stat-value">34</div>
                                    <div class="placed-stat-caption">This Month</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> 6 vs Apr 2025 (28)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-purple text-purple" style="background:#f8f5ff;color:#7239ea;"><i class="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <div class="placed-stat-label">Replacement Active</div>
                                    <div class="placed-stat-value">18</div>
                                    <div class="placed-stat-caption">Permanent Hiring</div>
                                    <div class="placed-stat-delta down mt-1"><i class="fa-solid fa-arrow-down"></i> 2 vs Apr 2025 (20)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-warning text-warning"><i class="fa-solid fa-calendar-week"></i></div>
                                <div>
                                    <div class="placed-stat-label">Joining This Week</div>
                                    <div class="placed-stat-value">6</div>
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
                                    <div class="placed-stat-value">12</div>
                                    <div class="placed-stat-caption">Project Ending &lt; 30 Days</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> 4 vs Apr 2025 (8)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="placed-stat-card">
                                <div class="placed-stat-icon bg-light-danger text-danger"><i class="fa-solid fa-building"></i></div>
                                <div>
                                    <div class="placed-stat-label">Companies Served</div>
                                    <div class="placed-stat-value">42</div>
                                    <div class="placed-stat-caption">Active Clients</div>
                                    <div class="placed-stat-delta up mt-1"><i class="fa-solid fa-arrow-up"></i> 5 vs Apr 2025 (37)</div>
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
            $('#kt_tagify_2').keyup(function() {
                fetchBenchBenchCandidatesCandidateList();
            });
        });

        function filtersdataget() {
            fetchBenchBenchCandidatesCandidateList();
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
            fetchBenchBenchCandidatesCandidateList();

            $(document).on('click', '#paginationbenchCandidatePlace .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;
                $(this).addClass('disabled');
                fetchBenchBenchCandidatesCandidateList(page).then(() => {
                    $('#paginationbenchCandidatePlace .page-link').removeClass('disabled');
                }).catch(() => {
                    $('#paginationbenchCandidatePlace .page-link').removeClass('disabled');
                });
            });

            $(document).on('change', '#paginationbenchCandidatePlace #rowsPerPageSelect', function() {
                fetchBenchBenchCandidatesCandidateList(1);
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

        function fetchBenchBenchCandidatesCandidateList(page = 1) {
            var sortingfilter = $('#sortingfilter').val();
            var country = $('#country').val();
            var cus_search = $('#kt_tagify_2').val();
            var size = $('#rowsPerPageSelect').val() || 10;

            $('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'block' });
            return $.ajax({
                url: `{!! route('service_company.bench-candidates.placedListingData_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    cus_search: cus_search,
                    country: country,
                    sortingfilter: sortingfilter,
                    size: size
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidatePlaceAjax').css({ display: 'none' });
                    $('#totalPlace').html(response.total);
                    $('#benchCandidatePlaceAjax').html(response.html);
                    $('#paginationbenchCandidatePlace').html(response.pagination);
                }
            });
        }
    </script>
@endpush
