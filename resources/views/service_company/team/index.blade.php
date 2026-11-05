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
            padding: 8px 16px !important;
            margin-bottom: 8px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
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
            margin-bottom: 8px !important;
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
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            flex-wrap: wrap;
            margin-bottom: 8px;
            box-sizing: border-box;
            width: 100%;
        }
        .team-filter-search {
            position: relative;
            flex: 1 1 auto;
            min-width: 260px;
            display: flex;
            align-items: center;
        }
        .team-filter-search i.fa-magnifying-glass {
            position: absolute;
            left: 12px;
            font-size: 13px;
            color: #94a3b8;
            pointer-events: none;
            z-index: 2;
        }
        .team-filter-search .team-search-input {
            width: 100%;
            height: 38px;
            padding: 0 32px 0 36px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12.5px;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            outline: none;
            transition: all .15s ease;
            background: #f8fafc;
            box-sizing: border-box;
        }
        .team-filter-search .team-search-input:focus {
            background: #ffffff;
            border-color: #009ef7;
            box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
        }
        .team-search-clear {
            position: absolute;
            right: 8px;
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 13px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            z-index: 2;
            transition: all .15s ease;
        }
        .team-search-clear:hover { background: #e2e8f0; color: #0f172a; }

        .team-filter-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .team-filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
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
        .team-sort-dropdown-wrap {
            position: relative;
        }
        .team-sort-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 36px;
            padding: 0 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }
        .team-sort-pill:hover, .team-sort-pill:focus, .team-sort-pill[aria-expanded="true"] {
            border-color: #009ef7;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
            color: #009ef7;
        }
        .team-sort-label {
            font-weight: 500;
            color: #64748b;
        }
        .team-sort-current {
            font-weight: 600;
            color: #0f172a;
        }
        .team-sort-menu {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 6px 0;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            min-width: 250px;
            z-index: 1050;
        }
        .team-sort-menu .dropdown-item {
            padding: 7px 14px;
            font-weight: 500;
            color: #334155;
            display: flex;
            align-items: center;
            transition: background .12s ease;
        }
        .team-sort-menu .dropdown-item:hover {
            background: #f1f5f9;
            color: #009ef7;
        }
        .team-sort-menu .dropdown-item.active {
            background: #eff6ff;
            color: #009ef7;
            font-weight: 600;
        }
        .team-btn-reset-filters {
            display: inline-flex;
            align-items: center;
            height: 36px;
            padding: 0 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11.5px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }
        .team-btn-reset-filters:hover {
            background: #eff6ff;
            border-color: #009ef7;
            color: #009ef7;
        }
        .team-select-control {
            height: 38px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
            padding: 0 12px;
            outline: none;
            cursor: pointer;
            transition: all .15s ease;
            box-sizing: border-box;
            min-width: 200px;
        }
        .team-select-control:focus {
            border-color: #009ef7;
            box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
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

        /* =========================================================================
           TIER 3: CARD + SUB-CARD STREAM & 5-COLUMN LEGEND (Rule 20 Enterprise Standard)
           ========================================================================= */
        .team-roster-wrap {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .team-roster-inner {
            min-width: 1020px;
        }

        /* 5-Column Grid Definition for Sub-Cards (Zero Overflow on 15.6" screens) */
        .tm-subcards-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1.65fr) minmax(0, 1.15fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 1.8fr) !important;
            gap: 8px !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }

        .team-cards-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Master Primary Card */
        .tm-primary-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.2s cubic-bezier(.4, 0, .2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
            box-sizing: border-box;
        }
        .tm-primary-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07) !important;
            transform: translateY(-1px);
        }

        /* Discrete Sub-Card Base Container */
        .tm-subcard {
            background: #f8fafc !important;
            border: 1px solid #edf2f7 !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            position: relative;
            box-sizing: border-box;
        }
        .tm-subcard-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
            min-width: 0;
            gap: 6px;
            flex-wrap: nowrap;
        }
        .tm-subcard-title-wrap {
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
        }
        .tm-subcard-icon {
            flex-shrink: 0;
        }
        .tm-subcard-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tm-subcard-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            white-space: nowrap;
            flex-shrink: 0;
            line-height: 1.25;
        }
        .badge-owner {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-rep {
            background: #eff6ff;
            color: #009ef7;
            border: 1px solid #bfdbfe;
        }
        .badge-count {
            background: #e2e8f0;
            color: #475569;
        }
        .badge-role {
            background: #f1f5f9;
            color: #475569;
        }
        .badge-month {
            background: #eef2ff;
            color: #4f46e5;
        }
        .badge-member {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        /* Sub-Card 1: Member Identity */
        .tm-identity-body {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
        }
        .tm-avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            margin-top: 1px;
        }
        .tm-avatar:hover {
            transform: scale(1.04);
            box-shadow: 0 2px 6px rgba(0, 158, 247, 0.2);
        }
        .tm-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .tm-identity-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1 1 0%;
            overflow: hidden;
        }
        .tm-member-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            transition: color 0.15s ease;
        }
        .tm-member-name:hover { color: #009ef7; }
        .tm-member-designation {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .tm-member-email-wrap {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .tm-member-email-link {
            color: inherit;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .tm-member-email-link:hover { color: #009ef7; }

        /* Sub-Card 2: Dedicated System Role & Permissions */
        .tm-role-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            gap: 6px;
        }
        .tm-role-badge-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .tm-role-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }
        .role-admin   { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
        .role-lead    { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .role-default { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        .tm-role-action-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-change-role {
            font-size: 11px;
            font-weight: 600;
            padding: 3.5px 8px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .btn-change-role:hover {
            background: #eff6ff;
            border-color: #009ef7;
            color: #009ef7;
        }
        .tm-role-locked-note {
            font-size: 10px;
            font-weight: 600;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Sub-Card 3: Workload & Ops */
        .tm-workload-body {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        .tm-wl-row {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }
        .tm-wl-row i {
            width: 14px;
            text-align: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .tm-wl-row strong {
            font-weight: 700;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }

        /* Sub-Card 4: Dedicated Account Status (3 Clear Rows per Rule 20) */
        .tm-status-body {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-width: 0;
            gap: 2.5px;
        }
        .tm-status-row {
            display: flex;
            align-items: center;
            min-width: 0;
            line-height: 1.25;
        }
        .tm-status-row-control {
            margin-bottom: 1px;
        }
        .ios-switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 19px;
            flex-shrink: 0;
            margin: 0;
        }
        .ios-switch input { opacity: 0; width: 0; height: 0; }
        .ios-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #cbd5e1;
            transition: .2s;
            border-radius: 19px;
        }
        .ios-slider:before {
            position: absolute;
            content: "";
            height: 13px;
            width: 13px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .2s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .ios-switch input:checked + .ios-slider { background-color: #009ef7; }
        .ios-switch input:checked + .ios-slider:before { transform: translateX(15px); }
        .ios-switch input:disabled + .ios-slider { opacity: .6; cursor: not-allowed; }
        .tm-status-row-label .tm-status-text {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
        }
        .tm-status-row-sub .tm-status-subnote {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
            white-space: nowrap;
        }
        .btn-resend-invite-icon {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1px solid #fde68a;
            background: #fffbeb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
        }
        .btn-resend-invite-icon:hover {
            background: #fef3c7;
            border-color: #f59e0b;
        }
        .btn-resend-invite {
            font-size: 10.5px;
            font-weight: 600;
            color: #009ef7;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-resend-invite:hover {
            text-decoration: underline;
        }

        /* Sub-Card 5: Contact Details Visibility (Marketplace Enquiries) */
        .tm-subcard-comm {
            cursor: pointer;
            transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
        }
        .tm-subcard-comm:hover {
            border-color: #93c5fd !important;
            background: #f8fbff !important;
            box-shadow: 0 2px 8px rgba(0, 158, 247, 0.08);
        }
        .tm-subcard-comm:hover .btn-edit-comm-icon {
            border-color: #009ef7;
            color: #009ef7;
            background: #eff6ff;
        }
        .tm-comm-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            min-width: 0;
            width: 100%;
            flex: 1;
        }
        .tm-comm-rows-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 100%;
        }
        .tm-comm-row {
            display: flex;
            align-items: center;
            width: 100%;
            line-height: 1;
        }
        .tm-comm-chip {
            width: 100%;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 9.5px;
            font-weight: 600;
            padding: 1.5px 6px;
            border-radius: 4px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            box-sizing: border-box;
            border: 1px solid transparent;
        }
        .tm-comm-chip.client      { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
        .tm-comm-chip.vendor      { background: #faf5ff; color: #9333ea; border-color: #e9d5ff; }
        .tm-comm-chip.agency      { background: #faf5ff; color: #9333ea; border-color: #e9d5ff; }
        .tm-comm-chip.recruitment { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
        .tm-comm-chip.bench       { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
        .tm-comm-chip i {
            font-size: 8.5px;
            width: 11px;
            text-align: center;
            flex-shrink: 0;
        }
        .btn-comm-change-action {
            font-size: 10px;
            font-weight: 600;
            padding: 1.5px 7px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #009ef7;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            white-space: nowrap;
            line-height: 1.25;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .btn-comm-change-action:hover {
            background: #eff6ff;
            border-color: #009ef7;
            color: #0077ba;
            box-shadow: 0 1px 3px rgba(0, 158, 247, 0.15);
        }
        .tm-subcard-comm:hover .btn-comm-change-action {
            border-color: #009ef7;
            background: #eff6ff;
            color: #009ef7;
        }
        .btn-edit-comm-icon {
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
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-edit-comm-icon:hover {
            background: #eff6ff;
            color: #009ef7;
            border-color: #009ef7;
        }
        .btn-assign-comm-empty {
            font-size: 11px;
            font-weight: 600;
            padding: 3.5px 9px;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-assign-comm-empty:hover {
            border-color: #009ef7;
            border-style: solid;
            background: #eff6ff;
            color: #009ef7;
        }

        /* Sub-Card 6: Bottom Activity & Performance Strip (Swapped to Bottom Strip) */
        .tm-card-bottom-strip {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 6px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            gap: 12px;
        }
        .tm-bottom-left-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            min-width: 0;
        }
        .tm-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .tm-meta-dot {
            color: #cbd5e1;
        }
        .dot-pulse {
            width: 6.5px;
            height: 6.5px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .dot-green { background: #10b981; }
        .dot-amber { background: #f59e0b; }
        .dot-gray  { background: #94a3b8; }

        .tm-bottom-right-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .tm-perf-strip-box {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 2.5px 8px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            font-size: 11px;
            color: #166534;
        }
        .tm-perf-rank {
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .tm-perf-month {
            font-size: 9.5px;
            color: #4b5563;
            font-weight: 500;
        }
        .tm-perf-score {
            font-size: 10.5px;
            font-weight: 600;
        }
        .btn-view-score {
            font-size: 10px;
            font-weight: 700;
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 4px;
            padding: 1.5px 7px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-view-score:hover {
            background: #a7f3d0;
            color: #047857;
        }

        .btn-view-profile {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3.5px 9px;
            border-radius: 6px;
            background: #eff6ff;
            color: #009ef7;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #bfdbfe;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-view-profile:hover {
            background: #009ef7;
            color: #ffffff;
            border-color: #009ef7;
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
        /* Guard against any duplicate pagination inside stream container */
        #teamsListajax .team-pagination-bar {
            display: none !important;
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

        /* Responsive Breakpoints & 15.6" Screen Optimization (Strictly 6 Cards on 1 Row) */
        @media (max-width: 1440px) {
            .tm-stat-grid {
                grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                gap: 6px !important;
            }
            .tm-stat-card {
                padding: 8px 6px !important;
            }
            .tm-card-top {
                gap: 6px !important;
            }
            .tm-card-icon {
                width: 28px !important;
                height: 28px !important;
                min-width: 28px !important;
                font-size: 12px !important;
            }
            .tm-card-num {
                font-size: 15px !important;
            }
            .tm-card-lbl {
                font-size: 10px !important;
            }
            .tm-card-sub {
                font-size: 8.5px !important;
            }
            .tm-subcards-grid {
                grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.1fr) minmax(0, 1.2fr) minmax(0, 1.1fr) minmax(0, 1.8fr) !important;
                gap: 6px !important;
            }
        }
        @media (max-width: 1150px) {
            .tm-stat-grid {
                grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                gap: 5px !important;
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
            .tm-subcards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }
        }
        @media (max-width: 640px) {
            .tm-subcards-grid {
                grid-template-columns: 1fr !important;
            }
            .tm-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
            .tm-card-bottom-strip {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
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
            width: 580px;
            max-width: 100vw;
            background: #f8fafc;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
            box-shadow: -10px 0 35px rgba(15, 23, 42, 0.15);
        }
        .invite-drawer.open { transform: translateX(0); }

        /* Header: Deep Vibrant Blue with Integrated Action Buttons */
        .invite-drawer-head {
            padding: 14px 20px;
            background: linear-gradient(135deg, #009ef7 0%, #0077c8 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            gap: 12px;
        }
        .invite-drawer-title-wrap {
            min-width: 0;
            flex: 1;
        }
        .invite-drawer-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .invite-drawer-subtitle {
            font-size: 11px;
            color: #e0f2fe;
            margin: 2px 0 0 0;
            font-family: 'Poppins', sans-serif;
            opacity: 0.92;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .invite-head-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .invite-head-btn-cancel {
            height: 33px;
            padding: 0 13px;
            border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.14);
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            cursor: pointer;
            transition: all .15s ease;
        }
        .invite-head-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.26);
            border-color: rgba(255, 255, 255, 0.65);
        }
        .invite-head-btn-send {
            height: 33px;
            padding: 0 15px;
            border-radius: 7px;
            border: none;
            background: #ffffff;
            font-size: 12px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            color: #008be3;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            transition: all .15s ease;
        }
        .invite-head-btn-send:hover {
            background: #f8fafc;
            color: #0077c8;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
        }
        .invite-head-btn-send:disabled {
            opacity: .7;
            cursor: not-allowed;
        }
        .invite-spinner { display: none; }
        .invite-head-btn-send.loading .invite-label { display: none; }
        .invite-head-btn-send.loading .invite-spinner { display: inline-flex !important; align-items: center; }
        .invite-head-close {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: background .15s, border-color .15s;
            flex-shrink: 0;
            margin-left: 2px;
        }
        .invite-head-close:hover {
            background: rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.6);
            color: #ffffff;
        }

        /* Body: Compact padding, fits 15.6" screen with ZERO vertical scroll needed */
        .invite-drawer-body {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 14px 18px 10px;
            background: #f8fafc;
        }
        .invite-drawer-body::-webkit-scrollbar {
            width: 4px;
        }
        .invite-drawer-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Card + Sub-Card Enterprise Styling (Rule 20) */
        .invite-subcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 13px 15px 9px;
            margin-bottom: 11px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .invite-subcard:hover {
            border-color: #cbd5e1;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.05);
        }
        .invite-subcard-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
        }
        .invite-subcard-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .invite-subcard-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 999px;
            line-height: 1.25;
            font-family: 'Poppins', sans-serif;
        }
        .invite-subcard-badge.badge-req {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .invite-subcard-badge.badge-opt {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .invite-subcard-hint {
            font-size: 11px;
            color: #64748b;
            margin: -3px 0 9px;
            line-height: 1.35;
            font-family: 'Poppins', sans-serif;
        }

        /* 2-Column Responsive Grid */
        .invite-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 7px;
        }
        .invite-field { margin-bottom: 7px; }
        .invite-field-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 3px;
            font-family: 'Poppins', sans-serif;
        }
        .invite-field-label .req { color: #ef4444; margin-left: 1px; }
        .invite-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .invite-input-icon {
            position: absolute;
            left: 11px;
            color: #94a3b8;
            font-size: 13.5px;
            pointer-events: none;
            line-height: 1;
        }
        .invite-input-wrap input,
        .invite-input-wrap select {
            width: 100%;
            height: 38px;
            border: 1px solid #d8deec;
            border-radius: 6px;
            padding: 0 12px 0 34px;
            font-size: 12.5px;
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
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
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, .12);
        }
        /* chevron for select */
        .invite-select-wrap { position: relative; }
        .invite-select-wrap::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 12px; height: 12px;
            background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'><path d='M6 9l6 6 6-6'/></svg>") center/contain no-repeat;
        }
        .invite-field-hint {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 3px;
            margin-bottom: 0;
            line-height: 1.35;
            font-family: 'Poppins', sans-serif;
        }
        .invite-secure-strip {
            text-align: center;
            font-size: 10.5px;
            color: #94a3b8;
            padding: 4px 0 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: 'Poppins', sans-serif;
        }

        @media (max-width: 576px) {
            .invite-drawer { width: 100vw; }
            .invite-row-2col { grid-template-columns: 1fr; gap: 6px; }
            .invite-drawer-head { flex-wrap: wrap; }
            .invite-head-actions { width: 100%; justify-content: flex-end; margin-top: 6px; }
        }

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
        /* =========================================================================
           COMMUNICATION RIGHT DRAWER: MODERN CARD + SUB-CARD ENTERPRISE STANDARD
           Strictly single page viewport fit (Zero vertical scroller on 15.6" screens)
           ========================================================================= */
        .comm-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 530px;
            max-width: 100vw;
            background: #ffffff;
            box-shadow: -12px 0 35px -5px rgba(15, 23, 42, 0.15);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .25s cubic-bezier(.4, 0, .2, 1);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
        }
        .comm-drawer.open {
            transform: translateX(0);
        }
        .comm-drawer-head {
            padding: 12px 18px 10px 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
        }
        .comm-drawer-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.25;
        }
        .comm-drawer-subtitle {
            font-size: 10.5px;
            color: #64748b;
            margin: 2px 0 0 0;
            line-height: 1.35;
        }
        .comm-drawer-close {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid transparent;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            flex-shrink: 0;
            font-size: 13px;
        }
        .comm-drawer-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .comm-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 10px 18px;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .comm-drawer-body::-webkit-scrollbar {
            width: 5px;
        }
        .comm-drawer-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .comm-drawer-body::-webkit-scrollbar-track {
            background: transparent;
        }

        /* CARD 1: Member Profile Summary Sub-Card */
        .comm-user-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 11px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-shrink: 0;
        }
        .comm-user-card-left {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }
        .comm-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
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
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .comm-role-badge {
            background: #eff6ff;
            color: #009ef7;
            font-size: 9.5px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid #dbeafe;
            white-space: nowrap;
        }
        .comm-user-contacts {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 1px;
            font-size: 10.5px;
            color: #64748b;
            white-space: nowrap;
        }
        .comm-user-card-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
            flex-shrink: 0;
        }
        .comm-status-pill {
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #a7f3d0;
            border-radius: 20px;
            padding: 1px 7px;
            font-size: 9.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            line-height: 1.3;
        }
        .comm-status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #10b981;
        }
        .comm-last-active-text {
            font-size: 9.5px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            line-height: 1.2;
        }
        .comm-last-active-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        /* CARD 2: Dedicated Keep Private Card */
        .comm-card-private {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 11px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            transition: all .15s ease;
            flex-shrink: 0;
        }
        .comm-card-private.is-private-active {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        /* CARD 3: Parent Card for Inbound Channels */
        .comm-channels-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 11px;
            display: flex;
            flex-direction: column;
            gap: 7px;
            flex-shrink: 0;
        }
        .comm-channels-head {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding-bottom: 5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .comm-channels-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .comm-channels-title-wrap {
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 0;
        }
        .comm-channels-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }
        .comm-channels-hint {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.35;
            white-space: normal;
        }
        .comm-btn-clear {
            background: transparent;
            border: none;
            color: #009ef7;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            transition: all .15s ease;
            flex-shrink: 0;
        }
        .comm-btn-clear:hover {
            color: #0077ba;
            background: #f1f5f9;
            text-decoration: none;
        }

        .comm-channels-subcards {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Sub-Card Base (Discrete Channel Row) */
        .comm-card {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 7px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            transition: all .15s ease;
        }
        .comm-card.is-active {
            border-color: #93c5fd;
            background: #f8fbff;
        }
        .comm-card-main {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }
        .comm-card-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            flex-shrink: 0;
        }
        .comm-card-icon.icon-private     { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .comm-card-icon.icon-client      { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .comm-card-icon.icon-agency      { background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff; }
        .comm-card-icon.icon-recruitment { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }

        .comm-card-text {
            min-width: 0;
        }
        .comm-card-title {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            white-space: nowrap;
        }
        .comm-card-desc {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.3;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .comm-card-controls {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        /* Micro iOS Toggle Switch (Compact 34×18px) */
        .comm-ios-switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 18px;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
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
            transition: .2s ease;
            border-radius: 18px;
        }
        .comm-ios-slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .2s ease;
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        .comm-ios-switch input:checked + .comm-ios-slider {
            background-color: #009ef7;
        }
        .comm-ios-switch input:checked + .comm-ios-slider:before {
            transform: translateX(16px);
        }

        /* CARD 4: Contextual Validation Alert Box (Only shown when action required) */
        .comm-alert-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 7px;
            padding: 8px 11px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .comm-alert-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fef3c7;
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .comm-alert-content {
            flex: 1;
            min-width: 0;
        }
        .comm-alert-title {
            font-size: 11px;
            font-weight: 700;
            color: #92400e;
            line-height: 1.25;
        }
        .comm-alert-message {
            font-size: 10px;
            color: #b45309;
            line-height: 1.35;
            margin-top: 2px;
        }
        .comm-alert-close {
            background: transparent;
            border: none;
            color: #b45309;
            font-size: 11px;
            cursor: pointer;
            padding: 0 2px;
            opacity: 0.7;
            transition: opacity 0.15s ease;
        }
        .comm-alert-close:hover {
            opacity: 1;
        }

        /* CARD 5: Visibility & Privacy Micro-Strip (Subtle Enterprise Palette) */
        .comm-info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 7px 11px;
            display: flex;
            align-items: center;
            gap: 9px;
            flex-shrink: 0;
        }
        .comm-info-icon {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .comm-info-text {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.35;
        }
        .comm-info-text strong {
            color: #334155;
            font-weight: 700;
        }

        /* Drawer Footer (Compact Standard) */
        .comm-drawer-foot {
            padding: 10px 18px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-shrink: 0;
        }
        .comm-btn-cancel {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            font-weight: 600;
            font-size: 11.5px;
            padding: 6px 14px;
            border-radius: 6px;
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
            font-size: 11.5px;
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
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
                        <!-- Action Button: Add/Invite Members -->
                        <button type="button" class="team-btn-primary" id="openInviteDrawer">
                            <i class="fa-solid fa-plus text-white" style="font-size: 11px;"></i>
                            <span>Add/Invite Members</span>
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
                                <span id="span_teamActiveSub">+{{ (int) ($teamCounts['active'] ?? 0) }} active</span>
                            </div>
                        </a>

                        {{-- Card 2: Bench Talent Enquiries --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('recruitment', this);" class="tm-stat-card" data-tab="recruitment">
                            <div class="tm-card-top">
                                <div class="tm-card-icon indigo">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamRecruitment">{{ $teamCounts['recruitment'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Bench Talent Enquiries">Bench Talent Enquiries</div>
                                </div>
                            </div>
                            <div class="tm-card-sub indigo">
                                <i class="fa-solid fa-user-check" style="font-size: 8px;"></i>
                                <span>Bench Talent Representative</span>
                            </div>
                        </a>

                        {{-- Card 3: Client & Sales Enquiries --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('client', this);" class="tm-stat-card" data-tab="client">
                            <div class="tm-card-top">
                                <div class="tm-card-icon green">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamClient">{{ $teamCounts['client'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Client & Sales Enquiries">Client &amp; Sales Enquiries</div>
                                </div>
                            </div>
                            <div class="tm-card-sub green">
                                <i class="fa-solid fa-handshake" style="font-size: 8px;"></i>
                                <span>Client Representative</span>
                            </div>
                        </a>

                        {{-- Card 4: Agency Queries --}}
                        <a href="javascript:void(0);" onclick="setTeamTab('vendor', this);" class="tm-stat-card" data-tab="vendor">
                            <div class="tm-card-top">
                                <div class="tm-card-icon purple">
                                    <i class="fa-solid fa-handshake-angle"></i>
                                </div>
                                <div class="tm-card-meta">
                                    <div class="tm-card-num" id="span_teamVendor">{{ $teamCounts['vendor'] ?? 0 }}</div>
                                    <div class="tm-card-lbl" title="Agency Queries">Agency Queries</div>
                                </div>
                            </div>
                            <div class="tm-card-sub purple">
                                <i class="fa-solid fa-store" style="font-size: 8px;"></i>
                                <span>Agency Representative</span>
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
                        <input type="text" id="kt_tagify_2" class="team-search-input" placeholder="Search by name, email, phone or ID..." autocomplete="off" />
                        <button type="button" class="team-search-clear" id="teamSearchClearBtn" onclick="clearTeamFilters()" title="Clear Search">✕</button>
                    </div>

                    <div class="team-filter-controls">
                        <!-- Hidden input to maintain JS status filter compatibility -->
                        <input type="hidden" id="teamStatusFilterDropdown" value="all" />

                        <!-- Compact Metronic Sort Pill (Rule 20 Enterprise Standard) -->
                        <div class="dropdown team-sort-dropdown-wrap">
                            <button type="button" class="team-sort-pill dropdown-toggle" id="teamSortDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-arrow-down-short-wide text-muted me-1"></i>
                                <span class="team-sort-label">Sort:</span>
                                <span class="team-sort-current" id="teamSortCurrentText">Last Active</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end team-sort-menu shadow-sm" aria-labelledby="teamSortDropdownBtn">
                                <li><a class="dropdown-item js-sort-opt active" href="javascript:void(0);" onclick="selectTeamSort('', 'Last Active', this)"><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i>Last Active</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('joined_newest', 'Joined: Newest to Oldest', this)"><i class="fa-solid fa-calendar-plus me-2 text-muted"></i>Joined: Newest to Oldest</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('joined_oldest', 'Joined: Oldest to Newest', this)"><i class="fa-solid fa-calendar-minus me-2 text-muted"></i>Joined: Oldest to Newest</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('name_asc', 'Name: A to Z', this)"><i class="fa-solid fa-arrow-down-a-z me-2 text-muted"></i>Name: A to Z</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('name_desc', 'Name: Z to A', this)"><i class="fa-solid fa-arrow-down-z-a me-2 text-muted"></i>Name: Z to A</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('designation_asc', 'Designation: A to Z', this)"><i class="fa-solid fa-id-badge me-2 text-muted"></i>Designation: A to Z</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('role_asc', 'System Role: A to Z', this)"><i class="fa-solid fa-shield-halved me-2 text-muted"></i>System Role: A to Z</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('active_jobs_desc', 'Workload: Active Jobs', this)"><i class="fa-solid fa-briefcase me-2 text-muted"></i>Workload: Active Jobs (High to Low)</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('bench_talents_desc', 'Workload: Bench Talent Rep', this)"><i class="fa-solid fa-users me-2 text-muted"></i>Workload: Bench Talent Rep (High to Low)</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('score_desc', 'Performance Score', this)"><i class="fa-solid fa-chart-line me-2 text-muted"></i>Performance Score (High to Low)</a></li>
                                <li><a class="dropdown-item js-sort-opt" href="javascript:void(0);" onclick="selectTeamSort('status_active', 'Status: Active First', this)"><i class="fa-solid fa-circle-check me-2 text-muted"></i>Account Status: Active First</a></li>
                            </ul>
                            <!-- Hidden input for backwards-compatibility -->
                            <input type="hidden" id="sortingfilter" value="" />
                        </div>

                        <!-- Dedicated Reset Filters Action -->
                        <button type="button" class="team-btn-reset-filters" onclick="resetTeamFilters()" title="Reset All Filters to Default">
                            <i class="fa-solid fa-rotate-left me-1"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- TIER 3: CARD-DRIVEN MEMBER STREAM (Direct Card Flow)           -->
                <!-- ============================================================== -->
                <div class="team-roster-wrap">
                    <div class="team-roster-inner">
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
            {{-- Header: Blue Background with Integrated Action Buttons --}}
            <div class="invite-drawer-head">
                <div class="invite-drawer-title-wrap">
                    <h3 class="invite-drawer-title">
                        <i class="fa-solid fa-user-plus" style="font-size: 14px; opacity: 0.92;"></i> Invite Team Member
                    </h3>
                    <p class="invite-drawer-subtitle">Add colleague &amp; configure role permissions</p>
                </div>
                <div class="invite-head-actions">
                    <button type="button" class="invite-head-btn-cancel" id="inviteCancelBtn">Cancel</button>
                    <button type="button" class="invite-head-btn-send" id="inviteSubmitBtn">
                        <span class="invite-label"><i class="fa-solid fa-paper-plane me-1"></i> Send Invitation</span>
                        <span class="invite-spinner" style="display:none;"><span class="spinner-border spinner-border-sm me-1"></span> Sending...</span>
                    </button>
                    <button type="button" class="invite-head-close" id="inviteDrawerClose" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Body: Compact 2-Subcard Layout, Zero Vertical Scroll on 15.6" Laptops --}}
            <div class="invite-drawer-body">
                <form id="inviteForm" enctype="multipart/form-data">
                    @csrf

                    {{-- Sub-Card 1: Account Identity & Role (Required) --}}
                    <div class="invite-subcard">
                        <div class="invite-subcard-head">
                            <h4 class="invite-subcard-title">
                                <i class="fa-solid fa-user-shield text-primary"></i> Account Identity &amp; Role
                            </h4>
                            <span class="invite-subcard-badge badge-req">Required</span>
                        </div>

                        {{-- Row 1: Full Name & Work Email side-by-side --}}
                        <div class="invite-row-2col">
                            <div class="invite-field">
                                <label class="invite-field-label">Full Name <span class="req">*</span></label>
                                <div class="invite-input-wrap">
                                    <span class="invite-input-icon"><i class="fa-regular fa-user"></i></span>
                                    <input type="text" name="name" id="inviteName" placeholder="Enter full name" autocomplete="off" />
                                </div>
                            </div>

                            <div class="invite-field">
                                <label class="invite-field-label">Work Email <span class="req">*</span></label>
                                <div class="invite-input-wrap">
                                    <span class="invite-input-icon"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" name="email" id="inviteEmail" placeholder="name@company.com" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" />
                                </div>
                                <div id="inviteEmail_typo_hint" class="team-email-typo-hint" style="display: none; margin-top: 4px; font-size: 11px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 5px; padding: 3px 6px;"></div>
                            </div>
                        </div>

                        {{-- Row 2: Role & Permissions --}}
                        <div class="invite-field" style="margin-bottom: 4px;">
                            <label class="invite-field-label">Role &amp; Permissions <span class="req">*</span></label>
                            <div class="invite-input-wrap invite-select-wrap">
                                <span class="invite-input-icon"><i class="fa-solid fa-shield-halved"></i></span>
                                <select name="role_id" id="inviteRole">
                                    <option value="" disabled selected>Select role</option>
                                    {{-- Options populated cleanly via AJAX --}}
                                </select>
                            </div>
                            <p class="invite-field-hint">Defines the system access permissions and workspace visibility for this member.</p>
                        </div>
                    </div>

                    {{-- Sub-Card 2: Professional Contact Details (Optional) --}}
                    <div class="invite-subcard">
                        <div class="invite-subcard-head">
                            <h4 class="invite-subcard-title">
                                <i class="fa-solid fa-address-book text-primary"></i> Contact &amp; Social
                            </h4>
                            <span class="invite-subcard-badge badge-opt">Optional</span>
                        </div>
                        <p class="invite-subcard-hint">
                            Can also be provided by the team member upon account activation or in profile.
                        </p>

                        {{-- Row: Mobile & LinkedIn side-by-side --}}
                        <div class="invite-row-2col" style="margin-bottom: 2px;">
                            <div class="invite-field" style="margin-bottom: 0;">
                                <label class="invite-field-label">Mobile Number</label>
                                <x-jero-phone-input
                                    id="inviteMobile"
                                    name="mobile"
                                    countryCodeName="phone_country_code"
                                    countryCodeId="invitePhoneCountryCode"
                                    value=""
                                    countryCode=""
                                    label=""
                                    :required="false"
                                    placeholder="Enter mobile" />
                            </div>

                            <div class="invite-field" style="margin-bottom: 0;">
                                <label class="invite-field-label">LinkedIn Profile URL</label>
                                <div class="invite-input-wrap">
                                    <span class="invite-input-icon"><i class="fa-brands fa-linkedin" style="color: #0a66c2;"></i></span>
                                    <input type="text" name="linkedin" id="inviteLinkedin" placeholder="linkedin.com/in/..." autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message area --}}
                    <div id="inviteMsg" style="display:none; font-size:12.5px; padding:8px 12px; border-radius:7px; margin-top:4px;"></div>

                    {{-- Micro security strip --}}
                    <div class="invite-secure-strip">
                        <i class="fa-solid fa-lock text-muted"></i> Tenant-scoped invite &bull; Enterprise RBAC protected
                    </div>
                </form>
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
        <div class="comm-drawer" id="commDrawer" role="dialog" aria-modal="true" aria-label="Share Contact Details for Marketplace Enquiries">
            <!-- Drawer Header (Compact Standard) -->
            <div class="comm-drawer-head">
                <div>
                    <h5 class="comm-drawer-title">Share Contact Details for Marketplace Enquiries</h5>
                    <p class="comm-drawer-subtitle">Choose which inbound enquiries this team member receives on Jero Marketplace.</p>
                </div>
                <button type="button" class="comm-drawer-close" id="commDrawerClose" onclick="closeAssignCommDrawer()" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Drawer Body (Card + Sub-Card Enterprise Design, Zero Vertical Scroll) -->
            <div class="comm-drawer-body">
                <input type="hidden" id="commDrawerUserId" value="" />

                <!-- CARD 1: Member Identity Compact Bar -->
                <div class="comm-user-card">
                    <div class="comm-user-card-left">
                        <div class="comm-user-avatar" id="commDrawerAvatar">U</div>
                        <div class="comm-user-info">
                            <div class="d-flex align-items-center flex-wrap gap-1">
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
                            <span id="commDrawerLastActiveText">Active recently</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Dedicated Keep Private Master Card -->
                <div class="comm-card-private" id="commCard_private">
                    <div class="comm-card-main">
                        <div class="comm-card-icon icon-private">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div class="comm-card-text">
                            <div class="comm-card-title">Keep Private (Do Not Share Contact Details)</div>
                            <div class="comm-card-desc">Keep mobile &amp; email confidential. No contact details shown on Marketplace.</div>
                        </div>
                    </div>
                    <div class="comm-card-controls">
                        <label class="comm-ios-switch" title="Toggle Keep Private mode">
                            <input type="checkbox" id="commToggle_private">
                            <span class="comm-ios-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- CARD 3: Modern Card + Sub-Card Container for Enquiry Channels -->
                <div class="comm-channels-card">
                    <div class="comm-channels-head">
                        <div class="comm-channels-header-top">
                            <div class="comm-channels-title-wrap">
                                <i class="fa-solid fa-headset text-primary me-1" style="font-size: 11px;"></i>
                                <span class="comm-channels-title">Inbound Marketplace Channels</span>
                            </div>
                            <button type="button" class="comm-btn-clear" onclick="clearAllCommDrawerRoles()" title="Disable all enquiry channels">
                                <i class="fa-solid fa-arrows-rotate"></i>
                                <span>Clear All</span>
                            </button>
                        </div>
                        <div class="comm-channels-hint">
                            Direct phone and email will be shared on Jero Marketplace for active channels.
                        </div>
                    </div>

                    <div class="comm-channels-subcards" id="commDrawerCardsList">
                        <!-- Sub-Card 1: Sales & Client Enquiries -->
                        <div class="comm-card" id="commCard_client" data-channel="client">
                            <div class="comm-card-main">
                                <div class="comm-card-icon icon-client">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="comm-card-text">
                                    <span class="comm-card-title">Sales &amp; Client Enquiries</span>
                                    <div class="comm-card-desc">Client hiring requests, project discovery calls &amp; sales inquiries.</div>
                                </div>
                            </div>
                            <div class="comm-card-controls">
                                <label class="comm-ios-switch" title="Toggle Sales &amp; Client Enquiries">
                                    <input type="checkbox" class="comm-drawer-toggle" data-channel="client" id="commToggle_client">
                                    <span class="comm-ios-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Sub-Card 2: Agency Queries -->
                        <div class="comm-card" id="commCard_agency" data-channel="agency">
                            <div class="comm-card-main">
                                <div class="comm-card-icon icon-agency">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <div class="comm-card-text">
                                    <span class="comm-card-title">Agency Queries</span>
                                    <div class="comm-card-desc">Agency partner inquiries, split-fee requests &amp; vendor submissions.</div>
                                </div>
                            </div>
                            <div class="comm-card-controls">
                                <label class="comm-ios-switch" title="Toggle Agency Queries">
                                    <input type="checkbox" class="comm-drawer-toggle" data-channel="agency" id="commToggle_agency">
                                    <span class="comm-ios-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Sub-Card 3: Bench Talent Enquiries -->
                        <div class="comm-card" id="commCard_bench" data-channel="bench">
                            <div class="comm-card-main">
                                <div class="comm-card-icon icon-recruitment">
                                    <i class="fa-solid fa-user-tag"></i>
                                </div>
                                <div class="comm-card-text">
                                    <span class="comm-card-title">Bench Talent Enquiries</span>
                                    <div class="comm-card-desc">Inbound talent deployment calls, candidate placements &amp; queries.</div>
                                </div>
                            </div>
                            <div class="comm-card-controls">
                                <label class="comm-ios-switch" title="Toggle Bench Talent Enquiries">
                                    <input type="checkbox" class="comm-drawer-toggle" data-channel="bench" id="commToggle_bench">
                                    <span class="comm-ios-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Contextual Validation Alert Box (Only shown when action required) -->
                <div id="commInvariantAlert" class="comm-alert-box" style="display: none;">
                    <div class="d-flex align-items-start gap-2">
                        <div class="comm-alert-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="comm-alert-content">
                            <div class="comm-alert-title" id="commInvariantAlertTitle">Contact Requirement</div>
                            <div class="comm-alert-message" id="commInvariantAlertText">At least one contact is required. Please add at least one contact or configure a fallback representative to continue.</div>
                        </div>
                        <button type="button" class="comm-alert-close" onclick="$('#commInvariantAlert').slideUp(180);" aria-label="Dismiss">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- CARD 5: Visibility & Privacy Micro-Strip -->
                <div class="comm-info-box">
                    <div class="comm-info-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="comm-info-text">
                        <span><strong>Privacy Guarantee:</strong> Mobile number &amp; email are only published on Jero.works Marketplace for channels enabled above. Turning "Keep Private" ON keeps all contacts 100% confidential.</span>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer (Compact Standard) -->
            <div class="comm-drawer-foot">
                <button type="button" class="comm-btn-cancel" onclick="closeAssignCommDrawer()">Cancel</button>
                <button type="button" class="comm-btn-save" id="btnSaveCommRoles" onclick="saveCommunicationRoles()">
                    <span class="indicator-label">Save Preferences</span>
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
            const TEAM_DB_NAME = 'jero_team_local_store_v13_' + CURRENT_COMPANY_ID;
            const TEAM_DB_VERSION = 11;
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

            // Phase 1 & 2 Optimization: Unified Client RAM State Container (Canonical Server Dataset)
            window.teamState = {
                records: [],            // Canonical server dataset (NEVER mutate directly!)
                isFullDataset: false,   // True if company <= 2000 members and full dataset loaded
                statusTab: 'all',
                searchQuery: '',
                sortVal: '',
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

                // 1. Invalidate L2 IndexedDB recovery cache snapshot
                if (window.JeroTeamLocalStore) {
                    window.JeroTeamLocalStore.purge();
                }

                // If event originated from this exact browser session, local UI was already patched optimistically
                if (senderId && senderId === currentUserId) {
                    return;
                }

                var userId = parseInt(e.user_id, 10);

                // Optimistic Concurrency & Monotonic Version check
                if (window.teamState && Array.isArray(window.teamState.records)) {
                    var targetRec = window.teamState.records.find(function(r) { return parseInt(r.id || r.user_id, 10) === userId; });
                    if (targetRec && e.version && targetRec.version && parseInt(e.version, 10) <= parseInt(targetRec.version, 10)) {
                        console.info('Ignoring out-of-order Reverb event (version ' + e.version + ' <= ' + targetRec.version + ')');
                        return;
                    }
                }

                // 2. Event Lifecycle Taxonomy Handling
                if (e.action === 'permission.changed') {
                    if (userId === currentUserId && Array.isArray(e.revoked_permissions) && e.revoked_permissions.indexOf('view_team') !== -1) {
                        window.teamState.records = [];
                        if (window.JeroTeamLocalStore) { window.JeroTeamLocalStore.purge(); }
                        window.location.reload();
                        return;
                    }
                } else if (e.action === 'member.deleted' || e.action === 'team_member.deleted') {
                    if (window.teamState && Array.isArray(window.teamState.records)) {
                        window.teamState.records = window.teamState.records.filter(function(r) {
                            return parseInt(r.id || r.user_id, 10) !== userId;
                        });
                        window.teamState.totalCount = window.teamState.records.length;
                    }
                    if (window.teamState && window.teamState.isFullDataset) {
                        renderDerivedTeamStream(window.teamState.currentPage || 1);
                    } else {
                        fetchTeamsServer(window.teamState ? window.teamState.currentPage : 1);
                    }
                } else if (e.action === 'member.created' || e.action === 'team_member.created') {
                    if (e.member && window.teamState && Array.isArray(window.teamState.records)) {
                        window.teamState.records.unshift(e.member);
                        window.teamState.totalCount = window.teamState.records.length;
                        renderDerivedTeamStream(1);
                    } else {
                        fetchTeamsServer(window.teamState ? window.teamState.currentPage : 1);
                    }
                } else if (e.action === 'team_member.role_updated' || e.action === 'member.role_updated') {
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
                        var rec = window.teamState.records.find(function(r) { return parseInt(r.id || r.user_id, 10) === userId; });
                        if (rec) {
                            rec.display_name = e.role_name;
                            rec.role_name = e.role_name;
                            if (e.version) rec.version = e.version;
                        }
                    }
                } else if (e.action === 'team_member.status_updated' || e.action === 'member.status_updated') {
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
                        var rec = window.teamState.records.find(function(r) { return parseInt(r.id || r.user_id, 10) === userId; });
                        if (rec) {
                            rec.status = e.status;
                            if (e.version) rec.version = e.version;
                        }
                    }
                } else if (e.action === 'team_member.working_status_updated') {
                    var $ws = $('#working_status_' + userId);
                    if ($ws.length) {
                        $ws.prop('checked', parseInt(e.working_status, 10) === 1);
                    }
                } else {
                    fetchTeams(window.teamState ? window.teamState.currentPage : 1);
                }
            }

            // Reconnect Synchronization Probe
            function checkTeamSyncStatus() {
                var currentVer = (window.teamState && window.teamState.records && window.teamState.records[0]) 
                    ? (window.teamState.records[0].version || 1) 
                    : 1;
                $.ajax({
                    url: '{{ route("service_company.team.sync_status") }}',
                    type: 'GET',
                    data: { last_known_version: currentVer },
                    dataType: 'json',
                    success: function(resp) {
                        if (resp && resp.requires_full_resync) {
                            if (window.JeroTeamLocalStore) {
                                window.JeroTeamLocalStore.purge();
                            }
                            fetchTeamsServer(window.teamState ? window.teamState.currentPage : 1);
                        }
                    }
                });
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

            // Reverb updates and real-time delta synchronization handled via window.teamState

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
                            if (typeof updateLocalStatTabCounts === 'function') {
                                updateLocalStatTabCounts();
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
                $('#inviteLinkedin').val('');
                if ($('#inviteMobile').length) {
                    $('#inviteMobile').val('');
                }
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

                var rawLinkedin = $.trim($('#inviteLinkedin').val());
                if (rawLinkedin && !/^https?:\/\//i.test(rawLinkedin)) {
                    $('#inviteLinkedin').val('https://' + rawLinkedin.replace(/^\/+/, ''));
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

			// =========================================================================
			// PHASE 2: CLIENT-FIRST INTERACTION LAYER (RAM Engine, 0% Server Strain)
			// =========================================================================

			function escapeHtml(str) {
				if (str === null || str === undefined) return '';
				return String(str)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			}

			function getAvatarClass(name) {
				var raw = (name || 'User').trim();
				var first = (raw[0] || 'u').toLowerCase();
				var code = first.charCodeAt(0);
				if (code >= 97 && code <= 101) return 'bg-light-danger text-danger';
				if (code >= 102 && code <= 106) return 'bg-light-warning text-warning';
				if (code >= 107 && code <= 111) return 'bg-light-primary text-primary';
				if (code >= 112 && code <= 116) return 'bg-light-success text-success';
				if (code >= 117 && code <= 121) return 'bg-dark text-white';
				return 'bg-light-secondary text-dark';
			}

			function getRolePill(roleName) {
				var lower = (roleName || '').toLowerCase();
				if (lower.indexOf('admin') !== -1) return 'role-admin';
				if (lower.indexOf('lead') !== -1) return 'role-lead';
				return 'role-default';
			}

			function formatRelativeTime(dateStr) {
				if (!dateStr) return { text: 'Never logged in', dotClass: 'dot-gray' };
				var d = new Date(dateStr);
				if (isNaN(d.getTime())) return { text: 'Never logged in', dotClass: 'dot-gray' };
				var now = new Date();
				var diffSec = Math.floor((now - d) / 1000);
				var mins = Math.floor(diffSec / 60);
				var dotClass = mins <= 60 ? 'dot-green' : (mins <= 1440 ? 'dot-amber' : 'dot-gray');
				if (diffSec < 60) return { text: 'Just now', dotClass: dotClass };
				if (diffSec < 3600) return { text: Math.floor(diffSec / 60) + ' minutes ago', dotClass: dotClass };
				if (diffSec < 86400) return { text: Math.floor(diffSec / 3600) + ' hours ago', dotClass: dotClass };
				if (diffSec < 2592000) return { text: Math.floor(diffSec / 86400) + ' days ago', dotClass: dotClass };
				return { text: Math.floor(diffSec / 2592000) + ' months ago', dotClass: dotClass };
			}

			function formatJoinedDate(dateStr) {
				if (!dateStr) return 'Recently';
				var d = new Date(dateStr);
				if (isNaN(d.getTime())) return 'Recently';
				var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
				var day = String(d.getDate()).padStart(2, '0');
				var month = months[d.getMonth()];
				var year = d.getFullYear();
				return day + ' ' + month + ', ' + year;
			}

			function updateLocalStatTabCounts() {
				if (!window.teamState || !Array.isArray(window.teamState.records)) return;
				var total = window.teamState.records.length;
				var recruitmentCount = 0;
				var clientCount = 0;
				var vendorCount = 0;
				var inactiveCount = 0;
				var invitedCount = 0;
				var activeCount = 0;

				for (var i = 0; i < total; i++) {
					var r = window.teamState.records[i];
					var status = (r.status || '').toLowerCase();
					var channels = Array.isArray(r.channels) ? r.channels : [];
					var isInvited = !r.password || status === 'invited';

					if (channels.indexOf('client') !== -1 || r.is_client_rep) clientCount++;
					if (channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1 || r.is_agency_rep || (r.pre_agency_shares > 0)) vendorCount++;
					if (channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1 || (parseInt(r.root_user, 10) !== 1 && status === 'active' && !isInvited)) recruitmentCount++;
					if (status === 'inactive' && !isInvited) inactiveCount++;
					if (isInvited) {
						invitedCount++;
					} else if (status === 'active') {
						activeCount++;
					}
				}

				if (recruitmentCount === 0 && total > 0) recruitmentCount = 1;
				if (clientCount === 0 && total > 0) clientCount = 1;
				if (vendorCount === 0 && total > 0) vendorCount = 1;

				$('#span_teamAll').text(total);
				$('#span_teamActiveSub').text('+' + activeCount + ' active');
				$('#span_teamRecruitment').text(recruitmentCount);
				$('#span_teamClient').text(clientCount);
				$('#span_teamVendor').text(vendorCount);
				$('#span_teamInactive').text(inactiveCount);
				$('#span_teamInvited').text(invitedCount);
			}

			function getDerivedRecords() {
				if (!window.teamState || !Array.isArray(window.teamState.records)) {
					return [];
				}
				var statusTab = window.teamState.statusTab || teamStatusTab || 'all';
				var query = (window.teamState.searchQuery || $('#kt_tagify_2').val() || '').trim().toLowerCase();
				var sortVal = window.teamState.sortVal || $('#sortingfilter').val() || '';

				// 1. SHALLOW CLONE TO GUARANTEE IMMUTABILITY
				var list = window.teamState.records.map(function(item) {
					return Object.assign({}, item);
				});

				// 2. STATUS TAB FILTERING
				if (statusTab && statusTab !== 'all') {
					list = list.filter(function(r) {
						var channels = Array.isArray(r.channels) ? r.channels : [];
						var status = (r.status || '').toLowerCase();
						var isInvited = !r.password || status === 'invited';

						if (statusTab === 'client') {
							return channels.indexOf('client') !== -1 || !!r.is_client_rep;
						} else if (statusTab === 'vendor' || statusTab === 'agency') {
							return channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1 || !!r.is_agency_rep || ((r.pre_agency_shares || 0) > 0);
						} else if (statusTab === 'recruitment' || statusTab === 'bench') {
							return channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1 || (parseInt(r.root_user, 10) !== 1 && status === 'active' && !isInvited) || (parseInt(r.root_user, 10) === 1 && !channels.length);
						} else if (statusTab === 'inactive') {
							return status === 'inactive' && !isInvited;
						} else if (statusTab === 'invited') {
							return isInvited;
						} else if (statusTab === 'active') {
							return status === 'active' && !isInvited;
						}
						return true;
					});
				}

				// 3. FULL-TEXT SEARCH FILTERING (Unified Normalized Specification)
				if (query) {
					var queryDigits = query.replace(/\D/g, '');
					list = list.filter(function(r) {
						var name = (r.name || '').toLowerCase();
						var email = (r.email || '').toLowerCase();
						var phone = (r.phone || r.mobile || '').toLowerCase();
						var phoneNorm = (r.phone_normalized || '').toLowerCase();
						var designation = (r.designation || '').toLowerCase();
						var role = (r.display_name || r.role_name || '').toLowerCase();
						var empId = String(r.user_id || r.id || '');
						var status = (r.status || '').toLowerCase();

						return name.indexOf(query) !== -1 ||
							   email.indexOf(query) !== -1 ||
							   phone.indexOf(query) !== -1 ||
							   (queryDigits && phoneNorm.indexOf(queryDigits) !== -1) ||
							   designation.indexOf(query) !== -1 ||
							   role.indexOf(query) !== -1 ||
							   empId.indexOf(query) !== -1 ||
							   status.indexOf(query) !== -1;
					});
				}

				// 4. MULTI-DIMENSIONAL IN-MEMORY SORTING (Canonical 11-Sort Parity)
				list.sort(function(a, b) {
					switch (sortVal) {
						case 'joined_newest':
						case '1': // Joined: Newest to Oldest
							var tA1 = a.created_at ? new Date(a.created_at).getTime() : 0;
							var tB1 = b.created_at ? new Date(b.created_at).getTime() : 0;
							if (tA1 !== tB1) return tB1 - tA1;
							return (b.id || 0) - (a.id || 0);

						case 'joined_oldest':
						case '2': // Joined: Oldest to Newest
							var tA2 = a.created_at ? new Date(a.created_at).getTime() : 0;
							var tB2 = b.created_at ? new Date(b.created_at).getTime() : 0;
							if (tA2 !== tB2) return tA2 - tB2;
							return (a.id || 0) - (b.id || 0);

						case 'name_asc': // Name: A to Z
							var nA = (a.name || '').toLowerCase();
							var nB = (b.name || '').toLowerCase();
							var nCmp = nA.localeCompare(nB);
							if (nCmp !== 0) return nCmp;
							return (a.id || 0) - (b.id || 0);

						case 'name_desc': // Name: Z to A
							var ndA = (a.name || '').toLowerCase();
							var ndB = (b.name || '').toLowerCase();
							var ndCmp = ndB.localeCompare(ndA);
							if (ndCmp !== 0) return ndCmp;
							return (b.id || 0) - (a.id || 0);

						case 'designation_asc': // Designation: A to Z
							var desA = (a.designation || a.role_name || '').toLowerCase();
							var desB = (b.designation || b.role_name || '').toLowerCase();
							var desCmp = desA.localeCompare(desB);
							if (desCmp !== 0) return desCmp;
							return (a.id || 0) - (b.id || 0);

						case 'role_asc': // System Role: A to Z
							var rA = (a.display_name || a.role_name || '').toLowerCase();
							var rB = (b.display_name || b.role_name || '').toLowerCase();
							var rCmp = rA.localeCompare(rB);
							if (rCmp !== 0) return rCmp;
							return (a.id || 0) - (b.id || 0);

						case 'active_jobs_desc': // Workload: Active Jobs High to Low
							var ajDiff = (b.active_jobs || b.pre_active_jobs || 0) - (a.active_jobs || a.pre_active_jobs || 0);
							if (ajDiff !== 0) return ajDiff;
							return (b.id || 0) - (a.id || 0);

						case 'bench_talents_desc': // Workload: Bench Talent Rep High to Low
							var btDiff = (b.trm_talents || b.pre_trm_talents || 0) - (a.trm_talents || a.pre_trm_talents || 0);
							if (btDiff !== 0) return btDiff;
							return (b.id || 0) - (a.id || 0);

						case 'score_desc': // Performance Score High to Low
							var scDiff = (b.performance_score || b.pre_score || b.score || 0) - (a.performance_score || a.pre_score || a.score || 0);
							if (scDiff !== 0) return scDiff;
							return (b.id || 0) - (a.id || 0);

						case 'status_active': // Account Status: Active First
							var aAct = (String(a.status || '').toLowerCase() === 'active') ? 1 : 0;
							var bAct = (String(b.status || '').toLowerCase() === 'active') ? 1 : 0;
							if (aAct !== bAct) return bAct - aAct;
							return (b.id || 0) - (a.id || 0);

						case 'last_active':
						default: // Last Active (Most Recent First)
							var aTime = a.last_login ? new Date(a.last_login).getTime() : 0;
							var bTime = b.last_login ? new Date(b.last_login).getTime() : 0;
							if (aTime !== bTime) return bTime - aTime;
							return (b.id || 0) - (a.id || 0);
					}
				});

				return list;
			}

			function buildTeamMemberCardHtml(row) {
				var rawName = (row.name || 'User').trim();
				var strVal = (rawName[0] || 'U').toLowerCase();
				var avatarClass = getAvatarClass(rawName);

				var roleName = row.display_name || row.role_name || 'Team Member';
				var rolePill = getRolePill(roleName);
				var userDesignation = (row.designation && row.designation.trim()) ? row.designation.trim() : '';
				var designation = userDesignation ? userDesignation : roleName;

				var activeJobs = parseInt(row.active_jobs || row.pre_active_jobs, 10) || 0;
				var captainJobs = parseInt(row.captain_jobs || row.pre_captain_jobs, 10) || 0;
				var trmTalents = parseInt(row.trm_talents || row.pre_trm_talents, 10) || 0;
				var totalOps = activeJobs + captainJobs + trmTalents;

				var channels = Array.isArray(row.channels) ? row.channels : [];
				var publicCount = channels.length;

				var isInvited = (!row.password && row.status !== 'active') || row.status === 'invited';
				var isActive = (row.status === 'active');

				var cardAccent = '#cbd5e1';
				if (isActive) {
					cardAccent = (row.is_client_rep || row.is_agency_rep) ? '#009ef7' : '#10b981';
				} else if (isInvited) {
					cardAccent = '#f59e0b';
				}

				var relTime = formatRelativeTime(row.last_login);
				var joinedDate = formatJoinedDate(row.created_at);

				var phone = row.phone || '';
				if (!phone && row.mobile) {
					phone = (row.calling_code ? '+' + row.calling_code.replace(/^\+/, '') + ' ' : '') + row.mobile;
				}

				var isRanked = !isInvited && ((typeof row.ranking === 'number' && row.ranking > 0) || (typeof row.pre_rank === 'number' && row.pre_rank > 0)) && isActive;
				var rawRank = (typeof row.ranking === 'number' && row.ranking > 0) ? row.ranking : row.pre_rank;
				var rankDisplay = isRanked ? '#' + rawRank : 'N/A';
				var rankMonthDisplay = (isRanked && (row.performance_band || row.pre_rank_month)) ? '(' + (row.performance_band || row.pre_rank_month) + ')' : '';
				var scoreVal = isRanked ? (parseInt(row.performance_score || row.pre_score || row.score, 10) || 0) : 0;
				var medal = (isRanked && rawRank === 1) ? '🥇' : ((isRanked && rawRank === 2) ? '🥈' : ((isRanked && rawRank === 3) ? '🥉' : ''));

				var userDataObj = {
					id: row.id,
					name: rawName,
					email: row.email || '',
					phone: phone,
					image: row.image || '',
					initial: strVal.toUpperCase(),
					avatar_class: avatarClass,
					role: roleName,
					designation: designation,
					status: (row.status || 'active').toLowerCase(),
					last_active: relTime.text,
					channels: channels,
					priorities: row.priorities || []
				};
				var userDataAttr = escapeHtml(JSON.stringify(userDataObj));

				var html = '<div class="tm-primary-card js-team-card" data-user-id="' + row.id + '" data-is-client-rep="' + (row.is_client_rep ? '1' : '0') + '" data-is-agency-rep="' + (row.is_agency_rep ? '1' : '0') + '" style="border-left: 3.5px solid ' + cardAccent + ';">';
				html += '<div class="tm-subcards-grid">';

				// Subcard 1: Member Identity
				html += '<div class="tm-subcard tm-subcard-identity">';
				html += '<div class="tm-subcard-head">';
				html += '<div class="tm-subcard-title-wrap">';
				html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
				html += '<span class="tm-subcard-title">Member Identity</span>';
				html += '</div>';
				if (parseInt(row.root_user, 10) === 1) {
					html += '<span class="tm-subcard-badge badge-owner" title="Organization Owner"><i class="fa-solid fa-crown text-warning me-1"></i>Owner</span>';
				} else {
					html += '<span class="tm-subcard-badge badge-member">• Member</span>';
				}
				html += '</div>';
				html += '<div class="tm-identity-body">';
				html += '<div class="tm-avatar ' + avatarClass + ' show-detail-team" data-row-id="' + row.id + '" role="button" tabindex="0" title="View ' + escapeHtml(rawName) + ' Profile" style="cursor: pointer;">';
				if (row.image) {
					html += '<img src="' + escapeHtml(row.image) + '" alt="' + escapeHtml(rawName) + '">';
				} else {
					html += strVal.toUpperCase();
				}
				html += '</div>';
				html += '<div class="tm-identity-details">';
				html += '<span class="tm-member-name show-detail-team" data-row-id="' + row.id + '" role="button" tabindex="0" title="View ' + escapeHtml(rawName) + ' Profile">' + escapeHtml(rawName.substring(0, 24)) + '</span>';
				html += '<div class="tm-member-designation" title="' + (userDesignation ? 'Designation: ' + escapeHtml(userDesignation) : 'System Role: ' + escapeHtml(roleName)) + '">';
				html += '<i class="fa-solid fa-id-badge text-muted"></i>';
				html += '<span>' + escapeHtml(designation.substring(0, 26)) + '</span>';
				html += '</div>';
				html += '<div class="tm-member-email-wrap">';
				html += '<i class="fa-regular fa-envelope text-muted"></i>';
				html += '<a href="mailto:' + escapeHtml(row.email || '') + '" class="tm-member-email-link" title="' + escapeHtml(row.email || '') + '">' + escapeHtml((row.email || '').substring(0, 26)) + '</a>';
				html += '</div>';
				html += '</div></div></div>';

				// Subcard 2: System Role
				html += '<div class="tm-subcard tm-subcard-role">';
				html += '<div class="tm-subcard-head">';
				html += '<div class="tm-subcard-title-wrap">';
				html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
				html += '<span class="tm-subcard-title">System Role</span>';
				html += '</div></div>';
				html += '<div class="tm-role-body">';
				html += '<div class="tm-role-badge-row">';
				html += '<span class="tm-role-badge team-role-pill ' + rolePill + '" title="Assigned system role"><i class="fa-solid fa-shield-halved me-1"></i>' + escapeHtml(roleName) + '</span>';
				html += '</div>';
				html += '<div class="tm-role-action-row">';
				if (parseInt(row.root_user, 10) !== 1) {
					html += '<button type="button" class="btn-change-role update-user-role" data-row-id="' + row.id + '" title="Change role and permissions directly from here"><i class="fa-solid fa-user-gear me-1 text-primary"></i>Change Role</button>';
				} else {
					html += '<span class="tm-role-locked-note" title="Primary owner role is authoritative and cannot be changed"><i class="fa-solid fa-lock text-muted"></i>Owner Role</span>';
				}
				html += '</div></div></div>';

				// Subcard 3: Workload
				html += '<div class="tm-subcard tm-subcard-workload">';
				html += '<div class="tm-subcard-head">';
				html += '<div class="tm-subcard-title-wrap">';
				html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
				html += '<span class="tm-subcard-title">Workload</span>';
				html += '</div>';
				html += '<span class="tm-subcard-badge badge-count">' + totalOps + ' Ops</span>';
				html += '</div>';
				html += '<div class="tm-workload-body">';
				html += '<div class="tm-wl-row" title="Active Jobs Assigned"><i class="fa-solid fa-briefcase text-primary me-1"></i><span><strong>' + activeJobs + '</strong> Active Jobs</span></div>';
				html += '<div class="tm-wl-row" title="SPOC / Captain on Jobs"><i class="fa-solid fa-crown text-purple me-1"></i><span><strong>' + captainJobs + '</strong> Captain Jobs</span></div>';
				html += '<div class="tm-wl-row" title="Bench Talents Managed (Bench Talent Representative)"><i class="fa-solid fa-users text-success me-1"></i><span title="Bench Talents Managed (Bench Talent Representative)"><strong>' + trmTalents + '</strong> Bench Talent Rep</span></div>';
				html += '</div></div>';

				// Subcard 4: Account Status
				html += '<div class="tm-subcard tm-subcard-status">';
				html += '<div class="tm-subcard-head">';
				html += '<div class="tm-subcard-title-wrap">';
				html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>';
				html += '<span class="tm-subcard-title">Account Status</span>';
				html += '</div></div>';
				html += '<div class="tm-status-body">';
				if (row.password || (row.status && row.status.toLowerCase() === 'active')) {
					html += '<div class="tm-status-row tm-status-row-control">';
					html += '<label class="ios-switch" title="Toggle active status">';
					html += '<input type="checkbox" class="chkStatus" id="status_change_' + row.id + '" value="active" ' + (isActive ? 'checked' : '') + ' onclick="handleClick(this, ' + row.id + ');" ' + (parseInt(row.root_user, 10) === 1 ? 'disabled' : '') + '>';
					html += '<span class="ios-slider"></span>';
					html += '</label></div>';
					html += '<div class="tm-status-row tm-status-row-label">';
					html += '<span class="tm-status-text status_text ' + (isActive ? 'text-success' : 'text-muted') + '">' + (isActive ? 'Active' : 'Inactive') + '</span>';
					html += '</div>';
					html += '<div class="tm-status-row tm-status-row-sub">';
					if (parseInt(row.root_user, 10) === 1) {
						html += '<span class="tm-status-subnote">Owner (Always Active)</span>';
					} else {
						html += '<span class="tm-status-subnote">Click switch to toggle</span>';
					}
					html += '</div>';
				} else {
					html += '<div class="tm-status-row tm-status-row-control">';
					html += '<button type="button" class="btn-resend-invite-icon" onclick="reSendhandleClick(this, ' + row.id + ');" title="Resend invitation email">';
					html += '<i class="fa-solid fa-paper-plane text-warning" style="font-size: 11px;"></i>';
					html += '</button></div>';
					html += '<div class="tm-status-row tm-status-row-label"><span class="tm-status-text text-warning">Awaiting Signup</span></div>';
					html += '<div class="tm-status-row tm-status-row-sub">';
					html += '<a href="javascript:;" class="btn-resend-invite" onclick="reSendhandleClick(this, ' + row.id + ');" title="Resend invitation email"><i class="fa-solid fa-arrows-rotate me-1"></i>Resend Invite</a>';
					html += '</div>';
				}
				html += '</div></div>';

				// Subcard 5: Contact Details Visibility
				html += '<div class="tm-subcard tm-subcard-comm" data-user=\'' + userDataAttr + '\' onclick="openAssignCommDrawer(this)" role="button" tabindex="0" title="Click anywhere to manage contact visibility">';
				html += '<div class="tm-subcard-head">';
				html += '<div class="tm-subcard-title-wrap">';
				html += '<i class="fa-solid fa-address-book text-primary" style="font-size: 11px;"></i>';
				html += '<span class="tm-subcard-title">Contact Details Visibility</span>';
				html += '</div>';
				html += '<div class="d-flex align-items-center gap-1 flex-shrink-0">';
				if (publicCount === 0) {
					html += '<span class="tm-subcard-badge badge-role" title="Contact details kept private"><i class="fa-solid fa-eye-slash me-1"></i>Private</span>';
				}
				html += '<button type="button" class="btn-comm-change-action" data-user=\'' + userDataAttr + '\' onclick="event.stopPropagation(); openAssignCommDrawer(this);" title="Change contact visibility preferences">Change</button>';
				html += '</div></div>';
				html += '<div class="tm-comm-body">';
				if (publicCount > 0) {
					html += '<div class="tm-comm-rows-list">';
					if (channels.indexOf('client') !== -1) {
						html += '<div class="tm-comm-row"><span class="tm-comm-chip client" title="Sales &amp; Client Enquiries"><i class="fa-solid fa-phone me-1"></i>Sales &amp; Client Enquiries</span></div>';
					}
					if (channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1) {
						html += '<div class="tm-comm-row"><span class="tm-comm-chip vendor" title="Agency Queries"><i class="fa-solid fa-phone me-1"></i>Agency Queries</span></div>';
					}
					if (channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1) {
						html += '<div class="tm-comm-row"><span class="tm-comm-chip recruitment" title="Bench Talent Enquiries"><i class="fa-solid fa-phone me-1"></i>Bench Talent Enquiries</span></div>';
					}
					html += '</div>';
				} else {
					html += '<div class="d-flex align-items-center justify-content-between w-100 gap-1 flex-wrap">';
					html += '<span class="text-muted" style="font-size: 10.5px; font-weight: 500;"><i class="fa-solid fa-lock text-muted me-1"></i> No Details Shared (Keep Private)</span>';
					html += '<button type="button" class="btn-assign-comm-empty" data-user=\'' + userDataAttr + '\' onclick="event.stopPropagation(); openAssignCommDrawer(this);" title="Share contact details for marketplace enquiries"><i class="fa-solid fa-plus me-1 text-primary"></i>Share Details</button>';
					html += '</div>';
				}
				html += '</div></div>';

				html += '</div>'; // End tm-subcards-grid

				// Subcard 6: Bottom Activity Strip
				html += '<div class="tm-card-bottom-strip">';
				html += '<div class="tm-bottom-left-meta">';
				html += '<span class="tm-meta-item" title="Registration Date">';
				html += '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
				html += '<span>Joined: <strong>' + joinedDate + '</strong></span>';
				html += '</span>';
				html += '<span class="tm-meta-dot">•</span>';
				html += '<span class="tm-meta-item" title="Last Login Freshness">';
				html += '<span class="dot-pulse ' + relTime.dotClass + '"></span>';
				html += '<span>Last active: <strong>' + escapeHtml(relTime.text) + '</strong></span>';
				html += '</span>';
				if (phone) {
					html += '<span class="tm-meta-dot">•</span>';
					html += '<span class="tm-meta-item" title="Direct Phone">';
					html += '<i class="fa-solid fa-phone" style="font-size: 10px;"></i>';
					html += '<span>' + escapeHtml(phone) + '</span>';
					html += '</span>';
				}
				html += '</div>';

				html += '<div class="tm-bottom-right-group">';
				html += '<div class="tm-perf-strip-box">';
				html += '<span class="tm-perf-rank" title="Performance Rank">';
				html += medal + ' <strong>' + rankDisplay + '</strong>';
				if (rankMonthDisplay) {
					html += '<span class="tm-perf-month">' + escapeHtml(rankMonthDisplay) + '</span>';
				}
				html += '</span>';
				if (isRanked) {
					html += '<span class="tm-meta-dot">•</span>';
					html += '<span class="tm-perf-score" title="Score Points">Score: <strong>' + scoreVal + ' pts</strong></span>';
				}
				html += '<button type="button" class="btn-view-score teamViewScore" data-user-id="' + row.id + '" title="Click to view detailed performance scoreboard"><i class="fa-solid fa-chart-line me-1"></i>View Score</button>';
				html += '</div>';

				html += '<button type="button" class="btn-view-profile show-detail-team" data-row-id="' + row.id + '" title="View Full Profile"><span>View Profile</span><i class="fa-solid fa-arrow-right" style="font-size: 9px;"></i></button>';
				html += '</div>';

				html += '</div>'; // End tm-card-bottom-strip
				html += '</div>'; // End tm-primary-card

				return html;
			}

			function buildTeamPaginationHtml(total, currentPage, perPage) {
				if (total <= 0) return '';
				perPage = perPage || teamPageSize || 10;
				currentPage = parseInt(currentPage, 10) || 1;
				var totalPages = Math.ceil(total / perPage) || 1;
				var from = (currentPage - 1) * perPage + 1;
				var to = Math.min(currentPage * perPage, total);

				var html = '<div class="team-pagination-bar">';
				html += '<div class="team-pagination-info">Showing ' + from + ' to ' + to + ' of ' + total + ' members</div>';
				html += '<div class="team-pagination-pages">';

				// Previous button
				var prevDisabled = currentPage > 1 ? '' : 'is-disabled';
				html += '<button type="button" class="team-page-link team-page-arrow ' + prevDisabled + '" data-page="' + (currentPage - 1) + '" data-listType="allcandidate" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>';

				// Page numbers with ellipsis
				for (var i = 1; i <= totalPages; i++) {
					if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
						var activeClass = (i === currentPage) ? 'is-active' : '';
						html += '<button type="button" class="team-page-link ' + activeClass + '" data-page="' + i + '" data-listType="allcandidate">' + i + '</button>';
					} else if (i === currentPage - 2 || i === currentPage + 2) {
						html += '<span class="team-page-dots">…</span>';
					}
				}

				// Next button
				var nextDisabled = currentPage < totalPages ? '' : 'is-disabled';
				html += '<button type="button" class="team-page-link team-page-arrow ' + nextDisabled + '" data-page="' + (currentPage + 1) + '" data-listType="allcandidate" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>';

				html += '</div></div>';
				return html;
			}

			function renderDerivedTeamStream(targetPage) {
				if (!window.teamState || !window.teamState.isFullDataset) {
					return fetchTeamsServer(targetPage || 1);
				}

				var derived = getDerivedRecords();
				var total = derived.length;
				var pageSize = window.teamState.pageSize || teamPageSize || 10;
				var totalPages = Math.ceil(total / pageSize) || 1;
				var page = Math.max(1, Math.min(parseInt(targetPage, 10) || 1, totalPages));

				window.teamState.currentPage = page;
				localStorage.setItem('currentPageTeam', page);

				var fromIdx = (page - 1) * pageSize;
				var pagedItems = derived.slice(fromIdx, fromIdx + pageSize);

				var cardsHtml = '';
				if (pagedItems.length > 0) {
					cardsHtml = '<div class="team-cards-container">';
					for (var i = 0; i < pagedItems.length; i++) {
						cardsHtml += buildTeamMemberCardHtml(pagedItems[i]);
					}
					cardsHtml += '</div>';
				} else {
					cardsHtml = '<div class="team-empty-card">' +
						'<i class="fa-solid fa-user-group fa-3x mb-3 text-muted"></i>' +
						'<div class="fw-bold fs-6 text-gray-800 mb-1">No Team Members Found</div>' +
						'<div class="text-muted fs-7">No team members match the selected filter criteria.</div>' +
						'</div>';
				}

				var paginationHtml = buildTeamPaginationHtml(total, page, pageSize);

				$('#teamsListajax').html(cardsHtml);
				$('#paginationTeams').html(paginationHtml);
				updateTeamSelectedCount();

				// Update stat tabs count dynamically when viewing all members without search
				if (window.teamState.statusTab === 'all' && !(window.teamState.searchQuery || '').trim()) {
					updateLocalStatTabCounts();
				}

				return Promise.resolve({ total: total, page: page });
			}

			async function fetchTeamsServer(page = page_no, search = '') {
				if (currentTeamFetchXhr && currentTeamFetchXhr.readyState !== 4) {
					currentTeamFetchXhr.abort();
				}

				var customSearch = ($('#kt_tagify_2').val() || '').trim();
				var sortFilter = $('#sortingfilter').val() || '';
				var cacheKey = 'team_' + teamStatusTab + '_p' + page + '_s' + teamPageSize + '_q' + customSearch + '_sort' + sortFilter;

				// Check IndexedDB L2 Cache first
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
								window.teamState.isFullDataset = !!cached.is_full_dataset;
								if (cached.raw_data && Array.isArray(cached.raw_data)) {
									window.teamState.records = cached.raw_data;
								}
							}

							$('#teamsListajax').html(cached.html);
							$('#paginationTeams').html(cached.pagination);
							updateTeamSelectedCount();
							return Promise.resolve(cached);
						}
					} catch(e) {}
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

						if (window.teamState) {
							window.teamState.currentPage = page;
							window.teamState.totalCount = response.total || 0;
							window.teamState.statusTab = teamStatusTab;
							window.teamState.isFullDataset = !!response.is_full_dataset;
							if (response.raw_data && Array.isArray(response.raw_data)) {
								window.teamState.records = response.raw_data;
							}
						}

						if (window.JeroTeamLocalStore && response && response.html) {
							window.JeroTeamLocalStore.set(cacheKey, response);
						}

						$('#teamsListajax').html(response.html);
						$('#paginationTeams').html(response.pagination);
						updateTeamSelectedCount();

						if (response.counts) {
							$('#span_teamAll, .tm-count-all').text(response.counts.total || 0);
							if (response.counts.active !== undefined) $('#span_teamActiveSub').text('+' + response.counts.active + ' active');
							$('#span_teamRecruitment, .tm-count-recruitment').text(response.counts.recruitment || 0);
							$('#span_teamClient, .tm-count-client').text(response.counts.client || 0);
							$('#span_teamVendor, .tm-count-agency').text(response.counts.vendor || 0);
							$('#span_teamInactive, .tm-count-inactive').text(response.counts.inactive || 0);
							$('#span_teamInvited, .tm-count-invited').text(response.counts.invited || 0);
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

			function fetchTeams(page = page_no, search = '') {
				// 0-Network client-first rendering if full dataset is in RAM
				if (window.teamState && window.teamState.isFullDataset && Array.isArray(window.teamState.records) && window.teamState.records.length > 0) {
					return renderDerivedTeamStream(page);
				}
				return fetchTeamsServer(page, search);
			}

			function setTeamTab(tab, el) {
				teamStatusTab = tab;
				if (window.teamState) {
					window.teamState.statusTab = tab;
				}
				$('.tm-stat-grid .tm-stat-card').removeClass('active');
				$(el).addClass('active');
				$('#teamStatusFilterDropdown').val(tab);

				if (window.teamState && window.teamState.isFullDataset) {
					renderDerivedTeamStream(1);
				} else {
					fetchTeamsServer(1);
				}
			}

			function changeStatusDropdown(val) {
				teamStatusTab = val;
				if (window.teamState) {
					window.teamState.statusTab = val;
				}
				$('.tm-stat-grid .tm-stat-card').removeClass('active');
				$('.tm-stat-grid .tm-stat-card[data-tab="' + val + '"]').addClass('active');

				if (window.teamState && window.teamState.isFullDataset) {
					renderDerivedTeamStream(1);
				} else {
					fetchTeamsServer(1);
				}
			}

			function selectTeamSort(sortVal, sortLabel, el) {
				$('#sortingfilter').val(sortVal);
				$('#teamSortCurrentText').text(sortLabel);
				$('.team-sort-menu .js-sort-opt').removeClass('active');
				if (el) {
					$(el).addClass('active');
				}
				if (window.teamState) {
					window.teamState.sortVal = sortVal;
				}
				if (window.teamState && window.teamState.isFullDataset) {
					renderDerivedTeamStream(1);
				} else {
					fetchTeamsServer(1);
				}
			}
			window.selectTeamSort = selectTeamSort;

			function onTeamSortChange(sortVal) {
				if (window.teamState) {
					window.teamState.sortVal = sortVal;
				}
				if (window.teamState && window.teamState.isFullDataset) {
					renderDerivedTeamStream(1);
				} else {
					fetchTeamsServer(1);
				}
			}

			// =========================================================
			// Assign Communication Contacts Right Drawer Controller
			// =========================================================
			function openAssignCommDrawer(trigger) {
				var user = null;
				if (typeof trigger === 'object' && trigger !== null) {
					if (trigger.getAttribute && trigger.getAttribute('data-user')) {
						try { user = JSON.parse(trigger.getAttribute('data-user')); } catch(e){}
					} else if (typeof $ !== 'undefined' && $(trigger).closest('[data-user]').length) {
						try { user = JSON.parse($(trigger).closest('[data-user]').attr('data-user')); } catch(e){}
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

				// Reset all channel cards, switches, and private state
				var channels = Array.isArray(user.channels) ? user.channels : [];
				var hasActiveChannel = false;
				var availableChannels = ['client', 'agency', 'bench'];

				availableChannels.forEach(function(ch) {
					var legacyCh = (ch === 'bench') ? 'recruitment' : ((ch === 'agency') ? 'vendor' : ch);
					var isChecked = channels.indexOf(ch) !== -1 || channels.indexOf(legacyCh) !== -1;

					var $card = $('#commCard_' + ch);
					var $toggle = $('#commToggle_' + ch);

					$toggle.prop('checked', isChecked);

					if (isChecked) {
						$card.addClass('is-active');
						hasActiveChannel = true;
					} else {
						$card.removeClass('is-active');
					}
				});

				// Set Keep Private switch state
				if (hasActiveChannel) {
					$('#commToggle_private').prop('checked', false);
					$('#commCard_private').removeClass('is-private-active');
				} else {
					$('#commToggle_private').prop('checked', true);
					$('#commCard_private').addClass('is-private-active');
				}

				// Hide any previous validation/invariant alert
				$('#commInvariantAlert').hide();

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
				var availableChannels = ['client', 'agency', 'bench'];
				availableChannels.forEach(function(ch) {
					$('#commCard_' + ch).removeClass('is-active');
					$('#commToggle_' + ch).prop('checked', false);
				});
				$('#commToggle_private').prop('checked', true);
				$('#commCard_private').addClass('is-private-active');
				$('#commInvariantAlert').slideUp(180);
			}

			// Handle Keep Private toggle switch change
			$(document).on('change', '#commToggle_private', function() {
				var isPrivate = $(this).is(':checked');
				if (isPrivate) {
					$('#commCard_private').addClass('is-private-active');
					var availableChannels = ['client', 'agency', 'bench'];
					availableChannels.forEach(function(ch) {
						$('#commCard_' + ch).removeClass('is-active');
						$('#commToggle_' + ch).prop('checked', false);
					});
				} else {
					$('#commCard_private').removeClass('is-private-active');
				}
				$('#commInvariantAlert').slideUp(180);
			});

			// Handle channel toggle switch change inside drawer
			$(document).on('change', '.comm-drawer-toggle', function() {
				var ch = $(this).data('channel');
				var isChecked = $(this).is(':checked');
				var $card = $('#commCard_' + ch);

				if (isChecked) {
					$card.addClass('is-active');
					$('#commToggle_private').prop('checked', false);
					$('#commCard_private').removeClass('is-private-active');
				} else {
					$card.removeClass('is-active');
					var anyChecked = $('.comm-drawer-toggle:checked').length > 0;
					if (!anyChecked) {
						$('#commToggle_private').prop('checked', true);
						$('#commCard_private').addClass('is-private-active');
					}
				}
				$('#commInvariantAlert').slideUp(180);
			});

			// Save Contact Details Sharing Preferences
			function saveCommunicationRoles() {
				var userId = $('#commDrawerUserId').val();
				if (!userId) return;

				var isPrivate = $('#commToggle_private').is(':checked');
				var selectedChannels = [];
				var priorities = {};

				if (!isPrivate) {
					$('.comm-drawer-toggle:checked').each(function() {
						var ch = $(this).data('channel');
						selectedChannels.push(ch);
						var prioVal = 'Primary';
						priorities[ch] = prioVal;

						// Compatibility aliases
						if (ch === 'agency') {
							selectedChannels.push('vendor');
							priorities['vendor'] = prioVal;
						} else if (ch === 'bench') {
							selectedChannels.push('recruitment');
							priorities['recruitment'] = prioVal;
						}
					});
				}

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
							try { window.JeroTeamLocalStore.purge(); } catch(e){}
						}

						// Update tab badge counters if returned
						if (response.counts) {
							var c = response.counts;
							if (c.total !== undefined) $('#span_teamAll, .tm-count-all').text(c.total);
							if (c.active !== undefined) $('#span_teamActiveSub').text('+' + c.active + ' active');
							if (c.recruitment !== undefined) $('#span_teamRecruitment, .tm-count-recruitment').text(c.recruitment);
							if (c.client !== undefined) $('#span_teamClient, .tm-count-client').text(c.client);
							if (c.vendor !== undefined) $('#span_teamVendor, .tm-count-agency').text(c.vendor);
							if (c.inactive !== undefined) $('#span_teamInactive, .tm-count-inactive').text(c.inactive);
							if (c.invited !== undefined) $('#span_teamInvited, .tm-count-invited').text(c.invited);
						}

						// Update RAM state
						if (window.teamState && Array.isArray(window.teamState.records)) {
							var rec = window.teamState.records.find(function(r) { return parseInt(r.id, 10) === parseInt(userId, 10); });
							if (rec) {
								rec.channels = selectedChannels;
								rec.priorities = priorities;
							}
						}

						if (window.teamState && window.teamState.isFullDataset) {
							renderDerivedTeamStream(window.teamState.currentPage || 1);
						} else {
							fetchTeamsServer(window.teamState.currentPage || 1);
						}

						if (typeof toastr !== 'undefined') {
							toastr.success(response.message || 'Contact sharing preferences updated successfully.');
						}
					},
					error: function(xhr) {
						$btn.find('.indicator-label').removeClass('d-none');
						$btn.find('.indicator-progress').addClass('d-none');
						$btn.prop('disabled', false);

						var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update contact sharing preferences.';
						
						// Show Intelligent Invariant / Validation Alert in Drawer
						$('#commInvariantAlertTitle').text('Contact Requirement');
						$('#commInvariantAlertText').html(msg);
						$('#commInvariantAlert').slideDown(200);

						if (typeof toastr !== 'undefined') {
							toastr.error(msg);
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

			// Dynamic Search with 300ms debounce & instant RAM synchronization
			var teamSearchTimeout = null;
			$('#kt_tagify_2').on('input', function() {
				var val = $(this).val();
				if (window.teamState) {
					window.teamState.searchQuery = (val || '').trim();
				}
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

			$('#kt_tagify_2').on('keydown', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					clearTimeout(teamSearchTimeout);
					var val = $(this).val();
					if (window.teamState) {
						window.teamState.searchQuery = (val || '').trim();
					}
					fetchTeams(1);
				} else if (e.key === 'Escape') {
					e.preventDefault();
					clearTeamFilters();
				}
			});

			// Rows-per-page selector in the footer
			function changeTeamPageSize(val) {
				teamPageSize = parseInt(val) || 10;
				localStorage.setItem('teamPageSize', teamPageSize);
				fetchTeams(1);
			}

			// Clear Search: Resets query ONLY; preserves active tab, sort preference, and page size
			function clearTeamFilters() {
				$('#kt_tagify_2').val('');
				$('.team-search-clear').hide();
				if (window.teamState) {
					window.teamState.searchQuery = '';
				}
				fetchTeams(1);
			}

			// Reset Filters: Restores all faceted filters back to system default
			function resetTeamFilters() {
				$('#kt_tagify_2').val('');
				$('.team-search-clear').hide();
				$('#sortingfilter').val('');
				$('#teamSortCurrentText').text('Last Active');
				$('.team-sort-menu .js-sort-opt').removeClass('active');
				$('.team-sort-menu .js-sort-opt').first().addClass('active');

				teamStatusTab = 'all';
				$('#teamStatusFilterDropdown').val('all');
				$('.tm-stat-grid .tm-stat-card').removeClass('active');
				$('.tm-stat-grid .tm-stat-card[data-tab="all"]').addClass('active');

				if (window.teamState) {
					window.teamState.searchQuery = '';
					window.teamState.sortVal = '';
					window.teamState.statusTab = 'all';
				}

				fetchTeams(1);
			}
			window.resetTeamFilters = resetTeamFilters;

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
