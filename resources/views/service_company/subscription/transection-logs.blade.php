@extends('layouts.service_company_layout')

@push('head-script')
{{--
  JERO ATS — Service Company Coins Activity Ledger (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Replaces legacy flat timeline table with 3-Tier Enterprise Card + Sub-Card layout:
          Tier 1 Pure White Toolbar Card with debounced Search + Clear,
          Tier 2 Pure White Sub-Tabs Card (Operational Coins / Sales Coins) + Date/Type Filter Strip,
          Tier 3 Primary Card Stream with 4 KPI Telemetry Sub-Cards + 6-Part Coin Activity Cards.
  Tenancy & Business Logic: Preserves 100% of transectionLogData AJAX endpoint, URL query deep-linking,
          LRU RAM cache, AbortController, Reverb real-time sync, and window.JWTransactionLog API.
--}}
<style>
body {
    overflow-x: hidden;
}

/* ── ISOLATED ROOT SCOPE: .txact-wrapper ── */
.txact-wrapper {
    width: 100%;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #0f172a;
    box-sizing: border-box;
    padding: 4px 0 24px;
}
.txact-wrapper *, .txact-wrapper *::before, .txact-wrapper *::after {
    box-sizing: border-box;
}
.txact-wrapper i, .txact-wrapper [class*="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    line-height: 1 !important;
}
.txact-wrapper .fas, .txact-wrapper .fa-solid { font-weight: 900 !important; }
.txact-wrapper .far, .txact-wrapper .fa-regular { font-weight: 400 !important; }
.txact-wrapper .txact-num { font-variant-numeric: tabular-nums; }

/* ── SEMANTIC ICON & BADGE TOKENS ── */
.txact-icon-blue { background: #eff6ff !important; color: #009ef7 !important; border: 1px solid #dbeafe !important; }
.txact-icon-blue i { color: #009ef7 !important; }
.txact-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.txact-icon-emerald i { color: #059669 !important; }
.txact-icon-rose { background: #fef2f2 !important; color: #e11d48 !important; border: 1px solid #fecdd3 !important; }
.txact-icon-rose i { color: #e11d48 !important; }
.txact-icon-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
.txact-icon-purple i { color: #7c3aed !important; }
.txact-icon-amber { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }
.txact-icon-amber i { color: #d97706 !important; }

.txact-badge-blue { background: #eff6ff; color: #0284c7; border: 1px solid #bae6fd; }
.txact-badge-blue i { color: #0284c7 !important; }
.txact-badge-purple { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
.txact-badge-purple i { color: #6d28d9 !important; }
.txact-badge-emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.txact-badge-emerald i { color: #059669 !important; }

/* ── TIER 1: PURE WHITE TOOLBAR HEADER CARD ── */
.txact-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    flex-wrap: wrap;
}
.txact-toolbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}
.txact-toolbar-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    color: #009ef7;
    flex-shrink: 0;
}
.txact-toolbar-icon-box i { color: #009ef7 !important; }
.txact-toolbar-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.txact-toolbar-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.25;
}
.txact-count-pill {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.txact-toolbar-sub {
    font-size: 12px;
    color: #64748b;
    margin: 3px 0 0 0;
    line-height: 1.35;
}
.txact-toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.txact-search-box {
    position: relative;
    width: 265px;
    max-width: 100%;
}
.txact-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12.5px;
    color: #94a3b8 !important;
    pointer-events: none;
}
.txact-search-input {
    width: 100%;
    height: 36px;
    padding: 0 32px 0 34px;
    font-size: 12px;
    font-family: inherit;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: all 0.2s ease;
}
.txact-search-input:focus {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.txact-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #94a3b8;
    background: none;
    border: none;
    cursor: pointer;
    display: none;
    padding: 3px;
}
.txact-search-clear:hover i { color: #ef4444 !important; }

.txact-btn-primary {
    height: 36px;
    padding: 0 15px;
    background: #009ef7;
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none !important;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
    white-space: nowrap;
}
.txact-btn-primary:hover { background: #0086d4; box-shadow: 0 4px 8px rgba(0, 158, 247, 0.28); }
.txact-btn-primary i { color: #ffffff !important; font-size: 12px; }

.txact-btn-outline {
    height: 36px;
    padding: 0 13px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155 !important;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.txact-btn-outline:hover {
    background: #f8fafc;
    border-color: #009ef7;
    color: #009ef7 !important;
}
.txact-btn-outline i { color: #64748b !important; font-size: 12px; }
.txact-btn-outline:hover i { color: #009ef7 !important; }

/* ── TIER 2: PURE WHITE SUB-TABS & FILTER CARD ── */
.txact-subtabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.txact-subtabs-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
}
.txact-subtabs-nav {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.txact-subtab-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #475569 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none !important;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.txact-subtab-pill i { color: #64748b !important; font-size: 12.5px; }
.txact-subtab-pill:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a !important;
}
.txact-subtab-pill.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
}
.txact-subtab-pill.active i { color: #ffffff !important; }

.txact-header-telemetry {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.txact-mini-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 5px 10px;
}
.txact-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.txact-dot-credit { background: #10b981; }
.txact-dot-debit { background: #e11d48; }
.txact-dot-total { background: #009ef7; }

/* Filter sub-strip */
.txact-filter-strip {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    padding-top: 10px;
}
.txact-filter-controls {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}
.txact-filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.txact-filter-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.45px;
    text-transform: uppercase;
}
.txact-input, .txact-select {
    height: 34px;
    padding: 0 10px;
    font-size: 12px;
    font-family: inherit;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    outline: none;
    transition: all 0.15s ease;
}
.txact-input:focus, .txact-select:focus {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.txact-preset-chips {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.txlog-chip {
    height: 34px;
    padding: 0 11px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.txlog-chip:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}
.txlog-chip.active-chip {
    background: #f1faff;
    border-color: rgba(0, 158, 247, 0.4);
    color: #009ef7;
}
.btn-txreset {
    height: 34px;
    padding: 0 13px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.btn-txreset:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}
.btn-txreset i { font-size: 11px; }

/* ── TIER 3: PRIMARY CARDS STREAM ── */
.txact-stream {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.txact-primary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.txact-primary-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
}
.txact-card-header {
    padding: 13px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.txact-card-header-left {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
}
.txact-cat-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.txact-card-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
.txact-card-desc { font-size: 11.5px; color: #64748b; margin: 2px 0 0 0; }
.txact-card-body { padding: 14px 18px; }

/* ── KPI TELEMETRY SUB-CARDS ── */
.txact-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}
.txact-kpi-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.txact-kpi-val {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.txact-kpi-val.is-credit { color: #059669; }
.txact-kpi-val.is-debit { color: #e11d48; }
.txact-kpi-val.is-blue { color: #009ef7; }
.txact-kpi-label {
    font-size: 11px;
    font-weight: 500;
    color: #64748b;
    margin-top: 2px;
}
.txact-kpi-mini-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}

/* ── LIVE NOTICE BANNER ── */
.txact-live-banner {
    background: #eff6ff;
    border: 1px solid #bae6fd;
    border-radius: 9px;
    padding: 10px 16px;
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.txact-live-pulse {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #009ef7;
    display: inline-block;
    animation: txPulse 1.5s infinite;
}
@keyframes txPulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}

/* ── LEDGER ENTRY CARDS + DISCRETE SUB-CARDS ── */
.txact-entries-stream {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 180px;
}
.txact-entry-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 12px 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    animation: txfadeIn 0.2s ease;
}
.txact-entry-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
}
.txact-entry-credit { border-left-color: #10b981; }
.txact-entry-debit { border-left-color: #e11d48; }
.txact-entry-settle { border-left-color: #f59e0b; }

@keyframes txfadeIn {
    from { opacity: 0; transform: translateY(3px); }
    to { opacity: 1; transform: translateY(0); }
}

.txact-entry-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 2.2fr) minmax(0, 1.25fr) minmax(0, 1fr) minmax(0, 1fr);
    gap: 10px;
    align-items: stretch;
}
.txact-entry-subcard {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 7px;
    padding: 9px 11px;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.txact-micro-label {
    display: block;
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.45px;
    color: #94a3b8;
    text-transform: uppercase;
    margin-bottom: 3px;
}
.txact-cell-main {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    word-break: break-word;
}
.txact-cell-sub {
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.txact-cell-sub i { color: #94a3b8 !important; font-size: 10.5px; margin-right: 3px; }

.txact-event-badges {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
    flex-wrap: wrap;
}
.tx-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.tx-badge.credit { background: #dcfce7; color: #15803d; }
.tx-badge.debit { background: #ffe4e6; color: #be123c; }
.tx-badge.settle { background: #fef3c7; color: #b45309; }
.tx-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

.txact-delta-credit { font-size: 14px; font-weight: 800; color: #059669; }
.txact-delta-debit { font-size: 14px; font-weight: 800; color: #e11d48; }
.txact-bal-val { font-size: 14px; font-weight: 800; color: #009ef7; }

.txact-entry-bottom {
    margin-top: 9px;
    padding-top: 8px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 11px;
    color: #64748b;
}
.txact-bottom-left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.txact-bottom-left i { color: #94a3b8 !important; font-size: 11px; margin-right: 3px; }
.txact-bottom-dot { color: #cbd5e1; }
.txact-verified-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10.5px;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 2px 8px;
}
.txact-verified-tag i { color: #10b981 !important; font-size: 10.5px; }

/* ── LOADING, EMPTY & PAGINATION ── */
.txlog-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 46px 0;
    gap: 10px;
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
}
.txlog-spinner {
    width: 22px;
    height: 22px;
    border: 2.5px solid #e2e8f0;
    border-top-color: #009ef7;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.txact-empty-subcard {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 9px;
    padding: 40px 20px;
    text-align: center;
}
.txact-empty-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    margin-bottom: 10px;
}
.txact-empty-icon i { color: #009ef7 !important; }
.txact-empty-title { font-size: 14px; font-weight: 700; color: #0f172a; }
.txact-empty-desc { font-size: 12px; color: #64748b; margin-top: 4px; }

.txlog-pagination {
    display: flex;
    gap: 6px;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
.txlog-pagination .pg-info {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
}
.txlog-pagination .page-link {
    padding: 0 10px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    background: #ffffff;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 30px;
}
.txlog-pagination .page-link:hover { background: #eff6ff; border-color: #bae6fd; color: #009ef7; }
.txlog-pagination .page-link.active { background: #009ef7; color: #ffffff; border-color: #009ef7; }
.txlog-pagination .page-link.disabled { opacity: 0.4; pointer-events: none; background: #f8fafc; }

/* ── RESPONSIVE BREAKPOINTS (15.6" 1366x768 & 1920x1080 + MOBILE) ── */
@media (max-width: 1366px) {
    .txact-kpi-val { font-size: 15.5px; }
}
@media (max-width: 1199px) {
    .txact-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .txact-entry-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 767px) {
    .txact-toolbar-card { padding: 12px 14px; }
    .txact-toolbar-right { width: 100%; }
    .txact-search-box { width: 100%; }
    .txact-kpi-grid, .txact-entry-grid { grid-template-columns: minmax(0, 1fr); }
}
</style>
@endpush

@section('content')

{{-- Metronic Toolbar --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-2 mb-lg-0">
            <div class="d-flex flex-column">
                <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                    <span class="text-gray-800 mb-0">Coins Activity</span>
                </h5>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-4">
            <div class="txact-wrapper coins-activity-card-system">

                {{-- ══════════════════════════════════════════════════════════════
                     TIER 1: PURE WHITE ENTERPRISE TOOLBAR HEADER CARD
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="txact-toolbar-card tier1-toolbar-card">
                    <div class="txact-toolbar-left">
                        <div class="txact-toolbar-icon-box">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <div class="txact-toolbar-title-row">
                                <h1 class="txact-toolbar-title">Coins Activity Ledger</h1>
                                <span class="txact-count-pill txact-badge-purple" id="activeLedgerBadge">
                                    <i class="fas fa-cogs"></i> Operational Coins
                                </span>
                            </div>
                            <p class="txact-toolbar-sub">Track operational and sales coin credits, debits, settlements, and running wallet balances in real time.</p>
                        </div>
                    </div>

                    <div class="txact-toolbar-right">
                        <div class="txact-search-box">
                            <i class="fas fa-search txact-search-icon"></i>
                            <input type="text" id="txlog-search" class="txact-search-input"
                                placeholder="Search event or user name..." autocomplete="off">
                            <button type="button" id="txlog-search-clear" class="txact-search-clear" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <a href="{{ route('service_company.subscribe.newindex') }}#tab=overview" class="txact-btn-outline">
                            <i class="fas fa-wallet"></i> Wallet Center
                        </a>

                        <a href="{{ url('service_company/subscribe') }}" class="txact-btn-primary">
                            <i class="fas fa-table"></i> View Coin Usage Table
                        </a>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     TIER 2: PURE WHITE SUB-TABS & FILTER CONTROLS CARD
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="txact-subtabs-card tier2-subtabs-card">
                    <div class="txact-subtabs-top">
                        <div class="txact-subtabs-nav" role="tablist">
                            <a class="txact-subtab-pill txlog-tab active" href="#" data-pagetype="operationaltab" id="tab-operational">
                                <i class="fas fa-cogs"></i>
                                <span>Operational Coins</span>
                            </a>
                            <a class="txact-subtab-pill txlog-tab" href="#" data-pagetype="salestab" id="tab-sales">
                                <i class="fas fa-briefcase"></i>
                                <span>Sales Coins</span>
                            </a>
                        </div>

                        <div class="txact-header-telemetry" id="txlog-header-stats">
                            <div class="txact-mini-stat-chip">
                                <span class="txact-dot txact-dot-credit"></span>
                                <span>Credit: <strong id="stat-credit" class="text-success txact-num">—</strong></span>
                            </div>
                            <div class="txact-mini-stat-chip">
                                <span class="txact-dot txact-dot-debit"></span>
                                <span>Debit: <strong id="stat-debit" class="text-danger txact-num">—</strong></span>
                            </div>
                            <div class="txact-mini-stat-chip">
                                <span class="txact-dot txact-dot-total"></span>
                                <span>Total: <strong id="stat-total" class="text-primary txact-num">—</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="txact-filter-strip">
                        <div class="txact-filter-controls">
                            <div class="txact-filter-group">
                                <label class="txact-filter-label" for="txlog-date-from">From Date</label>
                                <input type="date" id="txlog-date-from" class="txact-input" style="width:142px;">
                            </div>

                            <div class="txact-filter-group">
                                <label class="txact-filter-label" for="txlog-date-to">To Date</label>
                                <input type="date" id="txlog-date-to" class="txact-input" style="width:142px;">
                            </div>

                            <div class="txact-filter-group">
                                <span class="txact-filter-label">Quick Range</span>
                                <div class="txact-preset-chips">
                                    <button type="button" class="txlog-chip active-chip" data-preset="this_month">This Month</button>
                                    <button type="button" class="txlog-chip" data-preset="last_month">Last Month</button>
                                    <button type="button" class="txlog-chip" data-preset="last_7">7 Days</button>
                                    <button type="button" class="txlog-chip" data-preset="all">All Time</button>
                                </div>
                            </div>

                            <div class="txact-filter-group">
                                <label class="txact-filter-label" for="txlog-type">Type</label>
                                <select id="txlog-type" class="txact-select" style="width:122px;">
                                    <option value="">All Types</option>
                                    <option value="credit">Credit</option>
                                    <option value="debit">Debit</option>
                                </select>
                            </div>

                            <div class="txact-filter-group">
                                <label class="txact-filter-label" for="txlog-per-page">Rows</label>
                                <select id="txlog-per-page" class="txact-select" style="width:82px;">
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="txlog-reset" class="btn-txreset">
                                <i class="fas fa-undo"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     TIER 3: PRIMARY CARD STREAM
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="txact-stream tier3-cards-stream">

                    {{-- Live Update Notification Banner (Phase 4: Reverb Real-Time Sync) --}}
                    <div id="txlog-live-notice" class="txact-live-banner">
                        <div class="d-flex align-items-center gap-2">
                            <span class="txact-live-pulse"></span>
                            <span class="fs-7 fw-semibold text-gray-800">New coin transactions were recorded on your account.</span>
                        </div>
                        <button type="button" id="btn-txlog-live-reload" class="txact-btn-primary" style="height:30px;padding:0 12px;font-size:11.5px;">
                            <i class="fas fa-sync-alt"></i> Jump to Latest (Page 1)
                        </button>
                    </div>

                    {{-- Primary Card 1: Period Coin Telemetry KPI Sub-Cards --}}
                    <div class="txact-primary-card">
                        <div class="txact-card-body">
                            <div class="txact-kpi-grid">
                                <div class="txact-kpi-subcard">
                                    <div>
                                        <div class="txact-kpi-val is-credit txact-num" id="kpi-credit-val">—</div>
                                        <div class="txact-kpi-label">Period Credits</div>
                                    </div>
                                    <span class="txact-kpi-mini-icon txact-icon-emerald"><i class="fas fa-arrow-up"></i></span>
                                </div>
                                <div class="txact-kpi-subcard">
                                    <div>
                                        <div class="txact-kpi-val is-debit txact-num" id="kpi-debit-val">—</div>
                                        <div class="txact-kpi-label">Period Debits</div>
                                    </div>
                                    <span class="txact-kpi-mini-icon txact-icon-rose"><i class="fas fa-arrow-down"></i></span>
                                </div>
                                <div class="txact-kpi-subcard">
                                    <div>
                                        <div class="txact-kpi-val is-blue txact-num" id="kpi-net-val">—</div>
                                        <div class="txact-kpi-label">Net Coin Delta</div>
                                    </div>
                                    <span class="txact-kpi-mini-icon txact-icon-blue"><i class="fas fa-balance-scale"></i></span>
                                </div>
                                <div class="txact-kpi-subcard">
                                    <div>
                                        <div class="txact-kpi-val txact-num" id="kpi-total-val">—</div>
                                        <div class="txact-kpi-label">Matching Events</div>
                                    </div>
                                    <span class="txact-kpi-mini-icon txact-icon-amber"><i class="fas fa-list-ol"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Primary Card 2: Coin Activity Ledger Stream --}}
                    <div class="txact-primary-card">
                        <div class="txact-card-header">
                            <div class="txact-card-header-left">
                                <div class="txact-cat-icon txact-icon-blue">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>
                                    <h4 class="txact-card-title">Coin Transaction Events</h4>
                                    <p class="txact-card-desc">Every credit, debit, and settlement event scoped to the selected wallet and date window.</p>
                                </div>
                            </div>
                            <span class="txact-count-pill txact-badge-blue txact-num" id="txlog-period-pill">
                                <i class="fas fa-calendar-alt"></i> <span id="txlog-period-label">Active Range</span>
                            </span>
                        </div>

                        <div class="txact-card-body">
                            <div class="txact-entries-stream" id="txlog-content">
                                <div class="txlog-loading" id="txlog-loader">
                                    <div class="txlog-spinner"></div>
                                    <span>Loading coin activity...</span>
                                </div>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="txlog-pagination" id="txlog-pagination" style="display:none;"></div>
                    </div>

                </div>{{-- /txact-stream --}}

            </div>{{-- /txact-wrapper --}}
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script>
/**
 * Coins Activity Ledger V2 (Rule 20 Card + Sub-Card UI) — AJAX-driven timeline.
 * Defaults to current month date range on load unless overridden by URL query params.
 * Tenancy-scoped server-side with in-memory LRU cache and Reverb live updates.
 */
(function () {
    'use strict';

    // Subdirectory-agnostic AJAX URL
    var dataUrl = window.location.href
        .split('?')[0]
        .replace(/\/+$/, '')
        .replace(/transection-log$/, 'transection-log/data');

    // ── Date helpers ──────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2, '0'); }
    function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

    function getMonthRange(offset) {
        var now  = new Date();
        var year = now.getFullYear();
        var mon  = now.getMonth() + offset;
        var first = new Date(year, mon, 1);
        var last  = new Date(year, mon + 1, 0);
        return { from: fmt(first), to: fmt(last) };
    }

    function thisMonthRange() { return getMonthRange(0); }
    function lastMonthRange() { return getMonthRange(-1); }

    function last7Range() {
        var to   = new Date();
        var from = new Date();
        from.setDate(to.getDate() - 6);
        return { from: fmt(from), to: fmt(to) };
    }

    // ── State ─────────────────────────────────────────────────────────────────
    var range = thisMonthRange();
    var state = {
        pagetype        : 'operationaltab',
        search          : '',
        date_from       : range.from,
        date_to         : range.to,
        transection_type: '',
        per_page        : 10,
        page            : 1,
    };
    var debounceTimer     = null;
    var currentController = null;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    var elContent     = document.getElementById('txlog-content');
    var elLoader      = document.getElementById('txlog-loader');
    var elPagination  = document.getElementById('txlog-pagination');
    var elSearch      = document.getElementById('txlog-search');
    var elSearchClear = document.getElementById('txlog-search-clear');
    var elDateFrom    = document.getElementById('txlog-date-from');
    var elDateTo      = document.getElementById('txlog-date-to');
    var elType        = document.getElementById('txlog-type');
    var elPerPage     = document.getElementById('txlog-per-page');
    var elReset       = document.getElementById('txlog-reset');
    var elStatCredit  = document.getElementById('stat-credit');
    var elStatDebit   = document.getElementById('stat-debit');
    var elStatTotal   = document.getElementById('stat-total');
    var elKpiCredit   = document.getElementById('kpi-credit-val');
    var elKpiDebit    = document.getElementById('kpi-debit-val');
    var elKpiNet      = document.getElementById('kpi-net-val');
    var elKpiTotal    = document.getElementById('kpi-total-val');
    var elLedgerBadge = document.getElementById('activeLedgerBadge');
    var elPeriodLabel = document.getElementById('txlog-period-label');

    // ── Parse URL search parameters for direct deep-linking / page refresh ────
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pagetype')) {
        var pt = urlParams.get('pagetype');
        if (pt === 'salestab' || pt === 'operationaltab') {
            state.pagetype = pt;
        }
    }
    if (urlParams.has('page')) {
        var pg = parseInt(urlParams.get('page'), 10);
        if (!isNaN(pg) && pg > 0) state.page = pg;
    }
    if (urlParams.has('search')) {
        state.search = urlParams.get('search');
    }
    if (urlParams.has('date_from')) {
        state.date_from = urlParams.get('date_from');
    }
    if (urlParams.has('date_to')) {
        state.date_to = urlParams.get('date_to');
    }
    if (urlParams.has('transection_type')) {
        state.transection_type = urlParams.get('transection_type');
    }
    if (urlParams.has('per_page')) {
        var pp = parseInt(urlParams.get('per_page'), 10);
        if (!isNaN(pp) && pp > 0) state.per_page = pp;
    }

    function syncHeaderBadges() {
        if (elLedgerBadge) {
            if (state.pagetype === 'salestab') {
                elLedgerBadge.className = 'txact-count-pill txact-badge-emerald';
                elLedgerBadge.innerHTML = '<i class="fas fa-briefcase"></i> Sales Coins';
            } else {
                elLedgerBadge.className = 'txact-count-pill txact-badge-purple';
                elLedgerBadge.innerHTML = '<i class="fas fa-cogs"></i> Operational Coins';
            }
        }
        if (elPeriodLabel) {
            if (state.date_from && state.date_to) {
                elPeriodLabel.textContent = state.date_from + ' to ' + state.date_to;
            } else if (state.date_from) {
                elPeriodLabel.textContent = 'From ' + state.date_from;
            } else if (state.date_to) {
                elPeriodLabel.textContent = 'Up to ' + state.date_to;
            } else {
                elPeriodLabel.textContent = 'All Time';
            }
        }
    }

    // ── Sync UI inputs & active states with initial state ─────────────────────
    document.querySelectorAll('.txlog-tab').forEach(function(t) {
        t.classList.toggle('active', t.getAttribute('data-pagetype') === state.pagetype);
    });
    if (elSearch) {
        elSearch.value = state.search;
        if (elSearchClear) elSearchClear.style.display = state.search ? 'block' : 'none';
    }
    if (elDateFrom) elDateFrom.value = state.date_from;
    if (elDateTo) elDateTo.value = state.date_to;
    if (elType) elType.value = state.transection_type;
    if (elPerPage) elPerPage.value = String(state.per_page);
    syncHeaderBadges();

    var isThisMonth = state.date_from === range.from && state.date_to === range.to;
    var lastM       = lastMonthRange();
    var isLastMonth = state.date_from === lastM.from && state.date_to === lastM.to;
    var l7          = last7Range();
    var isLast7     = state.date_from === l7.from && state.date_to === l7.to;
    var isAll       = state.date_from === '' && state.date_to === '';

    document.querySelectorAll('.txlog-chip').forEach(function(c) {
        var p = c.getAttribute('data-preset');
        var act = (p === 'this_month' && isThisMonth) ||
                  (p === 'last_month' && isLastMonth) ||
                  (p === 'last_7' && isLast7) ||
                  (p === 'all' && isAll);
        c.classList.toggle('active-chip', act);
    });

    // ── In-Memory Bounded RAM Cache (LRU, max 10 pages, 60s TTL) ───────────────
    var pageCache = new Map();
    var MAX_CACHE_ENTRIES = 10;
    var CACHE_TTL_MS = 60000;

    function getCacheKey() {
        return JSON.stringify({
            pagetype         : state.pagetype,
            search           : state.search,
            date_from        : state.date_from,
            date_to          : state.date_to,
            transection_type : state.transection_type,
            per_page         : state.per_page,
            page             : state.page,
        });
    }

    function getFromCache(key) {
        if (!pageCache.has(key)) return null;
        var entry = pageCache.get(key);
        if (Date.now() - entry.timestamp > CACHE_TTL_MS) {
            pageCache.delete(key);
            return null;
        }
        pageCache.delete(key);
        pageCache.set(key, entry);
        return entry.data;
    }

    function setInCache(key, data) {
        if (pageCache.size >= MAX_CACHE_ENTRIES) {
            var oldestKey = pageCache.keys().next().value;
            pageCache.delete(oldestKey);
        }
        pageCache.set(key, { data: data, timestamp: Date.now() });
    }

    function clearCache() {
        pageCache.clear();
    }

    // ── URL Query String Synchronization ───────────────────────────────────────
    function syncBrowserUrl() {
        if (typeof window.history.replaceState === 'function') {
            var url = new URL(window.location.href);
            url.searchParams.set('pagetype', state.pagetype);
            if (state.page > 1) {
                url.searchParams.set('page', state.page);
            } else {
                url.searchParams.delete('page');
            }
            if (state.search) {
                url.searchParams.set('search', state.search);
            } else {
                url.searchParams.delete('search');
            }
            if (state.date_from) {
                url.searchParams.set('date_from', state.date_from);
            } else {
                url.searchParams.delete('date_from');
            }
            if (state.date_to) {
                url.searchParams.set('date_to', state.date_to);
            } else {
                url.searchParams.delete('date_to');
            }
            if (state.transection_type) {
                url.searchParams.set('transection_type', state.transection_type);
            } else {
                url.searchParams.delete('transection_type');
            }
            if (state.per_page && state.per_page !== 10) {
                url.searchParams.set('per_page', state.per_page);
            } else {
                url.searchParams.delete('per_page');
            }
            window.history.replaceState(null, '', url.pathname + url.search);
        }
    }

    // ── Fetch ─────────────────────────────────────────────────────────────────
    function fetchData(forceRefresh) {
        if (state.page === 1) {
            hideLiveNotice();
        }
        syncHeaderBadges();
        syncBrowserUrl();

        var cacheKey = getCacheKey();
        if (!forceRefresh) {
            var cached = getFromCache(cacheKey);
            if (cached) {
                renderItems(cached.data);
                renderPagination(cached);
                updateStats(cached);
                return;
            }
        }

        showLoader();

        if (currentController) {
            currentController.abort();
        }
        if (typeof AbortController !== 'undefined') {
            currentController = new AbortController();
        }

        var params = new URLSearchParams({
            pagetype         : state.pagetype,
            search           : state.search,
            date_from        : state.date_from,
            date_to          : state.date_to,
            transection_type : state.transection_type,
            per_page         : state.per_page,
            page             : state.page,
        });

        var fetchOptions = {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        };
        if (currentController) {
            fetchOptions.signal = currentController.signal;
        }

        fetch(dataUrl + '?' + params.toString(), fetchOptions)
        .then(function(r) {
            if (!r.ok) {
                throw new Error('HTTP status ' + r.status);
            }
            return r.json();
        })
        .then(function(json) {
            if (json.success) {
                setInCache(cacheKey, json);
                renderItems(json.data);
                renderPagination(json);
                updateStats(json);
            } else {
                showError('Unable to load transactions. Please try again.');
            }
        })
        .catch(function(err) {
            if (err.name === 'AbortError') {
                return;
            }
            showError('A network error occurred. Please refresh the page.');
        });
    }

    // ── Stats & KPI Sub-Card Update ───────────────────────────────────────────
    function updateStats(json) {
        var credit = 0, debit = 0, total = json.total || 0;
        if (json.stats) {
            credit = parseFloat(json.stats.total_credits || 0);
            debit  = parseFloat(json.stats.total_debits  || 0);
        } else {
            (json.data || []).forEach(function(row) {
                if (row.transection_type === 'credit') credit += parseFloat(row.display_coins) || 0;
                else debit += parseFloat(row.display_coins) || 0;
            });
        }
        var netDelta = credit - debit;
        var fmtOpts = { minimumFractionDigits: 0, maximumFractionDigits: 2 };
        var creditFmt = credit.toLocaleString(undefined, fmtOpts);
        var debitFmt  = debit.toLocaleString(undefined, fmtOpts);
        var netPrefix = netDelta > 0 ? '+' : (netDelta < 0 ? '−' : '');
        var netFmt    = netPrefix + Math.abs(netDelta).toLocaleString(undefined, fmtOpts) + ' Coins';

        if (elStatCredit) elStatCredit.textContent = creditFmt + ' C';
        if (elStatDebit)  elStatDebit.textContent  = debitFmt  + ' C';
        if (elStatTotal)  elStatTotal.textContent  = total + ' rows';

        if (elKpiCredit) elKpiCredit.textContent = '+' + creditFmt + ' Coins';
        if (elKpiDebit)  elKpiDebit.textContent  = '−' + debitFmt  + ' Coins';
        if (elKpiNet)    elKpiNet.textContent    = netFmt;
        if (elKpiTotal)  elKpiTotal.textContent  = Number(total).toLocaleString();
    }

    // ── Render Modern Card + Sub-Card Ledger Entries ──────────────────────────
    function renderItems(items) {
        elLoader.style.display = 'none';
        if (!items || items.length === 0) {
            elContent.innerHTML = emptyState();
            elPagination.style.display = 'none';
            return;
        }

        var walletLabel = (state.pagetype === 'salestab') ? 'Sales Wallet' : 'Operational Wallet';
        var html = '';

        items.forEach(function(row) {
            var dateStr = row.formatted_date || '';
            var timeStr = row.formatted_time || '';

            if (!dateStr && row.created_at) {
                var cleanIso = String(row.created_at).replace(' ', 'T');
                var d = new Date(cleanIso);
                if (!isNaN(d.getTime())) {
                    dateStr = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                    timeStr = d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase();
                } else {
                    dateStr = row.created_at;
                }
            }

            var isCredit = row.transection_type === 'credit';
            var isSettle = row.settlement_coins && parseFloat(row.settlement_coins) > 0;
            var cardAccent = isSettle ? 'txact-entry-settle' : (isCredit ? 'txact-entry-credit' : 'txact-entry-debit');

            var typeBadge = '<span class="tx-badge ' + (isCredit ? 'credit' : 'debit') + '"><span class="dot"></span>' + (isCredit ? 'Credit' : 'Debit') + '</span>';
            var settleBdg = isSettle
                ? '<span class="tx-badge settle"><span class="dot"></span>Settlement</span>'
                : '';

            var amtClass   = isCredit ? 'txact-delta-credit' : 'txact-delta-debit';
            var amtSign    = isCredit ? '+' : '−';
            var displayAmt = parseFloat(row.display_coins || 0);
            var displayBal = parseFloat(row.balance_coins || 0);
            var settleAmt  = parseFloat(row.settlement_coins || 0);
            var entryId    = row.id ? ('#' + String(row.id).padStart(5, '0')) : '—';

            html += '' +
                '<div class="txact-entry-card ' + cardAccent + '">' +
                    '<div class="txact-entry-grid">' +
                        '<div class="txact-entry-subcard">' +
                            '<span class="txact-micro-label">DATE &amp; TIME</span>' +
                            '<div class="txact-cell-main txact-num">' + esc(dateStr) + '</div>' +
                            '<div class="txact-cell-sub txact-num"><i class="far fa-clock"></i>' + esc(timeStr || '—') + '</div>' +
                        '</div>' +

                        '<div class="txact-entry-subcard">' +
                            '<span class="txact-micro-label">EVENT DESCRIPTION</span>' +
                            '<div class="txact-cell-main">' + esc(row.event || '—') + '</div>' +
                            '<div class="txact-event-badges">' + typeBadge + settleBdg + '</div>' +
                        '</div>' +

                        '<div class="txact-entry-subcard">' +
                            '<span class="txact-micro-label">INITIATED BY</span>' +
                            '<div class="txact-cell-main">' + esc(row.actor_name || 'System') + '</div>' +
                            '<div class="txact-cell-sub"><i class="fas fa-building"></i>' + esc(row.actor_company || 'Default') + '</div>' +
                        '</div>' +

                        '<div class="txact-entry-subcard">' +
                            '<span class="txact-micro-label">COIN DELTA</span>' +
                            '<div class="' + amtClass + ' txact-num">' + amtSign + ' ' + displayAmt.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}) + '</div>' +
                            '<div class="txact-cell-sub">Coins</div>' +
                        '</div>' +

                        '<div class="txact-entry-subcard">' +
                            '<span class="txact-micro-label">WALLET BALANCE</span>' +
                            '<div class="txact-bal-val txact-num">' + displayBal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}) + '</div>' +
                            '<div class="txact-cell-sub">Remaining</div>' +
                        '</div>' +
                    '</div>' +

                    '<div class="txact-entry-bottom">' +
                        '<div class="txact-bottom-left">' +
                            '<span class="txact-num"><i class="fas fa-hashtag"></i>Entry ' + esc(entryId) + '</span>' +
                            '<span class="txact-bottom-dot">&bull;</span>' +
                            '<span><i class="fas fa-wallet"></i>' + esc(walletLabel) + '</span>' +
                            (isSettle ? ('<span class="txact-bottom-dot">&bull;</span><span class="txact-num"><i class="fas fa-sync"></i>Settlement: ' + settleAmt.toLocaleString() + ' Coins</span>') : '') +
                        '</div>' +
                        '<span class="txact-verified-tag"><i class="fas fa-shield-alt"></i> Ledger Verified</span>' +
                    '</div>' +
                '</div>';
        });

        elContent.innerHTML = html;
    }

    // ── Pagination ────────────────────────────────────────────────────────────
    function renderPagination(json) {
        if (json.last_page <= 1 && (!json.from)) {
            elPagination.style.display = 'none';
            return;
        }
        elPagination.style.display = 'flex';

        var from = json.from || 0, to = json.to || 0;
        var html = '<div class="pg-info txact-num">Showing <strong>' + from + '&ndash;' + to + '</strong> of <strong>' + json.total + '</strong> records</div><div style="display:flex;gap:4px;flex-wrap:wrap;">';

        html += pgLink('&lsaquo;', json.current_page - 1, json.current_page <= 1);

        var start = Math.max(1, json.current_page - 2);
        var end   = Math.min(json.last_page, json.current_page + 2);
        if (start > 1) {
            html += pgLink('1', 1, false);
            if (start > 2) html += '<span class="page-link disabled">&hellip;</span>';
        }
        for (var i = start; i <= end; i++) {
            html += pgLink(i, i, false, i === json.current_page);
        }
        if (end < json.last_page) {
            if (end < json.last_page - 1) html += '<span class="page-link disabled">&hellip;</span>';
            html += pgLink(json.last_page, json.last_page, false);
        }

        html += pgLink('&rsaquo;', json.current_page + 1, json.current_page >= json.last_page);
        html += '</div>';

        elPagination.innerHTML = html;
        elPagination.querySelectorAll('.page-link[data-pg]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                if (el.classList.contains('disabled') || el.classList.contains('active')) return;
                state.page = parseInt(el.getAttribute('data-pg'), 10);
                fetchData();
            });
        });
    }

    function pgLink(label, pg, disabled, active) {
        var cls = 'page-link txact-num' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
        return '<a href="#" class="' + cls + '" data-pg="' + pg + '">' + label + '</a>';
    }

    // ── Preset chips ──────────────────────────────────────────────────────────
    function applyPreset(preset) {
        var r;
        document.querySelectorAll('.txlog-chip').forEach(function(c) {
            c.classList.toggle('active-chip', c.getAttribute('data-preset') === preset);
        });
        if (preset === 'this_month')  r = thisMonthRange();
        else if (preset === 'last_month') r = lastMonthRange();
        else if (preset === 'last_7')  r = last7Range();
        else { r = { from: '', to: '' }; }

        if (elDateFrom) elDateFrom.value = r.from;
        if (elDateTo) elDateTo.value   = r.to;
        state.date_from  = r.from;
        state.date_to    = r.to;
        state.page = 1;
        fetchData();
    }

    document.querySelectorAll('.txlog-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            applyPreset(chip.getAttribute('data-preset'));
        });
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function showLoader() {
        elLoader.style.display = 'flex';
        elContent.innerHTML = '';
        elContent.appendChild(elLoader);
        elPagination.style.display = 'none';
    }
    function showError(msg) {
        elLoader.style.display = 'none';
        elContent.innerHTML = '<div class="txact-empty-subcard"><div class="txact-empty-icon"><i class="fas fa-exclamation-triangle"></i></div><div class="txact-empty-title">' + esc(msg) + '</div><div class="txact-empty-desc">Please retry or refresh the page.</div></div>';
    }
    function emptyState() {
        return '<div class="txact-empty-subcard"><div class="txact-empty-icon"><i class="fas fa-receipt"></i></div><div class="txact-empty-title">No coin transactions found</div><div class="txact-empty-desc">Try adjusting your search keyword, transaction type, or date range.</div></div>';
    }
    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function resetPage() { state.page = 1; }

    // ── Events ────────────────────────────────────────────────────────────────
    document.querySelectorAll('.txlog-tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            var targetType = tab.getAttribute('data-pagetype');
            if (state.pagetype === targetType && tab.classList.contains('active')) {
                return;
            }
            document.querySelectorAll('.txlog-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            state.pagetype = targetType;
            resetPage();
            fetchData();
        });
    });

    if (elSearch) {
        elSearch.addEventListener('input', function() {
            if (elSearchClear) elSearchClear.style.display = elSearch.value ? 'block' : 'none';
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                state.search = elSearch.value.trim();
                resetPage(); fetchData();
            }, 350);
        });
    }

    if (elSearchClear) {
        elSearchClear.addEventListener('click', function() {
            if (elSearch) elSearch.value = '';
            elSearchClear.style.display = 'none';
            state.search = '';
            resetPage(); fetchData();
        });
    }

    if (elDateFrom) {
        elDateFrom.addEventListener('change', function() {
            state.date_from = elDateFrom.value;
            document.querySelectorAll('.txlog-chip').forEach(function(c) { c.classList.remove('active-chip'); });
            resetPage(); fetchData();
        });
    }

    if (elDateTo) {
        elDateTo.addEventListener('change', function() {
            state.date_to = elDateTo.value;
            document.querySelectorAll('.txlog-chip').forEach(function(c) { c.classList.remove('active-chip'); });
            resetPage(); fetchData();
        });
    }

    if (elType) {
        elType.addEventListener('change', function() {
            state.transection_type = elType.value;
            resetPage(); fetchData();
        });
    }

    if (elPerPage) {
        elPerPage.addEventListener('change', function() {
            state.per_page = parseInt(elPerPage.value, 10) || 10;
            resetPage(); fetchData();
        });
    }

    if (elReset) {
        elReset.addEventListener('click', function() {
            var r = thisMonthRange();
            if (elSearch) elSearch.value = '';
            if (elSearchClear) elSearchClear.style.display = 'none';
            if (elDateFrom) elDateFrom.value = r.from;
            if (elDateTo) elDateTo.value = r.to;
            if (elType) elType.value = '';
            if (elPerPage) elPerPage.value = '10';
            state.search = '';
            state.date_from = r.from;
            state.date_to = r.to;
            state.transection_type = '';
            state.per_page = 10;
            document.querySelectorAll('.txlog-chip').forEach(function(c) {
                c.classList.toggle('active-chip', c.getAttribute('data-preset') === 'this_month');
            });
            clearCache();
            resetPage();
            fetchData(true);
        });
    }

    // ── Real-Time WebSocket Synchronization via Laravel Reverb (Phase 4) ─────
    var authCompanyId = {{ (int) (auth()->user()->company_id ?? 0) }};
    var elLiveNotice  = document.getElementById('txlog-live-notice');
    var btnLiveReload = document.getElementById('btn-txlog-live-reload');

    function showLiveNotice() {
        if (elLiveNotice) {
            elLiveNotice.style.setProperty('display', 'flex', 'important');
        }
    }

    function hideLiveNotice() {
        if (elLiveNotice) {
            elLiveNotice.style.setProperty('display', 'none', 'important');
        }
    }

    if (btnLiveReload) {
        btnLiveReload.addEventListener('click', function(e) {
            e.preventDefault();
            hideLiveNotice();
            state.page = 1;
            fetchData(true);
        });
    }

    function initRealtimeSync() {
        if (typeof window.Echo === 'undefined' || !authCompanyId) {
            return;
        }

        try {
            window.Echo.private('company.' + authCompanyId)
                .listen('.coin.transaction.updated', function(e) {
                    if (!e || parseInt(e.company_id, 10) !== authCompanyId) return;
                    clearCache();
                    var currentTabType = (state.pagetype === 'salestab') ? 'sales' : 'operational';
                    if (e.event_type && e.event_type !== currentTabType) {
                        return;
                    }
                    if (state.page === 1 && state.search === '') {
                        fetchData(true);
                    } else {
                        showLiveNotice();
                    }
                })
                .listen('.OperationalCoinUpdated', function(e) {
                    if (!e) return;
                    clearCache();
                    if (state.pagetype === 'operationaltab') {
                        if (state.page === 1 && state.search === '') {
                            fetchData(true);
                        } else {
                            showLiveNotice();
                        }
                    }
                });
        } catch (err) {
            // Suppress unexpected WebSocket connection errors
        }
    }

    // ── Expose Interface for Real-Time Synchronization (Phase 4) ─────────────
    window.JWTransactionLog = {
        refresh: function() {
            clearCache();
            hideLiveNotice();
            fetchData(true);
        },
        clearCache: clearCache,
        getState: function() { return Object.assign({}, state); }
    };

    // ── Initial load & WebSocket attach ───────────────────────────────────────
    fetchData();
    initRealtimeSync();

})();
</script>
@endpush
