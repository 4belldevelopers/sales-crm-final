{{--
  JERO ATS — Service Company Career Page Settings (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Enterprise-grade, page-isolated Career Portal Studio featuring:
          Tier 1 Pure White Toolbar Card with debounced global settings search + Clear + Visit + Save CTA,
          5-Card Interactive KPI Telemetry Strip (Portal Status, Template, Jobs Layout, SEO Readiness, Speed & Tracking),
          Tier 2 Pure White Sub-Tabs Card (9 domain modules with single-active #009ef7 blue highlight),
          Tier 3 Primary Cards + Discrete Sub-Cards + Sticky Live Previews + Client-First Export & Sync.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id in AccountSettingsService / AccountRepository.
                      Zero database queries executed inside this Blade template.
  Screen Fit: 100% fluid fit on 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}
@extends('layouts.service_company_layout')

@push('head-script')
<style>
/* ==========================================================================
   Career Page Settings — Page-Isolated Enterprise Card + Sub-Card System
   Font: Poppins strictly throughout; tabular-nums for all metrics
   ========================================================================== */
:root {
    --cps-primary: #009ef7;
    --cps-primary-deep: #2563eb;
    --cps-primary-soft: #eff6ff;
    --cps-success: #10b981;
    --cps-success-dark: #059669;
    --cps-success-soft: #ecfdf5;
    --cps-warning: #f59e0b;
    --cps-warning-dark: #d97706;
    --cps-warning-soft: #fffbeb;
    --cps-danger: #ef4444;
    --cps-danger-soft: #fef2f2;
    --cps-indigo: #4f46e5;
    --cps-indigo-soft: #eef2ff;
    --cps-purple: #7c3aed;
    --cps-purple-soft: #f5f3ff;
    --cps-text-main: #0f172a;
    --cps-text-mid: #475569;
    --cps-text-muted: #64748b;
    --cps-text-light: #94a3b8;
    --cps-border: #e2e8f0;
    --cps-border-soft: #f1f5f9;
    --cps-bg-subcard: #f8fafc;
    --kt-toolbar-height: 0px !important;
    --kt-toolbar-height-tablet-and-mobile: 0px !important;
}

/* Remove legacy Metronic toolbar gap & guarantee 0% horizontal page scroll */
#kt_wrapper { background: #f4f6fa !important; }
#kt_toolbar, .toolbar {
    display: none !important;
    height: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
}
#kt_content, .content {
    margin-top: 0 !important;
    padding-top: 12px !important;
    padding-bottom: 0 !important;
    background: #f4f6fa !important;
}
#kt_post, .post { margin-top: 0 !important; padding: 0 !important; }
#kt_content_container, body { overflow-x: hidden !important; }

.cps-page,
.cps-page *,
.cps-page *::before,
.cps-page *::after {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    box-sizing: border-box;
}
.cps-page i.ti,
.cps-page [class^="ti-"],
.cps-page [class*=" ti-"] {
    font-family: "tabler-icons" !important;
    font-style: normal !important;
    line-height: 1 !important;
}
.cps-page i.fa,
.cps-page i.fas,
.cps-page i.far,
.cps-page i.fab,
.cps-page [class*="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    line-height: 1 !important;
}
.cps-page {
    width: 100%;
    max-width: 100%;
    padding: 0 14px 28px;
    color: var(--cps-text-main);
    -webkit-font-smoothing: antialiased;
}
.cps-num { font-variant-numeric: tabular-nums !important; }

/* ==========================================================================
   TIER 1: PURE WHITE TOOLBAR HEADER CARD
   ========================================================================== */
.cps-toolbar-card {
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 10px;
    padding: 13px 18px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}
.cps-toolbar-left {
    display: flex;
    align-items: center;
    gap: 13px;
    min-width: 0;
}
.cps-toolbar-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cps-primary);
    font-size: 20px;
    flex-shrink: 0;
}
.cps-toolbar-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.cps-toolbar-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--cps-text-main);
    margin: 0;
    line-height: 1.25;
}
.cps-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.4;
}
.cps-status-pill.is-live { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.cps-status-pill.is-offline { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.cps-status-pill.is-maint { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.cps-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
}
.cps-tenant-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    background: #eff6ff;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.cps-dirty-pill {
    display: none;
    align-items: center;
    gap: 5px;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fcd34d;
}
.cps-dirty-pill.show { display: inline-flex; }
.cps-toolbar-sub {
    font-size: 11.5px;
    color: var(--cps-text-muted);
    margin: 3px 0 0;
    line-height: 1.35;
}
.cps-toolbar-right {
    display: flex;
    align-items: center;
    gap: 9px;
    flex-wrap: wrap;
}

/* Search Sub-Card */
.cps-search-wrap {
    position: relative;
    width: 268px;
    max-width: 100%;
}
.cps-search-subcard {
    display: flex;
    align-items: center;
    height: 36px;
    background: #f8fafc;
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    padding: 0 10px;
    gap: 7px;
    transition: all 0.18s ease;
}
.cps-search-subcard:focus-within {
    background: #ffffff;
    border-color: var(--cps-primary);
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.cps-search-subcard i.ti-search {
    color: #94a3b8;
    font-size: 14px;
    flex-shrink: 0;
}
.cps-search-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    color: var(--cps-text-main);
    width: 100%;
    min-width: 0;
}
.cps-search-input::placeholder { color: #94a3b8; }
.cps-search-clear {
    border: none;
    background: #e2e8f0;
    color: #475569;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    flex-shrink: 0;
    padding: 0;
}
.cps-search-clear:hover { background: #cbd5e1; color: #0f172a; }
.cps-search-results {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 10px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
    z-index: 60;
    max-height: 320px;
    overflow-y: auto;
    display: none;
    padding: 6px;
}
.cps-search-results.open { display: block; }
.cps-search-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 7px;
    cursor: pointer;
    transition: background 0.12s;
}
.cps-search-item:hover { background: #f1faff; }
.cps-search-item-title { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.cps-search-item-sub { font-size: 10.5px; color: var(--cps-text-muted); }
.cps-search-item-tab {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 999px;
    background: #eff6ff;
    color: #0284c7;
    white-space: nowrap;
}

/* Toolbar Buttons */
.cps-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 36px;
    padding: 0 14px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid var(--cps-border);
    color: #334155 !important;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.cps-btn-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--cps-text-main) !important;
}
.cps-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 36px;
    padding: 0 16px;
    border-radius: 8px;
    background: var(--cps-primary);
    border: 1px solid var(--cps-primary);
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.24);
    transition: all 0.15s ease;
}
.cps-btn-primary:hover {
    background: #0284c7;
    border-color: #0284c7;
}
.cps-btn-primary:disabled {
    opacity: 0.7;
    cursor: wait;
}

/* ==========================================================================
   ENTERPRISE KPI TELEMETRY STRIP (5 INTERACTIVE SUB-CARDS)
   ========================================================================== */
.cps-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}
.cps-kpi-card {
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 10px;
    padding: 11px 13px;
    display: flex;
    align-items: center;
    gap: 11px;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    min-width: 0;
}
.cps-kpi-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    transform: translateY(-1px);
}
.cps-kpi-card.active {
    border-color: var(--cps-primary);
    background: #f8fcff;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
}
.cps-kpi-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}
.cps-kpi-body { min-width: 0; flex: 1; }
.cps-kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--cps-text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cps-kpi-val {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--cps-text-main);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cps-kpi-sub {
    font-size: 10.5px;
    color: var(--cps-text-light);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ==========================================================================
   TIER 2: PURE WHITE SUB-TABS CARD (SINGLE ACTIVE #009EF7 HIGHLIGHT)
   ========================================================================== */
.cps-tabs-card {
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 10px;
    padding: 8px 12px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.cps-tabs-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.cps-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--cps-text-mid);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
}
.cps-tab-btn i { font-size: 14px; }
.cps-tab-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--cps-text-main);
}
.cps-tab-btn.active {
    background: var(--cps-primary) !important;
    border-color: var(--cps-primary) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 5px rgba(0, 158, 247, 0.24) !important;
}
.cps-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 6px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    font-variant-numeric: tabular-nums;
}
.cps-tab-badge.badge-live { background: #dcfce7; color: #15803d; }
.cps-tab-badge.badge-blue { background: #e0f2fe; color: #0369a1; }
.cps-tab-btn.active .cps-tab-badge {
    background: rgba(255, 255, 255, 0.26) !important;
    color: #ffffff !important;
}

/* ==========================================================================
   TIER 3: PRIMARY CARDS + DISCRETE SUB-CARDS STREAM
   ========================================================================== */
.cps-panel { display: none; }
.cps-panel.active { display: block; animation: cpsFadeIn 0.18s ease; }
@keyframes cpsFadeIn { from { opacity: 0; transform: translateY(3px); } to { opacity: 1; transform: translateY(0); } }

.cps-workspace-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
    gap: 14px;
    align-items: start;
    width: 100%;
}
.cps-workspace-grid.balanced {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
}
.cps-col-main, .cps-col-side {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.cps-col-side.sticky-side {
    position: sticky;
    top: 14px;
}

/* Primary Card */
.cps-card {
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-left: 3.5px solid var(--cps-primary);
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
    min-width: 0;
}
.cps-card:hover {
    border-color: #cbd5e1;
    border-left-color: var(--cps-primary);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
}
.cps-card.accent-emerald { border-left-color: #10b981; }
.cps-card.accent-emerald:hover { border-left-color: #10b981; }
.cps-card.accent-indigo { border-left-color: #6366f1; }
.cps-card.accent-indigo:hover { border-left-color: #6366f1; }
.cps-card.accent-amber { border-left-color: #f59e0b; }
.cps-card.accent-amber:hover { border-left-color: #f59e0b; }
.cps-card.accent-purple { border-left-color: #8b5cf6; }
.cps-card.accent-purple:hover { border-left-color: #8b5cf6; }
.cps-card.cps-search-flash {
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.28), 0 8px 20px rgba(15, 23, 42, 0.08) !important;
}

.cps-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--cps-border-soft);
    background: #ffffff;
    flex-wrap: wrap;
}
.cps-card-head-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.cps-card-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #eff6ff;
    color: var(--cps-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.cps-card-icon.emerald { background: #ecfdf5; color: #059669; }
.cps-card-icon.indigo { background: #eef2ff; color: #4f46e5; }
.cps-card-icon.amber { background: #fffbeb; color: #d97706; }
.cps-card-icon.purple { background: #f5f3ff; color: #7c3aed; }
.cps-card-icon.rose { background: #fff1f2; color: #e11d48; }
.cps-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--cps-text-main);
    margin: 0;
    line-height: 1.25;
}
.cps-card-sub {
    font-size: 11px;
    color: var(--cps-text-muted);
    margin: 2px 0 0;
    line-height: 1.3;
}
.cps-card-pill {
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
    white-space: nowrap;
}
.cps-card-pill.blue { background: #eff6ff; color: #0284c7; }
.cps-card-pill.green { background: #ecfdf5; color: #059669; }
.cps-card-pill.amber { background: #fffbeb; color: #d97706; }

.cps-card-body {
    padding: 14px 16px;
}

.cps-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 9px 16px;
    background: #fafbfc;
    border-top: 1px solid var(--cps-border-soft);
    font-size: 11px;
    color: var(--cps-text-muted);
    flex-wrap: wrap;
}

/* Discrete Sub-Cards */
.cps-subcard-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}
.cps-subcard-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}
.cps-subcard-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
}
.cps-subcard {
    background: var(--cps-bg-subcard);
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    padding: 11px 13px;
    min-width: 0;
    transition: border-color 0.15s ease, background 0.15s ease;
}
.cps-subcard:hover { border-color: #cbd5e1; }
.cps-subcard.white { background: #ffffff; }
.cps-subcard.span-2 { grid-column: span 2; }
.cps-subcard-kicker {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

/* Form Controls */
.cps-label {
    display: block;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 5px;
}
.cps-hint {
    font-size: 10.5px;
    color: var(--cps-text-light);
    margin-top: 4px;
    line-height: 1.35;
}
.cps-input,
.hs-input,
.ci-input,
.bn-input,
.seo-input {
    width: 100%;
    height: 37px;
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    padding: 0 11px;
    font-size: 12.5px;
    color: var(--cps-text-main);
    background: #ffffff;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cps-input:focus,
.hs-input:focus,
.ci-input:focus,
.bn-input:focus,
.seo-input:focus {
    border-color: var(--cps-primary);
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.11);
}
.cps-textarea,
.hs-textarea,
.ci-textarea,
.bn-textarea,
.seo-textarea {
    width: 100%;
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    padding: 9px 11px;
    font-size: 12.5px;
    color: var(--cps-text-main);
    background: #ffffff;
    outline: none;
    resize: vertical;
    min-height: 76px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cps-textarea:focus,
.hs-textarea:focus,
.ci-textarea:focus,
.bn-textarea:focus,
.seo-textarea:focus {
    border-color: var(--cps-primary);
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.11);
}
.cps-select,
.ci-select,
.bn-select,
.js-select,
.seo-select,
.adv-select {
    width: 100%;
    height: 37px;
    border: 1px solid var(--cps-border);
    border-radius: 8px;
    padding: 0 32px 0 11px;
    font-size: 12.5px;
    color: var(--cps-text-main);
    background: #ffffff;
    outline: none;
    appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.cps-select:focus,
.ci-select:focus,
.bn-select:focus,
.js-select:focus,
.seo-select:focus,
.adv-select:focus {
    border-color: var(--cps-primary);
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.11);
}

/* URL wrap & copy */
.cps-url-wrap { display: flex; gap: 6px; align-items: center; }
.cps-url-wrap .cps-input { flex: 1; background: #ffffff; color: #475569; min-width: 0; font-size: 11.5px; }
.cps-copy-btn {
    height: 37px;
    padding: 0 12px;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    color: #0284c7;
    font-size: 11.5px;
    font-weight: 600;
    background: #eff6ff;
    cursor: pointer;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.cps-copy-btn:hover { background: #dbeafe; }
.cps-url-ok { font-size: 10.5px; color: #059669; display: flex; align-items: center; gap: 4px; margin-top: 5px; font-weight: 500; }

/* Toggle Switch */
.cps-toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.cps-toggle-desc { font-size: 11.5px; color: var(--cps-text-muted); line-height: 1.45; }
.cps-switch { position: relative; display: inline-block; width: 42px; height: 23px; flex-shrink: 0; margin-top: 2px; }
.cps-switch input { opacity: 0; width: 0; height: 0; }
.cps-slider { position: absolute; cursor: pointer; inset: 0; background: #cbd5e1; border-radius: 34px; transition: 0.22s; }
.cps-slider:before {
    position: absolute; content: ''; height: 17px; width: 17px; left: 3px; bottom: 3px;
    background: #ffffff; border-radius: 50%; transition: 0.22s; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.cps-switch input:checked + .cps-slider { background: var(--cps-primary); }
.cps-switch input:checked + .cps-slider:before { transform: translateX(19px); }

/* Color swatch */
.cps-color-row { display: flex; align-items: center; gap: 8px; }
.cps-color-swatch, .td-color-swatch {
    width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--cps-border);
    cursor: pointer; flex-shrink: 0; padding: 2px; background: #ffffff;
}

/* Logo rows */
.cps-logo-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.cps-logo-img { width: 72px; height: 38px; object-fit: contain; border: 1px solid var(--cps-border); border-radius: 8px; padding: 4px; background: #ffffff; }
.cps-logo-placeholder {
    width: 72px; height: 38px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8;
}
.cps-logo-name { font-size: 11.5px; font-weight: 600; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cps-logo-meta { font-size: 10px; color: #94a3b8; }
.cps-change-btn {
    height: 32px; padding: 0 12px; border: 1px solid var(--cps-border); border-radius: 7px;
    font-size: 11.5px; font-weight: 600; color: #334155; background: #ffffff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 4px;
}
.cps-change-btn:hover { border-color: var(--cps-primary); color: var(--cps-primary); }

/* Selectable Option Sub-Cards (Layout, Display, Hero, Benefits, Templates) */
.cps-layout-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
.cps-layout-card,
.cps-display-card,
.hs-layout-card,
.bn-style-card,
.td-layout-card {
    border: 1.5px solid var(--cps-border);
    border-radius: 9px;
    padding: 11px 9px;
    cursor: pointer;
    text-align: center;
    transition: all 0.15s;
    position: relative;
    background: #ffffff;
}
.cps-layout-card:hover,
.cps-display-card:hover,
.hs-layout-card:hover,
.bn-style-card:hover,
.td-layout-card:hover { border-color: #93c5fd; }
.cps-layout-card.selected,
.cps-display-card.selected,
.hs-layout-card.selected,
.bn-style-card.selected,
.td-layout-card.selected {
    border-color: var(--cps-primary);
    background: #f1faff;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
}
.cps-lc-check, .hs-lc-check, .bn-style-check, .td-lc-check {
    position: absolute; top: 6px; right: 6px; width: 17px; height: 17px;
    background: var(--cps-primary); border-radius: 50%; display: none; align-items: center; justify-content: center;
}
.cps-layout-card.selected .cps-lc-check,
.hs-layout-card.selected .hs-lc-check,
.bn-style-card.selected .bn-style-check,
.td-layout-card.selected .td-lc-check { display: flex; }
.cps-lc-icon {
    width: 34px; height: 26px; background: #f1f5f9; border-radius: 6px;
    margin: 0 auto 6px; display: flex; align-items: center; justify-content: center;
}
.cps-layout-card.selected .cps-lc-icon { background: #dbeafe; }
.cps-lc-name, .cps-dc-name, .bn-style-name, .td-lc-name { font-size: 11.5px; font-weight: 700; color: var(--cps-text-main); }
.cps-lc-desc, .cps-dc-desc, .bn-style-desc, .td-lc-desc { font-size: 9.5px; color: var(--cps-text-muted); margin-top: 2px; }

.cps-display-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
.cps-display-card { display: flex; align-items: center; gap: 9px; text-align: left; padding: 11px 12px; }

/* Preview Box */
.cps-preview-box { border: 1px solid var(--cps-border); border-radius: 10px; overflow: hidden; background: #ffffff; }
.cps-prev-hdr { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); padding: 14px; }
.cps-prev-hdr-title { font-size: 10.5px; color: rgba(255, 255, 255, 0.85); margin: 0 0 2px; }
.cps-prev-hdr-name { font-size: 14px; font-weight: 700; color: #ffffff; margin: 0 0 9px; }
.cps-prev-search { background: rgba(255, 255, 255, 0.94); border-radius: 6px; padding: 6px 10px; font-size: 10px; color: #64748b; }
.cps-prev-body { background: #f8fafc; padding: 12px; }
.cps-prev-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-bottom: 10px; }
.cps-prev-stat-n { font-size: 13px; font-weight: 700; color: var(--cps-text-main); font-variant-numeric: tabular-nums; }
.cps-prev-stat-l { font-size: 9.5px; color: #64748b; }
.cps-prev-positions { font-size: 10.5px; font-weight: 700; color: #334155; margin-bottom: 6px; }
.cps-prev-job { background: #ffffff; border: 1px solid var(--cps-border); border-radius: 7px; padding: 7px 10px; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
.cps-prev-job-t { font-size: 10.5px; font-weight: 700; color: var(--cps-text-main); }
.cps-prev-job-s { font-size: 9.5px; color: #64748b; }
.cps-prev-footer { text-align: center; padding-top: 4px; }
.cps-prev-footer a { font-size: 10.5px; color: var(--cps-primary); font-weight: 600; text-decoration: none; }

/* Quick actions */
.cps-qa {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 10px; border-radius: 8px; border: 1px solid var(--cps-border);
    background: #f8fafc; margin-bottom: 7px; cursor: pointer; transition: all 0.15s;
}
.cps-qa:last-child { margin-bottom: 0; }
.cps-qa:hover { border-color: #93c5fd; background: #f1faff; }
.cps-qa-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
.cps-qa-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cps-qa-title { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.cps-qa-desc { font-size: 10.5px; color: var(--cps-text-muted); }

/* ── Hero Section ── */
.hs-label { font-size: 11.5px; font-weight: 600; color: #334155; margin-bottom: 5px; display: block; }
.hs-hint { font-size: 10.5px; color: var(--cps-text-light); margin-top: 4px; }
.hs-char-count, .ci-char-count, .bn-char-count, .seo-char { font-size: 10.5px; color: #94a3b8; font-variant-numeric: tabular-nums; }
.hs-layout-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
.hs-lc-thumb { height: 40px; background: #f1f5f9; border-radius: 6px; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.hs-lc-name { font-size: 10px; font-weight: 600; color: #334155; line-height: 1.25; }

.hs-tags-wrap {
    display: flex; flex-wrap: wrap; gap: 7px; align-items: center;
    padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; min-height: 40px; background: #ffffff;
}
.hs-tag {
    display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 999px;
    font-size: 11px; font-weight: 600; color: #1d4ed8;
}
.hs-tag-icon { width: 15px; height: 15px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; }
.hs-tag-del { cursor: pointer; color: #60a5fa; font-size: 13px; line-height: 1; font-weight: 700; }
.hs-tag-del:hover { color: #1d4ed8; }
.hs-add-point {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;
    border: 1px dashed #cbd5e1; border-radius: 999px; font-size: 11px; font-weight: 600;
    color: #475569; cursor: pointer; background: #ffffff; margin-top: 8px;
}
.hs-add-point:hover { border-color: var(--cps-primary); color: var(--cps-primary); }

.hs-cta-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; align-items: end; }
.hs-cta-newtab { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; color: #475569; margin-top: 7px; cursor: pointer; }
.hs-bg-options { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 12px; }
.hs-bg-opt { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12px; font-weight: 500; color: #334155; }
.hs-bg-opt input { accent-color: var(--cps-primary); width: 14px; height: 14px; cursor: pointer; }
.hs-grad-grid { display: flex; gap: 8px; flex-wrap: wrap; }
.hs-grad-swatch { width: 54px; height: 38px; border-radius: 8px; cursor: pointer; border: 2px solid transparent; position: relative; transition: transform 0.12s; }
.hs-grad-swatch:hover { transform: scale(1.04); }
.hs-grad-swatch.selected { border-color: var(--cps-primary); box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.25); }
.hs-grad-swatch .hs-gs-check { position: absolute; top: 3px; right: 3px; width: 14px; height: 14px; background: var(--cps-primary); border-radius: 50%; display: none; align-items: center; justify-content: center; }
.hs-grad-swatch.selected .hs-gs-check { display: flex; }
.hs-grad-custom { width: 54px; height: 38px; border-radius: 8px; border: 1px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 9px; color: #64748b; cursor: pointer; background: #ffffff; }

/* Hero live preview */
.hs-prev-wrap { border: 1px solid var(--cps-border); border-radius: 10px; overflow: hidden; background: #ffffff; }
.hs-prev-nav { background: #ffffff; padding: 9px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; }
.hs-prev-nav-logo { font-size: 11px; font-weight: 700; color: #0f172a; }
.hs-prev-nav-links { display: flex; gap: 9px; }
.hs-prev-nav-link { font-size: 9px; color: #64748b; }
.hs-prev-nav-btn { font-size: 9px; font-weight: 600; color: #ffffff; background: var(--cps-primary); padding: 4px 10px; border-radius: 5px; }
.hs-prev-hero { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 20px 16px; display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr); gap: 14px; align-items: center; min-height: 185px; }
.hs-prev-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8.5px; font-weight: 600; color: #1d4ed8; background: rgba(255, 255, 255, 0.92); padding: 3px 8px; border-radius: 999px; margin-bottom: 7px; }
.hs-prev-heading { font-size: 14px; font-weight: 800; color: #ffffff; line-height: 1.3; margin-bottom: 6px; }
.hs-prev-desc { font-size: 9px; color: rgba(255, 255, 255, 0.88); line-height: 1.45; margin-bottom: 9px; }
.hs-prev-highlights { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 10px; }
.hs-prev-hl { display: flex; align-items: center; gap: 3px; font-size: 8.5px; color: rgba(255, 255, 255, 0.92); }
.hs-prev-btns { display: flex; gap: 6px; flex-wrap: wrap; }
.hs-prev-btn-primary { font-size: 8.5px; font-weight: 700; color: #0f172a; background: #ffffff; padding: 5px 11px; border-radius: 5px; }
.hs-prev-btn-secondary { font-size: 8.5px; font-weight: 700; color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.5); padding: 5px 11px; border-radius: 5px; background: rgba(255, 255, 255, 0.12); }
.hs-prev-img { background: rgba(255, 255, 255, 0.16); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 8px; height: 140px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.hs-prev-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

.hs-upload-box { border: 1.5px dashed #cbd5e1; border-radius: 9px; padding: 18px; text-align: center; cursor: pointer; background: #f8fafc; display: block; transition: border-color 0.15s; }
.hs-upload-box:hover { border-color: var(--cps-primary); background: #f1faff; }
.hs-upload-icon { font-size: 24px; color: #64748b; margin-bottom: 4px; }
.hs-upload-title { font-size: 12px; font-weight: 600; color: #334155; }
.hs-upload-meta { font-size: 10.5px; color: #94a3b8; margin-top: 2px; }
.hs-illus-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }
.hs-illus-thumb { border: 1.5px solid var(--cps-border); border-radius: 8px; height: 52px; cursor: pointer; display: flex; align-items: center; justify-content: center; background: #f8fafc; }
.hs-illus-thumb.selected { border-color: var(--cps-primary); background: #eff6ff; }
.hs-tip { display: flex; align-items: flex-start; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; color: #334155; }
.hs-tip:last-child { border-bottom: none; padding-bottom: 0; }
.hs-tip-icon { width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 700; }

/* ── Company Info ── */
.ci-logo-wrap { display: flex; align-items: center; gap: 12px; padding: 10px; border: 1px solid var(--cps-border); border-radius: 8px; background: #ffffff; }
.ci-logo-img { width: 54px; height: 54px; object-fit: contain; border-radius: 8px; background: #ffffff; border: 1px solid var(--cps-border); padding: 3px; }
.ci-logo-placeholder { width: 54px; height: 54px; border-radius: 8px; background: #eff6ff; border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ci-logo-meta { font-size: 10px; color: #94a3b8; margin-top: 2px; }
.ci-logo-actions { display: flex; gap: 6px; margin-top: 6px; }
.ci-change-btn { height: 28px; padding: 0 11px; border: 1px solid var(--cps-border); border-radius: 6px; font-size: 11px; font-weight: 600; color: #334155; background: #ffffff; cursor: pointer; display: inline-flex; align-items: center; }
.ci-del-btn { width: 28px; height: 28px; border: 1px solid #fecaca; border-radius: 6px; background: #fef2f2; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #ef4444; }
.ci-input-icon { position: relative; }
.ci-input-icon .ci-input { padding-left: 34px; padding-right: 30px; }
.ci-input-icon .ci-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #94a3b8; pointer-events: none; }
.ci-input-icon .ci-icon-right { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 13px; color: #94a3b8; }
.ci-rich-toolbar { display: flex; align-items: center; gap: 2px; padding: 6px 8px; border: 1px solid var(--cps-border); border-bottom: none; border-radius: 8px 8px 0 0; background: #f8fafc; flex-wrap: wrap; }
.ci-rich-btn { width: 26px; height: 26px; border: none; background: transparent; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #334155; }
.ci-rich-btn:hover { background: #e2e8f0; }
.ci-rich-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 3px; }
.ci-rich-textarea { width: 100%; border: 1px solid var(--cps-border); border-radius: 0 0 8px 8px; padding: 10px 11px; font-size: 12.5px; color: var(--cps-text-main); outline: none; resize: vertical; min-height: 110px; background: #ffffff; }
.ci-rich-textarea:focus { border-color: var(--cps-primary); }

.ci-prev-card { border: 1px solid var(--cps-border); border-radius: 10px; overflow: hidden; background: #ffffff; }
.ci-prev-banner { height: 70px; background: linear-gradient(135deg, #1e3a8a, #009ef7); position: relative; }
.ci-prev-logo-circle { position: absolute; bottom: -22px; left: 16px; width: 46px; height: 46px; border-radius: 10px; background: #ffffff; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.ci-prev-body { padding: 28px 16px 16px; background: #ffffff; }
.ci-prev-name { font-size: 14.5px; font-weight: 700; color: var(--cps-text-main); display: flex; align-items: center; gap: 6px; margin-bottom: 2px; }
.ci-prev-tagline { font-size: 11px; color: #64748b; margin-bottom: 10px; }
.ci-prev-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
.ci-prev-meta-item { display: flex; align-items: center; gap: 4px; font-size: 10px; color: #64748b; }
.ci-prev-social { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
.ci-prev-about-title { font-size: 11.5px; font-weight: 700; color: var(--cps-text-main); margin-bottom: 4px; }
.ci-prev-about-text { font-size: 10.5px; color: #64748b; line-height: 1.55; margin-bottom: 12px; }
.ci-prev-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
.ci-prev-stat-n { font-size: 13px; font-weight: 700; color: var(--cps-primary); font-variant-numeric: tabular-nums; }
.ci-prev-stat-l { font-size: 9.5px; color: #94a3b8; }

.ci-hl-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; border: 1px solid var(--cps-border); border-radius: 8px; margin-bottom: 7px; background: #ffffff; }
.ci-hl-left { display: flex; align-items: center; gap: 9px; }
.ci-hl-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ci-hl-label { font-size: 11.5px; font-weight: 600; color: #334155; }
.ci-hl-val { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); font-variant-numeric: tabular-nums; }
.ci-hl-del { width: 26px; height: 26px; border: 1px solid #fecaca; border-radius: 6px; background: #fef2f2; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #ef4444; }
.ci-add-hl { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; color: var(--cps-primary); cursor: pointer; padding: 6px 0 0; background: none; border: none; }

/* ── Benefits ── */
.bn-toggle-row { display: flex; align-items: center; gap: 10px; }
.bn-toggle-label { font-size: 11.5px; color: var(--cps-text-muted); }
.bn-style-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
.bn-style-icon { font-size: 20px; color: #64748b; margin-bottom: 5px; display: block; }
.bn-style-card.selected .bn-style-icon { color: var(--cps-primary); }
.bn-benefit-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--cps-border); border-radius: 9px; margin-bottom: 7px; background: #ffffff; }
.bn-benefit-row:hover { border-color: #93c5fd; }
.bn-drag { color: #cbd5e1; font-size: 15px; flex-shrink: 0; }
.bn-benefit-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bn-benefit-info { flex: 1; min-width: 0; }
.bn-benefit-title { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); }
.bn-benefit-desc { font-size: 11px; color: #64748b; margin-top: 1px; }
.bn-benefit-actions { display: flex; align-items: center; gap: 5px; flex-shrink: 0; }
.bn-act-btn { width: 28px; height: 28px; border: 1px solid var(--cps-border); border-radius: 6px; background: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; }
.bn-act-btn.eye { color: var(--cps-primary); border-color: #bae6fd; background: #f0f9ff; }
.bn-act-btn.edit { color: #475569; }
.bn-act-btn.del { color: #ef4444; border-color: #fecaca; background: #fef2f2; }
.bn-add-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 13px; background: var(--cps-primary); color: #ffffff; border: none; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.bn-tip-bar { display: flex; align-items: center; gap: 8px; padding: 9px 12px; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; margin-top: 10px; font-size: 11px; color: #1d4ed8; font-weight: 500; }
.bn-prev-wrap { border: 1px solid var(--cps-border); border-radius: 10px; overflow: hidden; background: #ffffff; }
.bn-prev-nav { background: #ffffff; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; }
.bn-prev-nav-logo { display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 700; color: #0f172a; }
.bn-prev-nav-links { display: flex; gap: 7px; }
.bn-prev-nav-link { font-size: 8.5px; color: #64748b; }
.bn-prev-nav-btn { font-size: 8.5px; font-weight: 700; color: #ffffff; background: var(--cps-primary); padding: 3px 8px; border-radius: 4px; }
.bn-prev-section { background: #ffffff; padding: 14px; }
.bn-prev-title { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
.bn-prev-desc { font-size: 9px; color: #64748b; line-height: 1.45; margin-bottom: 12px; }
.bn-prev-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 7px; }
.bn-prev-card { text-align: center; padding: 9px 5px; border: 1px solid #f1f5f9; border-radius: 8px; background: #fafbfc; }
.bn-prev-card-icon { width: 28px; height: 28px; border-radius: 7px; margin: 0 auto 5px; display: flex; align-items: center; justify-content: center; }
.bn-prev-card-name { font-size: 8.5px; font-weight: 700; color: #0f172a; margin-bottom: 2px; line-height: 1.25; }
.bn-prev-card-desc { font-size: 7.5px; color: #94a3b8; line-height: 1.35; }
.bn-prev-link { text-align: center; padding-top: 9px; font-size: 9.5px; color: var(--cps-primary); font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 4px; }
.bn-tip-item { display: flex; align-items: flex-start; gap: 8px; padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; color: #334155; }
.bn-tip-item:last-child { border-bottom: none; padding-bottom: 0; }

/* ── Job Settings ── */
.js-hint { font-size: 10.5px; color: #94a3b8; margin-top: 4px; }
.js-toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 9px 11px; background: #ffffff; border: 1px solid var(--cps-border); border-radius: 8px; }
.js-toggle-label { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.js-toggle-desc { font-size: 10.5px; color: var(--cps-text-muted); margin-top: 1px; }
.js-radio-group { display: flex; flex-direction: column; gap: 8px; }
.js-radio-item { display: flex; align-items: flex-start; gap: 9px; cursor: pointer; padding: 9px 11px; background: #ffffff; border: 1px solid var(--cps-border); border-radius: 8px; }
.js-radio-item input[type="radio"] { accent-color: var(--cps-primary); width: 15px; height: 15px; flex-shrink: 0; margin-top: 2px; }
.js-radio-label { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.js-radio-desc { font-size: 10.5px; color: var(--cps-text-muted); margin-top: 1px; }
.js-chk-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
.js-chk-item { display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 500; color: #334155; cursor: pointer; padding: 7px 9px; background: #f8fafc; border: 1px solid var(--cps-border); border-radius: 7px; }
.js-chk-item input[type="checkbox"] { accent-color: var(--cps-primary); width: 14px; height: 14px; }
.js-prev-wrap { border: 1px solid var(--cps-border); border-radius: 10px; overflow: hidden; }
.js-prev-nav { background: #ffffff; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; }
.js-prev-nav-logo { display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 700; color: #0f172a; }
.js-prev-nav-links { display: flex; gap: 7px; }
.js-prev-nav-link { font-size: 8.5px; color: #64748b; }
.js-prev-nav-btn { font-size: 8.5px; font-weight: 700; color: #ffffff; background: var(--cps-primary); padding: 3px 8px; border-radius: 4px; }
.js-prev-body { background: #f8fafc; padding: 10px; }
.js-job-card { background: #ffffff; border: 1px solid var(--cps-border); border-left: 3px solid var(--cps-primary); border-radius: 8px; padding: 10px 11px; margin-bottom: 7px; position: relative; }
.js-job-badge { display: inline-block; font-size: 7.5px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: #dcfce7; color: #16a34a; margin-bottom: 5px; }
.js-job-title { font-size: 11px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.js-job-meta { display: flex; gap: 6px; font-size: 8.5px; color: #64748b; margin-bottom: 5px; flex-wrap: wrap; }
.js-job-info { display: flex; gap: 10px; font-size: 8.5px; color: #64748b; margin-bottom: 5px; }
.js-job-desc { font-size: 8.5px; color: #64748b; line-height: 1.45; margin-bottom: 6px; }
.js-job-tags { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; justify-content: space-between; }
.js-tag { font-size: 7.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: #f1f5f9; color: #334155; }
.js-job-btn { font-size: 8.5px; font-weight: 700; color: #ffffff; background: var(--cps-primary); padding: 4px 9px; border-radius: 5px; }
.js-job-bookmark { position: absolute; top: 9px; right: 11px; font-size: 12px; color: #cbd5e1; }
.js-prev-link { text-align: center; padding: 6px 0 2px; font-size: 9.5px; color: var(--cps-primary); font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 3px; }
.js-addopt-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; }
.js-addopt-row:last-child { margin-bottom: 0; }
.js-addopt-left { display: flex; align-items: flex-start; gap: 9px; }
.js-addopt-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.js-addopt-title { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.js-addopt-desc { font-size: 10.5px; color: var(--cps-text-muted); }

/* ── SEO & Social ── */
.seo-hint { font-size: 10.5px; color: #94a3b8; margin-top: 4px; }
.seo-slug-url { font-size: 10.5px; color: #64748b; margin-top: 4px; word-break: break-all; }
.seo-slug-url a { color: var(--cps-primary); font-weight: 600; text-decoration: none; }
.seo-social-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border: 1px solid var(--cps-border); border-radius: 8px; background: #ffffff; margin-bottom: 7px; }
.seo-social-icon { width: 32px; height: 32px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 15px; }
.seo-social-label { width: 86px; font-size: 11.5px; font-weight: 600; color: #334155; flex-shrink: 0; }
.seo-social-input { flex: 1; min-width: 0; height: 34px; border: 1px solid var(--cps-border); border-radius: 7px; padding: 0 9px; font-size: 12px; color: var(--cps-text-main); outline: none; }
.seo-social-input:focus { border-color: var(--cps-primary); }
.seo-add-link { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; color: var(--cps-primary); cursor: pointer; background: none; border: none; padding: 4px 0 0; }
.seo-se-prev { border: 1px solid var(--cps-border); border-radius: 9px; padding: 14px; background: #ffffff; }
.seo-se-url { font-size: 11px; color: #16a34a; margin-bottom: 3px; word-break: break-all; }
.seo-se-title { font-size: 14px; font-weight: 700; color: #1d4ed8; margin-bottom: 4px; }
.seo-se-desc { font-size: 11.5px; color: #475569; line-height: 1.5; }
.seo-share-prev { border: 1px solid var(--cps-border); border-radius: 9px; overflow: hidden; background: #ffffff; }
.seo-share-logo-bar { padding: 7px 11px; display: flex; align-items: center; gap: 6px; border-bottom: 1px solid #f1f5f9; }
.seo-share-hero { background: linear-gradient(135deg, #1e3a8a, #009ef7); padding: 18px 16px; position: relative; min-height: 92px; display: flex; align-items: flex-end; }
.seo-share-hero-text { color: #ffffff; position: relative; z-index: 1; }
.seo-share-hero-title { font-size: 14px; font-weight: 800; line-height: 1.2; margin-bottom: 4px; }
.seo-share-hero-sub { font-size: 9.5px; opacity: 0.9; }
.seo-share-body { padding: 11px 13px; background: #ffffff; }
.seo-share-og-title { font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.seo-share-og-desc { font-size: 10.5px; color: #64748b; line-height: 1.45; margin-bottom: 5px; }
.seo-share-og-url { font-size: 9.5px; color: #94a3b8; margin-bottom: 8px; }
.seo-share-btns { display: flex; gap: 6px; flex-wrap: wrap; }
.seo-share-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border: 1px solid var(--cps-border); border-radius: 6px; font-size: 10px; font-weight: 600; color: #334155; background: #f8fafc; }
.seo-addopt-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; }
.seo-addopt-row:last-child { margin-bottom: 0; }
.seo-addopt-left { display: flex; align-items: flex-start; gap: 9px; }
.seo-addopt-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.seo-addopt-title { font-size: 11.5px; font-weight: 600; color: var(--cps-text-main); }
.seo-addopt-desc { font-size: 10px; color: var(--cps-text-muted); }

/* ── Integrations ── */
.int-search-bar { display: flex; gap: 9px; align-items: center; margin-bottom: 12px; flex-wrap: wrap; }
.int-search-wrap { flex: 1; min-width: 180px; position: relative; }
.int-search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #94a3b8; }
.int-search-input { width: 100%; height: 36px; border: 1px solid var(--cps-border); border-radius: 8px; padding: 0 11px 0 33px; font-size: 12px; color: var(--cps-text-main); outline: none; background: #f8fafc; }
.int-search-input:focus { border-color: var(--cps-primary); background: #ffffff; }
.int-cat-select { height: 36px; border: 1px solid var(--cps-border); border-radius: 8px; padding: 0 28px 0 11px; font-size: 12px; font-weight: 600; color: #334155; background: #ffffff; appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; }
.int-tabs { display: flex; gap: 6px; margin-bottom: 14px; overflow-x: auto; padding-bottom: 2px; }
.int-tab { padding: 5px 11px; font-size: 11px; font-weight: 600; color: #475569; cursor: pointer; border-radius: 7px; border: 1px solid var(--cps-border); background: #ffffff; white-space: nowrap; }
.int-tab:hover { border-color: #cbd5e1; color: var(--cps-text-main); }
.int-tab.active { background: var(--cps-primary); border-color: var(--cps-primary); color: #ffffff; }
.int-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px; }
.int-card { border: 1px solid var(--cps-border); border-radius: 9px; padding: 14px; background: #ffffff; transition: all 0.15s; display: flex; flex-direction: column; }
.int-card:hover { border-color: #93c5fd; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05); }
.int-card.connected { border-left: 3px solid #10b981; }
.int-card-logo { height: 36px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.int-card-name { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); margin-bottom: 3px; }
.int-card-desc { font-size: 11px; color: #64748b; line-height: 1.45; margin-bottom: 12px; flex: 1; }
.int-card-footer { display: flex; align-items: center; justify-content: space-between; gap: 6px; padding-top: 8px; border-top: 1px solid #f1f5f9; }
.int-connected-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: #dcfce7; color: #15803d; border-radius: 999px; font-size: 10px; font-weight: 700; }
.int-connect-btn { font-size: 11.5px; font-weight: 600; color: var(--cps-primary); background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; cursor: pointer; padding: 3px 10px; }
.int-manage-btn { font-size: 11px; font-weight: 600; color: #475569; background: none; border: none; cursor: pointer; padding: 0; }
.int-request-card { border: 1px solid var(--cps-border); border-radius: 9px; padding: 14px 16px; display: flex; align-items: center; gap: 14px; background: #f8fafc; flex-wrap: wrap; }
.int-request-icon { width: 44px; height: 44px; flex-shrink: 0; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.int-request-title { font-size: 13px; font-weight: 700; color: var(--cps-text-main); }
.int-request-desc { font-size: 11px; color: #64748b; }
.int-request-btn { padding: 7px 14px; background: #ffffff; border: 1px solid var(--cps-border); border-radius: 7px; font-size: 11.5px; font-weight: 600; color: #334155; cursor: pointer; white-space: nowrap; }
.int-request-btn:hover { border-color: var(--cps-primary); color: var(--cps-primary); }
.int-active-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; }
.int-active-row:last-child { margin-bottom: 0; }
.int-active-left { display: flex; align-items: center; gap: 9px; }
.int-active-logo { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid var(--cps-border); background: #ffffff; }
.int-active-name { font-size: 12px; font-weight: 700; color: var(--cps-text-main); }
.int-active-sync { font-size: 10px; color: #94a3b8; }
.int-active-right { display: flex; align-items: center; gap: 7px; }
.int-connected-sm { padding: 2px 8px; background: #dcfce7; color: #15803d; border-radius: 999px; font-size: 10px; font-weight: 700; }
.int-gear-btn { width: 26px; height: 26px; border: 1px solid var(--cps-border); border-radius: 6px; background: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 12px; }
.int-wh-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; gap: 8px; }
.int-wh-left { display: flex; align-items: center; gap: 9px; min-width: 0; }
.int-wh-icon { width: 30px; height: 30px; border-radius: 7px; background: #ffffff; border: 1px solid var(--cps-border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.int-wh-name { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.int-wh-url { font-size: 10px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px; }
.int-wh-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.int-active-badge { padding: 2px 8px; background: #dcfce7; color: #15803d; border-radius: 999px; font-size: 10px; font-weight: 700; }
.int-add-wh-btn { display: inline-flex; align-items: center; gap: 4px; padding: 5px 11px; border: 1px solid #bae6fd; border-radius: 7px; font-size: 11px; font-weight: 600; color: #0284c7; background: #eff6ff; cursor: pointer; }
.int-other-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; }
.int-other-row:last-child { margin-bottom: 0; }
.int-other-left { display: flex; align-items: flex-start; gap: 9px; }
.int-other-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.int-other-title { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.int-other-desc { font-size: 10px; color: #64748b; }
.int-arrow-btn { width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #94a3b8; font-size: 14px; }

/* ── Advanced ── */
.adv-toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #ffffff; margin-bottom: 7px; }
.adv-toggle-row:last-child { margin-bottom: 0; }
.adv-toggle-label { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.adv-toggle-desc { font-size: 10.5px; color: #64748b; margin-top: 1px; }
.adv-hint { font-size: 10.5px; color: #94a3b8; }
.adv-perf-card { border: 1px solid var(--cps-border); border-radius: 9px; padding: 14px; background: #f8fafc; }
.adv-gauge-wrap { text-align: center; margin-bottom: 10px; }
.adv-gauge-score { font-size: 24px; font-weight: 800; color: var(--cps-text-main); font-variant-numeric: tabular-nums; }
.adv-gauge-total { font-size: 12px; color: #94a3b8; }
.adv-gauge-label { font-size: 11px; font-weight: 700; color: #16a34a; margin-top: 1px; }
.adv-perf-check { display: flex; align-items: center; justify-content: space-between; font-size: 10.5px; padding: 4px 0; border-bottom: 1px solid #e2e8f0; }
.adv-perf-check:last-child { border-bottom: none; padding-bottom: 0; }
.adv-perf-check-name { display: flex; align-items: center; gap: 5px; color: #334155; }
.adv-perf-check-name i { font-size: 11px; color: #16a34a; }
.adv-perf-excellent { color: #16a34a; font-weight: 600; }
.adv-perf-good { color: #d97706; font-weight: 600; }
.adv-sec-card { border: 1px solid var(--cps-border); border-radius: 9px; padding: 14px; background: #f8fafc; }
.adv-sec-link { font-size: 11px; color: var(--cps-primary); display: flex; align-items: center; gap: 4px; margin-bottom: 9px; text-decoration: none; word-break: break-all; }
.adv-copy-btn { width: 100%; padding: 7px; border: 1px solid var(--cps-border); border-radius: 7px; font-size: 11.5px; font-weight: 600; color: #334155; background: #ffffff; cursor: pointer; }
.adv-share-title { font-size: 11px; font-weight: 600; color: #334155; margin: 11px 0 4px; }
.adv-share-desc { font-size: 10px; color: #94a3b8; margin-bottom: 7px; }
.adv-share-icons { display: flex; gap: 7px; margin-bottom: 9px; }
.adv-share-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 14px; border: 1px solid var(--cps-border); }
.adv-preview-btn { width: 100%; padding: 7px; border: 1px solid var(--cps-border); border-radius: 7px; font-size: 11.5px; font-weight: 600; color: #334155; background: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; text-decoration: none; }
.adv-maint-card { border: 1px solid #fde68a; border-radius: 9px; padding: 13px; background: #fffbeb; }
.adv-maint-header { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
.adv-maint-title { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); }
.adv-maint-desc { font-size: 10.5px; color: #92400e; }
.adv-maint-textarea { width: 100%; border: 1px solid #fcd34d; border-radius: 8px; padding: 9px 11px; font-size: 12px; color: var(--cps-text-main); outline: none; resize: vertical; min-height: 60px; background: #ffffff; }
.adv-code-tabs { display: flex; border-bottom: 1px solid var(--cps-border); padding: 0 14px; background: #f8fafc; }
.adv-code-tab { padding: 8px 12px; font-size: 11.5px; font-weight: 600; color: #64748b; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; }
.adv-code-tab.active { color: var(--cps-primary); border-bottom-color: var(--cps-primary); }
.adv-code-editor { background: #0f172a; padding: 12px 14px; font-family: 'Courier New', monospace; font-size: 11px; line-height: 1.65; color: #94a3b8; overflow-x: auto; }
.adv-code-line { display: flex; gap: 12px; }
.adv-code-ln { color: #475569; min-width: 16px; user-select: none; flex-shrink: 0; }
.adv-code-text { color: #cbd5e1; }
.adv-track-input-wrap { position: relative; margin-bottom: 9px; }
.adv-track-input { width: 100%; height: 36px; border: 1px solid var(--cps-border); border-radius: 8px; padding: 0 100px 0 11px; font-size: 12px; color: var(--cps-text-main); outline: none; background: #ffffff; }
.adv-track-input:focus { border-color: var(--cps-primary); }
.adv-track-badge { position: absolute; right: 7px; top: 50%; transform: translateY(-50%); padding: 2px 8px; background: #f1f5f9; color: #64748b; border-radius: 999px; font-size: 9.5px; font-weight: 700; }
.adv-track-badge.connected { background: #dcfce7; color: #15803d; }
.adv-verify-btn { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); padding: 3px 9px; border: 1px solid #bae6fd; border-radius: 6px; font-size: 10px; font-weight: 700; color: #0284c7; background: #eff6ff; cursor: pointer; }
.adv-track-label { font-size: 11px; font-weight: 600; color: #334155; margin-bottom: 4px; }
.adv-track-status { border: 1px solid var(--cps-border); border-radius: 9px; padding: 12px; background: #f8fafc; }
.adv-ts-row { display: flex; align-items: center; justify-content: space-between; padding: 4px 0; }
.adv-ts-name { font-size: 11px; color: #334155; }
.adv-ts-badge { padding: 2px 7px; background: #dcfce7; color: #15803d; border-radius: 999px; font-size: 9.5px; font-weight: 700; }
.adv-track-desc { font-size: 10px; color: #64748b; line-height: 1.45; margin-top: 8px; display: flex; align-items: flex-start; gap: 6px; }
.adv-export-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid var(--cps-border); border-radius: 8px; background: #ffffff; margin-bottom: 7px; }
.adv-export-row:last-child { margin-bottom: 0; }
.adv-export-label { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.adv-export-desc { font-size: 10px; color: #64748b; }
.adv-export-btn { padding: 6px 12px; border: 1px solid var(--cps-border); border-radius: 7px; font-size: 11px; font-weight: 600; color: #334155; background: #f8fafc; cursor: pointer; white-space: nowrap; }
.adv-export-btn:hover { border-color: var(--cps-primary); color: var(--cps-primary); background: #ffffff; }
.adv-dl-btn { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: var(--cps-primary); cursor: pointer; background: none; border: none; padding: 4px 0 0; }

/* ── Template Design ── */
.td-tpl-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 9px; }
.td-tpl-card { border: 1.5px solid var(--cps-border); border-radius: 9px; overflow: hidden; cursor: pointer; transition: all 0.15s; position: relative; background: #ffffff; }
.td-tpl-card:hover { border-color: #93c5fd; }
.td-tpl-card.selected { border-color: var(--cps-primary); box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.15); }
.td-tpl-check { position: absolute; top: 6px; right: 6px; width: 18px; height: 18px; background: var(--cps-primary); border-radius: 50%; display: none; align-items: center; justify-content: center; z-index: 2; }
.td-tpl-card.selected .td-tpl-check { display: flex; }
.td-tpl-thumb { height: 64px; overflow: hidden; }
.td-tpl-info { padding: 7px 8px 8px; background: #ffffff; }
.td-tpl-name { font-size: 11.5px; font-weight: 700; color: var(--cps-text-main); }
.td-tpl-desc { font-size: 9.5px; color: #64748b; }
.td-design-wrap { display: flex; border: 1px solid var(--cps-border); border-radius: 9px; overflow: hidden; background: #ffffff; }
.td-design-nav { width: 155px; flex-shrink: 0; border-right: 1px solid var(--cps-border); background: #f8fafc; }
.td-design-nav-item { display: flex; align-items: center; gap: 7px; padding: 9px 12px; font-size: 11.5px; font-weight: 600; color: #64748b; cursor: pointer; border-left: 2.5px solid transparent; }
.td-design-nav-item:hover { background: #eff6ff; color: var(--cps-primary); }
.td-design-nav-item.active { background: #eff6ff; color: var(--cps-primary); border-left-color: var(--cps-primary); }
.td-design-tab { display: none; }
.td-design-tab.active { display: block; }
.td-design-content { flex: 1; padding: 14px 16px; min-width: 0; }
.td-design-section-title { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); margin: 0 0 2px; }
.td-design-section-sub { font-size: 10.5px; color: #94a3b8; margin: 0 0 11px; }
.td-color-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px; }
.td-color-label { font-size: 11px; font-weight: 600; color: #334155; margin-bottom: 4px; }
.td-color-row { display: flex; align-items: center; gap: 6px; }
.td-color-input { flex: 1; min-width: 0; height: 34px; border: 1px solid var(--cps-border); border-radius: 7px; padding: 0 8px; font-size: 11.5px; font-weight: 600; color: #334155; outline: none; }
.td-theme-opts { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 12px; }
.td-theme-opt { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 11.5px; font-weight: 500; color: #334155; }
.td-theme-opt input { accent-color: var(--cps-primary); width: 14px; height: 14px; }
.td-layout-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
.td-lc-thumb { height: 32px; background: #e2e8f0; border-radius: 5px; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; }
.td-layout-card.selected .td-lc-thumb { background: #bfdbfe; }
.td-asset-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 8px; }
.td-asset-row:last-child { margin-bottom: 0; }
.td-asset-thumb { width: 52px; height: 34px; border: 1px solid var(--cps-border); border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #ffffff; flex-shrink: 0; overflow: hidden; }
.td-asset-info { flex: 1; min-width: 0; }
.td-asset-name { font-size: 12px; font-weight: 600; color: #334155; }
.td-asset-size { font-size: 10px; color: #94a3b8; }
.td-asset-edit { width: 30px; height: 30px; border: 1px solid var(--cps-border); border-radius: 7px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; background: #ffffff; flex-shrink: 0; }
.td-asset-edit:hover { border-color: var(--cps-primary); color: var(--cps-primary); }
.td-opt-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; padding: 10px 12px; border: 1px solid var(--cps-border); border-radius: 8px; background: #f8fafc; margin-bottom: 7px; }
.td-opt-row:last-child { margin-bottom: 0; }
.td-opt-left { display: flex; align-items: flex-start; gap: 9px; }
.td-opt-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.td-opt-title { font-size: 12px; font-weight: 600; color: var(--cps-text-main); }
.td-opt-desc { font-size: 10px; color: #64748b; }
.td-prev-device-bar { display: flex; align-items: center; gap: 5px; }
.td-prev-device-btn { width: 28px; height: 28px; border: 1px solid var(--cps-border); border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 13px; background: #ffffff; }
.td-prev-device-btn.active { border-color: var(--cps-primary); color: var(--cps-primary); background: #eff6ff; }
.td-live-prev { border: 1px solid var(--cps-border); border-radius: 9px; overflow: hidden; margin: 0 auto; }
.td-prev-nav { background: #ffffff; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; }
.td-prev-nav-logo { font-size: 10.5px; font-weight: 800; color: #0f172a; }
.td-prev-nav-links { display: flex; gap: 8px; }
.td-prev-nav-link { font-size: 8px; color: #64748b; }
.td-prev-nav-btn { font-size: 8px; font-weight: 700; color: #ffffff; background: #2563eb; padding: 3px 8px; border-radius: 4px; }
.td-prev-hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #3b82f6 100%); padding: 18px 14px; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 10px; align-items: center; }
.td-prev-hero-title { font-size: 15px; font-weight: 900; color: #ffffff; line-height: 1.2; margin-bottom: 5px; }
.td-prev-hero-sub { font-size: 8.5px; color: rgba(255, 255, 255, 0.8); line-height: 1.45; margin-bottom: 9px; }
.td-prev-hero-search { display: flex; background: #ffffff; border-radius: 5px; overflow: hidden; }
.td-prev-hero-search input { flex: 1; min-width: 0; border: none; outline: none; padding: 5px 8px; font-size: 8px; color: #64748b; }
.td-prev-hero-search-btn { padding: 5px 9px; background: #2563eb; color: #ffffff; font-size: 8px; font-weight: 700; border: none; white-space: nowrap; }
.td-prev-why { background: #ffffff; padding: 14px 12px; text-align: center; }
.td-prev-why-title { font-size: 12px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
.td-prev-why-sub { font-size: 8px; color: #64748b; margin-bottom: 10px; }
.td-prev-why-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px; }
.td-prev-why-card { padding: 6px 3px; }
.td-prev-why-icon { width: 24px; height: 24px; border-radius: 6px; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center; }
.td-prev-why-name { font-size: 7.5px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.td-prev-why-desc { font-size: 6.5px; color: #64748b; line-height: 1.3; }
.td-prev-cta { background: linear-gradient(135deg, #1e3a8a, #2563eb); padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.td-prev-cta-title { font-size: 11px; font-weight: 800; color: #ffffff; }
.td-prev-cta-sub { font-size: 8px; color: rgba(255, 255, 255, 0.85); }
.td-prev-cta-btn { font-size: 8px; font-weight: 700; color: #2563eb; background: #ffffff; padding: 5px 10px; border-radius: 4px; white-space: nowrap; }
.td-publish-card { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 9px; padding: 14px; }
.td-publish-title { font-size: 12.5px; font-weight: 700; color: var(--cps-text-main); margin-bottom: 2px; }
.td-publish-desc { font-size: 11px; color: #475569; line-height: 1.45; margin-bottom: 10px; }
.td-publish-btn { width: 100%; padding: 9px; background: var(--cps-primary); color: #ffffff; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
.td-publish-btn:hover { background: #0284c7; }

/* Bottom Global Action Bar */
.cps-bottom-bar {
    margin-top: 14px;
    background: #ffffff;
    border: 1px solid var(--cps-border);
    border-radius: 10px;
    padding: 11px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.cps-bottom-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 11.5px;
    color: var(--cps-text-muted);
}
.cps-bottom-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ==========================================================================
   15.6" SCREEN & TABLET / MOBILE RESPONSIVENESS (1366x768 & 1920x1080)
   ========================================================================== */
@media (max-width: 1280px) {
    .cps-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .td-tpl-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .int-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 1024px) {
    .cps-workspace-grid,
    .cps-workspace-grid.balanced { grid-template-columns: minmax(0, 1fr); }
    .cps-col-side.sticky-side { position: static; }
    .js-chk-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .cps-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .cps-subcard-grid-2,
    .cps-subcard-grid-3,
    .cps-subcard-grid-4,
    .hs-layout-grid,
    .bn-style-grid,
    .hs-cta-grid,
    .td-color-grid,
    .int-grid { grid-template-columns: minmax(0, 1fr); }
    .cps-subcard.span-2 { grid-column: span 1; }
    .td-design-wrap { flex-direction: column; }
    .td-design-nav { width: 100%; border-right: none; border-bottom: 1px solid var(--cps-border); display: flex; overflow-x: auto; }
    .td-tpl-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .cps-search-wrap { width: 100%; }
}
</style>
@endpush

@section('content')
@php
    $company_id     = auth()->user()->company_id;
    $company        = $company ?? null;
    $careerSettings = $careerSettings ?? null;
    $companyName    = $careerSettings->ci_company_name ?? $company->company_name ?? auth()->user()->name ?? 'Company';
    $careerUrl      = url('career-jobs/' . str_replace(' ', '-', $company->company_name ?? $companyName) . '/' . $company_id);
    $companyHost    = parse_url($careerSettings->ci_website ?? '', PHP_URL_HOST) ?: strtolower(preg_replace('/[^a-z0-9]/i', '', $companyName)) . '.com';
    $primaryColor   = $careerSettings->primary_color    ?? '#2563EB';
    $secondaryColor = $careerSettings->secondary_color  ?? '#10B981';
    $fontFamily     = $careerSettings->font_family      ?? 'Poppins';
    $pageLayout     = $careerSettings->page_layout      ?? 'modern';
    $jobsDisplay    = $careerSettings->jobs_display     ?? 'card';
    $isActive       = isset($careerSettings->career_page_status) ? (int) $careerSettings->career_page_status : 1;
    $isMaint        = !empty($careerSettings->adv_maintenance);
    $tdTpl          = $careerSettings->td_template      ?? 'modern';
    $tdThemeMode    = $careerSettings->td_theme_mode    ?? 'light';
    $benefitsCount  = $careerSettings->benefits_count   ?? 8;
    $jsStyle        = $careerSettings->js_display_style ?? $jobsDisplay;
    $jsPerPage      = $careerSettings->js_per_page      ?? 12;
    $seoRobots      = $careerSettings->seo_robots       ?? 'index,follow';
    $advCaching     = !isset($careerSettings->adv_caching) || !empty($careerSettings->adv_caching);
    $advCdn         = !isset($careerSettings->adv_cdn) || !empty($careerSettings->adv_cdn);
    $advImgOpt      = !isset($careerSettings->adv_img_opt) || !empty($careerSettings->adv_img_opt);
    $perfScore      = 72 + ($advCaching ? 10 : 0) + ($advCdn ? 8 : 0) + ($advImgOpt ? 7 : 0);
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div id="kt_content_container" class="container-fluid px-0">

<div class="cps-page" id="cpsPageRoot">

    {{-- TIER 1: Pure White Enterprise Toolbar Header Card --}}
    <div class="cps-toolbar-card">
        <div class="cps-toolbar-left">
            <div class="cps-toolbar-icon">
                <i class="ti ti-world-cog"></i>
            </div>
            <div style="min-width:0;">
                <div class="cps-toolbar-title-row">
                    <h1 class="cps-toolbar-title">Career Page Settings</h1>
                    <span class="cps-status-pill {{ $isMaint ? 'is-maint' : ($isActive ? 'is-live' : 'is-offline') }}" id="cpsHeaderStatusPill">
                        <span class="cps-status-dot"></span>
                        <span id="cpsHeaderStatusText">{{ $isMaint ? 'Maintenance Mode' : ($isActive ? 'Career Page Live' : 'Offline') }}</span>
                    </span>
                    <span class="cps-tenant-pill">
                        <i class="ti ti-building-skyscraper" style="font-size:12px;"></i>
                        {{ $companyName }}
                    </span>
                    <span class="cps-dirty-pill" id="cpsDirtyBadge">
                        <i class="ti ti-alert-circle" style="font-size:12px;"></i>
                        Unsaved Changes
                    </span>
                </div>
                <p class="cps-toolbar-sub">Configure employer branding, hero experience, job listings, SEO, integrations, and template design.</p>
            </div>
        </div>

        <div class="cps-toolbar-right">
            <div class="cps-search-wrap">
                <div class="cps-search-subcard">
                    <i class="ti ti-search"></i>
                    <input type="text" class="cps-search-input" id="cpsGlobalSearch" placeholder="Search settings (Press /)..." autocomplete="off">
                    <button type="button" class="cps-search-clear" id="cpsSearchClear" title="Clear search">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="cps-search-results" id="cpsSearchResults"></div>
            </div>

            <a href="{{ $careerUrl }}" target="_blank" class="cps-btn-secondary">
                <i class="ti ti-external-link" style="font-size:14px;"></i>
                Visit Career Page
            </a>

            <button type="submit" form="careerSettingsForm" class="cps-btn-primary js-cps-save-btn">
                <i class="ti ti-device-floppy" style="font-size:15px;"></i>
                <span>Save All Changes</span>
            </button>
        </div>
    </div>

    {{-- ENTERPRISE KPI TELEMETRY STRIP (5 INTERACTIVE SUB-CARDS) --}}
    <div class="cps-kpi-grid">
        <div class="cps-kpi-card active" data-kpi-tab="general" onclick="cpsActivateTab('general')">
            <div class="cps-kpi-icon" style="background:#ecfdf5;color:#059669;">
                <i class="ti ti-broadcast"></i>
            </div>
            <div class="cps-kpi-body">
                <div class="cps-kpi-label">Portal Status</div>
                <div class="cps-kpi-val cps-num" id="kpiPortalStatus">{{ $isMaint ? 'Maintenance' : ($isActive ? 'Live & Public' : 'Offline') }}</div>
                <div class="cps-kpi-sub">{{ strtoupper($careerSettings->currency ?? 'INR') }} • {{ strtoupper($careerSettings->default_language ?? 'en') }}</div>
            </div>
        </div>

        <div class="cps-kpi-card" data-kpi-tab="templates" onclick="cpsActivateTab('templates')">
            <div class="cps-kpi-icon" style="background:#eff6ff;color:#009ef7;">
                <i class="ti ti-palette"></i>
            </div>
            <div class="cps-kpi-body">
                <div class="cps-kpi-label">Design Template</div>
                <div class="cps-kpi-val" id="kpiTemplateVal">{{ ucfirst($tdTpl) }} Theme</div>
                <div class="cps-kpi-sub" id="kpiTemplateSub">{{ ucfirst($tdThemeMode) }} • {{ $fontFamily }}</div>
            </div>
        </div>

        <div class="cps-kpi-card" data-kpi-tab="jobs" onclick="cpsActivateTab('jobs')">
            <div class="cps-kpi-icon" style="background:#eef2ff;color:#4f46e5;">
                <i class="ti ti-briefcase"></i>
            </div>
            <div class="cps-kpi-body">
                <div class="cps-kpi-label">Job Board Layout</div>
                <div class="cps-kpi-val cps-num" id="kpiJobsVal">{{ ucfirst($jsStyle) }} View</div>
                <div class="cps-kpi-sub cps-num" id="kpiJobsSub">{{ $jsPerPage }} Jobs / Page</div>
            </div>
        </div>

        <div class="cps-kpi-card" data-kpi-tab="seo" onclick="cpsActivateTab('seo')">
            <div class="cps-kpi-icon" style="background:#f5f3ff;color:#7c3aed;">
                <i class="ti ti-world-search"></i>
            </div>
            <div class="cps-kpi-body">
                <div class="cps-kpi-label">SEO & Social</div>
                <div class="cps-kpi-val" id="kpiSeoVal">{{ str_contains($seoRobots, 'noindex') ? 'No-Index' : 'Search Indexed' }}</div>
                <div class="cps-kpi-sub">{{ str_replace(',', ' / ', $seoRobots) }}</div>
            </div>
        </div>

        <div class="cps-kpi-card" data-kpi-tab="advanced" onclick="cpsActivateTab('advanced')">
            <div class="cps-kpi-icon" style="background:#fffbeb;color:#d97706;">
                <i class="ti ti-bolt"></i>
            </div>
            <div class="cps-kpi-body">
                <div class="cps-kpi-label">Speed & Cache</div>
                <div class="cps-kpi-val cps-num" id="kpiPerfVal">{{ $perfScore }}/100</div>
                <div class="cps-kpi-sub" id="kpiPerfSub">{{ $advCaching ? 'Redis Cache Active' : 'Cache Paused' }}</div>
            </div>
        </div>
    </div>

    {{-- TIER 2: PURE WHITE SUB-TABS CARD (SINGLE ACTIVE #009EF7 HIGHLIGHT) --}}
    <div class="cps-tabs-card">
        <div class="cps-tabs-group" role="tablist">
            <button type="button" class="cps-tab-btn cps-tab active" data-tab="general">
                <i class="ti ti-settings-2"></i>
                <span>General</span>
                <span class="cps-tab-badge {{ $isActive ? 'badge-live' : '' }}" id="tabBadgeGeneral">{{ $isActive ? 'Live' : 'Off' }}</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="hero">
                <i class="ti ti-layout-navbar"></i>
                <span>Hero Section</span>
                <span class="cps-tab-badge">{{ ucfirst($careerSettings->hero_layout ?? 'left-right') }}</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="company">
                <i class="ti ti-building"></i>
                <span>Company Info</span>
                <span class="cps-tab-badge">Profile</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="benefits">
                <i class="ti ti-gift"></i>
                <span>Benefits</span>
                <span class="cps-tab-badge badge-blue cps-num">{{ $benefitsCount }}</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="jobs">
                <i class="ti ti-briefcase"></i>
                <span>Job Settings</span>
                <span class="cps-tab-badge cps-num">{{ $jsPerPage }}/pg</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="seo">
                <i class="ti ti-world"></i>
                <span>SEO & Social</span>
                <span class="cps-tab-badge">{{ str_contains($seoRobots, 'noindex') ? 'Hidden' : 'Index' }}</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="integrations">
                <i class="ti ti-plug"></i>
                <span>Integrations</span>
                <span class="cps-tab-badge badge-live cps-num">2 Active</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="advanced">
                <i class="ti ti-adjustments-horizontal"></i>
                <span>Advanced</span>
                <span class="cps-tab-badge cps-num" id="tabBadgePerf">{{ $perfScore }}%</span>
            </button>
            <button type="button" class="cps-tab-btn cps-tab" data-tab="templates">
                <i class="ti ti-layout-grid-add"></i>
                <span>Templates Design</span>
                <span class="cps-tab-badge badge-blue" id="tabBadgeTemplate">{{ ucfirst($tdTpl) }}</span>
            </button>
        </div>
    </div>

    {{-- TIER 3: ENTERPRISE FORM + MODULAR TAB PANELS --}}
    <form id="careerSettingsForm" method="POST"
          action="{{ route('service_company.career.page.settings.save') }}"
          enctype="multipart/form-data">
        @csrf

        @include('service_company.career-page.tabs.general')
        @include('service_company.career-page.tabs.hero')
        @include('service_company.career-page.tabs.company')
        @include('service_company.career-page.tabs.benefits')
        @include('service_company.career-page.tabs.jobs')
        @include('service_company.career-page.tabs.seo')
        @include('service_company.career-page.tabs.integrations')
        @include('service_company.career-page.tabs.advanced')
        @include('service_company.career-page.tabs.templates')

        {{-- Persistent Workspace Bottom Action Bar --}}
        <div class="cps-bottom-bar">
            <div class="cps-bottom-meta">
                <span><i class="ti ti-shield-check" style="color:#10b981;"></i> Tenant Isolated (Company #{{ $company_id }})</span>
                <span>•</span>
                <span>Redis 24h Cached</span>
                <span>•</span>
                <span id="cpsLastSavedText">{{ !empty($careerSettings->updated_at) ? 'Updated ' . \Carbon\Carbon::parse($careerSettings->updated_at)->diffForHumans() : 'Ready to configure' }}</span>
            </div>
            <div class="cps-bottom-actions">
                <a href="{{ $careerUrl }}" target="_blank" class="cps-btn-secondary">
                    <i class="ti ti-eye" style="font-size:14px;"></i> Live Portal
                </a>
                <button type="submit" class="cps-btn-primary js-cps-save-btn">
                    <i class="ti ti-device-floppy" style="font-size:15px;"></i>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>
    </form>

</div>{{-- end .cps-page --}}

</div>
</div>
</div>
<!--end::Content-->

<script>
/* ── Global Tab Activation (Tier 2 + KPI Sync + Hash Deep-Link) ── */
function cpsActivateTab(tabKey) {
    document.querySelectorAll('.cps-tab-btn').forEach(function(t) {
        t.classList.toggle('active', t.dataset.tab === tabKey);
    });
    document.querySelectorAll('.cps-panel').forEach(function(p) {
        p.classList.toggle('active', p.id === 'panel-' + tabKey);
    });
    document.querySelectorAll('.cps-kpi-card').forEach(function(k) {
        k.classList.toggle('active', k.dataset.kpiTab === tabKey);
    });
    try {
        if (history.replaceState) {
            history.replaceState(null, '', '#tab-' + tabKey);
        }
    } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cps-tab-btn').forEach(function(tab) {
        tab.addEventListener('click', function() {
            cpsActivateTab(this.dataset.tab);
        });
    });

    // Restore tab from URL hash if present
    if (window.location.hash && window.location.hash.indexOf('#tab-') === 0) {
        var initialTab = window.location.hash.replace('#tab-', '');
        if (document.getElementById('panel-' + initialTab)) {
            cpsActivateTab(initialTab);
        }
    }

    // Dirty state tracking
    var formEl = document.getElementById('careerSettingsForm');
    if (formEl) {
        formEl.addEventListener('input', cpsMarkDirty);
        formEl.addEventListener('change', cpsMarkDirty);
    }

    // Sync primary/secondary colors between General and Templates Design tabs
    cpsBindColorSync();
    // Global settings search (300ms debounce)
    cpsInitGlobalSearch();
    // Integrations filter
    cpsInitIntegrationsFilter();
    // Initial live preview sync
    tdUpdatePreview();
    cpsRefreshKpiTelemetry();
});

function cpsMarkDirty() {
    var badge = document.getElementById('cpsDirtyBadge');
    if (badge) badge.classList.add('show');
    cpsRefreshKpiTelemetry();
}

function cpsRefreshKpiTelemetry() {
    var statusChk = document.querySelector('input[name="career_page_status"]');
    var maintChk = document.querySelector('input[name="adv_maintenance"]');
    var isLive = statusChk ? statusChk.checked : true;
    var isMaint = maintChk ? maintChk.checked : false;

    var headerPill = document.getElementById('cpsHeaderStatusPill');
    var headerText = document.getElementById('cpsHeaderStatusText');
    var kpiStatus = document.getElementById('kpiPortalStatus');
    var tabBadgeGen = document.getElementById('tabBadgeGeneral');

    if (headerPill && headerText) {
        headerPill.classList.remove('is-live', 'is-offline', 'is-maint');
        if (isMaint) {
            headerPill.classList.add('is-maint');
            headerText.textContent = 'Maintenance Mode';
        } else if (isLive) {
            headerPill.classList.add('is-live');
            headerText.textContent = 'Career Page Live';
        } else {
            headerPill.classList.add('is-offline');
            headerText.textContent = 'Offline';
        }
    }
    if (kpiStatus) {
        kpiStatus.textContent = isMaint ? 'Maintenance' : (isLive ? 'Live & Public' : 'Offline');
    }
    if (tabBadgeGen) {
        tabBadgeGen.textContent = isLive ? 'Live' : 'Off';
        tabBadgeGen.classList.toggle('badge-live', isLive);
    }

    // Performance score live calculation
    var cacheChk = document.querySelector('input[name="adv_caching"]');
    var cdnChk = document.querySelector('input[name="adv_cdn"]');
    var imgChk = document.querySelector('input[name="adv_img_opt"]');
    var score = 72 + (cacheChk && cacheChk.checked ? 10 : 0) + (cdnChk && cdnChk.checked ? 8 : 0) + (imgChk && imgChk.checked ? 7 : 0);
    var kpiPerf = document.getElementById('kpiPerfVal');
    var kpiPerfSub = document.getElementById('kpiPerfSub');
    var gaugeScore = document.getElementById('advGaugeScore');
    var tabPerf = document.getElementById('tabBadgePerf');
    if (kpiPerf) kpiPerf.textContent = score + '/100';
    if (kpiPerfSub) kpiPerfSub.textContent = (cacheChk && cacheChk.checked) ? 'Redis Cache Active' : 'Cache Paused';
    if (gaugeScore) gaugeScore.textContent = score;
    if (tabPerf) tabPerf.textContent = score + '%';
}

/* ── Global Settings Search (Client-First, 300ms Debounce) ── */
function cpsInitGlobalSearch() {
    var input = document.getElementById('cpsGlobalSearch');
    var clearBtn = document.getElementById('cpsSearchClear');
    var resultsBox = document.getElementById('cpsSearchResults');
    if (!input || !resultsBox) return;

    var indexItems = [];
    document.querySelectorAll('.cps-panel').forEach(function(panel) {
        var tabKey = panel.id.replace('panel-', '');
        var tabBtn = document.querySelector('.cps-tab-btn[data-tab="' + tabKey + '"] span');
        var tabLabel = tabBtn ? tabBtn.textContent.trim() : tabKey;
        panel.querySelectorAll('.cps-card').forEach(function(card, idx) {
            if (!card.id) card.id = 'cps-card-' + tabKey + '-' + idx;
            var titleEl = card.querySelector('.cps-card-title');
            var subEl = card.querySelector('.cps-card-sub');
            var textBlob = (card.textContent || '').replace(/\s+/g, ' ').trim();
            indexItems.push({
                tabKey: tabKey,
                tabLabel: tabLabel,
                cardId: card.id,
                title: titleEl ? titleEl.textContent.trim() : tabLabel,
                sub: subEl ? subEl.textContent.trim() : '',
                searchText: textBlob.toLowerCase()
            });
        });
    });

    var timer = null;
    input.addEventListener('input', function() {
        var val = this.value.trim();
        if (clearBtn) clearBtn.style.display = val.length ? 'flex' : 'none';
        clearTimeout(timer);
        timer = setTimeout(function() {
            if (!val) {
                resultsBox.classList.remove('open');
                resultsBox.innerHTML = '';
                return;
            }
            var q = val.toLowerCase();
            var matches = indexItems.filter(function(item) {
                return item.title.toLowerCase().indexOf(q) !== -1 || item.searchText.indexOf(q) !== -1;
            }).slice(0, 8);

            if (!matches.length) {
                resultsBox.innerHTML = '<div style="padding:10px;font-size:11.5px;color:#64748b;text-align:center;">No matching settings found.</div>';
            } else {
                resultsBox.innerHTML = matches.map(function(m) {
                    return '<div class="cps-search-item" data-tab="' + m.tabKey + '" data-card="' + m.cardId + '">' +
                        '<div style="min-width:0;">' +
                        '<div class="cps-search-item-title">' + cpsEscapeHtml(m.title) + '</div>' +
                        '<div class="cps-search-item-sub">' + cpsEscapeHtml(m.sub || 'Jump to setting section') + '</div>' +
                        '</div>' +
                        '<span class="cps-search-item-tab">' + cpsEscapeHtml(m.tabLabel) + '</span>' +
                        '</div>';
                }).join('');
            }
            resultsBox.classList.add('open');
        }, 300);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            clearBtn.style.display = 'none';
            resultsBox.classList.remove('open');
            resultsBox.innerHTML = '';
            input.focus();
        });
    }

    resultsBox.addEventListener('click', function(e) {
        var row = e.target.closest('.cps-search-item');
        if (!row) return;
        cpsActivateTab(row.dataset.tab);
        resultsBox.classList.remove('open');
        var card = document.getElementById(row.dataset.card);
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card.classList.add('cps-search-flash');
            setTimeout(function() { card.classList.remove('cps-search-flash'); }, 1500);
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cps-search-wrap')) {
            resultsBox.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            input.focus();
        }
    });
}

function cpsEscapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function(m) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
    });
}

/* ── Color Sync Between General & Templates Design ── */
function cpsBindColorSync() {
    var gp = document.getElementById('primaryPicker');
    var gt = document.getElementById('primaryText');
    var tp = document.getElementById('tdColorPrimary');
    var tt = document.getElementById('tdColorPrimaryText');

    function syncPrimary(val) {
        if (!/^#[0-9A-Fa-f]{6}$/.test(val)) return;
        if (gp) gp.value = val;
        if (gt) gt.value = val;
        if (tp) tp.value = val;
        if (tt) tt.value = val;
        updatePreview();
        tdUpdatePreview();
    }
    if (gp) gp.addEventListener('input', function() { syncPrimary(this.value); });
    if (gt) gt.addEventListener('input', function() { syncPrimary(this.value); });
    if (tp) tp.addEventListener('input', function() { syncPrimary(this.value); });
    if (tt) tt.addEventListener('input', function() { syncPrimary(this.value); });

    var gs = document.getElementById('secondaryPicker');
    var gst = document.getElementById('secondaryText');
    var ts = document.getElementById('tdColorSecondary');
    var tst = document.getElementById('tdColorSecondaryText');

    function syncSecondary(val) {
        if (!/^#[0-9A-Fa-f]{6}$/.test(val)) return;
        if (gs) gs.value = val;
        if (gst) gst.value = val;
        if (ts) ts.value = val;
        if (tst) tst.value = val;
        tdUpdatePreview();
    }
    if (gs) gs.addEventListener('input', function() { syncSecondary(this.value); });
    if (gst) gst.addEventListener('input', function() { syncSecondary(this.value); });
    if (ts) ts.addEventListener('input', function() { syncSecondary(this.value); });
    if (tst) tst.addEventListener('input', function() { syncSecondary(this.value); });
}

/* ── Layout & Display cards ── */
function selectLayout(key, el) {
    document.querySelectorAll('.cps-layout-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('pageLayoutInput');
    if (inp) inp.value = key;
    cpsMarkDirty();
}

function selectDisplay(key, el) {
    document.querySelectorAll('.cps-display-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('jobsDisplayInput');
    if (inp) inp.value = key;
    var jsSel = document.querySelector('select[name="js_display_style"]');
    if (jsSel && (key === 'card' || key === 'list')) jsSel.value = key;
    var kpiJobs = document.getElementById('kpiJobsVal');
    if (kpiJobs) kpiJobs.textContent = key.charAt(0).toUpperCase() + key.slice(1) + ' View';
    cpsMarkDirty();
}

/* ── Copy URL ── */
function copyCareerUrl() {
    var input = document.getElementById('careerPageUrl');
    if (!input) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value).then(function() {
            toastr.success('Career page URL copied!');
        }).catch(function() {
            input.select();
            document.execCommand('copy');
            toastr.success('Career page URL copied!');
        });
    } else {
        input.select();
        try { document.execCommand('copy'); toastr.success('Career page URL copied!'); } catch (e) {}
    }
}

/* ── Logo preview ── */
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var el = document.getElementById('logoPreviewImg');
            if (!el) return;
            if (el.tagName === 'IMG') {
                el.src = e.target.result;
            } else {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'cps-logo-img';
                img.id = 'logoPreviewImg';
                el.parentNode.replaceChild(img, el);
            }
            var lbl = document.getElementById('generalLogoName');
            if (lbl) lbl.textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
        cpsMarkDirty();
    }
}

/* ── Live preview color ── */
function updatePreview() {
    var textEl = document.getElementById('primaryText');
    var hdrEl = document.getElementById('previewHeader');
    if (!textEl || !hdrEl) return;
    var c = textEl.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(c)) {
        hdrEl.style.background = 'linear-gradient(135deg,' + c + ',#7C3AED)';
    }
}

/* ── Hero Section Helpers ── */
function selectHeroLayout(key, el) {
    document.querySelectorAll('.hs-layout-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var input = document.getElementById('heroLayoutInput');
    if (input) input.value = key;
    var prevHero = document.getElementById('hsHeroPreview');
    if (prevHero) prevHero.dataset.layout = key;
    cpsMarkDirty();
}

document.addEventListener('DOMContentLoaded', function() {
    var heroBadge = document.getElementById('heroBadge');
    var heroHeading = document.getElementById('heroHeading');
    var heroDesc = document.getElementById('heroDesc');
    var cta1 = document.querySelector('input[name="hero_cta1_text"]');
    var cta2 = document.querySelector('input[name="hero_cta2_text"]');

    if (heroBadge) {
        heroBadge.addEventListener('input', function() {
            var el = document.querySelector('#hsPreviewBadge span');
            if (el) el.textContent = this.value || 'Hiring Top Talent';
        });
    }
    if (heroHeading) {
        heroHeading.addEventListener('input', function() {
            var el = document.getElementById('hsPreviewHeading');
            if (el) el.textContent = this.value.replace(/<[^>]*>/g, '') || 'Build the Future';
        });
    }
    if (heroDesc) {
        heroDesc.addEventListener('input', function() {
            var el = document.getElementById('hsPreviewDesc');
            if (el) el.textContent = this.value || 'Join our team of innovators and help shape the future of technology.';
        });
    }
    if (cta1) {
        cta1.addEventListener('input', function() {
            var el = document.getElementById('hsPreviewCta1');
            if (el) el.textContent = this.value || 'Explore Roles';
        });
    }
    if (cta2) {
        cta2.addEventListener('input', function() {
            var el = document.getElementById('hsPreviewCta2');
            if (el) el.textContent = this.value || 'Learn More';
        });
    }

    // Company Info live preview sync
    var ciName = document.querySelector('input[name="ci_company_name"]');
    var ciHq = document.querySelector('input[name="ci_headquarters"]');
    var ciTag = document.getElementById('ciTagline');
    var ciWeb = document.querySelector('input[name="ci_website"]');
    var ciYear = document.querySelector('input[name="ci_founded_year"]');
    var ciSize = document.querySelector('select[name="ci_company_size"]');
    var ciAbout = document.getElementById('ciAbout');

    if (ciName) ciName.addEventListener('input', function() {
        var el = document.getElementById('ciPrevName');
        if (el) el.textContent = this.value || 'Company';
    });
    if (ciHq) ciHq.addEventListener('input', function() {
        var el = document.getElementById('ciPrevHq');
        if (el) el.textContent = this.value || 'Global Headquarters';
    });
    if (ciTag) ciTag.addEventListener('input', function() {
        var el = document.getElementById('ciPrevTagline');
        if (el) el.textContent = this.value || 'Building high-performing teams with trust and innovation.';
    });
    if (ciWeb) ciWeb.addEventListener('input', function() {
        var el = document.getElementById('ciPrevWeb');
        if (el) el.textContent = (this.value || '').replace(/^https?:\/\//i, '') || 'careers.company.com';
    });
    if (ciYear) ciYear.addEventListener('input', function() {
        var el = document.getElementById('ciPrevYear');
        var statEl = document.getElementById('ciPrevStatYear');
        if (el) el.textContent = 'Founded ' + (this.value || '2020');
        if (statEl) statEl.textContent = this.value || '2020';
    });
    if (ciSize) ciSize.addEventListener('change', function() {
        var el = document.getElementById('ciPrevSize');
        if (el) el.textContent = this.value;
    });
    if (ciAbout) ciAbout.addEventListener('input', function() {
        var el = document.getElementById('ciPrevAbout');
        if (el) el.textContent = this.value || 'Tell candidates about your mission, engineering culture, and growth opportunities.';
    });

    // Benefits live preview sync
    var bnTitle = document.getElementById('bnTitle');
    var bnDesc = document.getElementById('bnDesc');
    if (bnTitle) bnTitle.addEventListener('input', function() {
        var el = document.getElementById('bnPreviewTitle');
        if (el) el.textContent = this.value || 'Why Work With Us';
    });
    if (bnDesc) bnDesc.addEventListener('input', function() {
        var el = document.getElementById('bnPreviewDesc');
        if (el) el.textContent = this.value || 'Explore the benefits our team enjoys.';
    });

    // SEO live preview sync
    var seoTitle = document.getElementById('seoMetaTitle');
    var seoDesc = document.getElementById('seoMetaDesc');
    var seoRobots = document.querySelector('select[name="seo_robots"]');
    if (seoTitle) seoTitle.addEventListener('input', function() {
        var t1 = document.getElementById('seoPreviewTitle');
        var t2 = document.getElementById('seoOgPreviewTitle');
        if (t1) t1.textContent = this.value;
        if (t2) t2.textContent = this.value;
    });
    if (seoDesc) seoDesc.addEventListener('input', function() {
        var d1 = document.getElementById('seoPreviewDesc');
        var d2 = document.getElementById('seoOgPreviewDesc');
        if (d1) d1.textContent = this.value;
        if (d2) d2.textContent = this.value;
    });
    if (seoRobots) seoRobots.addEventListener('change', function() {
        var kpiSeo = document.getElementById('kpiSeoVal');
        if (kpiSeo) kpiSeo.textContent = this.value.indexOf('noindex') !== -1 ? 'No-Index' : 'Search Indexed';
    });

    // Job settings KPI sync
    var jsDisplay = document.querySelector('select[name="js_display_style"]');
    var jsPerPage = document.querySelector('select[name="js_per_page"]');
    if (jsDisplay) jsDisplay.addEventListener('change', function() {
        var kpiJobs = document.getElementById('kpiJobsVal');
        if (kpiJobs) kpiJobs.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1) + ' View';
    });
    if (jsPerPage) jsPerPage.addEventListener('change', function() {
        var kpiJobsSub = document.getElementById('kpiJobsSub');
        if (kpiJobsSub) kpiJobsSub.textContent = this.value + ' Jobs / Page';
    });
});

function selectGradient(el, val) {
    document.querySelectorAll('.hs-grad-swatch').forEach(function(s) { s.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('heroGradientInput');
    var prev = document.getElementById('hsHeroPreview');
    if (inp) inp.value = val;
    if (prev) prev.style.background = val;
    cpsMarkDirty();
}

function removeHsTag(el) {
    el.parentElement.remove();
    cpsMarkDirty();
}

function addHsTag() {
    var text = prompt('Enter highlight point:');
    if (!text || !text.trim()) return;
    var wrap = document.getElementById('hsTagsWrap');
    var tag = document.createElement('div');
    tag.className = 'hs-tag';
    tag.innerHTML = '<span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>' +
        cpsEscapeHtml(text.trim()) +
        '<span class="hs-tag-del" onclick="removeHsTag(this)">×</span>';
    wrap.appendChild(tag);
    cpsMarkDirty();
}

function previewHeroIllus(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var box = document.querySelector('.hs-prev-img');
            if (box) box.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">';
        };
        reader.readAsDataURL(input.files[0]);
        cpsMarkDirty();
    }
}

function resetHeroSection() {
    var b = document.querySelector('[name="hero_badge"]');
    var h = document.querySelector('[name="hero_heading"]');
    var d = document.querySelector('[name="hero_description"]');
    if (b) b.value = '';
    if (h) h.value = '';
    if (d) d.value = '';
    if (document.getElementById('heroBadgeCount')) document.getElementById('heroBadgeCount').textContent = '0/80';
    if (document.getElementById('heroHeadingCount')) document.getElementById('heroHeadingCount').textContent = '0/80';
    if (document.getElementById('heroDescCount')) document.getElementById('heroDescCount').textContent = '0/200';
    selectGradient(document.querySelector('.hs-grad-swatch'), 'linear-gradient(135deg,#4F46E5,#7C3AED)');
    toastr.info('Hero section reset to default preview.');
}

/* ── Company Info Helpers ── */
function ciPreviewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var box = document.getElementById('ciLogoBox');
            if (box) box.innerHTML = '<img src="' + e.target.result + '" class="ci-logo-img" style="width:54px;height:54px;">';
            var nm = document.getElementById('ciLogoName');
            if (nm) nm.textContent = input.files[0].name;
            var prevCircle = document.querySelector('.ci-prev-logo-circle');
            if (prevCircle) prevCircle.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:contain;">';
        };
        reader.readAsDataURL(input.files[0]);
        cpsMarkDirty();
    }
}

function ciDelLogo() {
    var box = document.getElementById('ciLogoBox');
    var nm = document.getElementById('ciLogoName');
    if (box) box.innerHTML = '<i class="ti ti-building" style="font-size:22px;color:#009ef7;"></i>';
    if (nm) nm.textContent = 'No logo uploaded';
    cpsMarkDirty();
}

function ciAddHighlight() {
    var label = prompt('Highlight label (e.g. Offices):');
    if (!label || !label.trim()) return;
    var val = prompt('Value (e.g. 10+):') || '';
    var list = document.getElementById('ciHighlightsList');
    var row = document.createElement('div');
    row.className = 'ci-hl-row';
    row.innerHTML = '<div class="ci-hl-left"><div class="ci-hl-icon" style="background:#EFF6FF;">' +
        '<i class="ti ti-star" style="font-size:15px;color:#009ef7;"></i></div>' +
        '<div><div class="ci-hl-label">' + cpsEscapeHtml(label.trim()) + '</div></div></div>' +
        '<div style="display:flex;align-items:center;gap:10px;"><div class="ci-hl-val">' + cpsEscapeHtml(val.trim()) + '</div>' +
        '<button type="button" class="ci-hl-del" onclick="this.closest(\'.ci-hl-row\').remove()">' +
        '<i class="ti ti-trash" style="font-size:12px;"></i></button></div>';
    list.appendChild(row);
    cpsMarkDirty();
}

/* ── Advanced Helpers & Client-First JSON Export ── */
function switchCodeTab(el, targetId) {
    document.querySelectorAll('.adv-code-tab').forEach(function(t) { t.classList.remove('active'); });
    el.classList.add('active');
    ['adv-code-header', 'adv-code-footer', 'adv-code-body'].forEach(function(id) {
        var box = document.getElementById(id);
        if (box) box.style.display = id === targetId ? 'block' : 'none';
    });
}

function cpsExportSettingsJson() {
    var form = document.getElementById('careerSettingsForm');
    if (!form) return;
    var payload = {};
    var fd = new FormData(form);
    fd.forEach(function(val, key) {
        if (key === '_token' || val instanceof File) return;
        payload[key] = val;
    });
    payload._exported_at = new Date().toISOString();
    var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'career-page-settings-' + new Date().toISOString().slice(0, 10) + '.json';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
    toastr.success('Career page settings exported in browser RAM.');
}

/* ── Integrations Search & Category Filter (Client-First) ── */
function cpsInitIntegrationsFilter() {
    var searchInp = document.getElementById('intSearchInput');
    var catSelect = document.getElementById('intCatSelect');
    var tabs = document.querySelectorAll('.int-tab');
    var cards = document.querySelectorAll('.int-card');
    if (!cards.length) return;

    function filterInt() {
        var q = searchInp ? searchInp.value.trim().toLowerCase() : '';
        var activeTab = document.querySelector('.int-tab.active');
        var cat = activeTab ? (activeTab.dataset.cat || 'all') : 'all';
        cards.forEach(function(card) {
            var name = (card.dataset.name || '').toLowerCase();
            var cardCat = (card.dataset.cat || '').toLowerCase();
            var matchQ = !q || name.indexOf(q) !== -1 || card.textContent.toLowerCase().indexOf(q) !== -1;
            var matchCat = cat === 'all' || cardCat === cat.toLowerCase();
            card.style.display = (matchQ && matchCat) ? 'flex' : 'none';
        });
    }

    if (searchInp) searchInp.addEventListener('input', filterInt);
    if (catSelect) {
        catSelect.addEventListener('change', function() {
            var val = this.value;
            tabs.forEach(function(t) {
                t.classList.toggle('active', (t.dataset.cat || 'all') === val);
            });
            filterInt();
        });
    }
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            if (catSelect) catSelect.value = this.dataset.cat || 'all';
            filterInt();
        });
    });
}

/* ── SEO Helpers ── */
function seoAddSocialRow() {
    var list = document.getElementById('seoSocialList');
    if (!list) return;
    var row = document.createElement('div');
    row.className = 'seo-social-row';
    row.innerHTML = '<div class="seo-social-icon" style="background:#F8FAFC;"><i class="ti ti-share" style="color:#64748B;font-size:15px;"></i></div>' +
        '<input type="text" class="seo-social-input" style="width:90px;flex:none;" placeholder="Platform" value="">' +
        '<input type="url" class="seo-social-input" placeholder="https://" value="">' +
        '<label class="cps-switch" style="flex-shrink:0;"><input type="checkbox" value="1" checked><span class="cps-slider"></span></label>';
    list.appendChild(row);
    cpsMarkDirty();
}

/* ── Benefits Helpers ── */
function selectBnStyle(key, el) {
    document.querySelectorAll('.bn-style-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('bnStyleInput');
    if (inp) inp.value = key;
    cpsMarkDirty();
}

function bnAddBenefit() {
    var title = prompt('Benefit title:');
    if (!title || !title.trim()) return;
    var desc = prompt('Short description:') || '';
    var list = document.getElementById('bnBenefitsList');
    var row = document.createElement('div');
    row.className = 'bn-benefit-row';
    row.innerHTML = '<span class="bn-drag"><i class="ti ti-grip-vertical"></i></span>' +
        '<div class="bn-benefit-icon" style="background:#EFF6FF;"><i class="ti ti-star" style="font-size:16px;color:#009ef7;"></i></div>' +
        '<div class="bn-benefit-info"><div class="bn-benefit-title">' + cpsEscapeHtml(title.trim()) + '</div>' +
        '<div class="bn-benefit-desc">' + cpsEscapeHtml(desc.trim()) + '</div></div>' +
        '<div class="bn-benefit-actions">' +
        '<button type="button" class="bn-act-btn eye"><i class="ti ti-eye"></i></button>' +
        '<button type="button" class="bn-act-btn edit"><i class="ti ti-pencil"></i></button>' +
        '<button type="button" class="bn-act-btn del" onclick="this.closest(\'.bn-benefit-row\').remove()"><i class="ti ti-trash"></i></button>' +
        '</div>';
    list.appendChild(row);
    cpsMarkDirty();
}

/* ── Template Design Helpers ── */
var tdTemplatePresets = {
    modern:    { primary:'#2563EB', secondary:'#1F2937', accent:'#10B981', bg:'#FFFFFF' },
    corporate: { primary:'#1F2937', secondary:'#374151', accent:'#3B82F6', bg:'#FFFFFF' },
    creative:  { primary:'#7C3AED', secondary:'#EC4899', accent:'#F59E0B', bg:'#FFFFFF' },
    minimal:   { primary:'#64748B', secondary:'#94A3B8', accent:'#2563EB', bg:'#F8FAFC' },
    tech:      { primary:'#38BDF8', secondary:'#1E293B', accent:'#818CF8', bg:'#0F172A' }
};

function tdSelectTemplate(key, el) {
    document.querySelectorAll('.td-tpl-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('tdTemplateInput');
    if (inp) inp.value = key;
    var kpiTpl = document.getElementById('kpiTemplateVal');
    var tabTpl = document.getElementById('tabBadgeTemplate');
    var label = key.charAt(0).toUpperCase() + key.slice(1);
    if (kpiTpl) kpiTpl.textContent = label + ' Theme';
    if (tabTpl) tabTpl.textContent = label;

    var preset = tdTemplatePresets[key];
    if (!preset) return;
    var fields = {
        tdColorPrimary:   preset.primary,
        tdColorSecondary: preset.secondary,
        tdColorAccent:    preset.accent,
        tdColorBg:        preset.bg
    };
    Object.keys(fields).forEach(function(id) {
        var swatch = document.getElementById(id);
        var text   = document.getElementById(id + 'Text');
        if (swatch) swatch.value = fields[id];
        if (text)   text.value   = fields[id];
    });
    if (document.getElementById('primaryPicker')) document.getElementById('primaryPicker').value = preset.primary;
    if (document.getElementById('primaryText')) document.getElementById('primaryText').value = preset.primary;
    if (document.getElementById('secondaryPicker')) document.getElementById('secondaryPicker').value = preset.secondary;
    if (document.getElementById('secondaryText')) document.getElementById('secondaryText').value = preset.secondary;

    updatePreview();
    tdUpdatePreview();
    cpsMarkDirty();
}

function tdSwitchDesignTab(key, el) {
    document.querySelectorAll('.td-design-nav-item').forEach(function(n) { n.classList.remove('active'); });
    if (el) el.classList.add('active');
    document.querySelectorAll('.td-design-tab').forEach(function(t) { t.style.display = 'none'; });
    var t = document.getElementById('td-design-' + key);
    if (t) t.style.display = 'block';
}

function tdSelectLayout(key, el) {
    document.querySelectorAll('.td-layout-card').forEach(function(c) { c.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var inp = document.getElementById('tdLayoutInput');
    if (inp) inp.value = key;
    cpsMarkDirty();
}

function tdSwitchDevice(device, el) {
    document.querySelectorAll('.td-prev-device-btn').forEach(function(b) { b.classList.remove('active'); });
    if (el) el.classList.add('active');
    var frame = document.getElementById('tdPreviewFrame');
    if (!frame) return;
    if (device === 'desktop') frame.style.maxWidth = '100%';
    else if (device === 'tablet') frame.style.maxWidth = '420px';
    else frame.style.maxWidth = '270px';
}

function syncColorSwatch(swatchId, textInput) {
    var val = textInput.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        var sw = document.getElementById(swatchId);
        if (sw) sw.value = val;
    }
}

function tdUpdatePreview() {
    var pEl = document.getElementById('tdColorPrimaryText');
    var sEl = document.getElementById('tdColorSecondaryText');
    var aEl = document.getElementById('tdColorAccentText');
    var bEl = document.getElementById('tdColorBgText');
    var frame = document.getElementById('tdPreviewFrame');
    if (!frame || !pEl || !sEl || !aEl || !bEl) return;

    var p = pEl.value || '#2563EB';
    var s = sEl.value || '#1F2937';
    var a = aEl.value || '#10B981';
    var b = bEl.value || '#FFFFFF';

    frame.style.setProperty('--tdP', p);
    frame.style.setProperty('--tdS', s);
    frame.style.setProperty('--tdA', a);
    frame.style.background = b;

    var hero = frame.querySelector('.td-prev-hero');
    if (hero) hero.style.background = 'linear-gradient(135deg,' + p + ',' + s + ')';
    var navBtn = frame.querySelector('.td-prev-nav-btn');
    if (navBtn) navBtn.style.background = p;
    var srchBtn = frame.querySelector('.td-prev-hero-search-btn');
    if (srchBtn) srchBtn.style.background = p;
    var cta = frame.querySelector('.td-prev-cta');
    if (cta) cta.style.background = 'linear-gradient(135deg,' + p + ',' + s + ')';
    var ctaBtn = frame.querySelector('.td-prev-cta-btn');
    if (ctaBtn) ctaBtn.style.color = p;
}

function tdPreviewAsset(input, thumbId) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var box = document.getElementById(thumbId);
        if (box) box.innerHTML = '<img src="' + e.target.result + '" style="max-width:52px;max-height:34px;object-fit:contain;">';
    };
    reader.readAsDataURL(input.files[0]);
    cpsMarkDirty();
}

/* ── Form submit (AJAX) ── */
document.getElementById('careerSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btns = document.querySelectorAll('.js-cps-save-btn');
    btns.forEach(function(b) {
        b.disabled = true;
        b.dataset.origHtml = b.innerHTML;
        b.innerHTML = '<i class="ti ti-loader-2" style="font-size:14px;"></i> <span>Saving...</span>';
    });

    var fd = new FormData(this);
    $.ajax({
        url: this.action,
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status === 'success') {
                toastr.success(res.message || 'Career page settings saved successfully!');
                var dirtyBadge = document.getElementById('cpsDirtyBadge');
                if (dirtyBadge) dirtyBadge.classList.remove('show');
                var savedTxt = document.getElementById('cpsLastSavedText');
                if (savedTxt) savedTxt.textContent = 'Saved just now';
            } else {
                toastr.error(res.message || 'Could not save settings.');
            }
        },
        error: function() {
            toastr.error('Failed to save. Please try again.');
        },
        complete: function() {
            btns.forEach(function(b) {
                b.disabled = false;
                if (b.dataset.origHtml) b.innerHTML = b.dataset.origHtml;
            });
        }
    });
});
</script>
@endsection
