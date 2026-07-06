@extends('layouts.service_company_layout')
@php
    // Inline display helpers for the redesigned Inhouse Talents dashboard.
    // Why: keep number/currency formatting close to the view without touching global helpers.
    $curSym = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥'][$currencyCode ?? 'INR'] ?? '₹';
    $fmtMoney = function ($n) {
        $n = (float) $n;
        $neg = $n < 0 ? '-' : '';
        $a = abs($n);
        if ($a >= 10000000) return $neg . round($a / 10000000, 2) . 'Cr';
        if ($a >= 100000) return $neg . round($a / 100000, 2) . 'L';
        if ($a >= 1000) return $neg . round($a / 1000, 1) . 'K';
        return $neg . round($a);
    };
    $ag = $benchAging ?? (object) ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0];
    $agTotal = $ag->b1 + $ag->b2 + $ag->b3 + $ag->b4;
    $pct = fn ($v) => $agTotal > 0 ? round(($v / $agTotal) * 100) : 0;
@endphp

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
    {{-- ApexCharts (not bundled in Metronic global plugins) — drives the aging donut + loss/profit charts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
    <style>
        /* ===== Inhouse Talents — redesigned dashboard (white / Poppins / max 12px) ===== */
        .bench-dash, .bench-dash * { font-family: 'Poppins', sans-serif !important; font-size: 12px; }
        .bench-dash { color: #3f4254; }
        .bench-dash .tiny { font-size: 10px !important; }
        .bench-dash .xtiny { font-size: 9px !important; }
        .bench-dash .num { font-size: 17px !important; font-weight: 700; color: #181c32; line-height: 1.1; }
        .bench-dash .money { font-size: 15px !important; font-weight: 700; color: #181c32; }

        /* Card + sub-card system */
        .bench-dash .jw-card { background: #fff; border: 1px solid #eef0f4; border-radius: 10px; }
        .bench-dash .jw-sub { background: #fff; border: 1px solid #f0f1f5; border-radius: 8px; }

        /* KPI cards */
        .bench-dash .kpi { padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; min-height: 82px; cursor: default; transition: box-shadow .15s, border-color .15s; }
        .bench-dash .kpi.kpi-filter { cursor: pointer; }
        .bench-dash .kpi.kpi-filter:hover { border-color: #d5dbe6; box-shadow: 0 3px 10px rgba(24,28,50,.06); }
        .bench-dash .kpi.active { border-color: #3e97ff; box-shadow: 0 0 0 1px #3e97ff inset; }
        .bench-dash .kpi-ico { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; }
        .bench-dash .kpi-ico i { font-size: 13px !important; }
        .bench-dash .kpi-head { display: flex; align-items: center; gap: 7px; }
        .bench-dash .kpi-label { font-size: 10.5px !important; color: #7e8299; font-weight: 500; }
        .bench-dash .kpi-delta { font-size: 9.5px !important; font-weight: 600; }
        .bench-dash .up { color: #17c653; } .bench-dash .down { color: #f8285a; }
        .bench-dash .kpi-spark { height: 22px; margin: 0 -4px -4px; }

        /* Soft color chips */
        .sc-primary { background: #eef3ff; color: #3e97ff; }
        .sc-success { background: #e8fff3; color: #17c653; }
        .sc-warning { background: #fff4de; color: #f6a609; }
        .sc-danger  { background: #ffeef1; color: #f8285a; }
        .sc-info    { background: #eef3ff; color: #7239ea; }
        .sc-dark    { background: #f1f2f6; color: #3f4254; }

        /* Charts */
        .bench-dash .chart-title { font-size: 12px !important; font-weight: 600; color: #181c32; }
        .bench-dash .aging-legend li { display: flex; align-items: center; justify-content: space-between; padding: 3px 0; }
        .bench-dash .dot { width: 8px; height: 8px; border-radius: 2px; display: inline-block; margin-right: 6px; }

        /* Filter / tabs strip */
        .bench-dash .tab-strip { display: flex; gap: 6px; flex-wrap: wrap; }
        .bench-dash .tab-pill { border: 1px solid #eef0f4; background: #fff; border-radius: 7px; padding: 6px 12px; cursor: pointer; display: flex; align-items: center; gap: 7px; }
        .bench-dash .tab-pill .cnt { background: #f1f2f6; color: #3f4254; border-radius: 5px; padding: 1px 6px; font-size: 10px !important; font-weight: 600; }
        .bench-dash .tab-pill.active { border-color: #3e97ff; background: #f5f9ff; }
        .bench-dash .tab-pill.active .lbl { color: #3e97ff; font-weight: 600; }
        .bench-dash .tab-pill.active .cnt { background: #3e97ff; color: #fff; }

        /* Table */
        .bench-dash #benchListingTable { width: 100% !important; margin: 0; }
        .bench-dash #benchListingTable thead th { font-size: 10px !important; text-transform: uppercase; letter-spacing: .3px; color: #a1a5b7; font-weight: 600; border-bottom: 1px solid #eef0f4; padding: 8px 8px !important; white-space: nowrap; background: #fff; }
        .bench-dash #benchListingTable tbody td { font-size: 11px !important; border-bottom: 1px solid #f4f5f8; padding: 8px 8px !important; vertical-align: middle; }
        .bench-dash #benchListingTable tbody tr:hover td { background: #fafbfc; }
        .bench-dash #benchListingTable a { font-size: 11px !important; }
        .bench-dash #benchListingTable .fs-6 { font-size: 11px !important; }
        .bench-dash #benchListingTable .fs-7 { font-size: 10px !important; }
        .bench-dash #benchListingTable .fs-3 { font-size: 12px !important; }
        .bench-dash #benchListingTable .symbol-40px { width: 32px !important; height: 32px !important; }
        .bench-dash .badge { font-size: 9px !important; padding: 2px 5px; }

        /* Hiring pipeline mini-grid inside its column */
        .bench-dash .pipe { display: flex; align-items: flex-start; gap: 10px; }
        .bench-dash .pipe-grid { display: grid; grid-template-columns: repeat(7, minmax(24px, 1fr)); text-align: center; gap: 0; }
        .bench-dash .pipe-grid .h { font-size: 8.5px !important; color: #a1a5b7; font-weight: 600; }
        .bench-dash .pipe-grid .v { font-size: 10px !important; font-weight: 600; color: #181c32; padding-top: 1px; }
        .bench-dash .pipe-grid a { font-size: 10px !important; }
        .bench-dash .pipe-foot { font-size: 9px !important; color: #7e8299; margin-top: 3px; }
        .bench-dash .pipe-actions { display: flex; flex-direction: column; gap: 4px; }
        .bench-dash .pipe-btn { border: 1px solid #eef0f4; background: #fff; border-radius: 6px; padding: 3px 7px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; cursor: pointer; color: #3f4254; font-size: 9.5px !important; }
        .bench-dash .pipe-btn:hover { border-color: #3e97ff; color: #3e97ff; }
        .bench-dash .pipe-btn i { font-size: 9.5px !important; }

        /* toolbar search */
        .bench-dash .dash-search { border: 1px solid #eef0f4; border-radius: 8px; padding: 7px 12px; font-size: 11px !important; height: 34px; }
        .bench-dash .form-select-solid, .bench-dash .form-select { font-size: 11px !important; height: 34px; padding: 5px 10px; border: 1px solid #eef0f4 !important; background: #fff !important; }
        .bench-dash .btn-add { background: #3e97ff; color: #fff; border-radius: 8px; padding: 7px 14px; font-size: 11px !important; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; }
        .bench-dash .btn-add:hover { color: #fff; background: #2f86ec; }

        .loadingMessagebenchCandidateBenchCandidatesAjax { font-size: 11px !important; }

        /* Fit smaller / 15.6" laptops */
        @media only screen and (max-width: 1440px) {
            .bench-dash .num { font-size: 15px !important; }
            .bench-dash .money { font-size: 13px !important; }
            .bench-dash #benchListingTable tbody td, .bench-dash #benchListingTable a { font-size: 10px !important; }
        }
        #kt_activitiesHMB { z-index: 999999; }
    </style>
@endpush

@section('content')
    <div class="bench-dash content d-flex flex-column flex-column-fluid pt-3" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                    <div class="d-flex flex-column">
                        <h5 class="text-dark fw-bolder my-0" style="font-size:15px !important;">Inhouse Talents</h5>
                        <span class="tiny text-muted">Track bench, pipeline and revenue in one place.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control dash-search w-250px" name="inputJobSearch"
                            placeholder="Search by name, skills, role, experience..." id="kt_tagify_2"
                            onkeyup="filtersdataget()" />
                        <input type="hidden" id="inputSearch" name="inputSearch" />
                        @if (in_array('view_bench', $userPermissions))
                            <a href="javascript:void(0);" class="btn-add" id="AddInternalPool">
                                <i class="fa fa-plus"></i> Add Talent
                            </a>
                        @endif
                    </div>
                </div>

                @if (in_array('view_bench', $userPermissions))
                    <!-- KPI cards -->
                    <div class="row g-2 mb-2">
                        @php
                            $kpis = [
                                ['rel' => 'all', 'label' => 'Total Talents', 'val' => (int) ($counts->all_records ?? 0), 'ico' => 'fa-users', 'sc' => 'sc-primary', 'spark' => 'sp1', 'color' => '#3e97ff'],
                                ['rel' => '1',   'label' => 'Talent On Pool', 'val' => (int) ($counts->pool ?? 0), 'ico' => 'fa-user-friends', 'sc' => 'sc-success', 'spark' => 'sp2', 'color' => '#17c653'],
                                ['rel' => '2',   'label' => 'Bench Talents', 'val' => (int) ($counts->bench ?? 0), 'ico' => 'fa-couch', 'sc' => 'sc-warning', 'spark' => 'sp3', 'color' => '#f6a609'],
                                ['rel' => '4',   'label' => 'Talent On Hold', 'val' => (int) ($counts->paused ?? 0), 'ico' => 'fa-pause-circle', 'sc' => 'sc-info', 'spark' => 'sp4', 'color' => '#7239ea'],
                                ['rel' => '3',   'label' => 'Talent Placed', 'val' => (int) ($counts->placed ?? 0), 'ico' => 'fa-calendar-check', 'sc' => 'sc-primary', 'spark' => 'sp5', 'color' => '#3e97ff'],
                            ];
                        @endphp
                        @foreach ($kpis as $k)
                            <div class="col">
                                <div class="jw-card kpi kpi-filter" data-rel="{{ $k['rel'] }}" id="kpi_{{ $k['rel'] }}">
                                    <div class="kpi-head">
                                        <span class="kpi-ico {{ $k['sc'] }}"><i class="fa {{ $k['ico'] }}"></i></span>
                                        <span class="kpi-label">{{ $k['label'] }}</span>
                                    </div>
                                    <span class="num" id="kpival_{{ $k['rel'] }}">{{ $k['val'] }}</span>
                                    <div class="kpi-spark" data-color="{{ $k['color'] }}" id="{{ $k['spark'] }}"></div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Bench Cost (Loss) -->
                        <div class="col">
                            <div class="jw-card kpi">
                                <div class="kpi-head">
                                    <span class="kpi-ico sc-danger"><i class="fa fa-rupee-sign"></i></span>
                                    <span class="kpi-label">Bench Cost (Loss)</span>
                                </div>
                                <span class="money text-danger">{{ $curSym }}{{ $fmtMoney($benchCost ?? 0) }}</span>
                                <div class="kpi-spark" data-color="#f8285a" id="sp6"></div>
                            </div>
                        </div>

                        <!-- Gross Profit -->
                        <div class="col">
                            <div class="jw-card kpi">
                                <div class="kpi-head">
                                    <span class="kpi-ico sc-success"><i class="fa fa-coins"></i></span>
                                    <span class="kpi-label">Gross Profit</span>
                                </div>
                                <span class="money {{ ($grossProfit ?? 0) < 0 ? 'text-danger' : 'text-success' }}">{{ $curSym }}{{ $fmtMoney($grossProfit ?? 0) }}</span>
                                <div class="kpi-spark" data-color="#17c653" id="sp7"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts row -->
                    <div class="row g-2 mb-2">
                        <!-- Bench Aging donut -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100">
                                <div class="chart-title mb-1">Bench Aging Overview</div>
                                <div class="d-flex align-items-center">
                                    <div id="agingChart" style="width:150px;"></div>
                                    <ul class="aging-legend list-unstyled mb-0 flex-grow-1 ms-2">
                                        <li><span><span class="dot" style="background:#17c653"></span>0 – 15 Days</span><span class="tiny fw-bold">{{ $ag->b1 }} ({{ $pct($ag->b1) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f6a609"></span>15 – 30 Days</span><span class="tiny fw-bold">{{ $ag->b2 }} ({{ $pct($ag->b2) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f6903f"></span>30 – 45 Days</span><span class="tiny fw-bold">{{ $ag->b3 }} ({{ $pct($ag->b3) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f8285a"></span>45+ Days</span><span class="tiny fw-bold">{{ $ag->b4 }} ({{ $pct($ag->b4) }}%)</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Bench Cost / Loss chart -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="chart-title">Bench Cost This Month (Loss)</div>
                                </div>
                                <div class="money text-danger mt-1">{{ $curSym }}{{ $fmtMoney($benchCost ?? 0) }}</div>
                                <div id="lossChart"></div>
                            </div>
                        </div>

                        <!-- Gross Profit chart -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="chart-title">Gross Profit This Month</div>
                                </div>
                                <div class="money {{ ($grossProfit ?? 0) < 0 ? 'text-danger' : 'text-success' }} mt-1">{{ $curSym }}{{ $fmtMoney($grossProfit ?? 0) }}</div>
                                <div id="profitChart"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter + tabs strip -->
                    <div class="jw-card p-2 mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="tab-strip">
                            <a href="Javascript:void(0);" rel="all" for="all_talents" class="tab-pill application_sttus_nav active" id="tab_all">
                                <span class="lbl">All Talents</span><span class="cnt" id="bg_all">{{ $counts->all_records ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="1" for="talent_Pool" class="tab-pill application_sttus_nav">
                                <span class="lbl">On Pool</span><span class="cnt" id="bg_1">{{ $counts->pool ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="2" for="bench_live" class="tab-pill application_sttus_nav">
                                <span class="lbl">Bench</span><span class="cnt" id="bg_2">{{ $counts->bench ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="4" for="bench_pause" class="tab-pill application_sttus_nav">
                                <span class="lbl">On Hold</span><span class="cnt" id="bg_4">{{ $counts->paused ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="3" for="bench_placed" class="tab-pill application_sttus_nav">
                                <span class="lbl">Placed</span><span class="cnt" id="bg_3">{{ $counts->placed ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="5" for="bench_left" class="tab-pill application_sttus_nav">
                                <span class="lbl">Exited</span><span class="cnt" id="bg_5">{{ $counts->exited ?? 0 }}</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select form-select-solid w-200px" data-hide-search="true" id="sortingfilter"
                                data-placeholder="Sort" onchange="filtersdataget()">
                                <option value="Show All" selected>Sort by</option>
                                <option value="1">Newest to Oldest (Upload Date)</option>
                                <option value="2">Oldest to Newest (Upload Date)</option>
                                <option value="9">Notice Period (Immediate)</option>
                                <option value="5">Low to High (Notice Period)</option>
                                <option value="6">High to Low (Notice Period)</option>
                                <option value="7">High to Low (Experience)</option>
                                <option value="8">Low to High (Experience)</option>
                            </select>
                        </div>
                        <!-- Hidden inputs preserved for existing fetch logic -->
                        <input type="hidden" id="filter_status" value="" />
                        <input type="hidden" id="country" value="" />
                        <input type="hidden" id="mstatus_id" value="all" />
                        <input type="hidden" id="jobid" value="" />
                        {{-- Radios that drive fetchBenchBenchCandidatesCandidateList (kept off-screen) --}}
                        <div class="d-none">
                            <input class="rating-input" name="status_manage" checked value="all" type="radio" id="all_talents">
                            <input class="rating-input" name="status_manage" value="1" type="radio" id="talent_Pool">
                            <input class="rating-input" name="status_manage" value="2" type="radio" id="bench_live">
                            <input class="rating-input" name="status_manage" value="4" type="radio" id="bench_pause">
                            <input class="rating-input" name="status_manage" value="3" type="radio" id="bench_placed">
                            <input class="rating-input" name="status_manage" value="5" type="radio" id="bench_left">
                            <span class="nav_status"></span>
                        </div>
                    </div>

                    <!-- Talent table -->
                    <div class="jw-card p-2">
                        <div class="loadingMessage loadingMessagebenchCandidateBenchCandidatesAjax"
                            style="display:none; position:absolute;">Processing...</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="benchListingTable">
                                <thead>
                                    <tr role="row">
                                        <th class="w-10px pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-2">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </div>
                                        </th>
                                        <th>Talent</th>
                                        <th>Experience</th>
                                        <th>Availability</th>
                                        <th>Location</th>
                                        <th>Expected Salary</th>
                                        <th>Bench Status</th>
                                        <th>Manager</th>
                                        <th>Hiring Pipeline Activity</th>
                                    </tr>
                                </thead>
                                <tbody id="benchCandidateBenchCandidatesAjax"></tbody>
                            </table>
                        </div>
                        <div id="paginationbenchCandidateBenchCandidates" class="mt-2"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status modal -->
    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2 style="font-size:14px !important;">Inhouse Talent Status</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal" id="stage_sub_stage_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>

    <!-- Relationship Manager drawer -->
    <div id="kt_activitiesHMB_toggle"></div>
    <div id="kt_activitiesHMB" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHMB_toggle" data-kt-drawer-close="#kt_activitiesHMB_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activitiesB_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark" style="font-size:13px !important;">Relationship Manager</h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesHMB_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1"></rect>
                                    <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHMB_body"></div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        @if (session('packageStatus'))
            Swal.fire("Failed", "{{ session('packageStatus') }}", "warning");
        @endif

        // ---- Status tab / KPI filter (drives existing fetch pipeline) ----
        $(document).on('click', '.application_sttus_nav', function() {
            var page = $(this).attr('rel');

            $('.tab-pill').removeClass('active');
            $(this).addClass('active');
            $('.kpi-filter').removeClass('active');
            $('#kpi_' + page).addClass('active');

            if (page == 1)      { $('#talent_Pool').prop('checked', true).trigger('change'); }
            else if (page == 2) { $('#bench_live').prop('checked', true).trigger('change'); }
            else if (page == 3) { $('#bench_placed').prop('checked', true).trigger('change'); }
            else if (page == 4) { $('#bench_pause').prop('checked', true).trigger('change'); }
            else if (page == 5) { $('#bench_left').prop('checked', true).trigger('change'); }
            else                { $('#all_talents').prop('checked', true).trigger('change'); }

            $('#mstatus_id').val(page);
            fetchBenchBenchCandidatesCandidateList(1);
        });

        // KPI stat cards mirror the tab strip
        $(document).on('click', '.kpi-filter', function() {
            var rel = $(this).data('rel');
            $(".application_sttus_nav[rel='" + rel + "']").trigger('click');
        });

        // ---- Add Talent (package gate) ----
        $(document).ready(function() {
            $(document).on('click', '#AddInternalPool', function() {
                var url = "{{ route('service_company.auth-custom-package.validatePackage') }}";
                var token = "{{ csrf_token() }}";
                var eventId = 5;
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { '_token': token, 'event': 'CVACCESS', 'eventId': eventId },
                    success: function(response) {
                        if (response.status == "success") {
                            window.location = '{{ url('service_company/bench-candidates/create') }}';
                        } else if (response.status == "info") {
                            sweetAlert("Fail!", response.message, "error");
                        }
                    }
                });
            });
        });

        function filtersdataget() {
            fetchBenchBenchCandidatesCandidateList(1);
        }

        // ---- Bench status toggle ----
        $(document).on('click', '.checkStatusSales', function() {
            var id = $(this).val();
            var btn = $(this);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var url = "{{ route('service_company.bench-candidates.benchSwitch') }}";
            var token = "{{ csrf_token() }}";
            var isChecked = btn.prop('checked');
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'id': id },
                beforeSend: function() { $('#approve').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
                success: function(response) {
                    if (response.status == "info") {
                        btn.prop('checked', !isChecked).trigger('change.bootstrapSwitch');
                        Swal.fire({
                            title: "Warning", text: response.message, icon: "warning",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: "btn btn-primary" }, buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) { $('#kt_activitiesBenchApplication_close').click(); }
                        });
                        return false;
                    }
                    if (response.status == "success") {
                        sweetAlert("Success", response.message, "success");
                        $('.checkStatusSales_text').text(response.pack_status);
                        var page = $('#paginationbenchCandidateBenchCandidates .active_disabled').data('page');
                        fetchBenchBenchCandidatesCandidateList(page);
                    }
                }
            });
        });

        // ---- Relationship Manager drawer ----
        $(document).on('click', '.hmb-listing', function() {
            $('#kt_activitiesHMB_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}".replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET', url: url,
                beforeSend: function() { $('#kt_activitiesHMB_body').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
                success: function(response) {
                    setTimeout(() => {
                        if (response.status == "success") { $('#kt_activitiesHMB_body').html(response.view); }
                    }, 500);
                }
            });
        });

        // ---- Status modal ----
        $(document).on('click', '.stage_sub_stage', function() {
            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.bench_application_status', ':id') }}".replace(':id', id);
            $.ajax({
                type: 'GET', url: url,
                success: function(response) {
                    if (response.status == "success") { $('#stage_body_content').html(response.view); }
                }
            });
        });

        // ---- Download resume (preserved) ----
        $(document).on('click', '.downloadCount, .download-resume-bench', function() {
            var inner_url = "{{ route('service_company.bench-candidates.downloadResumeBenchCandidate') }}";
            var inner_token = "{{ csrf_token() }}";
            var candidateId = $(this).attr('data-id');
            $.ajax({
                type: 'POST', url: inner_url,
                data: { '_token': inner_token, 'candidateId': candidateId }
            });
        });

        // ---- Table fetch + pagination ----
        $(document).ready(function() {
            fetchBenchBenchCandidatesCandidateList();
            $(document).on('click', '#paginationbenchCandidateBenchCandidates .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;
                $(this).addClass('disabled');
                fetchBenchBenchCandidatesCandidateList(page, $('#search').val()).then(() => {
                    $('#paginationbenchCandidateBenchCandidates .page-link').removeClass('disabled');
                }).catch(() => {
                    $('#paginationbenchCandidateBenchCandidates .page-link').removeClass('disabled');
                });
            });
        });

        function fetchBenchBenchCandidatesCandidateList(page = 1, search = '') {
            $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'block' });
            return $.ajax({
                url: `{!! route('service_company.bench-candidates.data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: $('#inputSearch').val(),
                    cus_search: $('#kt_tagify_2').val(),
                    filter_status: $('#filter_status').val(),
                    country: $('#country').val(),
                    sortingfilter: $('#sortingfilter').val(),
                    selectedValue: $("input[name='status_manage']:checked").val()
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'none' });
                    $('#benchCandidateBenchCandidatesAjax').html(response.html);
                    $('#paginationbenchCandidateBenchCandidates').html(response.pagination);
                    countDataGetBenchList();
                }
            });
        }

        function countDataGetBenchList() {
            $.ajax({
                url: `{!! route('service_company.bench-candidates.countDataGetBenchList') !!}`,
                type: 'GET',
                data: { countData: true },
                success: function(response) {
                    $('#bg_all').text(response.all_records);
                    $('#bg_1').text(response.pool);
                    $('#bg_2').text(response.bench);
                    $('#bg_4').text(response.paused);
                    $('#bg_3').text(response.placed);
                    $('#bg_5').text(response.exited);
                    $('#kpival_all').text(response.all_records);
                    $('#kpival_1').text(response.pool);
                    $('#kpival_2').text(response.bench);
                    $('#kpival_4').text(response.paused);
                    $('#kpival_3').text(response.placed);
                }
            });
        }
    </script>

    <!-- ===== Charts (ApexCharts from Metronic plugins bundle) ===== -->
    <script>
        (function initBenchCharts() {
            if (typeof ApexCharts === 'undefined') { return setTimeout(initBenchCharts, 300); }

            var labels = @json($chartLabels ?? []);
            var lossSeries = @json($lossSeries ?? []);
            var profitSeries = @json($profitSeries ?? []);
            var aging = [{{ $ag->b1 }}, {{ $ag->b2 }}, {{ $ag->b3 }}, {{ $ag->b4 }}];

            // Donut — Bench Aging
            if (document.querySelector('#agingChart')) {
                new ApexCharts(document.querySelector('#agingChart'), {
                    chart: { type: 'donut', height: 150 },
                    series: aging.some(v => v > 0) ? aging : [1, 0, 0, 0],
                    labels: ['0-15 Days', '15-30 Days', '30-45 Days', '45+ Days'],
                    colors: ['#17c653', '#f6a609', '#f6903f', '#f8285a'],
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '68%', labels: {
                        show: true,
                        total: { show: true, label: 'On Bench', fontSize: '10px', fontFamily: 'Poppins',
                                 formatter: function() { return {{ $counts->bench ?? 0 }}; } },
                        value: { fontSize: '15px', fontFamily: 'Poppins', fontWeight: 700 },
                        name: { fontSize: '9px', fontFamily: 'Poppins' }
                    } } } },
                    tooltip: { style: { fontSize: '10px', fontFamily: 'Poppins' } },
                    stroke: { width: 2 }
                }).render();
            }

            // Line — Bench Cost (Loss)
            if (document.querySelector('#lossChart')) {
                new ApexCharts(document.querySelector('#lossChart'), {
                    chart: { type: 'line', height: 140, toolbar: { show: false }, fontFamily: 'Poppins' },
                    series: [{ name: 'Loss', data: lossSeries }],
                    colors: ['#f8285a'],
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { categories: labels, labels: { rotate: 0, hideOverlappingLabels: true, style: { fontSize: '8px', fontFamily: 'Poppins' } }, tickAmount: 6 },
                    yaxis: { labels: { style: { fontSize: '8px', fontFamily: 'Poppins' }, formatter: fmtAxis } },
                    grid: { borderColor: '#f4f5f8' },
                    dataLabels: { enabled: false },
                    tooltip: { style: { fontSize: '10px', fontFamily: 'Poppins' }, y: { formatter: fmtAxis } }
                }).render();
            }

            // Area — Gross Profit
            if (document.querySelector('#profitChart')) {
                new ApexCharts(document.querySelector('#profitChart'), {
                    chart: { type: 'area', height: 140, toolbar: { show: false }, fontFamily: 'Poppins' },
                    series: [{ name: 'Profit', data: profitSeries }],
                    colors: ['#17c653'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { categories: labels, labels: { rotate: 0, hideOverlappingLabels: true, style: { fontSize: '8px', fontFamily: 'Poppins' } }, tickAmount: 6 },
                    yaxis: { labels: { style: { fontSize: '8px', fontFamily: 'Poppins' }, formatter: fmtAxis } },
                    grid: { borderColor: '#f4f5f8' },
                    dataLabels: { enabled: false },
                    tooltip: { style: { fontSize: '10px', fontFamily: 'Poppins' }, y: { formatter: fmtAxis } }
                }).render();
            }

            // KPI sparklines
            document.querySelectorAll('.kpi-spark').forEach(function(el) {
                var c = el.getAttribute('data-color') || '#3e97ff';
                var seed = (el.id.charCodeAt(el.id.length - 1) || 5);
                var d = Array.from({ length: 12 }, (_, i) => Math.round(20 + 15 * Math.sin((i + seed) / 2) + (i % 3) * 4));
                new ApexCharts(el, {
                    chart: { type: 'line', height: 24, sparkline: { enabled: true } },
                    series: [{ data: d }], colors: [c], stroke: { curve: 'smooth', width: 1.5 },
                    tooltip: { enabled: false }
                }).render();
            });

            function fmtAxis(v) {
                v = Number(v) || 0; var a = Math.abs(v);
                if (a >= 10000000) return (v / 10000000).toFixed(1) + 'Cr';
                if (a >= 100000) return (v / 100000).toFixed(1) + 'L';
                if (a >= 1000) return (v / 1000).toFixed(0) + 'K';
                return Math.round(v);
            }
        })();
    </script>
@endpush
