@extends('layouts.superadmin_layout')

@push('head-script')
<style>
    /* ─── Global & Top-Gap Normalization (15.6" Screen Standard) ─── */
    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }
    #kt_toolbar {
        display: none !important;
    }
    #kt_content {
        padding-top: 0 !important;
    }
    .brmnote-wrapper {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* ─── Tier 1: Top Header Toolbar Card ─── */
    .brmnote-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 18px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
    }
    .brmnote-header-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }
    .brmnote-header-icon {
        width: 38px !important;
        height: 38px !important;
        border-radius: 8px !important;
        background: #eff6ff !important;
        border: 1px solid #dbeafe !important;
        color: #2563eb !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 16px !important;
        flex-shrink: 0 !important;
    }
    .brmnote-header-title {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .brmnote-header-sub {
        font-size: 11.5px !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }
    .brmnote-kpi-chip {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        border: 1px solid transparent !important;
    }
    .brmnote-kpi-chip.total {
        background: #f1f5f9 !important;
        color: #334155 !important;
        border-color: #e2e8f0 !important;
    }
    .brmnote-kpi-chip.active {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border-color: #a7f3d0 !important;
    }
    .brmnote-kpi-chip.inactive {
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border-color: #fecaca !important;
    }
    .brmnote-kpi-chip.scope {
        background: #eff6ff !important;
        color: #2563eb !important;
        border-color: #bfdbfe !important;
    }

    .brmnote-header-right {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }
    .brmnote-search-subcard {
        position: relative !important;
        height: 36px !important;
        width: 280px !important;
        max-width: 100% !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 10px !important;
        transition: all 0.2s ease !important;
    }
    .brmnote-search-subcard:focus-within {
        background: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .brmnote-search-subcard i.fa-search {
        color: #94a3b8 !important;
        font-size: 12px !important;
        margin-right: 8px !important;
    }
    .brmnote-search-subcard input {
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-size: 12px !important;
        font-family: 'Poppins', sans-serif !important;
        color: #0f172a !important;
        width: 100% !important;
    }
    .brmnote-search-subcard input::placeholder {
        color: #94a3b8 !important;
    }
    .brmnote-search-clear {
        background: transparent !important;
        border: none !important;
        color: #94a3b8 !important;
        font-size: 12px !important;
        cursor: pointer !important;
        padding: 0 4px !important;
        line-height: 1 !important;
        display: none;
    }
    .brmnote-search-clear:hover {
        color: #ef4444 !important;
    }
    .brmnote-add-btn {
        height: 36px !important;
        padding: 0 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        white-space: nowrap !important;
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2) !important;
        transition: all 0.15s ease !important;
    }
    .brmnote-add-btn:hover {
        background: #0086d4 !important;
        border-color: #0086d4 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    /* ─── Tier 2: Sub-Tabs Filter Card (Single Active Highlight Standard) ─── */
    .brmnote-tabs-card {
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
        gap: 10px !important;
    }
    .brmnote-tabs-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .brmnote-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        white-space: nowrap !important;
    }
    .brmnote-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .brmnote-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
    }
    .brmnote-tab-btn.active i {
        color: #ffffff !important;
    }
    .brmnote-tab-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 20px !important;
        height: 18px !important;
        padding: 0 6px !important;
        border-radius: 10px !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
        transition: all 0.15s ease !important;
    }
    .brmnote-tab-badge.badge-active {
        background: #ecfdf5 !important;
        color: #059669 !important;
    }
    .brmnote-tab-badge.badge-inactive {
        background: #fef2f2 !important;
        color: #dc2626 !important;
    }
    .brmnote-tab-btn.active .brmnote-tab-badge,
    .brmnote-tab-btn.active .brmnote-tab-badge.badge-active,
    .brmnote-tab-btn.active .brmnote-tab-badge.badge-inactive {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }
    .brmnote-tab-right-info {
        font-size: 11.5px !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* ─── Tier 3: Primary Card Stream & Discrete Sub-Cards Standard ─── */
    .brmnote-stream-header-strip {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        margin-bottom: 8px !important;
    }
    .brmnote-grid-headers {
        display: grid !important;
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 2.7fr) minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        gap: 12px !important;
        align-items: center !important;
    }
    .brmnote-col-label {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        color: #64748b !important;
        text-transform: uppercase !important;
    }

    /* Master Primary Entity Card */
    .brmnote-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    }
    .brmnote-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px);
    }
    .brmnote-card-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 2.7fr) minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        gap: 12px !important;
        padding: 12px 16px !important;
        align-items: center !important;
    }

    /* Sub-Card 1: Identity & Avatar */
    .brmnote-subcard-identity {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }
    .brmnote-avatar-box {
        width: 38px !important;
        height: 38px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        flex-shrink: 0 !important;
    }
    .brmnote-info-wrap {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }
    .brmnote-card-title {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        cursor: pointer !important;
    }
    .brmnote-card-title:hover {
        color: #009ef7 !important;
    }
    .brmnote-id-pill {
        font-size: 10px !important;
        font-weight: 700 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #dbeafe !important;
        display: inline-block !important;
    }
    .brmnote-scope-pill {
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
        display: inline-block !important;
    }

    /* Sub-Card 2: Content Preview */
    .brmnote-subcard-content {
        min-width: 0 !important;
    }
    .brmnote-preview-snippet {
        font-size: 12px !important;
        color: #475569 !important;
        line-height: 1.45 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        word-break: break-word !important;
    }
    .brmnote-view-full-link {
        font-size: 11px !important;
        color: #009ef7 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin-top: 3px !important;
        cursor: pointer !important;
    }
    .brmnote-view-full-link:hover {
        color: #0086d4 !important;
        text-decoration: underline !important;
    }

    /* Sub-Card 3: Telemetry */
    .brmnote-subcard-telemetry {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        min-width: 0 !important;
    }
    .brmnote-metric-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        background: #f1f5f9 !important;
        padding: 2px 7px !important;
        border-radius: 4px !important;
        width: fit-content !important;
    }
    .brmnote-date-text {
        font-size: 11px !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    /* Sub-Card 4: Status */
    .brmnote-subcard-status {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
    }
    .brmnote-status-badge {
        display: inline-block !important;
        padding: 2px 8px !important;
        border-radius: 20px !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        text-align: center !important;
    }
    .brmnote-status-badge.active {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .brmnote-status-badge.inactive {
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
    }

    /* Sub-Card 5: Actions */
    .brmnote-subcard-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
    }
    .brmnote-action-btn {
        width: 32px !important;
        height: 32px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 12px !important;
        padding: 0 !important;
        transition: all 0.15s ease !important;
    }
    .brmnote-action-btn:hover {
        transform: translateY(-1px);
    }

    /* Empty State Card */
    .brmnote-empty-card {
        background: #ffffff !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 48px 24px !important;
        text-align: center !important;
        margin: 20px 0 !important;
    }
    .brmnote-empty-icon {
        width: 56px !important;
        height: 56px !important;
        border-radius: 12px !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        font-size: 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 12px !important;
    }

    /* 15.6" Responsiveness & Media Queries */
    @media (max-width: 1200px) {
        .brmnote-grid-headers,
        .brmnote-card-grid {
            grid-template-columns: minmax(0, 2fr) minmax(0, 2fr) minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        }
    }
    @media (max-width: 991px) {
        .brmnote-stream-header-strip {
            display: none !important;
        }
        .brmnote-card-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .brmnote-subcard-actions {
            justify-content: flex-start !important;
        }
        .brmnote-subcard-status {
            align-items: flex-start !important;
        }
    }

    #brmModal .modal-dialog { max-width:750px; }
    #brmPreviewModal .modal-dialog { max-width:700px; }

    /* ─── Modal & Bench RM Variable Tokens Bar ─── */
    .brmnote-modal-header {
        background: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 16px 20px !important;
    }
    .brmnote-tokens-container {
        background: #f8fafc !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        margin-bottom: 12px !important;
    }
    .brmnote-tokens-label {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin-bottom: 6px !important;
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
    }
    .brmnote-token-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        font-family: monospace, sans-serif !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #0f172a !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        margin-bottom: 4px !important;
    }
    .brmnote-token-btn:hover {
        background: #eff6ff !important;
        border-color: #009ef7 !important;
        color: #009ef7 !important;
        transform: translateY(-1px);
    }
    .brmnote-preview-body {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 18px !important;
        min-height: 180px !important;
        max-height: 400px !important;
        overflow-y: auto !important;
        font-size: 13px !important;
        line-height: 1.6 !important;
        color: #1e293b !important;
    }
    .brmnote-preview-body p {
        margin-bottom: 10px !important;
    }
    .brmnote-token-highlight {
        background: #fef3c7 !important;
        color: #92400e !important;
        font-weight: 600 !important;
        padding: 1px 5px !important;
        border-radius: 4px !important;
        border: 1px solid #fde68a !important;
        font-family: monospace !important;
    }

    /* Super Admin Standard Pagination Integration */
    #brmnotePaginationContainer .sa-pagination-bar {
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        margin-top: 10px !important;
    }
