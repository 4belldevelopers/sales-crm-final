{{--
  JERO ATS — Service Company Joining Tracker (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Enterprise-grade, page-isolated Joining Tracker featuring Client-Tracker-inspired
          interactive 6-card KPI strip, Rule 20 Tier 1 Search/Filter Toolbar & Tier 2 Sub-Tabs,
          and Tier 3 Card + Discrete Sub-Card stream powered by Elasticsearch projections
          (cvtracker.allcandidates, status_cus=6), 3-tier Browser RAM/IndexedDB/ES caching,
          Client-First CSV/PDF export, and real-time Reverb + BroadcastChannel sync.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id across IndexedDB keys,
                      BroadcastChannel, and Reverb private channels. Zero MySQL reads on listing path.
  Screen Fit: 100% fluid fit on 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}
@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* ==========================================================================
   Joining Tracker — Page-Isolated Enterprise Card + Sub-Card Design System
   Font: Poppins strictly throughout; tabular-nums for all metrics/currency
   ========================================================================== */
:root {
    --jt-primary: #009ef7;
    --jt-primary-deep: #2563eb;
    --jt-primary-soft: #eff6ff;
    --jt-success: #16a34a;
    --jt-success-soft: #ecfdf5;
    --jt-warning: #d97706;
    --jt-warning-soft: #fffbeb;
    --jt-orange: #ea580c;
    --jt-orange-soft: #fff7ed;
    --jt-danger: #dc2626;
    --jt-danger-soft: #fef2f2;
    --jt-indigo: #4f46e5;
    --jt-indigo-soft: #eef2ff;
    --jt-rose: #db2777;
    --jt-rose-soft: #fdf2f8;
    --jt-violet: #7c3aed;
    --jt-violet-soft: #f5f3ff;
    --jt-text-main: #0f172a;
    --jt-text-mid: #475569;
    --jt-text-muted: #64748b;
    --jt-text-light: #94a3b8;
    --jt-border: #e2e8f0;
    --jt-border-soft: #f1f5f9;
    --jt-bg-subcard: #f8fafc;
}

/* Remove legacy toolbar gap & guarantee 0% horizontal page scroll (Standardized Compact Spacing) */
#kt_toolbar { display: none !important; }
.kt_content_joiningtracker,
#kt_content.kt_content_joiningtracker,
#kt_content {
    margin-top: -65px !important;
    padding-top: 10px !important;
}
@media (max-width: 991.98px) {
    .kt_content_joiningtracker,
    #kt_content.kt_content_joiningtracker,
    #kt_content {
        margin-top: -55px !important;
        padding-top: 8px !important;
    }
}
#kt_post { margin-top: 0 !important; }
#kt_content_container,
body { overflow-x: hidden !important; }

.jt-dashboard,
.jt-dashboard * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.jt-dashboard {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 14px 24px !important;
    color: var(--jt-text-main);
    -webkit-font-smoothing: antialiased;
}

.jt-num,
.jt-kpi-num,
.jt-tab-badge,
.jt-comp-amount {
    font-variant-numeric: tabular-nums !important;
}

/* ==========================================================================
   HEADER CARD (Client Tracker Inspired Enterprise Header)
   ========================================================================== */
.jt-header-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 12px !important;
    padding: 13px 18px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.jt-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.jt-header-icon-box {
    width: 42px !important;
    height: 42px !important;
    border-radius: 10px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #2563eb !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08) !important;
}

.jt-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.jt-header-title {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
    line-height: 1.25 !important;
}

.jt-verified-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #16a34a !important;
}

.jt-header-count-pill {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px 9px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    background: var(--jt-primary-soft) !important;
    color: var(--jt-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    line-height: 1.3 !important;
}

.jt-header-subtitle {
    font-size: 12px !important;
    font-weight: 400 !important;
    color: var(--jt-text-muted) !important;
    margin-top: 2px !important;
    line-height: 1.35 !important;
}

.jt-header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    flex-wrap: wrap !important;
}

.jt-hdr-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 36px !important;
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 8px !important;
    padding: 0 13px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--jt-text-main) !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.jt-hdr-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--jt-primary-deep) !important;
}

.jt-hdr-btn.is-syncing i.fa-sync-alt {
    animation: jtSpin 0.8s linear infinite;
}

@keyframes jtSpin {
    to { transform: rotate(360deg); }
}

.jt-dropdown-menu {
    min-width: 245px !important;
    padding: 6px !important;
    border-radius: 10px !important;
    border: 1px solid var(--jt-border) !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
    background: #ffffff !important;
    margin-top: 6px !important;
    z-index: 1050 !important;
}

.jt-dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 10px !important;
    border-radius: 7px !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: background 0.12s ease !important;
}

.jt-dropdown-item:hover {
    background: #f1f5f9 !important;
}

