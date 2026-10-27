@extends('layouts.superadmin_layout')

@section('content')
{{-- ==========================================================================
     Master Enterprise Coin Packages Management
     Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
     Follows UI/UX Standard: Modern Enterprise Card + Sub-Card Design System
     ========================================================================== --}}

<style>
/* ==========================================================================
   Master Enterprise Scoped Styling — Coin Packages Module
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

.cd-coin-module-wrap {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding: 0 16px 30px 16px !important;
    margin-top: 0 !important;
}

.cd-coin-module-wrap * {
    box-sizing: border-box !important;
}

/* Guarantee font icon preservation across all FontAwesome and Metronic icon classes */
.cd-coin-module-wrap i,
.cd-coin-module-wrap [class*="fa-"],
.cd-coin-module-wrap .fa,
.cd-coin-module-wrap .fas,
.cd-coin-module-wrap .far,
.cd-coin-module-wrap .fab,
.cd-coin-module-wrap [class*="ki-"] {
    font-family: "Font Awesome 6 Free", "FontAwesome", "Font Awesome 5 Free", sans-serif !important;
    font-style: normal !important;
    font-weight: 900 !important;
}

/* ==========================================================================
   KPI Summary Metric Cards
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
    padding: 14px 16px !important;
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
.cd-kpi-icon-purple { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-kpi-icon-amber { background: #fffbeb !important; color: #d97706 !important; }
.cd-kpi-icon-green { background: #ecfdf5 !important; color: #059669 !important; }

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
.cd-coin-header-card {
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

/* Currency Switcher Pill */
.cd-currency-toggle {
    display: flex !important;
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 2px !important;
    height: 36px !important;
}

.cd-curr-btn {
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

.cd-curr-btn.active {
    background: #ffffff !important;
    color: #009ef7 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
}

/* Search Sub-Card inside Top Header Card */
.cd-coin-search-subcard {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 36px !important;
    width: 280px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-coin-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-coin-search-subcard i.fa-search {
    color: #94a3b8 !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.cd-coin-search-subcard input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 12px !important;
    color: #0f172a !important;
    width: 100% !important;
    padding: 0 !important;
    font-family: inherit !important;
}

.cd-coin-search-subcard input::placeholder {
    color: #94a3b8 !important;
    font-size: 11.5px !important;
}

.cd-coin-search-clear {
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

.cd-coin-search-clear:hover {
    color: #ef4444 !important;
}

.cd-coin-add-btn {
    height: 36px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 0 14px !important;
    background-color: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
}

.cd-coin-add-btn:hover {
    background-color: #0095e8 !important;
    border-color: #0095e8 !important;
    color: #ffffff !important;
}

/* ==========================================================================
   Tier 2: Sub-Tabs Card with Single Active Primary Blue Highlight
   ========================================================================== */
.cd-coin-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
}

.cd-coin-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
}

.cd-coin-tab-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: all 0.15s ease !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.cd-coin-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Strict Mandate: ONLY the single currently active tab receives vibrant highlight */
.cd-coin-tab-btn.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}

.cd-coin-tab-badge {
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
}

.cd-coin-tab-btn.active .cd-coin-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