</style>
@endpush

@section('content')
@php
    // Deterministic palette mapping for avatar initials
    $getPalette = function($char) {
        $c = strtoupper($char);
        if (in_array($c, ['A', 'I', 'Q', 'Y'])) return ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'];
        if (in_array($c, ['B', 'J', 'R', 'Z'])) return ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'];
        if (in_array($c, ['C', 'K', 'S']))      return ['bg' => '#fdf4ff', 'color' => '#c026d3', 'border' => '#f5d0fe'];
        if (in_array($c, ['D', 'L', 'T']))      return ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'];
        if (in_array($c, ['E', 'M', 'U']))      return ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'];
        if (in_array($c, ['F', 'N', 'V']))      return ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'];
        if (in_array($c, ['G', 'O', 'W']))      return ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'];
        if (in_array($c, ['H', 'P', 'X']))      return ['bg' => '#e0f2fe', 'color' => '#0284c7', 'border' => '#bae6fd'];
        return ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0'];
    };
@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3 brmnote-wrapper">

            <!--begin::Tier 1: Top Header Toolbar Card-->
            <div class="brmnote-header-card">
                <div class="brmnote-header-left">
                    <div class="brmnote-header-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div>
                        <div class="brmnote-header-title">
                            <span>Relationship Manager Note Templates</span>
                            <span class="brmnote-kpi-chip total" id="kpiTotalChip">
                                <i class="fas fa-layer-group text-muted fs-8"></i>
                                <span id="kpiTotalCount">{{ $totalCount ?? 0 }}</span> Total
                            </span>
                            <span class="brmnote-kpi-chip active" id="kpiActiveChip">
                                <i class="fas fa-check-circle text-success fs-8"></i>
                                <span id="kpiActiveCount">{{ $activeCount ?? 0 }}</span> Active
                            </span>
                            <span class="brmnote-kpi-chip inactive" id="kpiInactiveChip">
                                <i class="fas fa-pause-circle text-danger fs-8"></i>
                                <span id="kpiInactiveCount">{{ $inactiveCount ?? 0 }}</span> Inactive
                            </span>
                            <span class="brmnote-kpi-chip scope">
                                <i class="fas fa-globe fs-8"></i> Global Bench ATS Scope
                            </span>
                        </div>
                        <div class="brmnote-header-sub">
                            Standardized evaluation, commercial margin, and deployment readiness templates distributed across all Service Companies for Bench Talent.
                        </div>
                    </div>
                </div>

                <div class="brmnote-header-right">
                    <!-- Search Sub-Card -->
                    <div class="brmnote-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="brmnoteSearchInput" placeholder="Search RM templates by title or content..." autocomplete="off">
                        <button type="button" class="brmnote-search-clear" id="brmnoteSearchClear" title="Clear search">✕</button>
                    </div>

                    <!-- Primary Action Button -->
                    <button type="button" class="btn btn-primary brmnote-add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add RM Template
                    </button>
                </div>
            </div>
            <!--end::Tier 1: Top Header Toolbar Card-->

            <!--begin::Tier 2: Sub-Tabs Filter Card-->
            <div class="brmnote-tabs-card">
                <div class="brmnote-tabs-group">
                    <button type="button" class="brmnote-tab-btn active" data-filter="all" onclick="filterTemplates('all', this)">
                        <i class="fas fa-list-ul fs-8"></i> All Templates
                        <span class="brmnote-tab-badge" id="tabCountAll">{{ $totalCount ?? 0 }}</span>
                    </button>
                    <button type="button" class="brmnote-tab-btn" data-filter="active" onclick="filterTemplates('active', this)">
                        <i class="fas fa-check-circle text-success fs-8"></i> Active Templates
                        <span class="brmnote-tab-badge badge-active" id="tabCountActive">{{ $activeCount ?? 0 }}</span>
                    </button>
                    <button type="button" class="brmnote-tab-btn" data-filter="inactive" onclick="filterTemplates('inactive', this)">
                        <i class="fas fa-pause-circle text-danger fs-8"></i> Inactive Templates
                        <span class="brmnote-tab-badge badge-inactive" id="tabCountInactive">{{ $inactiveCount ?? 0 }}</span>
                    </button>
                </div>

                <div class="brmnote-tab-right-info">
                    <span class="text-muted"><i class="fas fa-info-circle me-1"></i> Active templates are immediately accessible to Relationship Managers evaluating bench candidates</span>
                </div>
            </div>
            <!--end::Tier 2: Sub-Tabs Filter Card-->

            <!--begin::Tier 3: Primary RM Note Card Stream-->
            <div id="brmnoteStreamContainer" class="brmnote-stream-container">
                <!-- Stream Column Header Strip -->
                <div class="brmnote-stream-header-strip" id="brmnoteHeaderStrip" style="{{ $templates->isEmpty() ? 'display: none;' : '' }}">
                    <div class="brmnote-grid-headers">
                        <div><span class="brmnote-col-label">TEMPLATE & IDENTIFIER</span></div>
                        <div><span class="brmnote-col-label">EVALUATION RUBRIC & SNIPPET</span></div>
                        <div><span class="brmnote-col-label">TELEMETRY & DATES</span></div>
                        <div class="text-center"><span class="brmnote-col-label">BENCH ATS STATUS</span></div>
                        <div class="text-end"><span class="brmnote-col-label">ACTIONS</span></div>
                    </div>
                </div>

                <div id="brmnoteCardsList">
                    @include('super-admin.rm-note.card_items', ['templates' => $templates])
                </div>

                <!-- No Search Results Found Card -->
                <div class="brmnote-empty-card" id="brmnoteNoSearchCard" style="display: none;">
                    <div class="brmnote-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="fw-bold text-gray-800 mb-1">No Matching Templates</h5>
                    <p class="text-muted fs-7 mb-3">No templates matched your search query or filter selection.</p>
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i> Reset Filters
                    </button>
                </div>

                <!-- Pagination Bar Container (Super Admin Standard) -->
                <div id="brmnotePaginationContainer" class="mt-3">
                    {!! $paginationHtml ?? '' !!}
                </div>
            </div>
            <!--end::Tier 3: Primary RM Note Card Stream-->
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="brmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header brmnote-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="brmnote-header-icon" id="brmModalIcon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="brmModalTitle">Add Relationship Manager Template</h5>
                        <span class="text-muted fs-8">Configure global Bench Candidate evaluation rubrics distributed to all Service Companies</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <form id="brmForm">
                    @csrf
                    <input type="hidden" id="brm_id" name="id" value="">

                    <!-- Title Input with Character Counter -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-bold text-dark fs-7 mb-0">
                                Template Title <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted fs-8" id="titleCharCount">0 / 255</span>
                        </div>
                        <input type="text" class="form-control form-control-solid" id="brm_title" name="title" placeholder="e.g. Bench Availability & Deployment Readiness" maxlength="255" autocomplete="off">
                        <span class="text-muted fs-9 mt-1 d-block">Descriptive name displayed to Relationship Managers evaluating bench talent</span>
                    </div>

                    <!-- Bench RM Variable Tokens Toolbar -->
                    <div class="brmnote-tokens-container">
                        <div class="brmnote-tokens-label">
                            <i class="fas fa-bolt text-warning fs-9"></i> Quick Insert Bench RM Variables (Click to insert at cursor):
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[talent_name]')" title="Insert Talent Full Name">
                                <i class="fas fa-user fs-9 text-primary"></i> [talent_name]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[primary_skills]')" title="Insert Core Technical Skills">
                                <i class="fas fa-code fs-9 text-info"></i> [primary_skills]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[total_experience]')" title="Insert Total Years of Experience">
                                <i class="fas fa-briefcase fs-9 text-success"></i> [total_experience]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[current_bench_tenure]')" title="Insert Active Days on Bench">
                                <i class="fas fa-hourglass-half fs-9 text-warning"></i> [current_bench_tenure]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[availability_status]')" title="Insert Availability / Notice Period">
                                <i class="fas fa-clock fs-9 text-danger"></i> [availability_status]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[desired_bill_rate]')" title="Insert Proposed Client Bill Rate">
                                <i class="fas fa-hand-holding-usd fs-9 text-primary"></i> [desired_bill_rate]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[candidate_pay_rate]')" title="Insert Candidate Base Pay Rate">
                                <i class="fas fa-money-bill-wave fs-9 text-success"></i> [candidate_pay_rate]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[work_authorization]')" title="Insert Work Authorization / Visa">
                                <i class="fas fa-passport fs-9 text-info"></i> [work_authorization]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[preferred_location]')" title="Insert Remote / Hybrid / Relocation Preference">
                                <i class="fas fa-map-marker-alt fs-9 text-danger"></i> [preferred_location]
                            </button>
                            <button type="button" class="brmnote-token-btn" onclick="insertToken('[rm_evaluator_name]')" title="Insert RM Evaluator Name">
                                <i class="fas fa-id-badge fs-9 text-secondary"></i> [rm_evaluator_name]
                            </button>
                        </div>
                    </div>

                    <!-- Content Textarea for CKEditor -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                            Template Content / Evaluation Body <span class="text-danger">*</span>
                        </label>
                        <textarea id="brm_content" name="content" rows="6"></textarea>
                    </div>

                    <!-- Active Switch -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light-primary border border-primary border-dashed">
                        <div>
                            <div class="fw-bold text-dark fs-7">Active Status</div>
                            <div class="text-muted fs-8">Make this template immediately visible to all Service Companies for bench evaluations</div>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" id="brm_status" name="status" checked>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light px-5 py-3">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="brmSaveBtn" onclick="saveTemplate()">
                    <i class="fas fa-check me-1"></i> <span id="brmSaveBtnText">Save Template</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Full Content Preview Modal -->
