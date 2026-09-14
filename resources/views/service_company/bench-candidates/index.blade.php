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
@endphp

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
    {{-- ApexCharts (not bundled in Metronic global plugins) — drives the aging donut + loss/profit charts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
    <style>
        /* ===== Inhouse Talents — redesigned dashboard (white / Poppins / max 13px body) ===== */
        /* +10% pass applied to every size below; page-title/.num/.money were already at/above the
           project's hard 16px ceiling (docs/UI_UX_STANDARDS.md) so they're clamped at 16px instead
           of growing further. */
        .bench-dash, .bench-dash * { font-family: 'Poppins', sans-serif !important; font-size: 13px; }
        .bench-dash { color: #3f4254; }
        .bench-dash .tiny { font-size: 11px !important; }
        .bench-dash .xtiny { font-size: 10px !important; }
        /* Page title stands out clearly above the compact body text used everywhere else */
        .bench-dash .page-title { font-size: 16px !important; line-height: 1.25; }
        .bench-dash .num { font-size: 16px !important; font-weight: 700; color: #181c32; line-height: 1.1; }
        .bench-dash .money { font-size: 16px !important; font-weight: 700; color: #181c32; }

        /* Card + sub-card system */
        .bench-dash .jw-card { background: #fff; border: 1px solid #eef0f4; border-radius: 10px; }
        .bench-dash .jw-sub { background: #fff; border: 1px solid #f0f1f5; border-radius: 8px; }

        /* KPI cards — icon on the left, label above value/action on one compact row */
        .bench-dash .kpi { padding: 10px 12px; display: flex; align-items: flex-start; gap: 10px; min-height: 78px; cursor: default; transition: box-shadow .15s, border-color .15s; }
        .bench-dash .kpi.kpi-filter { cursor: pointer; }
        .bench-dash .kpi.kpi-filter:hover { border-color: #d5dbe6; box-shadow: 0 3px 10px rgba(24,28,50,.06); }
        .bench-dash .kpi.active { border-color: #3e97ff; box-shadow: 0 0 0 1px #3e97ff inset; }
        .bench-dash .kpi-ico { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bench-dash .kpi-ico i { font-size: 14px !important; }
        .bench-dash .kpi-text { display: flex; flex-direction: column; gap: 6px; min-width: 0; flex: 1 1 auto; }
        .bench-dash .kpi-label { font-size: 11.5px !important; color: #7e8299; font-weight: 500; }
        .bench-dash .kpi-value-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; min-width: 0; }
        .bench-dash .kpi-action { font-size: 10.5px !important; color: #3e97ff; font-weight: 600; white-space: nowrap; }
        .bench-dash .kpi-filter:hover .kpi-action { text-decoration: underline; }
        .bench-dash .kpi-delta { display: none; }
        .bench-dash .up { color: #17c653; } .bench-dash .down { color: #f8285a; }
        .bench-dash .kpi-delta i { font-size: 9px !important; }
        .bench-dash .kpi-vs { color: #a1a5b7; font-weight: 500; }

        /* Ghost buttons (Export) */
        .bench-dash .btn-ghost { border: 1px solid #eef0f4; background: #fff; color: #3f4254; border-radius: 8px; padding: 7px 12px; font-size: 12px !important; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
        .bench-dash .btn-ghost:hover { border-color: #d5dbe6; color: #3e97ff; }
        .bench-dash .btn-ghost i { font-size: 12px !important; }

        /* Sub-cards (avg/longest age, margin, roi) */
        .bench-dash .stat-sub { display: flex; align-items: center; gap: 8px; padding: 8px 10px; }
        .bench-dash .stat-sub .ico { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .bench-dash .stat-sub .ico i { font-size: 12px !important; }
        .bench-dash .stat-sub .lbl { font-size: 10.5px !important; color: #7e8299; }
        .bench-dash .stat-sub .val { font-size: 14px !important; font-weight: 700; color: #181c32; }

        /* Chart header dropdown */
        .bench-dash .chart-range { border: 1px solid #eef0f4; background: #fff; border-radius: 6px; font-size: 10.5px !important; padding: 3px 8px; color: #7e8299; height: 24px; }

        /* Soft color chips */
        .sc-primary { background: #eef3ff; color: #3e97ff; }
        .sc-success { background: #e8fff3; color: #17c653; }
        .sc-warning { background: #fff4de; color: #f6a609; }
        .sc-danger  { background: #ffeef1; color: #f8285a; }
        .sc-info    { background: #eef3ff; color: #7239ea; }
        .sc-dark    { background: #f1f2f6; color: #3f4254; }

        /* Charts */
        .bench-dash .chart-title { font-size: 13px !important; font-weight: 600; color: #181c32; }
        .bench-dash .aging-legend li { display: flex; align-items: center; justify-content: space-between; padding: 3px 0; }
        .bench-dash .dot { width: 8px; height: 8px; border-radius: 2px; display: inline-block; margin-right: 6px; }

        /* Filter / tabs strip */
        .bench-dash .tab-strip { display: flex; align-items: stretch; flex-wrap: nowrap; width: 100%; overflow-x: auto; }
        .bench-dash .tab-pill { flex: 1 1 0; min-width: 118px; border: 0; border-bottom: 2px solid transparent; background: #fff; border-radius: 0; padding: 11px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; color: #637083; }
        .bench-dash .tab-pill .tab-ico { color: #7e8aa0; font-size: 12px !important; }
        .bench-dash .tab-pill .lbl { color: #4b5675; font-weight: 500; white-space: nowrap; }
        .bench-dash .tab-pill .cnt { background: #f1f2f6; color: #3f4254; border-radius: 999px; padding: 1px 6px; font-size: 10.5px !important; font-weight: 700; line-height: 1.35; }
        .bench-dash .tab-pill:not(.active):hover { background: #fafbfc; }
        .bench-dash .tab-pill.active { background: #fff; }
        .bench-dash .tab-pill.active .lbl, .bench-dash .tab-pill.active .tab-ico { color: var(--tab-color); font-weight: 600; }
        .bench-dash .tab-pill.active { border-bottom-color: var(--tab-color); }
        .bench-dash .tab-pill.active .cnt { background: var(--tab-bg); color: var(--tab-color); }
        .bench-dash .tab-all { --tab-color: #3e97ff; --tab-bg: #eef3ff; }
        .bench-dash .tab-pool { --tab-color: #17c653; --tab-bg: #e8fff3; }
        .bench-dash .tab-bench { --tab-color: #f6a609; --tab-bg: #fff4de; }
        .bench-dash .tab-hold { --tab-color: #7239ea; --tab-bg: #f3effe; }
        .bench-dash .tab-placed { --tab-color: #3e97ff; --tab-bg: #eef3ff; }
        .bench-dash .tab-exited { --tab-color: #7e8299; --tab-bg: #f1f2f6; }

        /* Table */
        .bench-dash .table-responsive { overflow-x: hidden; }
        .bench-dash #benchListingTable { width: 100% !important; min-width: 0; margin: 0; table-layout: fixed; }
        .bench-dash #benchListingTable .col-select { width: 3%; }
        .bench-dash #benchListingTable .col-talent-name { width: 21.5%; }
        .bench-dash #benchListingTable .col-experience { width: 8.5%; }
        .bench-dash #benchListingTable .col-availability { width: 9%; }
        .bench-dash #benchListingTable .col-location { width: 9.5%; }
        .bench-dash #benchListingTable .col-salary { width: 10.5%; }
        .bench-dash #benchListingTable .col-status { width: 8.5%; }
        .bench-dash #benchListingTable .col-manager-name { width: 11%; }
        .bench-dash #benchListingTable .col-pipeline-activity { width: 18.5%; }
        .bench-dash #benchListingTable thead th { font-size: 10px !important; text-transform: uppercase; letter-spacing: .2px; color: #a1a5b7; font-weight: 600; border-bottom: 1px solid #eef0f4; padding: 7px 6px !important; white-space: normal; line-height: 1.2; background: #fff; }
        .bench-dash #benchListingTable tbody td { font-size: 12px !important; border-bottom: 1px solid #f4f5f8; padding: 7px 6px !important; vertical-align: middle; min-width: 0; overflow: hidden; }
        .bench-dash #benchListingTable tbody tr:hover td { background: #fafbfc; }
        .bench-dash #benchListingTable a { font-size: 12px !important; }
        .bench-dash #benchListingTable .fs-6 { font-size: 12px !important; }
        .bench-dash #benchListingTable .fs-7 { font-size: 11px !important; }
        .bench-dash #benchListingTable .fs-3 { font-size: 13px !important; }
        .bench-dash #benchListingTable .symbol-40px { width: 32px !important; height: 32px !important; }
        .bench-dash #benchListingTable .symbol-label { line-height: 1; }
        .bench-dash .badge { font-size: 10px !important; padding: 2px 5px; }
        .bench-dash .talent-name-main,
        .bench-dash .talent-name-role,
        .bench-dash .manager-name-main {
            display: -webkit-box !important;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.25;
        }
        .bench-dash .talent-name-main,
        .bench-dash .talent-name-role,
        .bench-dash .manager-name-main { max-width: 100%; }

        /* Clients Pipeline Activity mini-grid — bold throughout */
        .bench-dash .pipe { display: flex; align-items: flex-start; gap: 0; width: 100%; min-width: 0; overflow: hidden; }
        .bench-dash .pipe > div { width: 100%; min-width: 0; }
        .bench-dash .pipe-grid { display: grid; grid-template-columns: repeat(7, minmax(15px, 1fr)); text-align: center; gap: 0; width: 100%; min-width: 0; }
        .bench-dash .pipe-grid .h { font-size: 8.5px !important; color: #a1a5b7; font-weight: 700; overflow: hidden; }
        .bench-dash .pipe-grid .v { font-size: 10px !important; font-weight: 700; color: #181c32; padding-top: 1px; overflow: hidden; }
        .bench-dash .pipe-grid a { font-size: 10px !important; font-weight: 700; }
        .bench-dash .pipe-foot { font-size: 9.5px !important; font-weight: 700; color: #3f4254; margin-top: 3px; display: flex; align-items: center; gap: 4px 7px; flex-wrap: wrap; line-height: 1.15; white-space: normal; overflow: hidden; }
        .bench-dash .pipe-stat { display: inline-flex; align-items: center; color: #7e8299; font-size: 9.5px !important; font-weight: 600; line-height: 1.15; flex-shrink: 0; }

        /* Manager "Change" — plain text link, no button chrome */
        .bench-dash .manager-change { color: #3e97ff; font-size: 10.5px !important; font-weight: 600; cursor: pointer; width: fit-content; }
        .bench-dash .manager-change:hover { text-decoration: underline; color: #3e97ff; }

        /* Pool status — same purple used for Pool in the Change Status drawer */
        .bench-dash .text-Apool { color: #7239ea !important; }

        /* Let long text (e.g. "Bangalore, India") wrap onto a 2nd line instead of being hard-truncated;
           clamps at 2 lines with an ellipsis so an unusually long value still can't blow out row height. */
        .bench-dash .wrap-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; white-space: normal !important; word-break: break-word; line-height: 1.3; }

        /* toolbar search */
        .bench-dash .dash-search { border: 1px solid #eef0f4; border-radius: 8px; padding: 7px 12px; font-size: 12px !important; height: 34px; }
        .bench-dash .form-select-solid, .bench-dash .form-select { font-size: 12px !important; height: 34px; padding: 5px 10px; border: 1px solid #eef0f4 !important; background: #fff !important; }
        .bench-dash .btn-add { background: #3e97ff; color: #fff; border-radius: 8px; padding: 7px 14px; font-size: 12px !important; font-weight: 600; display: inline-flex; align-items: center; gap: 7px; }
        .bench-dash .btn-add:hover { color: #fff; background: #2f86ec; }

        .loadingMessagebenchCandidateBenchCandidatesAjax { font-size: 12px !important; }

        /* Job Tracker style meta strip: date first, then compact skill chips on the same row */
        .bench-dash #benchListingTable tbody td.col-talent-name { position: relative; padding-bottom: 24px !important; }
        .bench-dash #benchListingTable .job-meta-strip { position: absolute; bottom: 4px; left: 6px; right: 6px; overflow: hidden; }
        .bench-dash #benchListingTable .job-meta-line { display: flex; align-items: center; gap: 4px; white-space: nowrap; overflow: hidden; }
        .bench-dash #benchListingTable .job-date-tag,
        .bench-dash #benchListingTable .skill-tag {
            display: inline-flex;
            align-items: center;
            min-height: 17px;
            border-radius: 5px;
            padding: 1px 5px;
            font-size: 9.5px !important;
            line-height: 1.25;
            font-weight: 500;
            background: #f1f3f8;
            color: #5e6278;
            flex-shrink: 0;
        }
        .bench-dash #benchListingTable .job-date-tag { color: #181c32; font-weight: 700; }
        .bench-dash #benchListingTable .job-date-tag svg { width: 11px; height: 11px; margin-right: 3px; color: #3f4254; flex-shrink: 0; }
        .bench-dash #benchListingTable .skill-tag { color: #7e8299; font-weight: 500; }

        /* Fit smaller / 15.6" laptops */
        @media only screen and (max-width: 1440px) {
            .bench-dash .num { font-size: 16px !important; }
            .bench-dash .money { font-size: 14px !important; }
            .bench-dash #benchListingTable tbody td, .bench-dash #benchListingTable a { font-size: 11px !important; }
            .bench-dash #benchListingTable thead th { font-size: 9.5px !important; padding-left: 5px !important; padding-right: 5px !important; }
            .bench-dash #benchListingTable tbody td { padding-left: 5px !important; padding-right: 5px !important; }
            .bench-dash #benchListingTable .symbol-40px { width: 30px !important; height: 30px !important; }
            .bench-dash .pipe-grid .h { font-size: 8px !important; }
            .bench-dash .pipe-grid .v,
            .bench-dash .pipe-grid a { font-size: 9.5px !important; }
        }
        #kt_activitiesHMB { z-index: 999999; }

        /* ============================================================
           Change Talent Status drawer — enterprise redesign
           Positioning/overlay/width owned by the drawer plugin (same
           width config as the Job drawer). Below is content styling only.
           ============================================================ */
        #bstatus_drawer, #bstatus_drawer * { font-family: 'Poppins', sans-serif !important; box-sizing: border-box; }
        #bstatus_drawer { z-index: 999999; font-size: 10.5px; } /* base: -20% then +10% from original 12px */
        #bstatus_drawer .min-w-0 { min-width: 0; }

        /* ---- Header ---- */
        .bstatus-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 22px; background: #fff; border-bottom: 1px solid #eef0f4; flex-shrink: 0; }
        .bstatus-topbar-left { display: flex; align-items: center; gap: 12px; }
        .bstatus-title-ico { width: 40px; height: 40px; border-radius: 10px; background: #eef3ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 15px; }
        .bstatus-title { font-size: 16.5px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bstatus-topbar-center { display: flex; flex-direction: column; align-items: center; }
        .bstatus-changing { font-size: 10.5px; color: #7e8299; }
        .bstatus-pill { border-radius: 8px; padding: 6px 18px; font-size: 12px; font-weight: 700; }
        .bstatus-pill.cur { background: #eef3ff; color: #3e97ff; border: 1px solid #dce8ff; }
        .bstatus-pill.new { background: #f6f7f9; color: #a1a5b7; border: 1px solid #e4e6ef; }
        .bstatus-pill.new.filled { background: #e8fff3; color: #17c653; border-color: #c8f4dc; }
        .bstatus-arrow { color: #b5b9c8; font-size: 12px; }
        .bstatus-topbar-right { display: flex; align-items: center; gap: 10px; }

        .bstatus-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 11.5px; }
        .bstatus-btn-cancel:hover { border-color: #d5dbe6; background: #f9fafc; }
        .bstatus-btn-confirm { border: none; background: #17c653; color: #fff; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 11.5px; display: inline-flex; align-items: center; gap: 8px; }
        .bstatus-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
        .bstatus-btn-confirm:not(:disabled):hover { background: #14b04a; }
        .bstatus-close { width: 36px; height: 36px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 14px; }
        .bstatus-close:hover { background: #f4f5f8; color: #181c32; }

        /* ---- Body ---- */
        .bstatus-body { overflow-y: auto; background: #f5f6fa; padding: 16px 22px; flex-grow: 1; }
        .bs-card { background: #fff; border: 1px solid #eef0f4; border-radius: 14px; padding: 18px 22px; }
        .bs-card + .bs-card { margin-top: 14px; }

        /* ---- Candidate summary bar ---- */
        .bs-cand-bar { display: flex; align-items: center; gap: 0; }
        .bs-cand-id { display: flex; align-items: center; gap: 14px; padding-right: 26px; }
        .bs-cand-avatar { width: 56px; height: 56px; border-radius: 50%; background: #efeafe; color: #7239ea; display: flex; align-items: center; justify-content: center; font-size: 16.5px; font-weight: 700; flex-shrink: 0; }
        .bs-cand-name { font-size: 16.5px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bs-cand-role { font-size: 11.5px; color: #7e8299; }
        .bs-cand-stat { flex: 1 1 0; min-width: 0; text-align: center; padding: 0 12px; border-left: 1px solid #eef0f4; }
        .bs-cand-stat .v { font-size: 17.5px; font-weight: 700; color: #181c32; line-height: 1.1; }
        .bs-cand-stat .l { font-size: 10px; color: #7e8299; margin-top: 2px; }

        /* ---- Numbered section headers ---- */
        .bs-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .bs-sec-num { width: 26px; height: 26px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bs-sec-title { font-size: 14px; font-weight: 700; color: #181c32; }

        /* ---- Status cards row (with arrows) ---- */
        .bs-status-row { display: flex; align-items: stretch; gap: 0; }
        .bs-status-cell { flex: 1 1 0; min-width: 0; }
        .bs-status-arrow { display: flex; align-items: center; justify-content: center; color: #c2c9d6; font-size: 12px; padding: 0 8px; flex-shrink: 0; }
        .bs-status-card { border: 1.5px solid #eef0f4; border-radius: 14px; padding: 18px; cursor: pointer; background: #fff; height: 100%; transition: border-color .15s, background .15s, box-shadow .15s; }
        .bs-status-card:hover { border-color: #cdd4e0; }
        .bs-status-card-top { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .bs-status-card .ic { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .bs-status-card .nm { font-size: 14px; font-weight: 700; }
        .bs-status-card .sb { font-size: 10.5px; color: #7e8299; line-height: 1.4; }
        .bs-status-card.is-current { border-color: #3e97ff; background: #f5f9ff; }
        .bs-status-card.is-selected { border-color: #17c653; background: #f2fdf7; box-shadow: 0 0 0 1px #17c653 inset; }

        /* ---- Impact strip ---- */
        .bs-impact { background: #f2fdf7; border: 1px solid #d5f2e2; border-radius: 12px; padding: 16px 18px; margin-top: 16px; }
        .bs-impact-head { display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #17845a; margin-bottom: 12px; }
        .bs-impact-item { display: flex; align-items: flex-start; gap: 8px; font-size: 11px; color: #3f4254; font-weight: 500; }
        .bs-impact-item i { color: #17c653; font-size: 11.5px; margin-top: 1px; flex-shrink: 0; }

        /* ---- Job/Project section ---- */
        .bs-job-table { border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; }
        .bs-job-table table { width: 100%; margin: 0; table-layout: fixed; }
        .bs-job-table thead th { font-size: 10px; color: #a1a5b7; font-weight: 600; padding: 12px 10px; background: #f8f9fb; border-bottom: 1px solid #eef0f4; white-space: nowrap; text-align: left; }
        .bs-job-table td { font-size: 11.5px; padding: 14px 10px; border-bottom: 1px solid #f4f5f8; vertical-align: middle; }
        /* Fixed proportional widths, sized so the Action button always has room to sit fully inside the card */
        .bs-job-table th:nth-child(1), .bs-job-table td:nth-child(1) { width: 21%; }
        .bs-job-table th:nth-child(2), .bs-job-table td:nth-child(2) { width: 20%; }
        .bs-job-table th:nth-child(3), .bs-job-table td:nth-child(3) { width: 15%; }
        .bs-job-table th:nth-child(4), .bs-job-table td:nth-child(4) { width: 13%; }
        .bs-job-table th:nth-child(5), .bs-job-table td:nth-child(5) { width: 11%; }
        .bs-job-table th:nth-child(6), .bs-job-table td:nth-child(6) { width: 8%; }
        .bs-job-table th:nth-child(7), .bs-job-table td:nth-child(7) { width: 12%; }
        .bs-job-table tbody tr:last-child td { border-bottom: none; }
        .bs-job-table tbody tr:hover td { background: #fafbfc; }
        /* Normalise the server-rendered cell typography to the design's hierarchy */
        .bs-job-table .fs-6, .bs-job-table .fw-bolder { font-size: 11.5px !important; font-weight: 700 !important; color: #181c32 !important; }
        .bs-job-table .fs-7 { font-size: 10px !important; color: #7e8299 !important; font-weight: 500 !important; }
        .bs-job-table .badge { font-size: 10px !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 7px !important; }
        /* Job / Company — logo beside the title/company text, never stacked */
        .bs-job-cell { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .bs-job-cell .symbol { margin: 0 !important; flex-shrink: 0; }
        .bs-job-cell .d-flex.flex-column { min-width: 0; }
        /* Experience column redesign — experience range / budget range / client type, each its own row */
        .bs-exp-row { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #181c32; }
        .bs-exp-row + .bs-exp-row { margin-top: 4px; }
        .bs-exp-row i { width: 12px; text-align: center; color: #a1a5b7; font-size: 10px; flex-shrink: 0; }
        .bs-exp-row.client { color: #3e97ff; }
        .bs-exp-row.client .ref { color: #a1a5b7; font-weight: 500; }
        /* Snug padding so the button always fits fully inside its column (and the card) without clipping */
        .bs-place-btn { border: 1px solid #cdd9f7; background: #fff; color: #3e97ff; border-radius: 8px; padding: 7px 10px; font-size: 10.5px; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; width: 100%; justify-content: center; }
        .bs-place-btn:hover { background: #eef5ff; }
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
    <div class="bench-dash content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -60px;">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-5">

                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2 py-3" style="background:#fff;padding: 6px;">
                    <div class="d-flex flex-column">
                        <h5 class="page-title text-dark fw-bolder my-0 d-flex align-items-center gap-2">
                            Inhouse Talents
                            <i class="fa fa-circle-info text-muted" style="font-size:13px !important;" data-toggle="tooltip"
                                title="Track bench, pipeline and revenue in one place."></i>
                        </h5>
                        <span class="tiny text-muted">Track bench, pipeline and revenue in one place.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="hidden" id="inputSearch" name="inputSearch" />
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
                                ['rel' => 'all', 'label' => 'Total Talents', 'val' => (int) ($counts->all_records ?? 0), 'ico' => 'fa-users', 'sc' => 'sc-primary', 'spark' => 'sp1', 'color' => '#3e97ff'],
                                ['rel' => '1',   'label' => 'Talent On Pool', 'val' => (int) ($counts->pool ?? 0), 'ico' => 'fa-user-friends', 'sc' => 'sc-success', 'spark' => 'sp2', 'color' => '#17c653'],
                                ['rel' => '2',   'label' => 'Talents on Bench', 'val' => (int) ($counts->bench ?? 0), 'ico' => 'fa-couch', 'sc' => 'sc-warning', 'spark' => 'sp3', 'color' => '#f6a609'],
                                ['rel' => '4',   'label' => 'Talent On Hold', 'val' => (int) ($counts->paused ?? 0), 'ico' => 'fa-pause-circle', 'sc' => 'sc-info', 'spark' => 'sp4', 'color' => '#7239ea'],
                                ['rel' => '3',   'label' => 'Talent Placed', 'val' => (int) ($counts->placed ?? 0), 'ico' => 'fa-calendar-check', 'sc' => 'sc-primary', 'spark' => 'sp5', 'color' => '#3e97ff'],
                            ];
                        @endphp
                        @foreach ($kpis as $k)
                            <div class="col">
                                <div class="jw-card kpi kpi-filter" data-rel="{{ $k['rel'] }}" id="kpi_{{ $k['rel'] }}">
                                    <span class="kpi-ico {{ $k['sc'] }}"><i class="fa {{ $k['ico'] }}"></i></span>
                                    <div class="kpi-text">
                                        <span class="kpi-label">{{ $k['label'] }}</span>
                                        <span class="kpi-value-row">
                                            <span class="num" id="kpival_{{ $k['rel'] }}">{{ $k['val'] }}</span>
                                            <span class="kpi-action">View Candidate</span>
                                        </span>
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search + Sort row -->
                    <div class="jw-card p-2 mb-2 d-flex align-items-center gap-2">
                        <div class="position-relative flex-grow-1">
                            <i class="fa fa-search position-absolute text-muted" style="left:12px; top:11px; font-size:12px !important;"></i>
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
                    <div class="jw-card mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="overflow:hidden;">
                        <div class="tab-strip">
                            <a href="Javascript:void(0);" rel="all" for="all_talents" class="tab-pill tab-all application_sttus_nav active" id="tab_all">
                                <i class="fa fa-users tab-ico"></i><span class="lbl">All Talents</span><span class="cnt" id="bg_all">{{ $counts->all_records ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="1" for="talent_Pool" class="tab-pill tab-pool application_sttus_nav">
                                <i class="fa fa-user-group tab-ico"></i><span class="lbl">On Pool</span><span class="cnt" id="bg_1">{{ $counts->pool ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="2" for="bench_live" class="tab-pill tab-bench application_sttus_nav">
                                <i class="fa fa-briefcase tab-ico"></i><span class="lbl">Bench</span><span class="cnt" id="bg_2">{{ $counts->bench ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="4" for="bench_pause" class="tab-pill tab-hold application_sttus_nav">
                                <i class="fa fa-circle-pause tab-ico"></i><span class="lbl">On Hold</span><span class="cnt" id="bg_4">{{ $counts->paused ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="3" for="bench_placed" class="tab-pill tab-placed application_sttus_nav">
                                <i class="fa fa-calendar-check tab-ico"></i><span class="lbl">Placed</span><span class="cnt" id="bg_3">{{ $counts->placed ?? 0 }}</span>
                            </a>
                            <a href="Javascript:void(0);" rel="5" for="bench_left" class="tab-pill tab-exited application_sttus_nav">
                                <i class="fa fa-right-from-bracket tab-ico"></i><span class="lbl">Exited</span><span class="cnt" id="bg_5">{{ $counts->exited ?? 0 }}</span>
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
                                        <th class="col-select pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-2">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </div>
                                        </th>
                                        <th class="col-talent-name">Talent Name</th>
                                        <th class="col-experience">Experience</th>
                                        <th class="col-availability">Availability</th>
                                        <th class="col-location">Location</th>
                                        <th class="col-salary">Expected Salary</th>
                                        <th class="col-status">Bench Status</th>
                                        <th class="col-manager-name">Relationship Manager</th>
                                        <th class="col-pipeline-activity">Clients Pipeline Activity</th>
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

    <!-- Change Talent Status drawer — same drawer plugin as the Job drawer, but narrower so a
         column of the Inhouse Talents table stays visible behind it (feels like a slide-in panel,
         not a near-fullscreen takeover). -->
    <div id="bstatus_drawer_toggle" style="display:none;"></div>
    <div id="bstatus_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="bench-status"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'550px', 'lg': '1050px'}" data-kt-drawer-direction="end"
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
        data-kt-drawer-width="{default:'550px', 'lg': '1050px'}" data-kt-drawer-direction="end"
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
        // "Impact of changing status" bullets per target status.
        var BS_IMPACT = {
            1: ['Moved to internal talent pool', 'Not visible in marketplace searches', 'Only visible to your company', 'Bench counter stops', 'Profile remains on record'],
            2: ['Candidate becomes available for new opportunities', 'Appears in Bench Dashboard & Marketplace', 'Bench counter starts from today', 'Profile remains active & searchable'],
            4: ['Temporarily marked unavailable', 'Hidden from marketplace searches', 'Bench counter is paused', 'Existing submissions are retained', 'Profile remains on record'],
            3: ['Marked as assigned to a Job/Project', 'Removed from Bench listing & marketplace', 'Not visible to other companies', 'Work-in-progress tracking begins'],
            5: ['Marked as no longer with company', 'Removed from all active listings', 'Access to opportunities revoked', 'Historical records are retained', 'Profile archived for compliance']
        };

        // Same semantic colours as the "Choose New Status" cards — reused for the "New Status"
        // pill and the Impact strip so the whole flow reads as one coherent colour per status.
        var BS_STATUS_COLORS = {
            1: { color: '#7239ea', bg: '#f3effe', border: '#e3d7fb' },
            2: { color: '#3e97ff', bg: '#eef3ff', border: '#dce8ff' },
            4: { color: '#f6a609', bg: '#fff4de', border: '#ffe3ad' },
            3: { color: '#17c653', bg: '#e8fff3', border: '#c8f4dc' },
            5: { color: '#f8285a', bg: '#ffeef1', border: '#ffd3da' }
        };

        // Single source of truth for the confirm button's resting label — reused whenever it needs
        // to be reset (drawer open, status re-selected, submit failed) so it never gets stuck on
        // "Updating…" from a previous selection or a request that was already in flight.
        var BSTATUS_CONFIRM_DEFAULT_HTML = '<i class="fa fa-circle-check"></i> Confirm Status Change';

        window.initBenchStatusDrawer = function(cfg) {
            $('#bstatus_cur_name').text(cfg.currentName || '—');
            $('#bstatus_new_name').text('Select status').removeClass('filled').css({ background: '', color: '', borderColor: '' });
            $('.bstatus-confirm-btn').prop('disabled', true).html(BSTATUS_CONFIRM_DEFAULT_HTML);
            $('#bs_impact').hide();
            $('#bs_job_card').hide();

            // Select a target status (from a status card) — single source of truth = the hidden radios.
            $(document).off('click.bstatus', '.bs-status-card')
                .on('click.bstatus', '.bs-status-card', function() {
                    var id = String($(this).data('status-id'));
                    var label = $(this).data('status-label');
                    var c = BS_STATUS_COLORS[id] || BS_STATUS_COLORS[2];

                    $('input[name="bench_master_status"]').prop('checked', false);
                    $('input[name="bench_master_status"][value="' + id + '"]').prop('checked', true);

                    $('.bs-status-card').removeClass('is-selected');
                    $('.bs-status-card[data-status-id="' + id + '"]').addClass('is-selected');

                    // Header transition pill — tinted to match the chosen status
                    $('#bstatus_new_name').text(label).addClass('filled')
                        .css({ background: c.bg, color: c.color, borderColor: c.border });

                    // Confirm button — always reset to its resting label when a status is (re-)picked,
                    // then enabled only when the target differs from the current status.
                    var same = (id === String(cfg.currentId));
                    $('.bstatus-confirm-btn').html(BSTATUS_CONFIRM_DEFAULT_HTML).prop('disabled', same);

                    // Impact strip — same colour treatment
                    $('#bs_impact_title').text('Impact of changing status to ' + label);
                    $('#bs_impact').css({ background: c.bg, borderColor: c.border });
                    $('#bs_impact_head').css('color', c.color);
                    var items = BS_IMPACT[id] || [];
                    var html = '';
                    items.forEach(function(t) {
                        // Fixed 3-per-row (33%) so both 4-item and 5-item lists wrap into exactly
                        // 2 rows, giving longer bullets (e.g. "Removed from Bench listing &
                        // marketplace") enough width to read cleanly instead of cramming a line.
                        html += '<div class="col-lg-4 col-md-6 col-12 mb-2"><div class="bs-impact-item"><i class="fa fa-circle-check" style="color:' + c.color + ';"></i> ' + t + '</div></div>';
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
                            $btns.prop('disabled', false).html(BSTATUS_CONFIRM_DEFAULT_HTML);
                            if (response.message) { sweetAlert('Fail!', response.message, 'error'); }
                        }
                    },
                    error: function() {
                        $btns.prop('disabled', false).html(BSTATUS_CONFIRM_DEFAULT_HTML);
                    }
                });
            });
        };

        // ---- Card 2: Shortlisted on the Job (only for target status = Placed) ----

        // Reads the bold/muted two-line cells the server already renders (fs-6/fs-7 spans)
        // and returns their plain text, so we can recombine them into the new layouts below.
        function bsTwoLineText(html) {
            var spans = $('<div>').html(html || '').find('span');
            return { bold: $(spans.get(0)).text().trim(), muted: $(spans.get(1)).text().trim() };
        }

        function loadBenchJobListing(benchId) {
            $('#bs_job_body').html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading jobs…</td></tr>');
            $.getJSON('{{ route('service_company.bench-candidates.bench_assign_to_job_listing') }}', { bench_id: benchId, stage: 'all' })
                .done(function(resp) {
                    var rows = (resp && resp.data) ? resp.data : [];
                    if (!rows.length) {
                        $('#bs_job_body').html('<tr><td colspan="7" class="text-center text-muted py-4">No job applications found for this talent.</td></tr>');
                        return;
                    }
                    var html = '';
                    rows.forEach(function(r) {
                        // Job / Company — the server marks the logo up with an old float:left layout,
                        // which can end up stacking (logo above title) once the column narrows. Rebuild
                        // it as an explicit flex row so the logo always sits beside the title/company text.
                        var $req = $('<div>').html(r.Requisition || '');
                        var $reqSymbol = $req.find('.symbol').first().removeAttr('style').css('float', 'none');
                        var $reqText = $req.find('.d-flex.flex-column').first();
                        var jobHtml = '<div class="bs-job-cell">' + $reqSymbol.prop('outerHTML') + $reqText.prop('outerHTML') + '</div>';

                        // The exact job id used for the "Select Job" radio — the single reliable
                        // source of truth, reused below as the JID reference (same one Job pages show).
                        var jobId = $('<div>').html(r.chkbox || '').find('input[name="chk_list_item"]').attr('value') || '';

                        // Experience column redesigned into 3 rows: experience range, budget range (in K), client type + JID.
                        var exp = bsTwoLineText(r.Exp);       // bold = max_exp+'+', muted = min_exp+'+'
                        var expRange = (exp.muted || '0').replace('+', '') + ' - ' + (exp.bold || '0').replace('+', '') + ' Years';

                        var sal = bsTwoLineText(r.Salary);    // bold = top of range, muted = bottom of range
                        var minK = toKFormat(sal.muted);
                        var maxK = toKFormat(sal.bold);
                        var budgetRange = (minK && maxK) ? (minK + ' - ' + maxK) : '—';

                        var status = bsTwoLineText(r.Status); // muted contains "Internal"/"Client" somewhere in its text
                        var isInternal = (status.muted || '').toLowerCase().indexOf('internal') !== -1;
                        var clientLabel = isInternal ? 'Internal Client' : 'Direct Client';

                        var expCellHtml =
                            '<div class="bs-exp-row"><i class="fa fa-clock"></i> ' + expRange + '</div>' +
                            '<div class="bs-exp-row"><i class="fa fa-wallet"></i> ' + budgetRange + '</div>' +
                            '<div class="bs-exp-row client"><i class="fa fa-building"></i> ' + clientLabel + (jobId ? ' <span class="ref">&middot; JID-' + jobId + '</span>' : '') + '</div>';

                        // Offer Amount — strip the redundant ".00" from the headline figure.
                        var offerHtml = (r.offeramount || '').replace(/\.00(?=[^\d]|$)/g, '');

                        // Stage — force Joined = green, Offered = orange, regardless of the server's own CSS class.
                        var $stage = $('<div>').html(r.Stage || '');
                        var $badge = $stage.find('.badge').first();
                        var stageTxt = $badge.text().trim().toLowerCase();
                        if (stageTxt.indexOf('join') !== -1) {
                            $badge.css({ background: '#e8fff3', color: '#17c653' });
                        } else if (stageTxt.indexOf('offer') !== -1) {
                            $badge.css({ background: '#fff4de', color: '#f6a609' });
                        }

                        html += '<tr>' +
                            '<td>' + jobHtml + '</td>' +
                            '<td>' + expCellHtml + '</td>' +
                            '<td>' + (r.ContactPerson || '') + '</td>' +
                            '<td>' + offerHtml + '</td>' +
                            '<td>' + (r.joindate || '') + '</td>' +
                            '<td>' + $stage.html() + '</td>' +
                            '<td>' + buildBsPlaceAction(r.chkbox || '') + '</td>' +
                            '</tr>';
                    });
                    $('#bs_job_body').html(html);
                })
                .fail(function() {
                    $('#bs_job_body').html('<tr><td colspan="7" class="text-center text-danger py-4">Could not load jobs. Please try again.</td></tr>');
                });
        }

        // Converts a raw amount string (with currency/commas/mode suffix stripped) into "NNNK" notation.
        function toKFormat(text) {
            var num = parseFloat((text || '').replace(/[^0-9.]/g, ''));
            if (!num || isNaN(num)) { return ''; }
            return Math.round(num / 1000) + 'K';
        }

        // Builds the "Select Job" action button from the server-rendered chk_list_item radio.
        // IMPORTANT: force the radio UNCHECKED so no job is pre-selected — the user must pick one.
        // "Placed" (grey, disabled) only appears when this exact bench+job pair is a REAL existing
        // placement record; everything else starts as a plain toggleable "Select Job".
        function buildBsPlaceAction(chkboxHtml) {
            var $tmp = $('<div>').html(chkboxHtml);
            var $input = $tmp.find('input[name="chk_list_item"]');
            var value = $input.attr('value') || '';
            var isDisabled = $input.is(':disabled');
            $input.prop('checked', false).removeAttr('checked'); // no default selection
            var cleanRadio = $tmp.html();
            var label = isDisabled ? 'Placed' : 'Select Job';
            return '<div class="d-none">' + cleanRadio + '</div>' +
                '<button type="button" class="bs-place-btn" data-job-id="' + value + '" ' + (isDisabled ? 'disabled' : '') + '>' +
                label +
                '</button>';
        }

        // Only ONE job selectable at a time. Clicking the already-selected job again undoes it.
        $(document).on('click', '.bs-place-btn:not(:disabled)', function() {
            var $btn = $(this);
            var $row = $btn.closest('tr');
            var alreadyPicked = $btn.hasClass('is-picked');

            $('#bs_job_body tr').each(function() {
                var $radio = $(this).find('input[name="chk_list_item"]');
                var $b = $(this).find('.bs-place-btn');
                if ($radio.is(':disabled')) { return; }
                $radio.prop('checked', false);
                $b.removeClass('is-picked').text('Select Job');
            });

            if (!alreadyPicked) {
                $row.find('input[name="chk_list_item"]').prop('checked', true);
                $btn.addClass('is-picked').html('<i class="fa fa-check"></i> Selected');
            }
            // If it WAS already picked, the loop above already cleared it back to "Select Job" (undo).
        });

        // ---- Table fetch + pagination ----
        $(document).ready(function() {
            fetchBenchBenchCandidatesCandidateList();
            // Re-init tooltips after each AJAX load so newly inserted chips get tooltips
            $(document).on('ajaxComplete', function() {
                $('[title]').tooltip({ trigger: 'hover', placement: 'top' });
            });
            $(document).on('click', '#paginationbenchCandidateBenchCandidates .pagi-page, #paginationbenchCandidateBenchCandidates .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
                $(this).addClass('disabled');
                fetchBenchBenchCandidatesCandidateList(page, $('#search').val()).then(() => {
                    $('#paginationbenchCandidateBenchCandidates .pagi-page, #paginationbenchCandidateBenchCandidates .pagi-arrow').removeClass('disabled');
                }).catch(() => {
                    $('#paginationbenchCandidateBenchCandidates .pagi-page, #paginationbenchCandidateBenchCandidates .pagi-arrow').removeClass('disabled');
                });
            });

            $(document).on('change', '#paginationbenchCandidateBenchCandidates .pagi-perpage', function(e) {
                const selectedPerPage = $(this).val();
                if (selectedPerPage) {
                    localStorage.setItem('candidatePerPage', selectedPerPage);
                    fetchBenchBenchCandidatesCandidateList(1, $('#search').val());
                }
            });
        });

        function fetchBenchBenchCandidatesCandidateList(page = 1, search = '') {
            $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'block' });
            var perPage = localStorage.getItem('candidatePerPage') || 10;
            return $.ajax({
                url: `{!! route('service_company.bench-candidates.data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: $('#inputSearch').val(),
                    cus_search: $('#kt_tagify_2').val(),
                    filter_status: $('#filter_status').val(),
                    country: $('#country').val(),
                    sortingfilter: $('#sortingfilter').val(),
                    selectedValue: $("input[name='status_manage']:checked").val(),
                    per_page: perPage,
                    size: perPage
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

    <!-- ===== Toolbar functionality: Export / filters ===== -->
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
    </script>
@endpush
