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
    $dl = $deltas ?? (object) ['all' => 0, 'pool' => 0, 'bench' => 0, 'hold' => 0, 'placed' => 0, 'cost' => 0, 'profit' => 0];
    // Renders an "↑/↓ N% vs last month" chip. For "cost", a rise is bad (red), everything else a rise is good.
    $deltaChip = function ($v, $invert = false) {
        $up = $v >= 0;
        $good = $invert ? !$up : $up;
        return '<span class="kpi-delta ' . ($good ? 'up' : 'down') . '"><i class="fa fa-arrow-' . ($up ? 'up' : 'down') . '"></i> ' .
            abs($v) . '% <span class="kpi-vs">vs last month</span></span>';
    };
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

        /* KPI cards — icon on the left, label/value/delta stacked to its right (value sits directly under the label, not the icon) */
        .bench-dash .kpi { padding: 10px 12px; display: flex; align-items: flex-start; gap: 10px; min-height: 82px; cursor: default; transition: box-shadow .15s, border-color .15s; }
        .bench-dash .kpi.kpi-filter { cursor: pointer; }
        .bench-dash .kpi.kpi-filter:hover { border-color: #d5dbe6; box-shadow: 0 3px 10px rgba(24,28,50,.06); }
        .bench-dash .kpi.active { border-color: #3e97ff; box-shadow: 0 0 0 1px #3e97ff inset; }
        .bench-dash .kpi-ico { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bench-dash .kpi-ico i { font-size: 13px !important; }
        .bench-dash .kpi-text { display: flex; flex-direction: column; gap: 2px; }
        .bench-dash .kpi-label { font-size: 10.5px !important; color: #7e8299; font-weight: 500; }
        .bench-dash .kpi-delta { font-size: 9.5px !important; font-weight: 600; }
        .bench-dash .up { color: #17c653; } .bench-dash .down { color: #f8285a; }
        .bench-dash .kpi-delta { display: inline-flex; align-items: center; gap: 3px; }
        .bench-dash .kpi-delta i { font-size: 8px !important; }
        .bench-dash .kpi-vs { color: #a1a5b7; font-weight: 500; }

        /* Ghost buttons (Saved Views / Export) */
        .bench-dash .btn-ghost { border: 1px solid #eef0f4; background: #fff; color: #3f4254; border-radius: 8px; padding: 7px 12px; font-size: 11px !important; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
        .bench-dash .btn-ghost:hover { border-color: #d5dbe6; color: #3e97ff; }
        .bench-dash .btn-ghost i { font-size: 11px !important; }

        /* Sub-cards (avg/longest age, margin, roi) */
        .bench-dash .stat-sub { display: flex; align-items: center; gap: 8px; padding: 8px 10px; }
        .bench-dash .stat-sub .ico { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .bench-dash .stat-sub .ico i { font-size: 11px !important; }
        .bench-dash .stat-sub .lbl { font-size: 9.5px !important; color: #7e8299; }
        .bench-dash .stat-sub .val { font-size: 13px !important; font-weight: 700; color: #181c32; }

        /* Chart header dropdown */
        .bench-dash .chart-range { border: 1px solid #eef0f4; background: #fff; border-radius: 6px; font-size: 9.5px !important; padding: 3px 8px; color: #7e8299; height: 24px; }

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
        .bench-dash #benchListingTable .symbol-label { line-height: 1; }
        .bench-dash .badge { font-size: 9px !important; padding: 2px 5px; }

        /* Clients Pipeline Activity mini-grid — bold throughout */
        .bench-dash .pipe { display: flex; align-items: flex-start; gap: 10px; }
        .bench-dash .pipe-grid { display: grid; grid-template-columns: repeat(7, minmax(24px, 1fr)); text-align: center; gap: 0; }
        .bench-dash .pipe-grid .h { font-size: 8.5px !important; color: #a1a5b7; font-weight: 700; }
        .bench-dash .pipe-grid .v { font-size: 10px !important; font-weight: 700; color: #181c32; padding-top: 1px; }
        .bench-dash .pipe-grid a { font-size: 10px !important; font-weight: 700; }
        .bench-dash .pipe-foot { font-size: 9px !important; font-weight: 700; color: #3f4254; margin-top: 3px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .bench-dash .pipe-ico { display: inline-flex; align-items: center; gap: 3px; color: #7e8299; }
        .bench-dash .pipe-ico i { font-size: 9px !important; }

        /* Manager "Change" — plain text link, no button chrome */
        .bench-dash .manager-change { color: #3e97ff; font-size: 9.5px !important; font-weight: 600; cursor: pointer; width: fit-content; }
        .bench-dash .manager-change:hover { text-decoration: underline; color: #3e97ff; }

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

        /* ============================================================
           Change Talent Status drawer — enterprise redesign
           Positioning/overlay/width owned by the drawer plugin (same
           width config as the Job drawer). Below is content styling only.
           ============================================================ */
        #bstatus_drawer, #bstatus_drawer * { font-family: 'Poppins', sans-serif !important; box-sizing: border-box; }
        #bstatus_drawer { z-index: 999999; font-size: 12px; }
        #bstatus_drawer .min-w-0 { min-width: 0; }

        /* ---- Header ---- */
        .bstatus-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 22px; background: #fff; border-bottom: 1px solid #eef0f4; flex-shrink: 0; }
        .bstatus-topbar-left { display: flex; align-items: center; gap: 12px; }
        .bstatus-title-ico { width: 40px; height: 40px; border-radius: 10px; background: #eef3ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; }
        .bstatus-title { font-size: 19px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bstatus-topbar-center { display: flex; flex-direction: column; align-items: center; }
        .bstatus-changing { font-size: 12px; color: #7e8299; }
        .bstatus-pill { border-radius: 8px; padding: 6px 18px; font-size: 14px; font-weight: 700; }
        .bstatus-pill.cur { background: #eef3ff; color: #3e97ff; border: 1px solid #dce8ff; }
        .bstatus-pill.new { background: #f6f7f9; color: #a1a5b7; border: 1px solid #e4e6ef; }
        .bstatus-pill.new.filled { background: #e8fff3; color: #17c653; border-color: #c8f4dc; }
        .bstatus-arrow { color: #b5b9c8; font-size: 14px; }
        .bstatus-topbar-right { display: flex; align-items: center; gap: 10px; }

        .bstatus-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 13px; }
        .bstatus-btn-cancel:hover { border-color: #d5dbe6; background: #f9fafc; }
        .bstatus-btn-confirm { border: none; background: #17c653; color: #fff; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; }
        .bstatus-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
        .bstatus-btn-confirm:not(:disabled):hover { background: #14b04a; }
        .bstatus-close { width: 36px; height: 36px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 16px; }
        .bstatus-close:hover { background: #f4f5f8; color: #181c32; }

        /* ---- Body ---- */
        .bstatus-body { overflow-y: auto; background: #f5f6fa; padding: 16px 22px; flex-grow: 1; }
        .bs-card { background: #fff; border: 1px solid #eef0f4; border-radius: 14px; padding: 18px 22px; }
        .bs-card + .bs-card { margin-top: 14px; }

        /* ---- Candidate summary bar ---- */
        .bs-cand-bar { display: flex; align-items: center; gap: 0; }
        .bs-cand-id { display: flex; align-items: center; gap: 14px; padding-right: 26px; }
        .bs-cand-avatar { width: 56px; height: 56px; border-radius: 50%; background: #efeafe; color: #7239ea; display: flex; align-items: center; justify-content: center; font-size: 19px; font-weight: 700; flex-shrink: 0; }
        .bs-cand-name { font-size: 19px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bs-cand-role { font-size: 13px; color: #7e8299; }
        .bs-cand-stat { flex: 1 1 0; min-width: 0; text-align: center; padding: 0 12px; border-left: 1px solid #eef0f4; }
        .bs-cand-stat .v { font-size: 20px; font-weight: 700; color: #181c32; line-height: 1.1; }
        .bs-cand-stat .l { font-size: 11.5px; color: #7e8299; margin-top: 2px; }

        /* ---- Numbered section headers ---- */
        .bs-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .bs-sec-num { width: 26px; height: 26px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bs-sec-title { font-size: 16px; font-weight: 700; color: #181c32; }

        /* ---- Status cards row (with arrows) ---- */
        .bs-status-row { display: flex; align-items: stretch; gap: 0; }
        .bs-status-cell { flex: 1 1 0; min-width: 0; }
        .bs-status-arrow { display: flex; align-items: center; justify-content: center; color: #c2c9d6; font-size: 14px; padding: 0 8px; flex-shrink: 0; }
        .bs-status-card { border: 1.5px solid #eef0f4; border-radius: 14px; padding: 18px; cursor: pointer; background: #fff; height: 100%; transition: border-color .15s, background .15s, box-shadow .15s; }
        .bs-status-card:hover { border-color: #cdd4e0; }
        .bs-status-card .ic { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 12px; }
        .bs-status-card .nm { font-size: 16px; font-weight: 700; }
        .bs-status-card .sb { font-size: 12px; color: #7e8299; line-height: 1.4; margin-top: 3px; }
        .bs-status-card.is-current { border-color: #3e97ff; background: #f5f9ff; }
        .bs-status-card.is-selected { border-color: #17c653; background: #f2fdf7; box-shadow: 0 0 0 1px #17c653 inset; }

        /* ---- Impact strip ---- */
        .bs-impact { background: #f2fdf7; border: 1px solid #d5f2e2; border-radius: 12px; padding: 16px 18px; margin-top: 16px; }
        .bs-impact-head { display: flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 700; color: #17845a; margin-bottom: 12px; }
        .bs-impact-item { display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: #3f4254; font-weight: 500; }
        .bs-impact-item i { color: #17c653; font-size: 13px; margin-top: 1px; flex-shrink: 0; }

        /* ---- Job/Project section ---- */
        .bs-job-search { position: relative; margin-bottom: 14px; }
        .bs-job-search input { width: 100%; border: 1px solid #eef0f4; border-radius: 10px; padding: 11px 14px 11px 38px; font-size: 13px; background: #fff; }
        .bs-job-search i { position: absolute; left: 14px; top: 13px; color: #a1a5b7; font-size: 13px; }
        .bs-job-table table { width: 100%; }
        .bs-job-table th { font-size: 11px; color: #a1a5b7; font-weight: 600; padding: 10px 8px; border-bottom: 1px solid #eef0f4; white-space: nowrap; text-align: left; }
        .bs-job-table td { font-size: 13px; padding: 12px 8px; border-bottom: 1px solid #f4f5f8; vertical-align: middle; }
        .bs-place-btn { border: 1px solid #dce8ff; background: #fff; color: #3e97ff; border-radius: 8px; padding: 7px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .bs-place-btn:hover { background: #3e97ff; color: #fff; border-color: #3e97ff; }
        .bs-place-btn.is-picked { border-color: #3e97ff; background: #3e97ff; color: #fff; }
        .bs-place-btn:disabled { background: #f1f2f6; color: #a1a5b7; border-color: #eef0f4; cursor: not-allowed; }

        @media (max-width: 1100px) {
            .bs-status-row { flex-wrap: wrap; }
            .bs-status-arrow { display: none; }
            .bs-status-cell { flex: 1 1 30%; }
        }
    </style>
@endpush

@section('content')
    <div class="bench-dash content d-flex flex-column flex-column-fluid pt-3" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-1" style="background:#fff;">
                    <div class="d-flex flex-column">
                        <h5 class="text-dark fw-bolder my-0 d-flex align-items-center gap-2" style="font-size:15px !important;">
                            Inhouse Talents
                            <i class="fa fa-circle-info text-muted" style="font-size:11px !important;" data-toggle="tooltip"
                                title="Track bench, pipeline and revenue in one place."></i>
                        </h5>
                        <span class="tiny text-muted">Track bench, pipeline and revenue in one place.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="hidden" id="inputSearch" name="inputSearch" />
                        <div class="dropdown">
                            <button class="btn-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bookmark"></i> Saved Views
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-2" style="font-size:11px;">
                                <li><a class="dropdown-item saved-view" data-view="all" href="javascript:void(0);">All Talents</a></li>
                                <li><a class="dropdown-item saved-view" data-view="2" href="javascript:void(0);">On Bench</a></li>
                                <li><a class="dropdown-item saved-view" data-view="1" href="javascript:void(0);">On Pool</a></li>
                                <li><a class="dropdown-item saved-view" data-view="3" href="javascript:void(0);">Placed</a></li>
                            </ul>
                        </div>
                        @if (in_array('view_bench', $userPermissions))
                            <a href="javascript:void(0);" class="btn-add" id="AddInternalPool">
                                <i class="fa fa-plus"></i> Add Talent
                            </a>
                        @endif
                        <button type="button" class="btn-ghost" id="exportTalents">
                            <i class="fa fa-file-export"></i> Export
                        </button>
                    </div>
                </div>

                @if (in_array('view_bench', $userPermissions))
                    <!-- KPI cards -->
                    <div class="row g-2 mb-2">
                        @php
                            $kpis = [
                                ['rel' => 'all', 'label' => 'Total Talents', 'val' => (int) ($counts->all_records ?? 0), 'ico' => 'fa-users', 'sc' => 'sc-primary', 'spark' => 'sp1', 'color' => '#3e97ff', 'd' => $dl->all],
                                ['rel' => '1',   'label' => 'Talent On Pool', 'val' => (int) ($counts->pool ?? 0), 'ico' => 'fa-user-friends', 'sc' => 'sc-success', 'spark' => 'sp2', 'color' => '#17c653', 'd' => $dl->pool],
                                ['rel' => '2',   'label' => 'Bench Talents', 'val' => (int) ($counts->bench ?? 0), 'ico' => 'fa-couch', 'sc' => 'sc-warning', 'spark' => 'sp3', 'color' => '#f6a609', 'd' => $dl->bench],
                                ['rel' => '4',   'label' => 'Talent On Hold', 'val' => (int) ($counts->paused ?? 0), 'ico' => 'fa-pause-circle', 'sc' => 'sc-info', 'spark' => 'sp4', 'color' => '#7239ea', 'd' => $dl->hold],
                                ['rel' => '3',   'label' => 'Talent Placed', 'val' => (int) ($counts->placed ?? 0), 'ico' => 'fa-calendar-check', 'sc' => 'sc-primary', 'spark' => 'sp5', 'color' => '#3e97ff', 'd' => $dl->placed],
                            ];
                        @endphp
                        @foreach ($kpis as $k)
                            <div class="col">
                                <div class="jw-card kpi kpi-filter" data-rel="{{ $k['rel'] }}" id="kpi_{{ $k['rel'] }}">
                                    <span class="kpi-ico {{ $k['sc'] }}"><i class="fa {{ $k['ico'] }}"></i></span>
                                    <div class="kpi-text">
                                        <span class="kpi-label">{{ $k['label'] }}</span>
                                        <span class="num" id="kpival_{{ $k['rel'] }}">{{ $k['val'] }}</span>
                                        {!! $deltaChip($k['d']) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Bench Cost (Loss) -->
                        <div class="col">
                            <div class="jw-card kpi">
                                <span class="kpi-ico sc-danger"><i class="fa fa-rupee-sign"></i></span>
                                <div class="kpi-text">
                                    <span class="kpi-label">Bench Cost (Loss)</span>
                                    <span class="money text-danger">{{ $curSym }}{{ $fmtMoney($benchCost ?? 0) }}</span>
                                    {!! $deltaChip($dl->cost, true) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search + Sort row -->
                    <div class="jw-card p-2 mb-2 d-flex align-items-center gap-2">
                        <div class="position-relative flex-grow-1">
                            <i class="fa fa-search position-absolute text-muted" style="left:12px; top:11px; font-size:11px !important;"></i>
                            <input type="text" class="form-control dash-search w-100" style="padding-left:30px;" name="inputJobSearch"
                                placeholder="Search by name, skills, role, experience, location..." id="kt_tagify_2" onkeyup="filtersdataget()" />
                        </div>
                        <select class="form-select form-select-solid w-200px flex-shrink-0" data-hide-search="true" id="sortingfilter"
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

                    <!-- Tabs strip -->
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
                                        <th>Relationship Manager</th>
                                        <th>Clients Pipeline Activity</th>
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

    <!-- Change Talent Status drawer — same drawer mechanism/width as the Job drawer ({default:'500px','lg':'1200px'}) -->
    <div id="bstatus_drawer_toggle" style="display:none;"></div>
    <div id="bstatus_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="bench-status"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#bstatus_drawer_toggle" data-kt-drawer-close="#bstatus_close_x">
        <div class="d-flex flex-column h-100 w-100">
            <!-- Header -->
            <div class="bstatus-topbar">
                <div class="bstatus-topbar-left">
                    <span class="bstatus-title-ico"><i class="fa fa-user-group"></i></span>
                    <div class="bstatus-title">Change Inhouse Talent Status</div>
                </div>
                <div class="bstatus-topbar-center">
                    <div class="bstatus-changing">You are about to change the status</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="bstatus-pill cur" id="bstatus_cur_name">—</span>
                        <i class="fa fa-arrow-right bstatus-arrow"></i>
                        <span class="bstatus-pill new" id="bstatus_new_name">Select status</span>
                    </div>
                </div>
                <div class="bstatus-topbar-right">
                    <button type="button" class="bstatus-btn-cancel bstatus-cancel-btn">Cancel</button>
                    <button type="button" class="bstatus-btn-confirm bstatus-confirm-btn" disabled><i class="fa fa-circle-check"></i> Confirm Status Change</button>
                    <span class="bstatus-close" id="bstatus_close_x"><i class="fa fa-xmark"></i></span>
                </div>
            </div>

            <!-- Body (AJAX content) -->
            <div class="bstatus-body flex-grow-1" id="stage_body_content">
                <div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>
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

        // ---- Change Status drawer ----
        // Uses the same drawer mechanism/width as the Job & Relationship Manager drawers
        // on this page ({default:'500px','lg':'1200px'}) — no custom positioning logic.
        $(document).on('click', '.bstatus-cancel-btn', function() { $('#bstatus_close_x').trigger('click'); });

        $(document).on('click', '.stage_sub_stage', function() {
            var id = $(this).data('row-id');
            $('#stage_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
            $('#bstatus_drawer_toggle').trigger('click'); // open instantly, fill in once fetched
            var url = "{{ route('service_company.bench-candidates.bench_application_status', ':id') }}".replace(':id', id);
            $.ajax({
                type: 'GET', url: url,
                success: function(response) {
                    if (response && response.status == "success") {
                        $('#stage_body_content').html(response.view);
                    } else {
                        $('#stage_body_content').html('<div class="text-center py-20 text-danger">Could not load talent status. Please close and try again.</div>');
                    }
                },
                error: function() {
                    $('#stage_body_content').html('<div class="text-center py-20 text-danger">Something went wrong loading this talent. Please close and try again.</div>');
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

        // ============================================================
        // Change Status drawer — one-time global wiring (per drawer instance)
        // Content is re-fetched per talent, but the interaction logic below
        // is defined once and rebound to whatever markup just loaded.
        // ============================================================
        // "Impact of changing status" bullets per target status (6 short items → single row).
        var BS_IMPACT = {
            1: ['Removed from Bench Listing', 'Hidden from Marketplace', 'Visible to internal team', 'Bench counter stops', 'Vendor tracking paused', 'Internal tracking continues'],
            2: ['Listed on Bench for work', 'Visible in Marketplace', 'Searchable by clients', 'Bench counter starts', 'Vendors can submit', 'Internal tracking continues'],
            4: ['Hidden from Marketplace', 'Bench counter paused', 'Not visible to clients', 'Submissions are retained', 'Vendor submissions paused', 'Internal tracking continues'],
            3: ['Removed from Bench Listing', 'Hidden from Marketplace', 'Not visible to Companies', 'Vendor cannot submit', 'Vendor tracking continues', 'Internal tracking continues'],
            5: ['Removed from all Listings', 'Access to opportunities revoked', 'Not visible to clients', 'Historical records retained', 'Vendor tracking stopped', 'Profile archived']
        };

        window.initBenchStatusDrawer = function(cfg) {
            $('#bstatus_cur_name').text(cfg.currentName || '—');
            $('#bstatus_new_name').text('Select status').removeClass('filled');
            $('.bstatus-confirm-btn').prop('disabled', true);
            $('#bs_impact').hide();
            $('#bs_job_card').hide();

            // Select a target status (from a status card) — single source of truth = the hidden radios.
            $(document).off('click.bstatus', '.bs-status-card')
                .on('click.bstatus', '.bs-status-card', function() {
                    var id = String($(this).data('status-id'));
                    var label = $(this).data('status-label');

                    $('input[name="bench_master_status"]').prop('checked', false);
                    $('input[name="bench_master_status"][value="' + id + '"]').prop('checked', true);

                    $('.bs-status-card').removeClass('is-selected');
                    $('.bs-status-card[data-status-id="' + id + '"]').addClass('is-selected');

                    // Header transition pill
                    $('#bstatus_new_name').text(label).addClass('filled');

                    // Confirm button (enabled only when target differs from current)
                    var same = (id === String(cfg.currentId));
                    $('.bstatus-confirm-btn').prop('disabled', same);

                    // Impact strip
                    $('#bs_impact_title').text('Impact of changing status to ' + label);
                    var items = BS_IMPACT[id] || [];
                    var html = '';
                    items.forEach(function(t) {
                        html += '<div class="col-lg-2 col-md-4 col-6 mb-2"><div class="bs-impact-item"><i class="fa fa-circle-check"></i> ' + t + '</div></div>';
                    });
                    $('#bs_impact_list').html(html);
                    $('#bs_impact').show();

                    // Job/Project selection only when target = Placed (id 3)
                    if (id === '3') { $('#bs_job_card').slideDown(150); loadBenchJobListing(cfg.benchId); }
                    else { $('#bs_job_card').slideUp(150); }
                });

            // Confirm
            $(document).off('click.bstatus', '.bstatus-confirm-btn').on('click.bstatus', '.bstatus-confirm-btn', function() {
                var checked = $('input[name="bench_master_status"]:checked').val();
                if (!checked) { return; }
                if (String(checked) === String(cfg.currentId)) {
                    Swal.fire('Warning', 'Same status cannot be updated, please select other option !', 'info');
                    return;
                }
                if (String(checked) === '3' && $('input[name="chk_list_item"]:checked').length === 0) {
                    Swal.fire('Info', 'Please select a job/project to mark this talent as Placed.', 'info');
                    return;
                }
                var $btns = $('.bstatus-confirm-btn');
                var original = $btns.first().html();
                $btns.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating…');
                $.ajax({
                    url: '{{ route('service_company.bench-candidates.status_submit') }}',
                    type: 'POST',
                    data: $('#status_frm').serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            sweetAlert('Success', 'Status changed successfully!', 'success');
                            $('#bstatus_close_x').trigger('click');
                            fetchBenchBenchCandidatesCandidateList();
                        } else {
                            $btns.prop('disabled', false).html(original);
                            if (response.message) { sweetAlert('Fail!', response.message, 'error'); }
                        }
                    },
                    error: function() {
                        $btns.prop('disabled', false).html(original);
                    }
                });
            });
        };

        // ---- Card 2: Select the Job / Project (only for target status = Placed) ----
        function loadBenchJobListing(benchId) {
            $('#bs_job_body').html('<tr><td colspan="9" class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading jobs…</td></tr>');
            $.getJSON('{{ route('service_company.bench-candidates.bench_assign_to_job_listing') }}', { bench_id: benchId, stage: 'all' })
                .done(function(resp) {
                    var rows = (resp && resp.data) ? resp.data : [];
                    if (!rows.length) {
                        $('#bs_job_body').html('<tr><td colspan="9" class="text-center text-muted py-4">No job applications found for this talent.</td></tr>');
                        return;
                    }
                    var html = '';
                    rows.forEach(function(r) {
                        // data-search holds concatenated text for the client-side search box.
                        var searchText = $('<div>').html((r.Requisition || '') + ' ' + (r.ContactPerson || '')).text().toLowerCase();
                        html += '<tr data-search="' + searchText.replace(/"/g, '') + '">' +
                            '<td>' + (r.Requisition || '') + '</td>' +
                            '<td>' + (r.Exp || '') + '</td>' +
                            '<td>' + (r.Location || '') + '</td>' +
                            '<td>' + (r.ContactPerson || '') + '</td>' +
                            '<td>' + (r.offeramount || '') + '</td>' +
                            '<td>' + (r.joindate || '') + '</td>' +
                            '<td>' + (r.Stage || '') + '</td>' +
                            '<td>' + buildBsPlaceAction(r.chkbox || '') + '</td>' +
                            '</tr>';
                    });
                    $('#bs_job_body').html(html);
                    $('#bs_job_search_input').val('');
                })
                .fail(function() {
                    $('#bs_job_body').html('<tr><td colspan="8" class="text-center text-danger py-4">Could not load jobs. Please try again.</td></tr>');
                });
        }

        // Builds the "Select Job" action button from the server-rendered chk_list_item radio,
        // preserving its real name/value/checked/disabled state so submission wiring stays untouched.
        function buildBsPlaceAction(chkboxHtml) {
            var $tmp = $('<div>').html(chkboxHtml);
            var $input = $tmp.find('input[name="chk_list_item"]');
            var value = $input.attr('value') || '';
            var isChecked = $input.is(':checked');
            var isDisabled = $input.is(':disabled');
            var label = isDisabled ? 'Placed' : (isChecked ? 'Selected' : 'Select Job');
            return '<div class="d-none">' + chkboxHtml + '</div>' +
                '<button type="button" class="bs-place-btn' + (isChecked ? ' is-picked' : '') + '" data-job-id="' + value + '" ' + (isDisabled ? 'disabled' : '') + '>' +
                label +
                '</button>';
        }

        $(document).on('click', '.bs-place-btn:not(:disabled)', function() {
            $('#bs_job_body tr').each(function() {
                var $radio = $(this).find('input[name="chk_list_item"]');
                var $btn = $(this).find('.bs-place-btn');
                if ($radio.is(':disabled')) { return; }
                $radio.prop('checked', false);
                $btn.removeClass('is-picked').text('Select Job');
            });
            $(this).closest('tr').find('input[name="chk_list_item"]').prop('checked', true);
            $(this).addClass('is-picked').text('Selected');
        });

        // Client-side search over the job/project table (job, company, hiring manager).
        $(document).on('keyup', '#bs_job_search_input', function() {
            var q = $(this).val().toLowerCase().trim();
            $('#bs_job_body tr').each(function() {
                var hay = $(this).data('search') || '';
                $(this).toggle(q === '' || String(hay).indexOf(q) !== -1);
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

    <!-- ===== Toolbar functionality: Export / filters / saved views ===== -->
    <script>
        // Export the currently loaded talent table to CSV.
        $(document).on('click', '#exportTalents', function() {
            var rows = [];
            var headers = [];
            $('#benchListingTable thead th').each(function(i) {
                if (i === 0) return; // skip checkbox col
                headers.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
            });
            rows.push(headers.join(','));
            $('#benchListingTable tbody tr').each(function() {
                if ($(this).find('td').length < 2) return; // skip "no data" row
                var cols = [];
                $(this).find('td').each(function(i) {
                    if (i === 0) return;
                    var txt = $(this).text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    cols.push('"' + txt + '"');
                });
                rows.push(cols.join(','));
            });
            if (rows.length <= 1) { sweetAlert('Info', 'No records to export.', 'info'); return; }
            var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'inhouse-talents-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
        });

        // Saved views jump to the matching tab.
        $(document).on('click', '.saved-view', function() {
            var v = $(this).data('view');
            $(".application_sttus_nav[rel='" + v + "']").trigger('click');
        });
    </script>
@endpush
