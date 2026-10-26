@extends('layouts.superadmin_layout')

@php
    $currentStatusLower = strtolower($jd_status ?? 'pending');
    $statusEntityLabel = $currentStatusLower === 'approved' 
        ? 'approved JDs' 
        : (($currentStatusLower === 'decline' || $currentStatusLower === 'declined') ? 'declined JDs' : 'pending JDs');
    $statusTitleLabel = ucfirst($jd_status ?? 'Pending');
    $currentCount = $currentStatusLower === 'approved' 
        ? ($approvedJdCount ?? 0) 
        : (($currentStatusLower === 'decline' || $currentStatusLower === 'declined') ? ($declinedJdCount ?? 0) : ($pendingJdCount ?? 0));
    $kpiIconBoxClass = $currentStatusLower === 'approved' ? 'green' : (($currentStatusLower === 'decline' || $currentStatusLower === 'declined') ? 'rose' : 'amber');
    $kpiIconClass = $currentStatusLower === 'approved' ? 'fa-check-circle-o' : (($currentStatusLower === 'decline' || $currentStatusLower === 'declined') ? 'fa-times-circle-o' : 'fa-file-text-o');
@endphp

@section('header_title')
<div class="jd-header-title d-flex flex-column justify-content-center my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold my-0">{{ $statusTitleLabel }} JD List</h1>
    <span class="header-subtitle">
        @if($currentStatusLower === 'approved')
            Review and manage approved job descriptions in the master catalog.
        @elseif($currentStatusLower === 'decline' || $currentStatusLower === 'declined')
            Review and manage declined job descriptions.
        @else
            Review and manage job descriptions awaiting approval.
        @endif
    </span>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* ==========================================================================
   Super Admin — JD Pending List Isolated Enterprise Styles
   Design Standard: Modern Card + Sub-Card Enterprise Design System
   Screen Target: 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow
   ========================================================================== */

:root {
    --jd-primary: #009ef7;
    --jd-primary-hover: #0088d6;
    --jd-primary-light: #f1faff;
    --jd-text-dark: #0f172a;
    --jd-text-muted: #64748b;
    --jd-border-color: #e2e8f0;
    --jd-card-bg: #ffffff;
    --jd-bg-page: #f8fafc;
}

body {
    overflow-x: hidden;
    background-color: var(--jd-bg-page);
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Top Navigation Header Title Stack & Subtitle */
.jd-header-title h1 {
    font-family: 'Poppins', sans-serif !important;
    font-size: 1.35rem !important;
    font-weight: 600 !important;
    color: #181c32 !important;
    line-height: 1.2 !important;
    margin: 0 !important;
}
.jd-header-title .header-subtitle {
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    color: #7e8299 !important;
    line-height: 1.2 !important;
    margin-top: 3px !important;
}
@media (max-width: 991.98px) {
    .jd-header-title h1 {
        font-size: 1.1rem !important;
    }
    .jd-header-title .header-subtitle {
        display: none !important;
    }
}

/* Top Space & Header Gap Management (Eliminate redundant toolbar whitespace) */
#kt_header_search {
    display: none !important;
}
#kt_header {
    border-bottom: 1px solid #eff2f5 !important;
}
#kt_toolbar {
    display: none !important;
}
@media (min-width: 992px) {
    #jd-pending-page-container {
        margin-top: -48px !important;
    }
}
@media (max-width: 991.98px) {
    #jd-pending-page-container {
        margin-top: -38px !important;
    }
}

/* Top Tier: Summary / KPI Cards */
.jd-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 992px) {
    .jd-summary-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
}
.jd-kpi-card {
    background: var(--jd-card-bg);
    border: 1px solid var(--jd-border-color);
    border-radius: 12px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.jd-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.jd-kpi-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.jd-kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.jd-kpi-icon-box.amber {
    background-color: #FEF3C7;
    color: #D97706;
}
.jd-kpi-icon-box.green {
    background-color: #D1FAE5;
    color: #059669;
}
.jd-kpi-icon-box.blue {
    background-color: #DBEAFE;
    color: #2563EB;
}
.jd-kpi-icon-box.rose {
    background-color: #FFE4E6;
    color: #E11D48;
}
.jd-kpi-content {
    display: flex;
    flex-direction: column;
}
.jd-kpi-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--jd-text-muted);
    margin-bottom: 2px;
}
.jd-kpi-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--jd-text-dark);
    line-height: 1.1;
}
.jd-kpi-menu-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 4px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}
.jd-kpi-menu-btn:hover {
    background-color: #f1f5f9;
    color: var(--jd-text-dark);
}

/* Tier 2: Filter Toolbar Card */
.jd-toolbar-card {
    background: var(--jd-card-bg);
    border: 1px solid var(--jd-border-color);
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.jd-toolbar-left {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    flex: 1;
}
.jd-toolbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}
.jd-search-wrapper {
    position: relative;
    width: 290px;
}
@media (max-width: 768px) {
    .jd-search-wrapper {
        width: 100%;
    }
}
.jd-search-wrapper i.fa-search {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
}
.jd-search-input {
    width: 100%;
    height: 40px;
    padding: 8px 36px 8px 38px;
    font-size: 13px;
    color: var(--jd-text-dark);
    background-color: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 8px;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.jd-search-input:focus {
    border-color: var(--jd-primary);
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.jd-search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}
.jd-search-clear:hover {
    color: var(--jd-text-dark);
}

.jd-filter-select {
    height: 40px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    background-color: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 8px;
    cursor: pointer;
    outline: none;
    transition: all 0.15s ease;
}
.jd-filter-select:hover {
    border-color: #cbd5e1;
}
.jd-filter-select:focus {
    border-color: var(--jd-primary);
}

.jd-sort-select {
    height: 40px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    background-color: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 8px;
    cursor: pointer;
    outline: none;
    transition: all 0.15s ease;
}
.jd-sort-select:hover {
    border-color: #cbd5e1;
}

/* Primary "+ Add New JD" Button */
.jd-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 40px;
    padding: 0 18px;
    background-color: var(--jd-primary);
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.15s ease, transform 0.1s ease;
    white-space: nowrap;
}
.jd-btn-primary:hover {
    background-color: var(--jd-primary-hover);
    color: #ffffff !important;
}

/* Tier 3: Modern Data Table Card */
.jd-table-card {
    background: var(--jd-card-bg);
    border: 1px solid var(--jd-border-color);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.jd-table-container {
    width: 100%;
    overflow-x: auto;
}
.jd-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    text-align: left;
}
.jd-table thead tr {
    background-color: #fafbfd;
    border-bottom: 1px solid var(--jd-border-color);
}
.jd-table th {
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    white-space: nowrap;
}
.jd-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.1s ease;
}
.jd-table tbody tr:hover {
    background-color: #f8fafc;
}
.jd-table tbody tr.selected {
    background-color: #f0f9ff;
}
.jd-table td {
    padding: 16px 18px;
    font-size: 13px;
    color: #334155;
    vertical-align: middle;
}
.jd-table th:first-child,
.jd-table td:first-child {
    padding-left: 24px;
}
.jd-table th:last-child,
.jd-table td:last-child {
    padding-right: 24px;
}

