@extends('layouts.superadmin_layout')

@push('head-script')
<style>
/* ==========================================================================
   Master Enterprise Scoped Styling — Default Terms Module
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   Follows UI/UX Standard: Modern Enterprise Card + Sub-Card Design System
   ========================================================================== */
#kt_body {
    overflow-x: hidden !important;
}

/* ==========================================================================
   Top Gap Optimization: Collapse Metronic toolbar-fixed height and wrapper padding
   ========================================================================== */
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

.cd-terms-wrap {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding: 0 16px 30px 16px !important;
    margin-top: 0 !important;
}

.cd-terms-wrap * {
    box-sizing: border-box !important;
}

/* Guarantee font icon preservation across all FontAwesome and Metronic icon classes */
.cd-terms-wrap i,
.cd-terms-wrap [class*="fa-"],
.cd-terms-wrap .fa,
.cd-terms-wrap .fas,
.cd-terms-wrap .far,
.cd-terms-wrap .fab,
.cd-terms-wrap [class*="ki-"] {
    font-family: "Font Awesome 6 Free", "FontAwesome", "Font Awesome 5 Free", sans-serif !important;
    font-style: normal !important;
    font-weight: 900 !important;
}

/* ==========================================================================
   Tier 0: Executive KPI Summary Metric Cards
   ========================================================================== */
.cd-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 12px !important;
    margin-bottom: 14px !important;
    width: 100% !important;
}

@media (max-width: 992px) {
    .cd-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 576px) {
    .cd-kpi-grid {
        grid-template-columns: 1fr !important;
    }
}

.cd-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 13px 16px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease !important;
}

.cd-kpi-card:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}

.cd-kpi-icon {
    width: 42px !important;
    height: 42px !important;
    border-radius: 9px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 17px !important;
    flex-shrink: 0 !important;
}

.cd-kpi-icon-blue { background: #eff6ff !important; color: #2563eb !important; }
.cd-kpi-icon-green { background: #ecfdf5 !important; color: #059669 !important; }
.cd-kpi-icon-amber { background: #fffbeb !important; color: #d97706 !important; }
.cd-kpi-icon-purple { background: #f5f3ff !important; color: #7c3aed !important; }

.cd-kpi-content {
    min-width: 0 !important;
    flex: 1 !important;
}

.cd-kpi-val {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
}

.cd-kpi-title {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    font-weight: 500 !important;
}

/* ==========================================================================
   Tier 1: Top Header Toolbar Card
   ========================================================================== */
.cd-terms-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-header-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    flex-shrink: 0 !important;
}

.cd-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
}

.cd-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Dynamic Search Sub-Card */
.cd-terms-search-subcard {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 36px !important;
    width: 290px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-terms-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-terms-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.cd-terms-search-subcard input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    font-family: inherit !important;
}

.cd-terms-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-terms-search-clear {
    border: none !important;
    background: transparent !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    padding: 0 !important;
    font-size: 12px !important;
    display: none;
    align-items: center !important;
    justify-content: center !important;
}

.cd-terms-search-clear:hover {
    color: #ef4444 !important;
}

/* View Switcher */
.cd-view-switcher {
    display: flex !important;
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 2px !important;
    height: 36px !important;
}

.cd-view-btn {
    border: none !important;
    background: transparent !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-view-btn.active {
    background: #ffffff !important;
    color: #009ef7 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
}

/* ==========================================================================
   Tier 2: Sub-Tabs Filter Card
   ========================================================================== */
.cd-terms-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 6px 10px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    overflow-x: auto !important;
    width: 100% !important;
}

.cd-tabs-list {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    white-space: nowrap !important;
}

.cd-tab-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 7px 14px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    user-select: none !important;
}

.cd-tab-item:hover {
    color: #0f172a !important;
    background: #f8fafc !important;
}

.cd-tab-item.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.25) !important;
}

.cd-tab-badge {
    padding: 1px 7px !important;
    border-radius: 10px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
}

.cd-tab-item.active .cd-tab-badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* ==========================================================================
   Tier 3: Modern Card + Sub-Card Enterprise Stream
   ========================================================================== */
.cd-terms-card-stream {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    width: 100% !important;
}

.cd-terms-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 14px 18px !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease !important;
    position: relative !important;
}

.cd-terms-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06) !important;
}

.cd-terms-subcards-row {
    display: grid !important;
    grid-template-columns: 240px 170px 1fr 190px 180px !important;
    gap: 12px !important;
    align-items: center !important;
    width: 100% !important;
}

@media (max-width: 1366px) {
    .cd-terms-subcards-row {
        grid-template-columns: 220px 150px 1fr 160px 170px !important;
        gap: 10px !important;
    }
}

@media (max-width: 1199.98px) {
    .cd-terms-subcards-row {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .cd-terms-subcards-row {
        grid-template-columns: 1fr !important;
    }
}

/* Sub-Card 1: Identity & Categorization */
.cd-subcard-identity {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
}

.cd-term-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 15px !important;
    flex-shrink: 0 !important;
}

