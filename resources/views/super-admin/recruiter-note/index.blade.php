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
    .rnote-wrapper {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* ─── Tier 1: Top Header Toolbar Card ─── */
    .rnote-header-card {
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
    .rnote-header-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }
    .rnote-header-icon {
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
    .rnote-header-title {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .rnote-header-sub {
        font-size: 11.5px !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }
    .rnote-kpi-chip {
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
    .rnote-kpi-chip.total {
        background: #f1f5f9 !important;
        color: #334155 !important;
        border-color: #e2e8f0 !important;
    }
    .rnote-kpi-chip.active {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border-color: #a7f3d0 !important;
    }
    .rnote-kpi-chip.inactive {
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border-color: #fecaca !important;
    }
    .rnote-kpi-chip.scope {
        background: #eff6ff !important;
        color: #2563eb !important;
        border-color: #bfdbfe !important;
    }

    .rnote-header-right {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }
    .rnote-search-subcard {
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
    .rnote-search-subcard:focus-within {
        background: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .rnote-search-subcard i.fa-search {
        color: #94a3b8 !important;
        font-size: 12px !important;
        margin-right: 8px !important;
    }
    .rnote-search-subcard input {
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-size: 12px !important;
        font-family: 'Poppins', sans-serif !important;
        color: #0f172a !important;
        width: 100% !important;
    }
    .rnote-search-subcard input::placeholder {
        color: #94a3b8 !important;
    }
    .rnote-search-clear {
        background: transparent !important;
        border: none !important;
        color: #94a3b8 !important;
        font-size: 12px !important;
        cursor: pointer !important;
        padding: 0 4px !important;
        line-height: 1 !important;
        display: none;
    }
    .rnote-search-clear:hover {
        color: #ef4444 !important;
    }
    .rnote-add-btn {
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
    .rnote-add-btn:hover {
        background: #0086d4 !important;
        border-color: #0086d4 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    /* ─── Tier 2: Sub-Tabs Filter Card (Single Active Highlight Standard) ─── */
    .rnote-tabs-card {
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
    .rnote-tabs-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .rnote-tab-btn {
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
    .rnote-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .rnote-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
    }
    .rnote-tab-badge {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        padding: 1px 6px !important;
        border-radius: 10px !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
    .rnote-tab-btn.active .rnote-tab-badge {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }
    .rnote-tab-right-info {
        font-size: 11.5px !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* ─── Tier 3: Primary Card Stream & Discrete Sub-Cards Standard ─── */
    .rnote-stream-header-strip {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        margin-bottom: 8px !important;
    }
    .rnote-grid-headers {
        display: grid !important;
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 2.7fr) minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        gap: 12px !important;
        align-items: center !important;
    }
    .rnote-col-label {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        color: #64748b !important;
        text-transform: uppercase !important;
    }

    /* Master Primary Entity Card */
    .rnote-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    }
    .rnote-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px);
    }
    .rnote-card-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 2.7fr) minmax(0, 1.4fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        gap: 12px !important;
        padding: 12px 16px !important;
        align-items: center !important;
    }

    /* Sub-Card 1: Identity & Avatar */
    .rnote-subcard-identity {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }
    .rnote-avatar-box {
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
    .rnote-info-wrap {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }
    .rnote-card-title {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        cursor: pointer !important;
    }
    .rnote-card-title:hover {
        color: #009ef7 !important;
    }
    .rnote-id-pill {
        font-size: 10px !important;
        font-weight: 700 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #dbeafe !important;
        display: inline-block !important;
    }
    .rnote-scope-pill {
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
    .rnote-subcard-content {
        min-width: 0 !important;
    }
    .rnote-preview-snippet {
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
    .rnote-view-full-link {
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
    .rnote-view-full-link:hover {
        color: #0086d4 !important;
        text-decoration: underline !important;
    }

    /* Sub-Card 3: Telemetry */
    .rnote-subcard-telemetry {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        min-width: 0 !important;
    }
    .rnote-metric-pill {
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
    .rnote-date-text {
        font-size: 11px !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    /* Sub-Card 4: Status */
    .rnote-subcard-status {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
    }
    .rnote-status-badge {
        display: inline-block !important;
        padding: 2px 8px !important;
        border-radius: 20px !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        text-align: center !important;
    }
    .rnote-status-badge.active {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .rnote-status-badge.inactive {
        background: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
    }

    /* Sub-Card 5: Actions */
    .rnote-subcard-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
    }
    .rnote-action-btn {
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
    .rnote-action-btn:hover {
        transform: translateY(-1px);
    }

    /* Empty State Card */
    .rnote-empty-card {
        background: #ffffff !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 48px 24px !important;
        text-align: center !important;
        margin: 20px 0 !important;
    }
    .rnote-empty-icon {
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
        .rnote-grid-headers,
        .rnote-card-grid {
            grid-template-columns: minmax(0, 2fr) minmax(0, 2fr) minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1.4fr) !important;
        }
    }
    @media (max-width: 991px) {
        .rnote-stream-header-strip {
            display: none !important;
        }
        .rnote-card-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .rnote-subcard-actions {
            justify-content: flex-start !important;
        }
        .rnote-subcard-status {
            align-items: flex-start !important;
        }
    }

    #rnoteModal .modal-dialog { max-width:750px; }
    #rnotePreviewModal .modal-dialog { max-width:700px; }

    /* ─── Modal & Variable Tokens Bar ─── */
    .rnote-modal-header {
        background: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 16px 20px !important;
    }
    .rnote-tokens-container {
        background: #f8fafc !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        margin-bottom: 12px !important;
    }
    .rnote-tokens-label {
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
    .rnote-token-btn {
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
    .rnote-token-btn:hover {
        background: #eff6ff !important;
        border-color: #009ef7 !important;
        color: #009ef7 !important;
        transform: translateY(-1px);
    }
    .rnote-preview-body {
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
    .rnote-preview-body p {
        margin-bottom: 10px !important;
    }
    .rnote-token-highlight {
        background: #fef3c7 !important;
        color: #92400e !important;
        font-weight: 600 !important;
        padding: 1px 5px !important;
        border-radius: 4px !important;
        border: 1px solid #fde68a !important;
        font-family: monospace !important;
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
        <div id="kt_content_container" class="container-fluid px-3 rnote-wrapper">

            <!--begin::Tier 1: Top Header Toolbar Card-->
            <div class="rnote-header-card">
                <div class="rnote-header-left">
                    <div class="rnote-header-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <div class="rnote-header-title">
                            <span>Recruiter Note Templates</span>
                            <span class="rnote-kpi-chip total" id="kpiTotalChip">
                                <i class="fas fa-layer-group text-muted fs-8"></i>
                                <span id="kpiTotalCount">{{ $totalCount ?? 0 }}</span> Total
                            </span>
                            <span class="rnote-kpi-chip active" id="kpiActiveChip">
                                <i class="fas fa-check-circle text-success fs-8"></i>
                                <span id="kpiActiveCount">{{ $activeCount ?? 0 }}</span> Active
                            </span>
                            <span class="rnote-kpi-chip inactive" id="kpiInactiveChip">
                                <i class="fas fa-pause-circle text-danger fs-8"></i>
                                <span id="kpiInactiveCount">{{ $inactiveCount ?? 0 }}</span> Inactive
                            </span>
                            <span class="rnote-kpi-chip scope">
                                <i class="fas fa-globe fs-8"></i> Global ATS Scope
                            </span>
                        </div>
                        <div class="rnote-header-sub">
                            Standardized candidate screening rubrics and interview evaluation notes distributed across all ATS organizations.
                        </div>
                    </div>
                </div>

                <div class="rnote-header-right">
                    <!-- Search Sub-Card -->
                    <div class="rnote-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="rnoteSearchInput" placeholder="Search templates by title or content..." autocomplete="off">
                        <button type="button" class="rnote-search-clear" id="rnoteSearchClear" title="Clear search">✕</button>
                    </div>

                    <!-- Primary Action Button -->
                    <button type="button" class="btn btn-primary rnote-add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Template
                    </button>
                </div>
            </div>
            <!--end::Tier 1: Top Header Toolbar Card-->

            <!--begin::Tier 2: Sub-Tabs Filter Card-->
            <div class="rnote-tabs-card">
                <div class="rnote-tabs-group">
                    <button type="button" class="rnote-tab-btn active" data-filter="all" onclick="filterTemplates('all', this)">
                        <i class="fas fa-list-ul fs-8"></i> All Templates
                        <span class="rnote-tab-badge" id="tabCountAll">{{ $totalCount ?? 0 }}</span>
                    </button>
                    <button type="button" class="rnote-tab-btn" data-filter="active" onclick="filterTemplates('active', this)">
                        <i class="fas fa-check-circle text-success fs-8"></i> Active Templates
                        <span class="rnote-tab-badge" id="tabCountActive">{{ $activeCount ?? 0 }}</span>
                    </button>
                    <button type="button" class="rnote-tab-btn" data-filter="inactive" onclick="filterTemplates('inactive', this)">
                        <i class="fas fa-pause-circle text-danger fs-8"></i> Inactive Templates
                        <span class="rnote-tab-badge" id="tabCountInactive">{{ $inactiveCount ?? 0 }}</span>
                    </button>
                </div>

                <div class="rnote-tab-right-info">
                    <span class="text-muted"><i class="fas fa-info-circle me-1"></i> Active templates are immediately accessible to hiring managers & recruiters</span>
                </div>
            </div>
            <!--end::Tier 2: Sub-Tabs Filter Card-->

            <!--begin::Tier 3: Primary Recruiter Note Card Stream-->
            <div id="rnoteStreamContainer" class="rnote-stream-container">
                @if(!$templates->isEmpty())
                    <!-- Stream Column Header Strip -->
                    <div class="rnote-stream-header-strip">
                        <div class="rnote-grid-headers">
                            <div><span class="rnote-col-label">TEMPLATE & IDENTIFIER</span></div>
                            <div><span class="rnote-col-label">EVALUATION RUBRIC & SNIPPET</span></div>
                            <div><span class="rnote-col-label">TELEMETRY & DATES</span></div>
                            <div class="text-center"><span class="rnote-col-label">ATS STATUS</span></div>
                            <div class="text-end"><span class="rnote-col-label">ACTIONS</span></div>
                        </div>
                    </div>
                @endif

                <div id="rnoteCardsList">
                    @forelse($templates as $tpl)
                        @php
                            $initial = mb_substr($tpl->title ?? 'T', 0, 1);
                            $palette = $getPalette($initial);
                        @endphp
                        <div class="rnote-card" id="rnote-card-{{ $tpl->id }}" data-id="{{ $tpl->id }}" data-status="{{ $tpl->status ? 'active' : 'inactive' }}" data-search="{{ strtolower($tpl->id . ' #' . $tpl->id . ' ' . $tpl->title . ' ' . strip_tags($tpl->content)) }}">
                            <div class="rnote-card-grid">

                                <!-- Sub-Card 1: Identity & Avatar -->
                                <div class="rnote-subcard-identity">
                                    <div class="rnote-avatar-box" style="background-color: {{ $palette['bg'] }}; color: {{ $palette['color'] }}; border: 1px solid {{ $palette['border'] }};">
                                        {{ strtoupper($initial) }}
                                    </div>
                                    <div class="rnote-info-wrap">
                                        <div class="rnote-card-title" onclick="openPreviewModal({{ $tpl->id }})" title="Click to preview note">{{ $tpl->title }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="rnote-id-pill" title="Template ID">#{{ $tpl->id }}</span>
                                            <span class="rnote-scope-pill"><i class="fas fa-globe fs-9 me-1"></i> Global ATS</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub-Card 2: Content Preview Snippet -->
                                <div class="rnote-subcard-content">
                                    <div class="rnote-preview-snippet" id="preview-text-{{ $tpl->id }}">
                                        {!! strip_tags($tpl->content) !!}
                                    </div>
                                    <a href="javascript:void(0)" class="rnote-view-full-link" onclick="openPreviewModal({{ $tpl->id }})">
                                        <i class="fas fa-eye fs-9"></i> View Full Note
                                    </a>
                                </div>

                                <!-- Sub-Card 3: Telemetry & Temporal Metrics -->
                                <div class="rnote-subcard-telemetry">
                                    <div class="rnote-metric-pill">
                                        <i class="fas fa-file-alt text-muted fs-9"></i>
                                        <span>{{ $tpl->word_count }} Words • {{ $tpl->char_count }} Chars</span>
                                    </div>
                                    <div class="rnote-date-text">
                                        <i class="fas fa-calendar-alt text-muted fs-9"></i>
                                        <span>{{ $tpl->created_formatted }}</span>
                                    </div>
                                    <div class="rnote-date-text text-muted fs-9">
                                        <i class="fas fa-clock fs-9"></i>
                                        <span>Updated {{ $tpl->updated_human }}</span>
                                    </div>
                                </div>

                                <!-- Sub-Card 4: Live Status Switch -->
                                <div class="rnote-subcard-status">
                                    <div class="form-check form-switch form-check-custom form-check-solid mb-1">
                                        <input class="form-check-input rnote-status-toggle" type="checkbox" id="switch-{{ $tpl->id }}" data-id="{{ $tpl->id }}" {{ $tpl->status ? 'checked' : '' }} onchange="toggleTemplateStatus({{ $tpl->id }}, this)">
                                    </div>
                                    <span class="rnote-status-badge {{ $tpl->status ? 'active' : 'inactive' }}" id="status-badge-{{ $tpl->id }}">
                                        {{ $tpl->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Sub-Card 5: Action Toolbar -->
                                <div class="rnote-subcard-actions">
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary rnote-action-btn" onclick="copyTemplateContent({{ $tpl->id }})" title="Copy template content">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-info rnote-action-btn" onclick="duplicateTemplate({{ $tpl->id }})" title="Duplicate template">
                                        <i class="fas fa-clone"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-warning rnote-action-btn" onclick="openEditModal({{ $tpl->id }})" title="Edit template">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger rnote-action-btn" onclick="deleteTemplate({{ $tpl->id }})" title="Delete template">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="rnote-empty-card" id="rnoteEmptyCard">
                            <div class="rnote-empty-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h5 class="fw-bold text-gray-800 mb-1">No Recruiter Note Templates Found</h5>
                            <p class="text-muted fs-7 mb-3">Get started by creating your first global evaluation rubric template.</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openAddModal()">
                                <i class="fas fa-plus me-1"></i> Add First Template
                            </button>
                        </div>
                    @endforelse
                </div>

                <!-- No Search Results Found Card -->
                <div class="rnote-empty-card" id="rnoteNoSearchCard" style="display: none;">
                    <div class="rnote-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="fw-bold text-gray-800 mb-1">No Matching Templates</h5>
                    <p class="text-muted fs-7 mb-3">No templates matched your search query or filter selection.</p>
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i> Reset Filters
                    </button>
                </div>
            </div>
            <!--end::Tier 3: Primary Recruiter Note Card Stream-->
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="rnoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header rnote-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="rnote-header-icon" id="rnoteModalIcon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="rnoteModalTitle">Add Template</h5>
                        <span class="text-muted fs-8">Configure global evaluation rubrics distributed to all ATS organizations</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <form id="rnoteForm">
                    @csrf
                    <input type="hidden" id="rnote_id" name="id" value="">

                    <!-- Title Input with Character Counter -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-bold text-dark fs-7 mb-0">
                                Template Title <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted fs-8" id="titleCharCount">0 / 255</span>
                        </div>
                        <input type="text" class="form-control form-control-solid" id="rnote_title" name="title" placeholder="e.g. 🟢 Immediate Joiner & Product Fit" maxlength="255" autocomplete="off">
                        <span class="text-muted fs-9 mt-1 d-block">Descriptive name shown in candidate evaluation dropdowns</span>
                    </div>

                    <!-- ATS Variable Tokens Toolbar -->
                    <div class="rnote-tokens-container">
                        <div class="rnote-tokens-label">
                            <i class="fas fa-bolt text-warning fs-9"></i> Quick Insert ATS Variables (Click to insert at cursor):
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[candidate_name]')" title="Insert Candidate Full Name">
                                <i class="fas fa-user fs-9 text-primary"></i> [candidate_name]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[relevant_experience]')" title="Insert Relevant Years of Experience">
                                <i class="fas fa-briefcase fs-9 text-success"></i> [relevant_experience]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[all_skills]')" title="Insert Matched Job Skills">
                                <i class="fas fa-code fs-9 text-info"></i> [all_skills]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[current_company]')" title="Insert Current Employer Name">
                                <i class="fas fa-building fs-9 text-warning"></i> [current_company]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[notice_period]')" title="Insert Notice Period Duration">
                                <i class="fas fa-clock fs-9 text-danger"></i> [notice_period]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[joining_timeline]')" title="Insert Expected Joining Date/Timeline">
                                <i class="fas fa-calendar-check fs-9 text-success"></i> [joining_timeline]
                            </button>
                            <button type="button" class="rnote-token-btn" onclick="insertToken('[expected_ctc]')" title="Insert Expected Compensation">
                                <i class="fas fa-money-bill-wave fs-9 text-primary"></i> [expected_ctc]
                            </button>
                        </div>
                    </div>

                    <!-- Content Textarea for CKEditor -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7 mb-1">
                            Template Content / Rubric Body <span class="text-danger">*</span>
                        </label>
                        <textarea id="rnote_content" name="content" rows="6"></textarea>
                    </div>

                    <!-- Active Switch -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light-primary border border-primary border-dashed">
                        <div>
                            <div class="fw-bold text-dark fs-7">Active Status</div>
                            <div class="text-muted fs-8">Make this template immediately visible to all tenant organizations</div>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" id="rnote_status" name="status" checked>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light px-5 py-3">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="rnoteSaveBtn" onclick="saveTemplate()">
                    <i class="fas fa-check me-1"></i> <span id="rnoteSaveBtnText">Save Template</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Full Content Preview Modal -->
<div class="modal fade" id="rnotePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header rnote-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="rnote-header-icon" style="background: #ecfdf5; border-color: #a7f3d0; color: #059669;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="previewModalTitle">Template Preview</h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="rnote-id-pill" id="previewModalId">#—</span>
                            <span class="rnote-status-badge active" id="previewModalStatus">Active</span>
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
                <div class="rnote-preview-body" id="previewModalContent">
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
var _rnoteEditor = null;
var _rnoteActiveFilter = 'all';
var _rnoteSearchTimer = null;

// Tier 2: Sub-tab filtering
function filterTemplates(type, btn) {
    _rnoteActiveFilter = type;
    $('.rnote-tab-btn').removeClass('active');
    $(btn).addClass('active');
    applyFilters();
}

// Tier 1 & 2: Unified Filter Application
function applyFilters() {
    var query = $.trim($('#rnoteSearchInput').val()).toLowerCase();
    var visibleCount = 0;

    $('.rnote-card').each(function() {
        var cardStatus = $(this).attr('data-status');
        var cardSearch = $(this).attr('data-search') || '';

        var matchesFilter = (_rnoteActiveFilter === 'all') || (cardStatus === _rnoteActiveFilter);
        var matchesSearch = !query || (cardSearch.indexOf(query) !== -1);

        if (matchesFilter && matchesSearch) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    if (visibleCount === 0 && $('.rnote-card').length > 0) {
        $('#rnoteNoSearchCard').show();
    } else {
        $('#rnoteNoSearchCard').hide();
    }
}

function resetFilters() {
    $('#rnoteSearchInput').val('');
    $('#rnoteSearchClear').hide();
    filterTemplates('all', $('.rnote-tab-btn[data-filter="all"]'));
}

function updateKpiCounters() {
    var total = $('.rnote-card').length;
    var active = $('.rnote-card[data-status="active"]').length;
    var inactive = $('.rnote-card[data-status="inactive"]').length;

    $('#kpiTotalCount').text(total);
    $('#kpiActiveCount').text(active);
    $('#kpiInactiveCount').text(inactive);

    $('#tabCountAll').text(total);
    $('#tabCountActive').text(active);
    $('#tabCountInactive').text(inactive);

    if (total === 0) {
        $('#rnoteEmptyCard').show();
        $('.rnote-stream-header-strip').hide();
    } else {
        $('#rnoteEmptyCard').hide();
        $('.rnote-stream-header-strip').show();
    }
}

// Tier 3: Instant 1-Click AJAX Status Toggle
function toggleTemplateStatus(id, switchEl) {
    var $switch = $(switchEl);
    $switch.prop('disabled', true);

    $.ajax({
        url: '{{ route("superadmin.recruiter-note.toggleStatus", ["id" => "__ID__"]) }}'.replace('__ID__', id),
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message);
                var isNowActive = (res.template_status == 1);
                var $card = $('#rnote-card-' + id);
                $card.attr('data-status', isNowActive ? 'active' : 'inactive');

                var $badge = $('#status-badge-' + id);
                $badge.removeClass('active inactive')
                      .addClass(isNowActive ? 'active' : 'inactive')
                      .text(res.status_label);

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
            $switch.prop('disabled', false);
        }
    });
}

// Tier 3: Instant 1-Click Duplicate
function duplicateTemplate(id) {
    Swal.fire({
        title: 'Duplicate Template?',
        text: 'This will create an inactive draft copy of this evaluation template.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009ef7',
        cancelButtonColor: '#7e8299',
        confirmButtonText: 'Yes, duplicate it'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        $.ajax({
            url: '{{ route("superadmin.recruiter-note.duplicate", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    setTimeout(function() { location.reload(); }, 600);
                } else {
                    toastr.error(res.message || 'Duplication failed.');
                }
            },
            error: function() {
                toastr.error('Failed to duplicate template.');
            }
        });
    });
}

// Tier 3: 1-Click Clipboard Copying
function copyTemplateContent(id) {
    $.get('{{ route("superadmin.recruiter-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(res) {
        if (res.status !== 'success') return toastr.error('Failed to load template content.');
        var text = $('<div>').html(res.data.content).text().trim();
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
    $('#rnoteSearchInput').on('input', function() {
        var val = $(this).val();
        if (val.length > 0) {
            $('#rnoteSearchClear').show();
        } else {
            $('#rnoteSearchClear').hide();
        }

        clearTimeout(_rnoteSearchTimer);
        _rnoteSearchTimer = setTimeout(function() {
            applyFilters();
        }, 300);
    });

    $('#rnoteSearchClear').on('click', function() {
        $('#rnoteSearchInput').val('').trigger('input');
        $(this).hide();
    });
});

var _activePreviewId = null;

// Character counter for title input
$('#rnote_title').on('input', function() {
    var len = $(this).val().length;
    $('#titleCharCount').text(len + ' / 255');
});

// Insert ATS Variable Token at CKEditor cursor position
function insertToken(token) {
    if (_rnoteEditor) {
        _rnoteEditor.insertText(token);
        _rnoteEditor.focus();
    } else {
        toastr.info('Editor is still mounting, please try again in a second.');
    }
}

// Open Add Modal
function openAddModal() {
    $('#rnoteModalTitle').text('Add Recruiter Note Template');
    $('#rnoteModalIcon i').attr('class', 'fas fa-plus');
    $('#rnote_id').val('');
    $('#rnote_title').val('');
    $('#titleCharCount').text('0 / 255');
    $('#rnote_status').prop('checked', true);
    $('#rnoteSaveBtnText').text('Save Template');

    $('#rnoteModal').modal('show');
}

// Open Edit Modal
function openEditModal(id) {
    $('#rnoteModalTitle').text('Edit Recruiter Note Template');
    $('#rnoteModalIcon i').attr('class', 'fas fa-edit');
    $('#rnoteSaveBtnText').text('Update Template');

    $.get('{{ route("superadmin.recruiter-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(res) {
        if (res.status !== 'success') return toastr.error('Failed to load template data.');
        var d = res.data;
        $('#rnote_id').val(d.id);
        $('#rnote_title').val(d.title);
        $('#titleCharCount').text((d.title || '').length + ' / 255');
        $('#rnote_status').prop('checked', d.status == 1);

        $('#rnoteModal').modal('show');

        // Set editor content safely
        if (_rnoteEditor && _rnoteEditor.status === 'ready') {
            _rnoteEditor.setData(d.content || '');
        } else {
            $('#rnoteModal').data('pendingContent', d.content || '');
        }
    });
}

// Full Content Preview Modal
function openPreviewModal(id) {
    _activePreviewId = id;
    $('#previewModalTitle').text('Loading...');
    $('#previewModalId').text('#' + id);
    $('#previewModalContent').html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading formatted template...</div>');
    $('#rnotePreviewModal').modal('show');

    $.get('{{ route("superadmin.recruiter-note.getData", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(res) {
        if (res.status !== 'success') {
            $('#previewModalContent').html('<div class="text-danger py-3">Failed to load template content.</div>');
            return toastr.error('Failed to load template.');
        }

        var d = res.data;
        $('#previewModalTitle').text(d.title);
        $('#previewModalId').text('#' + d.id);
        
        var isAct = (d.status == 1);
        $('#previewModalStatus').removeClass('active inactive')
                               .addClass(isAct ? 'active' : 'inactive')
                               .text(isAct ? 'Active' : 'Inactive');

        var raw = d.content || '';
        // Highlight ATS variable tokens like [candidate_name] in preview
        var formatted = raw.replace(/(\[[a-zA-Z0-9_]+\])/g, '<span class="rnote-token-highlight">$1</span>');
        $('#previewModalContent').html(formatted);

        var plain = $('<div>').html(raw).text().trim();
        var words = plain !== '' ? plain.split(/\s+/).length : 0;
        $('#previewModalStats').text(words + ' Words • ' + plain.length + ' Chars');
    });
}

function copyCurrentPreview() {
    if (_activePreviewId) {
        copyTemplateContent(_activePreviewId);
    }
}

function editFromPreview() {
    if (_activePreviewId) {
        var id = _activePreviewId;
        $('#rnotePreviewModal').modal('hide');
        setTimeout(function() {
            openEditModal(id);
        }, 300);
    }
}

// Hardened CKEditor Lifecycle
$('#rnoteModal').on('shown.bs.modal', function() {
    if (_rnoteEditor) {
        try { _rnoteEditor.destroy(); } catch(e){}
        _rnoteEditor = null;
    }

    _rnoteEditor = CKEDITOR.replace('rnote_content', {
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

    _rnoteEditor.on('instanceReady', function() {
        var pending = $('#rnoteModal').data('pendingContent');
        if (typeof pending !== 'undefined' && pending !== null) {
            _rnoteEditor.setData(pending);
            $('#rnoteModal').removeData('pendingContent');
        } else if (!$('#rnote_id').val()) {
            _rnoteEditor.setData('');
        }
    });
});

$('#rnoteModal').on('hidden.bs.modal', function() {
    if (_rnoteEditor) {
        try { _rnoteEditor.destroy(); } catch(e){}
        _rnoteEditor = null;
    }
    $('#rnote_id').val('');
    $('#rnoteModal').removeData('pendingContent');
    $('#rnoteSaveBtn').prop('disabled', false);
});

// Save Template with FormRequest validation feedback
function saveTemplate() {
    if (_rnoteEditor) {
        _rnoteEditor.updateElement();
    }

    var id      = $('#rnote_id').val();
    var title   = $.trim($('#rnote_title').val());
    var content = _rnoteEditor ? _rnoteEditor.getData() : $.trim($('#rnote_content').val());
    var status  = $('#rnote_status').is(':checked') ? 1 : 0;

    if (!title) {
        toastr.warning('Please provide a template title.');
        $('#rnote_title').focus();
        return;
    }

    var strippedContent = $('<div>').html(content).text().trim();
    if (!strippedContent && !/<img|<table|<hr|<iframe/i.test(content)) {
        toastr.warning('The template content cannot be empty.');
        return;
    }

    var url = id
        ? '{{ route("superadmin.recruiter-note.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
        : '{{ route("superadmin.recruiter-note.store") }}';
    var method = id ? 'PUT' : 'POST';

    var $btn = $('#rnoteSaveBtn');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');

    $.ajax({
        url: url,
        type: method,
        data: {
            _token: '{{ csrf_token() }}',
            title: title,
            content: content,
            status: status
        },
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Saved successfully.');
                $('#rnoteModal').modal('hide');
                setTimeout(function() {
                    location.reload();
                }, 600);
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
            $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> <span id="rnoteSaveBtnText">' + (id ? 'Update Template' : 'Save Template') + '</span>');
        }
    });
}
</script>
@endpush