/* Avatar Box */
.jd-title-cell {
    display: flex;
    align-items: center;
    gap: 14px;
}
.jd-avatar-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
    text-transform: uppercase;
}
.jd-avatar-box.theme-blue   { background-color: #E0F2FE; color: #0284C7; }
.jd-avatar-box.theme-amber  { background-color: #FEF3C7; color: #D97706; }
.jd-avatar-box.theme-green  { background-color: #DCFCE7; color: #16A34A; }
.jd-avatar-box.theme-rose   { background-color: #FFE4E6; color: #E11D48; }
.jd-avatar-box.theme-purple { background-color: #EDE9FE; color: #7C3AED; }

.jd-title-text-group {
    display: flex;
    flex-direction: column;
}
.jd-title-link {
    font-size: 14px;
    font-weight: 600;
    color: var(--jd-text-dark);
    text-decoration: none;
    transition: color 0.15s ease;
    cursor: pointer;
}
.jd-title-link:hover {
    color: var(--jd-primary);
}
.jd-title-subtitle {
    font-size: 12px;
    color: var(--jd-text-muted);
    margin-top: 2px;
}

/* Experience Formatting */
.jd-exp-text {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    white-space: nowrap;
}

/* Skills Chips */
.jd-skills-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    max-width: 380px;
}
.jd-skill-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 6px;
    white-space: nowrap;
}
.jd-skill-pill.pill-blue   { background-color: #E0F2FE; color: #0369A1; }
.jd-skill-pill.pill-green  { background-color: #DCFCE7; color: #15803D; }
.jd-skill-pill.pill-purple { background-color: #F3E8FF; color: #7E22CE; }
.jd-skill-pill.pill-pink   { background-color: #FFE4E6; color: #BE123C; }
.jd-skill-pill.pill-amber  { background-color: #FEF3C7; color: #B45309; }
.jd-skill-pill.pill-gray   { background-color: #F1F5F9; color: #475569; }

/* Status Badge Pill */
.jd-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    white-space: nowrap;
}
.jd-status-badge.status-approved {
    background-color: #E8FFF3;
    color: #50CD89;
    border: 1px solid #c7f7dc;
}
.jd-status-badge.status-declined {
    background-color: #FFF5F8;
    color: #F1416C;
    border: 1px solid #fed7e2;
}
.jd-status-badge.status-pending {
    background-color: #FFF8DD;
    color: #D97706;
    border: 1px solid #fae69e;
}

/* Submitted Timestamp */
.jd-submitted-cell {
    display: flex;
    flex-direction: column;
    white-space: nowrap;
}
.jd-sub-date {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
}
.jd-sub-time {
    font-size: 12px;
    color: #94a3b8;
}

/* Action Icon Buttons */
.jd-actions-group {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.jd-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid var(--jd-border-color);
    background-color: #ffffff;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    font-size: 13px;
}
.jd-action-btn:hover {
    background-color: var(--jd-primary-light);
    border-color: var(--jd-primary);
    color: var(--jd-primary);
}
.jd-action-btn.btn-more {
    font-size: 15px;
    letter-spacing: 1px;
}

/* Custom Checkbox */
.jd-custom-check {
    width: 18px;
    height: 18px;
    cursor: pointer;
    border-radius: 4px;
    accent-color: var(--jd-primary);
}

/* Table Footer & Pagination */
.jd-table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background-color: #ffffff;
    border-top: 1px solid var(--jd-border-color);
    flex-wrap: wrap;
    gap: 12px;
}
.jd-footer-info {
    font-size: 13px;
    color: var(--jd-text-muted);
}
.jd-pagination {
    display: flex;
    align-items: center;
    gap: 6px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.jd-page-btn {
    min-width: 34px;
    height: 34px;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: 1px solid var(--jd-border-color);
    background-color: #ffffff;
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}
.jd-page-btn:hover:not(.disabled):not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--jd-text-dark);
}
.jd-page-btn.active {
    background-color: var(--jd-primary);
    border-color: var(--jd-primary);
    color: #ffffff;
    font-weight: 600;
}
.jd-page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f8fafc;
}

/* Modal Quick View Drawer */
.jd-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.5);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.jd-modal-card {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 750px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: jdModalSlideIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes jdModalSlideIn {
    from { transform: translateY(15px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.jd-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--jd-border-color);
}
.jd-modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--jd-text-dark);
    margin: 0;
}
.jd-modal-close {
    background: transparent;
    border: none;
    font-size: 20px;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 6px;
    padding: 4px 8px;
}
.jd-modal-close:hover {
    color: var(--jd-text-dark);
    background-color: #f1f5f9;
}
.jd-modal-body {
    padding: 24px;
}
.jd-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid var(--jd-border-color);
    background-color: #fafbfd;
}

/* Empty State */
.jd-empty-state {
    padding: 60px 20px;
    text-align: center;
    color: var(--jd-text-muted);
}
.jd-empty-icon {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 14px;
}
.jd-empty-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--jd-text-dark);
    margin-bottom: 4px;
}
.jd-empty-subtitle {
    font-size: 13px;
    color: var(--jd-text-muted);
}

/* Dropdown Menu Positioning */
.jd-dropdown {
    position: relative;
    display: inline-block;
}
.jd-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 4px);
    background-color: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    min-width: 170px;
    z-index: 100;
    padding: 6px 0;
}
.jd-dropdown.open .jd-dropdown-menu {
    display: block;
}
.jd-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 16px;
    font-size: 13px;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.1s ease;
}
.jd-dropdown-item:hover {
    background-color: #f1f5f9;
    color: var(--jd-primary);
}
.jd-dropdown-item.text-danger:hover {
    background-color: #fef2f2;
    color: #dc2626;
}

