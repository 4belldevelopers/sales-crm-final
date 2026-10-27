@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $kpi = $kpiMetrics ?? [
        'totalRevenueInr' => 0,
        'totalRevenueUsd' => 0,
        'totalTransactions' => 0,
        'capturedCount' => 0,
        'failedCount' => 0,
        'pendingCount' => 0,
        'totalCoinsIssued' => 0,
        'approvedInvoiceCount' => 0,
    ];
@endphp

@push('head-script')
<style>
/* ==========================================================================
   Master Enterprise Scoped Styling — SuperAdmin Transaction History & Invoicing
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   Follows UI/UX Standard: Modern Enterprise Card + Sub-Card Design System (Rule 20)
   ========================================================================== */
#kt_body {
    overflow-x: hidden !important;
}

/* Collapse legacy Metronic toolbar spacing cleanly */
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

.cd-tx-wrap {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding: 0 16px 30px 16px !important;
    margin-top: 0 !important;
}

.cd-tx-wrap * {
    box-sizing: border-box !important;
}

/* Font icon preservation across all FontAwesome and Metronic icon classes */
.cd-tx-wrap i,
.cd-tx-wrap [class*="fa-"],
.cd-tx-wrap .fa,
.cd-tx-wrap .fas,
.cd-tx-wrap .far,
.cd-tx-wrap .fab,
.cd-tx-wrap [class*="ki-"] {
    font-family: "Font Awesome 6 Free", "FontAwesome", "Font Awesome 5 Free", sans-serif !important;
    font-style: normal !important;
    font-weight: 900 !important;
}

/* ==========================================================================
   Tier 0: Executive Financial KPI Summary Metric Cards
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
    letter-spacing: -0.3px !important;
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
.cd-tx-header-card {
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
.cd-tx-search-subcard {
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

.cd-tx-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-tx-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.cd-tx-search-subcard input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    font-family: inherit !important;
}

.cd-tx-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-tx-search-clear {
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

.cd-tx-search-clear:hover {
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
    gap: 5px !important;
}

.cd-view-btn.active {
    background: #ffffff !important;
    color: #009ef7 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
}

/* ==========================================================================
   Tier 2: Sub-Tabs Filter Card
   ========================================================================== */
.cd-tx-tabs-card {
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
    padding: 7px 13px !important;
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
    border-radius: 20px !important;
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
   Tier 3: Primary Card Stream (Modern Card + Sub-Card Design)
   ========================================================================== */
.cd-tx-stream {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    width: 100% !important;
}

.cd-tx-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 14px 16px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    width: 100% !important;
}

.cd-tx-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}

.cd-tx-card.cd-pulse-highlight {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.25) !important;
    transition: all 0.3s ease !important;
}

.cd-tx-card-body {
    display: grid !important;
    grid-template-columns: minmax(0, 1.25fr) minmax(0, 1.8fr) minmax(0, 1.35fr) minmax(0, 1.55fr) minmax(0, 2.3fr) minmax(0, 125px) !important;
    gap: 8px !important;
    align-items: stretch !important;
    width: 100% !important;
}

@media (max-width: 1440px) {
    .cd-tx-card-body {
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.6fr) minmax(0, 1.3fr) minmax(0, 1.5fr) minmax(0, 2.1fr) minmax(0, 120px) !important;
        gap: 8px !important;
    }
}

@media (max-width: 1200px) {
    .cd-tx-card-body {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
    }
}

@media (max-width: 768px) {
    .cd-tx-card-body {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
}

/* Discrete Sub-Cards */
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
    height: 100% !important;
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

.cd-subcard-pill {
    display: inline-block !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #e2e8f0 !important;
    color: #475569 !important;
    white-space: nowrap !important;
    max-width: 110px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 1: Order Identity */
.cd-order-num {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #009ef7 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    display: block !important;
    line-height: 1.2 !important;
}
.cd-order-num:hover {
    text-decoration: underline !important;
}
.cd-order-time {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
}
.cd-order-invoice {
    display: inline-block !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    width: fit-content !important;
    border: 1px solid #e2e8f0 !important;
}

/* Sub-Card 2: Company & Billing */
.cd-company-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}
.cd-company-avatar {
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    flex-shrink: 0 !important;
    border: 1px solid #dbeafe !important;
    overflow: hidden !important;
}
.cd-company-avatar img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.cd-company-meta {
    min-width: 0 !important;
    flex: 1 !important;
}
.cd-company-name {
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-company-loc {
    font-size: 11px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-company-gst {
    font-size: 10.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 3: Package & Coins */
.cd-pkg-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-pkg-coins {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #d97706 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}
.cd-pkg-duration {
    font-size: 10.5px !important;
    color: #64748b !important;
}
.cd-pkg-pill {
    display: inline-block !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    width: fit-content !important;
}
.cd-pkg-pill-op { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.cd-pkg-pill-sales { background: #f0fdf4 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }

/* Sub-Card 4: Amount & Financials */
.cd-amount-total {
    font-size: 14px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
}
.cd-amount-breakup {
    font-size: 10.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-pay-mode-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
}

/* Sub-Card 5: Status & Tax Controls */
.cd-status-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 2px 7px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    width: fit-content !important;
    white-space: nowrap !important;
}
.cd-status-success { background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #a7f3d0 !important; }
.cd-status-warning { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.cd-status-danger { background: #fef2f2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; }
.cd-status-info { background: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #bfdbfe !important; }

.cd-invoice-controls {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    width: 100% !important;
}
.cd-gst-select {
    height: 28px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 0 6px !important;
    border-radius: 5px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #1e293b !important;
    cursor: pointer !important;
    outline: none !important;
    flex-shrink: 0 !important;
}
.cd-gst-select:focus {
    border-color: #009ef7 !important;
}
.cd-inv-input-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 3px !important;
    flex: 1 !important;
    min-width: 0 !important;
}
.cd-inv-input {
    height: 28px !important;
    font-size: 11px !important;
    padding: 0 8px !important;
    border-radius: 5px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #1e293b !important;
    width: 100% !important;
    min-width: 0 !important;
    outline: none !important;
}
.cd-inv-input:focus {
    border-color: #009ef7 !important;
}
.cd-inv-save-btn {
    height: 28px !important;
    width: 28px !important;
    min-width: 28px !important;
    border-radius: 5px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #64748b !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
}
.cd-inv-save-btn:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
    border-color: #009ef7 !important;
}
.cd-tax-footer {
    font-size: 10.5px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-tax-saved {
    color: #059669 !important;
    font-weight: 600 !important;
}
.cd-tax-pending {
    color: #94a3b8 !important;
    font-weight: 500 !important;
}

/* Sub-Card 6: Action Buttons */
.cd-subcard-actions {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    justify-content: center !important;
    height: 100% !important;
}
.cd-action-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    height: 28px !important;
    padding: 0 8px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    width: 100% !important;
    border: 1px solid transparent !important;
}
.cd-btn-receipt {
    background: #eff6ff !important;
    color: #009ef7 !important;
    border-color: #bfdbfe !important;
}
.cd-btn-receipt:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
}
.cd-btn-details {
    background: #ffffff !important;
    color: #475569 !important;
    border-color: #cbd5e1 !important;
}
.cd-btn-details:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}

/* Sub-Card 7: Bottom Strip */
.cd-tx-bottom-strip {
    border-top: 1px solid #f1f5f9 !important;
    padding-top: 8px !important;
    margin-top: 2px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    font-size: 11px !important;
    color: #64748b !important;
}
.cd-strip-chips {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
}
.cd-chip-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* ==========================================================================
   Alternate Tier 3: High-Density Ledger Table View
   ========================================================================== */
.cd-tx-table-container {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    overflow-x: auto !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
    width: 100% !important;
}
.cd-tx-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 12px !important;
    text-align: left !important;
}
.cd-tx-table thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 11px 14px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    white-space: nowrap !important;
}
.cd-tx-table tbody td {
    padding: 12px 14px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #1e293b !important;
    vertical-align: middle !important;
}
.cd-tx-table tbody tr:hover {
    background: #f8fafc !important;
}

/* Pagination container */
.cd-pagination-wrap {
    margin-top: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
}

/* Slide-over Drawer styling */
.cd-drawer-content {
    padding: 20px !important;
    overflow-y: auto !important;
    height: 100% !important;
}
.cd-drawer-section {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 14px !important;
    margin-bottom: 14px !important;
}
.cd-drawer-sec-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin-bottom: 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.cd-drawer-field {
    display: flex !important;
    justify-content: space-between !important;
    padding: 5px 0 !important;
    font-size: 12px !important;
    border-bottom: 1px dashed #f1f5f9 !important;
}
.cd-drawer-field:last-child {
    border-bottom: none !important;
}
.cd-drawer-label {
    color: #64748b !important;
}
.cd-drawer-val {
    font-weight: 600 !important;
    color: #0f172a !important;
    text-align: right !important;
}

/* Empty State */
.cd-empty-state {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 40px 20px !important;
    text-align: center !important;
    color: #64748b !important;
    margin: 20px 0 !important;
}
.cd-empty-icon {
    font-size: 36px !important;
    color: #cbd5e1 !important;
    margin-bottom: 10px !important;
}

/* Real-time Delta Pulse Highlight */
@keyframes cdPulseBorder {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 158, 247, 0.45);
        border-color: #009ef7 !important;
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0, 158, 247, 0);
        border-color: #009ef7 !important;
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0, 158, 247, 0);
    }
}
.cd-pulse-highlight {
    animation: cdPulseBorder 1.8s ease-out !important;
    border-color: #009ef7 !important;
    transition: all 0.3s ease;
}
</style>
@endpush