<div class="modal fade" id="brmPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header brmnote-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="brmnote-header-icon" style="background: #ecfdf5; border-color: #a7f3d0; color: #059669;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="previewModalTitle">Template Preview</h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="brmnote-id-pill" id="previewModalId">#—</span>
                            <span class="brmnote-status-badge active" id="previewModalStatus">Active</span>
                            <span class="text-muted fs-8" id="previewModalStats">—</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <div class="mb-2 text-muted fs-8 fw-semibold text-uppercase letter-spacing-1">
                    <i class="fas fa-align-left me-1"></i> Formatted Note Content
                </div>
                <div class="brmnote-preview-body" id="previewModalContent">
                    Loading content...
                </div>
            </div>
            <div class="modal-footer bg-light px-5 py-3">
                <button type="button" class="btn btn-sm btn-light-primary" id="previewCopyBtn" onclick="copyCurrentPreview()">
                    <i class="fas fa-copy me-1"></i> Copy Content
                </button>
                <button type="button" class="btn btn-sm btn-light-warning" id="previewEditBtn" onclick="editFromPreview()">
                    <i class="fas fa-edit me-1"></i> Edit Template
                </button>
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
var _brmEditor = null;
var _brmActiveFilter = 'all';
var _brmCurrentPage = {{ $currentPage ?? 1 }};
var _brmPerPage = {{ $perPage ?? 10 }};
var _brmSearchTimer = null;
var _brmActiveAjax = null;
var _clientTemplateCache = {};
var _inFlightToggles = {};
var _inFlightDuplicates = {};
var _inFlightDeletes = {};
var _inFlightDataFetches = {};
var _lastActiveTimestamp = Date.now();