.cd-term-icon-offers { background: #eff6ff !important; color: #2563eb !important; }
.cd-term-icon-legal { background: #ecfdf5 !important; color: #059669 !important; }
.cd-term-icon-agency { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-term-icon-commercial { background: #fffbeb !important; color: #d97706 !important; }

.cd-term-meta {
    min-width: 0 !important;
    flex: 1 !important;
}

.cd-term-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    text-overflow: ellipsis !important;
    overflow: hidden !important;
    white-space: nowrap !important;
}

.cd-term-chips {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 4px !important;
    flex-wrap: wrap !important;
}

.cd-seq-chip {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    background: #f1f5f9 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
}

.cd-audience-pill {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    padding: 1px 7px !important;
    border-radius: 4px !important;
}

.cd-audience-candidates { background: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #dbeafe !important; }
.cd-audience-website { background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #d1fae5 !important; }
.cd-audience-vendors { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fef3c7 !important; }
.cd-audience-agencies { background: #f5f3ff !important; color: #6d28d9 !important; border: 1px solid #ede9fe !important; }

/* Sub-Card 2: Health & Volume Metrics */
.cd-subcard-health {
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
}

.cd-status-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
    width: fit-content !important;
}

.cd-status-configured {
    background: #ecfdf5 !important;
    color: #047857 !important;
    border: 1px solid #a7f3d0 !important;
}

.cd-status-configured i {
    color: #10b981 !important;
    font-size: 8px !important;
}

.cd-status-empty {
    background: #fffbeb !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
}

.cd-status-empty i {
    color: #f59e0b !important;
    font-size: 8px !important;
}

.cd-volume-meta {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
}

.cd-fallback-pill {
    font-size: 10px !important;
    color: #475569 !important;
    background: #f8fafc !important;
    border: 1px dashed #cbd5e1 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    margin-top: 2px !important;
    display: inline-block !important;
}

/* Sub-Card 3: Content Excerpt */
.cd-subcard-excerpt {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 8px 11px !important;
    min-height: 52px !important;
    display: flex !important;
    align-items: center !important;
}

.cd-excerpt-text {
    font-size: 11.5px !important;
    color: #334155 !important;
    line-height: 1.4 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
    word-break: break-word !important;
}

.cd-excerpt-empty {
    font-style: italic !important;
    color: #94a3b8 !important;
}

/* Sub-Card 4: Tokens & Route */
.cd-subcard-tokens {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}

.cd-token-title {
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    color: #94a3b8 !important;
}

.cd-token-pills {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 3px !important;
}

.cd-token-chip {
    font-size: 10px !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
}

.cd-token-chip:hover {
    background: #dbeafe !important;
}

.cd-route-chip {
    font-size: 10.5px !important;
    color: #059669 !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    padding: 1px 7px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* Sub-Card 5: Instant Actions */
.cd-subcard-actions {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 6px !important;
}

.cd-btn-action {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    padding: 6px 11px !important;
    border-radius: 7px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    text-decoration: none !important;
    border: 1px solid transparent !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.cd-btn-preview {
    background: #eff6ff !important;
    color: #2563eb !important;
    border-color: #bfdbfe !important;
}

.cd-btn-preview:hover {
    background: #2563eb !important;
    color: #ffffff !important;
}

.cd-btn-edit {
    background: #009ef7 !important;
    color: #ffffff !important;
}

.cd-btn-edit:hover {
    background: #0086d4 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.3) !important;
}

.cd-btn-copy {
    background: #f8fafc !important;
    color: #475569 !important;
    border-color: #e2e8f0 !important;
    padding: 6px 8px !important;
}

.cd-btn-copy:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}

/* Sub-Card 6: Bottom Audit Strip */
.cd-terms-bottom-strip {
    margin-top: 10px !important;
    padding-top: 8px !important;
    border-top: 1px dashed #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 10.5px !important;
    color: #94a3b8 !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}

.cd-strip-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.cd-strip-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* ==========================================================================
   Alternative High-Density Table View Mode
   ========================================================================== */
.cd-terms-table-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    overflow: hidden !important;
    width: 100% !important;
}

.cd-table {
    width: 100% !important;
    border-collapse: collapse !important;
    margin: 0 !important;
}

.cd-table thead th {
    background: #f8fafc !important;
    padding: 10px 14px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    white-space: nowrap !important;
}

.cd-table tbody td {
    padding: 12px 14px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    font-size: 12px !important;
    vertical-align: middle !important;
}

.cd-table tbody tr:hover td {
    background: #fafafa !important;
}

/* ==========================================================================
   Slide-Over Off-Canvas Preview Drawer
   ========================================================================== */
.cd-drawer-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(15, 23, 42, 0.4) !important;
    backdrop-filter: blur(2px) !important;
    z-index: 1050 !important;
    display: none;
    opacity: 0;
    transition: opacity 0.2s ease !important;
}

.cd-drawer-overlay.active {
    display: block !important;
    opacity: 1 !important;
}

.cd-drawer {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 660px !important;
    max-width: 95vw !important;
    background: #ffffff !important;
    box-shadow: -4px 0 24px rgba(15, 23, 42, 0.15) !important;
    z-index: 1060 !important;
    transform: translateX(100%) !important;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    display: flex !important;
    flex-direction: column !important;
}

.cd-drawer.active {
    transform: translateX(0) !important;
}

.cd-drawer-header {
    padding: 16px 20px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #ffffff !important;
}

.cd-drawer-body {
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 20px !important;
    background: #ffffff !important;
}

.cd-drawer-footer {
    padding: 14px 20px !important;
    border-top: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #f8fafc !important;
}

.cd-drawer-reading-pane {
    font-size: 13px !important;
    line-height: 1.65 !important;
    color: #1e293b !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 16px !important;
    min-height: 250px !important;
}

.cd-drawer-reading-pane h1,
.cd-drawer-reading-pane h2,
.cd-drawer-reading-pane h3 {
    color: #0f172a !important;
    margin-top: 14px !important;
    margin-bottom: 8px !important;
}

.cd-drawer-reading-pane p {
    margin-bottom: 10px !important;
}

.cd-drawer-close {
    border: none !important;
    background: transparent !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    font-size: 15px !important;
    transition: all 0.15s ease !important;
}

.cd-drawer-close:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}

/* Empty State */
.cd-empty-search-state {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 40px 20px !important;
    text-align: center !important;
    display: none;
    margin-top: 10px !important;
}

.cd-empty-search-state i {
    font-size: 36px !important;
    color: #cbd5e1 !important;
    margin-bottom: 10px !important;
}
</style>
@endpush

@php
    use App\Helper\GeneralMethod;
@endphp

@section('content')
<div class="cd-terms-wrap">

    {{-- ======================================================================
         TIER 0: EXECUTIVE KPI SUMMARY METRIC CARDS
         ====================================================================== --}}
    <div class="cd-kpi-grid">
        {{-- Card 1: Total Terms --}}
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-blue">
                <i class="fa fa-file-contract"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_total_count">{{ $kpiStats['total'] ?? count($termsCatalog) }}</div>
                <div class="cd-kpi-title">Total Default Terms</div>
            </div>
        </div>

        {{-- Card 2: Configured & Live --}}
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-green">
                <i class="fa fa-circle-check"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_configured_count" style="color:#059669;">{{ $kpiStats['configured'] ?? 0 }}</div>
                <div class="cd-kpi-title">Configured & Live</div>
            </div>
        </div>

        {{-- Card 3: Needs Setup / Empty --}}
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-amber">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_needs_setup_count" style="color:#d97706;">{{ $kpiStats['needs_setup'] ?? 0 }}</div>
                <div class="cd-kpi-title">Needs Setup (Empty)</div>
            </div>
        </div>

        {{-- Card 4: Offer Letter Templates --}}
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-purple">
                <i class="fa fa-briefcase"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_offers_count" style="color:#7c3aed;">{{ $kpiStats['offers'] ?? 0 }}</div>
                <div class="cd-kpi-title">Candidate Offer Letters</div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Border #e2e8f0)
         ====================================================================== --}}
    <div class="cd-terms-header-card">
        {{-- Left: Identity Branding --}}
        <div class="cd-header-left">
            <div class="cd-header-icon">
                <i class="fa fa-scale-balanced"></i>
            </div>
            <div>
                <div class="cd-header-title">Default Legal & Commercial Terms</div>
                <div class="cd-header-sub">Configure platform-wide compliance policies, candidate offer letters, and agency commercial agreements.</div>
            </div>
        </div>

        {{-- Right: Actions Suite (Search + View Switcher) --}}
        <div class="cd-header-right">
            {{-- Dynamic Search Sub-Card with 300ms debounce and clear button --}}
            <div class="cd-terms-search-subcard">
                <i class="fa fa-search"></i>
                <input type="text" id="cd_terms_search" placeholder="Search terms, audience, text..." autocomplete="off">
                <button type="button" class="cd-terms-search-clear" id="cd_terms_search_clear" title="Clear Search">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            {{-- View Mode Switcher (Card Stream vs Compact Table) --}}
            <div class="cd-view-switcher" role="group" aria-label="View Switcher">
                <button type="button" class="cd-view-btn active" id="cd_view_btn_stream" data-view="stream" title="Card Stream View">
                    <i class="fa fa-table-cells-large"></i> Stream
                </button>
                <button type="button" class="cd-view-btn" id="cd_view_btn_table" data-view="table" title="Compact Table View">
                    <i class="fa fa-table-list"></i> Table
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         TIER 2: SUB-TABS FILTER CARD (Pure White #ffffff, Border #e2e8f0)
         ====================================================================== --}}
    <div class="cd-terms-tabs-card">
        <ul class="cd-tabs-list" id="cd_terms_tabs">
            <li>
                <a href="javascript:void(0);" class="cd-tab-item active" data-tab="all">
                    <i class="fa fa-list"></i>
                    <span>All Templates</span>
                    <span class="cd-tab-badge" id="tab_badge_all">{{ $kpiStats['total'] ?? count($termsCatalog) }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="cd-tab-item" data-tab="offers">
                    <i class="fa fa-briefcase"></i>
                    <span>Offer Letters</span>
                    <span class="cd-tab-badge" id="tab_badge_offers">{{ $kpiStats['offers'] ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="cd-tab-item" data-tab="legal">
                    <i class="fa fa-scale-balanced"></i>
                    <span>Legal & Website</span>
                    <span class="cd-tab-badge" id="tab_badge_legal">{{ $kpiStats['legal'] ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="cd-tab-item" data-tab="commercial">
                    <i class="fa fa-handshake"></i>
                    <span>Agency & Commercial</span>
                    <span class="cd-tab-badge" id="tab_badge_commercial">{{ $kpiStats['commercial'] ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="cd-tab-item" data-tab="configured">
                    <i class="fa fa-circle-check text-success"></i>
                    <span>Configured</span>
                    <span class="cd-tab-badge" id="tab_badge_configured">{{ $kpiStats['configured'] ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="cd-tab-item" data-tab="needs_setup">
                    <i class="fa fa-triangle-exclamation text-warning"></i>
                    <span>Needs Setup</span>
                    <span class="cd-tab-badge" id="tab_badge_needs_setup">{{ $kpiStats['needs_setup'] ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- ======================================================================
         TIER 3: MODERN CARD + SUB-CARD ENTERPRISE STREAM
         ====================================================================== --}}
    <div class="cd-terms-card-stream" id="cd_terms_card_stream">
        @forelse($termsCatalog as $term)
            @php
                $categoryIconClass = 'cd-term-icon-legal';
                if ($term['category'] === 'offers') {
                    $categoryIconClass = 'cd-term-icon-offers';
                } elseif ($term['category'] === 'agency') {
                    $categoryIconClass = 'cd-term-icon-agency';
                } elseif ($term['category'] === 'commercial') {
                    $categoryIconClass = 'cd-term-icon-commercial';
                }

                $audienceClass = 'cd-audience-website';
                if ($term['audience_key'] === 'candidates') {
                    $audienceClass = 'cd-audience-candidates';
                } elseif ($term['audience_key'] === 'vendors') {
                    $audienceClass = 'cd-audience-vendors';
                } elseif ($term['audience_key'] === 'agencies') {
                    $audienceClass = 'cd-audience-agencies';
                }
            @endphp

            <div class="cd-terms-card"
                 id="term_card_{{ $term['id'] }}"
                 data-id="{{ $term['id'] }}"
                 data-category="{{ $term['category'] }}"
                 data-configured="{{ $term['is_configured'] ? 'yes' : 'no' }}"
                 data-search="{{ strtolower($term['title'] . ' ' . $term['audience'] . ' ' . $term['category_label'] . ' ' . $term['seq_id'] . ' ' . $term['excerpt']) }}">
                
                {{-- Upper 5 Discrete Sub-Cards Grid --}}
                <div class="cd-terms-subcards-row">
                    {{-- Sub-Card 1: Identity & Scope --}}
                    <div class="cd-subcard-identity">
                        <div class="cd-term-icon {{ $categoryIconClass }}">
                            <i class="fa {{ $term['icon'] }}"></i>
                        </div>
                        <div class="cd-term-meta">
                            <div class="cd-term-title" title="{{ $term['title'] }}">{{ $term['title'] }}</div>
                            <div class="cd-term-chips">
                                <span class="cd-seq-chip">{{ $term['seq_id'] }}</span>
                                <span class="cd-audience-pill {{ $audienceClass }}">{{ $term['audience'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card 2: Health & Volume Metrics --}}
                    <div class="cd-subcard-health">
                        @if($term['is_configured'])
                            <span class="cd-status-badge cd-status-configured">
                                <i class="fa fa-circle"></i> Configured
                            </span>
                            <div class="cd-volume-meta">
                                <strong>{{ number_format($term['char_count']) }}</strong> chars • ~{{ number_format($term['word_count']) }} words
                            </div>
                        @else
                            <span class="cd-status-badge cd-status-empty">
                                <i class="fa fa-circle"></i> Needs Setup
                            </span>
                            <div class="cd-volume-meta text-muted">
                                0 characters defined
                            </div>
                            @if(!empty($term['fallback_to']))
                                <span class="cd-fallback-pill" title="Inherited starter copy">
                                    <i class="fa fa-arrow-turn-down me-1"></i> Inherits {{ $term['fallback_to'] }}
                                </span>
                            @endif
                        @endif
                    </div>

                    {{-- Sub-Card 3: Live Excerpt Snippet --}}
                    <div class="cd-subcard-excerpt" title="Live Preview Excerpt">
                        <div class="cd-excerpt-text {{ !$term['is_configured'] ? 'cd-excerpt-empty' : '' }}">
                            "{{ $term['excerpt'] }}"
                        </div>
                    </div>

                    {{-- Sub-Card 4: Supported Dynamic Tokens & Route --}}
                    <div class="cd-subcard-tokens">
                        @if(!empty($term['public_route']))
                            <div class="cd-token-title">Public Endpoint</div>
                            <div>
                                <span class="cd-route-chip" title="Live served URL">
                                    <i class="fa fa-globe"></i> {{ $term['public_route'] }}
                                </span>
                            </div>
                        @elseif(!empty($term['tokens']) && count($term['tokens']) > 0)
                            <div class="cd-token-title">Supported Tokens</div>
                            <div class="cd-token-pills">
                                @foreach(array_slice($term['tokens'], 0, 3) as $token)
                                    <span class="cd-token-chip btn-copy-token" data-token="{{ $token }}" title="Click to copy {{ $token }}">
                                        {{ $token }}
                                    </span>
                                @endforeach
                                @if(count($term['tokens']) > 3)
                                    <span class="cd-token-chip" style="background:#f1f5f9; color:#64748b; border-color:#e2e8f0;" title="{{ count($term['tokens']) - 3 }} more tokens available in Preview Drawer">
                                        +{{ count($term['tokens']) - 3 }} more
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="cd-token-title">Scope</div>
                            <span class="text-muted fs-8">{{ $term['category_label'] }} Policy</span>
                        @endif
                    </div>

                    {{-- Sub-Card 5: Instant Actions Suite --}}
                    <div class="cd-subcard-actions">
                        <button type="button"
                                class="cd-btn-action cd-btn-preview btn-open-term-preview"
                                data-id="{{ $term['id'] }}"
                                title="Quick Preview Content">
                            <i class="fa fa-eye"></i> Preview
                        </button>
                        <a href="{{ route('superadmin.company.defaultTerms', [$term['encrypted_id']]) }}"
                           class="cd-btn-action cd-btn-edit"
                           title="Edit in Master Editor">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button type="button"
                                class="cd-btn-action cd-btn-copy btn-copy-term-text"
                                data-id="{{ $term['id'] }}"
                                title="Copy Text to Clipboard">
                            <i class="fa fa-clone"></i>
                        </button>
                    </div>
                </div>

                {{-- Sub-Card 6: Bottom Audit Strip --}}
                <div class="cd-terms-bottom-strip">
                    <div class="cd-strip-left">
                        <span class="cd-strip-item">
                            <i class="fa fa-database"></i> Storage: <strong>{{ $term['storage'] }}</strong>
                        </span>
                        <span class="cd-strip-item">
                            <i class="fa fa-code-branch"></i> Field: <code>{{ $term['db_field'] }}</code>
                        </span>
                        <span class="cd-strip-item">
                            <i class="fa fa-lock text-success"></i> Encrypted Token: <strong>Active</strong>
                        </span>
                    </div>
                    <div class="cd-strip-right">
                        <span>{{ $term['scope_note'] }}</span>
                    </div>
                </div>

            </div>
        @empty
            <div class="card p-5 text-center text-muted">
                No default terms records configured.
            </div>
        @endforelse
    </div>

    {{-- ======================================================================
         ALTERNATIVE: HIGH-DENSITY COMPACT TABLE VIEW MODE
         ====================================================================== --}}
    <div class="cd-terms-table-card" id="cd_terms_table_view" style="display:none;">
        <div class="table-responsive">
            <table class="cd-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">Seq ID</th>
                        <th style="min-width: 220px;">Term Name & Purpose</th>
                        <th style="width: 140px;">Target Audience</th>
                        <th style="width: 140px;">Status & Health</th>
                        <th style="min-width: 260px;">Live Content Excerpt</th>
                        <th style="width: 160px;">Storage / Field</th>
                        <th style="width: 130px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="cd_terms_table_body">
                    @foreach($termsCatalog as $term)
                        @php
                            $audienceClass = 'cd-audience-website';
                            if ($term['audience_key'] === 'candidates') {
                                $audienceClass = 'cd-audience-candidates';
                            } elseif ($term['audience_key'] === 'vendors') {
                                $audienceClass = 'cd-audience-vendors';
                            } elseif ($term['audience_key'] === 'agencies') {
                                $audienceClass = 'cd-audience-agencies';
                            }

                            $categoryIconClass = 'cd-term-icon-legal';
                            if ($term['category'] === 'offers') {
                                $categoryIconClass = 'cd-term-icon-offers';
                            } elseif ($term['category'] === 'agency') {
                                $categoryIconClass = 'cd-term-icon-agency';
                            } elseif ($term['category'] === 'commercial') {
                                $categoryIconClass = 'cd-term-icon-commercial';
                            }
                        @endphp
                        <tr class="cd-terms-table-row"
                            id="term_row_{{ $term['id'] }}"
                            data-id="{{ $term['id'] }}"
                            data-category="{{ $term['category'] }}"
                            data-configured="{{ $term['is_configured'] ? 'yes' : 'no' }}"
                            data-search="{{ strtolower($term['title'] . ' ' . $term['audience'] . ' ' . $term['category_label'] . ' ' . $term['seq_id'] . ' ' . $term['excerpt']) }}">
                            <td>
                                <span class="cd-seq-chip">{{ $term['seq_id'] }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cd-term-icon {{ $categoryIconClass }}" style="width:28px; height:28px; font-size:12px; border-radius:6px;">
                                        <i class="fa {{ $term['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-7">{{ $term['title'] }}</div>
                                        <div class="text-muted fs-8">{{ $term['category_label'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="cd-audience-pill {{ $audienceClass }}">{{ $term['audience'] }}</span>
                            </td>
                            <td>
                                @if($term['is_configured'])
                                    <span class="cd-status-badge cd-status-configured" style="font-size:10px; padding:1px 6px;">
                                        <i class="fa fa-circle"></i> Configured
                                    </span>
                                    <div class="text-muted fs-8 mt-1">{{ number_format($term['char_count']) }} chars</div>
                                @else
                                    <span class="cd-status-badge cd-status-empty" style="font-size:10px; padding:1px 6px;">
                                        <i class="fa fa-circle"></i> Needs Setup
                                    </span>
                                    <div class="text-muted fs-8 mt-1">Empty</div>
                                @endif
                            </td>
                            <td>
                                <div class="cd-excerpt-text {{ !$term['is_configured'] ? 'cd-excerpt-empty' : '' }}" style="-webkit-line-clamp: 1 !important; font-size: 11px;">
                                    "{{ $term['excerpt'] }}"
                                </div>
                            </td>
                            <td>
                                <div class="fs-8 text-dark fw-semibold"><code>{{ $term['db_field'] }}</code></div>
                                <div class="fs-8 text-muted">{{ $term['storage'] }}</div>
                            </td>
                            <td style="text-align: right;">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-icon btn-sm btn-light-primary btn-open-term-preview" data-id="{{ $term['id'] }}" title="Preview" style="width:28px; height:28px;">
                                        <i class="fa fa-eye fs-7"></i>
                                    </button>
                                    <a href="{{ route('superadmin.company.defaultTerms', [$term['encrypted_id']]) }}" class="btn btn-icon btn-sm btn-light-info" title="Edit" style="width:28px; height:28px;">
                                        <i class="fa fa-edit fs-7"></i>
                                    </a>
                                    <button type="button" class="btn btn-icon btn-sm btn-light-secondary btn-copy-term-text" data-id="{{ $term['id'] }}" title="Copy Text" style="width:28px; height:28px;">
                                        <i class="fa fa-clone fs-7"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Empty Search / Filter State --}}
    <div class="cd-empty-search-state" id="cd_terms_empty_state">
        <i class="fa fa-folder-open"></i>
        <h5 class="fw-bold text-dark mb-1">No Matching Terms Found</h5>
        <p class="text-muted fs-7 mb-3">No default terms match your active search keyword or category filter.</p>
        <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btn_reset_terms_filter">
            <i class="fa fa-rotate-left me-1"></i> Reset Filters
        </button>
    </div>

    {{-- ======================================================================
         OFF-CANVAS SLIDE-OVER PREVIEW DRAWER & OVERLAY
         ====================================================================== --}}
    <div class="cd-drawer-overlay" id="cd_drawer_overlay"></div>

    <div class="cd-drawer" id="kt_drawer_term_preview" aria-hidden="true" role="dialog">
        {{-- Drawer Header --}}
        <div class="cd-drawer-header">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="cd-term-icon cd-term-icon-offers" id="drawer_term_icon" style="width:36px; height:36px; font-size:14px; border-radius:8px;">
                    <i class="fa fa-file-contract"></i>
                </div>
                <div class="min-w-0">
                    <h5 class="fw-bolder text-dark mb-1 text-truncate" id="drawer_term_title" style="font-size: 15px;">Fulltime Offer Letter</h5>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="cd-seq-chip" id="drawer_term_seq">#TERM-002</span>
                        <span class="cd-audience-pill cd-audience-candidates" id="drawer_term_audience">For Candidates</span>
                        <span class="cd-status-badge cd-status-configured" id="drawer_term_status" style="font-size:10.5px; padding:1px 6px;">
                            <i class="fa fa-circle"></i> Configured
                        </span>
                    </div>
                </div>
            </div>
            <button type="button" class="cd-drawer-close" id="btn_close_drawer" title="Close Preview (Esc)">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Drawer Body --}}
        <div class="cd-drawer-body">
            {{-- Storage & Fallback Meta Strip --}}
            <div class="p-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; font-size:11.5px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="text-muted">Storage:</span> <strong id="drawer_meta_storage" class="text-dark">global_settings</strong> •
                        <span class="text-muted">Field:</span> <code id="drawer_meta_field">offer_terms</code>
                    </div>
                    <div>
                        <span class="text-muted">Volume:</span> <strong id="drawer_meta_volume" class="text-dark">25,750 chars</strong>
                    </div>
                </div>
                <div id="drawer_fallback_notice" class="mt-2 text-primary" style="display:none; font-size:11px;">
                    <i class="fa fa-arrow-turn-down me-1"></i> <span id="drawer_fallback_text">Inherits Fulltime Offer Letter if unconfigured</span>
                </div>
            </div>

            {{-- Interactive Tokens Inspector --}}
            <div id="drawer_tokens_container" class="mb-3" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-dark" style="letter-spacing:0.2px;">Supported Dynamic Tokens</span>
                    <span class="text-muted fs-8">Click any token to copy</span>
                </div>
                <div class="d-flex flex-wrap gap-1" id="drawer_tokens_list"></div>
            </div>

            {{-- Public Route Endpoint Notice --}}
            <div id="drawer_route_container" class="mb-3" style="display:none;">
                <div class="fw-bold fs-7 text-dark mb-1">Public Endpoint Route</div>
                <div class="p-2 bg-light rounded d-flex align-items-center justify-content-between">
                    <code id="drawer_route_url" class="text-success fs-7">/privacy-policy</code>
                    <a href="javascript:void(0);" id="drawer_route_link" target="_blank" class="btn btn-xs btn-light-success fs-8">
                        <i class="fa fa-arrow-up-right-from-square me-1"></i> Visit URL
                    </a>
                </div>
            </div>

            {{-- Reading Container --}}
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold fs-7 text-dark">Rendered Content Preview</span>
                <span class="text-muted fs-8" id="drawer_reading_word_count">~4,200 words</span>
            </div>
            <div class="cd-drawer-reading-pane" id="drawer_content_body">
                {{-- Formatted HTML populated by JavaScript --}}
            </div>
        </div>

        {{-- Drawer Footer Toolbar --}}
        <div class="cd-drawer-footer">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btn_drawer_copy_html" title="Copy Raw HTML">
                    <i class="fa fa-code me-1"></i> Copy HTML
                </button>
                <button type="button" class="btn btn-sm btn-light-dark fw-bold" id="btn_drawer_copy_text" title="Copy Plain Text">
                    <i class="fa fa-clone me-1"></i> Copy Text
                </button>
            </div>
            <div>
                <a href="javascript:void(0);" id="btn_drawer_open_editor" class="btn btn-sm btn-primary fw-bold">
                    <i class="fa fa-edit me-1"></i> Open Dedicated Editor
                </a>
            </div>
        </div>
    </div>

    {{-- JSON In-Memory Bridge for High-Speed Client-First Execution --}}
    <script type="application/json" id="cd_terms_catalog_json">
        {!! json_encode($termsCatalog, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>

</div>
@endsection

@push('footer-script')
<script>
$(document).ready(function() {
    var activeTab = 'all';
    var searchKeyword = '';

    // Switch between Card Stream and Table view
    function applyActiveViewMode() {
        var currentMode = $('.cd-view-btn.active').data('view') || 'stream';
        if (currentMode === 'table') {
            $('#cd_terms_card_stream').hide();
            $('#cd_terms_table_view').show();
        } else {
            $('#cd_terms_table_view').hide();
            $('#cd_terms_card_stream').show();
        }
    }

    // Filter Engine for Card Stream and Compact Table
    function filterTerms() {
        var visibleCount = 0;
        var q = searchKeyword.toLowerCase().trim();

        function checkMatch(category, configured, searchText) {
            var matchesTab = false;
            if (activeTab === 'all') {
                matchesTab = true;
            } else if (activeTab === 'offers') {
                matchesTab = (category === 'offers');
            } else if (activeTab === 'legal') {
                matchesTab = (category === 'legal');
            } else if (activeTab === 'commercial') {
                matchesTab = (category === 'commercial' || category === 'agency');
            } else if (activeTab === 'configured') {
                matchesTab = (configured === 'yes');
            } else if (activeTab === 'needs_setup') {
                matchesTab = (configured === 'no');
            }

            var matchesSearch = (q === '' || searchText.indexOf(q) !== -1);
            return matchesTab && matchesSearch;
        }

        $('.cd-terms-card').each(function() {
            var $el = $(this);
            var isMatch = checkMatch($el.data('category'), $el.data('configured'), $el.data('search') || '');
            if (isMatch) {
                $el.show();
                visibleCount++;
            } else {
                $el.hide();
            }
        });

        $('.cd-terms-table-row').each(function() {
            var $el = $(this);
            var isMatch = checkMatch($el.data('category'), $el.data('configured'), $el.data('search') || '');
            if (isMatch) {
                $el.show();
            } else {
                $el.hide();
            }
        });

        if (visibleCount === 0) {
            $('#cd_terms_empty_state').show();
            $('#cd_terms_card_stream').hide();
            $('#cd_terms_table_view').hide();
        } else {
            $('#cd_terms_empty_state').hide();
            applyActiveViewMode();
        }
    }

    // Dynamic Search Clear Button Toggle with 300ms debounce
    var searchDebounceTimer = null;
    $('#cd_terms_search').on('input', function() {
        var val = $(this).val();
        if (val.trim().length > 0) {
            $('#cd_terms_search_clear').css('display', 'flex');
        } else {
            $('#cd_terms_search_clear').hide();
        }

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(function() {
            searchKeyword = val;
            filterTerms();
        }, 150);
    });

    $('#cd_terms_search_clear').on('click', function() {
        $('#cd_terms_search').val('');
        $(this).hide();
        searchKeyword = '';
        filterTerms();
        $('#cd_terms_search').focus();
    });

    // Sub-Tabs Handler
    $('.cd-tab-item').on('click', function(e) {
        e.preventDefault();
        $('.cd-tab-item').removeClass('active');
        $(this).addClass('active');
        activeTab = $(this).data('tab');
        filterTerms();
    });

    // Reset Filters Button
    $('#btn_reset_terms_filter').on('click', function() {
        $('#cd_terms_search').val('');
        $('#cd_terms_search_clear').hide();
        searchKeyword = '';
        $('.cd-tab-item[data-tab="all"]').trigger('click');
    });

    // View Switcher Handlers
    $('.cd-view-btn').on('click', function() {
        $('.cd-view-btn').removeClass('active');
        $(this).addClass('active');
        var view = $(this).data('view');
        try {
            localStorage.setItem('jero_terms_view_mode', view);
        } catch(e) {}
        applyActiveViewMode();
    });

    // Restore saved view preference
    try {
        var savedMode = localStorage.getItem('jero_terms_view_mode') || 'stream';
        if (savedMode === 'table') {
            $('.cd-view-btn').removeClass('active');
            $('#cd_view_btn_table').addClass('active');
        }
    } catch(e) {}
    applyActiveViewMode();

    // =========================================================================
    // Phase 6: Slide-Over Off-Canvas Preview Drawer & Clipboard Bridge
    // =========================================================================
    var termsCatalog = [];
    try {
        var rawJson = $('#cd_terms_catalog_json').html();
        if (rawJson) {
            termsCatalog = JSON.parse(rawJson);
        }
    } catch(e) {
        console.error('Failed to parse terms catalog JSON', e);
    }

    var termsMap = {};
    $.each(termsCatalog, function(i, term) {
        termsMap[term.id] = term;
    });

    var currentDrawerTerm = null;

    function numberWithCommas(x) {
        return (x || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function getCategoryClass(cat) {
        if (cat === 'offers') return 'cd-term-icon-offers';
        if (cat === 'agency') return 'cd-term-icon-agency';
        if (cat === 'commercial') return 'cd-term-icon-commercial';
        return 'cd-term-icon-legal';
    }

    function getAudienceClass(aud) {
        if (aud === 'candidates') return 'cd-audience-candidates';
        if (aud === 'vendors') return 'cd-audience-vendors';
        if (aud === 'agencies') return 'cd-audience-agencies';
        return 'cd-audience-website';
    }

    function showCopyToast(title) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: title,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    }

    function copyToClipboard(text, successTitle) {
        if (!text || String(text).trim() === '') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'No content to copy',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
            return;
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showCopyToast(successTitle || 'Copied to clipboard');
            }).catch(function() {
                fallbackCopy(text, successTitle);
            });
        } else {
            fallbackCopy(text, successTitle);
        }
    }

    function fallbackCopy(text, successTitle) {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showCopyToast(successTitle || 'Copied to clipboard');
        } catch(err) {
            console.error('Fallback copy failed', err);
        }
        document.body.removeChild(textArea);
    }

    function openTermPreview(termId) {
        var term = termsMap[termId];
        if (!term) return;

        currentDrawerTerm = term;

        // Title and ID
        $('#drawer_term_title').text(term.title);
        $('#drawer_term_seq').text(term.seq_id);

        // Icon
        $('#drawer_term_icon').removeClass().addClass('cd-term-icon ' + getCategoryClass(term.category));
        $('#drawer_term_icon').html('<i class="fa ' + term.icon + '"></i>');

        // Audience
        $('#drawer_term_audience').removeClass().addClass('cd-audience-pill ' + getAudienceClass(term.audience_key)).text(term.audience);

        // Status
        if (term.is_configured) {
            $('#drawer_term_status').removeClass('cd-status-empty').addClass('cd-status-configured').html('<i class="fa fa-circle"></i> Configured');
        } else {
            $('#drawer_term_status').removeClass('cd-status-configured').addClass('cd-status-empty').html('<i class="fa fa-circle"></i> Needs Setup');
        }

        // Meta info
        $('#drawer_meta_storage').text(term.storage);
        $('#drawer_meta_field').text(term.db_field);
        $('#drawer_meta_volume').text(numberWithCommas(term.char_count) + ' chars');
        $('#drawer_reading_word_count').text('~' + numberWithCommas(term.word_count) + ' words');

        // Fallback note
        if (term.fallback_to) {
            $('#drawer_fallback_text').text('Inherits ' + term.fallback_to + ' as editable starter copy.');
            $('#drawer_fallback_notice').show();
        } else {
            $('#drawer_fallback_notice').hide();
        }

        // Tokens Inspector
        if (term.tokens && term.tokens.length > 0) {
            var tokenHtml = '';
            $.each(term.tokens, function(idx, tok) {
                tokenHtml += '<span class="cd-token-chip btn-copy-token" data-token="' + tok + '" title="Click to copy ' + tok + '">' + tok + '</span>';
            });
            $('#drawer_tokens_list').html(tokenHtml);
            $('#drawer_tokens_container').show();
        } else {
            $('#drawer_tokens_container').hide();
        }

        // Route URL
        if (term.public_route) {
            $('#drawer_route_url').text(term.public_route);
            $('#drawer_route_link').attr('href', '{{ url("/") }}' + term.public_route);
            $('#drawer_route_container').show();
        } else {
            $('#drawer_route_container').hide();
        }

        // Formatted Content Body
        if (term.is_configured) {
            if (term.id === 13) {
                // Formatted commercial summary
                var commHtml = '<div class="alert alert-primary mb-3 py-2 px-3 fs-8"><i class="fa fa-info-circle me-1"></i> Global fallback agency commercials configured in <code>agency_commercial_terms</code> table.</div>';
                commHtml += '<pre style="background:#0f172a; color:#38bdf8; padding:14px; border-radius:8px; font-size:12px; overflow-x:auto; line-height:1.5;">' + escapeHtml(term.raw_content) + '</pre>';
                $('#drawer_content_body').html(commHtml);
            } else {
                $('#drawer_content_body').html(term.raw_content);
            }
        } else {
            var emptyHtml = '<div class="text-center py-5">' +
                '<div class="mb-3"><i class="fa fa-file-circle-exclamation" style="font-size:38px; color:#f59e0b;"></i></div>' +
                '<h6 class="fw-bold text-dark mb-1">No Master Copy Configured Yet</h6>' +
                '<p class="text-muted fs-7 mb-3" style="max-width:400px; margin:0 auto;">' +
                (term.fallback_to ? 'This template currently inherits <strong>' + term.fallback_to + '</strong> when generated in ATS. You can create a specialized copy anytime.' : 'This regulatory/compliance policy has not been populated.') +
                '</p>' +
                '<a href="{{ url("/super-admin/default-terms") }}/' + term.encrypted_id + '" class="btn btn-sm btn-primary fw-bold">' +
                '<i class="fa fa-edit me-1"></i> Edit & Initialize' +
                '</a>' +
                '</div>';
            $('#drawer_content_body').html(emptyHtml);
        }

        // Editor URL
        $('#btn_drawer_open_editor').attr('href', '{{ url("/super-admin/default-terms") }}/' + term.encrypted_id);

        // Open Drawer Animation
        $('#cd_drawer_overlay').addClass('active');
        $('#kt_drawer_term_preview').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeTermPreview() {
        $('#cd_drawer_overlay').removeClass('active');
        $('#kt_drawer_term_preview').removeClass('active');
        $('body').css('overflow', '');
    }

    // Drawer Open Triggers (Card and Table)
    $(document).on('click', '.btn-open-term-preview', function(e) {
        e.preventDefault();
        var termId = $(this).data('id');
        openTermPreview(termId);
    });

    // Drawer Close Triggers
    $('#btn_close_drawer, #cd_drawer_overlay').on('click', function() {
        closeTermPreview();
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#kt_drawer_term_preview').hasClass('active')) {
            closeTermPreview();
        }
    });

    // Drawer Copy Actions
    $('#btn_drawer_copy_html').on('click', function() {
        if (!currentDrawerTerm) return;
        copyToClipboard(currentDrawerTerm.raw_content, 'HTML copied to clipboard!');
    });

    $('#btn_drawer_copy_text').on('click', function() {
        if (!currentDrawerTerm) return;
        copyToClipboard(currentDrawerTerm.clean_text, 'Text copied to clipboard!');
    });

    // 1-Click Card and Table Text Copy
    $(document).on('click', '.btn-copy-term-text', function(e) {
        e.preventDefault();
        var termId = $(this).data('id');
        var term = termsMap[termId];
        if (term) {
            copyToClipboard(term.clean_text, 'Copied ' + term.title + ' text!');
        }
    });

    // 1-Click Token Copy
    $(document).on('click', '.btn-copy-token', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (token) {
            copyToClipboard(token, 'Copied: ' + token);
        }
    });
});
</script>
@endpush