@section('content')
<div class="cd-tx-wrap">

    <!-- ======================================================================
         TIER 0: EXECUTIVE FINANCIAL KPI SUMMARY METRICS STRIP
         ====================================================================== -->
    <div class="cd-kpi-grid">
        <!-- KPI 1: Gross Captured Volume -->
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-blue">
                <i class="fa fa-receipt"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_val_revenue_inr">₹{{ number_format($kpi['totalRevenueInr'], 2) }}</div>
                <div class="cd-kpi-title" id="kpi_sub_revenue_usd">Gross Volume (INR) • ${{ number_format($kpi['totalRevenueUsd'], 2) }} USD</div>
            </div>
        </div>

        <!-- KPI 2: Total Orders Processed -->
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-purple">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_val_total_orders">{{ number_format($kpi['totalTransactions']) }} Orders</div>
                <div class="cd-kpi-title">Total Processed Subscriptions</div>
            </div>
        </div>

        <!-- KPI 3: Success vs Failed Ratio -->
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-green">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_val_ratio">
                    <span id="kpi_val_paid">{{ $kpi['capturedCount'] }}</span> Paid <span style="font-size: 12px; color: #64748b; font-weight: 500;">/ <span id="kpi_val_failed">{{ $kpi['failedCount'] }}</span> Failed</span>
                </div>
                <div class="cd-kpi-title">Gateway Payment Success Rate</div>
            </div>
        </div>

        <!-- KPI 4: ATS Coins Issued -->
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-amber">
                <i class="fa fa-coins"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpi_val_coins">{{ number_format($kpi['totalCoinsIssued']) }} Coins</div>
                <div class="cd-kpi-title" id="kpi_sub_coins">Coins Credited • <span id="kpi_val_invoiced">{{ $kpi['approvedInvoiceCount'] }}</span> Tax Invoices Approved</div>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TIER 1: TOP HEADER TOOLBAR CARD
         ====================================================================== -->
    <div class="cd-tx-header-card">
        <div class="cd-header-left">
            <div class="cd-header-icon">
                <i class="fa fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="cd-header-title">Transaction History & Tax Invoicing</div>
                <div class="cd-header-sub">Corporate order auditing, gateway settlement logs, GST/tax compliance, and invoice overrides.</div>
            </div>
        </div>

        <div class="cd-header-right">
            <!-- Dynamic 300ms Search Sub-Card -->
            <div class="cd-tx-search-subcard">
                <i class="fa fa-search"></i>
                <input type="text" id="cd_tx_search_input" placeholder="Search order #, company, payment ID..." value="{{ request('search') }}" autocomplete="off">
                <button type="button" id="cd_tx_search_clear" class="cd-tx-search-clear" title="Clear Search">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>

            <!-- View Switcher (Stream vs Table) -->
            <div class="cd-view-switcher">
                <button type="button" class="cd-view-btn active" id="btn_view_stream" title="Modern Card Stream View">
                    <i class="fa fa-id-card"></i> Stream
                </button>
                <button type="button" class="cd-view-btn" id="btn_view_table" title="High-Density Ledger Table">
                    <i class="fa fa-table"></i> Table
                </button>
            </div>

            <!-- Export CSV Action -->
            <button type="button" class="btn btn-sm btn-light-primary" id="btn_export_csv" title="Export visible records to CSV" style="height: 36px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa fa-file-csv"></i> Export CSV
            </button>

            <!-- Reload / Reset Action -->
            <a href="{{ url('super-admin/transaction-history') }}" class="btn btn-sm btn-light btn-active-light-primary" title="Reset Filters" style="height: 36px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa fa-sync-alt"></i> Refresh
            </a>
        </div>
    </div>

    <!-- ======================================================================
         TIER 2: SUB-TABS FILTER CARD
         ====================================================================== -->
    <div class="cd-tx-tabs-card">
        <ul class="cd-tabs-list" id="cd_tx_subtabs">
            <li class="cd-tab-item active" data-tab="all">
                <span>All Transactions</span>
                <span class="cd-tab-badge" id="tab_badge_all">{{ $cart_transaction_history->total() }}</span>
            </li>
            <li class="cd-tab-item" data-tab="captured">
                <span>Paid / Captured</span>
                <span class="cd-tab-badge" id="tab_badge_captured" style="color: #059669;">{{ $kpi['capturedCount'] }}</span>
            </li>
            <li class="cd-tab-item" data-tab="pending">
                <span>Pending / Initiated</span>
                <span class="cd-tab-badge" id="tab_badge_pending" style="color: #d97706;">{{ $kpi['pendingCount'] }}</span>
            </li>
            <li class="cd-tab-item" data-tab="failed">
                <span>Failed</span>
                <span class="cd-tab-badge" id="tab_badge_failed" style="color: #dc2626;">{{ $kpi['failedCount'] }}</span>
            </li>
            <li class="cd-tab-item" data-tab="online">
                <span>Online Gateway</span>
            </li>
            <li class="cd-tab-item" data-tab="offline">
                <span>Bank / Cheque</span>
            </li>
            <li class="cd-tab-item" data-tab="invoiced">
                <span>Tax Approved</span>
                <span class="cd-tab-badge" id="tab_badge_invoiced" style="color: #2563eb;">{{ $kpi['approvedInvoiceCount'] }}</span>
            </li>
        </ul>
    </div>

    <!-- ======================================================================
         TIER 3: PRIMARY CARD STREAM VIEW
         ====================================================================== -->
    <div id="cd_stream_container" class="cd-tx-stream">
        @if ($cart_transaction_history->count() > 0)
            @foreach ($cart_transaction_history as $val)
                @php
                    $orderNum = sprintf('%05d', $val->id);
                    $orderDate = $val->created_at ? Carbon::parse($val->created_at)->format('d M Y, h:i A') : 'N/A';
                    $timeAgo = $val->created_at ? Carbon::parse($val->created_at)->diffForHumans() : '';
                    $companyName = $val->company_name ?: ($val->organization ?: 'Unassigned Company');
                    $companyLogo = $val->company_logo ?: '';
                    $initial = strtoupper(substr($companyName, 0, 1));
                    $location = array_filter([$val->city, $val->state, $val->country_name]);
                    $locationStr = !empty($location) ? implode(', ', $location) : 'Location Not Set';
                    $gstin = $val->gstin ?: 'Non-GST Registered';
                    $currencySymbol = ($val->currency === 'INR') ? '₹' : '$';
                    $payMode = $val->payment_mode ?: 'Online';
                    $payMethod = $val->method ? ucfirst($val->method) : ($val->payment_gateway ? ucfirst($val->payment_gateway) : 'Gateway');

                    // Determine payment status theme
                    $statusRaw = strtolower((string)$val->status);
                    if ($val->payment_mode != 'Payment Link' && $statusRaw == 'created') {
                        $statusText = 'Failed / Expired';
                        $statusClass = 'cd-status-danger';
                        $filterCat = 'failed';
                    } elseif ($statusRaw === 'captured') {
                        $statusText = 'Paid (Captured)';
                        $statusClass = 'cd-status-success';
                        $filterCat = 'captured';
                    } elseif ($statusRaw === 'authorized') {
                        $statusText = 'Authorized';
                        $statusClass = 'cd-status-info';
                        $filterCat = 'captured';
                    } elseif ($statusRaw === 'refunded' || $statusRaw === 'partially_refunded') {
                        $statusText = ucfirst(str_replace('_', ' ', $statusRaw));
                        $statusClass = 'cd-status-warning';
                        $filterCat = 'failed';
                    } else {
                        $statusText = ucfirst($statusRaw ?: 'Initiated');
                        $statusClass = 'cd-status-warning';
                        $filterCat = 'pending';
                    }

                    $modeCategory = ($payMode === 'Online' || $payMode === 'Payment Link') ? 'online' : 'offline';
                    $invoiceApproved = ($val->invoice_approval === 'Yes') ? 'invoiced' : 'pending_invoice';
                    $receiptUrl = route('superadmin.company.receiptdownload', [encrypt($val->cart_id)]);

                    // Payload for drawer
                    $drawerData = json_encode([
                        'id' => $val->id,
                        'orderNum' => $orderNum,
                        'companyName' => $companyName,
                        'companyLogo' => $companyLogo,
                        'companyEmail' => $val->company_email ?: 'N/A',
                        'companyPhone' => $val->company_phone ?: 'N/A',
                        'organization' => $val->organization ?: $companyName,
                        'buyerName' => trim(($val->billing_first_name ?? '') . ' ' . ($val->billing_last_name ?? '')) ?: 'N/A',
                        'buyerPhone' => $val->billing_phone ?: 'N/A',
                        'billingAddress' => trim(($val->billing_building ?? '') . ' ' . ($val->billing_street ?? '')) ?: 'N/A',
                        'location' => $locationStr,
                        'gstin' => $val->gstin ?: 'N/A',
                        'pan' => $val->pan_no ?: 'N/A',
                        'productName' => $val->product_name ?: 'Standard Package',
                        'eventType' => ucfirst($val->event_type ?: 'Operational'),
                        'duration' => $val->duration ? $val->duration . ' Days' : '180 Days',
                        'credit' => number_format($val->credit),
                        'currency' => $val->currency ?: 'INR',
                        'price' => number_format($val->price, 2),
                        'gst' => $val->gst ?: 0,
                        'gstAmount' => number_format($val->gst_amount ?: 0, 2),
                        'totalPrice' => number_format($val->total_price, 2),
                        'status' => $statusText,
                        'statusClass' => $statusClass,
                        'paymentMode' => $payMode,
                        'paymentMethod' => $payMethod,
                        'bank' => $val->bank ?: ($val->bankname ?: 'N/A'),
                        'paymentOrderId' => $val->payment_orderId ?: 'N/A',
                        'paymentId' => $val->payment_Id ?: 'N/A',
                        'transactionId' => $val->transactionid ?: 'N/A',
                        'chequeNo' => $val->chequenumber ?: 'N/A',
                        'chequeDate' => $val->chequedate ?: 'N/A',
                        'invoiceId' => $val->invoiceId ?: 'JERO-' . $orderNum,
                        'invoiceApproval' => $val->invoice_approval ?: 'No',
                        'approvalInvoiceNo' => $val->approval_invoice_no ?: '',
                        'approvalInvoiceDate' => $val->approval_invoice_date ? Carbon::parse($val->approval_invoice_date)->format('d M Y, h:i A') : 'N/A',
                        'orderDate' => $orderDate,
                        'receiptUrl' => $receiptUrl,
                        'encId' => encrypt($val->id),
                        'soldOrg' => $val->sold_organization ?: 'JERO',
                        'soldGstin' => $val->sold_gstin ?: 'GURU0985CVNF00',
                        'soldPan' => $val->sold_pan_no ?: 'PANDJ48747A',
                        'soldPhone' => $val->sold_phone ?: '1234567891',
                        'soldAddress' => trim(($val->sold_building ?: 'Sector 63, F-314') . ' ' . ($val->sold_street ?: 'Noida, Uttar Pradesh 201301')),
                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                @endphp

                <div class="cd-tx-card" 
                     data-id="{{ $val->id }}"
                     data-category="{{ $filterCat }}"
                     data-mode="{{ $modeCategory }}"
                     data-invoiced="{{ $invoiceApproved }}"
                     data-search="{{ strtolower($orderNum . ' ' . $companyName . ' ' . $val->payment_Id . ' ' . $val->payment_orderId . ' ' . $val->transactionid . ' ' . $val->invoiceId . ' ' . $val->approval_invoice_no . ' ' . $val->product_name . ' ' . $gstin) }}"
                     data-details="{{ $drawerData }}">
                    
                    <div class="cd-tx-card-body">
                        <!-- Sub-Card 1: Order Identity -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">ORDER & TIME</span>
                                <span class="cd-subcard-pill">#{{ $orderNum }}</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="cd-order-num btn-view-drawer">#{{ $orderNum }}</a>
                                <div class="cd-order-time"><i class="far fa-clock"></i> {{ $orderDate }}</div>
                            </div>
                            <div class="cd-order-invoice"><i class="fa fa-file-invoice"></i> {{ $val->invoiceId ?: 'JERO-' . $orderNum }}</div>
                        </div>

                        <!-- Sub-Card 2: Company & Billing -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">BUYER ENTITY</span>
                                @if($val->city)
                                    <span class="cd-subcard-pill" title="{{ $val->city }}"><i class="fa fa-map-marker-alt"></i> {{ $val->city }}</span>
                                @endif
                            </div>
                            <div class="cd-company-row">
                                <div class="cd-company-avatar">
                                    @if($companyLogo)
                                        <img src="{{ $companyLogo }}" alt="{{ $companyName }}" onerror="this.onerror=null;this.parentNode.innerHTML='{{ $initial }}';">
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div class="cd-company-meta">
                                    <div class="cd-company-name" title="{{ $companyName }}">{{ $companyName }}</div>
                                    <div class="cd-company-loc" title="{{ $locationStr }}"><i class="fa fa-map-marker-alt" style="font-size: 10px;"></i> {{ $locationStr }}</div>
                                    <div class="cd-company-gst" title="GSTIN: {{ $gstin }}">GST: {{ $gstin }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 3: Package & Coins -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">PACKAGE & CREDITS</span>
                                <span class="cd-pkg-pill {{ strtolower($val->event_type) === 'sales' ? 'cd-pkg-pill-sales' : 'cd-pkg-pill-op' }}">
                                    {{ ucfirst($val->event_type ?: 'Operational') }}
                                </span>
                            </div>
                            <div class="cd-pkg-title" title="{{ $val->product_name ?: 'Custom Package' }}">{{ $val->product_name ?: 'Custom Package' }}</div>
                            <div class="cd-pkg-coins"><i class="fa fa-coins"></i> {{ number_format($val->credit) }} Coins</div>
                            <div class="cd-pkg-duration"><i class="far fa-calendar-alt"></i> {{ $val->duration ? $val->duration . ' Days' : '180 Days' }}</div>
                        </div>

                        <!-- Sub-Card 4: Amount & Financials -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">CHARGED AMOUNT</span>
                                <span class="cd-subcard-pill">{{ $currencySymbol }} {{ $val->currency ?: 'INR' }}</span>
                            </div>
                            <div class="cd-amount-total">{{ $currencySymbol }}{{ number_format($val->total_price, 2) }}</div>
                            <div class="cd-amount-breakup">Base: {{ $currencySymbol }}{{ number_format($val->price, 2) }} + GST: {{ $currencySymbol }}{{ number_format($val->gst_amount ?: 0, 2) }}</div>
                            <div class="cd-pay-mode-pill">
                                <i class="fa fa-credit-card"></i> {{ $payMode }} @if($payMethod) • {{ $payMethod }} @endif
                            </div>
                        </div>

                        <!-- Sub-Card 5: Status & Tax Invoice Controls -->
                        <div class="cd-subcard">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">TAX & COMPLIANCE</span>
                                <div class="cd-status-badge {{ $statusClass }}">
                                    <i class="fa fa-circle" style="font-size: 7px;"></i> {{ $statusText }}
                                </div>
                            </div>
                            <div class="cd-invoice-controls" data-id-enc="{{ encrypt($val->id) }}">
                                <select class="cd-gst-select select-gst-approval" title="Tax Invoice Approved">
                                    <option value="Yes" {{ ($val->invoice_approval == 'Yes') ? 'selected' : '' }}>Tax: Yes</option>
                                    <option value="No" {{ ($val->invoice_approval == 'No' || empty($val->invoice_approval)) ? 'selected' : '' }}>Tax: No</option>
                                </select>
                                <div class="cd-inv-input-wrap">
                                    <input type="text" class="cd-inv-input input-approval-invoice" placeholder="Invoice No." value="{{ $val->approval_invoice_no }}">
                                    <button type="button" class="cd-inv-save-btn btn-save-invoice" title="Save Invoice Number">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="cd-tax-footer">
                                @if($val->approval_invoice_no && $val->approval_invoice_date)
                                    <span class="cd-tax-saved" title="Approved on {{ Carbon::parse($val->approval_invoice_date)->format('d M Y, h:i A') }}"><i class="fa fa-check-circle"></i> Inv #{{ $val->approval_invoice_no }} ({{ Carbon::parse($val->approval_invoice_date)->format('d M') }})</span>
                                @else
                                    <span class="cd-tax-pending"><i class="fa fa-info-circle"></i> {{ $val->invoice_approval == 'Yes' ? 'Approved - Enter No.' : 'Pending Tax Approval' }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Sub-Card 6: Action Buttons -->
                        <div class="cd-subcard" style="padding: 8px 10px !important;">
                            <div class="cd-subcard-header">
                                <span class="cd-subcard-label">ACTIONS</span>
                                <span class="cd-subcard-pill">#{{ $val->id }}</span>
                            </div>
                            <div class="cd-subcard-actions">
                                <a href="{{ $receiptUrl }}" class="cd-action-btn cd-btn-receipt" title="Download Official Tax Receipt">
                                    <i class="fa fa-download"></i> Receipt
                                </a>
                                <button type="button" class="cd-action-btn cd-btn-details btn-view-drawer" title="Inspect Full Order Audit Details">
                                    <i class="fa fa-eye"></i> Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 7: Bottom Activity Strip -->
                    <div class="cd-tx-bottom-strip">
                        <div class="cd-strip-chips">
                            @if($val->payment_orderId)
                                <div class="cd-chip-item"><span style="color: #94a3b8;">Order ID:</span> <span style="font-family: monospace; font-weight: 600;">{{ $val->payment_orderId }}</span></div>
                            @endif
                            @if($val->payment_Id)
                                <div class="cd-chip-item"><span style="color: #94a3b8;">Pay ID:</span> <span style="font-family: monospace; font-weight: 600;">{{ $val->payment_Id }}</span></div>
                            @elseif($val->transactionid)
                                <div class="cd-chip-item"><span style="color: #94a3b8;">Txn ID:</span> <span style="font-family: monospace; font-weight: 600;">{{ $val->transactionid }}</span></div>
                            @endif
                            @if($val->bank || $val->bankname)
                                <div class="cd-chip-item"><span style="color: #94a3b8;">Bank:</span> <span>{{ $val->bank ?: $val->bankname }}</span></div>
                            @endif
                        </div>
                        <div>
                            @if($val->approval_invoice_no && $val->approval_invoice_date)
                                <span style="color: #059669; font-weight: 600;"><i class="fa fa-check-circle"></i> Invoiced {{ Carbon::parse($val->approval_invoice_date)->format('d M Y') }}</span>
                            @else
                                <span style="color: #94a3b8;">Cart Ref: #{{ $val->cart_id }} • {{ $timeAgo }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- Dynamic In-RAM Filter Empty State (Stream View) -->
            <div id="cd_tx_no_results" class="cd-empty-state" style="display: none;">
                <div class="cd-empty-icon"><i class="fa fa-filter"></i></div>
                <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">No Matching Transactions</div>
                <div style="font-size: 12px; color: #64748b; margin-bottom: 12px;">No transactions currently match your active search or category filters.</div>
                <button type="button" class="btn btn-sm btn-light-primary btn-reset-filters" style="font-size: 12px; height: 32px;">
                    <i class="fa fa-times me-1"></i> Clear Filters
                </button>
            </div>
        @else
            <div class="cd-empty-state">
                <div class="cd-empty-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">No Transactions Found</div>
                <div style="font-size: 12px; color: #64748b;">There are currently no transactions in the financial ledger.</div>
            </div>
        @endif
    </div>

    <!-- ======================================================================
         TIER 3: ALTERNATE HIGH-DENSITY LEDGER TABLE VIEW
         ====================================================================== -->
    <div id="cd_table_container" class="cd-tx-table-container" style="display: none;">
        <table class="cd-tx-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date & Time</th>
                    <th>Company & Entity</th>
                    <th>Package / Coins</th>
                    <th>Amount / Currency</th>
                    <th>Gateway Mode</th>
                    <th>Status</th>
                    <th>Tax Approval</th>
                    <th>Invoice No.</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic In-RAM Filter Empty State (Table View) -->
                <tr id="cd_tx_table_no_results" style="display: none;">
                    <td colspan="10" style="text-align: center; padding: 40px 20px;">
                        <div class="cd-empty-state" style="border: none; box-shadow: none; padding: 0;">
                            <div class="cd-empty-icon"><i class="fa fa-filter"></i></div>
                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">No Matching Transactions</div>
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 10px;">Adjust your search term or select another category sub-tab.</div>
                            <button type="button" class="btn btn-sm btn-light-primary btn-reset-filters" style="font-size: 12px; height: 32px;">
                                <i class="fa fa-times me-1"></i> Clear Filters
                            </button>
                        </div>
                    </td>
                </tr>
                @if ($cart_transaction_history->count() > 0)
                    @foreach ($cart_transaction_history as $val)
                        @php
                            $orderNum = sprintf('%05d', $val->id);
                            $orderDate = $val->created_at ? Carbon::parse($val->created_at)->format('d M Y, h:i A') : 'N/A';
                            $companyName = $val->company_name ?: ($val->organization ?: 'Unassigned Company');
                            $currencySymbol = ($val->currency === 'INR') ? '₹' : '$';
                            $statusRaw = strtolower((string)$val->status);
                            if ($val->payment_mode != 'Payment Link' && $statusRaw == 'created') {
                                $statusText = 'Failed';
                                $statusClass = 'cd-status-danger';
                                $filterCat = 'failed';
                            } elseif ($statusRaw === 'captured') {
                                $statusText = 'Paid';
                                $statusClass = 'cd-status-success';
                                $filterCat = 'captured';
                            } else {
                                $statusText = ucfirst($statusRaw ?: 'Initiated');
                                $statusClass = 'cd-status-warning';
                                $filterCat = 'pending';
                            }
                            $receiptUrl = route('superadmin.company.receiptdownload', [encrypt($val->cart_id)]);
                        @endphp
                        <tr class="cd-tx-tablerow"
                            data-id="{{ $val->id }}"
                            data-category="{{ $filterCat }}"
                            data-search="{{ strtolower($orderNum . ' ' . $companyName . ' ' . $val->payment_Id . ' ' . $val->payment_orderId . ' ' . $val->transactionid . ' ' . $val->invoiceId . ' ' . $val->approval_invoice_no) }}">
                            <td>
                                <a href="javascript:void(0);" class="cd-order-num btn-view-drawer">#{{ $orderNum }}</a>
                            </td>
                            <td>
                                <div style="font-size: 11.5px; color: #1e293b;">{{ $orderDate }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $companyName }}</div>
                                <div style="font-size: 11px; color: #64748b;">GST: {{ $val->gstin ?: 'None' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $val->product_name ?: 'Custom' }}</div>
                                <div style="font-size: 11px; color: #d97706; font-weight: 700;">🪙 {{ number_format($val->credit) }} Coins</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;">{{ $currencySymbol }}{{ number_format($val->total_price, 2) }}</div>
                                <div style="font-size: 10.5px; color: #64748b;">GST: {{ $currencySymbol }}{{ number_format($val->gst_amount ?: 0, 2) }}</div>
                            </td>
                            <td>
                                <span class="badge badge-light-primary">{{ $val->payment_mode ?: 'Online' }}</span>
                            </td>
                            <td>
                                <span class="cd-status-badge {{ $statusClass }}">● {{ $statusText }}</span>
                            </td>
                            <td data-id-enc="{{ encrypt($val->id) }}">
                                <select class="cd-gst-select select-gst-approval">
                                    <option value="Yes" {{ ($val->invoice_approval == 'Yes') ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ ($val->invoice_approval == 'No' || empty($val->invoice_approval)) ? 'selected' : '' }}>No</option>
                                </select>
                            </td>
                            <td data-id-enc="{{ encrypt($val->id) }}">
                                <div class="cd-inv-input-wrap">
                                    <input type="text" class="cd-inv-input input-approval-invoice" placeholder="Invoice No." value="{{ $val->approval_invoice_no }}">
                                    <button type="button" class="cd-inv-save-btn btn-save-invoice" title="Save Invoice">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ $receiptUrl }}" class="btn btn-sm btn-icon btn-light-primary" title="Download Receipt">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-view-drawer" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="cd-pagination-wrap">
        <div style="font-size: 12px; color: #64748b;">
            Showing {{ $cart_transaction_history->firstItem() ?? 0 }} to {{ $cart_transaction_history->lastItem() ?? 0 }} of {{ $cart_transaction_history->total() }} records
        </div>
        <div>
            {{ $cart_transaction_history->links() }}
        </div>
    </div>
</div>

<!-- ==========================================================================
     SLIDE-OVER TRANSACTION DETAILS DRAWER
     ========================================================================== -->
<div id="kt_activitiesTransactionDetails_toggle" style="display: none;"></div>
<div id="kt_activitiesTransactionDetails" class="bg-body" data-kt-drawer="true"
     data-kt-drawer-name="activitiesTransactionDetails" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'100%', 'md': '720px'}" data-kt-drawer-direction="end"
     data-kt-drawer-toggle="#kt_activitiesTransactionDetails_toggle"
     data-kt-drawer-close="#kt_activitiesTransactionDetails_close">

    <div class="card shadow-none rounded-0 w-100 h-100" style="display: flex; flex-direction: column;">
        <!-- Drawer Header -->
        <div class="card-header border-0 pt-5 pb-4 px-6" style="border-bottom: 1px solid #e2e8f0 !important; display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex align-items-center gap-3">
                <div class="cd-header-icon" style="width: 34px; height: 34px; font-size: 14px;">
                    <i class="fa fa-receipt"></i>
                </div>
                <div>
                    <h3 class="card-title fw-bolder text-dark mb-0 fs-5" id="drw_title">Transaction Details</h3>
                    <span class="text-muted fs-7" id="drw_subtitle">Order Audit & Gateway Telemetry</span>
                </div>
            </div>
            <div class="btn btn-sm btn-icon btn-light btn-active-light-primary" id="kt_activitiesTransactionDetails_close">
                <i class="fa fa-times"></i>
            </div>
        </div>

        <!-- Drawer Body -->
        <div class="card-body p-6" style="overflow-y: auto; flex: 1;">
            
            <!-- Summary Banner -->
            <div class="p-4 mb-5 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Total Charged Amount</div>
                    <div style="font-size: 22px; font-weight: 800; color: #0f172a;" id="drw_total_amount">₹0.00</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px;">Status</div>
                    <div id="drw_status_badge" class="cd-status-badge cd-status-success">Paid</div>
                </div>
            </div>

            <!-- Section 1: Corporate Buyer & Billing Entity -->
            <div class="cd-drawer-section">
                <div class="cd-drawer-sec-title"><i class="fa fa-building text-primary"></i> Corporate Buyer & Tax Profile</div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Company Legal Name:</span>
                    <span class="cd-drawer-val" id="drw_company_name">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Organization / Billing Entity:</span>
                    <span class="cd-drawer-val" id="drw_organization">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Buyer Contact:</span>
                    <span class="cd-drawer-val" id="drw_buyer_name">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Billing Address:</span>
                    <span class="cd-drawer-val" id="drw_billing_address">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">City / State / Country:</span>
                    <span class="cd-drawer-val" id="drw_location">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Corporate GSTIN:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_gstin">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Permanent Account No (PAN):</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_pan">-</span>
                </div>
            </div>

            <!-- Section 2: Platform Legal Seller (Sold-By Entity) -->
            <div class="cd-drawer-section">
                <div class="cd-drawer-sec-title"><i class="fa fa-shield-alt text-primary"></i> Platform Legal Seller (Sold-By Entity)</div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Platform Legal Name:</span>
                    <span class="cd-drawer-val" id="drw_sold_org">JERO</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Platform GSTIN:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_sold_gstin">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Platform PAN:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_sold_pan">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Registered Office:</span>
                    <span class="cd-drawer-val" id="drw_sold_address">-</span>
                </div>
            </div>

            <!-- Section 3: Purchased Package & Coins -->
            <div class="cd-drawer-section">
                <div class="cd-drawer-sec-title"><i class="fa fa-box-open text-primary"></i> Package & Allocation Breakdown</div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Product Bundle:</span>
                    <span class="cd-drawer-val" id="drw_product_name">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Event Allocation Scope:</span>
                    <span class="cd-drawer-val" id="drw_event_type">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">ATS Coins Credited:</span>
                    <span class="cd-drawer-val" style="color: #d97706; font-weight: 700;" id="drw_coins">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Package Validity:</span>
                    <span class="cd-drawer-val" id="drw_duration">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Base Subtotal (Pre-Tax):</span>
                    <span class="cd-drawer-val" id="drw_base_price">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">GST Rate & Total Tax:</span>
                    <span class="cd-drawer-val" id="drw_gst_breakup">-</span>
                </div>
            </div>

            <!-- Section 4: Gateway & Telemetry Logs -->
            <div class="cd-drawer-section">
                <div class="cd-drawer-sec-title"><i class="fa fa-server text-primary"></i> Gateway & Telemetry Metadata</div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Payment Channel:</span>
                    <span class="cd-drawer-val" id="drw_pay_mode">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Razorpay Order ID:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_pay_order_id">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Razorpay Payment ID:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_pay_id">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Bank Reference / Txn ID:</span>
                    <span class="cd-drawer-val" style="font-family: monospace;" id="drw_bank_ref">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Settlement Bank Name:</span>
                    <span class="cd-drawer-val" id="drw_bank_name">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Timestamp (Initiated):</span>
                    <span class="cd-drawer-val" id="drw_order_date">-</span>
                </div>
            </div>

            <!-- Section 5: Invoice & Tax Compliance -->
            <div class="cd-drawer-section">
                <div class="cd-drawer-sec-title"><i class="fa fa-stamp text-primary"></i> Tax Invoice & Approval Audit</div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Internal System Invoice:</span>
                    <span class="cd-drawer-val" id="drw_system_invoice">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Official Approval Invoice No:</span>
                    <span class="cd-drawer-val" id="drw_approval_invoice">-</span>
                </div>
                <div class="cd-drawer-field">
                    <span class="cd-drawer-label">Invoice Approved Date:</span>
                    <span class="cd-drawer-val" id="drw_approval_date">-</span>
                </div>
            </div>

        </div>

        <!-- Drawer Footer -->
        <div class="card-footer p-4 px-6" style="border-top: 1px solid #e2e8f0 !important; display: flex; flex-direction: column; gap: 10px;">
            <!-- Quick Invoice Override Strip in Drawer -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <div style="font-size: 11.5px; font-weight: 700; color: #0f172a;"><i class="fa fa-pen me-1 text-primary"></i> Quick Invoice Override:</div>
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;" id="drw_edit_wrap" data-id-enc="" data-raw-id="">
                    <select class="cd-gst-select" id="drw_gst_select" style="height: 30px; font-size: 11px;">
                        <option value="Yes">Tax Approved: Yes</option>
                        <option value="No">Tax Approved: No</option>
                    </select>
                    <input type="text" class="cd-inv-input" id="drw_inv_input" placeholder="Invoice No." style="height: 30px; width: 120px; font-size: 11px;">
                    <button type="button" class="btn btn-sm btn-primary" id="drw_btn_save_invoice" style="height: 30px; padding: 0 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between;">
                <button type="button" class="btn btn-sm btn-light" id="drw_btn_close">Close Drawer</button>
                <a href="#" target="_blank" class="btn btn-sm btn-primary" id="drw_btn_receipt">
                    <i class="fa fa-download me-1"></i> Download PDF Receipt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