.jt-drop-ico {
    width: 30px !important;
    height: 30px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    font-size: 13px !important;
}
.jt-drop-ico.csv { background: #eff6ff; color: #2563eb; }
.jt-drop-ico.pdf { background: #fef2f2; color: #dc2626; }
.jt-drop-ico.act { background: #eef2ff; color: #4f46e5; }
.jt-drop-ico.wrn { background: #fff7ed; color: #ea580c; }
.jt-drop-ico.rst { background: #fffbeb; color: #d97706; }

.jt-drop-title {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    display: block !important;
    line-height: 1.25 !important;
}

.jt-drop-sub {
    font-size: 10.5px !important;
    color: #64748b !important;
    display: block !important;
    margin-top: 1px !important;
}

/* ==========================================================================
   KPI STAT CARDS — Client Tracker Inspired (6 Cards in 1 Horizontal Row)
   ========================================================================== */
.jt-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 9px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

.jt-kpi-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 11px !important;
    padding: 10px 11px !important;
    min-height: 82px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease !important;
    min-width: 0 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    text-align: left !important;
    outline: none !important;
}

.jt-kpi-card:hover {
    transform: translateY(-1px) !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    text-decoration: none !important;
}

.jt-kpi-top {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 6px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.jt-kpi-icon {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    transition: all 0.15s ease !important;
}

.jt-kpi-icon svg {
    width: 17px !important;
    height: 17px !important;
}

.jt-kpi-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
.jt-kpi-icon.teal   { background: #f0fdfa !important; color: #0d9488 !important; }
.jt-kpi-icon.amber  { background: #fffbeb !important; color: #d97706 !important; }
.jt-kpi-icon.indigo { background: #eef2ff !important; color: #4f46e5 !important; }
.jt-kpi-icon.green  { background: #ecfdf5 !important; color: #16a34a !important; }
.jt-kpi-icon.orange { background: #fff7ed !important; color: #ea580c !important; }
.jt-kpi-icon.rose   { background: #fdf2f8 !important; color: #db2777 !important; }

.jt-kpi-meta {
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.jt-kpi-num-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 4px !important;
}

.jt-kpi-num {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
}

.jt-kpi-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--jt-text-muted) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.jt-kpi-pulse-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 1.5px 6px !important;
    background: #ede9fe !important;
    border: 1px solid #ddd6fe !important;
    border-radius: 999px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    color: #6d28d9 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.jt-kpi-dot-pulse {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #7c3aed !important;
    display: inline-block !important;
    animation: jtPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes jtPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.45; transform: scale(1.2); }
}

.jt-kpi-sub {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 2px !important;
}

.jt-kpi-sub svg { width: 11px !important; height: 11px !important; flex-shrink: 0 !important; }
.jt-kpi-sub.slate  { color: #64748b !important; }
.jt-kpi-sub.teal   { color: #0d9488 !important; }
.jt-kpi-sub.amber  { color: #d97706 !important; }
.jt-kpi-sub.indigo { color: #4f46e5 !important; }
.jt-kpi-sub.green  { color: #16a34a !important; }
.jt-kpi-sub.orange { color: #ea580c !important; }
.jt-kpi-sub.rose   { color: #db2777 !important; }

/* ── Client Tracker Style Active KPI Highlight States ── */
.jt-kpi-card.active[data-tab="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.jt-kpi-card.active[data-tab="all"] .jt-kpi-icon { background: #ffffff !important; color: #2563eb !important; box-shadow: 0 1px 3px rgba(37, 99, 235, 0.16) !important; }
.jt-kpi-card.active[data-tab="all"] .jt-kpi-num  { color: #1d4ed8 !important; }
.jt-kpi-card.active[data-tab="all"] .jt-kpi-lbl  { color: #1e40af !important; font-weight: 700 !important; }

.jt-kpi-card.active[data-tab="last_3_months"] {
    background: #f0fdfa !important;
    border-color: #5eead4 !important;
    box-shadow: 0 0 0 1.5px #0d9488, 0 4px 14px rgba(13, 148, 136, 0.12) !important;
}
.jt-kpi-card.active[data-tab="last_3_months"] .jt-kpi-icon { background: #ffffff !important; color: #0d9488 !important; box-shadow: 0 1px 3px rgba(13, 148, 136, 0.16) !important; }
.jt-kpi-card.active[data-tab="last_3_months"] .jt-kpi-num  { color: #0f766e !important; }
.jt-kpi-card.active[data-tab="last_3_months"] .jt-kpi-lbl  { color: #115e59 !important; font-weight: 700 !important; }

.jt-kpi-card.active[data-tab="this_month"] {
    background: #fffbeb !important;
    border-color: #fde047 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.12) !important;
}
.jt-kpi-card.active[data-tab="this_month"] .jt-kpi-icon { background: #ffffff !important; color: #d97706 !important; box-shadow: 0 1px 3px rgba(217, 119, 6, 0.16) !important; }
.jt-kpi-card.active[data-tab="this_month"] .jt-kpi-num  { color: #b45309 !important; }
.jt-kpi-card.active[data-tab="this_month"] .jt-kpi-lbl  { color: #92400e !important; font-weight: 700 !important; }

.jt-kpi-card.active[data-tab="joining_today"] {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 1.5px #4f46e5, 0 4px 14px rgba(79, 70, 229, 0.12) !important;
}
.jt-kpi-card.active[data-tab="joining_today"] .jt-kpi-icon { background: #ffffff !important; color: #4f46e5 !important; box-shadow: 0 1px 3px rgba(79, 70, 229, 0.16) !important; }
.jt-kpi-card.active[data-tab="joining_today"] .jt-kpi-num  { color: #3730a3 !important; }
.jt-kpi-card.active[data-tab="joining_today"] .jt-kpi-lbl  { color: #4338ca !important; font-weight: 700 !important; }

.jt-kpi-card.active[data-tab="onboarded"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #16a34a, 0 4px 14px rgba(22, 163, 74, 0.13) !important;
}
.jt-kpi-card.active[data-tab="onboarded"] .jt-kpi-icon { background: #ffffff !important; color: #16a34a !important; box-shadow: 0 1px 3px rgba(22, 163, 74, 0.16) !important; }
.jt-kpi-card.active[data-tab="onboarded"] .jt-kpi-num  { color: #15803d !important; }
.jt-kpi-card.active[data-tab="onboarded"] .jt-kpi-lbl  { color: #166534 !important; font-weight: 700 !important; }

.jt-kpi-card.active[data-tab="backed_out"] {
    background: #fdf2f8 !important;
    border-color: #f472b6 !important;
    box-shadow: 0 0 0 1.5px #db2777, 0 4px 14px rgba(219, 39, 119, 0.14) !important;
}
.jt-kpi-card.active[data-tab="backed_out"] .jt-kpi-icon { background: #ffffff !important; color: #db2777 !important; box-shadow: 0 1px 3px rgba(219, 39, 119, 0.16) !important; }
.jt-kpi-card.active[data-tab="backed_out"] .jt-kpi-num  { color: #9d174d !important; }
.jt-kpi-card.active[data-tab="backed_out"] .jt-kpi-lbl  { color: #831843 !important; font-weight: 700 !important; }

/* ==========================================================================
   RULE 20 TIER 1 & TIER 2: TOOLBAR & DEDICATED SUB-TABS CARDS
   ========================================================================== */
.jt-toolbar-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    margin-bottom: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.jt-search-subcard {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex: 1 1 280px !important;
    min-width: 220px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 8px !important;
    padding: 0 10px 0 12px !important;
    transition: all 0.15s ease !important;
}

.jt-search-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.jt-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: var(--jt-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.jt-search-subcard i.fa-search {
    color: var(--jt-text-light) !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.jt-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    color: var(--jt-text-main) !important;
    width: 100% !important;
    height: 34px !important;
    padding: 0 !important;
}

.jt-search-subcard input::placeholder {
    color: var(--jt-text-light) !important;
    font-size: 12px !important;
    font-weight: 400 !important;
}

.jt-search-clear-btn {
    display: none !important;
}

.jt-search-subcard input::-webkit-search-decoration,
.jt-search-subcard input::-webkit-search-cancel-button,
.jt-search-subcard input::-webkit-search-results-button,
.jt-search-subcard input::-webkit-search-results-decoration {
    display: none !important;
    -webkit-appearance: none;
}

.jt-search-kbd {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 4px !important;
    padding: 2px 5px !important;
    line-height: 1 !important;
    flex-shrink: 0 !important;
    user-select: none !important;
}

.jt-inline-filters {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
}

.jt-filter-pill {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    transition: all 0.15s ease !important;
}

.jt-filter-pill:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.jt-filter-pill:focus-within {
    background: #ffffff !important;
    border-color: var(--jt-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.jt-filter-pill-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--jt-text-muted) !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.jt-filter-select {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--jt-text-main) !important;
    height: 34px !important;
    padding-right: 6px !important;
    cursor: pointer !important;
}

.jt-reset-btn {
    height: 38px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    border: 1px solid var(--jt-border) !important;
    background: #ffffff !important;
    color: var(--jt-primary-deep) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.jt-reset-btn:hover {
    background: var(--jt-primary-soft) !important;
    border-color: #bfdbfe !important;
}

/* Tier 2: Dedicated White Sub-Tabs Card */
.jt-tabs-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.jt-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
}

.jt-tab-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 11px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: var(--jt-text-mid) !important;
    border: 1px solid var(--jt-border) !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.jt-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--jt-text-main) !important;
}

/* Strict Rule 20 Single Active Primary Blue Highlight */
.jt-tab-btn.active {
    background: var(--jt-primary) !important;
    color: #ffffff !important;
    border-color: var(--jt-primary) !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.jt-tab-badge {
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
    color: var(--jt-text-mid) !important;
    line-height: 1 !important;
}

.jt-tab-btn.active .jt-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.jt-tab-btn:not(.active) .jt-tab-badge.badge-today     { background: #ede9fe !important; color: #6d28d9 !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-week      { background: #eff6ff !important; color: #1d4ed8 !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-delayed   { background: #ffedd5 !important; color: #c2410c !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-noshow    { background: #fee2e2 !important; color: #b91c1c !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-docs      { background: #fef3c7 !important; color: #b45309 !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-onboarded { background: #dcfce7 !important; color: #15803d !important; }
.jt-tab-btn:not(.active) .jt-tab-badge.badge-backedout { background: #fdf2f8 !important; color: #be185d !important; }

.jt-tabs-right-meta {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 11px !important;
    color: var(--jt-text-muted) !important;
    font-weight: 500 !important;
    flex-shrink: 0 !important;
}

.jt-live-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 3px 9px !important;
    border-radius: 999px !important;
    background: #f0fdf4 !important;
    border: 1px solid #bbf7d0 !important;
    color: #15803d !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
}

.jt-live-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #16a34a !important;
    animation: jtPulse 2s infinite;
}

/* ==========================================================================
   RULE 20 TIER 3: PRIMARY JOINING CARD & 5 DISCRETE SUB-CARDS STREAM
   ========================================================================== */
.jt-stream-wrapper {
    position: relative !important;
    width: 100% !important;
    min-height: 160px !important;
}

.jt-cards-stream {
    width: 100% !important;
    max-width: 100% !important;
}

.jt-entity-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

.jt-entity-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px);
}

.jt-entity-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.05fr) minmax(0, 1.9fr) minmax(0, 1.3fr) minmax(0, 1.45fr) minmax(0, 1.45fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    align-items: stretch !important;
}

.jt-subcard {
    background: var(--jt-bg-subcard) !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 8px !important;
    padding: 8px 10px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    gap: 6px !important;
    min-width: 0 !important;
    position: relative !important;
}

.jt-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 5px !important;
    min-height: 18px !important;
}

.jt-subcard-label {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: var(--jt-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.jt-subcard-badges {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

.jt-micro-pill {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}

.jt-pill-neutral { background: #ffffff !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }
.jt-pill-bench   { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }

/* Sub-Card 1: Candidate */
.jt-cand-body {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    min-width: 0 !important;
}

.jt-avatar {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    border: 1px solid rgba(15, 23, 42, 0.06) !important;
}

.jt-avatar-img {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    object-fit: cover !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    border: 1px solid var(--jt-border) !important;
}

.jt-avatar.bg-light-danger  { background:#fee2e2 !important; color:#dc2626 !important; }
.jt-avatar.bg-light-warning { background:#fef3c7 !important; color:#d97706 !important; }
.jt-avatar.bg-light-primary { background:#e0e7ff !important; color:#4f46e5 !important; }
.jt-avatar.bg-light-success { background:#dcfce7 !important; color:#16a34a !important; }
.jt-avatar.bg-light-info    { background:#cffafe !important; color:#0891b2 !important; }
.jt-avatar.bg-dark, .jt-avatar.bg-light-secondary { background:#e2e8f0 !important; color:#475569 !important; }

.jt-cand-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.jt-cand-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
    cursor: pointer !important;
}

.jt-cand-name:hover {
    color: var(--jt-primary) !important;
}

.jt-cand-role {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: var(--jt-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
}

.jt-cand-loc {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10px !important;
    color: var(--jt-text-light) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 2: Requisition & Client */
.jt-scope-pill {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.jt-scope-direct   { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.jt-scope-internal { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }

.jt-job-status-pill {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.jt-pill-hiring  { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
.jt-pill-hold    { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.jt-pill-closed  { background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; }

.jt-job-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.jt-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.jt-job-meta {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    color: var(--jt-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    min-width: 0 !important;
}

.jt-job-id {
    font-weight: 700 !important;
    color: #0284c7 !important;
    flex-shrink: 0 !important;
}

.jt-job-client {
    font-weight: 600 !important;
    color: var(--jt-text-mid) !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    font-size: 10px !important;
}

.jt-job-tags {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: 2px !important;
}

.jt-tag-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: var(--jt-text-muted) !important;
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 4px !important;
    padding: 1px 6px !important;
}

/* Sub-Card 3: Compensation */
.jt-comp-body {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    gap: 3px !important;
    min-width: 0 !important;
}

.jt-comp-amount {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.jt-comp-sub {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: var(--jt-text-muted) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* Sub-Card 4: Joining Status */
.jt-status-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    min-width: 0 !important;
}

.jt-joining-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    width: fit-content !important;
    padding: 3px 9px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
}

.jt-joining-badge svg {
    width: 12px !important;
    height: 12px !important;
    flex-shrink: 0 !important;
}

.jt-joining-sub {
    font-size: 10.5px !important;
    color: var(--jt-text-muted) !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 5: Joining Date */
.jt-joining-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.jt-join-date {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
}

.jt-join-date i {
    color: var(--jt-primary-deep) !important;
    font-size: 11px !important;
}

.jt-join-time {
    font-size: 10px !important;
    color: var(--jt-text-muted) !important;
    font-weight: 500 !important;
}

.jt-join-empty {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #cbd5e1 !important;
    line-height: 1.2 !important;
}

/* Sub-Card 6: Bottom Strip */
.jt-entity-bottom {
    background: #ffffff !important;
    border-top: 1px solid var(--jt-border-soft) !important;
    padding: 7px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

.jt-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
    font-size: 10.5px !important;
    color: var(--jt-text-muted) !important;
}

.jt-bottom-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    min-width: 0 !important;
}

.jt-bottom-lbl {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.25px !important;
    color: var(--jt-text-light) !important;
}

.jt-bottom-strong {
    color: var(--jt-text-main) !important;
    font-weight: 600 !important;
}

.jt-bottom-muted {
    color: var(--jt-text-muted) !important;
    font-weight: 500 !important;
}

.jt-dot-sep {
    color: #cbd5e1 !important;
    font-size: 9px !important;
}

.jt-stage-btn {
    display: inline-flex !important;
    align-items: center !important;
    padding: 2px 9px !important;
    border-radius: 5px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

.jt-stage-btn:hover {
    background: #bbf7d0 !important;
    border-color: #4ade80 !important;
}

.jt-stage-disabled {
    background: #f8fafc !important;
    color: #94a3b8 !important;
    border-color: #e2e8f0 !important;
    cursor: default !important;
}

.jt-substage-pill {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1.5px 7px !important;
    border-radius: 4px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    background: #f8fafc !important;
    color: var(--jt-text-mid) !important;
    border: 1px solid var(--jt-border) !important;
}

.jt-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.jt-action-ghost {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    color: var(--jt-text-mid) !important;
    border: 1px solid var(--jt-border) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.jt-action-ghost:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--jt-text-main) !important;
}

.jt-action-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 11px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #eff6ff !important;
    color: var(--jt-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.jt-action-primary:hover {
    background: var(--jt-primary) !important;
    border-color: var(--jt-primary) !important;
    color: #ffffff !important;
}

/* Loading, Empty & Footer Cards */
.jt-loading-card {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 36px 20px !important;
    text-align: center !important;
    color: var(--jt-text-muted) !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    margin-bottom: 12px !important;
}

.jt-empty-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 10px !important;
    padding: 48px 20px !important;
    text-align: center !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    margin-bottom: 12px !important;
}

.jt-empty-icon {
    width: 50px !important;
    height: 50px !important;
    margin: 0 auto 12px !important;
    border-radius: 12px !important;
    background: var(--jt-primary-soft) !important;
    color: var(--jt-primary-deep) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.jt-empty-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: var(--jt-text-main) !important;
}

.jt-empty-sub {
    font-size: 12px !important;
    color: var(--jt-text-muted) !important;
    margin-top: 4px !important;
}

.jt-empty-reset-btn {
    margin-top: 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 7px 14px !important;
    border-radius: 8px !important;
    border: 1px solid #bfdbfe !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
}

.jt-footer-card {
    background: #ffffff !important;
    border: 1px solid var(--jt-border) !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}

.jt-footer-card .pagi-bar {
    border-top: none !important;
}

/* ==========================================================================
   15.6" SCREEN FIT & RESPONSIVE BREAKPOINTS (1366x768 & 1920x1080)
   ========================================================================== */
@media (max-width: 1440px) {
    .jt-kpi-grid { gap: 7px !important; }
    .jt-kpi-card { padding: 9px 9px !important; min-height: 78px !important; }
    .jt-kpi-top  { gap: 7px !important; margin-bottom: 5px !important; }
    .jt-kpi-icon { width: 30px !important; height: 30px !important; min-width: 30px !important; border-radius: 7px !important; }
    .jt-kpi-icon svg { width: 15px !important; height: 15px !important; }
    .jt-kpi-num  { font-size: 16.5px !important; }
    .jt-kpi-lbl  { font-size: 10px !important; }
    .jt-kpi-sub  { font-size: 9.5px !important; }

    .jt-entity-grid {
        grid-template-columns: minmax(0, 2fr) minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.45fr) minmax(0, 1.4fr) !important;
        gap: 7px !important;
        padding: 9px 11px !important;
    }
    .jt-subcard { padding: 7px 9px !important; }
    .jt-search-kbd { display: none !important; }
    .jt-tab-btn { padding: 5px 10px !important; font-size: 11px !important; }
}

@media (max-width: 1150px) {
    .jt-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    .jt-entity-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
}

@media (max-width: 768px) {
    .jt-dashboard { padding: 0 10px 20px !important; }
    .jt-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .jt-entity-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .jt-header-card { flex-direction: column !important; align-items: flex-start !important; }
    .jt-header-actions { width: 100% !important; justify-content: flex-start !important; }
}

@media (max-width: 520px) {
    .jt-kpi-grid { grid-template-columns: minmax(0, 1fr) !important; }
    .jt-entity-grid { grid-template-columns: minmax(0, 1fr) !important; }
}

/* Print / Executive PDF styling */
@media print {
    .jt-header-actions, .jt-toolbar-card, .jt-tabs-card, .jt-footer-card,
    .jt-bottom-right { display: none !important; }
    .jt-dashboard { padding: 0 !important; }
    .jt-entity-card { break-inside: avoid; box-shadow: none !important; }
}
</style>
@endpush

@section('content')

<div class="content d-flex flex-column flex-column-fluid pt-0 kt_content_joiningtracker" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div class="container-fluid px-0" id="kt_content_container">

<div class="jt-dashboard" id="joiningTrackerDashboard">

    {{-- ══════════════════════════════════════════════════════════════
         ENTERPRISE HEADER TOOLBAR CARD (Client Tracker Inspired)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="jt-header-card">
        <div class="jt-header-left">
            <div class="jt-header-icon-box">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <polyline points="16 11 18 13 22 9"/>
                </svg>
            </div>
            <div>
                <div class="jt-header-title-row">
                    <h1 class="jt-header-title">Joining Tracker</h1>
                    <span class="jt-verified-badge" title="Verified ATS Module">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4" stroke-width="2.4"/>
                        </svg>
                    </span>
                    <span class="jt-header-count-pill jt-num" id="jtHeaderTotalBadge">{{ $joiningStats['total'] ?? 0 }}</span>
                </div>
                <div class="jt-header-subtitle">Track candidate onboarding milestones, joining schedules, document readiness, and post-offer confirmations.</div>
            </div>
        </div>

        <div class="jt-header-actions">
            <button type="button" class="jt-hdr-btn" id="jtRefreshBtn" onclick="window.jeroJoiningTracker && window.jeroJoiningTracker.refresh(false)" title="Sync live joinings from Elasticsearch">
                <i class="fas fa-sync-alt text-primary"></i>
                <span>Sync</span>
            </button>

            <div class="dropdown">
                <button type="button" class="jt-hdr-btn" id="jtExportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Export</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:#64748b;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end jt-dropdown-menu" aria-labelledby="jtExportDropdownBtn">
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="exportJoiningCsv()">
                            <span class="jt-drop-ico csv"><i class="fas fa-file-csv"></i></span>
                            <span>
                                <span class="jt-drop-title">Export Current View (CSV)</span>
                                <span class="jt-drop-sub">Instant browser-side download</span>
                            </span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="exportJoining()">
                            <span class="jt-drop-ico pdf"><i class="fas fa-print"></i></span>
                            <span>
                                <span class="jt-drop-title">Save / Print Report (PDF)</span>
                                <span class="jt-drop-sub">Executive joining summary</span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button type="button" class="jt-hdr-btn" id="jtMoreActionsBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <span>More Actions</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:#64748b;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end jt-dropdown-menu" aria-labelledby="jtMoreActionsBtn">
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="selectJoiningTab('joining_today')">
                            <span class="jt-drop-ico act"><i class="far fa-calendar-check"></i></span>
                            <span>
                                <span class="jt-drop-title">View Joining Today</span>
                                <span class="jt-drop-sub">Candidates scheduled for today</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="selectJoiningTab('delayed')">
                            <span class="jt-drop-ico wrn"><i class="fas fa-hourglass-half"></i></span>
                            <span>
                                <span class="jt-drop-title">Review Delayed Joinings</span>
                                <span class="jt-drop-sub">Reschedule &amp; follow-up queue</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="selectJoiningTab('pending_docs')">
                            <span class="jt-drop-ico rst"><i class="far fa-file-alt"></i></span>
                            <span>
                                <span class="jt-drop-title">Check Pending Docs</span>
                                <span class="jt-drop-sub">Onboarding paperwork in progress</span>
                            </span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                    <li>
                        <a class="jt-dropdown-item" href="javascript:void(0);" onclick="window.jeroJoiningTracker && window.jeroJoiningTracker.resetFilters()">
                            <span class="jt-drop-ico csv"><i class="fas fa-undo-alt"></i></span>
                            <span>
                                <span class="jt-drop-title">Reset All Filters</span>
                                <span class="jt-drop-sub">Clear search, location &amp; tab</span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         KPI STAT CARDS — Client Tracker Inspired (6 Clickable Cards)
         ══════════════════════════════════════════════════════════════ --}}
    @php
        $totalJoinInit      = (int) ($joiningStats['total'] ?? 0);
        $last3MonthsInit    = (int) ($joiningStats['last_3_months'] ?? 0);
        $thisMonthInit      = (int) ($joiningStats['this_month'] ?? 0);
        $todayJoinInit      = (int) ($joiningStats['today'] ?? 0);
        $onboardedInit      = (int) ($joiningStats['onboarded'] ?? 0);
        $backedOutInit      = (int) ($joiningStats['backed_out'] ?? 0);
        $noShowInit         = (int) ($joiningStats['no_show'] ?? 0);
        $onboardedPctInit   = $totalJoinInit > 0 ? round(($onboardedInit / $totalJoinInit) * 100) : 0;
        $backedOutPctInit   = $totalJoinInit > 0 ? round(($backedOutInit / $totalJoinInit) * 100) : 0;
        $thisMonthShort     = date('M y');
    @endphp
    <div class="jt-kpi-grid" id="jtKpiGrid">
        {{-- Card 1: Total Joinings --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter active" data-tab="all" onclick="selectJoiningTab('all')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num-row">
                        <div class="jt-kpi-num" data-kpi="total">{{ $totalJoinInit }}</div>
                    </div>
                    <div class="jt-kpi-lbl">Total Joinings</div>
                </div>
            </div>
            <div class="jt-kpi-sub slate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>All scheduled &amp; completed</span>
            </div>
        </button>

        {{-- Card 2: Joining Last 3 Months --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter" data-tab="last_3_months" onclick="selectJoiningTab('last_3_months')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon teal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 14 14"/>
                        <path d="M12 2a10 10 0 0 0-7 3l-3-1v5h5l-2-2a8 8 0 1 1-1.3 7.8"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num" data-kpi="last_3_months">{{ $last3MonthsInit }}</div>
                    <div class="jt-kpi-lbl">Joining Last 3 Months</div>
                </div>
            </div>
            <div class="jt-kpi-sub teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                <span>Past 90 days joinings</span>
            </div>
        </button>

        {{-- Card 3: Joining This Month --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter" data-tab="this_month" onclick="selectJoiningTab('this_month')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num" data-kpi="this_month">{{ $thisMonthInit }}</div>
                    <div class="jt-kpi-lbl">Joining This Month</div>
                </div>
            </div>
            <div class="jt-kpi-sub amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span id="kpiThisMonthSub" data-month="{{ $thisMonthShort }}">{{ $thisMonthShort }} schedule</span>
            </div>
        </button>

        {{-- Card 4: Joining Today --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter" data-tab="joining_today" onclick="selectJoiningTab('joining_today')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num" data-kpi="today">{{ $todayJoinInit }}</div>
                    <div class="jt-kpi-lbl">Joining Today</div>
                </div>
            </div>
            <div class="jt-kpi-sub indigo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m18 15-6-6-6 6"/></svg>
                <span>Scheduled for today</span>
            </div>
        </button>

        {{-- Card 5: Joined & Onboarded --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter" data-tab="onboarded" onclick="selectJoiningTab('onboarded')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num" data-kpi="onboarded">{{ $onboardedInit }}</div>
                    <div class="jt-kpi-lbl">Joined &amp; Onboarded</div>
                </div>
            </div>
            <div class="jt-kpi-sub green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                <span data-kpi-pct="onboarded">{{ $onboardedPctInit }}% of total joinings</span>
            </div>
        </button>

        {{-- Card 6: Joined & Backedout --}}
        <button type="button" class="jt-kpi-card jt-kpi-filter" data-tab="backed_out" onclick="selectJoiningTab('backed_out')">
            <div class="jt-kpi-top">
                <div class="jt-kpi-icon rose">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="18" y1="8" x2="23" y2="13"/>
                        <line x1="23" y1="8" x2="18" y2="13"/>
                    </svg>
                </div>
                <div class="jt-kpi-meta">
                    <div class="jt-kpi-num" data-kpi="backed_out">{{ $backedOutInit }}</div>
                    <div class="jt-kpi-lbl">Joined &amp; Backedout</div>
                </div>
            </div>
            <div class="jt-kpi-sub rose">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                <span id="kpiBackedOutSub">{{ $backedOutPctInit }}% &middot; {{ $noShowInit }} no-show</span>
            </div>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RULE 20 TIER 1: SEARCH & FILTER TOOLBAR CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="jt-toolbar-card">
        <div class="jt-search-subcard">
            <i class="fas fa-search"></i>
            <input type="text" id="jt_search" placeholder="Search by candidate name, job title, client, recruiter, or ID..." autocomplete="off" />
            <kbd class="jt-search-kbd" id="jtSearchKbd" title="Press Ctrl+K to search">Ctrl+K</kbd>
        </div>

        <div class="jt-inline-filters">
            <div class="jt-filter-pill">
                <span class="jt-filter-pill-lbl"><i class="fas fa-map-marker-alt text-muted"></i> Location:</span>
                <select class="jt-filter-select" id="jt_country" aria-label="Filter by location">
                    <option value="">All Locations</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="jt-filter-pill">
                <span class="jt-filter-pill-lbl"><i class="fas fa-sort-amount-down text-muted"></i> Sort:</span>
                <select class="jt-filter-select" id="jt_sort" aria-label="Sort joinings">
                    <option value="12">Latest Joining Date</option>
                    <option value="13">Oldest Joining Date</option>
                    <option value="15">High → Low (Amount)</option>
                    <option value="14">Low → High (Amount)</option>
                    <option value="10">Newest (Offer Date)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RULE 20 TIER 3: PRIMARY JOINING CARD + SUB-CARDS STREAM
         ══════════════════════════════════════════════════════════════ --}}
    @if(in_array('view_joining', $userPermissions))
        <div class="jt-stream-wrapper">
            <div id="joining-tracker" class="jt-cards-stream">
                <div class="jt-loading-card">
                    <img src="{{ asset('assets/images/Loading2.gif') }}" height="28" alt=""> &nbsp; Loading joinings...
                </div>
            </div>
        </div>

        <div class="jt-footer-card">
            <div id="jt_pagination"></div>
        </div>
    @else
        <div class="jt-empty-card">
            <div class="jt-empty-icon" style="background:#fef2f2!important;color:#dc2626!important;">
                <i class="fas fa-lock"></i>
            </div>
            <div class="jt-empty-title">Access Denied</div>
            <div class="jt-empty-sub">You do not have permission to view Joining Tracker. Contact your administrator.</div>
        </div>
    @endif

</div>{{-- /jt-dashboard --}}

{{-- Change Candidate Stage drawer --}}
<style>
  #csd_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 28px; background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; box-shadow: 0 1px 3px rgba(15,23,42,0.04); position: sticky; top: 0; z-index: 20; flex-wrap: wrap; }
  #csd_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  #csd_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
  #csd_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.3; }
  #csd_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 400; color: #64748b; margin-top: 1px; }
  #csd_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
  #csd_drawer .sd-btn-cancel { border: 1px solid #e2e8f0; background: #ffffff; color: #475569; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all .15s ease; }
  #csd_drawer .sd-btn-cancel:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }
  #csd_drawer .sd-btn-confirm { border: none; background: #3b82f6; color: #fff; border-radius: 8px; padding: 9px 20px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: filter .15s; }
  #csd_drawer .sd-btn-confirm:hover { filter: brightness(0.94); }
  #csd_drawer .sd-btn-confirm:disabled { background: #cbd5e1 !important; color: #64748b !important; cursor: not-allowed; }
  #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e2e8f0; }
  #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid transparent; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
  #csd_drawer .sd-close:hover { background: #f1f5f9; border-color: #e2e8f0; color: #ef4444; }
</style>
<div id="csd_drawer_toggle" style="display:none;"></div>
<div id="csd_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="candidate-stage"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#csd_drawer_toggle" data-kt-drawer-close="#csd_drawer_close">
  <div class="d-flex flex-column h-100 w-100">
    <div class="sd-topbar">
      <div class="sd-topbar-left">
        <span class="sd-topbar-ico"><i class="fa fa-random"></i></span>
        <div>
          <div class="sd-topbar-title">Change Candidate Stage</div>
          <div class="sd-topbar-sub" id="csd_drawer_subtitle">&nbsp;</div>
        </div>
      </div>
      <div class="sd-topbar-right">
        <button type="button" class="sd-btn-cancel" id="stage_sub_stage_close">Cancel</button>
        <button type="button" class="sd-btn-confirm" id="custom_stage_submit">
          <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>
        </button>
        <span class="sd-divider"></span>
        <span class="sd-close" id="csd_drawer_close"><i class="fa fa-times"></i></span>
      </div>
    </div>
    <div class="flex-grow-1 px-6 py-4" style="overflow-y:auto;">
      <div class="stage_body_content" id="stage_body_content"></div>
    </div>
  </div>
</div>

</div>{{-- /container-fluid --}}
</div>{{-- /kt_post --}}
</div>{{-- /kt_content --}}

@endsection

@push('footer-script')
<script>
/**
 * Joining Tracker Client Engine (Rule 20 Card + Sub-Card + 3-Tier Caching + Realtime)
 *
 * Intent:
 * Provides user RAM-first state management (L1), IndexedDB persistence (L2) scoped by company_id,
 * Stale-While-Revalidate rehydration, in-flight request deduplication/abort, synchronized
 * Client-Tracker-inspired KPI card + sub-tab filtering, Client-First CSV/PDF export,
 * and real-time Reverb + BroadcastChannel delta updates for the Service Company Joining Tracker.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
(function () {
    'use strict';

    /* ── State & Limits ── */
    var TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};

    var state = {
        page: 1,
        size: parseInt(localStorage.getItem('candidatePerPage'), 10) || 10,
        search: '',
        country: '',
        sort: '12',
        tab: 'all'
    };

    var URL_CANDIDATES = '{!! route('service_company.cvtracker.allcandidates') !!}';

    // In-flight XHR tracker to cancel stale requests and eliminate race conditions
    var activeXhr = null;

    /* ── L1 RAM Snapshot Cache ── */
    var JoiningTrackerCache = {
        MAX_ENTRIES: 20,
        TTL_MS: 120000, // 2 minutes TTL
        store: new Map(),

        makeKey: function (s) {
            return [TENANT_ID, s.page, s.size, s.search.trim().toLowerCase(), s.country, s.sort, s.tab].join('|');
        },

        get: function (s) {
            var key = this.makeKey(s);
            var entry = this.store.get(key);
            if (!entry) return null;
            if (Date.now() - entry.time > this.TTL_MS) {
                this.store.delete(key);
                return null;
            }
            return entry.data;
        },

        set: function (s, data) {
            var key = this.makeKey(s);
            if (this.store.size >= this.MAX_ENTRIES) {
                var oldestKey = this.store.keys().next().value;
                this.store.delete(oldestKey);
            }
            this.store.set(key, { data: data, time: Date.now() });
        },

        clear: function () {
            this.store.clear();
        }
    };

    /* ── L2 IndexedDB Persistence Layer (Tenant-Isolated) ── */
    var JoiningTrackerIDB = {
        DB_NAME: 'JeroJoiningTrackerDB',
        DB_VERSION: 1,
        STORE_NAME: 'snapshots',
        TTL_MS: 300000, // 5 minutes TTL
        MAX_ENTRIES: 30,
        db: null,

        open: function () {
            var self = this;
            if (self.db) return Promise.resolve(self.db);
            if (!window.indexedDB) return Promise.resolve(null);

            return new Promise(function (resolve) {
                try {
                    var req = window.indexedDB.open(self.DB_NAME, self.DB_VERSION);
                    req.onupgradeneeded = function (e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains(self.STORE_NAME)) {
                            var store = db.createObjectStore(self.STORE_NAME, { keyPath: 'key' });
                            store.createIndex('time', 'time', { unique: false });
                        }
                    };
                    req.onsuccess = function (e) {
                        self.db = e.target.result;
                        resolve(self.db);
                    };
                    req.onerror = function () {
                        resolve(null);
                    };
                } catch (err) {
                    resolve(null);
                }
            });
        },

        get: function (key) {
            var self = this;
            return self.open().then(function (db) {
                if (!db) return null;
                return new Promise(function (resolve) {
                    try {
                        var tx = db.transaction(self.STORE_NAME, 'readonly');
                        var store = tx.objectStore(self.STORE_NAME);
                        var req = store.get(key);
                        req.onsuccess = function (e) {
                            var record = e.target.result;
                            if (!record) return resolve(null);
                            if (Date.now() - record.time > self.TTL_MS) {
                                self.delete(key);
                                return resolve(null);
                            }
                            resolve(record.data);
                        };
                        req.onerror = function () {
                            resolve(null);
                        };
                    } catch (err) {
                        resolve(null);
                    }
                });
            });
        },

        set: function (key, data) {
            var self = this;
            return self.open().then(function (db) {
                if (!db) return;
                try {
                    var tx = db.transaction(self.STORE_NAME, 'readwrite');
                    var store = tx.objectStore(self.STORE_NAME);
                    store.put({ key: key, data: data, time: Date.now() });
                    self.trim(store);
                } catch (err) {}
            });
        },

        delete: function (key) {
            var self = this;
            return self.open().then(function (db) {
                if (!db) return;
                try {
                    var tx = db.transaction(self.STORE_NAME, 'readwrite');
                    tx.objectStore(self.STORE_NAME).delete(key);
                } catch (err) {}
            });
        },

        clear: function () {
            var self = this;
            return self.open().then(function (db) {
                if (!db) return;
                try {
                    var tx = db.transaction(self.STORE_NAME, 'readwrite');
                    tx.objectStore(self.STORE_NAME).clear();
                } catch (err) {}
            });
        },

        trim: function (store) {
            try {
                var countReq = store.count();
                var self = this;
                countReq.onsuccess = function () {
                    if (countReq.result > self.MAX_ENTRIES) {
                        var index = store.index('time');
                        var cursorReq = index.openCursor();
                        cursorReq.onsuccess = function (e) {
                            var cursor = e.target.result;
                            if (cursor) {
                                cursor.delete();
                            }
                        };
                    }
                };
            } catch (e) {}
        }
    };

    /* ── Cached DOM Elements ── */
    var els = {
        stream: document.getElementById('joining-tracker'),
        pagination: document.getElementById('jt_pagination'),
        search: document.getElementById('jt_search'),
        country: document.getElementById('jt_country'),
        sort: document.getElementById('jt_sort'),
        refreshBtn: document.getElementById('jtRefreshBtn')
    };

    function loadingMarkup() {
        return '<div class="jt-loading-card"><img src="{{ asset('assets/images/Loading2.gif') }}" height="28" alt=""> &nbsp; Loading joinings...</div>';
    }

    function setSyncSpin(isSpinning) {
        if (els.refreshBtn) {
            els.refreshBtn.classList.toggle('is-syncing', !!isSpinning);
        }
    }

    /* ── Sync Active KPI Card Highlight ── */
    function syncActiveTabUI(tabKey) {
        document.querySelectorAll('#jtKpiGrid .jt-kpi-filter').forEach(function (card) {
            card.classList.toggle('active', card.dataset.tab === tabKey);
        });
    }

    window.selectJoiningTab = function (tabKey) {
        if (!tabKey) tabKey = 'all';
        state.tab = tabKey;
        syncActiveTabUI(tabKey);
        fetchTable(true);
    };

    /* ── Render View from Data (Memory or Server) ── */
    function renderData(resp) {
        if (!resp || !els.stream) return;
        var html = resp.html;
        if (typeof html === 'string' && html.trim().length > 0) {
            els.stream.innerHTML = html;
        } else {
            els.stream.innerHTML = '<div class="jt-empty-card"><div class="jt-empty-title">No Joining Records Found</div><div class="jt-empty-sub">No candidates match your active filter criteria.</div></div>';
        }
        if (els.pagination) {
            els.pagination.innerHTML = resp.pagination || '';
        }
        if (resp.joiningCounts) {
            updateCounts(resp.joiningCounts);
        }
    }

    /* ── Fetch Stream with L1 RAM Cache, L2 IndexedDB Stale-While-Revalidate & Stale Request Abort ── */
    function fetchTable(resetPage) {
        if (resetPage) state.page = 1;

        var ramCached = JoiningTrackerCache.get(state);
        if (ramCached) {
            renderData(ramCached);
            return;
        }

        var key = JoiningTrackerCache.makeKey(state);

        JoiningTrackerIDB.get(key).then(function (idbCached) {
            if (idbCached) {
                JoiningTrackerCache.set(state, idbCached);
                renderData(idbCached);
                executeNetworkFetch(true);
            } else {
                if (els.stream) {
                    els.stream.innerHTML = loadingMarkup();
                }
                executeNetworkFetch(false);
            }
        }).catch(function () {
            if (els.stream) {
                els.stream.innerHTML = loadingMarkup();
            }
            executeNetworkFetch(false);
        });
    }

    function executeNetworkFetch(isBackgroundRevalidate) {
        if (activeXhr && activeXhr.readyState !== 4) {
            activeXhr.abort();
            activeXhr = null;
        }

        setSyncSpin(true);

        activeXhr = $.ajax({
            url  : URL_CANDIDATES + '?page=' + state.page,
            type : 'GET',
            data : {
                status_cus         : 6,
                size               : state.size,
                per_page           : state.size,
                cus_search         : state.search,
                country            : state.country,
                sortingfilter      : state.sort,
                joining_tab_filter : state.tab,
            },
            success: function (resp) {
                activeXhr = null;
                setSyncSpin(false);
                if (typeof resp === 'string') {
                    try { resp = JSON.parse(resp); } catch (e) {}
                }
                JoiningTrackerCache.set(state, resp);
                var key = JoiningTrackerCache.makeKey(state);
                JoiningTrackerIDB.set(key, resp);
                renderData(resp);
            },
            error: function (xhr, status) {
                setSyncSpin(false);
                activeXhr = null;
                if (status === 'abort') return;
                if (!isBackgroundRevalidate && els.stream) {
                    els.stream.innerHTML = '<div class="jt-empty-card"><div class="jt-empty-title" style="color:#dc2626;">Error Loading Joinings</div><div class="jt-empty-sub">Unable to reach search projection. Please click Sync or refresh.</div></div>';
                }
            }
        });
    }

    /* ── Sync KPI cards + header pill ── */
    function updateCounts(c) {
        if (!c) return;
        var n = function (v) { return (v != null ? Number(v) : 0); };
        var total = n(c.total);

        // Update numeric metric elements inside KPI cards only
        document.querySelectorAll('#jtKpiGrid [data-kpi]').forEach(function (el) {
            var k = el.getAttribute('data-kpi');
            if (c[k] != null) el.textContent = n(c[k]);
        });

        var headerBadge = document.getElementById('jtHeaderTotalBadge');
        if (headerBadge) headerBadge.textContent = total;

        document.querySelectorAll('[data-kpi-pct]').forEach(function (el) {
            var k = el.getAttribute('data-kpi-pct');
            var pct = total > 0 ? Math.round((n(c[k]) / total) * 100) : 0;
            el.textContent = pct + '% of total joinings';
        });

        var backedOutSub = document.getElementById('kpiBackedOutSub');
        if (backedOutSub) {
            var boPct = total > 0 ? Math.round((n(c.backed_out) / total) * 100) : 0;
            backedOutSub.textContent = boPct + '% \u00B7 ' + n(c.no_show) + ' no-show';
        }
    }

    function resetFilters() {
        state.page = 1;
        state.search = '';
        state.country = '';
        state.sort = '12';
        state.tab = 'all';

        if (els.search) els.search.value = '';
        if (els.country) els.country.value = '';
        if (els.sort) els.sort.value = '12';

        syncActiveTabUI('all');
        fetchTable(true);
    }

    /* ── Event Handlers ── */
    var searchTimer;
    if (els.search) {
        els.search.addEventListener('input', function () {
            clearTimeout(searchTimer);
            var val = this.value;
            searchTimer = setTimeout(function () {
                state.search = val;
                fetchTable(true);
            }, 300);
        });
        els.search.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && this.value) {
                this.value = '';
                state.search = '';
                fetchTable(true);
            }
        });
    }

    /* Ctrl+K shortcut focuses search */
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (els.search) els.search.focus();
        }
    });

    if (els.country) {
        els.country.addEventListener('change', function () {
            state.country = this.value;
            fetchTable(true);
        });
    }

    if (els.sort) {
        els.sort.addEventListener('change', function () {
            state.sort = this.value;
            fetchTable(true);
        });
    }

    /* Pagination (delegated) */
    if (els.pagination) {
        els.pagination.addEventListener('click', function (e) {
            var link = e.target.closest('.pagi-page, .pagi-arrow');
            if (!link || link.classList.contains('pagi-disabled') || link.classList.contains('pagi-active-disabled')) return;
            e.preventDefault();
            var pg = parseInt(link.dataset.page, 10);
            if (!pg) return;
            state.page = pg;
            fetchTable(false);
        });

        els.pagination.addEventListener('change', function (e) {
            if (e.target.classList.contains('pagi-perpage')) {
                var selectedPerPage = parseInt(e.target.value, 10);
                if (selectedPerPage) {
                    state.size = selectedPerPage;
                    localStorage.setItem('candidatePerPage', selectedPerPage);
                    fetchTable(true);
                }
            }
        });
    }

    /* Stage sub-stage drawer */
    $(document).on('click', '#stage_sub_stage_close', function () {
        $('#csd_drawer_close').trigger('click');
    });

    $(document).on('click', '.stage_sub_stage', function () {
        $('#stage_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
        $('#csd_drawer_toggle').trigger('click');
        var id  = $(this).data('row-id');
        var url = '{{ route('service_company.job-applications.stage_body_content', ':id') }}'.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            success: function (r) {
                if (r.status === 'success') {
                    $('#stage_body_content').html(r.view);
                    $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
                }
            }
        });
    });

    /**
     * Helper: Cryptographically secure UUIDv4 generator for request idempotency
     */
    function generateIdempotencyUUID() {
        if (window.crypto && window.crypto.getRandomValues) {
            return 'jt_' + TENANT_ID + '_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, function(c) {
                return (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16);
            });
        }
        return 'jt_' + TENANT_ID + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 15);
    }

    $(document).on('click', '#custom_stage_submit', function (e) {
        e.preventDefault();
        var ms = $('input[name="master_status"]:checked').val();
        var cm = $('#comment').val();
        if (ms == 8 && !cm) {
            Swal.fire("Warning", 'InReview Comment Required!', "info");
            return;
        }
        if ($('#main_substage').is(':visible') && $('input[name="sub_stages"]:checked').length === 0) {
            Swal.fire("Warning", 'Please select a sub stage to continue.', "info");
            return;
        }
        var $btn = $(this);
        if ($btn.data('submitting')) {
            return;
        }
        $btn.data('submitting', true);
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Waiting...');

        var idempotencyKey = generateIdempotencyUUID();
        var serializedData = $('#status_frm').serialize();
        serializedData += '&idempotency_key=' + encodeURIComponent(idempotencyKey);

        $.ajax({
            url: '{{ route('service_company.job-applications.stage_submit') }}',
            type: 'POST',
            data: serializedData,
            headers: {
                'X-Idempotency-Key': idempotencyKey,
                'Idempotency-Key': idempotencyKey
            },
            success: function (r) {
                $btn.data('submitting', false);
                $btn.prop("disabled", false).html('<span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>');
                if (typeof csdUpdateSubmitButton === 'function') {
                    csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
                }
                if (r.status === 'success') {
                    JoiningTrackerCache.clear();
                    JoiningTrackerIDB.clear();
                    if (jtBroadcastChannel) {
                        try {
                            jtBroadcastChannel.postMessage({ type: 'INVALIDATE_CACHE' });
                        } catch (err) {}
                    }
                    Swal.fire("Success", 'Application stage updated!', "success").then(function () {
                        $("#stage_sub_stage_close").click();
                    });
                    fetchTable(false);
                } else if (r.status === 'processing') {
                    Swal.fire("Info", r.message || 'Request is currently processing. Please wait...', "info");
                } else if (r.status === 'conflict') {
                    Swal.fire("Notice", r.message || 'Candidate stage was modified by another user. Refreshing...', "warning").then(function () {
                        $("#stage_sub_stage_close").click();
                    });
                    JoiningTrackerCache.clear();
                    JoiningTrackerIDB.clear();
                    fetchTable(false);
                } else if (r.status === 'cvpending') {
                    Swal.fire("Warning", 'Resume pending this job application.', "warning").then(function () {
                        $("#stage_sub_stage_close").click();
                    });
                } else if (r.status === 'candidatevalidation') {
                    if (typeof window.showStageRequiredFieldsModal === 'function') {
                        window.showStageRequiredFieldsModal(r);
                    } else {
                        Swal.fire("Warning", r.message, "warning").then(function () {
                            $("#stage_sub_stage_close").click();
                        });
                    }
                }
            },
            error: function () {
                $btn.data('submitting', false);
                $btn.prop("disabled", false).html('<span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>');
                Swal.fire("Error", 'Unable to update stage. Please try again.', "error");
            }
        });
    });

    /* Client-First Browser-Side CSV Export (0% Server Read Strain) */
    window.exportJoiningCsv = function () {
        var cards = document.querySelectorAll('#joining-tracker .jt-entity-card');
        if (!cards.length) {
            if (typeof Swal !== 'undefined') Swal.fire('No Data', 'There are no visible joining records to export.', 'info');
            return;
        }
        var esc = function (v) { return '"' + String(v || '').replace(/"/g, '""') + '"'; };
        var rows = [['Candidate', 'Designation', 'Job Title', 'Job ID', 'Client', 'Compensation', 'Joining Status', 'Joining Date', 'Owner', 'Stage']];
        cards.forEach(function (card) {
            rows.push([
                card.dataset.candName || '',
                card.dataset.candRole || '',
                card.dataset.jobTitle || '',
                card.dataset.jobId || '',
                card.dataset.clientName || '',
                card.dataset.comp || '',
                card.dataset.joiningStatus || '',
                card.dataset.joiningDate || '',
                card.dataset.owner || '',
                card.dataset.stage || ''
            ]);
        });
        var csv = rows.map(function (r) { return r.map(esc).join(','); }).join('\r\n');
        var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Joining-Tracker-' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    /* Executive Print-to-PDF Export (no server round-trip) */
    window.exportJoining = function () {
        var prev = document.title;
        document.title = 'Joining-Tracker-' + new Date().toISOString().slice(0, 10);
        var done = function () {
            document.title = prev;
            window.removeEventListener('afterprint', done);
        };
        window.addEventListener('afterprint', done);
        window.print();
        setTimeout(done, 1500);
    };

    /* ── Phase 4: Multi-Tab Coordination & Realtime Reverb Synchronization ── */
    var jtBroadcastChannel = null;
    if ('BroadcastChannel' in window) {
        try {
            jtBroadcastChannel = new BroadcastChannel('jero_joining_tracker_' + TENANT_ID);
            jtBroadcastChannel.onmessage = function (ev) {
                if (!ev || !ev.data) return;
                var msg = ev.data;
                if (msg.type === 'STAGE_UPDATED' || msg.type === 'INVALIDATE_CACHE') {
                    JoiningTrackerCache.clear();
                    JoiningTrackerIDB.clear();
                    if (msg.payload) {
                        applyRealtimeDelta(msg.payload, false);
                    } else {
                        executeNetworkFetch(true);
                    }
                }
            };
        } catch (e) {}
    }

    var recentEventKeys = new Set();
    var MAX_EVENT_BUFFER = 50;

    function applyRealtimeDelta(e, broadcastToTabs) {
        if (!e) return;

        var eventKey = e.event_id || (e.candidate_id + '_' + (e.entity_version || Date.now()) + '_' + (e.stage_id || 0));
        if (recentEventKeys.has(eventKey)) return;
        recentEventKeys.add(eventKey);
        if (recentEventKeys.size > MAX_EVENT_BUFFER) {
            var oldest = recentEventKeys.values().next().value;
            recentEventKeys.delete(oldest);
        }

        JoiningTrackerCache.clear();
        JoiningTrackerIDB.clear();

        if (broadcastToTabs && jtBroadcastChannel) {
            try {
                jtBroadcastChannel.postMessage({ type: 'STAGE_UPDATED', payload: e });
            } catch (err) {}
        }

        var targetWiseIds = Array.isArray(e.wise_ids) ? e.wise_ids : (e.wise_id ? [e.wise_id] : []);
        var matchedCard = null;

        for (var i = 0; i < targetWiseIds.length; i++) {
            var $el = $('.jt-entity-card[data-wise-id="' + targetWiseIds[i] + '"]');
            if ($el.length) {
                matchedCard = $el;
                break;
            }
        }

        if (matchedCard && matchedCard.length) {
            var newStageId = parseInt(e.stage_id, 10);
            if (newStageId === 6) {
                var stageBtn = matchedCard.find('.jt-stage-btn');
                if (stageBtn.length) {
                    stageBtn.text(e.stage_name || 'Joined');
                }
                if (e.sub_stage_name) {
                    var subEl = matchedCard.find('.jt-substage-pill');
                    if (subEl.length) {
                        subEl.text(e.sub_stage_name);
                    }
                }
                matchedCard.css('transition', 'background-color 0.4s ease');
                matchedCard.css('background-color', '#ecfdf5');
                setTimeout(function () {
                    matchedCard.css('background-color', '');
                }, 1800);
                executeNetworkFetch(true);
            } else {
                matchedCard.css('transition', 'all 0.5s ease');
                matchedCard.css('opacity', '0.3');
                matchedCard.css('background-color', '#fef2f2');
                setTimeout(function () {
                    matchedCard.remove();
                    executeNetworkFetch(true);
                }, 600);
            }
        } else {
            executeNetworkFetch(true);
        }
    }

    function initRealtime() {
        if (typeof window.Echo !== 'undefined' && TENANT_ID > 0) {
            try {
                window.Echo.private('company.' + TENANT_ID)
                    .listen('.candidate.drawer.updated', function (e) {
                        applyRealtimeDelta(e, true);
                    });

                var connector = window.Echo.connector;
                if (connector && connector.pusher && connector.pusher.connection) {
                    connector.pusher.connection.bind('state_change', function (states) {
                        if (states.current === 'connected' && (states.previous === 'disconnected' || states.previous === 'unavailable')) {
                            JoiningTrackerCache.clear();
                            JoiningTrackerIDB.clear();
                            executeNetworkFetch(true);
                        }
                    });
                }
            } catch (e) {}
        }
    }

    /* Expose cache clear & bridge methods for external triggers */
    window.invalidateJoiningTrackerCache = function () {
        JoiningTrackerCache.clear();
        JoiningTrackerIDB.clear();
    };

    window.jeroJoiningTracker = {
        refresh: function (resetPage) {
            JoiningTrackerCache.clear();
            JoiningTrackerIDB.clear();
            fetchTable(resetPage === true);
        },
        invalidateCache: window.invalidateJoiningTrackerCache,
        fetchTable: fetchTable,
        resetFilters: resetFilters
    };

    /* ── Initial Fetch & Realtime Boot ── */
    fetchTable(false);
    initRealtime();
})();
</script>
@endpush
