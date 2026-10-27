@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

    <style>
        /* ==========================================================================
           JERO Super Admin — Accepted Job Collaboration Requests Enterprise Card + Sub-Card
           Complies with docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md & AGENTS.md Rule 20
           Optimized specifically for 15.6" screens (1366x768 & 1920x1080)
           Zero Horizontal Overflow (overflow-x: hidden) • Strict Client-First Engine
           ========================================================================== */

        :root {
            --cd-border-color: #e2e8f0;
            --cd-card-bg: #ffffff;
            --cd-subcard-bg: #f8fafc;
            --cd-subcard-border: #edf2f7;
            --cd-primary-blue: #009ef7;
            --cd-primary-blue-hover: #0086d1;
            --cd-text-dark: #0f172a;
            --cd-text-muted: #64748b;
            --cd-font: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Top Space & Header Gap Management (Collapse redundant Metronic toolbar whitespace) */
        #kt_header_search {
            display: none !important;
        }

        #kt_header {
            border-bottom: 1px solid #eff2f5 !important;
        }

        @media (min-width: 992px) {
            .cd-jr-page-wrapper {
                margin-top: -48px !important;
            }
        }

        @media (max-width: 991.98px) {
            .cd-jr-page-wrapper {
                margin-top: -38px !important;
            }
        }

        .cd-jr-page-wrapper {
            font-family: var(--cd-font);
            color: var(--cd-text-dark);
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* --------------------------------------------------------------------------
           TIER 1: TOP HEADER TOOLBAR CARD
           -------------------------------------------------------------------------- */
        .cd-jr-header-card {
            background: #ffffff !important;
            border: 1px solid var(--cd-border-color) !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
        }

        .cd-jr-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 240px;
        }

        .cd-jr-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #ecfdf5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .cd-jr-header-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }

        .cd-jr-header-sub {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .cd-jr-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Search Sub-Card */
        .cd-jr-search-subcard {
            position: relative;
            display: flex;
            align-items: center;
            height: 36px;
            width: 300px;
            max-width: 100%;
            background: #f8fafc;
            border: 1px solid var(--cd-border-color);
            border-radius: 8px;
            padding: 0 10px;
            transition: all 0.2s ease;
        }

        .cd-jr-search-subcard:focus-within {
            background: #ffffff;
            border-color: var(--cd-primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        }

        .cd-jr-search-subcard i.fa-search {
            color: #94a3b8;
            font-size: 12px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .cd-jr-search-input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-size: 12px !important;
            font-family: var(--cd-font) !important;
            color: #0f172a !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .cd-jr-search-input::placeholder {
            color: #94a3b8;
        }

        .cd-jr-search-clear {
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 12px;
            cursor: pointer;
            padding: 2px 4px;
            display: none;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .cd-jr-search-clear:hover {
            color: #ef4444;
        }

        .cd-jr-refresh-btn {
            height: 36px;
            padding: 0 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--cd-border-color);
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .cd-jr-refresh-btn:hover {
            background: #f8fafc;
            color: var(--cd-primary-blue);
            border-color: #cbd5e1;
        }

        /* --------------------------------------------------------------------------
           TIER 2: SUB-TABS CARD (Pure White Background)
           -------------------------------------------------------------------------- */
        .cd-jr-tabs-card {
            background: #ffffff !important;
            border: 1px solid var(--cd-border-color) !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
        }

        .cd-jr-tabs-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cd-jr-tab-btn {
            background: #ffffff !important;
            border: 1px solid var(--cd-border-color) !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .cd-jr-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        /* Mandatory Rule: Single Active Blue Highlight */
        .cd-jr-tab-btn.active {
            background: var(--cd-primary-blue) !important;
            border-color: var(--cd-primary-blue) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
        }

        .cd-jr-tab-badge {
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            border-radius: 10px;
            font-size: 10.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: var(--cd-primary-blue);
        }

        .cd-jr-tab-btn.active .cd-jr-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        .cd-jr-live-status-pill {
            font-size: 11px;
            font-weight: 600;
            color: #059669;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cd-jr-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
            animation: cdPulse 2s infinite;
        }

        @keyframes cdPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* --------------------------------------------------------------------------
           TIER 3: PRIMARY ENTITY CARD STREAM & DISCRETE SUB-CARDS
           -------------------------------------------------------------------------- */
        .cd-jr-stream {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        /* Master Primary Card */
        .cd-jr-card {
            background: #ffffff !important;
            border: 1px solid var(--cd-border-color) !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .cd-jr-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07) !important;
            transform: translateY(-1px);
        }

        /* 15.6" Screen Responsive Fractional Grid */
        .cd-jr-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.4fr) minmax(0, 1.35fr) minmax(0, 1.35fr) minmax(0, 1.4fr) !important;
            gap: 10px !important;
            padding: 12px 14px 10px 14px !important;
            width: 100% !important;
            align-items: stretch !important;
        }

        /* Discrete Sub-Card Containers */
        .cd-subcard {
            background: var(--cd-subcard-bg);
            border: 1px solid var(--cd-subcard-border);
            border-radius: 8px;
            padding: 9px 11px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .cd-subcard-header-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Sub-Card 1: Job & Client Profile */
        .cd-subcard-job {
            background: #ffffff;
            border-color: #e2e8f0;
        }

        .cd-job-profile-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .cd-company-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .cd-avatar-letter {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .cd-job-info {
            min-width: 0;
            flex-grow: 1;
        }

        .cd-job-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            text-decoration: none;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: color 0.15s;
        }

        .cd-job-title:hover {
            color: var(--cd-primary-blue);
        }

        .cd-job-meta-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 3px;
            flex-wrap: wrap;
        }

        .cd-req-badge {
            font-size: 10px;
            font-weight: 700;
            color: #2563eb;
            background: #eff6ff;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid #dbeafe;
        }

        .cd-scope-pill {
            font-size: 10px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .cd-scope-direct {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .cd-client-name {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cd-emp-pill {
            font-size: 10px;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1px 6px;
            border-radius: 4px;
            margin-left: 4px;
        }

        /* Sub-Card 2: Comp & Exp */
        .cd-comp-val {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }

        .cd-info-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }

        .cd-info-row i {
            font-size: 10.5px;
            color: #94a3b8;
            width: 13px;
            text-align: center;
        }

        /* Sub-Cards 3 & 4: Sender & Recipient */
        .cd-party-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cd-party-logo {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            flex-shrink: 0;
        }

        .cd-party-name {
            font-size: 11.5px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cd-contact-btn {
            border: 1px solid var(--cd-border-color);
            background: #ffffff;
            color: #334155;
            font-size: 10.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            width: fit-content;
            transition: all 0.15s ease;
        }

        .cd-contact-btn:hover {
            border-color: var(--cd-primary-blue);
            color: var(--cd-primary-blue);
            background: #eff6ff;
        }

        /* Sub-Card 5: Actions */
        .cd-actions-box {
            display: flex;
            flex-direction: column;
            gap: 6px;
            justify-content: center;
            height: 100%;
        }

        .cd-btn-secondary-action {
            background: #ffffff;
            border: 1px solid var(--cd-border-color);
            color: #475569;
            font-size: 10.5px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .cd-btn-secondary-action:hover {
            border-color: var(--cd-primary-blue);
            color: var(--cd-primary-blue);
            background: #f8fafc;
        }

        /* Sub-Card 6: Bottom Activity Strip */
        .cd-activity-strip {
            border-top: 1px solid #f1f5f9;
            padding: 7px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #fafbfc;
            flex-wrap: wrap;
        }

        .cd-activity-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cd-openings-pill {
            font-size: 10.5px;
            font-weight: 700;
            color: #0369a1;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            padding: 2px 7px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .cd-skills-wrap {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .cd-skill-pill {
            font-size: 10px;
            font-weight: 500;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 1px 7px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .cd-activity-right {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* Badges */
        .cd-badge-accepted {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
        }

        .cd-badge-closed {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
        }

        /* Skeleton Loading Cards */
        .cd-jr-skeleton-card {
            background: #ffffff;
            border: 1px solid var(--cd-border-color);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            animation: cdShimmer 1.5s infinite;
        }

        @keyframes cdShimmer {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .cd-skeleton-line {
            height: 12px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        /* Empty State */
        .cd-jr-empty-state {
            background: #ffffff;
            border: 1px solid var(--cd-border-color);
            border-radius: 10px;
            padding: 48px 24px;
            text-align: center;
            margin: 10px 0;
        }

        .cd-jr-empty-icon {
            font-size: 40px;
            color: #cbd5e1;
            margin-bottom: 14px;
        }

        .cd-jr-empty-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .cd-jr-empty-sub {
            font-size: 12px;
            color: #64748b;
            max-width: 420px;
            margin: 0 auto;
        }

        /* Modern Standard SuperAdmin Pagination Styling */
        .sa-pagination-bar {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 18px;
            border: 1px solid var(--cd-border-color);
            background: #ffffff;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            border-radius: 10px;
            box-sizing: border-box;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .sa-pagination-info {
            font-size: 13px;
            font-weight: 500;
            color: #7e8299;
            margin: 0;
        }

        .sa-pagination-info span {
            font-weight: 700;
            color: #1e293b;
        }

        .sa-pagination-controls {
            display: flex !important;
            align-items: center !important;
            gap: 6px;
            margin: 0;
            padding: 0;
        }

        .sa-pagination-controls .page-link {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1.5px solid #e4e6ef !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            color: #5e6278 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1;
            box-shadow: none !important;
            outline: none !important;
            margin: 0;
        }

        .sa-pagination-controls .page-link:hover:not(.active):not(.disabled):not(.previous_disabled):not(.next_disabled):not(.pagi-disabled) {
            background: #f1faff !important;
            border-color: #009ef7 !important;
            color: #009ef7 !important;
        }

        .sa-pagination-controls .page-link.active,
        .sa-pagination-controls .page-link.pagi-active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            cursor: default;
            pointer-events: none;
        }

        .sa-pagination-controls .page-link.disabled,
        .sa-pagination-controls .page-link.previous_disabled,
        .sa-pagination-controls .page-link.next_disabled,
        .sa-pagination-controls .page-link.pagi-disabled {
            opacity: 0.35 !important;
            cursor: not-allowed !important;
            pointer-events: none;
            background: #ffffff !important;
            border-color: #eff2f5 !important;
            color: #b5b5c3 !important;
        }

        .sa-pagination-controls .page-link.dots {
            border: none !important;
            background: transparent !important;
            cursor: default;
            pointer-events: none;
            color: #b5b5c3 !important;
            padding: 0 4px;
            min-width: auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1280px) {
            .cd-jr-card-grid {
                grid-template-columns: minmax(0, 2fr) minmax(0, 1.3fr) minmax(0, 1.3fr) minmax(0, 1.3fr) minmax(0, 1.4fr) !important;
                gap: 8px !important;
                padding: 10px 12px !important;
            }
        }

        @media (max-width: 991.98px) {
            .cd-jr-card-grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 640px) {
            .cd-jr-card-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="cd-jr-page-wrapper">
        <div class="container-fluid px-3 pt-2">

            <!-- ====================================================================
                 TIER 1: TOP HEADER TOOLBAR CARD
                 ==================================================================== -->
            <div class="cd-jr-header-card">
                <div class="cd-jr-header-left">
                    <div class="cd-jr-header-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <h1 class="cd-jr-header-title">Accepted Collaboration Requests</h1>
                        <div class="cd-jr-header-sub">Super Admin Platform Oversight • Authoritative Accepted Stream</div>
                    </div>
                </div>

                <div class="cd-jr-header-right">
                    <!-- Dynamic Instant Search Sub-Card -->
                    <div class="cd-jr-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               id="inputJobRequestSearch"
                               class="cd-jr-search-input"
                               placeholder="Search title, sender, recipient, skill, city..."
                               autocomplete="off">
                        <button type="button" class="cd-jr-search-clear" id="btnJobRequestSearchClear" title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Live Count Badge -->
                    <span class="badge badge-light-success fw-bold px-3 py-2" id="headerLiveBadge">
                        <span id="headerLiveCount">{{ (int) (@$dataCnt['customRequisition'] ?? 0) }}</span> Accepted
                    </span>

                    <!-- Refresh Button -->
                    <button type="button" class="cd-jr-refresh-btn" id="btnRefreshJobRequests" title="Refresh data">
                        <i class="fas fa-sync-alt" id="refreshIcon"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>

            <!-- ====================================================================
                 TIER 2: SUB-TABS CARD (Pure White Background)
                 ==================================================================== -->
            <div class="cd-jr-tabs-card">
                <div class="cd-jr-tabs-group">
                    <!-- Tab 1: Job Requests Pending -->
                    <a href="{{ url('super-admin/jobs/job_request/0') }}" class="cd-jr-tab-btn" id="tabJobRequestPending">
                        <i class="fas fa-clock"></i>
                        <span>Job Requests Pending</span>
                        <span class="cd-jr-tab-badge" id="span_jobRequest">{{ (int) (@$dataCnt['jobRequest'] ?? 0) }}</span>
                    </a>

                    <!-- Tab 2: Job Accepted (Active) -->
                    <a href="javascript:void(0);" class="cd-jr-tab-btn active" id="tabJobRequestAccepted">
                        <i class="fas fa-check-circle"></i>
                        <span>Job Accepted</span>
                        <span class="cd-jr-tab-badge" id="span_customRequisition">{{ (int) (@$dataCnt['customRequisition'] ?? 0) }}</span>
                    </a>
                </div>

                <!-- Live CQRS WebSocket Connection Indicator -->
                <div class="cd-jr-live-status-pill">
                    <span class="cd-jr-live-dot"></span>
                    <span>Live 0ms CQRS Read</span>
                </div>
            </div>

            <!-- ====================================================================
                 TIER 3: PRIMARY CARD STREAM CONTAINER
                 ==================================================================== -->
            <div id="jobRequestsStreamContainer" class="cd-jr-stream">
                <!-- Skeleton loader on initial render -->
                <div class="cd-jr-skeleton-card">
                    <div class="cd-skeleton-line" style="width: 40%;"></div>
                    <div class="cd-skeleton-line" style="width: 70%;"></div>
                    <div class="cd-skeleton-line" style="width: 90%;"></div>
                </div>
                <div class="cd-jr-skeleton-card">
                    <div class="cd-skeleton-line" style="width: 35%;"></div>
                    <div class="cd-skeleton-line" style="width: 65%;"></div>
                    <div class="cd-skeleton-line" style="width: 85%;"></div>
                </div>
            </div>

            <!-- Pagination Container -->
            <div id="paginationContainer" class="mt-3 mb-4 w-100"></div>

        </div>
    </div>

    <!-- ====================================================================
         DRAWERS & MODALS (Enterprise Isolated Integrations)
         ==================================================================== -->

    <!-- Direct Client Requisition Drawer -->
    <div id="kt_activitiesClientjob_toggle" style="display:none;"></div>
    <div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '90%', 'lg': '80%'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
        <div class="card shadow-none rounded-0 w-100 h-100">
            <div class="card-header px-5 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h3 class="card-title fw-bolder text-dark mb-0 fs-5">Direct Client Requisition</h3>
                        <span class="text-muted fs-7">Detailed requisition profile and specifications</span>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_activitiesClientjob_close">
                        <i class="fas fa-times fs-4"></i>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative p-0" id="kt_activitiesclientjob_body" style="overflow-y:auto;"></div>
        </div>
    </div>

    <!-- Direct Client Contact Person / Recruiter Drawer -->
    <div id="kt_activitiesRecruiter_toggle" style="display:none;"></div>
    <div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '650px', 'lg': '750px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
        <div class="card shadow-none rounded-0 w-100 h-100">
            <div class="card-header px-5 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px bg-light-info text-info me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h3 class="card-title fw-bolder text-dark mb-0 fs-5" id="light_recruiter_name">Contact Person</h3>
                        <span class="text-muted fs-7">Organization point of contact details</span>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_activitiesRecruiter_close">
                        <i class="fas fa-times fs-4"></i>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative p-4" id="kt_activitiesRecruiter_body" style="overflow-y:auto;">&nbsp;</div>
        </div>
    </div>

    <!-- Terms & Conditions Drawer -->
    <div id="kt_activitiesTerms_toggle" style="display:none;"></div>
    <div id="kt_activitiesTerms" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', 'sm': '550px', 'lg': '680px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesTerms_toggle" data-kt-drawer-close="#kt_activitiesTerms_close">
        <div class="card shadow-none rounded-0 w-100 h-100">
            <div class="card-header px-5 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px bg-light-warning text-warning me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <h3 class="card-title fw-bolder text-dark mb-0 fs-5">Collaboration Contract Terms</h3>
                        <span class="text-muted fs-7">Multi-tenant agreed terms & commission structures</span>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_activitiesTerms_close">
                        <i class="fas fa-times fs-4"></i>
                    </button>
                </div>
            </div>
            <div class="card-body position-relative p-5" id="kt_activitiesTerms_body" style="overflow-y:auto;">
                <div class="p-4 bg-light rounded text-muted">Loading terms...</div>
            </div>
        </div>
    </div>

    <!-- Conversation Feedback Modal -->
    <div href="javascript:void(0);" id="intervied_feedback_toggle" style="display:none;"
        data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
    <div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack py-3 px-5 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-35px bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="modal-title fw-bolder text-dark fs-5 mb-0">Conversation & Hiring Notes</h3>
                    </div>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="fas fa-times fs-4"></i>
                    </div>
                </div>
                <div class="modal-body p-5" id="load_interview_feedback">
                    <div class="text-center py-12"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <script>
        /**
         * JERO Super Admin Accepted Job Collaboration Requests Client-First Engine
         */

        window.jobDrawerCache = {};

        window.jobRequestsState = {
            all: [],
            filtered: [],
            total: 0,
            pendingCount: {{ (int) (@$dataCnt['jobRequest'] ?? 0) }},
            acceptedCount: {{ (int) (@$dataCnt['customRequisition'] ?? 0) }},
            currentPage: 1,
            pageSize: 10,
            searchQuery: '',
            statusFilter: 'accepted'
        };

        window.jobRequestsAbortController = null;

        function getAvatarColorClass(str) {
            if (!str) return 'bg-light-primary text-primary';
            const code = str.charCodeAt(0);
            if (code >= 97 && code <= 101) return 'bg-light-danger text-danger';
            if (code >= 102 && code <= 106) return 'bg-light-warning text-warning';
            if (code >= 107 && code <= 111) return 'bg-light-primary text-primary';
            if (code >= 112 && code <= 116) return 'bg-light-success text-success';
            if (code >= 117 && code <= 121) return 'bg-light-info text-info';
            return 'bg-light-secondary text-dark';
        }

        function renderJobRequestsClientSide(items) {
            if (!items || items.length === 0) {
                return `
                    <div class="cd-jr-empty-state">
                        <div class="cd-jr-empty-icon"><i class="fas fa-check-circle"></i></div>
                        <h4 class="cd-jr-empty-title">No Accepted Requests Found</h4>
                        <p class="cd-jr-empty-sub">There are currently no accepted job collaboration requests matching your query.</p>
                    </div>
                `;
            }

            let html = '';
            items.forEach((item) => {
                const titleFirstLetter = (item.title && item.title.trim().length > 0) ? item.title.trim().charAt(0).toUpperCase() : 'R';
                const colorClass = getAvatarColorClass(titleFirstLetter.toLowerCase());

                const logoHtml = (item.logo && item.logo.length > 10 && item.logo !== '{{ asset("img/client-default.png") }}')
                    ? `<img src="${item.logo}" alt="Logo" class="cd-company-logo" onerror="this.onerror=null;this.src='{{ asset('img/client-default.png') }}';">`
                    : `<div class="cd-avatar-letter ${colorClass}">${titleFirstLetter}</div>`;

                const logoToHtml = (item.logo_to && item.logo_to.length > 10 && item.logo_to !== '{{ asset("img/client-default.png") }}')
                    ? `<img src="${item.logo_to}" alt="Recipient" class="cd-party-logo" onerror="this.onerror=null;this.src='{{ asset('img/client-default.png') }}';">`
                    : `<div class="cd-party-logo d-flex align-items-center justify-content-center bg-light-primary text-primary fw-bold fs-7">${(item.company_name_to || 'P').charAt(0)}</div>`;

                let skillsHtml = '';
                if (Array.isArray(item.skills) && item.skills.length > 0) {
                    const maxDisplay = 4;
                    item.skills.slice(0, maxDisplay).forEach((sk) => {
                        skillsHtml += `<span class="cd-skill-pill">${escapeHtml(sk)}</span>`;
                    });
                    if (item.skills.length > maxDisplay) {
                        skillsHtml += `<span class="cd-skill-pill fw-bold text-primary">+${item.skills.length - maxDisplay} more</span>`;
                    }
                } else {
                    skillsHtml = `<span class="text-muted fs-8">General Requisition</span>`;
                }

                let statusBadge = '';
                if (item.final_status === 1) {
                    statusBadge = `<span class="cd-badge-accepted"><i class="fas fa-check-circle me-1"></i> Accepted</span>`;
                } else {
                    statusBadge = `<span class="cd-badge-closed"><i class="fas fa-times-circle me-1"></i> Closed</span>`;
                }

                const safeTerms = item.accepterms ? encodeURIComponent(item.accepterms) : '';

                html += `
                    <div class="cd-jr-card" data-rec-id="${item.id}" data-job-id="${item.job_id}">
                        <div class="cd-jr-card-grid">

                            <!-- SUB-CARD 1: Job Details & Client Profile -->
                            <div class="cd-subcard cd-subcard-job">
                                <div>
                                    <div class="cd-job-profile-row">
                                        <div class="client-show-detail" data-row-id="${item.job_id}" style="cursor:pointer;">
                                            ${logoHtml}
                                        </div>
                                        <div class="cd-job-info">
                                            <a href="javascript:void(0);"
                                               class="cd-job-title client-show-detail"
                                               data-row-id="${item.job_id}"
                                               title="${escapeHtml(item.title)}">
                                                ${escapeHtml(item.title)}
                                            </a>
                                            <div class="cd-job-meta-row">
                                                <span class="cd-req-badge">#R-${item.job_id}</span>
                                                <span class="cd-scope-pill cd-scope-direct">Direct Client</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cd-client-name">
                                        <span>${escapeHtml(item.clients_name || item.company_name)}</span>
                                        <span class="cd-emp-pill"><i class="fas fa-briefcase me-1"></i>${escapeHtml(item.employment_type)}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-CARD 2: Compensation & Experience -->
                            <div class="cd-subcard">
                                <div class="cd-subcard-header-label">
                                    <i class="fas fa-coins text-warning"></i> COMPENSATION & EXP
                                </div>
                                <div>
                                    <div class="cd-comp-val">${escapeHtml(item.salary_display)}</div>
                                    <div class="cd-info-row">
                                        <i class="fas fa-history"></i>
                                        <span>${escapeHtml(item.exp_display)}</span>
                                    </div>
                                    <div class="cd-info-row">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        <span>${escapeHtml(item.city_name)} • ${escapeHtml(item.work_mode)}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-CARD 3: Request Sender (Origin) -->
                            <div class="cd-subcard">
                                <div class="cd-subcard-header-label">
                                    <i class="fas fa-paper-plane text-primary"></i> SENDER / ORIGIN
                                </div>
                                <div>
                                    <div class="cd-party-box">
                                        <img src="${item.logo}" alt="Sender" class="cd-party-logo" onerror="this.onerror=null;this.src='{{ asset('img/client-default.png') }}';">
                                        <div class="cd-party-name" title="${escapeHtml(item.company_name)}">
                                            ${escapeHtml(item.company_name)}
                                        </div>
                                    </div>
                                    <div class="cd-info-row mt-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Req: ${escapeHtml(item.created_at)}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-CARD 4: Request Destination & Partner -->
                            <div class="cd-subcard">
                                <div class="cd-subcard-header-label">
                                    <i class="fas fa-building text-info"></i> TARGET RECIPIENT
                                </div>
                                <div>
                                    <div class="cd-party-box">
                                        ${logoToHtml}
                                        <div class="cd-party-name" title="${escapeHtml(item.company_name_to)}">
                                            ${escapeHtml(item.company_name_to)}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <button type="button"
                                                class="cd-contact-btn client-terms-listing"
                                                data-row-id="${item.id}"
                                                data-row-recommendation_relation_id="${escapeHtml(item.company_name_to)}"
                                                title="View Contact Person">
                                            <i class="fas fa-user"></i> Contact
                                        </button>
                                        <div>${statusBadge}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-CARD 5: Action Hub & Feedback -->
                            <div class="cd-subcard cd-subcard-actions">
                                <div class="cd-subcard-header-label">
                                    <i class="fas fa-bolt text-warning"></i> ACTIONS & NOTES
                                </div>
                                <div class="cd-actions-box">
                                    <button type="button"
                                            class="cd-btn-secondary-action js-btn-view-terms w-100 mb-1"
                                            data-rec-id="${item.id}"
                                            data-terms="${safeTerms}"
                                            title="View Contract Terms">
                                        <i class="fas fa-file-contract text-warning"></i> Contract Terms
                                    </button>
                                    <button type="button"
                                            class="cd-btn-secondary-action show-feedback w-100"
                                            data-row-job_id="${item.job_id}"
                                            data-row-conversations_id="${item.conversation_id || ''}"
                                            data-row-recommendation_relation_id="${item.id}"
                                            title="Conversation & Notes">
                                        <i class="fas fa-comment-dots text-primary me-1"></i>
                                        <span>${escapeHtml((item.conversation_feedback && item.conversation_feedback !== 'Add Note') ? item.conversation_feedback : 'Conversation Notes')}</span>
                                        ${item.conversation_id ? '<span class="badge badge-light-primary ms-1 px-1 py-0 fs-9">Active</span>' : ''}
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- SUB-CARD 6: Bottom Activity Strip -->
                        <div class="cd-activity-strip">
                            <div class="cd-activity-left">
                                <span class="cd-openings-pill">
                                    <i class="fas fa-user-plus"></i> ${item.total_positions || 1} Openings
                                </span>
                                <div class="cd-skills-wrap">
                                    ${skillsHtml}
                                </div>
                            </div>
                            <div class="cd-activity-right">
                                <span><i class="fas fa-clock me-1"></i> Dispatched ${escapeHtml(item.created_at)}</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            return html;
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /**
         * Standard SuperAdmin Pagination Renderer (10 records per page, matching other pages)
         */
        function renderPagination(total, currentPage, pageSize = 10) {
            const totalCount = parseInt(total) || 0;
            const current = parseInt(currentPage) || 1;
            const size = 10;
            const totalPages = Math.ceil(totalCount / size);

            if (totalCount === 0) return '';

            const startRecord = (current - 1) * size + 1;
            const endRecord = Math.min(current * size, totalCount);

            let html = `
                <div class="sa-pagination-bar">
                    <div class="sa-pagination-info">
                        Showing <span>${startRecord}</span> to <span>${endRecord}</span> of <span>${totalCount}</span> records.
                    </div>
                    <div class="sa-pagination-controls">
            `;

            // Previous Button
            if (current > 1) {
                html += `
                    <button type="button" class="page-link pagi-arrow" data-page="${current - 1}" aria-label="Previous Page">
                        <i class="fas fa-chevron-left" style="font-size: 11px;"></i>
                    </button>
                `;
            } else {
                html += `
                    <button type="button" class="page-link pagi-arrow disabled previous_disabled pagi-disabled" aria-label="Previous Page" disabled>
                        <i class="fas fa-chevron-left" style="font-size: 11px;"></i>
                    </button>
                `;
            }

            // Page Numbers with sliding window
            let startPage = Math.max(1, current - 2);
            let endPage = Math.min(totalPages, current + 2);

            if (startPage > 1) {
                html += `<button type="button" class="page-link pagi-page" data-page="1">1</button>`;
                if (startPage > 2) {
                    html += `<span class="page-link dots">…</span>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === current) {
                    html += `<button type="button" class="page-link pagi-page active pagi-active disabled active_disabled">${i}</button>`;
                } else {
                    html += `<button type="button" class="page-link pagi-page" data-page="${i}">${i}</button>`;
                }
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="page-link dots">…</span>`;
                }
                html += `<button type="button" class="page-link pagi-page" data-page="${totalPages}">${totalPages}</button>`;
            }

            // Next Button
            if (current < totalPages) {
                html += `
                    <button type="button" class="page-link pagi-arrow" data-page="${current + 1}" aria-label="Next Page">
                        <i class="fas fa-chevron-right" style="font-size: 11px;"></i>
                    </button>
                `;
            } else {
                html += `
                    <button type="button" class="page-link pagi-arrow disabled next_disabled pagi-disabled" aria-label="Next Page" disabled>
                        <i class="fas fa-chevron-right" style="font-size: 11px;"></i>
                    </button>
                `;
            }

            html += `
                    </div>
                </div>
            `;
            return html;
        }

        function filterJobRequestsInRam(query) {
            const q = (query || '').toLowerCase().trim();
            if (!q) {
                window.jobRequestsState.filtered = window.jobRequestsState.all;
            } else {
                window.jobRequestsState.filtered = window.jobRequestsState.all.filter((item) => {
                    return (item.title && item.title.toLowerCase().includes(q)) ||
                           (item.company_name && item.company_name.toLowerCase().includes(q)) ||
                           (item.company_name_to && item.company_name_to.toLowerCase().includes(q)) ||
                           (item.clients_name && item.clients_name.toLowerCase().includes(q)) ||
                           (item.city_name && item.city_name.toLowerCase().includes(q)) ||
                           (String(item.job_id).includes(q)) ||
                           (Array.isArray(item.skills) && item.skills.some(s => s && s.toLowerCase().includes(q)));
                });
            }

            $('#jobRequestsStreamContainer').html(renderJobRequestsClientSide(window.jobRequestsState.filtered));
            $('#headerLiveCount').text(window.jobRequestsState.filtered.length);
        }

        function fetchJobRequests(page = 1, searchQuery = '') {
            window.fetchJobRequests = fetchJobRequests;
            window.fetchjobrequestpending = function() { fetchJobRequests(window.jobRequestsState ? window.jobRequestsState.currentPage : 1, $('#inputJobRequestSearch').val() || ''); };
            window.fetchJobRequestAcceptAjax = function() { fetchJobRequests(window.jobRequestsState ? window.jobRequestsState.currentPage : 1, $('#inputJobRequestSearch').val() || ''); };

            if (window.jobRequestsAbortController) {
                window.jobRequestsAbortController.abort();
            }
            window.jobRequestsAbortController = new AbortController();

            $('#refreshIcon').addClass('fa-spin');

            const size = window.jobRequestsState.pageSize || 10;
            const url = `{{ route('superadmin.company.recommandedrequest_accept_ajax') }}?page=${page}&size=${size}`;

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    search: searchQuery,
                    size: size
                },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#refreshIcon').removeClass('fa-spin');

                    if (response.status === 'success') {
                        const records = response.data || [];
                        const total = response.total !== undefined ? response.total : records.length;
                        const currentPage = response.current_page || page || 1;
                        const pageSize = response.size || window.jobRequestsState.pageSize || 10;

                        window.jobRequestsState.all = records;
                        window.jobRequestsState.filtered = records;
                        window.jobRequestsState.total = total;
                        window.jobRequestsState.pendingCount = response.pendingCount || 0;
                        window.jobRequestsState.acceptedCount = response.acceptedCount || 0;
                        window.jobRequestsState.currentPage = currentPage;
                        window.jobRequestsState.pageSize = pageSize;

                        $('#headerLiveCount').text(response.acceptedCount || total || 0);
                        $('#span_jobRequest').text(response.pendingCount || 0);
                        $('#span_customRequisition').text(response.acceptedCount || 0);

                        $('#jobRequestsStreamContainer').html(renderJobRequestsClientSide(records));
                        $('#paginationContainer').html(renderPagination(total, currentPage, pageSize));
                    } else {
                        $('#jobRequestsStreamContainer').html(`
                            <div class="cd-jr-empty-state">
                                <div class="cd-jr-empty-icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                                <h4 class="cd-jr-empty-title">Error Loading Requests</h4>
                                <p class="cd-jr-empty-sub">${escapeHtml(response.message || 'Unable to fetch data from Elasticsearch.')}</p>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    $('#refreshIcon').removeClass('fa-spin');
                    if (status === 'abort') return;

                    $('#jobRequestsStreamContainer').html(`
                        <div class="cd-jr-empty-state">
                            <div class="cd-jr-empty-icon text-danger"><i class="fas fa-exclamation-circle"></i></div>
                            <h4 class="cd-jr-empty-title">Connection Interrupted</h4>
                            <p class="cd-jr-empty-sub">Failed to reach the platform read service. Please check your network or try refreshing.</p>
                        </div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            fetchJobRequests(1, '');

            let searchDebounceTimer = null;
            $('#inputJobRequestSearch').on('input keyup', function() {
                const query = $(this).val();

                if (query.length > 0) {
                    $('#btnJobRequestSearchClear').show();
                } else {
                    $('#btnJobRequestSearchClear').hide();
                }

                filterJobRequestsInRam(query);

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    fetchJobRequests(1, query);
                }, 350);
            });

            $('#btnJobRequestSearchClear').on('click', function() {
                $('#inputJobRequestSearch').val('');
                $(this).hide();
                filterJobRequestsInRam('');
                fetchJobRequests(1, '');
            });

            $('#btnRefreshJobRequests').on('click', function() {
                const q = $('#inputJobRequestSearch').val();
                fetchJobRequests(window.jobRequestsState.currentPage, q);
            });

            $(document).on('click', '#paginationContainer .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page') || $(this).attr('data-page');
                if (!page || $(this).hasClass('disabled') || $(this).hasClass('active') || $(this).hasClass('pagi-disabled') || $(this).hasClass('dots')) return;
                const q = $('#inputJobRequestSearch').val() || '';
                window.jobRequestsState.currentPage = parseInt(page);
                fetchJobRequests(window.jobRequestsState.currentPage, q);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Drawers wiring
            $(document).on('click', '.client-show-detail, .show-detail', function(e) {
                e.preventDefault();
                const jobId = $(this).data('row-id');
                if (!jobId) return;

                $('#kt_activitiesClientjob_toggle').trigger('click');
                const cacheKey = 'job_' + jobId;

                if (window.jobDrawerCache[cacheKey]) {
                    $('#kt_activitiesclientjob_body').html(window.jobDrawerCache[cacheKey]);
                    return;
                }

                $('#kt_activitiesclientjob_body').html(`
                    <div class="text-center py-20">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <div class="text-muted fs-7">Loading Requisition Specifications...</div>
                    </div>
                `);

                const url = "{{ route('superadmin.company.jobs.job_viewmore', ':id') }}".replace(':id', jobId);

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        $('#kt_activitiesclientjob_body').html(response.view || response);
                        window.jobDrawerCache[cacheKey] = response.view || response;
                    },
                    error: function() {
                        $('#kt_activitiesclientjob_body').html(`
                            <div class="text-center py-20 text-danger">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <div>Failed to load requisition details.</div>
                            </div>
                        `);
                    }
                });
            });

            $(document).on('click', '.client-terms-listing', function(e) {
                e.preventDefault();
                const recId = $(this).data('row-id');
                const companyName = $(this).data('row-recommendation_relation_id') || 'Contact Person';

                $('#light_recruiter_name').text(companyName);
                $('#kt_activitiesRecruiter_toggle').trigger('click');

                $('#kt_activitiesRecruiter_body').html(`
                    <div class="text-center py-20">
                        <i class="fas fa-spinner fa-spin fa-2x text-info mb-3"></i>
                        <div class="text-muted fs-7">Fetching Organization Contact...</div>
                    </div>
                `);

                const url = "{{ route('superadmin.company.hmlisting', ':id') }}".replace(':id', recId);

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': recId
                    },
                    success: function(response) {
                        $('#kt_activitiesRecruiter_body').html(response.view || response);
                    },
                    error: function() {
                        $('#kt_activitiesRecruiter_body').html(`
                            <div class="text-center py-20 text-danger">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <div>Could not retrieve contact person profile.</div>
                            </div>
                        `);
                    }
                });
            });

            $(document).on('click', '.js-btn-view-terms', function(e) {
                e.preventDefault();
                const recId = $(this).data('rec-id');
                const encodedTerms = $(this).data('terms');
                let rawTerms = '';

                try {
                    rawTerms = encodedTerms ? decodeURIComponent(encodedTerms) : '';
                } catch(e) {
                    rawTerms = encodedTerms;
                }

                $('#kt_activitiesTerms_toggle').trigger('click');

                if (rawTerms && rawTerms.trim() !== '') {
                    $('#kt_activitiesTerms_body').html(`
                        <div class="card card-custom">
                            <div class="card-body p-5">
                                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shield-alt text-success me-2"></i>Agreed Collaboration Terms</h5>
                                <div class="p-4 bg-light rounded text-dark fs-7 lh-lg" style="white-space: pre-line;">
                                    ${escapeHtml(rawTerms)}
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('#kt_activitiesTerms_body').html(`
                        <div class="card card-custom">
                            <div class="card-body p-5 text-center">
                                <i class="fas fa-file-invoice text-muted fa-3x mb-3"></i>
                                <h5 class="fw-bold text-dark">Standard Platform Terms</h5>
                                <p class="text-muted fs-7">No custom contract terms override specified. Standard multi-tenant recruitment terms apply.</p>
                            </div>
                        </div>
                    `);
                }
            });

            $(document).on('click', '.show-feedback', function(e) {
                e.preventDefault();
                $('#intervied_feedback_toggle').trigger('click');

                const jobId = $(this).attr('data-row-job_id') || $(this).data('row-job_id');
                const convId = $(this).attr('data-row-conversations_id') || $(this).data('row-conversations_id') || '';
                const recId = $(this).attr('data-row-recommendation_relation_id') || $(this).data('row-recommendation_relation_id') || '';

                $('#load_interview_feedback').html(`
                    <div class="text-center py-12">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <div class="text-muted fs-7">Loading Conversation Thread...</div>
                    </div>
                `);

                const url = "{{ route('superadmin.company.showfeedback', ':id') }}".replace(':id', jobId);

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'id': jobId,
                        'conversations_id': convId,
                        'recommendation_relation_id': recId
                    },
                    success: function(response) {
                        if (response.status === 'success' || response.view) {
                            $('#load_interview_feedback').html(response.view || response);
                        } else {
                            $('#load_interview_feedback').html(`
                                <div class="text-center py-8 text-muted">
                                    <i class="fas fa-comment-slash fa-2x mb-2"></i>
                                    <div>No conversation notes recorded yet.</div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#load_interview_feedback').html(`
                            <div class="text-center py-8 text-danger">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <div>Failed to load feedback conversation.</div>
                            </div>
                        `);
                    }
                });
            });

            // ----------------------------------------------------------------------
            // PHASE 7: REAL-TIME WEBSOCKET & BROADCASTCHANNEL SYNC
            // ----------------------------------------------------------------------
            const crossTabChannel = ('BroadcastChannel' in window)
                ? new BroadcastChannel('jero_superadmin_job_requests')
                : null;
            window.crossTabChannel = crossTabChannel;

            if (crossTabChannel) {
                crossTabChannel.onmessage = function(e) {
                    if (e.data && e.data.type === 'job_request_updated') {
                        handleRealtimeUpdate(e.data.payload);
                    }
                };
            }

            function handleRealtimeUpdate(delta) {
                if (!delta || !delta.rec_relation_id) return;

                const item = window.jobRequestsState.all.find(x => String(x.id) === String(delta.rec_relation_id));
                if (item) {
                    if (delta.final_status !== undefined) item.final_status = delta.final_status;
                    if (delta.status_label !== undefined) item.status_label = delta.status_label;
                    if (delta.conversation_feedback !== undefined) item.conversation_feedback = delta.conversation_feedback;
                    if (delta.conversation_id !== undefined) item.conversation_id = delta.conversation_id;
                    if (delta.conversations_status !== undefined) item.conversations_status = delta.conversations_status;

                    if (delta.conversation_feedback) {
                        const $btn = $(`.show-feedback[data-row-recommendation_relation_id="${delta.rec_relation_id}"]`);
                        if ($btn.length) {
                            $btn.html(`
                                <i class="fas fa-comment-dots text-primary me-1"></i>
                                <span>${escapeHtml(delta.conversation_feedback)}</span>
                                <span class="badge badge-light-primary ms-1 px-1 py-0 fs-9">Active</span>
                            `);
                            if (delta.conversation_id) {
                                $btn.attr('data-row-conversations_id', delta.conversation_id);
                            }
                            $btn.css('transition', 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)')
                                .css('box-shadow', '0 0 0 3px rgba(0, 158, 247, 0.4)')
                                .css('background', '#eff6ff');
                            setTimeout(() => { $btn.css('box-shadow', '').css('background', ''); }, 1500);
                        }
                    } else {
                        filterJobRequestsInRam($('#inputJobRequestSearch').val());
                    }

                    const $card = $(`.cd-jr-card[data-rec-id="${delta.rec_relation_id}"]`);
                    $card.css('transition', 'box-shadow 0.3s ease').css('box-shadow', '0 0 0 3px rgba(0, 158, 247, 0.4)');
                    setTimeout(() => { $card.css('box-shadow', ''); }, 1500);
                }

                if (delta.counters) {
                    if (delta.counters.jobRequest !== undefined) {
                        $('#span_jobRequest').text(delta.counters.jobRequest);
                    }
                    if (delta.counters.jobAccepted !== undefined) {
                        $('#span_customRequisition').text(delta.counters.jobAccepted);
                        $('#headerLiveCount').text(delta.counters.jobAccepted);
                    }
                }
            }
        });
    </script>
@endpush
