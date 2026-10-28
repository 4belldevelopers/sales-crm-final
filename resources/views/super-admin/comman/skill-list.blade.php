@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
    /* ─── Tagify Custom Enterprise Styling ─── */
    .tagify {
        border-radius: 8px !important;
        border-color: #e2e8f0 !important;
        background-color: #f8fafc !important;
        padding: 4px 6px !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .tagify:hover {
        border-color: #cbd5e1 !important;
    }
    .tagify.tagify--focus {
        border-color: #009ef7 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .tagify__tag {
        background-color: #eff6ff !important;
        border-radius: 6px !important;
        color: #2563eb !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        margin: 3px !important;
    }
    .tagify__tag > div {
        color: #2563eb !important;
    }
    .tagify__tag__removeBtn {
        color: #2563eb !important;
        margin-left: 4px !important;
    }
    .tagify__tag__removeBtn:hover {
        background: #dc2626 !important;
        color: #ffffff !important;
    }

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
    .cd-skills-wrapper {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* ─── Tier 1: Top Header Toolbar Card ─── */
    .cd-skills-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 18px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        gap: 16px !important;
    }
    .cd-skills-header-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
    }
    .cd-skills-header-icon {
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
    .cd-skills-header-title {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .cd-skills-header-sub {
        font-size: 11.5px !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }
    .cd-skills-header-right {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }

    /* Dynamic Search Sub-Card */
    .cd-skills-search-subcard {
        position: relative !important;
        height: 36px !important;
        width: 290px !important;
        max-width: 100% !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 10px !important;
        transition: all 0.2s ease !important;
    }
    .cd-skills-search-subcard:focus-within {
        background: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .cd-skills-search-subcard i.fa-search {
        color: #94a3b8 !important;
        font-size: 12px !important;
        margin-right: 8px !important;
    }
    .cd-skills-search-subcard input {
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-size: 12px !important;
        font-family: 'Poppins', sans-serif !important;
        color: #0f172a !important;
        width: 100% !important;
    }
    .cd-skills-search-subcard input::placeholder {
        color: #94a3b8 !important;
    }
    .cd-skills-search-clear {
        background: transparent !important;
        border: none !important;
        color: #94a3b8 !important;
        font-size: 12px !important;
        cursor: pointer !important;
        padding: 0 4px !important;
        line-height: 1 !important;
    }
    .cd-skills-search-clear:hover {
        color: #ef4444 !important;
    }
    .cd-skills-add-btn {
        height: 36px !important;
        padding: 0 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        white-space: nowrap !important;
        box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2) !important;
    }

    /* ─── Tier 2: Sub-Tabs Filter Card (Single Active Highlight Standard) ─── */
    .cd-skills-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .cd-skills-tabs-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        padding-bottom: 8px !important;
        border-bottom: 1px dashed #e2e8f0 !important;
        margin-bottom: 8px !important;
    }
    .cd-skills-tabs-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .cd-skills-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
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
    .cd-skills-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .cd-skills-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
    }
    .cd-skills-tab-badge {
        font-size: 10px !important;
        font-weight: 700 !important;
        padding: 2px 6px !important;
        border-radius: 10px !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
    .cd-skills-tab-btn.active .cd-skills-tab-badge {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }

    /* Tier 2 View Controls: Selectors & Refresh */
    .cd-skills-view-controls {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }
    .cd-skills-select {
        height: 32px !important;
        width: auto !important;
        min-width: 95px !important;
        max-width: 140px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background-color: #f8fafc !important;
        padding: 0 24px 0 8px !important;
        cursor: pointer !important;
        display: inline-block !important;
    }
    .cd-skills-select:focus {
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }
    .cd-skills-refresh-btn {
        height: 32px !important;
        width: 32px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
    }
    .cd-skills-refresh-btn:hover {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
    }

    /* Alphabet Ribbon Row */
    .cd-alphabet-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
    }
    .cd-alphabet-ribbon {
        display: flex !important;
        align-items: center !important;
        gap: 3px !important;
        flex-wrap: wrap !important;
    }
    .cd-letter-label {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        margin-right: 4px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .cd-letter-btn {
        width: 24px !important;
        height: 24px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #475569 !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        transition: all 0.12s ease !important;
    }
    .cd-letter-btn:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .cd-letter-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }
    .cd-alphabet-telemetry {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 11.5px !important;
        color: #64748b !important;
        margin-left: auto !important;
        white-space: nowrap !important;
    }

    /* ─── Tier 3: Primary Entity Cards & Sub-Cards Stream ─── */
    .cd-skills-stream {
        width: 100% !important;
    }
    .cd-stream-header-strip {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        margin-bottom: 8px !important;
    }
    .cd-strip-label {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
    }
    .cd-skill-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        margin-bottom: 8px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
        transition: all 0.18s ease !important;
    }
    .cd-skill-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px) !important;
    }
    .cd-skill-card.selected {
        border-color: #009ef7 !important;
        background: #f8fcff !important;
    }
    .cd-skill-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.3fr) minmax(0, 1.2fr) minmax(0, 1.1fr) !important;
        gap: 12px !important;
        align-items: center !important;
        width: 100% !important;
    }

    /* Sub-Card 1: Identity & Monogram */
    .cd-subcard-identity {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
    }
    .cd-skill-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        flex-shrink: 0 !important;
    }
    .cd-skill-name-wrap {
        display: flex !important;
        flex-direction: column !important;
        min-width: 0 !important;
    }
    .cd-skill-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.25 !important;
    }
    .cd-skill-id-badge {
        font-size: 10px !important;
        color: #94a3b8 !important;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        margin-top: 2px !important;
    }
    .cd-search-highlight {
        background-color: #fef08a !important;
        color: #0f172a !important;
        padding: 0 2px !important;
        border-radius: 2px !important;
        font-weight: 700 !important;
    }

    /* Sub-Card 2: Telemetry */
    .cd-subcard-telemetry {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }
    .cd-telemetry-date {
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #475569 !important;
        display: flex !important;
        align-items: center !important;
    }
    .cd-telemetry-tag {
        margin-top: 3px !important;
    }

    /* Sub-Card 3: Taxonomy Status */
    .cd-subcard-status {
        display: flex !important;
        align-items: center !important;
    }

    /* Sub-Card 4: Actions */
    .cd-subcard-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
    }

    /* Empty State */
    .cd-empty-card {
        background: #ffffff !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 36px 20px !important;
        text-align: center !important;
        margin-bottom: 12px !important;
    }
    .cd-empty-icon {
        width: 50px !important;
        height: 50px !important;
        border-radius: 50% !important;
        background: #f1f5f9 !important;
        color: #94a3b8 !important;
        font-size: 20px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 12px !important;
    }

    /* Floating Sticky Batch Actions Toolbar */
    .cd-skills-bulk-bar {
        position: fixed !important;
        bottom: 24px !important;
        left: 50% !important;
        transform: translateX(-50%) translateY(100px) !important;
        background: #0f172a !important;
        color: #ffffff !important;
        border-radius: 30px !important;
        padding: 8px 18px !important;
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
        z-index: 999 !important;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .cd-skills-bulk-bar.visible {
        transform: translateX(-50%) translateY(0) !important;
    }

    /* Pagination container */
    .cd-pagination-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 16px !important;
    }

    /* ─── Offcanvas Slide-Over Drawer Styling (Bulletproof Standalone + Metronic Compatible) ─── */
    #kt_drawer_skill_form.cd-skills-drawer {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: auto !important;
        height: 100vh !important;
        max-height: 100vh !important;
        width: 480px !important;
        max-width: 95vw !important;
        background: #ffffff !important;
        box-shadow: -8px 0 32px rgba(15, 23, 42, 0.22) !important;
        z-index: 99999 !important;
        overflow: hidden !important;
        transform: translateX(105%) !important;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        pointer-events: none !important;
    }

    #kt_drawer_skill_form.cd-skills-drawer.drawer-on {
        transform: translateX(0) !important;
        pointer-events: auto !important;
    }

    .cd-drawer-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(2px) !important;
        z-index: 99998 !important;
        animation: cdDrawerFadeIn 0.2s ease !important;
    }

    @keyframes cdDrawerFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ─── Responsive Breakpoints (15.6" Laptops, Tablets, Mobile) ─── */
    @media (max-width: 1200px) {
        .cd-skill-grid {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.2fr) minmax(0, 1fr) auto !important;
        }
        .cd-skills-search-subcard {
            width: 220px !important;
        }
    }

    @media (max-width: 991px) {
        .cd-stream-header-strip {
            display: none !important;
        }
        .cd-skill-grid {
            grid-template-columns: 1fr auto !important;
            gap: 10px !important;
        }
        .cd-subcard-telemetry,
        .cd-subcard-status {
            grid-column: span 2 !important;
            padding-left: 42px !important;
        }
        .cd-subcard-actions {
            justify-content: flex-end !important;
        }
        .cd-skills-header-card {
            flex-wrap: wrap !important;
        }
        .cd-skills-header-right {
            width: 100% !important;
            justify-content: space-between !important;
        }
        .cd-skills-search-subcard {
            flex-grow: 1 !important;
            width: auto !important;
        }
        .cd-skills-tabs-row {
            flex-wrap: wrap !important;
        }
        .cd-skills-view-controls {
            width: 100% !important;
            justify-content: flex-end !important;
            margin-top: 6px !important;
        }
    }

    @media (max-width: 576px) {
        .cd-skills-header-card {
            padding: 10px 12px !important;
        }
        .cd-skill-grid {
            grid-template-columns: 1fr !important;
        }
        .cd-subcard-actions {
            justify-content: flex-start !important;
            padding-left: 42px !important;
            margin-top: 6px !important;
        }
        .cd-skills-bulk-bar {
            width: 92% !important;
            justify-content: space-between !important;
        }
        .cd-skills-alphabet-ribbon {
            gap: 2px !important;
        }
        .cd-letter-btn {
            min-width: 20px !important;
            height: 22px !important;
            font-size: 10px !important;
        }
        .cd-alphabet-row {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .cd-alphabet-telemetry {
            margin-left: 0 !important;
            margin-top: 6px !important;
            width: 100% !important;
            justify-content: space-between !important;
        }
    }
</style>
@endpush

@section('content')
<div class="cd-skills-wrapper">

    {{-- Tier 1: Top Header Toolbar Card --}}
    <div class="cd-skills-header-card">
        <div class="cd-skills-header-left">
            <div class="cd-skills-header-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <div class="cd-skills-header-title">
                    Global Master Skills Directory
                    <span class="badge badge-light-primary fw-bolder fs-8 ms-1">
                        <i class="fa-solid fa-layer-group text-primary me-1"></i><span id="kpiTotalSkills">{{ number_format($totalAll ?? $total) }}</span> Indexed
                    </span>
                    <span class="badge badge-light-info fw-bolder fs-8 ms-1" id="kpiFilterCount" style="{{ empty($q) && empty($letter) && empty($range) ? 'display:none;' : '' }}">
                        <i class="fa-solid fa-filter text-info me-1"></i><span id="kpiFiltered">{{ number_format($total) }}</span> Matches
                    </span>
                    <span class="badge badge-light-success fw-bolder fs-8 ms-1">
                        <i class="fa fa-circle text-success fs-9 me-1"></i>Elasticsearch Live
                    </span>
                </div>
                <div class="cd-skills-header-sub">
                    Authoritative talent taxonomy indexed in Elasticsearch for candidate matching, auto-tagging, and ATS search.
                </div>
            </div>
        </div>

        <div class="cd-skills-header-right">
            {{-- 300ms Debounced Dynamic Search Sub-Card --}}
            <div class="cd-skills-search-subcard">
                <i class="fa fa-search"></i>
                <input type="text" id="inputSkillSearch" placeholder="Search skills (e.g. php, react)..." value="{{ $q ?? '' }}" autocomplete="off">
                <button type="button" class="cd-skills-search-clear" id="btnSkillSearchClear" style="{{ empty($q) ? 'display: none;' : '' }}" title="Clear search">✕</button>
            </div>

            {{-- Primary Action Button --}}
            <button type="button" class="btn btn-sm btn-primary fw-bolder cd-skills-add-btn" id="btnOpenAddDrawer">
                <i class="fa fa-plus me-1"></i> Add Skills
            </button>
        </div>
    </div>

    {{-- Tier 2: Sub-Tabs Filter Card (Single Active Highlight Standard) --}}
    <div class="cd-skills-tabs-card">
        {{-- Row 1: Category Filter Tabs + View Controls (Size, Sort, Refresh) --}}
        <div class="cd-skills-tabs-row">
            <div class="cd-skills-tabs-group">
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ empty($range) && empty($letter) ? 'active' : '' }}" data-range="">
                    <i class="fa-solid fa-list-ul fs-8"></i>
                    <span>All Skills</span>
                    <span class="cd-skills-tab-badge">{{ number_format($totalAll ?? $total) }}</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'a-d' ? 'active' : '' }}" data-range="a-d">
                    <span>A — D</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'e-h' ? 'active' : '' }}" data-range="e-h">
                    <span>E — H</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'i-l' ? 'active' : '' }}" data-range="i-l">
                    <span>I — L</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'm-p' ? 'active' : '' }}" data-range="m-p">
                    <span>M — P</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'q-t' ? 'active' : '' }}" data-range="q-t">
                    <span>Q — T</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'u-z' ? 'active' : '' }}" data-range="u-z">
                    <span>U — Z</span>
                </button>
                <button type="button" class="cd-skills-tab-btn js-tab-filter {{ $range === 'other' ? 'active' : '' }}" data-range="other">
                    <span>0-9 & Other</span>
                </button>
            </div>

            {{-- View Controls: Per-Page Size, Sort Dropdown & Refresh Index Button --}}
            <div class="cd-skills-view-controls">
                <select id="selectSkillsSize" class="form-select form-select-sm cd-skills-select" title="Items per page">
                    <option value="20" {{ ($size ?? 20) == 20 ? 'selected' : '' }}>20 / page</option>
                    <option value="50" {{ ($size ?? 20) == 50 ? 'selected' : '' }}>50 / page</option>
                    <option value="100" {{ ($size ?? 20) == 100 ? 'selected' : '' }}>100 / page</option>
                    <option value="250" {{ ($size ?? 20) == 250 ? 'selected' : '' }}>250 / page</option>
                </select>

                <select id="selectSkillsSort" class="form-select form-select-sm cd-skills-select" title="Sort skills">
                    <option value="newest" {{ ($sort ?? 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ ($sort ?? 'newest') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="alpha_asc" {{ ($sort ?? 'newest') == 'alpha_asc' ? 'selected' : '' }}>A → Z (Alphabetical)</option>
                    <option value="alpha_desc" {{ ($sort ?? 'newest') == 'alpha_desc' ? 'selected' : '' }}>Z → A (Reverse)</option>
                </select>

                <button type="button" class="btn btn-sm btn-icon btn-light cd-skills-refresh-btn" id="btnRefreshSkills" title="Refresh Index">
                    <i class="fa fa-rotate text-muted"></i>
                </button>
            </div>
        </div>

        {{-- Row 2: Micro-Alphabet Ribbon + Telemetry & Filter Reset --}}
        <div class="cd-alphabet-row">
            <div class="cd-alphabet-ribbon">
                <span class="cd-letter-label">Drill:</span>
                @foreach(range('A', 'Z') as $char)
                    @php $charLower = strtolower($char); @endphp
                    <button type="button" class="cd-letter-btn js-letter-filter {{ $letter === $charLower ? 'active' : '' }}" data-letter="{{ $charLower }}" title="Filter starting with {{ $char }}">
                        {{ $char }}
                    </button>
                @endforeach
                @if(!empty($letter))
                    <button type="button" class="btn btn-link btn-color-muted btn-active-color-primary fs-8 py-0 px-2 js-letter-clear" title="Clear letter filter">
                        Clear ({{ strtoupper($letter) }})
                    </button>
                @endif
            </div>

            <div class="cd-alphabet-telemetry">
                <span>
                    <i class="fa-solid fa-layer-group text-primary me-1"></i>
                    Showing <strong id="lblShowingCount">{{ count($dataskill) }}</strong> of <strong id="lblTotalCount">{{ $total }}</strong> skills
                </span>
                <button type="button" class="btn btn-xs btn-light-danger fw-bold ms-2 py-1 px-2" id="btnResetAllFilters" style="{{ empty($q) && empty($letter) && empty($range) ? 'display: none;' : '' }}" title="Reset search and filters">
                    <i class="fa fa-times me-1"></i> Reset All
                </button>
            </div>
        </div>
    </div>

    {{-- Tier 3: Primary Skill Card Stream (Rendered Container) --}}
    <div id="skillsStreamWrapper">
        @include('super-admin.comman.skill-cards-stream')
    </div>

    {{-- Floating Sticky Batch Actions Bar --}}
    <div class="cd-skills-bulk-bar" id="bulkActionsToolbar">
        <span><i class="fa-solid fa-check-double text-info me-1"></i> <strong id="bulkSelectedCount">0</strong> skills selected</span>
        <button type="button" class="btn btn-sm btn-light-secondary text-white py-1 px-3" id="btnBulkDeselect">Deselect</button>
        <button type="button" class="btn btn-sm btn-danger py-1 px-3" id="btnBulkDelete">
            <i class="fa fa-trash me-1"></i> Delete Selected
        </button>
    </div>

</div>

{{-- In-Place Slide-Over Drawer for Adding / Editing Skills --}}
<div id="kt_drawer_skill_form" class="cd-skills-drawer bg-white" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#btnOpenAddDrawer" data-kt-drawer-close="#kt_drawer_skill_close, #btnCancelDrawer" data-kt-drawer-width="{default:'320px', 'md': '480px'}">
    <div class="card w-100 h-100 rounded-0 border-0 shadow-none d-flex flex-column">
        {{-- Sticky Drawer Header --}}
        <div class="card-header pe-5 border-bottom flex-shrink-0 d-flex align-items-center justify-content-between" style="min-height: 65px;">
            <div class="card-title m-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="cd-skills-header-icon" style="width: 36px; height: 36px; font-size: 15px;">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div>
                        <span class="fs-5 fw-bolder text-gray-900 d-block" id="drawerTitle">Add Master Skills</span>
                        <span class="fs-8 text-muted d-block" id="drawerSubtitle">Index one or multiple skills into Elasticsearch taxonomy.</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar m-0">
                <button type="button" class="btn btn-sm btn-icon btn-light" id="kt_drawer_skill_close" title="Close Drawer">
                    <i class="fa fa-times fs-5 text-gray-700"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Drawer Body --}}
        <div class="card-body p-6 flex-grow-1 overflow-auto">
            <form id="skillDrawerForm">
                @csrf
                <input type="hidden" name="tokenId" id="drawerTokenId" value="0">

                <div class="mb-5">
                    <label class="form-label fs-7 fw-bolder text-dark mb-1 required" id="drawerInputLabel">Skill Name(s)</label>
                    <div class="text-muted fs-8 mb-2" id="drawerInputHelper">
                        Type skill names and press enter or comma. You can paste comma-separated skills (up to 50 at once).
                    </div>
                    <input class="form-control form-control-solid" name="skill_name" id="drawerSkillInput" placeholder="e.g. PHP, Laravel, React Native" value="">
                </div>

                <div class="alert alert-light-primary d-flex align-items-center p-3 mb-5">
                    <i class="fa-solid fa-circle-info fs-4 text-primary me-3"></i>
                    <div class="fs-8 text-gray-700">
                        Skills are automatically normalized to <strong>lowercase</strong> and trimmed. Special characters allowed: <code>+ # / . -</code>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sticky Drawer Footer --}}
        <div class="card-footer p-4 border-top flex-shrink-0 d-flex justify-content-end gap-2 bg-white">
            <button type="button" class="btn btn-sm btn-light" id="btnCancelDrawer">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary fw-bolder px-5" id="btnSaveSkillDrawer">
                <span class="indicator-label" id="btnSaveLabel"><i class="fa fa-save me-1"></i> Save to Taxonomy</span>
                <span class="indicator-progress" style="display: none;">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
$(document).ready(function() {
    "use strict";

    // State Variables
    let currentSearch = "{{ $q ?? '' }}";
    let currentRange  = "{{ $range ?? '' }}";
    let currentLetter = "{{ $letter ?? '' }}";
    let currentSize   = "{{ $size ?? 20 }}";
    let currentSort   = "{{ $sort ?? 'newest' }}";
    let searchDebounceTimer = null;
    let selectedSkillIds = new Set();
    let tagifyInstance = null;

    // Initialize Tagify on Drawer Input
    const inputEl = document.querySelector('#drawerSkillInput');
    if (inputEl && typeof Tagify !== 'undefined') {
        tagifyInstance = new Tagify(inputEl, {
            whitelist: [],
            maxTags: 50,
            dropdown: {
                maxItems: 20,
                enabled: 0,
                closeOnSelect: true
            },
            transformTag: function(tagData) {
                tagData.value = tagData.value.replace(/[^a-zA-Z0-9 _+#\/.-]/g, '').toLowerCase().trim();
                return tagData;
            },
            validate: function(tagData) {
                return tagData.value.trim().length > 0;
            }
        });
    }

    // ─── Fetch Skills Data (AJAX Stream Update) ───
    function fetchSkills(page = 1) {
        let params = {
            page: page,
            q: currentSearch,
            range: currentRange,
            letter: currentLetter,
            size: currentSize,
            sort: currentSort
        };

        // Update URL query string without reloading page
        let queryStr = $.param(params);
        let newUrl = window.location.pathname + '?' + queryStr;
        window.history.pushState(null, '', newUrl);

        // Show subtle loading state
        $('#skillsStreamWrapper').css('opacity', '0.5');

        $.ajax({
            url: "{{ route('superadmin.company.skillskElastic') }}",
            type: "GET",
            data: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                $('#skillsStreamWrapper').css('opacity', '1');
                if (res.status === 'success') {
                    $('#skillsStreamWrapper').html(res.html);

                    // Update KPI counters
                    $('#kpiTotalSkills').text(res.totalAll.toLocaleString());
                    $('#kpiFiltered').text(res.total.toLocaleString());
                    $('#lblTotalCount').text(res.total.toLocaleString());
                    let countDisplayed = res.count !== undefined ? res.count : (res.total ? Math.min(res.total, parseInt(currentSize)).toLocaleString() : '0');
                    $('#lblShowingCount').text(countDisplayed);

                    if (currentSearch || currentRange || currentLetter) {
                        $('#kpiFilterCount').show();
                        $('#btnResetAllFilters').show();
                    } else {
                        $('#kpiFilterCount').hide();
                        $('#btnResetAllFilters').hide();
                    }

                    // Reset selection bar
                    selectedSkillIds.clear();
                    updateBulkToolbar();
                }
            },
            error: function(err) {
                $('#skillsStreamWrapper').css('opacity', '1');
                console.error("Failed to fetch skills:", err);
            }
        });
    }

    // ─── Search Input Debounce (300ms Standard) ───
    $('#inputSkillSearch').on('input', function() {
        clearTimeout(searchDebounceTimer);
        let val = $(this).val().trim();
        currentSearch = val;

        if (val.length > 0) {
            $('#btnSkillSearchClear').show();
        } else {
            $('#btnSkillSearchClear').hide();
        }

        searchDebounceTimer = setTimeout(function() {
            fetchSkills(1);
        }, 300);
    });

    // Clear Search Input Button
    $('#btnSkillSearchClear').on('click', function() {
        $('#inputSkillSearch').val('');
        currentSearch = '';
        $(this).hide();
        fetchSkills(1);
    });

    // ─── Range Sub-Tab Switching (Single Active Highlight Standard) ───
    $(document).on('click', '.js-tab-filter', function(e) {
        e.preventDefault();
        let $btn = $(this);
        currentRange = $btn.data('range') || '';
        currentLetter = ''; // Clear micro-alphabet when range clicked

        $('.js-tab-filter').removeClass('active');
        $btn.addClass('active');
        $('.js-letter-filter').removeClass('active');

        fetchSkills(1);
    });

    // ─── Alphabet Micro Drill Ribbon ───
    $(document).on('click', '.js-letter-filter', function(e) {
        e.preventDefault();
        let $btn = $(this);
        let clickedLetter = $btn.data('letter') || '';

        if (currentLetter === clickedLetter) {
            currentLetter = '';
            $btn.removeClass('active');
        } else {
            currentLetter = clickedLetter;
            currentRange = ''; // Reset range when single letter chosen
            $('.js-letter-filter').removeClass('active');
            $btn.addClass('active');
            $('.js-tab-filter').removeClass('active');
            $('.js-tab-filter[data-range=""]').addClass('active');
        }

        fetchSkills(1);
    });

    $(document).on('click', '.js-letter-clear', function(e) {
        e.preventDefault();
        currentLetter = '';
        $('.js-letter-filter').removeClass('active');
        fetchSkills(1);
    });

    $(document).on('click', '#btnResetAllFilters', function(e) {
        e.preventDefault();
        $('#inputSkillSearch').val('');
        $('#btnSkillSearchClear').hide();
        currentSearch = '';
        currentRange = '';
        currentLetter = '';
        $('.js-tab-filter').removeClass('active');
        $('.js-tab-filter[data-range=""]').addClass('active');
        $('.js-letter-filter').removeClass('active');
        fetchSkills(1);
    });

    // ─── Per-Page & Sort Selectors ───
    $('#selectSkillsSize').on('change', function() {
        currentSize = $(this).val();
        fetchSkills(1);
    });

    $('#selectSkillsSort').on('change', function() {
        currentSort = $(this).val();
        fetchSkills(1);
    });

    // ─── Refresh Button ───
    $('#btnRefreshSkills').on('click', function() {
        let $icon = $(this).find('i');
        $icon.addClass('fa-spin');
        fetchSkills(1);
        setTimeout(function() { $icon.removeClass('fa-spin'); }, 500);
    });

    // ─── Reset Filters from Empty State ───
    $(document).on('click', '.js-btn-reset-filters', function(e) {
        e.preventDefault();
        $('#inputSkillSearch').val('');
        $('#btnSkillSearchClear').hide();
        currentSearch = '';
        currentRange = '';
        currentLetter = '';
        $('.js-tab-filter').removeClass('active');
        $('.js-tab-filter[data-range=""]').addClass('active');
        $('.js-letter-filter').removeClass('active');
        fetchSkills(1);
    });

    // ─── Custom Pagination Click Handling ───
    $(document).on('click', '.cd-pagination-card a', function(e) {
        e.preventDefault();
        if ($(this).hasClass('active_disabled') || $(this).hasClass('previous_disabled') || $(this).hasClass('next_disabled')) {
            return;
        }
        let href = $(this).attr('href');
        if (href && href !== '#') {
            let url = new URL(href, window.location.origin);
            let page = url.searchParams.get('page') || 1;
            fetchSkills(page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // ─── 1-Click Clipboard Copy ───
    $(document).on('click', '.btn-copy-skill', function() {
        let skillName = $(this).data('skill');
        let $btn = $(this);

        if (navigator.clipboard) {
            navigator.clipboard.writeText(skillName).then(function() {
                toastr.success(`Copied "${skillName}" to clipboard!`, '', { timeOut: 1500 });
                $btn.html('<i class="fa fa-check text-success"></i>');
                setTimeout(function() { $btn.html('<i class="fa-regular fa-copy"></i>'); }, 1200);
            });
        }
    });

    // ─── Multi-Select & Sticky Bulk Actions Bar ───
    $(document).on('change', '#chkSelectAllSkills', function() {
        let isChecked = $(this).is(':checked');
        $('.cd-skill-checkbox').each(function() {
            let docId = $(this).val();
            let $card = $(this).closest('.cd-skill-card');
            $(this).prop('checked', isChecked);
            if (isChecked) {
                selectedSkillIds.add(docId);
                $card.addClass('selected');
            } else {
                selectedSkillIds.delete(docId);
                $card.removeClass('selected');
            }
        });
        updateBulkToolbar();
    });

    $(document).on('change', '.cd-skill-checkbox', function() {
        let docId = $(this).val();
        let $card = $(this).closest('.cd-skill-card');

        if ($(this).is(':checked')) {
            selectedSkillIds.add(docId);
            $card.addClass('selected');
        } else {
            selectedSkillIds.delete(docId);
            $card.removeClass('selected');
        }
        updateBulkToolbar();
    });

    function updateBulkToolbar() {
        let count = selectedSkillIds.size;
        let totalVisible = $('.cd-skill-checkbox').length;
        $('#bulkSelectedCount').text(count);
        if (count > 0) {
            $('#bulkActionsToolbar').addClass('visible');
            if (totalVisible > 0 && count === totalVisible) {
                $('#chkSelectAllSkills').prop('checked', true).prop('indeterminate', false);
            } else {
                $('#chkSelectAllSkills').prop('checked', false).prop('indeterminate', true);
            }
        } else {
            $('#bulkActionsToolbar').removeClass('visible');
            $('#chkSelectAllSkills').prop('checked', false).prop('indeterminate', false);
            $('.cd-skill-card').removeClass('selected');
            $('.cd-skill-checkbox').prop('checked', false);
        }
    }

    $('#btnBulkDeselect').on('click', function() {
        selectedSkillIds.clear();
        updateBulkToolbar();
    });

    // Bulk Delete Action
    $('#btnBulkDelete').on('click', function() {
        let count = selectedSkillIds.size;
        if (count === 0) return;

        Swal.fire({
            title: `Delete ${count} Skills?`,
            text: "These master skills will be removed from the Elasticsearch taxonomy.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, Delete ${count} Skills`,
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('superadmin.company.skillsBulkDelete') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: Array.from(selectedSkillIds)
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted!', res.message, 'success');
                            selectedSkillIds.clear();
                            updateBulkToolbar();
                            fetchSkills(1);
                        } else {
                            Swal.fire('Error', res.message || 'Could not delete skills.', 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'An unexpected error occurred during bulk deletion.', 'error');
                    }
                });
            }
        });
    });

    // ─── Single Delete Action ───
    $(document).on('click', '.btn-delete-skill', function() {
        let docId = $(this).data('id');
        let skillName = $(this).data('name');

        Swal.fire({
            title: `Delete "${skillName}"?`,
            text: "This skill will be permanently removed from the master Elasticsearch index.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('superadmin.company.skillDelete') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: docId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            toastr.success(res.message || 'Skill deleted successfully.');
                            $('#skill-card-' + docId).fadeOut(200, function() {
                                $(this).remove();
                                fetchSkills(1);
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Failed to delete skill.', 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'An error occurred while deleting the skill.', 'error');
                    }
                });
            }
        });
    });

    // ─── Offcanvas Drawer Controls ───
    function showSkillDrawer() {
        // 1. Direct CSS sliding & Overlay (Always Works Guaranteed)
        $('#kt_drawer_skill_form').addClass('drawer-on');
        if ($('.cd-drawer-overlay').length === 0) {
            $('body').append('<div class="cd-drawer-overlay"></div>');
        }
        $('body').css('overflow', 'hidden');

        // 2. Metronic KTDrawer integration if present
        if (typeof KTDrawer !== 'undefined') {
            try {
                let drawerEl = document.querySelector("#kt_drawer_skill_form");
                let drawerObj = KTDrawer.getInstance(drawerEl) || new KTDrawer(drawerEl);
                if (drawerObj && typeof drawerObj.show === 'function') {
                    drawerObj.show();
                }
            } catch (e) {}
        }

        // 3. Focus Tagify input or plain input
        setTimeout(function() {
            if (tagifyInstance && tagifyInstance.DOM && tagifyInstance.DOM.input) {
                tagifyInstance.DOM.input.focus();
            } else {
                $('#drawerSkillInput').focus();
            }
        }, 150);
    }

    function hideSkillDrawer() {
        if (typeof KTDrawer !== 'undefined') {
            try {
                let drawerEl = document.querySelector("#kt_drawer_skill_form");
                let drawerObj = KTDrawer.getInstance(drawerEl);
                if (drawerObj && typeof drawerObj.hide === 'function') {
                    drawerObj.hide();
                }
            } catch (e) {}
        }
        $('#kt_drawer_skill_form').removeClass('drawer-on');
        $('.cd-drawer-overlay, .drawer-overlay').remove();
        $('body').css('overflow', '');
    }

    // Open Drawer for Add Mode (Event Delegation)
    $(document).on('click', '#btnOpenAddDrawer', function(e) {
        e.preventDefault();
        $('#drawerTokenId').val('0');
        $('#drawerTitle').text('Add Master Skills');
        $('#drawerSubtitle').text('Index one or multiple skills into Elasticsearch taxonomy.');
        $('#drawerInputLabel').text('Skill Name(s)');
        $('#drawerInputHelper').show();
        $('#btnSaveLabel').html('<i class="fa fa-save me-1"></i> Save to Taxonomy');

        if (tagifyInstance) {
            tagifyInstance.removeAllTags();
            tagifyInstance.settings.maxTags = 50;
        } else {
            $('#drawerSkillInput').val('');
        }

        showSkillDrawer();
    });

    // Open Drawer for Edit Mode (Event Delegation)
    $(document).on('click', '.btn-edit-skill', function(e) {
        e.preventDefault();
        let docId = $(this).data('id');
        let skillName = $(this).data('name');

        $('#drawerTokenId').val(docId);
        $('#drawerTitle').text('Edit Master Skill');
        $('#drawerSubtitle').text(`Modify normalized skill identifier: #${docId}`);
        $('#drawerInputLabel').text('Skill Name');
        $('#drawerInputHelper').hide();
        $('#btnSaveLabel').html('<i class="fa fa-save me-1"></i> Update Skill');

        if (tagifyInstance) {
            tagifyInstance.removeAllTags();
            tagifyInstance.settings.maxTags = 1;
            tagifyInstance.addTags([skillName]);
        } else {
            $('#drawerSkillInput').val(skillName);
        }

        showSkillDrawer();
    });

    $(document).on('click', '#kt_drawer_skill_close, #btnCancelDrawer, .cd-drawer-overlay, .drawer-overlay', function(e) {
        e.preventDefault();
        hideSkillDrawer();
    });

    // Keyboard Shortcuts: Escape to close drawer, Ctrl+Enter to save
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && ($('#kt_drawer_skill_form').hasClass('drawer-on') || $('.cd-drawer-overlay').length > 0)) {
            hideSkillDrawer();
        }
    });

    $('#skillDrawerForm').on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            $('#btnSaveSkillDrawer').trigger('click');
        }
    });

    // Save Skills via Drawer AJAX
    $('#btnSaveSkillDrawer').on('click', function(e) {
        e.preventDefault();
        let $btn = $(this);

        // Auto-commit any typed text in Tagify input before submitting
        if (tagifyInstance && tagifyInstance.DOM && tagifyInstance.DOM.input && tagifyInstance.DOM.input.textContent && tagifyInstance.DOM.input.textContent.trim()) {
            tagifyInstance.addTags([tagifyInstance.DOM.input.textContent.trim()]);
        }

        let form = $('#skillDrawerForm')[0];
        let formData = new FormData(form);

        $btn.find('.indicator-label').hide();
        $btn.find('.indicator-progress').show();
        $btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('superadmin.company.addEditSkillUpdate') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();
                $btn.prop('disabled', false);

                if (res.status === 'success') {
                    hideSkillDrawer();
                    Swal.fire({
                        title: 'Success!',
                        text: res.message || 'Skills saved successfully.',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    });
                    fetchSkills(1);
                } else {
                    Swal.fire('Validation Error', res.message || 'Please verify the entered skill values.', 'error');
                }
            },
            error: function(xhr) {
                $btn.find('.indicator-label').show();
                $btn.find('.indicator-progress').hide();
                $btn.prop('disabled', false);

                let msg = 'Failed to save skills.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

});
</script>
@endpush