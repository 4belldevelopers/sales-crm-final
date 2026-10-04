@extends('layouts.service_company_layout')
@php
    $heroItems = [
        ['key' => 'talent', 'label' => 'Total Talent', 'icon' => 'fa-users', 'tone' => 'blue', 'rel' => 'all'],
        ['key' => 'bench', 'label' => 'Bench & Marketplace', 'icon' => 'fa-couch', 'tone' => 'amber', 'rel' => '2'],
        ['key' => 'sent', 'label' => 'Requests Sent', 'icon' => 'fa-paper-plane', 'tone' => 'blue', 'rel' => null],
        ['key' => 'accepted', 'label' => 'Requests Accepted', 'icon' => 'fa-circle-check', 'tone' => 'green', 'rel' => null],
        ['key' => 'expired', 'label' => 'Requests Expired', 'icon' => 'fa-clock', 'tone' => 'red', 'rel' => null],
        ['key' => 'views', 'label' => 'Profile Views', 'icon' => 'fa-eye', 'tone' => 'violet', 'rel' => null],
        ['key' => 'interviews', 'label' => 'Interviews', 'icon' => 'fa-user-tie', 'tone' => 'violet', 'rel' => null],
        ['key' => 'placed', 'label' => 'Placed', 'icon' => 'fa-trophy', 'tone' => 'amber', 'rel' => '3'],
    ];
