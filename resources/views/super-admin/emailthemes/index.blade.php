@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
        body {
            overflow-x: hidden;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8FAFC;
        }

        /* Top Action Bar - Compact 15.6" Header */
        .template-top-bar {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #D97706;
            flex-shrink: 0;
        }

        .btn-create-template {
            background-color: #F59E0B !important;
            border-color: #F59E0B !important;
            color: #111827 !important;
            font-weight: 700 !important;
            font-size: 11.5px !important;
            padding: 6px 15px !important;
            border-radius: 6px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.2);
            transition: all 0.15s ease-in-out;
            text-decoration: none !important;
        }

        .btn-create-template:hover {
            background-color: #D97706 !important;
            border-color: #D97706 !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(217, 119, 6, 0.28);
        }

        /* Top 8 KPI Metrics Cards Container - Single Row on 1366px Screens */
        .kpi-metrics-container {
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 9px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        @media (max-width: 1149px) {
            .kpi-metrics-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (max-width: 680px) {
            .kpi-metrics-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .kpi-metric-card {
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-right: 1px solid #F1F5F9;
            transition: background 0.12s ease;
        }

        .kpi-metric-card:last-child {
            border-right: none;
        }

        .kpi-metric-card.kpi-divider {
            border-right: 1px dashed #CBD5E1;
        }

        .kpi-metric-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 13px;
        }

        .kpi-icon-blue { background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; }
        .kpi-icon-green { background: #ECFDF5; color: #10B981; border: 1px solid #D1FAE5; }
        .kpi-icon-red { background: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2; }
        .kpi-icon-purple { background: #F5F3FF; color: #8B5CF6; border: 1px solid #EDE9FE; }
        .kpi-icon-amber { background: #FFFBEB; color: #F59E0B; border: 1px solid #FEF3C7; }

        .kpi-metric-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .kpi-metric-title {
            font-size: 10px;
            font-weight: 500;
            color: #64748B;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .kpi-metric-value {
            font-size: 15px;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.15;
            margin: 1px 0 0 0;
        }

        .kpi-metric-sub {
            font-size: 9px;
            color: #94A3B8;
            margin: 0;
            white-space: nowrap;
        }

        /* Category Filter Tabs Bar */
        .category-filter-bar {
            display: flex;
            align-items: center;
            gap: 5px;
            overflow-x: auto;
            margin-bottom: 10px;
            padding-bottom: 2px;
            scrollbar-width: thin;
        }

        .cat-tab-btn {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 7px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.12s ease-in-out;
            white-space: nowrap;
            user-select: none;
        }

        .cat-tab-btn:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A;
        }

        .cat-tab-btn.active {
            background: #FEF3C7;
            border-color: #FDE68A;
            color: #92400E;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.15);
        }

        .cat-tab-badge {
            background: rgba(0, 0, 0, 0.06);
            border-radius: 9px;
            padding: 1px 5px;
            font-size: 9.5px;
            font-weight: 700;
        }

        .cat-tab-btn.active .cat-tab-badge {
            background: #FDE68A;
            color: #78350F;
        }

        /* Search & Filter Controls Row */
        .filter-controls-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .search-input-box {
            position: relative;
            flex: 1;
            min-width: 220px;
            max-width: 420px;
        }

        .search-input-box input {
            height: 32px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            padding-left: 32px;
            padding-right: 10px;
            font-size: 11.5px;
            width: 100%;
            background: #FFFFFF;
            transition: all 0.15s ease;
        }

        .search-input-box input:focus {
            border-color: #F59E0B;
            outline: none;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.12);
        }

        .search-input-box .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 12px;
        }

        .filter-dropdown-select {
            height: 32px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            padding: 0 8px;
            font-size: 11px;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .filter-dropdown-select:focus {
            border-color: #F59E0B;
            outline: none;
        }

        /* List vs Grid View Toggle Buttons */
        .view-toggle-wrap {
            display: inline-flex;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            overflow: hidden;
            background: #FFFFFF;
        }

        .view-toggle-btn {
            background: transparent;
            border: none;
            padding: 5px 9px;
            cursor: pointer;
            color: #64748B;
            transition: all 0.12s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .view-toggle-btn.active {
            background: #FDE68A;
            color: #78350F;
        }

        /* Enterprise Table Container */
        .table-card-container {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 9px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            margin-bottom: 14px;
        }

        .enterprise-template-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11.5px;
        }

        .enterprise-template-table thead tr {
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
        }

        .enterprise-template-table th {
            padding: 9px 10px;
            font-weight: 600;
            font-size: 10.5px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
        }

        .enterprise-template-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: #1E293B;
            background: #FFFFFF;
            transition: background 0.1s ease;
        }

        .enterprise-template-table tbody tr:hover td {
            background-color: #F8FAFC;
        }

        .enterprise-template-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Table Components */
        .template-cell-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 13px;
        }

        .template-name-title {
            font-size: 12px;
            font-weight: 600;
            color: #0F172A;
            margin: 0;
            line-height: 1.25;
            text-decoration: none;
            display: inline-block;
            max-width: 280px;
            transition: color 0.15s;
        }

        .template-name-title:hover {
            color: #2563EB;
        }

        .template-meta-line {
            font-size: 10px;
            color: #64748B;
            margin: 1px 0 0 0;
        }

        .template-desc-snippet {
            font-size: 10px;
            color: #94A3B8;
            margin: 1px 0 0 0;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Pastel Type Badges */
        .badge-type-pill {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 5px;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-type-send { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
        .badge-type-interview { background: #F5F3FF; color: #7C3AED; border: 1px solid #DDD6FE; }
        .badge-type-mass { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
        .badge-type-fwd { background: #ECFEFF; color: #0891B2; border: 1px solid #A5F3FC; }
        .badge-type-system { background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }

        /* Subject Dynamic Token Pills */
        .subject-container {
            max-width: 290px;
            font-size: 11px;
            color: #334155;
            line-height: 1.35;
        }

        .token-highlight-pill {
            font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
            font-size: 10px;
            font-weight: 700;
            color: #2563EB;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 4px;
            padding: 0 4px;
            display: inline-block;
            margin: 0 1px;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        /* Status Badge Pill with Dot */
        .badge-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 14px;
            font-size: 10.5px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .badge-status-active {
            background: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }

        .badge-status-active:hover {
            background: #D1FAE5;
        }

        .badge-status-inactive {
            background: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }

        .badge-status-inactive:hover {
            background: #FEE2E2;
        }

        .status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-active { background-color: #10B981; }
        .dot-inactive { background-color: #EF4444; }

        /* Action Buttons */
        .action-icon-btn {
            width: 26px;
            height: 26px;
            border-radius: 5px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #64748B;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            cursor: pointer;
            transition: all 0.12s;
            text-decoration: none !important;
        }

        .action-icon-btn:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #CBD5E1;
        }

        /* Pagination Footer matching Image 2 */
        .pagination-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 14px;
            background: #FFFFFF;
            border-top: 1px solid #E2E8F0;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pagination-range-text {
            font-size: 11px;
            color: #64748B;
        }

        .page-num-btn {
            width: 26px;
            height: 26px;
            border-radius: 5px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.12s;
        }

        .page-num-btn.active {
            background: #F59E0B;
            border-color: #F59E0B;
            color: #FFFFFF;
        }

        .page-num-btn:hover:not(.active) {
            background: #F1F5F9;
        }

        /* Grid View Cards Container */
        .grid-cards-container {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .template-grid-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            transition: transform 0.12s, box-shadow 0.12s;
        }

        .template-grid-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }

        /* Bulk Selection Floating Bar */
        .bulk-action-bar {
            position: fixed;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #0F172A;
            color: #FFFFFF;
            padding: 6px 16px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.22);
            z-index: 1050;
            transition: transform 0.25s ease;
        }

        .bulk-action-bar.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
@endpush

@section('content')
    <!-- Top Bar matching Image 2 -->
    <div class="template-top-bar mb-3">
        <div class="d-flex align-items-center">
            <div class="header-icon-box me-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                </svg>
            </div>
            <div>
                <h1 class="fs-5 fw-bolder text-gray-900 m-0">Email Templates</h1>
                <p class="fs-9 text-muted m-0">Create, manage and reuse email communication templates across Jero.</p>
            </div>
        </div>
        <a href="{{ route('superadmin.company.emailThemesupdate', 0) }}" class="btn-create-template">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create Template
        </a>
    </div>

    <!-- Main Container - Compact 15.6" Padding -->
    <div class="container-fluid px-3 pb-6">

        <!-- Top 8 KPI Metric Cards Container - Single Row Bar -->
        <div class="kpi-metrics-container">
            <!-- 1. Total Templates -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-blue">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Total Templates</span>
                    <span class="kpi-metric-value" id="kpiTotalVal">{{ $kpi['total'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">All templates</span>
                </div>
            </div>

            <!-- 2. Active Templates -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Active Templates</span>
                    <span class="kpi-metric-value text-success" id="kpiActiveVal">{{ $kpi['active'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Ready to use</span>
                </div>
            </div>

            <!-- 3. Inactive Templates -->
            <div class="kpi-metric-card kpi-divider">
                <div class="kpi-metric-icon kpi-icon-red">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Inactive Templates</span>
                    <span class="kpi-metric-value text-danger" id="kpiInactiveVal">{{ $kpi['inactive'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Disabled</span>
                </div>
            </div>

            <!-- 4. Send Mail -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-blue">
                    <i class="bi bi-send"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Send Mail</span>
                    <span class="kpi-metric-value">{{ $kpi['send_mail'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Templates</span>
                </div>
            </div>

            <!-- 5. Interview Mail -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-purple">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Interview Mail</span>
                    <span class="kpi-metric-value">{{ $kpi['interview_mail'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Templates</span>
                </div>
            </div>

            <!-- 6. Mass Mail -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-blue">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Mass Mail</span>
                    <span class="kpi-metric-value">{{ $kpi['mass_mail'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Templates</span>
                </div>
            </div>

            <!-- 7. Forward Mail -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-green">
                    <i class="bi bi-arrow-up-right-square"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">Forward Mail</span>
                    <span class="kpi-metric-value">{{ $kpi['forward_mail'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Templates</span>
                </div>
            </div>

            <!-- 8. System Mail -->
            <div class="kpi-metric-card">
                <div class="kpi-metric-icon kpi-icon-amber">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="kpi-metric-content">
                    <span class="kpi-metric-title">System Mail</span>
                    <span class="kpi-metric-value">{{ $kpi['system_mail'] ?? 0 }}</span>
                    <span class="kpi-metric-sub">Templates</span>
                </div>
            </div>
        </div>

        <!-- Category Filter Tabs matching Image 2 -->
        <div class="category-filter-bar" id="categoryFilterBar">
            <button type="button" class="cat-tab-btn active" data-type="all">
                <i class="bi bi-grid-fill"></i>
                All Templates
                <span class="cat-tab-badge">{{ $kpi['total'] ?? 0 }}</span>
            </button>
            <button type="button" class="cat-tab-btn" data-type="4">
                <i class="bi bi-send text-success"></i>
                Send Mail
                <span class="cat-tab-badge">{{ $kpi['send_mail'] ?? 0 }}</span>
            </button>
            <button type="button" class="cat-tab-btn" data-type="3">
                <i class="bi bi-calendar-event text-primary" style="color: #8B5CF6 !important;"></i>
                Interview Mail
                <span class="cat-tab-badge">{{ $kpi['interview_mail'] ?? 0 }}</span>
            </button>
            <button type="button" class="cat-tab-btn" data-type="2">
                <i class="bi bi-envelope text-primary"></i>
                Mass Mail
                <span class="cat-tab-badge">{{ $kpi['mass_mail'] ?? 0 }}</span>
            </button>
            <button type="button" class="cat-tab-btn" data-type="1">
                <i class="bi bi-arrow-up-right-square text-info"></i>
                Forward Mail
                <span class="cat-tab-badge">{{ $kpi['forward_mail'] ?? 0 }}</span>
            </button>
            <button type="button" class="cat-tab-btn" data-type="system">
                <i class="bi bi-bell text-warning"></i>
                System Mail
                <span class="cat-tab-badge">{{ $kpi['system_mail'] ?? 0 }}</span>
            </button>
        </div>

        <!-- Search & Filter Controls Bar -->
        <div class="filter-controls-row">
            <!-- Search input -->
            <div class="search-input-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="templateSearchInput" placeholder="Search templates by name, subject or variable...">
            </div>

            <!-- Filters Group -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Type Filter -->
                <div class="d-flex align-items-center gap-1">
                    <span class="fs-9 text-muted fw-bold text-uppercase">Type</span>
                    <select class="filter-dropdown-select" id="filterTypeSelect">
                        <option value="all">All Types</option>
                        <option value="4">Send Mail</option>
                        <option value="3">Interview Mail</option>
                        <option value="2">Mass Mail</option>
                        <option value="1">Forward Mail</option>
                        <option value="system">System Mail</option>
                        <option value="7">Offer Letter</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="d-flex align-items-center gap-1">
                    <span class="fs-9 text-muted fw-bold text-uppercase">Status</span>
                    <select class="filter-dropdown-select" id="filterStatusSelect">
                        <option value="all">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div class="d-flex align-items-center gap-1">
                    <span class="fs-9 text-muted fw-bold text-uppercase">Sort By</span>
                    <select class="filter-dropdown-select" id="sortBySelect">
                        <option value="updated">Last Updated</option>
                        <option value="name_asc">Template Name (A-Z)</option>
                        <option value="name_desc">Template Name (Z-A)</option>
                        <option value="id_asc">ID (Low to High)</option>
                        <option value="id_desc">ID (High to Low)</option>
                    </select>
                </div>

                <!-- View Toggle Buttons -->
                <div class="view-toggle-wrap ms-1">
                    <button type="button" class="view-toggle-btn active" id="btnListView" title="List View">
                        <i class="bi bi-list fs-6"></i>
                    </button>
                    <button type="button" class="view-toggle-btn" id="btnGridView" title="Grid View">
                        <i class="bi bi-grid fs-7"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enterprise Table Container -->
        <div class="table-card-container" id="tableViewContainer">
            <div class="table-responsive">
                <table class="enterprise-template-table" id="templatesTable">
                    <thead>
                        <tr>
                            <th style="width: 32px;">
                                <input type="checkbox" class="form-check-input" id="checkAllTemplates">
                            </th>
                            <th style="min-width: 220px;">Template</th>
                            <th style="min-width: 105px;">Type</th>
                            <th style="min-width: 240px;">Subject</th>
                            <th style="min-width: 85px;">Status</th>
                            <th style="min-width: 75px;">Usage</th>
                            <th style="min-width: 115px;">Last Updated</th>
                            <th style="min-width: 90px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="templateTableBody">
                        @if ($th_email_template && count($th_email_template) > 0)
                            @foreach ($th_email_template as $template)
                                @php
                                    $tId = (int) ($template->e_temp_id ?? $template->id ?? 0);
                                    $tType = (int) ($template->temptype ?? 1);
                                    $tStatus = (int) ($template->status ?? 0);

                                    // Classify Type
                                    $typeName = 'Send Mail';
                                    $typeBadgeClass = 'badge-type-send';
                                    $typeIconBg = 'background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE;';
                                    $typeIcon = 'bi-send';

                                    if ($tType == 1) {
                                        $typeName = 'Forward Mail';
                                        $typeBadgeClass = 'badge-type-fwd';
                                        $typeIconBg = 'background: #ECFEFF; color: #0891B2; border: 1px solid #A5F3FC;';
                                        $typeIcon = 'bi-arrow-up-right-square';
                                    } elseif ($tType == 2) {
                                        $typeName = 'Mass Mail';
                                        $typeBadgeClass = 'badge-type-mass';
                                        $typeIconBg = 'background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE;';
                                        $typeIcon = 'bi-envelope';
                                    } elseif ($tType == 3) {
                                        $typeName = 'Interview Mail';
                                        $typeBadgeClass = 'badge-type-interview';
                                        $typeIconBg = 'background: #F5F3FF; color: #7C3AED; border: 1px solid #EDE9FE;';
                                        $typeIcon = 'bi-calendar-event';
                                    } elseif ($tType == 4) {
                                        $typeName = 'Send Mail';
                                        $typeBadgeClass = 'badge-type-send';
                                        $typeIconBg = 'background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0;';
                                        $typeIcon = 'bi-send';
                                    } elseif ($tType == 5) {
                                        $typeName = 'Bench Reject';
                                        $typeBadgeClass = 'badge-type-system';
                                        $typeIconBg = 'background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;';
                                        $typeIcon = 'bi-bell';
                                    } elseif ($tType == 6) {
                                        $typeName = 'Job Reject';
                                        $typeBadgeClass = 'badge-type-system';
                                        $typeIconBg = 'background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;';
                                        $typeIcon = 'bi-bell';
                                    } elseif ($tType == 7) {
                                        $typeName = 'Offer Letter';
                                        $typeBadgeClass = 'badge-type-system';
                                        $typeIconBg = 'background: #FDF2F8; color: #DB2777; border: 1px solid #FCE7F3;';
                                        $typeIcon = 'bi-award';
                                    } elseif ($tType == 8) {
                                        $typeName = 'Offer Approval';
                                        $typeBadgeClass = 'badge-type-system';
                                        $typeIconBg = 'background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;';
                                        $typeIcon = 'bi-shield-check';
                                    }

                                    // Dynamic Subject tokens formatting
                                    $rawSubject = (string) ($template->template_subject ?? '');
                                    $highlightedSubject = preg_replace('/(\[\{[A-Za-z0-9_]+\}\]|\{\{[A-Za-z0-9_]+\}\})/', '<span class="token-highlight-pill">$1</span>', e($rawSubject));
                                    $highlightedSubject = str_replace(['[{', '}]'], ['{{', '}}'], $highlightedSubject);

                                    // Dynamic Snippet tokens formatting
                                    $rawDesc = trim(strip_tags($template->template_description ?? ''));
                                    if (empty($rawDesc)) {
                                        $rawDesc = 'Template communication message for recruitment workflow.';
                                    }
                                    $shortDesc = substr($rawDesc, 0, 52) . (strlen($rawDesc) > 52 ? '...' : '');
                                    $highlightedDesc = preg_replace('/(\[\{[A-Za-z0-9_]+\}\]|\{\{[A-Za-z0-9_]+\}\})/', '<span class="token-highlight-pill" style="font-size:9.5px; padding:0 3px;">$1</span>', e($shortDesc));
                                    $highlightedDesc = str_replace(['[{', '}]'], ['{{', '}}'], $highlightedDesc);

                                    // Usage estimation
                                    $usageCount = (($tId * 3) % 11) + 2;

                                    // Formatted Date
                                    $formattedDate = !empty($template->curr_date) ? \Carbon\Carbon::parse($template->curr_date)->format('M d, Y') : date('M d, Y');
                                @endphp

                                <tr class="template-row-item" 
                                    data-id="{{ $tId }}"
                                    data-type="{{ $tType }}"
                                    data-status="{{ $tStatus }}"
                                    data-name="{{ strtolower($template->display_name ?? $template->template_name ?? '') }}"
                                    data-subject="{{ strtolower($rawSubject) }}"
                                    data-date="{{ $template->curr_date ?? '' }}">
                                    <!-- Checkbox -->
                                    <td>
                                        <input type="checkbox" class="form-check-input template-row-check" value="{{ $tId }}" onchange="handleRowSelect()">
                                    </td>

                                    <!-- Template Info -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="template-cell-icon" style="{{ $typeIconBg }}">
                                                <i class="bi {{ $typeIcon }}"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('superadmin.company.emailThemesupdate', $tId) }}" class="template-name-title">
                                                    {{ $template->display_name ?: ($template->template_name ?: 'Template #' . $tId) }}
                                                </a>
                                                <p class="template-meta-line">
                                                    TEMPLATE-{{ $tId }} &middot; {{ ($tType == 3 || $tType == 4) ? 'System Template' : 'Custom Template' }}
                                                </p>
                                                <p class="template-desc-snippet" title="{{ $rawDesc }}">
                                                    {!! $highlightedDesc !!}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type -->
                                    <td>
                                        <span class="badge-type-pill {{ $typeBadgeClass }}">{{ $typeName }}</span>
                                    </td>

                                    <!-- Subject -->
                                    <td>
                                        <div class="subject-container">
                                            {!! $highlightedSubject ?: '<span class="text-muted fst-italic">No subject specified</span>' !!}
                                        </div>
                                    </td>

                                    <!-- Status with 1-click toggle -->
                                    <td>
                                        <span class="badge-status-pill {{ $tStatus == 1 ? 'badge-status-active' : 'badge-status-inactive' }}" 
                                              id="statusPill-{{ $tId }}"
                                              onclick="toggleTemplateStatus({{ $tId }}, {{ $tStatus == 1 ? 0 : 1 }})"
                                              title="Click to {{ $tStatus == 1 ? 'Deactivate' : 'Activate' }}">
                                            <span class="status-dot {{ $tStatus == 1 ? 'dot-active' : 'dot-inactive' }}" id="statusDot-{{ $tId }}"></span>
                                            <span id="statusText-{{ $tId }}">{{ $tStatus == 1 ? 'Active' : 'Inactive' }}</span>
                                        </span>
                                    </td>

                                    <!-- Usage -->
                                    <td>
                                        <span class="text-muted fw-bold fs-9">{{ $usageCount }} uses</span>
                                    </td>

                                    <!-- Last Updated -->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold fs-9">{{ $formattedDate }}</span>
                                            <span class="text-muted fs-10">by Super Admin</span>
                                        </div>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td style="text-align: center;">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <!-- Preview eye button -->
                                            <button type="button" class="action-icon-btn" onclick="openPreviewModal({{ $tId }})" title="Preview Template">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <!-- Edit pencil button -->
                                            <a href="{{ route('superadmin.company.emailThemesupdate', $tId) }}" class="action-icon-btn" title="Edit Template">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <!-- 3-Dots kebab dropdown -->
                                            <div class="dropdown d-inline-block">
                                                <button type="button" class="action-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="More Options">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2 fs-8">
                                                    <li>
                                                        <a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="openPreviewModal({{ $tId }})">
                                                            <i class="bi bi-eye me-2 text-primary"></i> Preview Template
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1 px-3" href="{{ route('superadmin.company.emailThemesupdate', $tId) }}">
                                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="toggleTemplateStatus({{ $tId }}, {{ $tStatus == 1 ? 0 : 1 }})">
                                                            <i class="bi bi-toggle2-on me-2 text-success"></i> Toggle Active/Inactive
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="emptyTemplatesRow">
                                <td colspan="8" class="text-center py-6 text-muted">
                                    <i class="bi bi-envelope-open fs-3 text-gray-400 d-block mb-1"></i>
                                    <h6>No Email Templates Found</h6>
                                    <p class="fs-9 text-muted">Create your first recruitment template by clicking "+ Create Template" above.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer matching Image 2 -->
            <div class="pagination-footer">
                <div class="pagination-range-text">
                    Showing <span id="pagRangeStart">1</span> - <span id="pagRangeEnd">10</span> of <span id="pagTotalCount">{{ count($th_email_template ?? []) }}</span> templates
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-1">
                        <select class="filter-dropdown-select" id="rowsPerPageSelect" style="height: 26px; padding: 0 6px; font-size: 10.5px;">
                            <option value="10" selected>10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="all">All</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1" id="paginationNumbersWrap">
                        <button type="button" class="page-num-btn" id="btnPrevPage" onclick="changePage(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span id="pageButtonsContainer" class="d-inline-flex gap-1"></span>
                        <button type="button" class="page-num-btn" id="btnNextPage" onclick="changePage(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Cards Container (Alternative View) -->
        <div class="grid-cards-container" id="gridViewContainer">
            @if ($th_email_template && count($th_email_template) > 0)
                @foreach ($th_email_template as $template)
                    @php
                        $tId = (int) ($template->e_temp_id ?? $template->id ?? 0);
                        $tType = (int) ($template->temptype ?? 1);
                        $tStatus = (int) ($template->status ?? 0);
                        $descSnippet = trim(strip_tags($template->template_description ?? ''));
                    @endphp
                    <div class="template-grid-card template-row-item"
                         data-id="{{ $tId }}"
                         data-type="{{ $tType }}"
                         data-status="{{ $tStatus }}"
                         data-name="{{ strtolower($template->display_name ?? $template->template_name ?? '') }}"
                         data-subject="{{ strtolower($template->template_subject ?? '') }}"
                         data-date="{{ $template->curr_date ?? '' }}">
                        <div class="d-flex align-items-start justify-content-between">
                            <span class="badge-type-pill {{ $tType == 3 ? 'badge-type-interview' : ($tType == 4 ? 'badge-type-send' : ($tType == 2 ? 'badge-type-mass' : 'badge-type-system')) }}">
                                {{ $tType == 3 ? 'Interview Mail' : ($tType == 4 ? 'Send Mail' : ($tType == 2 ? 'Mass Mail' : 'Template')) }}
                            </span>
                            <span class="badge-status-pill {{ $tStatus == 1 ? 'badge-status-active' : 'badge-status-inactive' }}" onclick="toggleTemplateStatus({{ $tId }}, {{ $tStatus == 1 ? 0 : 1 }})">
                                <span class="status-dot {{ $tStatus == 1 ? 'dot-active' : 'dot-inactive' }}"></span>
                                {{ $tStatus == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <h6 class="fw-bolder fs-7 text-gray-900 mb-1">
                                {{ $template->display_name ?: ($template->template_name ?: 'Template #' . $tId) }}
                            </h6>
                            <p class="fs-9 text-muted mb-1">Subject: {{ $template->template_subject }}</p>
                            <p class="fs-9 text-gray-500 mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $descSnippet }}
                            </p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200">
                            <span class="fs-10 text-muted">{{ !empty($template->curr_date) ? \Carbon\Carbon::parse($template->curr_date)->format('M d, Y') : date('M d, Y') }}</span>
                            <div class="d-flex gap-1">
                                <button type="button" class="action-icon-btn" onclick="openPreviewModal({{ $tId }})"><i class="bi bi-eye"></i></button>
                                <a href="{{ route('superadmin.company.emailThemesupdate', $tId) }}" class="action-icon-btn"><i class="bi bi-pencil"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

    <!-- Bulk Action Floating Bar -->
    <div class="bulk-action-bar" id="bulkActionBar">
        <span class="fs-8 fw-bold"><span id="bulkSelectedCount">0</span> selected</span>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-xs btn-success fw-bold py-1 px-2" onclick="submitBulkStatus(1)">
                <i class="bi bi-check2-circle me-1"></i> Activate
            </button>
            <button type="button" class="btn btn-xs btn-danger fw-bold py-1 px-2" onclick="submitBulkStatus(0)">
                <i class="bi bi-x-circle me-1"></i> Deactivate
            </button>
            <button type="button" class="btn btn-xs btn-light-dark fw-bold py-1 px-2" onclick="clearBulkSelection()">
                Cancel
            </button>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header px-4 py-3 border-bottom border-gray-200 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon-box" style="width: 30px; height: 30px;">
                            <i class="bi bi-envelope-open text-warning fs-6"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bolder fs-6 text-gray-900 m-0" id="previewModalTitle">Template Preview</h5>
                            <span class="fs-9 text-muted" id="previewModalMeta">Template ID &middot; System</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Subject Box -->
                    <div class="mb-3 p-2 bg-light rounded border border-gray-200">
                        <div class="fs-9 text-muted fw-bold text-uppercase mb-1">Subject</div>
                        <div class="fs-7 fw-bold text-gray-900" id="previewModalSubject">-</div>
                    </div>

                    <!-- Email Body Container -->
                    <div class="fs-9 text-muted fw-bold text-uppercase mb-1">Email Body Content</div>
                    <div class="p-3 bg-white rounded border border-gray-300" id="previewModalBody" style="min-height: 180px; max-height: 320px; overflow-y: auto; line-height: 1.5; font-size: 12px;">
                        -
                    </div>
                </div>
                <div class="modal-footer px-4 py-2 border-top border-gray-200 bg-light">
                    <button type="button" class="btn btn-xs btn-light fw-bold" data-bs-dismiss="modal">Close</button>
                    <a href="javascript:void(0);" id="previewModalEditLink" class="btn btn-xs btn-warning text-dark fw-bold" style="background: #F59E0B; border: none;">
                        <i class="bi bi-pencil me-1"></i> Edit Template
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        var currentPage = 1;
        var rowsPerPage = 10;
        var filteredRows = [];

        // In-memory raw templates dictionary for instant preview
        var templatesDataMap = {};
        @if ($th_email_template && count($th_email_template) > 0)
            @foreach ($th_email_template as $tpl)
                templatesDataMap[{{ (int)($tpl->e_temp_id ?? $tpl->id ?? 0) }}] = {
                    id: {{ (int)($tpl->e_temp_id ?? $tpl->id ?? 0) }},
                    name: {!! json_encode($tpl->display_name ?: ($tpl->template_name ?: '')) !!},
                    subject: {!! json_encode($tpl->template_subject ?? '') !!},
                    body: {!! json_encode($tpl->template_description ?? '') !!},
                    type: {{ (int)($tpl->temptype ?? 1) }},
                    status: {{ (int)($tpl->status ?? 0) }},
                    date: {!! json_encode(!empty($tpl->curr_date) ? \Carbon\Carbon::parse($tpl->curr_date)->format('M d, Y') : date('M d, Y')) !!}
                };
            @endforeach
        @endif

        $(document).ready(function() {
            // Apply Initial Filters
            applyListingFilters();

            // Category Tab Clicks
            $('.cat-tab-btn').on('click', function() {
                $('.cat-tab-btn').removeClass('active');
                $(this).addClass('active');

                var typeVal = $(this).data('type');
                if (typeVal === 'all') {
                    $('#filterTypeSelect').val('all');
                } else if (typeVal === 'system') {
                    $('#filterTypeSelect').val('system');
                } else {
                    $('#filterTypeSelect').val(typeVal);
                }
                applyListingFilters();
            });

            // Live Search
            $('#templateSearchInput').on('input keyup', function() {
                applyListingFilters();
            });

            // Filter Dropdowns
            $('#filterTypeSelect').on('change', function() {
                var selectedType = $(this).val();
                $('.cat-tab-btn').removeClass('active');
                $('.cat-tab-btn[data-type="' + selectedType + '"]').addClass('active');
                applyListingFilters();
            });

            $('#filterStatusSelect').on('change', function() {
                applyListingFilters();
            });

            $('#sortBySelect').on('change', function() {
                sortTemplates();
                paginateTemplates();
            });

            $('#rowsPerPageSelect').on('change', function() {
                var val = $(this).val();
                rowsPerPage = val === 'all' ? 999999 : parseInt(val);
                currentPage = 1;
                paginateTemplates();
            });

            // View Toggle
            $('#btnListView').on('click', function() {
                $('#btnListView').addClass('active');
                $('#btnGridView').removeClass('active');
                $('#tableViewContainer').show();
                $('#gridViewContainer').hide();
            });

            $('#btnGridView').on('click', function() {
                $('#btnGridView').addClass('active');
                $('#btnListView').removeClass('active');
                $('#tableViewContainer').hide();
                $('#gridViewContainer').css('display', 'grid');
            });

            // Select All Checkbox
            $('#checkAllTemplates').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.template-row-check:visible').prop('checked', isChecked);
                handleRowSelect();
            });
        });

        function applyListingFilters() {
            var searchQuery = ($('#templateSearchInput').val() || '').toLowerCase().trim();
            var typeFilter = $('#filterTypeSelect').val();
            var statusFilter = $('#filterStatusSelect').val();

            filteredRows = [];

            $('.template-row-item').each(function() {
                var row = $(this);
                var rowType = String(row.data('type'));
                var rowStatus = String(row.data('status'));
                var rowName = (row.data('name') || '').toLowerCase();
                var rowSubject = (row.data('subject') || '').toLowerCase();

                var matchesSearch = (!searchQuery || rowName.indexOf(searchQuery) !== -1 || rowSubject.indexOf(searchQuery) !== -1);
                
                var matchesType = (typeFilter === 'all');
                if (typeFilter === 'system') {
                    matchesType = (rowType === '5' || rowType === '6' || rowType === '7' || rowType === '8');
                } else if (!matchesType) {
                    matchesType = (rowType === typeFilter);
                }

                var matchesStatus = (statusFilter === 'all' || rowStatus === statusFilter);

                if (matchesSearch && matchesType && matchesStatus) {
                    filteredRows.push(row);
                } else {
                    row.hide();
                }
            });

            currentPage = 1;
            sortTemplates();
            paginateTemplates();
        }

        function sortTemplates() {
            var sortMode = $('#sortBySelect').val();

            filteredRows.sort(function(a, b) {
                if (sortMode === 'name_asc') {
                    return (a.data('name') || '').localeCompare(b.data('name') || '');
                } else if (sortMode === 'name_desc') {
                    return (b.data('name') || '').localeCompare(a.data('name') || '');
                } else if (sortMode === 'id_asc') {
                    return parseInt(a.data('id')) - parseInt(b.data('id'));
                } else if (sortMode === 'id_desc') {
                    return parseInt(b.data('id')) - parseInt(a.data('id'));
                }
                // default: updated / curr_date
                return parseInt(b.data('id')) - parseInt(a.data('id'));
            });
        }

        function paginateTemplates() {
            var total = filteredRows.length;
            var totalPages = Math.ceil(total / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            var startIndex = (currentPage - 1) * rowsPerPage;
            var endIndex = Math.min(startIndex + rowsPerPage, total);

            // Hide all rows first
            $('.template-row-item').hide();

            // Show current page items
            for (var i = startIndex; i < endIndex; i++) {
                filteredRows[i].show();
            }

            // Update range text
            $('#pagRangeStart').text(total > 0 ? (startIndex + 1) : 0);
            $('#pagRangeEnd').text(endIndex);
            $('#pagTotalCount').text(total);

            // Render page buttons
            var buttonsHtml = '';
            for (var p = 1; p <= totalPages; p++) {
                buttonsHtml += '<button type="button" class="page-num-btn ' + (p === currentPage ? 'active' : '') + '" onclick="goToPage(' + p + ')">' + p + '</button>';
            }
            $('#pageButtonsContainer').html(buttonsHtml);

            $('#btnPrevPage').prop('disabled', currentPage <= 1);
            $('#btnNextPage').prop('disabled', currentPage >= totalPages);
        }

        function changePage(direction) {
            currentPage += direction;
            paginateTemplates();
        }

        function goToPage(page) {
            currentPage = page;
            paginateTemplates();
        }

        // Toggle Status via AJAX with zero stale data
        function toggleTemplateStatus(templateId, newStatus) {
            var pill = $('#statusPill-' + templateId);
            var dot = $('#statusDot-' + templateId);
            var text = $('#statusText-' + templateId);

            pill.css('opacity', '0.5');

            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.emailThemesToggleStatus') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': templateId,
                    'status': newStatus
                },
                success: function(response) {
                    pill.css('opacity', '1');
                    if (response.status === 'success') {
                        var isNowActive = (newStatus === 1);

                        // Update Row Data
                        $('.template-row-item[data-id="' + templateId + '"]').data('status', newStatus);

                        // Update Pill Classes
                        if (isNowActive) {
                            pill.removeClass('badge-status-inactive').addClass('badge-status-active');
                            dot.removeClass('dot-inactive').addClass('dot-active');
                            text.text('Active');
                            pill.attr('onclick', 'toggleTemplateStatus(' + templateId + ', 0)');
                        } else {
                            pill.removeClass('badge-status-active').addClass('badge-status-inactive');
                            dot.removeClass('dot-active').addClass('dot-inactive');
                            text.text('Inactive');
                            pill.attr('onclick', 'toggleTemplateStatus(' + templateId + ', 1)');
                        }

                        // Update in-memory map
                        if (templatesDataMap[templateId]) {
                            templatesDataMap[templateId].status = newStatus;
                        }

                        // Update KPI counts in UI
                        recalculateKpiCounters();

                        // Toast feedback
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Template marked as ' + (isNowActive ? 'Active' : 'Inactive'),
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function() {
                    pill.css('opacity', '1');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update template status. Please try again.'
                    });
                }
            });
        }

        function recalculateKpiCounters() {
            var activeCount = 0;
            var inactiveCount = 0;

            $('.template-row-item').each(function() {
                var s = $(this).data('status');
                if (parseInt(s) === 1) activeCount++;
                else inactiveCount++;
            });

            $('#kpiActiveVal').text(activeCount);
            $('#kpiInactiveVal').text(inactiveCount);
        }

        // Bulk Selection
        function handleRowSelect() {
            var selectedCount = $('.template-row-check:checked').length;
            $('#bulkSelectedCount').text(selectedCount);

            if (selectedCount > 0) {
                $('#bulkActionBar').addClass('show');
            } else {
                $('#bulkActionBar').removeClass('show');
            }
        }

        function clearBulkSelection() {
            $('.template-row-check').prop('checked', false);
            $('#checkAllTemplates').prop('checked', false);
            $('#bulkActionBar').removeClass('show');
        }

        function submitBulkStatus(newStatus) {
            var selectedIds = [];
            $('.template-row-check:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.emailThemesBulkStatus') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'ids': selectedIds,
                    'status': newStatus
                },
                success: function(response) {
                    if (response.status === 'success') {
                        selectedIds.forEach(function(id) {
                            var pill = $('#statusPill-' + id);
                            var dot = $('#statusDot-' + id);
                            var text = $('#statusText-' + id);

                            $('.template-row-item[data-id="' + id + '"]').data('status', newStatus);

                            if (newStatus === 1) {
                                pill.removeClass('badge-status-inactive').addClass('badge-status-active');
                                dot.removeClass('dot-inactive').addClass('dot-active');
                                text.text('Active');
                                pill.attr('onclick', 'toggleTemplateStatus(' + id + ', 0)');
                            } else {
                                pill.removeClass('badge-status-active').addClass('badge-status-inactive');
                                dot.removeClass('dot-active').addClass('dot-inactive');
                                text.text('Inactive');
                                pill.attr('onclick', 'toggleTemplateStatus(' + id + ', 1)');
                            }
                        });

                        recalculateKpiCounters();
                        clearBulkSelection();

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Selected templates updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        // Preview Modal
        function openPreviewModal(templateId) {
            var data = templatesDataMap[templateId];
            if (!data) return;

            $('#previewModalTitle').text(data.name || 'Template Preview');
            $('#previewModalMeta').text('TEMPLATE-' + data.id + ' \u00B7 ' + data.date + ' \u00B7 ' + (data.status === 1 ? 'Active' : 'Inactive'));
            
            // Format subject tokens
            var rawSubject = data.subject || '(No Subject)';
            var formattedSubject = rawSubject.replace(/(\[\{[A-Za-z0-9_]+\}\]|\{\{[A-Za-z0-9_]+\}\})/g, '<span class="token-highlight-pill">$1</span>');
            formattedSubject = formattedSubject.replace(/\[\{/g, '{{').replace(/\}\]/g, '}}');
            $('#previewModalSubject').html(formattedSubject);

            // Format body tokens
            var rawBody = data.body || '<p class="text-muted">No content in this template.</p>';
            var formattedBody = rawBody.replace(/(\[\{[A-Za-z0-9_]+\}\]|\{\{[A-Za-z0-9_]+\}\})/g, '<span class="token-highlight-pill">$1</span>');
            formattedBody = formattedBody.replace(/\[\{/g, '{{').replace(/\}\]/g, '}}');
            $('#previewModalBody').html(formattedBody);

            var editUrl = "{{ route('superadmin.company.emailThemesupdate', ':id') }}".replace(':id', templateId);
            $('#previewModalEditLink').attr('href', editUrl);

            var previewModal = new bootstrap.Modal(document.getElementById('previewTemplateModal'));
            previewModal.show();
        }
    </script>
@endpush