/* Semantic Inactive Badges */
.badge-operational { background: #eff6ff !important; color: #2563eb !important; }
.badge-sales { background: #fffbeb !important; color: #b45309 !important; }
.badge-active { background: #ecfdf5 !important; color: #059669 !important; }
.badge-inactive { background: #fef2f2 !important; color: #dc2626 !important; }
.badge-custom { background: #f5f3ff !important; color: #7c3aed !important; }

/* ==========================================================================
   Tier 3: Master Primary Card & Discrete Sub-Cards Stream
   ========================================================================== */
.cd-coin-stream {
    width: 100% !important;
}

.cd-coin-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.2s ease !important;
    position: relative !important;
    overflow: hidden !important;
}

.cd-coin-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* Tier Themes — subtle left accent border */
.cd-coin-card.tier-gold { border-left: 3px solid #f59e0b !important; }
.cd-coin-card.tier-silver { border-left: 3px solid #64748b !important; }
.cd-coin-card.tier-platinum { border-left: 3px solid #6366f1 !important; }
.cd-coin-card.tier-free { border-left: 3px solid #10b981 !important; }
.cd-coin-card.tier-custom { border-left: 3px solid #0d9488 !important; }

/* Discrete Sub-Cards CSS Grid (Fractional 15.6" Screen Fit) */
.cd-coin-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.8fr) minmax(0, 1.9fr) minmax(0, 1.6fr) minmax(0, 2.0fr) !important;
    gap: 10px !important;
    padding: 12px 16px !important;
    width: 100% !important;
    align-items: stretch !important;
}

@media (max-width: 1200px) {
    .cd-coin-card-grid {
        grid-template-columns: minmax(0, 2.5fr) minmax(0, 2fr) minmax(0, 2fr) !important;
    }
}

@media (max-width: 768px) {
    .cd-coin-card-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Common Sub-Card Box */
.cd-subcard {
    background: #f8fafc !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    min-width: 0 !important;
}

.cd-subcard-label {
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 6px !important;
}

/* Sub-Card 1: Identity with Clickable Edit Title */
.cd-subcard-identity {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
}

.cd-identity-header {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
}

.cd-pkg-icon-box {
    width: 40px !important;
    height: 40px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 17px !important;
    flex-shrink: 0 !important;
}

.tier-gold .cd-pkg-icon-box { background: #fffbeb !important; color: #d97706 !important; }
.tier-silver .cd-pkg-icon-box { background: #f1f5f9 !important; color: #475569 !important; }
.tier-platinum .cd-pkg-icon-box { background: #eef2ff !important; color: #4f46e5 !important; }
.tier-free .cd-pkg-icon-box { background: #ecfdf5 !important; color: #059669 !important; }
.tier-custom .cd-pkg-icon-box { background: #f0fdfa !important; color: #0d9488 !important; }

.cd-identity-meta {
    min-width: 0 !important;
    flex: 1 !important;
}

.cd-pkg-title-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
}

/* Clickable Package Title Link */
.cd-pkg-title-link {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    transition: color 0.15s ease !important;
    cursor: pointer !important;
}

.cd-pkg-title-link:hover {
    color: #009ef7 !important;
    text-decoration: underline !important;
}

.cd-title-edit-icon {
    font-size: 11px !important;
    color: #009ef7 !important;
    opacity: 0.75 !important;
    transition: transform 0.15s ease !important;
}

.cd-pkg-title-link:hover .cd-title-edit-icon {
    opacity: 1 !important;
    transform: scale(1.15) !important;
}

.cd-pkg-code {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    background: #eff6ff !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
}

.cd-identity-pills {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 5px !important;
    flex-wrap: wrap !important;
}

.cd-pill {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-pill-operational { background: #eff6ff !important; color: #1d4ed8 !important; }
.cd-pill-sales { background: #fffbeb !important; color: #b45309 !important; }
.cd-pill-standard { background: #f1f5f9 !important; color: #475569 !important; }
.cd-pill-custom { background: #f5f3ff !important; color: #6d28d9 !important; }
.cd-pill-free { background: #ecfdf5 !important; color: #047857 !important; }

/* Dedicated Quick Edit Pill */
.cd-pill-edit-action {
    background: #e0f2fe !important;
    color: #0284c7 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-pill-edit-action:hover {
    background: #009ef7 !important;
    color: #ffffff !important;
}

/* Sub-Card 2: Coins */
.cd-coins-display {
    display: flex !important;
    align-items: baseline !important;
    gap: 5px !important;
}

.cd-coin-amount {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.cd-coin-unit {
    font-size: 11px !important;
    color: #64748b !important;
    font-weight: 600 !important;
}

.cd-unit-rates {
    margin-top: 4px !important;
}

.cd-rate-item {
    font-size: 11px !important;
    color: #475569 !important;
    display: flex !important;
    align-items: baseline !important;
    gap: 3px !important;
}

.cd-rate-label {
    color: #64748b !important;
    font-size: 10px !important;
}

.cd-rate-val {
    font-weight: 600 !important;
    color: #0f172a !important;
}

.cd-rate-sub {
    font-size: 9.5px !important;
    color: #94a3b8 !important;
}

/* Sub-Card 3: Pricing */
.cd-main-price {
    display: flex !important;
    align-items: baseline !important;
    gap: 4px !important;
}

.cd-currency-sign {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
}

.cd-price-number {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.cd-billing-term {
    font-size: 10.5px !important;
    color: #64748b !important;
    font-weight: 500 !important;
}

.cd-discount-badge {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    background: #fef2f2 !important;
    color: #dc2626 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    border: 1px solid #fee2e2 !important;
}

.cd-secondary-rates {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 3px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-weight: 500 !important;
}

.cd-rate-dot {
    color: #cbd5e1 !important;
}

/* Currency Switch visibility control */
.show-usd .cd-curr-inr { display: none !important; }
.show-usd .cd-curr-usd { display: block !important; }
.show-inr .cd-curr-inr { display: block !important; }
.show-inr .cd-curr-usd { display: none !important; }

/* Sub-Card 4: Duration & Visibility */
.cd-duration-row {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 12px !important;
    color: #0f172a !important;
}

.cd-duration-icon {
    color: #009ef7 !important;
    font-size: 13px !important;
}

.cd-duration-approx {
    font-size: 11px !important;
    color: #64748b !important;
}

.cd-visibility-row {
    margin-top: 6px !important;
}

.cd-vis-pill {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-vis-yearly { background: #ecfdf5 !important; color: #047857 !important; }
.cd-vis-monthly { background: #eff6ff !important; color: #1d4ed8 !important; }
.cd-vis-daily { background: #f8fafc !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }

/* Sub-Card 5: Feature Access & Points */
.cd-features-header {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
}

.cd-features-preview {
    margin-top: 4px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
}

.cd-feature-pill {
    font-size: 10.5px !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.cd-feature-pill.is-disabled {
    color: #94a3b8 !important;
    text-decoration: line-through !important;
}

.cd-btn-view-points {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    margin-top: 4px !important;
    text-align: left !important;
    text-decoration: none !important;
}

.cd-btn-view-points:hover {
    color: #0084cc !important;
    text-decoration: underline !important;
}

/* ==========================================================================
   Sub-Card 6: Bottom Activity Strip with Prominent Actions
   ========================================================================== */
.cd-coin-card-bottom {
    background: #ffffff !important;
    border-top: 1px solid #f1f5f9 !important;
    padding: 8px 16px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
}

.cd-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 11px !important;
    color: #64748b !important;
}

.cd-meta-dot {
    color: #cbd5e1 !important;
}

.cd-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.cd-status-control {
    display: flex !important;
    align-items: center !important;
}

.cd-status-text {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    margin-bottom: 0 !important;
}

.cd-action-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

/* Prominent Action Button Pills with Text and Icons */
.cd-action-btn-pill {
    height: 30px !important;
    font-size: 11.5px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 0 10px !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

/* Empty State Card */
.cd-empty-card {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 40px 20px !important;
    text-align: center !important;
    margin-bottom: 20px !important;
}

.cd-empty-icon {
    width: 54px !important;
    height: 54px !important;
    border-radius: 50% !important;
    background: #eff6ff !important;
    color: #009ef7 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 24px !important;
    margin-bottom: 12px !important;
}

.cd-empty-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin-bottom: 4px !important;
}

.cd-empty-text {
    font-size: 12px !important;
    color: #64748b !important;
    margin-bottom: 16px !important;
}

.cd-pagination-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    margin-top: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>

<div class="cd-coin-module-wrap show-inr" id="cdCoinModuleWrap">

    {{-- KPI Metric Overview Strip (Top redundant section removed per user request) --}}
    <div class="cd-kpi-grid">
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-blue">
                <i class="fa fa-layer-group"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpiCountTotal">{{ $kpiTotal }}</div>
                <div class="cd-kpi-title">Total Configured Plans</div>
            </div>
        </div>
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-purple">
                <i class="fa fa-cogs"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpiCountOperational">{{ $kpiOperational }}</div>
                <div class="cd-kpi-title">ATS Operational Plans</div>
            </div>
        </div>
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-amber">
                <i class="fa fa-bullhorn"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpiCountSales">{{ $kpiSales }}</div>
                <div class="cd-kpi-title">Sales & Outreach Plans</div>
            </div>
        </div>
        <div class="cd-kpi-card">
            <div class="cd-kpi-icon cd-kpi-icon-green">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="cd-kpi-content">
                <div class="cd-kpi-val" id="kpiCountActive">{{ $kpiActive }} <span class="text-muted fs-7 fw-normal">/ {{ $kpiTotal }}</span></div>
                <div class="cd-kpi-title">Active for Purchase</div>
            </div>
        </div>
    </div>

    {{-- Tier 1: Top Header Toolbar Card with Dynamic Search & Currency Switcher --}}
    <div class="cd-coin-header-card">
        <div class="cd-header-left">
            <div class="cd-header-icon">
                <i class="fa fa-coins"></i>
            </div>
            <div>
                <div class="cd-header-title">Package Catalog & Pricing</div>
                <div class="cd-header-sub">Configure platform coins allocation, subscription validity, and modular access permissions.</div>
            </div>
        </div>
        <div class="cd-header-right">
            {{-- Currency Switcher Pill --}}
            <div class="cd-currency-toggle" title="Switch displayed currency">
                <button type="button" class="cd-curr-btn active" data-currency="inr">
                    <span>₹ INR</span>
                </button>
                <button type="button" class="cd-curr-btn" data-currency="usd">
                    <span>$ USD</span>
                </button>
            </div>

            {{-- 300ms Debounced Dynamic Search Sub-Card --}}
            <div class="cd-coin-search-subcard">
                <i class="fa fa-search"></i>
                <input type="text" id="inputCoinSearch" placeholder="Search package, coins, price..." value="{{ $currentSearch ?? '' }}" autocomplete="off">
                <button type="button" class="cd-coin-search-clear" id="btnCoinSearchClear" title="Clear search">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            {{-- Primary Action --}}
            <a href="{{ route('superadmin.company.addEditcoinPackages') }}" class="btn cd-coin-add-btn">
                <i class="fa fa-plus"></i> Add Package
            </a>
        </div>
    </div>

    {{-- Tier 2: Sub-Tabs Card with Single Active Primary Blue Highlight --}}
    <div class="cd-coin-tabs-card">
        <div class="cd-coin-tabs-group">
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'all' ? 'active' : '' }}" data-tab="all">
                <span>All Packages</span>
                <span class="cd-coin-tab-badge" id="tabCountAll">{{ $kpiTotal }}</span>
            </button>
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'operational' ? 'active' : '' }}" data-tab="operational">
                <span>Operational</span>
                <span class="cd-coin-tab-badge badge-operational" id="tabCountOperational">{{ $kpiOperational }}</span>
            </button>
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'sales' ? 'active' : '' }}" data-tab="sales">
                <span>Sales</span>
                <span class="cd-coin-tab-badge badge-sales" id="tabCountSales">{{ $kpiSales }}</span>
            </button>
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'active' ? 'active' : '' }}" data-tab="active">
                <span>Active</span>
                <span class="cd-coin-tab-badge badge-active" id="tabCountActive">{{ $kpiActive }}</span>
            </button>
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'inactive' ? 'active' : '' }}" data-tab="inactive">
                <span>Inactive</span>
                <span class="cd-coin-tab-badge badge-inactive" id="tabCountInactive">{{ $kpiInactive }}</span>
            </button>
            <button type="button" class="cd-coin-tab-btn js-tab-filter {{ $currentTab === 'custom' ? 'active' : '' }}" data-tab="custom">
                <span>Custom / Free</span>
                <span class="cd-coin-tab-badge badge-custom" id="tabCountCustom">{{ $kpiCustom }}</span>
            </button>
        </div>

        <div class="d-none d-lg-flex align-items-center text-muted fs-8">
            <i class="fa fa-info-circle text-primary me-1"></i> Changes synchronize to Elasticsearch in real-time
        </div>
    </div>

    {{-- Tier 3: Primary Entity Cards Stream Container --}}
    <div class="cd-coin-stream" id="coinPackagesContainer">
        @include('super-admin.coin-settings.partials.package-cards')
    </div>

</div>

{{-- Feature Points Modal --}}
<div class="modal fade" id="modalPackageFeatures" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="symbol symbol-30px bg-primary rounded">
                        <i class="fa fa-list-check text-white p-2"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fs-6 fw-bold mb-0" id="modalPkgTitle">Package Features</h5>
                        <span class="text-muted fs-8" id="modalPkgSub">Included feature access and limits</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-4" id="modalPkgBody">
                {{-- Populated via JS --}}
            </div>
            <div class="modal-footer py-2 px-4 bg-light">
                <button type="button" class="btn btn-sm btn-light fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
/**
 * Isolated Coin Packages JavaScript Controller
 * Strict Page-Level Scope (Rule 10 & 17)
 */
(function($) {
    'use strict';

    var currentTab = '{{ $currentTab ?? "all" }}';
    var searchTimer = null;
    var currentCurrency = localStorage.getItem('jero_coin_curr_pref') || 'inr';

    // Apply currency view preference
    function applyCurrency(curr) {
        currentCurrency = curr;
        localStorage.setItem('jero_coin_curr_pref', curr);
        if (curr === 'usd') {
            $('#cdCoinModuleWrap').removeClass('show-inr').addClass('show-usd');
            $('.cd-curr-btn[data-currency="usd"]').addClass('active');
            $('.cd-curr-btn[data-currency="inr"]').removeClass('active');
        } else {
            $('#cdCoinModuleWrap').removeClass('show-usd').addClass('show-inr');
            $('.cd-curr-btn[data-currency="inr"]').addClass('active');
            $('.cd-curr-btn[data-currency="usd"]').removeClass('active');
        }
    }
    applyCurrency(currentCurrency);

    // Currency Switcher Event
    $(document).on('click', '.cd-curr-btn', function() {
        var curr = $(this).data('currency');
        applyCurrency(curr);
    });

    // Check search clear button visibility on load
    if ($('#inputCoinSearch').val().length > 0) {
        $('#btnCoinSearchClear').show();
    }

    // Dynamic Live Filter Request
    function fetchPackages(page) {
        var searchVal = $('#inputCoinSearch').val();
        page = page || 1;

        $('#coinPackagesContainer').css('opacity', '0.5');

        $.ajax({
            url: "{{ route('superadmin.company.coinPackages') }}",
            type: 'GET',
            data: {
                tab: currentTab,
                search: searchVal,
                page: page
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $('#coinPackagesContainer').css('opacity', '1');
                if (res.status === 'success') {
                    $('#coinPackagesContainer').html(res.html);

                    // Update sub-tab badge counts
                    if (res.counts) {
                        $('#tabCountAll').text(res.counts.all);
                        $('#tabCountOperational').text(res.counts.operational);
                        $('#tabCountSales').text(res.counts.sales);
                        $('#tabCountActive').text(res.counts.active);
                        $('#tabCountInactive').text(res.counts.inactive);
                        $('#tabCountCustom').text(res.counts.custom);
                        $('#kpiCountTotal').text(res.counts.all);
                    }
                }
            },
            error: function() {
                $('#coinPackagesContainer').css('opacity', '1');
            }
        });
    }

    // 300ms Debounced Dynamic Search
    $(document).on('keyup', '#inputCoinSearch', function() {
        clearTimeout(searchTimer);
        var val = $(this).val();

        if (val.length > 0) {
            $('#btnCoinSearchClear').show();
        } else {
            $('#btnCoinSearchClear').hide();
        }

        searchTimer = setTimeout(function() {
            fetchPackages(1);
        }, 300);
    });

    // Clear Search Input Button
    $(document).on('click', '#btnCoinSearchClear', function() {
        $('#inputCoinSearch').val('');
        $(this).hide();
        fetchPackages(1);
    });

    // Sub-Tab Switching (Single Active Highlight Standard)
    $(document).on('click', '.js-tab-filter', function(e) {
        e.preventDefault();
        var $btn = $(this);
        currentTab = $btn.data('tab') || 'all';

        $('.js-tab-filter').removeClass('active');
        $btn.addClass('active');

        fetchPackages(1);
    });

    // Reset Filters from Empty State
    $(document).on('click', '.js-btn-reset-filters', function(e) {
        e.preventDefault();
        $('#inputCoinSearch').val('');
        $('#btnCoinSearchClear').hide();
        currentTab = 'all';
        $('.js-tab-filter').removeClass('active');
        $('.js-tab-filter[data-tab="all"]').addClass('active');
        fetchPackages(1);
    });

    // AJAX Pagination Link Clicking
    $(document).on('click', '.cd-pagination-card a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (href) {
            var url = new URL(href, window.location.origin);
            var page = url.searchParams.get('page') || 1;
            fetchPackages(page);
        }
    });

    // Quick Status Toggle Switch
    $(document).on('change', '.js-toggle-pkg-status', function() {
        var $switch = $(this);
        var pkgId = $switch.data('id');
        var $card = $('#pkg-card-' + pkgId);
        var $statusLabel = $card.find('.cd-status-text');

        $.ajax({
            url: "{{ route('superadmin.company.coinPackageToggleStatus') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: pkgId
            },
            success: function(res) {
                if (res.status === 'success') {
                    if (res.new_status === 1) {
                        $switch.prop('checked', true);
                        $statusLabel.removeClass('text-danger').addClass('text-success').text('Active');
                        $card.attr('data-status', 1);
                    } else {
                        $switch.prop('checked', false);
                        $statusLabel.removeClass('text-success').addClass('text-danger').text('Inactive');
                        $card.attr('data-status', 0);
                    }

                    // Show lightweight toast
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    }
                } else {
                    // Rollback switch state
                    $switch.prop('checked', !$switch.prop('checked'));
                    alert(res.message || 'Failed to update status.');
                }
            },
            error: function() {
                // Rollback switch state
                $switch.prop('checked', !$switch.prop('checked'));
                alert('Connection error occurred. Status could not be updated.');
            }
        });
    });

    // Delete Package with Confirmation
    $(document).on('click', '.js-btn-delete-pkg', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var pkgId = $btn.data('id');
        var pkgName = $btn.data('name') || 'this package';

        var confirmDelete = function() {
            $.ajax({
                url: "{{ route('superadmin.company.coinPackageDestroy') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: pkgId
                },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#pkg-card-' + pkgId).slideUp(300, function() {
                            $(this).remove();
                            fetchPackages(1);
                        });

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: res.message,
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary btn-sm'
                                }
                            });
                        }
                    } else {
                        alert(res.message || 'Failed to delete package.');
                    }
                },
                error: function() {
                    alert('Connection error. Could not delete package.');
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Coin Package?',
                text: 'Are you sure you want to delete "' + pkgName + '"? Associated feature points will also be removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger btn-sm',
                    cancelButton: 'btn btn-light btn-sm'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    confirmDelete();
                }
            });
        } else if (confirm('Are you sure you want to delete package "' + pkgName + '"?')) {
            confirmDelete();
        }
    });

    // View All Points Modal Viewer
    $(document).on('click', '.js-btn-view-points', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var pkgName = $btn.data('pkg-name');
        var points = $btn.data('points') || [];

        $('#modalPkgTitle').text(pkgName + ' — Included Features');
        $('#modalPkgSub').text(points.length + ' feature points mapped to this tier');

        var html = '<div class="d-flex flex-column gap-2">';
        if (points.length === 0) {
            html += '<div class="text-center text-muted py-4">No specific feature points configured for this plan.</div>';
        } else {
            for (var i = 0; i < points.length; i++) {
                var pt = points[i];
                var isEnabled = (pt.status == 1);
                html += '<div class="d-flex align-items-center justify-content-between p-2 rounded ' + (isEnabled ? 'bg-light-success' : 'bg-light') + '">';
                html += '  <div class="d-flex align-items-center gap-2">';
                html += '    <i class="fa ' + (isEnabled ? 'fa-check-circle text-success' : 'fa-times-circle text-muted') + ' fs-6"></i>';
                html += '    <span class="fs-7 fw-semibold ' + (isEnabled ? 'text-gray-800' : 'text-muted text-decoration-line-through') + '">' + $('<div>').text(pt.title).html() + '</span>';
                html += '  </div>';
                html += '  <span class="badge ' + (isEnabled ? 'badge-light-success text-success' : 'badge-light text-muted') + ' fs-9">' + (isEnabled ? 'Active' : 'Disabled') + '</span>';
                html += '</div>';
            }
        }
        html += '</div>';

        $('#modalPkgBody').html(html);
        $('#modalPackageFeatures').modal('show');
    });

})(jQuery);
</script>
@endpush