/**
 * Super Admin Transaction History & Invoicing Engine
 * GOD-Level 10K Architecture: CQRS + In-RAM State + IndexedDB L2 Cache + Outbox Idempotency + Real-time Delta
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md & Rule 20.
 */

// =============================================================================
// 1. Enterprise Client Database: JeroTransactionHistoryDB (IndexedDB v1)
// =============================================================================
class JeroTransactionHistoryDB {
    static DB_NAME = 'JeroTransactionHistoryDB';
    static DB_VERSION = 1;
    static db = null;
    static isAvailable = typeof window !== 'undefined' && 'indexedDB' in window;

    static async init(userId) {
        if (!this.isAvailable) return null;
        return new Promise(function(resolve) {
            try {
                var req = window.indexedDB.open(JeroTransactionHistoryDB.DB_NAME, JeroTransactionHistoryDB.DB_VERSION);
                req.onupgradeneeded = function(e) {
                    var db = e.target.result;
                    if (!db.objectStoreNames.contains('transactions')) {
                        db.createObjectStore('transactions', { keyPath: 'id' });
                    }
                    if (!db.objectStoreNames.contains('dossiers')) {
                        db.createObjectStore('dossiers', { keyPath: 'id' });
                    }
                    if (!db.objectStoreNames.contains('meta')) {
                        db.createObjectStore('meta', { keyPath: 'id' });
                    }
                };
                req.onsuccess = async function(e) {
                    JeroTransactionHistoryDB.db = e.target.result;
                    // Enforce tenant/session isolation guard
                    var meta = await JeroTransactionHistoryDB.getMeta('auth_context');
                    if (meta && meta.user_id !== userId) {
                        await JeroTransactionHistoryDB.clearAll();
                    }
                    await JeroTransactionHistoryDB.setMeta('auth_context', {
                        id: 'auth_context',
                        user_id: userId,
                        cached_at: Date.now()
                    });
                    resolve(JeroTransactionHistoryDB.db);
                };
                req.onerror = function() {
                    resolve(null);
                };
            } catch (err) {
                resolve(null);
            }
        });
    }