// ─── Enterprise Client Database: JeroBenchRmNotesDB (IndexedDB v1) ───
class JeroBenchRmNotesDB {
    static DB_NAME = 'JeroBenchRmNotesDB';
    static DB_VERSION = 1;
    static STORE_NAME = 'bench_rm_notes_store';
    static db = null;

    static init() {
        if (!('indexedDB' in window)) return Promise.resolve(null);
        return new Promise(function(resolve) {
            try {
                let req = window.indexedDB.open(JeroBenchRmNotesDB.DB_NAME, JeroBenchRmNotesDB.DB_VERSION);
                req.onupgradeneeded = function(e) {
                    let db = e.target.result;
                    if (!db.objectStoreNames.contains(JeroBenchRmNotesDB.STORE_NAME)) {
                        db.createObjectStore(JeroBenchRmNotesDB.STORE_NAME, { keyPath: 'key' });
                    }
                };
                req.onsuccess = function(e) {
                    JeroBenchRmNotesDB.db = e.target.result;
                    resolve(JeroBenchRmNotesDB.db);
                };
                req.onerror = function() { resolve(null); };
            } catch (err) {
                resolve(null);
            }
        });
    }

    static async get(key) {
        if (!this.db) await this.init();
        if (!this.db) return null;
        return new Promise(function(resolve) {
            try {
                let tx = JeroBenchRmNotesDB.db.transaction(JeroBenchRmNotesDB.STORE_NAME, 'readonly');
                let req = tx.objectStore(JeroBenchRmNotesDB.STORE_NAME).get(key);
                req.onsuccess = function(e) {
                    let record = e.target.result;
                    if (record && (Date.now() - record.timestamp < 300000)) { // 5-min TTL
                        resolve(record.data);
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = function() { resolve(null); };
            } catch (e) {
                resolve(null);
            }
        });
    }

    static async set(key, data) {
        if (!this.db) await this.init();
        if (!this.db) return false;
        return new Promise(function(resolve) {
            try {
                let tx = JeroBenchRmNotesDB.db.transaction(JeroBenchRmNotesDB.STORE_NAME, 'readwrite');
                tx.objectStore(JeroBenchRmNotesDB.STORE_NAME).put({
                    key: key,
                    data: data,
                    timestamp: Date.now()
                });
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) {
                resolve(false);
            }
        });
    }

    static async delete(key) {
        if (!this.db) await this.init();
        if (!this.db) return false;
        return new Promise(function(resolve) {
            try {
                let tx = JeroBenchRmNotesDB.db.transaction(JeroBenchRmNotesDB.STORE_NAME, 'readwrite');
                tx.objectStore(JeroBenchRmNotesDB.STORE_NAME).delete(key);
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) {
                resolve(false);
            }
        });
    }

    static async clear() {
        if (!this.db) await this.init();
        if (!this.db) return false;
        return new Promise(function(resolve) {
            try {
                let tx = JeroBenchRmNotesDB.db.transaction(JeroBenchRmNotesDB.STORE_NAME, 'readwrite');
                tx.objectStore(JeroBenchRmNotesDB.STORE_NAME).clear();
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) {
                resolve(false);
            }
        });
    }
}

// Safely initialize IndexedDB
JeroBenchRmNotesDB.init();

// ─── Real-Time Multi-Tab Synchronization (BroadcastChannel) ───
const brmSyncChannel = (typeof window.BroadcastChannel !== 'undefined')
    ? new BroadcastChannel('jero_superadmin_bench_rm_notes_sync')
    : null;

function broadcastMutation(action, id, extra) {
    if (brmSyncChannel) {
        try {
            brmSyncChannel.postMessage(Object.assign({
                action: action,
                id: id || null,
                timestamp: Date.now()
            }, extra || {}));
        } catch (e) {
            console.warn('[BenchRmNotesSync] BroadcastChannel error:', e);
        }
    }
}

function invalidateClientCaches(broadcast, action, id, extra) {
    _clientTemplateCache = {};
    if (id) {
        JeroBenchRmNotesDB.delete('template_' + id);
    } else {
        JeroBenchRmNotesDB.clear();
    }
    if (broadcast) {
        broadcastMutation(action || 'mutation', id, extra);
    }
}

if (brmSyncChannel) {
    brmSyncChannel.onmessage = function(e) {
        if (!e.data) return;
        var msg = e.data;
        if (msg.action === 'status_toggle' && msg.id) {
            delete _clientTemplateCache[msg.id];
            JeroBenchRmNotesDB.delete('template_' + msg.id);
            var isNowActive = (msg.status == 1);
            var $card = $('#brmnote-card-' + msg.id);
            if ($card.length > 0) {
                $card.attr('data-status', isNowActive ? 'active' : 'inactive');
                var $switch = $('#switch-' + msg.id);
                $switch.prop('checked', isNowActive);
                var $badge = $('#status-badge-' + msg.id);
                $badge.removeClass('active inactive')
                      .addClass(isNowActive ? 'active' : 'inactive')
                      .text(msg.label || (isNowActive ? 'Active' : 'Inactive'));
                updateKpiCounters();
                applyFilters();
            }
        } else if (msg.action === 'delete' && msg.id) {
            delete _clientTemplateCache[msg.id];
            JeroBenchRmNotesDB.delete('template_' + msg.id);
            var $card = $('#brmnote-card-' + msg.id);
            if ($card.length > 0) {
                $card.slideUp(250, function() {
                    $(this).remove();
                    updateKpiCounters();
                    applyFilters();
                });
            }
        } else if (msg.action === 'mutation') {
            invalidateClientCaches(false);
        }
    };
}

// Real-Time WebSockets Synchronization via Laravel Reverb / Echo
if (typeof window.Echo !== 'undefined') {
    try {
        window.Echo.channel('superadmin.bench-rm-notes')
            .listen('.bench-rm-note.updated', function() {
                invalidateClientCaches(false);
            });
    } catch (echoErr) {
        console.warn('[BenchRmNotesEcho] Listener notice:', echoErr);
    }
}

// Zero Stale Data: Automatic re-validation on tab focus if data is older than 60 seconds
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        if (Date.now() - _lastActiveTimestamp > 60000) {
            invalidateClientCaches(false);
        }
        _lastActiveTimestamp = Date.now();
    }
});

