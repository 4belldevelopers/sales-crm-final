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
            font-size: 1.1rem;
        }
        .placed-stat-label { font-size: 12px; font-weight: 500; color: #7e8299; }
        .placed-stat-value { font-size: 20px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .placed-stat-caption { font-size: 11px; color: #a1a5b7; }
        .placed-stat-delta { font-size: 11px; font-weight: 600; }
        .placed-stat-delta.up { color: #17c653; }
        .placed-stat-delta.down { color: #f1416c; }

        /* ---- placement funnel ---- */
        .placed-funnel-card { border: 1px solid #eef0f4; border-radius: .75rem; background: #fff; }
        .placed-funnel-step { display: flex; flex-direction: column; align-items: center; text-align: center; gap: .35rem; min-width: 84px; }
        .placed-funnel-icon {
            width: 46px; height: 46px; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
        }
        .placed-funnel-value { font-size: 17px; font-weight: 700; color: #181c32; }
        .placed-funnel-label { font-size: 11px; font-weight: 500; color: #7e8299; }
        .placed-funnel-chevron { color: #d7dae1; font-size: 1rem; }
        .placed-funnel-toggle .btn { font-size: 12px; font-weight: 500; padding: .35rem .75rem; }

        /* ---- filter bar ---- */
        .placed-filter-bar {
            display: flex; flex-wrap: wrap; align-items: center; gap: .5rem;
            padding: .6rem .25rem;
        }
        .placed-filter-btn {
            border: 1px solid #e4e6ef; background: #f9f9f9; color: #3f4254;
            border-radius: .5rem; font-size: 12.5px; font-weight: 500;
            padding: .45rem .75rem; white-space: nowrap;
            display: inline-flex; align-items: center; gap: .35rem;
        }
        .placed-filter-btn:hover { background: #f1f1f4; }

        /* ---- table ---- */
        #placedTableWrap { overflow-x: auto; }
        #myTable thead th {
            position: sticky; top: 0; z-index: 2;
            background: #fff; font-size: 12px; font-weight: 500; color: #7e8299;
            border-bottom: 1px solid #eef0f4; white-space: nowrap; padding: .75rem .6rem;
        }
        #myTable tbody td { padding: .6rem; vertical-align: middle; font-size: 13px; }
        #myTable tbody tr { border-bottom: 1px solid #f4f4f8; }

        .placed-avatar {
            width: 40px; height: 40px; border-radius: .55rem;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }
        .placed-skill-badge {
            background: #f4f4f8; color: #5e6278; font-weight: 400; font-size: 10.5px;
            margin-right: 2px;
        }
        .fs-8 { font-size: 11.5px !important; }
        .fs-9 { font-size: 10.5px !important; }

        .placed-timeline { min-width: 190px; }
        .placed-timeline-track { display: flex; align-items: flex-start; }
        .placed-timeline-node-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .placed-timeline-node { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .placed-timeline-node.is-static { background: #fff; border: 2px solid #e4e6ef; }
        .placed-timeline-node.is-done { background: #17c653; border: 2px solid #17c653; }
        .placed-timeline-node-label { font-size: 9.5px; font-weight: 600; color: #7e8299; margin-top: 4px; white-space: nowrap; }
        .placed-timeline-line { flex: 1; height: 2px; background: #e4e6ef; min-width: 14px; margin: 4px 1px 0; }
        .placed-timeline-line.is-done { background: #17c653; }
        .placed-timeline-labels { display: flex; justify-content: space-between; margin-top: 1px; }
        .placed-timeline-labels span { font-size: 9px; color: #a1a5b7; }

        .placed-detail-line { display: flex; align-items: center; gap: .35rem; margin-bottom: 1px; }
        .placed-detail-line i { font-size: 10px; width: 12px; text-align: center; }

        .placed-status-pill {
            display: inline-flex; align-items: center; width: fit-content;
            font-size: 11.5px; font-weight: 600; padding: .25rem .55rem; border-radius: .45rem;
        }
        .placed-status-pill.is-success { background: #e8fff3; color: #17c653; }
        .placed-status-pill.is-info { background: #eef3ff; color: #009ef7; }
        .placed-status-pill.is-danger { background: #fff5f8; color: #f1416c; }
        .placed-action-btn { width: 30px; height: 30px; }

        .placed-commercial-line { display: flex; align-items: center; gap: .35rem; margin-top: 3px; }
        .placed-commercial-line i { font-size: 10px; width: 12px; text-align: center; }
        .placed-change-link {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: 11px; font-weight: 600; color: #009ef7; margin-top: 4px; width: fit-content;
        }
        .placed-change-link i { font-size: 9.5px; }
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
                    <span class="text-gray-600 fs-8">Track bench candidates who got placed and their project / placement journey.</span>
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
                         Placement funnel.
                         Static placeholder — stage counts need a new ES
                         aggregation query per stage, not yet built.
                    ====================================================== --}}
                    <div class="placed-funnel-card p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                            <h6 class="fw-bolder text-dark mb-0">Placement Funnel</h6>
                            <div class="placed-funnel-toggle btn-group" role="group">
                                <button type="button" class="btn btn-light-primary active">May 2025</button>
                                <button type="button" class="btn btn-light">Last 6 Months</button>
                                <button type="button" class="btn btn-light">Last 1 Year</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-secondary text-dark"><i class="fa-solid fa-building"></i></div>
                                <div class="placed-funnel-value">426</div>
                                <div class="placed-funnel-label">Bench</div>
                            </div>
                            <i class="fa-solid fa-chevron-right placed-funnel-chevron"></i>
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-primary text-primary"><i class="fa-solid fa-shield-halved"></i></div>
                                <div class="placed-funnel-value">161</div>
                                <div class="placed-funnel-label">Shortlisted</div>
                            </div>
                            <i class="fa-solid fa-chevron-right placed-funnel-chevron"></i>
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-warning text-warning"><i class="fa-solid fa-file-lines"></i></div>
                                <div class="placed-funnel-value">44</div>
                                <div class="placed-funnel-label">Offer Released</div>
                            </div>
                            <i class="fa-solid fa-chevron-right placed-funnel-chevron"></i>
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-success text-success"><i class="fa-solid fa-circle-check"></i></div>
                                <div class="placed-funnel-value">39</div>
                                <div class="placed-funnel-label">Offer Accepted</div>
                            </div>
                            <i class="fa-solid fa-chevron-right placed-funnel-chevron"></i>
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-info text-info"><i class="fa-solid fa-user-check"></i></div>
                                <div class="placed-funnel-value">37</div>
                                <div class="placed-funnel-label">Joined</div>
                            </div>
                            <i class="fa-solid fa-chevron-right placed-funnel-chevron"></i>
                            <div class="placed-funnel-step">
                                <div class="placed-funnel-icon bg-light-success text-success"><i class="fa-solid fa-user-check"></i></div>
                                <div class="placed-funnel-value" id="totalPlace">62</div>
                                <div class="placed-funnel-label">Placed</div>
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
                                            <th>Current Assignment /<br>Replacement</th>
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
                    <label class="cc-option-card" data-value="free">
                        <span class="cc-option-icon"><i class="fa-regular fa-calendar"></i></span>
                        <span class="cc-option-label">Free Service</span>
                        <span class="cc-option-radio"></span>
                    </label>
                    <label class="cc-option-card is-selected" data-value="paid">
                        <span class="cc-option-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                        <span class="cc-option-label">Paid Service</span>
                        <span class="cc-option-radio"></span>
                    </label>
                </div>

                <div id="ccPaidServiceSection">
                    {{-- Step 2: Commercial Model --}}
                    <div class="cc-step-head">
                        <span class="cc-step-num">2</span>
                        <span class="cc-step-title">Commercial Model <span class="cc-step-suffix">(Paid Service)</span></span>
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
                        <span>You are configuring terms for <strong id="ccModelBannerText">Contract Staffing</strong>.</span>
                    </div>

                    <div class="cc-tabs">
                        <div class="cc-tab is-active" data-tab="general"><i class="fa-regular fa-user"></i> General</div>
                        <div class="cc-tab" data-tab="notes"><i class="fa-regular fa-note-sticky"></i> Notes</div>
                        <div class="cc-tab" data-tab="history"><i class="fa-solid fa-clock-rotate-left"></i> History</div>
                    </div>

                    <div id="ccTabGeneral">
                        {{-- Contract Staffing fields: billed recurring, margin-based --}}
                        <div id="ccContractFields">
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

                <div id="ccFreeServiceNote" class="d-none text-center text-muted py-10 fs-8">
                    <i class="fa-regular fa-calendar fs-2 d-block mb-2"></i>
                    Free Service placements have no billing terms to configure.
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 px-6 py-4">
                <button type="button" class="btn btn-light" id="kt_commercialConfig_cancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="kt_commercialConfig_save">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Save Configuration
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

        $(document).on('click', '#ccServiceTypeGrid .cc-option-card', function() {
            $('#ccServiceTypeGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            if ($(this).data('value') === 'free') {
                $('#ccPaidServiceSection').addClass('d-none');
                $('#ccFreeServiceNote').removeClass('d-none');
            } else {
                $('#ccPaidServiceSection').removeClass('d-none');
                $('#ccFreeServiceNote').addClass('d-none');
            }
        });

        $(document).on('click', '#ccModelGrid .cc-option-card', function() {
            $('#ccModelGrid .cc-option-card').removeClass('is-selected');
            $(this).addClass('is-selected');
            var isContract = $(this).data('value') === 'contract';
            var modelName = isContract ? 'Contract Staffing' : 'Full-time Hiring';

            $('#ccModelBannerText').text(modelName);
            $('#ccContractFields').toggleClass('d-none', !isContract);
            $('#ccFulltimeFields').toggleClass('d-none', isContract);

            if (isContract) {
                ccRecalc();
            } else {
                ccFulltimeRecalc();
            }
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

            var amount = parseFloat($(this).data('amount')) || 0;
            var empType = ($(this).data('employment-type') || '').toString().toLowerCase();
            var isContract = empType.indexOf('contract') !== -1 || empType.indexOf('hour') !== -1;

            $('#ccServiceTypeGrid .cc-option-card').removeClass('is-selected');
            $('#ccServiceTypeGrid .cc-option-card[data-value="paid"]').addClass('is-selected');
            $('#ccPaidServiceSection').removeClass('d-none');
            $('#ccFreeServiceNote').addClass('d-none');

            // Contract Staffing fields
            $('#ccMarginPct').val(20);
            $('#ccBillingAmount').val(amount ? Math.round(amount).toLocaleString('en-IN') : '0');

            // Full-time Hiring fields — reset to defaults, seeded with the real offer amount
            $('#ccFeeMethodGrid .cc-option-card').removeClass('is-selected');
            $('#ccFeeMethodGrid .cc-option-card[data-value="percentage"]').addClass('is-selected');
            $('#ccCalcBaseGrid .cc-option-card').removeClass('is-selected');
            $('#ccCalcBaseGrid .cc-option-card[data-value="offered"]').addClass('is-selected');
            $('#ccCalcBaseLabel').html('Offered Amount (CTC) <span class="text-muted fw-normal">(Read Only)</span>');
            $('#ccCalcBaseAmount').prop('disabled', true).val(amount ? Math.round(amount).toLocaleString('en-IN') : '0');
            $('#ccFeePct').val(15);
            $('#ccFixedFee').val(amount ? Math.round(amount).toLocaleString('en-IN') : '0');
            $('#ccReplacementPeriod').val('90');
            $('#ccReplacementPeriodText').text('90');
            $('#ccReplacementRequestBlock').addClass('d-none');
            $('#ccToggleReplacementRequest').html('<i class="fa-solid fa-plus"></i> Log a Replacement Request');
            $('#ccInternalNote').val('');
            $('#ccNoteCount').text('0');

            $('#ccModelGrid .cc-option-card[data-value="' + (isContract ? 'contract' : 'fulltime') + '"]').click();

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
            Swal.fire({
                icon: 'info',
                title: 'Preview Only',
                text: 'Commercial terms captured for preview — connect this to a save endpoint to persist them.',
                confirmButtonText: 'Got it'
            }).then(function() {
                $('#kt_commercialConfig_close').trigger('click');
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