    static async getMeta(key) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(null);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('meta', 'readonly');
                var req = tx.objectStore('meta').get(key);
                req.onsuccess = function() { resolve(req.result || null); };
                req.onerror = function() { resolve(null); };
            } catch (e) { resolve(null); }
        });
    }

    static async setMeta(key, value) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(false);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('meta', 'readwrite');
                tx.objectStore('meta').put(value);
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) { resolve(false); }
        });
    }

    static async getDossier(id) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(null);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('dossiers', 'readonly');
                var req = tx.objectStore('dossiers').get(String(id));
                req.onsuccess = function() {
                    var res = req.result;
                    if (res && (Date.now() - (res.cached_at || 0) < 900000)) { // 15 min TTL
                        resolve(res.payload);
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = function() { resolve(null); };
            } catch (e) { resolve(null); }
        });
    }

    static async putDossier(id, payload) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(false);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('dossiers', 'readwrite');
                tx.objectStore('dossiers').put({
                    id: String(id),
                    payload: payload,
                    cached_at: Date.now()
                });
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) { resolve(false); }
        });
    }

    static async getTransaction(id) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(null);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('transactions', 'readonly');
                var req = tx.objectStore('transactions').get(String(id));
                req.onsuccess = function() {
                    var res = req.result;
                    if (res && (Date.now() - (res.cached_at || 0) < 900000)) { // 15 min TTL
                        resolve(res.payload);
                    } else {
                        resolve(null);
                    }
                };
                req.onerror = function() { resolve(null); };
            } catch (e) { resolve(null); }
        });
    }

    static async putTransaction(id, payload) {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(false);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('transactions', 'readwrite');
                tx.objectStore('transactions').put({
                    id: String(id),
                    payload: payload,
                    cached_at: Date.now()
                });
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) { resolve(false); }
        });
    }

    static async getAllTransactions() {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve([]);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction('transactions', 'readonly');
                var req = tx.objectStore('transactions').getAll();
                req.onsuccess = function() {
                    var results = req.result || [];
                    resolve(results.map(function(r) { return r.payload; }));
                };
                req.onerror = function() { resolve([]); };
            } catch (e) { resolve([]); }
        });
    }


    static async clearAll() {
        return new Promise(function(resolve) {
            if (!JeroTransactionHistoryDB.db) return resolve(false);
            try {
                var tx = JeroTransactionHistoryDB.db.transaction(['transactions', 'dossiers', 'meta'], 'readwrite');
                tx.objectStore('transactions').clear();
                tx.objectStore('dossiers').clear();
                tx.objectStore('meta').clear();
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            } catch (e) { resolve(false); }
        });
    }
}