// Client-First Idempotency UUID Generator (bounded to 64 chars)
function generateIdempotencyKey() {
    if (window.crypto && window.crypto.randomUUID) {
        return window.crypto.randomUUID().substring(0, 64);
    }
    return ('brmnote_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11)).substring(0, 64);
}

// Multi-Tier Client Cache for Single Template Lookups (L1 RAM -> L2 IndexedDB -> L3 Network)
async function fetchTemplateData(id, callback) {
    var now = Date.now();
    // 1. L1 RAM Cache Check (0ms transition, 0 server strain)
    if (_clientTemplateCache[id] && (now - _clientTemplateCache[id].timestamp < 60000)) {
        callback(_clientTemplateCache[id].data);
        return;
    }

    // 2. L2 IndexedDB Check (<5ms transition, 0 server strain)
    try {
        var cachedDb = await JeroBenchRmNotesDB.get('template_' + id);
        if (cachedDb) {
            _clientTemplateCache[id] = {
                data: cachedDb,
                timestamp: Date.now()
            };
            callback(cachedDb);
            return;
        }
    } catch (e) {}

    // In-Flight Request Deduplication: join existing in-flight request if already fetching
    if (_inFlightDataFetches[id]) {
        _inFlightDataFetches[id].push(callback);
        return;
    }
    _inFlightDataFetches[id] = [callback];

    // 3. Network Fetch
    $.ajax({
        url: '{{ route("superadmin.rm-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(res) {
            if (res.status === 'success' && res.data) {
                // Populate L1 RAM and L2 IndexedDB
                _clientTemplateCache[id] = {
                    data: res.data,
                    timestamp: Date.now()
                };
                JeroBenchRmNotesDB.set('template_' + id, res.data);
                var callbacks = _inFlightDataFetches[id] || [];
                callbacks.forEach(function(cb) {
                    try { cb(res.data); } catch (cbErr) {}
                });
            } else {
                toastr.error(res.message || 'Failed to load template data.');
            }
        },
        error: function() {
            toastr.error('Network error loading template.');
        },
        complete: function() {
            delete _inFlightDataFetches[id];
        }
    });
}

