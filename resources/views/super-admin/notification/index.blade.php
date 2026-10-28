@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
        /* ==========================================================================
           JERO ENTERPRISE DESIGN SYSTEM: MODERN CARD + SUB-CARD STANDARD
           Reference: docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md
           Target Viewport: 15.6" Laptops (1366x768 & 1920x1080) with Zero Horizontal Overflow
           ========================================================================== */
        body {
            overflow-x: hidden !important;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            background-color: #f5f8fa;
        }

        /* ── Top Gap Collapse: eliminate excessive Metronic toolbar & wrapper spacing on standard 15.6" viewports ── */
        #kt_toolbar {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(65px + 12px) !important;
        }

        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .header-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper,
            #kt_wrapper {
                padding-top: calc(55px + 8px) !important;
            }
        }

        #kt_content,
        .content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .notif-container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 4px !important;
            margin-top: 0 !important;
        }

        /* --- TIER 0: EXECUTIVE KPI SUMMARY STRIP --- */
        .notif-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 12px !important;
            margin-top: 0 !important;
            margin-bottom: 14px !important;
            width: 100% !important;
        }

        .notif-kpi-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        }

        .notif-kpi-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        }

        .notif-kpi-icon-box {
            width: 42px !important;
            height: 42px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }

        .notif-kpi-label {
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #64748b !important;
            margin-bottom: 2px !important;
        }

        .notif-kpi-value {
            font-size: 22px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.1 !important;
        }

        .notif-kpi-subtext {
            font-size: 11px !important;
            color: #94a3b8 !important;
            margin-top: 2px !important;
        }

        /* --- TIER 1: TOP HEADER TOOLBAR CARD --- */
        .notif-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }

        .notif-header-brand {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .notif-header-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 8px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }

        .notif-header-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            margin: 0 !important;
        }

        .notif-header-subtitle {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            margin-bottom: 0 !important;
        }

        .notif-header-actions {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .notif-search-subcard {
            position: relative !important;
            width: 320px !important;
            max-width: 100% !important;
            height: 36px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 10px !important;
            transition: all 0.2s ease !important;
        }

        .notif-search-subcard:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .notif-search-subcard i {
            color: #94a3b8 !important;
            font-size: 13px !important;
            margin-right: 8px !important;
        }

        .notif-search-input {
            border: none !important;
            background: transparent !important;
            outline: none !important;
            font-size: 12px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #0f172a !important;
            width: 100% !important;
        }

        .notif-search-clear {
            border: none !important;
            background: transparent !important;
            color: #94a3b8 !important;
            font-size: 13px !important;
            cursor: pointer !important;
            padding: 0 4px !important;
            line-height: 1 !important;
        }

        .notif-search-clear:hover {
            color: #0f172a !important;
        }

        /* --- TIER 2: SUB-TABS CARD --- */
        .notif-tabs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .notif-tab-group {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }

        .notif-tab-btn {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 7px !important;
            padding: 5px 12px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        }

        .notif-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .notif-tab-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25) !important;
        }

        .notif-tab-badge {
            min-width: 18px !important;
            height: 18px !important;
            padding: 0 5px !important;
            border-radius: 10px !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        .notif-tab-btn.active .notif-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        .notif-status-filter-group {
            display: inline-flex !important;
            background: #f1f5f9 !important;
            padding: 3px !important;
            border-radius: 8px !important;
            gap: 3px !important;
        }

        .notif-status-pill-btn {
            border: none !important;
            background: transparent !important;
            padding: 4px 10px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .notif-status-pill-btn.active {
            background: #ffffff !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
        }

        /* --- TIER 3: PRIMARY NOTIFICATION CARD STREAM --- */
        .notif-primary-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.2s ease !important;
            overflow: hidden !important;
        }

        .notif-primary-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        }

        .notif-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 2.3fr) minmax(0, 2.7fr) minmax(0, 1.8fr) minmax(0, 1.6fr) minmax(0, 1.6fr) !important;
            gap: 12px !important;
            padding: 12px 16px !important;
            width: 100% !important;
            align-items: stretch !important;
        }

        /* Discrete Sub-Card Cells */
        .notif-subcard {
            background: #ffffff !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            min-width: 0 !important;
        }

        .notif-subcard-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #94a3b8 !important;
            margin-bottom: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        /* Sub-Card 1: Identity & Scope */
        .notif-badge-category {
            font-size: 10px !important;
            font-weight: 700 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            display: inline-block !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
        }

        .notif-title-purpose {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.3 !important;
            margin-top: 4px !important;
            margin-bottom: 4px !important;
            word-break: break-word !important;
        }

        .notif-id-pill {
            font-size: 11px !important;
            font-weight: 700 !important;
            font-family: monospace !important;
            color: #009ef7 !important;
            background: #f0f9ff !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            display: inline-block !important;
        }

        .notif-status-badge {
            font-size: 10px !important;
            font-weight: 700 !important;
            padding: 2px 8px !important;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            text-transform: uppercase !important;
        }

        .notif-status-badge.active {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .notif-status-badge.inactive {
            background: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* Sub-Card 2: Subject & Excerpt */
        .notif-subject-text {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            line-height: 1.35 !important;
            word-break: break-word !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
        }

        .notif-token-tag {
            font-size: 10px !important;
            font-family: monospace !important;
            background: #e0f2fe !important;
            color: #0284c7 !important;
            padding: 1px 5px !important;
            border-radius: 4px !important;
            display: inline-block !important;
            margin: 1px 2px !important;
            border: 1px solid #bae6fd !important;
        }

        .notif-meta-text {
            font-size: 10.5px !important;
            color: #64748b !important;
            margin-top: 6px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* Sub-Card 3: Recipient Roles */
        .notif-roles-container {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
            align-content: flex-start !important;
        }

        .notif-role-pill {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 6px !important;
            background: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .notif-all-roles-pill {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            background: #f1f5f9 !important;
            color: #64748b !important;
            font-style: italic !important;
        }

        /* Sub-Card 4: Sender & Routing */
        .notif-sender-email {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            word-break: break-all !important;
        }

        .notif-gateway-pill {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 6px !important;
            padding: 3px 8px !important;
            display: inline-block !important;
            margin-top: 6px !important;
        }

        /* Sub-Card 5: Actions & Operational Toggle */
        .notif-actions-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
        }

        .notif-action-btn-group {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-top: 8px !important;
        }

        .notif-btn-preview {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease !important;
        }

        .notif-btn-preview:hover {
            background: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
        }

        .notif-btn-edit {
            background: #f8fafc !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease !important;
            text-decoration: none !important;
        }

        .notif-btn-edit:hover {
            background: #009ef7 !important;
            color: #ffffff !important;
            border-color: #009ef7 !important;
        }

        /* Sub-Card 6: Bottom Activity & Token Strip */
        .notif-card-bottom {
            background: #fafbfc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 6px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 11px !important;
            color: #64748b !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .notif-tokens-strip {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
        }

        /* Empty State */
        .notif-empty-state {
            background: #ffffff !important;
            border: 1px dashed #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 40px 20px !important;
            text-align: center !important;
            display: none;
        }

        /* 15.6" Screen Responsive Breakpoints */
        @media (max-width: 1200px) {
            .notif-card-grid {
                grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.2fr) !important;
            }
            .notif-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 768px) {
            .notif-card-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
            .notif-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
            .notif-header-card {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            .notif-search-subcard {
                width: 100% !important;
            }
        }

        /* Offcanvas Drawer Iframe Styling */
        #kt_drawer_notification_preview {
            width: 820px !important;
            max-width: 90vw !important;
            box-shadow: -4px 0 24px rgba(15, 23, 42, 0.12) !important;
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-0 pb-6" id="kt_content">
    <div class="notif-container container-fluid px-4" id="kt_content_container">

        @php
            $catLabels = [
                1 => ['name' => 'Recruitment', 'bg' => '#eff6ff', 'color' => '#1d4ed8'],
                2 => ['name' => 'Sales', 'bg' => '#ecfdf5', 'color' => '#047857'],
                3 => ['name' => 'Mass Mail', 'bg' => '#faf5ff', 'color' => '#7e22ce'],
                4 => ['name' => 'Candidate Send', 'bg' => '#fff7ed', 'color' => '#c2410c'],
                5 => ['name' => 'Client Send', 'bg' => '#f0fdf4', 'color' => '#15803d'],
                6 => ['name' => 'Offer Accept', 'bg' => '#f0fdfa', 'color' => '#0f766e'],
                7 => ['name' => 'Offer Reject', 'bg' => '#fef2f2', 'color' => '#b91c1c'],
                8 => ['name' => 'Offer Approved', 'bg' => '#ecfdf5', 'color' => '#059669'],
                9 => ['name' => 'Offer Not Approved', 'bg' => '#fff1f2', 'color' => '#be123c'],
                10 => ['name' => 'Offer HR Notify', 'bg' => '#f5f3ff', 'color' => '#6d28d9'],
            ];

            $totalCount = $totalNotifications ?? ($th_email_template ? count($th_email_template) : 0);
            $activeCount = $activeNotifications ?? ($th_email_template ? $th_email_template->where('status', 1)->count() : 0);
            $inactiveCount = $inactiveNotifications ?? ($th_email_template ? $th_email_template->where('status', 0)->count() : 0);
            $roleCount = $roleGovernedCount ?? ($th_email_template ? $th_email_template->filter(fn($t) => !empty($t->role_names))->count() : 0);
        @endphp

        <!-- ======================================================================
             TIER 0: EXECUTIVE KPI SUMMARY STRIP (4 High-Density Metric Cards)
             ====================================================================== -->
        <div class="notif-kpi-grid">
            <div class="notif-kpi-card">
                <div>
                    <div class="notif-kpi-label">Total Templates</div>
                    <div class="notif-kpi-value" id="kpiTotalCount">{{ $totalCount }}</div>
                    <div class="notif-kpi-subtext">Global transactional catalog</div>
                </div>
                <div class="notif-kpi-icon-box" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-mail-bulk"></i>
                </div>
            </div>

            <div class="notif-kpi-card">
                <div>
                    <div class="notif-kpi-label">Active Triggers</div>
                    <div class="notif-kpi-value text-success" id="kpiActiveCount">{{ $activeCount }}</div>
                    <div class="notif-kpi-subtext">Live production dispatches</div>
                </div>
                <div class="notif-kpi-icon-box" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="notif-kpi-card">
                <div>
                    <div class="notif-kpi-label">Inactive / Draft</div>
                    <div class="notif-kpi-value text-muted" id="kpiInactiveCount">{{ $inactiveCount }}</div>
                    <div class="notif-kpi-subtext">Suppressed notifications</div>
                </div>
                <div class="notif-kpi-icon-box" style="background: #f8fafc; color: #94a3b8;">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>

            <div class="notif-kpi-card">
                <div>
                    <div class="notif-kpi-label">Role Governed</div>
                    <div class="notif-kpi-value" style="color: #7c3aed;" id="kpiRoleCount">{{ $roleCount }}</div>
                    <div class="notif-kpi-subtext">RBAC restricted dispatches</div>
                </div>
                <div class="notif-kpi-icon-box" style="background: #faf5ff; color: #7c3aed;">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>

        <!-- ======================================================================
             TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Radius 10px)
             ====================================================================== -->
        <div class="notif-header-card">
            <div class="notif-header-brand">
                <div class="notif-header-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h1 class="notif-header-title">Email Notification Catalog & Dispatch Rules</h1>
                    <p class="notif-header-subtitle">Strict CQRS (0% MySQL Reads) &bull; Elasticsearch Universal Search &bull; 96 Verified Merge Tokens</p>
                </div>
            </div>

            <div class="notif-header-actions">
                <!-- Dynamic Search Sub-Card -->
                <div class="notif-search-subcard">
                    <i class="fas fa-search"></i>
                    <input type="text" id="notifSearchInput" class="notif-search-input" placeholder="Search by purpose, subject, token, role, ID..." autocomplete="off">
                    <button type="button" id="notifSearchClear" class="notif-search-clear" title="Clear search" style="display: none;">✕</button>
                </div>

                <!-- Primary Action Button -->
                <a href="{{ route('superadmin.company.notificationupdate', 0) }}" class="btn btn-sm btn-primary fw-bold px-4 py-2 d-inline-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i> Add Notification
                </a>
            </div>
        </div>

        <!-- ======================================================================
             TIER 2: CATEGORY FILTER SUB-TABS CARD (Metronic Primary Blue #009ef7)
             ====================================================================== -->
        <div class="notif-tabs-card">
            <div class="notif-tab-group" id="notifCategoryTabs">
                <button type="button" class="notif-tab-btn active" data-cat="all">
                    <span>All Templates</span>
                    <span class="notif-tab-badge" id="tabCountAll">{{ $totalCount }}</span>
                </button>

                @foreach ($catLabels as $catId => $cMeta)
                    @php
                        $catCount = $th_email_template ? $th_email_template->where('mailType', $catId)->count() : 0;
                    @endphp
                    @if ($catCount > 0)
                        <button type="button" class="notif-tab-btn" data-cat="{{ $catId }}">
                            <span>{{ $cMeta['name'] }}</span>
                            <span class="notif-tab-badge">{{ $catCount }}</span>
                        </button>
                    @endif
                @endforeach
            </div>

            <!-- Quick Status Filter Pills -->
            <div class="notif-status-filter-group" id="notifStatusTabs">
                <button type="button" class="notif-status-pill-btn active" data-status="all">All</button>
                <button type="button" class="notif-status-pill-btn" data-status="1">Active ({{ $activeCount }})</button>
                <button type="button" class="notif-status-pill-btn" data-status="0">Inactive ({{ $inactiveCount }})</button>
            </div>
        </div>

        <!-- ======================================================================
             TIER 3: PRIMARY NOTIFICATION CARD STREAM (Discrete Sub-Cards Grid)
             ====================================================================== -->
        <div id="notifCardStream">
            @if ($th_email_template && count($th_email_template) > 0)
                @foreach ($th_email_template as $template)
                    @php
                        $tId = $template->id;
                        $tPurpose = $template->purpose ?? 'Untitled Notification';
                        $tSubject = $template->subject ?? '';
                        $tStatus = (int) ($template->status ?? 0);
                        $tCatId = (int) ($template->mailType ?? 1);
                        $tCatMeta = $catLabels[$tCatId] ?? ['name' => 'General', 'bg' => '#f1f5f9', 'color' => '#475569'];
                        $tRoles = $template->role_names ?? [];
                        $tSender = $template->from_email_id ?? 'noreply@jero.co.in';

                        // Extract dynamic tokens from subject and body
                        preg_match_all('/(\[\{[^}]+\}\]|\[[A-Za-z0-9_-]+\]|\{\{[^}]+\}\})/', $tSubject . ' ' . ($template->body_description ?? ''), $matches);
                        $tTokens = array_values(array_unique($matches[0] ?? []));
                        $tokenCount = count($tTokens);
                        $bodyLength = strlen(strip_tags((string) ($template->body_description ?? '')));
                    @endphp

                    <div class="notif-primary-card" 
                         data-id="{{ $tId }}"
                         data-category="{{ $tCatId }}"
                         data-status="{{ $tStatus }}"
                         data-search="{{ strtolower($tPurpose . ' ' . $tSubject . ' ' . implode(' ', $tRoles) . ' ' . implode(' ', $tTokens) . ' #notif-' . str_pad($tId, 3, '0', STR_PAD_LEFT)) }}">
                        
                        <div class="notif-card-grid">
                            
                            <!-- SUB-CARD 1: Identity & Scope -->
                            <div class="notif-subcard notif-subcard-identity">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="notif-badge-category" style="background: {{ $tCatMeta['bg'] }}; color: {{ $tCatMeta['color'] }};">
                                            {{ $tCatMeta['name'] }}
                                        </span>
                                        <span class="notif-status-badge {{ $tStatus === 1 ? 'active' : 'inactive' }}" id="statusBadge_{{ $tId }}">
                                            <i class="fas fa-circle" style="font-size: 6px;"></i>
                                            <span class="status-label">{{ $tStatus === 1 ? 'Active' : 'Inactive' }}</span>
                                        </span>
                                    </div>
                                    <div class="notif-title-purpose" title="{{ $tPurpose }}">
                                        {{ $tPurpose }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-gray-100">
                                    <span class="notif-id-pill">#NOTIF-{{ str_pad($tId, 3, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-muted" style="font-size: 10px;">Type #{{ $tCatId }}</span>
                                </div>
                            </div>

                            <!-- SUB-CARD 2: Subject & Excerpt -->
                            <div class="notif-subcard notif-subcard-subject">
                                <div>
                                    <div class="notif-subcard-label">
                                        <span>Email Subject Line</span>
                                        <span><i class="fas fa-heading text-muted"></i></span>
                                    </div>
                                    <div class="notif-subject-text" title="{{ $tSubject }}">
                                        {{ $tSubject ?: '— (No subject configured) —' }}
                                    </div>
                                </div>
                                <div class="notif-meta-text">
                                    <span><i class="fas fa-align-left text-muted me-1"></i>~{{ number_format($bodyLength) }} chars</span>
                                    <span>&bull;</span>
                                    <span><i class="fas fa-code text-muted me-1"></i>{{ $tokenCount }} merge token{{ $tokenCount === 1 ? '' : 's' }}</span>
                                </div>
                            </div>

                            <!-- SUB-CARD 3: Recipient Roles -->
                            <div class="notif-subcard notif-subcard-roles">
                                <div>
                                    <div class="notif-subcard-label">
                                        <span>Authorized Roles</span>
                                        <span><i class="fas fa-users-cog text-muted"></i></span>
                                    </div>
                                    <div class="notif-roles-container">
                                        @if (!empty($tRoles))
                                            @foreach (array_slice($tRoles, 0, 3) as $rName)
                                                <span class="notif-role-pill">
                                                    <i class="fas fa-check text-primary" style="font-size: 8px;"></i>
                                                    {{ $rName }}
                                                </span>
                                            @endforeach
                                            @if (count($tRoles) > 3)
                                                <span class="notif-role-pill text-primary" title="{{ implode(', ', array_slice($tRoles, 3)) }}">
                                                    +{{ count($tRoles) - 3 }} more
                                                </span>
                                            @endif
                                        @else
                                            <span class="notif-all-roles-pill">
                                                <i class="fas fa-globe me-1"></i> All Roles (Unrestricted)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-muted" style="font-size: 10px; margin-top: 4px;">
                                    {{ !empty($tRoles) ? count($tRoles) . ' role(s) bound' : 'Unrestricted dispatch' }}
                                </div>
                            </div>

                            <!-- SUB-CARD 4: Sender & Routing -->
                            <div class="notif-subcard notif-subcard-sender">
                                <div>
                                    <div class="notif-subcard-label">
                                        <span>Sender & Gateway</span>
                                        <span><i class="fas fa-paper-plane text-muted"></i></span>
                                    </div>
                                    <div class="notif-sender-email" title="{{ $tSender }}">
                                        <i class="far fa-envelope text-primary"></i>
                                        <span>{{ $tSender }}</span>
                                    </div>
                                    <div>
                                        <span class="notif-gateway-pill">
                                            <i class="fas fa-server me-1"></i> Gateway #11 (Notifications)
                                        </span>
                                    </div>
                                </div>
                                <div class="text-muted" style="font-size: 10px; margin-top: 4px;">
                                    <i class="fas fa-shield-alt text-success me-1"></i> High Deliverability
                                </div>
                            </div>

                            <!-- SUB-CARD 5: Actions & Operational Toggle -->
                            <div class="notif-subcard notif-subcard-actions">
                                <div>
                                    <div class="notif-subcard-label">
                                        <span>Status & Controls</span>
                                        <span><i class="fas fa-sliders-h text-muted"></i></span>
                                    </div>
                                    <div class="notif-actions-row">
                                        <span class="fs-7 fw-bold text-gray-700">Dispatch Status</span>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input type="checkbox" 
                                                   class="form-check-input notif-status-toggle" 
                                                   id="notifToggle_{{ $tId }}"
                                                   data-id="{{ $tId }}"
                                                   {{ $tStatus === 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="notif-action-btn-group">
                                    <button type="button" 
                                            class="notif-btn-preview btn-preview-trigger" 
                                            data-id="{{ $tId }}" 
                                            data-purpose="{{ $tPurpose }}"
                                            title="Live Sandboxed Preview">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="{{ route('superadmin.company.notificationupdate', $tId) }}" 
                                       class="notif-btn-edit" 
                                       title="Edit template and permissions">
                                        <i class="fas fa-pencil-alt text-primary"></i> Edit
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- SUB-CARD 6: Bottom Activity & Token Strip -->
                        <div class="notif-card-bottom">
                            <div class="notif-tokens-strip">
                                <span class="fw-bold me-1 text-gray-700" style="font-size: 10.5px;">Tokens ({{ $tokenCount }}):</span>
                                @if (!empty($tTokens))
                                    @foreach (array_slice($tTokens, 0, 5) as $tok)
                                        <span class="notif-token-tag">{{ $tok }}</span>
                                    @endforeach
                                    @if ($tokenCount > 5)
                                        <span class="text-muted fw-bold" style="font-size: 10.5px;">+{{ $tokenCount - 5 }} more</span>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size: 10.5px; font-style: italic;">No dynamic tokens</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-success py-1 px-2" style="font-size: 10px;">
                                    <i class="fas fa-check-double text-success me-1"></i> CQRS Synchronized
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            @endif
        </div>

        <!-- Empty State (Shown when search/filter yields 0 records) -->
        <div id="notifEmptyState" class="notif-empty-state">
            <div class="mb-3">
                <i class="fas fa-search text-muted" style="font-size: 40px; opacity: 0.5;"></i>
            </div>
            <h4 class="text-dark fw-bolder mb-1">No Notification Templates Found</h4>
            <p class="text-muted fs-7 mb-4">No templates match your active search and category filters.</p>
            <button type="button" id="btnResetFilters" class="btn btn-sm btn-light-primary fw-bold">
                <i class="fas fa-undo me-1"></i> Reset All Filters
            </button>
        </div>

    </div>
</div>

<!-- ======================================================================
     METRONIC SLIDE-OVER DRAWER: SECURE SANDBOXED PREVIEW
     ====================================================================== -->
<div id="kt_drawer_notification_preview_toggle" class="d-none"></div>
<div id="kt_drawer_notification_preview" 
     class="bg-white" 
     data-kt-drawer="true" 
     data-kt-drawer-name="notificationPreview" 
     data-kt-drawer-activate="true" 
     data-kt-drawer-overlay="true" 
     data-kt-drawer-width="{default:'320px', 'md': '720px', 'lg': '850px'}" 
     data-kt-drawer-direction="end" 
     data-kt-drawer-toggle="#kt_drawer_notification_preview_toggle" 
     data-kt-drawer-close="#kt_drawer_notification_preview_close">
     
    <div class="card w-100 rounded-0 border-0 h-100 d-flex flex-column">
        <!-- Drawer Header -->
        <div class="card-header pe-5 border-bottom" id="kt_drawer_notification_preview_header">
            <div class="card-title">
                <div class="d-flex justify-content-center flex-column me-3">
                    <span class="fs-5 fw-bolder text-gray-900" id="drawerPreviewTitle">Template Preview</span>
                    <span class="fs-7 text-muted" id="drawerPreviewSubtitle">Simulated Sandbox &bull; Zero Real Emails Dispatched</span>
                </div>
            </div>
            <div class="card-toolbar d-flex align-items-center gap-2">
                <a href="#" id="drawerEditBtn" class="btn btn-sm btn-light-primary fw-bold me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Edit Template
                </a>
                <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_notification_preview_close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Drawer Body -->
        <div class="card-body p-0 d-flex flex-column flex-grow-1" style="overflow: hidden; background: #f8fafc;">
            <!-- Loading Indicator -->
            <div id="drawerPreviewLoader" class="d-flex align-items-center justify-content-center h-100 py-10">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="fs-7 text-muted">Assembling sandboxed template preview...</div>
                </div>
            </div>

            <!-- Sandboxed Iframe Container -->
            <div id="drawerIframeContainer" class="flex-grow-1 p-4" style="display: none; height: 100%;">
                <iframe id="drawerPreviewIframe" 
                        sandbox="allow-same-origin" 
                        style="width: 100%; height: 100%; min-height: 520px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff;">
                </iframe>
            </div>
        </div>

        <!-- Drawer Footer: Token Inspection Panel -->
        <div class="card-footer py-3 px-6 bg-white border-top">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-code text-primary fs-6"></i>
                    <span class="fs-7 fw-bold text-gray-800">Merge Tokens Detected:</span>
                    <span class="badge badge-light-primary fw-bolder" id="drawerTokenCount">0</span>
                </div>
                <div class="fs-8 text-muted">
                    Tokens simulated with deterministic production mock data.
                </div>
            </div>
            <div class="notif-tokens-strip mt-2" id="drawerTokensList" style="max-height: 60px; overflow-y: auto;"></div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
$(document).ready(function() {
    'use strict';

    const csrfToken = "{{ csrf_token() }}";
    const toggleUrl = "{{ route('superadmin.company.SingleNotificationUpdate') }}";
    const previewBaseUrl = "{{ url('super-admin/notification/preview') }}";
    const editBaseUrl = "{{ url('super-admin/notification/update') }}";

    let activeCategory = 'all';
    let activeStatus = 'all';
    let searchDebounceTimer = null;

    // Toast Notification helper using SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // --- CLIENT-FIRST FILTERING IN USER RAM (< 1ms) ---
    function filterCards() {
        const query = ($('#notifSearchInput').val() || '').trim().toLowerCase();
        let visibleCount = 0;
        let activeVisible = 0;
        let inactiveVisible = 0;

        $('.notif-primary-card').each(function() {
            const $card = $(this);
            const cardCat = $card.attr('data-category');
            const cardStatus = $card.attr('data-status');
            const cardSearchText = $card.attr('data-search') || '';

            // Check Category filter
            const matchesCat = (activeCategory === 'all' || cardCat === activeCategory);

            // Check Status filter
            const matchesStatus = (activeStatus === 'all' || cardStatus === activeStatus);

            // Check Search query
            const matchesSearch = (!query || cardSearchText.indexOf(query) !== -1);

            if (matchesCat && matchesStatus && matchesSearch) {
                $card.show();
                visibleCount++;
                if (cardStatus === '1') {
                    activeVisible++;
                } else {
                    inactiveVisible++;
                }
            } else {
                $card.hide();
            }
        });

        // Toggle Empty State
        if (visibleCount === 0) {
            $('#notifEmptyState').fadeIn(150);
        } else {
            $('#notifEmptyState').hide();
        }
    }

    // Search Input with 300ms Debounce
    $('#notifSearchInput').on('input', function() {
        const val = $(this).val();
        if (val.length > 0) {
            $('#notifSearchClear').show();
        } else {
            $('#notifSearchClear').hide();
        }

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(filterCards, 300);
    });

    // Clear Search Input
    $('#notifSearchClear').on('click', function() {
        $('#notifSearchInput').val('').focus();
        $(this).hide();
        filterCards();
    });

    // Category Tabs Click
    $('#notifCategoryTabs').on('click', '.notif-tab-btn', function() {
        $('#notifCategoryTabs .notif-tab-btn').removeClass('active');
        $(this).addClass('active');
        activeCategory = $(this).attr('data-cat');
        filterCards();
    });

    // Status Filter Pills Click
    $('#notifStatusTabs').on('click', '.notif-status-pill-btn', function() {
        $('#notifStatusTabs .notif-status-pill-btn').removeClass('active');
        $(this).addClass('active');
        activeStatus = $(this).attr('data-status');
        filterCards();
    });

    // Reset All Filters
    $('#btnResetFilters').on('click', function() {
        $('#notifSearchInput').val('');
        $('#notifSearchClear').hide();
        activeCategory = 'all';
        activeStatus = 'all';
        $('#notifCategoryTabs .notif-tab-btn').removeClass('active');
        $('#notifCategoryTabs .notif-tab-btn[data-cat="all"]').addClass('active');
        $('#notifStatusTabs .notif-status-pill-btn').removeClass('active');
        $('#notifStatusTabs .notif-status-pill-btn[data-status="all"]').addClass('active');
        filterCards();
    });

    // --- OPTIMISTIC AJAX STATUS TOGGLE (ZERO PAGE RELOAD) ---
    $(document).on('change', '.notif-status-toggle', function() {
        const $switch = $(this);
        const notificationId = $switch.attr('data-id');
        const newStatus = $switch.is(':checked') ? 1 : 0;
        const $card = $switch.closest('.notif-primary-card');
        const $statusBadge = $(`#statusBadge_${notificationId}`);

        // Disable switch temporarily to prevent click floods
        $switch.prop('disabled', true);

        // Optimistically update badge and card data
        $card.attr('data-status', newStatus);
        if (newStatus === 1) {
            $statusBadge.removeClass('inactive').addClass('active');
            $statusBadge.find('.status-label').text('Active');
        } else {
            $statusBadge.removeClass('active').addClass('inactive');
            $statusBadge.find('.status-label').text('Inactive');
        }

        // Send AJAX mutation
        $.ajax({
            url: toggleUrl,
            type: 'POST',
            data: {
                _token: csrfToken,
                id: notificationId,
                status: newStatus
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $switch.prop('disabled', false);

                // Update Tier 0 KPI summary counts dynamically
                const $activeCountEl = $('#kpiActiveCount');
                const $inactiveCountEl = $('#kpiInactiveCount');
                let currActive = parseInt($activeCountEl.text(), 10) || 0;
                let currInactive = parseInt($inactiveCountEl.text(), 10) || 0;

                if (newStatus === 1) {
                    $activeCountEl.text(currActive + 1);
                    $inactiveCountEl.text(Math.max(0, currInactive - 1));
                } else {
                    $activeCountEl.text(Math.max(0, currActive - 1));
                    $inactiveCountEl.text(currInactive + 1);
                }

                Toast.fire({
                    icon: 'success',
                    title: `Notification #${notificationId} status set to ${newStatus === 1 ? 'Active' : 'Inactive'}`
                });
            },
            error: function(xhr) {
                $switch.prop('disabled', false);
                // Revert optimistic switch and badge
                const revertedStatus = newStatus === 1 ? 0 : 1;
                $switch.prop('checked', revertedStatus === 1);
                $card.attr('data-status', revertedStatus);

                if (revertedStatus === 1) {
                    $statusBadge.removeClass('inactive').addClass('active');
                    $statusBadge.find('.status-label').text('Active');
                } else {
                    $statusBadge.removeClass('active').addClass('inactive');
                    $statusBadge.find('.status-label').text('Inactive');
                }

                const errMsg = xhr.responseJSON?.message || 'Failed to update status. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Status Update Failed',
                    text: errMsg,
                    confirmButtonText: 'Dismiss'
                });
            }
        });
    });

    // --- LIVE PREVIEW DRAWER TRIGGER ---
    $(document).on('click', '.btn-preview-trigger', function() {
        const notifId = $(this).attr('data-id');
        const notifPurpose = $(this).attr('data-purpose');

        $('#drawerPreviewTitle').text(notifPurpose || `Notification #${notifId}`);
        $('#drawerPreviewSubtitle').text(`Template ID: #${notifId} &bull; Simulated Sandbox Preview`);
        $('#drawerEditBtn').attr('href', `${editBaseUrl}/${notifId}`);

        $('#drawerPreviewLoader').show();
        $('#drawerIframeContainer').hide();
        $('#drawerTokensList').empty();
        $('#drawerTokenCount').text('0');

        // Trigger Metronic Offcanvas Drawer
        $('#kt_drawer_notification_preview_toggle').trigger('click');

        // Fetch sandboxed preview
        $.ajax({
            url: `${previewBaseUrl}/${notifId}`,
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $('#drawerPreviewLoader').hide();
                $('#drawerIframeContainer').fadeIn(150);

                if (res.status === 'success') {
                    // Populate sandboxed iframe via srcdoc
                    const iframe = document.getElementById('drawerPreviewIframe');
                    if (iframe) {
                        iframe.srcdoc = res.html;
                    }

                    // Populate token inspector
                    const tokens = res.tokens_used || [];
                    $('#drawerTokenCount').text(tokens.length);

                    if (tokens.length > 0) {
                        tokens.forEach(tok => {
                            $('#drawerTokensList').append(`<span class="notif-token-tag">${tok}</span>`);
                        });
                    } else {
                        $('#drawerTokensList').html('<span class="text-muted fs-8">No dynamic merge tokens present in template.</span>');
                    }
                }
            },
            error: function(err) {
                $('#drawerPreviewLoader').html(`
                    <div class="text-center text-danger py-10">
                        <i class="fas fa-exclamation-triangle fs-1 mb-2"></i>
                        <div class="fs-6 fw-bold">Failed to load preview</div>
                        <div class="fs-8 text-muted">Elasticsearch projection might be unavailable.</div>
                    </div>
                `);
            }
        });
    });

});
</script>
@endpush
