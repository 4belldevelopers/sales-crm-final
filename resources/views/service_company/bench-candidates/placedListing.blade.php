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

        .placed-timeline-track { display: flex; align-items: center; }
        .placed-timeline-node { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .placed-timeline-node.is-static { background: #e4e6ef; border: 2px solid #e4e6ef; }
        .placed-timeline-node.is-done { background: #17c653; border: 2px solid #17c653; }
        .placed-timeline-line { flex: 1; height: 2px; background: #e4e6ef; min-width: 26px; margin: 0 2px; }

        .placed-status-pill {
            display: inline-flex; align-items: center; width: fit-content;
            font-size: 11.5px; font-weight: 600; padding: .25rem .55rem; border-radius: .45rem;
        }
        .placed-action-btn { width: 30px; height: 30px; }

        .rows-per-page-select { cursor: pointer; }
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
                         Filter bar — Search and Sort are real and wired to
                         the existing AJAX endpoint. Placed Date / Recruiter /
                         Client / Job / Location / More Filters / Saved Views /
                         Group By have no backend support yet, so they render
                         as inert HTML controls for now.
                    ====================================================== --}}
                    <div class="card mb-3">
                        <div class="card-body py-3 px-4">
                            <div class="placed-filter-bar">
                                <div class="flex-grow-1" style="min-width: 220px; max-width: 340px;">
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

                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-calendar-days"></i> Placed Date</button>
                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-id-badge"></i> Recruiter</button>
                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-building"></i> Client</button>
                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-briefcase"></i> Job / Designation</button>
                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-location-dot"></i> Location</button>
                                <button type="button" class="placed-filter-btn"><i class="fa-solid fa-sliders"></i> More Filters</button>

                                <div class="ms-auto d-flex align-items-center gap-2">
                                    <button type="button" class="placed-filter-btn"><i class="fa-solid fa-star"></i> Saved Views</button>
                                    <button type="button" class="placed-filter-btn"><i class="fa-solid fa-table-columns"></i> Group By</button>
                                </div>

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
                                            <th>Current Assignment</th>
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