@endphp

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
    <style>
        /* ===== Bench Drawer Responsive Width & Scrollbar Stability ===== */
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesBenchApplication { width: 1025px !important; }
        }
        @media only screen and (min-width: 1440px) {
            #kt_activitiesBenchApplication { width: 1200px !important; }
        }
        #kt_activitiesBenchApplication {
            scrollbar-gutter: stable;
        }

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

        /* In-House Talent hero: one compact connected journey, scoped to this page. */
        .bench-dash .talent-hero-header { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 10px 2px 12px; }
        .bench-dash .talent-hero-heading { min-width: 0; }
        .bench-dash .talent-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .bench-dash .talent-title { margin: 0; color: #142044; font-size: 22px !important; font-weight: 700; line-height: 1.2; }
        .bench-dash .talent-info { color: #64769b; font-size: 12px !important; }
        .bench-dash .talent-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 4px; color: #008d68; background: #e7faf4; font-size: 10px !important; font-weight: 600; }
        .bench-dash .talent-subtitle { margin: 2px 0 0; color: #52658b; font-size: 12px !important; }
        .bench-dash .talent-hero-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .bench-dash .talent-hero-actions .btn-add,
        .bench-dash .talent-hero-actions .btn-ghost,
        .bench-dash .hero-period { min-height: 36px; border-radius: 6px; }
        .bench-dash .talent-hero-actions .btn-add { background: #1267e9; padding-inline: 15px; }
        .bench-dash .hero-period { border: 1px solid #dce6f5; background: #fff; color: #16254b; padding: 5px 10px; font-weight: 600; cursor: pointer; }
        .bench-dash .hero-more { position: relative; }
        .bench-dash .hero-more summary { list-style: none; cursor: pointer; width: 36px; height: 36px; display: grid; place-items: center; border: 1px solid #dce6f5; border-radius: 6px; background: #fff; color: #142044; }
        .bench-dash .hero-more summary::-webkit-details-marker { display: none; }
        .bench-dash .hero-more-menu { position: absolute; z-index: 15; right: 0; top: 40px; min-width: 155px; border: 1px solid #dce6f5; border-radius: 6px; background: #fff; box-shadow: 0 10px 25px rgba(24, 39, 75, .12); padding: 5px; }
        .bench-dash .hero-more-menu a { display: block; padding: 8px; color: #263b67; font-size: 11px !important; text-decoration: none; }
        .bench-dash .hero-more-menu a:hover { background: #f4f8ff; }
        .bench-dash .pipeline-panel { border: 1px solid #dceaff; border-radius: 6px; background: #fbfdff; padding: 10px 13px 12px; margin-bottom: 10px; }
        .bench-dash .pipeline-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .bench-dash .pipeline-heading-copy { display: flex; align-items: center; min-width: 0; gap: 9px; }
        .bench-dash .pipeline-heading-icon { display: grid; place-items: center; flex: 0 0 30px; width: 30px; height: 30px; border: 1px solid #d8e9ff; border-radius: 5px; background: #edf6ff; color: #1267e9; }
        .bench-dash .pipeline-heading-icon i { font-size: 15px !important; }
        .bench-dash .pipeline-heading-title { margin: 0; color: #15254a; font-size: 13px !important; font-weight: 700; }
        .bench-dash .pipeline-heading-note { margin: 0; color: #54698f; font-size: 10.5px !important; }
        .bench-dash .pipeline-performance { position: relative; display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; min-height: 34px; border: 1px solid #d6e2f5; border-radius: 5px; color: #1f3765; background: #fff; box-shadow: 0 1px 2px rgba(24, 39, 75, .05); padding: 0 10px; }
        .bench-dash .pipeline-performance:hover { border-color: #9ebef0; }
        .bench-dash .pipeline-performance:focus-within { border-color: #1267e9; box-shadow: 0 0 0 3px rgba(18, 103, 233, .12); }
        .bench-dash .pipeline-performance select { appearance: none; min-width: 174px; height: 32px; border: 0; outline: 0; background: transparent; color: inherit; font-size: 11px !important; font-weight: 600; cursor: pointer; }
        .bench-dash .pipeline-performance > i { color: #4775b3; font-size: 12px !important; pointer-events: none; }
        .bench-dash .pipeline-performance > i:last-child { color: #637897; font-size: 10px !important; }
        .bench-dash .pipeline-status:not(:empty) { margin: 4px 0 0; color: #b42336; font-size: 10px !important; text-align: right; }
        .bench-dash .pipeline-panel.is-loading .pipeline-stages { opacity: .55; }
        .bench-dash .pipeline-scroller { overflow-x: auto; scrollbar-width: thin; }
        .bench-dash .pipeline-stages { display: grid; grid-template-columns: repeat(8, minmax(122px, 1fr)); min-width: 976px; padding-top: 11px; transition: opacity .15s; }
        .bench-dash .pipeline-stage { position: relative; min-width: 0; text-align: center; color: #142044; padding: 0 3px; border: 0; background: transparent; }
        .bench-dash button.pipeline-stage { cursor: pointer; }
        .bench-dash button.pipeline-stage:hover .pipeline-stage-label { color: #1267e9; }
        .bench-dash button.pipeline-stage.active .pipeline-stage-icon { outline: 2px solid #1267e9; outline-offset: 2px; }
        .bench-dash button.pipeline-stage:focus-visible { outline: 2px solid #1267e9; outline-offset: 2px; }
        .bench-dash .pipeline-stage-label { display: block; min-height: 16px; color: #16254b; font-size: 10px !important; font-weight: 600; white-space: nowrap; }
        .bench-dash .pipeline-stage-icon { position: relative; z-index: 2; display: grid; place-items: center; width: 42px; height: 42px; margin: 2px auto 3px; border-radius: 50%; }
        .bench-dash .pipeline-stage-icon i { font-size: 19px !important; }
        .bench-dash .pipeline-stage.blue .pipeline-stage-icon { background: #e8f3ff; color: #1676e9; }
        .bench-dash .pipeline-stage.amber .pipeline-stage-icon { background: #fff5df; color: #ee9b00; }
        .bench-dash .pipeline-stage.green .pipeline-stage-icon { background: #e5f8f1; color: #11a781; }
        .bench-dash .pipeline-stage.red .pipeline-stage-icon { background: #ffecef; color: #f42642; }
        .bench-dash .pipeline-stage.violet .pipeline-stage-icon { background: #f2ebff; color: #7634e9; }
        .bench-dash .pipeline-connector { position: absolute; z-index: 3; top: 32px; left: calc(50% + 22px); right: calc(-50% + 22px); height: 16px; display: flex; align-items: center; justify-content: center; pointer-events: none; }
        .bench-dash .pipeline-connector::before { content: ''; position: absolute; left: 0; right: 0; top: 50%; border-top: 1px solid #d7e7ff; }
        .bench-dash .pipeline-motion { position: relative; display: grid; place-items: center; width: 20px; height: 16px; background: #fbfdff; color: #1267e9; animation: pipeline-flow 2.6s ease-in-out infinite; animation-delay: var(--flow-delay, 0s); }
        .bench-dash .pipeline-motion i { font-size: 11px !important; }
        @keyframes pipeline-flow { 0%, 58%, 100% { transform: translateX(0); opacity: .78; } 72% { transform: translateX(3px); opacity: 1; } }
        @media (prefers-reduced-motion: reduce) { .bench-dash .pipeline-motion { animation: none; } }
        .bench-dash .pipeline-stage-value { display: block; font-size: 18px !important; line-height: 1.12; font-weight: 700; font-variant-numeric: tabular-nums; }
        @media (max-width: 800px) {
            .bench-dash .talent-hero-header { align-items: flex-start; flex-direction: column; }
            .bench-dash .talent-hero-actions { width: 100%; }
            .bench-dash .pipeline-heading { align-items: flex-start; flex-direction: column; }
            .bench-dash .pipeline-heading-note { line-height: 1.4; }
            .bench-dash .pipeline-performance { white-space: nowrap; }
        }

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

        /* Clients Pipeline Activity mini-grid */
        .bench-dash .pipe { display: flex; align-items: flex-start; gap: 0; width: 100%; min-width: 0; overflow: hidden; }
        .bench-dash .pipe > div { width: 100%; min-width: 0; }
        .bench-dash .pipe-grid { display: grid; grid-template-columns: repeat(8, minmax(12px, 1fr)); text-align: center; gap: 0; width: 100%; min-width: 0; }
        .bench-dash .pipe-grid .h { font-size: 8.5px !important; color: #a1a5b7; font-weight: 700; overflow: hidden; }
        .bench-dash .pipe-grid .v { font-size: 10px !important; font-weight: 700; color: #181c32; padding-top: 1px; overflow: hidden; }
        .bench-dash .pipe-grid a { font-size: 10px !important; font-weight: 700; }
        .bench-dash .pipe-foot { font-size: 9.5px !important; font-weight: 500; color: #3f4254; margin-top: 3px; display: flex; align-items: center; gap: 4px 7px; flex-wrap: wrap; line-height: 1.15; white-space: normal; overflow: hidden; }
        .bench-dash .pipe-stat { display: inline-flex; align-items: center; color: #7e8299; font-size: 9.5px !important; font-weight: 500; line-height: 1.15; flex-shrink: 0; }

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
        .bench-dash #benchListingTable .job-date-tag { color: #181c32; font-weight: 500; }
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

        /* Hidden table header bar for Bench Talent multi-selection — placed at right corner above list headers */
        .bench-table-header-toolbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            background: #F0FDF4;
            border: 1px solid #A7F3D0;
            border-radius: 6px;
            padding: 6px 14px;
            min-height: 48px;
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s ease;
        }
        .bench-table-header-toolbar.show {
            display: flex !important;
        }
        .bench-th-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .bench-th-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 21px;
            height: 21px;
            padding: 0 6px;
            border-radius: 11px;
            background: #0D9488;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
        }
        .bench-th-text {
            font-size: 12.5px;
            font-weight: 600;
            color: #065F46;
            white-space: nowrap;
        }
        .bench-th-clear-btn {
            font-size: 11.5px;
            font-weight: 500;
            color: #047857;
            text-decoration: underline;
            background: none;
            border: none;
            padding: 0 4px;
            cursor: pointer;
            white-space: nowrap;
        }
        .bench-th-clear-btn:hover {
            color: #064E3B;
        }
        .bench-th-right {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }
        .bench-btn-fwd-resumes {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #ffffff !important;
            color: #0D9488 !important;
            border: 1px solid #0D9488 !important;
            border-radius: 6px !important;
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 14px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(13, 148, 136, 0.08) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer;
            white-space: nowrap;
            line-height: 1 !important;
        }
        .bench-btn-fwd-resumes svg {
            width: 15px;
            height: 15px;
            color: #0D9488;
            stroke: currentColor;
            flex-shrink: 0;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .bench-btn-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 20px;
            padding: 0 7px;
            border-radius: 10px;
            background: #E6F4EA;
            color: #065F46;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #A7F3D0;
            line-height: 1;
            transition: all 0.2s ease;
        }
        .bench-btn-fwd-resumes:hover {
            background: #0D9488 !important;
            border-color: #0D9488 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(13, 148, 136, 0.3) !important;
        }
        .bench-btn-fwd-resumes:hover svg {
            color: #ffffff !important;
            transform: translateX(1px);
        }
        .bench-btn-fwd-resumes:hover .bench-btn-count {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
            border-color: transparent !important;
        }
        .bench-btn-fwd-resumes:active {
            background: #0F766E !important;
            border-color: #0F766E !important;
            color: #ffffff !important;
            transform: scale(0.98);
        }
        @media (max-width: 576px) {
            .bench-table-header-toolbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
            .bench-th-right {
                width: 100%;
                justify-content: flex-end;
            }
        }

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

                <!-- In-House Talent hero -->
                <div class="talent-hero-header">
                    <div class="talent-hero-heading">
                        <div class="talent-title-row">
                            <h1 class="talent-title">In-House Talent</h1>
                            <i class="fa fa-circle-info talent-info" title="Your in-house talent pool and marketplace activity."></i>
                            <span class="talent-badge"><i class="fa fa-house"></i> In-House Talent</span>
                        </div>
                        <p class="talent-subtitle">Manage your talent pool, bench availability, placements and marketplace readiness.</p>
                    </div>
                    <div class="talent-hero-actions">
                        <input type="hidden" id="inputSearch" name="inputSearch" />
                        @if (in_array('view_bench', $userPermissions))
                            <form method="get" action="{{ route('service_company.bench-candidates.index') }}" id="heroAnalyticsForm" class="d-inline-flex align-items-center gap-1 hero-period">
                                <i class="fa fa-calendar ms-2" aria-hidden="true"></i>
                                <select name="hero_month" class="border-0 bg-transparent pe-2" aria-label="Performance ending month">
                                    @foreach ($heroMonths as $monthOption)
                                        <option value="{{ $monthOption['value'] }}" @selected($heroMonth === $monthOption['value'])>{{ $monthOption['label'] }}</option>
                                    @endforeach
                                </select>
                                <noscript><button type="submit">Apply</button></noscript>
                            </form>
                            <a href="javascript:void(0);" class="btn-add" id="AddInternalPool">
                                <i class="fa fa-plus"></i> Add Talent
                            </a>
                        @endif
                        <button type="button" class="btn-ghost" id="exportTalents">
                            <i class="fa fa-file-export"></i> Export
                        </button>
                        <details class="hero-more">
                            <summary aria-label="More actions"><i class="fa fa-ellipsis"></i></summary>
                            <div class="hero-more-menu">
                                <a href="{{ route('service_company.bench-candidates.index', ['hero_month' => $heroMonth, 'performance_window' => $performanceWindow]) }}">Refresh performance</a>
                                <a href="{{ route('service_company.bench-candidates.index') }}">Reset period</a>
                            </div>
                        </details>
                    </div>
                </div>

                @if (in_array('view_bench', $userPermissions))
                    <section class="pipeline-panel" aria-labelledby="talentPipelineTitle">
                        <div class="pipeline-heading">
                            <div class="pipeline-heading-copy">
                                <span class="pipeline-heading-icon" aria-hidden="true"><i class="fa fa-route"></i></span>
                                <div>
                                    <h2 class="pipeline-heading-title" id="talentPipelineTitle">Talent Pipeline &amp; Marketplace Engagement
                                        <i class="fa fa-circle-info ms-1 text-muted" title="Inventory and indexed profile views are current totals; requests, interviews and placements follow the selected period."></i>
                                    </h2>
                                    <p class="pipeline-heading-note">Track your in-house talent journey from bench to placement and measure marketplace engagement.</p>
                                </div>
                            </div>
                            <div>
                                <label class="pipeline-performance" for="performanceWindow">
                                    <i class="fa fa-calendar-days" aria-hidden="true"></i>
                                    <select id="performanceWindow" name="performance_window" form="heroAnalyticsForm" aria-label="Pipeline performance period">
                                        <option value="monthly" @selected($performanceWindow === 'monthly')>Current Month ({{ \Carbon\Carbon::createFromFormat('!Y-m', $heroMonth)->format('M y') }})</option>
                                        <option value="quarterly" @selected($performanceWindow === 'quarterly')>Last 3 Months</option>
                                        <option value="half_yearly" @selected($performanceWindow === 'half_yearly')>Last 6 Months</option>
                                        <option value="yearly" @selected($performanceWindow === 'yearly')>Yearly</option>
                                    </select>
                                    <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                </label>
                                <p class="pipeline-status" id="pipelineStatus" role="status" aria-live="polite"></p>
                            </div>
                        </div>
                        <div class="pipeline-scroller">
                            <div class="pipeline-stages">
                                @foreach ($heroItems as $item)
                                    @php $metric = $heroStages[$item['key']]; @endphp
                                    @if ($item['rel'] !== null)
                                        <button type="button" class="pipeline-stage {{ $item['tone'] }} kpi-filter" data-rel="{{ $item['rel'] }}" id="kpi_{{ $item['rel'] }}" title="Filter the talent list">
                                    @else
                                        <div class="pipeline-stage {{ $item['tone'] }}">
                                    @endif
                                        <span class="pipeline-stage-label" @if ($item['key'] === 'views') title="All-time indexed profile views" @endif>{{ $item['label'] }}</span>
                                        <span class="pipeline-stage-icon"><i class="fa {{ $item['icon'] }}" aria-hidden="true"></i></span>
                                        @unless ($loop->last)
                                            <span class="pipeline-connector" aria-hidden="true"><span class="pipeline-motion" style="--flow-delay: {{ $loop->index * 0.12 }}s"><i class="fa fa-chevron-right"></i></span></span>
                                        @endunless
                                        <span class="pipeline-stage-value" data-pipeline-stage="{{ $item['key'] }}" @if ($item['key'] === 'talent') id="kpival_all" @elseif ($item['key'] === 'bench') id="kpival_2" @endif @unless ($metric['available']) title="Analytics source unavailable" @endunless>{{ $metric['available'] ? number_format($metric['value']) : '—' }}</span>
                                    @if ($item['rel'] !== null)
                                        </button>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <!-- Search + Sort row -->
                    <div class="jw-card p-2 mb-2 d-flex align-items-center gap-2">
                        <div class="position-relative flex-grow-1">
                            <i class="fa fa-search position-absolute text-muted" style="left:12px; top:11px; font-size:12px !important;"></i>
                            <input type="text" class="form-control dash-search w-100" style="padding-left:30px;" name="inputJobSearch"
                                placeholder="Search by name, skills, role, experience, location..." id="kt_tagify_2" onkeyup="debouncedBenchSearch(event)" />
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

                        <!-- Hidden Table Header: Visible ONLY once any profile is selected, placed at the right corner above list headers -->
                        <div class="bench-table-header-toolbar" id="bench_selection_bar" style="display: none;">
                            <div class="bench-th-left">
                                <span class="bench-th-badge" id="bench_selected_count_badge">0</span>
                                <span class="bench-th-text" id="bench_selected_text_wrap"><span id="bench_selected_count">0</span> Candidates Selected</span>
                                <button type="button" class="bench-th-clear-btn" id="bench_selection_close" title="Clear selection">
                                    (Deselect All)
                                </button>
                            </div>
                            <div class="bench-th-right">
                                <button type="button" class="btn bench-btn-fwd-resumes" id="bench_fwd_resumes_bulk_btn" title="Forward selected candidate resumes">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    <span>Forward Resume</span>
                                    <span class="bench-btn-count" id="bench_btn_count_badge">0/5</span>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="benchListingTable">
                                <thead>
                                    <tr role="row">
                                        <th class="col-select pe-0">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-2">
                                                <input class="form-check-input chk_all_bench_list" type="checkbox" id="chk_all_bench_list" value="1" title="Select all on this page">
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
        data-kt-drawer-width="{default:'95%', 'sm': '550px', 'lg': '780px'}" data-kt-drawer-direction="end"
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

        // Keep period changes isolated to the tenant-scoped pipeline card.
        (function() {
            var form = document.getElementById('heroAnalyticsForm');
            var panel = document.querySelector('.pipeline-panel');
            var windowSelect = document.getElementById('performanceWindow');
            if (!form || !panel || !windowSelect) return;

            var monthSelect = form.querySelector('[name="hero_month"]');
            var status = document.getElementById('pipelineStatus');
            var selectedMonth = monthSelect.value;
            var selectedWindow = windowSelect.value;
            var request = null;
            var revision = 0;

            function refreshPipeline() {
                var activeRevision = ++revision;
                if (request) request.abort();
                panel.classList.add('is-loading');
                panel.setAttribute('aria-busy', 'true');
                status.textContent = '';

                request = $.ajax({
                    url: form.action,
                    method: 'GET',
                    dataType: 'json',
                    cache: false,
                    data: {
                        pipeline_only: '1',
                        hero_month: monthSelect.value,
                        performance_window: windowSelect.value
                    },
                    success: function(response) {
                        var keys = ['talent', 'bench', 'sent', 'accepted', 'expired', 'views', 'interviews', 'placed'];
                        if (!response || !response.stages || !keys.every(function(key) { return response.stages[key]; })) {
                            status.textContent = 'Performance data is unavailable. Please try again.';
                            monthSelect.value = selectedMonth;
                            windowSelect.value = selectedWindow;
                            return;
                        }

                        keys.forEach(function(key) {
                            var stage = response.stages[key];
                            var value = panel.querySelector('[data-pipeline-stage="' + key + '"]');
                            value.textContent = stage.available ? Number(stage.value).toLocaleString() : '\u2014';
                            if (stage.available) value.removeAttribute('title');
                            else value.title = 'Analytics source unavailable';
                        });
                        selectedMonth = response.month;
                        selectedWindow = response.window;
                        monthSelect.value = selectedMonth;
                        windowSelect.value = selectedWindow;
                        var monthLabel = new Date(selectedMonth + '-01T00:00:00').toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
                        windowSelect.options[0].text = (monthSelect.selectedIndex === 0 ? 'Current' : 'Selected') + ' Month (' + monthLabel + ')';
                    },
                    error: function(xhr, textStatus) {
                        if (textStatus === 'abort') return;
                        status.textContent = 'Performance data could not be refreshed. Please try again.';
                        monthSelect.value = selectedMonth;
                        windowSelect.value = selectedWindow;
                    },
                    complete: function() {
                        if (activeRevision !== revision) return;
                        panel.classList.remove('is-loading');
                        panel.removeAttribute('aria-busy');
                        request = null;
                    }
                });
            }

            monthSelect.addEventListener('change', refreshPipeline);
            windowSelect.addEventListener('change', refreshPipeline);
        })();

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

        // Phase 1 Optimization: 250ms debounce for free-text search input (#kt_tagify_2)
        // Prevents 10-character typing from triggering 20 sequential AJAX calls.
        var _benchSearchTimer = null;
        function debouncedBenchSearch(e) {
            if (_benchSearchTimer) {
                clearTimeout(_benchSearchTimer);
            }
            if (e && (e.key === 'Enter' || e.keyCode === 13)) {
                filtersdataget();
                return;
            }
            _benchSearchTimer = setTimeout(function() {
                filtersdataget();
            }, 250);
        }

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
                        if (typeof BenchPageRamStore !== 'undefined') { BenchPageRamStore.invalidate(); }
                        var page = $('#paginationbenchCandidateBenchCandidates .active_disabled').data('page');
                        fetchBenchBenchCandidatesCandidateList(page, '', true);
                    }
                }
            });
        });

        // ---- Relationship Manager drawer (Client-First Execution & Browser RAM Store) ----
        window.JeroRmStore = {
            roster: null,
            epoch: null,
            fetchRoster: function(cb) {
                if (this.roster && this.roster.length) {
                    if (cb) cb(this.roster);
                    return;
                }
                var self = this;
                var cacheKey = 'jero_rm_roster_{{ auth()->user()->company_id }}';
                var cachedSession = sessionStorage.getItem(cacheKey);
                if (cachedSession) {
                    try {
                        var parsed = JSON.parse(cachedSession);
                        if (parsed && Array.isArray(parsed.team)) {
                            self.roster = parsed.team;
                            self.epoch = parsed.epoch;
                            if (cb) cb(self.roster);
                            return;
                        }
                    } catch(e) {}
                }
                $.ajax({
                    url: "{{ route('service_company.bench-candidates.rm-roster') }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function(resp) {
                        if (resp && resp.status === 'success' && resp.team) {
                            self.roster = resp.team;
                            self.epoch = resp.epoch;
                            try {
                                sessionStorage.setItem(cacheKey, JSON.stringify({ team: resp.team, epoch: resp.epoch }));
                            } catch(e) {}
                            if (cb) cb(self.roster);
                        }
                    }
                });
            }
        };

        // Pre-warm team roster in Browser RAM on page idle
        setTimeout(function() { window.JeroRmStore.fetchRoster(); }, 800);

        // Optimistic DOM patching on parent table row (0ms, 0 secondary HTTP roundtrips)
        window.patchBenchCandidateRmRow = function(candidateId, response) {
            var $cell = $('a.hmb-listing[data-row-id="' + candidateId + '"]').closest('td.col-manager-name');
            if ($cell.length && response) {
                var count = Number(response.assigned_count || 0);
                var firstName = response.first_manager || 'Unassigned';
                var extra = count > 1 ? ' +' + (count - 1) : '';
                var fullText = firstName + extra;
                var $nameSpan = $cell.find('.manager-name-main');
                if ($nameSpan.length) {
                    $nameSpan.text(fullText).attr('title', fullText);
                }
            }
        };

        $(document).on('click', '.hmb-listing', function() {
            $('#kt_activitiesHMB_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}".replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var $body = $('#kt_activitiesHMB_body');

            $body.html('<div style="text-align:center;padding:40px;"><img src="' + baseurl + '" height="35px"></div>');

            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function(response) {
                    if (response.status === "success" && response.view) {
                        $body.html(response.view);
                    }
                },
                error: function() {
                    $body.html('<div style="padding:24px;text-align:center;color:#ef4444;font-size:13px;">Failed to load relationship managers. Please try again.</div>');
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
                            if (typeof BenchPageRamStore !== 'undefined') { BenchPageRamStore.invalidate(); }
                            fetchBenchBenchCandidatesCandidateList(1, '', true);
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
                        var clientLabel = isInternal ? 'Internal' : 'Direct Client';

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

        // ---- Table fetch + pagination + Phase 5 Client-First Browser RAM Store (Rule 3, Rule 5, Rule 6, Rule 17) ----
        var _benchListXhr = null;
        var _benchCountXhr = null;
        var _benchReqSeq = 0;

        /**
         * Isolated Browser RAM + sessionStorage store for In-house Bench Talents listing.
         * Intent: Serves repeat tab/page/filter switches in <2ms from user RAM with 0 server strain,
         * while enforcing strict version & TTL validation so stale data is never served (Rule 3, 5, 6, 17).
         */
        var BenchPageRamStore = (function() {
            var companyId = '{{ (int) (auth()->user()->company_id ?? 0) }}';
            var storagePrefix = 'jero_bench_v1_' + companyId + '_';
            var ramMap = new Map();
            var maxEntries = 30;
            var ttlMs = 60000; // 60s client freshness window
            var latestCacheVersion = null;

            return {
                buildKey: function(page, params) {
                    return [
                        page,
                        params.search || '',
                        params.cus_search || '',
                        params.filter_status || '',
                        params.country || '',
                        params.sortingfilter || '',
                        params.selectedValue || 'all',
                        params.per_page || 10
                    ].join('|');
                },
                get: function(key) {
                    var now = Date.now();
                    var entry = ramMap.get(key);
                    if (!entry) {
                        try {
                            var raw = sessionStorage.getItem(storagePrefix + key);
                            if (raw) {
                                entry = JSON.parse(raw);
                                ramMap.set(key, entry);
                            }
                        } catch (e) {}
                    }
                    if (!entry) return null;
                    if ((now - (entry.ts || 0)) > ttlMs) {
                        this.remove(key);
                        return null;
                    }
                    if (latestCacheVersion && entry.cache_version && entry.cache_version !== latestCacheVersion) {
                        this.invalidate();
                        return null;
                    }
                    return entry.data;
                },
                set: function(key, response) {
                    if (!response || !response.html) return;
                    if (response.cache_version) {
                        if (latestCacheVersion && latestCacheVersion !== response.cache_version) {
                            this.invalidate();
                        }
                        latestCacheVersion = response.cache_version;
                    }
                    if (ramMap.size >= maxEntries) {
                        var oldestKey = ramMap.keys().next().value;
                        this.remove(oldestKey);
                    }
                    var record = {
                        ts: Date.now(),
                        cache_version: response.cache_version || null,
                        data: response
                    };
                    ramMap.set(key, record);
                    try {
                        sessionStorage.setItem(storagePrefix + key, JSON.stringify(record));
                    } catch (e) {}
                },
                remove: function(key) {
                    ramMap.delete(key);
                    try { sessionStorage.removeItem(storagePrefix + key); } catch (e) {}
                },
                invalidate: function() {
                    ramMap.clear();
                    try {
                        for (var i = sessionStorage.length - 1; i >= 0; i--) {
                            var k = sessionStorage.key(i);
                            if (k && k.indexOf(storagePrefix) === 0) {
                                sessionStorage.removeItem(k);
                            }
                        }
                    } catch (e) {}
                }
            };
        })();

        window.addEventListener('jero:bench-candidate-updated', function() {
            BenchPageRamStore.invalidate();
        });
        window.addEventListener('jero:candidate-added-to-job', function(ev) {
            BenchPageRamStore.invalidate();
            try {
                var detail = (ev && ev.detail) ? ev.detail : {};
                var applicantIds = Array.isArray(detail.applicantIds) ? detail.applicantIds : [];
                var jobCount = (Array.isArray(detail.jobIds) && detail.jobIds.length > 0) ? detail.jobIds.length : 1;
                applicantIds.forEach(function(appId) {
                    var $row = $('#benchCandidateBenchCandidatesAjax tr[data-bench-row-id="' + appId + '"]');
                    if (!$row.length) return;
                    var $inw = $row.find('[data-bench-pipe="inreview"]');
                    if ($inw.length) {
                        var cur = parseInt($inw.text(), 10) || 0;
                        $inw.text(cur + jobCount);
                    }
                    var $intFoot = $row.find('[data-bench-pipe-interview]');
                    if ($intFoot.length) {
                        var curInt = parseInt($intFoot.text(), 10) || 0;
                        $intFoot.text(curInt + jobCount);
                    }
                });
            } catch (e) {}
        });

        $(document).ready(function() {
            if (sessionStorage.getItem('jero_bench_force_refresh') === '1' || window.location.search.indexOf('_fresh=') !== -1) {
                try { sessionStorage.removeItem('jero_bench_force_refresh'); } catch (e) {}
                BenchPageRamStore.invalidate();
            }
            fetchBenchBenchCandidatesCandidateList();
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

        function renderBenchCandidatesResponse(response) {
            $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'none' });
            $('#benchCandidateBenchCandidatesAjax').html(response.html);
            $('#paginationbenchCandidateBenchCandidates').html(response.pagination);
            if ($.fn.tooltip) {
                $('#benchCandidateBenchCandidatesAjax [title]').tooltip({ trigger: 'hover', placement: 'top' });
            }
            if (typeof updateBenchSelectionBar === 'function') {
                updateBenchSelectionBar();
            }
            if (response.counts) {
                applyBenchCounts(response.counts);
            } else {
                countDataGetBenchList();
            }
        }

        function fetchBenchBenchCandidatesCandidateList(page = 1, search = '', forceRefresh = false) {
            var seq = ++_benchReqSeq;
            if (_benchListXhr && _benchListXhr.readyState !== 4) {
                _benchListXhr.abort();
            }
            var perPage = localStorage.getItem('candidatePerPage') || 10;
            var requestParams = {
                search: $('#inputSearch').val(),
                cus_search: $('#kt_tagify_2').val(),
                filter_status: $('#filter_status').val(),
                country: $('#country').val(),
                sortingfilter: $('#sortingfilter').val(),
                selectedValue: $("input[name='status_manage']:checked").val(),
                per_page: perPage,
                size: perPage
            };

            var isFreshReq = window.location.search.indexOf('_fresh=') !== -1;
            var cacheKey = BenchPageRamStore.buildKey(page, requestParams);
            if (!forceRefresh && !isFreshReq) {
                var cachedResponse = BenchPageRamStore.get(cacheKey);
                if (cachedResponse) {
                    renderBenchCandidatesResponse(cachedResponse);
                    return $.Deferred().resolve(cachedResponse).promise();
                }
            }

            $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'block' });
            _benchListXhr = $.ajax({
                url: `{!! route('service_company.bench-candidates.data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: requestParams,
                success: function(response) {
                    if (seq !== _benchReqSeq) return;
                    BenchPageRamStore.set(cacheKey, response);
                    renderBenchCandidatesResponse(response);
                },
                error: function(xhr, status) {
                    if (status === 'abort' || seq !== _benchReqSeq) return;
                    $('.loadingMessagebenchCandidateBenchCandidatesAjax').css({ display: 'none' });
                }
            });
            return _benchListXhr;
        }

        function applyBenchCounts(counts) {
            if (!counts) return;
            $('#bg_all').text(counts.all_records);
            $('#bg_1').text(counts.pool);
            $('#bg_2').text(counts.bench);
            $('#bg_4').text(counts.paused);
            $('#bg_3').text(counts.placed);
            $('#bg_5').text(counts.exited);
            $('#kpival_all').text(Number(counts.all_records || 0).toLocaleString());
            $('#kpival_2').text(Number(counts.bench || 0).toLocaleString());
        }

        function countDataGetBenchList() {
            if (_benchCountXhr && _benchCountXhr.readyState !== 4) {
                _benchCountXhr.abort();
            }
            _benchCountXhr = $.ajax({
                url: `{!! route('service_company.bench-candidates.countDataGetBenchList') !!}`,
                type: 'GET',
                data: { countData: true },
                success: function(response) {
                    applyBenchCounts(response);
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

    <!-- ===== Bulk Selection & Multi-Candidate Forward Resume Handler ===== -->
    <script>
        (function() {
            function updateBenchSelectionBar() {
                var $checked = $('#benchListingTable tbody .chk_list_item:checked');
                var n = $checked.length;
                $('#bench_selected_count').text(n);
                $('#bench_selected_count_badge').text(n);
                $('#bench_btn_count_badge').text(n + '/5');
                $('#bench_selected_text_wrap').html('<span id="bench_selected_count">' + n + '</span> ' + (n === 1 ? 'Candidate Selected' : 'Candidates Selected'));

                if (n > 0) {
                    $('#bench_selection_bar').addClass('show').show();
                    $('#bench_selection_master_chk').prop('checked', true);
                } else {
                    $('#bench_selection_bar').removeClass('show').hide();
                    $('#bench_selection_master_chk').prop('checked', false);
                    $('#chk_all_bench_list').prop('checked', false);
                }

                var totalVisible = $('#benchListingTable tbody .chk_list_item').length;
                if (totalVisible > 0 && n === totalVisible) {
                    $('#chk_all_bench_list').prop('checked', true).prop('indeterminate', false);
                } else if (n > 0 && n < totalVisible) {
                    $('#chk_all_bench_list').prop('checked', false).prop('indeterminate', true);
                } else {
                    $('#chk_all_bench_list').prop('checked', false).prop('indeterminate', false);
                }
            }
            window.updateBenchSelectionBar = updateBenchSelectionBar;

            function clearBenchSelection() {
                $('#benchListingTable .chk_list_item, #chk_all_bench_list, #bench_selection_master_chk').prop('checked', false).prop('indeterminate', false);
                $('#bench_selected_count').text('0');
                $('#bench_selected_count_badge').text('0');
                $('#bench_btn_count_badge').text('0/5');
                $('#bench_selected_text_wrap').html('<span id="bench_selected_count">0</span> Candidates Selected');
                $('#bench_selection_bar').removeClass('show').hide();
            }
            window.clearBenchSelection = clearBenchSelection;

            // Select all checkbox in table header
            $(document).on('change', '#chk_all_bench_list', function() {
                var isChecked = $(this).is(':checked');
                $('#benchListingTable tbody .chk_list_item').prop('checked', isChecked);
                updateBenchSelectionBar();
            });

            // Row-level checkbox toggle
            $(document).on('change', '#benchListingTable tbody .chk_list_item', function() {
                updateBenchSelectionBar();
            });

            // Clear selection buttons
            $(document).on('click', '#bench_selection_close', function() {
                clearBenchSelection();
            });
            $(document).on('change', '#bench_selection_master_chk', function() {
                if (!$(this).is(':checked')) {
                    clearBenchSelection();
                }
            });

            // "Forward In-House Resumes" bulk action trigger
            $(document).on('click', '#bench_fwd_resumes_bulk_btn', function(e) {
                e.preventDefault();
                var arr = [];
                $('#benchListingTable tbody .chk_list_item:checked').each(function() {
                    var val = $(this).val();
                    if (val) arr.push(val);
                });

                if (arr.length === 0) {
                    Swal.fire({
                        title: 'No Candidate Selected',
                        text: 'Please select at least one candidate before forwarding resumes.',
                        icon: 'warning',
                        confirmButtonColor: '#0D9488'
                    });
                    return;
                }

                // Strict 5-candidate hard cap enforcement BEFORE opening drawer
                if (arr.length > 5) {
                    Swal.fire({
                        title: 'Maximum 5 Candidates Allowed',
                        html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                              '<p style="margin-bottom:8px;">You’ve selected <strong>' + arr.length + ' candidates</strong>. The forward resume engine supports up to <strong>5 candidates</strong> per email.</p>' +
                              '<p style="color:#6B7280;margin:0;">Please select 5 candidates or fewer to continue.</p>' +
                              '</div>',
                        icon: 'warning',
                        confirmButtonText: 'OK, I Understand',
                        confirmButtonColor: '#0D9488'
                    });
                    return;
                }

                // Open Forward Resume drawer with selected candidates
                if (typeof window.openBenchForwardResume === 'function') {
                    window.openBenchForwardResume(arr);
                } else {
                    console.error('[BenchCandidates] openBenchForwardResume function not found on window.');
                }
            });
        })();
    </script>
@endpush
