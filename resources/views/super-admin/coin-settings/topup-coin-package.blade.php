@extends('layouts.superadmin_layout')

@section('content')
<style>
/* ==========================================================================
   Super-Admin Top-Up Coin Packages — Enterprise Card + Sub-Card Design
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (overflow-x: hidden) Across Viewports
   ========================================================================== */

/* Universal Box Sizing & Font Stack */
.cd-topup-wrapper, .cd-topup-wrapper * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

/* Eliminate Metronic Fixed Toolbar Glitch & Anchor Header Offset */
#kt_toolbar {
    display: none !important;
    height: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
}

#kt_wrapper {
    padding-top: 65px !important;
}

@media (max-width: 991.98px) {
    #kt_wrapper {
        padding-top: 55px !important;
    }
}

.cd-topup-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding: 16px 20px 40px 20px !important;
}

/* ==========================================================================
   Tier 0: Executive KPI Summary Cards
   ========================================================================== */
.cd-topup-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 12px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

.cd-topup-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 16px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease !important;
}

.cd-topup-kpi-card:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05) !important;
}

.cd-topup-kpi-icon {
    width: 40px !important;
    height: 40px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 17px !important;
    flex-shrink: 0 !important;
}

.cd-topup-kpi-icon.icon-amber { background: #fffbeb !important; color: #d97706 !important; }
.cd-topup-kpi-icon.icon-emerald { background: #ecfdf5 !important; color: #059669 !important; }
.cd-topup-kpi-icon.icon-blue { background: #eff6ff !important; color: #2563eb !important; }
.cd-topup-kpi-icon.icon-purple { background: #faf5ff !important; color: #7c3aed !important; }

.cd-topup-kpi-label {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    margin-bottom: 1px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

.cd-topup-kpi-val {
    font-size: 19px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.cd-topup-kpi-badge {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
}

/* ==========================================================================
   Tier 1: Top Header Toolbar Card (Pure White #ffffff, In-Flow, Non-Fixed)
   ========================================================================== */
.cd-topup-header-card {
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
    position: static !important;
}

.cd-topup-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-topup-header-icon {
    width: 40px !important;
    height: 40px !important;
    border-radius: 8px !important;
    background: #fffbeb !important;
    color: #d97706 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 18px !important;
    flex-shrink: 0 !important;
}

.cd-topup-header-title {
    font-size: 15.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
}

.cd-topup-header-sub {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-topup-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-topup-search-subcard {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 36px !important;
    width: 250px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-topup-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-topup-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 12px !important;
    flex-shrink: 0 !important;
}

.cd-topup-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    line-height: 36px !important;
}

.cd-topup-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 12px !important;
}

.cd-topup-search-clear {
    background: none !important;
    border: none !important;
    color: #94a3b8 !important;
    font-size: 11px !important;
    padding: 0 !important;
    cursor: pointer !important;
    display: none;
}

.cd-topup-search-clear:hover {
    color: #475569 !important;
}

/* View Mode Switcher */
.cd-view-toggle-group {
    display: inline-flex !important;
    background: #f1f5f9 !important;
    border-radius: 8px !important;
    padding: 3px !important;
    gap: 2px !important;
}

.cd-view-btn {
    border: none !important;
    background: transparent !important;
    color: #64748b !important;
    padding: 5px 10px !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    transition: all 0.15s ease !important;
}

.cd-view-btn.active {
    background: #ffffff !important;
    color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
}

/* Add Top-Up Button */
.cd-topup-add-btn {
    height: 36px !important;
    padding: 0 14px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border-radius: 8px !important;
    background: #009ef7 !important;
    border: 1px solid #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

.cd-topup-add-btn:hover {
    background: #008be0 !important;
    border-color: #008be0 !important;
    color: #ffffff !important;
}

/* ==========================================================================
   Tier 2: Sub-Tabs Card
   ========================================================================== */
.cd-topup-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-topup-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    margin: 0 !important;
    padding: 0 !important;
}

.cd-topup-tab-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.cd-topup-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

.cd-topup-tab-btn.active {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

.cd-topup-tab-badge {
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
    line-height: 1 !important;
    transition: all 0.15s ease !important;
}

.cd-topup-tab-btn.active .cd-topup-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.cd-topup-tab-btn:not(.active) .badge-active-cnt { background: #dcfce7 !important; color: #15803d !important; }
.cd-topup-tab-btn:not(.active) .badge-inactive-cnt { background: #fee2e2 !important; color: #b91c1c !important; }
.cd-topup-tab-btn:not(.active) .badge-op-cnt { background: #fef3c7 !important; color: #b45309 !important; }
.cd-topup-tab-btn:not(.active) .badge-sal-cnt { background: #e0e7ff !important; color: #4338ca !important; }

/* ==========================================================================
   Tier 3: Primary Card Stream & Discrete Sub-Cards
   ========================================================================== */
.cd-topup-stream {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    width: 100% !important;
}

.cd-topup-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease-in-out !important;
    width: 100% !important;
    overflow: hidden !important;
}

.cd-topup-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* 5 Discrete Sub-Cards Grid */
.cd-topup-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.25fr) minmax(0, 1.35fr) minmax(0, 1.55fr) minmax(0, 1.45fr) !important;
    gap: 8px !important;
    padding: 12px 14px !important;
    width: 100% !important;
    align-items: stretch !important;
}

.cd-subcard {
    background: #f8fafc !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.cd-subcard-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    margin-bottom: 6px !important;
}

.cd-subcard-label {
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    white-space: nowrap !important;
}

/* Sub-Card 1: Identity & Classification */
.cd-subcard-id-body {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
}

.cd-topup-badge-icon {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 15px !important;
    flex-shrink: 0 !important;
}

.cd-topup-badge-icon.op-icon { background: #fffbeb !important; color: #d97706 !important; }
.cd-topup-badge-icon.sal-icon { background: #e0e7ff !important; color: #4338ca !important; }

.cd-topup-pkg-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-topup-meta-pills {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    margin-top: 4px !important;
}

.cd-pill {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 5px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    line-height: 1.3 !important;
}

.cd-pill-op { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.cd-pill-sal { background: #eef2ff !important; color: #4338ca !important; border: 1px solid #c7d2fe !important; }
.cd-pill-id { background: #f1f5f9 !important; color: #475569 !important; font-family: monospace !important; }

.cd-status-badge {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 5px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-status-badge.active { background: #dcfce7 !important; color: #15803d !important; }
.cd-status-badge.inactive { background: #fee2e2 !important; color: #b91c1c !important; }

/* Sub-Card 2: Volume & Tier */
.cd-vol-big {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.cd-vol-sub {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 3px !important;
}

/* Sub-Card 3: Pricing */
.cd-price-row {
    display: flex !important;
    align-items: baseline !important;
    justify-content: space-between !important;
    gap: 6px !important;
}

.cd-price-inr {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.cd-price-usd {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #475569 !important;
}

.cd-discount-tag {
    font-size: 10px !important;
    font-weight: 700 !important;
    background: #fee2e2 !important;
    color: #b91c1c !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
}

/* Sub-Card 4: Unit Economics */
.cd-rate-row {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    margin-bottom: 2px !important;
}

.cd-rate-usd-row {
    font-size: 11px !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
}

.cd-rate-val {
    font-family: 'Poppins', monospace !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

/* ==========================================================================
   Sub-Card 5: Zero-Overlap Action Suite & Custom Switch
   ========================================================================== */
.cd-actions-cell {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
    width: 100% !important;
}

/* Standalone Custom Toggle Switch */
.cd-toggle-wrap {
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 0 !important;
    position: relative !important;
    cursor: pointer !important;
    user-select: none !important;
}

.cd-toggle-input {
    position: absolute !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    pointer-events: none !important;
}

.cd-toggle-track {
    width: 36px !important;
    height: 20px !important;
    background-color: #cbd5e1 !important;
    border-radius: 20px !important;
    position: relative !important;
    transition: background-color 0.2s ease !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.cd-toggle-track::after {
    content: '' !important;
    position: absolute !important;
    top: 2px !important;
    left: 2px !important;
    width: 16px !important;
    height: 16px !important;
    background-color: #ffffff !important;
    border-radius: 50% !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2) !important;
    transition: transform 0.2s ease !important;
}

.cd-toggle-input:checked + .cd-toggle-track {
    background-color: #009ef7 !important;
}

.cd-toggle-input:checked + .cd-toggle-track::after {
    transform: translateX(16px) !important;
}

.cd-btn-group {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
}

.cd-edit-btn {
    height: 30px !important;
    padding: 0 10px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.cd-edit-btn:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}

.cd-del-btn {
    width: 30px !important;
    height: 30px !important;
    padding: 0 !important;
    font-size: 11.5px !important;
    border-radius: 6px !important;
    background: #fef2f2 !important;
    color: #ef4444 !important;
    border: 1px solid #fecaca !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
}

.cd-del-btn:hover {
    background: #ef4444 !important;
    color: #ffffff !important;
    border-color: #ef4444 !important;
}

/* Sub-Card 6: Bottom Activity Strip */
.cd-topup-card-bottom {
    background: #ffffff !important;
    border-top: 1px solid #f1f5f9 !important;
    padding: 7px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 11px !important;
    color: #64748b !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}

.cd-bottom-meta {
    display: flex !important;
    align-items: center !important;
    gap: 14px !important;
    flex-wrap: wrap !important;
}

.cd-bottom-meta span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* ==========================================================================
   Tier 3 (Alternate): High-Density Table View
   ========================================================================== */
.cd-topup-table-view {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: none;
    width: 100% !important;
    overflow-x: auto !important;
}

.cd-dense-table {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 12px !important;
}

.cd-dense-table th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    padding: 10px 12px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    white-space: nowrap !important;
}

.cd-dense-table td {
    padding: 10px 12px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #1e293b !important;
    vertical-align: middle !important;
}

.cd-dense-table tr:hover td {
    background: #f8fafc !important;
}

/* Empty State Card */
.cd-topup-empty-card {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 40px 20px !important;
    text-align: center !important;
    color: #64748b !important;
    display: none;
}

.cd-topup-empty-icon {
    font-size: 38px !important;
    color: #94a3b8 !important;
    margin-bottom: 10px !important;
}

/* ==========================================================================
   Slide-Over Drawer for Add / Edit
   ========================================================================== */
.cd-drawer-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(15, 23, 42, 0.45) !important;
    backdrop-filter: blur(2px) !important;
    z-index: 9998 !important;
    display: none;
    opacity: 0;
    transition: opacity 0.2s ease !important;
}

.cd-drawer-overlay.active {
    display: block !important;
    opacity: 1 !important;
}

.cd-drawer-panel {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    width: 500px !important;
    max-width: 100% !important;
    height: 100vh !important;
    background: #ffffff !important;
    box-shadow: -4px 0 24px rgba(15, 23, 42, 0.12) !important;
    z-index: 9999 !important;
    display: flex !important;
    flex-direction: column !important;
    transform: translateX(100%) !important;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.cd-drawer-panel.open {
    transform: translateX(0) !important;
}

.cd-drawer-header {
    padding: 16px 20px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #f8fafc !important;
}

.cd-drawer-title-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.cd-drawer-ico {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
}

.cd-drawer-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
}

.cd-drawer-subtitle {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
}

.cd-drawer-close {
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    border: none !important;
    background: #f1f5f9 !important;
    color: #64748b !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cd-drawer-close:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}

.cd-drawer-body {
    flex: 1 1 auto !important;
    overflow-y: auto !important;
    padding: 20px !important;
}

.cd-drawer-footer {
    padding: 14px 20px !important;
    border-top: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
}

/* Real-time Math Preview Box inside Drawer */
.cd-calc-box {
    background: #eff6ff !important;
    border: 1px dashed #bfdbfe !important;
    border-radius: 8px !important;
    padding: 12px 14px !important;
    margin-top: 12px !important;
}

.cd-calc-title {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #1e40af !important;
    text-transform: uppercase !important;
    margin-bottom: 8px !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.cd-calc-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 8px !important;
}

.cd-calc-item {
    font-size: 11.5px !important;
    color: #334155 !important;
}

.cd-calc-val {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

/* Responsive adjustments for 15.6" screens */
@media (max-width: 1400px) {
    .cd-topup-card-grid {
        grid-template-columns: minmax(0, 1.9fr) minmax(0, 1.2fr) minmax(0, 1.3fr) minmax(0, 1.55fr) minmax(0, 1.5fr) !important;
        gap: 6px !important;
        padding: 10px 12px !important;
    }
}

@media (max-width: 1200px) {
    .cd-topup-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .cd-topup-card-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 768px) {
    .cd-topup-kpi-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-topup-card-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-drawer-panel {
        width: 100% !important;
    }
}
</style>

<!-- Main Body in Canonical Post Container -->
<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid cd-topup-wrapper">

        <!-- ==========================================================================
             Tier 0: Executive KPI Summary Cards
             ========================================================================== -->
        <div class="cd-topup-kpi-grid">
            <div class="cd-topup-kpi-card">
                <div class="cd-topup-kpi-icon icon-amber">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="cd-topup-kpi-label">Total Coin Packages</div>
                    <div class="cd-topup-kpi-val">
                        <span id="kpiTotalCnt">{{ $totalPackages ?? count($coinEvents) }}</span>
                        <span class="cd-topup-kpi-badge bg-light-primary text-primary">Catalog</span>
                    </div>
                </div>
            </div>

            <div class="cd-topup-kpi-card">
                <div class="cd-topup-kpi-icon icon-emerald">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="cd-topup-kpi-label">Active / Live Bundles</div>
                    <div class="cd-topup-kpi-val">
                        <span id="kpiActiveCnt">{{ $activePackages ?? 0 }}</span>
                        <span class="cd-topup-kpi-badge bg-light-success text-success">{{ $inactivePackages ?? 0 }} Inactive</span>
                    </div>
                </div>
            </div>

            <div class="cd-topup-kpi-card">
                <div class="cd-topup-kpi-icon icon-blue">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div class="cd-topup-kpi-label">Operational Coin Packs</div>
                    <div class="cd-topup-kpi-val">
                        <span id="kpiOpCnt">{{ $operationalPackages ?? 0 }}</span>
                        <span class="cd-topup-kpi-badge bg-light-warning text-warning">ATS Pipeline</span>
                    </div>
                </div>
            </div>

            <div class="cd-topup-kpi-card">
                <div class="cd-topup-kpi-icon icon-purple">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <div class="cd-topup-kpi-label">Sales & Trading Packs</div>
                    <div class="cd-topup-kpi-val">
                        <span id="kpiSalCnt">{{ $salesPackages ?? 0 }}</span>
                        <span class="cd-topup-kpi-badge bg-light-info text-info">Marketplace</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================================================
             Tier 1: Top Header Toolbar Card (Pure White #ffffff, In-Flow, Non-Fixed)
             ========================================================================== -->
        <div class="cd-topup-header-card">
            <div class="cd-topup-header-left">
                <div class="cd-topup-header-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="cd-topup-header-title">Top-Up Coin Packages</div>
                    <div class="cd-topup-header-sub">Manage global coin bundles, multi-currency rates & discounts.</div>
                </div>
            </div>

            <div class="cd-topup-header-right">
                <!-- Dynamic Search Sub-Card -->
                <div class="cd-topup-search-subcard">
                    <i class="fas fa-search"></i>
                    <input type="text" id="inputTopupSearch" placeholder="Search coins, prices, rates..." autocomplete="off">
                    <button type="button" class="cd-topup-search-clear" id="btnTopupSearchClear" title="Clear search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- View Mode Switcher -->
                <div class="cd-view-toggle-group">
                    <button type="button" class="cd-view-btn active" id="btnViewCards" title="Card Stream View">
                        <i class="fas fa-th-large"></i> Cards
                    </button>
                    <button type="button" class="cd-view-btn" id="btnViewTable" title="Dense Table View">
                        <i class="fas fa-list"></i> Table
                    </button>
                </div>

                <!-- Primary Action Button: Opens Slide-Over Drawer -->
                <button type="button" class="cd-topup-add-btn" id="btnOpenAddDrawer">
                    <i class="fas fa-plus"></i> Add Top-Up
                </button>
            </div>
        </div>

        <!-- ==========================================================================
             Tier 2: Sub-Tabs Filter Card (Single Active Highlight & Instant Filter)
             ========================================================================== -->
        <div class="cd-topup-tabs-card">
            <div class="cd-topup-tabs-group">
                <button type="button" class="cd-topup-tab-btn js-topup-filter active" data-filter="all">
                    <span>All Packages</span>
                    <span class="cd-topup-tab-badge" id="tabCountAll">{{ count($coinEvents) }}</span>
                </button>
                <button type="button" class="cd-topup-tab-btn js-topup-filter" data-filter="active">
                    <span>Active</span>
                    <span class="cd-topup-tab-badge badge-active-cnt" id="tabCountActive">{{ $activePackages ?? 0 }}</span>
                </button>
                <button type="button" class="cd-topup-tab-btn js-topup-filter" data-filter="inactive">
                    <span>Inactive</span>
                    <span class="cd-topup-tab-badge badge-inactive-cnt" id="tabCountInactive">{{ $inactivePackages ?? 0 }}</span>
                </button>
                <button type="button" class="cd-topup-tab-btn js-topup-filter" data-filter="operational">
                    <span>Operational Coins</span>
                    <span class="cd-topup-tab-badge badge-op-cnt" id="tabCountOp">{{ $operationalPackages ?? 0 }}</span>
                </button>
                <button type="button" class="cd-topup-tab-btn js-topup-filter" data-filter="sales">
                    <span>Sales Coins</span>
                    <span class="cd-topup-tab-badge badge-sal-cnt" id="tabCountSal">{{ $salesPackages ?? 0 }}</span>
                </button>
                <button type="button" class="cd-topup-tab-btn js-topup-filter" data-filter="custom">
                    <span>Custom Bundles</span>
                    <span class="cd-topup-tab-badge" id="tabCountCustom">{{ $customPackages ?? 0 }}</span>
                </button>
            </div>
        </div>

        <!-- ==========================================================================
             Tier 3: Primary Card Stream Container
             ========================================================================== -->
        <div class="cd-topup-stream" id="topupCardsStream">
            @forelse ($coinEvents as $item)
                @php
                    $isCustom = ($item->custome_chk == 1 || ($item->coins == 0 && $item->price == 0));
                    $coins = (int) ($item->coins ?? 0);
                    $tierName = 'Custom';
                    if (!$isCustom) {
                        if ($coins < 500) { $tierName = 'Starter'; }
                        elseif ($coins <= 2000) { $tierName = 'Growth'; }
                        elseif ($coins <= 5000) { $tierName = 'Scale'; }
                        else { $tierName = 'Enterprise'; }
                    }
                    $eventType = strtolower($item->event_type ?? 'operational');
                    $status = (int) ($item->status ?? 0);
                    $discount = (float) ($item->discount ?? 0);
                    $price = (float) ($item->price ?? 0);
                    $priceUsd = (float) ($item->price_usd ?? 0);
                    $perCoin = (float) ($item->per_coin_rate ?? 0);
                    $perCoinUsd = (float) ($item->per_coin_rate_usd ?? 0);

                    // Base original price if discount exists
                    $origPriceInr = ($discount > 0 && $discount < 100) ? ($price / (1 - ($discount / 100))) : $price;
                    $origPriceUsd = ($discount > 0 && $discount < 100) ? ($priceUsd / (1 - ($discount / 100))) : $priceUsd;
                @endphp

                <div class="cd-topup-card js-pkg-item"
                     id="card-pkg-{{ $item->id }}"
                     data-id="{{ $item->id }}"
                     data-status="{{ $status == 1 ? 'active' : 'inactive' }}"
                     data-type="{{ $eventType }}"
                     data-custom="{{ $isCustom ? 'custom' : 'standard' }}"
                     data-coins="{{ $coins }}"
                     data-price="{{ $price }}"
                     data-price-usd="{{ $priceUsd }}"
                     data-discount="{{ $discount }}"
                     data-per-coin="{{ $perCoin }}"
                     data-per-coin-usd="{{ $perCoinUsd }}"
                     data-title="{{ $isCustom ? 'Custom Coin Pack' : number_format($coins) . ' Coins' }}"
                     data-search="{{ strtolower(($isCustom ? 'custom pack' : $coins . ' coins') . ' ' . $eventType . ' ' . $tierName . ' inr ' . $price . ' usd ' . $priceUsd . ' top-' . str_pad($item->id, 3, '0', STR_PAD_LEFT)) }}">

                    <!-- 5 Discrete Sub-Cards Grid -->
                    <div class="cd-topup-card-grid">

                        <!-- Sub-Card 1: Package Identity & Type -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">PACKAGE IDENTITY</span>
                                <span class="cd-status-badge {{ $status == 1 ? 'active' : 'inactive' }}" id="statusBadge-{{ $item->id }}">
                                    <i class="fas fa-circle fs-8 me-1"></i>
                                    <span>{{ $status == 1 ? 'Active' : 'Inactive' }}</span>
                                </span>
                            </div>
                            <div class="cd-subcard-id-body">
                                <div class="cd-topup-badge-icon {{ $eventType === 'sales' ? 'sal-icon' : 'op-icon' }}">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="cd-topup-pkg-title" title="{{ $isCustom ? 'Custom Coin Pack' : number_format($coins) . ' Coins Bundle' }}">
                                        {{ $isCustom ? 'Custom Coin Pack' : number_format($coins) . ' Coins Bundle' }}
                                    </div>
                                    <div class="cd-topup-meta-pills">
                                        <span class="cd-pill cd-pill-id">#TOP-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                        <span class="cd-pill {{ $eventType === 'sales' ? 'cd-pill-sal' : 'cd-pill-op' }}">
                                            <i class="fas fa-{{ $eventType === 'sales' ? 'store' : 'bolt' }}"></i>
                                            {{ ucfirst($eventType) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 2: Volume & Tier -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">COIN VOLUME</span>
                                <span class="badge bg-light-primary text-primary fw-bold fs-8">{{ $tierName }}</span>
                            </div>
                            <div>
                                <div class="cd-vol-big">
                                    <i class="fas fa-coins text-warning fs-6"></i>
                                    <span>{{ $isCustom ? 'Custom Entry' : number_format($coins) . ' Coins' }}</span>
                                </div>
                                <div class="cd-vol-sub">
                                    {{ $isCustom ? 'User defines coin amount' : 'Standard catalog bundle' }}
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 3: Pricing (INR & USD) -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">PRICING (INR / USD)</span>
                                @if ($discount > 0)
                                    <span class="cd-discount-tag"><i class="fas fa-tag"></i> {{ $discount }}% OFF</span>
                                @else
                                    <span class="badge bg-light-secondary text-muted fs-8">Standard</span>
                                @endif
                            </div>
                            <div>
                                <div class="cd-price-row">
                                    <span class="cd-price-inr">
                                        ₹{{ number_format($price, 2) }}
                                    </span>
                                    @if ($discount > 0 && $origPriceInr > $price)
                                        <span class="text-muted text-decoration-line-through fs-8">₹{{ number_format($origPriceInr, 2) }}</span>
                                    @endif
                                </div>
                                <div class="cd-price-usd">
                                    ${{ number_format($priceUsd, 2) }} USD
                                    @if ($discount > 0 && $origPriceUsd > $priceUsd)
                                        <span class="text-muted text-decoration-line-through fs-8 ms-1">${{ number_format($origPriceUsd, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 4: Unit Economics -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">UNIT ECONOMICS</span>
                                <span class="badge bg-light-info text-info fs-8">Rate / Coin</span>
                            </div>
                            <div>
                                <div class="cd-rate-row">
                                    <span class="text-muted">INR Rate:</span>
                                    <span class="cd-rate-val">₹{{ $perCoin > 0 ? number_format($perCoin, 4) : '—' }}</span>
                                </div>
                                <div class="cd-rate-usd-row">
                                    <span class="text-muted">USD Rate:</span>
                                    <span class="cd-rate-val">${{ $perCoinUsd > 0 ? number_format($perCoinUsd, 4) : '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 5: Instant Control & Actions (Zero-Overlap Guaranteed) -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">STATUS & ACTIONS</span>
                            </div>
                            <div class="cd-actions-cell">
                                <!-- Standalone Custom Toggle Switch -->
                                <label class="cd-toggle-wrap" title="Toggle Active / Inactive Status">
                                    <input type="checkbox"
                                           class="cd-toggle-input js-toggle-status"
                                           data-id="{{ $item->id }}"
                                           {{ $status == 1 ? 'checked' : '' }}>
                                    <span class="cd-toggle-track"></span>
                                </label>

                                <div class="cd-btn-group">
                                    <!-- Edit Button (Opens Drawer) -->
                                    <button type="button" class="cd-edit-btn js-btn-edit" data-id="{{ $item->id }}" title="Edit Package">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <!-- Delete / Deactivate Button -->
                                    <button type="button" class="cd-del-btn js-btn-delete" data-id="{{ $item->id }}" title="Delete / Deactivate Package">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Sub-Card 6: Bottom Activity Strip -->
                    <div class="cd-topup-card-bottom">
                        <div class="cd-bottom-meta">
                            <span><i class="far fa-calendar-alt text-muted"></i> Created: {{ $item->created_at ? date('d M Y, h:i A', strtotime($item->created_at)) : '—' }}</span>
                            <span><i class="far fa-clock text-muted"></i> Updated: {{ $item->updated_at ? date('d M Y, h:i A', strtotime($item->updated_at)) : '—' }}</span>
                            <span><i class="fas fa-cube text-muted"></i> Type: {{ $isCustom ? 'Custom Configurable' : 'Fixed Tier Bundle' }}</span>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark fw-bold px-2 py-1">Scope: {{ ucfirst($eventType) }} Coin Pipeline</span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="cd-topup-empty-card" style="display: block;">
                    <div class="cd-topup-empty-icon"><i class="fas fa-coins"></i></div>
                    <h5>No Top-Up Coin Packages Found</h5>
                    <p class="fs-7 text-muted">Get started by clicking "+ Add Top-Up Package" above to create your first package.</p>
                </div>
            @endforelse
        </div>

        <!-- Empty State for Live Search Filters -->
        <div class="cd-topup-empty-card" id="topupEmptyFilterState">
            <div class="cd-topup-empty-icon"><i class="fas fa-search"></i></div>
            <h5 class="fw-bold">No Matching Coin Packages</h5>
            <p class="fs-7 text-muted mb-3">No packages match your search filter criteria. Try clearing search or selecting another tab.</p>
            <button type="button" class="btn btn-sm btn-light-primary" id="btnResetFilters">
                <i class="fas fa-redo me-1"></i> Reset Filters
            </button>
        </div>

        <!-- ==========================================================================
             Tier 3 (Alternate): High-Density Table View
             ========================================================================== -->
        <div class="cd-topup-table-view" id="topupTableView">
            <table class="cd-dense-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#ID</th>
                        <th>Package & Type</th>
                        <th>Coins</th>
                        <th>INR Price</th>
                        <th>USD Price</th>
                        <th>Discount</th>
                        <th>Per Coin (INR)</th>
                        <th>Per Coin (USD)</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coinEvents as $item)
                        @php
                            $isCustom = ($item->custome_chk == 1 || ($item->coins == 0 && $item->price == 0));
                            $eventType = strtolower($item->event_type ?? 'operational');
                            $status = (int) ($item->status ?? 0);
                        @endphp
                        <tr class="js-pkg-table-row js-pkg-item"
                            id="row-pkg-{{ $item->id }}"
                            data-id="{{ $item->id }}"
                            data-status="{{ $status == 1 ? 'active' : 'inactive' }}"
                            data-type="{{ $eventType }}"
                            data-custom="{{ $isCustom ? 'custom' : 'standard' }}"
                            data-search="{{ strtolower(($isCustom ? 'custom pack' : $item->coins . ' coins') . ' ' . $eventType . ' inr ' . $item->price . ' usd ' . $item->price_usd) }}">
                            <td><span class="badge bg-light text-muted font-monospace">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cd-topup-badge-icon {{ $eventType === 'sales' ? 'sal-icon' : 'op-icon' }}" style="width: 28px; height: 28px; font-size: 13px;">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $isCustom ? 'Custom Coin Pack' : number_format($item->coins) . ' Coins' }}</div>
                                        <span class="badge {{ $eventType === 'sales' ? 'bg-light-info text-info' : 'bg-light-warning text-warning' }} fs-8 py-0">
                                            {{ ucfirst($eventType) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $isCustom ? 'Custom' : number_format($item->coins) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">₹{{ number_format((float)$item->price, 2) }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-muted">${{ number_format((float)$item->price_usd, 2) }}</span>
                            </td>
                            <td>
                                @if ((float)$item->discount > 0)
                                    <span class="badge bg-light-danger text-danger fw-bold">{{ (float)$item->discount }}% OFF</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-monospace text-dark">₹{{ (float)$item->per_coin_rate > 0 ? number_format((float)$item->per_coin_rate, 4) : '—' }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted">${{ (float)$item->per_coin_rate_usd > 0 ? number_format((float)$item->per_coin_rate_usd, 4) : '—' }}</span>
                            </td>
                            <td>
                                <label class="cd-toggle-wrap">
                                    <input type="checkbox"
                                           class="cd-toggle-input js-toggle-status"
                                           data-id="{{ $item->id }}"
                                           {{ $status == 1 ? 'checked' : '' }}>
                                    <span class="cd-toggle-track"></span>
                                </label>
                            </td>
                            <td style="text-align: right;">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary js-btn-edit" data-id="{{ $item->id }}" title="Edit">
                                        <i class="fas fa-edit fs-7"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger js-btn-delete" data-id="{{ $item->id }}" title="Delete">
                                        <i class="fas fa-trash-alt fs-7"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ==========================================================================
     Slide-Over Drawer for Add / Edit Top-Up Package
     ========================================================================== -->
<div class="cd-drawer-overlay" id="drawerOverlay"></div>

<div class="cd-drawer-panel" id="drawerTopupPackage">
    <div class="cd-drawer-header">
        <div class="cd-drawer-title-wrap">
            <div class="cd-drawer-ico">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="cd-drawer-title" id="drawerTitle">Create Top-Up Package</div>
                <div class="cd-drawer-subtitle">Configure bundle coins, pricing, and live unit economics.</div>
            </div>
        </div>
        <button type="button" class="cd-drawer-close" id="btnCloseDrawer" title="Close drawer">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form id="formTopupPackage" class="d-flex flex-column flex-grow-1" style="min-height: 0;">
        @csrf
        <input type="hidden" name="token_id" id="inp_token_id" value="">

        <div class="cd-drawer-body">

            <!-- Event Scope & Package Type -->
            <div class="row g-3 mb-4">
                <div class="col-md-7">
                    <label class="form-label fw-bold text-dark fs-7 required">Coin Package Scope</label>
                    <select class="form-select form-select-solid" name="event_type" id="inp_event_type" required>
                        <option value="operational">Operational Coins (ATS Pipeline)</option>
                        <option value="sales">Sales Coins (Marketplace Trading)</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-dark fs-7">Package Type</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="custome_chk" id="inp_custome_chk" value="1">
                        <label class="form-check-label fs-7 fw-semibold" for="inp_custome_chk">
                            Custom Pack
                        </label>
                    </div>
                </div>
            </div>

            <!-- Total Coins -->
            <div class="mb-4">
                <label class="form-label fw-bold text-dark fs-7 required">Total Coins</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-coins text-warning"></i></span>
                    <input type="number" class="form-control form-control-solid" name="total_coins" id="inp_total_coins" placeholder="e.g. 1000" min="0" step="1" required>
                </div>
                <div class="form-text text-muted fs-8">Total coin balance credited to the tenant upon purchase.</div>
            </div>

            <!-- Discount Percentage -->
            <div class="mb-4">
                <label class="form-label fw-bold text-dark fs-7">Discount Percentage (%)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-percent text-danger"></i></span>
                    <input type="number" class="form-control form-control-solid" name="discount" id="inp_discount" placeholder="0" min="0" max="100" step="0.01">
                </div>
                <div class="form-text text-muted fs-8">Special discount deducted from the base catalog price.</div>
            </div>

            <!-- Base Pricing Fields (INR & USD) -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark fs-7 required">Base Price (INR ₹)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">₹</span>
                        <input type="number" class="form-control form-control-solid" name="price" id="inp_price" placeholder="0.00" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark fs-7 required">Final Total (INR ₹)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">₹</span>
                        <input type="number" class="form-control form-control-solid bg-light text-dark fw-bold" name="total_price" id="inp_total_price" placeholder="0.00" readonly>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark fs-7">Base Price (USD $)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">$</span>
                        <input type="number" class="form-control form-control-solid" name="price_usd" id="inp_price_usd" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark fs-7">Final Total (USD $)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">$</span>
                        <input type="number" class="form-control form-control-solid bg-light text-dark fw-bold" name="total_price_usd" id="inp_total_price_usd" placeholder="0.00" readonly>
                    </div>
                </div>
            </div>

            <!-- Status Switch -->
            <div class="mb-4 d-flex align-items-center justify-content-between p-3 bg-light rounded">
                <div>
                    <div class="fw-bold text-dark fs-7">Active Status</div>
                    <div class="text-muted fs-8">Make this package available in client top-up drawers.</div>
                </div>
                <label class="cd-toggle-wrap">
                    <input type="checkbox" class="cd-toggle-input" name="status" id="inp_status" value="1" checked>
                    <span class="cd-toggle-track"></span>
                </label>
            </div>

            <!-- Hidden Fields for Rates -->
            <input type="hidden" name="per_coin_rate" id="inp_per_coin_rate" value="">
            <input type="hidden" name="per_coin_rate_usd" id="inp_per_coin_rate_usd" value="">

            <!-- Live Calculation Preview Card -->
            <div class="cd-calc-box">
                <div class="cd-calc-title">
                    <i class="fas fa-calculator"></i> Real-Time Pricing & Rate Preview
                </div>
                <div class="cd-calc-grid">
                    <div class="cd-calc-item">
                        <div class="text-muted fs-8">Final INR Price</div>
                        <div class="cd-calc-val" id="prevFinalInr">₹0.00</div>
                    </div>
                    <div class="cd-calc-item">
                        <div class="text-muted fs-8">Final USD Price</div>
                        <div class="cd-calc-val" id="prevFinalUsd">$0.00</div>
                    </div>
                    <div class="cd-calc-item">
                        <div class="text-muted fs-8">INR Rate / Coin</div>
                        <div class="cd-calc-val" id="prevRateInr">₹0.0000</div>
                    </div>
                    <div class="cd-calc-item">
                        <div class="text-muted fs-8">USD Rate / Coin</div>
                        <div class="cd-calc-val" id="prevRateUsd">$0.0000</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="cd-drawer-footer">
            <button type="button" class="btn btn-sm btn-light" id="btnCancelDrawer">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary" id="btnSavePackage">
                <i class="fas fa-save me-1"></i> Save Package
            </button>
        </div>
    </form>
</div>

@endsection

@push('footer-script')
<script>
$(document).ready(function() {

    // CSRF Token setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var currentFilter = 'all';
    var currentSearch = '';
    var searchTimer = null;

    // ==========================================================================
    // 1. Live Debounced Search Filter (Client-First Execution in User RAM)
    // ==========================================================================
    $('#inputTopupSearch').on('keyup', function() {
        clearTimeout(searchTimer);
        var val = $(this).val().trim().toLowerCase();
        currentSearch = val;

        if (val.length > 0) {
            $('#btnTopupSearchClear').show();
        } else {
            $('#btnTopupSearchClear').hide();
        }

        searchTimer = setTimeout(function() {
            applyFilters();
        }, 300);
    });

    $('#btnTopupSearchClear').on('click', function() {
        $('#inputTopupSearch').val('');
        currentSearch = '';
        $(this).hide();
        applyFilters();
    });

    // ==========================================================================
    // 2. Sub-Tabs Filter Switching (Single Active Primary Highlight)
    // ==========================================================================
    $('.js-topup-filter').on('click', function() {
        $('.js-topup-filter').removeClass('active');
        $(this).addClass('active');

        currentFilter = $(this).data('filter') || 'all';
        applyFilters();
    });

    $('#btnResetFilters').on('click', function() {
        $('#inputTopupSearch').val('');
        currentSearch = '';
        $('#btnTopupSearchClear').hide();
        currentFilter = 'all';
        $('.js-topup-filter').removeClass('active');
        $('.js-topup-filter[data-filter="all"]').addClass('active');
        applyFilters();
    });

    function applyFilters() {
        var visibleCount = 0;

        $('.js-pkg-item').each(function() {
            var $item = $(this);
            var status = $item.data('status');
            var type = $item.data('type');
            var custom = $item.data('custom');
            var searchData = $item.data('search') || '';

            var matchesFilter = true;
            if (currentFilter === 'active') {
                matchesFilter = (status === 'active');
            } else if (currentFilter === 'inactive') {
                matchesFilter = (status === 'inactive');
            } else if (currentFilter === 'operational') {
                matchesFilter = (type === 'operational');
            } else if (currentFilter === 'sales') {
                matchesFilter = (type === 'sales');
            } else if (currentFilter === 'custom') {
                matchesFilter = (custom === 'custom');
            }

            var matchesSearch = true;
            if (currentSearch.length > 0) {
                matchesSearch = (searchData.indexOf(currentSearch) !== -1);
            }

            if (matchesFilter && matchesSearch) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });

        if (visibleCount === 0) {
            $('#topupEmptyFilterState').show();
        } else {
            $('#topupEmptyFilterState').hide();
        }
    }

    // ==========================================================================
    // 3. View Mode Toggle (Card Stream vs High-Density Table)
    // ==========================================================================
    $('#btnViewCards').on('click', function() {
        $('.cd-view-btn').removeClass('active');
        $(this).addClass('active');
        $('#topupCardsStream').show();
        $('#topupTableView').hide();
    });

    $('#btnViewTable').on('click', function() {
        $('.cd-view-btn').removeClass('active');
        $(this).addClass('active');
        $('#topupCardsStream').hide();
        $('#topupTableView').show();
    });

    // ==========================================================================
    // 4. Instant Status Toggle Switch (Active / Inactive)
    // ==========================================================================
    $(document).on('change', '.js-toggle-status', function() {
        var $switch = $(this);
        var pkgId = $switch.data('id');
        var isChecked = $switch.is(':checked');

        $.ajax({
            url: "{{ route('superadmin.company.toggleTopUpCoinPackageStatus') }}",
            type: "POST",
            data: { id: pkgId },
            success: function(response) {
                if (response.status === 'success') {
                    var newStatus = response.new_status;
                    var statusStr = (newStatus === 1 ? 'active' : 'inactive');

                    // Synchronize both Stream Card and Table Row
                    var $card = $('#card-pkg-' + pkgId);
                    var $row = $('#row-pkg-' + pkgId);

                    $card.attr('data-status', statusStr).data('status', statusStr);
                    $row.attr('data-status', statusStr).data('status', statusStr);

                    var $badge = $('#statusBadge-' + pkgId);
                    if (newStatus === 1) {
                        $badge.removeClass('inactive').addClass('active').html('<i class="fas fa-circle fs-8 me-1"></i><span>Active</span>');
                    } else {
                        $badge.removeClass('active').addClass('inactive').html('<i class="fas fa-circle fs-8 me-1"></i><span>Inactive</span>');
                    }

                    // Keep checkboxes in sync
                    $('input[type="checkbox"][data-id="' + pkgId + '"]').prop('checked', (newStatus === 1));

                    // Update Top KPI counters
                    recalculateKpis();

                    // Modern toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                } else {
                    $switch.prop('checked', !isChecked);
                    Swal.fire({
                        text: response.message || "Failed to update package status.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            },
            error: function() {
                $switch.prop('checked', !isChecked);
                Swal.fire({
                    text: "Network error occurred while updating status.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            }
        });
    });

    function recalculateKpis() {
        var total = 0, active = 0, inactive = 0, op = 0, sal = 0, custom = 0;

        $('#topupCardsStream .cd-topup-card').each(function() {
            total++;
            var st = $(this).data('status');
            var tp = $(this).data('type');
            var cs = $(this).data('custom');

            if (st === 'active') active++; else inactive++;
            if (tp === 'operational') op++; else if (tp === 'sales') sal++;
            if (cs === 'custom') custom++;
        });

        $('#kpiTotalCnt').text(total);
        $('#kpiActiveCnt').text(active);
        $('#kpiOpCnt').text(op);
        $('#kpiSalCnt').text(sal);

        $('#tabCountAll').text(total);
        $('#tabCountActive').text(active);
        $('#tabCountInactive').text(inactive);
        $('#tabCountOp').text(op);
        $('#tabCountSal').text(sal);
        $('#tabCountCustom').text(custom);
    }

    // ==========================================================================
    // 5. Drawer Controls (Add & Edit)
    // ==========================================================================
    function openDrawer() {
        $('#drawerOverlay').addClass('active');
        $('#drawerTopupPackage').addClass('open');
        $('body').css('overflow', 'hidden');
    }

    function closeDrawer() {
        $('#drawerTopupPackage').removeClass('open');
        $('#drawerOverlay').removeClass('active');
        $('body').css('overflow', '');
        $('#formTopupPackage')[0].reset();
        $('#inp_token_id').val('');
        $('#inp_status').prop('checked', true);
        $('#inp_custome_chk').prop('checked', false);
        calcPricing();
    }

    $('#btnOpenAddDrawer').on('click', function() {
        $('#drawerTitle').text('Create Top-Up Package');
        $('#formTopupPackage')[0].reset();
        $('#inp_token_id').val('');
        $('#inp_status').prop('checked', true);
        $('#inp_custome_chk').prop('checked', false);
        calcPricing();
        openDrawer();
    });

    $('#btnCloseDrawer, #btnCancelDrawer, #drawerOverlay').on('click', function() {
        closeDrawer();
    });

    // Populate and open drawer for Edit
    $(document).on('click', '.js-btn-edit', function() {
        var pkgId = $(this).data('id');
        var $card = $('#card-pkg-' + pkgId);

        if (!$card.length) return;

        $('#drawerTitle').text('Edit Top-Up Package #TOP-' + ('000' + pkgId).slice(-3));
        $('#inp_token_id').val(pkgId);

        var coins = $card.data('coins') || 0;
        var discount = $card.data('discount') || 0;
        var finalPrice = parseFloat($card.data('price')) || 0;
        var finalPriceUsd = parseFloat($card.data('price-usd')) || 0;
        var status = $card.data('status');
        var type = $card.data('type') || 'operational';
        var isCustom = ($card.data('custom') === 'custom');

        // Calculate original base price before discount
        var basePrice = discount > 0 ? (finalPrice / (1 - discount / 100)) : finalPrice;
        var basePriceUsd = discount > 0 ? (finalPriceUsd / (1 - discount / 100)) : finalPriceUsd;

        $('#inp_total_coins').val(coins);
        $('#inp_discount').val(discount);
        $('#inp_price').val(basePrice.toFixed(2));
        $('#inp_price_usd').val(basePriceUsd.toFixed(2));
        $('#inp_total_price').val(finalPrice.toFixed(2));
        $('#inp_total_price_usd').val(finalPriceUsd.toFixed(2));

        $('#inp_event_type').val(type);
        $('#inp_custome_chk').prop('checked', isCustom);
        $('#inp_status').prop('checked', (status === 'active'));

        calcPricing();
        openDrawer();
    });

    // ==========================================================================
    // 6. Real-Time Math Calculator (Interactive Pricing & Rate Calculation)
    // ==========================================================================
    $('#inp_price, #inp_price_usd, #inp_discount, #inp_total_coins').on('input keyup change', function() {
        calcPricing();
    });

    function calcPricing() {
        var coins = parseFloat($('#inp_total_coins').val()) || 0;
        var basePrice = parseFloat($('#inp_price').val()) || 0;
        var basePriceUsd = parseFloat($('#inp_price_usd').val()) || 0;
        var discount = parseFloat($('#inp_discount').val()) || 0;

        if (discount < 0) discount = 0;
        if (discount > 100) discount = 100;

        var finalPrice = basePrice;
        var finalPriceUsd = basePriceUsd;

        if (discount > 0) {
            finalPrice = basePrice - (basePrice * (discount / 100));
            finalPriceUsd = basePriceUsd - (basePriceUsd * (discount / 100));
        }

        $('#inp_total_price').val(finalPrice.toFixed(2));
        $('#inp_total_price_usd').val(finalPriceUsd.toFixed(2));

        var perCoinInr = (coins > 0) ? (finalPrice / coins) : 0;
        var perCoinUsd = (coins > 0) ? (finalPriceUsd / coins) : 0;

        $('#inp_per_coin_rate').val(perCoinInr > 0 ? perCoinInr.toFixed(5) : '');
        $('#inp_per_coin_rate_usd').val(perCoinUsd > 0 ? perCoinUsd.toFixed(5) : '');

        // Update visual preview box
        $('#prevFinalInr').text('₹' + finalPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#prevFinalUsd').text('$' + finalPriceUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#prevRateInr').text('₹' + perCoinInr.toFixed(4));
        $('#prevRateUsd').text('$' + perCoinUsd.toFixed(4));
    }

    // ==========================================================================
    // 7. AJAX Form Submission with SweetAlert2
    // ==========================================================================
    $('#formTopupPackage').on('submit', function(e) {
        e.preventDefault();

        var $btn = $('#btnSavePackage');
        var origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('superadmin.company.topUpCoinPackageStore') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $btn.prop('disabled', false).html(origText);

                if (response.status === 'success') {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(function() {
                        closeDrawer();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        text: response.message || "Failed to save package.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origText);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    Swal.fire({
                        html: '<p class="fw-bold">Please check validation errors:</p><p class="text-danger text-start">' + errors + '</p>',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                } else {
                    Swal.fire({
                        text: "An error occurred while saving the package. Please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            }
        });
    });

    // ==========================================================================
    // 8. Delete / Deactivate Action Handler
    // ==========================================================================
    $(document).on('click', '.js-btn-delete', function() {
        var pkgId = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This package will be removed or deactivated from active purchasing.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, delete/deactivate",
            cancelButtonText: "Cancel",
            customClass: {
                confirmButton: "btn btn-danger me-2",
                cancelButton: "btn btn-light"
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('superadmin.company.destroyTopUpCoinPackage') }}",
                    type: "POST",
                    data: { id: pkgId },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: { confirmButton: "btn btn-primary" }
                            });

                            if (response.deleted) {
                                $('#card-pkg-' + pkgId).fadeOut(300, function() { $(this).remove(); recalculateKpis(); });
                                $('#row-pkg-' + pkgId).fadeOut(300, function() { $(this).remove(); });
                            } else if (response.deactivated) {
                                // Marked inactive
                                $('input[type="checkbox"][data-id="' + pkgId + '"]').prop('checked', false);
                                $('#card-pkg-' + pkgId).attr('data-status', 'inactive').data('status', 'inactive');
                                $('#row-pkg-' + pkgId).attr('data-status', 'inactive').data('status', 'inactive');
                                $('#statusBadge-' + pkgId).removeClass('active').addClass('inactive').html('<i class="fas fa-circle fs-8 me-1"></i><span>Inactive</span>');
                                recalculateKpis();
                            }
                        } else {
                            Swal.fire({
                                text: response.message || "Unable to delete package.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            text: "Network error occurred while deleting package.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            }
        });
    });

});
</script>
@endpush