// ─── Super Admin Data List Pagination Engine (Client-First + CQRS) ───
function fetchTemplatesAjax(page, bypassCache) {
    page = page || _brmCurrentPage || 1;
    _brmCurrentPage = page;
    var search = $.trim($('#brmnoteSearchInput').val());
    var status = _brmActiveFilter;
    var cacheKey = 'brm_list_' + status + '_' + search.toLowerCase() + '_p' + page + '_pp' + _brmPerPage;

    // 1. Check L1 RAM Cache (0ms, 0 server strain)
    if (!bypassCache && _clientTemplateCache[cacheKey] && (Date.now() - _clientTemplateCache[cacheKey].timestamp < 60000)) {
        renderTemplatesResponse(_clientTemplateCache[cacheKey].data);
        return;
    }

    // 2. Check L2 IndexedDB (<5ms, 0 server strain)
    if (!bypassCache) {
        JeroBenchRmNotesDB.get(cacheKey).then(function(cachedDb) {
            if (cachedDb) {
                _clientTemplateCache[cacheKey] = {
                    data: cachedDb,
                    timestamp: Date.now()
                };
                renderTemplatesResponse(cachedDb);
                return;
            }
            executeNetworkFetch();
        }).catch(function() {
            executeNetworkFetch();
        });
    } else {
        executeNetworkFetch();
    }

    function executeNetworkFetch() {
        if (_brmActiveAjax) {
            try { _brmActiveAjax.abort(); } catch (e) {}
            _brmActiveAjax = null;
        }

        $('#brmnoteCardsList').css('opacity', '0.5');

        _brmActiveAjax = $.ajax({
            url: '{{ route("superadmin.rm-note.index") }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                page: page,
                per_page: _brmPerPage,
                search: search,
                status: status
            },
            success: function(res) {
                $('#brmnoteCardsList').css('opacity', '1');
                if (res.status === 'success') {
                    _clientTemplateCache[cacheKey] = {
                        data: res,
                        timestamp: Date.now()
                    };
                    JeroBenchRmNotesDB.set(cacheKey, res);
                    renderTemplatesResponse(res);
                } else {
                    toastr.error(res.message || 'Failed to load templates.');
                }
            },
            error: function(xhr, statusText) {
                if (statusText === 'abort') return;
                $('#brmnoteCardsList').css('opacity', '1');
                toastr.error('Network error loading templates.');
            },
            complete: function() {
                _brmActiveAjax = null;
            }
        });
    }
}

function renderTemplatesResponse(res) {
    var html = res.html || '';
    var pagination = res.pagination || '';
    var total = (typeof res.total !== 'undefined') ? res.total : (res.data ? res.data.totalCount : 0);

    $('#brmnoteCardsList').html(html);
    $('#brmnotePaginationContainer').html(pagination);

    var searchQuery = $.trim($('#brmnoteSearchInput').val());
    if (total === 0) {
        if (searchQuery) {
            $('#brmnoteNoSearchCard').show();
            $('#brmnoteHeaderStrip').hide();
        } else {
            $('#brmnoteNoSearchCard').hide();
            $('#brmnoteHeaderStrip').hide();
        }
    } else {
        $('#brmnoteNoSearchCard').hide();
        $('#brmnoteHeaderStrip').show();
    }

    if (res.counts) {
        updateKpiCounters(res.counts);
    } else if (res.data) {
        updateKpiCounters({
            total: res.data.totalCount,
            active: res.data.activeCount,
            inactive: res.data.inactiveCount
        });
    }

    if (typeof $('[data-bs-toggle="tooltip"]').tooltip === 'function') {
        $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover', placement: 'top' });
    }
}

// ─── Super Admin Standard Pagination Click Handling ───
$(document).on('click', '#brmnotePaginationContainer .page-link', function(e) {
    e.preventDefault();
    var page = $(this).data('page');
    if (!page || $(this).hasClass('disabled') || $(this).hasClass('dots') || $(this).hasClass('pagi-disabled') || $(this).hasClass('active') || $(this).hasClass('pagi-active')) {
        return;
    }
    fetchTemplatesAjax(parseInt(page, 10));
    var streamElem = document.getElementById('brmnoteStreamContainer');
    if (streamElem) {
        streamElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

// Tier 2: Sub-tab filtering
function filterTemplates(type, btn) {
    _brmActiveFilter = type;
    $('.brmnote-tab-btn').removeClass('active');
    $(btn).addClass('active');
    fetchTemplatesAjax(1);
}

function resetFilters() {
    $('#brmnoteSearchInput').val('');
    $('#brmnoteSearchClear').hide();
    _brmActiveFilter = 'all';
    $('.brmnote-tab-btn').removeClass('active');
    $('.brmnote-tab-btn[data-filter="all"]').addClass('active');
    fetchTemplatesAjax(1);
}

function updateKpiCounters(counts) {
    if (counts) {
        $('#kpiTotalCount').text(counts.total);
        $('#kpiActiveCount').text(counts.active);
        $('#kpiInactiveCount').text(counts.inactive);

        $('#tabCountAll').text(counts.total);
        $('#tabCountActive').text(counts.active);
        $('#tabCountInactive').text(counts.inactive);
    }
}

// Tier 3: Instant 1-Click AJAX Status Toggle with Idempotency & In-Flight Fencing
function toggleTemplateStatus(id, switchEl) {
    var $switch = $(switchEl);
    if ($switch.prop('disabled') || _inFlightToggles[id]) return;
    
    $switch.prop('disabled', true);
    _inFlightToggles[id] = true;
    var idempotencyKey = generateIdempotencyKey();

    $.ajax({
        url: '{{ route("superadmin.rm-note.toggleStatus", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        type: 'POST',
        headers: {
            'X-Idempotency-Key': idempotencyKey
        },
        data: {
            _token: '{{ csrf_token() }}',
            _idempotency_key: idempotencyKey
        },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message);
                var isNowActive = (res.template_status == 1);
                var $card = $('#brmnote-card-' + id);
                $card.attr('data-status', isNowActive ? 'active' : 'inactive');

                var $badge = $('#status-badge-' + id);
                $badge.removeClass('active inactive')
                      .addClass(isNowActive ? 'active' : 'inactive')
                      .text(res.status_label);

                delete _clientTemplateCache[id];
                JeroBenchRmNotesDB.delete('template_' + id);
                broadcastMutation('status_toggle', id, { status: res.template_status, label: res.status_label });
                updateKpiCounters();
                applyFilters();
            } else {
                toastr.error(res.message || 'Failed to update status.');
                $switch.prop('checked', !$switch.prop('checked'));
            }
        },
        error: function() {
            toastr.error('Network error updating template status.');
            $switch.prop('checked', !$switch.prop('checked'));
        },
        complete: function() {
            delete _inFlightToggles[id];
            $switch.prop('disabled', false);
        }
    });
}

