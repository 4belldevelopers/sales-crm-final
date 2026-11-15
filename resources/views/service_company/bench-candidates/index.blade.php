@extends('layouts.service_company_layout')

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
        /* In-House Talent hero: page header & actions (Card Design) */
        .bench-dash .talent-hero-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .bench-dash .talent-hero-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 0;
        }
        .bench-dash .talent-hero-heading { min-width: 0; }
        .bench-dash .talent-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .bench-dash .talent-title { margin: 0; color: #181c32; font-size: 22px !important; font-weight: 700; line-height: 1.2; }
        .bench-dash .talent-info { color: #8e9bb2; font-size: 14px !important; cursor: pointer; }
        .bench-dash .talent-subtitle { margin: 4px 0 0; color: #7e8299; font-size: 12.5px !important; }
        .bench-dash .talent-hero-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .bench-dash .talent-hero-actions .btn-add { background: #006ae6; color: #fff; border-radius: 8px; padding: 0 16px; font-size: 12.5px !important; font-weight: 600; height: 38px; display: inline-flex; align-items: center; gap: 7px; border: none; transition: background 0.15s ease; }
        .bench-dash .talent-hero-actions .btn-add:hover { background: #0056b3; color: #fff; }
        .bench-dash .talent-hero-actions .btn-ghost { border: 1px solid #dce6f5; background: #fff; color: #5e6278; border-radius: 8px; padding: 0 14px; font-size: 12.5px !important; font-weight: 500; height: 38px; display: inline-flex; align-items: center; gap: 7px; transition: all 0.15s ease; }
        .bench-dash .talent-hero-actions .btn-ghost:hover { border-color: #b9d5fb; color: #006ae6; }
        .bench-dash .hero-more { position: relative; }
        .bench-dash .hero-more summary { list-style: none; cursor: pointer; width: 38px; height: 38px; display: grid; place-items: center; border: 1px solid #dce6f5; border-radius: 8px; background: #fff; color: #5e6278; transition: border-color 0.15s ease; }
        .bench-dash .hero-more summary::-webkit-details-marker { display: none; }
        .bench-dash .hero-more summary:hover { border-color: #b9d5fb; color: #006ae6; }
        .bench-dash .hero-more-menu { position: absolute; z-index: 15; right: 0; top: 44px; min-width: 160px; border: 1px solid #dce6f5; border-radius: 8px; background: #fff; box-shadow: 0 10px 25px rgba(24, 39, 75, .12); padding: 5px; }
        .bench-dash .hero-more-menu a { display: block; padding: 8px 12px; color: #263b67; font-size: 12px !important; text-decoration: none; border-radius: 6px; }
        .bench-dash .hero-more-menu a:hover { background: #f4f8ff; color: #006ae6; }

        /* ===== Rule 20: Hiring Pipeline Flow (Client Tracker Design Language) ===== */
        .hp-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            margin-bottom: 12px !important;
        }
        .hp-card .card-body {
            padding: 14px 18px !important;
        }
        .hp-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 2px;
            font-family: 'Poppins', sans-serif;
        }
        .hp-total-link {
            text-decoration: none;
            flex-shrink: 0;
        }
        .hp-total {
            width: 82px;
            height: 82px;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            transition: all .15s ease;
            cursor: pointer;
        }
        .hp-total.bg-total-active {
            background-color: #1d4ed8 !important;
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25) !important;
        }
        .hp-total.bg-gray {
            background: #f1f5f9 !important;
            border: 1.5px solid #e2e8f0 !important;
        }
        .hp-total.bg-gray:hover {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
        }
        .hp-total-icon {
            color: #ffffff;
            display: flex;
        }
        .hp-total.bg-gray .hp-total-icon {
            color: #64748b;
        }
        .hp-total-icon svg {
            width: 18px;
            height: 18px;
        }
        .hp-total-count {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }
        .hp-total.bg-gray .hp-total-count {
            color: #1e293b;
        }
        .hp-total-label {
            font-size: 9.5px;
            font-weight: 600;
            color: #dbeafe;
            text-align: center;
            line-height: 1.2;
            padding: 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .hp-total.bg-gray .hp-total-label {
            color: #64748b;
        }
        .hp-eq {
            font-size: 18px;
            font-weight: 600;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .hp-chevron {
            display: flex;
            color: #cbd5e1;
            flex-shrink: 0;
        }
        .hp-chevron svg {
            width: 16px;
            height: 16px;
        }
        .hp-divider {
            width: 1px;
            height: 44px;
            background: #e2e8f0;
            flex-shrink: 0;
            margin: 0 4px;
        }
        .hp-stage {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            cursor: pointer;
        }
        .hp-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            transition: border-color .15s, background-color .15s, transform .15s;
        }
        .hp-circle:hover {
            transform: translateY(-2px);
            border-color: var(--hp-color);
        }
        .hp-circle .hp-icon {
            display: flex;
            color: #94a3b8;
            transition: color .15s;
        }
        .hp-circle .hp-icon svg {
            width: 15px;
            height: 15px;
        }
        .hp-circle .hp-count {
            font-size: 13.5px;
            font-weight: 700;
            color: #334155;
            line-height: 1.2;
            transition: color .15s;
        }
        .hp-circle.active {
            border-color: var(--hp-color) !important;
            background: var(--hp-bg) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
        .hp-circle.active .hp-icon {
            color: var(--hp-color) !important;
        }
        .hp-circle.active .hp-count {
            color: var(--hp-color) !important;
        }
        .hp-label {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            text-align: center;
            white-space: nowrap;
            transition: color .15s, font-weight .15s;
        }
        .hp-label.active {
            font-weight: 700;
            color: #0f172a;
        }

        /* Toolbar Card (Rule 20 Sharp White Card Surface from ClientAppTracker) */
        .caf-toolbar-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            margin-bottom: 12px !important;
        }
        .caf-toolbar-card .card-body {
            padding: 10px 14px !important;
        }
        .caf-search-wrap,
        .caf-sort-wrap {
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            height: 38px !important;
            transition: all 0.15s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 0 12px !important;
        }
        .caf-search-wrap:hover,
        .caf-sort-wrap:hover {
            background: #ffffff !important;
            border-color: #94a3b8 !important;
        }
        .caf-search-wrap:focus-within,
        .caf-sort-wrap:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }
        .caf-search-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }
        .caf-sort-wrap {
            flex-shrink: 0;
            width: 210px;
            position: relative;
        }
        .caf-search-icon,
        .caf-sort-icon {
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }
        .caf-search-input.form-control {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 12.5px;
            line-height: 36px;
            height: 36px;
            color: #1e293b;
        }
        .caf-search-clear {
            border: none;
            background: transparent;
            color: #94a3b8;
            padding: 0;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        .caf-search-clear:hover { color: #dc2626; }
        .caf-sort-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0;
            font-size: 12.5px;
            font-weight: 500;
            color: #334155;
            text-align: left;
            cursor: pointer;
        }
        .caf-sort-btn:focus { outline: none; }
        .caf-sort-btn span {
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .caf-sort-btn .chevron {
            flex-shrink: 0;
            color: #94a3b8;
            transition: transform .15s;
        }
        .caf-sort-btn.open .chevron { transform: rotate(180deg); }
        .caf-sort-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 100%;
            width: max-content;
            max-width: 280px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(16,24,40,.12);
            padding: 6px;
            z-index: 60;
            max-height: 300px;
            overflow-y: auto;
        }
        .caf-sort-item {
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #334155;
            border-radius: 6px;
            cursor: pointer;
            white-space: nowrap;
        }
        .caf-sort-item:hover { background: #f8fafc; color: #009ef7; }
        .caf-sort-item.active { background: #eff6ff; color: #009ef7; font-weight: 600; }
        .caf-refresh-btn {
            height: 38px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .caf-refresh-btn:hover {
            background: #ffffff;
            border-color: #009ef7;
            color: #009ef7;
        }
        .cd-bench-export-btn {
            height: 38px !important;
            padding: 0 16px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* Similar Clients Sub-Card */
        .cd-subcard-similar-clients {
            width: 100% !important;
            min-width: 0 !important;
        }
        .btn-similar-clients {
            text-decoration: none !important;
            cursor: pointer !important;
        }

        /* Table */
        .bench-dash .table-responsive { overflow-x: hidden; }
        .bench-dash table#benchListingTable { width: 100% !important; min-width: 0; margin: 0; table-layout: fixed; }
        .bench-dash table#benchListingTable .col-select { width: 3%; }
        .bench-dash table#benchListingTable .col-talent-name { width: 21.5%; }
        .bench-dash table#benchListingTable .col-experience { width: 8.5%; }
        .bench-dash table#benchListingTable .col-availability { width: 9%; }
        .bench-dash table#benchListingTable .col-location { width: 9.5%; }
        .bench-dash table#benchListingTable .col-salary { width: 10.5%; }
        .bench-dash table#benchListingTable .col-status { width: 8.5%; }
        .bench-dash table#benchListingTable .col-manager-name { width: 11%; }
        .bench-dash table#benchListingTable .col-pipeline-activity { width: 18.5%; }
        .bench-dash table#benchListingTable thead th { font-size: 10px !important; text-transform: uppercase; letter-spacing: .2px; color: #a1a5b7; font-weight: 600; border-bottom: 1px solid #eef0f4; padding: 7px 6px !important; white-space: normal; line-height: 1.2; background: #fff; }
        .bench-dash table#benchListingTable tbody td { font-size: 12px !important; border-bottom: 1px solid #f4f5f8; padding: 7px 6px !important; vertical-align: middle; min-width: 0; overflow: hidden; }
        .bench-dash table#benchListingTable tbody tr:hover td { background: #fafbfc; }
        .bench-dash table#benchListingTable a { font-size: 12px !important; }
        .bench-dash table#benchListingTable .fs-6 { font-size: 12px !important; }
        .bench-dash table#benchListingTable .fs-7 { font-size: 11px !important; }
        .bench-dash table#benchListingTable .fs-3 { font-size: 13px !important; }
        .bench-dash table#benchListingTable .symbol-40px { width: 32px !important; height: 32px !important; }
        .bench-dash table#benchListingTable .symbol-label { line-height: 1; }
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
        .bench-dash table#benchListingTable tbody td.col-talent-name { position: relative; padding-bottom: 24px !important; }
        .bench-dash table#benchListingTable .job-meta-strip { position: absolute; bottom: 4px; left: 6px; right: 6px; overflow: hidden; }
        .bench-dash table#benchListingTable .job-meta-line { display: flex; align-items: center; gap: 4px; white-space: nowrap; overflow: hidden; }
        .bench-dash table#benchListingTable .job-date-tag,
        .bench-dash table#benchListingTable .skill-tag {
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
        .bench-dash table#benchListingTable .job-date-tag { color: #181c32; font-weight: 500; }
        .bench-dash table#benchListingTable .job-date-tag svg { width: 11px; height: 11px; margin-right: 3px; color: #3f4254; flex-shrink: 0; }
        .bench-dash table#benchListingTable .skill-tag { color: #7e8299; font-weight: 500; }

        /* Fit smaller / 15.6" laptops */
        @media only screen and (max-width: 1440px) {
            .bench-dash .talent-title { font-size: 20px !important; }
            .overview-kpi-card { padding: 8px 10px; min-height: 66px; }
            .overview-kpi-card .kpi-num { font-size: 18px !important; }
            .overview-kpi-card .kpi-label { font-size: 10.5px !important; }
            .overview-kpi-card .kpi-circle-icon { width: 24px; height: 24px; }
            .overview-kpi-card .kpi-circle-icon i { font-size: 10.5px !important; }
            .bench-dash .num { font-size: 16px !important; }
            .bench-dash .money { font-size: 14px !important; }
            .bench-dash table#benchListingTable tbody td, .bench-dash table#benchListingTable a { font-size: 11px !important; }
            .bench-dash table#benchListingTable thead th { font-size: 9.5px !important; padding-left: 5px !important; padding-right: 5px !important; }
            .bench-dash table#benchListingTable tbody td { padding-left: 5px !important; padding-right: 5px !important; }
            .bench-dash table#benchListingTable .symbol-40px { width: 30px !important; height: 30px !important; }
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
        .bstatus-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 22px; background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important; flex-shrink: 0; }
        #kt_activitiesB_header { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; }
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

        /* ==========================================================================
           RULE 20: MODERN CARD + SUB-CARD ENTERPRISE DESIGN SYSTEM (IN-HOUSE TALENT)
           ========================================================================== */
        /* Tier 1: Toolbar Card */
        .cd-bench-toolbar-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 14px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            width: 100% !important;
        }
        .cd-bench-search-subcard {
            flex: 1 1 auto !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0 12px !important;
            height: 38px !important;
            transition: all 0.15s ease-in-out !important;
            min-width: 0 !important;
        }
        .cd-bench-search-subcard:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }
        .cd-bench-search-subcard i.fa-search {
            color: #94a3b8 !important;
            font-size: 12.5px !important;
            flex-shrink: 0 !important;
        }
        .cd-bench-search-subcard input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-size: 12px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #0f172a !important;
            width: 100% !important;
            padding: 0 !important;
            line-height: 38px !important;
        }
        .cd-bench-search-clear {
            background: none !important;
            border: none !important;
            color: #94a3b8 !important;
            font-size: 11px !important;
            padding: 0 !important;
            cursor: pointer !important;
            display: none;
            flex-shrink: 0 !important;
        }
        .cd-bench-search-clear:hover {
            color: #475569 !important;
        }
        .cd-bench-toolbar-actions {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-shrink: 0 !important;
        }
        .cd-bench-sort-select {
            height: 38px !important;
            width: 190px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            font-size: 12px !important;
            color: #475569 !important;
            padding: 0 10px !important;
        }
        .cd-bench-export-btn {
            height: 38px !important;
            padding: 0 16px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            white-space: nowrap !important;
        }

        /* Tier 2: Sub-Tabs Card */
        .cd-bench-tabs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
        }
        .cd-bench-tabs-group {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }
        .cd-bench-tab-btn {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .cd-bench-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .cd-bench-tab-btn .cd-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            border-radius: 10px;
            font-size: 10.5px;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            transition: all 0.15s ease;
        }
        .cd-bench-tab-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
        }
        .cd-bench-tab-btn.active i {
            color: #ffffff !important;
        }
        .cd-bench-tab-btn.active .cd-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        /* Tier 3: Primary Talent Card Stream */
        .cd-talent-stream-wrapper {
            position: relative;
            width: 100%;
            min-width: 0;
        }
        .cd-talent-stream {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            min-width: 0;
        }
        .cd-talent-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-left: 4px solid #009ef7 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .cd-talent-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        }
        .cd-talent-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1.95fr) minmax(0, 1.35fr) minmax(0, 1.45fr) minmax(0, 1.05fr) minmax(0, 1.35fr) minmax(0, 2.15fr) !important;
            gap: 8px !important;
            padding: 10px 12px !important;
            width: 100% !important;
            align-items: stretch !important;
        }
        .cd-talent-avatar {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
            flex-shrink: 0 !important;
            overflow: hidden !important;
        }
        .cd-talent-avatar .symbol-label {
            width: 100% !important;
            height: 100% !important;
            border-radius: 9px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .cd-talent-company {
            font-size: 11px !important;
            color: #475569 !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }
        .cd-talent-company i {
            color: #009ef7 !important;
            font-size: 10px !important;
        }
        .cd-subcard-similar-clients {
            width: 100% !important;
            min-width: 0 !important;
        }
        .cd-subcard-pipeline {
            width: 100% !important;
            min-width: 0 !important;
            flex-grow: 1 !important;
        }
        .cd-talent-subcard {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        /* Mandatory Header Background Color Standard */
        .cd-subcard-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 5px 9px !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 28px;
            flex-shrink: 0;
        }
        .cd-subcard-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            color: #475569 !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cd-subcard-body {
            padding: 8px 10px !important;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            font-size: 11.5px;
            min-width: 0;
            overflow: hidden;
        }
        .cd-talent-name {
            font-size: 12.5px !important;
            font-weight: 700 !important;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        .cd-talent-role {
            font-size: 11px !important;
            color: #64748b !important;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
            line-height: 1.2;
        }
        .cd-status-badge {
            font-size: 9px !important;
            font-weight: 700 !important;
            padding: 2px 7px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .status-bench { background: #fff4de; color: #f6a609; border: 1px solid #ffe2a8; }
        .status-pool { background: #f3effe; color: #7239ea; border: 1px solid #dfcffc; }
        .status-hold { background: #fff8dd; color: #f59e0b; border: 1px solid #fde68a; }
        .status-placed { background: #eef3ff; color: #009ef7; border: 1px solid #cfe2ff; }
        .status-exited { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

        .cd-val-salary { font-size: 12.5px; color: #0f172a; font-weight: 700; line-height: 1.2; }
        .cd-val-service { font-size: 10.5px; font-weight: 600; }
        .cd-val-exp { font-size: 11px; }
        .cd-val-avail { font-size: 11.5px; line-height: 1.2; }
        .cd-val-wm { font-size: 10.5px; }
        .cd-val-location { font-size: 11px; line-height: 1.2; }
        .cd-manager-name { font-size: 11.5px; line-height: 1.25; }
        .cd-rm-change-btn { font-size: 10.5px !important; font-weight: 600 !important; color: #009ef7 !important; cursor: pointer; text-decoration: none; }
        .cd-rm-change-btn:hover { text-decoration: underline; color: #0077b6 !important; }

        /* Clients Pipeline Micro Grid */
        .cd-pipe-grid {
            display: grid !important;
            grid-template-columns: repeat(8, minmax(0, 1fr)) !important;
            gap: 3px !important;
            width: 100% !important;
        }
        .cd-pipe-col {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f8fafc !important;
            border: 1px solid #eef2f6 !important;
            border-radius: 4px !important;
            padding: 3px 1px !important;
            min-width: 0 !important;
            transition: all 0.15s ease-in-out;
        }
        .cd-pipe-col:hover {
            background: #eef3ff !important;
            border-color: #009ef7 !important;
        }
        .cd-pipe-col .h {
            font-size: 8px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            line-height: 1.1 !important;
        }
        .cd-pipe-col .v {
            font-size: 10.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.2 !important;
            margin-top: 1px !important;
        }
        .cd-pipe-col .v a {
            color: #0f172a !important;
            text-decoration: none !important;
            display: inline-block !important;
        }
        .cd-pipe-col .v a:hover {
            color: #009ef7 !important;
        }
        .cd-pipe-summary {
            font-size: 9.5px !important;
            line-height: 1.2 !important;
            width: 100% !important;
            min-width: 0 !important;
        }
        .cd-pipe-summary .pipe-stat {
            font-size: 9.5px !important;
            white-space: nowrap !important;
        }

        /* Sub-Card 6 / Bottom Activity Strip */
        .cd-talent-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 14px;
            background: #fafbfc;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
            font-size: 11px;
        }
        .cd-bottom-skills {
            display: flex;
            align-items: center;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            max-width: 580px;
        }
        .cd-bottom-skills::-webkit-scrollbar { display: none; }
        .cd-skill-pill {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            color: #475569;
            border-radius: 5px;
            padding: 2px 7px;
            font-size: 10px !important;
            font-weight: 500;
            white-space: nowrap;
            flex-shrink: 0;
            line-height: 1.25;
        }
        .cd-bottom-date { font-size: 11px; color: #64748b; white-space: nowrap; }

        /* Empty state */
        .cd-talent-empty {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 48px 24px;
            text-align: center;
            width: 100%;
        }

        .wrap-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        .wrap-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        /* 15.6" Screen responsiveness */
        @media only screen and (max-width: 1440px) {
            .cd-talent-card-grid {
                grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.3fr) minmax(0, 1.4fr) minmax(0, 1.0fr) minmax(0, 1.3fr) minmax(0, 2.1fr) !important;
                gap: 6px !important;
                padding: 8px 10px !important;
            }
            .cd-subcard-header { padding: 4px 6px !important; min-height: 26px; }
            .cd-subcard-body { padding: 6px 8px !important; font-size: 11px !important; }
            .cd-subcard-label { font-size: 9px !important; }
            .cd-pipe-col .h { font-size: 7.5px !important; }
            .cd-pipe-col .v { font-size: 9.5px !important; }
            .cd-bench-sort-select { width: 160px !important; }
        }
    </style>
@endpush

@section('content')
    <div class="bench-dash content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top: -60px;">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-5">

                <!-- In-House Talent hero card -->
                <div class="jw-card talent-hero-card mb-3">
                    <div class="talent-hero-header">
                        <div class="talent-hero-heading">
                            <div class="talent-title-row">
                                <h1 class="talent-title">In-House Talent</h1>
                                <i class="fa fa-circle-info talent-info" title="Manage your talent pool, bench availability, marketplace activity and placements."></i>
                            </div>
                            <p class="talent-subtitle">Manage your talent pool, bench availability, marketplace activity and placements.</p>
                        </div>
                        <div class="talent-hero-actions">
                            <input type="hidden" id="inputSearch" name="inputSearch" />
                            @if (in_array('view_bench', $userPermissions))
                                <a href="javascript:void(0);" class="btn-add" id="AddInternalPool">
                                    <i class="fa fa-plus"></i> Add In-house Talent
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
                </div>

                @if (in_array('view_bench', $userPermissions))
                    <!-- Hiring Pipeline Flow Card (Client App Tracker Design Standard) -->
                    <div class="card mb-3 hp-card">
                        <div class="card-body">
                            <div class="hp-flow">
                                <!-- Total / All Talents -->
                                <a class="application_sttus_nav hp-total-link" href="javascript:void(0);" rel="all">
                                    <div class="bg-total-active text-center hp-total" id="bg_all">
                                        <span class="hp-total-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                            </svg>
                                        </span>
                                        <span class="hp-total-count" id="bg_all_count">{{ $counts->all_records ?? 0 }}</span>
                                        <span class="hp-total-label">All Talents</span>
                                    </div>
                                </a>
                                <span class="hp-eq">=</span>

                                <!-- Stage 1: On Pool -->
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="1">
                                    <div class="hp-circle" data-hp="1" style="--hp-color: #0284c7; --hp-bg: #f0f9ff;">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_1">{{ $counts->pool ?? 0 }}</span>
                                    </div>
                                    <div class="hp-label" id="tx_1">On Pool</div>
                                </a>
                                <span class="hp-chevron">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 6 6 6-6 6" />
                                    </svg>
                                </span>

                                <!-- Stage 2: Bench -->
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="2">
                                    <div class="hp-circle" data-hp="2" style="--hp-color: #d97706; --hp-bg: #fffbeb;">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_2">{{ $counts->bench ?? 0 }}</span>
                                    </div>
                                    <div class="hp-label" id="tx_2">Bench</div>
                                </a>
                                <span class="hp-chevron">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 6 6 6-6 6" />
                                    </svg>
                                </span>

                                <!-- Stage 4: On Hold -->
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="4">
                                    <div class="hp-circle" data-hp="4" style="--hp-color: #64748b; --hp-bg: #f1f5f9;">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="10" x2="10" y1="15" y2="9" />
                                                <line x1="14" x2="14" y1="15" y2="9" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_4">{{ $counts->paused ?? 0 }}</span>
                                    </div>
                                    <div class="hp-label" id="tx_4">On Hold</div>
                                </a>
                                <span class="hp-chevron">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 6 6 6-6 6" />
                                    </svg>
                                </span>

                                <!-- Stage 3: Placed -->
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="3">
                                    <div class="hp-circle" data-hp="3" style="--hp-color: #16a34a; --hp-bg: #f0fdf4;">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                                                <path d="M4 22h16" />
                                                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" />
                                                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" />
                                                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_3">{{ $counts->placed ?? 0 }}</span>
                                    </div>
                                    <div class="hp-label" id="tx_3">Placed</div>
                                </a>
                                <span class="hp-divider"></span>

                                <!-- Stage 5: Exited -->
                                <a class="application_sttus_nav hp-stage" href="javascript:void(0);" rel="5">
                                    <div class="hp-circle" data-hp="5" style="--hp-color: #dc2626; --hp-bg: #fef2f2;">
                                        <span class="hp-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                                <polyline points="16 17 21 12 16 7" />
                                                <line x1="21" x2="9" y1="12" y2="12" />
                                            </svg>
                                        </span>
                                        <span class="hp-count" id="bg_5">{{ $counts->exited ?? 0 }}</span>
                                    </div>
                                    <div class="hp-label" id="tx_5">Exited</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tier 1: Toolbar Card with Search, Sort & Refresh (Client App Tracker Standard) -->
                    <div class="card mb-3 caf-toolbar-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="caf-search-wrap" style="position: relative;">
                                    <span class="caf-search-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                                    </span>
                                    <input type="text" class="form-control caf-search-input" name="inputJobSearch"
                                        placeholder="Search by name, skills, role, experience, location..."
                                        id="kt_tagify_2" onkeyup="debouncedBenchSearch(event)" autocomplete="off" />
                                    <button type="button" class="caf-search-clear" id="btnCafSearchClear" title="Clear search">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                                <div class="caf-sort-wrap" id="caf_sort_wrap">
                                    <span class="caf-sort-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
                                    </span>
                                    <button type="button" class="caf-sort-btn" id="caf_sort_btn">
                                        <span id="caf_sort_label">Sort</span>
                                        <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                    <select id="sortingfilter" style="display:none;" onchange="filtersdataget()">
                                        <option value="Show All" selected="">Sort by</option>
                                        <option value="1">Newest to Oldest (Upload Date)</option>
                                        <option value="2">Oldest to Newest (Upload Date)</option>
                                        <option value="9">Notice Period (Immediate)</option>
                                        <option value="5">Low to High (Notice Period)</option>
                                        <option value="6">High to Low (Notice Period)</option>
                                        <option value="7">High to Low (Experience)</option>
                                        <option value="8">Low to High (Experience)</option>
                                    </select>
                                    <div class="caf-sort-panel d-none" id="caf_sort_panel"></div>
                                </div>
                                <button type="button" class="btn btn-sm caf-refresh-btn" id="btnCafRefreshList" title="Refresh List">
                                    <i class="fa fa-rotate"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-sm btn-primary fw-bold flex-shrink-0" id="exportTalents" title="Export talent records to CSV">
                                    <i class="fa fa-file-export me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs & radios preserved for existing filter & fetch engine -->
                    <input type="hidden" id="filter_status" value="" />
                    <input type="hidden" id="country" value="" />
                    <input type="hidden" id="mstatus_id" value="all" />
                    <input type="hidden" id="jobid" value="" />
                    <div class="d-none">
                        <input class="rating-input" name="status_manage" checked value="all" type="radio" id="all_talents">
                        <input class="rating-input" name="status_manage" value="1" type="radio" id="talent_Pool">
                        <input class="rating-input" name="status_manage" value="2" type="radio" id="bench_live">
                        <input class="rating-input" name="status_manage" value="4" type="radio" id="bench_pause">
                        <input class="rating-input" name="status_manage" value="3" type="radio" id="bench_placed">
                        <input class="rating-input" name="status_manage" value="5" type="radio" id="bench_left">
                        <span class="nav_status"></span>
                    </div>

                    <!-- Multi-Selection Action Bar (Visible when any candidate is selected) -->
                    <div class="bench-table-header-toolbar" id="bench_selection_bar" style="display: none;">
                        <div class="bench-th-left">
                            <div class="form-check form-check-sm form-check-custom form-check-solid m-0 me-2">
                                <input class="form-check-input chk_all_bench_list" type="checkbox" id="bench_selection_master_chk" value="1" title="Select all on this page">
                            </div>
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

                    <!-- Tier 3: Primary Talent Card Stream (Rule 20 Standard) -->
                    <div class="cd-talent-stream-wrapper" id="benchListingTable">
                        <div class="loadingMessage loadingMessagebenchCandidateBenchCandidatesAjax"
                            style="display:none; position:absolute; z-index: 10; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,255,255,0.95); padding: 8px 18px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                            <i class="fa fa-spinner fa-spin text-primary me-2"></i>Loading talents...
                        </div>
                        <div id="benchCandidateBenchCandidatesAjax" class="cd-talent-stream"></div>
                        <div id="paginationbenchCandidateBenchCandidates" class="mt-4"></div>
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

    <!-- Similar Clients Drawer (Rule 20 Modern Card + Sub-Card Enterprise Standard) -->
    <div id="kt_activitiesSimilarClients_toggle" class="d-none"></div>
    <div id="kt_activitiesSimilarClients" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="similar_clients"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'95%', 'sm': '650px', 'lg': '880px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesSimilarClients_toggle" data-kt-drawer-close="#kt_activitiesSimilarClients_close">
        <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column" style="font-family: 'Poppins', sans-serif;">
            <!-- Header (Mandatory #f8fafc Background per Rule 20) -->
            <div class="card-header px-6 py-3 d-flex align-items-center justify-content-between" style="background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; min-height: 60px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                        <i class="fa fa-handshake"></i>
                    </div>
                    <div>
                        <h3 class="card-title fw-bolder text-dark m-0" style="font-size: 15px !important; letter-spacing: -0.2px;">Similar Potential Clients</h3>
                        <p class="text-muted m-0" style="font-size: 11px !important;" id="sc_drawer_subtitle">Matching direct client mandates and internal client requisitions</p>
                    </div>
                </div>
                <div class="card-toolbar d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="kt_activitiesSimilarClients_close">
                        <i class="fa fa-times fs-4"></i>
                    </button>
                </div>
            </div>

            <!-- Tab Navigation for Direct vs Internal Clients (Rule 25 Canonical Tagging) -->
            <div class="px-6 py-2 border-bottom" style="background: #ffffff; border-color: #e2e8f0 !important;">
                <ul class="nav nav-pills nav-pills-custom gap-2" id="similarClientsTabs">
                    <li class="nav-item">
                        <a class="nav-link active sc-tab-pill" id="tab_similar_direct" data-type="direct" href="javascript:void(0);" style="padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1px solid #e2e8f0; color: #475569;">
                            <i class="fa fa-shield-alt text-success me-1"></i> Direct Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sc-tab-pill" id="tab_similar_internal" data-type="internal" href="javascript:void(0);" style="padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1px solid #e2e8f0; color: #475569;">
                            <i class="fa fa-building text-primary me-1"></i> Internal Clients
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Drawer Body -->
            <div class="card-body position-relative p-6 flex-grow-1 overflow-auto" id="kt_activitiesSimilarClients_body">
                <div class="text-center py-20 text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                    <div>Loading similar client opportunities...</div>
                </div>
            </div>
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

        // Client-First Browser RAM cache for 10K concurrent users (0% server/DB read strain)
        const HeroAnalyticsRamStore = (function() {
            var _cache = new Map();
            return {
                get: function(month) { return _cache.get(month); },
                set: function(month, data) { _cache.set(month, data); },
                has: function(month) { return _cache.has(month); },
                clear: function() { _cache.clear(); }
            };
        })();

        // Seed initial month payload into user browser RAM immediately
        @if(isset($heroMonth) && isset($heroStages))
            HeroAnalyticsRamStore.set('{{ $heroMonth }}', {
                month: '{{ $heroMonth }}',
                window: '{{ $performanceWindow ?? "monthly" }}',
                stages: @json($heroStages)
            });
        @endif

        // Keep period changes isolated to the tenant-scoped pipeline card.
        (function() {
            var form = document.getElementById('heroAnalyticsForm');
            var panel = document.getElementById('talentOverviewCard') || document.querySelector('.pipeline-panel');
            var windowSelect = document.getElementById('performanceWindow');
            if (!form || !panel) return;

            var monthSelect = form.querySelector('[name="hero_month"]');
            var selectedMonth = monthSelect ? monthSelect.value : '';
            var selectedWindow = windowSelect ? windowSelect.value : 'monthly';
            var request = null;
            var revision = 0;

            function renderStages(response) {
                var keys = ['talent', 'bench', 'sent', 'accepted', 'expired', 'views', 'interviews', 'placed'];
                keys.forEach(function(key) {
                    var stage = response.stages[key];
                    var value = panel.querySelector('[data-pipeline-stage="' + key + '"]');
                    if (value && stage) {
                        value.textContent = stage.available ? Number(stage.value).toLocaleString() : '\u2014';
                        if (stage.available) value.removeAttribute('title');
                        else value.title = 'Analytics source unavailable';
                    }
                });
                selectedMonth = response.month;
                selectedWindow = response.window;
                if (monthSelect) monthSelect.value = selectedMonth;
                if (windowSelect) {
                    windowSelect.value = selectedWindow;
                    if (windowSelect.options && windowSelect.options.length > 0) {
                        var monthLabel = new Date(selectedMonth + '-01T00:00:00').toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
                        windowSelect.options[0].text = (monthSelect.selectedIndex === 0 ? 'Current' : 'Selected') + ' Month (' + monthLabel + ')';
                    }
                }
            }

            function refreshPipeline() {
                var targetMonth = monthSelect ? monthSelect.value : '';

                // Step 1: Check Client-First Browser RAM Cache (0ms, 0% server/Elasticsearch read strain for 10K concurrency)
                if (HeroAnalyticsRamStore.has(targetMonth)) {
                    renderStages(HeroAnalyticsRamStore.get(targetMonth));
                    return;
                }

                var activeRevision = ++revision;
                if (request) request.abort();
                panel.classList.add('is-loading');
                panel.setAttribute('aria-busy', 'true');

                request = $.ajax({
                    url: form.action,
                    method: 'GET',
                    dataType: 'json',
                    cache: true,
                    data: {
                        pipeline_only: '1',
                        hero_month: targetMonth,
                        performance_window: windowSelect ? windowSelect.value : 'monthly'
                    },
                    success: function(response) {
                        var keys = ['talent', 'bench', 'sent', 'accepted', 'expired', 'views', 'interviews', 'placed'];
                        if (!response || !response.stages || !keys.every(function(key) { return response.stages[key]; })) {
                            if (monthSelect) monthSelect.value = selectedMonth;
                            if (windowSelect) windowSelect.value = selectedWindow;
                            return;
                        }

                        // Store in Browser RAM
                        HeroAnalyticsRamStore.set(targetMonth, response);
                        renderStages(response);
                    },
                    error: function(xhr, textStatus) {
                        if (textStatus === 'abort') return;
                        if (monthSelect) monthSelect.value = selectedMonth;
                        if (windowSelect) windowSelect.value = selectedWindow;
                    },
                    complete: function() {
                        if (activeRevision !== revision) return;
                        panel.classList.remove('is-loading');
                        panel.removeAttribute('aria-busy');
                        request = null;
                    }
                });
            }

            if (monthSelect) monthSelect.addEventListener('change', refreshPipeline);
            if (windowSelect) windowSelect.addEventListener('change', refreshPipeline);
            window.refreshBenchPipeline = refreshPipeline;

            // "Share list" button handler
            $(document).on('click', '#shareTalentListBtn', function(e) {
                e.preventDefault();
                var shareUrl = window.location.origin + window.location.pathname;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareUrl).then(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Link Copied!',
                            text: 'In-house talent list link copied to clipboard.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }).catch(function() {
                        promptShareFallback(shareUrl);
                    });
                } else {
                    promptShareFallback(shareUrl);
                }
                function promptShareFallback(url) {
                    Swal.fire({
                        title: 'Share In-House Talent List',
                        input: 'text',
                        inputValue: url,
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#0D9488'
                    });
                }
            });

            // "View list" button handler - smooth scroll to table
            $(document).on('click', '#viewTalentListBtn', function(e) {
                e.preventDefault();
                var tableElem = document.getElementById('benchListingTable');
                if (tableElem) {
                    tableElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        })();

        // ---- Status tab / KPI flow filter (drives existing fetch pipeline) ----
        $(document).on('click', '.application_sttus_nav', function() {
            var page = $(this).attr('rel');

            $('.hp-circle').removeClass('active');
            $('.hp-label').removeClass('active');
            $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');

            if (page === 'all' || !page) {
                $('#bg_all').addClass('bg-total-active').removeClass('bg-gray');
                $('#all_talents').prop('checked', true).trigger('change');
            } else {
                $('.hp-circle[data-hp="' + page + '"]').addClass('active');
                $('#tx_' + page).addClass('active');
                if (page == 1)      { $('#talent_Pool').prop('checked', true).trigger('change'); }
                else if (page == 2) { $('#bench_live').prop('checked', true).trigger('change'); }
                else if (page == 3) { $('#bench_placed').prop('checked', true).trigger('change'); }
                else if (page == 4) { $('#bench_pause').prop('checked', true).trigger('change'); }
                else if (page == 5) { $('#bench_left').prop('checked', true).trigger('change'); }
            }

            $('#mstatus_id').val(page);
            fetchBenchBenchCandidatesCandidateList(1);
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

        // Search input with 250ms debounce and clear button (Rule 17)
        var _benchSearchTimer = null;
        function debouncedBenchSearch(e) {
            var val = $('#kt_tagify_2').val().trim();
            if (val.length > 0) {
                $('#btnCafSearchClear, #btnBenchSearchClear').show();
            } else {
                $('#btnCafSearchClear, #btnBenchSearchClear').hide();
            }
            if (e && (e.key === 'Enter' || e.keyCode === 13)) {
                if (_benchSearchTimer) clearTimeout(_benchSearchTimer);
                filtersdataget();
                return;
            }
            if (_benchSearchTimer) clearTimeout(_benchSearchTimer);
            _benchSearchTimer = setTimeout(function() {
                filtersdataget();
            }, 250);
        }

        $(document).on('input keyup', '#kt_tagify_2', debouncedBenchSearch);

        $(document).on('click', '#btnCafSearchClear, #btnBenchSearchClear', function() {
            $('#kt_tagify_2').val('');
            $(this).hide();
            filtersdataget();
        });

        // Custom Sort Panel (Rule 20 Standard from Client App Tracker)
        function cafRenderSortPanel() {
            var $panel = $('#caf_sort_panel').empty();
            var current = $('#sortingfilter').val();
            $('#sortingfilter option').each(function() {
                var $opt = $(this);
                var val = $opt.attr('value');
                var label = $opt.text();
                $('<div class="caf-sort-item"></div>').text(label)
                    .toggleClass('active', val === current)
                    .on('click', function() {
                        $('#sortingfilter').val(val);
                        $('#caf_sort_label').text(label);
                        cafCloseSortPanel();
                        filtersdataget();
                    })
                    .appendTo($panel);
            });
        }
        function cafOpenSortPanel() {
            cafRenderSortPanel();
            $('#caf_sort_panel').removeClass('d-none');
            $('#caf_sort_btn').addClass('open');
        }
        function cafCloseSortPanel() {
            $('#caf_sort_panel').addClass('d-none');
            $('#caf_sort_btn').removeClass('open');
        }
        $(document).on('click', '#caf_sort_btn', function(e) {
            e.stopPropagation();
            if ($('#caf_sort_panel').hasClass('d-none')) cafOpenSortPanel();
            else cafCloseSortPanel();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#caf_sort_wrap').length) cafCloseSortPanel();
        });

        // Refresh List Button
        $(document).on('click', '#btnCafRefreshList', function() {
            if (typeof BenchPageRamStore !== 'undefined') { BenchPageRamStore.invalidate(); }
            filtersdataget();
        });

        // Similar Clients Drawer AJAX Handler (Direct & Internal Client Tabs)
        var _activeSimilarCandidateId = null;
        var _activeSimilarType = 'direct';

        function loadSimilarClients(candidateId, type) {
            _activeSimilarCandidateId = candidateId;
            _activeSimilarType = type || 'direct';

            $('.sc-tab-pill').removeClass('active');
            if (_activeSimilarType === 'internal') {
                $('#tab_similar_internal').addClass('active');
            } else {
                $('#tab_similar_direct').addClass('active');
            }

            var $body = $('#kt_activitiesSimilarClients_body');
            $body.html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin fa-2x text-primary mb-3"></i><div>Loading opportunities...</div></div>');

            var endpoint = (_activeSimilarType === 'internal') 
                ? "{{ route('recommend.all_internal_data_ajax') }}" 
                : "{{ route('recommend.all_data_ajax') }}";

            var fullPageUrl = (_activeSimilarType === 'internal')
                ? "{{ url('service_company/bench-candidates/recommend_internal_req') }}/" + candidateId
                : "{{ url('service_company/bench-candidates/recommend_req') }}/" + candidateId;

            $.ajax({
                type: 'GET',
                url: endpoint,
                data: { condidate_id: candidateId, page: 1, limit: 10 },
                dataType: 'json',
                success: function(res) {
                    var html = '';
                    if (res && res.html) {
                        html += '<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">';
                        html += '  <span class="text-muted fs-7">Found <strong>' + (res.total || 0) + '</strong> matching ' + (_activeSimilarType === 'internal' ? 'Internal Client' : 'Direct Client') + ' mandates</span>';
                        html += '  <a href="' + fullPageUrl + '" target="_blank" class="btn btn-sm btn-light-primary fw-bold fs-8">';
                        html += '    <i class="fa fa-arrow-up-right-from-square me-1"></i> Open Full View';
                        html += '  </a>';
                        html += '</div>';
                        html += '<div class="similar-clients-content">' + res.html + '</div>';
                        if (res.pagination) {
                            html += '<div class="mt-4">' + res.pagination + '</div>';
                        }
                    } else {
                        html = '<div class="text-center py-20 text-muted"><i class="fa fa-briefcase fa-3x mb-3 text-gray-300"></i><p class="fs-6 fw-bold">No similar client mandates found</p><p class="fs-7">We couldn\'t find any active matching client requisitions for this candidate\'s primary skills.</p></div>';
                    }
                    $body.html(html);
                },
                error: function(xhr) {
                    $body.html('<div class="text-center py-20 text-danger"><i class="fa fa-triangle-exclamation fa-2x mb-2"></i><div>Failed to load client opportunities. Please try again.</div></div>');
                }
            });
        }

        $(document).on('click', '.btn-similar-clients', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var candidateId = $(this).data('bench-id') || $(this).data('candidate-id');
            var candidateName = $(this).data('bench-name') || '';
            var candidateRole = $(this).data('bench-role') || '';
            var clientType = $(this).data('client-type') || 'direct';

            if (candidateName) {
                $('#sc_drawer_subtitle').html('Matching opportunities for <strong>' + candidateName + '</strong>' + (candidateRole ? ' (' + candidateRole + ')' : ''));
            }

            var drawerEl = document.querySelector('#kt_activitiesSimilarClients');
            if (typeof KTDrawer !== 'undefined' && drawerEl) {
                var drawer = KTDrawer.getInstance(drawerEl);
                if (!drawer) {
                    drawer = new KTDrawer(drawerEl);
                }
                drawer.show();
            } else {
                $('#kt_activitiesSimilarClients_toggle').trigger('click');
            }

            loadSimilarClients(candidateId, clientType);
        });

        $(document).on('click', '.sc-tab-pill', function(e) {
            e.preventDefault();
            var type = $(this).data('type');
            if (_activeSimilarCandidateId) {
                loadSimilarClients(_activeSimilarCandidateId, type);
            }
        });

        $(document).on('click', '#AddInternalPoolEmpty', function() {
            $('#AddInternalPool').trigger('click');
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
                        if (typeof BenchPageRamStore !== 'undefined') { BenchPageRamStore.invalidate(); }
                        if (typeof BenchForwardResumeRamStore !== 'undefined') { BenchForwardResumeRamStore.invalidate(); }
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

        // Optimistic DOM patching on parent row or card (0ms, 0 secondary HTTP roundtrips)
        window.patchBenchCandidateRmRow = function(candidateId, response) {
            var $cell = $('a.hmb-listing[data-row-id="' + candidateId + '"]').closest('td.col-manager-name, .cd-subcard-manager');
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
                            if (typeof BenchForwardResumeRamStore !== 'undefined') { BenchForwardResumeRamStore.invalidate(); }
                            if (typeof HeroAnalyticsRamStore !== 'undefined') { HeroAnalyticsRamStore.clear(); }
                            if (typeof window.refreshBenchPipeline === 'function') { window.refreshBenchPipeline(); }
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
                    var $row = $('#benchCandidateBenchCandidatesAjax [data-bench-row-id="' + appId + '"]');
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
            $('#bg_all_count').text(counts.all_records || 0);
            $('#bg_1').text(counts.pool || 0);
            $('#bg_2').text(counts.bench || 0);
            $('#bg_4').text(counts.paused || 0);
            $('#bg_3').text(counts.placed || 0);
            $('#bg_5').text(counts.exited || 0);
            $('#headerTotalTalentCount').text(counts.all_records || 0);
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
        // Export the currently loaded talent records to CSV.
        $(document).on('click', '#exportTalents', function() {
            var rows = [];
            var headers = ['"Talent Name"', '"Experience"', '"Availability"', '"Location"', '"Expected Salary"', '"Bench Status"', '"Relationship Manager"', '"Interviews"', '"Views"', '"Downloads"'];
            rows.push(headers.join(','));

            var $cards = $('#benchCandidateBenchCandidatesAjax .cd-talent-card');
            if ($cards.length > 0) {
                $cards.each(function() {
                    var name = $(this).find('.cd-talent-name').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var exp = $(this).find('.cd-val-exp').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var avail = $(this).find('.cd-val-avail').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var loc = $(this).find('.cd-val-location').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var sal = $(this).find('.cd-val-salary strong').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var status = $(this).find('.cd-status-badge').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var rm = $(this).find('.cd-manager-name').text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                    var intCount = $(this).find('[data-bench-pipe-interview]').text().trim() || '0';
                    var views = $(this).find('.cd-pipe-summary-stat:contains("Views") strong').text().trim() || '0';
                    var downloads = $(this).find('.cd-pipe-summary-stat:contains("Downloads") strong').text().trim() || '0';

                    rows.push(['"' + name + '"', '"' + exp + '"', '"' + avail + '"', '"' + loc + '"', '"' + sal + '"', '"' + status + '"', '"' + rm + '"', '"' + intCount + '"', '"' + views + '"', '"' + downloads + '"'].join(','));
                });
            } else {
                $('#benchListingTable tbody tr').each(function() {
                    if ($(this).find('td').length < 2) return;
                    var cols = [];
                    $(this).find('td').each(function(i) {
                        if (i === 0) return;
                        var txt = $(this).text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                        cols.push('"' + txt + '"');
                    });
                    rows.push(cols.join(','));
                });
            }

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
                var $checked = $('.chk_list_item:checked');
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

                var totalVisible = $('.chk_list_item').length;
                if (totalVisible > 0 && n === totalVisible) {
                    $('#chk_all_bench_list').prop('checked', true).prop('indeterminate', false);
                    $('#bench_selection_master_chk').prop('checked', true).prop('indeterminate', false);
                } else if (n > 0 && n < totalVisible) {
                    $('#chk_all_bench_list').prop('checked', false).prop('indeterminate', true);
                    $('#bench_selection_master_chk').prop('checked', false).prop('indeterminate', true);
                } else {
                    $('#chk_all_bench_list').prop('checked', false).prop('indeterminate', false);
                    $('#bench_selection_master_chk').prop('checked', false).prop('indeterminate', false);
                }
            }
            window.updateBenchSelectionBar = updateBenchSelectionBar;

            function clearBenchSelection() {
                $('.chk_list_item, #chk_all_bench_list, #bench_selection_master_chk').prop('checked', false).prop('indeterminate', false);
                $('#bench_selected_count').text('0');
                $('#bench_selected_count_badge').text('0');
                $('#bench_btn_count_badge').text('0/5');
                $('#bench_selected_text_wrap').html('<span id="bench_selected_count">0</span> Candidates Selected');
                $('#bench_selection_bar').removeClass('show').hide();
            }
            window.clearBenchSelection = clearBenchSelection;

            // Select all checkbox in table header or selection bar
            $(document).on('change', '#chk_all_bench_list, #bench_selection_master_chk', function() {
                var isChecked = $(this).is(':checked');
                $('.chk_list_item').prop('checked', isChecked);
                updateBenchSelectionBar();
            });

            // Card / Row-level checkbox toggle
            $(document).on('change', '.chk_list_item', function() {
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
                $('.chk_list_item:checked').each(function() {
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
