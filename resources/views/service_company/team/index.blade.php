@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        body {
            overflow-x: hidden;
        }
        /* Team's toolbar is in-flow (not fixed) — cancel the wrapper's reserved
           top padding that Metronic adds for a fixed toolbar, else it leaves a
           blank gap above the "Team" header. */
        body#kt_body {
            --kt-toolbar-height: 0px !important;
            --kt-toolbar-height-tablet-and-mobile: 0px !important;
        }
    </style>
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        @media only screen and (min-width: 900px) and (max-width: 2560px) {
            .table td, .table th, .table tr { width: max-content !important; }
            .table> :not(caption)>*>* { padding: 0rem; }
            td:nth-child(n+2), th:nth-child(n+2) { width: revert !important; }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) { #kt_activitiesJob { width: 1025px; } }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) { #kt_activitiesJob { width: 1200px; } }
        #load_jobs tbody tr { height: 82px; position: relative; }
    </style>

    <style>
        #kt_content_container,
        #kt_toolbar_container {
            font-family: 'Poppins', sans-serif;
            color: #1e2129;
        }

        /* Remove any shadow bleeding near the scrollbar from Metronic wrappers */
        #kt_wrapper,
        #kt_content,
        #kt_post,
        #kt_content_container,
        .wrapper,
        .content {
            box-shadow: none !important;
        }

        /* =========================================================================
           Modern Card + Sub-Card Enterprise Design System (Rule 20 / Team Redesign)
           ========================================================================= */

        /* Tier 1: Toolbar Header Card */
        .team-toolbar-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }
        .team-toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .team-toolbar-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #eff6ff;
            color: #009ef7;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .team-toolbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }
        .team-toolbar-subtitle {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .team-badge-pill {
            background: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            margin-left: 8px;
        }
        .team-refresh-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            margin-left: 4px;
        }
        .team-refresh-btn:hover {
            background: #f8fafc;
            color: #009ef7;
            border-color: #009ef7;
        }

        /* Tier 1 Right Action Area */
        .team-toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .team-search-subcard {
            position: relative;
            display: flex;
            align-items: center;
            width: 280px;
            max-width: 100%;
            height: 36px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all .2s;
        }
        .team-search-subcard:focus-within {
            background: #fff;
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        }
        .team-search-icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            font-size: 13px;
            pointer-events: none;
        }
        .team-search-input {
            width: 100%;
            height: 100%;
            border: none;
            background: transparent;
            padding: 0 32px 0 34px;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            outline: none;
        }
        .team-search-input::placeholder { color: #94a3b8; }
        .team-search-clear {
            position: absolute;
            right: 8px;
            width: 20px;
            height: 20px;
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .team-search-clear:hover { background: #e2e8f0; color: #0f172a; }

        .team-btn-primary {
            height: 36px;
            padding: 0 16px;
            background: #009ef7;
            border: 1px solid #009ef7;
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }
        .team-btn-primary:hover { background: #0086d1; border-color: #0086d1; color: #fff; }

        .team-btn-light {
            height: 36px;
            padding: 0 14px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }
        .team-btn-light:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

        /* =========================================================================
           TIER 2: 6 STAT CARDS ROW (Card-Based Tabs — Perfectly Balanced Enterprise Standard)
           ========================================================================= */
        .tm-stat-grid-wrap {
            width: 100% !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            margin-bottom: 14px !important;
        }
        .tm-stat-grid-wrap::-webkit-scrollbar {
            display: none !important;
        }
        .tm-stat-grid {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 10px !important;
            width: 100% !important;
            min-width: 820px !important;
            box-sizing: border-box !important;
        }
        .tm-stat-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 10px 12px 9px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            text-decoration: none !important;
            color: inherit !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.18s cubic-bezier(.4, 0, .2, 1) !important;
            cursor: pointer !important;
            min-width: 0 !important;
            overflow: hidden !important;
            position: relative !important;
        }
        .tm-stat-card:hover {
            transform: translateY(-2px) !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.07) !important;
        }
        .tm-stat-card.active {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.18), 0 3px 8px rgba(0, 158, 247, 0.12) !important;
            background: #ffffff !important;
        }
        .tm-stat-card.active .tm-card-num {
            color: #009ef7 !important;
        }
        .tm-card-top {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            min-width: 0 !important;
        }
        .tm-card-icon {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            font-size: 13.5px !important;
            transition: transform 0.15s ease !important;
        }
        .tm-stat-card:hover .tm-card-icon {
            transform: scale(1.06);
        }
        .tm-card-icon.blue   { background: #eff6ff; color: #009ef7; }
        .tm-card-icon.indigo { background: #eef2ff; color: #4f46e5; }
        .tm-card-icon.green  { background: #f0fdf4; color: #16a34a; }
        .tm-card-icon.purple { background: #faf5ff; color: #9333ea; }
        .tm-card-icon.pink   { background: #fff1f2; color: #e11d48; }
        .tm-card-icon.cyan   { background: #ecfeff; color: #0891b2; }

        .tm-card-meta {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            flex: 1 1 0% !important;
            overflow: hidden !important;
        }
        .tm-card-num {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.2 !important;
            font-variant-numeric: tabular-nums !important;
            font-family: 'Poppins', sans-serif !important;
            transition: color 0.15s ease !important;
        }
        .tm-card-lbl {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            line-height: 1.25 !important;
            margin-top: 1px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .tm-card-sub {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-size: 9.5px !important;
            font-weight: 600 !important;
            margin-top: 6px !important;
            padding-top: 5px !important;
            border-top: 1px dashed #f1f5f9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .tm-card-sub.blue   { color: #009ef7; }
        .tm-card-sub.indigo { color: #4f46e5; }
        .tm-card-sub.green  { color: #16a34a; }
        .tm-card-sub.purple { color: #9333ea; }
        .tm-card-sub.pink   { color: #e11d48; }
        .tm-card-sub.cyan   { color: #0891b2; }

        /* =========================================================================
           TIER 2.5: SEARCH & FILTER BAR (Pure White Card Standard)
           ========================================================================= */
        .team-filter-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .team-filter-search {
            position: relative;
            flex: 1;
            min-width: 260px;
            max-width: 460px;
            display: flex;
            align-items: center;
        }
        .team-filter-search i.fa-magnifying-glass {
            position: absolute;
            left: 10px;
            font-size: 13px;
            color: #94a3b8;
            pointer-events: none;
        }
        .team-filter-search .team-search-input {
            width: 100%;
            height: 34px;
            padding: 0 32px 0 32px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-size: 12.5px;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            outline: none;
            transition: all .15s;
            background: #f8fafc;
        }
        .team-filter-search .team-search-input:focus {
            background: #ffffff;
            border-color: #009ef7;
            box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
        }
        .team-search-clear {
            position: absolute;
            right: 8px;
            width: 20px;
            height: 20px;
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 13px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .team-search-clear:hover { background: #e2e8f0; color: #0f172a; }

        .team-filter-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .team-filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .team-filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .team-select-control {
            height: 34px;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            background: #ffffff;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
            padding: 0 10px;
            outline: none;
            cursor: pointer;
            transition: all .15s;
        }
        .team-select-control:focus {
            border-color: #009ef7;
        }

        /* =========================================================================
           TIER 3: ROSTER COLUMN LEGEND & CARD STREAM (8 Discrete Columns Aligned)
           ========================================================================= */
        .team-roster-wrap {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .team-roster-inner {
            min-width: 1020px;
        }

        /* Mathematical 7-Column Grid Definition (Shared by Legend & Cards) */
        .team-roster-legend,
        .team-card-grid {
            display: grid !important;
            grid-template-columns: minmax(210px, 2.2fr) minmax(85px, 0.9fr) minmax(185px, 1.8fr) minmax(115px, 1.1fr) minmax(220px, 2.2fr) minmax(95px, 1fr) minmax(105px, 1fr) !important;
            gap: 12px !important;
            align-items: center !important;
            box-sizing: border-box !important;
        }

        .team-roster-legend {
            border: 1px solid transparent;
            padding: 6px 18px !important;
            margin-bottom: 8px !important;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tm-owner-tag {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            color: #b45309;
            margin: 1px 0;
            line-height: 1.2;
            white-space: nowrap;
        }

        .team-cards-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .team-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all .2s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden;
            box-sizing: border-box;
        }
        .team-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        }
        .team-card-grid {
            padding: 12px 18px !important;
            min-height: 64px;
        }

        /* Discrete Cell Elements */
        .tm-cell {
            min-width: 0;
            overflow: hidden;
        }

        /* Col 1: Member Identity */
        .tm-cell-member {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .tm-avatar {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
            overflow: hidden;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .tm-avatar:hover {
            transform: scale(1.04);
            box-shadow: 0 2px 6px rgba(0, 158, 247, 0.2);
        }
        .tm-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .tm-member-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
            transition: color .15s;
        }
        .tm-member-name:hover { color: #009ef7; }
        .tm-member-email {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        /* Col 2: Rank & Score */
        .tm-cell-rank {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }
        .tm-rank-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            display: flex;
            align-items: center;
        }
        .tm-rank-month {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 500;
            margin-left: 3px;
        }
        .tm-btn-view-score {
            background: #d1fae5;
            color: #065f46;
            border: none;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 10.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            line-height: 1.35;
            display: inline-flex;
            align-items: center;
            margin-top: 3px;
        }
        .tm-btn-view-score:hover {
            background: #a7f3d0;
            color: #047857;
        }

        /* Col 3: Workload */
        .tm-cell-workload {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 3px;
        }
        .tm-wl-row {
            font-size: 11px;
            color: #475569;
            white-space: nowrap;
            display: flex;
            align-items: center;
            line-height: 1.35;
        }
        .tm-wl-row i {
            width: 14px;
            text-align: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .tm-wl-row strong {
            color: #0f172a;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            margin-right: 2px;
        }

        /* Col 4: Role & Permissions */
        .tm-cell-role {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .tm-role-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3.5px 9px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .role-admin   { background: #ede9fe; color: #6d28d9; }
        .role-lead    { background: #e0f2fe; color: #0284c7; }
        .role-default { background: #fef3c7; color: #b45309; }
        .tm-btn-role-edit {
            width: 22px;
            height: 22px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 10px;
            transition: all .15s;
        }
        .tm-btn-role-edit:hover {
            background: #eff6ff;
            color: #009ef7;
            border-color: #009ef7;
        }

        /* Col 5: Corporate Communication Purpose */
        .tm-cell-communication {
            display: flex;
            align-items: center;
            min-width: 0;
        }
        .tm-comm-chip {
            display: inline-flex;
            align-items: center;
            font-size: 10px;
            font-weight: 600;
            padding: 2.5px 6.5px;
            border-radius: 5px;
            line-height: 1.25;
            white-space: nowrap;
        }
        .tm-comm-chip.client      { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .tm-comm-chip.vendor      { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }
        .tm-comm-chip.agency      { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }
        .tm-comm-chip.recruitment { background: #e6fcf5; color: #059669; border: 1px solid #a7f3d0; }

        .tm-btn-edit-comm {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 9px;
            transition: all .15s;
            padding: 0;
            flex-shrink: 0;
        }
        .tm-btn-edit-comm:hover {
            background: #eff6ff;
            color: #009ef7;
            border-color: #009ef7;
        }

        .tm-btn-add-comm {
            font-size: 10.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
            background: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s;
            font-family: inherit;
        }
        .tm-btn-add-comm:hover {
            background: #eff6ff;
            color: #009ef7;
            border-color: #009ef7;
            border-style: solid;
        }

        .comm-channel-opt:hover {
            border-color: #009ef7 !important;
            background: #f8fafc !important;
        }

        /* Col 6: Status Toggle */
        .tm-cell-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tm-switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 18px;
            flex-shrink: 0;
            margin: 0;
        }
        .tm-switch input { opacity: 0; width: 0; height: 0; }
        .tm-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #cbd5e1;
            border-radius: 20px;
            transition: .2s;
        }
        .tm-slider:before {
            content: "";
            position: absolute;
            height: 12px;
            width: 12px;
            left: 3px;
            bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .2s;
        }
        .tm-switch input:checked + .tm-slider { background: #2563eb; }
        .tm-switch input:checked + .tm-slider:before { transform: translateX(16px); }
        .tm-switch input:disabled + .tm-slider { opacity: .6; cursor: not-allowed; }
        .tm-status-text {
            font-size: 11.5px;
            font-weight: 700;
        }

        /* Col 7: Last Active */
        .tm-cell-lastactive {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: #475569;
            font-weight: 500;
            white-space: nowrap;
        }
        .tm-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
        .dot-green { background: #10b981; }
        .dot-amber { background: #f59e0b; }
        .dot-gray  { background: #94a3b8; }

        /* Col 8: Row Actions */
        .tm-cell-actions {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Empty State */
        .team-empty-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
        }

        /* Pagination Bar */
        .team-pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 4px 6px 4px;
            font-size: 12px;
            color: #64748b;
            flex-wrap: wrap;
            gap: 12px;
        }
        .team-pagination-info {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .team-pagination-pages {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .team-page-link {
            min-width: 30px;
            height: 30px;
            padding: 0 6px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .team-page-link:hover:not(.is-disabled):not(.is-active) {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .team-page-link.is-active {
            background: #009ef7;
            border-color: #009ef7;
            color: #ffffff;
        }
        .team-page-link.is-disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f8fafc;
        }
        .team-page-dots {
            padding: 0 4px;
            color: #94a3b8;
            font-size: 12px;
        }

        /* Rep Selection Modal Items */
        .rep-picker-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 380px;
            overflow-y: auto;
            padding: 4px;
        }
        .rep-picker-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s;
        }
        .rep-picker-item:hover {
            border-color: #009ef7;
            background: #f8fafc;
        }
        .rep-picker-item.selected {
            border-color: #009ef7;
            background: #eff6ff;
        }
        .rep-picker-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .rep-picker-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .rep-picker-meta {
            font-size: 11.5px;
            color: #64748b;
        }

        /* Responsive Breakpoints & 15.6" Screen Optimization (Strictly 7 Cards on 1 Row) */
        @media (max-width: 1440px) {
            .tm-stat-grid {
                grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                gap: 5px !important;
            }
            .tm-stat-card {
                padding: 7px 5px !important;
            }
            .tm-card-top {
                gap: 5px !important;
            }
            .tm-card-icon {
                width: 26px !important;
                height: 26px !important;
                min-width: 26px !important;
                font-size: 11px !important;
            }
            .tm-card-num {
                font-size: 14px !important;
            }
            .tm-card-lbl {
                font-size: 9.5px !important;
            }
            .tm-card-sub {
                font-size: 8px !important;
            }
        }
        @media (max-width: 1150px) {
            .tm-stat-grid {
                grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                gap: 4px !important;
            }
            .tm-stat-card {
                padding: 6px 4px !important;
            }
            .tm-card-icon {
                width: 24px !important;
                height: 24px !important;
                min-width: 24px !important;
                font-size: 10px !important;
            }
            .tm-card-num {
                font-size: 13px !important;
            }
            .tm-card-lbl {
                font-size: 9px !important;
            }
            .tm-card-sub {
                font-size: 7.5px !important;
            }
        }

        /* ========== Invite Drawer ========== */
        .invite-overlay {
            position: fixed; inset: 0;
            background: rgba(15,20,40,.45);
            backdrop-filter: blur(2px);
            z-index: 1040;
            opacity: 0; pointer-events: none;
            transition: opacity .28s ease;
        }
        .invite-overlay.open { opacity: 1; pointer-events: all; }

        .invite-drawer {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: 520px;
            max-width: 100vw;
            background: #fff;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }
        .invite-drawer.open { transform: translateX(0); }

        /* Header */
        .invite-drawer-head {
            padding: 28px 32px 20px;
            border-bottom: 1px solid #f0f1f6;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .invite-drawer-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            margin: 0 0 4px;
        }
        .invite-drawer-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
        .invite-drawer-close {
            width: 34px; height: 34px;
            border-radius: 8px;
            border: none;
            background: #f3f4f6;
            color: #374151;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background .15s;
            flex-shrink: 0;
            margin-left: 16px;
            margin-top: 2px;
        }
        .invite-drawer-close:hover { background: #e5e7eb; color: #111; }

        /* Body */
        .invite-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* Field */
        .invite-field { margin-bottom: 22px; }
        .invite-field-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .invite-field-label .req { color: #ef4444; margin-left: 1px; }
        .invite-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .invite-input-icon {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 15px;
            pointer-events: none;
            line-height: 1;
        }
        .invite-input-wrap input,
        .invite-input-wrap select {
            width: 100%;
            height: 48px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0 16px 0 44px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            -webkit-appearance: none;
            appearance: none;
        }
        .invite-input-wrap input::placeholder,
        .invite-input-wrap select.placeholder-active { color: #9ca3af; }
        .invite-input-wrap input:focus,
        .invite-input-wrap select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }
        /* chevron for select */
        .invite-select-wrap { position: relative; }
        .invite-select-wrap::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 14px; height: 14px;
            background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'><path d='M6 9l6 6 6-6'/></svg>") center/contain no-repeat;
        }
        .invite-field-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 7px;
        }

        /* Footer */
        .invite-drawer-foot {
            padding: 20px 32px 28px;
            border-top: 1px solid #f0f1f6;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }
        .invite-btn-cancel {
            height: 44px;
            padding: 0 24px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #374151;
            cursor: pointer;
            transition: background .15s;
        }
        .invite-btn-cancel:hover { background: #f3f4f6; }
        .invite-btn-send {
            height: 44px;
            padding: 0 24px;
            border-radius: 10px;
            border: none;
            background: #6366f1;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: background .15s;
        }
        .invite-btn-send:hover { background: #4f46e5; }
        .invite-btn-send:disabled { opacity: .65; cursor: not-allowed; }
        .invite-spinner { display: none; }
        .invite-btn-send.loading .invite-label { display: none; }
        .invite-btn-send.loading .invite-spinner { display: inline-block; }

        @media (max-width: 1200px) {
            .team-stats-row { grid-template-columns: repeat(2, 1fr); }
            .team-workload { gap: 14px; }
        }
        @media (max-width: 768px) {
            .team-stats-row { grid-template-columns: 1fr; }
        }

        /* =========================================================================
           Assign Communication Contacts Right Drawer (Rule 20 Enterprise Standard)
           ========================================================================= */
        .comm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1040;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s ease;
        }
        .comm-overlay.open {
            opacity: 1;
            pointer-events: all;
        }
        .comm-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 560px;
            max-width: 100vw;
            background: #ffffff;
            box-shadow: -12px 0 35px -5px rgba(15, 23, 42, 0.15);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
            font-family: 'Poppins', sans-serif;
        }
        .comm-drawer.open {
            transform: translateX(0);
        }
        .comm-drawer-head {
            padding: 22px 26px 18px 26px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
        }
        .comm-drawer-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.3;
        }
        .comm-drawer-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 0 0;
            line-height: 1.45;
        }
        .comm-drawer-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            flex-shrink: 0;
            font-size: 15px;
        }
        .comm-drawer-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .comm-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 22px 26px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .comm-drawer-body::-webkit-scrollbar {
            width: 6px;
        }
        .comm-drawer-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .comm-drawer-body::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Target Member Profile Card inside Drawer */
        .comm-user-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }
        .comm-user-card-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .comm-user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .comm-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .comm-user-info {
            min-width: 0;
        }
        .comm-user-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .comm-role-badge {
            background: #eff6ff;
            color: #009ef7;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #dbeafe;
            white-space: nowrap;
        }
        .comm-user-contacts {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 4px;
            font-size: 11.5px;
            color: #64748b;
            flex-wrap: wrap;
        }
        .comm-user-card-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .comm-status-pill {
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #a7f3d0;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .comm-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
        }
        .comm-last-active-text {
            font-size: 11.5px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .comm-last-active-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        /* Section Head */
        .comm-section-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
        }
        .comm-section-subtitle {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .comm-btn-clear {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all .15s;
        }
        .comm-btn-clear:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* 4 Channel Cards */
        .comm-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 13px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            transition: all .2s ease;
        }
        .comm-card.is-active {
            border-color: #93c5fd;
            background: #f8fbff;
        }
        .comm-card-main {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }
        .comm-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .comm-card-icon.icon-client { background: #dcfce7; color: #16a34a; }
        .comm-card-icon.icon-agency { background: #f3e8ff; color: #9333ea; }
        .comm-card-icon.icon-recruitment { background: #e6fcf5; color: #00b96b; }

        .comm-card-text {
            min-width: 0;
        }
        .comm-card-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }
        .comm-card-desc {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.35;
            margin-top: 3px;
        }
        .comm-card-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        /* iOS Toggle Switch */
        .comm-ios-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            margin: 0;
            cursor: pointer;
        }
        .comm-ios-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .comm-ios-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .25s ease;
            border-radius: 24px;
        }
        .comm-ios-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .25s ease;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        .comm-ios-switch input:checked + .comm-ios-slider {
            background-color: #009ef7;
        }
        .comm-ios-switch input:checked + .comm-ios-slider:before {
            transform: translateX(20px);
        }

        /* Priority Dropdown */
        .comm-prio-select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2364748b'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 30px 6px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #1e293b;
            width: 116px;
            cursor: pointer;
            transition: all .15s ease;
        }
        .comm-prio-select:focus {
            outline: none;
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        }
        .comm-prio-select:disabled {
            background-color: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Info Alert Box */
        .comm-info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .comm-info-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #009ef7;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .comm-info-text {
            font-size: 12px;
            color: #1e40af;
            line-height: 1.45;
            font-weight: 400;
        }

        /* Drawer Footer */
        .comm-drawer-foot {
            padding: 16px 26px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }
        .comm-btn-cancel {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            padding: 9px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s ease;
        }
        .comm-btn-cancel:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #cbd5e1;
        }
        .comm-btn-save {
            background: #009ef7;
            border: 1px solid #009ef7;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            padding: 9px 22px;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2);
        }
        .comm-btn-save:hover {
            background: #008be0;
            border-color: #008be0;
        }
        .comm-btn-save:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
@endpush 
 
@section('content')
    @php
        $teamCid = (int) (auth()->user()->company_id ?? 0);
        if (!isset($teamCounts)) {
            $teamCounts = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController::class)->getTeamCounts($teamCid);
        }
        $defaultReps = $defaultRepresentatives ?? app(\App\Services\CompanyRepresentativeService::class)->getDefaultRepresentatives($teamCid);
        $clientRep = $defaultReps['client_rep'] ?? null;
        $agencyRep = $defaultReps['agency_rep'] ?? null;
    @endphp

    <div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <!-- ============================================================== -->
                <!-- TIER 1: TOP HEADER TOOLBAR (Matching Reference Standard)        -->
                <!-- ============================================================== -->
                <div class="team-toolbar-card mb-4">
                    <div class="team-toolbar-left">
                        <div class="team-toolbar-icon" style="background:#eff6ff; color:#009ef7;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <h1 class="team-toolbar-title">Team Members</h1>
                            </div>
                            <div class="team-toolbar-subtitle">Manage your team and configure who represents your company for external communication through Jero Marketplace.</div>
                        </div>
                    </div>

                    <div class="team-toolbar-right">
                        <!-- Action Button: Invite Member -->
                        <button type="button" class="team-btn-primary" id="openInviteDrawer">
                            <i class="fa-solid fa-plus me-1"></i>
                            <span>Invite Member</span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- TIER 2: 6 STAT CARDS ROW (Card-Based Tabs — Enterprise Grid)   -->
                <!-- ============================================================== -->
                <div class="tm-stat-grid-wrap mb-3">
                    <div class="tm-stat-grid">
                        {{-- Card 1: All Members --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('all', this);" class="tm-stat-card active" data-tab="all">
                            <div class="tm-card-top">
                                <div class="tm-card-icon blue">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamAll">{{ $teamCounts['total'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="All Members">All Members</div>
                                </div>
                            </div>
                            <div class="tm-card-sub blue">
                                <i class="fa-solid fa-arrow-up" style="font-size: 8px;"></i>
                                <span>+{{ max($teamCounts['total'] ?? 0, 1) }} registered</span>
                            </div>
                        </a>

                        {{-- Card 2: Recruitment Team --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('recruitment', this);" class="tm-stat-card" data-tab="recruitment">
                            <div class="tm-card-top">
                                <div class="tm-card-icon indigo">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamRecruitment">{{ $teamCounts['recruitment'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Recruitment Team">Recruitment Team</div>
                                </div>
                            </div>
                            <div class="tm-card-sub indigo">
                                <i class="fa-solid fa-user-check" style="font-size: 8px;"></i>
                                <span>Active recruiters</span>
                            </div>
                        </a>

                        {{-- Card 3: Client & Sales --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('client', this);" class="tm-stat-card" data-tab="client">
                            <div class="tm-card-top">
                                <div class="tm-card-icon green">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamClient">{{ $teamCounts['client'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Client & Sales">Client &amp; Sales</div>
                                </div>
                            </div>
                            <div class="tm-card-sub green">
                                <i class="fa-solid fa-handshake" style="font-size: 8px;"></i>
                                <span>Client contacts</span>
                            </div>
                        </a>

                        {{-- Card 4: Vendor & Agency --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('vendor', this);" class="tm-stat-card" data-tab="vendor">
                            <div class="tm-card-top">
                                <div class="tm-card-icon purple">
                                    <i class="fa-solid fa-handshake-angle"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamVendor">{{ $teamCounts['vendor'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Vendor & Agency">Vendor &amp; Agency</div>
                                </div>
                            </div>
                            <div class="tm-card-sub purple">
                                <i class="fa-solid fa-store" style="font-size: 8px;"></i>
                                <span>Agency partners</span>
                            </div>
                        </a>

                        {{-- Card 5: Inactive Members --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('inactive', this);" class="tm-stat-card" data-tab="inactive">
                            <div class="tm-card-top">
                                <div class="tm-card-icon pink">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamInactive">{{ $teamCounts['inactive'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Inactive Members">Inactive Members</div>
                                </div>
                            </div>
                            <div class="tm-card-sub pink">
                                <i class="fa-solid fa-pause" style="font-size: 8px;"></i>
                                <span>Offboarded/paused</span>
                            </div>
                        </a>

                        {{-- Card 6: Pending Invites --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('invited', this);" class="tm-stat-card" data-tab="invited">
                            <div class="tm-card-top">
                                <div class="tm-card-icon cyan">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamInvited">{{ $teamCounts['invited'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Pending Invites">Pending Invites</div>
                                </div>
                            </div>
                            <div class="tm-card-sub cyan">
                                <i class="fa-solid fa-clock" style="font-size: 8px;"></i>
                                <span>Awaiting signup</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- TIER 2.5: SEARCH & FILTER BAR                                  -->
                <!-- ============================================================== -->
                <div class="team-filter-bar mb-3">
                    <div class="team-filter-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="kt_tagify_2" class="team-search-input" placeholder="Search by name, email or phone..." autocomplete="off" />
                        <button type="button" class="team-search-clear" onclick="clearTeamFilters()" title="Clear Search">✕</button>
                    </div>

                    <div class="team-filter-controls">
                        <!-- Hidden input to maintain JS status filter compatibility -->
                        <input type="hidden" id="teamStatusFilterDropdown" value="all" />

                        <div class="team-filter-group">
                            <span class="team-filter-label"><i class="fa-solid fa-arrow-down-short-wide me-1 text-muted"></i>Sort by:</span>
                            <select class="team-select-control" id="sortingfilter" onchange="fetchTeams(1)">
                                <option value="">Last Active</option>
                                <option value="1">Newest to Oldest</option>
                                <option value="2">Oldest to Newest</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- TIER 3: ROSTER COLUMN LEGEND & CARD STREAM (7 Columns Aligned) -->
                <!-- ============================================================== -->
                <div class="team-roster-wrap">
                    <div class="team-roster-inner">
                        <div class="team-roster-legend">
                            <div>Member</div>
                            <div>Rank</div>
                            <div>Workload</div>
                            <div>Role</div>
                            <div>Communication Purpose</div>
                            <div>Status</div>
                            <div>Last Active</div>
                        </div>

                        <div class="position-relative">
                            <div class="loadingMessage loadingMessageteamsListajax" style="display: none; position: absolute; z-index: 20; background: rgba(255,255,255,0.85); inset: 0; padding: 40px; text-align: center; font-weight: 600; color: #009ef7; border-radius: 10px;">
                                <span class="spinner-border spinner-border-sm me-2"></span> Loading team members...
                            </div>
                            
                            <!-- Dynamic Stream Container Populated via AJAX -->
                            <div id="teamsListajax"></div>
                            
                            <!-- Pagination Bar -->
                            <div id="paginationTeams" class="mt-2"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--end::Content-->
        <!--end::Content-->

        {{-- Invite Member Drawer --}}
        <div class="invite-overlay" id="inviteOverlay"></div>
        <div class="invite-drawer" id="inviteDrawer" role="dialog" aria-modal="true" aria-label="Invite Team Member">
            {{-- Header --}}
            <div class="invite-drawer-head">
                <div>
                    <p class="invite-drawer-title">Invite Team Member</p>
                    <p class="invite-drawer-subtitle">Add a new team member and set their role &amp; permissions</p>
                </div>
                <button class="invite-drawer-close" id="inviteDrawerClose" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="invite-drawer-body">
                <form id="inviteForm" enctype="multipart/form-data">
                    @csrf

                    {{-- Name --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Name <span class="req">*</span></label>
                        <div class="invite-input-wrap">
                            <span class="invite-input-icon"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="name" id="inviteName" placeholder="Enter full name" autocomplete="off" />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Email <span class="req">*</span></label>
                        <div class="invite-input-wrap">
                            <span class="invite-input-icon"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" id="inviteEmail" placeholder="Enter email address" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" />
                        </div>
                        <div id="inviteEmail_typo_hint" class="team-email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
                    </div>

                    {{-- Role --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Role &amp; Permissions <span class="req">*</span></label>
                        <div class="invite-input-wrap invite-select-wrap">
                            <span class="invite-input-icon"><i class="fa-solid fa-shield-halved"></i></span>
                            <select name="role_id" id="inviteRole">
                                <option value="" disabled selected>Select role</option>
                                {{-- Populated by AJAX --}}
                                <div id="inviteRoleOptions"></div>
                            </select>
                        </div>
                        <p class="invite-field-hint">Role will define the permissions the user will have in the system.</p>
                    </div>

                    {{-- Message area --}}
                    <div id="inviteMsg" style="display:none; font-size:13px; padding:10px 14px; border-radius:8px; margin-top:4px;"></div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="invite-drawer-foot">
                <button class="invite-btn-cancel" id="inviteCancelBtn">Cancel</button>
                <button class="invite-btn-send" id="inviteSubmitBtn">
                    <span class="invite-label"><i class="fa-solid fa-paper-plane me-1"></i> Send Invitation</span>
                    <span class="invite-spinner"><span class="spinner-border spinner-border-sm"></span> Sending...</span>
                </button>
            </div>
        </div>
        {{-- End Invite Drawer --}}

        <!--begin::Edit Role Drawer-->
        <div id="editRoleOverlay" onclick="closeEditRoleDrawer()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(2px); z-index:1050;"></div>

        <div id="editRoleDrawer" style="
            position:fixed; top:0; right:0; width:520px; max-width:100vw; height:100vh;
            background:#fff; z-index:1055; display:flex; flex-direction:column;
            transform:translateX(100%); transition:transform .3s cubic-bezier(.4,0,.2,1);">

            {{-- Drawer Head --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; padding:22px 24px 18px; border-bottom:1px solid #e5e7eb; flex-shrink:0;">
                <div>
                    <div style="font-size:17px; font-weight:700; color:#111827; font-family:'Poppins',sans-serif; margin-bottom:4px;">Change User Role</div>
                    <div style="font-size:13px; color:#6b7280; font-family:'Poppins',sans-serif;">Update role and access permissions for this user.</div>
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-left:16px;">
                    <button type="button" onclick="closeEditRoleDrawer()" style="width:34px; height:34px; border-radius:8px; border:1px solid #e5e7eb; background:#f9fafb; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#6b7280;" title="Close">
                        <i class="fa-solid fa-xmark" style="font-size:14px;"></i>
                    </button>
                </div>
            </div>

            {{-- Drawer Body (AJAX loaded) --}}
            <div id="editRoleDrawerBody" style="flex:1; overflow-y:auto; padding:24px;">
                {{-- content injected by AJAX --}}
            </div>

            {{-- Drawer Footer --}}
            <div id="editRoleDrawerFooter" style="display:flex; gap:12px; justify-content:flex-end; padding:16px 24px; border-top:1px solid #e5e7eb; flex-shrink:0; background:#fff;">
                <button type="button" onclick="closeEditRoleDrawer()" style="padding:10px 22px; border-radius:9px; border:1.5px solid #d1d5db; background:#fff; font-size:13px; font-weight:600; color:#374151; font-family:'Poppins',sans-serif; cursor:pointer;">Cancel</button>
                <button type="button" id="updateRoleSubmitBtn" onclick="submitEditRole()" style="padding:10px 22px; border-radius:9px; border:none; background:#6366f1; font-size:13px; font-weight:600; color:#fff; font-family:'Poppins',sans-serif; cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-check"></i> Update Role
                </button>
            </div>
        </div>
        <!--end::Edit Role Drawer-->


        <div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header flex-stack">
                        <h2>Requisition Status</h2>
                        <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                            id="status_sub_stage_close"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" />
                                </svg>
                            </span> </div>
                    </div>
                    <div class="status_body_content" id="status_body_content"></div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="team_rank_user" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content custmbg" style="border-radius:18px;">
                    <div class="modal-body sb-wrap" style="padding:20px 24px 24px;" id="team_scoreboard_body">
                        <div style="text-align:center; padding:40px; color:#8a93a8;">Loading scoreboard...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- REPRESENTATIVE SELECTION MODAL                                 -->
        <!-- ============================================================== -->
        <div class="modal fade" id="repSelectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(15,23,42,0.12);">
                    <div class="modal-header py-3 px-4" style="border-bottom: 1px solid #e2e8f0;">
                        <div>
                            <h3 class="modal-title fs-6 fw-bold text-gray-900 mb-0">Select Company Representative</h3>
                            <div class="text-muted fs-8 mt-1">
                                Choose who will represent your company to <span id="repModalTypeLabel" class="text-primary fw-bold">Clients</span>.
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body py-3 px-4">
                        <input type="hidden" id="repModalTargetType" value="client">
                        
                        <!-- Search Box -->
                        <div class="position-relative mb-3">
                            <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 12px; top: 11px; font-size: 12px;"></i>
                            <input type="text" id="repPickerSearch" class="form-control form-control-sm ps-5" placeholder="Search team members by name or email..." autocomplete="off" oninput="filterRepPicker(this.value)">
                        </div>

                        <!-- Info Pill -->
                        <div class="p-2 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 11.5px; color: #475569;">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i>
                            <span id="repModalScopeExplain">The selected user will be shown as the primary contact on all Direct Client interfaces.</span>
                        </div>

                        <!-- Eligible Team Members Radio List -->
                        <div class="rep-picker-list" id="repPickerList">
                            @php
                                $eligibleList = $eligibleRepresentatives ?? app(\App\Services\CompanyRepresentativeService::class)->getEligibleRepresentatives($teamCid);
                            @endphp
                            @foreach($eligibleList as $elUser)
                                @php
                                    $uInit = strtoupper(substr($elUser['name'] ?? 'U', 0, 1));
                                @endphp
                                <label class="rep-picker-item js-rep-picker-item" data-search="{{ strtolower($elUser['name'] . ' ' . $elUser['email'] . ' ' . $elUser['designation']) }}">
                                    <div class="d-flex align-items-center">
                                        <div class="rep-picker-avatar bg-light-primary text-primary">
                                            @if(!empty($elUser['image']))
                                                <img src="{{ $elUser['image'] }}" alt="{{ $elUser['name'] }}" style="width:100%; height:100%; border-radius:8px; object-fit:cover;">
                                            @else
                                                {{ $uInit }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="rep-picker-name">{{ $elUser['name'] }}</div>
                                            <div class="rep-picker-meta">
                                                <span>{{ $elUser['designation'] }}</span>
                                                <span class="mx-1">&bull;</span>
                                                <span>{{ $elUser['email'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if(!empty($elUser['is_client_rep']))
                                            <span class="badge-rep-pill badge-rep-client fs-8">Current Client Rep</span>
                                        @endif
                                        @if(!empty($elUser['is_agency_rep']))
                                            <span class="badge-rep-pill badge-rep-agency fs-8">Current Agency Rep</span>
                                        @endif
                                        <input type="radio" name="selected_rep_user_id" value="{{ $elUser['id'] }}" class="form-check-input ms-2" style="cursor: pointer;">
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-4" style="border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="btnConfirmRep" onclick="submitRepSelection()">
                            <i class="fa-solid fa-check me-1"></i> Confirm Representative
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- ASSIGN COMMUNICATION CONTACTS RIGHT DRAWER (RULE 20 STANDARD)  -->
        <!-- ============================================================== -->
        <div class="comm-overlay" id="commOverlay"></div>
        <div class="comm-drawer" id="commDrawer" role="dialog" aria-modal="true" aria-label="Assign Communication Contacts">
            <!-- Drawer Header -->
            <div class="comm-drawer-head">
                <div>
                    <h5 class="comm-drawer-title">Assign Communication Contacts</h5>
                    <p class="comm-drawer-subtitle">Select the communication types this member will be responsible for and set the contact priority for each.</p>
                </div>
                <button type="button" class="comm-drawer-close" id="commDrawerClose" onclick="closeAssignCommDrawer()" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Drawer Body -->
            <div class="comm-drawer-body">
                <input type="hidden" id="commDrawerUserId" value="" />

                <!-- Member Identity Card -->
                <div class="comm-user-card">
                    <div class="comm-user-card-left">
                        <div class="comm-user-avatar" id="commDrawerAvatar">U</div>
                        <div class="comm-user-info">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="comm-user-name" id="commDrawerName">Member Name</span>
                                <span class="comm-role-badge" id="commDrawerRole">Member</span>
                            </div>
                            <div class="comm-user-contacts">
                                <span id="commDrawerEmailWrap"><i class="fa-regular fa-envelope me-1 text-muted"></i><span id="commDrawerEmail">email@example.com</span></span>
                                <span id="commDrawerPhoneWrap"><i class="fa-solid fa-phone me-1 text-muted"></i><span id="commDrawerPhone">+91 0000000000</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="comm-user-card-right">
                        <div class="comm-status-pill" id="commDrawerStatusPill">
                            <span class="comm-status-dot" id="commDrawerStatusDot"></span>
                            <span id="commDrawerStatusText">Active</span>
                        </div>
                        <div class="comm-last-active-text" id="commDrawerLastActive">
                            <span class="comm-last-active-dot"></span>
                            <span id="commDrawerLastActiveText">Last active: Recently</span>
                        </div>
                    </div>
                </div>

                <!-- Section Title & Clear All -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="comm-section-title mb-0">Communication Types</h6>
                        <p class="comm-section-subtitle mb-0">Enable the communication types this member will be responsible for and set the contact priority.</p>
                    </div>
                    <button type="button" class="comm-btn-clear" onclick="clearAllCommDrawerRoles()" title="Disable all communication types">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Clear All</span>
                    </button>
                </div>

                <!-- 4 Channel Cards -->
                <div class="d-flex flex-column gap-3" id="commDrawerCardsList">
                    <!-- Channel 1: Client & Sales -->
                    <div class="comm-card" id="commCard_client" data-channel="client">
                        <div class="comm-card-main">
                            <div class="comm-card-icon icon-client">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="comm-card-text">
                                <div class="comm-card-title">Client &amp; Sales</div>
                                <div class="comm-card-desc">Client enquiries, sales communication, profile discovery and client job requests.</div>
                            </div>
                        </div>
                        <div class="comm-card-controls">
                            <label class="comm-ios-switch" title="Toggle Client &amp; Sales representation">
                                <input type="checkbox" class="comm-drawer-toggle" data-channel="client" id="commToggle_client">
                                <span class="comm-ios-slider"></span>
                            </label>
                            <select class="comm-prio-select" data-channel="client" id="commPrio_client" disabled title="Contact Priority">
                                <option value="Primary" selected>Primary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Backup">Backup</option>
                            </select>
                        </div>
                    </div>

                    <!-- Channel 2: Agency -->
                    <div class="comm-card" id="commCard_agency" data-channel="agency">
                        <div class="comm-card-main">
                            <div class="comm-card-icon icon-agency">
                                <i class="fa-solid fa-shapes"></i>
                            </div>
                            <div class="comm-card-text">
                                <div class="comm-card-title">Agency</div>
                                <div class="comm-card-desc">Agency enquiries, job requests, candidate submissions and marketplace communication.</div>
                            </div>
                        </div>
                        <div class="comm-card-controls">
                            <label class="comm-ios-switch" title="Toggle Agency representation">
                                <input type="checkbox" class="comm-drawer-toggle" data-channel="agency" id="commToggle_agency">
                                <span class="comm-ios-slider"></span>
                            </label>
                            <select class="comm-prio-select" data-channel="agency" id="commPrio_agency" disabled title="Contact Priority">
                                <option value="Primary" selected>Primary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Backup">Backup</option>
                            </select>
                        </div>
                    </div>

                    <!-- Channel 3: Recruitment / Talent -->
                    <div class="comm-card" id="commCard_recruitment" data-channel="recruitment">
                        <div class="comm-card-main">
                            <div class="comm-card-icon icon-recruitment">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <div class="comm-card-text">
                                <div class="comm-card-title">Recruitment / Talent</div>
                                <div class="comm-card-desc">Candidate coordination, interviews, hiring operations and talent related communication.</div>
                            </div>
                        </div>
                        <div class="comm-card-controls">
                            <label class="comm-ios-switch" title="Toggle Recruitment / Talent representation">
                                <input type="checkbox" class="comm-drawer-toggle" data-channel="recruitment" id="commToggle_recruitment">
                                <span class="comm-ios-slider"></span>
                            </label>
                            <select class="comm-prio-select" data-channel="recruitment" id="commPrio_recruitment" disabled title="Contact Priority">
                                <option value="Primary" selected>Primary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Backup">Backup</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Info Alert Callout -->
                <div class="comm-info-box">
                    <div class="comm-info-icon">
                        <i class="fa-solid fa-info"></i>
                    </div>
                    <div class="comm-info-text">
                        <div>A member can be assigned to multiple communication types.</div>
                        <div>You can set different priority levels (Primary, Secondary, Backup) for each type.</div>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="comm-drawer-foot">
                <button type="button" class="comm-btn-cancel" onclick="closeAssignCommDrawer()">Cancel</button>
                <button type="button" class="comm-btn-save" id="btnSaveCommRoles" onclick="saveCommunicationRoles()">
                    <span class="indicator-label">Save Assignments</span>
                    <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm align-middle me-2"></span> Saving...</span>
                </button>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- JOB-LEVEL AGENCY SHARES MAPPING INSPECTOR MODAL               -->
        <!-- ============================================================== -->
        <div class="modal fade" id="agencySharesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(15,23,42,0.12);">
                    <div class="modal-header py-3 px-4" style="border-bottom: 1px solid #e2e8f0;">
                        <div>
                            <h3 class="modal-title fs-6 fw-bold text-gray-900 mb-0">
                                <i class="fa-solid fa-diagram-project text-primary me-2"></i>Job-Level Agency Representation Mapping
                            </h3>
                            <div class="text-muted fs-8 mt-1">
                                Authoritative representation priority: <strong>Job + Agency Relationship → User Who Shared/Added Agency → Representative</strong>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body py-3 px-4">
                        <!-- Rule Reminder Alert -->
                        <div class="p-3 mb-3 rounded" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 11.5px; color: #1e40af;">
                            <div class="fw-bold mb-1"><i class="fa-solid fa-shield-halved me-1"></i>Direct Client &amp; Agency Representation Law:</div>
                            When a job is shared with an agency, the team member who actually shared/invited that agency becomes the representative for that agency relationship. The job creator is <strong>NOT</strong> automatically the representative unless they shared the job themselves. Both the Partner Agency and Direct Client see this exact representative.
                        </div>

                        <!-- Content Loaded via AJAX -->
                        <div id="agencySharesModalBody">
                            <div class="text-center py-5 text-muted">
                                <span class="spinner-border spinner-border-sm me-2"></span> Loading agency relationship mapping...
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-4" style="border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Authoritative Representation Rules Modal --}}
        <div class="modal fade" id="repRulesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(15,23,42,0.12);">
                    <div class="modal-header py-3 px-4" style="border-bottom: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#009ef7; display:flex; align-items:center; justify-content:center; font-size:16px;">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h3 class="modal-title fs-6 fw-bold text-gray-900 mb-0">Company Representation Architecture &amp; Hierarchy</h3>
                                <div class="text-muted fs-8 mt-1">Authoritative identity resolution across Direct Clients and Partner Agencies</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- 4 Pillars Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:3.5px solid #009ef7;">
                                    <div class="fw-bold fs-7 text-dark mb-1"><i class="fa-solid fa-user-tie text-primary me-2"></i>Client SPOC</div>
                                    <div class="fs-8 text-muted">The authoritative company representative shown to <strong>Direct Clients</strong> across job requisitions, candidate review pipelines, and organization profiles.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:3.5px solid #10b981;">
                                    <div class="fw-bold fs-7 text-dark mb-1"><i class="fa-solid fa-building-user text-success me-2"></i>Agency SPOC</div>
                                    <div class="fs-8 text-muted">The default contact presented to <strong>Partner Agencies</strong> on job invitations, talent sharing feeds, and placement discussions.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:3.5px solid #f59e0b;">
                                    <div class="fw-bold fs-7 text-dark mb-1"><i class="fa-solid fa-pen-nib text-warning me-2"></i>Job Internal Author</div>
                                    <div class="fs-8 text-muted">The team member who originally authored or opened the job requisition internally. Retained for internal audit trails.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-2" style="background:#f8fafc; border:1px solid #e2e8f0; border-left:3.5px solid #8b5cf6;">
                                    <div class="fw-bold fs-7 text-dark mb-1"><i class="fa-solid fa-share-nodes text-purple me-2"></i>Job-Agency Sharer</div>
                                    <div class="fs-8 text-muted">The specific team member who actively shared a requisition with an agency. Always takes top precedence on shared views.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Priority Cascade Banner -->
                        <div class="p-3 rounded-2" style="background:#eff6ff; border:1px solid #bfdbfe;">
                            <div class="fw-bold fs-7 text-primary mb-2"><i class="fa-solid fa-arrow-trend-up me-2"></i>Authoritative Identity Priority Cascade</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap fs-8 text-dark">
                                <span class="badge bg-white text-primary border border-primary px-2 py-1">1. Job + Agency Specific Sharer</span>
                                <i class="fa-solid fa-arrow-right text-muted fs-9"></i>
                                <span class="badge bg-white text-dark border px-2 py-1">2. User Who Added Partner Agency</span>
                                <i class="fa-solid fa-arrow-right text-muted fs-9"></i>
                                <span class="badge bg-primary text-white px-2 py-1">3. Default Company Representative</span>
                            </div>
                            <div class="fs-8 text-muted mt-2">
                                This guarantees that partner agencies and clients always see the authoritative person they are actively working with, never a generic placeholder.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-4" style="border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal" data-dismiss="modal">Got it</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('footer-script')
        <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
        <div class="closeclass"></div>

        <!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->
        <script>
            var input2 = document.querySelector("#kt_tagify_2");
            // new Tagify(input2);
        </script>
        <script>
            // =========================================================================
            // JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
            // RFC 5321 (254 char), Deterministic Sanitizer, 3-State Typo Detection
            // =========================================================================
            window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || (function () {
                var CONSUMER_DOMAIN_TYPOS = {
                    'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com',
                    'gmaill.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gmeil.com': 'gmail.com',
                    'gmaul.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaili.com': 'gmail.com',
                    'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'ymail.com': 'yahoo.com',
                    'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
                    'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
                    'iclou.com': 'icloud.com', 'iclod.com': 'icloud.com',
                    'redifmail.com': 'rediffmail.com', 'rediffmial.com': 'rediffmail.com'
                };

                var CONSUMER_TLD_TYPOS = {
                    'con': 'com', 'cmo': 'com', 'ocm': 'com', 'cpm': 'com',
                    'comm': 'com', 'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
                };

                return {
                    cleanDeterministic: function (raw) {
                        if (!raw) return '';
                        var str = String(raw).trim();
                        str = str.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, '');
                        str = str.replace(/^['"`<]+|['"`>]+$/g, '');
                        str = str.replace(/^mailto:\s*/i, '');
                        var match = str.match(/<([^>]+)>/);
                        if (match && match[1]) {
                            str = match[1];
                        }
                        str = str.replace(/[.,;:]+$/, '');
                        return str.toLowerCase().replace(/\s+/g, '');
                    },

                    getSuggestion: function (email) {
                        if (!email || email.indexOf('@') === -1) return null;
                        var parts = email.split('@');
                        if (parts.length !== 2) return null;

                        var local = parts[0];
                        var domain = parts[1];

                        if (domain.indexOf(',') !== -1) {
                            var commaFixed = domain.replace(/,([a-zA-Z]{2,})$/, '.$1');
                            return {
                                suggested: local + '@' + commaFixed,
                                message: 'Did you mean <strong>' + local + '@' + commaFixed + '</strong>?'
                            };
                        }

                        if (CONSUMER_DOMAIN_TYPOS[domain]) {
                            var fixedEmail = local + '@' + CONSUMER_DOMAIN_TYPOS[domain];
                            return {
                                suggested: fixedEmail,
                                message: 'Did you mean <strong>' + fixedEmail + '</strong>?'
                            };
                        }

                        var dParts = domain.split('.');
                        if (dParts.length >= 2) {
                            var root = dParts[0];
                            var tld = dParts[dParts.length - 1];
                            if (CONSUMER_TLD_TYPOS[tld] && (root === 'gmail' || root === 'yahoo' || root === 'outlook' || root === 'hotmail')) {
                                dParts[dParts.length - 1] = CONSUMER_TLD_TYPOS[tld];
                                var fixed = local + '@' + dParts.join('.');
                                return {
                                    suggested: fixed,
                                    message: 'Did you mean <strong>' + fixed + '</strong>?'
                                };
                            }
                        }

                        return null;
                    },

                    validateSyntax: function (email) {
                        if (!email || email.trim() === '') {
                            return { isValid: false, error: 'Email address is required.' };
                        }

                        if (email.length > 254) {
                            return { isValid: false, error: 'Email exceeds maximum allowed length of 254 characters.' };
                        }

                        if (/\s/.test(email)) {
                            return { isValid: false, error: 'Email address cannot contain spaces.' };
                        }

                        if (email.indexOf(',') !== -1) {
                            if (email.indexOf('@') !== -1 && email.split('@')[1].indexOf(',') !== -1) {
                                return { isValid: false, error: 'Domain name contains a comma instead of a period (e.g. gmail,com).' };
                            }
                            return { isValid: false, error: 'Email address cannot contain commas.' };
                        }

                        var parts = email.split('@');
                        if (parts.length < 2) {
                            return { isValid: false, error: 'Email is missing the "@" symbol.' };
                        }
                        if (parts.length > 2) {
                            return { isValid: false, error: 'Email contains more than one "@" symbol.' };
                        }

                        var local = parts[0];
                        var domain = parts[1];

                        if (local.length === 0) {
                            return { isValid: false, error: 'Email username cannot be empty.' };
                        }
                        if (local.length > 64) {
                            return { isValid: false, error: 'Email username cannot exceed 64 characters.' };
                        }

                        if (local.indexOf('..') !== -1 || domain.indexOf('..') !== -1) {
                            return { isValid: false, error: 'Email cannot contain consecutive dots ("..").' };
                        }

                        if (local.startsWith('.') || local.endsWith('.')) {
                            return { isValid: false, error: 'Email username cannot start or end with a dot.' };
                        }
                        if (domain.startsWith('.') || domain.endsWith('.')) {
                            return { isValid: false, error: 'Domain name cannot start or end with a dot.' };
                        }

                        if (domain.indexOf('_') !== -1) {
                            return { isValid: false, error: 'Domain name cannot contain underscores (use hyphens instead).' };
                        }

                        if (!/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/.test(domain)) {
                            return { isValid: false, error: 'Domain name format is invalid (letters, numbers, and hyphens only).' };
                        }

                        var tld = domain.split('.').pop();
                        if (!tld || tld.length < 2 || /^\d+$/.test(tld)) {
                            return { isValid: false, error: 'Domain must end with a valid extension (e.g. .com, .org).' };
                        }

                        return { isValid: true, error: null };
                    }
                };
            })();

            // Phase 2: Tenant-Scoped IndexedDB Store for Team Listing & Reference Data
            const CURRENT_COMPANY_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
            const CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
            const TEAM_DB_NAME = 'jero_team_local_store_v2_' + CURRENT_COMPANY_ID;
            const TEAM_DB_VERSION = 2;
            const TEAM_CACHE_TTL_MS = 15 * 60 * 1000; // 15 minutes TTL

            const JeroTeamLocalStore = (function() {
                let dbPromise = null;

                function getDB() {
                    if (!window.indexedDB) return Promise.resolve(null);
                    if (dbPromise) return dbPromise;

                    dbPromise = new Promise(function(resolve) {
                        try {
                            const req = window.indexedDB.open(TEAM_DB_NAME, TEAM_DB_VERSION);
                            req.onupgradeneeded = function(e) {
                                const db = e.target.result;
                                if (!db.objectStoreNames.contains('team_cache')) {
                                    db.createObjectStore('team_cache', { keyPath: 'cache_key' });
                                }
                                if (!db.objectStoreNames.contains('meta')) {
                                    db.createObjectStore('meta', { keyPath: 'id' });
                                }
                            };
                            req.onsuccess = function(e) {
                                const db = e.target.result;
                                try {
                                    const tx = db.transaction(['meta'], 'readonly');
                                    const metaStore = tx.objectStore('meta');
                                    const metaReq = metaStore.get('current_context');
                                    metaReq.onsuccess = function() {
                                        const meta = metaReq.result;
                                        if (meta && (meta.tenant_id !== CURRENT_COMPANY_ID || meta.user_id !== CURRENT_USER_ID)) {
                                            // Tenant/User mismatch: purge immediately to prevent cross-tenant data leaks
                                            const wipeTx = db.transaction(['team_cache', 'meta'], 'readwrite');
                                            wipeTx.objectStore('team_cache').clear();
                                            wipeTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_COMPANY_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                        } else if (!meta) {
                                            const setTx = db.transaction(['meta'], 'readwrite');
                                            setTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_COMPANY_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                        }
                                    };
                                } catch (err) {
                                    console.warn('JeroTeamLocalStore tenancy check error:', err);
                                }
                                resolve(db);
                            };
                            req.onerror = function() { resolve(null); };
                        } catch (err) {
                            resolve(null);
                        }
                    });

                    return dbPromise;
                }

                return {
                    get: async function(key) {
                        const db = await getDB();
                        if (!db || !key) return null;
                        return new Promise(function(resolve) {
                            try {
                                const tx = db.transaction(['team_cache'], 'readonly');
                                const store = tx.objectStore('team_cache');
                                const req = store.get(key);
                                req.onsuccess = function() {
                                    const res = req.result;
                                    if (res && (Date.now() - res.timestamp < TEAM_CACHE_TTL_MS)) {
                                        resolve(res.payload);
                                    } else {
                                        resolve(null);
                                    }
                                };
                                req.onerror = function() { resolve(null); };
                            } catch (e) {
                                resolve(null);
                            }
                        });
                    },
                    set: async function(key, payload) {
                        const db = await getDB();
                        if (!db || !key || !payload) return;
                        try {
                            const tx = db.transaction(['team_cache'], 'readwrite');
                            const store = tx.objectStore('team_cache');
                            store.put({
                                cache_key: key,
                                payload: payload,
                                timestamp: Date.now()
                            });
                        } catch (e) {
                            // Non-fatal if storage quota exceeded or disabled
                        }
                    },
                    purge: async function() {
                        const db = await getDB();
                        if (!db) return;
                        try {
                            const tx = db.transaction(['team_cache'], 'readwrite');
                            tx.objectStore('team_cache').clear();
                        } catch (e) {
                            // Non-fatal
                        }
                    }
                };
            })();
            window.JeroTeamLocalStore = JeroTeamLocalStore;

            // Phase 1 Optimization: Unified Client RAM state container
            window.teamState = {
                records: [],
                statusTab: 'all',
                pageSize: parseInt(localStorage.getItem('teamPageSize')) || 10,
                currentPage: parseInt(localStorage.getItem('currentPageTeam')) || 1,
                totalCount: 0,
                rolesCache: null,
                searchDebounceTimer: null
            };

            // Phase 5 Optimization: Client-side Idempotency Key generator
            function generateIdempotencyKey(action) {
                var uuid = (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') 
                    ? crypto.randomUUID() 
                    : ((action || 'tm') + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9));
                return uuid;
            }
            window.generateIdempotencyKey = generateIdempotencyKey;

            // Phase 4 Optimization: Real-Time Team Member Delta Synchronization via Reverb WebSockets
            function handleTeamMemberRealtimeUpdate(e) {
                if (!e || !e.user_id) return;
                var currentUserId = parseInt("{{ auth()->id() ?? 0 }}", 10);
                var senderId = e.sender_id ? parseInt(e.sender_id, 10) : 0;

                // 1. Invalidate L2 IndexedDB cache immediately so subsequent pagination or refreshes fetch fresh data
                if (window.JeroTeamLocalStore) {
                    window.JeroTeamLocalStore.purge();
                }

                // If event originated from this exact browser session, local UI was already patched optimistically
                if (senderId && senderId === currentUserId) {
                    return;
                }

                var userId = parseInt(e.user_id, 10);

                // 2. Perform surgical DOM and RAM delta updates
                if (e.action === 'team_member.role_updated') {
                    var $btn = $('.update-user-role[data-row-id="' + userId + '"]');
                    var $pill = $btn.siblings('.team-role-pill');
                    if ($pill.length && e.role_name) {
                        $pill.text(e.role_name);
                        var lower = e.role_name.toLowerCase();
                        $pill.removeClass('role-admin role-lead role-default');
                        if (lower.indexOf('admin') !== -1) {
                            $pill.addClass('role-admin');
                        } else if (lower.indexOf('lead') !== -1) {
                            $pill.addClass('role-lead');
                        } else {
                            $pill.addClass('role-default');
                        }
                    }

                    if (window.teamState && Array.isArray(window.teamState.records)) {
                        var rec = window.teamState.records.find(function(r) { return parseInt(r.id, 10) === userId; });
                        if (rec) {
                            rec.display_name = e.role_name;
                            rec.role_name = e.role_name;
                        }
                    }
                } else if (e.action === 'team_member.status_updated') {
                    var $chk = $('#status_change_' + userId);
                    if ($chk.length) {
                        var isActive = (e.status === 'active');
                        $chk.prop('checked', isActive);
                        var $statusText = $chk.parents('td').find('.status_text');
                        if (isActive) {
                            $statusText.text('Active').removeClass('text-danger').addClass('text-success');
                        } else {
                            $statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                        }
                    }

                    if (window.teamState && Array.isArray(window.teamState.records)) {
                        var rec = window.teamState.records.find(function(r) { return parseInt(r.id, 10) === userId; });
                        if (rec) {
                            rec.status = e.status;
                        }
                    }
                } else if (e.action === 'team_member.working_status_updated') {
                    var $ws = $('#working_status_' + userId);
                    if ($ws.length) {
                        $ws.prop('checked', parseInt(e.working_status, 10) === 1);
                    }
                } else if (e.action === 'team_member.created' || e.action === 'team_member.invited' || e.action === 'team_member.deleted') {
                    // Member added, invited, or removed: purge L1/L2 and reload team records
                    if (window.JeroTeamLocalStore) {
                        window.JeroTeamLocalStore.purge();
                    }
                    fetchTeams(window.teamState.currentPage || 1);
                }
            }

            // Bind Reverb WebSocket channel listener on company-scoped private channel
            $(document).ready(function() {
                var companyId = parseInt("{{ (int) auth()->user()->company_id }}", 10);
                if (companyId > 0 && typeof window.Echo !== 'undefined') {
                    try {
                        window.Echo.private('company.' + companyId)
                            .listen('.team.member.updated', function(eventData) {
                                handleTeamMemberRealtimeUpdate(eventData);
                            });
                    } catch (wsErr) {
                        console.warn('Echo Reverb subscription notice:', wsErr);
                    }
                }
            });

            // Phase 1 Optimization: 250ms debounced search to eliminate request storms
            $('#kt_tagify_2').on('input', function() {
                clearTimeout(window.teamState.searchDebounceTimer);
                window.teamState.searchDebounceTimer = setTimeout(function() {
                    filtersdataget();
                }, 250);
            });

            function filtersdataget() {
                fetchTeams(1);
            }

            $(document).on('click', '.teamViewScore', function() {
                var userId = $(this).data('user-id');
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('service_company.team.scoreboard') }}";

                $('#team_scoreboard_body').html('<div style="text-align:center; padding:40px;"><img src="' + baseurl + '" height="35px" width="100%"></div>');

                // Show modal — wrapped in try/catch so a Bootstrap transition-state error
                // doesn't abort the function before the $.ajax call below fires.
                var modalEl = document.getElementById('team_rank_user');
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else if (typeof $ !== 'undefined' && $.fn.modal) {
                        $(modalEl).modal('show');
                    }
                } catch (e) {
                    // fallback: force-show via display style if Bootstrap API fails
                    if (modalEl) {
                        modalEl.style.display = 'block';
                        modalEl.classList.add('show');
                        document.body.classList.add('modal-open');
                    }
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    data: { user_id: userId },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response && response.status === 'success') {
                            $('#team_scoreboard_body').html(response.view);
                            // Re-pin after injected script runs so our version always wins
                            window.sbCloseModal = closeTeamScoreModal;
                        } else {
                            var msg = (response && response.message) ? response.message : 'Could not load scoreboard.';
                            $('#team_scoreboard_body').html('<div style="text-align:center;padding:40px;color:#e53935;">' + msg + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errMsg = 'Error loading scoreboard.';
                        try { var r = JSON.parse(xhr.responseText); if (r && r.message) errMsg = r.message; } catch(e) {}
                        $('#team_scoreboard_body').html('<div style="text-align:center;padding:40px;color:#e53935;">' + errMsg + '</div>');
                    }
                });
            });

            // --- Team Score Board modal close ---
            function closeTeamScoreModal() {
                var el = document.getElementById('team_rank_user');
                if (!el) return;
                // Force visual close via direct DOM — works regardless of Bootstrap version/state
                el.style.display = 'none';
                el.classList.remove('show');
                document.body.classList.remove('modal-open');
                document.body.style.paddingRight = '';
                document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                // Sync Bootstrap 5 internal flag so the next .show() works correctly
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var inst = bootstrap.Modal.getInstance(el);
                        if (inst) inst._isShown = false;
                    }
                } catch (e) {}
            }

            // Expose globally — onclick="sbCloseModal(this)" in the injected scoreboard HTML
            // calls this. Defined here (static script) so it is always available.
            window.sbCloseModal = closeTeamScoreModal;

            // Close on outside click: any click not inside .modal-dialog
            $('#team_rank_user').on('click', function(e) {
                if (!$(e.target).closest('.modal-dialog').length) {
                    closeTeamScoreModal();
                }
            });

            // Close via ✕ button (data-bs-dismiss doesn't fire when modal opened via JS on this page)
            $(document).on('click', '#team_rank_user .sb2-x', function() {
                closeTeamScoreModal();
            });

            // Export Report — print the scoreboard as PDF via browser print dialog
            window.sbExportPdf = function (el) {
                var prevTitle = document.title;
                document.title = 'Score-Board-' + new Date().toISOString().slice(0, 10);
                var done = function () { document.title = prevTitle; window.removeEventListener('afterprint', done); };
                window.addEventListener('afterprint', done);
                window.print();
                setTimeout(done, 1500);
            };
            // --- end score board close ---

            $(document).on('click', '.update_role_close', function() {
                closeEditRoleDrawer();
            });
            $(document).on('click', '#kt_activitiesClient_close', function() {
                $(".closeclass").removeAttr("style");
                $(".closeclass").removeClass("drawer-overlay");
                $("#kt_activitiesClient").removeAttr("style");
            });

            $(document).on('click', '.closeclass', function() {

                $("#kt_activitiesClient_close").click();
            });
            $(document).on('click', '.drawer-overlay', function() {
                if (typeof window.closeTeamMemberDrawer === 'function') {
                    window.closeTeamMemberDrawer();
                }
                $("#kt_activitiesClient_close").click();
            });


            $(document).on('click', '.chk_list_item', () => {
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.teamdelete').removeClass("d-none");
                    } else {
                        $('.teamdelete').addClass("d-none");
                    }
                }, 10);

            });
            $(document).on('click', '.chk_list', () => {
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').attr('checked', 'checked');
                    $('.teamdelete').addClass("d-none");
                } else {
                    $('.chk_list_item').removeAttr('checked');
                    $('.teamdelete').removeClass("d-none");
                }
            });

            $(document).on('click', '.teamdelete', () => {
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                var id = 0;
                var url = "{{ route('service_company.team.teamDelete', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to delete team!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            type: 'POST',
                            url: url,
                            headers: {
                                'X-Idempotency-Key': generateIdempotencyKey('teamdelete')
                            },
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'arr': arr
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    if (window.JeroTeamLocalStore) {
                                        window.JeroTeamLocalStore.purge();
                                    }
									fetchTeams();
                                    $('.teamdelete').addClass("d-none");
                                    Swal.fire({
                                        text: "Team inactive successfully.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            }
                        });

                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'Team not delete:)', 'error')
                    }

                })

            });

            function handleClick(currentValue, currentId) {
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var $statusText = $(currentValue).closest('.team-status-cell, .tm-cell-status, td').find('.status_text');
                
                // Optimistic UI update
                if (chkStatus) {
                    $statusText.text('Active').removeClass('text-danger').addClass('text-success');
                } else {
                    $statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                }

                var url = "{{ route('service_company.team.statusUpdate') }}";
                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-Idempotency-Key': generateIdempotencyKey('status_' + userId)
                    },
                    data: {
                        '_token': token,
                        'chkStatus': chkStatus,
                        'userId': userId
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            if (window.JeroTeamLocalStore) {
                                window.JeroTeamLocalStore.purge();
                            }
                            // Phase 1 Optimization: Update RAM state without full-table re-fetch
                            if (window.teamState && Array.isArray(window.teamState.records)) {
                                var rec = window.teamState.records.find(function(r) { return parseInt(r.id, 10) === parseInt(userId, 10); });
                                if (rec) {
                                    rec.status = chkStatus ? 'active' : 'inactive';
                                }
                            }
                        } else {
                            // Revert on server business failure
                            currentValue.checked = !chkStatus;
                            if (!chkStatus) {
                                $statusText.text('Active').removeClass('text-danger').addClass('text-success');
                            } else {
                                $statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                            }
                            Swal.fire({ icon: 'warning', title: 'Update Failed', text: response.message || 'Status could not be updated.', confirmButtonColor: '#6366f1' });
                        }
                    },
                    error: function(xhr) {
                        // Revert on server/network error
                        currentValue.checked = !chkStatus;
                        if (!chkStatus) {
                            $statusText.text('Active').removeClass('text-danger').addClass('text-success');
                        } else {
                            $statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                        }
                        Swal.fire({ icon: 'error', title: 'Network Error', text: 'Error ' + xhr.status + '. Status update reverted.', confirmButtonColor: '#6366f1' });
                    }
                });
            }

            function reSendhandleClick(currentValue, currentId) {

                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.reSendmail') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";


                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to resend mail!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {

                                    Swal.fire({
                                        text: "Team member Re-send successfully. set password link has been sent to team member email.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });


                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'mail not send:)', 'error')
                    }

                })





            }


            $(document).on('click', '.update-user-role', function() {
                var id = $(this).data('row-id');
                openEditRoleDrawer(id);
            });

            // ---- Edit Role Drawer ----
            function openEditRoleDrawer(userId) {
                var url    = "{{ route('service_company.team.editrole', ':id') }}".replace(':id', userId);
                var loader = '<div style="text-align:center;padding:48px 0;"><img src="{{ asset('assets/images/Loading2.gif') }}" height="36"></div>';
                $('#editRoleDrawerBody').html(loader);
                $('#editRoleDrawerFooter').hide();
                $('#editRoleOverlay').fadeIn(200);
                $('#editRoleDrawer').css('transform', 'translateX(0)');
                $('body').addClass('overflow-hidden');

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        if (response.status === 'success' || response.view) {
                            $('#editRoleDrawerBody').html(response.view);
                            $('#editRoleDrawerFooter').show();
                        } else {
                            $('#editRoleDrawerBody').html('<div style="color:#e53935;padding:24px;">Could not load role data.</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#editRoleDrawerBody').html('<div style="color:#e53935;padding:24px;">Error ' + xhr.status + '</div>');
                    }
                });
            }

            function closeEditRoleDrawer() {
                $('#editRoleDrawer').css('transform', 'translateX(100%)');
                $('#editRoleOverlay').fadeOut(200);
                $('body').removeClass('overflow-hidden');
            }

            function submitEditRole() {
                var $selectedRadio = $('input[name="user_role"]:checked');
                var roleId = $selectedRadio.val();
                var roleName = $selectedRadio.data('role-name') || $selectedRadio.closest('label').find('div:nth-child(2) > div:first-child').text().trim();
                var userId = $('#cuser_id').val();
                if (!roleId) {
                    Swal.fire({ icon: 'warning', title: 'Select a role', text: 'Please choose a role before updating.', confirmButtonColor: '#6366f1' });
                    return;
                }
                var url   = "{{ route('service_company.team.roleupdate') }}";
                var token = "{{ csrf_token() }}";
                var btn   = document.getElementById('updateRoleSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-Idempotency-Key': generateIdempotencyKey('role_' + userId)
                    },
                    data: { _token: token, cuser_id: userId, user_role: roleId },
                    success: function(response) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Update Role';
                        if (response.status === 'success') {
                            if (window.JeroTeamLocalStore) {
                                window.JeroTeamLocalStore.purge();
                            }
                            closeEditRoleDrawer();

                            // Phase 1 Optimization: Local DOM row update without full-page document reload
                            var $btn = $('.update-user-role[data-row-id="' + userId + '"]');
                            var $pill = $btn.siblings('.team-role-pill');
                            if ($pill.length && roleName) {
                                $pill.text(roleName);
                                var lower = roleName.toLowerCase();
                                $pill.removeClass('role-admin role-lead role-default');
                                if (lower.indexOf('admin') !== -1) {
                                    $pill.addClass('role-admin');
                                } else if (lower.indexOf('lead') !== -1) {
                                    $pill.addClass('role-lead');
                                } else {
                                    $pill.addClass('role-default');
                                }
                            }

                            // Update RAM state
                            if (window.teamState && Array.isArray(window.teamState.records)) {
                                var rec = window.teamState.records.find(function(r) { return parseInt(r.id, 10) === parseInt(userId, 10); });
                                if (rec) {
                                    rec.display_name = roleName;
                                    rec.role_name = roleName;
                                }
                            }

                            Swal.fire({ icon: 'success', title: 'Role Updated', text: response.message || 'User role has been updated successfully.', confirmButtonColor: '#6366f1' });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Could not update role.', confirmButtonColor: '#6366f1' });
                        }
                    },
                    error: function(xhr) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Update Role';
                        Swal.fire({ icon: 'error', title: 'Server Error', text: xhr.status + ' ' + xhr.statusText, confirmButtonColor: '#6366f1' });
                    }
                });
            }

            // Close drawer on ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') closeEditRoleDrawer();
            });

            // Phase 1 Optimization: Populate roles into select helper
            function populateInviteRoleOptions(roles) {
                var $select = $('#inviteRole');
                $select.find('option:not([disabled])').remove();
                if (roles && roles.length > 0) {
                    $.each(roles, function(i, role) {
                        var val = role.id;
                        var label = role.display_name || role.name;
                        if (val && label) {
                            $select.append('<option value="' + val + '">' + label + '</option>');
                        }
                    });
                }
            }

            // ---- Invite Drawer ----
            function openInviteDrawer() {
                var rolesUrl = "{{ route('service_company.team.add_team_member_form', '123') }}";

                // Phase 1 Optimization: Use cached roles in RAM if available (0 HTTP requests!)
                if (window.teamState && window.teamState.rolesCache && window.teamState.rolesCache.length > 0) {
                    populateInviteRoleOptions(window.teamState.rolesCache);
                } else {
                    $.get(rolesUrl, function(response) {
                        if (response.status === 'coinCheck') {
                            Swal.fire("Insufficient Coins", response.message, "warning");
                            return;
                        }
                        if (response.status === 'success') {
                            if (response.roles && response.roles.length > 0) {
                                if (window.teamState) {
                                    window.teamState.rolesCache = response.roles;
                                }
                                populateInviteRoleOptions(response.roles);
                            } else if (response.view) {
                                var $html = $(response.view);
                                var extracted = [];
                                $html.find('input[type="radio"][name="role_id"]').each(function() {
                                    var val = $(this).val();
                                    var label = $(this).siblings('label').find('.fw-bolder').text().trim()
                                             || $(this).closest('.d-flex').find('.fw-bolder').text().trim();
                                    if (val && label) {
                                        extracted.push({ id: val, display_name: label });
                                    }
                                });
                                if (window.teamState) {
                                    window.teamState.rolesCache = extracted;
                                }
                                populateInviteRoleOptions(extracted);
                            }
                        }
                    });
                }

                // Reset form
                $('#inviteForm')[0].reset();
                $('#inviteForm').removeData('idempotency-key');
                $('#inviteEmail_typo_hint').hide().empty();
                $('#inviteMsg').hide().text('');
                $('#inviteSubmitBtn').removeClass('loading').prop('disabled', false);
                isInviteSubmitting = false;
                // Open
                $('#inviteOverlay, #inviteDrawer').addClass('open');
                document.body.style.overflow = 'hidden';
            }

            function closeInviteDrawer() {
                $('#inviteOverlay, #inviteDrawer').removeClass('open');
                $('#inviteEmail_typo_hint').hide().empty();
                document.body.style.overflow = '';
                isInviteSubmitting = false;
            }

            $('#openInviteDrawer').on('click', openInviteDrawer);
            $('#inviteDrawerClose, #inviteCancelBtn').on('click', closeInviteDrawer);
            $('#inviteOverlay').on('click', closeInviteDrawer);
            $(document).on('keydown', function(e) { if (e.key === 'Escape') closeInviteDrawer(); });

            // Paste & Blur deterministic clean and typo hints for Team Invite Email
            $(document).on('paste', '#inviteEmail', function () {
                var self = this;
                setTimeout(function () {
                    var clean = JeroEmailBoxEngine.cleanDeterministic($(self).val());
                    if (clean !== $(self).val()) {
                        $(self).val(clean).trigger('input');
                    }
                }, 10);
            });

            $(document).on('blur change', '#inviteEmail', function () {
                var raw = $(this).val();
                var clean = JeroEmailBoxEngine.cleanDeterministic(raw);
                if (clean !== raw) {
                    $(this).val(clean);
                }
                var $hint = $('#inviteEmail_typo_hint');
                if (!clean) {
                    $hint.hide().empty();
                    return;
                }
                var syntax = JeroEmailBoxEngine.validateSyntax(clean);
                if (!syntax.isValid) {
                    $hint.html('<i class="fa-solid fa-circle-exclamation text-danger"></i> ' + syntax.error).show();
                    return;
                }
                var suggestion = JeroEmailBoxEngine.getSuggestion(clean);
                if (suggestion) {
                    $hint.html('<i class="fa-solid fa-lightbulb text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#inviteEmail" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
                } else {
                    $hint.hide().empty();
                }
            });

            $(document).on('click', '.apply-email-fix', function (e) {
                e.preventDefault();
                var targetSelector = $(this).data('target');
                var fixed = $(this).data('fixed');
                if (targetSelector && fixed) {
                    $(targetSelector).val(fixed).trigger('input').trigger('change');
                    $(this).closest('.team-email-typo-hint').hide().empty();
                }
            });

            var isInviteSubmitting = false;
            $('#inviteSubmitBtn').on('click', function(e) {
                e.preventDefault();
                if (isInviteSubmitting) return;
                var name  = $.trim($('#inviteName').val());
                var email = JeroEmailBoxEngine.cleanDeterministic($('#inviteEmail').val());
                $('#inviteEmail').val(email);
                var role  = $('#inviteRole').val();

                if (!name) {
                    showInviteMsg('Please enter the full name.', 'error'); return;
                }
                var emailSyntax = JeroEmailBoxEngine.validateSyntax(email);
                if (!emailSyntax.isValid) {
                    showInviteMsg(emailSyntax.error, 'error'); return;
                }
                if (!role) {
                    showInviteMsg('Please select a role.', 'error'); return;
                }

                isInviteSubmitting = true;
                var $btn = $(this);
                $btn.addClass('loading').prop('disabled', true);
                $('#inviteMsg').hide();

                // Stable idempotency key: maintained across in-flight retries of the same form submission
                var $form = $('#inviteForm');
                var idempotencyKey = $form.data('idempotency-key');
                if (!idempotencyKey) {
                    idempotencyKey = generateIdempotencyKey('invite_' + email.toLowerCase().replace(/[^a-z0-9]/g, ''));
                    $form.data('idempotency-key', idempotencyKey);
                }

                var formData = new FormData($form[0]);

                $.ajax({
                    url: '{{ route("service_company.team.store") }}?_token={{ csrf_token() }}',
                    type: 'POST',
                    headers: {
                        'X-Idempotency-Key': idempotencyKey
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        isInviteSubmitting = false;
                        $btn.removeClass('loading').prop('disabled', false);
                        if (response.msgType === 'success') {
                            $form.removeData('idempotency-key');
                            if (window.JeroTeamLocalStore) {
                                window.JeroTeamLocalStore.purge();
                            }
                            closeInviteDrawer();
                            fetchTeams();
                            Swal.fire('Invitation Sent!', response.message, 'success');
                        } else {
                            showInviteMsg(response.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function(xhr) {
                        isInviteSubmitting = false;
                        $btn.removeClass('loading').prop('disabled', false);
                        var msg = 'Server error. Please try again.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showInviteMsg(msg, 'error');
                    }
                });
            });

            function showInviteMsg(msg, type) {
                var $m = $('#inviteMsg');
                $m.text(msg)
                  .css({
                      background: type === 'error' ? '#fef2f2' : '#f0fdf4',
                      color:      type === 'error' ? '#dc2626'  : '#16a34a',
                      border:     type === 'error' ? '1px solid #fecaca' : '1px solid #bbf7d0'
                  })
                  .show();
            }
        </script>

        <script>
            function WorkingStatus(currentValue, currentId) {
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.WorkingStatus') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to update Working Status.!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            headers: {
                                'X-Idempotency-Key': generateIdempotencyKey('working_status_' + userId)
                            },
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    if (window.JeroTeamLocalStore) {
                                        window.JeroTeamLocalStore.purge();
                                    }
                                    swalWithBootstrapButtons.fire('Success!',
                                        'Working status update successfully.', 'success')
                                } else if (response.status == "info") {
                                    if (chkStatus == true) {
                                        $('#working_status_' + userId).prop('checked', false);
                                    } else {
                                        $('#working_status_' + userId).prop('checked', true);
                                    }
                                    swalWithBootstrapButtons.fire('Fail!', response.message, 'error')
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        if (chkStatus == true) {
                            $('#working_status_' + userId).prop('checked', false);
                        } else {
                            $('#working_status_' + userId).prop('checked', true);
                        }
                        swalWithBootstrapButtons.fire('Cancelled', 'Working status can not update:)', 'error')
                    }

                })


            }

            function jointableLoad(userId) {
                return false;
                //    var userId		=	$('#userId').val();
                table = $('#myTable_joining').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.joiningdata') !!}?userId=' + userId,
                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            width: '5%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            width: '20%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Contact',
                            name: 'Contact',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Offer',
                            name: 'Offer',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Joining',
                            name: 'Joining',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                new $.fn.dataTable.FixedHeader(jointable);
            }

            function load_interviewSchedule(userId) {
                return false;

                var userId = $('#userId').val();

                table = $('#load_interviewSchedule').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.intdata') !!}?userId=' + userId,

                    "fnDrawCallback": function(settings, start, end, max, total, pre) {
                        var total_record = this.fnSettings().fnRecordsTotal();
                        $('#total_record').html(total_record);
                        $("body").tooltip({
                            selector: '[data-toggle="tooltip"]'
                        });
                    },


                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0
                        },
                        {
                            "width": "150px",
                            "targets": 1,
                            "class": "align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "50px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "50px",
                            "targets": 6
                        },
                        {
                            "width": "50px",
                            "targets": 7
                        },
                        {
                            "width": "50px",
                            "targets": 8
                        },
                        {
                            "width": "50px",
                            "targets": 9
                        }
                    ],

                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            width: '1%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            width: '15%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewMode',
                            name: 'InterviewMode',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Interviewers',
                            name: 'Interviewers',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewDate',
                            name: 'InterviewDate',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Feedback',
                            name: 'Feedback',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Rating',
                            name: 'Rating',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        }
                    ],

                });
                new $.fn.dataTable.FixedHeader(table);

            }

            function load_application(userId) {
                return false;
                // var userId		=	$('#userId').val();
                application_table = $('#load_application').dataTable({

                    responsive: true,
                    processing: true,
                    ordering: false, //disable ordering
                    destroy: true,
                    stateSave: true,
                    "bLengthChange": false,
                    sDom: 'lrtip',
                    ajax: '{!! route('service_company.team.appdata') !!}?userId=' + userId,
                    "initComplete": function(settings, json) {
                        $('.clientStatusLoader').html('');
                        $('.jobLoader').html('');
                        $('.sourceLoader').html('');
                        $('.search_np_idLoader').html('');
                        $('.search_passportLoader').html('');
                        $('.statusLoader').html('');
                    },

                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            orderable: false,
                            searchable: false,
                            width: '15%'
                        },
                        // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
                        {
                            data: 'Exp',
                            name: 'Exp',
                            searchable: false,
                            orderable: false,
                            width: '5%'
                        },
                        {
                            data: 'Salary',
                            name: 'Salary',
                            searchable: false,
                            orderable: false,
                            width: '5%'
                        },
                        {
                            data: 'NoticePeriod',
                            name: 'NoticePeriod',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Qualifaction',
                            name: 'Qualifaction',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Location',
                            name: 'Location',
                            orderable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            orderable: false
                        },
                        {
                            data: 'UploadDate',
                            name: 'UploadDate',
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            orderable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(application_table);

            }

            $(document).on('keyup', function() {
                var userId = $('#kt_tagify_hidden').val();
                load_jobs(userId);
            });
            function load_jobs(userId) {
                return false;
                var load_jobs = $('#load_jobs').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering   			
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: {
                        'url': '{!! route('service_company.team.jobdata') !!}?userId=' + userId,
                        "data": function(d) {
                            d.search = $('#inputSearch').val(), d.custom_search = $('#kt_tagify_search').val(), d
                                .custom_status = $('#statusjb').val(), d.custom_hiringtype = $('#hiringtypejob')
                                .val(), d.profile = true
                        }
                    },
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip1"]').tooltip();
                        $('[data-toggle="tooltip2"]').tooltip();
                        $('[data-toggle="tooltip3"]').tooltip();
                        $('[data-toggle="tooltip4"]').tooltip();
                    },
                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0,
                            "width": "4%",
                            "class": ""
                        },
                        {
                            "width": "200px",
                            "targets": 1,
                            "class": "d-flex align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "200px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "200px",
                            "targets": 6
                        },
                        {
                            "width": "150px",
                            "targets": 7
                        },
                        {
                            "width": "150px",
                            "targets": 8
                        },

                        {
                            "width": "20px",
                            "targets": 9
                        },
                        {
                            "width": "20px",
                            "targets": 10
                        },
                        {
                            "width": "20px",
                            "targets": 11
                        },
                        {
                            "width": "20px",
                            "targets": 12
                        },
                        {
                            "width": "20px",
                            "targets": 13
                        },
                        {
                            "width": "20px",
                            "targets": 14
                        },
                        {
                            "width": "20px",
                            "targets": 15
                        },
                        {
                            "width": "20px",
                            "targets": 16
                        }
                    ],
                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Requisition',
                            name: 'Requisition',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Exp',
                            name: 'Exp',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Salary',
                            name: 'Salary',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Location',
                            name: 'Location',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'Status',
                            name: 'Status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Date',
                            name: 'Date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'HM',
                            name: 'HM',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Partners',
                            name: 'Partners',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'TOT',
                            name: 'TOT',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SOU',
                            name: 'SOU',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SCR',
                            name: 'SCR',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'EVA',
                            name: 'EVA',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'NEO',
                            name: 'NEO',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'OFF',
                            name: 'OFF',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'JOI',
                            name: 'JOI',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'REJ',
                            name: 'REJ',
                            orderable: false,
                            searchable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(load_jobs);
            }

			$(document).ready(function() {
				fetchTeams();
				$(document).on('click', '#paginationTeams [data-page]', function(e) {
						e.preventDefault();

						const $btn = $(this);
						const page = $btn.data('page');
						if (!page || $btn.hasClass('is-disabled') || $btn.hasClass('is-active')
							|| $btn.hasClass('disabled') || $btn.hasClass('active')) return;

						$btn.addClass('is-disabled');

						fetchTeams(page, $('#search').val()).then(() => {
							$('#paginationTeams [data-page]').removeClass('is-disabled');
						}).catch(() => {
							$('#paginationTeams [data-page]').removeClass('is-disabled');
						});

				});
			});
            
               var page_no = localStorage.getItem('currentPageTeam');
                if (!page_no) {
                    page_no = 1;
                }
			

			// Active status tab + rows-per-page for the redesigned list.
			var teamStatusTab = 'all';
			var teamPageSize = parseInt(localStorage.getItem('teamPageSize')) || 10;

			var currentTeamFetchXhr = null;

			async function fetchTeams(page = page_no, search = '') {
				if (currentTeamFetchXhr && currentTeamFetchXhr.readyState !== 4) {
					currentTeamFetchXhr.abort();
				}

				var customSearch = ($('#kt_tagify_2').val() || '').trim();
				var sortFilter = $('#sortingfilter').val() || '';
				var cacheKey = 'team_' + teamStatusTab + '_p' + page + '_s' + teamPageSize + '_q' + customSearch + '_sort' + sortFilter;

				// Phase 2: Check IndexedDB L2 Persistent Cache first (0ms, 0 HTTP requests)
				if (window.JeroTeamLocalStore) {
					try {
						var cached = await window.JeroTeamLocalStore.get(cacheKey);
						if (cached && cached.html) {
							$('.loadingMessageteamsListajax').css({ display: 'none' });
							localStorage.setItem('currentPageTeam', page);

							if (window.teamState) {
								window.teamState.currentPage = page;
								window.teamState.totalCount = cached.total || 0;
								window.teamState.statusTab = teamStatusTab;
								if (cached.raw_data && Array.isArray(cached.raw_data)) {
									window.teamState.records = cached.raw_data;
								}
							}

							$('#teamsListajax').html(cached.html);
							$('#paginationTeams').html(cached.pagination);
							updateTeamSelectedCount();
							return Promise.resolve(cached);
						}
					} catch(e) {
						// Fall through to server fetch on any cache read exception
					}
				}

				$('.loadingMessageteamsListajax').css({ display: 'block' });
				currentTeamFetchXhr = $.ajax({
					url: `{!! route('service_company.team.data_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: customSearch,
						country: $('#country').val(),
						sortingfilter: sortFilter,
						statusTab: teamStatusTab,
						size: teamPageSize,
						per_page: teamPageSize
					},
					success: function(response) {
						$('.loadingMessageteamsListajax').css({ display: 'none' });
						localStorage.setItem('currentPageTeam', page);

						// Phase 1 Optimization: Cache raw records in RAM
						if (window.teamState) {
							window.teamState.currentPage = page;
							window.teamState.totalCount = response.total || 0;
							window.teamState.statusTab = teamStatusTab;
							if (response.raw_data && Array.isArray(response.raw_data)) {
								window.teamState.records = response.raw_data;
							}
						}

						// Phase 2 Optimization: Store in IndexedDB
						if (window.JeroTeamLocalStore && response && response.html) {
							window.JeroTeamLocalStore.set(cacheKey, response);
						}

						$('#teamsListajax').html(response.html);
						$('#paginationTeams').html(response.pagination);
						updateTeamSelectedCount();

						if (response.counts) {
							$('#span_teamAll').text(response.counts.total || 0);
							$('#span_teamRecruitment').text(response.counts.recruitment || 0);
							$('#span_teamClient').text(response.counts.client || 0);
							$('#span_teamVendor').text(response.counts.vendor || 0);
							$('#span_teamInactive').text(response.counts.inactive || 0);
							$('#span_teamInvited').text(response.counts.invited || 0);

							$('#stat-total-members').text(response.counts.total);
							$('#stat-active-members').text(response.counts.active);
							$('#stat-inactive-members').text(response.counts.inactive);
							if ($('#stat-captain-trm').length) {
								$('#stat-captain-trm').text((response.counts.captain || 0) + (response.counts.trm || 0));
							}
							if ($('#stat-captain-sub').length) {
								$('#stat-captain-sub').text(response.counts.captain || 0);
							}
							if ($('#stat-trm-sub').length) {
								$('#stat-trm-sub').text(response.counts.trm || 0);
							}
							$('#tab-count-all').text('(' + (response.counts.total || 0) + ')');
							$('#tab-count-all-badge').text((response.counts.total || 0) + ' Members');
							$('#tab-count-active').text('(' + (response.counts.active || 0) + ')');
							$('#tab-count-inactive').text('(' + (response.counts.inactive || 0) + ')');
							$('#tab-count-invited').text('(' + (response.counts.invited || 0) + ')');
							if (response.counts.representatives !== undefined) {
								$('#tab-count-reps').text(response.counts.representatives);
							}
						}
					},
					error: function(xhr, status) {
						if (status !== 'abort') {
							$('.loadingMessageteamsListajax').css({ display: 'none' });
						}
					}
				});
				return currentTeamFetchXhr;
			}

			function setTeamTab(tab, el) {
				teamStatusTab = tab;
				$('.tm-stat-grid .tm-stat-card').removeClass('active');
				$(el).addClass('active');
				$('#teamStatusFilterDropdown').val(tab);
				fetchTeams(1);
			}

			function changeStatusDropdown(val) {
				teamStatusTab = val;
				$('.tm-stat-grid .tm-stat-card').removeClass('active');
				$('.tm-stat-grid .tm-stat-card[data-tab="' + val + '"]').addClass('active');
				fetchTeams(1);
			}

			// =========================================================
			// Assign Communication Contacts Right Drawer Controller
			// =========================================================
			function openAssignCommDrawer(trigger) {
				var user = null;
				if (typeof trigger === 'object' && trigger !== null) {
					if (trigger.getAttribute && trigger.getAttribute('data-user')) {
						try { user = JSON.parse(trigger.getAttribute('data-user')); } catch(e){}
					} else if (trigger.id) {
						user = trigger;
					}
				}

				if (!user || !user.id) {
					console.error('Invalid user payload for communication drawer', trigger);
					return;
				}

				$('#commDrawerUserId').val(user.id);
				$('#commDrawerName').text(user.name || 'Member');
				$('#commDrawerRole').text(user.role || 'Member');

				// Avatar
				var $avatar = $('#commDrawerAvatar');
				if (user.image && user.image.length > 5) {
					$avatar.html('<img src="' + user.image + '" alt="' + (user.name || '') + '">');
				} else {
					var initial = user.initial || (user.name ? user.name.charAt(0).toUpperCase() : 'U');
					$avatar.text(initial);
				}

				// Email & Phone
				if (user.email) {
					$('#commDrawerEmail').text(user.email);
					$('#commDrawerEmailWrap').show();
				} else {
					$('#commDrawerEmailWrap').hide();
				}

				if (user.phone) {
					$('#commDrawerPhone').text(user.phone);
					$('#commDrawerPhoneWrap').show();
				} else {
					$('#commDrawerPhoneWrap').hide();
				}

				// Status & Last Active
				var statusStr = (user.status || 'active').toLowerCase();
				if (statusStr === 'active') {
					$('#commDrawerStatusPill').css({ 'background': '#ecfdf5', 'color': '#10b981', 'border-color': '#a7f3d0' });
					$('#commDrawerStatusDot').css('background', '#10b981');
					$('#commDrawerStatusText').text('Active');
				} else {
					$('#commDrawerStatusPill').css({ 'background': '#fffbeb', 'color': '#d97706', 'border-color': '#fde68a' });
					$('#commDrawerStatusDot').css('background', '#f59e0b');
					$('#commDrawerStatusText').text(statusStr.charAt(0).toUpperCase() + statusStr.slice(1));
				}

				$('#commDrawerLastActiveText').text(user.last_active ? 'Last active: ' + user.last_active : 'Never logged in');

				// Reset all channel cards, switches, and selects
				var channels = Array.isArray(user.channels) ? user.channels : [];
				var prios = (user.priorities && typeof user.priorities === 'object') ? user.priorities : {};

				var availableChannels = ['client', 'agency', 'recruitment'];
				availableChannels.forEach(function(ch) {
					var isChecked = channels.indexOf(ch) !== -1;
					if (ch === 'agency' && channels.indexOf('vendor') !== -1) {
						isChecked = true;
					}

					var $card = $('#commCard_' + ch);
					var $toggle = $('#commToggle_' + ch);
					var $prio = $('#commPrio_' + ch);

					$toggle.prop('checked', isChecked);
					if (isChecked) {
						$card.addClass('is-active');
						$prio.prop('disabled', false);
						var userPrio = prios[ch] || (ch === 'agency' ? (prios['vendor'] || 'Primary') : 'Primary');
						$prio.val(userPrio);
					} else {
						$card.removeClass('is-active');
						$prio.prop('disabled', true);
						$prio.val('Primary');
					}
				});

				// Slide open drawer
				$('#commOverlay').addClass('open');
				$('#commDrawer').addClass('open');
				$('body').css('overflow', 'hidden');
			}

			function closeAssignCommDrawer() {
				$('#commOverlay').removeClass('open');
				$('#commDrawer').removeClass('open');
				$('body').css('overflow', '');
			}

			function clearAllCommDrawerRoles() {
				var availableChannels = ['client', 'agency', 'recruitment'];
				availableChannels.forEach(function(ch) {
					$('#commCard_' + ch).removeClass('is-active');
					$('#commToggle_' + ch).prop('checked', false);
					$('#commPrio_' + ch).prop('disabled', true).val('Primary');
				});
			}

			// Handle toggle switch change inside drawer
			$(document).on('change', '.comm-drawer-toggle', function() {
				var ch = $(this).data('channel');
				var isChecked = $(this).is(':checked');
				var $card = $('#commCard_' + ch);
				var $prio = $('#commPrio_' + ch);

				if (isChecked) {
					$card.addClass('is-active');
					$prio.prop('disabled', false);
				} else {
					$card.removeClass('is-active');
					$prio.prop('disabled', true);
				}
			});

			// Save Assignments
			function saveCommunicationRoles() {
				var userId = $('#commDrawerUserId').val();
				if (!userId) return;

				var selectedChannels = [];
				var priorities = {};

				$('.comm-drawer-toggle:checked').each(function() {
					var ch = $(this).data('channel');
					selectedChannels.push(ch);
					if (ch === 'agency') {
						selectedChannels.push('vendor');
					}
					var prioVal = $('#commPrio_' + ch).val() || 'Primary';
					priorities[ch] = prioVal;
					if (ch === 'agency') {
						priorities['vendor'] = prioVal;
					}
				});

				var $btn = $('#btnSaveCommRoles');
				$btn.find('.indicator-label').addClass('d-none');
				$btn.find('.indicator-progress').removeClass('d-none');
				$btn.prop('disabled', true);

				$.ajax({
					url: '{{ route("service_company.team.set_communication_roles") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						user_id: userId,
						channels: selectedChannels,
						priorities: priorities
					},
					success: function(response) {
						$btn.find('.indicator-label').removeClass('d-none');
						$btn.find('.indicator-progress').addClass('d-none');
						$btn.prop('disabled', false);

						closeAssignCommDrawer();

						// Invalidate IndexedDB cache so fresh state displays instantly
						if (window.JeroTeamLocalStore) {
							try { window.JeroTeamLocalStore.clear(); } catch(e){}
						}

						// Update tab badge counters if returned
						if (response.counts) {
							var c = response.counts;
							if (c.total !== undefined) $('.tm-count-all').text(c.total);
							if (c.recruitment !== undefined) $('.tm-count-recruitment').text(c.recruitment);
							if (c.client !== undefined) $('.tm-count-client').text(c.client);
							if (c.vendor !== undefined) $('.tm-count-agency').text(c.vendor);
							if (c.inactive !== undefined) $('.tm-count-inactive').text(c.inactive);
							if (c.invited !== undefined) $('.tm-count-invited').text(c.invited);
						}

						fetchTeams(page_no);

						if (typeof toastr !== 'undefined') {
							toastr.success(response.message || 'Communication roles updated successfully.');
						}
					},
					error: function(xhr) {
						$btn.find('.indicator-label').removeClass('d-none');
						$btn.find('.indicator-progress').addClass('d-none');
						$btn.prop('disabled', false);

						var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update communication roles.';
						if (typeof toastr !== 'undefined') {
							toastr.error(msg);
						} else {
							alert(msg);
						}
					}
				});
			}

			// Backwards compatibility alias
			function openAssignCommModal(userId, userName, channels) {
				openAssignCommDrawer({ id: userId, name: userName, channels: channels || [] });
			}

			$('#commOverlay').on('click', closeAssignCommDrawer);
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' && $('#commDrawer').hasClass('open')) {
					closeAssignCommDrawer();
				}
			});

			// Dynamic Search with 300ms debounce
			var teamSearchTimeout = null;
			$('#kt_tagify_2').on('input', function() {
				var val = $(this).val();
				if (val && val.trim().length > 0) {
					$('.team-search-clear').show();
				} else {
					$('.team-search-clear').hide();
				}
				clearTimeout(teamSearchTimeout);
				teamSearchTimeout = setTimeout(function() {
					fetchTeams(1);
				}, 300);
			});

			// Rows-per-page selector in the footer
			function changeTeamPageSize(val) {
				teamPageSize = parseInt(val) || 10;
				localStorage.setItem('teamPageSize', teamPageSize);
				fetchTeams(1);
			}

			function clearTeamFilters() {
				$('#kt_tagify_2').val('');
				$('.team-search-clear').hide();
				$('#sortingfilter').val('');
				fetchTeams(1);
			}

			// Keep the "N selected" bulk counter in sync
			function updateTeamSelectedCount() {
				$('#teamSelectedCount').text($('.chk_list_item:checked').length);
			}
			$(document).on('change', '.chk_list_item', updateTeamSelectedCount);

			// Export hook — reuses the existing add/export plumbing
			function exportTeam() {
				window.print();
			}

			// Row actions dropdown
			function toggleTeamMenu(btn) {
				var menu = btn.nextElementSibling;
				var isOpen = menu.classList.contains('show');
				document.querySelectorAll('.tm-action-menu.show, .team-action-menu.show').forEach(function(m) { m.classList.remove('show'); });
				if (!isOpen) menu.classList.add('show');
			}
			$(document).on('click', function(e) {
				if (!$(e.target).closest('.tm-action-wrap, .team-action-wrap').length) {
					$('.tm-action-menu.show, .team-action-menu.show').removeClass('show');
				}
			});

			/* =========================================================================
			   Company Communication Representatives System
			   ========================================================================= */

			// Set default representative (Client or Agency)
			function setRepresentative(userId, type, userName) {
				if (!userId || !type) return;

				var typeLabel = type === 'client' ? 'Client' : 'Agency';
				var token = "{{ csrf_token() }}";
				var url = "{{ route('service_company.team.set_default_representative') }}";

				Swal.fire({
					title: 'Set ' + typeLabel + ' Representative?',
					text: 'Designate ' + (userName || 'this user') + ' as the default ' + typeLabel + ' Representative for your organization?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#009ef7',
					cancelButtonColor: '#e2e8f0',
					confirmButtonText: 'Yes, Designate',
					cancelButtonText: 'Cancel',
					customClass: {
						cancelButton: 'btn btn-light text-dark'
					}
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
							type: 'POST',
							url: url,
							headers: {
								'X-Idempotency-Key': generateIdempotencyKey('rep_def_' + type + '_' + userId)
							},
							data: {
								_token: token,
								user_id: userId,
								type: type
							},
							success: function(response) {
								if (response.status === 'success') {
									if (window.JeroTeamLocalStore) {
										window.JeroTeamLocalStore.purge();
									}
									// Update the Spotlight Sub-Card dynamically
									updateSpotlightUI(type, response.representative);

									// Re-fetch current page to update badges and action buttons
									fetchTeams(window.teamState ? window.teamState.currentPage : 1);

									Swal.fire({
										icon: 'success',
										title: typeLabel + ' Representative Updated',
										text: response.message || (userName + ' has been designated as the ' + typeLabel + ' Representative.'),
										timer: 2500,
										showConfirmButton: false
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Action Failed',
										text: response.message || 'Could not update representative.',
										confirmButtonColor: '#009ef7'
									});
								}
							},
							error: function(xhr) {
								Swal.fire({
									icon: 'error',
									title: 'Server Error',
									text: 'An error occurred while updating the representative.',
									confirmButtonColor: '#009ef7'
								});
							}
						});
					}
				});
			}

			// Dynamically updates the Spotlight Sub-Card without full page reload
			function updateSpotlightUI(type, user) {
				if (!user) return;
				var prefix = type === 'client' ? 'Client' : 'Agency';
				var init = (user.name || 'U').charAt(0).toUpperCase();

				var avatarHtml = user.image 
					? '<img src="' + user.image + '" alt="' + user.name + '" style="width:100%; height:100%; border-radius:6px; object-fit:cover;">' 
					: init;

				var phoneHtml = user.mobile ? '<span id="spotlight' + prefix + 'RepMobile"><i class="fa-solid fa-phone me-1"></i>' + user.mobile + '</span>' : '';

				var boxHtml = `
					<div class="tm-mp-avatar">
						${avatarHtml}
					</div>
					<div class="tm-mp-profile-details">
						<div class="tm-mp-profile-name" id="spotlight${prefix}RepName">${user.name}</div>
						<div class="tm-mp-profile-role" id="spotlight${prefix}RepDesig">${user.designation || 'Administrator'}</div>
						<div class="tm-mp-profile-contact">
							<span id="spotlight${prefix}RepEmail"><i class="fa-regular fa-envelope me-1"></i>${user.email}</span>
							${phoneHtml}
						</div>
					</div>
					<button type="button" class="btn-mp-change" onclick="openRepModal('${type}')" title="Change Contact">
						Change Contact
					</button>
				`;
				$('#spotlight' + prefix + 'RepBox').html(boxHtml);
			}

			// Open modal to select default representative
			function openRepModal(type) {
				type = type || 'client';
				$('#repModalTargetType').val(type);
				var typeLabel = type === 'client' ? 'Clients' : 'Partner Agencies';
				$('#repModalTypeLabel').text(typeLabel);

				if (type === 'client') {
					$('#repModalScopeExplain').html('The selected team member will be presented to <strong>Direct Clients</strong> on all job requisitions, candidate review workflows, and organizational profile summaries.');
				} else {
					$('#repModalScopeExplain').html('The selected team member will be presented as the default contact to <strong>Partner Agencies</strong> on shared requisitions (unless individually overridden at the job level).');
				}

				$('#repPickerSearch').val('');
				$('.js-rep-picker-item').show();
				$('#repSelectModal').modal('show');
			}

			// Filter team members inside the selection modal
			function filterRepPicker(query) {
				var q = (query || '').toLowerCase().trim();
				$('.js-rep-picker-item').each(function() {
					var searchData = ($(this).data('search') || '').toLowerCase();
					if (!q || searchData.indexOf(q) !== -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}

			// Confirm selection in the modal
			function submitRepSelection() {
				var $selected = $('input[name="selected_rep_user_id"]:checked');
				if (!$selected.length) {
					Swal.fire({
						icon: 'warning',
						title: 'Select a Team Member',
						text: 'Please select a team member from the list to assign as representative.',
						confirmButtonColor: '#009ef7'
					});
					return;
				}
				var userId = $selected.val();
				var userName = $selected.closest('.js-rep-picker-item').find('.rep-picker-name').text().trim();
				var type = $('#repModalTargetType').val() || 'client';

				$('#repSelectModal').modal('hide');
				setRepresentative(userId, type, userName);
			}

			// Open Job-Level Agency Representation Mapping Modal
			function openAgencySharesModal(userId, userName) {
				$('#agencySharesModal').modal('show');
				var url = "{{ route('service_company.team.job_agency_representatives') }}";
				if (userId) {
					url += '?userId=' + userId;
				}

				$('#agencySharesModalBody').html(`
					<div class="text-center py-5 text-muted">
						<span class="spinner-border spinner-border-sm me-2"></span> Loading agency relationship mapping for ${userName || 'team member'}...
					</div>
				`);

				$.ajax({
					type: 'GET',
					url: url,
					success: function(response) {
						if (response.status === 'success' && response.shares && response.shares.length > 0) {
							var html = `
								<div class="table-responsive">
									<table class="table table-row-dashed table-row-gray-200 align-middle gs-2 gy-3 fs-8">
										<thead>
											<tr class="fw-bold text-muted text-uppercase fs-9 border-bottom border-gray-200">
												<th style="min-width: 140px;">Job Requisition</th>
												<th style="min-width: 130px;">Job Created By</th>
												<th style="min-width: 130px;">Partner Agency</th>
												<th style="min-width: 150px;">Authoritative Representative</th>
												<th style="min-width: 100px;">Shared Date</th>
												<th class="text-end" style="min-width: 90px;">Action</th>
											</tr>
										</thead>
										<tbody>
							`;

							response.shares.forEach(function(share) {
								var creatorName = share.job_creator ? share.job_creator.name : 'Author';
								var creatorEmail = share.job_creator ? share.job_creator.email : '';
								var repName = share.representative ? share.representative.name : 'Default Contact';
								var repEmail = share.representative ? share.representative.email : '';
								var isCreatorRep = share.is_creator_the_rep;

								html += `
									<tr>
										<td>
											<div class="fw-bold text-dark fs-7">${share.job_title}</div>
											<div class="text-muted fs-9">Job #${share.job_id}</div>
										</td>
										<td>
											<div class="fw-semibold text-gray-800">${creatorName}</div>
											<div class="text-muted fs-9">${creatorEmail}</div>
										</td>
										<td>
											<div class="fw-semibold text-gray-800"><i class="fa-solid fa-handshake text-muted me-1"></i>${share.agency_name}</div>
										</td>
										<td>
											<div class="d-flex align-items-center gap-1">
												<span class="badge-rep-pill badge-rep-agency fs-9">Agency Rep</span>
												<span class="fw-bold text-gray-900">${repName}</span>
											</div>
											<div class="text-muted fs-9">${repEmail}</div>
											${isCreatorRep ? '<div class="text-success fs-9 mt-1"><i class="fa-solid fa-check me-1"></i>Also created the job</div>' : '<div class="text-primary fs-9 mt-1"><i class="fa-solid fa-share me-1"></i>Shared by this user</div>'}
										</td>
										<td>
											<div class="text-muted">${share.shared_at}</div>
										</td>
										<td class="text-end">
											<button type="button" class="btn btn-xs btn-light-primary fw-bold fs-9 py-1 px-2" onclick="reassignJobAgencyRep(${share.job_id}, ${share.agency_id}, ${share.representative_user_id}, '${escapeQuote(share.agency_name)}', '${escapeQuote(share.job_title)}')">
												<i class="fa-solid fa-user-pen fs-9 me-1"></i>Reassign
											</button>
										</td>
									</tr>
								`;
							});

							html += `
										</tbody>
									</table>
								</div>
							`;
							$('#agencySharesModalBody').html(html);
						} else {
							$('#agencySharesModalBody').html(`
								<div class="text-center py-5 text-muted">
									<i class="fa-solid fa-diagram-project fa-2x mb-2 text-muted"></i>
									<div class="fw-bold fs-7 text-dark">No Agency Job Shares Found</div>
									<div class="fs-8 mt-1">${userName || 'This user'} has not shared any job requisitions with partner agencies yet.</div>
								</div>
							`);
						}
					},
					error: function() {
						$('#agencySharesModalBody').html(`
							<div class="text-center py-4 text-danger fs-8">
								<i class="fa-solid fa-circle-exclamation me-1"></i> Failed to load agency relationships. Please try again.
							</div>
						`);
					}
				});
			}

			// Reassign Job Agency Representative
			function reassignJobAgencyRep(jobId, agencyCompanyId, currentUserId, agencyName, jobTitle) {
				var url = "{{ route('service_company.team.representatives_data') }}";
				$.get(url, function(res) {
					if (res.status === 'success' && res.eligible_users && res.eligible_users.length > 0) {
						var options = {};
						res.eligible_users.forEach(function(u) {
							options[u.id] = u.name + ' (' + (u.designation || u.role_name) + ')';
						});

						Swal.fire({
							title: 'Reassign Agency Representative',
							html: `
								<div class="text-start fs-8 mb-3">
									<div><strong>Requisition:</strong> ${jobTitle}</div>
									<div><strong>Partner Agency:</strong> ${agencyName}</div>
									<div class="text-muted mt-1">Select the team member who will act as the authoritative representative for this agency on this job:</div>
								</div>
							`,
							input: 'select',
							inputOptions: options,
							inputValue: currentUserId,
							showCancelButton: true,
							confirmButtonText: 'Reassign Representative',
							confirmButtonColor: '#009ef7',
							cancelButtonColor: '#e2e8f0',
							customClass: { cancelButton: 'btn btn-light text-dark' }
						}).then((sel) => {
							if (sel.isConfirmed && sel.value) {
								var newUserId = sel.value;
								$.ajax({
									type: 'POST',
									url: "{{ route('service_company.team.change_job_representative') }}",
									headers: {
										'X-Idempotency-Key': generateIdempotencyKey('reassign_' + jobId + '_' + agencyCompanyId + '_' + newUserId)
									},
									data: {
										_token: "{{ csrf_token() }}",
										job_id: jobId,
										agency_company_id: agencyCompanyId,
										new_user_id: newUserId
									},
									success: function(resp) {
										if (resp.status === 'success') {
											if (window.JeroTeamLocalStore) {
												window.JeroTeamLocalStore.purge();
											}
											Swal.fire({ icon: 'success', title: 'Reassigned', text: resp.message, timer: 2000, showConfirmButton: false });
											openAgencySharesModal(null, '');
											fetchTeams();
										} else {
											Swal.fire({ icon: 'error', title: 'Failed', text: resp.message });
										}
									},
									error: function() {
										Swal.fire({ icon: 'error', title: 'Error', text: 'Server error while reassigning.' });
									}
								});
							}
						});
					}
				});
			}

			function escapeQuote(str) {
				return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
			}



        </script>
    @endpush