// Tier 3: Instant 1-Click Duplicate with Single-Flight Fencing & Idempotency
function duplicateTemplate(id) {
    if (_inFlightDuplicates[id]) return;

    Swal.fire({
        title: 'Duplicate RM Template?',
        text: 'This will create an inactive draft copy of this Relationship Manager template.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009ef7',
        cancelButtonColor: '#7e8299',
        confirmButtonText: 'Yes, duplicate it'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        _inFlightDuplicates[id] = true;
        var idempotencyKey = generateIdempotencyKey();

        $.ajax({
            url: '{{ route("superadmin.rm-note.duplicate", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            type: 'POST',
            headers: {
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                _token: '{{ csrf_token() }}',
                _idempotency_key: idempotencyKey
            },
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    invalidateClientCaches(true, 'mutation');
                    fetchTemplatesAjax(1, true);
                } else {
                    toastr.error(res.message || 'Duplication failed.');
                }
            },
            error: function() {
                toastr.error('Failed to duplicate template.');
            },
            complete: function() {
                delete _inFlightDuplicates[id];
            }
        });
    });
}

// Tier 3: 1-Click Clipboard Copying with Fast RAM Cache
function copyTemplateContent(id) {
    fetchTemplateData(id, function(d) {
        var text = $('<div>').html(d.content).text().trim();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                toastr.success('Template content copied to clipboard!');
            });
        } else {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            toastr.success('Template content copied to clipboard!');
        }
    });
}

// Tier 1: Debounced Search Input & Clear
$(document).ready(function() {
    $('#brmnoteSearchInput').on('input', function() {
        var val = $(this).val();
        if (val.length > 0) {
            $('#brmnoteSearchClear').show();
        } else {
            $('#brmnoteSearchClear').hide();
        }

        clearTimeout(_brmSearchTimer);
        _brmSearchTimer = setTimeout(function() {
            fetchTemplatesAjax(1);
        }, 300);
    });

    $('#brmnoteSearchClear').on('click', function() {
        $('#brmnoteSearchInput').val('');
        $(this).hide();
        fetchTemplatesAjax(1);
    });
});

var _activePreviewId = null;

// Character counter for title input
$('#brm_title').on('input', function() {
    var len = $(this).val().length;
    $('#titleCharCount').text(len + ' / 255');
});

// Insert Bench RM Variable Token at CKEditor cursor position
function insertToken(token) {
    if (_brmEditor) {
        _brmEditor.insertText(token);
        _brmEditor.focus();
    } else {
        toastr.info('Editor is still mounting, please try again in a second.');
    }
}

// Open Add Modal
function openAddModal() {
    $('#brmModalTitle').text('Add Relationship Manager Template');
    $('#brmModalIcon i').attr('class', 'fas fa-plus');
    $('#brm_id').val('');
    $('#brm_title').val('');
    $('#titleCharCount').text('0 / 255');
    $('#brm_status').prop('checked', true);
    $('#brmSaveBtnText').text('Save Template');

    $('#brmModal').modal('show');
}

// Open Edit Modal with Fast RAM Cache
function openEditModal(id) {
    $('#brmModalTitle').text('Edit Relationship Manager Template');
    $('#brmModalIcon i').attr('class', 'fas fa-edit');
    $('#brmSaveBtnText').text('Update Template');

    fetchTemplateData(id, function(d) {
        $('#brm_id').val(d.id);
        $('#brm_title').val(d.title);
        $('#titleCharCount').text((d.title || '').length + ' / 255');
        $('#brm_status').prop('checked', d.status == 1);

        $('#brmModal').modal('show');

        // Set editor content safely
        if (_brmEditor && _brmEditor.status === 'ready') {
            _brmEditor.setData(d.content || '');
        } else {
            $('#brmModal').data('pendingContent', d.content || '');
        }
    });
}

// Full Content Preview Modal with Fast RAM Cache
function openPreviewModal(id) {
    _activePreviewId = id;
    $('#previewModalTitle').text('Loading...');
    $('#previewModalId').text('#' + id);
    $('#previewModalContent').html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading formatted template...</div>');
    $('#brmPreviewModal').modal('show');

    fetchTemplateData(id, function(d) {
        $('#previewModalTitle').text(d.title);
        $('#previewModalId').text('#' + d.id);
        
        var isAct = (d.status == 1);
        $('#previewModalStatus').removeClass('active inactive')
                               .addClass(isAct ? 'active' : 'inactive')
                               .text(isAct ? 'Active' : 'Inactive');

        var raw = d.content || '';
        // Highlight Bench RM variable tokens like [talent_name] in preview
        var formatted = raw.replace(/(\[[a-zA-Z0-9_]+\])/g, '<span class="brmnote-token-highlight">$1</span>');
        $('#previewModalContent').html(formatted);

        var plain = $('<div>').html(raw).text().trim();
        var words = plain !== '' ? plain.split(/\s+/).length : 0;
        $('#previewModalStats').text(words + ' Words • ' + plain.length + ' Chars');
    });
}