/* Floating Batch Action Bar */
.jd-batch-bar {
    display: none;
    align-items: center;
    justify-content: space-between;
    background-color: #1e293b;
    color: #ffffff;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 16px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    animation: fadeInDown 0.2s ease;
}
.jd-batch-bar.show {
    display: flex;
}
.jd-batch-info {
    font-size: 13px;
    font-weight: 500;
}
.jd-batch-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.jd-batch-btn {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: opacity 0.15s ease;
}
.jd-batch-btn:hover {
    opacity: 0.9;
}
.jd-batch-btn.btn-approve {
    background-color: #10b981;
    color: #ffffff;
}
.jd-batch-btn.btn-decline {
    background-color: #f59e0b;
    color: #ffffff;
}
.jd-batch-btn.btn-cancel {
    background-color: #334155;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pt-3 pb-4" id="jd-pending-page-container">

    <!-- Tier 1: Summary / KPI Cards -->
    <div class="jd-summary-grid">
        <!-- Card 1: Dynamic Status JDs -->
        <div class="jd-kpi-card">
            <div class="jd-kpi-left">
                <div class="jd-kpi-icon-box {{ $kpiIconBoxClass }}">
                    <i class="fa {{ $kpiIconClass }}"></i>
                </div>
                <div class="jd-kpi-content">
                    <span class="jd-kpi-label">{{ $statusTitleLabel }} JDs</span>
                    <span class="jd-kpi-value" id="kpi-pending-count">{{ $currentCount }}</span>
                </div>
            </div>
            <button type="button" class="jd-kpi-menu-btn" title="Options"><i class="fa fa-ellipsis-v"></i></button>
        </div>

        <!-- Card 2: Reviewed Today -->
        <div class="jd-kpi-card">
            <div class="jd-kpi-left">
                <div class="jd-kpi-icon-box green">
                    <i class="fa fa-check-circle-o"></i>
                </div>
                <div class="jd-kpi-content">
                    <span class="jd-kpi-label">Reviewed Today</span>
                    <span class="jd-kpi-value">08</span>
                </div>
            </div>
            <button type="button" class="jd-kpi-menu-btn" title="Options"><i class="fa fa-ellipsis-v"></i></button>
        </div>

        <!-- Card 3: Average Review Time -->
        <div class="jd-kpi-card">
            <div class="jd-kpi-left">
                <div class="jd-kpi-icon-box blue">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="jd-kpi-content">
                    <span class="jd-kpi-label">Average Review Time</span>
                    <span class="jd-kpi-value">2.4h</span>
                </div>
            </div>
            <button type="button" class="jd-kpi-menu-btn" title="Options"><i class="fa fa-ellipsis-v"></i></button>
        </div>
    </div>

    <!-- Tier 2: Filter & Action Toolbar -->
    <div class="jd-toolbar-card">
        <div class="jd-toolbar-left">
            <!-- Search Input -->
            <div class="jd-search-wrapper">
                <i class="fa fa-search"></i>
                <input type="text" id="jd-search-input" class="jd-search-input" placeholder="Search job title or skill..." autocomplete="off">
                <i class="fa fa-times jd-search-clear" id="jd-search-clear" title="Clear search"></i>
            </div>

            <!-- Experience Filter -->
            <select id="jd-filter-experience" class="jd-filter-select">
                <option value="">⚙️ Experience</option>
                <option value="fresher">Freshers (0 yrs)</option>
                <option value="1-3">1 – 3 years</option>
                <option value="3-5">3 – 5 years</option>
                <option value="5-8">5 – 8 years</option>
                <option value="8+">8+ years</option>
            </select>

            <!-- Skills Filter -->
            <select id="jd-filter-skills" class="jd-filter-select">
                <option value="">🏷️ Skills</option>
                <option value="laravel">Laravel</option>
                <option value="php">PHP</option>
                <option value="react">React</option>
                <option value="java">Java</option>
                <option value="node">Node.js</option>
                <option value="angular">Angular</option>
                <option value="flutter">Flutter</option>
                <option value="mysql">MySQL</option>
            </select>
        </div>

        <div class="jd-toolbar-right">
            <!-- Freshness & Cache Indicator -->
            <span class="jd-freshness-indicator text-muted" id="jd-freshness-indicator" style="font-size: 11px; font-weight: 500;"></span>

            <!-- Manual Refresh Button (Bypasses Cache) -->
            <button type="button" class="jd-action-btn" id="jd-refresh-btn" title="Refresh data from server (Bypasses cache)">
                <i class="fa fa-refresh" id="jd-refresh-icon"></i>
            </button>

            <!-- Sort Selector -->
            <select id="jd-sort-order" class="jd-sort-select">
                <option value="newest">⇅ Newest first</option>
                <option value="oldest">⇅ Oldest first</option>
                <option value="title_asc">⇅ Title A – Z</option>
                <option value="exp_asc">⇅ Experience (Low to High)</option>
            </select>

            <!-- "+ Add New JD" Split Dropdown -->
            <div class="jd-dropdown" id="add-jd-dropdown">
                <button type="button" class="jd-btn-primary" id="add-jd-btn">
                    <i class="fa fa-plus"></i> Add New JD <i class="fa fa-angle-down"></i>
                </button>
                <div class="jd-dropdown-menu">
                    <a href="{{ route('superadmin.company.createjd', [encrypt(1), encrypt(0)]) }}" class="jd-dropdown-item">
                        <i class="fa fa-edit text-primary"></i> Manually Add
                    </a>
                    <a href="{{ route('superadmin.company.createjd', [encrypt(2), encrypt(0)]) }}" class="jd-dropdown-item">
                        <i class="fa fa-table text-success"></i> By Google Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Action Floating Toolbar -->
    <div class="jd-batch-bar" id="jd-batch-bar">
        <div class="jd-batch-info">
            <i class="fa fa-check-square-o me-2 text-primary"></i>
            <span id="jd-selected-count">0</span> Job Descriptions selected
        </div>
        <div class="jd-batch-actions">
            <button type="button" class="jd-batch-btn btn-approve" id="batch-approve-btn">
                <i class="fa fa-check me-1"></i> Approve Selected
            </button>
            <button type="button" class="jd-batch-btn btn-decline" id="batch-decline-btn">
                <i class="fa fa-times me-1"></i> Decline Selected
            </button>
            <button type="button" class="jd-batch-btn btn-cancel" id="batch-cancel-btn">
                Cancel
            </button>
        </div>
    </div>

    <!-- Tier 3: Enterprise Data Stream & Table -->
    <div class="jd-table-card">
        <div class="jd-table-container">
            <table class="jd-table" id="jd-pending-table">
                <thead>
                    <tr>
                        <th>JOB TITLE</th>
                        <th>EXPERIENCE</th>
                        <th>SKILLS</th>
                        <th>STATUS</th>
                        <th>SUBMITTED <i class="fa fa-arrow-down" style="font-size: 10px; margin-left: 2px;"></i></th>
                        <th style="text-align: right; padding-right: 24px;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="jd-table-body">
                    <!-- Populated dynamically via High-Speed Client-First JdStore -->
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            <span class="text-muted">Loading {{ $statusEntityLabel }}...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer & Pagination -->
        <div class="jd-table-footer" id="jd-table-footer">
            <div class="jd-footer-info" id="jd-pagination-info">
                Showing 0 of 0 {{ $statusEntityLabel }}
            </div>
            <ul class="jd-pagination" id="jd-pagination-controls">
                <!-- Dynamically generated pagination -->
            </ul>
        </div>
    </div>
</div>

<!-- Slide-Out Quick View Modal -->
<div class="jd-modal-backdrop" id="jd-quick-view-modal">
    <div class="jd-modal-card">
        <div class="jd-modal-header">
            <div>
                <h3 class="jd-modal-title" id="modal-jd-title">Job Description Details</h3>
                <span class="badge badge-light-warning fw-bold fs-8 mt-1" id="modal-jd-id-badge">JD-0000</span>
            </div>
            <button type="button" class="jd-modal-close" id="modal-close-btn">&times;</button>
        </div>
        <div class="jd-modal-body">
            <!-- Metadata summary cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="p-3 border rounded bg-light-subtle">
                        <span class="text-muted fs-8 d-block mb-1">Required Experience</span>
                        <span class="fw-bold fs-6 text-dark" id="modal-jd-exp">0 – 2 years</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 border rounded bg-light-subtle">
                        <span class="text-muted fs-8 d-block mb-1">Current Status</span>
                        <span class="badge badge-light-warning fs-7" id="modal-jd-status">Pending Review</span>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="mb-4">
                <span class="text-muted fs-8 d-block mb-2 text-uppercase fw-semibold">Required Core Skills</span>
                <div class="d-flex flex-wrap gap-2" id="modal-jd-skills">
                    <!-- Skills tags -->
                </div>
            </div>

            <!-- Full Job Description -->
            <div>
                <span class="text-muted fs-8 d-block mb-2 text-uppercase fw-semibold">Job Description / Responsibilities</span>
                <div class="p-3 border rounded bg-white" style="max-height: 350px; overflow-y: auto; font-size: 13px; line-height: 1.6;" id="modal-jd-content">
                    <!-- Rendered HTML -->
                </div>
            </div>
        </div>
        <div class="jd-modal-footer">
            <button type="button" class="btn btn-sm btn-success" id="modal-approve-btn">
                <i class="fa fa-check me-1"></i> Approve JD
            </button>
            <button type="button" class="btn btn-sm btn-warning" id="modal-decline-btn">
                <i class="fa fa-times me-1"></i> Decline JD
            </button>
            <a href="#" class="btn btn-sm btn-light" id="modal-edit-link">
                <i class="fa fa-edit me-1"></i> Edit Full Details
            </a>
            <button type="button" class="btn btn-sm btn-secondary" id="modal-footer-close">Close</button>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
/**
 * Super Admin JD Pending List — Client-First In-Memory JdStore
 * Rule 14 & Rule 20: Modern Card + Sub-Card Enterprise Standard
 * Clean Architecture: High-speed local filtering, zero page reloads, zero dead scripts.
 */
(function() {
    'use strict';

    // Pastel Theme Map for Job Avatar Letters
    var avatarThemes = ['theme-blue', 'theme-amber', 'theme-green', 'theme-rose', 'theme-purple'];
    var skillPillColors = ['pill-blue', 'pill-green', 'pill-purple', 'pill-pink', 'pill-amber'];

    var JdStore = {
        CACHE_KEY: 'jero_sa_jd_cache_v3_{{ $jd_status }}',
        CACHE_TTL_MS: 300000, // 5 minutes TTL
        entityLabel: '{{ $statusEntityLabel }}',
        rawRecords: [],
        filteredRecords: [],
        selectedIds: new Set(),
        currentModalId: null,
        currentPage: 1,
        perPage: 10,
        broadcastChannel: null,
        isFetching: false,
        lastFetchedAt: null,
        filters: {
            search: '',
            experience: '',
            skills: '',
            sort: 'newest'
        },
        ajaxEndpoint: '{!! route("superadmin.company.jdlistdata") !!}?jd_status={{ $jd_status }}',

        init: function() {
            this.initBroadcastChannel();
            this.initEchoListener();
            this.bindEvents();

            // L2 Cache Check: Instant hydration from sessionStorage
            var isHydrated = this.hydrateFromCache();
            if (isHydrated) {
                // Stale-While-Revalidate: If data is older than 60 seconds, fetch silently in background
                var cacheAge = Date.now() - (this.lastFetchedAt || 0);
                if (cacheAge > 60000) {
                    this.fetchData(false); // background refresh
                }
            } else {
                this.fetchData(true); // initial fetch with loading spinner
            }
        },

        initBroadcastChannel: function() {
            var self = this;
            if ('BroadcastChannel' in window) {
                try {
                    self.broadcastChannel = new BroadcastChannel('jero_jd_pending_sync');
                    self.broadcastChannel.onmessage = function(event) {
                        if (event && event.data) {
                            if (event.data.type === 'INVALIDATE_CACHE') {
                                self.clearCache();
                                self.fetchData(false);
                            } else if (event.data.type === 'DELTA_UPDATE') {
                                self.handleDelta(event.data.delta, false);
                            }
                        }
                    };
                } catch (e) {
                    console.warn('[JdStore] BroadcastChannel init warning:', e);
                }
            }

            // Cross-tab fallback via storage event
            window.addEventListener('storage', function(e) {
                if (e.key === 'jero_sa_jd_invalidation_trigger') {
                    self.clearCache();
                    self.fetchData(false);
                }
            });
        },

        initEchoListener: function() {
            var self = this;
            if (typeof window.Echo !== 'undefined') {
                try {
                    window.Echo.private('superadmin.jd-catalog')
                        .listen('.jd.status.updated', function(delta) {
                            console.log('[JdStore] Reverb delta received:', delta);
                            self.handleDelta(delta, true);
                        });
                } catch (e) {
                    console.warn('[JdStore] Echo subscription error:', e);
                }
            }
        },

        handleDelta: function(delta, shouldBroadcastLocally) {
            var self = this;
            if (!delta || !delta.id) return;

            var targetId = parseInt(delta.id);
            var newStatus = parseInt(delta.new_status);
            var activeStatus = {{ $currentStatusLower === 'pending' ? 0 : ($currentStatusLower === 'approved' ? 1 : 2) }};

            // Invalidate local L2 cache
            self.clearCache();

            // Update KPI count if provided in delta
            if (delta.counts) {
                if (activeStatus === 0 && typeof delta.counts.pending !== 'undefined') {
                    $('#kpi-pending-count').text(delta.counts.pending);
                } else if (activeStatus === 1 && typeof delta.counts.approved !== 'undefined') {
                    $('#kpi-pending-count').text(delta.counts.approved);
                } else if (activeStatus === 2 && typeof delta.counts.declined !== 'undefined') {
                    $('#kpi-pending-count').text(delta.counts.declined);
                }
            }

            if (activeStatus === 0) {
                // We are viewing Pending List
                if (newStatus !== 0) {
                    // Item approved or declined: remove from in-memory records
                    var existingIdx = self.rawRecords.findIndex(function(r) { return r.id === targetId; });
                    if (existingIdx !== -1) {
                        self.rawRecords.splice(existingIdx, 1);
                        self.selectedIds.delete(targetId);
                        $('#kpi-pending-count').text(self.rawRecords.length);
                        self.applyFilters();
                    }
                } else {
                    // New pending JD created: fetch fresh records in background
                    self.fetchData(false);
                }
            } else if (activeStatus === 1) {
                // Viewing Approved List
                if (newStatus === 1) {
                    self.fetchData(false);
                } else {
                    var approvedIdx = self.rawRecords.findIndex(function(r) { return r.id === targetId; });
                    if (approvedIdx !== -1) {
                        self.rawRecords.splice(approvedIdx, 1);
                        self.applyFilters();
                    }
                }
            }

            if (shouldBroadcastLocally && self.broadcastChannel) {
                try {
                    self.broadcastChannel.postMessage({ type: 'DELTA_UPDATE', delta: delta });
                } catch (e) {}
            }
        },

        notifyOtherTabs: function() {
            if (this.broadcastChannel) {
                try {
                    this.broadcastChannel.postMessage({ type: 'INVALIDATE_CACHE', timestamp: Date.now() });
                } catch (e) {}
            }
            try {
                localStorage.setItem('jero_sa_jd_invalidation_trigger', Date.now().toString());
            } catch (e) {}
        },

        hydrateFromCache: function() {
            try {
                // Proactively purge any obsolete legacy cache keys from previous versions
                sessionStorage.removeItem('jero_sa_jd_pending_cache_v2');
                sessionStorage.removeItem('jero_sa_jd_pending_cache_v1');

                var cachedRaw = sessionStorage.getItem(this.CACHE_KEY);
                if (!cachedRaw) return false;

                var snapshot = JSON.parse(cachedRaw);
                if (!snapshot || !snapshot.timestamp || !Array.isArray(snapshot.records)) {
                    return false;
                }

                // Strict Status Isolation: ensure cached records belong to THIS exact status route
                if (snapshot.status && snapshot.status !== '{{ $jd_status }}') {
                    sessionStorage.removeItem(this.CACHE_KEY);
                    return false;
                }

                var age = Date.now() - snapshot.timestamp;
                if (age > this.CACHE_TTL_MS) {
                    sessionStorage.removeItem(this.CACHE_KEY);
                    return false;
                }

                this.lastFetchedAt = snapshot.timestamp;
                this.processRecords(snapshot.records, false); // false = do not re-save to cache during hydration
                this.updateFreshnessIndicator(snapshot.timestamp, true);
                return true;
            } catch (e) {
                console.warn('[JdStore] Cache hydration error:', e);
                return false;
            }
        },

        saveToCache: function(rawItems) {
            try {
                var snapshot = {
                    version: 3,
                    status: '{{ $jd_status }}',
                    timestamp: Date.now(),
                    records: rawItems
                };
                sessionStorage.setItem(this.CACHE_KEY, JSON.stringify(snapshot));
                this.lastFetchedAt = snapshot.timestamp;
                this.updateFreshnessIndicator(snapshot.timestamp, false);
            } catch (e) {
                // QuotaExceededError or private browsing restriction
                console.warn('[JdStore] sessionStorage quota exceeded or restricted:', e);
            }
        },

        clearCache: function() {
            try {
                sessionStorage.removeItem(this.CACHE_KEY);
                this.lastFetchedAt = null;
            } catch (e) {}
        },

        updateFreshnessIndicator: function(timestamp, isCached) {
            var indicator = $('#jd-freshness-indicator');
            if (!indicator.length) return;
            var diffSec = Math.max(0, Math.round((Date.now() - timestamp) / 1000));
            var text = isCached ? 'Cached (' + diffSec + 's ago)' : 'Live synced';
            indicator.text(text).attr('title', 'Last synchronized: ' + new Date(timestamp).toLocaleTimeString());
        },

        bindEvents: function() {
            var self = this;

            // Search input with 200ms debounce
            var searchDebounceTimer;
            $('#jd-search-input').on('input', function() {
                var query = $(this).val().trim();
                if (query.length > 0) {
                    $('#jd-search-clear').show();
                } else {
                    $('#jd-search-clear').hide();
                }
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    self.filters.search = query.toLowerCase();
                    self.currentPage = 1;
                    self.applyFilters();
                }, 200);
            });

            // Clear search
            $('#jd-search-clear').on('click', function() {
                $('#jd-search-input').val('');
                $(this).hide();
                self.filters.search = '';
                self.currentPage = 1;
                self.applyFilters();
            });

            // Experience Filter
            $('#jd-filter-experience').on('change', function() {
                self.filters.experience = $(this).val();
                self.currentPage = 1;
                self.applyFilters();
            });

            // Skills Filter
            $('#jd-filter-skills').on('change', function() {
                self.filters.skills = $(this).val().toLowerCase();
                self.currentPage = 1;
                self.applyFilters();
            });

            // Sort Filter
            $('#jd-sort-order').on('change', function() {
                self.filters.sort = $(this).val();
                self.applyFilters();
            });

            // Manual Refresh Button (Bypasses Cache)
            $('#jd-refresh-btn').on('click', function(e) {
                e.preventDefault();
                self.clearCache();
                self.fetchData(true);
                self.notifyOtherTabs();
            });

            // Split Dropdown Toggle
            $('#add-jd-btn').on('click', function(e) {
                e.stopPropagation();
                $('#add-jd-dropdown').toggleClass('open');
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#add-jd-dropdown').length) {
                    $('#add-jd-dropdown').removeClass('open');
                }
                $('.jd-dropdown').not('#add-jd-dropdown').removeClass('open');
            });

            // Select All Checkbox
            $('#select-all-jds').on('change', function() {
                var checked = $(this).prop('checked');
                if (checked) {
                    self.filteredRecords.forEach(function(r) { self.selectedIds.add(r.id); });
                } else {
                    self.selectedIds.clear();
                }
                $('.jd-row-check').prop('checked', checked);
                $('.jd-table tbody tr').toggleClass('selected', checked);
                self.updateBatchToolbar();
            });

            // Row Checkbox delegation
            $(document).on('change', '.jd-row-check', function() {
                var id = parseInt($(this).data('id'));
                if ($(this).prop('checked')) {
                    self.selectedIds.add(id);
                    $(this).closest('tr').addClass('selected');
                } else {
                    self.selectedIds.delete(id);
                    $(this).closest('tr').removeClass('selected');
                    $('#select-all-jds').prop('checked', false);
                }
                self.updateBatchToolbar();
            });

            // Batch Actions
            $('#batch-approve-btn').on('click', function() {
                self.executeBatchStatusMutation(1);
            });
            $('#batch-decline-btn').on('click', function() {
                self.executeBatchStatusMutation(2);
            });
            $('#batch-cancel-btn').on('click', function() {
                self.selectedIds.clear();
                self.updateBatchToolbar();
                $('.jd-row-check, #select-all-jds').prop('checked', false);
                $('.jd-table tbody tr').removeClass('selected');
            });

            // Modal Status Actions
            $('#modal-approve-btn').on('click', function() {
                if (self.currentModalId) {
                    self.executeStatusMutation(self.currentModalId, 1, function(success) {
                        if (success) $('#jd-quick-view-modal').fadeOut(150);
                    });
                }
            });
            $('#modal-decline-btn').on('click', function() {
                if (self.currentModalId) {
                    self.executeStatusMutation(self.currentModalId, 2, function(success) {
                        if (success) $('#jd-quick-view-modal').fadeOut(150);
                    });
                }
            });

            // Row Dropdown Action delegation
            $(document).on('click', '.jd-action-approve', function(e) {
                e.preventDefault();
                var id = parseInt($(this).data('id'));
                $('.jd-dropdown').removeClass('open');
                self.executeStatusMutation(id, 1);
            });
            $(document).on('click', '.jd-action-decline', function(e) {
                e.preventDefault();
                var id = parseInt($(this).data('id'));
                $('.jd-dropdown').removeClass('open');
                self.executeStatusMutation(id, 2);
            });
            $(document).on('click', '.jd-action-delete', function(e) {
                e.preventDefault();
                var id = parseInt($(this).data('id'));
                $('.jd-dropdown').removeClass('open');
                self.executeDeleteMutation(id);
            });

            // Quick View Trigger delegation
            $(document).on('click', '.jd-btn-view, .jd-title-link', function(e) {
                e.preventDefault();
                var id = parseInt($(this).data('id'));
                self.openQuickView(id);
            });

            // Row Dropdown menu toggle
            $(document).on('click', '.jd-btn-more-toggle', function(e) {
                e.stopPropagation();
                var dropdown = $(this).closest('.jd-dropdown');
                $('.jd-dropdown').not(dropdown).removeClass('open');
                dropdown.toggleClass('open');
            });

            // Modal Close Events
            $('#modal-close-btn, #modal-footer-close').on('click', function() {
                $('#jd-quick-view-modal').fadeOut(150);
            });
            $('#jd-quick-view-modal').on('click', function(e) {
                if ($(e.target).is('#jd-quick-view-modal')) {
                    $(this).fadeOut(150);
                }
            });
        },

        fetchData: function(showSpinner) {
            var self = this;
            if (self.isFetching) return;
            self.isFetching = true;

            var refreshIcon = $('#jd-refresh-icon');
            refreshIcon.addClass('fa-spin');

            if (showSpinner && (!self.rawRecords || self.rawRecords.length === 0)) {
                $('#jd-table-body').html(
                    '<tr><td colspan="6" class="text-center py-5">' +
                    '<div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>' +
                    '<span class="text-muted">Loading ' + escapeHtml(self.entityLabel) + '...</span>' +
                    '</td></tr>'
                );
            }

            $.ajax({
                url: self.ajaxEndpoint,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var items = [];
                    if (response && response.data && Array.isArray(response.data)) {
                        items = response.data;
                    }
                    self.processRecords(items, true); // save to cache
                },
                error: function(xhr) {
                    console.error('Failed to load ' + self.entityLabel + ':', xhr);
                    if (!self.rawRecords || self.rawRecords.length === 0) {
                        $('#jd-table-body').html(
                            '<tr><td colspan="6" class="text-center py-5 text-danger">' +
                            '<i class="fa fa-exclamation-triangle me-2"></i> Failed to load ' + escapeHtml(self.entityLabel) + '. Please try refreshing.</td></tr>'
                        );
                    }
                },
                complete: function() {
                    self.isFetching = false;
                    refreshIcon.removeClass('fa-spin');
                }
            });
        },

        processRecords: function(rawItems, shouldCache) {
            var self = this;
            self.rawRecords = rawItems.map(function(item, idx) {
                // Parse clean title (handling either raw_title or HTML stripped)
                var title = item.raw_title || '';
                if (!title && item.title) {
                    title = $('<div>').html(item.title).text().trim();
                }

                // Parse clean skills
                var skills = item.raw_skills || '';
                if (!skills && item.skills) {
                    skills = $('<div>').html(item.skills).text().trim();
                }
                var skillList = skills ? skills.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];

                // Parse experience numbers
                var minExp = parseFloat(item.minimum_exp) || 0;
                var maxExp = parseFloat(item.maxmum_exp) || 0;

                // Derive formatted reference ID
                var refCode = 'JD-' + (1000 + parseInt(item.id || idx));

                // Derive theme color based on first letter
                var charCode = (title && title.length > 0) ? title.charCodeAt(0) : 65;
                var themeClass = avatarThemes[charCode % avatarThemes.length];

                return {
                    id: item.id,
                    title: title || 'Untitled Job',
                    refCode: refCode,
                    companyName: 'Global Master Catalog',
                    minExp: minExp,
                    maxExp: maxExp,
                    skills: skills,
                    skillList: skillList,
                    jdHtml: item.raw_jd || item.jd || '',
                    status: item.status,
                    editUrl: item.action ? $(item.action).attr('href') : '#',
                    themeClass: themeClass,
                    firstChar: (title && title.length > 0) ? title.charAt(0).toUpperCase() : 'J',
                    submittedDate: '26 Oct 2026',
                    submittedTime: '11:30 AM'
                };
            });

            // Update KPI Card if present
            if ($('#kpi-pending-count').length) {
                $('#kpi-pending-count').text(self.rawRecords.length);
            }

            if (shouldCache) {
                self.saveToCache(rawItems);
            }

            self.applyFilters();
        },

        applyFilters: function() {
            var self = this;
            var results = self.rawRecords.slice();

            // 1. Text Search Filter (Title or Skills)
            if (self.filters.search) {
                var q = self.filters.search;
                results = results.filter(function(r) {
                    return r.title.toLowerCase().indexOf(q) !== -1 ||
                           r.skills.toLowerCase().indexOf(q) !== -1 ||
                           r.refCode.toLowerCase().indexOf(q) !== -1;
                });
            }

            // 2. Experience Filter
            if (self.filters.experience) {
                var exp = self.filters.experience;
                if (exp === 'fresher') {
                    results = results.filter(function(r) { return r.minExp === 0 && r.maxExp === 0; });
                } else if (exp === '1-3') {
                    results = results.filter(function(r) { return r.minExp <= 3 && r.maxExp >= 1; });
                } else if (exp === '3-5') {
                    results = results.filter(function(r) { return r.minExp <= 5 && r.maxExp >= 3; });
                } else if (exp === '5-8') {
                    results = results.filter(function(r) { return r.minExp <= 8 && r.maxExp >= 5; });
                } else if (exp === '8+') {
                    results = results.filter(function(r) { return r.maxExp >= 8; });
                }
            }

            // 3. Skills Filter
            if (self.filters.skills) {
                var sk = self.filters.skills;
                results = results.filter(function(r) {
                    return r.skills.toLowerCase().indexOf(sk) !== -1;
                });
            }

            // 4. Sorting
            if (self.filters.sort === 'newest') {
                results.sort(function(a, b) { return b.id - a.id; });
            } else if (self.filters.sort === 'oldest') {
                results.sort(function(a, b) { return a.id - b.id; });
            } else if (self.filters.sort === 'title_asc') {
                results.sort(function(a, b) { return a.title.localeCompare(b.title); });
            } else if (self.filters.sort === 'exp_asc') {
                results.sort(function(a, b) { return a.minExp - b.minExp; });
            }

            self.filteredRecords = results;
            self.renderTable();
        },

        renderTable: function() {
            var self = this;
            var tbody = $('#jd-table-body');
            tbody.empty();

            if (self.filteredRecords.length === 0) {
                tbody.html(
                    '<tr><td colspan="6">' +
                    '<div class="jd-empty-state">' +
                    '<div class="jd-empty-icon"><i class="fa fa-folder-open-o"></i></div>' +
                    '<div class="jd-empty-title">No ' + escapeHtml(self.entityLabel) + ' match your filter</div>' +
                    '<div class="jd-empty-subtitle">Try adjusting your search criteria or resetting filters.</div>' +
                    '</div></td></tr>'
                );
                self.renderPagination(0, 0, 0);
                return;
            }

            // Pagination slice
            var total = self.filteredRecords.length;
            var startIndex = (self.currentPage - 1) * self.perPage;
            var endIndex = Math.min(startIndex + self.perPage, total);
            var pageRecords = self.filteredRecords.slice(startIndex, endIndex);

            var rowsHtml = '';
            pageRecords.forEach(function(row) {
                // Format Experience text
                var expText = '';
                if (row.minExp === 0 && row.maxExp === 0) {
                    expText = 'Freshers';
                } else if (row.minExp > 0 && row.maxExp > 0) {
                    expText = row.minExp + ' – ' + row.maxExp + ' years';
                } else if (row.minExp > 0) {
                    expText = row.minExp + '+ years';
                } else if (row.maxExp > 0) {
                    expText = 'Up to ' + row.maxExp + ' years';
                } else {
                    expText = 'Not specified';
                }

                // Format Skills chips
                var skillsHtml = '';
                var maxVisibleSkills = 3;
                var visibleSkills = row.skillList.slice(0, maxVisibleSkills);
                var overflowCount = row.skillList.length - maxVisibleSkills;

                visibleSkills.forEach(function(skill, sIdx) {
                    var pillColor = skillPillColors[sIdx % skillPillColors.length];
                    skillsHtml += '<span class="jd-skill-pill ' + pillColor + '">' + escapeHtml(skill) + '</span>';
                });

                if (overflowCount > 0) {
                    var remainingSkills = row.skillList.slice(maxVisibleSkills).join(', ');
                    skillsHtml += '<span class="jd-skill-pill pill-gray" title="' + escapeHtml(remainingSkills) + '">+' + overflowCount + '</span>';
                }

                // Format Status Badge
                var statusBadgeHtml = '';
                var st = parseInt(row.status);
                if (st === 1) {
                    statusBadgeHtml = '<span class="jd-status-badge status-approved"><i class="fa fa-check-circle" style="font-size: 11px;"></i> Approved</span>';
                } else if (st === 2) {
                    statusBadgeHtml = '<span class="jd-status-badge status-declined"><i class="fa fa-times-circle" style="font-size: 11px;"></i> Declined</span>';
                } else {
                    statusBadgeHtml = '<span class="jd-status-badge status-pending"><i class="fa fa-clock-o" style="font-size: 11px;"></i> Pending</span>';
                }

                rowsHtml += 
                    '<tr>' +
                        '<td>' +
                            '<div class="jd-title-cell">' +
                                '<div class="jd-avatar-box ' + row.themeClass + '">' + row.firstChar + '</div>' +
                                '<div class="jd-title-text-group">' +
                                    '<a href="javascript:void(0);" class="jd-title-link" data-id="' + row.id + '">' + escapeHtml(row.title) + '</a>' +
                                    '<span class="jd-title-subtitle">' + escapeHtml(row.refCode) + ' &bull; ' + escapeHtml(row.companyName) + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                        '<td><span class="jd-exp-text">' + escapeHtml(expText) + '</span></td>' +
                        '<td><div class="jd-skills-container">' + skillsHtml + '</div></td>' +
                        '<td>' + statusBadgeHtml + '</td>' +
                        '<td>' +
                            '<div class="jd-submitted-cell">' +
                                '<span class="jd-sub-date">' + row.submittedDate + '</span>' +
                                '<span class="jd-sub-time">' + row.submittedTime + '</span>' +
                            '</div>' +
                        '</td>' +
                        '<td style="text-align: right; padding-right: 20px;">' +
                            '<div class="jd-actions-group" style="justify-content: flex-end;">' +
                                '<button type="button" class="jd-action-btn jd-btn-view" data-id="' + row.id + '" title="Quick View"><i class="fa fa-eye"></i></button>' +
                                '<a href="' + row.editUrl + '" class="jd-action-btn" title="Edit JD"><i class="fa fa-pencil"></i></a>' +
                                '<div class="jd-dropdown">' +
                                    '<button type="button" class="jd-action-btn btn-more jd-btn-more-toggle" title="More Options">&bull;&bull;&bull;</button>' +
                                    '<div class="jd-dropdown-menu">' +
                                        '<a href="javascript:void(0);" class="jd-dropdown-item text-success jd-action-approve" data-id="' + row.id + '">' +
                                            '<i class="fa fa-check text-success"></i> Approve JD' +
                                        '</a>' +
                                        '<a href="javascript:void(0);" class="jd-dropdown-item text-warning jd-action-decline" data-id="' + row.id + '">' +
                                            '<i class="fa fa-times text-warning"></i> Decline JD' +
                                        '</a>' +
                                        '<a href="javascript:void(0);" class="jd-dropdown-item text-danger jd-action-delete" data-id="' + row.id + '">' +
                                            '<i class="fa fa-trash text-danger"></i> Delete JD' +
                                        '</a>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';
            });

            tbody.html(rowsHtml);
            self.renderPagination(startIndex + 1, endIndex, total);
        },

        renderPagination: function(start, end, total) {
            var self = this;
            var info = $('#jd-pagination-info');
            var controls = $('#jd-pagination-controls');
            controls.empty();

            if (total === 0) {
                info.text('Showing 0 – 0 of 0 ' + self.entityLabel);
                return;
            }

            info.text('Showing ' + start + ' – ' + end + ' of ' + total + ' ' + self.entityLabel);

            var totalPages = Math.ceil(total / self.perPage);
            if (totalPages <= 1) return;

            // Prev Button
            var prevDisabled = self.currentPage === 1 ? 'disabled' : '';
            controls.append('<li><button class="jd-page-btn ' + prevDisabled + '" id="jd-page-prev"><i class="fa fa-angle-left"></i></button></li>');

            // Page numbers
            for (var p = 1; p <= totalPages; p++) {
                var activeClass = (p === self.currentPage) ? 'active' : '';
                controls.append('<li><button class="jd-page-btn ' + activeClass + '" data-page="' + p + '">' + p + '</button></li>');
            }

            // Next Button
            var nextDisabled = self.currentPage === totalPages ? 'disabled' : '';
            controls.append('<li><button class="jd-page-btn ' + nextDisabled + '" id="jd-page-next"><i class="fa fa-angle-right"></i></button></li>');

            // Bind Pagination Clicks
            controls.find('button[data-page]').on('click', function() {
                self.currentPage = parseInt($(this).data('page'));
                self.renderTable();
            });
            $('#jd-page-prev').on('click', function() {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.renderTable();
                }
            });
            $('#jd-page-next').on('click', function() {
                if (self.currentPage < totalPages) {
                    self.currentPage++;
                    self.renderTable();
                }
            });
        },

        openQuickView: function(id) {
            var self = this;
            var record = self.rawRecords.find(function(r) { return r.id === id; });
            if (!record) return;

            $('#modal-jd-title').text(record.title);
            $('#modal-jd-id-badge').text(record.refCode);
            
            var expText = (record.minExp || 0) + ' – ' + (record.maxExp || 0) + ' years';
            if (record.minExp === 0 && record.maxExp === 0) expText = 'Freshers (0 years)';
            $('#modal-jd-exp').text(expText);

            // Update modal status badge
            var modalStatus = $('#modal-jd-status');
            var st = parseInt(record.status);
            if (st === 1) {
                modalStatus.attr('class', 'badge badge-light-success fs-7').html('<i class="fa fa-check-circle me-1 text-success"></i> Approved');
            } else if (st === 2) {
                modalStatus.attr('class', 'badge badge-light-danger fs-7').html('<i class="fa fa-times-circle me-1 text-danger"></i> Declined');
            } else {
                modalStatus.attr('class', 'badge badge-light-warning fs-7').html('<i class="fa fa-clock-o me-1 text-warning"></i> Pending Review');
            }

            var skillsContainer = $('#modal-jd-skills');
            skillsContainer.empty();
            if (record.skillList.length > 0) {
                record.skillList.forEach(function(s, idx) {
                    var color = skillPillColors[idx % skillPillColors.length];
                    skillsContainer.append('<span class="jd-skill-pill ' + color + '">' + escapeHtml(s) + '</span>');
                });
            } else {
                skillsContainer.html('<span class="text-muted fs-8">No specific skills tagged</span>');
            }

            // Render Job Description HTML
            if (record.jdHtml && record.jdHtml.trim().length > 0) {
                $('#modal-jd-content').html(record.jdHtml);
            } else {
                $('#modal-jd-content').html('<p class="text-muted fst-italic">No detailed description provided for this job description template.</p>');
            }

            self.currentModalId = id;
            $('#modal-edit-link').attr('href', record.editUrl);
            $('#jd-quick-view-modal').css('display', 'flex').hide().fadeIn(150);
        },

        executeStatusMutation: function(id, newStatus, callback) {
            var self = this;
            var row = self.rawRecords.find(function(r) { return r.id === id; });
            var title = row ? row.title : ('JD #' + id);
            var actionName = newStatus === 1 ? 'Approve' : 'Decline';
            var confirmColor = newStatus === 1 ? '#10b981' : '#f59e0b';

            var confirmAction = function() {
                var idempKey = 'sa_jd_' + id + '_' + newStatus + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

                $.ajax({
                    url: '{{ url("super-admin/jd/status") }}/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    headers: {
                        'X-Idempotency-Key': idempKey
                    },
                    dataType: 'json',
                    success: function(response) {
                        self.removeRecordFromStore(id);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || ('Job Description has been ' + actionName.toLowerCase() + 'd.'),
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }

                        if (callback) callback(true);
                    },
                    error: function(xhr) {
                        var msg = 'Failed to execute action. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                        if (callback) callback(false);
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: actionName + ' Job Description?',
                    text: 'Are you sure you want to ' + actionName.toLowerCase() + ' "' + title + '"?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, ' + actionName + '!'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        confirmAction();
                    }
                });
            } else {
                if (confirm('Are you sure you want to ' + actionName.toLowerCase() + ' "' + title + '"?')) {
                    confirmAction();
                }
            }
        },

        executeBatchStatusMutation: function(newStatus) {
            var self = this;
            var ids = Array.from(self.selectedIds);
            if (!ids || ids.length === 0) return;

            var actionName = newStatus === 1 ? 'Approve' : 'Decline';
            var confirmColor = newStatus === 1 ? '#10b981' : '#f59e0b';

            var confirmAction = function() {
                var idempKey = 'sa_batch_' + newStatus + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

                $.ajax({
                    url: '{{ route("superadmin.company.batchUpdateJdStatus") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus,
                        ids: ids
                    },
                    headers: {
                        'X-Idempotency-Key': idempKey
                    },
                    dataType: 'json',
                    success: function(response) {
                        ids.forEach(function(id) {
                            self.removeRecordFromStore(id, false);
                        });
                        self.selectedIds.clear();
                        self.updateBatchToolbar();
                        self.saveToCache(self.rawRecords);
                        self.applyFilters();
                        self.notifyOtherTabs({ event_type: 'BATCH_STATUS_UPDATED', ids: ids, new_status: newStatus });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || (ids.length + ' Job Descriptions have been ' + actionName.toLowerCase() + 'd.'),
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Failed to execute batch action. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: actionName + ' ' + ids.length + ' Job Descriptions?',
                    text: 'Are you sure you want to ' + actionName.toLowerCase() + ' ' + ids.length + ' selected job descriptions?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, ' + actionName + ' All!'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        confirmAction();
                    }
                });
            } else {
                if (confirm('Are you sure you want to ' + actionName.toLowerCase() + ' ' + ids.length + ' selected job descriptions?')) {
                    confirmAction();
                }
            }
        },

        executeDeleteMutation: function(id) {
            var self = this;
            var row = self.rawRecords.find(function(r) { return r.id === id; });
            var title = row ? row.title : ('JD #' + id);

            var confirmAction = function() {
                var idempKey = 'sa_del_' + id + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

                $.ajax({
                    url: '{{ url("super-admin/jd") }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-Idempotency-Key': idempKey
                    },
                    dataType: 'json',
                    success: function(response) {
                        self.removeRecordFromStore(id);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Job Description has been deleted.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Failed to delete Job Description.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Job Description?',
                    text: 'Are you sure you want to permanently delete "' + title + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Delete!'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        confirmAction();
                    }
                });
            } else {
                if (confirm('Are you sure you want to permanently delete "' + title + '"?')) {
                    confirmAction();
                }
            }
        },

        removeRecordFromStore: function(id, reRender) {
            var self = this;
            if (typeof reRender === 'undefined') reRender = true;

            self.rawRecords = self.rawRecords.filter(function(r) { return r.id !== id; });
            self.selectedIds.delete(id);

            // Update KPI counter
            var pendingCountEl = $('#kpi-pending-count');
            var currentCount = parseInt(pendingCountEl.text()) || self.rawRecords.length;
            if (currentCount > 0) {
                pendingCountEl.text(Math.max(0, currentCount - 1));
            }

            if (reRender) {
                self.updateBatchToolbar();
                self.saveToCache(self.rawRecords);
                self.applyFilters();
                self.notifyOtherTabs({ id: id, event_type: 'STATUS_CHANGED' });
            }
        },

        updateBatchToolbar: function() {
            var self = this;
            var count = self.selectedIds.size;
            var bar = $('#jd-batch-bar');
            if (count > 0) {
                $('#jd-selected-count').text(count);
                bar.addClass('show');
            } else {
                bar.removeClass('show');
            }
        }
    };

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Initialize Store on DOM ready
    $(document).ready(function() {
        JdStore.init();
    });

})();
</script>
@endpush
