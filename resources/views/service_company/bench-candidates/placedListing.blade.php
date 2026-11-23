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

        /* ============================================================
           Talent Placed — Client Tracker & Card + Sub-Card Design System (Rule 20)
           15.6" Screen Fit (1366x768 & 1920x1080), Poppins, Max 13px/14px body
        ============================================================ */
        .kt_content_bench,
        #kt_content.kt_content_bench {
            margin-top: 0 !important;
            padding-top: 10px !important;
        }

        /* ── Tier 1: Toolbar Header Card (Client Tracker Standard) ── */
        .cl-header, .cl-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 14px 20px !important;
            margin-top: 0 !important;
            margin-bottom: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            flex-wrap: wrap !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            font-family: 'Poppins', sans-serif !important;
        }
        .cl-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
        }
        .cl-header-icon-box {
            width: 44px !important;
            height: 44px !important;
            border-radius: 10px !important;
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #2563eb !important;
            flex-shrink: 0 !important;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08) !important;
        }
        .cl-header-title-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        .cl-header-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: -0.015em !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }
        .cl-verified-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #16a34a !important;
        }
        .cl-verified-badge svg {
            color: #16a34a !important;
            stroke: #16a34a !important;
        }
        .cl-count-pill {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            padding: 2.5px 8.5px !important;
            border-radius: 6px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.01em !important;
        }
        .cl-header-subtitle {
            font-size: 13px !important;
            font-weight: 400 !important;
            color: #64748b !important;
            margin-top: 3px !important;
            line-height: 1.35 !important;
        }
        .cl-header-actions {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-shrink: 0 !important;
        }
        .cl-date-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }
        .cl-export-wrapper {
            position: relative !important;
            display: inline-block !important;
        }
        .cl-export-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            cursor: pointer !important;
            text-decoration: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
        }
        .cl-export-btn:hover, .cl-export-btn.show {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .cl-export-chevron {
            transition: transform 0.15s ease !important;
            margin-left: 2px !important;
        }
        .cl-export-btn.show .cl-export-chevron {
            transform: rotate(180deg) !important;
        }
        .cl-export-dropdown-menu {
            min-width: 270px !important;
            padding: 6px !important;
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
            background: #ffffff !important;
            margin-top: 6px !important;
            z-index: 1050 !important;
        }
        .cl-export-option {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            transition: all 0.12s ease !important;
            text-decoration: none !important;
            cursor: pointer !important;
        }
        .cl-export-option:hover {
            background: #f1f5f9 !important;
        }
        .cl-export-opt-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        .cl-export-opt-icon.csv {
            background: #eff6ff !important;
            color: #2563eb !important;
        }
        .cl-export-opt-icon.pdf {
            background: #fef2f2 !important;
            color: #dc2626 !important;
        }
        .cl-export-opt-text {
            display: flex !important;
            flex-direction: column !important;
            line-height: 1.25 !important;
        }
        .cl-export-opt-title {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
        }
        .cl-export-opt-sub {
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #64748b !important;
            margin-top: 2px !important;
        }

        /* ── Tier 2: 6 Interactive Stat Cards Grid (Client Tracker Layout) ── */
        .cl-stat-grid {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 8px !important;
            margin-bottom: 14px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        @media (max-width: 1200px) {
            .cl-stat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 768px) {
            .cl-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        .cl-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 10px 11px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        .cl-card:hover {
            transform: translateY(-1px);
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            text-decoration: none;
        }
        .cl-card.active {
            text-decoration: none;
        }
        .cl-card-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            min-width: 0;
        }
        .cl-card-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
            transition: all 0.15s ease;
        }
        .cl-card-icon.green  { background: #ecfdf5; color: #16a34a; }
        .cl-card-icon.blue   { background: #eff6ff; color: #2563eb; }
        .cl-card-icon.purple { background: #faf5ff; color: #9333ea; }
        .cl-card-icon.orange { background: #fff7ed; color: #ea580c; }
        .cl-card-icon.cyan   { background: #f0fdfa; color: #0d9488; }
        .cl-card-icon.rose   { background: #fdf2f8; color: #db2777; }

        .cl-card-meta {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }
        .cl-card-num {
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .cl-card-lbl {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .cl-card-sub {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 500;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .cl-card-sub.green  { color: #16a34a; }
        .cl-card-sub.blue   { color: #2563eb; }
        .cl-card-sub.purple { color: #9333ea; }
        .cl-card-sub.orange { color: #ea580c; }
        .cl-card-sub.cyan   { color: #0d9488; }
        .cl-card-sub.rose   { color: #db2777; }

        /* KPI Active Selected States */
        .cl-card.active[data-tab="working"] {
            background: #ecfdf5 !important;
            border-color: #34d399 !important;
            box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
        }
        .cl-card.active[data-tab="completed"] {
            background: #eff6ff !important;
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
        }
        .cl-card.active[data-tab="replacement"] {
            background: #faf5ff !important;
            border-color: #c084fc !important;
            box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
        }
        .cl-card.active[data-tab="joining"] {
            background: #fff7ed !important;
            border-color: #fb923c !important;
            box-shadow: 0 0 0 1.5px #ea580c, 0 4px 14px rgba(234, 88, 12, 0.12) !important;
        }
        .cl-card.active[data-tab="available"] {
            background: #f0fdfa !important;
            border-color: #2dd4bf !important;
            box-shadow: 0 0 0 1.5px #0d9488, 0 4px 14px rgba(13, 148, 136, 0.12) !important;
        }
        .cl-card.active[data-tab="companies"] {
            background: #fdf2f8 !important;
            border-color: #f472b6 !important;
            box-shadow: 0 0 0 1.5px #db2777, 0 4px 14px rgba(219, 39, 119, 0.12) !important;
        }

        /* ── Tier 2.5: Search & Filter Bar (Client Tracker Parity) ── */
        .cl-filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .cl-search-box {
            position: relative;
            flex: 1;
            min-width: 240px;
        }
        .cl-search-box svg.cl-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: #94a3b8;
            pointer-events: none;
        }
        .cl-search-inp {
            width: 100%;
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 100px 0 38px;
            font-size: 12.5px;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .cl-search-inp:focus {
            border-color: #009ef7;
            box-shadow: 0 0 0 2.5px rgba(0, 158, 247, 0.15);
        }
        .cl-search-inp::placeholder {
            color: #94a3b8;
        }
        .cl-search-right-affix {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cl-search-count-pill {
            font-size: 10.5px;
            font-weight: 600;
            background: #eff6ff;
            color: #009ef7;
            border: 1px solid #dbeafe;
            padding: 2px 7px;
            border-radius: 6px;
            white-space: nowrap;
            line-height: 1.2;
        }
        .cl-search-clear-btn {
            border: none;
            background: #e2e8f0;
            color: #64748b;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            transition: all 0.15s ease;
        }
        .cl-search-clear-btn:hover {
            background: #cbd5e1;
            color: #0f172a;
        }
        .cl-search-kbd-hint {
            font-size: 9.5px;
            font-weight: 600;
            font-family: inherit;
            color: #94a3b8;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 5px;
            line-height: 1;
            user-select: none;
        }
        @media (max-width: 1400px) {
            .cl-search-kbd-hint { display: none !important; }
            .cl-search-inp { padding: 0 75px 0 36px; }
        }

        /* ── Metronic Sort & Status Combobox Popovers ── */
        .cl-sort-dropdown-wrapper, .cl-status-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }
        .cl-sort-combobox-btn, .cl-status-combobox-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 12px;
            min-width: 155px;
            max-width: 240px;
            cursor: pointer;
            user-select: none;
            transition: all 0.15s ease;
            position: relative;
            font-family: inherit;
            outline: none;
        }
        .cl-sort-combobox-btn:hover, .cl-sort-combobox-btn.active,
        .cl-status-combobox-btn:hover, .cl-status-combobox-btn.active {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .cl-pill-icon {
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .cl-pill-texts {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            flex: 1;
            min-width: 0;
        }
        .cl-pill-lbl {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
            text-transform: capitalize;
        }
        .cl-pill-val {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cl-pill-chevron {
            color: #94a3b8;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            margin-left: 2px;
        }
        .cl-sort-dropdown-panel, .cl-status-dropdown-panel {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            width: 275px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
            z-index: 1055;
            display: none;
            flex-direction: column;
            overflow: hidden;
            padding: 6px 0;
            animation: clSortFadeIn 0.15s ease-out;
        }
        @keyframes clSortFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cl-sort-dropdown-panel.show, .cl-status-dropdown-panel.show {
            display: flex;
        }
        .cl-sort-section-hdr {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 6px 14px 4px 14px;
            user-select: none;
        }
        .cl-sort-section-hdr:not(:first-child) {
            border-top: 1px solid #f1f5f9;
            margin-top: 4px;
            padding-top: 8px;
        }
        .cl-sort-item, .cl-status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.12s ease;
            user-select: none;
        }
        .cl-sort-item:hover, .cl-status-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .cl-sort-item.selected, .cl-status-item.selected {
            background: #eff6ff;
            color: #009ef7;
            font-weight: 600;
        }
        .cl-sort-item-left, .cl-status-item-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            flex: 1;
        }
        .cl-sort-item-icon {
            color: #64748b;
            flex-shrink: 0;
        }
        .cl-sort-item:hover .cl-sort-item-icon,
        .cl-sort-item.selected .cl-sort-item-icon {
            color: #009ef7;
        }
        .cl-sort-direction-badge {
            font-size: 9.5px;
            font-weight: 600;
            padding: 1.5px 5.5px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #64748b;
        }
        .cl-sort-item.selected .cl-sort-direction-badge {
            background: #dbeafe;
            color: #0284c7;
        }
        .cl-sort-check {
            font-size: 11px;
            font-weight: 700;
            color: #009ef7;
            display: inline-flex;
            align-items: center;
            width: 14px;
            justify-content: center;
        }
        .cl-reset-filters-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 42px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .cl-reset-filters-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        /* ── Tier 3: Primary Placed Cards Container (Rule 20 Standard) ── */
        .tbl {
            padding: 0;
            margin-bottom: 24px;
            position: relative;
        }
        .tbl-w {
            background: transparent;
            border: none;
            box-shadow: none;
            border-radius: 0;
            overflow: visible;
        }
        .tbl-scroll-container {
            width: 100%;
            overflow: visible;
        }
        .cl-cards-stream {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        /* ── Individual Placed Card ── */
        .cl-primary-card.tbl-r {
            display: flex !important;
            flex-direction: column !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 10px 14px 8px 14px !important;
            margin-bottom: 2px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-sizing: border-box !important;
            width: 100% !important;
            min-width: 0 !important;
            position: relative !important;
        }
        .cl-primary-card.tbl-r:hover {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
            transform: translateY(-1px) !important;
        }
        .cl-primary-card.tbl-r.status-working { border-left: 3.5px solid #10b981 !important; }
        .cl-primary-card.tbl-r.status-completed { border-left: 3.5px solid #009ef7 !important; }
        .cl-primary-card.tbl-r.status-left { border-left: 3.5px solid #f43f5e !important; }

        /* Discrete Upper Sub-Cards Grid */
        .cl-subcards-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 1.2fr) minmax(0, 1.4fr) !important;
            gap: 8px !important;
            width: 100% !important;
            align-items: stretch !important;
        }
        @media (max-width: 1100px) {
            .cl-subcards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 767px) {
            .cl-subcards-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Individual Sub-Card Frame */
        .cl-subcard {
            background: #f8fafc !important;
            border: 1px solid #edf2f7 !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            position: relative !important;
        }
        .cl-subcard-head {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-bottom: 6px !important;
            margin-bottom: 6px !important;
            border-bottom: 1px solid #eef2f6 !important;
            background: transparent !important;
            min-width: 0 !important;
            gap: 6px !important;
        }
        .cl-subcard-title-wrap {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            min-width: 0 !important;
        }
        .cl-subcard-title {
            font-size: 10.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            color: #64748b !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .cl-subcard-badges {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            flex-shrink: 0 !important;
        }

        /* Rule 25 Canonical Scope Pills */
        .cl-scope-pill {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1.5px 6px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            white-space: nowrap !important;
        }
        .pill-direct, .type-direct {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
        }
        .pill-internal, .type-internal {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
        }
        .pill-my-org, .type-my-org {
            background: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fcd34d !important;
        }

        /* Bottom Activity Strip */
        .cl-card-bottom-strip {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-top: 8px !important;
            margin-top: 8px !important;
            border-top: 1px solid #f1f5f9 !important;
            font-size: 11.5px !important;
            color: #64748b !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .cl-bottom-meta {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }
        .cl-meta-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-size: 11.5px !important;
        }
        .cl-meta-item strong {
            color: #1e293b !important;
        }
        .cl-meta-dot {
            color: #cbd5e1 !important;
            font-size: 10px !important;
        }
        .cl-bottom-actions {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .cl-btn-act {
            width: 30px !important;
            height: 30px !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #64748b !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.12s ease !important;
            padding: 0 !important;
            font-size: 12px !important;
        }
        .cl-btn-act:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        /* Timeline and badges */
        .placed-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }
        .placed-skill-badge {
            background: #f4f4f8; color: #5e6278; font-weight: 400; font-size: 9.5px;
            margin-right: 2px;
        }
        .fs-8 { font-size: 10.5px !important; }
        .fs-9 { font-size: 9.5px !important; }

        .placed-timeline { width: 100%; min-width: 170px; }
        .placed-timeline-track { display: flex; align-items: flex-start; }
        .placed-timeline-node-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .placed-timeline-node { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .placed-timeline-node.is-static { background: #fff; border: 2px solid #e4e6ef; }
        .placed-timeline-node.is-done { background: #17c653; border: 2px solid #17c653; }
        .placed-timeline-node-label { font-size: 8px; font-weight: 600; color: #7e8299; margin-top: 3px; white-space: nowrap; }
        .placed-timeline-line { flex: 1; height: 2px; background: #e4e6ef; min-width: 10px; margin: 3.5px 1px 0; }
        .placed-timeline-line.is-done { background: #17c653; }
        .placed-timeline-labels { display: flex; justify-content: space-between; margin-top: 1px; }
        .placed-timeline-labels span { font-size: 8px; color: #a1a5b7; }

        .placed-status-pill {
            display: inline-flex; align-items: center; width: fit-content;
            font-size: 10px; font-weight: 600; padding: .2rem .5rem; border-radius: .4rem;
        }
        .placed-status-pill.is-success { background: #e8fff3; color: #17c653; border: 1px solid #bbf7d0; }
        .placed-status-pill.is-info { background: #eef3ff; color: #009ef7; border: 1px solid #bfdbfe; }
        .placed-status-pill.is-danger { background: #fff5f8; color: #f1416c; border: 1px solid #fecdd3; }

        .placed-change-link {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: 10px; font-weight: 600; color: #009ef7; margin-top: 2px; width: fit-content;
        }
        .placed-change-link i { font-size: 8.5px; }
        .placed-change-link:hover { color: #0077b5; }

        .placed-row.row-reverb-updated, .placed-card.row-reverb-updated {
            transition: background-color 0.6s ease;
            background-color: rgba(59, 91, 219, 0.08) !important;
        }

        /* ── Dedicated Executive Print-to-PDF Engine ── */
        @media print {
            body * { visibility: hidden !important; }
            #cl-print-report-canvas, #cl-print-report-canvas * { visibility: visible !important; }
            #cl-print-report-canvas {
                position: absolute !important;
                left: 0 !important; top: 0 !important;
                width: 100% !important; display: block !important;
                background: #ffffff !important; color: #0f172a !important;
                font-family: 'Poppins', sans-serif !important;
                padding: 10px 15px !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm 10mm 8mm 10mm;
            }
            .print-report-header {
                border-bottom: 2px solid #009ef7;
                padding-bottom: 8px; margin-bottom: 10px;
                display: flex; justify-content: space-between; align-items: flex-end;
            }
            .print-report-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
            .print-report-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
            .print-report-meta { text-align: right; font-size: 9px; color: #475569; line-height: 1.4; }
            .print-kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 12px; }
            .print-kpi-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; background: #f8fafc; }
            .print-kpi-num { font-size: 15px; font-weight: 700; color: #0f172a; }
            .print-kpi-lbl { font-size: 8.5px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
            .print-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
            .print-table th { background-color: #f1f5f9; color: #1e293b; font-weight: 700; padding: 5px 7px; border-bottom: 2px solid #cbd5e1; text-align: left; }
            .print-table td { padding: 5px 7px; border-bottom: 1px solid #e2e8f0; color: #334155; }
            .print-table tr { page-break-inside: avoid !important; }
            .print-footer { margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 8.5px; color: #94a3b8; }
        }

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
    @if (in_array('view_bench', $userPermissions))
        <div class="content d-flex flex-column flex-column-fluid kt_content_bench pt-2" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-fluid px-3">

                    <!-- ── TIER 1: TOOLBAR HEADER CARD (Client Tracker Design Standard) ── -->
                    <div class="cl-header cl-header-card">
                        <div class="cl-header-left">
                            <div class="cl-header-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <polyline points="16 11 18 13 22 9"/>
                                </svg>
                            </div>
                            <div>
                                <div class="cl-header-title-row">
                                    <h1 class="cl-header-title">Talent Placed</h1>
                                    <span class="cl-verified-badge" title="Verified ATS Placement Engine">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                    </span>
                                    <span class="cl-count-pill" id="totalPlaceBadge">
                                        <span id="totalPlace">0</span> Placed
                                    </span>
                                </div>
                                <div class="cl-header-subtitle">Track bench candidates who got placed and their project / placement journey.</div>
                            </div>
                        </div>

                        <div class="cl-header-actions">
                            {{-- Date range display pill --}}
                            <div class="cl-date-pill" id="placedDateRangePill" title="Placement Filter Period">
                                <i class="fa-solid fa-calendar-days text-primary"></i>
                                <span id="placedDateRangeLabel">This Month</span>
                            </div>

                            {{-- Export Dropdown (Client Tracker Standard with CSV + Executive PDF Print) --}}
                            <div class="cl-export-wrapper" id="clExportWrapper">
                                <button type="button" class="cl-export-btn" id="clExportBtn" onclick="toggleExportDropdown(event)" aria-haspopup="true" aria-expanded="false" title="Export placement records">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    <span>Export</span>
                                    <svg class="cl-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div class="cl-export-dropdown-menu" id="clExportDropdownMenu" style="display:none;">
                                    <a href="javascript:void(0);" class="cl-export-option" onclick="exportPlacedCandidatesCSV()">
                                        <div class="cl-export-opt-icon csv">
                                            <i class="fa-solid fa-file-csv fs-5"></i>
                                        </div>
                                        <div class="cl-export-opt-text">
                                            <span class="cl-export-opt-title">Export Records (CSV)</span>
                                            <span class="cl-export-opt-sub">Spreadsheet download of active placements</span>
                                        </div>
                                    </a>
                                    <a href="javascript:void(0);" class="cl-export-option" onclick="printPlacedPlacementReport()">
                                        <div class="cl-export-opt-icon pdf">
                                            <i class="fa-solid fa-file-pdf fs-5"></i>
                                        </div>
                                        <div class="cl-export-opt-text">
                                            <span class="cl-export-opt-title">Print Executive Report (PDF)</span>
                                            <span class="cl-export-opt-sub">A4 Landscape Placement Portfolio Summary</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── TIER 2: 6 INTERACTIVE STAT CARDS GRID (Client Tracker KPI Standard) ── -->
                    <div class="cl-stat-grid" id="placedStatGrid">
                        {{-- Card 1: Currently Working --}}
                        <div class="cl-card cl-stat-filter" data-tab="working" onclick="setPlacedTab('working', this)" title="Filter by Currently Working">
                            <div class="cl-card-top">
                                <div class="cl-card-icon green">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatWorking">--</div>
                                    <div class="cl-card-lbl">Currently Working</div>
                                </div>
                            </div>
                            <div class="cl-card-sub green">
                                <i class="fa-solid fa-arrow-up"></i>
                                <span>Active Assignments</span>
                            </div>
                        </div>

                        {{-- Card 2: Projects Completed --}}
                        <div class="cl-card cl-stat-filter" data-tab="completed" onclick="setPlacedTab('completed', this)" title="Filter by Projects Completed">
                            <div class="cl-card-top">
                                <div class="cl-card-icon blue">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatCompleted">--</div>
                                    <div class="cl-card-lbl">Projects Completed</div>
                                </div>
                            </div>
                            <div class="cl-card-sub blue">
                                <i class="fa-solid fa-arrow-up"></i>
                                <span>Total Completed</span>
                            </div>
                        </div>

                        {{-- Card 3: Replacement Active --}}
                        <div class="cl-card cl-stat-filter" data-tab="replacement" onclick="setPlacedTab('replacement', this)" title="Filter by Replacement Active">
                            <div class="cl-card-top">
                                <div class="cl-card-icon purple">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatReplacement">--</div>
                                    <div class="cl-card-lbl">Replacement Active</div>
                                </div>
                            </div>
                            <div class="cl-card-sub purple">
                                <i class="fa-solid fa-arrow-down"></i>
                                <span>Under Review</span>
                            </div>
                        </div>

                        {{-- Card 4: Joining This Week --}}
                        <div class="cl-card cl-stat-filter" data-tab="joining" onclick="setPlacedTab('joining', this)" title="Filter by Joining This Week">
                            <div class="cl-card-top">
                                <div class="cl-card-icon orange">
                                    <i class="fa-solid fa-calendar-week"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatJoining">--</div>
                                    <div class="cl-card-lbl">Joining This Week</div>
                                </div>
                            </div>
                            <div class="cl-card-sub orange">
                                <i class="fa-regular fa-clock"></i>
                                <span>Next 7 Days</span>
                            </div>
                        </div>

                        {{-- Card 5: Available Soon --}}
                        <div class="cl-card cl-stat-filter" data-tab="available" onclick="setPlacedTab('available', this)" title="Filter by Available Soon (< 30 Days)">
                            <div class="cl-card-top">
                                <div class="cl-card-icon cyan">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatAvailable">--</div>
                                    <div class="cl-card-lbl">Available Soon</div>
                                </div>
                            </div>
                            <div class="cl-card-sub cyan">
                                <i class="fa-solid fa-arrow-up"></i>
                                <span>Rolling Off (&lt; 30d)</span>
                            </div>
                        </div>

                        {{-- Card 6: Companies Served --}}
                        <div class="cl-card cl-stat-filter" data-tab="companies" onclick="setPlacedTab('companies', this)" title="View Companies Served">
                            <div class="cl-card-top">
                                <div class="cl-card-icon rose">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="cl-card-meta">
                                    <div class="cl-card-num" id="placedStatCompanies">--</div>
                                    <div class="cl-card-lbl">Companies Served</div>
                                </div>
                            </div>
                            <div class="cl-card-sub rose">
                                <i class="fa-solid fa-arrow-up"></i>
                                <span>Client Network</span>
                            </div>
                        </div>
                    </div>

                    <!-- ── TIER 2.5: SEARCH & FILTER BAR (Client Tracker UX Standard) ── -->
                    <div class="cl-filter-bar">
                        {{-- Enterprise Micro-Search Bar --}}
                        <div class="cl-search-box" id="clSearchBoxWrapper">
                            <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input 
                                type="text" 
                                class="cl-search-inp" 
                                name="inputJobSearch" 
                                value="" 
                                placeholder="Search placed talents by candidate name, job title, client, recruiter, skills..." 
                                id="kt_tagify_2" 
                                autocomplete="off" 
                                spellcheck="false" 
                                aria-label="Search talent placements" 
                                role="searchbox"
                            />
                            <div class="cl-search-right-affix">
                                <span class="cl-search-count-pill" id="clSearchMatchCount" style="display:none;" title="Matching placement records">0 matches</span>
                                <button type="button" class="cl-search-clear-btn" id="clSearchClearBtn" title="Clear search (Esc)" aria-label="Clear search" style="display:none;" onclick="clearPlacedSearchInput(event)">
                                    ✕
                                </button>
                                <kbd class="cl-search-kbd-hint" id="clSearchKbdHint" title="Press Ctrl+K to search">Ctrl+K</kbd>
                            </div>
                        </div>

                        {{-- Sort Combobox Popover (Enterprise Metronic Standard) --}}
                        <div class="cl-sort-dropdown-wrapper" id="clSortDropdownWrapper">
                            <button type="button" class="cl-sort-combobox-btn" id="clSortBtn" onclick="toggleSortDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clSortDropdownPanel" title="Sort Placement Listing">
                                <div class="cl-pill-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="4" x2="7" y2="20"/>
                                        <polyline points="3 8 7 4 11 8"/>
                                        <line x1="17" y1="4" x2="17" y2="20"/>
                                        <polyline points="13 16 17 20 21 16"/>
                                    </svg>
                                </div>
                                <div class="cl-pill-texts">
                                    <span class="cl-pill-lbl">Sort</span>
                                    <span class="cl-pill-val" id="sortDisplay">Newest Offer Date</span>
                                </div>
                                <div class="cl-pill-chevron">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </button>

                            <div class="cl-sort-dropdown-panel" id="clSortDropdownPanel" role="listbox" aria-labelledby="clSortBtn" tabindex="-1">
                                <div class="cl-sort-section-hdr">Offer Date</div>
                                <div class="cl-sort-item selected" id="sort-opt-10" data-sort-val="10" onclick="selectSortOption('10', 'Newest Offer Date', event)" role="option" aria-selected="true" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-regular fa-calendar-check cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">Newest to Oldest</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">Offer ↓</span>
                                        <span class="cl-sort-check">✓</span>
                                    </div>
                                </div>
                                <div class="cl-sort-item" id="sort-opt-11" data-sort-val="11" onclick="selectSortOption('11', 'Oldest Offer Date', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-regular fa-calendar cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">Oldest to Newest</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">Offer ↑</span>
                                        <span class="cl-sort-check" style="display:none;">✓</span>
                                    </div>
                                </div>

                                <div class="cl-sort-section-hdr">Join Date</div>
                                <div class="cl-sort-item" id="sort-opt-12" data-sort-val="12" onclick="selectSortOption('12', 'Newest Join Date', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-solid fa-user-check cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">Newest to Oldest</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">Join ↓</span>
                                        <span class="cl-sort-check" style="display:none;">✓</span>
                                    </div>
                                </div>
                                <div class="cl-sort-item" id="sort-opt-13" data-sort-val="13" onclick="selectSortOption('13', 'Oldest Join Date', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-solid fa-clock-rotate-left cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">Oldest to Newest</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">Join ↑</span>
                                        <span class="cl-sort-check" style="display:none;">✓</span>
                                    </div>
                                </div>

                                <div class="cl-sort-section-hdr">Commercials</div>
                                <div class="cl-sort-item" id="sort-opt-15" data-sort-val="15" onclick="selectSortOption('15', 'Offer Amount: High to Low', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-solid fa-arrow-down-wide-short cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">High to Low</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">₹ High ↓</span>
                                        <span class="cl-sort-check" style="display:none;">✓</span>
                                    </div>
                                </div>
                                <div class="cl-sort-item" id="sort-opt-14" data-sort-val="14" onclick="selectSortOption('14', 'Offer Amount: Low to High', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-sort-item-left">
                                        <i class="fa-solid fa-arrow-up-wide-short cl-sort-item-icon"></i>
                                        <span class="cl-sort-item-title">Low to High</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cl-sort-direction-badge">₹ Low ↑</span>
                                        <span class="cl-sort-check" style="display:none;">✓</span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="sortingfilter" name="sortingfilter" value="10" />
                        </div>

                        {{-- Status Filter Combobox Popover --}}
                        <div class="cl-status-dropdown-wrapper" id="clStatusDropdownWrapper">
                            <button type="button" class="cl-status-combobox-btn" id="clStatusBtn" onclick="toggleStatusDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clStatusDropdownPanel" title="Filter by Work Status">
                                <div class="cl-pill-icon">
                                    <i class="fa-solid fa-circle-dot text-primary fs-8"></i>
                                </div>
                                <div class="cl-pill-texts">
                                    <span class="cl-pill-lbl">Work Status</span>
                                    <span class="cl-pill-val" id="statusDisplay">All Statuses</span>
                                </div>
                                <div class="cl-pill-chevron">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </button>

                            <div class="cl-status-dropdown-panel" id="clStatusDropdownPanel" role="listbox" aria-labelledby="clStatusBtn" tabindex="-1">
                                <div class="cl-sort-section-hdr">Assignment Status</div>
                                <div class="cl-status-item selected" id="status-opt-all" data-status-val="all" onclick="selectStatusOption('all', 'All Statuses', event)" role="option" aria-selected="true" tabindex="0">
                                    <div class="cl-status-item-left">
                                        <i class="fa-solid fa-layer-group cl-sort-item-icon"></i>
                                        <span>All Statuses</span>
                                    </div>
                                    <span class="cl-sort-check">✓</span>
                                </div>
                                <div class="cl-status-item" id="status-opt-working" data-status-val="working" onclick="selectStatusOption('working', 'Currently Working', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-status-item-left">
                                        <i class="fa-solid fa-circle-check text-success cl-sort-item-icon"></i>
                                        <span>Currently Working</span>
                                    </div>
                                    <span class="cl-sort-check" style="display:none;">✓</span>
                                </div>
                                <div class="cl-status-item" id="status-opt-completed" data-status-val="completed" onclick="selectStatusOption('completed', 'Project Completed', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-status-item-left">
                                        <i class="fa-solid fa-check-double text-info cl-sort-item-icon"></i>
                                        <span>Project Completed</span>
                                    </div>
                                    <span class="cl-sort-check" style="display:none;">✓</span>
                                </div>
                                <div class="cl-status-item" id="status-opt-left" data-status-val="left" onclick="selectStatusOption('left', 'Candidate Left', event)" role="option" aria-selected="false" tabindex="0">
                                    <div class="cl-status-item-left">
                                        <i class="fa-solid fa-circle-xmark text-danger cl-sort-item-icon"></i>
                                        <span>Candidate Left</span>
                                    </div>
                                    <span class="cl-sort-check" style="display:none;">✓</span>
                                </div>
                            </div>

                            <input type="hidden" id="statusFilter" name="statusFilter" value="all" />
                        </div>

                        {{-- Reset Filters Button --}}
                        <button type="button" class="cl-reset-filters-btn" id="clResetFiltersBtn" onclick="resetPlacedFilters()" style="display:none;" title="Reset all search & status filters">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Reset Filters</span>
                        </button>

                        {{-- Hidden inputs to preserve backward compatibility --}}
                        <input type="hidden" id="country" name="country" value="110" />
                    </div>

                    <!-- ── TIER 3: PLACED TALENTS CARDS STREAM (Rule 20 Modern Card + Sub-Card Standard) ── -->
                    <div class="tbl" id="placedListWrapper">
                        <div class="tbl-w">
                            <div class="tbl-scroll-container">
                                {{-- Stream Toolbar strip for master selection & live count summary --}}
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 mb-2" id="clStreamToolbar" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" id="checkAllPlacedCandidates" title="Select All Placed Candidates">
                                        </div>
                                        <span class="fs-9 fw-bold text-gray-600 text-uppercase" style="letter-spacing:0.04em;">Select All Placed Talents</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-light-primary fw-bold fs-9" id="clSelectedCountBadge" style="display:none;">0 selected</span>
                                        <span class="text-muted fs-9" id="clListSummary">Showing live placement records</span>
                                    </div>
                                </div>

                                <div class="loadingMessage loadingMessagebenchCandidatePlaceAjax" style="display: none; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 10; background: rgba(255,255,255,0.95); padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-weight: 600; color: #009ef7;">
                                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Processing...
                                </div>

                                <div id="benchCandidatePlaceAjax" class="cl-cards-stream">
                                    {{-- Placed Candidate Cards rendered dynamically via User RAM / JS --}}
                                </div>

                                <div id="paginationbenchCandidatePlace"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ── DEDICATED 100% CLIENT-SIDE EXECUTIVE PRINT-TO-PDF CANVAS ── -->
                    <div id="cl-print-report-canvas" class="d-none" aria-hidden="true">
                        <div class="print-report-header">
                            <div>
                                <h1 class="print-report-title">Talent Placed & Placement Portfolio Report</h1>
                                <div class="print-report-sub">{{ auth()->user()->company->company_name ?? 'JERO Enterprise ATS' }} • Confidential Placement Review</div>
                            </div>
                            <div class="print-report-meta">
                                <div id="printReportTimestamp">Date: </div>
                                <div id="printReportScope">Scope: Active Placed Candidates</div>
                                <div id="printReportTotal">Total: </div>
                            </div>
                        </div>

                        <div class="print-kpi-grid">
                            <div class="print-kpi-card">
                                <div class="print-kpi-num" id="printKpiWorking">0</div>
                                <div class="print-kpi-lbl">Currently Working</div>
                            </div>
                            <div class="print-kpi-card">
                                <div class="print-kpi-num" id="printKpiCompleted">0</div>
                                <div class="print-kpi-lbl">Completed</div>
                            </div>
                            <div class="print-kpi-card">
                                <div class="print-kpi-num" id="printKpiReplacement">0</div>
                                <div class="print-kpi-lbl">Replacement Active</div>
                            </div>
                            <div class="print-kpi-card">
                                <div class="print-kpi-num" id="printKpiCompanies">0</div>
                                <div class="print-kpi-lbl">Companies Served</div>
                            </div>
                            <div class="print-kpi-card">
                                <div class="print-kpi-num" id="printKpiTotalPlaced">0</div>
                                <div class="print-kpi-lbl">Total Placed</div>
                            </div>
                        </div>

                        <table class="print-table">
                            <thead>
                                <tr>
                                    <th style="width:22%;">Candidate</th>
                                    <th style="width:20%;">Client & Job</th>
                                    <th style="width:18%;">Placement Timeline</th>
                                    <th style="width:15%;">Commercials</th>
                                    <th style="width:13%;">Work Status</th>
                                    <th style="width:12%;">Recruiter</th>
                                </tr>
                            </thead>
                            <tbody id="printReportTableBody">
                            </tbody>
                        </table>

                        <div class="print-footer">
                            <span>Generated by JERO Talent Engine • Confidential Placement Audit</span>
                            <span>Page 1 of 1</span>
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
                var query = ($(this).val() || '').trim();
                if (query.length > 0) {
                    $('#clSearchClearBtn').show();
                } else {
                    $('#clSearchClearBtn').hide();
                }
                checkFilterResetVisibility();

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    fetchBenchBenchCandidatesCandidateList(1);
                }, 300);
            });

            // Global Keyboard Shortcuts (Ctrl+K to search, Esc to close/clear)
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    $('#kt_tagify_2').focus().select();
                } else if (e.key === 'Escape') {
                    closeAllPopovers();
                    if ($('#kt_tagify_2').is(':focus') && $('#kt_tagify_2').val()) {
                        clearPlacedSearch();
                    }
                }
            });

            // Close popovers on click outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#clSortDropdownWrapper, #clStatusDropdownWrapper, #clExportDropdownWrapper').length) {
                    closeAllPopovers();
                }
            });

            // Master checkbox toggle for placed cards stream
            $(document).on('change', '#checkAllPlacedCandidates', function() {
                var isChecked = $(this).is(':checked');
                $('.chk_list_item').prop('checked', isChecked);
                updateSelectionCounter();
            });

            // Individual candidate checkbox toggle
            $(document).on('change', '.chk_list_item', function() {
                updateSelectionCounter();
            });
        });

        function clearPlacedSearch() {
            $('#kt_tagify_2').val('').focus();
            $('#clSearchClearBtn').hide();
            checkFilterResetVisibility();
            fetchBenchBenchCandidatesCandidateList(1);
        }

        function closeAllPopovers() {
            $('#clSortDropdownWrapper').removeClass('is-open');
            $('#clSortBtn').attr('aria-expanded', 'false');
            $('#clStatusDropdownWrapper').removeClass('is-open');
            $('#clStatusBtn').attr('aria-expanded', 'false');
            $('#clExportDropdownWrapper').removeClass('is-open');
            $('#clExportBtn').attr('aria-expanded', 'false');
        }

        function checkFilterResetVisibility() {
            var hasSearch = ($('#kt_tagify_2').val() || '').trim().length > 0;
            var hasStatus = $('#statusFilter').val() && $('#statusFilter').val() !== 'all';
            var hasSort = $('#sortingfilter').val() && $('#sortingfilter').val() !== '';
            var hasActiveTab = $('.cl-card.cl-stat-filter.is-active').length > 0;

            if (hasSearch || hasStatus || hasSort || hasActiveTab) {
                $('#clResetFiltersBtn').show();
            } else {
                $('#clResetFiltersBtn').hide();
            }
        }

        function toggleSortDropdown(e) {
            if (e) e.stopPropagation();
            var $wrap = $('#clSortDropdownWrapper');
            var isOpen = $wrap.hasClass('is-open');
            closeAllPopovers();
            if (!isOpen) {
                $wrap.addClass('is-open');
                $('#clSortBtn').attr('aria-expanded', 'true');
            }
        }

        function selectSortOption(val, text, e) {
            if (e) e.stopPropagation();
            $('#sortDisplay').text(text);
            $('#sortingfilter').val(val);
            $('.cl-sort-item').removeClass('selected').attr('aria-selected', 'false');
            $('.cl-sort-item .cl-sort-check').hide();
            var targetOptId = '#sort-opt-' + (val || 'default');
            $(targetOptId).addClass('selected').attr('aria-selected', 'true');
            $(targetOptId + ' .cl-sort-check').show();
            $('#clSortDropdownWrapper').removeClass('is-open');
            $('#clSortBtn').attr('aria-expanded', 'false');
            checkFilterResetVisibility();
            fetchBenchBenchCandidatesCandidateList(1);
        }

        function toggleStatusDropdown(e) {
            if (e) e.stopPropagation();
            var $wrap = $('#clStatusDropdownWrapper');
            var isOpen = $wrap.hasClass('is-open');
            closeAllPopovers();
            if (!isOpen) {
                $wrap.addClass('is-open');
                $('#clStatusBtn').attr('aria-expanded', 'true');
            }
        }

        function selectStatusOption(val, text, e) {
            if (e) e.stopPropagation();
            $('#statusDisplay').text(text);
            $('#statusFilter').val(val);
            $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
            $('.cl-status-item .cl-sort-check').hide();
            var targetOptId = '#status-opt-' + val;
            $(targetOptId).addClass('selected').attr('aria-selected', 'true');
            $(targetOptId + ' .cl-sort-check').show();
            $('#clStatusDropdownWrapper').removeClass('is-open');
            $('#clStatusBtn').attr('aria-expanded', 'false');

            // Sync active KPI card if matching
            $('.cl-card.cl-stat-filter').removeClass('is-active');
            if (val === 'working') {
                $('.cl-card.cl-stat-filter[data-tab="working"]').addClass('is-active');
            } else if (val === 'completed') {
                $('.cl-card.cl-stat-filter[data-tab="completed"]').addClass('is-active');
            } else if (val === 'left') {
                $('.cl-card.cl-stat-filter[data-tab="replacement"]').addClass('is-active');
            }

            checkFilterResetVisibility();
            applyClientSideFilters();
        }

        function setPlacedTab(tab, el) {
            var $card = $(el).closest('.cl-card.cl-stat-filter');
            var wasActive = $card.hasClass('is-active');

            $('.cl-card.cl-stat-filter').removeClass('is-active');

            if (wasActive || tab === 'all') {
                // Toggle off to all
                $('#statusFilter').val('all');
                $('#statusDisplay').text('All Statuses');
                $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
                $('.cl-status-item .cl-sort-check').hide();
                $('#status-opt-all').addClass('selected').attr('aria-selected', 'true');
                $('#status-opt-all .cl-sort-check').show();
            } else {
                $card.addClass('is-active');
                if (tab === 'working') {
                    $('#statusFilter').val('working');
                    $('#statusDisplay').text('Currently Working');
                    $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
                    $('.cl-status-item .cl-sort-check').hide();
                    $('#status-opt-working').addClass('selected').attr('aria-selected', 'true');
                    $('#status-opt-working .cl-sort-check').show();
                } else if (tab === 'completed') {
                    $('#statusFilter').val('completed');
                    $('#statusDisplay').text('Project Completed');
                    $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
                    $('.cl-status-item .cl-sort-check').hide();
                    $('#status-opt-completed').addClass('selected').attr('aria-selected', 'true');
                    $('#status-opt-completed .cl-sort-check').show();
                } else if (tab === 'replacement') {
                    $('#statusFilter').val('left');
                    $('#statusDisplay').text('Candidate Left');
                    $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
                    $('.cl-status-item .cl-sort-check').hide();
                    $('#status-opt-left').addClass('selected').attr('aria-selected', 'true');
                    $('#status-opt-left .cl-sort-check').show();
                } else {
                    $('#statusFilter').val(tab);
                }
            }

            checkFilterResetVisibility();
            applyClientSideFilters();
        }

        function applyClientSideFilters() {
            var statusVal = $('#statusFilter').val() || 'all';
            var allCandidates = window.PlacedListingStore.candidates || [];

            if (!allCandidates || allCandidates.length === 0) {
                renderCandidatesCardsOnly([]);
                return;
            }

            var filtered = allCandidates;
            if (statusVal && statusVal !== 'all') {
                filtered = allCandidates.filter(function(cand) {
                    var assignStatus = (cand.assignment_status || 'working').toLowerCase();
                    if (statusVal === 'working') return assignStatus === 'working';
                    if (statusVal === 'completed') return assignStatus === 'completed';
                    if (statusVal === 'left') return assignStatus === 'left';
                    if (statusVal === 'replacement') return assignStatus === 'left' || parseInt(cand.assignment_replacement_required, 10) === 1;
                    if (statusVal === 'joining') {
                        return (cand.of_joined_date && String(cand.of_joined_date).indexOf('0000-00-00') !== 0);
                    }
                    if (statusVal === 'available') {
                        if (!cand.assignment_expected_end_date) return false;
                        var endD = new Date(cand.assignment_expected_end_date);
                        var diffDays = Math.ceil((endD - new Date()) / (1000 * 60 * 60 * 24));
                        return diffDays >= 0 && diffDays <= 30;
                    }
                    return true;
                });
            }

            if (statusVal !== 'all') {
                $('#clSearchMatchCount').show().text(filtered.length + ' matching');
            } else {
                $('#clSearchMatchCount').hide();
            }

            renderCandidatesCardsOnly(filtered);
        }

        function resetPlacedFilters() {
            $('#kt_tagify_2').val('');
            $('#clSearchClearBtn').hide();
            $('#clSearchMatchCount').hide();

            $('#sortDisplay').text('Default Sorting');
            $('#sortingfilter').val('');
            $('.cl-sort-item').removeClass('selected').attr('aria-selected', 'false');
            $('.cl-sort-item .cl-sort-check').hide();
            $('#sort-opt-default').addClass('selected').attr('aria-selected', 'true');
            $('#sort-opt-default .cl-sort-check').show();

            $('#statusDisplay').text('All Statuses');
            $('#statusFilter').val('all');
            $('.cl-status-item').removeClass('selected').attr('aria-selected', 'false');
            $('.cl-status-item .cl-sort-check').hide();
            $('#status-opt-all').addClass('selected').attr('aria-selected', 'true');
            $('#status-opt-all .cl-sort-check').show();

            $('.cl-card.cl-stat-filter').removeClass('is-active');
            $('#clResetFiltersBtn').hide();
            closeAllPopovers();

            fetchBenchBenchCandidatesCandidateList(1);
        }

        function toggleExportDropdown(e) {
            if (e) e.stopPropagation();
            var $wrap = $('#clExportDropdownWrapper');
            var isOpen = $wrap.hasClass('is-open');
            closeAllPopovers();
            if (!isOpen) {
                $wrap.addClass('is-open');
                $('#clExportBtn').attr('aria-expanded', 'true');
            }
        }

        function exportPlacedCandidatesCSV() {
            closeAllPopovers();
            var candidates = window.PlacedListingStore.candidates || [];
            if (!candidates.length) {
                Swal.fire({ icon: 'info', text: 'No placement records available to export.', timer: 2000, showConfirmButton: false });
                return;
            }

            var selectedIds = [];
            $('.chk_list_item:checked').each(function() {
                selectedIds.push(String($(this).val()));
            });

            var exportList = candidates;
            if (selectedIds.length > 0) {
                exportList = candidates.filter(function(c) {
                    return selectedIds.indexOf(String(c.candidate_id)) !== -1;
                });
            }

            var csvRows = [
                ['Candidate Name', 'Designation', 'Total Exp (Yrs)', 'Location', 'Skills', 'Client Name', 'Job Title', 'Job ID', 'Client Type', 'Offer CTC', 'Commercial Model', 'Placement Fee', 'Work Status', 'Expected End Date', 'Joined Date', 'Recruiter', 'Recruiter Agency']
            ];

            exportList.forEach(function(row) {
                var fullName = (row.full_name || '').trim();
                var designation = row.current_designation || '';
                var exp = row.total_exp || '';
                var city = row.city || row.city_name || '';
                var skills = row.skills || '';
                var clientName = row.client_name || '';
                var jobTitle = row.title || '';
                var jobId = row.jobId || '';

                var recId = parseInt(row.recommendation_id, 10) || 0;
                var fromCompId = parseInt(row.from_company_id, 10) || 0;
                var isDirectClient = (recId > 0 && fromCompId > 0 && fromCompId !== window.PlacedListingStore.companyId);
                var clientType = isDirectClient ? 'Direct Client' : (row.client_self_company == 1 ? 'My Organization' : 'Internal Client');

                var offerAmt = row.of_offer_amount || '0';
                var empType = row.of_employment_type || '';
                var fee = row.commercial_fee_fixed || (row.commercial_fee_pct ? row.commercial_fee_pct + '%' : '');
                var workStatus = row.assignment_status === 'left' ? 'Candidate Left' : (row.assignment_status === 'completed' ? 'Project Completed' : 'Currently Working');
                var endDate = row.assignment_expected_end_date || '';
                var joinedDate = row.of_joined_date || '';
                var recruiterName = row.posted_by || row.submitted_by_name || '';
                var agency = row.submitted_by_company_name || row.post_company_name || '';

                csvRows.push([
                    fullName, designation, exp, city, skills, clientName, jobTitle, jobId, clientType, offerAmt, empType, fee, workStatus, endDate, joinedDate, recruiterName, agency
                ].map(function(val) {
                    return '"' + String(val || '').replace(/"/g, '""') + '"';
                }));
            });

            var csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + csvRows.map(function(e) { return e.join(','); }).join('\n');
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            var now = new Date();
            var dateStamp = now.toISOString().slice(0, 10);
            link.setAttribute('download', 'talent-placed-report-' + dateStamp + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function printPlacedPlacementReport() {
            closeAllPopovers();
            var store = window.PlacedListingStore;
            var candidates = store.candidates || [];
            var stats = store.stats || {};

            $('#printReportTimestamp').text('Date: ' + new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }));
            $('#printReportTotal').text('Total: ' + (store.total || candidates.length) + ' Placed');

            $('#printKpiWorking').text(stats.currently_working || $('#placedStatWorking').text() || '0');
            $('#printKpiCompleted').text(stats.projects_completed || $('#placedStatCompleted').text() || '0');
            $('#printKpiReplacement').text(stats.replacement_active || $('#placedStatReplacement').text() || '0');
            $('#printKpiCompanies').text(stats.companies_served || $('#placedStatCompanies').text() || '0');
            $('#printKpiTotalPlaced').text(store.total || candidates.length || '0');

            var bodyHtml = '';
            candidates.forEach(function(row) {
                var fullName = escapeHtml((row.full_name || '').trim());
                var designation = escapeHtml(row.current_designation || '-');
                var clientName = escapeHtml(row.client_name || '-');
                var jobTitle = escapeHtml(row.title || '-');
                var offerDisplay = row.confidential == 1 ? 'Confidential' : ('₹' + Math.round(parseFloat(row.of_offer_amount) || 0).toLocaleString('en-IN') + '/A');
                var workStatus = row.assignment_status === 'left' ? 'Candidate Left' : (row.assignment_status === 'completed' ? 'Project Completed' : 'Currently Working');
                var joinedDate = formatDisplayDate(row.of_joined_date);
                var recruiter = escapeHtml(row.posted_by || row.submitted_by_name || 'Recruiter');

                bodyHtml += '<tr>' +
                    '<td><strong>' + fullName + '</strong><br><small style="color:#64748b;">' + designation + '</small></td>' +
                    '<td><strong>' + clientName + '</strong><br><small style="color:#64748b;">' + jobTitle + '</small></td>' +
                    '<td>Joined: ' + joinedDate + '</td>' +
                    '<td>' + offerDisplay + '</td>' +
                    '<td>' + workStatus + '</td>' +
                    '<td>' + recruiter + '</td>' +
                '</tr>';
            });

            if (!bodyHtml) {
                bodyHtml = '<tr><td colspan="6" class="text-center py-4" style="color:#64748b;">No placement records to print</td></tr>';
            }

            $('#printReportTableBody').html(bodyHtml);
            window.print();
        }

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
                        // Update work status cell in the matching row/card
                        var $row = $('.placed-card[data-candidate-id="' + candidateId + '"], tr.placed-row[data-candidate-id="' + candidateId + '"]');
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
                    var $row = $('.placed-card[data-candidate-id="' + candidateId + '"], tr.placed-row[data-candidate-id="' + candidateId + '"]');
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
                    var $row = $('.placed-card[data-candidate-id="' + candidateId + '"], tr.placed-row[data-candidate-id="' + candidateId + '"]');
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

        // =========================================================================
        // TIER 3: MODERN CARD + SUB-CARD RENDERER (Rule 20 & Client Tracker Standard)
        // 4 Discrete Sub-Cards + Bottom Activity Strip (0% Layout Thrashing, Pure Client-First)
        // =========================================================================
        function renderCandidateCard(row) {
            var store = window.PlacedListingStore;
            var companyId = store.companyId;

            // 1. Candidate Info & Avatar
            var fullName = (row.full_name || '').trim();
            var strVal = fullName.length > 0 ? fullName[0].toUpperCase() : '';
            var bgClass = getAvatarClass(fullName);
            var flag = (row.is_duplicate || row.duplicate == 1) ? 1 : 0;
            var isBench = (row.condidate_type === 'bench_condidate');
            var tredmark = isBench ? '<span class="cl-bench-pill">Bench</span>' : '';
            var profileSidebarCss = 'show-application-detail';
            var nameImg = '';

            if (row.photo) {
                nameImg = '<img src="' + escapeHtml(row.photo) + '" alt="' + escapeHtml(fullName) + '" class="cl-avatar-img">' +
                          '<div class="position-absolute translate-middle bottom-0 start-100 mb-1 bg-success rounded-circle border border-2 border-white h-10px w-10px"></div>';
            } else {
                nameImg = '<div class="cl-avatar-text placed-avatar ' + bgClass + '">' + escapeHtml(strVal) + '</div>';
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
            var designation = row.current_designation || '-';

            // 2. Timeline
            var timelineHtml = isBench ? renderBenchTimeline(row) : renderDirectTimeline(row);

            // 3. Client & Job Mandate (Rule 25 Canonical Tags)
            var clientName = row.client_name || '-';
            var jobTitle = row.title || '-';
            var jobLocation = row.location || [row.city_name, row.state_name].filter(Boolean).join(', ');

            var recId = parseInt(row.recommendation_id, 10) || 0;
            var fromCompId = parseInt(row.from_company_id, 10) || 0;
            var isDirectClient = (recId > 0 && fromCompId > 0 && fromCompId !== companyId);
            var isSelfCompany = (row.client_self_company == 1);

            var recBadgeHtml = '';
            if (isDirectClient) {
                recBadgeHtml = '<span class="cl-tag-direct"><i class="fa-solid fa-shield-alt me-1"></i>Direct Client</span>';
            } else if (isSelfCompany) {
                recBadgeHtml = '<span class="cl-tag-myorg"><i class="fa-solid fa-id-badge me-1"></i>My Organization</span>';
            } else {
                recBadgeHtml = '<span class="cl-tag-internal"><i class="fa-solid fa-building me-1"></i>Internal Client</span>';
            }

            // 4. Recruiter
            var agencyCompName = row.submitted_by_company_name || row.post_company_name || row.from_company_name || row.uc_company_name || '';
            var agencyCompId = parseInt(row.thirdparty_company_id || row.other_company || 0, 10);
            var isAgency = (agencyCompId > 0 && agencyCompId !== companyId) || !!agencyCompName;
            var recruiterName = isAgency ? (row.agency_root_name || row.posted_by || row.submitted_by_name || 'Recruiter') : (row.posted_by || row.submitted_by_name || row.spokperson_name || 'Recruiter');
            var recruiterSub = isAgency ? (agencyCompName || row.company_name || 'Recruiter') : 'Inhouse Recruiter';

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
            var joinedDisplay = formatDisplayDate(row.of_joined_date);
            var offerDateDisplay = formatDisplayDate(row.offer_released_at || row.of_created_date || row.of_offer_release);

            var benchRaw = row.created_at || '';
            var durationDays = calcDurationDays(benchRaw, row.of_joined_date);
            var turnaroundPill = durationDays !== null ? ('<span class="cl-strip-badge"><i class="fa-solid fa-bolt text-warning me-1"></i>' + durationDays + 'd Turnaround</span>') : '';

            return '<div class="cl-primary-card tbl-r placed-card" data-candidate-id="' + escapeHtml(row.id) + '">' +
                // Upper 4 Discrete Sub-Cards Grid
                '<div class="cl-subcards-grid">' +
                    // Sub-Card 1: Candidate Profile
                    '<div class="cl-subcard cl-subcard-candidate">' +
                        '<div class="cl-subcard-hdr">' +
                            '<span class="cl-subcard-title"><i class="fa-solid fa-user me-1 text-primary"></i>Candidate Profile</span>' +
                            tredmark +
                        '</div>' +
                        '<div class="d-flex align-items-start gap-3 mt-1">' +
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden position-relative flex-shrink-0">' +
                                '<a href="javascript:void(0);" data-row-id="' + escapeHtml(row.wiseId || '') + '" class="' + profileSidebarCss + '">' + nameImg + '</a>' +
                            '</div>' +
                            '<div class="d-flex flex-column min-w-0 flex-grow-1">' +
                                '<a href="javascript:void(0);" data-row-id="' + escapeHtml(row.wiseId || '') + '" class="' + nameLinkClass + ' fw-bolder text-hover-primary fs-6 ' + profileSidebarCss + ' text-truncate" data-toggle="tooltip" title="' + escapeHtml(fullName) + '">' + escapeHtml(fullName) + '</a>' +
                                '<span class="text-muted fw-bold fs-8 text-truncate" data-toggle="tooltip" title="' + escapeHtml(designation) + '">' + escapeHtml(designation) + '</span>' +
                                '<span class="text-muted fw-semibold fs-9 mt-1"><i class="fa-solid fa-location-dot me-1 text-muted"></i>' + (expLocLine || 'Experience not specified') + '</span>' +
                                '<div class="cl-candidate-skills-wrap mt-2">' + skilltag + '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                    // Sub-Card 2: Client & Job Mandate
                    '<div class="cl-subcard cl-subcard-client">' +
                        '<div class="cl-subcard-hdr">' +
                            '<span class="cl-subcard-title"><i class="fa-solid fa-briefcase me-1 text-primary"></i>Client & Mandate</span>' +
                            recBadgeHtml +
                        '</div>' +
                        '<div class="d-flex flex-column gap-1 mt-1">' +
                            '<div class="d-flex align-items-center gap-2">' +
                                '<i class="fa-solid fa-building text-muted fs-8"></i>' +
                                '<span class="text-dark fw-bolder fs-7 text-truncate" data-toggle="tooltip" title="' + escapeHtml(clientName) + '">' + escapeHtml(clientName) + '</span>' +
                            '</div>' +
                            '<div class="d-flex align-items-center gap-2">' +
                                '<i class="fa-solid fa-id-badge text-muted fs-8"></i>' +
                                '<span class="text-dark fw-bold fs-8 text-truncate" data-toggle="tooltip" title="' + escapeHtml(jobTitle) + '">' + escapeHtml(jobTitle) + '</span>' +
                            '</div>' +
                            (jobLocation ? '<div class="d-flex align-items-center gap-2 text-muted fs-9"><i class="fa-solid fa-location-dot text-muted fs-8"></i><span class="text-truncate">' + escapeHtml(jobLocation) + '</span></div>' : '') +
                            '<div class="d-flex align-items-center gap-2 mt-1">' +
                                '<span class="badge badge-light fw-bold fs-9 text-muted">Req: #' + escapeHtml(row.jobId || 'N/A') + '</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                    // Sub-Card 3: Commercials & Recruiter
                    '<div class="cl-subcard cl-subcard-commercials">' +
                        '<div class="cl-subcard-hdr">' +
                            '<span class="cl-subcard-title"><i class="fa-solid fa-sack-dollar me-1 text-primary"></i>Commercials</span>' +
                            '<a href="javascript:void(0);" class="cl-subcard-action-link commercial-change-btn"' +
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
                                ' title="Configure Commercials"><i class="fa-solid fa-pen-to-square me-1"></i>Configure</a>' +
                        '</div>' +
                        '<div class="d-flex flex-column gap-1 mt-1">' +
                            '<div class="d-flex align-items-baseline justify-content-between">' +
                                '<span class="text-dark fw-bolder fs-6" style="font-variant-numeric:tabular-nums;">' + offerDisplay + '</span>' +
                                '<span class="badge badge-light-primary fw-bold fs-9" data-cc-model>' + escapeHtml(ccModelLabel) + '</span>' +
                            '</div>' +
                            '<div class="d-flex align-items-center gap-1 fs-9 mt-1">' +
                                '<i class="fa-solid fa-circle-check text-success"></i>' +
                                '<span class="text-dark fw-bold" data-cc-fee>' + escapeHtml(ccFeeLabel) + '</span>' +
                                '<span class="text-muted">' + marginLabel + '</span>' +
                            '</div>' +
                            '<div class="d-flex align-items-center gap-2 mt-2 pt-1 border-top border-gray-200">' +
                                '<div class="symbol symbol-24px symbol-circle bg-light-primary text-primary fs-9 fw-bold d-flex align-items-center justify-content-center">' +
                                    '<i class="fa-solid fa-user-tie fs-9"></i>' +
                                '</div>' +
                                '<div class="d-flex flex-column min-w-0">' +
                                    '<span class="text-dark fw-bold fs-9 text-truncate" data-toggle="tooltip" title="' + escapeHtml(recruiterName) + '">' + escapeHtml(recruiterName) + '</span>' +
                                    '<span class="text-muted fs-9 text-truncate" data-toggle="tooltip" title="' + escapeHtml(recruiterSub) + '">' + escapeHtml(recruiterSub) + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                    // Sub-Card 4: Placement Journey & Status
                    '<div class="cl-subcard cl-subcard-status">' +
                        '<div class="cl-subcard-hdr">' +
                            '<span class="cl-subcard-title"><i class="fa-solid fa-route me-1 text-primary"></i>Journey & Status</span>' +
                            '<a href="javascript:void(0);" class="cl-subcard-action-link assignment-change-btn"' +
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
                                ' title="Change Assignment Status"><i class="fa-solid fa-pen-to-square me-1"></i>Update</a>' +
                        '</div>' +
                        '<div class="d-flex flex-column gap-2 mt-1">' +
                            '<div class="d-flex align-items-center justify-content-between">' +
                                '<span class="placed-status-pill ' + adStatus.cls + '" data-ad-status>' +
                                    '<i class="fa-solid ' + adStatus.icon + ' me-1" data-ad-status-icon></i><span data-ad-status-label>' + adStatus.label + '</span>' +
                                '</span>' +
                                '<div class="d-flex align-items-center gap-1 fs-9 text-muted">' +
                                    '<i class="fa-regular fa-calendar-days"></i>' +
                                    '<span>End: </span><strong class="text-dark" data-ad-end-date>' + escapeHtml(adEndDateDisp) + '</strong>' +
                                '</div>' +
                            '</div>' +
                            '<div class="placed-card-timeline-box">' +
                                timelineHtml +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +

                // Discrete Bottom Activity & Action Strip (Rule 20)
                '<div class="cl-card-bottom-strip">' +
                    '<div class="cl-strip-left">' +
                        '<div class="form-check form-check-sm form-check-custom form-check-solid me-1">' +
                            '<input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" data-jobId="' + escapeHtml(row.jobId || '') + '" value="' + escapeHtml(row.candidate_id || '') + '" title="Select Candidate" />' +
                        '</div>' +
                        '<span class="cl-strip-badge"><i class="fa-regular fa-calendar-check text-success me-1"></i>Joined: ' + (joinedDisplay !== '--' ? joinedDisplay : 'Pending') + '</span>' +
                        (offerDateDisplay !== '--' ? '<span class="cl-strip-badge"><i class="fa-solid fa-file-signature text-primary me-1"></i>Offer: ' + offerDateDisplay + '</span>' : '') +
                        turnaroundPill +
                    '</div>' +
                    '<div class="cl-strip-right">' +
                        '<button type="button" class="btn btn-sm btn-light-primary px-3 py-1 fs-9 fw-bold stage_sub_stage" data-row-id="' + escapeHtml(row.wiseId || '') + '" title="View complete placement stage audit">' +
                            '<i class="fa-solid fa-diagram-project me-1"></i>Journey Status' +
                        '</button>' +
                        '<div class="dropdown">' +
                            '<button class="btn btn-sm btn-icon btn-light placed-action-btn" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">' +
                                '<i class="fa-solid fa-ellipsis-vertical fs-6"></i>' +
                            '</button>' +
                            '<ul class="dropdown-menu dropdown-menu-end shadow-sm fs-8">' +
                                '<li><a href="javascript:void(0);" class="dropdown-item stage_sub_stage" data-row-id="' + escapeHtml(row.wiseId || '') + '"><i class="fa-solid fa-diagram-project me-2 text-primary"></i>View Placement Status</a></li>' +
                                '<li><a href="javascript:void(0);" class="dropdown-item show-application-detail" data-row-id="' + escapeHtml(row.wiseId || '') + '"><i class="fa-solid fa-id-card me-2 text-info"></i>Candidate Full Profile</a></li>' +
                                '<li><hr class="dropdown-divider"></li>' +
                                '<li><a href="javascript:void(0);" class="dropdown-item text-danger placed-row-delete-btn" data-candidate-name="' + escapeHtml(fullName) + '"><i class="fa-solid fa-trash-can me-2"></i>Delete Record</a></li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        // Backward-compatible alias
        function renderCandidateRow(row) {
            return renderCandidateCard(row);
        }

        function renderCandidatesTable(candidates) {
            if (!candidates || candidates.length === 0) {
                var emptyHtml = '<div class="cl-empty-state">' +
                    '<div class="cl-empty-icon"><i class="fa-solid fa-user-slash"></i></div>' +
                    '<div class="cl-empty-title">No Placed Talents Found</div>' +
                    '<div class="cl-empty-text">No candidate placement records match your search or filter criteria.</div>' +
                    '<button type="button" class="btn btn-sm btn-light-primary mt-3" onclick="resetPlacedFilters()">' +
                        '<i class="fa-solid fa-rotate-left me-1"></i>Reset Filters' +
                    '</button>' +
                '</div>';
                $('#benchCandidatePlaceAjax').html(emptyHtml);
                $('#clListSummary').text('Showing 0 placement records');
                return;
            }

            var cardsHtml = '';
            for (var i = 0; i < candidates.length; i++) {
                cardsHtml += renderCandidateCard(candidates[i]);
            }
            $('#benchCandidatePlaceAjax').html(cardsHtml);

            var store = window.PlacedListingStore;
            var from = (store.page - 1) * store.perPage + 1;
            var to = Math.min(store.page * store.perPage, store.total);
            $('#clListSummary').text('Showing ' + from + ' to ' + to + ' of ' + store.total + ' records');

            updateSelectionCounter();

            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }

        function renderCandidatesCardsOnly(candidates) {
            if (!candidates || candidates.length === 0) {
                var emptyHtml = '<div class="cl-empty-state">' +
                    '<div class="cl-empty-icon"><i class="fa-solid fa-user-slash"></i></div>' +
                    '<div class="cl-empty-title">No Placed Talents Found</div>' +
                    '<div class="cl-empty-text">No candidate placement records match your active status filter.</div>' +
                    '<button type="button" class="btn btn-sm btn-light-primary mt-3" onclick="resetPlacedFilters()">' +
                        '<i class="fa-solid fa-rotate-left me-1"></i>Reset Filters' +
                    '</button>' +
                '</div>';
                $('#benchCandidatePlaceAjax').html(emptyHtml);
                return;
            }

            var cardsHtml = '';
            for (var i = 0; i < candidates.length; i++) {
                cardsHtml += renderCandidateCard(candidates[i]);
            }
            $('#benchCandidatePlaceAjax').html(cardsHtml);

            updateSelectionCounter();

            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }

        function updateSelectionCounter() {
            var totalSelected = $('.chk_list_item:checked').length;
            var totalVisible = $('.chk_list_item').length;

            if (totalSelected > 0) {
                $('#clSelectedCountBadge').show().text(totalSelected + ' selected');
            } else {
                $('#clSelectedCountBadge').hide();
            }

            $('#checkAllPlacedCandidates').prop('checked', totalVisible > 0 && totalSelected === totalVisible);
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

            // 3. Find target candidate row/card in DOM
            var $row = $('.placed-card, tr.placed-row').filter(function() {
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