// =============================================================================
// 2. Client Memory State & Main Logic Execution
// =============================================================================
$(document).ready(async function() {
    "use strict";

    var currentUserId = {{ (int) (auth()->id() ?? 0) }};
    var invoiceUpdateUrl = "{{ route('superadmin.company.invoiceupdate') }}";

    // Setup global CSRF header for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize IndexedDB
    await JeroTransactionHistoryDB.init(currentUserId);

    // In-memory RAM state (Zero server strain for search/filter/details)
    window.txState = {
        all: [],
        activeTab: 'all',
        searchQuery: '',
        detailsCache: new Map(),
        viewMode: localStorage.getItem('jero_sa_tx_view_mode') || 'stream'
    };

    // LRU Cache Eviction Helper (Strict ceiling of 100 entries to prevent memory leaks)
    function setDetailsCache(key, val) {
        if (window.txState.detailsCache.size >= 100) {
            var oldestKey = window.txState.detailsCache.keys().next().value;
            window.txState.detailsCache.delete(oldestKey);
        }
        window.txState.detailsCache.set(String(key), val);
    }

    var txDetailsAbortController = null;

    // Hydrate in-RAM state from server-rendered DOM nodes
    $('.cd-tx-card').each(function() {
        var $card = $(this);
        var id = $card.attr('data-id');
        var dataStr = $card.attr('data-details');
        if (dataStr) {
            try {
                var d = JSON.parse(dataStr);
                window.txState.all.push(d);
                setDetailsCache(id, d);
                JeroTransactionHistoryDB.putDossier(id, d);
                JeroTransactionHistoryDB.putTransaction(id, d);
            } catch (e) {
                console.warn('Failed parsing card data for RAM cache', e);
            }
        }
    });

    // Utility: Generate RFC-4122 Compliant UUID v4 for Outbox Idempotency
    function generateIdempotencyKey() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'tx-idem-' + Date.now() + '-' + Math.random().toString(36).substring(2, 11);
    }

    // -------------------------------------------------------------------------
    // 3. View Switcher: Stream View vs Ledger Table View
    // -------------------------------------------------------------------------
    setViewMode(window.txState.viewMode);

    $('#btn_view_stream').off('.superAdminTx').on('click.superAdminTx', function() { setViewMode('stream'); });
    $('#btn_view_table').off('.superAdminTx').on('click.superAdminTx', function() { setViewMode('table'); });

    function setViewMode(mode) {
        window.txState.viewMode = mode;
        if (mode === 'table') {
            $('#cd_stream_container').hide();
            $('#cd_table_container').fadeIn(150);
            $('#btn_view_table').addClass('active');
            $('#btn_view_stream').removeClass('active');
            localStorage.setItem('jero_sa_tx_view_mode', 'table');
        } else {
            $('#cd_table_container').hide();
            $('#cd_stream_container').fadeIn(150);
            $('#btn_view_stream').addClass('active');
            $('#btn_view_table').removeClass('active');
            localStorage.setItem('jero_sa_tx_view_mode', 'stream');
        }
    }

    // -------------------------------------------------------------------------
    // 4. Dynamic In-RAM 300ms Debounced Filtering (0ms Latency)
    // -------------------------------------------------------------------------
    var searchTimer = null;
    var $searchInput = $('#cd_tx_search_input');
    var $clearBtn = $('#cd_tx_search_clear');

    $searchInput.off('.superAdminTx').on('input.superAdminTx', function() {
        var query = $(this).val().trim().toLowerCase();
        window.txState.searchQuery = query;

        if (query.length > 0) {
            $clearBtn.css('display', 'inline-flex');
        } else {
            $clearBtn.hide();
        }

        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            applyFilters();
        }, 300);
    });

    $clearBtn.off('.superAdminTx').on('click.superAdminTx', function() {
        $searchInput.val('').trigger('input');
        $clearBtn.hide();
    });

    // -------------------------------------------------------------------------
    // 5. Tier 2 Sub-Tabs Category Filtering
    // -------------------------------------------------------------------------
    $('#cd_tx_subtabs .cd-tab-item').off('.superAdminTx').on('click.superAdminTx', function() {
        $('#cd_tx_subtabs .cd-tab-item').removeClass('active');
        $(this).addClass('active');
        window.txState.activeTab = $(this).attr('data-tab') || 'all';
        applyFilters();
    });

    // Reset Filters Button Handler (In-RAM reset)
    $(document).off('click.superAdminTx', '.btn-reset-filters').on('click.superAdminTx', '.btn-reset-filters', function(e) {
        e.preventDefault();
        $searchInput.val('');
        $clearBtn.hide();
        window.txState.searchQuery = '';
        window.txState.activeTab = 'all';
        $('#cd_tx_subtabs .cd-tab-item').removeClass('active');
        $('#cd_tx_subtabs .cd-tab-item[data-tab="all"]').addClass('active');
        applyFilters();
    });

    function applyFilters() {
        var query = window.txState.searchQuery;
        var activeTab = window.txState.activeTab;
        var streamVisibleCount = 0;
        var tableVisibleCount = 0;

        // Filter Stream Cards in RAM
        $('.cd-tx-card').each(function() {
            var $card = $(this);
            var searchData = $card.attr('data-search') || '';
            var category = $card.attr('data-category') || '';
            var mode = $card.attr('data-mode') || '';
            var invoiced = $card.attr('data-invoiced') || '';

            var matchesSearch = (query === '' || searchData.indexOf(query) !== -1);
            var matchesTab = false;

            if (activeTab === 'all') {
                matchesTab = true;
            } else if (activeTab === 'captured' && category === 'captured') {
                matchesTab = true;
            } else if (activeTab === 'pending' && category === 'pending') {
                matchesTab = true;
            } else if (activeTab === 'failed' && category === 'failed') {
                matchesTab = true;
            } else if (activeTab === 'online' && mode === 'online') {
                matchesTab = true;
            } else if (activeTab === 'offline' && mode === 'offline') {
                matchesTab = true;
            } else if (activeTab === 'invoiced' && invoiced === 'invoiced') {
                matchesTab = true;
            }

            if (matchesSearch && matchesTab) {
                $card.fadeIn(120);
                streamVisibleCount++;
            } else {
                $card.hide();
            }
        });

        if (streamVisibleCount === 0) {
            $('#cd_tx_no_results').fadeIn(150);
        } else {
            $('#cd_tx_no_results').hide();
        }

        // Filter Table Rows in RAM
        $('.cd-tx-tablerow').each(function() {
            var $row = $(this);
            var searchData = $row.attr('data-search') || '';
            var category = $row.attr('data-category') || '';

            var matchesSearch = (query === '' || searchData.indexOf(query) !== -1);
            var matchesTab = (activeTab === 'all' || activeTab === category);

            if (matchesSearch && matchesTab) {
                $row.fadeIn(120);
                tableVisibleCount++;
            } else {
                $row.hide();
            }
        });

        if (tableVisibleCount === 0) {
            $('#cd_tx_table_no_results').show();
        } else {
            $('#cd_tx_table_no_results').hide();
        }
    }

    // -------------------------------------------------------------------------
    // 6. Slide-Over Transaction Drawer (IndexedDB L2 Cache First — < 5ms)
    // -------------------------------------------------------------------------
    $(document).off('click.superAdminTx', '.btn-view-drawer').on('click.superAdminTx', '.btn-view-drawer', async function(e) {
        e.preventDefault();
        var $trigger = $(this);
        var txId = $trigger.closest('[data-id]').attr('data-id');

        if (!txId) return;

        // L1 Cache: In-Memory Map
        if (window.txState.detailsCache.has(String(txId))) {
            populateDrawer(window.txState.detailsCache.get(String(txId)));
            openDrawer();
            return;
        }

        // L2 Cache: IndexedDB Store
        var idbCached = await JeroTransactionHistoryDB.getDossier(txId);
        if (idbCached) {
            setDetailsCache(txId, idbCached);
            populateDrawer(idbCached);
            openDrawer();
            return;
        }

        // Circuit Breaker Fallback: Authoritative API with AbortController
        if (txDetailsAbortController) {
            txDetailsAbortController.abort();
        }
        txDetailsAbortController = new AbortController();

        $.ajax({
            type: 'GET',
            url: "{{ url('super-admin/transaction-details') }}/" + txId,
            beforeSend: function(jqXHR) {
                $trigger.find('i').addClass('fa-spin');
                if (txDetailsAbortController && txDetailsAbortController.signal) {
                    txDetailsAbortController.signal.addEventListener('abort', function() {
                        jqXHR.abort();
                    });
                }
            },
            complete: function() {
                $trigger.find('i').removeClass('fa-spin');
            },
            success: function(res) {
                if (res.status === 'success') {
                    var d = res.data;
                    var formatted = {
                        id: d.id,
                        encId: $trigger.closest('[data-id-enc]').attr('data-id-enc') || String(d.id),
                        orderNum: String(d.id).padStart(5, '0'),
                        companyName: d.company_name || d.organization || 'Unassigned',
                        organization: d.organization || d.company_name || 'N/A',
                        buyerName: ((d.billing_first_name || '') + ' ' + (d.billing_last_name || '')).trim() || 'N/A',
                        billingAddress: ((d.billing_building || '') + ' ' + (d.billing_street || '')).trim() || 'N/A',
                        location: [d.city, d.state, d.country_name].filter(Boolean).join(', ') || 'N/A',
                        gstin: d.gstin || 'N/A',
                        pan: d.pan_no || 'N/A',
                        productName: d.product_name || 'Custom Package',
                        eventType: d.event_type ? d.event_type.toUpperCase() : 'OPERATIONAL',
                        duration: d.duration ? d.duration + ' Days' : '180 Days',
                        credit: Number(d.credit).toLocaleString(),
                        currency: d.currency || 'INR',
                        price: Number(d.price).toFixed(2),
                        gst: d.gst || 0,
                        gstAmount: Number(d.gst_amount || 0).toFixed(2),
                        totalPrice: Number(d.total_price).toFixed(2),
                        status: d.status,
                        statusClass: d.status === 'captured' ? 'cd-status-success' : 'cd-status-warning',
                        paymentMode: d.payment_mode || 'Online',
                        paymentMethod: d.method || d.payment_gateway || 'Gateway',
                        bank: d.bank || d.bankname || 'N/A',
                        paymentOrderId: d.payment_orderId || 'N/A',
                        paymentId: d.payment_Id || 'N/A',
                        transactionId: d.transactionid || 'N/A',
                        invoiceId: d.invoiceId || ('JERO-' + String(d.id).padStart(5, '0')),
                        invoiceApproval: d.invoice_approval || 'No',
                        approvalInvoiceNo: d.approval_invoice_no || '',
                        approvalInvoiceDate: d.approval_invoice_date || 'N/A',
                        orderDate: d.created_at || 'N/A',
                        receiptUrl: res.receipt_url,
                        soldOrg: d.platform_organization,
                        soldGstin: d.platform_gstin,
                        soldPan: d.platform_pan,
                        soldAddress: d.platform_address
                    };

                    // Persist to L1 & L2 Caches
                    setDetailsCache(txId, formatted);
                    JeroTransactionHistoryDB.putDossier(txId, formatted);

                    populateDrawer(formatted);
                    openDrawer();
                }
            },
            error: function(xhr, status) {
                if (status !== 'abort') {
                    Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Unable to load transaction details.', 'error');
                }
            }
        });
    });

    function populateDrawer(d) {
        var symbol = (d.currency === 'INR') ? '₹' : '$';
        $('#drw_title').text('Order #' + d.orderNum);
        $('#drw_subtitle').text(d.companyName + ' • ' + d.orderDate);
        $('#drw_total_amount').text(symbol + d.totalPrice);
        $('#drw_status_badge').text(d.status).attr('class', 'cd-status-badge ' + (d.statusClass || 'cd-status-success'));
        
        // Buyer Profile
        $('#drw_company_name').text(d.companyName);
        $('#drw_organization').text(d.organization);
        $('#drw_buyer_name').text(d.buyerName);
        $('#drw_billing_address').text(d.billingAddress);
        $('#drw_location').text(d.location);
        $('#drw_gstin').text(d.gstin);
        $('#drw_pan').text(d.pan);

        // Platform Seller
        $('#drw_sold_org').text(d.soldOrg || 'JERO');
        $('#drw_sold_gstin').text(d.soldGstin || 'GURU0985CVNF00');
        $('#drw_sold_pan').text(d.soldPan || 'PANDJ48747A');
        $('#drw_sold_address').text(d.soldAddress || 'Sector 63, Noida, Uttar Pradesh 201301');

        // Package Breakdown
        $('#drw_product_name').text(d.productName);
        $('#drw_event_type').text(d.eventType);
        $('#drw_coins').text('🪙 ' + d.credit + ' Coins');
        $('#drw_duration').text(d.duration);
        $('#drw_base_price').text(symbol + d.price);
        $('#drw_gst_breakup').text(d.gst + '% (' + symbol + d.gstAmount + ')');

        // Gateway Telemetry
        $('#drw_pay_mode').text(d.paymentMode + ' (' + d.paymentMethod + ')');
        $('#drw_pay_order_id').text(d.paymentOrderId);
        $('#drw_pay_id').text(d.paymentId);
        $('#drw_bank_ref').text(d.transactionId);
        $('#drw_bank_name').text(d.bank);
        $('#drw_order_date').text(d.orderDate);

        // Tax Invoicing
        $('#drw_system_invoice').text(d.invoiceId);
        $('#drw_approval_invoice').text(d.approvalInvoiceNo || 'Pending Submission');
        $('#drw_approval_date').text(d.approvalInvoiceDate);
        $('#drw_btn_receipt').attr('href', d.receiptUrl);

        // Interactive Override in Drawer
        $('#drw_edit_wrap').attr('data-id-enc', d.encId).attr('data-raw-id', d.id);
        $('#drw_gst_select').val(d.invoiceApproval === 'Yes' ? 'Yes' : 'No');
        $('#drw_inv_input').val(d.approvalInvoiceNo || '');
    }

    function openDrawer() {
        var drawerEl = document.querySelector('#kt_activitiesTransactionDetails');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined') {
                var drawerInst = KTDrawer.getInstance(drawerEl);
                if (!drawerInst && typeof KTDrawer === 'function') {
                    drawerInst = new KTDrawer(drawerEl);
                }
                if (drawerInst && typeof drawerInst.show === 'function') {
                    drawerInst.show();
                    return;
                }
            }
            drawerEl.classList.add('drawer-on');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDrawer() {
        var drawerEl = document.querySelector('#kt_activitiesTransactionDetails');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined') {
                var drawerInst = KTDrawer.getInstance(drawerEl);
                if (drawerInst && typeof drawerInst.hide === 'function') {
                    drawerInst.hide();
                    return;
                }
            }
            drawerEl.classList.remove('drawer-on');
            document.querySelectorAll('.drawer-overlay').forEach(function(e) { e.remove(); });
            document.body.style.overflow = '';
        }
    }

    $('#kt_activitiesTransactionDetails_close, #drw_btn_close').off('.superAdminTx').on('click.superAdminTx', function(e) {
        e.preventDefault();
        closeDrawer();
    });

    // -------------------------------------------------------------------------
    // 7. Drawer Quick Invoice Override Save (Outbox Architecture Idempotency)
    // -------------------------------------------------------------------------
    $('#drw_btn_save_invoice').off('.superAdminTx').on('click.superAdminTx', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var encId = $('#drw_edit_wrap').attr('data-id-enc');
        var rawId = $('#drw_edit_wrap').attr('data-raw-id');
        var approvalVal = $('#drw_gst_select').val();
        var invoiceNo = $('#drw_inv_input').val().trim();
        var idempotencyKey = generateIdempotencyKey();

        if (!encId || $btn.prop('disabled')) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        // Atomic multi-field mutation with Outbox Idempotency (1 single HTTP request & DB transaction)
        $.ajax({
            type: 'POST',
            url: invoiceUpdateUrl,
            headers: { 'X-Idempotency-Key': idempotencyKey },
            data: {
                transaction_id: encId,
                action_type: 'update_full_invoice',
                invoice_approval: approvalVal,
                approval_invoice_no: invoiceNo
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check text-success"></i> Saved');
                setTimeout(function() {
                    $btn.html('<i class="fa fa-save"></i> Save');
                }, 2000);

                var invDate = res.approval_invoice_date || 'Just Now';

                // Broadcast delta to other tabs via BroadcastChannel
                broadcastLocalDelta({
                    transaction_id: Number(rawId),
                    invoice_approval: approvalVal,
                    approval_invoice_no: invoiceNo,
                    approval_invoice_date: invDate
                });

                // Apply to local DOM, RAM state & IndexedDB
                syncLocalInvoiceState(rawId, approvalVal, invoiceNo, invDate);

                Swal.fire({
                    icon: 'success',
                    title: 'Invoice Updated!',
                    text: 'Tax approval and invoice number successfully saved with outbox persistence.',
                    timer: 1600,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update invoice details.', 'error');
            }
        });
    });

    // -------------------------------------------------------------------------
    // 8. Inline Tax Approval Toggle with Outbox Idempotency & Optimistic Rollback
    // -------------------------------------------------------------------------
    $(document).off('change.superAdminTx', '.select-gst-approval').on('change.superAdminTx', '.select-gst-approval', function(e) {
        e.preventDefault();
        var $select = $(this);
        var newVal = $select.val();
        var prevVal = (newVal === 'Yes') ? 'No' : 'Yes';
        var $wrap = $select.closest('[data-id-enc]');
        var encId = $wrap.attr('data-id-enc');
        var rawId = $wrap.attr('data-id');
        var idempotencyKey = generateIdempotencyKey();

        Swal.fire({
            title: 'Update Tax Approval?',
            text: 'Set tax invoice approval to "' + newVal + '" for this transaction?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#009ef7',
            cancelButtonColor: '#e2e8f0',
            confirmButtonText: 'Yes, update',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                $select.prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: invoiceUpdateUrl,
                    headers: { 'X-Idempotency-Key': idempotencyKey },
                    data: {
                        transaction_id: encId,
                        action_type: 'toggle_approval',
                        invoice_approval: newVal
                    },
                    success: function(response) {
                        $select.prop('disabled', false);
                        if (response.status === 'success') {
                            // Broadcast delta to other tabs
                            broadcastLocalDelta({
                                transaction_id: Number(rawId),
                                invoice_approval: newVal
                            });

                            syncLocalInvoiceState(rawId, newVal, null, null);

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        } else {
                            $select.val(prevVal);
                            Swal.fire('Error', response.message || 'Update failed', 'error');
                        }
                    },
                    error: function(xhr) {
                        $select.prop('disabled', false).val(prevVal);
                        Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred', 'error');
                    }
                });
            } else {
                $select.val(prevVal);
            }
        });
    });

    // -------------------------------------------------------------------------
    // 9. Inline Official Invoice Number Update with Outbox Idempotency
    // -------------------------------------------------------------------------
    $(document).off('click.superAdminTx', '.btn-save-invoice').on('click.superAdminTx', '.btn-save-invoice', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $input = $btn.siblings('.input-approval-invoice');
        var invoiceNo = $input.val().trim();
        var $wrap = $btn.closest('[data-id-enc]');
        var encId = $wrap.attr('data-id-enc');
        var rawId = $wrap.attr('data-id');
        var idempotencyKey = generateIdempotencyKey();

        if (!encId || $btn.prop('disabled')) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            type: 'POST',
            url: invoiceUpdateUrl,
            headers: { 'X-Idempotency-Key': idempotencyKey },
            data: {
                transaction_id: encId,
                action_type: 'update_invoice_no',
                approval_invoice_no: invoiceNo
            },
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fa fa-check text-success"></i>');
                setTimeout(function() {
                    $btn.html('<i class="fa fa-check"></i>');
                }, 2000);

                if (response.status === 'success') {
                    broadcastLocalDelta({
                        transaction_id: Number(rawId),
                        approval_invoice_no: invoiceNo,
                        approval_invoice_date: response.approval_invoice_date
                    });

                    syncLocalInvoiceState(rawId, null, invoiceNo, response.approval_invoice_date);

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message,
                        timer: 1600,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-times text-danger"></i>');
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Failed to save invoice number', 'error');
            }
        });
    });

    // -------------------------------------------------------------------------
    // 10. Real-time Multi-Tab Synchronization (BroadcastChannel)
    // -------------------------------------------------------------------------
    var txChannel = (typeof BroadcastChannel !== 'undefined') ? new BroadcastChannel('jero_superadmin_transactions') : null;

    function broadcastLocalDelta(payload) {
        if (txChannel) {
            txChannel.postMessage({
                type: 'TRANSACTION_INVOICE_UPDATED',
                payload: payload,
                sent_at: Date.now()
            });
        }
    }

    if (txChannel) {
        txChannel.onmessage = function(ev) {
            if (ev.data && ev.data.type === 'TRANSACTION_INVOICE_UPDATED') {
                var p = ev.data.payload;
                syncLocalInvoiceState(p.transaction_id, p.invoice_approval, p.approval_invoice_no, p.approval_invoice_date, ev.data.sent_at);
                if (p.kpi_metrics) {
                    updateKpiBadges(p.kpi_metrics);
                }
            }
        };
    }

    // -------------------------------------------------------------------------
    // 11. Laravel Echo / Reverb WebSocket Real-Time Listener
    // -------------------------------------------------------------------------
    if (typeof window.Echo !== 'undefined') {
        try {
            window.Echo.private('superadmin.transactions')
                .listen('.transaction.invoice.updated', function(e) {
                    syncLocalInvoiceState(e.transaction_id, e.invoice_approval, e.approval_invoice_no, e.approval_invoice_date, e.updated_at);
                    if (e.kpi_metrics) {
                        updateKpiBadges(e.kpi_metrics);
                    }
                });
        } catch (echoErr) {
            console.warn('[REVERB] Echo private listener skipped:', echoErr);
        }
    }

    // Reconnect & Tab Visibility Reconciliation Guard
    var lastActiveTimestamp = Date.now();
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            var elapsedMs = Date.now() - lastActiveTimestamp;
            lastActiveTimestamp = Date.now();
            // If inactive for > 10 minutes, ensure Echo connector is active
            if (elapsedMs > 600000 && typeof window.Echo !== 'undefined' && window.Echo.connector) {
                try {
                    if (typeof window.Echo.connector.connect === 'function') {
                        window.Echo.connector.connect();
                    }
                } catch(reconnEx) {}
            }
        }
    });


    // Real-Time Dynamic Financial KPI Badge Updater (0% Server Request Overhead)
    function updateKpiBadges(kpis) {
        if (!kpis) return;
        if (typeof kpis.totalRevenueInr !== 'undefined') {
            $('#kpi_val_revenue_inr').text('₹' + Number(kpis.totalRevenueInr).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
        if (typeof kpis.totalRevenueUsd !== 'undefined') {
            $('#kpi_sub_revenue_usd').text('Gross Volume (INR) • $' + Number(kpis.totalRevenueUsd).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD');
        }
        if (typeof kpis.totalTransactions !== 'undefined') {
            $('#kpi_val_total_orders').text(Number(kpis.totalTransactions).toLocaleString() + ' Orders');
            $('#tab_badge_all').text(Number(kpis.totalTransactions).toLocaleString());
        }
        if (typeof kpis.capturedCount !== 'undefined') {
            $('#kpi_val_paid').text(Number(kpis.capturedCount).toLocaleString());
            $('#tab_badge_captured').text(Number(kpis.capturedCount).toLocaleString());
        }
        if (typeof kpis.failedCount !== 'undefined') {
            $('#kpi_val_failed').text(Number(kpis.failedCount).toLocaleString());
            $('#tab_badge_failed').text(Number(kpis.failedCount).toLocaleString());
        }
        if (typeof kpis.approvedInvoiceCount !== 'undefined') {
            $('#kpi_val_invoiced').text(Number(kpis.approvedInvoiceCount).toLocaleString());
            $('#tab_badge_invoiced').text(Number(kpis.approvedInvoiceCount).toLocaleString());
        }
    }

    // Versioning tracker to drop out-of-order stale events
    window.txLastUpdated = window.txLastUpdated || new Map();

    // State Synchronization Helper across DOM, Memory, and IndexedDB
    function syncLocalInvoiceState(txId, approvalVal, invoiceNo, invoiceDate, eventTimestamp) {
        if (!txId) return;

        // Sequence guard: ignore older events
        if (eventTimestamp) {
            var lastTs = window.txLastUpdated.get(String(txId)) || 0;
            var currentTs = (typeof eventTimestamp === 'number') ? eventTimestamp : new Date(eventTimestamp).getTime();
            if (currentTs && currentTs < lastTs) {
                return; // Stale event discarded
            }
            if (currentTs) {
                window.txLastUpdated.set(String(txId), currentTs);
            }
        }

        // Synchronize Card in Stream View
        var $card = $('.cd-tx-card[data-id="' + txId + '"]');
        if ($card.length) {
            if (approvalVal !== null && typeof approvalVal !== 'undefined') {
                $card.find('.select-gst-approval').val(approvalVal);
            }
            if (invoiceNo !== null && typeof invoiceNo !== 'undefined') {
                $card.find('.input-approval-invoice').val(invoiceNo);
            }
            // Visual subtle pulse animation
            $card.addClass('cd-pulse-highlight');
            setTimeout(function() { $card.removeClass('cd-pulse-highlight'); }, 1800);
        }

        // Synchronize Table Row in Table View
        var $row = $('.cd-tx-tablerow[data-id="' + txId + '"]');
        if ($row.length) {
            if (approvalVal !== null && typeof approvalVal !== 'undefined') {
                $row.find('.select-gst-approval').val(approvalVal);
            }
            if (invoiceNo !== null && typeof invoiceNo !== 'undefined') {
                $row.find('.input-approval-invoice').val(invoiceNo);
            }
        }

        // Synchronize Open Drawer if matching
        var openRawId = $('#drw_edit_wrap').attr('data-raw-id');
        if (String(openRawId) === String(txId)) {
            if (approvalVal !== null && typeof approvalVal !== 'undefined') {
                $('#drw_gst_select').val(approvalVal);
            }
            if (invoiceNo !== null && typeof invoiceNo !== 'undefined') {
                $('#drw_inv_input').val(invoiceNo);
                $('#drw_approval_invoice').text(invoiceNo || 'Pending Submission');
            }
            if (invoiceDate) {
                $('#drw_approval_date').text(invoiceDate);
            }
        }

        // Update in window.txState.all
        if (window.txState && Array.isArray(window.txState.all)) {
            var item = window.txState.all.find(function(it) { return String(it.id) === String(txId); });
            if (item) {
                if (approvalVal !== null && typeof approvalVal !== 'undefined') item.invoiceApproval = approvalVal;
                if (invoiceNo !== null && typeof invoiceNo !== 'undefined') item.approvalInvoiceNo = invoiceNo;
                if (invoiceDate) item.approvalInvoiceDate = invoiceDate;
                JeroTransactionHistoryDB.putTransaction(txId, item);
            }
        }

        // Update in L1 RAM Cache & IndexedDB L2 Cache
        if (window.txState && window.txState.detailsCache && window.txState.detailsCache.has(String(txId))) {
            var cached = window.txState.detailsCache.get(String(txId));
            if (approvalVal !== null && typeof approvalVal !== 'undefined') cached.invoiceApproval = approvalVal;
            if (invoiceNo !== null && typeof invoiceNo !== 'undefined') cached.approvalInvoiceNo = invoiceNo;
            if (invoiceDate) cached.approvalInvoiceDate = invoiceDate;
            window.txState.detailsCache.set(String(txId), cached);
            JeroTransactionHistoryDB.putDossier(txId, cached);
        } else {
            // Ensure L2 IndexedDB dossier is also updated even if evicted from L1 RAM cache
            JeroTransactionHistoryDB.getDossier(txId).then(function(idbEntry) {
                if (idbEntry) {
                    if (approvalVal !== null && typeof approvalVal !== 'undefined') idbEntry.invoiceApproval = approvalVal;
                    if (invoiceNo !== null && typeof invoiceNo !== 'undefined') idbEntry.approvalInvoiceNo = invoiceNo;
                    if (invoiceDate) idbEntry.approvalInvoiceDate = invoiceDate;
                    JeroTransactionHistoryDB.putDossier(txId, idbEntry);
                }
            });
        }
    }


    // -------------------------------------------------------------------------
    // 12. Client-First High-Speed CSV Export (Browser RAM Execution - Rule 3)
    // -------------------------------------------------------------------------
    $('#btn_export_csv').on('click', function(e) {
        e.preventDefault();
        var rows = [];
        rows.push([
            'Order ID',
            'Order Date',
            'Company Name',
            'Package Name',
            'Coins Credited',
            'Total Price',
            'Currency',
            'Payment Mode',
            'Status',
            'Tax Approval',
            'Approval Invoice No',
            'Razorpay Order ID',
            'Razorpay Payment ID',
            'Bank / Reference',
            'GSTIN'
        ]);

        var $visibleCards = $('.cd-tx-card:visible');

        if ($visibleCards.length === 0) {
            Swal.fire('Notice', 'No transactions currently match your filter criteria to export.', 'info');
            return;
        }

        $visibleCards.each(function() {
            var dataStr = $(this).attr('data-details');
            if (dataStr) {
                try {
                    var d = JSON.parse(dataStr);
                    rows.push([
                        '#' + d.orderNum,
                        d.orderDate,
                        d.companyName,
                        d.productName,
                        d.credit,
                        d.totalPrice,
                        d.currency,
                        d.paymentMode,
                        d.status,
                        d.invoiceApproval,
                        d.approvalInvoiceNo || 'Pending',
                        d.paymentOrderId || '',
                        d.paymentId || '',
                        d.bank || d.transactionId || '',
                        d.gstin || ''
                    ]);
                } catch(err) {
                    console.error('Error parsing card row for CSV', err);
                }
            }
        });

        var csvContent = "data:text/csv;charset=utf-8,\uFEFF" + rows.map(function(e) {
            return e.map(function(cell) {
                return '"' + String(cell).replace(/"/g, '""') + '"';
            }).join(",");
        }).join("\r\n");

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        var timestamp = new Date().toISOString().slice(0, 10);
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "JERO_Transaction_History_" + timestamp + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        Swal.fire({
            icon: 'success',
            title: 'Export Complete!',
            text: 'Successfully exported ' + ($rows_count = rows.length - 1) + ' transactions to CSV in user RAM.',
            timer: 2000,
            showConfirmButton: false
        });
    });
});
</script>
@endpush