function copyCurrentPreview() {
    if (_activePreviewId) {
        var $btn = $('#previewCopyBtn');
        var origHtml = $btn.html();
        copyTemplateContent(_activePreviewId);
        $btn.html('<i class="fas fa-check text-success me-1"></i> Copied!');
        setTimeout(function() {
            $btn.html(origHtml);
        }, 1500);
    }
}

function editFromPreview() {
    if (_activePreviewId) {
        var id = _activePreviewId;
        $('#brmPreviewModal').modal('hide');
        setTimeout(function() {
            openEditModal(id);
        }, 300);
    }
}

// Hardened CKEditor Lifecycle
$('#brmModal').on('shown.bs.modal', function() {
    if (_brmEditor) {
        try { _brmEditor.destroy(); } catch(e){}
        _brmEditor = null;
    }

    _brmEditor = CKEDITOR.replace('brm_content', {
        height: 220,
        removePlugins: 'elementspath',
        resize_enabled: false,
        toolbar: [
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'styles', items: [ 'Format', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize' ] }
        ]
    });

    _brmEditor.on('instanceReady', function() {
        var pending = $('#brmModal').data('pendingContent');
        if (typeof pending !== 'undefined' && pending !== null) {
            _brmEditor.setData(pending);
            $('#brmModal').removeData('pendingContent');
        } else if (!$('#brm_id').val()) {
            _brmEditor.setData('');
        }
    });
});

$('#brmModal').on('hidden.bs.modal', function() {
    if (_brmEditor) {
        try { _brmEditor.destroy(); } catch(e){}
        _brmEditor = null;
    }
    $('#brm_id').val('');
    $('#brmModal').removeData('pendingContent');
    $('#brmSaveBtn').prop('disabled', false).removeData('submitting');
});

// Save Template with Single-Submit Guard, Idempotency & FormRequest validation feedback
function saveTemplate() {
    var $btn = $('#brmSaveBtn');
    if ($btn.data('submitting') === true || $btn.prop('disabled') === true) {
        return;
    }

    if (_brmEditor) {
        _brmEditor.updateElement();
    }

    var id      = $('#brm_id').val();
    var title   = $.trim($('#brm_title').val());
    var content = _brmEditor ? _brmEditor.getData() : $.trim($('#brm_content').val());
    var status  = $('#brm_status').is(':checked') ? 1 : 0;

    if (!title) {
        toastr.warning('Please provide a template title.');
        $('#brm_title').focus();
        return;
    }

    var strippedContent = $('<div>').html(content).text().trim();
    if (!strippedContent && !/<img|<table|<hr|<iframe/i.test(content)) {
        toastr.warning('The template content cannot be empty.');
        return;
    }

    var url = id
        ? '{{ route("superadmin.rm-note.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
        : '{{ route("superadmin.rm-note.store") }}';
    var method = id ? 'PUT' : 'POST';

    var idempotencyKey = generateIdempotencyKey();

    $btn.data('submitting', true).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');

    $.ajax({
        url: url,
        type: method,
        headers: {
            'X-Idempotency-Key': idempotencyKey
        },
        data: {
            _token: '{{ csrf_token() }}',
            _idempotency_key: idempotencyKey,
            title: title,
            content: content,
            status: status
        },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Saved successfully.');
                if (id) {
                    delete _clientTemplateCache[id];
                    JeroBenchRmNotesDB.delete('template_' + id);
                }
                invalidateClientCaches(true, 'mutation');
                $('#brmModal').modal('hide');
                fetchTemplatesAjax(id ? _brmCurrentPage : 1, true);
            } else {
                toastr.error(res.message || 'Failed to save template.');
            }
        },
        error: function(xhr) {
            var msg = 'Failed to save template.';
            try {
                var r = JSON.parse(xhr.responseText);
                if (r.errors) {
                    var errArr = Object.values(r.errors);
                    if (errArr.length > 0 && errArr[0].length > 0) msg = errArr[0][0];
                } else if (r.message) {
                    msg = r.message;
                }
            } catch(e){}
            toastr.error(msg);
        },
        complete: function() {
            $btn.prop('disabled', false).removeData('submitting').html('<i class="fas fa-check me-1"></i> <span id="brmSaveBtnText">' + (id ? 'Update Template' : 'Save Template') + '</span>');
        }
    });
}

// Delete Template using SweetAlert2 with In-Flight Fencing & Idempotency
function deleteTemplate(id) {
    if (_inFlightDeletes[id]) return;

    Swal.fire({
        title: 'Delete Relationship Manager Template?',
        text: 'This action will permanently delete this global bench evaluation template.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f1416c',
        cancelButtonColor: '#7e8299',
        confirmButtonText: 'Yes, delete it'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        _inFlightDeletes[id] = true;
        var idempotencyKey = generateIdempotencyKey();

        $.ajax({
            url: '{{ route("superadmin.rm-note.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            type: 'DELETE',
            headers: {
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                _token: '{{ csrf_token() }}',
                _idempotency_key: idempotencyKey
            },
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message || 'Template deleted.');
                    delete _clientTemplateCache[id];
                    JeroBenchRmNotesDB.delete('template_' + id);
                    invalidateClientCaches(true, 'delete', id);
                    $('#brmnote-card-' + id).fadeOut(250, function() {
                        $(this).remove();
                        fetchTemplatesAjax(_brmCurrentPage, true);
                    });
                } else {
                    toastr.error(res.message || 'Failed to delete template.');
                }
            },
            error: function() {
                toastr.error('Failed to delete template.');
            },
            complete: function() {
                delete _inFlightDeletes[id];
            }
        });
    });
}
</script>
@endpush
